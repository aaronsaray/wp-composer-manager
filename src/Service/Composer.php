<?php
/**
 * Composer Service
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Service;

use AaronSaray\WPComposerManager\Model;

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

    /** @var string the composer vendor directory */
    public static $COMPOSER_VENDOR_DIRECTORY;

    /** @var string the lock file */
    public static $COMPOSER_LOCK_FILE;

    /**
     * Installer constructor.
     */
    public function __construct()
    {
        self::$COMPOSER_DIRECTORY = WP_CONTENT_DIR . '/composer';
        self::$COMPOSER_BINARY = self::$COMPOSER_DIRECTORY . '/composer.phar';
        self::$COMPOSER_VENDOR_DIRECTORY = self::$COMPOSER_DIRECTORY . '/vendor';
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
                $package = new Model\Package();
                $package->setName($packageObject->name)->setVersion($packageObject->version);
                $packages[] = $package;
            }
        }

        return $packages;
    }

    /**
     * Get path to the composer file
     * @param $pluginId
     * @return string
     * @throws \Exception
     */
    public function getComposerJsonFileFromPluginId($pluginId)
    {
        $composerFile = sprintf('%s/%s/composer.json', WP_PLUGIN_DIR , dirname($pluginId));
        if (!file_exists($composerFile)) {
            throw new \Exception('The composer.json file was not found: ' . $composerFile);
        }

        return $composerFile;
    }

    /**
     * Runs the composer update process, returns an array on success of the output
     *
     * @param Model\Plugin $plugin
     * @return mixed
     * @throws \Exception
     */
    public function runComposerUpdateForPlugin(Model\Plugin $plugin)
    {
        $baseComposerJson = json_decode(file_get_contents(self::$COMPOSER_DIRECTORY . '/composer.json'), true);
        $pluginComposerJson = json_decode(file_get_contents($this->getComposerJsonFileFromPluginId($plugin->getId())), true);

        if (isset($pluginComposerJson['autoload']['psr-4'])) {
            foreach ($pluginComposerJson['autoload']['psr-4'] as $namespace => $value) {
                $pluginComposerJson['autoload']['psr-4'][$namespace] = sprintf('../plugins/%s/%s', dirname($plugin->getId()), $value);
            }

            if (!isset($baseComposerJson['autoload']['psr-4'])) $baseComposerJson['autoload']['psr-4'] = array();
            $baseComposerJson['autoload']['psr-4'] = array_merge($baseComposerJson['autoload']['psr-4'], $pluginComposerJson['autoload']['psr-4']);
        }

        if (!defined('JSON_PRETTY_PRINT')) define('JSON_PRETTY_PRINT', 128); // does nothing though...
        if (!file_put_contents(self::$COMPOSER_DIRECTORY . '/composer.json', json_encode($baseComposerJson, JSON_PRETTY_PRINT))) {
            throw new \Exception('Unable to write merged composer.json file.');
        }

        $installCommand = sprintf(
            'COMPOSER_VENDOR_DIR=%s COMPOSER_HOME=%s %s install -d %s 2>&1',
            self::$COMPOSER_VENDOR_DIRECTORY,
            self::$COMPOSER_DIRECTORY,
            self::$COMPOSER_BINARY,
            self::$COMPOSER_DIRECTORY
        );

        exec($installCommand, $outputInstall, $returnVarInstall);
        if ($returnVarInstall !== 0) {
            throw new \Exception('Composer install failed with message: ' . implode(" ", $outputInstall));
        }

        return $outputInstall;
    }
}