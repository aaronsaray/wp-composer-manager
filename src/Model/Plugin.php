<?php
/**
 * Plugin Model
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Model;

/**
 * Class Plugin
 * @package AaronSaray\WPComposerManager\Model
 */
class Plugin
{
    /**
     * @var string the name of the plugin
     */
    protected $name;

    /**
     * @var string the description
     */
    protected $description;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Plugin
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Plugin
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
       
}