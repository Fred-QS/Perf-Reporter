<?php

namespace Smile\Perfreporter\DomBuilders;

use JetBrains\PhpStorm\Pure;
use Smile\Perfreporter\DomBuilders\FileSystem;
use Smile\Perfreporter\Traits\ConvertorsTrait;
use Carbon\Carbon;

class TemplateBuilder extends FileSystem
{
    use ConvertorsTrait;

    protected static string $title = 'Performances and Measurement';
    protected static string $app_owner_logo = '';
    protected static float $start = 0;
    protected static float $total = 0;
    protected static array $steps = [];
    protected static array $header = [];
    protected static int $alarm_step = 3;
    protected static int $max = 4;
    protected static string $timezone = 'Europe/London';
    protected static string $locale = 'en';

    protected static function setHTMLHeadTag(string $title, string $logo) :string
    {
        date_default_timezone_set(self::$timezone);
        Carbon::setLocale(self::$locale);

        ob_start(); ?>
        <!DOCTYPE html>
        <html lang="en_EN">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title><?= $title ?> au <?= Carbon::now()->isoFormat('LLL') ?></title>
            <link rel="icon" href="<?= self::convertImageToBase64(dirname(__DIR__, 2) . '/public/img/favicon.jpg') ?>">
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
        <span id="export-pdf">
            <svg viewBox="0 0 24 24">
                <path d="M18 3v3.984h-12v-3.984h12zM18.984 12q0.422 0 0.727-0.281t0.305-0.703-0.305-0.727-0.727-0.305-0.703 0.305-0.281 0.727 0.281 0.703 0.703 0.281zM15.984 18.984v-4.969h-7.969v4.969h7.969zM18.984 8.016q1.219 0 2.109 0.891t0.891 2.109v6h-3.984v3.984h-12v-3.984h-3.984v-6q0-1.219 0.891-2.109t2.109-0.891h13.969z"></path>
            </svg>
            Print report
        </span>
        <section>
        <img id="smile-logo" src="<?= self::convertImageToBase64(dirname(__DIR__, 2) . '/public/img/logo.png') ?>" alt="Smile Open Source">
        <?php if ($logo !== ''): ?>
        <?= $logo ?>
    <?php endif; ?>
        <div id="title">
            <small>SMILE - Perf Reporter Logs</small>
            <h1><?= $title ?></h1>
            <i>au <?= Carbon::now()->isoFormat('LLL') ?></i>
        </div>
        <?php return ob_get_clean();
    }

    protected static function setHTMLFooterTag(string $fileName) :string
    {
        $script = file_get_contents(dirname(__DIR__, 2) . '/public/js/script.js') . "\n";
        ob_start(); ?>
        </section>
        <script>
            const id = '/perf-reporter/<?= $fileName ?>';
            <?= $script ?>
        </script>
        </body>
        </html>
        <?php return ob_get_clean();
    }

    protected static function fillFileLines(string $file, array $header, array $steps, float $total) :string
    {
        $parsed_header = self::parseToList($header);
        $parsed_steps = self::stepsParser($steps);
        $parsed_total = self::totalRender();
        ob_start(); ?>
        <?= $parsed_header . "\n" . $parsed_total . "\n" . $parsed_steps ?>
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

    private static function totalRender(): string
    {
        $color = 'var(--primary-bg)';

        if (self::$total > self::$alarm_step - (self::$alarm_step / 3) && self::$total < self::$alarm_step) {
            $color = 'var(--secondary-bg)';
        }

        if (self::$total >= self::$alarm_step) {
            $color = 'firebrick';
        }

        $html = '<div id="total-container"><p class="total-title" style="background-color: ' . $color . ';">Total execution time<span>';
        $html .= self::convertMicrosecondsToHumanReadableFormat(self::$total);
        $html .= '</span></p></div>';
        return $html;
    }

    private static function stepsParser($steps) : string
    {
        $html = '<div id="steps-container"><p class="steps-title">Steps</p>';
        if (!empty($steps)) {
            foreach ($steps as $step) {
                $html .= '<ul>';
                foreach ($step as $key => $value) {
                    if (!is_array($value) && $value !== '') {
                        $html .= '<li>' . $key . ': <span>' . $value . '</span></li>';
                    } else {
                        // Backtraces
                        $array = $value[0];
                        $html .= '<li>File: <span>' . $array['file'] . '</span></li>';
                        $html .= '<li>Line: <span>' . $array['line'] . '</span></li>';
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

                date_default_timezone_set(self::$timezone);
                Carbon::setLocale(self::$locale);

                $date = self::convertDateFromFileName($file);
                $date = Carbon::parse($date)->isoFormat('LLLL');

                $exists[] = [
                    'id' => str_replace('.html', '', $file),
                    'name' => ucwords($date),
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
        <html lang="<?= self::$locale . '_' . strtoupper(self::$locale) ?>">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title><?= $title ?></title>
            <link rel="icon" href="<?= self::convertImageToBase64(dirname(__DIR__, 2) . '/public/img/favicon.jpg') ?>">
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
            <p>Welcome to the Performance Report Package Page<br/><small>You can create reports by following the <a href="https://github.com/Fred-QS/Perf-Reporter/blob/main/README.md" target="_blank">README</a> file instructions</small></p>
            <img id="symfony-logo" src="<?= self::convertImageToBase64(dirname(__DIR__, 2) . '/public/img/symfony.png') ?>" alt="Symfony">
        </header>
        <div id="params">
            <p>Parameters</p>
            <ul id="params-list">
                <li>
                    <svg viewBox="0 0 32 32">
                        <path d="M6.997 5.002l0.003-0.002c0 0 0 0-0 0s0 0 0 0c0.004-1.106 0.897-2 1.997-2 0.007-1.106 0.902-2 2.005-2h10.997l6 7v18.009c0 1.093-0.894 1.991-1.997 1.991-0.007 1.106-0.899 2-2 2-0.007 1.106-0.899 2-2 2h-15.005c-1.107 0-1.997-0.899-1.997-2.007v-22.985c0-1.109 0.894-2.007 1.997-2.007v0.002zM7 6c-0.552 0-1 0.455-1 0.995v23.009c0 0.55 0.455 0.995 1 0.995h15c0.552 0 1-0.455 1-0.995l-14.003-0.005c-1.107 0-1.997-0.899-1.997-2.007v-21.993c0 0 0 0-0-0v0zM9 4c-0.552 0-1 0.455-1 0.995v23.009c0 0.55 0.455 0.995 1 0.995h15c0.552 0 1-0.455 1-0.995v-0.005h-14.003c-1.107 0-1.997-0.899-1.997-2.007v-21.993h-0zM21 2h-10.004c-0.55 0-0.996 0.455-0.996 0.995v23.009c0 0.55 0.455 0.995 1 0.995h15c0.552 0 1-0.445 1-0.993v-17.007h-4.002c-1.103 0-1.998-0.887-1.998-2.006v-4.994zM22 2.5v4.491c0 0.557 0.451 1.009 0.997 1.009h3.703l-4.7-5.5z"></path>
                    </svg>
                    Maximum number of reports:<span><?= self::$max ?></span>
                </li>
                <li>
                    <svg viewBox="0 0 20 20">
                        <path d="M10 20c-5.523 0-10-4.477-10-10s4.477-10 10-10v0c5.523 0 10 4.477 10 10s-4.477 10-10 10v0zM10 18c4.418 0 8-3.582 8-8s-3.582-8-8-8v0c-4.418 0-8 3.582-8 8s3.582 8 8 8v0zM9 10.41v-6.41h2v5.59l3.95 3.95-1.41 1.41-4.54-4.54z"></path>
                    </svg>
                    Timezone:<span><?= self::$timezone ?></span>
                </li>
                <li>
                    <svg viewBox="0 0 24 28">
                        <path d="M10.219 16.844c-0.031 0.109-0.797-0.25-1-0.328-0.203-0.094-1.125-0.609-1.359-0.766s-1.125-0.891-1.234-0.938v0c-0.562 0.859-1.281 1.875-2.094 2.828-0.281 0.328-1.125 1.391-1.641 1.719-0.078 0.047-0.531 0.094-0.594 0.063 0.25-0.187 0.969-1.078 1.281-1.437 0.391-0.453 2.25-3.047 2.562-3.641 0.328-0.594 1.312-2.562 1.359-2.75-0.156-0.016-1.391 0.406-1.719 0.516-0.313 0.094-1.172 0.297-1.234 0.344-0.063 0.063-0.016 0.25-0.047 0.313s-0.313 0.203-0.484 0.234c-0.156 0.047-0.516 0.063-0.734 0-0.203-0.047-0.391-0.25-0.438-0.328 0 0-0.063-0.094-0.078-0.359 0.187-0.063 0.5-0.078 0.844-0.172s1.188-0.344 1.641-0.5 1.328-0.484 1.594-0.547c0.281-0.047 0.984-0.516 1.359-0.641s0.641-0.281 0.656-0.203 0 0.422-0.016 0.516c-0.016 0.078-0.766 1.547-0.875 1.781-0.063 0.125-0.5 0.953-1.203 2.047 0.25 0.109 0.781 0.328 1 0.438 0.266 0.125 2.125 0.906 2.219 0.938s0.266 0.75 0.234 0.875zM7.016 9.25c0.047 0.266-0.031 0.375-0.063 0.438-0.156 0.297-0.547 0.5-0.781 0.594s-0.625 0.187-0.938 0.187c-0.141-0.016-0.422-0.063-0.766-0.406-0.187-0.203-0.328-0.75-0.266-0.688s0.516 0.125 0.719 0.078 0.688-0.187 0.906-0.25c0.234-0.078 0.703-0.203 0.859-0.219 0.156 0 0.281 0.063 0.328 0.266zM17.922 11.266l0.984 3.547-2.172-0.656zM0.609 23.766l10.844-3.625v-16.125l-10.844 3.641v16.109zM20 18.813l1.594 0.484-2.828-10.266-1.563-0.484-3.375 8.375 1.594 0.484 0.703-1.719 3.297 1.016zM12.141 3.781l8.953 2.875v-5.938zM17 24.453l2.469 0.203-0.844 2.5-0.625-1.031c-1.266 0.812-2.828 1.437-4.312 1.687-0.453 0.094-0.969 0.187-1.422 0.187h-1.313c-1.656 0-4.672-0.984-5.984-1.937-0.094-0.078-0.125-0.141-0.125-0.25 0-0.172 0.125-0.297 0.281-0.297 0.141 0 0.875 0.453 1.078 0.547 1.406 0.703 3.375 1.344 4.953 1.344 1.953 0 3.281-0.25 5.063-1.016 0.516-0.234 0.969-0.531 1.453-0.797zM24 7.594v16.859c-12.078-3.844-12.094-3.844-12.094-3.844-0.25 0.109-11.453 3.891-11.609 3.891-0.125 0-0.234-0.078-0.281-0.203 0-0.016-0.016-0.031-0.016-0.047v-16.844c0.016-0.047 0.031-0.125 0.063-0.156 0.094-0.109 0.219-0.141 0.313-0.172 0.047-0.016 1-0.328 2.328-0.781v-6l8.719 3.094c0.109-0.031 9.828-3.391 9.969-3.391 0.172 0 0.313 0.125 0.313 0.328v6.531z"></path>
                    </svg>
                    Locale:<span><?= self::$locale ?></span>
                </li>
                <li>
                    <svg viewBox="0 0 28 28">
                        <path d="M14.25 26.5c0-0.141-0.109-0.25-0.25-0.25-1.234 0-2.25-1.016-2.25-2.25 0-0.141-0.109-0.25-0.25-0.25s-0.25 0.109-0.25 0.25c0 1.516 1.234 2.75 2.75 2.75 0.141 0 0.25-0.109 0.25-0.25zM3.844 22h20.312c-2.797-3.156-4.156-7.438-4.156-13 0-2.016-1.906-5-6-5s-6 2.984-6 5c0 5.563-1.359 9.844-4.156 13zM27 22c0 1.094-0.906 2-2 2h-7c0 2.203-1.797 4-4 4s-4-1.797-4-4h-7c-1.094 0-2-0.906-2-2 2.312-1.953 5-5.453 5-13 0-3 2.484-6.281 6.625-6.891-0.078-0.187-0.125-0.391-0.125-0.609 0-0.828 0.672-1.5 1.5-1.5s1.5 0.672 1.5 1.5c0 0.219-0.047 0.422-0.125 0.609 4.141 0.609 6.625 3.891 6.625 6.891 0 7.547 2.688 11.047 5 13z"></path>
                    </svg>
                    Execution time alarm:<span><?= self::$alarm_step ?>s</span>
                </li>
            </ul>
        </div>
        <main>
            <?php if (empty($exists)): ?>
                <p id="title">No performance report available</p>
            <?php else: ?>
                <p id="title">Performance reports list</p>
                <ul id="reports-list">
                    <?php foreach ($exists as $file) : ?>
                        <li>
                            <a href="/perf-reporter/<?= $file['id'] ?>">
                                <svg viewBox="0 0 32 32">
                                    <path d="M19.5 3h0.5l6 7v18.009c0 1.093-0.894 1.991-1.997 1.991h-15.005c-1.107 0-1.997-0.899-1.997-2.007v-22.985c0-1.109 0.897-2.007 2.003-2.007h10.497zM19 4h-10.004c-0.55 0-0.996 0.455-0.996 0.995v23.009c0 0.55 0.455 0.995 1 0.995h15c0.552 0 1-0.445 1-0.993v-17.007h-4.002c-1.103 0-1.998-0.887-1.998-2.006v-4.994zM20 4.5v4.491c0 0.557 0.451 1.009 0.997 1.009h3.703l-4.7-5.5z"></path>
                                </svg>
                                <?= $file['name'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </main>
        <?php $content = ob_get_clean();

        return self::mainTemplate('Perf Reports List', $content);
    }

    #[Pure]
    protected static function selectedReport(string $path) :string
    {
        return self::getSelectedReport($path);
    }
}