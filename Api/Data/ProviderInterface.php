<?php
namespace MageSuite\ErpConnector\Api\Data;

/**
 * @api
 */
interface ProviderInterface extends \Magento\Framework\Api\ExtensibleDataInterface, \Magento\Framework\DataObject\IdentityInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const PROVIDER_ID = 'provider_id';
    public const NAME = 'name';
    public const CODE = 'code';
    public const EMAIL = 'email';
    /**#@-*/

    const CACHE_TAG = 'erp_connector_provider';
    const EVENT_PREFIX = 'erp_connector_provider';

    /**
     * @return int|null
     */
    public function getProviderId();

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
     * @param int $providerId
     * @return ProviderInterface
     */
    public function setProviderId($providerId);

    /**
     * @param $name
     * @return ProviderInterface
     */
    public function setName($name);

    /**
     * @param string|null $email
     * @return ProviderInterface
     */
    public function setEmail($email);

    /**
     * @param $code
     * @return ProviderInterface
     */
    public function setCode($code);

    /**
     * @return \MageSuite\ErpConnector\Api\Data\ProviderExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ProviderExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\MageSuite\ErpConnector\Api\Data\ProviderExtensionInterface $extensionAttributes);
}
