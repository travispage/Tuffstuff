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
if ( !defined( 'ABSPATH' ) )
    die( 'Forbidden' );


if (!class_exists('NinukisApi')) {

    class NinukisApi {

        /**
         * Holds the singleton instance of this class
         *
         * @since 1.0.8
         * @var NinukisApi
         */
        private static $instance = false;

        /**
         * Singleton
         *
         * @since 1.0.8
         * @static
         * @return NinukisApi
         */
        public static function get_instance() {
            if (!self::$instance) {
                self::$instance = new NinukisApi;
            }

            return self::$instance;
        }

        /**
         * Constructor for singleton
         *
         * @since 1.0.8
         * @return NinukisApi
         */
        private function __construct() {
            
        }

        /**
         * Requests an instant backup 
         * 
         * @param type $backupComment
         * @return boolean
         */
        public function performInstantBackup($backupComment = null) {
            try {
                $response = $this->invokeBusAPI("/wpinstall/backup?comment=" . urlencode($backupComment));
                return wp_remote_retrieve_response_code($response) == 200;
            } catch (Exception $e) {
                Ninukis_Plugin::log_me(sprintf("requestInstantBackup: failed to execute requestInstantBackup operation due to '%s'", $e->getMessage()));
                return false;
            }
        }

        /**
         * Request a file permissions repair request for either the production
         * or dev site.
         *
         * More info @ EWPDEV-384
         * @since    1.0.9
         */
        public function performFixFilePermissions($staging = false) {
            try {
                $response = $this->invokeBusAPI("/wpinstall/fixPermissions?staging=" . urlencode($staging));
                return wp_remote_retrieve_response_code($response) == 200;
            } catch (Exception $e) {
                Ninukis_Plugin::log_me(sprintf("performFixFilePermissions: failed to execute requestInstantBackup operation due to '%s'", $e->getMessage()));
                return false;
            }
        }

        /**
         * Request a WP core update
         *
         * More info @ EWPDEV-778
         * @since    1.0.8-HOTFIX-2
         */
        public function performWPCoreUpdate($staging = false) {

            try {
                $response = $this->invokeBusAPI("/wpinstall/updateCore?staging=" . $staging);
                return wp_remote_retrieve_response_code($response) == 200;
            } catch (Exception $e) {
                Ninukis_Plugin::log_me(sprintf("requestWPCoreUpdate: failed to execute requestWPCoreUpdate operation due to '%s'", $e->getMessage()));
                return false;
            }
        }

        /**
         * Requests a object cache flash in the caching cluster
         */
        public function flushObjectCache() {
            try {
                Ninukis_Plugin::log_me("requesting flush object cache");
                $response = $this->invokeBusAPI("/wpinstall/objectCachePurge");
                return wp_remote_retrieve_response_code($response) == 200;
            } catch (Exception $e) {
                Ninukis_Plugin::log_me(sprintf("purge_site_object_cache: failed to execute purge_site_object_cache operation due to '%s'", $e->getMessage()));
                return false;
            }
        }
        
        /**
         * WIP
         * @param type $paths
         * @param type $profile
         * @return boolean
         */
        public function smackRequest($paths, $profile = 'default') {
          try {
            Ninukis_Plugin::log_me("requesting smacking service ");
            $params = array(
              "filePaths" => $paths,
              "profile" => $profile,
              "staging" => Ninukis_Plugin::isStagingEnv(),
            );
            $response = $this->postJSONToBusAPI("/wpinstall/smackerRequest", $params);
            return wp_remote_retrieve_response_code($response) == 200;
          } catch (Exception $ex) {
            Ninukis_Plugin::log_me(sprintf("smackRequest: failed to execute smackRequest operation due to '%s'", $e->getMessage()));
            return false;
          }
        }

        /**
         * Async invoke of a Ninukis API call. 
         * Lower level of 
         * @param type $url
         * @param type $parameters
         * @return type
         */
        public function asyncInvokeBusAPI($url, $parameters = null) {
            return $this->invokeBusAPI($url, $parameters, 15, false);
        }

        public function invokeBusAPI($url, $parameters = null, $timeout = 15, $blocking = true) {

            # create our signature
            $user_agent = 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url');
            $request_date = gmdate('D, d M Y H:i:s T');
            $salt = substr(md5(rand()), 0, 16);

            $data = WP_NINUKIS_PUBLIC_API_KEY . $user_agent . $request_date . $salt;
            $sec_sig = hash_hmac("sha256", $data, WP_NINUKIS_SECRET_API_KEY);

            # create request
            $args = array(
                'timeout' => $timeout,
                'redirection' => 5,
                'httpversion' => '1.0',
                'user-agent' => $user_agent,
                'blocking' => $blocking,
                'headers' => array(
                    'Date' => $request_date,
                    'X-Ninukis-API-Magic' => WP_NINUKIS_PUBLIC_API_KEY . ":" . base64_encode($salt),
                    'X-Ninukis-API-Sig' => base64_encode($sec_sig),
                ),
                'cookies' => array(),
                'body' => null,
                'compress' => false,
                'decompress' => true,
                'sslverify' => true,
                'stream' => false,
                'filename' => null
            );

            if (defined('WP_NINUKIS_API_URL') && WP_NINUKIS_API_URL) {
                $finalURL = WP_NINUKIS_API_URL . $url;
            } else {
                $finalURL = 'http://api.pressidium.com/restricted/api/v1.0' . $url;
            }
            #Ninukis_Plugin::log_me(sprintf("invoking bus at %s with arguments '%s'", $finalURL, print_r($args, true)));
            $response = wp_remote_get($finalURL, $args);
            return $response;
        }

        public function postJSONToBusAPI($url, $parameters = null, $timeout = 30, $blocking = true) {

            # create our signature
            $user_agent = 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url');
            $request_date = gmdate('D, d M Y H:i:s T');
            $salt = substr(md5(rand()), 0, 16);

            $data = WP_NINUKIS_PUBLIC_API_KEY . $user_agent . $request_date . $salt;
            $sec_sig = hash_hmac("sha256", $data, WP_NINUKIS_SECRET_API_KEY);

            # create request
            $args = array(
                'timeout' => $timeout,
                'redirection' => 5,
                'httpversion' => '1.0',
                'user-agent' => $user_agent,
                'blocking' => $blocking,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Date' => $request_date,
                    'X-Ninukis-API-Magic' => WP_NINUKIS_PUBLIC_API_KEY . ":" . base64_encode($salt),
                    'X-Ninukis-API-Sig' => base64_encode($sec_sig),
                ),
                'cookies' => array(),
                'body' => json_encode($parameters),
                'compress' => false,
                'decompress' => true,
                'sslverify' => true,
                'stream' => false,
                'filename' => null
            );

            if (defined('WP_NINUKIS_API_URL') && WP_NINUKIS_API_URL) {
                $finalURL = WP_NINUKIS_API_URL . $url;
            } else {
                $finalURL = 'http://api.pressidium.com/restricted/api/v1.0' . $url;
            }
            #Ninukis_Plugin::log_me(sprintf("invoking bus at %s with arguments '%s'", $finalURL, print_r($args, true)));
            $response = wp_remote_post($finalURL, $args);
            return $response;
        }

    }

}
