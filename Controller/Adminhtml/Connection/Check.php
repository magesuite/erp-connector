<?php
namespace MageSuite\ErpConnector\Controller\Adminhtml\Connection;

class Check extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'MageSuite_ErpConnector::erp_connector';
    
    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorResolver
     */
    protected $connectorResolver;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageSuite\ErpConnector\Model\ConnectorResolver $connectorResolver
    ) {
        parent::__construct($context);

        $this->connectorResolver = $connectorResolver;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        $connectorConfiguration = $this->getRequest()->getParams();

        if (!isset($connectorConfiguration['type'])) {
            $response = [
                'status' => 'error',
                'code' => 'missing_connector_id',
                'message' => __('Please save the provider with this connector first.')
            ];

            return $resultJson->setData($response);
        }

        $connector = $this->connectorResolver->getConnector($connectorConfiguration['type']);

        try {
            $response = [
                'status' => 'success',
                'code' => 'success',
                'message' => __('Connection Success.')
            ];

            /** @var \MageSuite\ErpConnector\Model\Client\ClientInterface $client */
            $client = $connector->getClient();
            $client->setData($connectorConfiguration);

            $client->checkConnection();
            return $resultJson->setData($response);
        } catch (\Exception $exception) {
            return $resultJson->setData(
                [
                    'status' => 'error',
                    'code' => 'cant_connect',
                    'message' => $exception->getMessage()
                ]
            );
        }
    }
}
