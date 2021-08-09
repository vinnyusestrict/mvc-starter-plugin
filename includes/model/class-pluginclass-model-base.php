<?php defined( 'ABSPATH' ) or die( 'No direct access allowed' );
/**
 * Plugin settings model.
 *
 * @author vinnyalves
 */
abstract class PluginClass_Model_Base {

	/**
	 * Holds validation errors. Cannot be touched by __set();
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * Flag to tell us if the instanciation was complete so we can handle ro properties.
	 *
	 * @var boolean
	 */
	private $did_instanciation = false;


	/**
	 * Data store for our magic __get and __set methods.
	 *
	 * @var array
	 */
	private $data = array();


	/**
	 * The constructor - it overrides any default settings with whatever is in $params
	 *
	 * @param array $params
	 */
	public function __construct( $params = array() ) {
		$this->set_properties( $params );

		$this->did_instanciation = true;
	}


	/**
	 * Our getter. Used mainly because we have to do_validations the setter
	 * and getter/setter magic methods only get called for private properties
	 *
	 * @param string $key
	 */
	public function __get( $key ) {

		 // Handle the default value.
		if ( ! isset( $this->data[ $key ] ) && isset( $this->{$key}['default'] ) ) {
			$this->data[ $key ] = is_callable( $this->{$key}['default'] ) ? call_user_func( $this->{$key}['default'] ) : $this->{$key}['default'];
		}

		return $this->data[ $key ];
	}


	/**
	 * Magic setter.
	 *
	 * @param string $key
	 * @param mixed  $val
	 */
	public function __set( $key, $val ) {
		$this->set_properties( array( $key => $val ) );
	}


	/**
	 * Validates and set properties.
	 *
	 * @param array $params
	 */
	private function set_properties( $params ) {
		foreach ( $params as $key => $val ) {
			list($val, $has_err) = $this->validate( $key, $val );

			if ( false === $has_err ) {
				$this->data[ $key ] = $val;
			}
		}
	}


	/**
	 * Used by the WP Settings API. See settings_dao.class.php
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return Ambigous <mixed, string, boolean>
	 */
	public function validate( $key, $val ) {
		return $this->do_validations( $key, $val );
	}


	/**
	 * Data store for our error messages;
	 *
	 * @param string $key
	 * @param string $message
	 */
	private function add_error( $key, $message ) {
		$this->errors[ $key ] = $message;
	}


	/**
	 * Accessor for per-property validation errors
	 *
	 * @param string $key
	 * @return boolean|mixed
	 */
	public function get_error( $key ) {
		 return isset( $this->errors[ $key ] ) ? $this->errors[ $key ] : false;
	}


	/**
	 * Returns the errors array.
	 *
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}


	/**
	 * Wether we have any validation errors
	 *
	 * @return boolean
	 */
	public function has_errors() {
		return (bool) count( $this->errors );
	}


	/**
	 * Validates if a property has been declared.
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return array mixed|boolean
	 */
	final private function validate_property_exists( $key, $val ) : array {
		$has_err = false;

		// Is it an actual property?
		if ( ! property_exists( $this, $key ) ) {
											   /* translators: 1: name of the setting field */
			$this->add_error( $key, sprintf( __( 'Unknown property "%1$s" for plugin-slug', 'plugin-slug' ), $key ) );

			$val     = null;
			$has_err = true;
		}

		return array( $val, $has_err );
	}


	/**
	 * Checks if a property has validation attributes.
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return array mixed|boolean
	 */
	final private function validate_attributes( $key, $val ) : array {
		$attrs   = $this->{$key};
		$has_err = false;

		if ( ! is_array( $attrs ) || empty( $attrs ) ) {
			$this->add_error( $key, sprintf( __( 'I do not know how to validate property "%1$s"!', 'plugin-slug' ), $key ) );

			$val     = null;
			$has_err = true;
		}

		return array( $val, $has_err );
	}


	/**
	 * Validates if a property is readonly and trying to get overwritten.
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return array mixed|boolean
	 */
	final private function validate_readonly( $key, $val ) : array {
		$attrs   = $this->{$key};
		$has_err = false;

		// is => ro
		if ( true === $this->did_instanciation ) {
			if ( isset( $attrs['is'] ) && 'ro' === $attrs['is'] ) {
												   /* translators: 1: name of the setting field */
				$this->add_error( $key, sprintf( __( 'Readonly property "%1$s" cannot be overwritten.', 'plugin-slug' ), $key ) );

				$val     = null;
				$has_err = true;
			}
		}

		return array( $val, $has_err );
	}


	/**
	 * Checks if the value being set is of the expected type.
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return array mixed|boolean
	 */
	final private function validate_type( $key, $val ) : array {
		$attrs   = $this->{$key};
		$has_err = false;

		if ( isset( $attrs['isa'] ) && gettype( $val ) !== $attrs['isa'] ) {
			if ( isset( $attrs['coerce'] ) && true === $attrs['coerce'] ) {
				settype( $val, $attrs['isa'] );
			} else {
											   /* translators: 1: the name of the setting field. 2: the type received. 3: the expected type. */
				$this->add_error( $key, sprintf( __( 'Wrong type for property "%1$s". Got "%2$s", expecing "%3$s".', 'plugin-slug' ), $key, gettype( $val ), $attrs['isa'] ) );

				$val     = null;
				$has_err = true;
			}
		}

		return array( $val, $has_err );
	}


	/**
	 * Checks if the value matches the expected regular expression.
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return array mixed|boolean
	 */
	final private function validate_regex( $key, $val ) : array {
		$attrs   = $this->{$key};
		$has_err = false;

		if ( isset( $attrs['regex'] ) ) {
			if ( ! is_scalar( $val ) ) {
											   /* translators: 1: the name of the setting field. 2: the expected regular expression pattern. */
				$this->add_error( $key, sprintf( __( 'Property "%1$s" cannot be checked against regex pattern "%2$s".', 'plugin-slug' ), $key, $attrs['regex'] ) );

				$val     = null;
				$has_err = true;
			} elseif ( ! preg_match( $attrs['regex'], $val ) ) {

											   /* translators: 1: the name of the setting field. 2: the expected regular expression pattern. */
				$this->add_error( $key, sprintf( __( 'Property "%1$s" does not match regex pattern "%2$s".', 'plugin-slug' ), $key, $attrs['regex'] ) );

				$val     = null;
				$has_err = true;
			}
		}

		return array( $val, $has_err );
	}


	/**
	 * Validates against custom callback attribute.
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return array mixed|boolean
	 */
	final private function validate_callback( $key, $val ) : array {
		$attrs   = $this->{$key};
		$has_err = false;

		if ( isset( $attrs['callback'] ) && is_callable( $attrs['callback'] ) ) {
			list( $val, $has_err ) = call_user_func( $attrs['callback'], $key, $val );
		}

		return array( $val, $has_err );
	}

	/**
	 * Setter validation
	 *
	 * @param string $key
	 * @param mixed  $val
	 * @return Ambigous <mixed, string, boolean>, boolean
	 */
	final private function do_validations( $key, $val ) {

		// Is this an actual property?
		list( $val, $has_err ) = $this->validate_property_exists( $key, $val );

		// Are the attributes set up correctly?
		if ( ! $has_err ) {
			list( $val, $has_err ) = $this->validate_attributes( $key, $val );
		}

		$attrs = $this->{$key};

		// is => ro.
		if ( ! $has_err ) {
			list( $val, $has_err ) = $this->validate_readonly( $key, $val );
		}

		// isa/coerce.
		if ( ! $has_err ) {
			list( $val, $has_err ) = $this->validate_type( $key, $val );
		}

		// regex.
		if ( ! $has_err ) {
			list( $val, $has_err ) = $this->validate_regex( $key, $val );
		}

		// Custom callback. Must be overridden by child class or will always return good data.
		if ( ! $has_err ) {
			list( $val, $has_err ) = $this->validate_callback( $key, $val );
		}

		return array( $val, $has_err );
	}
}

/**
 * End of file class-base.php
 * Location: plugin-slug/includes/model/class-base.php
 */
