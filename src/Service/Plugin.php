<?php
/**
 * Manages plugins
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Service;
use AaronSaray\WPComposerManager\Model;

/**
 * Class Plugin
 * @package AaronSaray\WPComposerManager\Service
 */
class Plugin
{
    /**
     * @return array
     */
    public function findAllWithComposerJson()
    {
        $plugins = array();

        foreach (get_plugins() as $pluginFile => $pluginDetails) {
            $pluginDirectory = sprintf('%s/%s', WP_PLUGIN_DIR, plugin_dir_path($pluginFile));
            if (file_exists($pluginDirectory . 'composer.json')) {
                $plugin = new Model\Plugin();
                $plugin->setId($pluginFile)->setName($pluginDetails['Name'])->setDescription($pluginDetails['Description']);
                $plugins[] = $plugin;
            }
        }
        return $plugins;
    }

    /**
     * @param $id string the id which is usually plugin-name/plugin-name.php
     * @return boolean
     */
    public function doesPluginExistById($id)
    {
        return file_exists(WP_PLUGIN_DIR . '/' . $id);
    }
}