<?php
/**
 * Ajax Request model
 *
 * @author vinnyalves
 * @package PluginClass
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

// phpcs:disable WordPress.Files.FileName

/**
 * Model for ajax requests.
 *
 * @author vinnyalves
 */
class PluginClass_Model_Ajax_Request extends PluginClass_Model_Base {

	/**
	 * The overall status.
	 *
	 * @var array
	 */
	protected $is_success = array(
		'is'      => 'rw',
		'isa'     => 'boolean',
		'default' => false,
	);

	/**
	 * A localized message to the client.
	 *
	 * @var string
	 */
	protected $msg = array(
		'is'  => 'rw',
		'isa' => 'string',
	);


	/*-----------------*/

	/**
	 * Echoes the correct structure
	 */
	public function output() {

		/**
		 * Use __get() so that we grab the parent's data[] values instead of our own properties.
		 * This isn't a problem when accessing the Model externally.
		 * We could also spin up a new parent() object and call them directly, but that is more confusing.
		 */
		$out = array(
			'success' => $this->__get( 'is_success' ),
			'msg'     => $this->__get( 'msg' ),
			'data'    => $this->__get( 'data' ),
		);

		return $out;
	}
}

/**
 * End of file class-pluginclass-model-ajax-request.php
 * Location: plugin-slug/includes/model/class-pluginclass-model-ajax-request.php
 */
