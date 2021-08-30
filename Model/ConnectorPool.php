<?php

namespace MageSuite\ErpConnector\Model;

class ConnectorPool
{
    protected $connectors;

    public function __construct(array $connectors)
    {
        $this->connectors = $connectors;
    }

    public function getConnectorConfigurations()
    {
        $connectorConfiguration = [];

        foreach ($this->connectors as $connectorType => $connector) {
            $connectorConfiguration[$connectorType] = $connector['configuration']; //TODO: check it
        }

        return $connectorConfiguration;
    }

    public function getConnector($key)
    {
        foreach ($this->connectors as $connector) {
            if (!$connector['class']->isApplicable($key)) { //TODO: check it
                continue;
            }

            /** @var \MageSuite\ErpConnector\Model\Connector\ConnectorInterface $connector['class'] */
            return $connector['class'];
        }

        return null;
    }
}
