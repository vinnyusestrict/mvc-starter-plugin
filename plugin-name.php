<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/*
* Plugin Name: PLUGIN_NAME
* Description: PLUGIN_DESC
* Version:	   0.1
* Author: 	   
* License:     GNU General Public License, v2 ( or newer )
* License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
* Domain Path: /lang
* Text Domain: PluginClass
* 
* 
* Based on MVC Starter Plugin v1.2.4 by UseStrict Consulting
*
* Copyright (C) <YEAR> <COPY_TEXT>, released under the GNU General Public License.
*/

class PluginClass 
{
	const VERSION = '0.1';
	
	/**
	 * The singletons
	 * @var array
	 */
	public static $instances = array();
	
	/**
	 * The domain to be used for l10n. Defaults to the parent class name
	 * @var string
	 */
	public $domain = __CLASS__;
	
	/**
	 * Holds the environment object once set_env() is called
	 * @var object
	 */
	protected static $env;
	
	/**
	 * The name of the key in wp_options table that holds our settings
	 * @var string
	 */
	protected $settings_name = __CLASS__;
	
	/**
	 * Holds library singletons
	 * @var object
	 */
	protected $libs;
	
	#########################
	
	
	public function __construct( $params=array() )
	{
		$this->set_env();
		
		// Load abstract model class
		$this->load_lib( 'model/abstract/model' );
		
		if ( self::is_admin() )
		{
			// Load the settings
			$this->load_lib( 'controller/settings' );
			
			register_activation_hook( __FILE__, array( $this, 'do_activation') );
			register_deactivation_hook( __FILE__, array( $this, 'do_deactivation') );
		}
	}
	
	/**
	 * Activation method for register_activation_hook
	 */
	public function do_activation()
	{
		return;
	}
	
	
	/**
	 * Deactivation method for register_deactivation_hook
	 */
	public function do_deactivation()
	{
		return;
	}
	
	
	/**
	 * The singleton method
	 * @return object
	 */
	public static function bootstrap( $params=array() ) 
	{
		$class = function_exists( 'get_called_class' ) ? get_called_class() : self::get_called_class();  
		
		if ( ! isset( self::$instances[$class] ) ) {
			self::$instances[$class] = new $class( $params );
        }
		
		return self::$instances[$class];
	}
	
	
	/**
	 * Workaround for PHP < 5.3 that doesn't have get_called_class
	 * @return boolean
	 */
	private static function get_called_class()
	{
		$bt = debug_backtrace();
	
		if ( is_array( $bt[2]['args'] ) && 2 === count( $bt[2]['args'] ) )
		{
			return $bt[2]['args'][0][0];
		}
	
		return $bt[1]['class'];
	}
	
	
	/**
	 * Sets some needed variables
	 */
	protected function set_env()
	{
		$root = trailingslashit( dirname( __FILE__ ) );
		$plugin_url = trailingslashit( plugins_url( 'assets', __FILE__ ) );
	
		self::$env = ( object ) array( 
				'root_dir' => $root,
				'inc_dir'  => $root . 'includes/',
				'tmpl_dir' => $root . 'includes/view/templates/',
				'js_url'   => $plugin_url . 'js/',
				'css_url'  => $plugin_url . 'css/',
				'img_url'  => $plugin_url . 'img/',
				'plugin_file' => __FILE__,
		 );
	}
	
	
	/**
	 * Gets the env vars we set earlier
	 * @return StdClass
	 */
	protected function get_env()
	{
		if ( ! isset( self::$env ) )
			$this->set_env();
	
		return self::$env;
	}
	
	
	/**
	 * Wrapper for requiring libraries
	 * @param string $name
	 * @param array $params
	 * @param bool $force_reload
	 * @return object
	 */
	protected function load_lib( $name, $params = array(), $force_reload = false )
	{
		if ( isset( $this->libs ) && isset( $this->libs->$name ) && false === $force_reload )
			return $this->libs->$name;
	
		$filename = $this->get_env()->inc_dir . $name . '.class.php';
		if ( ! file_exists( $filename ) )
		{
			$bt = debug_backtrace();
			wp_die( 'Cannot find Lib file: ' . $filename. ' Debug:<pre>' . print_r( array( 'file' => $bt[0]['file'], 'line' => $bt[0]['line'], 'method' => $bt[0]['function'] ),1 ) . '</pre>' );
		}
	
		require_once( $filename );
	
		$classname = __CLASS__ . '_' . join( '_', explode( '/', $name ) );
	
		// Only require abstraction classes
		if ( false !== strstr( $filename, 'abstract/' ) )
			return;
	
		if ( ! isset( $this->libs ) )
			$this->libs = ( object ) array();
	
		if ( false === $force_reload && method_exists( $classname, 'bootstrap' ) && is_callable( array( $classname, 'bootstrap' ) ) )
			$this->libs->$name = call_user_func( array( $classname, 'bootstrap' ), $params );
		else
			$this->libs->$name = new $classname( $params );
	
		return $this->libs->$name;
	}
	
	/**
	 * Bulk class loading.
	 * @param string $dir
	 * @return array of objects
	 */
	protected function load_all( $dir )
	{
		$inc_dir = $this->get_env()->inc_dir;
	
		if ( false === ( strstr( $dir, $inc_dir ) ) )
			$dir = $inc_dir . '/' . $dir;
		
		$dir = str_replace( '//', '/', $dir );
		$dir = preg_replace( ',/$,', '', $dir );
			
		$loaded = array();
	
		foreach ( glob( $dir . '/*.class.php' ) as $file )
		{
			preg_match( '|/includes/( .* )+?\.class\.php|', $file, $matches );
				
			$loaded[$matches[1]] = $this->load_lib( $matches[1] );
		}
	
		return $loaded;
	}
	
	
	/**
	 * Renders a template
	 * @param string $name
	 * @param array $stash
	 * @param bool $debug
	 */
	protected function render_template( $name, $stash=array(), $debug=false )
	{
		$env = $this->get_env();
	
		if ( '.tmpl.php' !== substr( $name,-9 ) )
			$name .= '.tmpl.php';
	
		if ( ! file_exists( $env->tmpl_dir . $name ) )
			wp_die( 'Bad template request: ' . $env->tmpl_dir . $name );
	
		$stash = ( object ) $stash;
		
		if ( true === $debug )
			echo "$env->tmpl_dir/$name";
	
			include ( $env->tmpl_dir . $name );
	}
	
	
	/**
	 * @since 0.1
	 * @desc Custom is_admin method for testing
	 */
	public static function is_admin()
	{
		if ( has_filter( __CLASS__ . '_is_admin' ) )
			return apply_filters( __CLASS__ . '_is_admin', false );
		else
			return is_admin();
	}
	
	
	/**
	 * Logging
	 * @param string $msg
	 */
	public function log_msg( $msg )
	{
		error_log( __LINE__ . ' [' . date('d/m/Y H:i:s') . '] ' . print_r( $msg ,1 ) . PHP_EOL, 3, dirname( __FILE__ ) . '/log.txt');
	}
}

// Kick off the plugin
$PluginClass = PluginClass::bootstrap();


/* End of file <plugin-dir>.php */
/* Location: <plugin-dir>/<plugin-dir>.php */
