<?php

namespace Smile\Perfreporter\Performers;

use Smile\Perfreporter\DomBuilders\TemplateBuilder;

class PerformancesLogger extends TemplateBuilder
{
    private static string $title = 'Performances and Measurement';
    private static string $start;
    private static int $total = 0;
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

    public static function setSteps(array $data) :self
    {
        self::$steps[] = $data;
        return new self;
    }

    public static function setHeader(string $key, mixed $value) :self
    {
        self::$header[$key] = $value;
        return new self;
    }

    public static function getResult() :self
    {
        // Remove older files until files count = self::$max
        self::cleanFiles(self::$max);

        // Create Report file
        $file = self::createFile(
            self::$title,
            self::setHTMLHeadTag(self::$title)
        );

        // Fill data in created file
        $content = self::fillFileLines(
            $file,
            self::$header,
            self::$steps
        );

        // Close created file
        self::reportFileClosure(
            $file,
            $content,
            self::setHTMLFooterTag()
        );

        return new self;
    }

    public static function deleteReports() :string
    {
        return self::removeReportsFolder();
    }
}