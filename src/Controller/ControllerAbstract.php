<?php
/**
 * Controller Abstract
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager\Controller;
use Aura\View\View;
use AaronSaray\WPComposerManager\Service;

/**
 * Class ControllerAbstract
 * @package AaronSaray\WPComposerManager\Controller
 */
abstract class ControllerAbstract
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var Service\Plugin
     */
    protected $pluginService;

    /**
     * @var Service\Composer
     */
    protected $composerService;

    /**
     * ControllerAbstract constructor.
     * @param View $view
     * @param Service\Plugin $pluginService
     * @param Service\Composer $composerService
     */
    public function __construct(View $view, Service\Plugin $pluginService, Service\Composer $composerService)
    {
        if (!current_user_can('manage_options')) {
            wp_die(__( 'You do not have sufficient permissions to access this page.', 'wp-composer-manager'), 403);
        }

        $this->view = $view;
        $this->pluginService = $pluginService;
        $this->composerService = $composerService;
    }

    /**
     * @param $key
     * @param null $default
     * @return null|mixed
     */
    protected function getGet($key, $default = null)
    {
        return $this->getRequest($_GET, $key, $default);
    }

    /**
     * Gets the requested type - just a code saver
     *
     * @param $variable
     * @param $key
     * @param $default
     * @return mixed
     */
    private function getRequest(array $variable, $key, $default)
    {
        $return = $default;
        if (array_key_exists($key, $variable)) {
            $return = stripslashes_deep($variable[$key]);
        }
        return $return;
    }
}