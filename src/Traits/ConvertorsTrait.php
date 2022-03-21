<?php

namespace Smile\Perfreporter\Traits;

trait ConvertorsTrait
{
    /**
     * @param float $micro
     * @return string
     */
    protected static function convertMicrosecondsToHumanReadableFormat(float $micro) :string
    {
        $milli = $micro * 1000;
        $res = round($milli, 4) . 'ms';
        if ($milli > 1000) {
            $res = round($milli / 1000, 4) . 's';
        }
        return $res;
    }

    /**
     * @param int $bytes
     * @return string
     */
    protected static function convertBytesToHumanReadableFormat(int $bytes) :string
    {
        $i = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $i), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
    }

    /**
     * @param string $date
     * @return string
     */
    protected static function convertDateFromFileName(string $date) :string
    {
        $date = str_replace('-perf-report.html', '', $date);
        $y = substr($date, 0, 4);
        $m = substr($date, 4, 2);
        $d = substr($date, 6, 2);
        $h = substr($date, 8, 2);
        $i = substr($date, 10, 2);
        $s = substr($date, 12, 2);
        return $y . '-' . $m . '-' . $d . ' ' . $h . ':' . $i . ':' . $s;
    }

    /**
     * @param string $path
     * @return string
     */
    protected static function convertImageToBase64(string $path) :string
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}