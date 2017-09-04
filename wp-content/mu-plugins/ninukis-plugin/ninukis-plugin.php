<?php
/**
 *
 * Pressidium WordPress Control Plugin
 *
 * @package   Ninukis Plugin
 * @author    Filip Slavik <filip@pressidium.com>
 * @license   GPL-2.0+
 * @link      https://pressidium.com
 *
 * @wordpress-plugin
 * Plugin Name:       Pressidium(R) Plugin
 * Plugin URI:        http://www.pressidium.com
 * Description:       Pressidium stuff
 * Version:           1.0.0
 * Author:            TechIO Ltd
 * Author URI:        http://www.pressidium.com
 * Text Domain:       ninukis-plugin-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
    die( 'Forbidden' );

if (!defined('NINUKIS_PLUGIN_DIR')) {
    define( 'NINUKIS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

require_once( NINUKIS_PLUGIN_DIR . '/includes/commons.php');
require_once( NINUKIS_PLUGIN_DIR . '/public/class-ninukis-plugin.php' );
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-api.php');
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-caching.php');
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-operations.php');
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-filter-output.php');
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-custom-rewrites.php');
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-cdn.php');
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-ssl.php');
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-cache-config.php');
require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-smacker.php');

/* instanciate classes that require it */

/* Caching Support */
$ninukis_caching_support = NinukisCaching::get_instance();

/* Support HTML content filtering */
$ninukis_filter_output = NinukisFilterOutput::get_instance();

/* Support custom rewrites */
$ninukis_custom_rewrite_support = NinukisCustomRewrites::get_instance();

/* Support CDN rewrites */
$ninukis_cdn_support = NinukisCDN::get_instance();

/* Support SSL fixes */
$ninukis_ssl_support = NinukisSSL::get_instance();

/* Set Caching Configuration */
$ninukis_cache_config = NinukisCacheConfig::get_instance();


// Is this WP-CLI ?  http://wp-cli.org/
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once( NINUKIS_PLUGIN_DIR . '/includes/class-ninukis-cli.php'       );
}
            

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
add_action( 'muplugins_loaded', array( 'Ninukis_Plugin', 'get_instance' ) );



/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/


if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( NINUKIS_PLUGIN_DIR . '/admin/class-ninukis-plugin-admin.php' );
	add_action( 'plugins_loaded', array( 'Ninukis_Plugin_Admin', 'get_instance' ) );

}

require_once( NINUKIS_PLUGIN_DIR . '/admin/class-ninukis-plugin-admin-smack.php' );
add_action( 'plugins_loaded', array( 'Ninukis_Plugin_Admin_Smack', 'get_instance' ) );
