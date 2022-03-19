<?php

namespace Smile\Perfreporter\DomBuilders;

define("SMILE_REPORTS_PATH", explode('vendor', __DIR__)[0] . '/reports');

class FileSystem
{
    private static string $reportFolder = SMILE_REPORTS_PATH;
    private static string $html = '';

    protected static function createFile(string $title, string $head) :string
    {
        if (!file_exists(self::$reportFolder) && !mkdir(self::$reportFolder, 0777, true) && !is_dir(self::$reportFolder)) {

            throw new \RuntimeException(sprintf('Directory "%s" was not created', self::$reportFolder));
        }

        $file = self::$reportFolder . '/' . date("YmdHis") . '-perf-report.html';
        self::$html = $head . "\n";
        return $file;
    }

    protected static function cleanFiles(int $max) :void
    {
        $dir = self::$reportFolder;

        if (file_exists($dir)) {

            $files = scandir($dir);
            $excepts = ['.', '..'];
            $reports = [];

            foreach ($files as $file) {

                if (!in_array($file, $excepts, true) && is_file($dir . '/' . $file)) {

                    $reports[] = $dir . '/' . $file;
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
    }

    protected static function reportFileClosure(string $file, string $lines, string $footer) :void
    {
        $create = fopen($file, 'wb');
        $txt = self::$html;
        $txt .= "\n" . $lines;
        $txt .= "\n" . $footer;

        $array = explode("\n", $txt);
        $content = '';
        foreach ($array as $row) {
            $content .= trim(str_replace(["\r", "\n", "\t"], '', $row));
        }
        fwrite($create, $content);
        fclose($create);
    }

    protected static function removeReportsFolder() :string
    {
        if (file_exists(self::$reportFolder)) {

            $excepts = ['.', '..'];
            $files = scandir(self::$reportFolder);

            foreach ($files as $file) {

                $fullPath = self::$reportFolder . '/' . $file;

                if (!in_array($file, $excepts, true) && is_file($fullPath) && file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            unlink(self::$reportFolder);
            return 'reports/ folder as been removed.';
        }

        return 'reports/ folder does not exist.';
    }
}