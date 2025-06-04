<?php

declare(strict_types=1);

namespace MageSuite\ErpConnector\Model\Command;

class FormatDirectoryName
{
    public function __construct(
        protected \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {}

    public function execute(string $directoryName): string
    {
        return str_replace(
            [
                '{date}',
                '{date_time_short}',
                '{date_time_full}',
            ],
            [
                $this->dateTime->date('Y.m.d'),
                $this->dateTime->date('Ymd_His'),
                $this->dateTime->date('Y-m-d_H:i:s')
            ],
            $directoryName
        );
    }
}
