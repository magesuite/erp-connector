<?php
namespace MageSuite\ErpConnector\Controller\Adminhtml\Provider;

class Delete extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'MageSuite_ErpConnector::erp_connector';

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository
    ) {
        $this->providerRepository = $providerRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->providerRepository->deleteById($id);
                $this->messageManager->addSuccess(__('You deleted the provider.'));

                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addError(__('We can\'t find a provider to delete.'));

        return $resultRedirect->setPath('*/*/index');
    }
}
