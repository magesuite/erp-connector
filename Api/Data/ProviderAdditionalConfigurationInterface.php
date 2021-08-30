<?php
namespace MageSuite\ErpConnector\Api\Data;

interface ProviderAdditionalConfigurationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    public const ID = 'entity_id';
    public const PROVIDER_ID = 'provider_id';
    public const KEY = 'key';
    public const VALUE = 'value';

    const CACHE_TAG = 'erp_connector_provider_additional_configuration';
    const EVENT_PREFIX = 'erp_connector_provider_additional_configuration';

    /**
     * @return int|null
     */
    public function getProviderId();

    /**
     * @return string|null
     */
    public function getKey();

    /**
     * @return string|null
     */
    public function getValue();

    /**
     * @param int $id
     * @return self
     */
    public function setProviderId($id);

    /**
     * @param $key
     * @return self
     */
    public function setKey($key);

    /**
     * @param $value
     * @return self
     */
    public function setValue($value);
}
