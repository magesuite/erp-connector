<?php
namespace MageSuite\ErpConnector\Model\Framework\Filesystem\Io;

class SftpProxy extends \Magento\Framework\Filesystem\Io\Sftp
{
    public const PROXY_TIMEOUT_IN_SECONDS = 1;

    public function open(array $args = [])
    {
        $proxy = $args['proxy'];

        if (empty($proxy['host']) || empty($proxy['port'])) {
            parent::open($args);
            return;
        }

        if (!isset($args['timeout'])) {
            $args['timeout'] = self::REMOTE_TIMEOUT;
        }

        if (strpos($args['host'] ?? '', ':') !== false) {
            list($host, $port) = explode(':', $args['host'], 2);
        } else {
            $host = $args['host'];
            $port = self::SSH2_PORT;
        }

        $proxyConnection = $this->establishProxyConnection($host, $port, $proxy);
        $this->_connection = new \phpseclib3\Net\SFTP($proxyConnection, $port, $args['timeout']);

        if (!$this->_connection->login($args['username'], $args['password'])) {
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new \Exception(
                sprintf("Unable to open SFTP connection as %s@%s", $args['username'], $args['host'])
            );
        }
    }

    // @codingStandardsIgnoreStart
    protected function establishProxyConnection($host, $port, $proxy)
    {
        $proxyConnection = fsockopen($proxy['host'], $proxy['port'], $errorCode, $errorMessage, self::PROXY_TIMEOUT_IN_SECONDS);

        if (!$proxyConnection) {
            throw new \Exception($errorMessage);
        }

        $port = pack('n', $port);
        $address = chr(strlen($host)) . $host;

        $request = "\5\1\0";

        if (fwrite($proxyConnection, $request) != strlen($request)) {
            throw new \Exception('Premature termination');
        }

        $response = fread($proxyConnection, 2);

        if ($response != "\5\0") {
            throw new \Exception('Unsupported protocol or unsupported method');
        }

        $request = "\5\1\0\3$address$port";

        if (fwrite($proxyConnection, $request) != strlen($request)) {
            throw new \Exception('Premature termination');
        }

        $response = fread($proxyConnection, strlen($address) + 6);

        if (substr($response, 0, 2) != "\5\0") {
            throw new \Exception("Unsupported protocol or connection refused");
        }

        return $proxyConnection;
    }
    // @codingStandardsIgnoreEnd
}
