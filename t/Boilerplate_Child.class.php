<?php
/**
 * Needed to access parent's protected methods 
 * @author vinnyalves
 */
require_once( ABSPATH . '/wp-content/plugins/<plugin-dir>/<plugin-dir>.php' );

class Boilerplate_Child extends PluginClass {
	
	public $settings_name;
	
	function __construct() 
	{
		global $USC_BoilerPlate;
		
		$USC_BoilerPlate = new PluginClass();
		
		$this->settings_name = $USC_BoilerPlate->settings_name;
	}
	
	public function get_env()
	{
		return parent::get_env();
	}
	
	public function load_lib( $name, $params = array(), $force_reload = false )
	{
		return parent::load_lib( $name, $params, $force_reload );
	}
	
	public function render_template( $name, $stash=array(), $debug=false )
	{
		return parent::render_template( $name, $stash, $debug );
	}
}


/* End of Boilerplate_Child.class.php */
/* Location: <plugin-dir>/t/Boilerplate_Child.class.php */
