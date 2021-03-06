<?php

namespace MageSuite\ErpConnector\Model\ProviderProcessor;

class ProviderProcessor extends \Magento\Framework\DataObject
{
    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\Command\LogErrorMessage
     */
    protected $logErrorMessage;

    protected $scheduler = null;

    /**
     * Scheduler ID => Provider
     * @var array
     */
    protected $schedulerIdToProviderMap = [];

    public function __construct(
        \MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository,
        \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        array $data = []
    ) {
        parent::__construct($data);

        $this->providerRepository = $providerRepository;
        $this->logErrorMessage = $logErrorMessage;
    }

    public function setScheduler($scheduler)
    {
        $this->scheduler = $scheduler;
    }

    protected function getProvider()
    {
        if ($this->scheduler === null) {
            throw new \InvalidArgumentException('Scheduler isn\'t set.');
        }

        $schedulerId = $this->scheduler->getId();

        if (isset($this->schedulerIdToProviderMap[$schedulerId])) {
            return $this->schedulerIdToProviderMap[$schedulerId];
        }

        $this->schedulerIdToProviderMap[$schedulerId] = $this->providerRepository->getById($this->scheduler->getProviderId());
        return $this->schedulerIdToProviderMap[$schedulerId];
    }

    protected function processErrorMessage($e)
    {
        $title = __('%1 provider orders export ERROR.', $this->getProvider()->getName());
        $messages = [
            $title,
            __($e->getMessage())
        ];

        $previous = $e->getPrevious();

        while ($previous) {
            $msg[] = __($previous->getMessage());
            $previous = $previous->getPrevious();
        }

        $messages = implode(' ', $messages);
        $this->logErrorMessage->execute($this, $messages, null, \Magento\Framework\Notification\MessageInterface::SEVERITY_MINOR);

        throw new \Exception($messages, 0, $e); //phpcs:ignore
    }
}
