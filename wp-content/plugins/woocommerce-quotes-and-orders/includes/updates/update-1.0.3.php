<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb, $wp_roles;
if ( ! isset( $wp_roles ) ) {
	$wp_roles = new WP_Roles();
}
$roles = $wp_roles->get_names();
$wc_email_inquiry_user = esc_attr(get_option('wc_email_inquiry_user'));
if ($wc_email_inquiry_user == 'yes') {
	update_option('wc_email_inquiry_role_apply_hide_cart', (array) array_keys($roles));
	update_option('wc_email_inquiry_role_apply_hide_price', (array) array_keys($roles));
	update_option('wc_email_inquiry_role_apply_show_inquiry_button', (array) array_keys($roles));
}

$products_email_inquiry_settings_custom = $wpdb->get_results( "SELECT * FROM ".$wpdb->postmeta." WHERE meta_key='_wc_email_inquiry_settings_custom' AND meta_value != '' " );
if (is_array($products_email_inquiry_settings_custom) && count($products_email_inquiry_settings_custom) > 0) {
	foreach ($products_email_inquiry_settings_custom as $product_meta) {
		$wc_email_inquiry_settings_custom = unserialize($product_meta->meta_value);
		if (isset($wc_email_inquiry_settings_custom['wc_email_inquiry_user'])) {
			if ($wc_email_inquiry_settings_custom['wc_email_inquiry_user'] == 'yes') {
				$wc_email_inquiry_settings_custom['role_apply_hide_cart'] = (array) array_keys($roles);
				$wc_email_inquiry_settings_custom['role_apply_hide_price'] = (array) array_keys($roles);
				$wc_email_inquiry_settings_custom['role_apply_show_inquiry_button'] = (array) array_keys($roles);
			} else {
				$wc_email_inquiry_settings_custom['role_apply_hide_cart'] = array();
				$wc_email_inquiry_settings_custom['role_apply_hide_price'] = array();
				$wc_email_inquiry_settings_custom['role_apply_show_inquiry_button'] = array();
			}
			
			if ($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt'] == 'yes') {
				$wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price'] = 'yes';
			} else {
				$wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price'] = 'no';
			}
			update_post_meta($product_meta->post_id, '_wc_email_inquiry_settings_custom', (array) $wc_email_inquiry_settings_custom);
		}
	}
}