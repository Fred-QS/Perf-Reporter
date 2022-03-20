<?php

namespace Smile\Perfreporter\Performers;

use JetBrains\PhpStorm\Pure;
use Smile\Perfreporter\DomBuilders\TemplateBuilder;
use Smile\Perfreporter\Traits\ConvertorsTrait;

class PerformancesLogger extends TemplateBuilder
{
    use ConvertorsTrait;

    public static function setTimezone(string $zone) :self
    {
        self::$timezone = $zone;
        return new self;
    }

    public static function setLocale(string $locale) :self
    {
        self::$locale = $locale;
        return new self;
    }

    public static function setMax(int $val) :self
    {
        self::$max = $val;
        return new self;
    }

    public static function setTitle(string $data) :self
    {
        self::$title = $data;
        return new self;
    }

    public static function setAppOwnerLogo(string $data) :self
    {
        $authorized = ['jpg', 'jpeg', 'png', 'svg', 'bmp'];
        $split = explode('.', $data);
        $ext = end($split);

        if (file_exists($data) && is_file($data) && in_array($ext, $authorized, true)) {

            if ($ext !== 'svg') {
                self::$app_owner_logo = '<span id="customer-logo" class="image"><img src="' . self::convertImageToBase64($data) . '" alt="App Owner Logo"></span>';
            } else {
                self::$app_owner_logo = '<span id="customer-logo" class="vector">' . str_replace(['width', 'height'], ['b', 'c'], file_get_contents($data)) . '</span>';
            }

        }
        return new self;
    }

    public static function setTotal(int $data) :self
    {
        self::$total = $data;
        return new self;
    }

    public static function setStep(string $data) :self
    {
        $time = microtime(true);
        self::$steps[] = [
            'Information' => $data,
            'Trace' => debug_backtrace(),
            'Time lapse' => $time,
            'Memory usage' => memory_get_usage()
        ];
        self::$total += $time;
        return new self;
    }

    public static function setHeader(string $key, mixed $value) :self
    {
        self::$header[$key] = $value;
        return new self;
    }

    public static function getResult() :string
    {
        // Remove older files until files count = self::$max
        self::cleanFiles(self::$max);

        // Set Timezone
        date_default_timezone_set(self::$timezone);
        $fileName = (date("YmdHis")) . '-perf-report';

        // Create Report file
        $file = self::createFile(
            $fileName,
            self::$title,
            self::setHTMLHeadTag(
                self::$title,
                self::$app_owner_logo
            )
        );

        // Fill data in created file
        $content = self::fillFileLines(
            $file,
            self::$header,
            self::$steps,
            self::$total
        );

        // Close created file
        return self::reportFileClosure(
            $file,
            $content,
            self::setHTMLFooterTag($fileName)
        );
    }

    public static function deleteReports() :string
    {
        return self::removeReportsFolder();
    }

    public static function getReportList(string $mode = '') : string|array
    {
        if ($mode === 'html') {
            return self::setHTMLListForFrontEnd();
        }
        return self::getExistingReports();
    }

    #[Pure]
    public static function getReport(string $path) :string
    {
        return self::selectedReport($path);
    }
}