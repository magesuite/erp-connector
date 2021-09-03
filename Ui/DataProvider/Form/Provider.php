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

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \MageSuite\ErpConnector\Model\ResourceModel\Provider\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface $connectorRepository,
        \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface $connectorConfigurationRepository,
        \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface $providerAdditionalRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->connectorRepository = $connectorRepository;
        $this->connectorConfigurationRepository = $connectorConfigurationRepository;
        $this->providerAdditionalConfigurationRepository = $providerAdditionalRepository;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        /** @var \MageSuite\ErpConnector\Api\Data\ProviderInterface $provider */
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

            /** @var \MageSuite\ErpConnector\Api\Data\ConnectorInterface $connector */
            foreach ($connectors as $connector) {

                if (isset($connectorConfigurations[$connector->getConnectorId()])) {
                    $connector->addData($connectorConfigurations[$connector->getConnectorId()]);
                }

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
}
