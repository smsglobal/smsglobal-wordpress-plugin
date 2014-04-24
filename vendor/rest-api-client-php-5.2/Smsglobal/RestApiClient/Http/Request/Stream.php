<?php
/**
 * Makes a HTTP request using the http:// stream wrapper
 *
 * @package Smsglobal\RestApiClient\Http\Request
 */
class Smsglobal_RestApiClient_Http_Request_Stream implements Smsglobal_RestApiClient_Http_Request_Adapter
{
    /**
     * {@inheritdoc}
     */
    public function request($url, $method = 'GET', array $headers = array(), $content = null)
    {
        $streamHeaders = array();
        foreach ($headers as $header => $value) {
            $streamHeaders[] = sprintf('%s: %s', $header, $value);
        }
        $headers = implode('
', $streamHeaders);
        $context = stream_context_create(array('http' => array('method' => $method, 'header' => $headers, 'content' => $content, 'follow_location' => 0, 'ignore_errors' => true)));
        return new Smsglobal_RestApiClient_Http_Response_Stream(file_get_contents($url, null, $context), $http_response_header);
    }
}