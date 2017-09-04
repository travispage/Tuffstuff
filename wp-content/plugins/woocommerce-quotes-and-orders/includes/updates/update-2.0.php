<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

@set_time_limit(86400);
@ini_set("memory_limit","640M");

$wc_email_inquiry_global_settings = get_option('wc_email_inquiry_global_settings', array() );

$wc_email_inquiry_customize_email_popup = get_option('wc_email_inquiry_customize_email_popup', array() );
$wc_email_inquiry_global_settings = array_merge( $wc_email_inquiry_customize_email_popup, $wc_email_inquiry_global_settings );

$wc_email_inquiry_3rd_contact_form_settings = get_option('wc_email_inquiry_3rd_contact_form_settings', array() );
$wc_email_inquiry_global_settings = array_merge( $wc_email_inquiry_3rd_contact_form_settings, $wc_email_inquiry_global_settings );

update_option('wc_email_inquiry_global_settings', $wc_email_inquiry_global_settings);


$wc_email_inquiry_read_more_settings = get_option('wc_email_inquiry_read_more_settings', array() );

$wc_ei_read_more_hover_position_style = get_option('wc_ei_read_more_hover_position_style', array() );
$wc_email_inquiry_read_more_settings = array_merge( $wc_ei_read_more_hover_position_style, $wc_email_inquiry_read_more_settings );

$wc_ei_read_more_under_image_style = get_option('wc_ei_read_more_under_image_style', array() );
$wc_email_inquiry_read_more_settings = array_merge( $wc_ei_read_more_under_image_style, $wc_email_inquiry_read_more_settings );

update_option('wc_email_inquiry_read_more_settings', $wc_email_inquiry_read_more_settings);

global $wpdb;
$all_products_settings_custom = $wpdb->get_results( $wpdb->prepare("SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key=%s", '_wc_email_inquiry_settings_custom' ) );
if ( ! is_array( $all_products_settings_custom ) && count( $all_products_settings_custom ) > 0 ) {
	foreach ( $all_products_settings_custom as $product_settings_custom ) {
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->postmeta}( post_id, meta_key, meta_value ) VALUES( %d, %s, %s )", (int) $product_settings_custom->post_id, '_wc_ei_cart_price_custom', $product_settings_custom->meta_value ) );
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->postmeta}( post_id, meta_key, meta_value ) VALUES( %d, %s, %s )", (int) $product_settings_custom->post_id, '_wc_ei_settings_custom', $product_settings_custom->meta_value ) );
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->postmeta}( post_id, meta_key, meta_value ) VALUES( %d, %s, %s )", (int) $product_settings_custom->post_id, '_wc_ei_button_custom', $product_settings_custom->meta_value ) );
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->postmeta}( post_id, meta_key, meta_value ) VALUES( %d, %s, %s )", (int) $product_settings_custom->post_id, '_wc_ei_read_more_button_custom', $product_settings_custom->meta_value ) );
	}
	delete_post_meta_by_key('_wc_email_inquiry_settings_custom');
}