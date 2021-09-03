<?php
namespace MageSuite\ErpConnector\Model\Client;

class Soap extends Client implements ClientInterface
{
    /**
     * @var \Magento\Framework\DomDocument\DomDocumentFactory
     */
    protected $domDocumentFactory;

    protected $soapClient = null;

    public function __construct(
        \MageSuite\ErpConnector\Model\Command\AddAdminNotification $addAdminNotification,
        \Magento\Framework\DomDocument\DomDocumentFactory $domDocumentFactory,
        \MageSuite\ErpConnector\Logger\Logger $logger,
        array $data = []
    ) {
        parent::__construct($addAdminNotification, $logger, $data);

        $this->domDocumentFactory = $domDocumentFactory;
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

    public function sendItem($provider, $data)
    {
        $content = $data['content'] ?? null;

        if (!$content) {
            $this->logErrorMessage($provider->getName() . ' provider ERROR', 'Missing content');
            return $this;
        }

        $soapClient = $this->getSoapClient();

        try {
            $response = $soapClient->__doRequest($content, $this->getData('location'), $this->getData('action'), $this->getData('version'));
            $this->processSoapApiResponse($response);
        } catch (\Exception $e) {
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

            $this->logErrorMessage($provider->getName() . ' provider ERROR', $mergedMessages);
        }

        return $this;
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

    protected function getSoapClient()
    {
        if ($this->soapClient !== null) {
            return $this->soapClient;
        }

        $soapLogin = $this->getData('login');
        $soapLocation = $this->getData('location');

        $credentials = [
            'login' => $soapLogin,
            'password' => $this->getData('password')
        ];

        try {
            $soapClient = new \SoapClient($this->getData('wsdl'), $credentials);
        } catch (\SoapFault $e) {
            $message = __('Unable to connect to a remote SOAP API "%1" as "%2"', $soapLocation, $soapLogin);

            $this->logErrorMessage('Unable to connect to a remote SOAP API', $message);

            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed($message);
        }

        $this->soapClient = $soapClient;
        return $this->soapClient;
    }
}
