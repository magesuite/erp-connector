<?php

namespace MageSuite\ErpConnector\Model\Modifier;

class ArrayModifier
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(\Magento\Framework\Serialize\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function isSaveAllowed($connectorConfigurationItem, $isSaveAllowed)
    {
        return $isSaveAllowed;
    }

    public function beforeSave($connectorConfigurationItem)
    {
        $value = $connectorConfigurationItem->getValue();

        if (!is_array($value) || empty($value)) {
            return null;
        }

        return $this->serializer->serialize($value);
    }

    public function afterLoad($value)
    {
        if (empty($value)) {
            return $value;
        }

        return $this->serializer->unserialize($value);
    }

    public function getDataForDataProvider($value)
    {
        return $value;
    }
}
