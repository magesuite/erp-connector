<?php

namespace MageSuite\ErpConnector\Model\Modifier;

class SensitiveModifier
{
    protected \MageSuite\ErpConnector\Model\Data\VaultItemFactory$vaultItemFactory;
    protected \MageSuite\ErpConnector\Api\VaultRepositoryInterface $vaultRepository;
    protected \MageSuite\ErpConnector\Model\ResourceModel\GetRawConnectorConfigurationValue $getRawConnectorConfigurationValue;
    protected \MageSuite\ErpConnector\Model\ResourceModel\VaultItem $vaultItemResourceModel;

    public function __construct(
        \MageSuite\ErpConnector\Model\Data\VaultItemFactory $vaultItemFactory,
        \MageSuite\ErpConnector\Api\VaultRepositoryInterface $vaultRepository,
        \MageSuite\ErpConnector\Model\ResourceModel\GetRawConnectorConfigurationValue $getRawConnectorConfigurationValue,
        \MageSuite\ErpConnector\Model\ResourceModel\VaultItem $vaultItemResourceModel
    ) {
        $this->vaultItemFactory = $vaultItemFactory;
        $this->vaultRepository = $vaultRepository;
        $this->getRawConnectorConfigurationValue = $getRawConnectorConfigurationValue;
        $this->vaultItemResourceModel = $vaultItemResourceModel;
    }

    public function isSaveAllowed($connectorConfigurationItem, $isSaveAllowed)
    {
        if ($connectorConfigurationItem->getValue() == \MageSuite\ErpConnector\Model\Data\VaultItem::VAULT_VALUE_PLACEHOLDER) {
            return false;
        }

        return $isSaveAllowed;
    }

    public function beforeSave($connectorConfigurationItem)
    {
        if (empty($connectorConfigurationItem->getValue())) {
            return null;
        }

        if ($connectorConfigurationItem->getValue() == \MageSuite\ErpConnector\Model\Data\VaultItem::VAULT_VALUE_PLACEHOLDER) {
            return null;
        }

        $vaultItem = $this->vaultItemFactory->create();
        $encryptedValue = $vaultItem->encryptValue($connectorConfigurationItem->getValue());

        $currentIdentifier = $this->getRawConnectorConfigurationValue->execute($connectorConfigurationItem->getConnectorId(), $connectorConfigurationItem->getName());

        if ($currentIdentifier) {
            $this->vaultItemResourceModel->load($vaultItem, $currentIdentifier, 'identifier');
            $vaultItem->setValue($encryptedValue);
            $this->vaultRepository->save($vaultItem);

            return $currentIdentifier;
        }

        $identifier = $connectorConfigurationItem->getConnectorId() . uniqid();

        $vaultItem
            ->setConnectorId($connectorConfigurationItem->getConnectorId())
            ->setIdentifier($identifier)
            ->setValue($encryptedValue);

        $this->vaultRepository->save($vaultItem);

        return $identifier;
    }

    public function afterLoad($value)
    {
        if (empty($value)) {
            return $value;
        }

        return $this->vaultRepository->getDecryptedValueByIdentifier($value);
    }

    public function getDataForDataProvider($value)
    {
        return \MageSuite\ErpConnector\Model\Data\VaultItem::VAULT_VALUE_PLACEHOLDER;
    }
}
