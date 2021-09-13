<?php
namespace MageSuite\ErpConnector\Ui\DataProvider\Form;

class Provider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array|null
     */
    protected $loadedData = null;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Provider\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface
     */
    protected $connectorRepository;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface
     */
    protected $connectorConfigurationRepository;

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface
     */
    protected $providerAdditionalConfigurationRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorResolver
     */
    protected $connectorResolver;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \MageSuite\ErpConnector\Model\ResourceModel\Provider\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface $connectorRepository,
        \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface $connectorConfigurationRepository,
        \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface $providerAdditionalRepository,
        \MageSuite\ErpConnector\Model\ConnectorResolver $connectorResolver,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->connectorRepository = $connectorRepository;
        $this->connectorConfigurationRepository = $connectorConfigurationRepository;
        $this->providerAdditionalConfigurationRepository = $providerAdditionalRepository;
        $this->connectorResolver = $connectorResolver;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $connectorConfigurationFields = $this->connectorResolver->getConnectorConfigurationFields();

        /** @var \MageSuite\ErpConnector\Model\Data\Provider $provider */
        foreach ($items as $provider) {
            $providerId = $provider->getId();

            $general = $provider->getData();
            $general['additional_configuration'] = $this->getProviderAdditionalConfigurationByProviderId($providerId);

            $this->loadedData[$providerId] = [
                'general' => $general,
                'connectors' => []
            ];

            $connectors = $this->connectorRepository->getByProviderId($providerId);
            $connectorConfigurations = $this->prepareConnectorConfigurations($providerId);

            /** @var \MageSuite\ErpConnector\Model\Data\Connector $connector */
            foreach ($connectors as $connector) {

                $connectorData = $connectorConfigurations[$connector->getId()] ?? [];
                $connectorData = $this->prepareConnectorData($connector, $connectorData, $connectorConfigurationFields);

                $connector->addData($connectorData);

                $this->loadedData[$providerId]['connectors'][$connector->getType()][$connector->getType()][] = $connector->getData();
            }
        }

        $data = $this->dataPersistor->get('erp_connector_provider');

        if (!empty($data)) {
            $provider = $this->collection->getNewEmptyItem();
            $provider->setData($data);
            $this->loadedData[$provider->getId()] = $provider->getData();
            $this->dataPersistor->clear('erp_connector_provider');
        }

        return $this->loadedData;
    }

    protected function prepareConnectorConfigurations($providerId)
    {
        $connectorConfigurations = $this->connectorConfigurationRepository->getByProviderId($providerId);

        $result = [];

        foreach ($connectorConfigurations as $configurationItem) {
            $configurationItemData = $configurationItem->getData();
            $result[$configurationItemData['connector_id']][$configurationItemData['name']] = $configurationItemData['value'];
        }

        return $result;
    }

    protected function getProviderAdditionalConfigurationByProviderId($providerId)
    {
        $result = ['additional_configuration' => []];
        $additionalConfiguration = $this->providerAdditionalConfigurationRepository->getByProviderId($providerId);

        if (empty($additionalConfiguration)) {
            return $result;
        }

        foreach ($additionalConfiguration as $configuration) {
            $result['additional_configuration'][] = $configuration->getData();
        }

        return $result;
    }

    private function prepareConnectorData($connector, $connectorData, $connectorConfigurationFields)
    {
        foreach ($connectorData as $key => $value) {
            $modifierClass = $connectorConfigurationFields[$connector->getType()][$key]['modifier_class'] ?? null;

            if (empty($modifierClass)) {
                continue;
            }

            $connectorData[$key] = $modifierClass->getDataForDataProvider($value);
        }

        return $connectorData;
    }
}
