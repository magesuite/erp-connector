<?php
namespace MageSuite\ErpConnector\Model\Command\Provider;

class SaveConnectors
{
    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorResolver
     */
    protected $connectorResolver;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\ConnectorFactory
     */
    protected $connectorFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface
     */
    protected $connectorRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\ConnectorConfigurationFactory
     */
    protected $connectorConfigurationFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface
     */
    protected $connectorConfigurationRepository;

    public function __construct(
        \MageSuite\ErpConnector\Model\ConnectorResolver $connectorResolver,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \MageSuite\ErpConnector\Model\Data\ConnectorFactory $connectorFactory,
        \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface $connectorRepository,
        \MageSuite\ErpConnector\Model\Data\ConnectorConfigurationFactory $connectorConfigurationFactory,
        \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface $connectorConfigurationRepository
    ) {
        $this->connectorResolver = $connectorResolver;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->connectorFactory = $connectorFactory;
        $this->connectorRepository = $connectorRepository;
        $this->connectorConfigurationFactory = $connectorConfigurationFactory;
        $this->connectorConfigurationRepository = $connectorConfigurationRepository;
    }

    public function execute($providerId, $formData)
    {
        $connectorsConfiguration = $this->connectorResolver->getConnectorConfigurations();

        foreach ($connectorsConfiguration as $connectorType => $connectorConfigurationFields) {
            $this->saveConnectors($providerId, $connectorType, $connectorConfigurationFields, $formData);
        }
    }

    protected function saveConnectors($providerId, $connectorType, $connectorConfigurationFields, $formData) //phpcs:ignore
    {
        $criteria = $this->searchCriteriaBuilderFactory
            ->create()
            ->addFilter('provider_id', $providerId)
            ->addFIlter('type', $connectorType);

        $connectors = $this->connectorRepository->getList($criteria->create());

        $connectorsData = [];

        if (isset($formData[$connectorType][$connectorType])) {

            foreach ($formData[$connectorType][$connectorType] as $connectorData) {

                if (isset($connectorData['connector_id'])) {
                    $connectorsData[$connectorData['connector_id']] = $connectorData;
                    continue;
                }

                $connector = $this->connectorFactory->create();
                $connectorData['provider_id'] = $providerId;

                $connector
                    ->setProviderId($providerId)
                    ->setName($connectorData['name'])
                    ->setType($connectorType);

                $this->connectorRepository->save($connector);

                foreach ($connectorConfigurationFields as $configurationField) {
                    $configurationItem = $this->connectorConfigurationFactory->create();
                    $configurationItem
                        ->setProviderId($providerId)
                        ->setConnectorId($connector->getId())
                        ->setName($configurationField)
                        ->setValue($connectorData[$configurationField]);

                    $this->connectorConfigurationRepository->save($configurationItem);
                }
            }
        }

        foreach ($connectors->getItems() as $connector) {

            if (!isset($connectorsData[$connector->getId()])) {
                $this->connectorRepository->delete($connector);
                continue;
            }

            $connectorData = $connectorsData[$connector->getId()];
            $connector->setData($connectorData);

            $this->connectorRepository->save($connector);

            foreach ($connectorConfigurationFields as $configurationField) {
                try {
                    $configurationItem = $this->connectorConfigurationRepository->getItemByConnectorIdAndName($connector->getId(), $configurationField);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $configurationItem = $this->connectorConfigurationFactory->create();
                    $configurationItem
                        ->setProviderId($providerId)
                        ->setConnectorId($connector->getId());
                }

                $configurationItem
                    ->setName($configurationField)
                    ->setValue($connectorData[$configurationField]);

                $this->connectorConfigurationRepository->save($configurationItem);
            }
        }
    }
}
