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
        $baseComposerJsonArray = json_decode(file_get_contents(self::$COMPOSER_DIRECTORY . '/composer.json'), true);
        $pluginComposerJsonArray = json_decode(file_get_contents($this->getComposerJsonFileFromPluginId($plugin->getId())), true);

        $baseComposerJsonArray = $this->mergeAutoload($baseComposerJsonArray, $pluginComposerJsonArray, $plugin);
        $baseComposerJsonArray = $this->mergeRequire($baseComposerJsonArray, $pluginComposerJsonArray);


        if (!defined('JSON_PRETTY_PRINT')) define('JSON_PRETTY_PRINT', 128); // does nothing though...
        if (!defined('JSON_UNESCAPED_SLASHES')) define('JSON_UNESCAPED_SLASHES', 64);
        if (!file_put_contents(self::$COMPOSER_DIRECTORY . '/composer.json', json_encode($baseComposerJsonArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
            throw new \Exception('Unable to write merged composer.json file.');
        }

        $installCommand = sprintf(
            'COMPOSER_VENDOR_DIR=%s COMPOSER_HOME=%s %s update -d %s 2>&1',
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

    /**
     * @param $baseComposerJsonArray
     * @param $pluginComposerJsonArray
     * @param Model\Plugin $plugin
     * @return array
     */
    protected function mergeAutoload($baseComposerJsonArray, $pluginComposerJsonArray, Model\Plugin $plugin)
    {
        if (isset($pluginComposerJsonArray['autoload']['psr-4'])) {
            foreach ($pluginComposerJsonArray['autoload']['psr-4'] as $namespace => $value) {
                $pluginComposerJsonArray['autoload']['psr-4'][$namespace] = sprintf('../plugins/%s/%s', dirname($plugin->getId()), $value);
            }

            if (!isset($baseComposerJsonArray['autoload']['psr-4'])) $baseComposerJsonArray['autoload']['psr-4'] = array();
            $baseComposerJsonArray['autoload']['psr-4'] = array_merge($baseComposerJsonArray['autoload']['psr-4'], $pluginComposerJsonArray['autoload']['psr-4']);
        }

        return $baseComposerJsonArray;
    }

    /**
     * Merge in required
     *
     * @note this fails if the values are not matching - which needs to be addressed
     *
     * @param $baseComposerJsonArray
     * @param $pluginComposerJsonArray
     * @return mixed
     */
    protected function mergeRequire($baseComposerJsonArray, $pluginComposerJsonArray)
    {
        if (isset($pluginComposerJsonArray['require'])) {
            if (!isset($baseComposerJsonArray['require'])) $baseComposerJsonArray['require'] = array(); //should never happen
            $baseComposerJsonArray['require'] = array_merge($baseComposerJsonArray['require'], $pluginComposerJsonArray['require']);
        }

        return $baseComposerJsonArray;
    }
}