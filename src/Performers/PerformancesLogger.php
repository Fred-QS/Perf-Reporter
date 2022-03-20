<?php

namespace Smile\Perfreporter\Performers;

use JetBrains\PhpStorm\Pure;
use Smile\Perfreporter\DomBuilders\TemplateBuilder;

class PerformancesLogger extends TemplateBuilder
{
    private static string $title = 'Performances and Measurement';
    private static string $start;
    private static float $total = 0;
    private static array $steps = [];
    private static array $header = [];
    private static int $max = 4;

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

        $fileName = (date("YmdHis")) . '-perf-report';

        // Create Report file
        $file = self::createFile(
            $fileName,
            self::$title,
            self::setHTMLHeadTag(self::$title)
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