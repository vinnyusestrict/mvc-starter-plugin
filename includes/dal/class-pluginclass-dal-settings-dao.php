<?php
/**
 * FileDoc for plugin settings file.
 *
 * @author vinnyalves
 * @package PluginClass
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

/**
 * Datalayer to plugin settings
 *
 * @author vinnyalves
 */
class PluginClass_DAL_Settings_Dao {

	/**
	 * Associative array where we store our cached Settings Model Object
	 *
	 * @var object
	 */
	private $cache = array();

	/**
	 * The name of the key in wp_options table that holds our settings
	 *
	 * @var string
	 */
	private $settings_name = 'PluginClass';

	/*-----------------*/

	/**
	 * Loads our data from the database and returns a settings model
	 *
	 * @return PluginClass_Model_Settings
	 */
	public function load() {
		if ( ! isset( $this->cache[ $this->settings_name ] ) ) {
			pluginclass()->load_lib( 'model/settings' );

			$db_params = get_option( $this->settings_name, array() );

			$this->cache[ $this->settings_name ] = new PluginClass_Model_Settings( $db_params );
		}

		return $this->cache[ $this->settings_name ];
	}


	/**
	 * Ensure some basic settings
	 *
	 * @param array $_post POSTed values.
	 * @return string
	 */
	public static function validate_settings( $_post ) {
		$settings = pluginclass()->load_lib( 'model/settings' );

		foreach ( $_post as $key => $value ) {
			$value = $settings->validate( $key, $value );

			if ( $settings->has_errors() && false !== ( $error_msg = $settings->get_error( $key ) ) ) { //phpcs:ignore
				add_settings_error( 'PluginClass', esc_attr( $key ), $error_msg );

				unset( $_post[ $key ] );
			}
		}

		return $_post;
	}

}


/**
 * End of file settings-dao.class.php
 * Location: plugin-slug/includes/dal/settings-dao.class.php
 */
