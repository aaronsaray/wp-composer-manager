<?php
/**
 * Finds Plugins with composer json file
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Service;

/**
 * Class PluginFinder
 * @package AaronSaray\WPComposerManager\Service
 */
class PluginFinder
{
    public function findAllWithComposerJson()
    {
        $plugins = array();

        foreach (get_plugins() as $pluginFile => $pluginDetails) {
            $pluginDirectory = sprintf('%s/%s', WP_PLUGIN_DIR, plugin_dir_path($pluginFile));
            if (file_exists($pluginDirectory . 'composer.json')) {
                
            }
        }
        return $plugins;
    }
}