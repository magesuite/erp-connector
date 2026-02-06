<?php

declare(strict_types=1);

namespace MageSuite\ErpConnector\Test\Unit\Model\Validator;

class CronExpressionTest extends \PHPUnit\Framework\TestCase
{
    protected ?\MageSuite\ErpConnector\Model\Validator\CronExpression $cronExpressionValidator;

    public function setUp(): void
    {
        $this->cronExpressionValidator = new \MageSuite\ErpConnector\Model\Validator\CronExpression();
    }

    /**
     * @dataProvider getCronExpressions
     */
    public function testItValidatesCronSyntaxCorrectly(string $cronExpression, bool $isValid): void
    {
        $result = $this->cronExpressionValidator->validate($cronExpression);

        $this->assertEquals($isValid, $result);
    }

    public static function getCronExpressions(): array
    {
        return [
            'empty syntax' => ['', false],
            'at every minute' => ['* * * * *', true],
            'too many elements' => ['* * * * * *', false],
            'too few elements' => ['* * * *', false],
            'text instead of cron syntax' => ['invalid cron syntax', false],
            'at 10:29 on Monday, Tuesday, Wednesday, Thursday, and Friday' => ['29 10 * * 1,2,3,4,5', true],
            'at minute 9, 24, 39, and 54' => ['9,24,39,54 * * * *', true],
            'at every 15th minute' => ['*/15 * * * *', true],
            'at 02:01 on day-of-month 31 in February' => ['1 2 31 2 *', true],
            'at every 10th minute past every hour from 0 through 3 and every hour from 6 through 23' => ['*/10 0-3,6-23 * * *', true],
            'at minute 30 past every hour from 0 through 3 and every hour from 6 through 23' => ['30 0-3,6-23 * * *', true],
            'at 02:00' => ['00 02 * * *', true],
            'at minute 9 and 12' => ['9,12 * * * *', true],
        ];
    }
}
