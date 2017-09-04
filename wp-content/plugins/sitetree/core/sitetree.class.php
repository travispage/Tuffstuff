<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 * Master plugin class.
 *
 * @since 1.0
 */
final class SiteTree {
	/**
	 * Set to true to disable the cache.
	 *
	 * @since 1.2.2
	 */
	const DEBUG_MODE = false;
	
	/**
	 * @since 1.1
	 */
	const VERSION = '1.5.3';
	
	/**
	 * Minimum version of SiteTree upgradeable by the this version.
	 *
	 * @since 1.4
	 */
	const MIN_VERSION = '1.1';
	
	/**
	 * @since 1.1
	 */
	const MIN_WP_VERSION = '3.3';
	
	/**
	 * @since 1.4
	 */
	const WEBSITE = 'http://sitetreeplugin.com/';
	
	/**
	 * Singleton instance.
	 *
	 * @since 1.4
	 * @var object
	 */
	private static $plugin;
	
	/**
	 * Instance of Debugger
	 *
	 * @since 1.4
	 * @var object
	 */
	private static $debugger;
	
	/**
	 * Instance of SiteTreeDB
	 *
	 * @since 1.3
	 * @var object
	 */
	private $db;
	
	/**
	 * Instance of ...
	 *
	 * @since 1.4
	 * @var object
	 */
	private $adminDelegate;
	
	/**
	 * Instance of ...
	 *
	 * @since 1.4
	 * @var object
	 */
	private $dataController;
	
	/**
	 * Instance of ...
	 *
	 * @since 1.5
	 * @var object
	 */
	private $pingController;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	private $file;
	
	/**
	 * @since 1.5
	 * @var string
	 */
	private $dirName;
	
	/**
	 * @since 1.5
	 * @var string
	 */
	private $dirPath;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	private $basename;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	private $url;
	
	/**
	 * @since 1.4
	 * @var array
	 */
	private static $websiteResources = array(
		'feedback'	 => 'contact/',
		'contribute' => 'contribute/',
		'faqs'		 => 'support/#faqs',
		'license'	 => 'license/',
		'bug_report' => 'contribute/#report-bugs',
		'l10n'		 => 'contribute/#translate',
	);
	
	/**
	 * @since 1.5
	 * @var string
	 */
	private $sitemapFilename;
	
	/**
	 *
	 *
	 * @since 1.5
	 */
	public static function launch( $loader_path ) {
		if ( self::$plugin )
			wp_die( __('Cheatin&#8217; huh?') );
			
		self::$plugin = new self( $loader_path );
		
		if ( WP_DEBUG ) {
			include( self::$plugin->dirPath . '/debugger/debugger.class.php' );
			
			self::$debugger = Debugger::init( 'SiteTree', self::$plugin->dirPath );
		}
		
		if ( version_compare( get_bloginfo( 'version' ), self::MIN_WP_VERSION, '<' ) ) {
			self::$plugin->registerFatalError(sprintf(
				__( 'To run %s you need at least WordPress %s. Please, update your WordPress installation to '
				  . 'the %slatest version%s available.', 'sitetree' ), 
				'SiteTree', self::MIN_WP_VERSION, '<a href="http://wordpress.org/download/" target="_blank">', '</a>'
			));
			
			add_action( 'plugins_loaded', array( self::$plugin, 'loadTextdomain' ) );
			
			return -1;
		}
		
		global $pagenow;
		
		switch ( $pagenow ) {
			case 'wp-cron.php':
				add_action( 'sitetree_ping', array( self::$plugin->pingController(), 'ping' ) );
				
				// During cron we load the bare minimum, so all works faster.
				return 2;
			
			case 'plugins.php':
				register_activation_hook( $loader_path, array( self::$plugin, 'activate' ) );
		}
		
		add_action( 'init', array( self::$plugin, 'finishLaunching' ) );
	}
	
	/**
	 * Returns reference to singleton instance.
	 *
	 * @since 1.4
	 * @return object
	 */
	public static function invoke() { return self::$plugin; }
	
	/**
	 *
	 *
	 * @since 1.0
	 */
	private function __construct( $loader_path ) {
		$this->file		= $loader_path;
		$this->dirPath	= dirname( $loader_path );
		$this->dirName	= basename( $this->dirPath );
		$this->basename = $this->dirName . '/' . basename( $loader_path );
		$this->url		= plugins_url( '/', $loader_path );
		
		include( $this->dirPath . '/core/sitetree-utilities.class.php' );
		include( $this->dirPath . '/core/sitetree-db.class.php' );
		include( $this->dirPath . '/core/sitetree-ping-state.class.php' );
		
		$this->db = new SiteTreeDB();
		
		register_shutdown_function( array( $this, 'shutdown' ) );
	}
	
	/**
	 * 
	 *
	 * @since 1.4
	 */
	public function __clone() { wp_die( __('Cheatin&#8217; huh?') ); }

	/**
	 * 
	 *
	 * @since 1.4
	 */
	public function __wakeup() { wp_die( __('Cheatin&#8217; huh?') ); }
	
	/**
	 * 
	 * @since 1.4
	 */
	public function finishLaunching() {
		if (! $this->upgrade() )
			return -1;
			
		if ( $this->db->xmlEnabled() )
			$this->registerRewriteRules();
		
		if (! is_admin() )
			return add_action( 'wp', array( $this, 'filter_page_request' ), 5 );
			
		include( $this->dirPath . '/admin/sitetree-admin-delegate.class.php' );
		
		$this->loadTextdomain();
		
		$pages				 = $this->dataController()->pages( $this->db->xmlEnabled(), $this->db->html5Enabled() );
		$this->adminDelegate = new SiteTreeAdminDelegate( $this, $pages );
		
		$this->adminInit();
		
		return true;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function shutdown() { $this->db->consolidate(); }
	
	/**
	 * Loads plugin textdomain.
	 *
	 * @since 1.4
	 */
	public function loadTextdomain() {
		load_plugin_textdomain( 'sitetree', false, $this->dirName . '/languages/' );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private function upgrade() {
		$current_version = $this->db->getOption( 'version' );
		
		if ( $current_version === self::VERSION )
			return 2;
		
		// The 'is_first_activation' flag is set in the callback of 'register_activation_hook()'
		// to be sure that the redirect at the end of this method fires when the plugin options have 
		// not been initialised but only after the first ever activation.
		// It's quite uself during testing, when the options array is manually deleted from the database 
		// while the plugin is running.
		$is_first_activation = $this->db->getOption( 'is_first_activation' );
		
		// Is it the first ever launch? Has the options array been corrupted?
		if ( (int) $current_version === 0 )
			$this->db->setOptions( array( 'instal_date' => time() ) );
		
		// Unsupported version?
		elseif ( version_compare( $current_version, self::MIN_VERSION, '<' ) ) {
			$this->registerFatalError( sprintf(
				__( 'The version you are upgrading from is unsupported. In order to keep your settings, please, '
				  . 'update to an older version first, then try again to upgrade to %s. %sMore information in the %sFAQs%s.', 'sitetree' ),
				'SiteTree ' . self::VERSION, '<em>', '<a href="' . self::website( 'faqs' ) . '" target="_blank">', '</a></em>'
			));
			
			return false;
		}
		
		// Otherwise, upgrade
		else {
			include( $this->dirPath . '/core/sitetree-upgrader.class.php' );
			
			$options  = $this->db->getOptions();
			$defaults = $this->dataController()->defaults();
			$upgrader = new SiteTreeUpgrader( $options, $defaults );
			
			$this->db->setOptions( $upgrader->upgrade() );
		}
		
		$this->db->setOption( 'version', self::VERSION );
		$this->db->setOption( 'last_updated', current_time( 'timestamp' ) );
		
		if ( $is_first_activation ) {
			$this->db->deleteOption( 'is_first_activation' );
			SiteTreeUtilities::adminRedirect( $this->dataController()->page( 'dashboard', false )->id );
		}
		
		return true;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 * @return bool|int
	 */
	public function registerRewriteRules() {
		global $wp;
		$wp->add_query_var( 'sitemap' );
		
		if ( !( $rules = get_option( 'rewrite_rules' ) ) )
			return false;

		$regex = '^' . $this->sitemapFilename() . '\.xml$';

		if ( isset( $rules[$regex] ) )
			return 2;

		global $wp_rewrite;
		$wp_rewrite->add_rule( $regex, 'index.php?sitemap=xml', 'top' );

		// Temporary patch: to minimise the chances of deregistering Custom Post Types
		// eventually registered by other plugins or the active theme, the flushing of 
		// the rewrite rules is done after all the actions registered into the 'init'
		// hook have been executed
		add_action( 'init', 'flush_rewrite_rules', 1000 );
		
		return true;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 * @param string $msg
	 */
	public function registerFatalError( $msg ) {
		if (! is_admin() )
			return false;
		
		add_action( 'admin_notices', array( $this, 'triggerFatalError' ) );
		
		if (! $this->db->getTransient( 'fatal_error' ) )
			$this->db->setTransient( 'fatal_error', $msg );
	}
	
	/**
	 *
	 * @since 1.4
	 */
	public function triggerFatalError() {
		$allowed_tags = array(
			'a'  => array( 'href' => true, 'target' => true ),
			'em' => array()
		);
		
		if ( $msg = $this->db->getTransient( 'fatal_error' ) ) {
			$this->db->deleteTransient( 'fatal_error' );
			
			echo '<div class="error"><p>' . wp_kses( $msg,  $allowed_tags ) . '</p></div>';
			
			// Hack: it enforces WordPress to not show the "Plugin Activated" message 
			// if the fatal error is triggered during activation.
			unset( $_GET['activate'] );
		}
		
		deactivate_plugins( $this->file, false, is_network_admin() );
	}
	
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function activate() {
		if ( $this->db->optionsEmpty() )
			$this->db->setOption( 'is_first_activation', true );
	}
	
	/**
	 * Flushes the rewrite rules and clears all the scheduled events.
	 *
	 * @since 1.5.2
	 */
	public function cleanUp() {
		global $wp_rewrite;
		
		$wp_rewrite->flush_rules();
		$this->pingController()->unschedulePing();
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function adminInit() {
		global $pagenow;
		
		add_action( 'admin_menu', array( $this->adminDelegate, 'add_menu' ) );
		
		switch ( $pagenow ) {
			case 'post.php':
			case 'post-new.php':
				include( $this->dirPath . '/admin/sitetree-metabox.class.php');
				include( $this->dirPath . '/admin/sitetree-field-view.class.php' );
				
				$metaBoxController = new SiteTreeMetaBoxController( $this );
				
				add_action( 'add_meta_boxes', array( $metaBoxController, 'register_meta_box' ) );
				add_action( 'edit_attachment', array( $this->adminDelegate, 'catch_attachment_event' ), 100 );
				add_action( 'delete_attachment', array( $this->adminDelegate, 'catch_attachment_event' ), 100 );
				
				// A priority higher then 20 sometimes causes 'process_metadata' to be discarded 
				// when the POST action is sent from 'post-new.php'
				add_action( 'save_post', array( $metaBoxController, 'process_metadata' ), 20, 2 );
				
				// Break omitted to force the actions below to be included also when in 'post.php' and 'post-new.php'
				
			case 'edit.php':
				add_action( 'trashed_post', array( $this->adminDelegate, 'catch_post_trash_event' ), 100 );
				add_action( 'untrashed_post', array( $this->adminDelegate, 'catch_post_trash_event' ), 100 );
				add_action( 'after_delete_post', array( $this->adminDelegate, 'catch_post_delete_event' ), 100 );
				break;
			
			case 'plugins.php':
				add_filter( 'plugin_action_links_' . $this->basename, array( $this->adminDelegate, 'addActionLinks' ) );
				register_deactivation_hook( $this->file, array( $this, 'cleanUp' ) );
				break;
				
			case 'edit-tags.php':
			case 'admin-ajax.php':
				if (! isset( $_REQUEST['taxonomy'] ) )
					break;
					
				$taxonomy = $_REQUEST['taxonomy'];
				
				if ( $taxonomy == 'category' || $taxonomy == 'post_tag' ) {
					add_action( 'edit_' . $taxonomy, array( $this->adminDelegate, 'catch_' . $taxonomy . '_event' ), 100 );
					add_action( 'create_' . $taxonomy, array( $this->adminDelegate, 'catch_' . $taxonomy . '_event' ), 100 );
					add_action( 'delete_' . $taxonomy, array( $this->adminDelegate, 'catch_' . $taxonomy . '_event' ), 100 );
				}
				break;
				
			case 'admin.php':
				include( $this->dirPath . '/admin/sitetree-field-view.class.php' );
				include( $this->dirPath . '/admin/sitetree-page-view.class.php' );
				include( $this->dirPath . '/admin/page-view-delegate-protocols.php' );
				include( $this->dirPath . '/admin/sitetree-page-view-controller.class.php' );
			
				if ( $_POST && isset( $_POST['option_page'] ) && $this->dataController->pageExists( $_POST['option_page'] ) ) {
					$tab = $input = null;
					
					if ( !( current_user_can('manage_options') && isset($_POST['action']) ) )
						wp_die( __('Cheatin&#8217; uh?') );
						
					if ( is_multisite() && !is_super_admin() )
						wp_die( __('Cheatin&#8217; uh?') );
						
					check_admin_referer( 'sitetree-options', '_sitetree_nonce' );
					
					if ( isset( $_POST['tab'] ) ) {
						$tab = $_POST['tab'];
						
						if (! $this->dataController->tabExists( $_POST['tab'], $_POST['option_page'] ) )
							wp_die( __( 'Request sent from an invalid tab.', 'sitetree' ) );
					}
						
					if ( isset( $_POST['sitetree'] ) ) {
						$input = &$_POST['sitetree'];
						
						if ( !( $_POST['sitetree'] && is_array( $_POST['sitetree'] ) ) )
							wp_die( __( 'No data to process.', 'sitetree' ) );
					}
					
					SiteTreePageViewController::instance( $this )->processAction( $_POST['action'], $_POST['option_page'], $input, $tab );
				}
				
				if ( $_GET && isset( $_GET['page'] ) && ( $page = $this->dataController->page( $_GET['page'] ) ) ) {
					$pageView = null;
					$pageViewController = SiteTreePageViewController::instance( $this );
					
					if ( isset( $_GET['tab'] ) ) {
						if (! $this->dataController->tabExists( $_GET['tab'], $_GET['page'] ) )
							wp_die( __( "The requested tab doesn't exists.", 'sitetree' ) );
					
						$pageViewController->setActiveTab( $_GET['tab'] );
					}
					elseif ( isset( $_GET['action'] ) ) {
						if ( ! ( isset( $_GET['_sitetree_nonce'] ) && wp_verify_nonce( $_GET['_sitetree_nonce'], $_GET['action'] ) ) )
							wp_die( __('Cheatin&#8217; uh?') );
					
						$pageViewController->processAction( $_GET['action'], $_GET['page'] );
					}
					elseif ( isset( $_GET['edit'] ) && ( $_GET['edit'] == 'config' ) )
						$pageViewController->enableConfigMode();
					
					if ( isset( $_GET['rebuilt'] ) || isset( $_GET['settings-updated'] ) )
						$pageViewController->setupErrorMessage();
					
					if ( class_exists( $page->class ) ) {
						$pageView = new $page->class( $page );
						$pageView->setDelegate( $pageViewController );
					}
					
					$this->adminDelegate->init( $pageView, $_GET['page'] );
				}
				break;
					
			case 'user-new.php':
				add_action( 'user_register', array( $this->adminDelegate, 'catch_authors_event' ), 100 );
				break;
				
			case 'user-edit.php':
			case 'profile.php':	
				add_action( 'profile_update', array( $this->adminDelegate, 'catch_authors_event' ), 100 );
				break;
				
			case 'users.php':
				add_action( 'delete_user', array( $this->adminDelegate, 'catch_authors_event' ), 100 );
				break;
		}
	}
	
	/**
	 *
	 *
	 * @since 1.3
	 */
	public function filter_page_request() {
		global $wp_query;
		
		if ( is_object( $post = $wp_query->get_queried_object() ) ) {
			if ( $post->ID === $this->db->getOption('page_for_sitemap', 0) ) {
				add_action( 'wp_head', array( $this, 'print_css' ) );
				
				// The priority of 11 let us hook into 'the_content' just after the wp_autop() function
				add_filter( 'the_content', array( $this, 'append_sitemap' ), 11 );
			}
			
			return 1;
		}
		
		if (! $this->db->xmlEnabled() )
			return false;
			
		if ( $wp_query->get( 'sitemap' ) == 'xml' ) {
			remove_filter( 'template_redirect', 'redirect_canonical' );
			add_action( 'template_redirect', array( $this, 'serveGoogleSitemap' ) );
		}
		elseif ( $wp_query->is_robots() )
			add_filter( 'robots_txt', array( $this, 'filterRobotsFile' ), 50, 2 );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param string $text
	 * @param string $site_is_public
	 * @return string
	 */
	public function filterRobotsFile( $text, $site_is_public ) {
		if ( $site_is_public === '0' ) 
			return $text;
		
		$sitetree_text = '';
			
		if ( $this->db->getOption( 'do_robots' ) && ( $ids = $this->db->getOption( 'xml', array(), 'exclude' ) ) ) {
			$wpQuery = new WP_Query(array(
				'posts_per_page' => -1,
				'post_type'		 => array( 'post', 'page' ),
				'post__in'		 => $ids, 
				'orderby'		 => 'name', 
				'order'			 => 'ASC'
			));
				
			if ( $posts = &$wpQuery->get_posts() ) {				
				$home_url	   = home_url();
				$sitetree_text = "\nUser-agent: *\n";
				
				foreach ( $posts as $post ) {
					$path			= esc_url( str_replace( $home_url, '', get_permalink( $post ) ) );
					$sitetree_text .= "Disallow: {$path}\n";
				}
			}
		}
		
		if ( $this->db->getOption( 'permalink_in_robots' ) )
			$sitetree_text .= "\nSitemap: " . $this->googleSitemapPermalink( 'raw' ) . "\n";
			
		if ( $sitetree_text )
			return $text . "\n\n# SiteTree Start{$sitetree_text}# SiteTree End\n";
		
		return $text;
	}
	
	/**
	 * Prints the css code to style the HTML5 sitemap.
	 *
	 * This method is hooked into the wp_head action hook.
	 *
	 * @since 1.3
	 */
	public function print_css() {
		$css = $this->db->getOption('css_code', '#sitetree-credits {font-size:90%; text-align:right;}');
			
		if ( $css ) echo "<style>\n" . wp_kses( $css, array() ) . "\n</style>\n";
	}
	
	/**
	 * Appends the HTML5 sitemap to the content of the page where the sitemap must be shown.
	 *
	 * This method is hooked into the the_content filter hook.
	 *
	 * @since 1.2
	 *
	 * @param string $the_content
	 * @return string
	 */
	public function append_sitemap( $the_content ) {
		if ( has_filter( 'the_content', 'wpautop' ) ) {
			$the_content = wpautop( $the_content );
			
			remove_filter( 'the_content', 'wpautop' );
		}
		
		if ( self::DEBUG_MODE || !( $sitemap = SiteTreeUtilities::ksesHTML5( $this->db->getCache( 'html5' ) ) ) )
			$sitemap = $this->rebuildHTML5Sitemap();
		
		$timestamp = $this->db->getOption( 'date', time(), 'stats_html5' );
		
		$the_content .= '<!-- Archive generated by SiteTree ' . self::VERSION  . ' on ' . gmdate( 'Y-m-d @ H:i', $timestamp );
		$the_content .= ' GMT - ' . self::WEBSITE . " -->\n";
		$the_content .= $sitemap;
		$the_content .= "<!-- Sitemap end -->\n";
		
		return $the_content;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function serveGoogleSitemap() {
		$now	   = time();
		$two_days  = 172800;
		$timestamp = (int) $this->db->getOption( 'date', $now, 'stats_xml' );
		
		// Disallow WP Super Cache from caching the sitemap
		define( 'DONOTCACHEPAGE', true );
		
		if ( !self::DEBUG_MODE && $this->db->isCacheAlive( 'xml' ) && isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) && 
			 ( strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) >= $timestamp ) )
		{
			header( 'Cache-Control: max-age=' . $two_days . ', must-revalidate' );
			header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', $now + $two_days ) . ' GMT' );
			header( 'HTTP/1.1 304 Not Modified' );
			exit;
		}
		
		if ( self::DEBUG_MODE || !( $sitemap = SiteTreeUtilities::ksesXML( $this->db->getCache( 'xml' ) ) ) ) {
			$sitemap   = $this->rebuildGoogleSitemap();
			$timestamp = (int) $this->db->getOption( 'date', $now, 'stats_xml' );
		}
		
		$output  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$output .= '<?xml-stylesheet type="text/xsl" href="' . $this->url . 'resources/template.xsl"?>' . "\n";
		$output .= '<!-- Sitemap generated by SiteTree ' . self::VERSION  . ' on ' . gmdate( 'Y-m-d @ H:i', $timestamp );
		$output .= ' GMT - ' . self::WEBSITE . " -->\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
		
		if ( $this->db->getOption( 'is_image_sitemap', true ) ) 
			$output .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
			
		$output .= '>' . $sitemap . '</urlset>';
		
		header( 'Cache-Control: max-age=' . $two_days . ', must-revalidate' );
		header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', $now + $two_days ) . ' GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $timestamp ) . ' GMT' );
		header( 'Content-type: application/xml; charset=UTF-8' );
		
		exit( $output );
	}
	
	/**
	 * 
	 * @since 1.4
	 * @return string
	 */
	public function &rebuildGoogleSitemap() {
		include( $this->dirPath . '/core/sitetree-factory.class.php' );
		include( $this->dirPath . '/core/sitetree-xml-factory.class.php' );
	
		$memory_limit = (int) @ini_get( 'memory_limit' );
		$limit_target = is_multisite() ? 128 : 64;
		
		if ( $reset_mem_limit = ( $memory_limit < $limit_target ) )
			@ini_set( 'memory_limit', $limit_target . 'M' );
		
		$factory = new SiteTreeXMLFactory( $this->db );
		$sitemap = $factory->getSitemap();
		
		$this->db->setOption( 'is_image_sitemap', $factory->isImageSitemap() );
		$this->db->setOption( 'stats_xml', $factory->getStats() );
		$this->db->setCache( 'xml', $sitemap );
			
		if ( $reset_mem_limit )
			@ini_set( 'memory_limit', $memory_limit . 'M' );
		
		return $sitemap;
	}
	
	/**
	 * 
	 * @since 1.4
	 * @return string
	 */
	public function &rebuildHTML5Sitemap() {
		include( $this->dirPath . '/core/sitetree-factory.class.php' );
		include( $this->dirPath . '/core/sitetree-html5-factory.class.php' );
		
		$page_id = (int) $this->db->getOption( 'page_for_sitemap' );
	
		// We don't use 'wp_update_post' because it would generate a new revision.
		global $wpdb;
		$query_str = $wpdb->prepare(
			"UPDATE {$wpdb->posts} SET `post_modified` = %s, `post_modified_gmt` = %s WHERE `ID` = %d",
			current_time('mysql'), current_time('mysql', 1), $page_id
		);
		
		$wpdb->query( $query_str );
		
		$factory = new SiteTreeHTML5Factory( $this->db );
		$sitemap = $factory->getSitemap();
		
		$this->db->setOption( 'stats_html5', $factory->getStats() );
		$this->db->setCache( 'html5', $sitemap );
			
		// Try to force WP Super Cache to flush the cached version of the page where the html5 sitemap is shown
		if ( WP_CACHE && function_exists('wp_cache_post_change') ) {
			if ( $page_id !== 0 ) {
				global $super_cache_enabled;
				$super_cache_enabled = 1;
				
				wp_cache_post_change( $page_id );
			}
		}
		
		return $sitemap;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 * @return string
	 */
	public function googleSitemapPermalink( $context = 'display' ) {
		$url = '';
		
		if ( get_option( 'permalink_structure' ) )
			$url = home_url( '/' . $this->sitemapFilename() . '.xml' );
		else
			$url = add_query_arg( 'sitemap', 'xml', home_url('/') );
			
		return esc_url( $url, null, $context );
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 * @return string
	 */
	private function sitemapFilename() {
		if (! $this->sitemapFilename) {
			$this->sitemapFilename = $this->db->getOption( 'filename', 'sitemap' );
			
			if ( $this->sitemapFilename != 'sitemap' ) {
				$this->sitemapFilename = preg_replace( '/[^a-z0-9\-]/', '', $this->sitemapFilename );
				
				if (! $this->sitemapFilename )
					$this->sitemapFilename = 'sitemap';
			}
		}
		
		return $this->sitemapFilename;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 * @return string
	 */
	public function basename() { return $this->basename; }
	
	/**
	 *
	 *
	 * @since 1.4
	 * @return string
	 */
	public function url( $relative_path = '' ) { return $this->url . $relative_path; }
	
	/**
	 *
	 *
	 * @since 1.4
	 * @return object
	 */
	public function db() { return $this->db; }
	
	/**
	 *
	 *
	 * @since 1.4
	 * @return object
	 */
	public function dataController() {
		if (! $this->dataController ) {
			include( $this->dirPath . '/data-model/data-model-classes.php' );
		
			$this->dataController = new SiteTreeDataController();
		}
		
		return $this->dataController;
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 * @return object
	 */
	public function pingController() {
		if (! $this->pingController ) {
			include( $this->dirPath . '/core/sitetree-ping-controller.class.php' );
		
			$this->pingController = new SiteTreePingController( $this );
		}
		
		return $this->pingController;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 * @return string
	 */
	public static function website( $resource = '',  $relative_url = '' ) {
		$url = self::WEBSITE;
	
		if ( $resource ) {
			if ( isset( self::$websiteResources[$resource] ) )
				$url .= self::$websiteResources[$resource];
			else
				$url .= $resource;
		}
			
		return $url . $relative_url;
	}
	
	/**
	 * @since 1.3
	 *
	 * @param mixed $data,... Unlimited optional number of data to debug
	 * @return bool|int
	 */
	public static function debug() {
		if ( self::$debugger )
			return call_user_func_array( array( self::$debugger, 'debug' ), func_get_args() );
			
		return -1;
	}
	
	/**
	 * @since 1.4
	 *
	 * @param string $msg
	 * @return bool|int
	 */
	public static function log( $msg = '' ) {
		if ( self::$debugger )
			return self::$debugger->log( $msg );
			
		return -1;
	}
}
?>