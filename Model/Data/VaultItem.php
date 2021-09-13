<?php
namespace MageSuite\ErpConnector\Model\Data;

class VaultItem extends \Magento\Framework\Model\AbstractModel
{
    const VAULT_VALUE_PLACEHOLDER = 'vault_item_placeholder';

    const ID = 'id';
    const CONNECTOR_ID = 'connector_id';
    const IDENTIFIER = 'identifier';
    const VALUE = 'value';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->encryptor = $encryptor;
    }

    protected function _construct()
    {
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\VaultItem::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getConnectorId()
    {
        return $this->getData(self::CONNECTOR_ID);
    }

    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    public function getValue()
    {
        return $this->getDataByKey(self::VALUE);
    }

    public function setId($id)
    {
        $this->setData(self::ID, $id);
        return $this;
    }

    public function setConnectorId($connectorId)
    {
        $this->setData(self::CONNECTOR_ID, $connectorId);
        return $this;
    }

    public function setIdentifier($identifier)
    {
        $this->setData(self::IDENTIFIER, $identifier);
        return $this;
    }

    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    public function encryptValue($value)
    {
        return $this->encryptor->encrypt($value);
    }

    public function decryptValue($value)
    {
        return $this->encryptor->decrypt($value);
    }
}
