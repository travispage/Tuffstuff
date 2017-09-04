<?php
/**
 * WC Email Inquiry Functions
 *
 * Table Of Contents
 *
 * plugins_loaded()
 * check_hide_add_cart_button()
 * check_hide_price()
 * check_add_email_inquiry_button()
 * check_add_email_inquiry_button_on_shoppage()
 * reset_products_to_global_settings()
 * email_inquiry()
 * get_from_address()
 * get_from_name()
 * get_content_type()
 */
class WC_Email_Inquiry_Functions 
{	
	
	/** 
	 * Set global variable when plugin loaded
	 */
	
	public static function check_hide_add_cart_button ($product_id) {
		global $wc_email_inquiry_rules_roles_settings;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ($apply_request_a_quote) return false;
		elseif ($apply_add_to_order) return false;
		
		$wc_ei_cart_price_custom = get_post_meta( $product_id, '_wc_ei_cart_price_custom', true);
		
		if (!isset($wc_ei_cart_price_custom['wc_email_inquiry_hide_addcartbt'])) $wc_email_inquiry_hide_addcartbt = $wc_email_inquiry_rules_roles_settings['hide_addcartbt'] ;
		else $wc_email_inquiry_hide_addcartbt = esc_attr($wc_ei_cart_price_custom['wc_email_inquiry_hide_addcartbt']);
		
		// dont hide add to cart button if setting is not checked and not logged in users
		if ($wc_email_inquiry_hide_addcartbt == 'no' && !is_user_logged_in() ) return false;
		
		// hide add to cart button if setting is checked and not logged in users
		if ($wc_email_inquiry_hide_addcartbt != 'no' &&  !is_user_logged_in()) return true;
		
		if (!isset($wc_ei_cart_price_custom['wc_email_inquiry_hide_addcartbt_after_login'])) $wc_email_inquiry_hide_addcartbt_after_login = $wc_email_inquiry_rules_roles_settings['hide_addcartbt_after_login'] ;
		else $wc_email_inquiry_hide_addcartbt_after_login = esc_attr($wc_ei_cart_price_custom['wc_email_inquiry_hide_addcartbt_after_login']);		

		// don't hide add to cart if for logged in users is deacticated
		if ( $wc_email_inquiry_hide_addcartbt_after_login != 'yes' ) return false;
		
		if (!isset($wc_ei_cart_price_custom['role_apply_hide_cart'])) {
			$role_apply_hide_cart = (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_cart'];
		} else {
			$role_apply_hide_cart = (array) $wc_ei_cart_price_custom['role_apply_hide_cart'];
			$role_apply_hide_cart = array_diff ( (array) $role_apply_hide_cart, (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] ) ;
			$role_apply_hide_cart = array_diff ( (array) $role_apply_hide_cart, (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] ) ;
			$role_apply_hide_cart = array_diff ( (array) $role_apply_hide_cart, (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] ) ;
		}
		
		$user_login = wp_get_current_user();
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, $role_apply_hide_cart );
			
			// hide add to cart button if current user role in list apply role
			if ( is_array( $role_existed ) && count( $role_existed ) > 0 ) return true;
		}
		return false;
		
	}
	
	public static function check_hide_price ($product_id) {
		global $wc_email_inquiry_rules_roles_settings;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ($apply_request_a_quote) return true;
		elseif ($apply_add_to_order) return false;
		
		$wc_ei_cart_price_custom = get_post_meta( $product_id, '_wc_ei_cart_price_custom', true);
			
		if (!isset($wc_ei_cart_price_custom['wc_email_inquiry_hide_price'])) $wc_email_inquiry_hide_price = $wc_email_inquiry_rules_roles_settings['hide_price'];
		else $wc_email_inquiry_hide_price = esc_attr($wc_ei_cart_price_custom['wc_email_inquiry_hide_price']);
		
		// dont hide price if setting is not checked and not logged in users
		if ($wc_email_inquiry_hide_price == 'no' && !is_user_logged_in() ) return false;
		
		// alway hide price if setting is checked and not logged in users
		if ($wc_email_inquiry_hide_price != 'no' && !is_user_logged_in()) return true;
		
		if (!isset($wc_ei_cart_price_custom['wc_email_inquiry_hide_price_after_login'])) $wc_email_inquiry_hide_price_after_login = $wc_email_inquiry_rules_roles_settings['hide_price_after_login'] ;
		else $wc_email_inquiry_hide_price_after_login = esc_attr($wc_ei_cart_price_custom['wc_email_inquiry_hide_price_after_login']);		

		// don't hide price if for logged in users is deacticated
		if ( $wc_email_inquiry_hide_price_after_login != 'yes' ) return false;
		
		if (!isset($wc_ei_cart_price_custom['role_apply_hide_price'])) {
			$role_apply_hide_price = (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_price'];
		} else {
			$role_apply_hide_price = (array) $wc_ei_cart_price_custom['role_apply_hide_price'];
			$role_apply_hide_price = array_diff ( (array) $role_apply_hide_price, (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] ) ;
		}
		
		$user_login = wp_get_current_user();		
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, $role_apply_hide_price );
			
			// hide price if current user role in list apply role
			if ( is_array( $role_existed ) && count( $role_existed ) > 0 ) return true;
		}
		
		return false;
	}
	
	public static function check_add_email_inquiry_button ($product_id) {
		global $wc_email_inquiry_global_settings;
		$wc_ei_settings_custom = get_post_meta( $product_id, '_wc_ei_settings_custom', true);
			
		if (!isset($wc_ei_settings_custom['wc_email_inquiry_show_button'])) $wc_email_inquiry_show_button = $wc_email_inquiry_global_settings['show_button'];
		else $wc_email_inquiry_show_button = esc_attr($wc_ei_settings_custom['wc_email_inquiry_show_button']);
		
		// dont show email inquiry button if setting is not checked and not logged in users
		if ($wc_email_inquiry_show_button == 'no' && !is_user_logged_in() ) return false;
		
		// alway show email inquiry button if setting is checked and not logged in users
		if ($wc_email_inquiry_show_button != 'no' && !is_user_logged_in()) return true;
		
		if (!isset($wc_ei_settings_custom['wc_email_inquiry_show_button_after_login'])) $wc_email_inquiry_show_button_after_login = $wc_email_inquiry_global_settings['show_button_after_login'] ;
		else $wc_email_inquiry_show_button_after_login = esc_attr($wc_ei_settings_custom['wc_email_inquiry_show_button_after_login']);		

		// don't show email inquiry button if for logged in users is deacticated
		if ( $wc_email_inquiry_show_button_after_login != 'yes' ) return false;
		
		if (!isset($wc_ei_settings_custom['role_apply_show_inquiry_button'])) $role_apply_show_inquiry_button = (array) $wc_email_inquiry_global_settings['role_apply_show_inquiry_button'];
		else $role_apply_show_inquiry_button = (array) $wc_ei_settings_custom['role_apply_show_inquiry_button'];
		
		
		$user_login = wp_get_current_user();		
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, $role_apply_show_inquiry_button );
			
			// show email inquiry button if current user role in list apply role
			if ( is_array( $role_existed ) && count( $role_existed ) > 0 ) return true;
		}
		
		return false;
		
	}
	
	public static function check_add_email_inquiry_button_on_shoppage ($product_id=0) {
		global $wc_email_inquiry_global_settings;
		$wc_ei_settings_custom = get_post_meta( $product_id, '_wc_ei_settings_custom', true);
			
		if (!isset($wc_ei_settings_custom['wc_email_inquiry_single_only'])) $wc_email_inquiry_single_only = $wc_email_inquiry_global_settings['inquiry_single_only'];
		else $wc_email_inquiry_single_only = esc_attr($wc_ei_settings_custom['wc_email_inquiry_single_only']);
		
		if ($wc_email_inquiry_single_only == 'yes') return false;
		
		return WC_Email_Inquiry_Functions::check_add_email_inquiry_button($product_id);
		
	}
	
	public static function reset_products_to_global_settings() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_wc_email_inquiry_settings_custom' " );
	}

	public static function reset_products_cart_price() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_wc_ei_cart_price_custom' " );
	}

	public static function reset_products_email_inquiry_settings() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_wc_ei_settings_custom' " );
	}

	public static function reset_products_ei_button_settings() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_wc_ei_button_custom' " );
	}

	public static function reset_products_read_more_button_settings() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_wc_ei_read_more_button_custom' " );
	}
	
	public static function email_inquiry($product_id, $your_name, $your_email, $your_phone, $your_message, $send_copy_yourself = 1) {
		global $wc_email_inquiry_contact_form_settings;
		$wc_email_inquiry_contact_success = stripslashes( get_option( 'wc_email_inquiry_contact_success', '' ) );
		$wc_ei_settings_custom = get_post_meta( $product_id, '_wc_ei_settings_custom', true);
		
		if ( WC_Email_Inquiry_Functions::check_add_email_inquiry_button($product_id) ) {
			
			if ( trim( $wc_email_inquiry_contact_success ) != '') $wc_email_inquiry_contact_success = wpautop(wptexturize(   $wc_email_inquiry_contact_success ));
			else $wc_email_inquiry_contact_success = __("Thanks for your inquiry - we'll be in touch with you as soon as possible!", 'wc_email_inquiry');
		
			if (!isset($wc_ei_settings_custom['wc_email_inquiry_email_to']) || trim(esc_attr($wc_ei_settings_custom['wc_email_inquiry_email_to'])) == '') $to_email = $wc_email_inquiry_contact_form_settings['inquiry_email_to'];
			else $to_email = esc_attr($wc_ei_settings_custom['wc_email_inquiry_email_to']);
			if (trim($to_email) == '') $to_email = get_option('admin_email');
			
			if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'] == '' )
				$from_email = get_option('admin_email');
			else
				$from_email = $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'];
				
			if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'] == '' )
				$from_name = ( function_exists('icl_t') ? icl_t( 'WP',__('Blog Title','wpml-string-translation'), get_option('blogname') ) : get_option('blogname') );
			else
				$from_name = $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'];
			
			if (!isset($wc_ei_settings_custom['wc_email_inquiry_email_cc']) || trim(esc_attr($wc_ei_settings_custom['wc_email_inquiry_email_cc'])) == '') $cc_emails = $wc_email_inquiry_contact_form_settings['inquiry_email_cc'];
			else $cc_emails = esc_attr($wc_ei_settings_custom['wc_email_inquiry_email_cc']);
			if (trim($cc_emails) == '') $cc_emails = '';
			
			$headers = array();
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset='. get_option('blog_charset');
			$headers[] = 'From: '.$from_name.' <'.$from_email.'>';
			$headers_yourself = $headers;
			$headers[] = 'Reply-To: '.$your_name.' <'.$your_email.'>';
			$headers_yourself[] = 'Reply-To: '.$from_name.' <'.$from_email.'>';
			
			if (trim($cc_emails) != '') {
				$cc_emails_a = explode("," , $cc_emails);
				if (is_array($cc_emails_a) && count($cc_emails_a) > 0) {
					foreach ($cc_emails_a as $cc_email) {
						$headers[] = 'Cc: '.$cc_email;
					}
				} else {
					$headers[] = 'Cc: '.$cc_emails;
				}
			}
			
			$product_name = get_the_title($product_id);
			$product_url = get_permalink($product_id);
			$subject = wc_ei_ict_t__( 'Default Form - Email Subject', __('Email inquiry for', 'wc_email_inquiry') ).' '.$product_name;
			$subject_yourself = wc_ei_ict_t__( 'Default Form - Copy Email Subject', __('[Copy]: Email inquiry for', 'wc_email_inquiry') ).' '.$product_name;
			
			$content = '
	<table width="99%" cellspacing="0" cellpadding="1" border="0" bgcolor="#eaeaea"><tbody>
	  <tr>
		<td>
		  <table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="#ffffff"><tbody>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Name', __('Name', 'wc_email_inquiry') ).'</strong></font> 
			  </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_name]</font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Email', __('Email', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><a target="_blank" href="mailto:[your_email]">[your_email]</a></font> 
			  </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Phone', __('Phone', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_phone]</font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Product Name', __('Product Name', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><a target="_blank" href="[product_url]">[product_name]</a></font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Message', __('Message', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_message]</font> 
		  </td></tr></tbody></table></td></tr></tbody></table>';
		  
			$content = str_replace('[your_name]', $your_name, $content);
			$content = str_replace('[your_email]', $your_email, $content);
			$content = str_replace('[your_phone]', $your_phone, $content);
			$content = str_replace('[product_name]', $product_name, $content);
			$content = str_replace('[product_url]', $product_url, $content);
			$your_message = str_replace( '://', ':&#173;­//', $your_message );
			$your_message = str_replace( '.com', '&#173;.com', $your_message );
			$your_message = str_replace( '.net', '&#173;.net', $your_message );
			$your_message = str_replace( '.info', '&#173;.info', $your_message );
			$your_message = str_replace( '.org', '&#173;.org', $your_message );
			$your_message = str_replace( '.au', '&#173;.au', $your_message );
			$content = str_replace('[your_message]', wpautop( $your_message ), $content);
			
			$content = apply_filters('wc_email_inquiry_inquiry_content', $content);
			
			// Filters for the email
			add_filter( 'wp_mail_from', array( 'WC_Email_Inquiry_Functions', 'get_from_address' ) );
			add_filter( 'wp_mail_from_name', array( 'WC_Email_Inquiry_Functions', 'get_from_name' ) );
			add_filter( 'wp_mail_content_type', array( 'WC_Email_Inquiry_Functions', 'get_content_type' ) );
			
			wp_mail( $to_email, $subject, $content, $headers, '' );
			
			if ($send_copy_yourself == 1) {
				wp_mail( $your_email, $subject_yourself, $content, $headers_yourself, '' );
			}
			
			// Unhook filters
			remove_filter( 'wp_mail_from', array( 'WC_Email_Inquiry_Functions', 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( 'WC_Email_Inquiry_Functions', 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( 'WC_Email_Inquiry_Functions', 'get_content_type' ) );
			
			return $wc_email_inquiry_contact_success;
		} else {
			return wc_ei_ict_t__( 'Default Form - Contact Not Allow', __("Sorry, this product don't enable email inquiry.", 'wc_email_inquiry') );
		}
	}
	
	public static function get_from_address() {
		global $wc_email_inquiry_contact_form_settings;
		if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'] == '' )
			$from_email = get_option('admin_email');
		else
			$from_email = $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'];
			
		return $from_email;
	}
	
	public static function get_from_name() {
		global $wc_email_inquiry_contact_form_settings;
		if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'] == '' )
			$from_name = ( function_exists('icl_t') ? icl_t( 'WP',__('Blog Title','wpml-string-translation'), get_option('blogname') ) : get_option('blogname') );
		else
			$from_name = $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'];
			
		return $from_name;
	}
	
	public static function get_content_type() {
		return 'text/html';
	}
	
	/**
	 * Create Page
	 */
	public static function create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;
				
		$page_id = $wpdb->get_var( "SELECT ID FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%$page_content%'  AND `post_type` = 'page' ORDER BY ID DESC LIMIT 1" );
		 
		if ( $page_id != NULL ) 
			return $page_id;
		
		$page_data = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'post_parent' 		=> $post_parent,
			'comment_status' 	=> 'closed'
		);
		$page_id = wp_insert_post( $page_data );
		
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$source_lang_code = $sitepress->get_default_language();
			$trid = $sitepress->get_element_trid( $page_id, 'post_page' );
			if ( ! $trid ) {
				$wpdb->query( "UPDATE ".$wpdb->prefix . "icl_translations SET trid=".$page_id." WHERE element_id=".$page_id." AND language_code='".$source_lang_code."' AND element_type='post_page' " );
			}
		}
						
		return $page_id;
	}
	
	public static function create_page_wpml( $trid, $lang_code, $source_lang_code, $slug, $page_title = '', $page_content = '' ) {
		global $wpdb;
		
		$element_id = $wpdb->get_var( "SELECT ID FROM " . $wpdb->posts . " AS p INNER JOIN " . $wpdb->prefix . "icl_translations AS ic ON p.ID = ic.element_id WHERE p.post_content LIKE '%$page_content%' AND p.post_type = 'page' AND p.post_status = 'publish' AND ic.trid=".$trid." AND ic.language_code = '".$lang_code."' AND ic.element_type = 'post_page' ORDER BY p.ID ASC LIMIT 1" );
		 
		if ( $element_id != NULL ) :
			return $element_id;
		endif;
		
		$page_data = array(
			'post_date'			=> gmdate( 'Y-m-d H:i:s' ),
			'post_modified'		=> gmdate( 'Y-m-d H:i:s' ),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_author' 		=> 1,
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'comment_status' 	=> 'closed'
		);
		$wpdb->insert( $wpdb->posts , $page_data);
		$element_id = $wpdb->insert_id;
		
		//$element_id = wp_insert_post( $page_data );
		
		$wpdb->insert( $wpdb->prefix . "icl_translations", array(
				'element_type'			=> 'post_page',
				'element_id'			=> $element_id,
				'trid'					=> $trid,
				'language_code'			=> $lang_code,
				'source_language_code'	=> $source_lang_code,
			) );
				
		return $element_id;
	}
	
	public static function auto_create_page_for_wpml(  $original_id, $slug, $page_title = '', $page_content = '' ) {
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$active_languages = $sitepress->get_active_languages();
			if ( is_array($active_languages)  && count($active_languages) > 0 ) {
				$source_lang_code = $sitepress->get_default_language();
				$trid = $sitepress->get_element_trid( $original_id, 'post_page' );
				foreach ( $active_languages as $language ) {
					if ( $language['code'] == $source_lang_code ) continue;
					WC_Email_Inquiry_Functions::create_page_wpml( $trid, $language['code'], $source_lang_code, $slug.'-'.$language['code'], $page_title.' '.$language['display_name'], $page_content );
				}
			}
		}
	}
		
	public static function get_product_information( $product_id, $show_product_name = 0, $width = 220, $height = 180, $class_image = '' ) {
		$image_src = WC_Email_Inquiry_Functions::get_post_thumbnail( $product_id, $width, $height, $class_image );
		if ( trim($image_src) == '' ) {
			$image_src = '<img alt="" src="'. ( ( version_compare( WC_VERSION, '2.1', '<' ) ) ? woocommerce_placeholder_img_src() : wc_placeholder_img_src() ) .'" class="'.$class_image.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
		}
		
		$product_information = '';
		ob_start();
	?>
    	<?php if ($show_product_name == 1) { ?>
        <div style="clear:both; margin-top:10px"></div>
		<div style="float:left; margin-right:10px;" class="wc_email_inquiry_default_image_container"><?php echo $image_src; ?></div>
        <div style="display:block; margin-bottom:10px; padding-left:22%;" class="wc_email_inquiry_product_heading_container">
        	<h1 class="wc_email_inquiry_custom_form_product_heading"><?php echo esc_html( get_the_title($product_id) ); ?></h1>
			<div class="wc_email_inquiry_custom_form_product_url_div"><a class="wc_email_inquiry_custom_form_product_url" href="<?php echo esc_url( get_permalink($product_id) ); ?>" title=""><?php echo esc_url( get_permalink($product_id) ); ?></a></div>
        </div>
        <div style="clear:both;"></div>
        <?php } else { ?>
        <?php echo $image_src; ?>
        <?php } ?>
	<?php
		$product_information = ob_get_clean();
		
		return $product_information;
	}
	
	public static function get_post_thumbnail( $postid=0, $width=220, $height=180, $class='') {
		$mediumSRC = '';
		// Get the product ID if none was passed
		if ( empty( $postid ) )
			$postid = get_the_ID();

		// Load the product
		$product = get_post( $postid );

		if (has_post_thumbnail($postid)) {
			$thumbid = get_post_thumbnail_id($postid);
			$attachmentArray = wp_get_attachment_image_src($thumbid, array(0 => $width, 1 => $height), false);
			$mediumSRC = $attachmentArray[0];
			if (trim($mediumSRC != '')) {
				return '<img class="'.$class.'" src="'.$mediumSRC.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
			}
		}
		if (trim($mediumSRC == '')) {
			$args = array( 'post_parent' => $postid , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null);
			$attachments = get_posts($args);
			if ($attachments) {
				foreach ( $attachments as $attachment ) {
					$mediumSRC = wp_get_attachment_image( $attachment->ID, array(0 => $width, 1 => $height), true, array('class' => $class, 'style' => 'max-width:'.$width.'px !important; max-height:'.$height.'px !important;' ) );
					break;
				}
			}
		}

		if (trim($mediumSRC == '')) {
			// Get ID of parent product if one exists
			if ( !empty( $product->post_parent ) )
				$postid = $product->post_parent;

			if (has_post_thumbnail($postid)) {
				$thumbid = get_post_thumbnail_id($postid);
				$attachmentArray = wp_get_attachment_image_src($thumbid, array(0 => $width, 1 => $height), false);
				$mediumSRC = $attachmentArray[0];
				if (trim($mediumSRC != '')) {
					return '<img class="'.$class.'" src="'.$mediumSRC.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
				}
			}
			if (trim($mediumSRC == '')) {
				$args = array( 'post_parent' => $postid , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null);
				$attachments = get_posts($args);
				if ($attachments) {
					foreach ( $attachments as $attachment ) {
						$mediumSRC = wp_get_attachment_image( $attachment->ID, array(0 => $width, 1 => $height), true, array('class' => $class, 'style' => 'max-width:'.$width.'px !important; max-height:'.$height.'px !important;' ) );
						break;
					}
				}
			}
		}
		return $mediumSRC;
	}
	
	public static function get_page_id_from_shortcode( $shortcode, $option ) {
		global $wpdb;
		global $wp_version;
		$page_id = get_option($option);
		if ( version_compare( $wp_version, '4.0', '<' ) ) {
			$shortcode = esc_sql( like_escape( $shortcode ) );
		} else {
			$shortcode = esc_sql( $wpdb->esc_like( $shortcode ) );
		}
		$page_data = null;
		if ($page_id)
			$page_data = $wpdb->get_row( "SELECT ID FROM " . $wpdb->posts . " WHERE post_content LIKE '%[{$shortcode}]%' AND ID = '".$page_id."' AND post_type = 'page' LIMIT 1" );
		if ( $page_data == null )
			$page_data = $wpdb->get_row( "SELECT ID FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%[{$shortcode}]%' AND `post_type` = 'page' ORDER BY post_date DESC LIMIT 1" );
			
		$page_id = $page_data->ID;
		
		// For WPML
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$translation_page_data = null;
			$trid = $sitepress->get_element_trid( $page_id, 'post_page' );
			if ( $trid ) {
				$translation_page_data = $wpdb->get_row( $wpdb->prepare( "SELECT element_id FROM " . $wpdb->prefix . "icl_translations WHERE trid = %d AND element_type='post_page' AND language_code = %s LIMIT 1", $trid , $sitepress->get_current_language() ) );
				if ( $translation_page_data != null )
					$page_id = $translation_page_data->element_id;
			}
		}
		
		return $page_id;
	}
	
	public static function wc_ei_yellow_message_dontshow() {
		check_ajax_referer( 'wc_ei_yellow_message_dontshow', 'security' );
		$option_name   = $_REQUEST['option_name'];
		update_option( $option_name, 1 );
		die();
	}
	
	public static function wc_ei_yellow_message_dismiss() {
		check_ajax_referer( 'wc_ei_yellow_message_dismiss', 'security' );
		$session_name   = $_REQUEST['session_name'];
		if ( !isset($_SESSION) ) { @session_start(); } 
		$_SESSION[$session_name] = 1 ;
		die();
	}
}
?>
