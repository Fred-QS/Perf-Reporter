<?php

namespace Smile\Perfreporter;

use Smile\Perfreporter\DomBuilders\FileSystem;

define('DEPLOYER_FILE_PATH', explode('/vendor', __DIR__)[0]);

class FilesDeployer extends FileSystem
{
    private static string $path = DEPLOYER_FILE_PATH . '/src';
    public static function add() :string
    {
        if (file_exists(self::$path)) {

            try {


                return 'Perf-Reporter folders and files have been successfully added to your project !';

            } catch (\ErrorException $e) {
                return 'An error has occurred: ' . $e->getMessage();
            }
        }
        return '<your_project>/src folder does not exist. Are you sure this is a Symfony project ?';
    }

    public static function remove() :string
    {
        if (file_exists(self::$path)) {

            try {

                $message = self::removeReportsFolder();
                $message .= "\n" . self::removeAddedFolders('command');
                $message .= "\n" . self::removeAddedFolders('controller');
                $message .= "\n" . 'Perf-Reporter folders and files have been successfully removed from your project !';
                return $message;

            } catch (\ErrorException $e) {
                return 'An error has occurred: ' . $e->getMessage();
            }
        }
        return '<your_project>/src folder does not exist. Are you sure this is a Symfony project ?';
    }
}