<?php
/**
 * <PLUGIN_NAME> Uninstall
 *
 * Uninstall methods.
 *
 * @package PluginClass
 */

// phpcs:disable WordPress.Files.FileName

if ( ! defined( 'PluginClass_TEST_UNINSTALL' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}


/**
 * Code run when uninstalling the plugin.
 *
 * @author vinnyalves
 */
class PluginClass_Uninstall {

	/**
	 * Object constructor.
	 */
	public function __construct() {
		delete_option( 'PluginClass' );

		require_once __DIR__ . '/includes/controller/class-pluginclass-controller-admin-notices.php';

		PluginClass_Controller_Admin_Notices::uninstall();
	}

}

new PluginClass_Uninstall();

/**
 * End of uninstall.php
 * Location: plugin-slug/uninstall.php
 */
