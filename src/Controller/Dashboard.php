<?php
/**
 * The Dashboard Controller
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Controller;

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
     * Dashboard constructor.
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * Run the controller
     */
    public function __invoke()
    {
        $this->view->setView('dashboard');

        $view = $this->view;
        echo $view();
    }
}