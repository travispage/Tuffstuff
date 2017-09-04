<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class a3_License_Manager_Plugin_Installer
{

	public $plugin = 'a3-license-manager';

	public function __construct() {
		add_action( 'update-custom_install-a3-license-manager-plugin', array( $this, 'install_plugin' ) );

		add_action( 'admin_notices', array( $this, 'install_notice' ), 11 );
	}

	public function install_notice() {
		if ( function_exists( 'responsi_premium_pack_check_pin' ) && responsi_premium_pack_check_pin() ) return;

		// Detected if a3 License Manager plugin is existed on current site or no
		$installed_plugin = null;
		if ( file_exists( WP_PLUGIN_DIR . '/' . $this->plugin ) || is_dir( WP_PLUGIN_DIR . '/' . $this->plugin ) ) {
			$installed_plugin = get_plugins('/' . $this->plugin );
		}

		if ( ! empty( $installed_plugin ) ) {
			// Activate it
			$plugin_slug = $this->plugin . '/a3-license-manager.php';
			$is_actived = is_plugin_active( $plugin_slug );
			if ( ! $is_actived ) {
				$activate_url = add_query_arg( array(
					'action' 		=> 'activate',
					'plugin'		=> $plugin_slug,
				), self_admin_url( 'plugins.php' ) );
				$activate_url = esc_url( wp_nonce_url( $activate_url, 'activate-plugin_' . $plugin_slug ) );
	?>
    	<div class="error below-h2" style="display:block !important; margin-left:2px;">
    		<p><?php echo sprintf( __( 'You need to activate the <a title="" href="%s" target="_parent">a3 License Manager</a> plugin for get auto upgrade when have new version.' , 'wc_email_inquiry' ), $activate_url ); ?></p>
    	</div>
    <?php
			}
		} else {
			// Install it
			$install_url = add_query_arg( array(
				'action' 		=> 'install-a3-license-manager-plugin',
				'plugin'		=> $this->plugin,
			), self_admin_url( 'update.php' ) );
			$install_url = esc_url( wp_nonce_url( $install_url, 'install-a3-license-manager-plugin_' . $this->plugin ) );
	?>
    	<div class="error below-h2" style="display:block !important; margin-left:2px;">
    		<p><?php echo sprintf( __( 'You need to install and activate the <a title="" href="%s" target="_parent">a3 License Manager</a> plugin for get auto upgrade when have new version.' , 'wc_email_inquiry' ), $install_url ); ?></p>
    	</div>
    <?php
		}
	}

	public function install_plugin() {
		$plugin               = isset($_REQUEST['plugin']) ? trim($_REQUEST['plugin']) : '';
		$action               = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

		if ( ! current_user_can('install_plugins') )
			wp_die( __( 'You do not have sufficient permissions to install plugins on this site.', 'wc_email_inquiry' ) );

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php'; //for plugins_api..

		check_admin_referer('install-a3-license-manager-plugin_' . $plugin);

		$download_url = 'https://s3.amazonaws.com/a3_plugins/published/a3+License+Manager/a3-license-manager.zip';

		$api = plugins_api('plugin_information', array('slug' => $plugin, 'fields' => array('sections' => false) ) ); //Save on a bit of bandwidth.

		$api                            = new stdClass();
		$api->name                      = __( 'a3 License Manager', 'wc_email_inquiry' );
		$api->slug                      = $plugin;
		$api->version                   = '1.0.0';
		$api->author                    = __( 'a3 Plugins', 'wc_email_inquiry' );
		$api->screenshot_url            = '';
		$api->homepage                  = 'http://a3rev.com';
		$api->download_link             = $download_url;
		$api->a3_license_manager_plugin = true;

		$title        = __('a3 License Manager Install', 'wc_email_inquiry' );
		$parent_file  = 'plugins.php';
		$submenu_file = 'plugin-install.php';
		load_template(ABSPATH . 'wp-admin/admin-header.php');

		$title = sprintf( __('Installing a3 License Manager Plugin: %s', 'wc_email_inquiry' ), $api->name . ' ' . $api->version );
		$nonce = 'install-a3-license-manager-plugin_' . $plugin;
		$url   = 'update.php?action=install-a3-license-manager-plugin&plugin=' . urlencode( $plugin );
		if ( isset($_GET['from']) ) {
			$url   .= '&from=' . urlencode(stripslashes($_GET['from']));
		}
		$type  = 'web'; //Install plugin type, From Web or an Upload.

		$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
		$upgrader->install($api->download_link);

		load_template(ABSPATH . 'wp-admin/admin-footer.php');
	}
}

global $a3_license_manager_plugin_installer;
$a3_license_manager_plugin_installer = new a3_License_Manager_Plugin_Installer();

?>