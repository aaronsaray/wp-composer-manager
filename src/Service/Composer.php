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
    /**
     * @param $lockFile string location of lock file
     * @return array of all packages
     */
    public function getAllPackagesFromLockFile($lockFile)
    {
        $packages = array();

        if (is_readable($lockFile)) {
            $jsonLockFile = json_decode(file_get_contents($lockFile));
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