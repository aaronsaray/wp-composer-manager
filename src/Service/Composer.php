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

    /** @var string the composer lock file */
    public static $COMPOSER_LOCK_FILE;

    /** @var string the path for the merge file */
    public static $COMPOSER_MERGE_JSON_FILE;
    
    /** @var string the composer vendor directory */
    public static $COMPOSER_VENDOR_DIRECTORY;

    /**
     * Installer constructor.
     */
    public function __construct()
    {
        self::$COMPOSER_DIRECTORY = realpath(__DIR__ . '/../../composer');
        self::$COMPOSER_BINARY = self::$COMPOSER_DIRECTORY . '/composer.phar';
        self::$COMPOSER_LOCK_FILE = self::$COMPOSER_DIRECTORY . '/composer.lock';
        self::$COMPOSER_MERGE_JSON_FILE = self::$COMPOSER_DIRECTORY . '/composer-merge.json';
        self::$COMPOSER_VENDOR_DIRECTORY = WP_CONTENT_DIR . '/vendor';
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
        // copy composer.lock file to the plugin dir
        // run composer install
        // if successful move composer.lock file back
        if (!copy(self::$COMPOSER_LOCK_FILE, $plugin->getPluginDirectory() . '/composer.lock')) {
            throw new \Exception('Unable to copy composer.lock file to plugin dir ' . $plugin->getPluginDirectory());
        }

        $installCommand = sprintf(
            'COMPOSER_VENDOR_DIR=%s COMPOSER_HOME=%s %s update -d %s 2>&1',
            self::$COMPOSER_VENDOR_DIRECTORY,
            self::$COMPOSER_DIRECTORY,
            self::$COMPOSER_BINARY,
            $plugin->getPluginDirectory()
        );

        exec($installCommand, $outputInstall, $returnVarInstall);
        if ($returnVarInstall !== 0) {
            throw new \Exception('Composer install failed with message: ' . implode(" ", $outputInstall));
        }

        if (!rename($plugin->getPluginDirectory() . '/composer.lock', self::$COMPOSER_LOCK_FILE)) {
            throw new \Exception('Unable to move the composer.lock file after finishing installation.');
        }
        
        return $outputInstall;
    }
}