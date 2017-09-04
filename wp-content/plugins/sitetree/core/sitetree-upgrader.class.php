<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 * @since 1.2
 */
final class SiteTreeUpgrader extends SiteTreeDBGatekeeper {
	/**
	 * @since 1.5
	 * @var string
	 */
	protected $namespace = 'sitetree';
	
	/**
	 * Backup object. Instance of SiteTreeDBGatekeeper.
	 *
	 * @see backup()
	 * @since 1.5
	 *
	 * @var object
	 */
	private $backup;
	
	/**
	 * @since 1.5.1
	 * @var string
	 */
	private $backupKey;
	
	/**
	 * @since 1.4
	 * @var array
	 */
	private $defaults;
	
	/**
	 * Upgraded version of the @see parent::$options array.
	 *
	 * @todo Must be removed when the upgrade support for 1.2.x will be dropped.
	 * @since 1.4
	 *
	 * @var array
	 */
	private $newOptions = array();

	/**
	 * Constructor method.
	 *
	 * @since 1.2
	 *
	 * @param array $options
	 * @param array $defaults
	 */
	public function __construct( $options, $defaults ) {
		$this->options   = $options;
		$this->defaults	 = $defaults;
		$this->backupKey = '_' . $this->namespace . '_backup';
		$this->backup	 = new SiteTreeDBGatekeeper( $this->backupKey );
	}
	
	/**
	 * @since 1.4
	 * @return array
	 */
	public function &upgrade() {
		$major_version = (float) $this->options['version'];
		
		if ( $major_version < 1.3 ) {
			$this->upgradeOptionsUpTo_1_2_2();
			
			// Here the backup() method must be called after upgradeOptionsUpTo_1_2_2()
			// to let the latter merge the two arrays of options.
			$this->backup();
			
			delete_transient( 'sitetree' );
			delete_option( 'sitetree_content' );
		}
		elseif ( $major_version < 1.4 ) {
			$this->backup();
			$this->upgrade_1_3_Options();
			
			delete_transient( 'sitetree' );
			delete_transient( 'sitetree_xml' );
			
			wp_clear_scheduled_hook( 'sitetree_rebuild' );
			wp_clear_scheduled_hook( 'sitetree_rebuild', array('xml') );
		}
		elseif ( $major_version < 1.5 ) {
			$this->backup();
			$this->upgrade_1_4_Data();
		}
		elseif ( $this->options['version'] === '1.5' )
			$this->restoreAndUpgradeExceptionsLostWith_1_5();
		else
			return $this->options;
		
		return $this->newOptions;
	}
	
	/**
	 * Makes a backup copy of the @see parent::$options array.
	 *
	 * @since 1.5
	 * @return bool True if a backup is created/updated, false otherwise.
	 */
	private function backup() {
		$new_backup					  = $this->options;
		$new_backup['backup_version'] = SiteTree::VERSION;
		
		// If there isn't a backup yet, a new one is created.
		if ( $this->backup->optionsEmpty() )
			return add_option( $this->backupKey, $new_backup, null, 'no' );
		
		// Otherwise, the existing backup is overwritten only if it was created by 
		// a version older than this version and the latter is a major version.
		//
		// This check protects the backup against a possible downgrade and makes sure 
		// that only major versions can update it.
		$backup_version = $this->backup->getOption( 'backup_version' );
		
		if ( ( strlen( SiteTree::VERSION ) == 3 ) && $backup_version && version_compare( $backup_version, SiteTree::VERSION, '<' ) )
			return update_option( $this->backupKey, $new_backup );
		
		return false;
	}
	
	/**
	 * Utility method: moves an option from one place to another within the $options array
	 * and set its value to $default if the option to move isn't found.
	 *
	 * @since 1.5
	 *
	 * @param array $old_keys
	 * @param array $new_keys
	 * @param mixed $default
	 */
	private function migrateOption( $old_keys, $new_keys, $default = false ) {
		$old_option = null;
		$old_keys	= (array) $old_keys;
		
		// First, we try to find and delete the option to move.
		// Its value is retained by $old_option
		switch ( count( $old_keys ) ) {
			case 1:
				$key = $old_keys[0];
			
				if ( isset( $this->options[$key] ) ) {
					$old_option = $this->options[$key];
					
					unset( $this->options[$key] );
				}
				break;
			case 2:
				list( $key, $group ) = $old_keys;
				
				if ( isset( $this->options[$group][$key] ) ) {
					$old_option = $this->options[$group][$key];
					
					unset( $this->options[$group][$key] );
				}
				break;
			case 3:
				list( $key, $group, $context ) = $old_keys;
				
				if ( isset( $this->options[$context][$group][$key] ) ) {
					$old_option = $this->options[$context][$group][$key];
					
					unset( $this->options[$context][$group][$key] );
				}
				break;
			default:
				return false;
		}
		
		// If not found or null, we take the $default value.
		if ( $old_option === null )
			$old_option = $default;
		
		// Finally, we add a new array element through the setter method. 
		list( $key, $group, $context ) = array_pad( (array) $new_keys, 3, '' );
		
		return $this->setOption( $key, $old_option, $group, $context );
	}
	
	/**
	 * Restores the array of excluded content from the backup and performs a new
	 * migration of the exceptions.
	 *
	 * This method patches a bug affecting version 1.5
	 * 
	 * @since 1.5.1
	 */
	private function restoreAndUpgradeExceptionsLostWith_1_5() {
		// To locate and retrieve the array(s) of excluded content from 
		// the backup, we use the 'version' key.
		$restored_version = (float) $this->backup->getOption( 'version' );
		
		if ( $restored_version < 1.3 )
			$this->restoreAndMigrateExcludedPages();
			
		elseif ( $restored_version < 1.5 )
			$this->restoreAndMigrateExcludedPostsAndPages();
		
		// Not needed in a future version.
		$this->newOptions = &$this->options;
	}
	
	/**
	 * Upgrades the options and the back-end data for version 1.4.x
	 * 
	 * @since 1.5
	 */
	private function upgrade_1_4_Data() {
		// Groups and migrates excluded posts and pages
		$this->restoreAndMigrateExcludedPostsAndPages();
		
		// Groups options to list images in the Google sitemap
		$this->options['images'] = $this->getOption( 'images', $this->defaults['images'], 'page', 'xml' ) || 
								   $this->getOption( 'images', $this->defaults['images'], 'post', 'xml' );
		
		// Init post limit option.
		$items_limit = $this->getOption( 'items_limit', $this->defaults['items_limit'] );
		
		if ( $items_limit != $this->defaults['items_limit'] );
			$this->options['html5']['post']['limit'] = $items_limit;
			
		// Init pingState object
		if ( $this->getOption( 'ping', $this->defaults['ping'] ) ) {
			$pingState = new SiteTreePingState();
			$pingState->setTime( $time = $this->getOption( 'date', 0, 'ping_status' ) );
			
			if ( $scheduledTime = $this->getOption( 'schedule', 0, 'ping_status' ) ) {
				$pingState->setCode( 'scheduled' );
				$pingState->reset( $scheduledTime );
			}
			elseif ( $time ) { $pingState->setCode( 'success' ); }
			
			$this->options['pingState'] = $pingState;
		}
		
		// Deletes deprecated options and back-end data;
		unset( $this->options['ping_status'], $this->options['xml']['page']['images'], $this->options['xml']['post']['images'] );
		
		// Not needed in a future version.
		$this->newOptions = &$this->options;
	}
	
	/**
	 * Upgrades the @see parent::$options array for version 1.3.x
	 * 
	 * @since 1.4
	 */
	private function upgrade_1_3_Options() {
		// Migrates some back-end data
		$this->migrateOption( 'first_install_date', 'instal_date', time() );
		$this->migrateOption( 'stats', 'stats_html5', array() );
		
		// Groups and migrates excluded posts and pages
		$this->restoreAndMigrateExcludedPostsAndPages();
		
		// Update max value of priority for all content types but pages and posts.
		$groups = array( 'category', 'authors', 'archives', 'post_tag' );
		
		foreach ( $groups as $group ) {
			if ( $this->getOption( 'priority', '', $group, 'xml' ) == '0.9' )
				$this->options['xml'][$group]['priority'] = '0.8';
		}
		
		// Deletes deprecated options and back-end data;
		unset( $this->options['install_date'] );
		
		// Not needed in a future version.
		$this->newOptions = &$this->options;
	}
	
	/**
	 * Upgrades the @see parent::$options array for versions between 1.1 and 1.2.2
	 * 
	 * @since 1.4
	 */
	private function upgradeOptionsUpTo_1_2_2() {
		$this->newOptions				 = $this->defaults;
		$this->newOptions['instal_date'] = time();
	
		// Renames CSS ids and classes.
		$old_css = array('.sitemap-title', '.sitemap-list', '#sitetree-credits {font-size:90%; float:right;}');
		$new_css = array('.sitetree-title', '.sitetree-list', '#sitetree-credits {font-size:90%; text-align:right;}');
			
		$this->newOptions['css_code'] = str_replace( $old_css, $new_css, $this->getOption( 'css_code', '' ) );
		
		// Renames and migrates the general options
		$dictionary = array(
			'page'			=> 'page_for_sitemap',
			'show_credits'	=> 'show_credits',
			'heading_tag'	=> 'title_tag',
			'wrapping_tag'	=> 'list_wrapper',
			'trailing_html'	=> 'trailing_html'
		);
		
		foreach ( $dictionary as $old_key => $new_key )
			$this->newOptions[$new_key] = $this->getOption( $old_key, $this->defaults[$new_key] );
		
		// Renames and migrates the content options
		//
		// The content option array is merged with the general one to let the backup() method make
		// a backup of all the options into one place.
		$this->options += (array) get_option( 'sitetree_content', array() );
		
		$dictionaries = array(
			'authors' => array(
				'old_group_id'	=> 'authors',
				'show'			=> 'include',
				'title'			=> 'title',
				'posts_count'	=> 'show_count',
				'show_avatar'	=> 'show_avatar',
				'avatar_size'	=> 'avatar_size',
				'show_descr'	=> 'show_bio',
				'blacklist'		=> 'exclude'
			),
			'page' => array(
				'old_group_id'	=> 'pages',
				'show'			=> 'include',
				'title'			=> 'title',
				'show_home'		=> 'show_home',
				'list_style'	=> 'list_style',
				'depth'			=> 'depth'
			),
			'archives' => array(
				'old_group_id'	=> 'archives',
				'show'			=> 'include',
				'title'			=> 'title',
				'posts_count'	=> 'show_count'
			),
			'categories' => array(
				'old_group_id'	=> 'categories',
				'show'			=> 'include',
				'title'			=> 'title',
				'blacklist'		=> 'exclude',
				'posts_count'	=> 'show_count',
				'feed_text'		=> 'feed_text',
				'list_style'	=> 'list_style'
			),
			'tags' => array(
				'old_group_id'	=> 'tags',
				'show'			=> 'include',
				'title'			=> 'title',
				'posts_count'	=> 'show_count'
			),
			'post' => array(
				'old_group_id'	 => 'posts',
				'show'			 => 'include',
				'title'			 => 'title',
				'groupby'		 => 'groupby',
				'category_label' => 'category_label',
				'orderby'		 => 'orderby',
				'comments_count' => 'show_comments_count',
				'show_date'		 => 'show_date'
			)
		);
		
		foreach ( $dictionaries as $id => $dictionary ) {
			$old_group_id = $dictionary['old_group_id'];
			
			unset( $dictionary['old_group_id'] );
			
			foreach ( $dictionary as $old_key => $new_key )
				$this->newOptions['html5'][$id][$new_key] = $this->getOption( $old_key, $this->defaults['html5'][$id][$new_key], $old_group_id );
		}
		
		// Converts the comma separated list of page ids into an array
		// and removes the deleted pages from the array.
		$this->restoreAndMigrateExcludedPages();
	}
	
	/**
	 * Helper method: migrates excluded pages for versions up to 1.2.2
	 *
	 * @since 1.5.1
	 */
	private function restoreAndMigrateExcludedPages() {
		$excluded_pages = $destinationArray = null;
		
		// If there isn't any backup, we regularly migrate the exceptions.
		if ( $this->backup->optionsEmpty() ) {
			$destinationArray = &$this->newOptions;
			$excluded_pages   = $this->getOption( 'blacklist', '', 'pages' );
		}
		// Otherwise, the source object must be $backup. The destination array is set accordingly.
		else {
			$excluded_pages = $this->backup->getOption( 'blacklist', '', 'pages' );
			
			// Because a backup copy esists, the destination array changes if we are upgrading from
			// version 1.5 or 1.1.x/1.2.x. In the latter case, the user downgraded after upgrading to version 1.5 ...
			if ( $this->options['version'] == '1.5' )
				$destinationArray = &$this->options;
			else {
				$destinationArray = &$this->newOptions;
				
				// … that's why we try to merge the exclusions from the backup with the ones 
				// that the user could have set after downgrading.
				$excluded_pages .= ',' . $this->getOption( 'blacklist', '', 'pages' );
			}
		}
		
		$destinationArray['exclude']['html5'] = $this->validateExcludedPosts( $excluded_pages, 'page' );
	}
	
	/**
	 * Helper method: migrates excluded posts and pages for versions between 1.3 and 1.4.3
	 *
	 * @since 1.5.1
	 */
	private function restoreAndMigrateExcludedPostsAndPages() {
		$contexts = array( 'xml', 'html5' );
		
		foreach ( $contexts as $context ) {
			$exclude = null;
			
			// If there isn't any backup, we regularly group and migrate the exceptions.
			if ( $this->backup->optionsEmpty() ) {
				$exclude = &array_merge(
					(array) $this->getOption( 'exclude', array(), 'page', $context ),
					(array) $this->getOption( 'exclude', array(), 'post', $context )
				);
			}
			// A backup exists, so we restore the data from it but only if we are upgrading from 1.5
			elseif ( $this->options['version'] == '1.5' ) {
				$exclude = &array_merge(
					(array) $this->backup->getOption( 'exclude', array(), 'page', $context ),
					(array) $this->backup->getOption( 'exclude', array(), 'post', $context )
				);
			}
			// In this case, the user downgraded after upgrading to 1.5, so, we try to merge
			// the exceptions that could have been set after the downgrade with the ones
			// restored from the backup.
			else {
				$exclude = &array_merge(
					(array) $this->getOption( 'exclude', array(), 'page', $context ),
					(array) $this->getOption( 'exclude', array(), 'post', $context ),
					(array) $this->backup->getOption( 'exclude', array(), 'page', $context ),
					(array) $this->backup->getOption( 'exclude', array(), 'post', $context )
				);
			}
			
			$this->options['exclude'][$context] = $this->validateExcludedPosts( $exclude, array( 'post', 'page' ) );
			
			unset( $this->options[$context]['page']['exclude'], $this->options[$context]['post']['exclude'] );
		}
	}
	
	/**
	 * Helper method.
	 *
	 * @since 1.5
	 * @return array
	 */
	private function validateExcludedPosts( $ids, $post_types ) {
		$valid_ids = array();
		
		if ( $ids = wp_parse_id_list( $ids ) ) {
			$wpQuery = new WP_Query(array(
				'posts_per_page' => -1,
				'post_type'		 => $post_types, 
				'post__in'		 => $ids
			));
				
			if ( $posts = &$wpQuery->get_posts() ) {
				foreach ( $posts as $post ) $valid_ids[] = $post->ID;
			}
		}
		
		return $valid_ids;
	}
}
?>