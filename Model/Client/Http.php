<?php
namespace MageSuite\ErpConnector\Model\Client;

class Http extends \Magento\Framework\DataObject implements ClientInterface
{
    /**
     * @var \GuzzleHttp\ClientFactory
     */
    protected $clientFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\Command\LogErrorMessage
     */
    protected $logErrorMessage;

    protected $client = null;

    public function __construct(
        \GuzzleHttp\ClientFactory $clientFactory,
        \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        array $data = []
    ) {
        parent::__construct($data);

        $this->clientFactory = $clientFactory;
        $this->logErrorMessage = $logErrorMessage;
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
            foreach ($files as $fileName => $content) {
                $response = $this->sendRequest($fileName, $content);
                $this->validateResponse($response, $fileName);
            }

        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                $e->getMessage(),
                $item
            );
        }

        return true;
    }

    public function downloadItems($provider)
    {
        $downloaded = [];

        try {
            $response = $this->sendRequest();
            $this->validateResponse($response);

            $downloaded[$this->getData('url')] = $response->getBody()->getContents();
        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                $e->getMessage()
            );
        }

        return $downloaded;
    }

    public function sendRequest($fileName = null, $content = null)
    {
        try {
            $client = $this->getClient();
            $params = $this->getParameters($content);

            return $client->request($this->getData('request_method'), '', $params);
        } catch (\Exception $e) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__(
                'Unable to send a request to %1 http location. File: %2, request_method: %3, error: %4',
                $this->getData('url'),
                $this->getData('request_method'),
                $fileName,
                $e->getMessage()
            ));
        }
    }

    public function getParameters($content)
    {
        $headers = [
            'Content-Type' => $this->getData('content_type')
        ];

        if ($headers['Content-Type'] == \MageSuite\ErpConnector\Model\Source\ContentType::CONTENT_TYPE_JSON) {
            $headers['Accept'] = $headers['Content-Type'];
        }

        $customHeadersGroup = $this->getData('custom_headers');

        if (!empty($customHeadersGroup)) {
            foreach ($customHeadersGroup['custom_headers'] as $customHeader) {
                $headers[$customHeader['key']] = $customHeader['value'];
            }
        }

        $parameters = [
            'headers' => $headers,
            'body' => $content
        ];

        if ($this->getData('username') && $this->getData('password')) {
            $parameters['auth'] = [$this->getData('username'), $this->getData('password')];
        }

        return $parameters;
    }

    protected function validateResponse($response, $fileName = null)
    {
        if (empty($response)) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Empty response for a send request of content %1 file to %2 http location.', $fileName, $this->getData('url')));
        }

        if ($response->getStatusCode() != \Symfony\Component\HttpFoundation\Response::HTTP_OK) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Wrong response status code for a send request of content %1 file to %2 http location.', $fileName, $this->getData('url')));
        }

        return true;
    }

    protected function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

         $client = $this->clientFactory->create($this->getClientConfiguration());

        $this->client = $client;
        return $this->client;
    }

    public function getClientConfiguration()
    {
        return [
            'config' => [
                'base_uri' => $this->getData('url'),
                'timeout' => $this->getData('timeout'),
                'allow_redirects' => true,
                'http_errors' => true,
            ]
        ];
    }

    public function validateProcessedFile($fileName)
    {
        throw new \Exception('Not possibile to verify if file exist for Http client.'); //phpcs:ignore
    }
}
