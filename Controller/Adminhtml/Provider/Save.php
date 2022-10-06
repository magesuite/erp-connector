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
     * @var \MageSuite\ErpConnector\Model\Data\ProviderFactory
     */
    protected $providerFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\Command\Provider\SaveAdditionalConfigurations
     */
    protected $saveAdditionalConfigurations;

    /**
     * @var \MageSuite\ErpConnector\Model\Command\Provider\SaveConnectors
     */
    protected $saveConnectors;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Event\Manager $eventManager,
        \MageSuite\ErpConnector\Model\Data\ProviderFactory $providerFactory,
        \MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository,
        \MageSuite\ErpConnector\Model\Command\Provider\SaveAdditionalConfigurations $saveAdditionalConfigurations,
        \MageSuite\ErpConnector\Model\Command\Provider\SaveConnectors $saveConnectors,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->eventManager = $eventManager;
        $this->providerFactory = $providerFactory;
        $this->providerRepository = $providerRepository;
        $this->saveAdditionalConfigurations = $saveAdditionalConfigurations;
        $this->saveConnectors = $saveConnectors;
        $this->logger = $logger;

        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue('general');

        if ($data) {
            if (empty($data['id'])) {
                $data['id'] = null;
            }

            $id = (int)$this->getRequest()->getParam('id');
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
                    $this->saveAdditionalConfigurations->execute($provider->getId(), $formData);

                    $formData = $this->getRequest()->getParam('connectors');
                    $this->saveConnectors->execute($provider->getId(), $formData);

                    $this->_eventManager->dispatch(
                        'erp_connector_full_save_after',
                        ['controller' => $this, 'provider' => $provider]
                    );
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }

                $this->messageManager->addSuccessMessage(__('You saved the provider.'));
                $this->dataPersistor->clear('erp_connector_provider');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $provider->getId()]);
                }

                return $resultRedirect->setPath('*/*/index');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the provider.'));
            }

            $this->dataPersistor->set('erp_connector_provider', $data);

            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
