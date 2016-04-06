<?php
/**
 * The Dashboard Controller
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Controller;

use AaronSaray\WPComposerManager\Service\LockFileReader;
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
     * Dashboard constructor.
     * @param View $view
     * @param LockFileReader $lockFileReaderService
     */
    public function __construct(View $view, LockFileReader $lockFileReaderService)
    {
        $this->view = $view;
        $this->lockFileReaderService = $lockFileReaderService;
    }

    /**
     * Run the controller
     */
    public function __invoke()
    {
        $this->view->setView('dashboard');
        $this->view->setData(array('packages' => $this->lockFileReaderService->getAllPackages()));
        $view = $this->view;
        echo $view();
    }
}