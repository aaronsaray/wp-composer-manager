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
            $registry->setPaths(array(__DIR__ . '/View'));
            return $view;
        };

        $di['service.composer'] = function() {
            return new Service\Composer();
        };
        $di['service.plugin'] = function() {
            return new Service\Plugin();
        };

        $di['controller.dashboard'] = function($di) {
            return new Controller\Dashboard($di['view'], $di['service.plugin'], $di['service.composer']);
        };
        $di['controller.composer-install'] = function($di) {
            return new Controller\ComposerInstall($di['view'], $di['service.plugin'], $di['service.composer']);
        };
    }

    /**
     * Run the app
     */
    public function __invoke()
    {
        $di = $this->di;

        /** register the menu and screen */
        add_action('admin_menu', function () use ($di) {
            /** main plugin page */
            add_submenu_page(
                'plugins.php',
                __('Composer Manager', 'wp-composer-manager'),
                __('Composer Manager', 'wp-composer-manager'),
                'manage_options',
                'composer-manager',
                $di['controller.dashboard']
            );

            /** composer install page */
            add_submenu_page(
                null,
                __('Composer Install', 'wp-composer-manager'),
                null,
                'manage_options',
                'composer-manager-composer-install',
                $di['controller.composer-install']
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

        /**
         * Add jquery to help build a better user interface
         */
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_script('jquery');
        });
    }
}