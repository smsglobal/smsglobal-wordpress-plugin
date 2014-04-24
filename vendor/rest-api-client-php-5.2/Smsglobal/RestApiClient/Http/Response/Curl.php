<?php
/**
 * Processes a cURL response
 *
 * @package Smsglobal\RestApiClient\Http\Response
 */
class Smsglobal_RestApiClient_Http_Response_Curl implements Smsglobal_RestApiClient_Http_Response_Adapter
{
    /**
     * Response body
     * @var mixed
     */
    protected $content;
    /**
     * cURL handle
     * @var resource
     */
    protected $handle;
    /**
     * Headers from the request
     * @var HeaderBag
     */
    protected $headers;
    /**
     * Request information fetched from cURL handle
     * @var array
     */
    protected $info;
    /**
     * HTTP status code of response
     * @var int
     */
    protected $statusCode;
    /**
     * cURL errors that we allow without throwing an exception
     * @var array
     */
    protected static $ignoredErrors = array(CURLE_OK, CURLE_HTTP_NOT_FOUND, CURLE_PARTIAL_FILE);
    /**
     * Constructor
     *
     * @param resource $handle cURL handle
     * @throws ServiceException
     */
    public function __construct($handle)
    {
        $this->handle = $handle;
        curl_setopt($handle, CURLOPT_HEADER, true);
        $parts = curl_exec($handle);
        $error = curl_errno($handle);
        if (!in_array($error, self::$ignoredErrors)) {
            throw new Smsglobal_RestApiClient_Exception_ServiceException(curl_error($handle), $error);
        }
        $parts = explode('

', $parts, 2);
        $this->headers = $parts[0];
        $this->content = $parts[1];
    }
    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        if (is_string($this->headers)) {
            $headers = explode('
', $this->headers);
            unset($headers[0]);
            $this->headers = array();
            foreach ($headers as $line) {
                list($name, $value) = explode(': ', $line, 2);
                $this->headers[$name] = $value;
            }
            $this->headers = new Smsglobal_RestApiClient_Http_HeaderBag($this->headers);
        }
        return $this->headers;
    }
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        if (null === $this->statusCode) {
            $this->statusCode = (int) curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
        }
        return $this->statusCode;
    }
}