<?php
/**
 * Plugin settings model. 
 * 
 * All of the hard work is done by the Base model, so keep the extends.
 *
 * @author vinnyalves
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

/**
 * Load the Base Class which we extend.
 */
require_once( __DIR__ . '/class-base.php');

class PluginClass_Model_Settings extends PluginClass_Model_Base {

	/**
	 * Declare properties as private so that the base model can access them and so that we can use __set() and __get() on them.
	 * Also used to make sure it's a valid value.
	 *
	 * Property attributes were loosely borrowed from Perl Moose.
	 *
	 * Supported values:
	 *     isa     => gettype()     One of the types supported by gettype. See https://www.php.net/manual/en/function.gettype.php.
	 *     is      => rw|ro         ReadOnly attributes cannot be changed after they have been instanciated by the DAO.
	 *     default => callable|var  Callable or specific value.
	 *     coerce  => boolean       Whether to try to convert the value to the expected isa.
	 *     regex   => pattern       Used with preg_match during validation.
	 *     callback => callable     Custom validation function. Must return an array( $value, $has_error_bool ).
	 *
	 * @var array
	 */
	protected $foo = array(
		'isa'     => 'string',
		'is'      => 'rw',
		'coerce'  => true,
		'regex'   => '/^.*$/',
	);

	
	/**
	 * You *must* call parent::__construct( $params ) after 
	 * setting any property defaults or validation callbacks.
	 * Or do not declare a __construct() method at all and let the parent do its thing.
	 * 
	 * @param array $params
	 */
	public function __construct( $params=array() ) {
	    
	    $this->foo['default']  = function(){ return 'bar'; };
	    $this->foo['callback'] = array( $this, 'my_foo_callback' );
	    
	    parent::__construct( $params );
	}
	
	
	/**
	 * Sample custom validation rule.
	 * 
	 * @param string $key
	 * @param mixed $val
	 * @return mixed|boolean
	 */
	protected function my_foo_callback( $key, $val ) {
	    return array( $val, $has_err = false );
	}
	
}

/**
 * End of file class-settings.php 
 * Location: <plugin-slug>/includes/model/class-settings.php
 */
