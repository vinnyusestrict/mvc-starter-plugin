<?php
/**
 * PHPUnit setup tests.
 *
 * This file contains basic tests for the framework.
 *
 * @package Tests_PluginClass_Setup
 */

/**
 * PHPUnit group names.
 *
 * @group PluginClass
 * @group PluginClass_uninstall
 */

/**
 * Plugin's uninstall tests.
 *
 * @category Class
 */
class Tests_PluginClass_Uninstall extends WP_UnitTestCase {

	/**
	 * Test plugin uninstall
	 */
	public function test_plugin_settings_are_deleted() {
		// Add some tests to match your Uninstall file.

		$this->assertTrue( true );
	}
}

/**
 * End of file test-99-uninstall.php
 * Location: t/test-99-uninstall.php
 */
