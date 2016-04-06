<?php
/**
 * The Main App Launcher
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager;
use AaronSaray\WPComposerManager\Controller;
use AaronSaray\WPComposerManager\Service;

/**
 * Class App
 * @package AaronSaray\WPComposerManager
 */
class App
{
    /**
     * @var \Pimple\Container
     */
    protected $di;

    /**
     * App constructor.
     *
     * Sets up DI basically
     */
    public function __construct()
    {
        $this->di = $di = new \Pimple\Container();

        $di['view'] = function($di) {
            $factory = new \Aura\View\ViewFactory();
            $view = $factory->newInstance();
            $registry = $view->getViewRegistry();
            $registry->setPaths([__DIR__ . '/View']);
            return $view;
        };

        $di['service.lock-file-reader'] = function() {
            $lockFile = realpath(__DIR__ . '/..') . '/composer.lock'; // not sure if this is a good idea at the moment
            return new Service\LockFileReader($lockFile);
        };

        $di['controller.dashboard'] = function($di) {
            return new Controller\Dashboard($di['view'], $di['service.lock-file-reader']);
        };
    }

    /**
     * Run the app
     */
    public function __invoke()
    {
        /** register the menu and screen */
        add_action('admin_menu', function () {
            add_submenu_page(
                'plugins.php',
                __('Composer Manager', 'wp-composer-manager'),
                __('Composer Manager', 'wp-composer-manager'),
                'manage_options',
                'composer-manager',
                $this->di['controller.dashboard']
            );
        });

        /**
         * add a setting link so that it's easier to configure
         */
        add_filter('plugin_action_links_wp-composer-manager/wp-composer-manager.php', function($links) {
            $links['configure'] = sprintf(
                '<a href="%s">%s</a>',
                admin_url('plugins.php?page=composer-manager'),
                __('Settings', 'wp-composer-manager')
            );
            return $links;
        });
    }
}