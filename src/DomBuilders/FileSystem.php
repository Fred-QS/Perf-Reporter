<?php

namespace Smile\Perfreporter\DomBuilders;

define("SMILE_REPORTS_PATH", explode('vendor', __DIR__)[0] . 'reports');

class FileSystem
{
    protected static string $reportFolder = SMILE_REPORTS_PATH;
    private static string $html = '';

    protected static function createFile(string $title, string $head) :string
    {
        if (!file_exists(self::$reportFolder) && !mkdir(self::$reportFolder, 0777, true) && !is_dir(self::$reportFolder)) {

            throw new \RuntimeException(sprintf('Directory "%s" was not created', self::$reportFolder));
        }

        $file = self::$reportFolder . '/' . (date("YmdHis")) . '-perf-report.html';
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

    protected static function reportFileClosure(string $file, string $lines, string $footer) :string
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

        return $txt;
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

            rmdir(self::$reportFolder);
            return 'reports/ folder as been removed.';
        }

        return 'reports/ folder does not exist.';
    }

    protected static function removeAddedFolders(string $folder) :string
    {
        $dir = null;
        $name = null;

        switch ($folder) {

            case 'command':
                $dir = dirname(self::$reportFolder . '/src/Command/PerfReporter');
                $name = 'Command/PerfReporter';
                break;

            case 'controller':
                $dir = dirname(self::$reportFolder . '/src/Controller/DisplayPerfReportsController.php');
                $name = 'Controller/DisplayPerfReportsController.php';
                break;

            default: break;
        }

        if (!is_null($dir) && file_exists($dir)) {

            if ($folder === 'controller') {

                unlink($dir);
                return $name . '/ file has been removed.';

            } else {

                $excepts = ['.', '..'];
                $files = scandir($dir);

                foreach ($files as $file) {

                    $fullPath = $dir . '/' . $file;

                    if (!in_array($file, $excepts, true) && is_file($fullPath) && file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }

                rmdir($dir);
                return $name . '/ folder has been removed.';
            }
        }

        return $name . '/ does not exist.';
    }

    protected static function convertImageToBase64(string $path) :string
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    protected static function addFolder($folder) :string
    {
        $dest = null;
        $name = null;
        $original = null;

        switch ($folder) {
            case 'command':
                $original = dirname(__DIR__) . '/Command';
                $dest = dirname(self::$reportFolder) . '/src/Command/PerfReporter';
                $name = 'Command/PerfReporter';
                break;
            case 'controller':
                $original = dirname(__DIR__) . '/Controller/DisplayPerfReportsController.php';
                $dest = dirname(self::$reportFolder) . '/src/Controller/DisplayPerfReportsController.php';
                $name = 'Controller/DisplayPerfReportsController.php';
                break;
            default: break;
        }

        if (!is_null($original) && file_exists($original)) {

            if ($folder === 'controller') {

                if (!copy($original, $dest)) {
                    return $name . ' copy failed...';
                }

                return $name . '/ file has been added.';
            }

            if ($folder === 'command') {

                if (!file_exists($dest) && !mkdir($dest, 0777, true) && !is_dir($dest)) {

                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dest));
                }

                $files = scandir($original);
                $excepts = ['.', '..'];
                foreach ($files as $file) {

                    if (!in_array($file, $excepts, true) && !copy($original . '/' . $file, $dest . '/' . $file)) {
                        return $name . ' copy failed...';
                    }
                }

                return $name . '/ files have been added.';
            }

            return $folder . ' is unknown mode...';
        }

        return $name . '/ does not exist.';
    }

    protected static function getSelectedReport(string $path) :string
    {
        return file_get_contents($path);
    }
}