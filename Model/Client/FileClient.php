<?php

declare(strict_types=1);

namespace MageSuite\ErpConnector\Model\Client;

class FileClient extends Client implements ClientInterface
{
    public function __construct(
        protected \Magento\Framework\Event\Manager $eventManager,
        protected \MageSuite\ErpConnector\Model\Command\FormatDirectoryName $formatDirectoryName,
        protected array $data = []
    ) {
        parent::__construct($eventManager, $data);
    }

    public function validateDirectoryExist(string $directory, string $providerName, ?string $rawDirectory = null): bool
    {
        $connection = $this->getConnection();
        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if ($rawDirectory && $this->hasDirectoryPlaceholder($rawDirectory)) {
            $this->ensureDynamicDirectoryExists($directory);
        }

        if ($connection->cd($directory)) {
            return true;
        }

        throw new \MageSuite\ErpConnector\Exception\DirectoryNotFound((string)__(
            'Unable to detect a directory "%1" at a "%2" remote location %3.',
            $directory,
            $providerName,
            $location
        ));
    }

    protected function hasDirectoryPlaceholder(string $directory): bool
    {
        return str_contains($directory, '{');
    }

    protected function ensureDynamicDirectoryExists(string $directory): void
    {
        $parts = explode('/', trim($directory, '/'));
        $current = '';

        $connection = $this->getConnection();

        foreach ($parts as $part) {
            $current .= '/' . $part;

            if (!$connection->cd($current)) {
                $connection->mkdir($current, 0755, false);
            }
        }
    }

    protected function getMovedFilePath(string $destinationDir, string $fileName, bool $renameMovedFile = true): string
    {
        if (!$renameMovedFile) {
            return sprintf(self::FILE_PATH_FORMAT, $destinationDir, $fileName);
        }

        return sprintf(self::MOVED_FILE_NAME_FORMAT, $destinationDir, date(self::FILE_PREFIX_DATETIME_FORMAT), $fileName);
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
