<?php

namespace MageSuite\ErpConnector\Test\Integration\Model;

/**
 * @magentoAppArea adminhtml
 */
class ConnectorTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * @var \MageSuite\ErpConnector\Model\ProviderRepository
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorRepository
     */
    protected $connectorRepository;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->providerRepository = $this->_objectManager->get(\MageSuite\ErpConnector\Model\ProviderRepository::class);
        $this->connectorRepository = $this->_objectManager->get(\MageSuite\ErpConnector\Model\ConnectorRepository::class);
        $this->connection = $this->_objectManager->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ErpConnector::Test/Integration/_files/provider.php
     */
    public function testItSavesAndLoadConnectorsCorrectly()
    {
        $provider = $this->providerRepository->getByName('Test Provider');

        $this->saveConnectors($provider);

        $connectors = $this->connectorRepository->getByProviderId($provider->getId());

        $this->assertCount(5, $connectors);
        $this->savedConnectorsHaveCorrectData($connectors);
    }

    protected function saveConnectors($provider)
    {
        $data = [
            'general' => $provider->getData(),
            'connectors' => [
                'ftp' => [
                    'ftp' => [
                        [
                            'name' => 'ftp',
                            'host' => 'test-host',
                            'port' => 21,
                            'username' => 'test-user',
                            'password' => 'ftppassword',
                            'passive_mode' => 1,
                            'source_dir' => '/in',
                            'destination_dir' => '/out'
                        ]
                    ]
                ],
                'sftp' => [
                    'sftp' => [
                        ['name' => 'sftp', 'host' => 'test-host', 'username' => 'normal-user', 'password' => 'secret', 'timeout' => 42, 'source_dir' => '/inside', 'destination_dir' => '/outside']
                    ]
                ],
                'soap' => [
                    'soap' => [
                        ['name' => 'soap', 'wsdl' => '/path/to/file.wsdl', 'version' => SOAP_1_2, 'login' => 'soapuser', 'password' => '123432', 'location' => '/users/data', 'action' => 'getUsers']
                    ]
                ],
                'http' => [
                    'http' => [
                        [
                            'name' => 'http',
                            'url' => 'http://rest-api.com',
                            'request_method' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST,
                            'login' => 'ruser',
                            'password' => 'abcdefgh',
                            'content_type' => \MageSuite\ErpConnector\Model\Source\ContentType::CONTENT_TYPE_JSON,
                            'timeout' => 7337
                        ]
                    ]
                ],
                'email' => [
                    'email' => [
                        ['name' => 'email', 'email' => 'john.doe@skynet.com', 'template' => 'very-important-message']
                    ]
                ]
            ]
        ];

        $this->getRequest()->setMethod(\Magento\Framework\App\Request\Http::METHOD_POST);
        $this->getRequest()->setPostValue($data);
        $this->dispatch('backend/erp_connector/provider/save');
    }

    protected function savedConnectorsHaveCorrectData($connectors)
    {
        $groupedConnectors = $this->groupConnectorsByType($connectors);
        $connectorConfigurationTableName = $this->connection->getTableName('erp_connector_connector_configuration');

        $ftpClient = $groupedConnectors['ftp']->getClient();

        $this->assertEquals('test-host', $ftpClient->getHost());
        $this->assertEquals('test-user', $ftpClient->getUsername());
        $this->assertEquals('ftppassword', $ftpClient->getPassword());
        $this->assertEquals('/in', $ftpClient->getSourceDir());
        $this->assertEquals('/out', $ftpClient->getDestinationDir());

        $ftConnectorId = $groupedConnectors['ftp']->getId();
        $query = "SELECT value FROM $connectorConfigurationTableName WHERE connector_id = $ftConnectorId AND name = 'username'";

        $ftpRawPassword = $this->connection->fetchOne($query);

        $this->assertNotNull($ftpRawPassword);
        $this->assertNotEquals($ftpRawPassword, $ftpClient->getPassword());

        $sftpClient = $groupedConnectors['sftp']->getClient();

        $this->assertEquals('test-host', $sftpClient->getHost());
        $this->assertEquals('normal-user', $sftpClient->getUsername());
        $this->assertEquals('secret', $sftpClient->getPassword());
        $this->assertEquals(42, $sftpClient->getTimeout());
        $this->assertEquals('/inside', $sftpClient->getSourceDir());
        $this->assertEquals('/outside', $sftpClient->getDestinationDir());

        $sftConnectorId = $groupedConnectors['sftp']->getId();
        $query = "SELECT value FROM $connectorConfigurationTableName WHERE connector_id = $sftConnectorId AND name = 'username'";

        $sftpRawPassword = $this->connection->fetchOne($query);

        $this->assertNotNull($sftpRawPassword);
        $this->assertNotEquals($sftpRawPassword, $sftpClient->getPassword());

        $soapClient = $groupedConnectors['soap']->getClient();

        $this->assertEquals('/path/to/file.wsdl', $soapClient->getWsdl());
        $this->assertEquals(SOAP_1_2, $soapClient->getVersion());
        $this->assertEquals('soapuser', $soapClient->getLogin());
        $this->assertEquals('123432', $soapClient->getPassword());
        $this->assertEquals('/users/data', $soapClient->getLocation());
        $this->assertEquals('getUsers', $soapClient->getAction());

        $soapConnectorId = $groupedConnectors['soap']->getId();
        $query = "SELECT value FROM $connectorConfigurationTableName WHERE connector_id = $soapConnectorId AND name = 'password'";

        $soapRawPassword = $this->connection->fetchOne($query);

        $this->assertNotNull($soapRawPassword);
        $this->assertNotEquals($soapRawPassword, $soapClient->getPassword());

        $httpClient = $groupedConnectors['http']->getClient();

        $this->assertEquals('http://rest-api.com', $httpClient->getUrl());
        $this->assertEquals(\Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST, $httpClient->getRequestMethod());
        $this->assertEquals('ruser', $httpClient->getLogin());
        $this->assertEquals('abcdefgh', $httpClient->getPassword());
        $this->assertEquals(\MageSuite\ErpConnector\Model\Source\ContentType::CONTENT_TYPE_JSON, $httpClient->getContentType());
        $this->assertEquals(7337, $httpClient->getTimeout());

        $httpConnectorId = $groupedConnectors['soap']->getId();
        $query = "SELECT value FROM $connectorConfigurationTableName WHERE connector_id = $httpConnectorId AND name = 'password'";

        $httpRawPassword = $this->connection->fetchOne($query);

        $this->assertNotNull($httpRawPassword);
        $this->assertNotEquals($httpRawPassword, $httpClient->getPassword());

        $emailClient = $groupedConnectors['email']->getClient();

        $this->assertEquals('john.doe@skynet.com', $emailClient->getEmail());
        $this->assertEquals('very-important-message', $emailClient->getTemplate());
    }

    private function groupConnectorsByType($connectors)
    {
        $groupedConnectors = [];

        foreach ($connectors as $connector) {
            $groupedConnectors[$connector->getType()] = $connector;
        }

        return $groupedConnectors;
    }
}
