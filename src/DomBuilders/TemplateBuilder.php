<?php

namespace Smile\Perfreporter\DomBuilders;

use Smile\Perfreporter\DomBuilders\FileSystem;
use Smile\Perfreporter\Traits\ConvertorsTrait;
use Carbon\Carbon;

class TemplateBuilder extends FileSystem
{
    use ConvertorsTrait;

    protected static function setHTMLHeadTag(string $title) :string
    {
        date_default_timezone_set('Europe/Paris');
        Carbon::setLocale('fr');
        ob_start(); ?>
        <!DOCTYPE html>
        <html lang="en_EN">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title><?= $title ?> au <?= Carbon::now()->isoFormat('LLL') ?></title>
            <style>
                :root {
                    --primary-bg: #006FB8;
                    --secondary-bg: #EF7B47;
                    --primary-color: #141414;
                    --secondary-color: #fff;
                    --hover-bg: #EF7B47;
                    --hover-color: #fff;
                }
                <?= file_get_contents(dirname(__DIR__, 2) . '/public/css/reset.css') . "\n" ?>
                <?= file_get_contents(dirname(__DIR__, 2) . '/public/css/style.css') . "\n" ?>
            </style>
        </head>
        <body>
        <span id="export-pdf">Print</span>
        <section>
        <img id="smile-logo" src="<?= self::convertImageToBase64(dirname(__DIR__, 2) . '/public/img/logo.png') ?>" alt="Smile Open Source">
        <div id="title">
            <small>SMILE - Perfs Reporter Logs</small>
            <h1><?= $title ?></h1>
            <i>au <?= Carbon::now()->isoFormat('LLL') ?></i>
        </div>
        <?php return ob_get_clean();
    }

    protected static function setHTMLFooterTag(string $fileName) :string
    {
        ob_start(); ?>
        </section>
        <script>
            const id = '/perf-reporter/<?= $fileName ?>';
            <?= file_get_contents(dirname(__DIR__, 2) . '/public/js/script.js') . "\n" ?>
        </script>
        </body>
        </html>
        <?php return ob_get_clean();
    }

    protected static function fillFileLines(string $file, array $header, array $steps, float $total) :string
    {
        $parsed_header = self::parseToList($header);
        $parsed_steps = self::stepsParser($steps);
        ob_start(); ?>
        <?= $parsed_header . "\n" . $parsed_steps ?>
        <?php return ob_get_clean();
    }

    private static function parseToList(array $arr) :string
    {
        $array = [];
        foreach ($arr as $key => $value) {
            if (!is_array($value) && !is_object($value) && !is_callable($value)) {
                $array[$key] = $value;
            } else {
                $array[$key] = [];
                foreach ($value as $label => $row) {
                    if (!is_array($row) && !is_object($row) && !is_callable($row)) {
                        $array[$key][ucfirst($label)] = (!is_null($row)) ? '<span class="set">' . $row . '</span>' : '<span class="not-set">Not set</span>';
                    } else {
                        $row = (array)$row;
                        $array[$key][ucfirst($label)] = '<ul class="sub-lists">';
                        foreach ($row as $k => $v) {
                            if (!is_array($v) && !is_object($v) && !is_callable($v) && !is_null($v)) {
                                $array[$key][ucfirst($label)] .= '<li><b>' . $k . '</b>: ' . $v . '</li>';
                            }
                        }
                        $array[$key][ucfirst($label)] .= '</ul>';
                    }
                }
            }
        }

        $html = '<div class="lists-container">';
        foreach ($array as $key => $value) {
            $html .= '<div class="list-bloc"><p class="list-title">' . ucfirst($key) . '</p><ul>';
            if (!is_array($value) && !is_object($value) && !is_callable($value)) {
                $html .= '<li>' . $value . '</li>';
            } else {
                foreach ($value as $k => $v) {
                    $html .= '<li><b>' . ucfirst($k) . ':</b> ' . $v . '</li>';
                }
            }
            $html .= '</ul></div>';
        }
        $html .= '</div>';

        return $html;
    }

    private static function stepsParser($steps) : string
    {
        $html = '<div id="steps-container"><p class="steps-title">Steps</p>';
        if (!empty($steps)) {
            foreach ($steps as $step) {
                $html .= '<ul>';
                foreach ($step as $key => $value) {
                    if ($value !== '') {
                        $html .= '<li><b>' . $key . ': </b>' . $value . '</li>';
                    }
                }
                $html .= '</ul>';
            }
        } else {
            $html .= '<i>No step set.</i>';
        }
        $html .= '</div>';
        return $html;
    }

    protected static function getExistingReports() :array
    {
        $files = scandir(self::$reportFolder);
        $exists = [];
        $excepts = ['.', '..'];

        foreach ($files as $file) {

            $path = self::$reportFolder . '/' . $file;
            if (!in_array($file, $excepts, true) && is_file($path)) {

                $exists[] = [
                    'id' => str_replace('.html', '', $file),
                    'name' => 'Report from the ' . self::convertDateFromFIleName($file),
                    'path' => $path
                ];
            }
        }

        return $exists;
    }

    private static function mainTemplate(string $title, string $content) :string
    {
        ob_start(); ?>
        <!DOCTYPE html>
        <html lang="en_EN">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title><?= $title ?></title>
            <style>
                :root {
                    --primary-bg: #006FB8;
                    --secondary-bg: #EF7B47;
                    --primary-color: #141414;
                    --secondary-color: #fff;
                    --hover-bg: #EF7B47;
                    --hover-color: #fff;
                }
                <?= file_get_contents(dirname(__DIR__, 2) . '/public/css/reset.css') . "\n" ?>
                <?= file_get_contents(dirname(__DIR__, 2) . '/public/css/front.css') . "\n" ?>
            </style>
        </head>
        <body>
        <?= $content ?>
        </body>
        </html>
        <?php return ob_get_clean();
    }

    protected static function setHTMLListForFrontEnd() :string
    {
        $exists = self::getExistingReports();

        ob_start(); ?>
        <header>
            <img id="smile-logo" src="<?= self::convertImageToBase64(dirname(__DIR__, 2) . '/public/img/logo.png') ?>" alt="Smile Open Source">
            <p>Welcome to the Performance Report Package Page<br/><small>You must create reports by following the <a href="https://github.com/Fred-QS/Perf-Reporter/blob/main/README.md" target="_blank">README</a> file instructions</small></p>
            <img id="symfony-logo" src="<?= self::convertImageToBase64(dirname(__DIR__, 2) . '/public/img/symfony.png') ?>" alt="Symfony">
        </header>
        <main>
            <?php if (empty($exists)): ?>
                <p id="title">No performance report available</p>
            <?php else: ?>
                <p id="title">Performance reports list :</p>
                <ul id="reports-list">
                    <?php foreach ($exists as $file) : ?>
                        <li>
                            <a href="/perf-reporter/<?= $file['id'] ?>"><?= $file['name'] ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </main>
        <?php $content = ob_get_clean();

        return self::mainTemplate('Perfs Reports List', $content);
    }

    protected static function selectedReport(string $path) :string
    {
        return self::getSelectedReport($path);
    }
}