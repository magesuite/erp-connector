<?php
namespace MageSuite\ErpConnector\Api\Data;

/**
 * @api
 */
interface ConnectorInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const CONNECTOR_ID = 'connector_id';
    public const PROVIDER_ID = 'provider_id';
    public const NAME = 'name';
    public const TYPE = 'type';
    /**#@-*/

    const CACHE_TAG = 'erp_connector_connector';
    const EVENT_PREFIX = 'erp_connector_connector';

    /**
     * @return int|null
     */
    public function getConnectorId();

    /**
     * @return string|null
     */
    public function getProviderId();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string|null
     */
    public function getType();

    /**
     * @param int $id
     * @return ConnectorInterface
     */
    public function setConnectorId($id);

    /**
     * @param int $id
     * @return ConnectorInterface
     */
    public function setProviderId($id);

    /**
     * @param string $name
     * @return ConnectorInterface
     */
    public function setName($name);

    /**
     * @param $type
     * @return ConnectorInterface
     */
    public function setType($type);
}
