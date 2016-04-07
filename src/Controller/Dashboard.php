<?php
/**
 * The Dashboard Controller
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Controller;

/**
 * Class Dashboard
 * @package AaronSaray\WPComposerManager\Controller
 */
class Dashboard extends ControllerAbstract
{
    /**
     * Run the controller
     */
    public function __invoke()
    {
        $this->view->setView('dashboard');
        $this->view->setData(array(
            'packages' => $this->composerService->getAllPackagesFromLockFile(),
            'plugins'   =>  $this->pluginService->findAllWithComposerJson()
        ));
        $view = $this->view;
        echo $view();
    }
}