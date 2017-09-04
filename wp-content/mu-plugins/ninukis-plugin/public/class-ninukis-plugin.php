<?php

/**
 * Ninukis Plugin.
 *
 * @package   Ninukis Plugin
 * @author    Filip Slavik <filip@pressidium.com>
 * @license   GPL-2.0+
 * @link      https://pressidium.com
 * @copyright 2014-2015 Pressidium
 */
// Make sure it's wordpress
if (!defined('ABSPATH'))
    die('Forbidden');

class Ninukis_Plugin extends NinukisPluginCommon {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    const VERSION = NINUKIS_VERSION;

    /**
     * 
     * Unique identifier for your plugin.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_slug = 'ninukis-plugin';

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    private function __construct() {


        // Load plugin text domain
        add_action('init', array($this, 'load_plugin_textdomain'));

        /**
         * Register hooks for removing html meta tag generator which exposes WP version
         */
        remove_action('wp_head', 'wp_generator');

        # hook add_special_header function to 'init' as there are few plugins
        # out there (coming soon plugins) that are ending the WP flow before
        # filter headers could be applied (PWNP-42)
        add_action('init', array($this, 'add_special_header'));

        # handle login failures (PWNP-137)
        add_action( 'wp_login_failed', array( $this, 'handle_login_failed' ) );
        # handle empty username and/or passwords (PWNP-138)
        add_filter( 'authenticate', array( $this, 'wp_login_username_password_check' ), 100, 3 );

        // Load public-facing style sheet and JavaScript.
        //add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        //add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        

        # PWNP-8 : bind 'check_for_internal_command' function to "plugins_loaded" hook
        # so it's executed early
        add_action('plugins_loaded', array($this, 'check_for_internal_command'));

        # suppress various default e-mail messages for updates since
        # are actually handling this on our own
        add_filter('auto_update_core', '__return_false');
        add_filter('auto_update_translation', '__return_false');
        add_filter('auto_core_update_send_email', '__return_false');
        add_filter('automatic_updates_send_debug_email', '__return_false');
        
        # PWNP-68 - disable xmlrpc some commands likesystem.multicall
        add_filter('xmlrpc_methods', array ( $this, 'disable_xmlrpc_methods' ));
       
    }

    public function check_for_internal_command() {
        $cmd = ninukis_param('ninukis-cmd');
        if ($cmd)
            return $this->handle_internal_command($cmd);
        else
            return true;
    }

    /**
     * Return the plugin slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * Returns the version of the plugin
     *
     */
    public static function getVersion() {
        return Ninukis_Plugin::VERSION;
    }

    /*
     * Set an site option (update or add)
     */

    public static function setWPSiteOption($option, $value) {
        if (!update_site_option($option, $value)) {
            /* update failed, so let's add the option */
            add_site_option($option, $value);
        }
    }

    public static function setWPOption($blogId, $option, $value) {
        if (function_exists('update_blog_option')) {
            if (!update_blog_option($blogId, $option, $value)) {
                /* updated failed for blog, so let's try add */
                add_blog_option($blogId, $option, $value);
            }
        } else {
            /* update_blog_option is not defined. This is good only if 
             * blogId is NULL
             */
            if (NULL === $blogId) {
                if (!update_option($option, $value)) {
                    /* updated failed for blog, so let's try add */
                    add_option($option, $value);
                }
            }
        }
    }

    /*
     * Get a site option
     * (Nothing special, but since we have setWPSiteOption ... )
     */

    public static function getWPSiteOption($option, $default = false) {
        return get_site_option($option, $default);
    }

    public static function getWPOption($blogId, $option, $default = false) {
        if (function_exists('get_blog_option')) {
            return get_blog_option($blogId, $option, $default);
        } else {
            /* get_blog_option is not defined. This is good only if 
             * blogId is NULL
             */
            if (NULL === $blogId) {
                return get_option($option, $default);
            } else
                return $default;
        }
    }
    
    /**
     * Returns True if the site is running on the staging enviroment
     * @return boolean
     */
    public static function isStagingEnv() {
        return isset($_SERVER["HTTP_X_PRESSIDIUM_STAGING"]) ? $_SERVER["HTTP_X_PRESSIDIUM_STAGING"] : false;
    }
    
    /**
     * Returns True if the site is being cached
     * @return boolean
     */
    public static function isCachingEnabled() {
      global $pressidium_cache_level;
      $pressidium_cache_level = isset($pressidium_cache_level)?$pressidium_cache_level:"A";
      return $pressidium_cache_level==="Z"?false:true;  
    }

    public static function log_me($message) {
        if (defined('WP_DEBUG') && WP_DEBUG === true) {
            if (is_array($message) || is_object($message)) {
                error_log(sprintf("wp-ninukis : %s", print_r($message, true)));
            } else {
                error_log(sprintf("wp-ninukis : %s", $message));
            }
        }
    }

    public static function byte_format($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    Ninukis_Plugin    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;   
        }
        return self::$instance;
    }

    /**
     * Add a special header (EWPDEV-464) to our responses
     */
    public function add_special_header() {
        if ( ! headers_sent() ) {
            header(sprintf('X-Pressidium-NinukisWP-Ver: %s', Ninukis_Plugin::getVersion()));
        } else {
            error_log("[press:2] failed to output header");
        }
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        $domain = $this->plugin_slug;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, FALSE, basename(plugin_dir_path(dirname(__FILE__))) . '/languages/');
    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_slug . '-plugin-styles', plugins_url('assets/css/public.css', __FILE__), array(), self::VERSION);
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_slug . '-plugin-script', plugins_url('assets/js/public.js', __FILE__), array('jquery'), self::VERSION);
    }

    /**
     * Returns the WP version
     *
     * This implemention is intentionally using PHP's include and not include_once
     */
    public static function getWPCoreVersion() {
        if (!(defined('wp_version') && $wp_version )) {
            include ABSPATH . 'wp-includes/version.php';
        }
        return $wp_version;
    }

    /**
     * Returns the WP version
     *
     * This implemention is intentionally using PHP's include and not include_once
     */
    public static function getWPCoreDBVersion() {
        if (!(defined('wp_db_version') && $wp_db_version )) {
            include ABSPATH . 'wp-includes/version.php';
        }
        return $wp_db_version;
    }

    /**
     * Converts a plugin basename back into a friendly slug.
     * (took from https://github.com/wp-cli/wp-cli/blob/340ca491e30a67bba5ca35511033cf62b270b298/php/utils-wp.php)
     */
    public static function get_plugin_name($basename) {
        if (false === strpos($basename, '/'))
            $name = basename($basename, '.php');
        else
            $name = dirname($basename);

        return $name;
    }

    /**
     * Retrieves version info about the current WP instance.
     * Currently we return the WP plugin, WP core & DB schema version
     *
     * More info @
     *
     * @since    1.0.2
     */
    public static function getVersionInfo() {

        static $wp_version_info = null;

        if (!$wp_version_info) {
            /* construct the info */
            $_t = new stdClass;
            $_t->wpPluginVersion = Ninukis_Plugin::getVersion();
            $_t->wpVersion = Ninukis_Plugin::getWPCoreVersion();
            $_t->wpDbVersion = Ninukis_Plugin::getWPCoreDBVersion();
            $wp_version_info = $_t;
        }

        return $wp_version_info;
    }

    /**
     * Returns the version of the latest available WordPress template Pressidium offers
     *
     * @since    1.5.0
     */
    public function getLatestWordPressVersion() {
        if (false === ( $latestVersion = get_transient('ninukis-latest-wordpress-version') )) {
            // version not here, so let's fetch
            try {
                $response = $this->invokeBusAPI("/wpinstall/latestVersion");
                if (wp_remote_retrieve_response_code($response) == 200) {
                    $body = json_decode(wp_remote_retrieve_body($response));
                    $latestVersion = $body->payload;
                    set_transient('ninukis-latest-wordpress-version', $latestVersion, 60 * 30);
                } else
                    $latestVersion = false;
            } catch (Exception $e) {
                Ninukis_Plugin::log_me(sprintf("getLatestWordPressVersion: failed to execute getLatestWordPressVersion operation due to '%s'", $e->getMessage()));
                $latestVersion = false;
            }
        }
        return $latestVersion;
    }
    
    /**
     * Ensures that the smacking feature is able to perform
     * it work
     * @deprecated since version 1.0.9
     * @since    1.5.1
     * @see NinukisOperations::ensureSmack()
     */
    private function ensureSmack() {
        return NinukisOperations::get_instance()->ensureSmack();
    }

    
    /**
     * Handles a CDN update request
     * @deprecated since version 1.0.10-HOTFIX-1
     */
    private function handle_cdn_update($isCDNCapable, $publicCDNDomain, $blogId = NULL) {
        if (is_multisite()) {
            /* this is a multisite installation, blogId is signifficat */
            Ninukis_Plugin::log_me("CDN update request received for blog '$blogId' with CDN rewrite '$publicCDNDomain' with status '$isCDNCapable'");
            $this->setWPOption($blogId, 'ninukis-cdn-enabled', $isCDNCapable);
            $this->setWPOption($blogId, 'ninukis-cdn-domain', $publicCDNDomain);
            /* PWNP-21 - handle the global network CDN flag */
            if ('enabled' === $isCDNCapable) {
                /* CDN has been enabled for one blog, so the global CDN flag
                 * can be set to True  */
                $this->setWPSiteOption('ninukis-network-cdn-enabled', TRUE);
            } else {
                /* CDN has been just disabled for one blog in the network, so
                 * check if the global CDN flag should still be true
                 */
                $globalCDNFlag = FALSE; // let's start by assuming FALSE

                /* we will iterate only for the first 100 sites. If the MS is
                 * bigger than that, then we will address this with a support 
                 * ticket
                 */
                $args = array(
                    'limit' => 100,
                    'offset' => 0,
                );

                $blogs = wp_get_sites($args);
                foreach ($blogs as $blogid => $blog) {
                    $globalCDNFlag = 'enabled' === $this->getWPOption($blog['blog_id'], 'ninukis-cdn-enabled', 'disabled');
                    if (true === $globalCDNFlag)
                        break; // we found a blog with CDN enabled, exit the loop
                }
                $this->setWPSiteOption('ninukis-network-cdn-enabled', $globalCDNFlag);
            }
        } else {
            Ninukis_Plugin::log_me("CDN update request received with CDN rewrite '$publicCDNDomain' with status '$isCDNCapable'");
            $this->setWPSiteOption('ninukis-cdn-enabled', $isCDNCapable);
            $this->setWPSiteOption('ninukis-cdn-domain', $publicCDNDomain);
        }

        return true;
    }

    private function handle_internal_command($cmd = null) {
        if (!$cmd) {
            return;    // without a command, it's not an internal request
        }
        #Ninukis_Plugin::log_me("internal_command called");


        if (!$this->is_allowed_ip($_SERVER['REMOTE_ADDR'])) {
            print("Ignoring request from non-local host: " . $_SERVER['REMOTE_ADDR'] . " to " . $_SERVER['SERVER_ADDR'] . "\n");
            exit(0);  // local requests only -- security! Meaning our public IP address or localhost
        }

        @ob_get_clean();
        error_reporting(-1);
        header("Content-Type: text/plain");

        // Execute command
        switch ($cmd) {
            case 'version':
                /* display version info (WP version / WP plugin version) */
                header("Content-Type: text/html");
                header("X-Ninukis-Host: " . gethostname() . " " . $_SERVER['SERVER_ADDR']);
                $result = $this->getVersionInfo();
                print json_encode($result);
                break;
            case 'update-cdn-config':
                /* We have received an 'update-cdn-config', but proceed only if this is a POST request */
                header("Content-Type: text/plain");
                header("X-Ninukis-Host: " . gethostname() . " " . $_SERVER['SERVER_ADDR']);
                if ($this->handle_cdn_update(ninukis_param('isCDNCapable', FALSE), ninukis_param('publicCDNDomain'), ninukis_param('blogId', NULL)))
                    print("CDN data updated\n");
                else
                    print( "CDN data *not* updated\n");
                break;
            case 'ping':
                header("Content-Type: text/plain");
                header("X-Ninukis-Host: " . gethostname() . " " . $_SERVER['SERVER_ADDR']);
                print( "pong\n");
                break;
            case 'purge-objectstore-cache':
                /* purges only object store cache */
                header("Content-Type: text/plain");
                header("X-Ninukis-Host: " . gethostname() . " " . $_SERVER['SERVER_ADDR']);
                echo("Purging Object Store Cache.\n");
                if ($this->purge_site_object_cache()) {
                    echo(" * Object store cache has been flushed.\n");
                }
                break;
            case 'purge-all-caches':
                header("Content-Type: text/plain");
                header("X-Ninukis-Host: " . gethostname() . " " . $_SERVER['SERVER_ADDR']);
                echo("Purging All Caches.\n");
                if ($this->purge_site_object_cache()) {
                    echo(" * Object store cache has been flushed.\n");
                }
                if ($this->purge_site_varnish_cache()) {
                    echo(" * Varnish Site has been flushed.\n");
                }
                break;
            default:
                die("ERROR: unknown command: `$cmd`\n");
        }

        // Stop processing
        exit(0);
    }

    /**
     * Purges all known caches.
     *
     * @since    1.0.0
     */
    public function purgeAllCaches() {
        return NinukisCaching::get_instance()->purgeAllCaches();
    }

    /**
     * Purges the site's CDN cache
     * @deprecated since version 1.0.10-HOTFIX-2
     * @see NinukisCaching::purge_site_cdn_cache()
     *
     * For more info @ EWPDEV-572
     *
     * @since    1.0.4
     */
    public function purge_site_cdn_cache() {
        return NinukisCaching::get_instance()->purge_site_cdn_cache();
    }

    /**
     * For more info @ EWPDEV-836
     * @deprecated since version 1.0.10-HOTFIX-2
     * @see NinukisCaching::purge_network_cdn_cache()
     * @since    1.0.7
     * 
     * @return boolean
     */
    public function purge_network_cdn_cache() {
        return NinukisCaching::get_instance()->purge_network_cdn_cache();
    }

    /**
     * Purges the site's object cache
     * @deprecated since version 1.0.8
     * @see NinukisCaching::purge_site_object_cache()
     * @since    1.0.0
     */
    public function purge_site_object_cache() {
        return NinukisCaching::get_instance()->purge_site_object_cache();
    }

    /**
     * Purges the entire site from Varnish Cache
     * @deprecated since version 1.0.10-HOTFIX-2
     * @see NinukisCaching::purge_site_varnish_cache()
     * @since    1.0.0
     */
    public function purge_site_varnish_cache() {
        return NinukisCaching::get_instance()->purge_site_varnish_cache();
    }

    /**
     * Purges all entirely all sites of the given network
     * @return boolean
     * @deprecated since version 1.0.10-HOTFIX-2
     * @see NinukisCaching::purge_network_varnish_cache()
     * @since 1.0.6
     */
    public function purge_network_varnish_cache() {
        return NinukisCaching::get_instance()->purge_network_varnish_cache();
    }

    /**
     * Purges a cached page/blog when something happens on it (update, edit, etc)
     * @deprecated since version 1.0.10-HOTFIX-2
     * @see NinukisCaching::purge_page_cache($post_id)
     * @since 1.0.0
     */
    public function purge_page_cache($post_id = null) {
        NinukisCaching::get_instance()->purge_page_cache($post_id);
    }
    
    /**
     * Unset / disable various xmlrpc methods (PWNP-68)
     * @param type $methods
     * @return type
     */
    function disable_xmlrpc_methods( $methods ) {
//	    unset( $methods['system.multicall'] );
//	    unset( $methods['system.listMethods'] );
//	    unset( $methods['system.getCapabilities'] );
        return $methods;
    }

    /**
     * Add our special header in the response, when the user fails to log-in
     * @param $username The username
     */
    public function handle_login_failed($username) {
        if( ! headers_sent() ) {
            header('X-Login-Failed: 1');
        } else {
            error_log("[press:1] failed to output header");
        }
    }

    /**
     * Triggers an `wp_login_failed` action when username or password is empty (PWNP-138)
     *
     * @since 1.3.6
     *
     * @param WP_User|WP_Error|null $user     WP_User or WP_Error object from a previous callback. Default null.
     * @param string                $username Username for authentication.
     * @param string                $password Password for authentication.
     * @return WP_User|WP_Error WP_User on success, WP_Error on failure.
     */
    public function wp_login_username_password_check( $user, $username, $password ) {

        if ( ( empty( $username ) || empty( $password ) ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            //do_action( 'wp_login_failed', $username );
            $this->handle_login_failed( $username ); # just call our function, don't trigger the 'wp_login_failed' action.
        }

        return $user;
    }

    

}
