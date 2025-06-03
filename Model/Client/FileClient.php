<?php

declare(strict_types=1);

namespace MageSuite\ErpConnector\Model\Client;

class FileClient extends Client
{
    public function __construct(
        protected \Magento\Framework\Event\Manager $eventManager,
        protected \MageSuite\ErpConnector\Model\Command\FormatDirectoryName $formatDirectoryName,
        protected array $data = []
    ) {
        parent::__construct($eventManager, $data);
    }

    protected function getSourceDirectory(): string
    {
        return $this->formatDirectoryName->execute(
            $this->getData('source_dir')
        );
    }

    protected function getDestinationDirectory(): string
    {
        return $this->formatDirectoryName->execute(
            $this->getData('destination_dir')
        );
    }
}
