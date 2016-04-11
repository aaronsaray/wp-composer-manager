=== WP Composer Manager ===
Contributors: aaronsaray
Tags: composer, dependencies
Requires at least: 4.0
Tested up to: 4.4
License: GPLv3
Stable tag: trunk

WP Composer Manager is a tool to install composer dependencies in other plugins.

== Description ==

**Alpha Version** This plugin is still under development and testing.

[Composer](http://getcomposer.org) is a great tool for managing dependencies and third-party libraries in PHP.
As WordPress plugins become more complex, it may become necessary to import reusable code libraries into your
plugin.  Using this plugin, a site owner can install plugin composer dependencies globally to their WordPress
installation.

Since using composer and doing system-level calls can be restricted on certain systems, this plugin may not always
work for each installation.  In order to test this, however, this plugin also relies on composer libraries.
Therefore, the first bootstrap part of this plugin uses the same techniques to install the management dashboard
of this plugin, thereby letting the user know if the plugin is compatible with their system before going too far.

In order for third-party plugins to be compatible with the way this plugin manages composer, there are a few coding
changes that need to be applied.  You can find out more about those in the Third-Party Plugin Instructions section.

== Installation ==

Installation of this plugin is really two part: installing the plugin and running the first time configuration.

1. Upload the plugin files to the `wp-content/plugins/wp-composer-manager` directory or install through the WordPress
   plugins screen directly.
1. Activate the plugin through the **Plugins** screen in WordPress.
1. Below the plugin title on the **Plugins** screen, click the link that says **Configure First Run** - or click the
   **Composer Manager** sub-menu item in the **Plugins** menu on the left of the screen.
1. Run the first time configuration.  If this is successful, your plugin is installed!

== Frequently Asked Questions ==

= What does this plugin do for me? =

This plugin is more of a tool for other plugin developers.  It allows them to import code into their plugin from
other developers without the need to bundle it directly into their plugin.  This is cool because multiple plugins
in your WordPress installation might end up using the same code, so this way they can share it, keeping down the
size of your WordPress installation.

= Error: We were unable to create the composer directory =

This means that your webserver user does not have the ability to write to your `wp-content` folder.  You'll need to
give the proper permission to that folder.  Make sure that the webserver user has write permission for this folder.
Each system is different, so I can't give exact instructions here, but if you Google this problem, make sure to
include the operating system and the webserver type.

= Error: We are unable to load items remotely =

Sometimes, for security reasons, certain webservers are configured to not load remote content.  This error is most
likely caused by a php.ini setting of `allow_url_fopen` set to `false` - change this to true and this should work.

= After running a composer update on a plugin, there is a lot of information on the screen that I don't recognize =

This is just the composer output after your command.  If you run into a problem with a plugin, make sure you include
this information when you report your bug.  If the installation was successful, though, you can just ignore this
output entirely.

= I am a plugin author - how can I make use of composer and this plugin? =

It's not that hard!  Please see the **Third-Party Plugin Instructions** section.

== Third-Party Plugin Instructions ==

**Plugin Overview**

This plugin itself installs the composer autoloader and vendor directory.  Then, it will always modify the include path
to have the parent directory of `vendor` as part of the include path.  Your plugin only needs to deal with the
path/file of `vendor/autoload.php` in order to access the autoloader.  Since we can't necessarily guarantee what order
plugins are loaded, your plugin should now execute its primary logic from the hook [plugins_loaded](https://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded).

**Sample Launch Code for Your Plugin**

Here is an example of how you can make your plugin compatible with WP Composer Manager.

**your-plugin-name.php**

    add_action('plugins_loaded', function() {
        if (stream_resolve_include_path('vendor/autoload.php')) {
            require_once 'vendor/autoload.php';

            if (class_exists('YourNamespace\\YourPluginClass')) {
                // do your main plugin logic here
            }
            else {
                // notify to run WP Composer Manager
            }
        }
        else {
            // notify to install WP Composer Manager and configure it.
        }
    });

Basically, there are two parts.  First, determine if WP Composer Manager is installed and has been configured.  If that
is successful, then determine if your specific class is available.  If it's not, one could generally assume that
the user hasn't ran the Composer Update function inside of WP Composer Manager for your plugin.

**Using the Built-in Composer Update WordPress Action**

This plugin also offers an action hook called `wp-composer-manager_run_composer_update` - this requires a parameter of
your plugin's identifier or basename.  This hook will output the same output that running composer update on a plugin
inside of the WP Composer Manager dashboard will.

In your plugin, you might try using code like this:

    do_action('wp-composer-manager_run_composer_update', plugin_basename(__FILE__);

Include this when you want to run the composer update command.