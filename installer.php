<?php
/**
 * This is the code that is used before the composer set up is complete
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager;
use AaronSaray\WPComposerManager\Service\Composer;

/**
 * Class Installer
 * @package AaronSaray\WPComposerManager
 */
class Installer
{
    /**
     * Installer constructor.
     * @param Composer $composerService
     */
    public function __construct(Composer $composerService)
    {
        $this->composerService = $composerService;
    }

    /**
     * Run the installer
     */
    public function __invoke()
    {
        $that = $this;

        /** register the menu and screen */
        add_action('admin_menu', function () use ($that) {
            add_submenu_page(
                'plugins.php',
                __('Install Composer Manager', 'wp-composer-manager'),
                __('Composer Manager', 'wp-composer-manager'),
                'manage_options',
                'composer-manager-install',
                array($that, 'installerScreen')
            );
        });

        /**
         * add a setting link so that it's easier to configure
         */
        add_filter('plugin_action_links_wp-composer-manager/wp-composer-manager.php', function($links) {
            $links['configure-first-run'] = sprintf(
                '<a href="%s">%s</a>',
                admin_url('plugins.php?page=composer-manager-install'),
                __('Configure First Run', 'wp-composer-manager')
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
                        $output .= "<p><a href='" . admin_url('plugins.php?page=composer-manager') . "'>" . __('Continue to Plugin Dashboard', 'wp-composer-manager') . "</a></p>";
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
            $output .= "<form method='post' id='wp-composer-manager-install-form'>";
            $output .= wp_nonce_field('wp-composer-manager-install');
            $output .= get_submit_button(__('OK - give it a shot!', 'wp-composer-manager'), 'primary large', 'submit', false);
            $output .= "</form>";
            $output .= "</p><p>";
            $output .= "<small>Just so you know, this could take a while depending on your server's internet connection.</small>";
            $output .= "</p>";

            // the javascript for the button functions
            $output .= "
                <script>
                (function($){
                    $(function() {
                        $('#wp-composer-manager-install-form').on('submit', function() {
                            var f = $(this),
                                b = $('input[type=submit]', f);
                            b.attr('disabled', 'disabled').val('Please wait...');
                            var i = $('<img />').css('height', '20px').css('width', '20px').css('marginLeft', '3px').css('marginTop', '5px')
                                        .attr('src', '" . site_url('/wp-includes/images/spinner-2x.gif') . "');
                            b.after(i);                           
                        });
                    });
                }(jQuery)());
                </script>
            ";
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
        $composer = $this->composerService;
        $source = "https://getcomposer.org/composer.phar";

        if (!file_exists($composer::$COMPOSER_BINARY)) {
            if (!is_writable($composer::$COMPOSER_DIRECTORY)) {
                throw new \Exception("We do not have permission to write to the directory " . $composer::$COMPOSER_DIRECTORY);
            }

            if (!ini_get('allow_url_fopen')) {
                throw new \Exception("We are unable to load items remotely.  Your php.ini setting of allow_url_fopen is set to false.");
            }

            if (!($handle = fopen($source, 'r'))) {
                throw new \Exception("We're unable to open the URL {$source}.  Could it be that you are not allowing outbound traffic from your server?");
            }

            if (!file_put_contents($composer::$COMPOSER_BINARY, $handle)) {
                throw new \Exception("We were unable to write the composer.phar file.");
            }
        }
    }

    /**
     * Install the plugin using composer
     */
    protected function pluginComposerInstall()
    {
        $composer = $this->composerService;

        $selfUpdateCommand = sprintf('COMPOSER_HOME=%s %s self-update 2>&1', $composer::$COMPOSER_DIRECTORY, $composer::$COMPOSER_BINARY);
        exec($selfUpdateCommand, $output, $returnVar);
        if ($returnVar !== 0) {
            throw new \Exception('Composer self-update failed with message: ' . implode(" ", $output));
        }

        $vendorDirBase = WP_CONTENT_DIR;
        if (!is_writable($vendorDirBase)) {
            throw new \Exception('We are unable to write to the vendor folder: ' . $vendorDirBase);
        }

        $workingDir = plugin_dir_path(__FILE__);
        $selfInstallCommand = sprintf(
            'COMPOSER_VENDOR_DIR=%s COMPOSER_HOME=%s %s install -d %s 2>&1',
            $vendorDirBase . '/vendor',
            $composer::$COMPOSER_DIRECTORY,
            $composer::$COMPOSER_BINARY,
            $workingDir
        );

        exec($selfInstallCommand, $outputInstall, $returnVarInstall);
        if ($returnVarInstall !== 0) {
            throw new \Exception('Composer install failed with message: ' . implode(" ", $outputInstall));
        }

        if (!rename($workingDir . '/composer.lock', $composer::$COMPOSER_LOCK_FILE)) {
            throw new \Exception('Unable to move the composer.lock file after finishing installation.');
        }

        $autoloaders = array('autoload' => array('psr-4'=>array("AaronSaray\\WPComposerManager\\"=>"src")));
        $autoloadersJson = json_encode($autoloaders);
        if (!file_put_contents($composer::$COMPOSER_MERGE_JSON_FILE, $autoloadersJson)) {
            throw new \Exception('Unable to write composer-merge.json file.');
        }
    }
}





