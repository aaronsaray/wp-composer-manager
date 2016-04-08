<?php
/**
 * Plugin Name: WP Composer Manager
 * Plugin URI: https://github.com/aaronsaray/wp-composer-manager
 * Description: Manage composer for WordPress plugins.
 * Author: Aaron Saray
 * Author URI: http://aaronsaray.com
 * Version: 0.1.0
 * License: GPLv3
 * Text Domain: wp-composer-manager
 */

if (!defined('ABSPATH')) {
    header('HTTP/1.0 403 Forbidden');
    die('Please do not surf to this file directly.');
}

/** this is done so we can see if vendor is available for autoload */
set_include_path(get_include_path() . PATH_SEPARATOR . WP_CONTENT_DIR . '/composer');

/** necessary because we want to set a good example */
add_action('plugins_loaded', function() {
    if (stream_resolve_include_path('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        $app = new \AaronSaray\WPComposerManager\App();
    }
    else {
        require __DIR__ . '/src/Service/Composer.php';
        require 'installer.php';
        $app = new \AaronSaray\WPComposerManager\Installer(new \AaronSaray\WPComposerManager\Service\Composer());
    }

    $app();
});
