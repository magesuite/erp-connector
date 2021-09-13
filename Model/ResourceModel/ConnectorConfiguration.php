<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class ConnectorConfiguration extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_connector_configuration', \MageSuite\ErpConnector\Model\Data\ConnectorConfiguration::ID);
    }

    protected function _beforeSave($object)
    {
        $modifierClass = $object->getModifierClass();

        if (empty($modifierClass)) {
            return parent::_beforeSave($object);
        }

        $object->setModifierClass(get_class($modifierClass));
        return parent::_beforeSave($object);
    }
}
