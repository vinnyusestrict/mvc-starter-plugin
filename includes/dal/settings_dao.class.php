<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Datalayer to plugin settings
 * @author vinnyalves
 */
class PluginClass_DAL_Settings_Dao {
	
	/**
	 * Associative array where we store our cached Settings Model Object
	 * @var object
	 */
	private $cache = [];
	
	/**
	 * The name of the key in wp_options table that holds our settings
	 * @var string
	 */
	private $settings_name = 'PluginClass';
	
	/*********/
	
	public function __construct() {	/* Noop */ }
	
	/**
	 * Loads our data from the database and returns a settings model
	 * @return PluginClass_Model_Settings
	 */
	public function load()
	{
		if ( ! isset( $this->cache[$this->settings_name] ) )
		{
			PluginClass()->load_lib( 'model/settings' );
			
			$db_params = get_option( $this->settings_name, [] );
			
			$this->cache[$this->settings_name] = new PluginClass_Model_Settings( $db_params );
		}
		
		return $this->cache[$this->settings_name];
		
	}
	
	
	/**
	 * Ensure some basic settings
	 * @param array $_post
	 * @return string
	 */
	public function validate_settings( $_post )
	{
		$settings = PluginClass()->load_lib( 'model/settings' );
		
		foreach ( $_post as $key => $value )
		{
			$value = $settings->validate( $key, $value );
			
			if ( $settings->has_errors() && false !== ( $error_msg = $settings->get_error( $key ) ) )
			{
			    add_settings_error( $this->settings_name, esc_attr($key), $error_msg );
			    
			    unset( $_post );
			}
		}
		
		return $_post;
	}

}


/* End of file settings_dao.class.php */
/* Location: <plugin-dir>/includes/dal/settings_dao.class.php */
