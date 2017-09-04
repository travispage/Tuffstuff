<?php

/**
 * Part of Ninukis Plugin.
 *
 * @package   Ninukis Plugin
 * @author    Filip Slavik <filip@pressidium.com>
 * @license   GPL-2.0+
 * @link      https://pressidium.com
 * @copyright 2014-2015 TechIO Ltd
 */
// Make sure it's wordpress
if (!defined('ABSPATH'))
    die('Forbidden');

if (!class_exists('NinukisCaching')) {

    class NinukisCaching {

        /**
         * Holds the singleton instance of this class
         *
         * @since 1.0.8
         * @var NinukisCaching
         */
        private static $instance = false;

        /**
         * Holds the reference to the Ninukis Api class
         * 
         * @var NinukisApi 
         */
        private $ninukisApi = NULL;

        /**
         * Singleton
         *
         * @since 1.0.8
         * @static
         * @return NinukisCaching
         */
        public static function get_instance() {
            if (!self::$instance) {
                self::$instance = new NinukisCaching;
            }

            return self::$instance;
        }
        
        /**
         * Handles the various post status updates 
         * @param string $new_status
         * @param string $old_status
         * @param WP_Post $post
         */
        public function handle_post_status_transition( $new_status, $old_status, $post ) {
          if ( $old_status == 'publish' && in_array( $new_status, array( 'pending', 'draft', 'private' ) ) ) {
            // post has transitioned to a pending or draft status, so
            // purge post/page cache. 
            if( ! empty( $post->ID ) ) {
              $this->purge_page_cache( $post->ID, true );
            }
          }
        }
        /**
         * Constructor for singleton
         *
         * @since 1.0.8
         * @return NinukisCaching
         */
        private function __construct() {
          
            $this->ninukisApi = NinukisApi::get_instance();
            
            /* if caching is enabled for this site, go ahead and add the various
             * cache invalidation action/hooks
             */
            if ( Ninukis_Plugin::isCachingEnabled() ) {
                // Purge Varnish for a specific post and related URLs when something happens to it.
                add_action('clean_post_cache', array($this, 'purge_page_cache'));
                add_action('trashed_post', array($this, 'purge_page_cache')); # really ?
                add_action('deleted_post', array($this, 'purge_page_cache'));
                add_action('edit_post', array($this, 'purge_page_cache'));
                add_action('publish_page', array($this, 'purge_page_cache'));
                add_action('publish_post', array($this, 'purge_page_cache'));
                add_action('save_post', array($this, 'purge_page_cache'));
                add_action('transition_post_status', array($this, 'handle_post_status_transition'), 10, 3);
                add_action('after_switch_theme', array($this, 'purgeAllCaches'));
                // comment specific purging
                add_action('comment_post', array($this, 'purge_page_cache'));
                add_action('edit_comment', array($this, 'purge_page_cache'));
                add_action('transition_comment_status', array($this, 'purge_page_cache'));
                // theme customizer
                add_action('wp_ajax_customize_save', array($this, 'purgeAllCaches'));
                // widget changes
                add_action('wp_ajax_save-widget', array($this, 'purgeAllCaches'));
                add_action('wp_ajax_widgets-order', array($this, 'purgeAllCaches'));
                add_action('update_option_sidebars_widgets', array($this, 'purgeAllCaches'));
                // Plugin activate/deactivate
                add_action( 'deactivated_plugin', array( $this, 'purgeAllCaches' ) );
                add_action( 'activated_plugin',   array( $this, 'purgeAllCaches' ) );
                
                // menu changes
                add_action('wp_create_nav_menu', array($this, 'purgeAllCaches'));
                add_action('wp_update_nav_menu', array($this, 'purgeAllCaches'));
                add_action('wp_delete_nav_menu', array($this, 'purgeAllCaches'));
                add_action('wp_update_nav_menu_item', array($this, 'purgeAllCaches'));
                // Theme customizer
                add_action( 'customize_save',	array( $this, 'purgeAllCaches' ) );

                // Purge database cache when something happens that doesn't use the WordPress API to purge it properly
                add_action('signup_finished', array($this, 'purge_site_object_cache'));
                add_action('bp_core_clear_cache', array($this, 'purge_site_object_cache'));
                add_action('bp_blogs_new_blog', array($this, 'purge_site_object_cache'));
            }
        }

        /**
         * Returns the list of mapped domains a blog may be associated with 
         * @param int $blog_id the id of the blog
         * @return an array of domains
         */
        public function getMappedDomains($blog_id = NULL) {
            global $wpdb;
            $domains = array();
            if (defined('SUNRISE') && SUNRISE) {
                if ($blog_id)
                    $rows = $wpdb->get_results($wpdb->prepare("SELECT domain FROM {$wpdb->dmtable} WHERE blog_id = %d", $blog_id));
                else
                    $rows = $wpdb->get_results("SELECT domain FROM {$wpdb->dmtable}", OBJECT);

                if ($rows && count($rows) > 0) {
                    foreach ($rows as $row) {
                        $domains[] = strtolower($row->domain);
                    }
                }
            }
            return $domains;
        }

        /**
         * Purges the site's object cache. 
         * 
         * Note, the operation will call the wp_cache_flush() function which will
         * then invoke the respective api call. This loop is required!
         *
         * @since    1.0.0
         */
        public function purge_site_object_cache() {
            if (Ninukis_Plugin::isStagingEnv())
                return false; // object-cache is not supported in staging
            else
                return wp_cache_flush();
        }

        /**
         * Purges all known caches.
         *
         * @since    1.0.0
         */
        public function purgeAllCaches() {
            if ( ! Ninukis_Plugin::isCachingEnabled() ) {
              return false; 
            }
                

            /* purge all caches from varnish */
            $result = $this->purge_site_varnish_cache();

            /* purge all caches from object store */
            $result &= $this->purge_site_object_cache();

            return $result;
        }

        /**
         * Purges the site's CDN cache
         *
         * For more info @ EWPDEV-572
         *
         * @since    1.0.4
         */
        public function purge_site_cdn_cache() {

            if (Ninukis_Plugin::isStagingEnv() or ! NinukisCDN::isCDNEnabled()) {
                return false; // CDN is not supported in staging or CDN is not enabled
            }
            try {
                if (is_multisite())
                    $response = NinukisApi::get_instance()->invokeBusAPI("/wpinstall/CDNCachePurge?blogId=" . urlencode(get_current_blog_id()));
                else
                    $response = NinukisApi::get_instance()->invokeBusAPI("/wpinstall/CDNCachePurge");
                return wp_remote_retrieve_response_code($response) == 200;
            } catch (Exception $e) {
                Ninukis_Plugin::log_me(sprintf("purge_site_object_cache: failed to execute purge_site_cdn_cache operation due to '%s'", $e->getMessage()));
                return false;
            }
        }

        /**
         * For more info @ EWPDEV-836
         *
         * @since    1.0.7
         * 
         * @return boolean
         */
        public function purge_network_cdn_cache() {
            if (Ninukis_Plugin::isStagingEnv() or ! NinukisCDN::isCDNNetworkEnabled() or ! is_multisite()) {
                return false; // CDN is not supported in staging or CDN is not enabled for any site in the network
            }
            try {
                $response = NinukisApi::get_instance()->invokeBusAPI("/wpinstall/CDNCacheNetworkPurge");
                return wp_remote_retrieve_response_code($response) == 200;
            } catch (Exception $e) {
                Ninukis_Plugin::log_me(sprintf("purge_network_cdn_cache: failed to execute purge_network_cdn_cache operation due to '%s'", $e->getMessage()));
                return false;
            }
        }

        /**
         * Purges the entire site from Varnish Cache
         *
         * @since    1.0.0
         */
        public function purge_site_varnish_cache() {

            if ( ! Ninukis_Plugin::isCachingEnabled() ) {
              return false; 
            }
                
            $blog_url = get_bloginfo('url');
            $blog_url_parts = @parse_url($blog_url);

            $blog_domains[] = $blog_url_parts['host'];
            /* PWNP-105 : check if the proxy endpoint is specified,
             * if it is, then the site is behind a RP 
             */
            if (defined('WP_NINUKIS_PROXY_ENDPOINT_URL')) {
                $proxy_url_parts = parse_url(WP_NINUKIS_PROXY_ENDPOINT_URL);
                if ( isset($proxy_url_parts['host']) ) {
                    $blog_domains[] = $proxy_url_parts['host'];
                }
            }  
                
            // if this is a multisite, add all mapped domains to the array
            // of domains to be flushed
            if (is_multisite()) {
                $blog_domains = array_merge($blog_domains, $this->getMappedDomains(get_current_blog_id()));
            }

            $blog_domains = array_unique($blog_domains);

            Ninukis_Plugin::log_me(sprintf("requesting page cache purge for hosts [%s]", implode(",", $blog_domains)));
            $requestParams = array('hosts' => $blog_domains);
            $response = NinukisApi::get_instance()->postJSONToBusAPI('/wpinstall/cachePurge', $requestParams);
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                Ninukis_Plugin::log_me(sprintf("failed to send purge cache request due to '%s'", $error_message));
                return false;
            } else {
                if ($response['response']['code'] == 200) {
                    /* PWNP-33 - expose callbacks for caching invalidation */
                    do_action("ninukis_site_purged", $blog_domains);
                    return true;
                } else {
                    return false;
                }
            }
        }

        /**
         * Purges all entirely all sites of the given network
         * @return boolean
         * 
         * @since 1.0.6
         */
        public function purge_network_varnish_cache() {

            if (! Ninukis_Plugin::isCachingEnabled() ) {
              return false;
            }
            
            if (!is_multisite())
                return false; // this is not a multisite installation !

                /* prepare params for wp_get_sites invocation */
            $args = array(
                'limit' => 100,
                'offset' => 0,
            );

            $blog_domains = array();

            /* loop over results as long as get results */
            while ($blogs = wp_get_sites($args)) {
                /* iterate over blogs and gather info */
                foreach ($blogs as $blogid => $blog) {
                    $blog_domains[] = $blog['domain'];
                }

                /* to next block */
                $args['offset']+= count($blogs);
            }

            /* to the list of domains, add also any mapped domain exists for the entire network */
            $blog_domains = array_merge($blog_domains, $this->getMappedDomains());

            $blog_domains = array_unique($blog_domains);

            Ninukis_Plugin::log_me(sprintf("requesting page cache purge for hosts [%s]", implode(",", $blog_domains)));
            $requestParams = array('hosts' => $blog_domains);
            $response = NinukisApi::get_instance()->postJSONToBusAPI('/wpinstall/cachePurge', $requestParams);
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                Ninukis_Plugin::log_me(sprintf("failed to send purge cache request due to '%s'", $error_message));
                return false;
            } else {
                if ($response['response']['code'] == 200) {
                    /* PWNP-33 - expose callbacks for caching invalidation */
                    do_action("ninukis_network_site_purged", $blog_domains);
                    return true;
                } else {
                    return false;
                } 
            }
        }

        /**
         * Purges a cached page/blog when something happens on it (update, edit, etc)
         * 
         * @since 1.0.0
         * @staticvar int $invocation_counter
         * @param int $post_id
         * @param boolean $ignore_status If set to TRUE the page/post will be purged regardless it's status
         * 
         * @return boolean
         */
        public function purge_page_cache($post_id = null, $ignore_status = false) {
          if ( ! Ninukis_Plugin::isCachingEnabled() ) {
            return false;
          }
          
            static $invocation_counter;
            
            // if invocation_counter is greater than 0, then we have run already for this
            // very web request, so simply ignore ;-)
            if (isset($invocation_counter) && $invocation_counter > 0) {
                return false;
            }
            
            $blog_url = get_bloginfo('url');
            $blog_url_parts = @parse_url($blog_url);
            $blog_domains[] = $blog_url_parts['host'];

            // if this is a multisite, add all mapped domains to the array
            // of domains to be flushed
            if (is_multisite()) {
                $blog_domains = array_merge($blog_domains, $this->getMappedDomains(get_current_blog_id()));
            }

            $blog_domains = array_unique($blog_domains);

            $paths = array(); // will leave empty if we want a purge-all
            if ($post_id && $post_id >= 1 && !!($post = get_post($post_id))) {
   
              if ( ! $ignore_status ) {
                // Certain post types aren't cached so we shouldn't purge
                if ($post->post_type == 'attachment' || $post->post_type == 'revision')
                    return;

                // If the post isn't published, we don't need to purge (draft, scheduled, deleted)
                if ($post->post_status != 'publish') {
                    return;
                }
              }

              $blog_path = NinukisPluginCommon::get_path_trailing_slash(@$blog_url_parts ['path']);
              if ($blog_path == '/') {
                  $blog_path_prefix = "";
              } else {
                  $tpath = substr($blog_path, 0, - 1);
                  $blog_path_prefix = $tpath . ".*";
              }

              // Always purge the post's own URI, along with anything similar
              $post_parts = parse_url(get_permalink($post_id));
              $post_uri = rtrim($post_parts ['path'], '/') . "(.*)";
              if (!empty($post_parts ['query']))
                  $post_uri .= "?" . $post_parts ['query'];

              $paths [] = $post_uri;

              // until we resolve EWPDEV-282, we will purge also the non-regular expression variant
              $post_uri = rtrim($post_parts ['path'], '/');
              if (!empty($post_parts ['query']))
                  $post_uri .= "?" . $post_parts ['query'];
              $paths [] = $post_uri;

              if (defined('NINUKIS_PURGE_CATS_N_TAGS')) {
                  foreach (wp_get_post_categories($post_id) as $cat_id) {
                      $cat = get_category($cat_id);
                      $slug = $cat->slug;
                      $paths [] = "$blog_path_prefix/$slug/";
                  }
                  foreach (wp_get_post_tags($post_id) as $tag) {
                      $slug = $tag->slug;
                      $paths [] = "$blog_path_prefix/$slug/";
                  }
              }

              // purge the feed only if post/page being purged is not older than 7 days
              if (time() - strtotime($post->post_date_gmt) < 60 * 60 * 24 * 7) {
                  $paths [] = "/feed";
              }

              // EWPDEV-706 - purge the index page without constraints
              $paths [] = "${blog_path}";

              $purge_thing = $post_id;
            }
            
            if ( ! count( $paths ) ) {
                return; // short-circuit if there's nothing to do.
            }

            $paths = array_unique( $paths ); // allow the code above to be sloppy
            // If we've already purged on this web-request, don't do it again.
            // DO NOT RUN THIS at the TOP of the method because it's possible the post-status changed in the middle!
            
            if ( ! isset( $invocation_counter ) )
                $invocation_counter = 1;
            else
                $invocation_counter ++;

            // at this point we should have all paths & all domains that need purging
            $requestParams = new stdClass();
            $requestParams->hosts = $blog_domains;
            $requestParams->paths = $paths;
            $response = NinukisApi::get_instance()->postJSONToBusAPI('/wpinstall/cachePagePurge', $requestParams);
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                Ninukis_Plugin::log_me(sprintf("failed to send purge cache request due to '%s'", $error_message));
                return false;
            } else {
                if ($response['response']['code'] == 200) {
                    /* PWNP-33 - expose callbacks for caching invalidation */
                    do_action("ninukis_page_purged", $post_id, $blog_domains, $paths);
                    return true;
                } else {
                    return false;
                }
            }
        }
        
        /**
         * Clear transients that have expired before certain amount of hours
         * 
         * More info in PWNP-69
         * 
         * Code inspired by https://github.com/pressjitsu/wp-transients-cleaner/blob/master/transient-cleaner.php
         * @global type $wpdb
         * @param int $expiration_hours clear expired transient after number of hours   
         * @param int $time_limit  Maximum time of runtime 
         * @param int $batch Batch limit (by default 100)
         * @return int the number of transient removed
         */
        public static function clear_transients($expiration_hours = 24, $time_limit=30, $batch=100) {
            global $wpdb;
            $timestamp = time() - $expiration_hours * HOUR_IN_SECONDS; // expired x hours ago.
            $time_start = time();
            $total_transients_cleared = 0;
            
            // Don't take longer than $time_limit seconds.
            while ( time() < $time_start + $time_limit ) {
                    $option_names = $wpdb->get_col( "SELECT `option_name` FROM {$wpdb->options} WHERE `option_name` LIKE '\_transient\_timeout\_%'
                            AND CAST(`option_value` AS UNSIGNED) < {$timestamp} LIMIT {$batch};" );
                    if ( empty( $option_names ) )
                            break;
                    // Add transient keys to transient timeout keys.
                    foreach ( $option_names as $key => $option_name )
                            $option_names[] = '_transient_' . substr( $option_name, 19 );
                    // Create a list to use with MySQL IN().
                    $options_in = implode( ', ', array_map( function( $item ) use ( $wpdb ) {
                            return $wpdb->prepare( '%s', $item );
                    }, $option_names ) );
                    // Delete transient and transient timeout fields.
                    $total_transients_cleared += $wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` IN ({$options_in});" );
                    // Break if no more deletable options available.
                    if ( count( $option_names ) < $batch * 2 )
                            break;
            }
            return $total_transients_cleared;    
        }

    }
    
}

