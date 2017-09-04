<?php
/**
 * WC Email Inquiry 3RD Contact Form
 *
 * Table Of Contents
 *
 * check_enable_3rd_contact_form()
 * show_inquiry_form()
 */
 
// Replace the template file from plugin
add_filter('template_include', array('WC_Email_Inquiry_3RD_ContactForm_Functions', 'template_loader') );

// Plugin loaded
add_action( 'plugins_loaded', array( 'WC_Email_Inquiry_3RD_ContactForm_Functions', 'plugins_loaded' ), 8 );

// Custom Rewrite Rules
//add_action('init', array('WC_Email_Inquiry_3RD_ContactForm_Functions', 'custom_rewrite_rule'), 1 );
add_action( 'init', array( 'WC_Email_Inquiry_3RD_ContactForm_Functions', 'add_endpoints' ) );

class WC_Email_Inquiry_3RD_ContactForm_Functions 
{	
	public static $plugin_prefix = 'wc_email_inquiry_';
	public static $page_slug_1 = 'email-inquiry-form';
	public static $page_shortcode_1 = '[wc_email_inquiry_page]';
	public static $page_option_key_1 = 'wc_email_inquiry_page_id';
	public static $endpoint_query_vars = array( 
			'product-id' => 'product-id'
		);
	
	public static function install_3rd_contactform() {
		$page_id = WC_Email_Inquiry_Functions::create_page( self::$page_slug_1, '', __('Email Inquiry Form', 'wc_email_inquiry'), self::$page_shortcode_1 );
		update_option( self::$page_option_key_1, $page_id);	
		WC_Email_Inquiry_Functions::auto_create_page_for_wpml( $page_id, self::$page_slug_1, __('Email Inquiry Form', 'wc_email_inquiry'), self::$page_shortcode_1 );
	}
	
	public static function plugins_loaded() {
		global $wc_email_inquiry_page_id;
		global $wpdb;
		$wc_email_inquiry_page_id = WC_Email_Inquiry_Functions::get_page_id_from_shortcode( 'wc_email_inquiry_page' , 'wc_email_inquiry_page_id');
	}
	
	public static function template_loader( $template ) {
		$wc_email_inquiry_page_id = WC_Email_Inquiry_Functions::get_page_id_from_shortcode( 'wc_email_inquiry_page' , 'wc_email_inquiry_page_id');
		global $post;

		if ( $post && $wc_email_inquiry_page_id == $post->ID && isset( $_GET['open-type'] ) && $_GET['open-type'] == 'popup' ) {

			$file 	= '3rd-contact-form-page.php';
			$find[] = $file;
			$find[] = apply_filters( 'woocommerce_template_url', 'woocommerce/' ) . $file;
			
			$template = locate_template( $find );
			if ( ! $template ) $template = WC_EMAIL_INQUIRY_FILE_PATH . '/templates/' . $file;

		}
	
		return $template;
	}
	
	/**
	 * Add endpoints for query vars
	 */
	public static function add_endpoints() {
		foreach ( WC_Email_Inquiry_3RD_ContactForm_Functions::$endpoint_query_vars as $key => $var )
			add_rewrite_endpoint( $var, EP_PAGES );
		
		if ( !is_admin() ) {	
			add_filter( 'query_vars', array( 'WC_Email_Inquiry_3RD_ContactForm_Functions', 'add_query_vars' ), 0 );
			add_action( 'parse_request', array( 'WC_Email_Inquiry_3RD_ContactForm_Functions', 'parse_request' ), 0 );
		}
	}
	
	public static function add_query_vars( $vars ) {
		foreach ( WC_Email_Inquiry_3RD_ContactForm_Functions::$endpoint_query_vars as $key => $var )
			$vars[] = $key;

		return $vars;
	}
	
	/**
	 * Parse the request and look for query vars - endpoints may not be supported
	 */
	public static function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( WC_Email_Inquiry_3RD_ContactForm_Functions::$endpoint_query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = $_GET[ $var ];
			}

			elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}
	
	public static function add_query_vars_old($aVars) {
		$aVars[] = "product-id";
		return $aVars;
	}
	
	public static function add_rewrite_rules($aRules) {
		//var_dump($_SERVER);
		$wc_email_inquiry_page_id = WC_Email_Inquiry_Functions::get_page_id_from_shortcode( 'wc_email_inquiry_page' , 'wc_email_inquiry_page_id');
		//$wc_email_inquiry_page_id = get_option( self::$page_option_key_1 );
		$email_inquiry_page = get_page( $wc_email_inquiry_page_id );
		if ( !empty($email_inquiry_page) ) {
			$email_inquiry_page_slug = $email_inquiry_page->post_name;
			if (stristr($_SERVER['REQUEST_URI'], $email_inquiry_page_slug) !== FALSE) {
				$position = strpos($_SERVER['REQUEST_URI'], $email_inquiry_page_slug);
				$new_url = substr($_SERVER['REQUEST_URI'], ($position + strlen($email_inquiry_page_slug.'/') ) );
				$parameters_array = explode("/", $new_url);
				
				if (is_array($parameters_array) && count($parameters_array) > 1) {
					$array_key = array();
					$array_value = array();
					$number = 0;
					foreach ($parameters_array as $parameter) {
						$number++;
						if (trim($parameter) == '') continue;
						if ($number%2 == 0) $array_value[] = $parameter;
						else $array_key[] = $parameter;
					}
					if (count($array_key) > 0 && count($array_value) > 0 ) {
						$rewrite_rule = '';
						$original_url = '';
						$number_matches = 0;
						foreach ($array_key as $key) {
							$number_matches++;
							$rewrite_rule .= $key.'/([^/]*)/';
							$original_url .= '&'.$key.'=$matches['.$number_matches.']';
						}
						
						$aNewRules = array($email_inquiry_page_slug.'/'.$rewrite_rule.'?$' => 'index.php?pagename='.$email_inquiry_page_slug.$original_url);
						$aRules = $aNewRules + $aRules;
						
					}
				}
			}
		}
		return $aRules;
	} 
	
	public static function custom_rewrite_rule() {
		// BEGIN rewrite
		// hook add_query_vars function into query_vars
		$wc_email_inquiry_page_id = WC_Email_Inquiry_Functions::get_page_id_from_shortcode( 'wc_email_inquiry_page' , 'wc_email_inquiry_page_id');
		add_filter('query_vars', array('WC_Email_Inquiry_3RD_ContactForm_Functions', 'add_query_vars'), 101 );
	
		add_filter('rewrite_rules_array', array('WC_Email_Inquiry_3RD_ContactForm_Functions', 'add_rewrite_rules'), 101 );
		
		//$wc_email_inquiry_page_id = get_option( self::$page_option_key_1 );
		$email_inquiry_page = get_page($wc_email_inquiry_page_id);
		if ( !empty($email_inquiry_page) ) {
			$email_inquiry_page_slug = $email_inquiry_page->post_name;
			if ( stristr($_SERVER['REQUEST_URI'], $email_inquiry_page_slug) !== FALSE ) {
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
			}
		}
		// END rewrite
	}
	
	public static function check_enable_3rd_contact_form ($product_id=0) {
		global $wc_email_inquiry_global_settings;
		
		if ( $wc_email_inquiry_global_settings['enable_3rd_contact_form_plugin'] == 'yes' ) return true;
		
		return false;
	}
	
	public static function wc_email_inquiry_page() {
		// Don't show content for shortcode on Dashboard, still support for admin ajax
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) return;

		global $wp_query;
		$product_id = 0;
		if ( isset( $wp_query->query_vars['product-id'] ) )
			$product_id = $wp_query->query_vars['product-id'];
		
		return WC_Email_Inquiry_3RD_ContactForm_Functions::show_inquiry_form( $product_id, 1 ); 
	}
	
	public static function show_inquiry_form ( $product_id=0, $show_product_info = 0, $open_type = 'full') {
		global $wc_email_inquiry_contact_form_settings, $wc_email_inquiry_global_settings;
		
		$inquiry_form = '';
		
		if ($product_id == 0 || $product_id == '') return;
		
		$wc_ei_settings_custom = get_post_meta( $product_id, '_wc_ei_settings_custom', true);
			
		if (!isset($wc_ei_settings_custom['contact_form_shortcode'])) $contact_form_shortcode = trim($wc_email_inquiry_global_settings['contact_form_shortcode']);
		else $contact_form_shortcode = trim(esc_attr($wc_ei_settings_custom['contact_form_shortcode']));
		
		if ( trim($contact_form_shortcode) == '' ) $contact_form_shortcode = trim($wc_email_inquiry_global_settings['contact_form_shortcode']);
		
		if ( trim($contact_form_shortcode) == '' ) return;
		
		$contact_form_shortcode = htmlspecialchars_decode( $contact_form_shortcode );
		
		$shortcode_slipt = explode( "]", $contact_form_shortcode );
		$contact_form_shortcode = $shortcode_slipt[0].' product_id="'.$product_id.'"'.']'.$shortcode_slipt[1];
		if ( '' != trim( $wc_email_inquiry_global_settings['custom_contact_form_heading'] ) ) {
			$inquiry_form .= '<div class="wc_email_inquiry_custom_form_heading">'.trim( $wc_email_inquiry_global_settings['custom_contact_form_heading'] ).'</div>';
		}
		if ( $show_product_info == 1) {
			$inquiry_form .= '<div class="wc_email_inquiry_custom_form_container">';
			if ( $open_type == 'popup' ) {
				$inquiry_form .= '<div class="wc_email_inquiry_image_container">'.WC_Email_Inquiry_Functions::get_product_information( $product_id, 0, 200, 200, 'wc_email_inquiry_product_image_large').'</div><div class="wc_email_inquiry_form_container">';
			} else {
				$inquiry_form .= '<div class="wc_email_inquiry_image_container" style="position: absolute; top: 0px; left: 0px; width:210px;">'.WC_Email_Inquiry_Functions::get_product_information( $product_id, 0, 200, 200, 'wc_email_inquiry_product_image_large').'</div><div class="wc_email_inquiry_form_container" style="margin-left:220px;">';	
			}
		}
		
		// Check if shortcode is from Contact Form 7
		if ( stristr($contact_form_shortcode, '[contact-form ') !== false || stristr($contact_form_shortcode, '[contact-form-7 ') !== false ) {
			$inquiry_form .= WC_Email_Inquiry_ContactForm7_Addon::show_inquiry_form_from_shortcode($contact_form_shortcode, $product_id);
		} elseif ( stristr($contact_form_shortcode, '[gravityform ') !== false ) {
			$inquiry_form .= WC_Email_Inquiry_GravityForms_Addon::show_inquiry_form_from_shortcode($contact_form_shortcode, $product_id);
		} else {
			$inquiry_form .= do_shortcode($contact_form_shortcode);
		}
		if ( $show_product_info == 1) {
			$inquiry_form .= '</div></div>';	
		}
		
		return $inquiry_form;
	}
}
?>