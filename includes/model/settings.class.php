<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Plugin settings model.
 * @author vinnyalves
 */
class PluginClass_Model_Settings {

	/**
	 * Declare properties as private so we can use __set() and __get() on them.
	 * Also used to make sure it's a valid setting.
	 * 
	 * Property attributes were borrowed from Perl Moose.
	 * 
	 * Supported values:
	 *     isa     =>              One of the types supported by gettype. See https://www.php.net/manual/en/function.gettype.php 
	 *     is      => rw|ro.       ReadOnly attributes cannot be changed after they have been instanciated by the DAO.
	 *     default =>              callable or specific value
	 *     coerce  => boolean      Whether to try to convert the value to the expected isa.
	 *     regex   => pattern      Used with preg_match during validation.
	 *     
	 * @var string
	 */
    private $foo = [ 'isa' => 'string', 'is' => 'rw', 'default' => function(){ return 'bar'; }, 'coerce' => true, 'regex' => '/^.*$/' ];
	
	
	########################
	
	/**
	 * Holds validation errors. Cannot be touched by __set();
	 * @var array
	 */
	private $errors = [];
	
	/**
	 * Flag to tell us if the instanciation was complete so we can handle ro properties.
	 * @var boolean
	 */
	private $did_instanciation = false;
	
	
	/**
	 * Data store for our magic __get and __set methods.
	 * @var array
	 */
	private $data = [];
	
	########################

	/**
	 * The constructor - it overrides any default settings with whatever is in $params 
	 * @param array $params
	 */
	public function __construct( $params=[] ) 
	{
	    $this->set_properties( $params );
	    
	    $this->did_instanciation = true;
	}
	
	/**
	 * Our getter. Used mainly because we have to _do_validation the setter
	 * and getter/setter magic methods only get called for private properties
	 * @param string $key
	 */
	public function __get( $key )
	{
	    // Handle the default value
	    if ( ! isset( $this->data[$key] ) && isset( $this->{$key}['default'] ) )
	    {
	        $this->data[$key] = is_callable( $this->{$key}['default'] ) ? call_user_func( $this->{$key}['default'] ) : $this->{$key}['default'];
	    }
	    
	    return $this->data[$key];
	}
	
	
	/**
	 * Our setter, takes care of validating input.
	 * @param string $key
	 * @param mixed $val
	 */
	public function __set( $key, $val )
	{
	    // Don't magicify $this->errors[], $this->data[] or $this->did_instanciation
	    if ( ! in_array( $key, ['errors', 'data', 'did_instanciation'] ) )
	    {
	        $this->set_properties( [ $key => $val ] );
	    }
	    else
	    {
	        throw new Exception ( __( 'Cheatin&#8217; huh?') );
	    }
	}
	

	/**
	 * Property setter
	 * @param array $params
	 */
	private function set_properties( $params  )
	{
		foreach ( $params as $key => $val )
		{
			list($val, $has_err) = $this->validate( $key, $val );
			
			if ( false === $has_err )
			{
                $this->data[$key] = $val;
			}
		}
	}
	

	/**
	 * Used by the WP Settings API. See settings_dao.class.php
	 * @param string $key
	 * @param mixed $val
	 * @return Ambigous <mixed, string, boolean>
	 */
	public function validate( $key, $val )
	{
		return $this->_do_validation( $key, $val );
	}
	
	
	/**
	 * Data store for our error messages;
	 * @param string $key
	 * @param string $message
	 */
	private function add_error( $key, $message )
	{
	    $this->errors[$key] = $message;
	}
	
	
	/**
	 * Accessor for validation errors
	 * @param string $key
	 * @return boolean|mixed
	 */
	public function get_error( $key )
	{
	    return isset( $this->errors[$key] ) ? $this->errors[$key] : false;
	}
	
	
	/**
	 * Wether we have any validation errors
	 * @return boolean
	 */
	public function has_errors()
	{
	    return (bool) count( $this->errors );
	}
	
	
	/**
	 * Setter validation
	 * @param string $key
	 * @param mixed $val
	 * @return Ambigous <mixed, string, boolean>, boolean
	 */
	private function _do_validation( $key, $val )
	{
	    $has_err = true;
	    
	    // Is it an actual property?
		if ( ! property_exists( $this, $key ) )
		{
		                                     /* translators: 1: name of the setting field */
		    $this->add_error( $key, sprintf( __( 'Unknown property "%1$s" for <TEXT_DOMAIN>', '<TEXT_DOMAIN>' ), $key ) );
		    
		    return [$val=false, $has_err];
		}
		
		// Validate against property attributes 
		$attrs = $this->{$key};
		
		// Did we get property attributes?
		if ( ! is_array( $attrs ) || empty( $attrs ) )
		{
		                                      /* translators: 1: name of the setting field */
		    $this->add_error( $key, sprintf( __( 'I do not know how to validate property "%1$s"!', '<TEXT_DOMAIN>' ), $key ) );
		    
		    return [$val=false, $has_err];
		}
		
		// is => ro
		if ( true === $this->did_instanciation )
		{
		    if ( isset( $attrs['is'] ) && 'ro' === $attrs['is'] )
		    {
		                                          /* translators: 1: name of the setting field */
		        $this->add_error( $key, sprintf( __( 'Readonly property "%1$s" cannot be overwritten.', '<TEXT_DOMAIN>' ), $key ) );
		        
		        return [$val=false, $has_err];
		    }
		}
		
		// isa/coerce
		if ( isset( $attrs['isa'] ) && gettype( $val ) !== $attrs['isa'] )
		{
		    if ( isset( $attrs['coerce'] ) && true === $attrs['coerce'] )
		    {
		        settype( $val, $attrs['isa'] );
		    }
		    else 
		    {
                                                /* translators: 1: the name of the setting field. 2: the type received. 3: the expected type. */
		        $this->add_error( $key, sprintf( __( 'Wrong type for property "%1$s". Got "%2$s", expecing "%3$s".', '<TEXT_DOMAIN>' ), $key, gettype( $val ), $attrs['isa'] ) );
		        
		        return [$val=false, $has_err];
		    }
		}
		
		// regex
		if ( isset( $attrs['regex'] ) )
		{
		    if ( ! preg_match( $attrs['regex'], $val ) )
		    {
		                                          /* translators: 1: the name of the setting field. 2: the expected regular expression pattern. */
		        $this->add_error( $key, sprintf( __( 'Property "%1$s" does not match regex pattern "%2$s".', '<TEXT_DOMAIN>' ), $key, $attrs['regex'] ) );
		        
		        return [$val=false, $has_err];
		    }
		}
		
		
		// Custom validation for each key/value pair
		switch( $key )
		{
			case 'foo':
				if ( 'bar' !== $val )
				{
				    $this->add_error( $key, sprintf( __( 'Invalid value for key "%1$s" <TEXT_DOMAIN>', '<TEXT_DOMAIN>' ), $key ) );
				    
				    return [$val=false, $has_err];
				}
				break;
			default:
				break;
		}

		return [$val, $has_err=false];
	}
}

/* End of file settings_model.class.php */
/* Location: <plugin-dir>/includes/model/settings.class.php */
