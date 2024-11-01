<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.wpconcierges.com/plugin-resources/hyper-fair-registration/
 * @since             1.0.0
 * @package           hyperfair_registration
 *
 * @wordpress-plugin
 * Plugin Name:       WPConcierges HyperFair Registration
 * Plugin URI:        https://www.wpconcierges.com/plugin-resources/hyper-fair-registration/
 * Description:       Hyper Fair registration Plugin allows you to pass the data from your Event Registration System to 
 * Version:           1.0.0
 * Author:            WpConcierges
 * Author URI:        http://wpconcierges.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpc-hyperfair-registration
 * Domain Path:       /languages
 * WC requires at least: 3.0
 * WC tested up to: 4.7.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HYPERFAIR_REGISTRATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hyperfair-registration-activator.php
 */
function activate_wpc_hyperfair_registration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpc-hyperfair-registration-activator.php';
	wpc_hyperfair_registration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hyperfair-registration-deactivator.php
 */
function deactivate_wpc_hyperfair_registration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpc-hyperfair-registration-deactivator.php';
	wpc_hyperfair_registration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpc_hyperfair_registration' );
register_deactivation_hook( __FILE__, 'deactivate_wpc_hyperfair_registration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpc-hyperfair-registration.php';

function wpc_hyperfair_registration_settings_link( $links ) {
	$settings_link = '<a href="tools.php?page=wpc-hyperfair-registration">' . __( 'Settings' ) . '</a>';
	$premium_link = '<a href="https://www.wpconcierges.com/plugins/hyperfair-registration">' . __( 'Upgrade to Premium / Documentation' ) . '</a>';
	array_push( $links, $settings_link );
	array_push( $links, $premium_link );
  	return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'wpc_hyperfair_registration_settings_link' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function hfr_do_login_page(){
	global $current_user;
	$html = "";
	if(isset($current_user->user_email)){
  	$hyfr = new wpc_hyperfair_registration_Public("wpc-hyperfair-registration",HYPERFAIR_REGISTRATION_VERSION);
  	$login_info = $hyfr->get_login_token();
  	if(isset($login_info['status']) && $login_info['status']=="success"){
  		$html = "<p><a href=\"".$login_info['place']."?e=".$login_info['email']."&t=".$login_info['token']."\" class=\"btn btn-success hyperfair-btn\">Login to Virtual Event</a></p>";
  	}else{
  		$html = "<p>Try again in a few moments or contact support</p>";
  	}
  }else{
  	$html = "<p>You must be logged in to view this</p>";
  }
  print $html;
  
}

function run_hyperfair_registration() {

	$plugin = new wpc_hyperfair_registration();
	$plugin->run();

}
run_hyperfair_registration();
add_shortcode('hyperfair-registration-login', 'hfr_do_login_page');
