<?php defined('ABSPATH') or die("No direct access allowed");
/**
 * Controls ajax requests.
 * 
 * @author vinnyalves
 */
class PluginClass_Controller_Ajax extends Plugin {

	private $ar;
	
	public function __construct()
	{
		if ( ! parent::is_admin() )
			return;
	
		$this->load_lib('model/ajax_request');
	
		add_action('wp_ajax_cb2-geo-load-countries', array( $this, 'geo_load_countries' ) );
		add_action('wp_ajax_cb2-geo-load-regions', array( $this, 'geo_load_regions' ) );
		add_action('wp_ajax_cb2-geo-load-cities', array( $this, 'geo_load_cities' ) );
		add_action('wp_ajax_cb2-run-simulation', array( $this, 'run_simulation' ) );
	}
	
	
	
	/**
	 * Wrapper to check if we're in an ajax call
	 * @return boolean
	 */
	private function _doing_ajax()
	{
		return (defined('DOING_AJAX') && DOING_AJAX);
	}
	
	/**
	 * Wrapper to fetch query params
	 * @param array $vars
	 * @param string $method
	 * @param string $callback
	 */
	private function _init( &$vars=array(), $method='POST', &$callback=null)
	{
		$this->ar = new PluginClass_Model_Ajax_Request();
		$params   = null;
		
		if ( 'GET' === $method && isset( $_GET ) ) 
		{
			$params =& $_GET;
		}
		elseif( 'POST' === $method && isset($_POST) ) 
		{
			$params =& $_POST;
		}
	
		if ( isset($params) ) 
		{
			foreach ($vars as $name => $value) 
			{
				if ( isset( $params[$name] ) ) 
				{
					$vars[$name] = $params[$name];
				}
			}
	
			if ( isset( $params['callback'] ) ) 
			{
				$callback = trim($params['callback']);
			}
		}
		
		$this->ar->callback = $callback;
	}
	
	
	/**
	 * Output or return Ajax Request model
	 */
	private function _done()
	{
		if ( $this->_doing_ajax() )
		{
			$this->ar->output();
			die();
		}
		
		ob_start();
		$this->ar->output();
		return ob_get_clean();
	}
	
}

/* End of file ajax.class.php */
/* Location: <plugin-dir>/includes/controller/ajax.class.php */
