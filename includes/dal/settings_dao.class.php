<?php defined('ABSPATH') or die("No direct access allowed");
/**
 * Datalayer to plugin settings
 * @author vinnyalves
 */
class PluginClass_DAL_Settings_Dao extends PluginClass {
	
	/**
	 * Associative array where we store our cached Settings Model Object
	 * @var object
	 */
	private $cache = array();
	

	public function __construct() 
	{
		// NOOP - we don't want PHP to call the parent automatically
	}
	
	/**
	 * Loads our data from the database and returns a settings model
	 * @return BizxpressBI_Model_Settings
	 */
	public function load()
	{
		if ( ! isset( $this->cache[$this->settings_name] ) )
		{
			$this->load_lib('model/settings');
			
			$db_params = get_option( $this->settings_name, array() );
			
			// Check that WP didn't return false for settings not found
			if (false === $db_params)
				$db_params = array();
			
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
		$settings_model = $this->load_lib('model/settings');
		
		foreach ( $_post as $key => $value )
		{
			if ( false === ( $_post[$key] = $settings_model->is_valid( $key, $value ) ) )
				unset( $_post[$key] );
		}
		
		return $_post;
	}
	
	
	/**
	 * This may not be needed at all, as we're using the WP Settings API to do our saves
	 * @param unknown $settings_model
	 */
// 	public function save( $settings_model )
// 	{
// 		# TODO: implement this once we have some settings to save
// 	}
}


/* End of file settings_dao.class.php */
/* Location: plugin-name/includes/dal/settings_dao.class.php */
