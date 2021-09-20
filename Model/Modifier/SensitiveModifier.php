<?php

namespace MageSuite\ErpConnector\Model\Modifier;

class SensitiveModifier
{
    /**
     * @var \MageSuite\ErpConnector\Model\Data\VaultItemFactory
     */
    protected $vaultItemFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\VaultRepositoryInterface
     */
    protected $vaultRepository;

    public function __construct(
        \MageSuite\ErpConnector\Model\Data\VaultItemFactory $vaultItemFactory,
        \MageSuite\ErpConnector\Api\VaultRepositoryInterface $vaultRepository
    ) {
        $this->vaultItemFactory = $vaultItemFactory;
        $this->vaultRepository = $vaultRepository;
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
        if ($connectorConfigurationItem->getValue() == \MageSuite\ErpConnector\Model\Data\VaultItem::VAULT_VALUE_PLACEHOLDER) {
            return null;
        }

        $vaultItem = $this->vaultItemFactory->create();

        $identifier = $connectorConfigurationItem->getConnectorId() . uniqid();
        $encryptedValue = $vaultItem->encryptValue($connectorConfigurationItem->getValue());

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
