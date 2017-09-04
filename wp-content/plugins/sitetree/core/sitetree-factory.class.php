<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 * 
 * @since 1.3
 */
abstract class SiteTreeFactory {
	/**
	 * Database object reference.
	 *
	 * @since 1.3
	 * @var object
	 */
	protected $db;
	
	/**
	 * What sitemap to make.
	 *
	 * @since 1.3
	 * @var string
	 */
	protected $sitemap_type = '';
	
	/**
	 *
	 * @since 1.3
	 * @var array
	 */
	protected $content_info;
	
	/**
	 *
	 *
	 * @since 1.4
	 * @var string
	 */
	protected $limit;
	
	/**
	 *
	 *
	 * @since 1.3
	 * @var array
	 */
	protected $info;
	
	/**
	 * @since 1.3
	 * @var string
	 */
	protected $sitemap = '';
	
	/**
	 * @since 1.5
	 * @var array
	 */
	protected $stats = array( 'url_count' => 0 );
	
	/**
	 * Initialises the properties of the class.
	 *
	 * @since 1.3
	 * @param $db object
	 */
	public function __construct($db) {
		$this->db = $db;
		$this->stats['runtime'] = -microtime(true);
		$this->stats['num_queries'] = -get_num_queries();
		$this->content_info = $this->db->getOption( $this->sitemap_type );
	}
	
	/**
	 * Returns the sitemap.
	 *
	 * @since 1.4
	 * @return string
	 */
	public function &getSitemap() {
		$this->loop_start();
		
		if ( is_array($this->content_info) ) {
			foreach ($this->content_info as $this->info) {
				if (! $this->get_info('include') ) continue;
				
				$callback = $this->get_info('callback');
				
				if ( method_exists($this, $callback) && ( $result = $this->{$callback}() ) )
					$this->loop_inner($callback, $result);
				
				if ( $this->getLimit() < 1 ) break;
			}
		}
		
		$this->loop_end();
		
		$this->stats['num_queries'] += get_num_queries();
		$this->stats['date']		 = time();
		$this->stats['runtime']		 = round( $this->stats['runtime'] + microtime(true), 3 );
		
		return $this->sitemap;
	}
	
	/**
	 *
	 * @since 1.3
	 */
	protected function loop_start() {}
	
	/**
	 *
	 * @since 1.3
	 */
	protected function loop_inner($callback, &$args) {}
	
	/**
	 *
	 * @since 1.3
	 */
	protected function loop_end() {}
	
	/**
	 *
	 * @since 1.3
	 * @return mixed
	 */
	protected function get_info($key, $default = null) {
		if ( isset($this->info[$key]) )
			return $this->info[$key];
			
		return $default;
	}
	
	/**
	 * Returns the number of URLs left to fill the sitemap.
	 *
	 * @since 1.4
	 * @return int
	 */	
	protected function getLimit() {
		return ( $this->limit - $this->stats['url_count'] );
	}
	
	/**
	 *
	 * @since 1.4
	 * @return array
	 */
	public function getStats() { return $this->stats; }
}
?>