<?php

namespace Smile\DomBuilders;

class TemplateBuilder
{
    private static string $html;

    protected static function setHTMLHeadTag($title) :string
    {
        date_default_timezone_set('Europe/Paris');
        ob_start(); ?>
        <!DOCTYPE html>
        <html lang="en_EN">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title><?= $title ?> at <?= date("Y-m-d H:i") ?></title>
            <style>
                :root {
                    --primary-bg: steelblue;
                    --secondary-bg: firebrick;
                    --primary-color: #141414;
                    --secondary-color: #fff;
                    --hover-bg: lightblue;
                    --hover-color: #fff;
                }
                <?= file_get_contents(__DIR__ . '/assets/style.css') . "\n" ?>
            </style>
        </head>
        <body>
        <span id="export-pdf">Export PDF</span>
        <?php return ob_get_clean();
    }

    protected static function setHTMLFooterTag() :string
    {
        ob_start(); ?>
        <script>
            <?= file_get_contents(__DIR__ . '/assets/script.js') . "\n" ?>
        </script>
        </body>
        </html>
        <?php return ob_get_clean();
    }

    protected static function createFile($reportFolder, $title) :string
    {
        if (!file_exists($reportFolder) && !mkdir($reportFolder, 0777, true) && !is_dir($reportFolder)) {

            throw new \RuntimeException(sprintf('Directory "%s" was not created', $reportFolder));
        }

        $file = $reportFolder . '/' . date("YmdHis") . '-perf-report.html';
        self::$html = self::setHTMLHeadTag($title) . "\n";
        return $file;
    }

    protected static function cleanFiles($reportFolder, $max) :void
    {
        $files = scandir($reportFolder);
        $excepts = ['.', '..'];
        $reports = [];

        foreach ($files as $file) {

            if (!in_array($file, $excepts, true) && is_file($reportFolder . '/' . $file)) {

                $reports[] = $reportFolder . '/' . $file;
            }
        }

        if (count($reports) >= $max) {

            $counter = count($reports);

            while ($counter >= $max) {

                $shift = array_shift($reports);
                if (file_exists($shift)) {
                    unlink($shift);
                }
                $counter--;
            }
        }
    }

    protected static function reportFileClosure(string $file) :void
    {
        $create = fopen($file, 'wb');
        $txt = self::$html;
        $txt .= "\n" . self::setHTMLFooterTag();

        $array = explode("\n", $txt);
        $content = '';
        foreach ($array as $row) {
            $content .= trim(str_replace(["\r", "\n", "\t"], '', $row));
        }
        fwrite($create, $content);
        fclose($create);
    }
}