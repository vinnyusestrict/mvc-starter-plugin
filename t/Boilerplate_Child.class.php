<?php
/**
 * Needed to access parent's protected methods 
 * @author vinnyalves
 */
require_once(ABSPATH . '/wp-content/plugins/boilerplate/plugin-name.php');

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
	
	public function load_lib($lib_name)
	{
		return parent::load_lib($lib_name);
	}
	
	public function render_template($name, $stash=array(), $debug=false)
	{
		return parent::render_template($name, $stash, $debug);
	}
}


/* End of Boilerplate_Child.class.php */
/* Location: t/Boilerplate_Child.class.php */
