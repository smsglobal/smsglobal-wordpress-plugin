<?php
class Smsglobal_RestApiClient_Resource_Proxy_SharedPoolProxy extends Smsglobal_RestApiClient_Resource_SharedPool
{
    private $rest;
    public function __construct($resourceUri, Smsglobal_RestApiClient_RestApiClient $rest)
    {
        $this->resourceUri = $resourceUri;
        $this->rest = $rest;
        $this->id = substr($resourceUri, 0, -1);
        $this->id = (int) substr($this->id, (strrpos('/', $this->id) + 1), (-1));
    }
    private function load()
    {
        if (isset($this->rest)) {
            $options = $this->rest->get($this->getResourceName(), $this->id);
            $this->setOptions($options);
            unset($this->rest);
        }
    }
    public function getName()
    {
        $this->load();
        return parent::getName();
    }
    public function getSize()
    {
        $this->load();
        return parent::getSize();
    }
}