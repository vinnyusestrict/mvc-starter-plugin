<?php
/**
 * Controls Admin Notices
 *
 * @author vinnyalves
 * @package PluginClass
 */

defined( 'ABSPATH' ) || die( 'No direct access allowed' );

/**
 * Helper library for managing admin notices.
 *
 * @author vinnyalves
 */
class PluginClass_Controller_Admin_Notices {


	/**
	 * Holds the notices that need to be rendered.
	 *
	 * @var array
	 */
	private $notices = array();


	/**
	 * The flag to whether we need the dismissible JS included.
	 *
	 * @var boolean
	 */
	private $has_dismissible = false;


	/**
	 * List of messages that have already been dismissed.
	 *
	 * @var array
	 */
	private $dismissed_notices = array();

	/*----------*/

	/**
	 * Dummy constructor.
	 */
	private function __construct() {
		/* Do nothing */ }


	/**
	 * Instanciate our singleton.
	 */
	public function instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
			$instance->add_actions();
			$instance->load_dismissed();
		}

		return $instance;
	}


	/**
	 * Adds the action to render notices.
	 */
	private function add_actions() {
		add_action( 'admin_notices', array( $this, 'render_notices' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_dismissible_js' ) );
	}


	/**
	 * The meat to adding notices. Used by add_{$type} methods.
	 *
	 * @param string  $message           The message to be displayed in the notice.
	 * @param string  $type              The type of error: success, error, warning, info.
	 * @param boolean $is_dismissible   Whether to add the is-dismissible class to the notice.
	 * @param string  $screen_id         Limits rendering only to the given screen_id.
	 * @param string  $scope             Whether this is a user or global message (for dismissals).
	 *                                   Possible values are 'user' and 'global'. Defaults to 'global'.
	 * @param string  $cap               A WordPress capability or role. Defaults to 'manage_options'.
	 */
	private function add_notice( $message, $type, $is_dismissible, $screen_id, $scope, $cap ) {
		$this->notices[] = array(
			'message'        => $message,
			'type'           => $type,
			'is_dismissible' => $is_dismissible,
			'screen_id'      => $screen_id,
			'scope'          => $scope,
			'cap'            => $cap,
		);
	}


	/**
	 * Magic method to handle adding a notice. Supports add_success(), add_error(), add_warning() and add_info().
	 *
	 * @param string $name     The name of the method.
	 * @param array  $args     See add_notice() for details on $args keys.
	 * @throws Exception       Throws an exception if the scope is not one of global or user.
	 * @return void|NULL
	 */
	public function __call( $name = '', $args = array() ) {

		// Make sure it's a valid call.
		if ( preg_match( '/^add_(success|error|warning|info)$/', $name, $matches ) ) {
			$type      = $matches[1];
			$message   = isset( $args['message'] ) ? $args['message'] : null;
			$screen_id = isset( $args['screen_id'] ) ? $args['screen_id'] : null;
			$cap       = isset( $args['cap'] ) ? $args['cap'] : 'manage_options';

			$is_dismissible = '';
			if ( isset( $args['is_dismissible'] ) && true === (bool) $args['is_dismissible'] ) {
				$is_dismissible        = true;
				$this->has_dismissible = true;
			}

			// Handle the scope. Defaults to 'user'.
			$scope = 'user';

			if ( isset( $args['scope'] ) ) {
				if ( ! in_array( $args['scope'], array( 'global', 'user' ), true ) ) {
										/* translators: 1: the scope provided */
					throw new Exception( sprintf( __( 'Invalid notification scope: %1$s.', 'plugin-slug' ), $args['scope'] ) );
				}

				$scope = $args['scope'];
			}

			return $this->add_notice( $message, $type, $is_dismissible, $screen_id, $scope, $cap );
		}

		unset( $name, $args );

		return null;
	}

	/**
	 * Renders the notice HTML.
	 */
	public function render_notices() {
		foreach ( $this->notices as $notice ) {
			$this->render_notice( $notice );
		}
	}

	/**
	 * Renders a given notice.
	 *
	 * @param array $notice     The notice array comprised of the following message, type, is_dismissible, screen_id, scope and cap.
	 */
	private function render_notice( $notice ) {
		$message        = $notice['message'];
		$type           = $notice['type'];
		$is_dismissible = true === $notice['is_dismissible'] ? 'is-dismissible' : '';
		$screen_id      = $notice['screen_id'];
		$scope          = $notice['scope'];
		$cap            = $notice['cap'];

		// Check capability.
		if ( ! current_user_can( $cap ) ) {
			return;
		}

		// Do we have a target screen?
		if ( $screen_id ) {
			$screen = get_current_screen();

			// Do nothing if this is not the target screen.
			if ( $screen->id !== $screen_id ) {
				return;
			}
		}

		// Handle dismissable notices.
		$notice_hash = '';
		if ( $is_dismissible ) {
			// Build the identifier.
			$md5 = md5( join( '', $notice ) );

			if ( isset( $this->dismissed_notices['global'][ $md5 ] ) || isset( $this->dismissed_notices['user'][ $md5 ] ) ) {
				return; // This notice was already dismissed globally or for the user.
			}

			$notice_hash = sprintf( 'data-hash="%s"', esc_attr( $md5 ) );
		}

		// Set the class name.
		$class      = sprintf( 'PluginClass-admin-notice notice notice-%s %s %s', $type, $is_dismissible, $notice_hash );
		$data_scope = $is_dismissible ? sprintf( 'data-scope="%s"', esc_attr( $scope ) ) : '';
		?>
<div class="<?php echo esc_attr( $class ); ?>">
	<p><?php echo esc_html( $message ); ?></p>
</div>
		<?php
	}

	/**
	 * Loads previously dismissed notices from the database.
	 */
	private function load_dismissed() {
		$global = get_option( 'PluginClass_notices', array() );
		$user   = get_user_meta( get_current_user_id(), 'PluginClass_notices' );

		$this->dismissed_notices = array(
			'global' => $global,
			'user'   => $user,
		);
	}

	/**
	 * Maybe adds the javascript required to make dismissible notices permanent.
	 */
	public function add_dismissible_js() {
		if ( $this->has_dismissible ) {
			wp_enqueue_script( 'plugin-slug_admin_dismissible_notice', PluginClass()->environment->js_url . 'dismissible_notices.js', array( 'jquery' ), PluginClass()->version, $in_footer = false );
		}
	}

	/**
	 * Cleanup during plugin uninstall.
	 */
	public static function uninstall() {
		global $wpdb;

		// Remove data from options.
		delete_option( 'PluginClass_notices' );

		// Remove data from usermeta table.
		$wpdb::delete(
			$wpdb->usermeta,
			array( 'meta_key' => 'PluginClass_notices' ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			array( '%s' )
		);
	}

}

/**
 * End of file class-pluginclass-controller-admin-notices.php
 * Location: plugin-slug/includes/controller/class-pluginclass-controller-admin-notices.php
 */

