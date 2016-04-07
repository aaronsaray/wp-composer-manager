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
        $lockFile = realpath(__DIR__ . '/../..') . '/composer.lock'; // not sure if this is a good idea at the moment

        $this->view->setView('dashboard');
        $this->view->setData(array(
            'packages' => $this->composerService->getAllPackagesFromLockFile($lockFile),
            'plugins'   =>  $this->pluginService->findAllWithComposerJson()
        ));
        $view = $this->view;
        echo $view();
    }
}