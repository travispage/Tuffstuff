<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wc_email_inquiry_rules_roles_settings = get_option( 'wc_email_inquiry_rules_roles_settings', array() );
$wc_email_inquiry_global_settings = get_option( 'wc_email_inquiry_global_settings', array() );
$wc_email_inquiry_read_more_settings = get_option( 'wc_email_inquiry_read_more_settings', array() );
$wc_email_inquiry_customize_email_button = get_option( 'wc_email_inquiry_customize_email_button', array('inquiry_single_only' => 'no') );

$wc_email_inquiry_global_settings['show_button'] = $wc_email_inquiry_rules_roles_settings['show_button'];
$wc_email_inquiry_global_settings['show_button_after_login'] = $wc_email_inquiry_rules_roles_settings['show_button_after_login'];
$wc_email_inquiry_global_settings['role_apply_show_inquiry_button'] = $wc_email_inquiry_rules_roles_settings['role_apply_show_inquiry_button'];
$wc_email_inquiry_global_settings['inquiry_single_only'] = $wc_email_inquiry_customize_email_button['inquiry_single_only'];

if ( isset( $wc_email_inquiry_rules_roles_settings['show_read_more_button_before_login'] ) )
	$wc_email_inquiry_read_more_settings['show_read_more_button_before_login'] = $wc_email_inquiry_rules_roles_settings['show_read_more_button_before_login'];
	
if ( isset( $wc_email_inquiry_rules_roles_settings['show_read_more_button_after_login'] ) )
	$wc_email_inquiry_read_more_settings['show_read_more_button_after_login'] = $wc_email_inquiry_rules_roles_settings['show_read_more_button_after_login'];

if ( isset( $wc_email_inquiry_rules_roles_settings['role_apply_show_read_more'] ) )
	$wc_email_inquiry_read_more_settings['role_apply_show_read_more'] = $wc_email_inquiry_rules_roles_settings['role_apply_show_read_more'];

update_option( 'wc_email_inquiry_global_settings', $wc_email_inquiry_global_settings );
update_option( 'wc_email_inquiry_read_more_settings', $wc_email_inquiry_read_more_settings );