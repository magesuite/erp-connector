<?php
namespace MageSuite\ErpConnector\Model;

class VaultRepository implements \MageSuite\ErpConnector\Api\VaultRepositoryInterface
{
    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\VaultItem
     */
    protected $resourceModel;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\VaultItemFactory
     */
    protected $vaultItemFactory;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \MageSuite\ErpConnector\Model\ResourceModel\VaultItem $resourceModel,
        \MageSuite\ErpConnector\Model\Data\VaultItemFactory $vaultItemFactory,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->resourceModel = $resourceModel;
        $this->vaultItemFactory = $vaultItemFactory;
        $this->logger = $logger;
    }

    public function save($connector)
    {
        try {
            $this->resourceModel->save($connector);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $connector;
    }

    public function getDecryptedValueByIdentifier($identifier)
    {
        $vaultItem = $this->vaultItemFactory->create();
        $this->resourceModel->load($vaultItem, $identifier, 'identifier');

        if (!$vaultItem->getId()) {
            $this->logger->error(sprintf('The vault item with the "%s" identifier doesn\'t exist.', $identifier));
            return null;
        }

        $value = $vaultItem->getValue();
        return $value ? $vaultItem->decryptValue($value) : null;
    }

    public function delete($vaultItem)
    {
        try {
            $this->resourceModel->delete($vaultItem);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
