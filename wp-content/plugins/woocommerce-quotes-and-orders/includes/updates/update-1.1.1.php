<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wc_email_inquiry_rules_roles_settings = get_option( 'wc_email_inquiry_rules_roles_settings', array() );
$wc_email_inquiry_global_settings = get_option( 'wc_email_inquiry_global_settings', array() );
$wc_email_inquiry_email_options = get_option( 'wc_email_inquiry_email_options', array() );
$wc_email_inquiry_3rd_contactforms_settings = get_option( 'wc_email_inquiry_3rd_contactforms_settings', array() );
$wc_email_inquiry_customize_email_popup = get_option( 'wc_email_inquiry_customize_email_popup', array() );
$wc_email_inquiry_customize_email_button = get_option( 'wc_email_inquiry_customize_email_button', array() );

$wc_email_inquiry_rules_roles_settings_new = array_merge( $wc_email_inquiry_rules_roles_settings, array( 
	'manual_quote_rule'					=> ( $wc_email_inquiry_rules_roles_settings['quote_mode_rule'] == 'manual' ) ? 'yes' : 'no',
	'auto_quote_rule'					=> ( $wc_email_inquiry_rules_roles_settings['quote_mode_rule'] == 'auto' ) ? 'yes' : 'no',
	'add_to_order_rule'					=> $wc_email_inquiry_rules_roles_settings['add_to_order'],
) );

// Process for Auto Quote rule
$wc_email_inquiry_rules_roles_settings_new['role_apply_auto_quote'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
	
// Process for Add to Order rule
$wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'] = $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'];
$wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
$wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] );
	
// Process for Hide Cart rule
$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'] = $wc_email_inquiry_rules_roles_settings['role_apply_hide_cart'];
$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] );
$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] );
	
// Process for Hide Price rule
$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_price'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_price'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] );

update_option( 'wc_email_inquiry_rules_roles_settings', $wc_email_inquiry_rules_roles_settings_new );

$wc_email_inquiry_contact_form_settings = array(
	
	'inquiry_email_from_name'			=> $wc_email_inquiry_email_options['inquiry_email_from_name'],
	'inquiry_email_from_address'		=> $wc_email_inquiry_email_options['inquiry_email_from_address'],
	'inquiry_send_copy'					=> $wc_email_inquiry_email_options['inquiry_send_copy'],
	'inquiry_email_to'					=> $wc_email_inquiry_email_options['inquiry_email_to'],
	'inquiry_email_cc'					=> $wc_email_inquiry_email_options['inquiry_email_cc'],
	
	'defaul_product_page_open_form_type'	=> $wc_email_inquiry_global_settings['defaul_product_page_open_form_type'],
	'defaul_category_page_open_form_type'	=> $wc_email_inquiry_global_settings['defaul_category_page_open_form_type'],
);
update_option( 'wc_email_inquiry_contact_form_settings', $wc_email_inquiry_contact_form_settings );

$wc_email_inquiry_3rd_contact_form_settings = array(
	'contact_form_shortcode'			=> $wc_email_inquiry_3rd_contactforms_settings['contact_form_shortcode'],
	'product_page_open_form_type'		=> $wc_email_inquiry_3rd_contactforms_settings['product_page_open_form_type'],
	'category_page_open_form_type'		=> $wc_email_inquiry_3rd_contactforms_settings['category_page_open_form_type'],
);
update_option( 'wc_email_inquiry_3rd_contact_form_settings', $wc_email_inquiry_3rd_contact_form_settings );

$wc_email_inquiry_global_settings = array(
	'inquiry_popup_type'				=> $wc_email_inquiry_customize_email_popup['inquiry_popup_type'],
	'enable_3rd_contact_form_plugin'	=> $wc_email_inquiry_3rd_contactforms_settings['enable_3rd_contact_form_plugin'],
);
update_option( 'wc_email_inquiry_global_settings', $wc_email_inquiry_global_settings );

$wc_email_inquiry_customize_email_button_new = array_merge( $wc_email_inquiry_customize_email_button, array( 
	'inquiry_button_type'				=> $wc_email_inquiry_global_settings['inquiry_button_type'],
	'inquiry_button_position'			=> $wc_email_inquiry_global_settings['inquiry_button_position'],
	'inquiry_button_margin_top'			=> $wc_email_inquiry_global_settings['inquiry_button_padding_top'],
	'inquiry_button_margin_bottom'		=> $wc_email_inquiry_global_settings['inquiry_button_padding_bottom'],
	'inquiry_single_only'				=> $wc_email_inquiry_global_settings['inquiry_single_only'],
	
	'inquiry_button_border'				=>  array(
				'width'		=> $wc_email_inquiry_customize_email_button['inquiry_button_border_size'],
				'style'		=> $wc_email_inquiry_customize_email_button['inquiry_button_border_style'],
				'color'		=> $wc_email_inquiry_customize_email_button['inquiry_button_border_colour'],
				'corner'	=> $wc_email_inquiry_customize_email_button['inquiry_button_rounded_corner'],
				'rounded_value'	=> $wc_email_inquiry_customize_email_button['inquiry_button_rounded_value'],
	),
	'inquiry_button_font'				=> array(
				'size'		=> $wc_email_inquiry_customize_email_button['inquiry_button_font_size'],
				'face'		=> $wc_email_inquiry_customize_email_button['inquiry_button_font'],
				'style'		=> $wc_email_inquiry_customize_email_button['inquiry_button_font_style'],
				'color'		=> $wc_email_inquiry_customize_email_button['inquiry_button_font_colour'],
	),
) );
update_option( 'wc_email_inquiry_customize_email_button', $wc_email_inquiry_customize_email_button_new );

$wc_email_inquiry_customize_email_popup_new = array_merge( $wc_email_inquiry_customize_email_popup, array( 
	'inquiry_contact_popup_text_font'	=> array(
				'size'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_popup_text_font_size'],
				'face'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_popup_text_font'],
				'style'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_popup_text_font_style'],
				'color'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_popup_text_font_colour'],
	),
	
	'inquiry_contact_button_border'		=>  array(
				'width'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_border_size'],
				'style'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_border_style'],
				'color'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_border_colour'],
				'corner'	=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_rounded_corner'],
				'rounded_value'	=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_rounded_value'],
	),
	'inquiry_contact_button_font'		=> array(
				'size'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_font_size'],
				'face'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_font'],
				'style'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_font_style'],
				'color'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_font_colour'],
	),
) );
update_option( 'wc_email_inquiry_customize_email_popup', $wc_email_inquiry_customize_email_popup_new );