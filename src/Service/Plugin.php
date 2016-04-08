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
                $isActive = is_plugin_active($pluginFile);
                $plugin = new Model\Plugin();
                $plugin->setId($pluginFile)
                    ->setName($pluginDetails['Name'])
                    ->setDescription($pluginDetails['Description'])
                    ->setActive($isActive);
                $plugins[] = $plugin;
            }
        }
        return $plugins;
    }

    /**
     * @param $id
     * @return Model\Plugin
     * @throws \Exception
     */
    public function getPluginById($id)
    {
        $pluginFile = sprintf('%s/%s', WP_PLUGIN_DIR, $id);
        if (!file_exists($pluginFile)) {
            throw new \Exception('The plugin ' . $pluginFile . ' does not exist.');
        }

        $pluginDetails = get_plugin_data($pluginFile);
        $plugin = new Model\Plugin();
        $plugin->setId($id)->setName($pluginDetails['Name'])->setDescription($pluginDetails['Description']);
        return $plugin;
    }
}