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

if (!class_exists('NinukisFilterOutput')) {
    
    class NinukisFilterOutput {

        /**
         * Holds the singleton instance of this class
         *
         * @since 1.0.9
         * @var NinukisFilterOutput
         */
        private static $instance = false;
        
        /**
         * Singleton
         *
         * @since 1.0.9
         * @static
         * @return NinukisFilterOutput
         */
        public static function get_instance() {
            if (!self::$instance) {
                self::$instance = new NinukisFilterOutput;
            }

            return self::$instance;
        }

        /**
         * Constructor for singleton
         *
         * @since 1.0.9
         * @return NinukisFilterOutput
         */
        private function __construct() {
            /* hook on 'init' action since some plugins will not work on
             * template_redirect */
            add_action('init', array($this, 'setup_filter_output'));
        }

        /**
         * rewrite outpute hook
         *
         * @since    1.0.9
         */
        public function setup_filter_output() {
            ob_start( array( $this, 'do_filter_output' ) );
        }
        
        /**
         * This function will be called by OB and will fire the 'ninukis_filter_output' filter.
         * 
         * @param type $html_content The generated html code
         * @return string
         */
        public function do_filter_output( $html_content ) {
            return apply_filters( 'ninukis_filter_output', $html_content );
	}
        

    }

}

