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
        $content = $item['content'] ?? null;

        if (!$content) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing content',
                $item
            );
            return false;
        }

        $fileName = $item['file_name'] ?? null;

        if (!$fileName) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing file name',
                $item
            );
            return false;
        }

        try {
            $response = $this->sendRequest($item);
            $this->validateResponse($response, $item);
        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                $e->getMessage(),
                $item
            );
        }

        return true;
    }

    public function sendRequest($item, $requestMethod = \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST)
    {
        try {
            $client = $this->getClient();
            $params = $this->getParameters($item['content']);

            return $client->request($requestMethod, '', $params);
        } catch (\Exception $e) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to send a content %1 file to %2 http location. Error: %3', $item['file_name'], $this->getData('url'), $e->getMessage()));
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

    protected function validateResponse($response, $item)
    {
        if (empty($response)) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Empty response for a send request of content %1 file to %2 http location.', $item['file_name'], $this->getData('url')));
        }

        if ($response->getStatusCode() != \Symfony\Component\HttpFoundation\Response::HTTP_OK) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Wrong response status code for a send request of content %1 file to %2 http location.', $item['file_name'], $this->getData('url')));
        }

        return true;
    }

    protected function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

         $client = $this->clientFactory->create([
            'config' => [
                'base_uri' => $this->getData('url'),
                'timeout' => $this->getData('timeout'),
                'allow_redirects' => true,
                'http_errors' => true,
            ]
         ]);

        $this->client = $client;
        return $this->client;
    }
}
