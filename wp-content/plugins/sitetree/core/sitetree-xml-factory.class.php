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
class SiteTreeXMLFactory extends SiteTreeFactory {
	/**
	 * @see SiteTreeFactory::$sitemap_type
	 * @since 1.3
	 * @var string
	 */
	protected $sitemap_type = 'xml';
	
	/**
	 *
	 * @since 1.4
	 * @var int
	 */
	protected $limit = 10000;

	/**
	 * The timezone offset expressed in hours (ex. +02:00)
	 *
	 * @since 1.4
	 * @var string
	 */
	private $timezoneOffset;
	
	/**
	 *
	 *
	 * @since 1.4
	 * @var bool
	 */
	private $isImageSitemap = false;
	
	/**
	 * 
	 *
	 * @since 1.4
	 * @var int
	 */
	private $pageOnFront;
	
	/**
	 * 
	 *
	 * @since 1.4
	 * @var int
	 */
	private $pageForPosts = -1;
	
	/**
	 * 
	 *
	 * @since 1.4
	 * @var object
	 */
	private $home;
	
	/**
	 * 
	 *
	 * @since 1.4
	 * @var object
	 */
	private $blog;
	
	/**
	 *
	 *
	 * @since 1.4
	 * @var string
	 */
	private $content = '';
	
	/**
	 *
	 *
	 * @since 1.4.2
	 * @var string
	 */
	private $siteCharset;
	
	/**
	 *
	 *
	 * @since 1.4.1
	 * @var string
	 */
	private $lineBreak = '';
	
	/**
	 *
	 *
	 * @since 1.5
	 * @var array
	 */
	private $allowedChangeFreqs = array(
		'hourly' => true, 'daily'	=> true, 
		'weekly' => true, 'monthly' => true,
		'yearly' => true, 'always'	=> true,
		'never'	 => true
	);
	
	/**
	 *
	 *
	 * @since 1.5
	 * @var array
	 */
	private $allowedPriorities = array(
		'0.0' => true, '0.1' => true, '0.2' => true,
		'0.3' => true, '0.4' => true, '0.5' => true,
		'0.6' => true, '0.7' => true, '0.8' => true,
		'0.9' => true, '1.0' => true
	);
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function isImageSitemap() { return $this->isImageSitemap; }
	
	/**
	 * Adds the home page url to the sitemap.
	 *
	 * This method hooks into the main loop.
	 *
	 * @see SiteTreeFactory::get_sitemap()
	 * @since 1.3
	 */
	protected function loop_start() {
		$this->pageOnFront	 	   = (int) get_option( 'page_on_front' );
		$this->timezoneOffset	   = sprintf( '%+03d:00', get_option( 'gmt_offset' ) );
		$this->siteCharset	 	   = get_bloginfo( 'charset' );
		$this->stats['num_images'] = 0;
		
		if ( WP_DEBUG || SiteTree::DEBUG_MODE )
			$this->lineBreak = "\n";
	}
	
	/**
	 *
	 *
	 * @since 1.3
	 */
	protected function loop_end() {
		if ( $this->pageOnFront ) {
			$def_changefreq = $this->db->getOption('home_changefreq', 'daily');
			
			if (! $this->home )
				$this->home = get_post( $this->pageOnFront );
			
			$this->sitemap .= $this->makeUrlItem(
				home_url('/'),
				$this->home->post_modified,
				$this->db->getPostMeta( $this->home->ID, 'changefreq', $def_changefreq ),
				$this->db->getPostMeta( $this->home->ID, 'priority', '1.0' ),
				$this->home->images
			);
		}
		else {
			$this->sitemap .= $this->makeUrlItem( home_url('/'), get_lastpostmodified('blog'), $this->db->getOption('home_changefreq', 'daily'), '1.0' );
		}
		
		if ( $this->blog ) {
			$this->sitemap .= $this->makeUrlItem(
				get_permalink( $this->blog ),
				$this->blog->post_modified,
				$this->db->getPostMeta( $this->blog->ID, 'changefreq', $this->blog->changefreq ),
				$this->db->getPostMeta( $this->blog->ID, 'priority', $this->blog->priority ),
				$this->blog->images
			);
		}
		
		$this->sitemap .= $this->content;
	}
	
	/**
	 *
	 * @since 1.4
	 */
	private function addUrlItem( $url, $lastmod, $chfreq, $prio, $images = null ) {
		$this->content .= $this->makeUrlItem( $url, $lastmod, $chfreq, $prio, $images );
	}
	
	/**
	 * Returns an URL item.
	 *
	 * @since 1.4
	 * @return string
	 */
	private function makeUrlItem( $url, $lastmod, $chfreq, $prio, &$images = null ) {
		$this->stats['url_count']++;
		
		$url_item = '<url>' . $this->lineBreak . '<loc>' . esc_url( $url ) . '</loc>' . $this->lineBreak;
		
		if ( $lastmod && ( $timestamp = strtotime($lastmod) ) )
			$url_item .= '<lastmod>' . gmdate( 'Y-m-d\TH:i:s', $timestamp ) . $this->timezoneOffset 
					   . '</lastmod>' . $this->lineBreak;
		
		if ( $chfreq && isset( $this->allowedChangeFreqs[$chfreq] ) )
			$url_item .= '<changefreq>' . $chfreq . '</changefreq>' . $this->lineBreak;
		
		if ( $prio && isset( $this->allowedPriorities[$prio] ) )
			$url_item .= '<priority>' . $prio . '</priority>' . $this->lineBreak;
			
		if ( $images ) {
			$limit = 1000;
			
			foreach ( $images as $image ) {
				$this->stats['num_images']++;
			
				$url_item .= '<image:image>' . $this->lineBreak . '<image:loc>';
				$url_item .= esc_url( wp_get_attachment_url( $image->ID ) );
				$url_item .= '</image:loc>'. $this->lineBreak;
				
				// Title Node
				if ( $image->post_title )
					$url_item .= '<image:title>' . $this->encode( $image->post_title, 70 ) . '</image:title>' . $this->lineBreak;
				
				// Caption Node
				if ( $image->post_excerpt )
					$url_item .= $this->makeCaptionNode( $image->post_excerpt );
				
				elseif ( $image->post_content )
					$url_item .= $this->makeCaptionNode( $image->post_content );
					
				elseif ( $alt_title = $this->db->getPostMeta( $image->ID, '_wp_attachment_image_alt' ) )
					$url_item .= $this->makeCaptionNode( $alt_title );
					
				// Max 1000 images per URL item.
				$url_item .= '</image:image>' . $this->lineBreak;
				
				if ( --$limit < 1 ) break;
			}
		
		}
			
		$url_item .= '</url>' . $this->lineBreak;
		
		return $url_item;
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 * @param string $text
	 * @return string
	 */
	private function makeCaptionNode( $text ) {
		return '<image:caption>' . $this->encode( $text ) . '</image:caption>' . $this->lineBreak;
	}
	
	/**
	 * Adds posts and pages to the sitemap.
	 *
	 * @since 1.3
	 */
	protected function posts() {
		global $wpdb;
		
		$post_type = $this->get_info('content_type');
		$post_type_is_page = ( $post_type == 'page' );
		$post_type_is_post = ( $post_type == 'post' );
		
		if ( !( $post_type_is_post || $post_type_is_page ) )
			return false;
		
		$post_ids	 = $post_attachments = array();
		$post_not_in = '';
		$limit		 = $this->getLimit();
		$exclude	 = $this->db->getOption( $this->sitemap_type, array(), 'exclude' );
		
		if ( $exclude && ( $ids = wp_parse_id_list( $exclude ) ) )
			$post_not_in = 'ID NOT IN (' . implode( ',', $ids ) . ') AND';
		
		if ( $post_type_is_page )
			$this->pageForPosts = (int) get_option('page_for_posts');
		
		// We retrieve a few more fields than needed to make get_permalink() work properly
		$posts = $wpdb->get_results(
			"SELECT ID, post_date, post_status, post_name, post_modified, post_parent, post_type
			 FROM {$wpdb->posts}
			 WHERE {$post_not_in} post_type = '{$post_type}' AND post_status = 'publish' AND post_password = ''
			 ORDER BY post_modified DESC
			 LIMIT {$limit}"
		);
		
		if (! $posts )
			return false;
		
		foreach ( $posts as $index => $post ) {
			$post = sanitize_post( $post, 'raw' );
			$post_ids[] = $post->ID;
		}
		
		if ( $post_type_is_post && $this->blog )
			$this->blog->post_modified = $posts[0]->post_modified;
			
		if ( $this->db->getOption( 'images', true ) ) {
			$ids = implode( ',', $post_ids );
			$attachments = $wpdb->get_results(
				"SELECT ID, post_title, post_content, post_excerpt, post_parent, post_type
				 FROM {$wpdb->posts}
				 WHERE post_parent IN ({$ids}) AND post_type = 'attachment' AND post_mime_type LIKE 'image/%'
				 ORDER BY post_modified DESC"
			);
			
			if ( $attachments ) {
				$this->isImageSitemap = true;
				
				foreach ( $attachments as $attachment ) {
					$attachment = sanitize_post( $attachment, 'raw' );
					$post_ids[] = $attachment->ID;
					$post_attachments[$attachment->post_parent][] = $attachment;
				}
				
				update_post_cache( $attachments );
			}
		}
		
		// Retrieves and stores into the cache the metadata we need later
		update_meta_cache( 'post', $post_ids );
		
		// This hack lets us save a lot of queries that would be performed when we call get_permalink()
		update_post_cache( $posts );
		
		$changefreq = $this->get_info('changefreq');
		$priority   = $this->get_info('priority');
		
		foreach ( $posts as $post ) {
			$images = null;
			
			if ( isset( $post_attachments[$post->ID] ) )
				$images = &$post_attachments[$post->ID];
			
			if ( $post->ID == $this->pageOnFront ) {
				$this->home			= $post;
				$this->home->images = $images;
				
				continue;
			}
			elseif ( $post->ID == $this->pageForPosts  ) {
				$this->blog				= $post;
				$this->blog->changefreq = $changefreq;
				$this->blog->priority	= $priority;
				$this->blog->images		= $images;
				
				continue;
			}
			
			$this->addUrlItem(
				get_permalink( $post ),
				$post->post_modified,
				$this->db->getPostMeta( $post->ID, 'changefreq', $changefreq ),
				$this->db->getPostMeta( $post->ID, 'priority', $priority ),
				$images
			);
		}
	}
	
	/**
	 * Adds tags and categories to the sitemap.
	 *
	 * @since 1.3
	 */
	protected function taxonomies() {
		global $wpdb;
		
		$taxonomy = $this->get_info('content_type');
		
		if (! ($taxonomy == 'category' || $taxonomy == 'post_tag') )
			return false;
			
		$tax_not_in = '';
		$limit		= $this->getLimit();
		$exclude	= $this->get_info( 'exclude', array() );
		
		if ( $exclude && ( $ids = wp_parse_id_list( $exclude ) ) )
			$tax_not_in = 't.term_id NOT IN (' . implode( ',', $ids ) . ') AND';
		
		// t.slug and tt.taxonomy are necessary to make get_term_link() work properly
		$terms = $wpdb->get_results(
			"SELECT t.term_id, t.slug, tt.taxonomy, MAX(p.post_modified) AS `last_modified`
			 FROM {$wpdb->terms} AS t
			 INNER JOIN {$wpdb->term_taxonomy} AS tt USING(term_id)
			 	CROSS JOIN {$wpdb->term_relationships} AS tr USING(term_taxonomy_id)
			 	CROSS JOIN {$wpdb->posts} AS p ON p.ID = tr.object_id
			 WHERE {$tax_not_in} tt.taxonomy = '{$taxonomy}' AND p.post_type = 'post' AND p.post_status = 'publish'
			 GROUP BY t.term_id
			 ORDER BY p.post_modified DESC
			 LIMIT {$limit}"
		);
		
		// This hack lets us save a lot of queries that would be performed when we call get_term_link()		
		update_term_cache( $terms );
		 
		if ( $terms ) {
			$changefreq = $this->get_info('changefreq', 'none');
			$priority = $this->get_info('priority', 'none');
		
			foreach ( $terms as $term )
				$this->addUrlItem( get_term_link($term), $term->last_modified, $changefreq, $priority );
		}
	}
	
	/**
	 * Adds author pages to the sitemap.
	 *
	 * @since 1.3
	 */
	protected function authors() {
		global $wpdb;
		
		$limit = $this->getLimit();
		$authors = $wpdb->get_results(
			"SELECT u.ID, u.user_nicename, MAX(p.post_modified) AS `last_post_modified`
			 FROM {$wpdb->users} AS u
			 INNER JOIN {$wpdb->posts} AS p ON p.post_author = u.ID
			 WHERE p.post_type = 'post' AND p.post_status = 'publish'
			 GROUP BY p.post_author 
			 ORDER BY p.post_modified DESC
			 LIMIT {$limit}"
		);
		
		if ( $authors ) {
			$changefreq = $this->get_info('changefreq', 'none');
			$priority = $this->get_info('priority', 'none');
			
			foreach ( $authors as $author )
				$this->addUrlItem( get_author_posts_url($author->ID, $author->user_nicename), $author->last_post_modified, $changefreq, $priority );
		}
	}
	
	/**
	 * Adds archives to the sitemap.
	 *
	 * @since 1.3
	 */
	protected function archives() {
		global $wpdb;
		
		$limit = $this->getLimit();
		$archives = $wpdb->get_results(
			"SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, MAX(post_modified) AS `last_modified`
			 FROM {$wpdb->posts} 
			 WHERE post_type = 'post' AND post_status = 'publish'
			 GROUP BY `year`, `month`
			 ORDER BY post_modified DESC
			 LIMIT {$limit}"
		);
		
		if ( $archives ) {
			$changefreq = $this->get_info('changefreq', 'none');
			$priority = $this->get_info('priority', 'none');
				
	        foreach ( $archives as $archive )
	        	$this->addUrlItem( get_month_link($archive->year, $archive->month), $archive->last_modified, $changefreq, $priority );
        }
	}
	
	/**
	 * 
	 *
	 * @since 1.4.3
	 *
	 * @param string $string
	 * @param int $num_chars Max length of the returned string
	 * @return string
	 */
	private function encode( $string, $num_chars = 160 ) {
		$string	= html_entity_decode( $string, ENT_QUOTES, $this->siteCharset );
		$string	= trim( preg_replace( '/[\n\r\t ]+/', ' ', strip_tags( $string ) ) );
		
		if ( strlen( $string ) > $num_chars ) {
			$string = substr( $string, 0, $num_chars );
			
			// Trim the last word — it could be truncated
			$string = preg_replace( '/ [^ ]+$/', ' ', $string ) . '...';
		}
		
		// The encoding argument is explicitly set for backward compatibility 
		// with versions of PHP older than 5.4
		return htmlspecialchars( $string, ENT_QUOTES, 'UTF-8' );
	}
}
?>