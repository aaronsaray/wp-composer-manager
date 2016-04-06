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

// no composer?
require 'installer.php';