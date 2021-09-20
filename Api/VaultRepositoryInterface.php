<?php
namespace MageSuite\ErpConnector\Api;

interface VaultRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Model\Data\VaultItem $vaultItem
     * @return \MageSuite\ErpConnector\Model\Data\VaultItem
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($vaultItem);

    /**
     * @param $identifier
     * @return string|null
     */
    public function getDecryptedValueByIdentifier($identifier);

    /**
     * @param \MageSuite\ErpConnector\Model\Data\VaultItem $vaultItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($vaultItem);

    /**
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
