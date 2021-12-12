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
 * @group PluginClass_setup
 */
require_once 'PluginClass_Child.class.php';


/**
 * Plugin's setup tests.
 *
 * @category Class
 */
class Tests_PluginClass_Setup extends WP_UnitTestCase {

	/**
	 * Stores the child test class.
	 *
	 * @var PluginClass_Child
	 */
	public $child;

	/**
	 * Tests Setup.
	 */
	public function setUp() {
		parent::setUp();

		$this->child = new PluginClass_Child();
	}

	/**
	 * Test plugin loading.
	 */
	public function test_plugin_loads() {
		$this->assertTrue( class_exists( 'PluginClass' ), 'The plugin was loaded' );
	}


	/**
	 * Tests environment.
	 */
	public function test_environment() {
		$this->assertTrue( function_exists( 'PluginClass' ), 'Instanciation function exists' );
	}


	/**
	 * Tests the is_admin method.
	 */
	public function test_is_admin_method() {
		$boilerplate = PluginClass::bootstrap();

		$this->assertTrue( method_exists( $boilerplate, 'is_admin' ), 'We have a custom is_admin' );

		$this->assertTrue( is_callable( array( $boilerplate, 'is_admin' ) ), 'And we can call it' );

		add_filter( get_parent_class( $this->child ) . '_is_admin', '__return_true' );

		$this->assertTrue( $boilerplate::is_admin(), 'Filter works and returns true' );

		remove_all_filters( get_parent_class( $this->child ) . '_is_admin' );

		add_filter( get_parent_class( $this->child ) . '_is_admin', '__return_false' );

		$this->assertFalse( $boilerplate::is_admin(), 'Filter works and returns false' );
	}


	/**
	 * Tests the load_lib method.
	 */
	public function test_load_lib() {
		$boilerplate = PluginClass::bootstrap();

		$this->assertTrue( method_exists( $boilerplate, 'load_lib' ), 'load_lib() method exists' );
		$this->assertFalse( is_callable( array( $boilerplate, 'load_lib' ) ), 'load_lib() is not public' );

		$lib = $this->child->load_lib( 'controller/settings' );
		$this->assertTrue( is_object( $lib ), 'Test lib is loaded' );
		$this->assertTrue( $lib instanceof PluginClass_Controller_Settings, 'lib is instance of the correct class' );

		$lib2 = $this->child->load_lib( 'controller/settings' );
		$this->assertTrue( $lib === $lib2, 'Got cached lib version' );
	}


	/**
	 * Tests the render_template method
	 */
	public function test_render_template() {
		$boilerplate = PluginClass::bootstrap();

		// Change tmpl_dir for testing.
		$env           = $this->child->get_env();
		$env->tmpl_dir = $env->root_dir . 't/data/';

		$this->assertTrue( is_dir( $env->tmpl_dir ), 'Testing tmpl_dir exists' );
		$this->assertTrue( file_exists( $env->tmpl_dir . '/template_for_test.tmpl.php' ), 'Template file exists' );

		$this->assertTrue( method_exists( $boilerplate, 'render_template' ), 'render_template Method exists' );
		$this->assertFalse( is_callable( $boilerplate, 'render_template' ), 'render_template Is not public' );

		$stash = array(
			'foo' => 'foo',
			'bar' => 'bar',
		);
		$this->expectOutputString( 'This is a test template having foo = foo and bar = bar' );
		$this->child->render_template( 'template_for_test', $stash );
	}

}

/**
 * End of file test-00-setup.php
 * Location: t/test-00-setup.php
 */
