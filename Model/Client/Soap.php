<?php

namespace MageSuite\ErpConnector\Model\Client;

class Soap extends Client implements ClientInterface
{
    public function checkConnection()
    {
        $soapLogin = $this->getData('login');
        $soapLocation = $this->getData('location');

        $credentials = [
            'login' => $soapLogin,
            'password' => $this->getData('password')
        ];

        try {
            $client = new \SoapClient($this->getData('wsdl'), $credentials);
        } catch (\SoapFault $e) {
            throw new \Exception( //phpcs:ignore
                __(
                    'Unable to connect to a remote SOAP API "%1" as "%2"',
                    $soapLocation,
                    $soapLogin
                )
            );
        }

        $content = '<soap:Envelope><soap:Header/><soap:Body>Ping</soap:Body></soap:Envelope>';
        $response = $client->__doRequest($content, $soapLocation, $this->getData('action'), $this->getData('version'));

        if (!$response) {
            throw new \Exception( //phpcs:ignore
                __(
                    'Unable to get a response from a remote SOAP API "%1" as "%2"',
                    $soapLocation,
                    $soapLogin
                )
            );
        }
    }
}
