<?php
/**
 * This is the code that is used before the composer set up is complete
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager;

/**
 * Class Installer
 * @package AaronSaray\WPComposerManager
 */
class Installer
{
    /** @var string the directory of the composer install */
    protected static $COMPOSER_BINARY_DIRECTORY;

    /** @var string the destination file of the composer install */
    protected static $COMPOSER_BINARY_DIRECTORY_FILE;

    /**
     * Installer constructor.
     */
    public function __construct()
    {
        self::$COMPOSER_BINARY_DIRECTORY = __DIR__ . '/bin';
        self::$COMPOSER_BINARY_DIRECTORY_FILE = self::$COMPOSER_BINARY_DIRECTORY . '/composer.phar';

        /** register the menu and screen */
        add_action('admin_menu', function () {
            add_submenu_page(
                'plugins.php',
                __('Install Composer Manager', 'wp-composer-manager'),
                __('Composer Manager', 'wp-composer-manager'),
                'manage_options',
                'composer-manager-install',
                array($this, 'installerScreen')
            );
        });

        /**
         * add a setting link so that it's easier to configure
         */
        add_filter('plugin_action_links_' . plugin_basename(__DIR__ . '/wp-composer-manager.php'), function($links) {
            $links['configure-first-run'] = sprintf(
                '<a href="%s">%s</a>',
                admin_url('plugins.php?page=composer-manager-install'),
                __('Configure First Run', 'wp-composer-manager')
            );
            return $links;
        });
    }

    /**
     * This shows the screen for the initial install
     */
    public function installerScreen()
    {
        $output = "<div class='wrap'><h1>" . __('Composer Manager', 'wp-composer-manager') . "</h1>";

        if (isset($_POST['_wpnonce'])) {
            if (!wp_verify_nonce($_POST['_wpnonce'], 'wp-composer-manager-install')) {
                $output .= '<p class="error-message">' .
                    __('Unfortunately, we were unable to process your request.  Please try again by clicking the Composer Manager menu on the left.', 'wp-composer-manager') .
                    '</p>';
            }
            else {
                try {
                    $this->composerInstall();
                    $output .= "<p class='success-message'>" . __('Composer binary installed successfully.', 'wp-composer-manager') . "</p>";
                    try {
                        $this->pluginComposerInstall();
                        $output .= "<p class='success-message'>" . __('This plugin was successfully installed using composer.', 'wp-composer-manager') . "</p>";
                    }
                    catch (\Exception $e) {
                        $output .= "<p class='error-message'>" . sprintf(__('This plugin was not installed successfully: %s', 'wp-composer-manager'), $e->getMessage()) . "</p>";
                    }
                }
                catch (\Exception $e) {
                    $output .= "<p class='error-message'>" . sprintf(__('Composer binary was not installed: %s', 'wp-composer-manager'), $e->getMessage()) . "</p>";
                }
            }
        }
        else {
            $output .= "<p>" . __('This plugin allows management of Composer dependencies in other WordPress plugins.', 'wp-composer-manager') . "</p>";

            // leaving the html in this one because maybe in other languages this won't really be a "joke"
            $output .= "<p>" . __("Much like the <a href='https://en.wikipedia.org/wiki/Chicken_or_the_egg' target='_blank'>Chicken or the Egg conundrum</a>,
                                   this plugin won't work until a Composer is set up and managing dependencies.", 'wp-composer-manager') . "</p>";

            $output .= "<p>" . __("So, what we'd like to do is simple:", 'wp-composer-manager') . "</p>";
            $output .= "<ol>";
            $output .= "<li>" . __('Try to install the Composer binary locally.', 'wp-composer-manager') . "</li>";
            $output .= "<li>" . __("If that works, we're going to run <code>composer install</code> on our local composer.json file to install the rest of this plugin.", 'wp-composer-manager') . "</li>";
            $output .= "</ol>";
            $output .= "<hr>";

            $output .= "<p>";
            $output .= "<a href='plugins.php' class='button button-cancel alignright'>" . __("No Thanks - let's disable this plugin.", 'wp-composer-manager') . "</a>";
            $output .= "<form method='post'>";
            $output .= wp_nonce_field('wp-composer-manager-install');
            $output .= get_submit_button(__('OK - give it a shot!', 'wp-composer-manager'), 'primary large', 'submit', false);
            $output .= "</form>";
            $output .= "</p>";
        }

        $output .= "</div>";

        echo $output;
    }

    /**
     * Installs the composer binary (if need be)
     * @throws \Exception
     * @todo should errors be translated? Prob.
     */
    protected function composerInstall()
    {
        $source = "https://getcomposer.org/composer.phar";

        if (!file_exists(self::$COMPOSER_BINARY_DIRECTORY_FILE)) {
            if (!is_writable(self::$COMPOSER_BINARY_DIRECTORY)) {
                throw new \Exception("We do not have permission to write to the directory " . self::$COMPOSER_BINARY_DIRECTORY);
            }

            if (!ini_get('allow_url_fopen')) {
                throw new \Exception("We are unable to load items remotely.  Your php.ini setting of allow_url_fopen is set to false.");
            }

            if (!($handle = fopen($source, 'r'))) {
                throw new \Exception("We're unable to open the URL {$source}.  Could it be that you are not allowing outbound traffic from your server?");
            }

            if (!file_put_contents(self::$COMPOSER_BINARY_DIRECTORY_FILE, $handle)) {
                throw new \Exception("We were unable to write the composer.phar file.");
            }
        }
    }

    /**
     * Install the plugin using composer
     */
    protected function pluginComposerInstall()
    {
        $workingDir = plugin_dir_path(__FILE__);
        $command = sprintf('%s install -d %s', self::$COMPOSER_BINARY_DIRECTORY_FILE, $workingDir);
        //$command = sprintf('%s --version', self::$COMPOSER_BINARY_DIRECTORY_FILE);
        $command = "echo $command";
        $lastLine = exec($command, $output, $returnVar);
        var_dump($output);
        var_dump($returnVar);
        var_dump($lastLine);
    }
}

/**
 * Run installer
 */
new Installer();




