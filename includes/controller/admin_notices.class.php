<?php defined( 'ABSPATH' ) or die( "No direct access allowed" );
/**
 * Controls Admin Notices
 * @author vinnyalves
 */
class PluginClass_Controller_Admin_Notices extends PluginClass {

	/**
	 * Holds our notices
	 * @var array
	 */
	protected $notices = array();
	
	
	/**
	 * All available messages 
	 * @var array
	 */
	private static $msg_pool;
	
	
	/**
	 * The query string element that holds the messages
	 * @var unknown
	 */
	private $query_element;
	
	
	#############################
	
	
	public function __construct( $params=array() )
	{
		if ( ! parent::is_admin() )
			return;
		
		$this->query_element = get_parent_class( $this );
		
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		
		// Captures redirects after posts like when saving metaboxes
// 		add_filter( 'redirect_post_location', array( $this, 'capture_redirect' ) );
		add_filter( 'wp_redirect', array( $this, 'capture_redirect' ) );
		
		if ( isset( $_GET[$this->query_element] ) )
		{
			add_filter( 'post_updated_messages', array( $this, 'show_notices' ) );
		}
		
		add_filter( $this->query_element . '_notice_pool', array( $this, 'get_notice_pool' ) );
	}
	

	/**
	 * Wrapper for _set_msg
	 * @param string $code
	 * @param bool $die_on_error
	 */
	public function set_notice( $code, $die_on_error=false )
	{
		$msg = $this->get_message( $code );
		
		if ( true === $die_on_error )
		{
			wp_die( $msg->msg );
		}
		else
		{
			$this->_set_msg( $code );
		}
	}
	
	
	/**
	 * Internal Notice setter. $code is added to settings API message div
	 * @param string $msg
	 * @param boolean $is_error
	 * @param boolean $is_nag
	 * @param string $code
	 */
	private function _set_msg( $code )
	{
		global $pagenow;
		
		// Maybe use Settings API
		if ( ( 'options.php' === $pagenow ||  
		       'options-general.php' === $pagenow ) /* && 
		       isset( $_GET['page'] ) && $this->domain === $_GET['page'] */ )
		{
			$msg = $this->get_message( $code );
			add_settings_error( $this->settings_name, $code, $msg->msg, $msg->type );
		}
		// Make sure we get unique notices
		$this->notices[$code] = true;
	}
	
	
	/**
	 * Displays any cached notices
	 */
	public function show_notices( $messages=array() )
	{
		if ( isset( $_GET[$this->query_element] ) )
		{
			$keys = explode( ',', trim( $_GET[$this->query_element] ) );
			$this->notices = array_combine( $keys, $keys );
		}
		
		foreach ( array_keys( $this->notices ) as $code )
		{
			$msg = $this->get_message( $code );
			$div = sprintf( '<div class="%s"><p>%s</p></div>', $msg->type, $msg->msg );
			
			if ( doing_filter( 'post_updated_messages' ) )
			{
				$messages[] = $div;
			}
			else 
			{
				echo $div;
				unset( $this->notices[$code] );
			}
		}
		
		return $messages;
	}
	
	
	
	/**
	 * Clears notices
	 */
	public function clear_notices()
	{
		$this->notices = array();
	}
	
	
	/**
	 * Keeps state between redirects 
	 * @param string $location
	 * @return string
	 */
	public function capture_redirect( $location )
	{
		if ( ! $this->has_notices() )
			return esc_url_raw( remove_query_arg( $this->query_element, $location ) );

		
		$keys = join( ',', array_keys( $this->notices ) );
		return esc_url_raw( add_query_arg( $this->query_element, $keys, $location ) );
	}
	
	
	/**
	 * Access to the message pool
	 * @param string $code
	 * @return multitype:StdClass
	 */
	public function get_message( $code )
	{
		if ( ! isset( self::$msg_pool ) )
		{
			self::$msg_pool = apply_filters( $this->query_element . '_notice_pool', array() );
		}
		
		if ( ! isset( self::$msg_pool[$code] ) )
		{
			wp_die( sprintf( __( 'Invalid message code %s', 'PluginClass' ), $code ) );
		}
		
		return self::$msg_pool[$code];
		
	}
	
	
	/**
	 * Allows checking if there are notices 
	 * @return boolean
	 */
	public function has_notices()
	{
		return ! empty( $this->notices );
	}
	
	
	/**
	 * Returns array of common notice objects
	 * @param array $notices
	 * @return array
	 */
	public function get_notice_pool( $notices=array() )
	{
		// Not all classes get reloaded after wp_redirect, so add those messages here.
		return array_merge( $notices, array( 
				'invalid-postid' => ( object ) array( 'type' => 'error', 'msg' => __( 'Invalid post_id.', 'PluginClass' ) ),
				'bad-params'     => ( object ) array( 'type' => 'error', 'msg' => __( 'Invalid parameter type.', 'PluginClass' ) ),
		 ) );
	}
	
}

/* End of file admin_notices.class.php */
/* Location: <plugin-dir>/includes/controller/admin_notices.class.php */

