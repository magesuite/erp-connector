<?php
namespace MageSuite\ErpConnector\Controller\Adminhtml\Provider;

class Index extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'MageSuite_ErpConnector::erp_connector';

    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('MageSuite_ErpConnector::erp_connector');
        $resultPage->addBreadcrumb(__('Providers'), __('Providers'));
        $resultPage->getConfig()->getTitle()->prepend(__('Providers'));

        return $resultPage;
    }
}
