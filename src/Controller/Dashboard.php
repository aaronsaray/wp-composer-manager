<?php
/**
 * The Dashboard Controller
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Controller;

use AaronSaray\WPComposerManager\Service\LockFileReader;
use AaronSaray\WPComposerManager\Service\PluginFinder;
use Aura\View\View;

/**
 * Class Dashboard
 * @package AaronSaray\WPComposerManager\Controller
 */
class Dashboard
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var LockFileReader
     */
    protected $lockFileReaderService;

    /**
     * @var PluginFinder
     */
    protected $pluginFinderService;

    /**
     * Dashboard constructor.
     * @param View $view
     * @param LockFileReader $lockFileReaderService
     * @param PluginFinder $pluginFinderService
     */
    public function __construct(View $view, LockFileReader $lockFileReaderService, PluginFinder $pluginFinderService)
    {
        $this->view = $view;
        $this->lockFileReaderService = $lockFileReaderService;
        $this->pluginFinderService = $pluginFinderService;
    }

    /**
     * Run the controller
     */
    public function __invoke()
    {
        $this->view->setView('dashboard');
        $this->view->setData(array(
            'packages' => $this->lockFileReaderService->getAllPackages(),
            'plugins'   =>  $this->pluginFinderService->findAllWithComposerJson()
        ));
        $view = $this->view;
        echo $view();
    }
}