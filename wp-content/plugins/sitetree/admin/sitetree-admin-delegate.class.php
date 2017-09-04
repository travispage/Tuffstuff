<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 *
 * @since 1.4
 */
class SiteTreeAdminDelegate {
	/**
	 *
	 *
	 * @since 1.4
	 * @var object
	 */
	private $plugin;
	
	/**
	 * Database object reference
	 *
	 * @since 1.4
	 * @var object
	 */
	private $db;
	
	/**
	 *
	 *
	 * @since 1.4
	 * @var string
	 */
	private $pages;
	
	/**
	 *
	 *
	 * @since 1.4
	 * @var string
	 */
	private $currentPageId;
	
	/**
	 *
	 *
	 * @since 1.4
	 * @var string
	 */
	private $menuId;
	
	/**
	 *
	 *
	 * @since 1.4
	 * @var string
	 */
	private $scriptSuffix;
	
	/**
	 * @since 1.4
	 * @var object
	 */
	private static $instance;
	
	/**
	 * @since 1.4
	 * @return object
	 */
	public static function instance() {
		if (! self::$instance )
			self::$instance = new self();
		
		return self::$instance;
	}
	
	/**
	 * Initialises the properties and hooks into the WordPress admin system.
	 *
	 * @since 1.4
	 */
	public function __construct( $plugin, $pages ) {
		$this->db			= $plugin->db();
		$this->plugin		= $plugin;
		$this->pages		= $pages;
		$this->menuId		= reset( $pages )->id;
		$this->scriptSuffix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
	}
	
	/**
	 *
	 * @since 1.4
	 */
	public function init( $page_view, $page_id ) {
		$this->pageView		 = $page_view;
		$this->currentPageId = $page_id;
	}
	
	/**
	 *
	 * @since 1.4
	 * @param int $post_id
	 */
	public function catch_post_trash_event( $post_id ) {
		$post = get_post( $post_id );
		
		if ( !( $post && ( $post->post_type == 'post' ) || ( $post->post_type == 'page' ) ) )
			return false;
		
		if (! in_array( $post_id, $this->db->getOption( 'html5', array(), 'exclude' ) ) )
			$this->db->invalidateCache( 'html5' );
		
		if ( $this->db->getOption( 'include', true, $post->post_type, 'xml' ) && 
			!in_array( $post_id, $this->db->getOption( 'xml', array(), 'exclude' ) ) )
		{
				$this->db->invalidateCache( 'xml' );
		}
	}
	
	/**
	 *
	 * @since 1.5
	 * @param int $post_id
	 */
	public function catch_post_delete_event( $post_id ) {
		if ( $exclude = $this->db->getOption( 'html5', array(), 'exclude' ) )
			$this->db->setOption( 'html5', array_diff( $exclude, array( $post_id ) ), 'exclude' );
			
		if ( $exclude = $this->db->getOption( 'xml', array(), 'exclude' ) )
			$this->db->setOption( 'xml', array_diff( $exclude, array( $post_id ) ), 'exclude' );
	}
	
	/**
	 *
	 * @since 1.4
	 */
	public function catch_authors_event() {
		if ( $this->db->getOption('include', false, 'authors', 'html5') || ($this->db->getOption('groupby', false, 'post', 'html5') == 'author') )
			$this->db->invalidateCache( 'html5' );
		
		if ( $this->db->getOption('include', false, 'authors', 'xml') )
			$this->db->invalidateCache( 'xml' );
	}
	
	/**
	 *
	 * @since 1.4
	 */
	public function catch_category_event() {
		if ( $this->db->getOption('include', false, 'categories', 'html5') || ($this->db->getOption('groupby', false, 'post', 'html5') == 'category') )
			$this->db->invalidateCache( 'html5' );
		
		if ( $this->db->getOption('include', false, 'category', 'xml') )
			$this->db->invalidateCache( 'xml' );
	}
	
	/**
	 *
	 * @since 1.4
	 */
	public function catch_post_tag_event() {
		if ( $this->db->getOption('include', false, 'tags', 'html5') )
			$this->db->invalidateCache( 'html5' );
		
		if ( $this->db->getOption('include', false, 'post_tag', 'xml') )
			$this->db->invalidateCache( 'xml' );
	}
	
	
	/**
	 * @since 1.5.2
	 * @param int $attachment_id
	 */
	public function catch_attachment_event( $attachment_id ) {
		if (! $this->db->getOption( 'images', true ) )
			return false;
		
		$attachment = get_post( $attachment_id );
		
		if ( $attachment && $attachment->post_parent && !in_array( $attachment->post_parent, $this->db->getOption( 'xml', array(), 'exclude' ) ) )
			$this->db->invalidateCache( 'xml' );
	}
	
	/**
	 *
	 * @since 1.4
	 */
	public function add_menu() {
		add_menu_page( '', 'SiteTree', 'manage_options', $this->menuId );
		
		foreach ( $this->pages as $page ) {
			if ( $page->id == $this->currentPageId ) {
				$hook_suffix = add_submenu_page( $this->menuId, $page->title, $page->menu_title, 'manage_options', $page->id, 
					array( $this->pageView, 'render' )
				);
				
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'printInitScript' ) );
			}
			else
				add_submenu_page( $this->menuId, $page->title, $page->menu_title, 'manage_options', $page->id, '__return_false' );
		}
	
	}
	
	/**
	 * Returns the plugin action links ('Settings', 'Deactivate').
	 *
	 * This method is hooked into the plugin_action_links filter hook.
	 *
	 * @since 1.4
	 *
	 * @param array $links
	 * @return array
	 */
	public function addActionLinks( $links ) {
		$links = array(
			'settings' => '<a href="' . SiteTreeUtilities::adminURL( $this->menuId ) . '">' . __( 'Settings', 'sitetree' ) . '</a>',
			'deactivate' => $links['deactivate']
		);
		
		return $links;
	}
	
	/**
	 *
	 * This method is hooded into the admin_print_scripts- action hook
	 *
	 *
	 * @since 1.4
	 */
	public function enqueueScripts() {
		$resource = $this->plugin->url( 'resources/sitetree' . $this->scriptSuffix );
		
		wp_enqueue_style( 'sitetree', ( $resource . '.css' ), null, SiteTree::VERSION );
		wp_enqueue_script( 'sitetree', ( $resource . '.js' ), array('jquery'), SiteTree::VERSION );
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 */
	public function printInitScript() {
		echo '<script>SiteTree.init("' . $this->currentPageId .'", {warnDisable:"';
		
		_e( 'Are you sure you want to disable the Google Sitemap?', 'sitetree' );
		
		if ( $this->db->getOption( 'ping', true ) )
			echo '\n\n' . __( 'All the scheduled Pings will be cancelled.', 'sitetree' );
		
		echo '",warnCancelPing:"';
		
		_e( "You are about to cancel a scheduled ping.\\n'Cancel' to stop, 'OK' to proceed.", 'sitetree' );
		
		echo '"});</script>';
	}
}
?>