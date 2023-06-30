<?php

namespace MageSuite\ErpConnector\Model\Client;

class Soap extends \MageSuite\ErpConnector\Model\Client\Client implements ClientInterface
{
    const RESPONSE_METHOD_FORMAT = '%sResult';

    protected \Magento\Framework\DomDocument\DomDocumentFactory $domDocumentFactory;
    protected \MageSuite\ErpConnector\Helper\Configuration $configuration;
    protected \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage;
    protected \MageSuite\ErpConnector\Logger\Logger $logger;

    protected $soapClient = null;

    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\DomDocument\DomDocumentFactory $domDocumentFactory,
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        \MageSuite\ErpConnector\Logger\Logger $logger,
        array $data = []
    ) {
        parent::__construct($eventManager, $data);

        $this->domDocumentFactory = $domDocumentFactory;
        $this->configuration = $configuration;
        $this->logErrorMessage = $logErrorMessage;
        $this->logger = $logger;
    }

    public function checkConnection()
    {
        $soapClient = $this->getSoapClient();

        $content = '<soap:Envelope><soap:Header/><soap:Body>Ping</soap:Body></soap:Envelope>';
        $response = $soapClient->__doRequest($content, $this->getData('location'), $this->getData('action'), $this->getData('version'));

        if (!$response) {
            throw new \MageSuite\ErpConnector\Exception\ConnectionFailed(__('Unable to get a response from a remote SOAP API "%1" as "%2"', $this->getData('location'), $this->getData('login')));
        }
    }

    public function sendItems($provider, $items)
    {
        foreach ($items as $item) {
            $this->sendItem($provider, $item);
        }

        return $this;
    }

    protected function sendItem($provider, $item)
    {
        $files = $item['files'] ?? null;

        if (empty($files)) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing files data',
                $item
            );
            return false;
        }

        try {
            $soapClient = $this->getSoapClient();

            foreach ($files as $fileName => $content) {
                $response = $soapClient->__doRequest($content, $this->getData('location'), $this->getData('action'), $this->getData('version'));

                $this->logger->info($response);
                if (empty($response)) {
                    $this->logger->info($soapClient->__getLastResponseHeaders());
                }

                $this->processSoapApiResponse($response);
            }

        } catch (\Exception $e) {
            $this->processErrorMessage($provider, $e);
            throw $e;
        }

        return true;
    }

    public function downloadItems($provider)
    {
        $downloaded = [];

        try {
            $soapClient = $this->getSoapClient();
            $action = $this->getData('action');

            $preparedParameters = [];
            $parameters = $this->getData('parameters');

            if (!empty($parameters)) {
                foreach ($parameters['parameters'] as $parameter) {
                    $preparedParameters[$parameter['key']] = $parameter['value'];
                }
            }

            $response = $soapClient->__soapCall($action, [$preparedParameters]);

            if (is_string($response)) {
                $this->logger->info($response);
                if (empty($response)) {
                    $this->logger->info($soapClient->__getLastResponseHeaders());
                }
            }

            $responseMethod = sprintf(self::RESPONSE_METHOD_FORMAT, $action);
            $downloaded[$action] = $response->$responseMethod;
        } catch (\Exception $e) {
            $this->processErrorMessage($provider, $e);
        }

        if (empty($downloaded)) {
            throw new \MageSuite\ErpConnector\Exception\MissingDownloadData(__('Can\'t detect any valid data'));
        }

        return $downloaded;
    }

    public function processSoapApiResponse($response)
    {
        if (!$response) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed('A SOAP API response is empty.');
        }

        $domDocument = $this->domDocumentFactory->create();

        if (!$domDocument->loadXML($response)) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed('Unable to parse SOAP API response.');
        }

        $xpath = new \DOMXPath($domDocument);
        $xpath->registerNamespace('env', 'http://www.w3.org/2003/05/soap-envelope');
        $xpath->registerNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope');

        $errorMessage = 'Unable to parse SOAP API response.' . PHP_EOL;
        $faults = $xpath->query('//soap-env:Body/soap-env:Fault/faultstring');

        if ($faults && $faults->length) {
            foreach ($faults as $fault) {
                $errorMessage .= $fault->textContent . ';' . PHP_EOL;
            }

            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed($errorMessage);
        }

        $faults = $xpath->query('//env:Fault/env:Reason/env:Text');

        if ($faults && $faults->length) {
            foreach ($faults as $fault) {
                $errorMessage .= __($fault->textContent) . ';' . PHP_EOL;
            }

            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed($errorMessage);
        }
    }

    public function processErrorMessage($provider, $e)
    {
        $messages = [
            __('%1 provider ERROR.', $provider->getName()),
            __($e->getMessage())
        ];

        $previous = $e->getPrevious();

        while ($previous) {
            $messages[] = __($previous->getMessage());
            $previous = $previous->getPrevious();
        }

        $mergedMessages = implode(' ', $messages);

        $this->logErrorMessage->execute(
            sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
            $mergedMessages
        );
    }

    protected function getSoapClient()
    {
        if ($this->soapClient !== null) {
            return $this->soapClient;
        }

        $soapLogin = $this->getData('login');
        $soapLocation = $this->getData('location');

        try {
            $soapClient = new \SoapClient($this->getData('wsdl'), $this->getClientConfiguration());
        } catch (\SoapFault $e) {
            $message = __('Unable to connect to a remote SOAP API "%1" as "%2"', $soapLocation, $soapLogin);

            $this->logErrorMessage->execute(
                'Unable to connect to a remote SOAP API',
                $message,
                $e->getMessage()
            );

            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed($message);
        }

        $this->soapClient = $soapClient;
        return $this->soapClient;
    }

    public function getClientConfiguration()
    {
        $configuration = [
            'soap_version' => $this->getData('version'),
            'login' => $this->getData('login'),
            'password' => $this->getData('password'),
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => true
        ];

        $proxyHost = $this->configuration->getSoapConnectorProxyHost();
        $proxyPort = $this->configuration->getSoapConnectorProxyPort();

        if (empty($proxyHost) || empty($proxyPort)) {
            return $configuration;
        }

        $configuration['proxy_host'] = $proxyHost;
        $configuration['proxy_port'] = $proxyPort;

        return $configuration;
    }

    public function validateProcessedFile($fileName)
    {
        throw new \Exception('Not possible to verify if file exist for Soap client.'); //phpcs:ignore
    }
}
