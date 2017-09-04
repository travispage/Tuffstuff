<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Send Quote Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		WC_Email_Inquiry_Send_Quote_Email
 * @version		2.1.0
 * @package		WooCommerce/Classes/Emails
 * @author 		a3rev
 * @extends 	WC_Email
 */
class WC_Email_Inquiry_Send_Quote_Email {
	
	public static $plain_search = array(
        "/\r/",                                  // Non-legal carriage return
        '/&(nbsp|#160);/i',                      // Non-breaking space
        '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i',
		                                         // Double quotes
        '/&(apos|rsquo|lsquo|#8216|#8217);/i',   // Single quotes
        '/&gt;/i',                               // Greater-than
        '/&lt;/i',                               // Less-than
        '/&(amp|#38);/i',                        // Ampersand
        '/&(copy|#169);/i',                      // Copyright
        '/&(trade|#8482|#153);/i',               // Trademark
        '/&(reg|#174);/i',                       // Registered
        '/&(mdash|#151|#8212);/i',               // mdash
        '/&(ndash|minus|#8211|#8722);/i',        // ndash
        '/&(bull|#149|#8226);/i',                // Bullet
        '/&(pound|#163);/i',                     // Pound sign
        '/&(euro|#8364);/i',                     // Euro sign
        '/&[^&;]+;/i',                           // Unknown/unhandled entities
        '/[ ]{2,}/'                              // Runs of spaces, post-handling
    );
	
	
	public static $plain_replace = array(
        '',                                     // Non-legal carriage return
        ' ',                                    // Non-breaking space
        '"',                                    // Double quotes
        "'",                                    // Single quotes
        '>',
        '<',
        '&',
        '(c)',
        '(tm)',
        '(R)',
        '--',
        '-',
        '*',
        '£',
        'EUR',                                  // Euro sign. € ?
        '',                                     // Unknown/unhandled entities
        ' '                                     // Runs of spaces, post-handling
    );
	
	public static function get_email_content_html( $email_args = array() ) {
		ob_start();
		global $wc_ei_quotes_mode_send_quote_email_settings;
		extract($email_args);
		include ( WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $wc_ei_quotes_mode_send_quote_email_settings->template_html ) );
		return ob_get_clean();
	}
	
	public static function get_email_content_plain( $email_args = array() ) {
		ob_start();
		global $wc_ei_quotes_mode_send_quote_email_settings;
		extract($email_args);
		include ( WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $wc_ei_quotes_mode_send_quote_email_settings->template_plain ) );
		return ob_get_clean();
	}
	
	public static function get_email_content( $email_args=array() ) {
		global $wc_email_inquiry_quote_send_quote_email_settings;
		
		$email_content = '';
		
		if ( $wc_email_inquiry_quote_send_quote_email_settings['email_type'] == 'plain' ) {
			$email_content = preg_replace( WC_Email_Inquiry_Send_Quote_Email::$plain_search, WC_Email_Inquiry_Send_Quote_Email::$plain_replace, strip_tags( WC_Email_Inquiry_Send_Quote_Email::get_email_content_plain($email_args) ) );
		} else {
			$email_content = WC_Email_Inquiry_Send_Quote_Email::style_inline( WC_Email_Inquiry_Send_Quote_Email::get_email_content_html($email_args) );
		}
		
		return $email_content;
	}
	
	public static function get_header() {		
		return "Content-Type: " . WC_Email_Inquiry_Send_Quote_Email::get_content_type() . "\r\n";
	}

	public static function get_content_type() {
		global $wc_email_inquiry_quote_send_quote_email_settings;
		
		$email_type = $wc_email_inquiry_quote_send_quote_email_settings['email_type'];
		
		switch ( $email_type ) {
			case "html" :
				return 'text/html';
			case "multipart" :
				return 'multipart/alternative';
			default :
				return 'text/plain';
		}
	}
	
	public static function style_inline( $content ) {

		if ( ! class_exists( 'DOMDocument' ) )
			return $content;

		$dom = new DOMDocument();
		@$dom->loadHTML( $content );

		$nodes = $dom->getElementsByTagName('img');

		foreach( $nodes as $node )
			if ( ! $node->hasAttribute( 'style' ) )
				$node->setAttribute( "style", "display:inline; border:none; font-size:14px; font-weight:bold; height:auto; line-height:100%; outline:none; text-decoration:none; text-transform:capitalize;" );

		$nodes_h1 = $dom->getElementsByTagName('h1');
		$nodes_h2 = $dom->getElementsByTagName('h2');
		$nodes_h3 = $dom->getElementsByTagName('h3');

		foreach( $nodes_h1 as $node )
			if ( ! $node->hasAttribute( 'style' ) )
				$node->setAttribute( "style", "color: " . get_option( 'woocommerce_email_text_color' ) . "; display:block; font-family:Arial; font-size:34px; font-weight:bold; margin-top: 10px; margin-right:0; margin-bottom:10px; margin-left:0; text-align:left; line-height: 150%;" );

		foreach( $nodes_h2 as $node )
			if ( ! $node->hasAttribute( 'style' ) )
				$node->setAttribute( "style", "color: " . get_option( 'woocommerce_email_text_color' ) . "; display:block; font-family:Arial; font-size:30px; font-weight:bold; margin-top: 10px; margin-right:0; margin-bottom:10px; margin-left:0; text-align:left; line-height: 150%;" );

		foreach( $nodes_h3 as $node )
			if ( ! $node->hasAttribute( 'style' ) )
				$node->setAttribute( "style", "color: " . get_option( 'woocommerce_email_text_color' ) . "; display:block; font-family:Arial; font-size:26px; font-weight:bold; margin-top: 10px; margin-right:0; margin-bottom:10px; margin-left:0; text-align:left; line-height: 150%;" );

		$nodes = $dom->getElementsByTagName('a');

		foreach( $nodes as $node )
			if ( ! $node->hasAttribute( 'style' ) )
				$node->setAttribute( "style", "color: " . get_option( 'woocommerce_email_text_color' ) . "; font-weight:normal; text-decoration:underline;" );

		$content = $dom->saveHTML();

		return $content;
	}
}