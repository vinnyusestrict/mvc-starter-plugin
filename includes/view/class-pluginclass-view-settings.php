<?php
/**
 * Controls settings display.
 *
 * @category Class
 * @package  PluginClass_View_Settings
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

/**
 * Settings View class
 *
 * @author vinnyalves
 */
class PluginClass_View_Settings {

	/**
	 * Constructor. Do nothing if not admin.
	 */
	public function __construct() {
		/* do nothing */
	}

	/**
	 * Enqueue the CSS file in the settings screen
	 */
	public function add_admin_css() {
		wp_enqueue_style( 'PluginClass-settings-admin', pluginclass()->environment->css_url . 'plugin-settings.css', array(), pluginclass()->version );
	}


	/**
	 * Enqueue the JS file in the settings screen
	 */
	public function add_admin_js() {
		wp_enqueue_script( 'PluginClass-settings-admin', pluginclass()->environment->js_url . 'plugin-settings.js', array( 'jquery' ), pluginclass()->version, $in_footer = false );
	}


	/**
	 * Show the settings screen.
	 */
	public static function show_admin() {
		// Get stash items.
		$stash = array( 'settings' => pluginclass()->settings );

		pluginclass()->render_template( 'plugin-settings', $stash );
	}

}

/**
 * End of file class-pluginclass-view-settings.php
 * Location: plugin-slug/includes/view/class-pluginclass-view-settings.php
 */
