<?php

namespace MageSuite\ErpConnector\Model\Client;

class Http extends \MageSuite\ErpConnector\Model\Client\Client implements ClientInterface
{
    const AUTH_BEARER_TOKEN_FORMAT = 'Bearer %s';

    protected \GuzzleHttp\ClientFactory $clientFactory;
    protected \MageSuite\ErpConnector\Helper\Configuration $configuration;
    protected \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage;

    protected $client = null;

    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \GuzzleHttp\ClientFactory $clientFactory,
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        array $data = []
    ) {
        parent::__construct($eventManager, $data);

        $this->clientFactory = $clientFactory;
        $this->configuration = $configuration;
        $this->logErrorMessage = $logErrorMessage;
    }

    public function sendItems($provider, $items)
    {
        $responseContents = [];

        foreach ($items as $item) {
            $responseContents[] = $this->sendItem($provider, $item);
        }

        return empty($responseContents) ? null : implode(PHP_EOL, $responseContents);
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

        $responseContents = [];

        try {
            foreach ($files as $fileName => $content) {
                $response = $this->sendRequest($fileName, $content);

                $responseContent = $response->getBody()->getContents();
                $responseContents[] = $responseContent;

                $this->validateResponse($response, ['provider' => $provider, 'content' => $responseContent, 'file_name' => $fileName]);
            }

        } catch (\Exception $e) {
            $responseContents = empty($responseContents) ? null : implode(PHP_EOL, $responseContents);
            $messageWithResponseContents = sprintf('%s, response content: %s', $e->getMessage(), $responseContents);

            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                $messageWithResponseContents,
                $item
            );

            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed($messageWithResponseContents);
        }

        return empty($responseContents) ? null : implode(PHP_EOL, $responseContents);
    }

    public function downloadItems($provider)
    {
        $downloaded = [];

        try {
            $response = $this->sendRequest();
            $responseContent = $response->getBody()->getContents();

            $this->validateResponse($response, ['provider' => $provider, 'content' => $responseContent, 'file_name' => null]);

            $downloaded[$this->getData('url')] = $responseContent;
        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                $e->getMessage()
            );
        }

        if (empty($downloaded)) {
            throw new \MageSuite\ErpConnector\Exception\MissingDownloadData(__('Can\'t detect any valid data'));
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

        if ($this->getData('login') && $this->getData('password')) {
            $parameters['auth'] = [$this->getData('login'), $this->getData('password')];
        }

        if ($this->getData('authorization_bearer')) {
            $parameters['headers']['Authorization'] = sprintf(self::AUTH_BEARER_TOKEN_FORMAT, $this->getData('authorization_bearer'));
        }

        return $parameters;
    }

    public function validateResponse($response, $data)
    {
        if (empty($response)) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Empty response for a send request of content %1 file to %2 http location.', $data['file_name'], $this->getData('url')));
        }

        if ($response->getStatusCode() != \Symfony\Component\HttpFoundation\Response::HTTP_OK && $response->getStatusCode() != \Symfony\Component\HttpFoundation\Response::HTTP_CREATED) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Wrong response status code for a send request of content %1 file to %2 http location.', $data['file_name'], $this->getData('url')));
        }

        return true;
    }

    public function getClient()
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
        $configuration = [
            'config' => [
                'base_uri' => $this->getData('url'),
                'timeout' => $this->getData('timeout'),
                'allow_redirects' => true,
                'http_errors' => true,
            ]
        ];

        $proxy = $this->configuration->getHttpConnectorProxy();

        if (empty($proxy)) {
            return $configuration;
        }

        $configuration['config']['proxy'] = $proxy;
        return $configuration;
    }

    public function validateProcessedFile($fileName)
    {
        throw new \Exception('Not possible to verify if file exist for Http client.'); //phpcs:ignore
    }
}
