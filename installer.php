<?php
/**
 * This is the code that is used before the composer set up is complete
 *
 * @author Aaron Saray
 */

namespace AaronSaray\WPComposerManager;

/** @var string the directory of the composer install */
define('COMPOSER_BINARY_DIRECTORY', __DIR__ . '/bin');

/** @var string the destination file of the composer install */
define('COMPOSER_BINARY_DIRECTORY_FILE', COMPOSER_BINARY_DIRECTORY . '/composer.phar');

/**
 * This shows the screen for the initial install
 */
function installer_screen()
{
    $output = "<div class='wrap'><h1>Composer Manager</h1>";

    if (isset($_POST['_wpnonce'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'wp-composer-manager-install')) {
            $output .= '<p class="error-message">Unfortunately, we were unable to process your request.  Please try again by clicking the Composer Manager menu on the left.</p>';
        }
        else {
            try {
                composer_install();
                $output .= "<p class='success-message'>Composer binary installed successfully.</p>";
                try {
                    plugin_composer_install();
                    $output .= "<p class='success-message'>This plugin was successfully installed using composer.</p>";
                }
                catch (\Exception $e) {
                    $output .= "<p class='error-message'>This plugin was not installed successfully: {$e->getMessage()}</p>";
                }
            }
            catch (\Exception $e) {
                $output .= "<p class='error-message'>Composer binary was not installed: {$e->getMessage()}</p>";
            }
        }
    }
    else {
        $output .= "
            <p>
                This plugin allows management of Composer dependencies in other WordPress plugins.  
            </p>
            <p>
                Much like the <a href='https://en.wikipedia.org/wiki/Chicken_or_the_egg' target='_blank'>Chicken or the Egg conundrum</a>,
                this plugin won't work until a Composer is set up and managing dependencies.
            </p>
            <p>
                So, what we'd like to do is simple:
            </p>
            <ol>
                <li>Try to install the Composer binary locally.</li>
                <li>If that works, we're going to run <code>composer install</code> on our local composer.json file to install the rest of this plugin.</li>
            </ol>
            <hr>
            <p>
                <a href='plugins.php' class='button button-cancel alignright'>No Thanks - let's disable this plugin.</a>
                <form method='post'>
                    " . wp_nonce_field('wp-composer-manager-install') . "
                    " . get_submit_button('OK - give it a shot!', 'primary large', 'submit', false) . "
                </form>
            </p>
        ";
    }

    $output .= "</div>";

    echo $output;
}

/** register the menu and screen */
add_action('admin_menu', function () {
    add_submenu_page('plugins.php', 'Install Composer Manager', 'Composer Manager', 'manage_options', 'composer-manager-install', __NAMESPACE__ . '\\installer_screen');
});

/**
 * Install the binary of composer
 *
 * This will not try to reinstall it if it already exists
 */
function composer_install()
{
    $source = "https://getcomposer.org/composer.phar";

    if (!file_exists(COMPOSER_BINARY_DIRECTORY_FILE)) {
        if (!is_writable(COMPOSER_BINARY_DIRECTORY)) {
            throw new \Exception("We do not have permission to write to the directory " . COMPOSER_BINARY_DIRECTORY);
        }

        if (!ini_get('allow_url_fopen')) {
            throw new \Exception("We are unable to load items remotely.  Your php.ini setting of allow_url_fopen is set to false.");
        }

        if (!($handle = fopen($source, 'r'))) {
            throw new \Exception("We're unable to open the URL {$source}.  Could it be that you are not allowing outbound traffic from your server?");
        }

        if (!file_put_contents(COMPOSER_BINARY_DIRECTORY_FILE, $handle)) {
            throw new \Exception("We were unable to write the composer.phar file.");
        }
    }
}

function plugin_composer_install()
{
    $workingDir = plugin_dir_path(__FILE__);
    $command = sprintf('%s install -d %s', COMPOSER_BINARY_DIRECTORY_FILE, $workingDir);
    $command = 'echo ' . $command;
    $lastLine = exec($command, $output, $returnVar);
    var_dump($output);
    var_dump($returnVar);
    var_dump($lastLine);
}