<?php
/**
 * The Composer Update Controller
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Controller;

/**
 * Class ComposerUpdate
 * @package AaronSaray\WPComposerManager\Controller
 */
class ComposerUpdate extends ControllerAbstract
{
    /**
     * Run the controller
     */
    public function __invoke()
    {
        $pluginId = $this->getGet('plugin');

        if (empty($pluginId)) {
            die(wp_redirect('plugins.php?page=composer-manager'));
        }

        $errors = array();
        $plugin = null;
        $composerOutput = array();

        try {
            $plugin = $this->pluginService->getPluginById($pluginId);
            $this->composerService->getComposerJsonFileFromPluginId($pluginId);
            $composerOutput = $this->composerService->runComposerUpdateForPlugin($plugin);
        }
        catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $this->view->setView('composer-update-results');
        $this->view->setData(array(
            'plugin'    =>  $plugin,
            'errors' => $errors,
            'composerOutput'    =>  $composerOutput
        ));
        $view = $this->view;
        echo $view();
    }
}