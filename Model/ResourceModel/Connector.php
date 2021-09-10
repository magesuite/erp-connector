<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class Connector extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_connector', \MageSuite\ErpConnector\Model\Data\Connector::ID);
    }
}
