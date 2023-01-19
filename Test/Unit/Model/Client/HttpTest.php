<?php

namespace MageSuite\ErpConnector\Test\Unit\Model\Client;

class HttpTest extends \PHPUnit\Framework\TestCase
{
    protected ?\MageSuite\ErpConnector\Model\Client\Http $httpClient;

    public function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->httpClient = $objectManager->get(\MageSuite\ErpConnector\Model\Client\Http::class);
    }

    /**
     * @dataProvider getTestData
     * @param array $responseData
     * @param string|null $exceptionMessage
     */
    public function testItValidatesResponseCorrectly(array $responseData, ?string $exceptionMessage)
    {
        $response = $this->prepareResponse($responseData);

        if ($exceptionMessage) {
            $this->expectException(\MageSuite\ErpConnector\Exception\RemoteExportFailed::class);
            $this->expectExceptionMessage($exceptionMessage);
        }

        $result = $this->httpClient->validateResponse($response, ['file_name' => $responseData['file_name'] ?? null]);

        if (!$exceptionMessage) {
            $this->assertTrue($result);
        }
    }

    protected function prepareResponse($responseData)
    {
        if (isset($responseData['is_empty'])) {
            return null;
        }

        return new \Magento\Framework\DataObject(['status_code' => $responseData['response_status_code']]);
    }

    public static function getTestData(): array
    {
        return [
            'response with incorrect status' => [
                ['response_status_code' => \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST, 'file_name' => 'test.csv'],
                'exceptionMessage' => 'Wrong response status code for a send request of content test.csv file to  http location'
            ],
            'response with ok status' => [
                ['response_status_code' => \Symfony\Component\HttpFoundation\Response::HTTP_OK, 'file_name' => 'test.csv'],
                'exceptionMessage' => null
            ],
            'response with create status' => [
                ['response_status_code' => \Symfony\Component\HttpFoundation\Response::HTTP_CREATED, 'file_name' => 'test.csv'],
                'exceptionMessage' => null
            ],
            'empty response' => [
                ['is_empty' => true, 'file_name' => 'test.csv'],
                'exceptionMessage' => 'Empty response for a send request of content test.csv file to  http location.'
            ],
        ];
    }
}
