<?php

namespace Smile\Perfreporter;

use ErrorException;
use Smile\Perfreporter\DomBuilders\FileSystem;

define('DEPLOYER_FILE_PATH', explode('/vendor', __DIR__)[0]);

class FilesDeployer extends FileSystem
{
    /**
     * @var string
     */
    private static string $path = DEPLOYER_FILE_PATH . '/src';

    /**
     * @return void
     */
    public static function add() :void
    {
        if (file_exists(self::$path)) {

            try {
                $message = "\n" . self::addFolder('command');
                $message .= "\n" . self::addFolder('controller');
                $message .= "\n" . 'Perf-Reporter folders and files have been successfully added to your project !';
                echo $message . "\n\n";
                return;

            } catch (ErrorException $e) {
                echo "\n\n" . 'An error has occurred: ' . $e->getMessage() . "\n\n";
                return;
            }
        }
        echo "\n" . '<your_project>/src folder does not exist. Are you sure this is a Symfony project ?' . "\n\n";
    }

    /**
     * @return void
     */
    public static function remove() :void
    {
        if (file_exists(self::$path)) {

            try {

                $message = "\n" . self::removeReportsFolder();
                $message .= "\n" . self::removeAddedFolders('command');
                $message .= "\n" . self::removeAddedFolders('controller');
                $message .= "\n" . 'Perf-Reporter folders and files have been successfully removed from your project !';
                echo "\n" . $message . "\n\n";
                return;

            } catch (ErrorException $e) {
                echo "\n" . 'An error has occurred: ' . $e->getMessage() . "\n\n";
                return;
            }
        }
        echo "\n\n" . '<your_project>/src folder does not exist. Are you sure this is a Symfony project ?' . "\n\n";
    }
}