<?php
/**
 * Controls plugin settings.
 * See http://codex.wordpress.org/Settings_API
 *
 * @author vinnyalves
 * @package PluginClass
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

/**
 * Settings controller class
 *
 * @author vinnyalves
 */
class PluginClass_Controller_Settings {

	/**
	 * Class constructor
	 */
	public function __construct() {
		// We only allow instantiation in admin.
		if ( ! PluginClass::is_admin() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_init', array( $this, 'register_options' ) );
	}


	/**
	 * Creates the settings submenu item
	 */
	public function add_options_page() {
		$env = PluginClass()->environment;

		$plugin_data = (object) get_plugin_data( $env->plugin_file );

		$name = 'Name';

		$plugin_page = add_options_page(
			$plugin_data->{$name},
			$plugin_data->{$name},
			'manage_options',
			'PluginClass',
			array( 'PluginClass_View_Settings', 'show_admin' )
		);

		// Add CSS and JS.
		add_action( 'admin_head-' . $plugin_page, array( 'PluginClass_View_Settings', 'add_admin_css' ) );
		add_action( 'admin_head-' . $plugin_page, array( 'PluginClass_View_Settings', 'add_admin_js' ) );
	}


	/**
	 * Whitelists our settings
	 */
	public function register_options() {
		register_setting( 'PluginClass', 'PluginClass', array( 'PluginClass_DAL_Settings_Dao', 'validate_settings' ) );
	}
}

/**
 * End of file settings.class.php
 * Location: plugin-slug/includes/controller/settings.class.php
 */
