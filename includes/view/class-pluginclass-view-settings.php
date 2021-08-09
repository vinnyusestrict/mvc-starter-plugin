<?php
/**
 * Controls settings display
 *
 * @author vinnyalves
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

/**
 * Settings View class
 * 
 * @author vinnyalves
 */
class PluginClass_View_Settings {

    /**
     * Constructor. Do nothing if not admin.
     */
	public function __construct() {
		if ( ! PluginClass::is_admin() ) {
			return;
		}
	}

	
	/**
	 * Singleton.
	 * @return NULL|PluginClass_View_Settings
	 */
	public static function instance() {
	    
        // We only allow instantiation in admin.
	    if ( ! PluginClass::is_admin() ) {
	        return null;
	    }
	    
	    static $instance = null;
	    
	    if ( null === $instance ) {
	        $instance = new self();
	        
	        $instance->version = PluginClass()->version;
	        $instance->css_url = PluginClass()->css_url;
	        $instance->js_url  = PluginClass()->js_url;
	    }
	    
	    return $instance;
	}
	
	
	/**
	 * Enqueue the CSS file in the settings screen
	 */
	public function add_admin_css() {
	    
	    $self = self::instance();
	    
		wp_enqueue_style( 'PluginClass-settings-admin', $self->css_url . 'plugin-settings.css', array(), $self->version );
	}

	
	/**
	 * Enqueue the JS file in the settings screen
	 */
	public function add_admin_js() {
	    
	    $self = self::instance();
	    
		wp_enqueue_script( 'PluginClass-settings-admin', $self->js_url . 'plugin-settings.js', array( 'jquery' ), $self->version );
	}

	
	/**
	 * Show the settings screen.
	 */
	public static function show_admin() {
		// Get stash items
		$stash = array( 'settings' => PluginClass()->settings );

		PluginClass()->render_template( 'plugin-settings', $stash );
	}

}

/**
 * End of file settings.class.php
 * Location: plugin-slug/includes/view/settings.class.php
 */
