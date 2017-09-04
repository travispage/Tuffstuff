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

if (!class_exists('NinukisSSL')) {

    class NinukisSSL {

        /**
         * Holds the singleton instance of this class
         *
         * @since 1.0.9
         * @var NinukisSSL
         */
        private static $instance = false;

        /**
         * Singleton
         *
         * @since 1.0.9
         * @static
         * @return NinukisSSL
         */
        public static function get_instance() {
            if (!self::$instance) {
                self::$instance = new NinukisSSL;
            }

            return self::$instance;
        }

        /**
         * Constructor for singleton
         *
         * @since 1.0.9
         * @return NinukisSSL
         */
        private function __construct() {
            /* if ssl is enabled or force SSL on admin or login page is enabled, then
             * enable SSL rewrite rules
             */
            if (is_ssl() || ( defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ) || ( defined('FORCE_SSL_LOGIN') && FORCE_SSL_LOGIN )) {
                add_filter('ninukis_filter_output', array($this, 'do_filter_output'), 900, 1); # add the filter 'last' in the chain
            }
        }

        /**
         * Perform SSL mixed content fixes
         * @since 1.0.9
         * @param type $html_content
         */
        public function do_filter_output($html_content) {
            
            // fetch the domain of the site (don't use the HTTP_HOST variable, but
            // rather the home option (althought slower, but safer) 
            $currentDomain = parse_url( get_option( "home"), PHP_URL_HOST );
            $html_content = preg_replace( "#http://{$currentDomain}(/?)#", 'https://' . $currentDomain . '$1', $html_content );
            
            // is the domain in the HTTP_HOST header different from the home option ?
            $httpDomain = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_SPECIAL_CHARS);
            if( $httpDomain != $currentDomain ) {
                $html_content = preg_replace( "#http://{$httpDomain}(/?)#", 'https://' . $httpDomain . '$1', $html_content );
            }
            
            return $html_content;
        }

        

    }

}

