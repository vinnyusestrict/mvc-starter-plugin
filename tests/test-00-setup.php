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

/**
 * Plugin's setup tests.
 *
 * @category Class
 */
class Tests_PluginClass_Setup extends WP_UnitTestCase {

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
		$this->assertTrue( function_exists( 'pluginclass' ), 'Instanciation function exists' );
	}


	/**
	 * Tests the is_admin method.
	 */
	public function test_is_admin_method() {
		$boilerplate = PluginClass::instance();

		$this->assertTrue( method_exists( $boilerplate, 'is_admin' ), 'We have a custom is_admin' );

		$this->assertTrue( is_callable( array( $boilerplate, 'is_admin' ) ), 'And we can call it' );

		add_filter( strtolower( get_class( $boilerplate ) ) . '_is_admin', '__return_true' );

		$this->assertTrue( $boilerplate::is_admin(), 'Filter works and returns true' );

		remove_all_filters( strtolower( get_class( $boilerplate ) ) . '_is_admin' );

		add_filter( get_class( $boilerplate ) . '_is_admin', '__return_false' );

		$this->assertFalse( $boilerplate::is_admin(), 'Filter works and returns false' );
	}


	/**
	 * Tests the load_lib method.
	 */
	public function test_load_lib() {
		$boilerplate = PluginClass::instance();

		$this->assertTrue( method_exists( $boilerplate, 'load_lib' ), 'load_lib() method exists' );
		$this->assertTrue( is_callable( array( $boilerplate, 'load_lib' ) ), 'load_lib() is public' );

		$lib = $boilerplate->load_lib( 'controller/settings' );
		$this->assertTrue( is_object( $lib ), 'Test lib is loaded' );
		$this->assertTrue( $lib instanceof PluginClass_Controller_Settings, 'lib is instance of the correct class' );

		$lib2 = $boilerplate->load_lib( 'controller/settings' );
		$this->assertTrue( $lib === $lib2, 'Got cached lib version' );
	}


	/**
	 * Tests the render_template method
	 */
	public function test_render_template() {
		$boilerplate = PluginClass::instance();

		add_filter(
			'pluginclass_template_path',
			function( $path, $name ) {
				return __DIR__ . '/templates/' . $name;
			},
			10,
			2
		);

		$stash = array(
			'foo' => 'foo',
			'bar' => 'bar',
		);

		$this->expectOutputString( 'This is a test template having foo = foo and bar = bar' );
		$boilerplate->render_template( 'template-for-test', $stash );

		remove_all_filters( 'pluginclass_template_path' );
	}

}

/**
 * End of file test-00-setup.php
 * Location: t/test-00-setup.php
 */
