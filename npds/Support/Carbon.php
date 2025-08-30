<?php

namespace Npds\Support;

use Carbon\Carbon as BaseCarbon;
use Carbon\CarbonImmutable as BaseCarbonImmutable;
use Npds\Conditionable\Traits\Conditionable;

class Carbon extends BaseCarbon
{
    use Conditionable;

    /**
     * {@inheritdoc}
     */
    public static function setTestNow($testNow = null): void
    {
        BaseCarbon::setTestNow($testNow);
        BaseCarbonImmutable::setTestNow($testNow);
    }
}
