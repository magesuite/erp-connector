<?php

namespace MageSuite\ErpConnector\Model\Connector;

class Ftp extends Connector implements ConnectorInterface
{
    const CONNECTOR_TYPE = 'ftp';

    /**
     * @var \MageSuite\ErpConnector\Model\Client\Ftp
     */
    protected $ftpClient;

    public function __construct(\MageSuite\ErpConnector\Model\Client\Ftp $ftpClient)
    {
        $this->ftpClient = $ftpClient;
    }

    public function isApplicable($type)
    {
        return $type == self::CONNECTOR_TYPE;
    }

    public function getClient()
    {
        return $this->ftpClient;
    }
}
