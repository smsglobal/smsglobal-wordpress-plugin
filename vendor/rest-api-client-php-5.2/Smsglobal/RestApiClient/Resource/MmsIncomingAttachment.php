<?php
/**
 * A resource representing an SMSGlobal incoming MMS attachment
 *
 * @package Smsglobal\RestApiClient\Resource
 */
class Smsglobal_RestApiClient_Resource_MmsIncomingAttachment extends Smsglobal_RestApiClient_Resource_Base
{
    /**
     * MMS
     * @var MmsIncoming
     */
    protected $mms;
    /**
     * Name
     * @var string
     */
    protected $name;
    /**
     * Content Type
     * @var string
     */
    protected $type;
    /**
     * File binary data
     * @var string
     */
    protected $data;
    /**
     * Gets the MMS
     *
     * @return MmsIncoming
     */
    public function getMms()
    {
        return $this->mms;
    }
    /**
     * Gets the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Gets the type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Gets the data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}