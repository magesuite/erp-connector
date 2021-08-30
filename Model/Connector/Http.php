<?php

namespace MageSuite\ErpConnector\Model\Connector;

class Http extends Connector implements ConnectorInterface
{
    const CONNECTOR_TYPE = 'http';

    /**
     * @var \MageSuite\ErpConnector\Model\Client\Http
     */
    protected $httpClient;

    public function __construct(\MageSuite\ErpConnector\Model\Client\Http $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function isApplicable($type)
    {
        return $type == self::CONNECTOR_TYPE;
    }

    public function getClient()
    {
        return $this->httpClient;
    }
}
