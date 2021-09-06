<?php
namespace MageSuite\ErpConnector\Controller\Adminhtml\Provider;

class Save extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'MageSuite_ErpConnector::erp_connector';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ProviderInterfaceFactory
     */
    protected $providerFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterfaceFactory
     */
    protected $providerAdditionalConfigurationFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface
     */
    protected $providerAdditionalConfigurationRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorPool
     */
    protected $connectorPool;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ConnectorInterfaceFactory
     */
    protected $connectorFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface
     */
    protected $connectorRepository;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterfaceFactory
     */
    protected $connectorConfigurationFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface
     */
    protected $connectorConfigurationRepository;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Event\Manager $eventManager,
        \MageSuite\ErpConnector\Api\Data\ProviderInterfaceFactory $providerFactory,
        \MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository,
        \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterfaceFactory $providerAdditionalConfigurationFactory,
        \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface $providerAdditionalConfigurationRepository,
        \MageSuite\ErpConnector\Model\ConnectorPool $connectorPool,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \MageSuite\ErpConnector\Api\Data\ConnectorInterfaceFactory $connectorFactory,
        \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface $connectorRepository,
        \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterfaceFactory $connectorConfigurationFactory,
        \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface $connectorConfigurationRepository,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->eventManager = $eventManager;
        $this->providerFactory = $providerFactory;
        $this->providerRepository = $providerRepository;
        $this->providerAdditionalConfigurationFactory = $providerAdditionalConfigurationFactory;
        $this->providerAdditionalConfigurationRepository = $providerAdditionalConfigurationRepository;
        $this->connectorPool = $connectorPool;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->connectorFactory = $connectorFactory;
        $this->connectorRepository = $connectorRepository;
        $this->connectorConfigurationFactory = $connectorConfigurationFactory;
        $this->connectorConfigurationRepository = $connectorConfigurationRepository;
        $this->logger = $logger;

        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue('general');

        if ($data) {
            if (empty($data['provider_id'])) {
                $data['provider_id'] = null;
            }

            $id = (int)$this->getRequest()->getParam('provider_id');
            $provider = $this->providerFactory->create();

            if ($id) {
                try {
                    $provider = $this->providerRepository->getById($id);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This provider no longer exists.'));
                    return $resultRedirect->setPath('*/*/index');
                }
            }

            $provider->addData($data);

            try {
                $this->providerRepository->save($provider);

                try {
                    $this->eventManager->dispatch('erp_connector_full_save_before', ['provider' => $provider]);

                    $formData = $data['additional_configuration']['additional_configuration'] ?? [];
                    $this->processProviderAdditionalConfig($provider, $formData);

                    $this->processConnectors($provider);

                    $this->eventManager->dispatch('erp_connector_full_save_after', ['provider' => $provider]);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }

                $this->messageManager->addSuccessMessage(__('You saved the provider.'));
                $this->dataPersistor->clear('erp_connector_provider');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['provider_id' => $provider->getProviderId()]);
                }

                return $resultRedirect->setPath('*/*/index');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the provider.'));
            }

            $this->dataPersistor->set('erp_connector_provider', $data);

            return $resultRedirect->setPath('*/*/edit', ['provider_id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function processProviderAdditionalConfig($provider, $formData)
    {
        $providerAdditionalConfiguration = $this->providerAdditionalConfigurationRepository->getByProviderId($provider->getProviderId());

        $configsData = [];

        if (!empty($formData)) {
            foreach ($formData as $configData) {
                $configData['provider_id'] = $provider->getProviderId();

                if (isset($configData['entity_id'])) {
                    $configsData[$configData['entity_id']] = $configData;
                } else {
                    $config = $this->providerAdditionalConfigurationFactory->create();
                    $config->setData($configData);
                    $this->providerAdditionalConfigurationRepository->save($config);
                }
            }
        }

        foreach ($providerAdditionalConfiguration as $config) {

            if (isset($configsData[$config->getId()])) {
                $config->setData($configsData[$config->getId()]);
                $this->providerAdditionalConfigurationRepository->save($config);
            } else {
                $this->providerAdditionalConfigurationRepository->delete($config);
            }
        }
    }

    protected function processConnectors($provider)
    {
        $connectorsConfiguration = $this->connectorPool->getConnectorConfigurations();

        foreach ($connectorsConfiguration as $connectorType => $connectorConfigurationFields) {
            $this->saveConnectors($provider->getProviderId(), $connectorType, $connectorConfigurationFields);
        }
    }

    protected function saveConnectors($providerId, $connectorType, $connectorConfigurationFields)
    {
        $criteria = $this->searchCriteriaBuilderFactory
            ->create()
            ->addFilter('provider_id', $providerId)
            ->addFIlter('type', $connectorType);

        $connectors = $this->connectorRepository->getList($criteria->create());
        $formData = $this->getRequest()->getParam('connectors');

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
