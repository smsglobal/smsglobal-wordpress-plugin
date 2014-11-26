<?php
/**
 * Acts as an ORM of sorts for the REST API. Allows fetching, saving and
 * deleting resource objects
 *
 * @package Smsglobal\RestApiClient
 */
class Smsglobal_RestApiClient_RestApiClient
{
    /**
     * The default host for the API
     */
    const DEFAULT_HOST = 'api.smsglobal.com';
    /**
     * Time zone instance
     * @var \DateTimeZone
     */
    protected $timeZone;
    /**
     * Whether to use SSL for API calls
     * @var bool
     */
    protected $useSsl = true;
    /**
     * API key details
     * @var ApiKey
     */
    protected $apiKey;
    /**
     * Class name of request adapter to use
     * @var string
     */
    protected $requestAdapter;
    /**
     * Whether to allow caching objects
     * @var bool
     */
    protected $useCache = true;
    /**
     * Array of schema for each resource
     * @var array
     *
     */
    protected $schema;
    /**
     * Cache of objects that have been previously retrieved
     * @var array
     */
    protected $resourceCache = array();
    /**
     * Constructor
     *
     * @param ApiKey $apiKey         API key instance
     * @param string $requestAdapter Request adapter class to use
     * @param bool   $useSsl         Whether to use SSL for API calls
     * @param string $host           API host name
     * @param bool   $allowCache     Whether to allow caching objects
     */
    public function __construct(Smsglobal_RestApiClient_ApiKey $apiKey, $requestAdapter = Smsglobal_RestApiClient_Http_Request::DEFAULT_ADAPTER, $useSsl = true, $host = self::DEFAULT_HOST, $allowCache = true)
    {
        $this->apiKey = $apiKey;
        $this->requestAdapter = $requestAdapter;
        $this->useSsl = (bool) $useSsl;
        $this->host = (string) $host;
        $this->useCache = (bool) $allowCache;
    }
    /**
     * Gets the schema for all resources
     *
     * @return array
     */
    protected function getSchema()
    {
        if (null === $this->schema) {
            $this->schema = include dirname(__FILE__) . '/Schema.php';
        }
        return $this->schema;
    }
    /**
     * Deletes the given resource
     *
     * @param Base $resource Resource instance
     * @return bool True if deletion was successful
     */
    public function delete(Smsglobal_RestApiClient_Resource_Base $resource)
    {
        $id = $resource->getId();
        if (null === $id) {
            return true;
        }
        $uri = $this->getResourceUri($resource, $id);
        $this->makeRequest($uri, 'DELETE');
        $resource->setId(null);
        if ($this->useCache) {
            unset($this->resourceCache[$resource->getResourceName()][$id]);
        }
        return true;
    }
    /**
     * Gets a single resource of the given type and ID
     *
     * @param string $resource Resource name
     * @param int    $id       ID
     * @return Base
     */
    public function get($resource, $id)
    {
        $id = (int) $id;
        if ($this->useCache) {
            return $this->getResourceFromCache($resource, $id);
        } else {
            return $this->loadResourceData($resource, $id);
        }
    }
    /**
     * Gets a list of resources
     *
     * @param string $resource Resource name
     * @param int    $offset   Offset (used for pagination)
     * @param int    $limit    Limit (max results per page)
     * @param array  $filters  Optional filters to apply
     * @return object
     */
    public function getList($resource, $offset = 0, $limit = Smsglobal_RestApiClient_PaginationData::DEFAULT_LIMIT, array $filters = array())
    {
        $uri = $this->getResourceUri($resource);
        $limit = (int) $limit;
        if ($limit !== Smsglobal_RestApiClient_PaginationData::DEFAULT_LIMIT) {
            $filters['limit'] = $limit;
        }
        $offset = (int) $offset;
        if (0 !== $offset) {
            $filters['offset'] = $offset;
        }
        if (!empty($filters)) {
            $uri = sprintf('%s?%s', $uri, http_build_query($filters));
        }
        $data = $this->makeRequest($uri);
        foreach ($data->objects as $i => $object) {
            $id = (int) $object->id;
            if ($this->useCache) {
                $object = $this->getResourceFromCache($resource, $id, $object);
            } else {
                $object = $this->instantiateResource($resource, $data);
            }
            $data->objects[$i] = $object;
        }
        $data->meta = new Smsglobal_RestApiClient_PaginationData($data->meta);
        return $data;
    }
    /**
     * Saves the resource. If it is new, it is created and the ID property will
     * be set to the new ID
     *
     * @param Base $resource Resource to save
     * @return Base $resource Saved resource
     */
    public function save(Smsglobal_RestApiClient_Resource_Base $resource)
    {
        $id = $resource->getId();
        $resourceName = $resource->getResourceName();
        $uri = $this->getResourceUri($resourceName, $id);
        $data = new stdClass();
        $schema = $this->getSchema();
        foreach ($schema[$resourceName] as $name => $field) {
            $method = 'get' . ucfirst($name);
            $value = $resource->{$method}();
            if ('related' === $field->type) {
                if ($value instanceof Smsglobal_RestApiClient_Resource_Base) {
                    $value = $value->getId();
                }
            } elseif ('datetime' === $field->type && null !== $value) {
                /** @var DateTime $value */
                $value = $value->format(DateTime::ISO8601);
            }
            $data->{$name} = $value;
        }
        $method = null === $id ? 'POST' : 'PATCH';
        /** @var Adapter $response */
        $response = $this->makeRequest($uri, $method, $data, true);
        $id = $response->getHeaders()->get('Location');
        if (null !== $id) {
            $id = substr($id, 0, -1);
            $id = (int) substr($id, (strrpos('/', $id) + 1), (-1));
            $resource->setId($id);
            if ($this->useCache) {
                $this->addResourceToCache($resourceName, $id, $resource);
            }
        }
        return $resource;
    }
    /**
     * Gets the URI for a resource
     *
     * @param string   $resource Resource name
     * @param int|null $id       Optional ID number
     * @return string
     */
    protected function getResourceUri($resource, $id = null)
    {
        $uri = sprintf('/v%s/%s/', Smsglobal_RestApiClient_Version::REST_API_VERSION, $resource);
        if (null !== $id) {
            $uri = sprintf('%s%s/', $uri, $id);
        }
        return $uri;
    }
    /**
     * Checks the status code of a response and throws an exception if required
     *
     * @param Adapter $response Response instance
     *
     * @return void
     * @throws Exception\MethodNotAllowedException
     * @throws Exception\InvalidDataException
     * @throws Exception\ResourceNotFoundException
     * @throws Exception\AuthorizationException
     * @throws Exception\ServiceException
     * @throws \Exception
     */
    protected function handleStatusCode(Smsglobal_RestApiClient_Http_Response_Adapter $response)
    {
        $statusCode = $response->getStatusCode();
        switch ($statusCode) {
        case 200:
            
        case 201:
            
        case 202:
            
        case 204:
            
        case 410:
            break;
        case 400:
            $errors = json_decode($response->getContent());
            $errors = (array) $errors->errors;
            throw new Smsglobal_RestApiClient_Exception_InvalidDataException($errors, $statusCode);
        case 401:
            $message = json_decode($response->getContent())->error;
            throw new Smsglobal_RestApiClient_Exception_AuthorizationException($message, $statusCode);
        case 404:
            throw new Smsglobal_RestApiClient_Exception_ResourceNotFoundException('', $statusCode);
        case 405:
            throw new Smsglobal_RestApiClient_Exception_MethodNotAllowedException('', $statusCode);
        case 500:
            
        case 502:
            
        case 503:
            
        case 504:
            throw new Smsglobal_RestApiClient_Exception_ServiceException('', $statusCode);
        default:
            throw new Exception('Received unexpected response', $statusCode);
        }
    }
    /**
     * Makes a request to the API and returns the decoded response body ready
     * for use.
     *
     * Can also return the response object itself if $returnResponse is true
     *
     * @param string      $uri            URI to request (without hostname)
     * @param string      $method         HTTP method
     * @param null|string $content        Optional body content of the request,
     * not encoded
     * @param bool        $returnResponse Whether to return the response object
     * @return mixed
     */
    protected function makeRequest($uri, $method = 'GET', $content = null, $returnResponse = false)
    {
        if($this->apiKey->isSetPackage()) {
            $uri .= '?package=' . (string) $this->apiKey->getPackage();
        }

        $fullUri = sprintf('http%s://%s%s', $this->useSsl ? 's' : '', $this->host, $uri);
        $request = new Smsglobal_RestApiClient_Http_Request($fullUri);
        $request->headers->set('Accept', 'application/json');
        $this->setAuthorizationHeader($request, $method, $uri);
        if (null !== $content) {
            $request->headers->set('Content-Type', 'application/json');
            $content = json_encode($content);
        }
        $response = $request->setMethod($method)->makeRequest($content);
        $this->handleStatusCode($response);
        if ($returnResponse) {
            return $response;
        }
        $content = json_decode($response->getContent());
        return $content;
    }
    /**
     * Sets the Authorization header on the given request
     *
     * @param Request $request    Request instance
     * @param string  $method     HTTP method
     * @param string  $requestUri Request URI
     * @return $this Provides a fluent interface
     */
    protected function setAuthorizationHeader(Smsglobal_RestApiClient_Http_Request $request, $method, $requestUri)
    {
        $header = $this->apiKey->getAuthorizationHeader($method, $requestUri, $this->host, $this->useSsl ? 443 : 80);
        $request->headers->set('Authorization', $header);
        return $this;
    }
    /**
     * Checks if a resource is cached
     *
     * @param string $resource Resource name
     * @param int    $id       ID
     * @return bool
     */
    protected function isResourceCached($resource, $id)
    {
        if (!isset($this->resourceCache[$resource])) {
            $this->resourceCache[$resource] = array();
        }
        return isset($this->resourceCache[$resource][$id]);
    }
    /**
     * Gets a resource from the cache. Instantiates then caches one if not found
     *
     * @param string      $resource Resource name
     * @param int         $id       ID
     * @param object|null $data     Data to use for instantiation
     * @return Base
     */
    protected function getResourceFromCache($resource, $id, $data = null)
    {
        if (!$this->isResourceCached($resource, $id)) {
            if (null === $data) {
                $data = $this->loadResourceData($resource, $id);
            }
            $this->resourceCache[$resource][$id] = $this->instantiateResource($resource, $data);
        }
        return $this->resourceCache[$resource][$id];
    }
    /**
     * Instantiate a resource of the given type using the given data
     *
     * @param string $resource Resource name
     * @param object $data     Data to use for the instance
     *
     * @return Base
     */
    protected function instantiateResource($resource, $data)
    {
        $class = sprintf('%s\\Resource\\%s', 'Smsglobal_RestApiClient', $resource);
        $schema = $this->getSchema();
        foreach ($schema[strtolower($resource)] as $name => $field) {
            $data->name = $this->convertFieldValue($data->name, $field->type, $name);
        }
        return new ${'_value_52141bc45cb92' . !($_value_52141bc45cb92 = is_string($class) ? str_replace('\\', '_', $class) : $class)}($data);
    }
    /**
     * Gets the time zone to use for instantiating resources with date fields
     *
     * @return \DateTimeZone
     */
    protected function getTimeZone()
    {
        if (null === $this->timeZone) {
            $this->timeZone = new DateTimeZone('UTC');
        }
        return $this->timeZone;
    }
    /**
     * Convert a field's value to a suitable PHP value
     *
     * @param mixed  $value Value to convert
     * @param string $type  Field type
     * @param string $name  Field name (used for returning Proxy objects)
     *
     * @return mixed
     */
    protected function convertFieldValue($value, $type, $name)
    {
        switch ($type) {
        case 'boolean':
            $value = (bool) $value;
            break;
        case 'dateTime':
            $value = new DateTime($value, $this->getTimeZone());
            break;
        case 'integer':
            $value = (int) $value;
            break;
        case 'float':
            $value = (double) $value;
            break;
        case 'related':
            $name = ucfirst($name);
            if (is_string($value)) {
                $proxy = sprintf('%s\\Resource\\Proxy\\%sProxy', 'Smsglobal_RestApiClient', $name);
                $value = new ${'_value_52141bc45d831' . !($_value_52141bc45d831 = is_string($proxy) ? str_replace('\\', '_', $proxy) : $proxy)}($value, $this);
            } else {
                $class = sprintf('%s\\Resource\\%s', 'Smsglobal_RestApiClient', $name);
                $value = new ${'_value_52141bc45db22' . !($_value_52141bc45db22 = is_string($class) ? str_replace('\\', '_', $class) : $class)}($value);
            }
            break;
        case 'string':
            
        default:
            $value = (string) $value;
        }
        return $value;
    }
    /**
     * Loads the data for a given resource
     *
     * @param string $resource Resource name
     * @param int    $id       ID
     * @return object
     */
    protected function loadResourceData($resource, $id)
    {
        $uri = $this->getResourceUri($resource, $id);
        $data = $this->makeRequest($uri);
        return $data;
    }
    /**
     * Adds a resource to the cache
     *
     * @param string $resourceName
     * @param int    $id
     * @param Base   $resource
     * @return $this Provides a fluent interface
     */
    protected function addResourceToCache($resourceName, $id, Smsglobal_RestApiClient_Resource_Base $resource)
    {
        if (!isset($this->resourceCache[$resourceName])) {
            $this->resourceCache[$resourceName] = array();
        }
        $this->resourceCache[$resourceName][$id] = $resource;
        return $this;
    }
    /**
     * Sets whether to cache resources.
     *
     * Disable to improve performance when batch processing, or when objects may
     * change outside of this request and you want to maintain freshness
     *
     * Enable to improve performance when there's a chance of objects being
     * fetched multiple times
     *
     * @param bool $useCache
     * @return $this Provides a fluent interface
     */
    public function setUseCache($useCache = true)
    {
        $this->useCache = (bool) $useCache;
        return $this;
    }
    /**
     * Gets whether to cache resources
     *
     * @return bool
     */
    public function useCache()
    {
        return $this->useCache;
    }
}
