<?php
/**
 * Finds Plugins with composer json file
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Service;
use AaronSaray\WPComposerManager\Model\Plugin;

/**
 * Class PluginFinder
 * @package AaronSaray\WPComposerManager\Service
 */
class PluginFinder
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
                $plugin = new Plugin();
                $plugin->setName($pluginDetails['Name'])->setDescription($pluginDetails['Description']);
                $plugins[] = $plugin;
            }
        }
        return $plugins;
    }
}