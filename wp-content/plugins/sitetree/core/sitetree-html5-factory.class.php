<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 *
 *
 * @since 1.3
 */
class SiteTreeHTML5Factory extends SiteTreeFactory {
	/**
	 * @see SiteTreeFactory::$sitemap_type
	 * @since 1.3
	 * @var string
	 */
	protected $sitemap_type = 'html5';
	
	/**
	 * Initialises some dynamic properties.
	 *
	 * This method hooks into the main loop.
	 *
	 * @see SiteTreeFactory::get_sitemap()
	 * @since 1.3
	 */
	protected function loop_start() {
		$this->title_tag	 = $this->db->getOption( 'title_tag', 'h3' );
		$this->wrapper		 = $this->db->getOption( 'list_wrapper' );
		$this->trailing_html = $this->db->getOption( 'trailing_html' );
		$this->limit		 = (int) $this->db->getOption( 'items_limit', 200 );
	}
	
	/**
	 * Manipulates a list generated from one of the callbacks according to the user settings.
	 *
	 * This method hooks into the main loop.
	 *
	 * @see SiteTreeFactory::get_sitemap()
	 * @since 1.3
	 */
	protected function loop_inner( $list_id, &$list ) {
		if ( $title = $this->get_info('title') )
			$title = "<" . $this->title_tag . ' class="sitetree-title">' . $title . '</' . $this->title_tag . ">\n";
		
		$list =  $title . '<ul class="sitetree-list">' . "\n" . $list . "</ul>\n" . $this->trailing_html;
		
		if ( $this->wrapper ) 
			$list = "<" . $this->wrapper . ' id="sitetree-' . $list_id . '">' . "\n" . $list . '</' . $this->wrapper . ">\n";
			
		$this->sitemap .= $list;
	}
	
	/**
	 * Appends some credits to the sitemap.
	 *
	 * This method hooks into the main loop.
	 *
	 * @see SiteTreeFactory::get_sitemap()
	 * @since 1.3
	 */
	protected function loop_end() {
		if ( $this->db->getOption('show_credits') ) {
			$this->sitemap .= '<p id="sitetree-credits">Powered by <a href="' . SiteTree::WEBSITE 
							. '" target="_blank" title="A Sitemap Tool for WordPress">SiteTree</a>'
							. "</p>\n";
		}
	}
	
	/**
	 * Returns the list of pages.
	 *
	 * @since 1.3
	 * @return string
	 */
	protected function &pages() {
		$output = '';
		
		if ( $this->get_info('show_home') ) {
			$this->stats['url_count']++;
			$output .= '<li><a href="' . esc_url( home_url('/') ) . '" rel="bookmark" title="' . __( 'Home', 'sitetree' ) 
					 . '">' . __( 'Home', 'sitetree' ) . '</a></li>';
		}
		
		if ( ( $limit = $this->getLimit() ) == 0 )
			return $output;
		
		$args = array(
	        'depth'			=> (bool) $this->get_info('list_style', true) ? (int) $this->get_info('depth') : -1,
	        'sort_column'	=> 'menu_order, post_title',
	        'hierarchical'	=> 0,
	        'number'		=> $limit
        );
        
        if ( ( $exclude = $this->db->getOption( $this->sitemap_type, array(), 'exclude' ) ) && is_array( $exclude ) )
			$args['exclude'] = &$exclude;
		
		// This prevents the double inclusion of the home page link
		if ( $front_page = (int) get_option('page_on_front') )
			$args['exclude'][] = $front_page;
			
		if ( $pages = &get_pages($args) ) {
        	$this->stats['url_count'] += count( $pages );
        	
        	$output .= call_user_func_array(
				array(new SiteTreePageWalker(), 'walk'),
				array(&$pages, $args['depth'], $args)
			);
		}
		
		return $output;
	}
	
	/**
	 * Returns the list of categories.
	 *
	 * @since 1.3
	 * @return string
	 */
	protected function &categories() {
		$output = '';
		$args = array(
			'show_count'	=> (bool) $this->get_info('posts_count', true),
			'feed'			=> $this->get_info('feed_text', 'RSS'),
			'exclude'		=> $this->get_info('exclude'),
			'hierarchical'	=> (bool) $this->get_info('list_style', true),
			'depth'			=> -1,
			'orderby'		=> $this->get_info('orderby', 'name'),
			'number'		=> $this->getLimit()
		);
		
		if ($args['orderby'] == 'count')
			$args['order'] = 'DESC';
		
		if ($args['hierarchical']) {
	        $args['exclude_tree'] = $args['exclude'];
	        $args['exclude'] = '';
	        $args['depth'] = 0;
		}
		
		if ($categories = &get_terms('category', $args)) {
			$this->stats['url_count'] += count($categories);
			
			$output = call_user_func_array(
	    		array(new SiteTreeCategoryWalker(), 'walk'),
	    		array(&$categories, $args['depth'], $args)
	    	);
		}
		
		return $output;
	}
	
	/**
	 * Returns the list of tags.
	 *
	 * @since 1.3
	 * @return string
	 */
	protected function &tags() {
		$output = '';
		$args = array( 'number' => $this->getLimit(), 'exclude' => $this->get_info( 'exclude', array() ) );
		
		if ($this->get_info('orderby', 'name') != 'name') {
			$args['orderby'] = $this->get_info('orderby');
			$args['order']   = 'DESC';
		}
		
		if ($tags = &get_terms('post_tag', $args)) {
			$show_count = $this->get_info('show_count');
			
			foreach ($tags as $tag) {
				$this->stats['url_count']++;
				
				$output .= '<li><a href="' . esc_url(get_term_link($tag)) . '" rel="tag" title="';
				$output .= sprintf( __('View all posts tagged %s', 'sitetree'), SiteTreeUtilities::escTitleAttr($tag->name) );
				$output .= '">' . esc_attr( $tag->name ) . '</a>';
						    
				if ( $show_count && ( (int)$tag->count > 1 ) )
					$output .= ' <span class="sitetree-posts-number">(' . $tag->count . ')</span>';
				
				$output .= "</li>\n";
			}
		}
		
		return $output;
	}
	
	/**
	 * Returns the list of authors.
	 *
	 * @since 1.3
	 * @return string
	 */
	protected function &authors() {
		global $wpdb;
		
		$output = $orderby = $order = '';
		$fields = 'u.ID, COUNT(u.ID) AS `posts_count`, u.user_nicename, u.display_name';
		$join	= "INNER JOIN {$wpdb->posts} AS p ON p.post_author = u.ID";
		$where	= "WHERE p.post_type = 'post' AND p.post_status = 'publish'";
		$limit	= $this->getLimit();
		
		if ( $show_avatar = $this->get_info('show_avatar') )
			$fields .= ', u.user_email';
			
		if ( $show_bio = $this->get_info('show_bio') ) {
			$fields .= ', um.meta_value AS `bio_info`';
			$join .= " LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = u.ID";
			$where .= " AND um.meta_key = 'description'";
		}
		
		if ($this->get_info('orderby') == 'posts_count') {
			$order = 'DESC';
			$orderby = '`posts_count`';
		}
		else {
			$order = 'ASC';
			$orderby = 'u.display_name';
		}
		
		$query = "SELECT {$fields} FROM {$wpdb->users} AS u {$join} {$where} GROUP BY u.ID ORDER BY {$orderby} {$order} LIMIT {$limit}";
		$authors = $wpdb->get_results($query);

		if ($authors) {
			$show_count = $this->get_info('show_count');
			$avatar_size = (int) $this->get_info('avatar_size', 60);
			$exclude = $this->get_info('exclude', array());
		
			foreach ( $authors as $author ) {
				if ( strpos($exclude, $author->display_name) !== false ) continue;
				
				$this->stats['url_count']++;
				
				$tmp = '<a href="' . esc_url(get_author_posts_url($author->ID, $author->user_nicename)) . '" title="';
				$tmp .= sprintf(__('View all posts by %s', 'sitetree'), SiteTreeUtilities::escTitleAttr($author->display_name));
				$tmp .= '">' . esc_attr($author->display_name) . '</a>';
				
				if ( $show_count )
					$tmp .= ' <span class="sitetree-posts-number">(' . (int)$author->posts_count . ')</span>';
				
				if ( $show_bio && $author->bio_info )
				   $tmp .= '<p>' . $author->bio_info . '</p>';
				
				if ( $show_avatar && ( $avatar = get_avatar($author->user_email, $avatar_size, null, $author->display_name) ) )
					$tmp = $avatar . '<div class="sitetree-author-info">' . $tmp . '</div>';
				
				$output .= '<li>' . $tmp . "</li>\n";
			}
		}
		
		return $output;
	}
	
	/**
	 * Returns the list of archives.
	 *
	 * @since 1.3
	 * @return string
	 */
	protected function &archives() {
		global $wpdb, $wp_locale;
		
		$output = '';
		$archives = $wpdb->get_results(
			"SELECT COUNT(ID) AS `posts_count`, YEAR(post_date) AS `year`, MONTH(post_date) AS `month`
			 FROM {$wpdb->posts}
			 WHERE post_type = 'post' AND post_status = 'publish'
			 GROUP BY `year`, `month`
			 ORDER BY post_date DESC
			 LIMIT " . $this->getLimit()
		);
		
		if ( $archives ) {
			$show_count = $this->get_info('show_count');
			
			foreach ( $archives as $archive ) {
				$this->stats['url_count']++;
				
				$title = sprintf(__('%1$s %2$d'), $wp_locale->get_month($archive->month), $archive->year);
				$output .= '<li><a href="' . esc_url(get_month_link($archive->year, $archive->month))
					   . '" title="' . sprintf(__('View all posts published on %s', 'sitetree'), SiteTreeUtilities::escTitleAttr($title)) 
					   . '">' . esc_attr($title) . '</a>';
					   
				if ( $show_count )
					$output .= ' <span class="sitetree-posts-number">(' . (int) $archive->posts_count . ')</span>';
				
				$output .= "</li>\n";
			}
		}
	
		return $output;
	}
	
	/**
	 * Returns the list of posts.
	 *
	 * @since 1.3
	 * @return string
	 */
	protected function &posts() {
		global $wpdb;
		
		$hierarchical = true;
		$property = 'ID';
		$output = $join = $groupby = '';
		
		// p.post_author and p.post_content fields are retrieved to prevent breaking the Recent Posts Widget or others of the same kind
		$fields = 'p.ID, p.post_author, p.post_date, p.post_content, p.post_title, p.post_status, p.post_name, p.post_type';
		$where = "WHERE p.post_type = 'post' AND p.post_status = 'publish' AND p.post_password = ''";
		$orderby_field = $this->get_info('orderby', 'post_date');
		$orderby = 'p.' . $orderby_field;
		$order = ( $orderby_field == 'post_title' ) ? 'ASC' : 'DESC';
		$limit = abs( $this->get_info( 'limit', 200 ) );
		
		if ( $limit > 0 )
			$limit = min( $this->getLimit(), $limit );
		else
			$limit = $this->getLimit();
		
		if ( $show_comments_count = $this->get_info('show_comments_count') )
			$fields .= ', p.comment_count';
		
		if ( $exclude = $this->db->getOption( $this->sitemap_type, array(), 'exclude' ) )
			$where .= ' AND p.ID NOT IN (' . implode( ',', wp_parse_id_list($exclude) ) . ')';
		
		switch ( $this->get_info('groupby', 'none') ) {
			case 'date':
				$property = 'post_month';
				$fields .= ', MONTH(p.post_date) AS `post_month`';
				$orderby = 'p.post_date';
				break;
			case 'category':
				$property = 'term_id';
				$fields .= ', t.term_id, t.slug AS `category_slug`, t.name AS `category`';
				$join = "INNER JOIN {$wpdb->term_relationships} AS tr ON tr.object_id = p.ID
					  	 CROSS JOIN {$wpdb->term_taxonomy} AS tt USING(term_taxonomy_id)
						 CROSS JOIN {$wpdb->terms} AS t USING(term_id)";
				$where .= " AND tt.taxonomy = 'category'";
				$groupby = 'GROUP BY p.ID';
				$orderby = '`category`, ' . $orderby;
				break;
			case 'author':
				$property = 'post_author';
				$fields .= ', p.post_author, u.user_nicename AS `author_nicename`, u.display_name AS `author_name`';
				$join = "INNER JOIN {$wpdb->users} AS u ON p.post_author = u.ID";
				$orderby = 'u.display_name, ' . $orderby;
				break;
			default:
				$hierarchical = false;
				break;
		}
		
		$query = "SELECT {$fields} FROM {$wpdb->posts} AS p {$join} {$where} {$groupby} ORDER BY {$orderby} {$order} LIMIT {$limit}";
		$posts = $wpdb->get_results($query);
		
		if ( $posts ) {
			$current_value = null;
			$date = $day = '';
			$groupby = $this->get_info('groupby');
			$show_date = $this->get_info('show_date');
			
			foreach ( $posts as $post ) {
				if ( $hierarchical ) {
					// Make sure that the list doesn't finish with an open child list.
					if ( $this->getLimit() < 2 ) break;
					
					if ( $post->{$property} != $current_value ) {
						$this->stats['url_count']++;
						
						$current_value = $post->{$property};
						$method_name   = 'orderby_' . $groupby;
						$output		  .= "</ul>\n</li>\n" . $this->{$method_name}($post) . '<ul class="children">' . "\n";
					}
				}
				
				if ( $show_date ) {
					if ( $groupby == 'date' )
						$day = '<time datetime="' . SiteTreeUtilities::mysqlToDate($post->post_date, 'Y-m-d') 
							 . '">' . SiteTreeUtilities::mysqlToDate($post->post_date, 'd') . ':</time> ';
					else
						$date = ' <time datetime="' . SiteTreeUtilities::mysqlToDate($post->post_date, 'Y-m-d')
							  . '">' . SiteTreeUtilities::mysqlToDate($post->post_date) . '</time>';
				}
				
				$title = get_the_title($post);
				$output .= '<li>' . $day . '<a href="' . esc_url( get_permalink($post) )
						 . '" rel="bookmark" title="' . SiteTreeUtilities::escTitleAttr($title) . '">' . $title . '</a>';
						
				if ( $show_comments_count && $post->comment_count )
					$output .= ' <span class="sitetree-comments-number">(' . (int) $post->comment_count . ')</span>';
						   
				$output .= $date . "</li>\n";
				
				$this->stats['url_count']++;
			}
			
			if ( $hierarchical && $output )
				$output = substr( $output, 12 ) . "</ul>\n</li>\n";
		}
		
		return $output;
	}
	
	/**
	 *
	 * @since 1.3
	 *
	 * @param $post object
	 * @return string
	 */
	private function orderby_date($post) {
		$date = SiteTreeUtilities::mysqlToDate($post->post_date, 'F Y');
		$link = get_month_link(SiteTreeUtilities::mysqlToDate($post->post_date, 'Y'), SiteTreeUtilities::mysqlToDate($post->post_date, 'm'));
		
		return '<li><a href="' . esc_url($link) . '" title="' . $date . '">' . $date . "</a>\n";;
	}
	
	/**
	 *
	 * @since 1.3
	 *
	 * @param $post object
	 * @return string
	 */
	private function orderby_category($post) {
		// Make a term object
		$term = new stdClass();
		$term->term_id = $post->term_id;
		$term->name = $post->category;
		$term->slug = $post->category_slug;
		$term->taxonomy = 'category';
		
		// Update the term cache to save a lot of queries that would be performed when we call get_term_link()
		wp_cache_add($term->term_id, $term, 'category');
		
		if ( $label = $this->get_info('category_label') )
			$label .= ' ';
	
		$output = '<li>' . $label .'<a href="' . esc_url(get_term_link($term)) . '" title="';
		$output .= sprintf(__('View all posts filed under %s', 'sitetree'), SiteTreeUtilities::mysqlToDate($term->name));
		$output .= '">' . esc_attr($term->name) . "</a>\n";
		
		return $output;
	}
	
	/**
	 *
	 * @since 1.3
	 *
	 * @param $post object
	 * @return string
	 */
	private function orderby_author($post) {
		$output = '<li><a href="' . esc_url(get_author_posts_url($post->post_author, $post->author_nicename)) . '" title="';
		$output .= sprintf(__('View all posts by %s', 'sitetree'), SiteTreeUtilities::mysqlToDate($post->author_name));
		$output .= '">' . esc_attr($post->author_name) . "</a>\n";
		
		return $output;
	}
}


/**
 * Create HTML list of pages. 
 *
 * @since 1.3
 */
class SiteTreePageWalker extends Walker {
	/**
	 * @see Walker::$tree_type
	 * @since 1.3
	 * @var string
	 */
	public $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @since 1.3
	 * @todo Decouple this.
	 * @var array
	 */
	public $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

	/**
	 * @see Walker::start_lvl()
	 * @since 1.3
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl(&$output, $depth = 0, $args = array()) {
		$output .= "\n<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 * @since 1.3
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function end_lvl(&$output, $depth = 0, $args = array()) {
		$output .= "</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 * @since 1.3
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object.
	 * @param int $depth Depth of page. Used for padding.
	 * @param int $current_page Page ID.
	 * @param array $args
	 */
	function start_el(&$output, $page, $depth, $args, $current_page = 0) {
		$output .= '<li><a href="' . esc_url(get_permalink($page->ID)) . '" rel="bookmark" title="' . SiteTreeUtilities::escTitleAttr($page->post_title)
				 . '">' . apply_filters('the_title', $page->post_title, $page->ID) . '</a>';
	}

	/**
	 * @see Walker::end_el()
	 * @since 1.3
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 */
	function end_el(&$output, $page, $depth = 0, $args = array()) {
		$output .= "</li>\n";
	}
}


/**
 * Create HTML list of categories.
 *
 * @package WordPress
 * @since 1.3
 * @uses Walker
 */
class SiteTreeCategoryWalker extends Walker {
        /**
         * @see Walker::$tree_type
         * @since 1.3
         * @var string
         */
        public $tree_type = 'category';

        /**
         * @see Walker::$db_fields
         * @since 1.3
         * @todo Decouple this
         * @var array
         */
        public $db_fields = array ('parent' => 'parent', 'id' => 'term_id');

        /**
         * @see Walker::start_lvl()
         * @since 1.3
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param int $depth Depth of category. Used for tab indentation.
         * @param array $args Will only append content if style argument value is 'list'.
         */
        function start_lvl( &$output, $depth = 0, $args = array() ) {
        	$output .= "<ul class='children'>\n";
        }

        /**
         * @see Walker::end_lvl()
         * @since 1.3
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param int $depth Depth of category. Used for tab indentation.
         * @param array $args Will only append content if style argument value is 'list'.
         */
        function end_lvl( &$output, $depth = 0, $args = array() ) {
                $output .= "</ul>\n";
        }

        /**
         * @see Walker::start_el()
         * @since 1.3
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param object $category Category data object.
         * @param int $depth Depth of category in reference to parents.
         * @param array $args
         */
        function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        	$output .= '<li><a href="' . esc_url(get_term_link($category)) . '" title="';
			
			if ( $category->description )
				$output .= esc_attr($category->description);
			else
				$output .= sprintf(__('View all posts filed under %s', 'sitetree'), SiteTreeUtilities::escTitleAttr($category->name));
			
			$output .= '">' . esc_attr( $category->name ) . '</a>';
			
			if ( $args['feed'] )
				$output .= ' (<a href="' . esc_url(get_term_feed_link($category->term_id)) 
						 . '" title="' . SiteTreeUtilities::escTitleAttr($args['feed'])
						 . '">' . esc_attr($args['feed']) . '</a>)';
			
			if ( $args['show_count'] )
				$output .= ' <span class="sitetree-posts-number">(' . (int) $category->count . ')</span>';
        }

        /**
         * @see Walker::end_el()
         * @since 1.3
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param object $page Not used.
         * @param int $depth Depth of category. Not used.
         * @param array $args Only uses 'list' for whether should append to output.
         */
        function end_el( &$output, $page, $depth = 0, $args = array() ) {
               $output .= "</li>\n";
        }

}
?>