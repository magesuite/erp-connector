<?php
namespace MageSuite\ErpConnector\Model\Client;

class Client extends \Magento\Framework\DataObject
{
    protected \Magento\Framework\Event\Manager $eventManager;

    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        array $data = []
    ) {
        parent::__construct($data);

        $this->eventManager = $eventManager;
    }

    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        try {
            return parent::__call($method, $args);
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $snakeCaseMethodName = $this->convertCamelCaseToSnakeCase($method);

            $this->eventManager->dispatch(
                sprintf('erp_connector_client_%s', $snakeCaseMethodName),
                ['client' => $this]
            );

            $this->eventManager->dispatch(
                sprintf('erp_connector_%s_client_%s', $this->getClassName(), $snakeCaseMethodName),
                ['client' => $this]
            );
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    protected function getClassName(): string
    {
        $classNameParts = explode('\\', get_class($this));
        return strtolower(end($classNameParts));
    }

    protected function convertCamelCaseToSnakeCase(string $name): string
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
    }
}
