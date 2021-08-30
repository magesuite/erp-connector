<?php
namespace MageSuite\ErpConnector\Controller\Adminhtml\Connection;

class Check implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorPool
     */
    protected $connectorPool;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\RequestInterface $request,
        \MageSuite\ErpConnector\Model\ConnectorPool $connectorPool
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->connectorPool = $connectorPool;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        $connectorConfiguration = $this->request->getParams();

        if (!isset($connectorConfiguration['type'])) {
            $response = [
                'status' => 'error',
                'code' => 'missing_connector_id',
                'message' => __('Please save the provider with this connector first.')
            ];

            return $resultJson->setData($response);
        }

        $connector = $this->connectorPool->getConnector($connectorConfiguration['type']);

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
