<?php

namespace MageSuite\ErpConnector\Console\Command;


class Test extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \MageSuite\ErpConnector\Cron\Process
     */
    protected $process;

    /**
     * @var \Magento\Cron\Model\Config\Data
     */
    protected $cronConfig;

    /**
     * @var \Magento\Cron\Observer\ProcessCronQueueObserver
     */
    protected $cronObserver;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    public function __construct(
        \MageSuite\ErpConnector\Cron\Process $process,
        \Magento\Cron\Model\Config\Data $cronConfig,
        \Magento\Cron\Observer\ProcessCronQueueObserver $cronObserver,
        \Magento\Framework\App\State $state
    ) {
        parent::__construct();

        $this->process = $process;
        $this->cronConfig = $cronConfig;
        $this->cronObserver = $cronObserver;
        $this->state = $state;
    }

    protected function configure()
    {
        $this->setName('erp:test')
            ->setDescription('Test command');
    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

        $result = $this->process->execute();

        $jobs = $this->cronConfig->getJobs();

        $this->cronObserver->execute();
        //$this->cronObserver->processPendingJobs('erp_connector', );

        die(var_dump($result));
    }
}
