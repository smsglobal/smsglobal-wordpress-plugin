<?php
class Smsglobal_RestApiClient_Resource_Proxy_MmsIncomingAttachmentProxy extends Smsglobal_RestApiClient_Resource_MmsIncomingAttachment
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
    public function getMms()
    {
        $this->load();
        return parent::getMms();
    }
    public function getName()
    {
        $this->load();
        return parent::getName();
    }
    public function getType()
    {
        $this->load();
        return parent::getType();
    }
    public function getData()
    {
        $this->load();
        return parent::getData();
    }
}