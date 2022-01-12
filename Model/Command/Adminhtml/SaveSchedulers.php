<?php

namespace MageSuite\ErpConnector\Model\Command\Adminhtml;

class SaveSchedulers
{
    /**
     * @var \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface
     */
    protected $schedulerRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\Validator\CronExpression
     */
    protected $cronExpressionValidator;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\SchedulerFactory
     */
    protected $schedulerFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\SchedulerConnectorConfigurationRepositoryInterface
     */
    protected $schedulerConnectorConfigurationRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfigurationFactory
     */
    protected $schedulerConnectorConfigurationFactory;

    public function __construct(
        \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface $schedulerRepository,
        \MageSuite\ErpConnector\Model\Validator\CronExpression $cronExpressionValidator,
        \MageSuite\ErpConnector\Model\Data\SchedulerFactory $schedulerFactory,
        \MageSuite\ErpConnector\Api\SchedulerConnectorConfigurationRepositoryInterface $schedulerConnectorConfigurationRepository,
        \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfigurationFactory $schedulerConnectorConfigurationFactory
    ) {
        $this->schedulerRepository = $schedulerRepository;
        $this->cronExpressionValidator = $cronExpressionValidator;
        $this->schedulerFactory = $schedulerFactory;
        $this->schedulerConnectorConfigurationRepository = $schedulerConnectorConfigurationRepository;
        $this->schedulerConnectorConfigurationFactory = $schedulerConnectorConfigurationFactory;
    }

    public function execute($providerId, $type, $formData)
    {
        $schedulers = $this->schedulerRepository->getByProviderIdAndType($providerId, $type);

        $schedulersData = [];

        foreach ($formData as $schedulerData) {
            $isCronExpressionValid = $this->cronExpressionValidator->validate($schedulerData['cron_expression']);

            if (!$isCronExpressionValid) {
                throw new \MageSuite\ErpConnector\Exception\InvalidSchedulerException(__('Invalid cron syntax "%1" in %2 scheduler.', trim($schedulerData['cron_expression']), $schedulerData['name']));
            }

            $schedulerData['provider_id'] = $providerId;

            if (isset($schedulerData['id'])) {
                $schedulersData[$schedulerData['id']] = $schedulerData;
            } else {
                $scheduler = $this->schedulerFactory->create();

                $scheduler->setData($schedulerData);
                $this->schedulerRepository->save($scheduler);

                $connectorFormData = $schedulerData['connectors']['connectors'] ?? [];
                $this->saveSchedulerConnectorConfiguration($scheduler->getId(), $scheduler->getProviderId(), $connectorFormData);
            }
        }

        foreach ($schedulers as $scheduler) {
            if (isset($schedulersData[$scheduler->getId()])) {
                $schedulerData = $schedulersData[$scheduler->getId()];

                $scheduler->setData($schedulerData);
                $this->schedulerRepository->save($scheduler);

                $connectorFormData = $schedulerData['connectors']['connectors'] ?? [];
                $this->saveSchedulerConnectorConfiguration($scheduler->getId(), $scheduler->getProviderId(), $connectorFormData);
            } else {
                $this->schedulerRepository->delete($scheduler);
            }
        }
    }

    protected function saveSchedulerConnectorConfiguration($schedulerId, $providerId, $formData)
    {
        $configuration = $this->schedulerConnectorConfigurationRepository->getBySchedulerId($schedulerId);

        $schedulerConnectorConfigurations = [];

        foreach ($formData as $config) {
            $config['scheduler_id'] = $schedulerId;
            $config['provider_id'] = $providerId;

            if (isset($config['id'])) {
                $schedulerConnectorConfigurations[$config['id']] = $config;
            } else {
                $schedulerConnectorConfiguration = $this->schedulerConnectorConfigurationFactory->create();
                $schedulerConnectorConfiguration->setData($config);
                $this->schedulerConnectorConfigurationRepository->save($schedulerConnectorConfiguration);
            }
        }

        foreach ($configuration as $item) {
            if (isset($schedulerConnectorConfigurations[$item->getId()])) {
                $item->setData($schedulerConnectorConfigurations[$item->getId()]);
                $this->schedulerConnectorConfigurationRepository->save($item);
            } else {
                $this->schedulerConnectorConfigurationRepository->delete($item);
            }
        }
    }
}
