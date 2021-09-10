<?php

namespace MageSuite\ErpConnector\Model;

class Connector
{
    /**
     * @var \MageSuite\ErpConnector\Model\Client\ClientInterface
     */
    protected $client;

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }
}
