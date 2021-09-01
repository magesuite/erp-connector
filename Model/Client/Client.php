<?php

namespace MageSuite\ErpConnector\Model\Client;

class Client extends \Magento\Framework\DataObject
{
    public function checkConnection()
    {
        throw new \MageSuite\ErpConnector\Exception\ConnectionFailed(__('Not possible to test connection.'));
    }
}
