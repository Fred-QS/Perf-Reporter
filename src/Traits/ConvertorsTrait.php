<?php

namespace Smile\Perfreporter\Traits;

use Carbon\Carbon;

trait ConvertorsTrait
{
    protected static function convertMicrosecondsToHumanReadableFormat(float $micro) :string
    {
        return 'test';
    }

    protected static function convertBytesToHumanReadableFormat(int $val) :string
    {
        return 'test';
    }

    protected static function convertDateFromFIleName(string $date) :string
    {
        return 'test';
    }
}