<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Plugin Name: <PLUGIN_NAME>
 * Description: <PLUGIN_DESC>
 * Version:	   0.1
 * Author: 	   
 * License:     GNU General Public License, v2 ( or newer )
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Domain Path: /lang
 * Text Domain: PluginClass
 * 
 * @package     PluginClass 
 * 
 * Based on MVC Starter Plugin v1.3 by UseStrict Consulting
 *
 * Copyright (C) <YEAR> <COPY_TEXT>, released under the GNU General Public License.
 */
/**
 * Plugin Name:       <PLUGIN_NAME>
 * Plugin URI:        
 * Description:       <PLUGIN_DESC>
 * Version:           0.1
 * Requires PHP:      5.6.20
 * Requires at least: 5.0
 * Author:            
 * Author URI:        
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:
 * Text Domain:       TEXT_DOMAIN
 * Domain Path:       /languages
 */

if ( ! class_exists( 'PluginClass' ) ) :

class PluginClass 
{
	const VERSION = '0.1';
	
	/**
	 * Holds library singletons
	 * @var object
	 */
	protected $libs;
	
	
	/**
	 * Holds Magic variables
	 * @var array
	 */
	private $data = [];
	
	
	/**
	 * Environments listed here will not allow external setters.
	 */
	private $readonly = [
	    'settings'    => true,
	    'version'     => true,
	];
	
	private $environment;
	
	
	#########################
	
	/**
	 * Instantiate our singleton
	 * @return Object|PluginClass
	 */
	public static function instance()
	{
	    static $instance = null;
	    
	    if ( null === $instance )
	    {
	        $instance = new self();
	        
	        $instance->do_common();        // Run what always needs to run
	        $instance->do_admin();         // Does nothing if not admin
	        $instance->do_front_end();     // Does nothing if not front end
	    }
	    
	    return $instance;
	}
	
	/**
	 * Run code that is common in admin and front-end modes
	 */
	private function do_common()
	{
	    
	    /**
	     * Actions
	     */
	    add_action( 'init', array( $this, 'load_textdomain' ) );
	    
	    /**
	     * Environment
	     */
	    $this->environment      = $this->setup_environment();
	    
	    /**
	     * Variables
	     */
	    $this->data['settings'] = $this->load_lib('dal/settings_dao')->load();
	    $this->data['version']  = self::VERSION;
	}
	
	/**
	 * Run code intended for the back-end
	 */
	private function do_admin()
	{
	    if ( ! self::is_admin() )
	    {
	        return;
	    }
	    
	    // Load the settings admin controller
	    $this->load_lib( 'controller/settings' );
	    
	    register_activation_hook( __FILE__, array( $this, 'do_activation') );
	    register_deactivation_hook( __FILE__, array( $this, 'do_deactivation') );
	    
	    /* Add Admin Code here */
	}
	
	/**
	 * Run code intended for the front-end
	 */
	private function do_front_end()
	{
	    if ( self::is_admin() )
	    {
	        return;
	    }
	    
	    /* Add Front End code here */
	}
	
	/**
	 * Activation method for register_activation_hook
	 */
	public function do_activation()
	{
		/* Add activation code here */
	}
	
	
	/**
	 * Deactivation method for register_deactivation_hook
	 */
	public function do_deactivation()
	{
	    /* Add deactivation code here */
	}
	
	
	/**
	 * i18n
	 */
	public function load_textdomain()
	{
	    load_plugin_textdomain( 'TEXT_DOMAIN', false, dirname( untrailingslashit( plugin_basename( __FILE__ ) ) ) . '/lang' );
	}
	

	/**
	 * Sets some needed variables
	 */
	private function setup_environment()
	{
	    static $env = null;
	    
	    if ( null === $env )
	    {
            $this->libs = new StdClass();
    	    
    		$root = trailingslashit( dirname( __FILE__ ) );
    		$plugin_url = trailingslashit( plugins_url( 'assets', __FILE__ ) );
    	
    		$env = (object) [ 
    				'root_dir' => $root,
    				'inc_dir'  => $root . 'includes/',
    				'private_tmpl_dir' => $root . 'includes/view/templates/',
    		        'tmpl_dir' => $root . 'templates/',
    				'js_url'   => $plugin_url . 'js/',
    				'css_url'  => $plugin_url . 'css/',
    				'plugin_file' => __FILE__,
    		 ];
	    }
	    
	    return $env;
	}
	
	
	/**
	 * Wrapper for requiring libraries
	 * @param string $name
	 * @param array $params
	 * @param bool $force_reload
	 * @return object
	 */
	public function load_lib( $name, $params = [], $force_reload = false )
	{
	    if ( isset( $this->libs->{$name} ) && false === $force_reload )
		{
		    return $this->libs->{$name};
		}
	
		$filename = $this->environment->inc_dir . $name . '.class.php';
		if ( ! file_exists( $filename ) )
		{
			$bt = debug_backtrace();
			wp_die( 'Cannot find Lib file: ' . $filename. ' Debug:<pre>' . print_r( [ 'file' => $bt[0]['file'], 'line' => $bt[0]['line'], 'method' => $bt[0]['function'] ],1 ) . '</pre>' );
		}
	
		require_once( $filename );
	
		$classname = __CLASS__ . '_' . join( '_', explode( '/', $name ) );
	
		// Only require abstraction classes
		if ( false !== strstr( $filename, 'abstract/' ) )
		{
			return;
		}

		if ( ! isset( $this->libs->{$name} ) || true === $force_reload )
		{
		    if ( method_exists( $classname, 'instance' ) && is_callable([ $classname, 'instance' ] ) )
		    {
		        $this->libs->{$name} = call_user_func( [$classname, 'instance'], $params );
		    }
		    else
		    {
		        $this->libs->{$name} = new $classname( $params );
		    }
		}
	
		return $this->libs->{$name};
	}
	
	/**
	 * Renders a template with support for overriding the template file by placing it in 
	 * the active theme directory: wp-content/themes/my_theme/this-plugin-directory-name/...
	 * See the actual template files for instructions.
	 * 
	 * @param string $name
	 * @param array $stash
	 * @param bool $debug
	 */
	protected function render_template($name, $stash=array(), $debug=false, $is_private=false)
	{
		if ('.tmpl.php' !== substr($name,-9))
		{
			$name .= '.tmpl.php';
		}
	
        // Default path
		$path = $is_private ? $this->environment->private_tmpl_dir : $this->environment->tmpl_dir;
		$path .= $name;
		
		// Does the plugin dir exist in the active theme?
		$theme_plugin_dir = trailingslashit( get_stylesheet_directory() . '/' . basename(__DIR__) );

		if ( file_exists( $theme_plugin_dir . $name ) )
		{
		    $path = $theme_plugin_dir . $name;
		}
		else
		{
    		/**
    		 * Allow coders to override the template directory.
    		 * @var $path - the full path to the template file
    		 * @var $name - the name of the template file or path being requested 
    		 */
    		// If not, call apply_filters with the default path
		    $path = apply_filters( __CLASS__ . '_template_path', $path, $name );
		}
		
		// Check for existence and croak if bad
		if ( ! file_exists( $path ) )
		{
		    wp_die('Bad template request: ' . $path);
		}

		$stash = (object) $stash;
		
		if (true === $debug)
		{
			echo $path;
		}
	
		include ($path);
	}
	
	
	/**
	 * @since 0.1
	 * @desc Custom is_admin method for testing
	 */
	public static function is_admin()
	{
		if ( has_filter( __CLASS__ . '_is_admin' ) )
		{
			return apply_filters( __CLASS__ . '_is_admin', false );
		}
		else
		{
			return is_admin();
		}
	}
	
	
	/**
	 * Logging
	 * @param string $msg
	 */
	public function log_msg( $msg )
	{
		error_log( '[' . date('d/m/Y H:i:s') . '] ' . print_r( $msg ,1 ) . PHP_EOL, 3, dirname( __FILE__ ) . '/log.txt');
	}
	
	
	/**
	 * A dummy constructor to prevent PluginClass from being loaded more than once.
	 *
	 * @since 1.0
	 *
	 * @see PluginClass::instance()
	 * @see PluginClass();
	 */
	private function __construct() { /* Do nothing here */ }
	
	/**
	 * A dummy magic method to prevent PluginClass from being cloned
	 *
	 * @since 1.0
	 */
	public function __clone() { _doing_it_wrong( __CLASS__ . '::' . __FUNCTION__, __( 'Cheatin&#8217; huh?', 'TEXT_DOMAIN' ), '1.0' ); }
	
	/**
	 * A dummy magic method to prevent PluginClass from being unserialized
	 *
	 * @since 1.0
	 */
	public function __wakeup() { _doing_it_wrong( __CLASS__ . '::' . __FUNCTION__, __( 'Cheatin&#8217; huh?', 'TEXT_DOMAIN' ), '1.0' ); }
	
	/**
	 * Magic method for checking the existence of a certain custom field
	 *
	 * @since 1.0
	 */
	public function __isset( $key ) { return isset( $this->data[ $key ] ); }
	
	/**
	 * Magic method for getting PluginClass variables
	 *
	 * @since 1.0
	 */
	public function __get( $key ) { return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null; }
	
	/**
	 * Magic method for setting PluginClass variables
	 *
	 * @since 1.0
	 */
	public function __set( $key , $value ) 
	{
	    if ( isset( $this->readonly[$key] ) && $this->readonly[$key] )
	    {
	        _doing_it_wrong( __CLASS__ . '::' . __FUNCTION__, 
	            sprintf( 
	               /* translators: 1: %1$s: Name of the variable */
	                __( 'Cannot set readonly variable "%1$s"', 'TEXT_DOMAIN' ), 
	                $key 
                ), '1.0' );
	        
	        return;
	    }
	    
	    $this->data[ $key ] = $value; 
	}
	
	/**
	 * Magic method for unsetting PluginClass variables
	 *
	 * @since 1.0
	 */
	public function __unset( $key ) {
	    if ( isset( $this->data[ $key ] ) ) {
	        unset( $this->data[ $key ] );
	    }
	}
	
	/**
	 * Magic method to prevent notices and errors from invalid method calls
	 *
	 * @since 1.0
	 */
	public function __call( $name = '', $args = array() ) { unset( $name, $args ); return null; }
}

// Kick off the plugin
function PluginClass() {
    return PluginClass::instance();
}
PluginClass();

endif; // End if class_exists

/* End of file <plugin-dir>.php */
/* Location: <plugin-dir>/<plugin-dir>.php */
