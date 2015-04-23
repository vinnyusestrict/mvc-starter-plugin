<?php defined('ABSPATH') or die("No direct access allowed");
/**
 * Plugin settings model.
 * @author vinnyalves
 */
class PluginClass_Model_Settings {

	/**
	 * Declare properties as private so we can use __set() and __get() on them
	 * @var string
	 */
	private $foo = 'bar';
	
	########################

	/**
	 * The constructor - it overrides any default settings with whatever is in $params 
	 * @param array $params
	 */
	public function __construct( $params=array() ) 
	{
		$this->set_properties( $params );
	}
	
	/**
	 * Our getter. Used mainly because we have to _validate the setter
	 * and getter/setter magic methods only get called for private properties
	 * @param string $key
	 */
	public function __get( $key )
	{
		return $this->{$key};
	}
	
	
	/**
	 * Our setter, takes care of validating input.
	 * @param string $key
	 * @param mixed $val
	 */
	public function __set( $key, $val )
	{
		$val = $this->_validate( $key, $val);
		
		$this->{$key} = $val;
	}
	

	/**
	 * Property setter
	 * @param array $params
	 */
	private function set_properties( $params=array() )
	{
		foreach ($params as $key => $val)
		{
			$val = $this->_validate( $key, $val );
			
			$this->{$key} = $val;
		}
	}
	

	/**
	 * Used by the WP Settings API. See settings_dao.class.php
	 * @param string $key
	 * @param mixed $val
	 * @return Ambigous <mixed, string, boolean>
	 */
	public function is_valid( $key, $val )
	{
		return $this->_validate( $key, $val, false );
	}
	
	
	/**
	 * Setter validation
	 * @param string $key
	 * @param mixed $val
	 * @param boolean $die_on_error - whether to throw wp_die() or return false on errors
	 * @return Ambigous <mixed, string, boolean>
	 */
	private function _validate( $key, $val, $die_on_error=true )
	{
		if (! property_exists( $this, $key ) )
		{
			return $die_on_error ? 
				   wp_die( __( sprintf('Invalid property %s for Settings Model', $key ), PluginClass::$instance->domain ) ) :
				   false;
		}
		
		// Validate each key/value pair
		switch( $key )
		{
			case 'foo':
				if ( 'bar' !== $val )
				{
					return $die_on_error ? 
						   wp_die( __( sprintf( 'Some message about %s', $key), PluginClass::$instance->domain ) ) : 
						   false;
				}
				break;
			default:
				break;
		}
		
		return $val;
	}
}

/* End of file settings_model.class.php */
/* Location: plugin-name/includes/model/settings.class.php */
