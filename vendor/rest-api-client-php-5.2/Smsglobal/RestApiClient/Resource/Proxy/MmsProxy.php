<?php
class Smsglobal_RestApiClient_Resource_Proxy_MmsProxy extends Smsglobal_RestApiClient_Resource_Mms
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
    public function getOrigin()
    {
        $this->load();
        return parent::getOrigin();
    }
    public function getDestination()
    {
        $this->load();
        return parent::getDestination();
    }
    public function getSubject()
    {
        $this->load();
        return parent::getSubject();
    }
    public function getMessage()
    {
        $this->load();
        return parent::getMessage();
    }
    public function getDateTimeSent()
    {
        $this->load();
        return parent::getDateTimeSent();
    }
    public function getStatus()
    {
        $this->load();
        return parent::getStatus();
    }
    public function getDateTimeStatusUpdate()
    {
        $this->load();
        return parent::getDateTimeStatusUpdate();
    }
    public function getAttachments()
    {
        $this->load();
        return parent::getAttachments();
    }
}