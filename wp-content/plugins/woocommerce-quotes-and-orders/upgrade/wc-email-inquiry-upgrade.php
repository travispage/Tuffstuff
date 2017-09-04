<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

add_action('install_plugins_pre_plugin-information', array('WC_Email_Inquiry_Upgrade', 'display_changelog'));

add_filter("pre_set_site_transient_update_plugins", array('WC_Email_Inquiry_Upgrade', 'check_update'));

add_filter('plugins_api_result', array('WC_Email_Inquiry_Upgrade', 'make_compatibility'), 11, 3);

// Defined this plugin as external so that WordPress don't call to the WordPress.org Plugin Install API
add_filter( 'plugins_api', array('WC_Email_Inquiry_Upgrade', 'is_external'), 11, 3 );

class WC_Email_Inquiry_Upgrade
{

	public static function get_version_info( $cache=true ) {
		global $wc_ei_admin_init;

		$version_info = '';

		// Check if a3 License Manager plugin is installed
		global $a3_license_manager_tracking;
		if ( ! empty( $a3_license_manager_tracking ) && method_exists( $a3_license_manager_tracking, 'get_license_info' ) ) {
			$version_info = $a3_license_manager_tracking->get_license_info( get_option('a3rev_wc_orders_quotes_plugin'), get_option('a3rev_wc_orders_quotes_version'), $cache );
		}

		return $version_info;
    }
	
	public static function check_update($update_plugins_option){
        global $responsi_premium_addons;

        $new_version = false;
		if ( function_exists( 'responsi_premium_pack_check_pin' ) && responsi_premium_pack_check_pin() && $responsi_premium_addons && method_exists( $responsi_premium_addons, 'get_plugin_data' ) ) {
			$new_version = $responsi_premium_addons->get_plugin_data( get_option('a3rev_wc_orders_quotes_plugin'), 'woocommerce' );
		}

		if ( ! $new_version || ( is_array( $new_version ) && isset( $new_version['is_valid_key'] ) && $new_version['is_valid_key'] != 'valid' ) ) {
			$new_version = self::get_version_info();
		}

        if (!is_array($new_version))
            return $update_plugins_option;

        $plugin_name = WC_EMAIL_INQUIRY_NAME;
        if(empty($update_plugins_option->response[$plugin_name]))
            $update_plugins_option->response[$plugin_name] = new stdClass();

        //Empty response means that the key is invalid. Do not queue for upgrade
        if($new_version['is_valid_key'] != 'valid' || version_compare(get_option('a3rev_wc_orders_quotes_version'), $new_version['version'], '>=')){
            unset($update_plugins_option->response[$plugin_name]);
        }else{
            $update_plugins_option->response[$plugin_name]->url = "http://www.a3rev.com";
            $update_plugins_option->response[$plugin_name]->slug = get_option('a3rev_wc_orders_quotes_plugin');
            $update_plugins_option->response[$plugin_name]->package = $new_version["url"];
            $update_plugins_option->response[$plugin_name]->new_version = $new_version['version'];
			$update_plugins_option->response[$plugin_name]->upgrade_notice = $new_version['upgrade_notice'];
            $update_plugins_option->response[$plugin_name]->id = "0";
        }

        return $update_plugins_option;

    }
	
	//Displays current version details on Plugin's page
   	public static function display_changelog(){
        if($_REQUEST["plugin"] != get_option('a3rev_wc_orders_quotes_plugin'))
            return;

        $page_text = self::get_changelog();
        echo $page_text;

        exit;
    }

    public static function get_changelog(){
		$options = array(
			'method' 	=> 'POST', 
			'timeout' 	=> 8, 
			'body' 		=> array(
							'plugin' 		=> get_option('a3rev_wc_orders_quotes_plugin'),
							'domain_name'	=> $_SERVER['SERVER_NAME'],
							'address_ip'	=> $_SERVER['SERVER_ADDR'],
						) 
				);

        $raw_response = wp_remote_request(WC_EMAIL_INQUIRY_MANAGER_URL . "/changelog.php", $options);

        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
            $page_text = __('Error: ', 'wc_email_inquiry').' '.$raw_response->get_error_message();
        }else{
            $page_text = $raw_response['body'];
        }
        return stripslashes($page_text);
    }

	public static function make_compatibility( $info, $action, $args ) {
		global $wp_version;
		$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
		$our_plugin_name = get_option('a3rev_wc_orders_quotes_plugin');
		if ( $action == 'plugin_information' ) {
			if ( version_compare( $wp_version, '3.7', '<=' ) ) {
				if ( is_object( $args ) && isset( $args->slug ) && $args->slug == $our_plugin_name ) {
					$info->tested = $wp_version;
				}
			} elseif ( version_compare( $wp_version, '3.7', '>' ) && is_array( $args ) && isset( $args['body']['request'] ) ) {
				$request = unserialize( $args['body']['request'] );
				if ( $request->slug == $our_plugin_name ) {
					$info->tested = $wp_version;
				}
			}
		}
		return $info;
	}

	public static function is_external( $external, $action, $args ) {
		if ( 'plugin_information' == $action ) {
			if ( is_object( $args ) && isset( $args->slug ) &&  get_option('a3rev_wc_orders_quotes_plugin') == $args->slug ) {
				global $wp_version;
				$external = array(
					'tested'  => $wp_version
				);
				$external = (object) $external;
			}
		}
		return $external;
	}
}
?>
