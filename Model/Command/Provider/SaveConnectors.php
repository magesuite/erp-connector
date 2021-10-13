<?php
namespace MageSuite\ErpConnector\Model\Command\Provider;

class SaveConnectors
{
    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorResolver
     */
    protected $connectorResolver;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

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
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \MageSuite\ErpConnector\Model\Data\ConnectorFactory $connectorFactory,
        \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface $connectorRepository,
        \MageSuite\ErpConnector\Model\Data\ConnectorConfigurationFactory $connectorConfigurationFactory,
        \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface $connectorConfigurationRepository
    ) {
        $this->connectorResolver = $connectorResolver;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->connectorFactory = $connectorFactory;
        $this->connectorRepository = $connectorRepository;
        $this->connectorConfigurationFactory = $connectorConfigurationFactory;
        $this->connectorConfigurationRepository = $connectorConfigurationRepository;
    }

    public function execute($providerId, $formData)
    {
        $connectorsConfigurationFields = $this->connectorResolver->getConnectorConfigurationFields();

        foreach ($connectorsConfigurationFields as $connectorType => $connectorConfigurationFields) {
            $this->saveConnectors($providerId, $connectorType, $connectorConfigurationFields, $formData);
        }
    }

    protected function saveConnectors($providerId, $connectorType, $connectorConfigurationFields, $formData) //phpcs:ignore
    {
        $criteria = $this->searchCriteriaBuilder
            ->addFilter('provider_id', $providerId)
            ->addFIlter('type', $connectorType);

        $connectors = $this->connectorRepository->getList($criteria->create());

        $connectorsData = [];
        $connectorItemsData = $formData[$connectorType][$connectorType] ?? [];

        foreach ($connectorItemsData as $connectorData) {

            if (isset($connectorData['id'])) {
                $connectorsData[$connectorData['id']] = $connectorData;
                continue;
            }

            $connector = $this->connectorFactory->create();
            $connectorData['provider_id'] = $providerId;

            $connector
                ->setProviderId($providerId)
                ->setName($connectorData['name'])
                ->setType($connectorType);

            $this->connectorRepository->save($connector);

            foreach ($connectorConfigurationFields as $configurationField => $fieldConfig) {
                $modifierClass = $fieldConfig['modifier_class'] ?? null;

                $configurationItemValue = $connectorData[$configurationField] ?? null;

                $configurationItem = $this->connectorConfigurationFactory->create();
                $configurationItem
                    ->setProviderId($providerId)
                    ->setConnectorId($connector->getId())
                    ->setModifierClass($modifierClass)
                    ->setName($configurationField)
                    ->setValue($configurationItemValue);

                $this->connectorConfigurationRepository->save($configurationItem);
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

            foreach ($connectorConfigurationFields as $configurationField => $fieldConfig) {
                $modifierClass = $fieldConfig['modifier_class'] ?? null;

                $configurationItem = $this->connectorConfigurationRepository->getItemByConnectorIdAndName($connector->getId(), $configurationField);

                if ($configurationItem === null) {
                    $configurationItem = $this->connectorConfigurationFactory->create();
                    $configurationItem
                        ->setProviderId($providerId)
                        ->setConnectorId($connector->getId());
                }

                $configurationItemValue = $connectorData[$configurationField] ?? null;

                $configurationItem
                    ->setModifierClass($modifierClass)
                    ->setName($configurationField)
                    ->setValue($configurationItemValue);

                $this->connectorConfigurationRepository->save($configurationItem);
            }
        }
    }
}
