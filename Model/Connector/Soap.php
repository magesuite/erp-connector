<?php

namespace MageSuite\ErpConnector\Model\Connector;

class Soap extends Connector implements ConnectorInterface
{
    const CONNECTOR_TYPE = 'soap';

    /**
     * @var \MageSuite\ErpConnector\Model\Client\Soap
     */
    protected $soapClient;

    public function __construct(\MageSuite\ErpConnector\Model\Client\Soap $soapClient)
    {
        $this->soapClient = $soapClient;
    }

    public function isApplicable($type)
    {
        return $type == self::CONNECTOR_TYPE;
    }

    public function getClient()
    {
        return $this->soapClient;
    }
}
