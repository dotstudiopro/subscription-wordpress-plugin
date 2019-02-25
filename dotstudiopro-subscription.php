<?php

/**
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.dotstudiopro.com
 * @since             1.0.1
 * @package           Dotstudiopro_Subscription
 *
 * @wordpress-plugin
 * Plugin Name:       dotstudioPRO Subscription
 * Plugin URI:        https://www.dotstudiopro.com
 * Description:       This plugin is an addon to the dosstudioPRO API plugin to allow users to manage their subscriptions.
 * Version:           1.0.1
 * Author:            dotstudioPRO
 * Author URI:        http://www.dotstudiopro.com
 * License:           GPLv3
 * Text Domain:       dotstudiopro-subscription
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// we need to activate the dotstudioPRO API plugin first before installing this plugin.
if (class_exists('Dotstudiopro_Api') == NULL and current_user_can('activate_plugins')) {
    wp_die('Sorry, but this plugin requires the "dotstudioPRO API" plugin to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
}
/**
 * Defining global variable for plugin basefile to use anywhere througnt the site
 */
if (!defined('DOTSTUDIOPRO_SUBSCRIPTION_BASENAME')) {
    define('DOTSTUDIOPRO_SUBSCRIPTION_BASENAME', plugin_basename(__FILE__));
}
if (!defined('DOTSTUDIOPRO_SUBSCRIPTION_BASE_DIR'))
    define('DOTSTUDIOPRO_SUBSCRIPTION_BASE_DIR', dirname(DOTSTUDIOPRO_SUBSCRIPTION_BASENAME));

/**
 * A script/plugin that communicates with our WP Updater service to determine plugin updates
 */
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://updates.wordpress.dotstudiopro.com/wp-update-server/?action=get_metadata&slug=dspdev-subscription-plugin',
    __FILE__,
    'dspdev-subscription-plugin'
);

/**
 * Helper functions
 */
require_once plugin_dir_path(__FILE__) . 'helpers.php';

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-dotstudiopro-subscription-activator.php';

/** This action is documented in includes/class-dotstudiopro-api-activator.php */
register_activation_hook(__FILE__, array('Dotstudiopro_Subscription_Activator', 'activate'));

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-dotstudiopro-subscription-deactivator.php';

/** This action is documented in includes/class-dotstudiopro-api-deactivator.php */
register_deactivation_hook(__FILE__, array('Dotstudiopro_Subscription_Deactivator', 'deactivate'));

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-dotstudiopro-subscription.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Dotstudiopro_Subscription() {

    $plugin = new Dotstudiopro_Subscription();
    $plugin->run();
}

run_Dotstudiopro_Subscription();
