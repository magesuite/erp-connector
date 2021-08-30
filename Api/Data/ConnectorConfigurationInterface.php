<?php
namespace MageSuite\ErpConnector\Api\Data;

/**
 * @api
 */
interface ConnectorConfigurationInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID = 'entity_id';
    public const PROVIDER_ID = 'provider_id';
    public const CONNECTOR_ID = 'connector_id';
    public const NAME = 'name';
    public const VALUE = 'value';
    /**#@-*/

    const CACHE_TAG = 'erp_connector_connector_configuration';
    const EVENT_PREFIX = 'erp_connector_connector_configuration';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getProviderId();

    /**
     * @return int|null
     */
    public function getConnectorId();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string|null
     */
    public function getValue();

    /**
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * @param int $id
     * @return self
     */
    public function setProviderId($id);

    /**
     * @param int $id
     * @return self
     */
    public function setConnectorId($id);

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @param $value
     * @return self
     */
    public function setValue($value);
}
