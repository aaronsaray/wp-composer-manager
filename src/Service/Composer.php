<?php
/**
 * Composer Service
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Service;

use AaronSaray\WPComposerManager\Model\Package;

/**
 * Class Composer
 * @package AaronSaray\WPComposerManager\Service
 */
class Composer
{
    /** @var string the directory of the composer install */
    public static $COMPOSER_DIRECTORY;

    /** @var string the destination file of the composer install */
    public static $COMPOSER_BINARY;

    /** @var  string the composer lock file */
    public static $COMPOSER_LOCK_FILE;

    /**
     * Installer constructor.
     */
    public function __construct()
    {
        self::$COMPOSER_DIRECTORY = realpath(__DIR__ . '/../../composer');
        self::$COMPOSER_BINARY = self::$COMPOSER_DIRECTORY . '/composer.phar';
        self::$COMPOSER_LOCK_FILE = self::$COMPOSER_DIRECTORY . '/composer.lock';
    }

    /**
     * @return array of all packages
     */
    public function getAllPackagesFromLockFile()
    {
        $packages = array();

        if (is_readable(self::$COMPOSER_LOCK_FILE)) {
            $jsonLockFile = json_decode(file_get_contents(self::$COMPOSER_LOCK_FILE));
            foreach ($jsonLockFile->packages as $packageObject) {
                $package = new Package();
                $package->setName($packageObject->name)->setVersion($packageObject->version);
                $packages[] = $package;
            }
        }

        return $packages;
    }

    /**
     * Get path to the composer file
     * @param $plugin
     * @return bool|string
     */
    public function getComposerJsonFileFromPluginId($plugin)
    {
        $composerFile = WP_PLUGIN_DIR . '/' . dirname($plugin);
        if (!file_exists($composerFile)) $composerFile = false;
        return $composerFile;
    }
}