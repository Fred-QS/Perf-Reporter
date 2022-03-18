<?php

namespace Smile\Performers;

use Smile\DomBuilders\TemplateBuilder;

class PerformancesLogger extends TemplateBuilder
{
    private static string $title;
    private static int $end;
    private static array $steps;
    private static array $header;
    private static string $reportFolder;
    private static int $max = 4;

    public static function setMax($val) :self
    {
        self::$max = $val;
        return new self;
    }

    public static function setTitle($data) :self
    {
        self::$title = $data;
        self::$reportFolder = dirname(__DIR__, 2) . '/reports';
        return new self;
    }

    public static function setEnd($data) :self
    {
        self::$end = $data;
        return new self;
    }

    public static function setSteps($data) :self
    {
        self::$steps[] = $data;
        return new self;
    }

    public static function setHeader($key, $value) :self
    {
        self::$header[$key] = $value;
        return new self;
    }

    public static function getResult() :self
    {
        /*self::cleanFiles(self::$reportFolder, self::$max);
        $file = self::createFile(self::$reportFolder, self::$title);

        self::reportFileClosure($file);*/
        dump(self::test());
        return new self;
    }
}