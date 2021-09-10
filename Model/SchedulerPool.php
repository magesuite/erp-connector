<?php
namespace MageSuite\ErpConnector\Model;

class SchedulerPool
{
    const DEFAULT_SCHEDULER_TYPE = 'general';
    const DEFAULT_PROVIDER_PROCESSOR = 'general';

    protected $schedulerGroups;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        array $schedulerGroups,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->schedulerGroups = $schedulerGroups;
        $this->logger = $logger;
    }

    public function getSchedulerNamesByType($type)
    {
        if (!isset($this->schedulerGroups[$type])) {
            throw new \Exception('Scheduler group for type %1 doesn\'t exist', $type); //phpcs:ignore
        }

        $schedulerNames = array_keys($this->schedulerGroups[$type]);

        $result = [];

        foreach ($schedulerNames as $schedulerName) {
            $result[] = [
                'label' => ucfirst($schedulerName),
                'code' => $schedulerName
            ];
        }

        return $result;
    }

    public function getProviderProcessorBySchedulerTypeAndProviderCode($type, $providerCode)
    {
        try {
            $schedulersByType = $this->getSchedulersByType($type);
            return $schedulersByType[$providerCode];
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Provider processor for scheduler type %s and provider code %s doesn\'t exist', $type, $providerCode));
            return $this->schedulerGroups[self::DEFAULT_SCHEDULER_TYPE][self::DEFAULT_PROVIDER_PROCESSOR];
        }
    }
}
