<?php
/**
 * Package model
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Model;

/**
 * Class Package
 * @package AaronSaray\WPComposerManager\Model
 */
class Package
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $version;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Package
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return Package
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }
}