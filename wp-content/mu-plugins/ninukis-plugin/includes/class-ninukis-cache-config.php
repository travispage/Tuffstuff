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

if (!class_exists('NinukisCacheConfig')) {

    class NinukisCacheConfig
    {

        /**
         * Holds the singleton instance of this class
         *
         * @since 1.0.10-HOTFIX-2
         * @var NinukisCacheConfig
         */
        private static $instance = false;

        /**
         * Singleton
         *
         * @since 1.0.10-HOTFIX=2
         * @static
         * @return NinukisCacheConfig
         */
        public static function get_instance()
        {
            if (!self::$instance) {
                self::$instance = new NinukisCacheConfig;
            }

            return self::$instance;
        }

        /**
         * Constructor for singleton
         *
         * @since 1.0.10-HOTFIX-2
         * @return NinukisCacheConfig
         */
        private function __construct()
        {
            //error_log(__METHOD__);
                        
            add_action('init', array($this, 'set_caching_configuration'));
            //$this->_addPluginActions();
        }

        private function _addPluginActions()
        {
            //woocommerce page options updates
            if ( function_exists('WC') ) {
                add_action( 'update_option_woocommerce_cart_page_id', array($this, '__ninukis_cache_config_update_options'), 10, 2 );
                add_action( 'update_option_woocommerce_checkout_page_id', array($this, '__ninukis_cache_config_update_options'), 10, 2 );
            }
            //jigoshop @todo
            //WPShop @todo
            //EDD @todo
        }
        
        /**
         * Constructs the X-Cache-Payload which is send as an http header along with the response         
         * @global string $pressidium_cache_level
         * @global array $pressidium_plugins_exclude_from_cache
         * @global array $pressidium_exclude_from_cache
         * @global array $pressidium_custom_ttl_for_urls
         */
        public function set_caching_configuration()
        {

            //error_log(__METHOD__);
            global $pressidium_cache_level;
            global $pressidium_plugins_exclude_from_cache;
            global $pressidium_exclude_from_cache;
            global $pressidium_custom_ttl_for_urls;

            if (!isset($pressidium_custom_ttl_for_urls)) {
                $pressidium_custom_ttl_for_urls = array();
            }
            if (!isset($pressidium_exclude_from_cache)) {
                $pressidium_exclude_from_cache = array();
            }
            if (!isset($pressidium_plugins_exclude_from_cache)) {
                $pressidium_plugins_exclude_from_cache = array();
            }
            if (!isset($pressidium_cache_level)) {
                $pressidium_cache_level = "A";
            }

            //default values
            $header_X_Cache_Why = "";
            $header_X_Cache_TTL = "";

            //error_log("request url= " . $_SERVER["REQUEST_URI"]);
            $request_uri = is_string($_SERVER["REQUEST_URI"]) && !empty($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : null;

            //check if user is logged in
            if (is_user_logged_in()) {
                $header_X_Cache_Why = $this->appendString($header_X_Cache_Why, "user-logged-in");
            }
            foreach ($pressidium_exclude_from_cache as $regex_body) {
                $regex_uri = "@" . str_replace("@", "\@", $regex_body) . "@i";
                if (1 === preg_match($regex_uri, $request_uri)) {
                    $header_X_Cache_Why = $this->appendString($header_X_Cache_Why, "url:client-defined");
                    break;
                }
            }
            //check if DONTCACHEPAGE constant is defined            
            if (defined('DONOTCACHEPAGE') && DONOTCACHEPAGE) {
                //error_log("DONOTCACHEPAGE==" . DONOTCACHEPAGE);
                $header_X_Cache_Why = $this->appendString($header_X_Cache_Why, "backend-nocache-response:plugin");
            }
            //check if request_uri has custom defined ttl value            
            if (array_key_exists($request_uri, $pressidium_custom_ttl_for_urls)) {
                $header_X_Cache_TTL = $pressidium_custom_ttl_for_urls[$request_uri];
            }

            $payload = json_encode(array(
                'X-Cache-Level' => $pressidium_cache_level,
                'X-Cache-Why' => $header_X_Cache_Why,
                'X-Cache-TTL' => $header_X_Cache_TTL,
            ));

            //error_log($payload);
            if (!headers_sent()) {
                header("X-Backend-Payload: " . $payload);
            } else {
                error_log("[press:3] failed to output header");
            }
        }

        public function appendString($original, $new)
        {
            return empty($original) ? $new : $original . "," . $new;
        }

        /**
         * Scans for known plugins and constructs an array with urls that 
         * need cache exclusion
         * @return array
         */
        public function getExcludedPages()
        {
            $urls = array();

            //WooCommerce get 'checkout' and 'cart' pages
            if (function_exists('WC') && function_exists('wc_get_page_id')) {

                $checkoutPageId = wc_get_page_id('checkout');
                if ($checkoutPageId && $checkoutPageId != '-1') {

                    //error_log("wc_checkout_page==" . $checkoutPageId . " #### /" . basename(get_permalink($checkoutPageId)));
                    $urls = array_merge($urls, array("/" . basename(get_permalink($checkoutPageId))));
                }
                $cartPageId = wc_get_page_id('cart');
                if ($cartPageId && $cartPageId != '-1') {
                    //error_log("wc_cart_page==" . $cartPageId . " #### /" . basename(get_permalink($cartPageId)));
                    $urls = array_merge($urls, array("/" . basename(get_permalink($cartPageId))));
                }
            }
            //jigoshop @todo
            //WPShop @todo
            //EDD @todo            

            return $urls;
        }
        /**
         * Notifies Ninukis for caching updates
         * @param type $old
         * @param type $new
         */
        public function __ninukis_cache_config_update_notification($old, $new)
        {
            //error_log("okok: " . __METHOD__);
            if ($old != $new) {
                //$this->notifyNinukis(); //@todo
            }
        }

              
    }

}
