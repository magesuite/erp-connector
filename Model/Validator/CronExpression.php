<?php

namespace MageSuite\ErpConnector\Model\Validator;

class CronExpression
{
    public const CRON_EXPRESSION_REGEX = '/(@(annually|yearly|monthly|weekly|daily|hourly|reboot))|(@every (\d+(ns|us|µs|ms|s|m|h))+)|((((\d+,)+\d+|([\d\*]+(\/|-)\d+)|,|-|\d+|\*) ?){5,7})/';

    public function validate($cronExpression)
    {
        if (empty($cronExpression)) {
            return false;
        }

        $parts = explode(' ', $cronExpression);

        if (count($parts) != 5) {
            return false;
        }

        return preg_match(self::CRON_EXPRESSION_REGEX, $cronExpression);
    }
}
