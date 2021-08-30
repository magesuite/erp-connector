<?php

namespace MageSuite\ErpConnector\Model\Connector;

interface ConnectorInterface
{
    /**
     * @param $type
     * @return bool
     */
    public function isApplicable($type);

    /**
     * @return \MageSuite\ErpConnector\Model\Client\ClientInterface
     */
    public function getClient();
}
