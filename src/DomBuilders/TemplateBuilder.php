<?php

namespace Smile\DomBuilders;

use Smile\DomBuilders\FileSystem;

class TemplateBuilder extends FileSystem
{
    protected static function setHTMLHeadTag(string $title) :string
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
                <?= file_get_contents(dirname(__DIR__) . '/assets/style.css') . "\n" ?>
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
            <?= file_get_contents(dirname(__DIR__) . '/assets/script.js') . "\n" ?>
        </script>
        </body>
        </html>
        <?php return ob_get_clean();
    }

    protected static function fillFileLines(string $file, array $header, array $steps) :string
    {
        ob_start(); ?>
        test
        <?php return ob_get_clean();
    }
}