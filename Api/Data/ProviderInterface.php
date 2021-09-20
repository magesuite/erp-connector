<?php
namespace MageSuite\ErpConnector\Api\Data;

interface ProviderInterface extends \Magento\Framework\DataObject\IdentityInterface, \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const CODE = 'code';
    const EMAIL = 'email';
    /**#@-*/

    const CACHE_TAG = 'erp_connector_provider';
    const EVENT_PREFIX = 'erp_connector_provider';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string|null
     */
    public function getCode();

    /**
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * @param $name
     * @return self
     */
    public function setName($name);

    /**
     * @param string|null $email
     * @return self
     */
    public function setEmail($email);

    /**
     * @param $code
     * @return self
     */
    public function setCode($code);

    /**
     * @return \MageSuite\ErpConnector\Api\Data\ProviderExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ProviderExtensionInterface $extensionAttributes
     * @return self
     */
    public function setExtensionAttributes($extensionAttributes);
}
