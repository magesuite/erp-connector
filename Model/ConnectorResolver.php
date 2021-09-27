<?php

namespace MageSuite\ErpConnector\Model;

class ConnectorResolver
{
    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorFactory
     */
    protected $connectorFactory;

    protected $connectorsConfiguration;

    public function __construct(
        \MageSuite\ErpConnector\Model\ConnectorFactory $connectorFactory,
        array $connectorsConfiguration
    ) {
        $this->connectorFactory = $connectorFactory;
        $this->connectorsConfiguration = $connectorsConfiguration;
    }

    public function getConnectorConfigurationFields()
    {
        $result = [];

        foreach ($this->connectorsConfiguration as $connectorType => $connectorConfiguration) {
            $result[$connectorType] = $connectorConfiguration['fields'];
        }

        return $result;
    }

    public function getConnector($type)
    {
        foreach ($this->connectorsConfiguration as $connectorType => $connectorConfiguration) {

            if ($connectorType != $type) {
                continue;
            }

            /** @var \MageSuite\ErpConnector\Model\Connector $connector */
            $connector = $this->connectorFactory->create();
            $connector->setClient($connectorConfiguration['client']);

            return $connector;
        }

        return null;
    }

    public function getClient($type)
    {
        foreach ($this->connectorsConfiguration as $connectorType => $connectorConfiguration) {

            if ($connectorType != $type) {
                continue;
            }

            return $connectorConfiguration['client'];
        }

        return null;
    }
}
