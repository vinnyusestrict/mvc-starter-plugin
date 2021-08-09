<?php
/**
 * @group PluginClass
 * @group PluginClass_uninstall
 */

require_once 'PluginClass_Child.class.php';

class Tests_PluginClass_Uninstall extends WP_UnitTestCase {

	public $child;

	public function setUp() {
		parent::setUp();

		$this->child = new PluginClass_Child();
	}

	public function test_plugin_settings_are_deleted() {
		// Add some tests to match your Uninstall file.

		$this->assertTrue( true );
	}


}

/*
 End of 01-uninstall.t.php */
/* Location: t/01-uninstall.t.php */
