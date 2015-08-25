<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Controls settings display
 * @author vinnyalves
 */
class PluginClass_View_Settings extends PluginClass {

	public function __construct()
	{
		// We only allow instantiation in admin
		if ( ! parent::is_admin() )
			return;
	}
	
	public function add_admin_css()
	{
		wp_enqueue_style( $this->domain . '-settings-admin', $this->get_env()->css_url . 'plugin_settings.css', array(), self::VERSION );
	}
	
	public function add_admin_js()
	{
		wp_enqueue_script( $this->domain . '-settings-admin', $this->get_env()->js_url . 'plugin_settings.js', array( 'jquery' ), self::VERSION );
	}
	
	public function show_admin()
	{
		// Get stash items
		$stash = array( 'settings' => apply_filters( $this->domain . '_settings', array() ) );
		
		$this->render_template( 'plugin_settings', $stash );
	}
	
}

/* End of file settings.class.php */
/* Location: <plugin-dir>/includes/view/settings.class.php */
