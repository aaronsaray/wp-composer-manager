<?php
/**
 * The Composer Install Controller
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Controller;

/**
 * Class ComposerInstall
 * @package AaronSaray\WPComposerManager\Controller
 */
class ComposerInstall extends ControllerAbstract
{
    /**
     * Run the controller
     */
    public function __invoke()
    {
        $plugin = $this->getGet('plugin');

        if (empty($plugin)) {
            die(wp_redirect('plugins.php?page=composer-manager'));
        }

        $errors = array();
        
        if (!$this->pluginService->doesPluginExistById($plugin)) {
            $errors[] = __('The plugin file does not exist.', 'wp-composer-manager');
        }
        elseif (!$this->composerService->getComposerJsonFileFromPluginId($plugin)) {
            $errors[] = __('The composer.json file was not found.', 'wp-composer-manager');
        }
        

        var_dump($errors);
    }
}