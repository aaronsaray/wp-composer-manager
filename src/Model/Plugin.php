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
     * @var string the path/slug for this plugin
     */
    protected $id;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Plugin
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

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

    /**
     * @return string the plugin directory
     */
    public function getPluginDirectory()
    {
        return sprintf('%s/%s', WP_PLUGIN_DIR, dirname($this->id));
    }
}