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
        $di['controller.composer-update'] = function($di) {
            return new Controller\ComposerUpdate($di['view'], $di['service.plugin'], $di['service.composer']);
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

            /** composer update page */
            add_submenu_page(
                null,
                __('Composer Update', 'wp-composer-manager'),
                null,
                'manage_options',
                'composer-manager-composer-update',
                $di['controller.composer-update']
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

        /**
         * Add action to run composer update on their plugin
         */
        add_action('wp-composer-manager_run_composer_update', array($this, 'composerUpdateHook'));
    }

    /**
     * This hook runs when other plugins request a composer update on themselves
     * @param $pluginId
     */
    public function composerUpdateHook($pluginId)
    {
        if (validate_plugin($pluginId) === 0) {
            $_GET['plugin'] = $pluginId; // not my favorite way of doing this but ...
            $controller = $this->di['controller.composer-update'];
            $controller();
        }
        else {
            echo "<p>The plugin {$pluginId} is not available.</p>";
        }
    }
}