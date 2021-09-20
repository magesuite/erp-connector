<?php
namespace MageSuite\ErpConnector\Controller\Adminhtml\Connection;

class Check extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'MageSuite_ErpConnector::erp_connector';

    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorResolver
     */
    protected $connectorResolver;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface
     */
    protected $connectorConfigurationRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageSuite\ErpConnector\Model\ConnectorResolver $connectorResolver,
        \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface $connectorConfigurationRepository
    ) {
        parent::__construct($context);

        $this->connectorResolver = $connectorResolver;
        $this->connectorConfigurationRepository = $connectorConfigurationRepository;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        $connectorConfiguration = $this->getRequest()->getParams();
        $connector = $this->connectorResolver->getConnector($connectorConfiguration['type']);

        try {
            $response = [
                'status' => 'success',
                'code' => 'success',
                'message' => __('Connection Success.')
            ];

            $connectorConfiguration = $this->prepareConnectorConfiguration($connector, $connectorConfiguration);

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

    public function prepareConnectorConfiguration($connector, $connectorConfiguration)
    {
        if (!isset($connectorConfiguration['id'])) {
            return $connectorConfiguration;
        }

        foreach ($connectorConfiguration as $key => $value) {
            if ($value != \MageSuite\ErpConnector\Model\Data\VaultItem::VAULT_VALUE_PLACEHOLDER) {
                continue;
            }

            $connectorConfigurationItem = $this->connectorConfigurationRepository->getItemByConnectorIdAndName($connectorConfiguration['id'], $key);
            $connectorConfiguration[$key] = $connectorConfigurationItem ? $connectorConfigurationItem->getValue() : $value;
        }

        return $connectorConfiguration;
    }
}
