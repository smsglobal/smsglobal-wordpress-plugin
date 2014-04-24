<?php
/**
 * Processes a response from the http:// stream wrapper
 *
 * @package Smsglobal\RestApiClient\Http\Response
 */
class Smsglobal_RestApiClient_Http_Response_Stream implements Smsglobal_RestApiClient_Http_Response_Adapter
{
    /**
     * Response body
     * @var string
     */
    protected $content;
    /**
     * Headers from the request
     * @var HeaderBag
     */
    protected $headers;
    /**
     * Status code
     * @var int
     */
    protected $statusCode;
    /**
     * Constructor
     *
     * @param string $content Response body
     * @param array  $headers Headers array
     */
    public function __construct($content, array $headers)
    {
        $this->content = $content;
        $this->statusCode = array_shift($headers);
        $this->headers = $headers;
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
        if (!$this->headers instanceof Smsglobal_RestApiClient_Http_HeaderBag) {
            foreach ($this->headers as $i => $line) {
                list($name, $value) = explode(': ', $line, 2);
                unset($this->headers[$i]);
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
        if (is_string($this->statusCode)) {
            $this->statusCode = (int) substr($this->statusCode, 9, 3);
        }
        return $this->statusCode;
    }
}