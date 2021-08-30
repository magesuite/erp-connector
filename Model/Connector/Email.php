<?php

namespace MageSuite\ErpConnector\Model\Connector;

class Email extends Connector implements ConnectorInterface
{
    const CONNECTOR_TYPE = 'email';

    /**
     * @var \MageSuite\ErpConnector\Model\Client\Email
     */
    protected $emailClient;

    public function __construct(\MageSuite\ErpConnector\Model\Client\Email $emailClient)
    {
        $this->emailClient = $emailClient;
    }

    public function isApplicable($type)
    {
        return $type == self::CONNECTOR_TYPE;
    }

    public function getClient()
    {
        return $this->emailClient;
    }
}
