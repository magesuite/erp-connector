<?php
namespace MageSuite\ErpConnector\Model\Source;

class SchedulerType implements \Magento\Framework\Data\OptionSourceInterface
{
    const TYPE_CRON = 'cron';
    const TYPE_RABBITMQ = 'rabbitmq';

    public function getCollection()
    {
        return [
            [
                'value' => self::TYPE_CRON,
                'label' => __('Cron'),
            ],
            [
                'value' => self::TYPE_RABBITMQ,
                'label' => __('RabbitMQ'),
            ],
        ];
    }

    public function toOptionArray()
    {
        return $this->getCollection();
    }
}
