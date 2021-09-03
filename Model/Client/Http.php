<?php
namespace MageSuite\ErpConnector\Model\Client;

class Http extends Client implements ClientInterface
{
    /**
     * @var \GuzzleHttp\ClientFactory
     */
    protected $clientFactory;

    protected $client = null;

    public function __construct(
        \MageSuite\ErpConnector\Model\Command\AddAdminNotification $addAdminNotification,
        \GuzzleHttp\ClientFactory $clientFactory,
        \MageSuite\ErpConnector\Logger\Logger $logger,
        array $data = []
    ) {
        parent::__construct($addAdminNotification, $logger, $data);

        $this->clientFactory = $clientFactory;
    }

    public function sendItem($provider, $data)
    {
        $content = $data['content'] ?? null;
        $fileName = $data['file_name'] ?? null;

        if (!$content || !$fileName) {
            $this->logErrorMessage($provider->getName() . ' provider ERROR', 'Missing content or fileName');
            return $this;
        }

        try {
            $response = $this->sendRequest($data);
            $this->validateResponse($response, $data);
        } catch (\Exception $e) {
            $this->logErrorMessage($provider->getName() . ' provider ERROR', $e->getMessage());
        }

        return $this;
    }

    public function sendRequest($data, $requestMethod = \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST)
    {
        try {
            $client = $this->getClient();
            $params = $this->getParameters($data['content']);

            return $client->request($requestMethod, '', $params);
        } catch (\Exception $e) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to send a content %1 file to %2 http location. Error: %3', $data['file_name'], $this->getData('url'), $e->getMessage()));
        }
    }

    public function getParameters($data)
    {
        return [
            'headers' => [
                'Content-Type' => 'text/xml'
            ],
            'body' => $data
        ];
    }

    protected function validateResponse($response, $data)
    {
        if (empty($response)) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Empty response for a send request of content %1 file to %2 http location.', $data['file_name'], $this->getData('url')));
        }

        if ($response->getStatusCode() != \Symfony\Component\HttpFoundation\Response::HTTP_OK) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Wrong response status code for a send request of content %1 file to %2 http location.', $data['file_name'], $this->getData('url')));
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
