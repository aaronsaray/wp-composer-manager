<?php
/**
 * Reads composer lock files
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Service;

use AaronSaray\WPComposerManager\Model\Package;

/**
 * Class LockFileReader
 * @package AaronSaray\WPComposerManager\Service
 */
class LockFileReader
{
    /**
     * @var string the lock file location
     */
    protected $lockFile;

    /**
     * LockFileReader constructor.
     * @param $lockFile string
     */
    public function __construct($lockFile)
    {
        $this->lockFile = $lockFile;
    }

    /**
     * @return array of all packages
     */
    public function getAllPackages()
    {
        $packages = array();

        if (is_readable($this->lockFile)) {
            $jsonLockFile = json_decode(file_get_contents($this->lockFile));
            foreach ($jsonLockFile->packages as $packageObject) {
                $package = new Package();
                $package->setName($packageObject->name)->setVersion($packageObject->version);
                $packages[] = $package;
            }
        }

        return $packages;
    }
}