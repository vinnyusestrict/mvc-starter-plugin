<?php
/**
 * Controls ajax requests.
 *
 * @author vinnyalves
 * @package PluginClass
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

/**
 * Ajax controller class.
 *
 * @author vinnyalves
 */
class PluginClass_Controller_Ajax {

	/**
	 * Object constructor.
	 */
	public function __construct() {
		if ( ! PluginClass::is_admin() ) {
			return;
		}

		$this->add_actions();
	}


	/**
	 * Add the supported actions in this method.
	 */
	private function add_actions() {
		add_action( 'wp_ajax_example', array( $this, 'example' ) );
	}


	/**
	 * Wrapper to output the json data and die.
	 *
	 * @param mixed   $data          The data to be converted into JSON and outputted.
	 * @param boolean $is_success  Whether to call json_success or json_error.
	 * @param int     $status_code     The HTTP Status code to use. Defaults to 200.
	 * @param mixed   $flags         The flags passed to json_encode(). See https://www.php.net/manual/en/function.json-encode.php.
	 */
	private function done( $data, $is_success = true, $status_code = 200, $flags = 0 ) {
		if ( true === $is_success ) {
			wp_send_json_success( $data, $status_code, $flags );
		} else {
			wp_send_json_error( $output, $status_code, $flags );
		}
	}


	/**
	 * Cherry-pick accepted data.
	 *
	 * @param array  $allowed Accepted keys to get from the submission.
	 * @param string $method  Whether to look in $_GET or $_POST. Defaults to $_POST.
	 * @return array
	 */
	private function get_params( $allowed, $method = 'POST' ) {
		$_requested = 'POST' === $method ? $_POST : $_GET; //phpcs:ignore WordPress.Security.NonceVerification

		return array_intersect_key( $_requested, array_flip( $allowed ) );
	}


	/**
	 * An example action handler.
	 */
	public function example() {
		$allowed_keys = array(
			'the_nonce_field',
			'field1',
			'field2',
		);

		$params = $this->get_params( $allowed_keys, 'POST' );

		// Always verify the nonce field!
		if ( ! wp_verify_nonce( $params['the_nonce_field'], 'the_nonce_action' ) ) {
			$this->done( __( 'Bad Nonce', 'plugin-slug' ), $is_success = false, 403 );
		}

		// Do something with the parameters.
		$output = array( 'foo' => 'bar' );

		// Call done to print output and exit.
		$this->done( $output );
	}

}

/**
 * End of file class-pluginclass-controller-ajax.php
 * Location: plugin-slug/includes/controller/class-pluginclass-controller-ajax.php
 */
