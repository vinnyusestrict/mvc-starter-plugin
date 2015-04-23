<?php defined('ABSPATH') or die("No direct access allowed");
/**
 * Controls plugin settings.
 * See http://codex.wordpress.org/Settings_API
 * 
 * @author vinnyalves
 */
class PluginClass_Controller_Settings extends PluginClass {

	protected $settings_dao;
	
	public function __construct()
	{
		// We only allow instantiation in admin
		if (! parent::is_admin() )
			return;
		
		$this->settings_dao = $this->load_lib('dal/settings_dao');
		
		add_action('admin_menu', array(&$this, 'add_options_page'));
		add_filter( get_parent_class($this) . '_settings', array(&$this->settings_dao, 'load'), 0, 10);
		add_action('admin_init', array(&$this, 'register_options'));
	}
	
	
	/**
	 * Creates the settings submenu item
	 */
	public function add_options_page()
	{
		$env = $this->get_env();
		
		$plugin_data = (object) get_plugin_data($env->root_dir . 'plugin-name.php');
		$view = $this->load_lib('view/settings');
		
		$plugin_page = add_options_page( $plugin_data->Name, $plugin_data->Name, 'manage_options',
										 $this->domain, array(&$view,'show_admin'));
	
		// Add CSS and JS
		add_action('admin_head-' . $plugin_page, array(&$view,'add_admin_css'));
		add_action('admin_head-' . $plugin_page, array(&$view,'add_admin_js'));
	}
	
	
	/**
	 * Whitelists our settings
	 */
	public function register_options()
	{
		register_setting($this->settings_name, $this->settings_name, array(&$this->settings_dao, 'validate_settings'));
	}
}

/* End of file settings.class.php */
/* Location: plugin-name/includes/controller/settings.class.php */
