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

if (!class_exists('NinukisCustomRewrites')) {

    class NinukisCustomRewrites {

        /**
         * Holds the singleton instance of this class
         *
         * @since 1.0.10-HOTFIX-1
         * @var NinukisCustomRewrites
         */
        private static $instance = false;

        /**
         * Singleton
         *
         * @since 1.0.10-HOTFIX=1
         * @static
         * @return NinukisCustomRewrites
         */
        public static function get_instance() {
            if (!self::$instance) {
                self::$instance = new NinukisCustomRewrites;
            }

            return self::$instance;
        }

        /**
         * Constructor for singleton
         *
         * @since 1.0.10-HOTFIX-1
         * @return NinukisCustomRewrites
         */
        private function __construct() {
            /*
             * If $prsdm_custom_rewrites are defined then filter !
             */
            global $prsdm_custom_rewrites;
            if (!empty($prsdm_custom_rewrites) && is_array( $prsdm_custom_rewrites ) ) {
                add_filter('ninukis_filter_output', array($this, 'do_filter_output'), 920, 1); # add the filter 'last' in the chain
            }
        }

        /**
         * Perform custom rewrites
         * @since 1.0.10-HOTFIX-1
         * @param type $html_content
         */
        public function do_filter_output($html_content) {
            global $prsdm_custom_rewrites;
            $patterns = array_keys($prsdm_custom_rewrites);
            // fix patterns
            array_walk($patterns, function(&$value, $key) {
                $value = '#' . $value . '#';
            });
            $replacements = array_values($prsdm_custom_rewrites);
            return preg_replace($patterns, $replacements, $html_content);
        }

        
        

    }

}

