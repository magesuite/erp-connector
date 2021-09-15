<?php
namespace MageSuite\ErpConnector\Model\Config\Backend\ProviderAdditionalConfiguration;

class ConfigurationCodes extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * @var \MageSuite\ErpConnector\Model\Command\ProviderAdditionalConfiguration\ValidateConfigurationCodes
     */
    protected $validateConfigurationCodes;

    public function __construct( //phpcs:ignore
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \MageSuite\ErpConnector\Model\Command\ProviderAdditionalConfiguration\ValidateConfigurationCodes $validateConfigurationCodes,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data,
            $serializer
        );

        $this->validateConfigurationCodes = $validateConfigurationCodes;
    }

    public function beforeSave()
    {
        $value = $this->getValue();

        $errorMessages = $this->validateConfigurationCodes->execute($value);

        if (!empty($errorMessages)) {
            throw new \Exception('<br/>' . implode('<br/>', $errorMessages)); //phpcs:ignore
        }

        return parent::beforeSave();
    }
}
