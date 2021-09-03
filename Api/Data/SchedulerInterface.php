<?php
namespace MageSuite\ErpConnector\Api\Data;

/**
 * @api
 */
interface SchedulerInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const SCHEDULER_ID = 'scheduler_id';
    public const PROVIDER_ID = 'provider_id';
    public const CONNECTOR_ID = 'connector_id';
    public const NAME = 'name';
    public const TYPE = 'type';
    public const CRON_EXPRESSION = 'cron_expression';
    public const TEMPLATE = 'template';
    public const FILE_NAME = 'file_name';
    public const ADDITIONAL_TEMPLATE = 'additional_template';
    /**#@-*/

    const CACHE_TAG = 'erp_connector_scheduler';
    const EVENT_PREFIX = 'erp_connector_scheduler';

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
    public function getType();

    /**
     * @return string|null
     */
    public function getCronExpression();

    /**
     * @return string|null
     */
    public function getTemplate();

    /**
     * @return string|null
     */
    public function getFileName();

    /**
     * @return string|null
     */
    public function getAdditionalTemplate();

    /**
     * @param int $id
     * @return self
     */
    public function setId($id);

    /**
     * @param int $providerId
     * @return self
     */
    public function setProviderId($providerId);

    /**
     * @param int $connectorId
     * @return self
     */
    public function setConnectorId($connectorId);

    /**
     * @param $name
     * @return self
     */
    public function setName($name);

    /**
     * @param $type
     * @return self
     */
    public function setType($type);

    /**
     * @param $cronExpression
     * @return self
     */
    public function setCronExpression($cronExpression);

    /**
     * @param $template
     * @return self
     */
    public function setTemplate($template);

    /**
     * @param $fileName
     * @return self
     */
    public function setFileName($fileName);

    /**
     * @param $template
     * @return self
     */
    public function setAdditionalTemplate($template);
}
