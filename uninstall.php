<?php
/**
 * <PLUGIN_NAME> Uninstall
 *
 * Uninstall methods
 */
if ( ! defined( 'PluginClass_TEST_UNINSTALL' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

require_once '<plugin-slug>.php';

class PluginClass_Uninstall extends PluginClass {

	public function __construct() {
		 return;
	}

}


new PluginClass_Uninstall();

/*
 End of uninstall.php */
/* Location: <plugin-slug>/uninstall.php */
