<?php
namespace MageSuite\ErpConnector\Controller\Adminhtml\Provider;

class Edit extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'MageSuite_ErpConnector::erp_connector';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->providerRepository = $providerRepository;
        $this->registry = $registry;

        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            $provider = $this->providerRepository->getById($id);

            if (!$provider->getId()) {
                $this->messageManager->addErrorMessage(__('This provider no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }

            $this->registry->register('current_provider', $provider);
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Provider') : __('New Provider'),
            $id ? __('Edit Provider') : __('New Provider')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Providers'));
        $resultPage->getConfig()->getTitle()
            ->prepend($id ?  __('Edit Provider') : __('New Provider'));

        return $resultPage;
    }
}
