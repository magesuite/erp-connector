<?php

namespace MageSuite\ErpConnector\Model\Connector;

class Sftp extends Connector implements ConnectorInterface
{
    const CONNECTOR_TYPE = 'sftp';

    /**
     * @var \MageSuite\ErpConnector\Model\Client\Sftp
     */
    protected $sftpClient;

    public function __construct(\MageSuite\ErpConnector\Model\Client\Sftp $sftpClient)
    {
        $this->sftpClient = $sftpClient;
    }

    public function isApplicable($type)
    {
        return $type == self::CONNECTOR_TYPE;
    }

    public function getClient()
    {
        return $this->sftpClient;
    }
}
