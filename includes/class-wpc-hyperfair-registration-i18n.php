<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.wpconcierges.com/plugin-resources/hyper-fair-registration/
 * @since      1.0.0
 *
 * @package    hyperfair_registration
 * @subpackage hyperfair_registration/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    hyperfair_registration
 * @subpackage hyperfair_registration/includes
 * @author     Your Name <email@example.com>
 */
class wpc_hyperfair_registration_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'hyperfair-registration',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
