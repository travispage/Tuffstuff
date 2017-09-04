<?php
/**
 * WC Email Inquiry Quote & Order Functions
 *
 * Table Of Contents
 *
 * get_template_file_path()
 * check_apply_manual_quote()
 * check_apply_manual_quote_by_userid()
 * check_apply_auto_quote()
 * check_apply_auto_quote_by_userid()
 * check_apply_request_a_quote()
 * check_apply_request_a_quote_by_userid()
 * check_apply_add_to_order()
 * check_apply_add_to_order_by_userid()
 * check_hide_shipping_options()
 * check_hide_shipping_prices()
 * check_enable_guest_checkout()
 * shortcode_create_user_email()
 * shortcode_send_quote_email()
 * create_user_email()
 * create_quote_roles()
 * create_quote_order_status()
 */
class WC_Email_Inquiry_Quote_Order_Functions 
{
	/** 
	 * Set global variable when plugin loaded
	 */
	
	public static function get_template_file_path( $file = '' ){
	
		// If we're not looking for a file, do not proceed
		if ( empty( $file ) )
			return;
	
		// Look for file in stylesheet
		if ( file_exists( get_stylesheet_directory() . '/woocommerce/' . $file ) ) {
			$file_path = get_stylesheet_directory() . '/woocommerce/' . $file;
	
		// Look for file in template
		} elseif ( file_exists( get_template_directory() . '/woocommerce/' . $file ) ) {
			$file_path = get_template_directory() . '/woocommerce/' . $file;
	
		// Backwards compatibility
		} else {
			$file_path = WC_EMAIL_INQUIRY_TEMPLATE_PATH . '/' . $file;
		}
	
		// Return filtered result
		return apply_filters( 'wc_email_inquiry_get_template_file_path' , $file_path, $file );
	}
	
	public static function check_apply_manual_quote () {
		global $wc_email_inquiry_rules_roles_settings;

		// apply Manual Quote rule if it is acticated and not logged in users
		if ( $wc_email_inquiry_rules_roles_settings['manual_quote_rule'] == 'yes' && !is_user_logged_in()  ) return true;
		
		// don't apply Manual Quote rule if it is deacticated and not logged in users
		if ( $wc_email_inquiry_rules_roles_settings['manual_quote_rule'] != 'yes' && !is_user_logged_in()  ) return false;
		
		// check role of user logged in list apply role  
		$user_login = wp_get_current_user();
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
			
			// apply Manual Quote rule if current user role in list apply role
			if ( ( is_array( $role_existed ) && count( $role_existed ) > 0 ) || in_array( 'manual_quote', $user_login->roles ) ) return true;
		}
		
		return false;
	}
	
	public static function check_apply_manual_quote_by_userid ( $user_id = 0 ) {
		global $wc_email_inquiry_rules_roles_settings;
		
		if ($user_id < 1) return false;
		
		// check role of user id in list apply role  
		$user_login = get_userdata( $user_id );
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
			
			// apply Manual Quote rule if current user role in list apply role
			if ( ( is_array( $role_existed ) && count( $role_existed ) > 0 ) || in_array( 'manual_quote', $user_login->roles ) ) return true;
		}
		
		return false;
	}
	
	public static function check_apply_auto_quote () {
		global $wc_email_inquiry_rules_roles_settings;
		
		// apply Auto Quote rule if it is acticated and not logged in users
		if ( $wc_email_inquiry_rules_roles_settings['auto_quote_rule'] == 'yes' && !is_user_logged_in()  ) return true;
		
		// don't apply Auto Quote rule if it is deacticated and not logged in users
		if ( $wc_email_inquiry_rules_roles_settings['auto_quote_rule'] != 'yes' && !is_user_logged_in()  ) return false;

		// check role of user logged in list apply role  
		$user_login = wp_get_current_user();
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] );
			
			// apply Auto Quote rule if current user role in list apply role
			if ( ( is_array( $role_existed ) && count( $role_existed ) > 0 ) || in_array( 'auto_quote', $user_login->roles ) ) return true;
		}
		
		return false;
	}
	
	public static function check_apply_auto_quote_by_userid ( $user_id = 0 ) {
		global $wc_email_inquiry_rules_roles_settings;
		
		if ($user_id < 1) return false;
		
		// check role of user id in list apply role  
		$user_login = get_userdata( $user_id );
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] );
			
			// apply Auto Quote rule if current user role in list apply role
			if ( ( is_array( $role_existed ) && count( $role_existed ) > 0 ) || in_array( 'auto_quote', $user_login->roles ) ) return true;
		}
		
		return false;
	}
	
	public static function check_apply_request_a_quote() {
		// dont apply Add to Order rule if Manual Quote rule is activated
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote() ) return true;
		
		// dont apply Add to Order rule if Auto Quote rule is activated
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote() ) return true;
		
		return false;
	}
	
	public static function check_apply_request_a_quote_by_userid( $user_id = 0 ) {
		// dont apply Add to Order rule if Manual Quote rule is activated
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote_by_userid( $user_id ) ) return true;
		
		// dont apply Add to Order rule if Auto Quote rule is activated
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote_by_userid( $user_id ) ) return true;
		
		return false;
	}
	
	public static function check_apply_add_to_order () {
		global $wc_email_inquiry_rules_roles_settings;
		
		// dont apply Add to Order rule if Manual Quote rule or Auto Quote rule is activated
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote() ) return false;
		
		// apply Add to Order rule if it is acticated and not logged in users
		if ( $wc_email_inquiry_rules_roles_settings['add_to_order_rule'] == 'yes' && !is_user_logged_in()  ) return true;
		
		// don't apply Add to Order rule if it is deacticated and not logged in users
		if ( $wc_email_inquiry_rules_roles_settings['add_to_order_rule'] != 'yes' && !is_user_logged_in()  ) return false;
		
		// don't apply Add to Order rule if Add to Order for logged in users is deacticated
		if ( $wc_email_inquiry_rules_roles_settings['activate_order_logged_in'] != 'yes' ) return false;

		// check role of user logged in list apply role
		$user_login = wp_get_current_user();
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] );
			
			// apply Add to Order rule if current user role in list apply role
			if ( is_array( $role_existed ) && count( $role_existed ) > 0 ) return true;
		}
		
		return false;
	}
	
	public static function check_apply_add_to_order_by_userid ( $user_id = 0 ) {
		global $wc_email_inquiry_rules_roles_settings;
		
		if ($user_id < 1) return false;
		
		// dont apply Add to Order rule if Manual Quote rule or Auto Quote rule is activated
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote_by_userid( $user_id ) ) return false;
		
		// don't apply Add to Order rule if Add to Order for logged in users is deacticated
		if ( $wc_email_inquiry_rules_roles_settings['activate_order_logged_in'] != 'yes' ) return false;

		// check role of user logged in list apply role
		$user_login = get_userdata( $user_id );
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$role_existed = array_intersect( $user_login->roles, (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] );
			
			// apply Add to Order rule if current user role in list apply role
			if ( is_array( $role_existed ) && count( $role_existed ) > 0 ) return true;
		}
		
		return false;
	}
	
	// Check to hide shipping options for all rules
	public static function check_hide_shipping_options() {
		global $wc_email_inquiry_quotes_mode_global_settings, $wc_email_inquiry_orders_mode_global_settings;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		$hide_shipping_options = false;
		
		if ( $apply_manual_quote && $wc_email_inquiry_quotes_mode_global_settings['manual_quotes_display_shipping_options'] != 'yes' ) {
			$hide_shipping_options = true;
		} elseif ( $apply_auto_quote && $wc_email_inquiry_quotes_mode_global_settings['auto_quotes_display_shipping_options'] != 'yes' ) {
			$hide_shipping_options = true;
		} elseif ( $apply_add_to_order && $wc_email_inquiry_orders_mode_global_settings['order_display_shipping_options'] != 'yes' ) {
			$hide_shipping_options = true;
		}
		
		return $hide_shipping_options;
	}
	
	// Check to hide shipping prices for all rules with shipping options is showed
	public static function check_hide_shipping_prices() {
		global $wc_email_inquiry_quotes_mode_global_settings, $wc_email_inquiry_orders_mode_global_settings;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		$hide_shipping_prices = false;
		
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_hide_shipping_options() ) return $hide_shipping_prices;
		
		if ( $apply_manual_quote && $wc_email_inquiry_quotes_mode_global_settings['manual_quotes_display_shipping_prices'] != 'yes' ) {
			$hide_shipping_prices = true;
		} elseif ( $apply_auto_quote && $wc_email_inquiry_quotes_mode_global_settings['auto_quotes_display_shipping_prices'] != 'yes' ) {
			$hide_shipping_prices = true;
		} elseif ( $apply_add_to_order && $wc_email_inquiry_orders_mode_global_settings['order_quotes_display_shipping_prices'] != 'yes' ) {
			$hide_shipping_prices = true;
		}
				
		return $hide_shipping_prices;
	}
	
	// Check to enable guest checkout for all rules
	public static function check_enable_guest_checkout() {
		
		if ( is_user_logged_in() ) return true;
		
		global $wc_email_inquiry_rules_roles_settings;
		
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		$enable_guest_checkout = false;
		
		if ( $apply_manual_quote ) {
			if ( isset( $wc_email_inquiry_rules_roles_settings['manual_quote_enable_guest_checkout'] ) && $wc_email_inquiry_rules_roles_settings['manual_quote_enable_guest_checkout'] == 'yes' ) $enable_guest_checkout = true;
		} elseif ( $apply_auto_quote ) {
			if ( isset( $wc_email_inquiry_rules_roles_settings['auto_quote_enable_guest_checkout'] ) && $wc_email_inquiry_rules_roles_settings['auto_quote_enable_guest_checkout'] == 'yes' ) $enable_guest_checkout = true;
		} elseif ( $apply_add_to_order ) {
			if ( isset( $wc_email_inquiry_rules_roles_settings['order_mode_enable_guest_checkout'] ) && $wc_email_inquiry_rules_roles_settings['order_mode_enable_guest_checkout'] == 'yes' ) $enable_guest_checkout = true;
		}
		
		return $enable_guest_checkout;
	}
	
	// Shortcode for auto create account
	public static function shortcode_create_user_email() {
		$shortcode_create_user_email = array(
			'blogname'			=> __('Site Name', 'wc_email_inquiry'),
			'first_name'		=> __('First Name', 'wc_email_inquiry'),
			'last_name'			=> __('Last Name', 'wc_email_inquiry'),
			'customer_email' 	=> __('Customer Email', 'wc_email_inquiry'),
			'username'			=> __('Username', 'wc_email_inquiry'),
			'password'			=> __('Password Random', 'wc_email_inquiry'),
			'account_url'		=> __('Account URL', 'wc_email_inquiry'),
		);
		
		return $shortcode_create_user_email;
	}
	
	// Shortcode for send quote
	public static function shortcode_send_quote_email() {
		$shortcode_send_quote_email = array(
			'blogname'			=> __('Site Name', 'wc_email_inquiry'),
			'first_name'		=> __('First Name', 'wc_email_inquiry'),
			'last_name'			=> __('Last Name', 'wc_email_inquiry'),
			'customer_email' 	=> __('Customer Email', 'wc_email_inquiry'),
		);
		
		return $shortcode_send_quote_email;
	}
	
	public static function get_new_account_email_content( $email_args = array() ) {
		global $wc_email_inquiry_quote_new_account_email_content;
		global $wc_email_inquiry_order_new_account_email_content;
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
		extract($email_args);
		if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
			$account_page_url = esc_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
		} else {
			$account_page_url = esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
		}
		$account_url = '<a href="'.$account_page_url.'" target="_blank">'.$account_page_url.'</a>' ;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote )
			$content = wpautop(stripslashes($wc_email_inquiry_quote_new_account_email_content));
		else
			$content = wpautop(stripslashes($wc_email_inquiry_order_new_account_email_content));
		
		foreach (WC_Email_Inquiry_Quote_Order_Functions::shortcode_create_user_email() as $key=>$value) {
			$content = str_replace('{'.$key.'}', $$key , $content);
		}
		
		return $content;
	}
	
	public static function create_user_email( $email_args=array() ) {
		global $wc_email_inquiry_quote_new_account_email_settings;
		global $wc_email_inquiry_order_new_account_email_settings;
		
		global $woocommerce;
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
			$mailer = $woocommerce->mailer();
		} else {
			$mailer = WC()->mailer();
		}
		
		extract($email_args);
						
		include_once( WC_EMAIL_INQUIRY_FILE_PATH.'/classes/emails/class-email-new-account.php' );
		
		if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
			$account_page_url = esc_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) );
		} else {
			$account_page_url = esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) );
		}
		$account_url = '<a href="'.$account_page_url.'" target="_blank">'.$account_page_url.'</a>' ;
		
		if ( $apply_request_a_quote ) {
			$subject = $wc_email_inquiry_quote_new_account_email_settings['quote_new_account_email_subject'];
			$heading = $wc_email_inquiry_quote_new_account_email_settings['quote_new_account_email_heading'];
		} else {
			$subject = $wc_email_inquiry_order_new_account_email_settings['order_new_account_email_subject'];
			$heading = $wc_email_inquiry_order_new_account_email_settings['order_new_account_email_heading'];
		}
		
		foreach (WC_Email_Inquiry_Quote_Order_Functions::shortcode_create_user_email() as $key=>$value) {
			$subject = str_replace('{'.$key.'}', $$key , $subject);
			$heading = str_replace('{'.$key.'}', $$key , $heading);
		}
		$email_args['email_heading'] = $heading;
				
		$email_content = WC_Email_Inquiry_New_Account_Email::get_email_content($email_args);
		
		$mailer->send( $customer_email, $subject, $email_content, WC_Email_Inquiry_New_Account_Email::get_header() );
	}
	
	public static function create_quote_roles() {
		global $wp_roles;
	
		if ( class_exists('WP_Roles') )
			if ( ! isset( $wp_roles ) )
				$wp_roles = new WP_Roles();
	
		if ( is_object( $wp_roles ) ) {
	
			// Manual Quote role
			add_role( 'manual_quote', __( 'Manual Quote', 'wc_email_inquiry' ), array(
				'read' 						=> true,
				'edit_posts' 				=> false,
				'delete_posts' 				=> false
			) );
			
			// Auto Quote role
			add_role( 'auto_quote', __( 'Auto Quote', 'wc_email_inquiry' ), array(
				'read' 						=> true,
				'edit_posts' 				=> false,
				'delete_posts' 				=> false
			) );
		}
	}
	
	public static function create_quote_order_status() {
		if ( ! get_term_by( 'slug', sanitize_title( 'quote' ), 'shop_order_status' ) ) {
			wp_insert_term( 'quote', 'shop_order_status' );
		}
	}
	
	public static function add_quote_order_satus( $order_statuses = array() ) {
		$order_statuses['wc-quote'] = wc_ei_ict_t__( 'Order Status - Quote', __( 'Quote', 'wc_email_inquiry' ) );
		
		return $order_statuses;
	}

	public static function add_quote_status_as_editable( $is_editable, $order ) {
		if ( in_array( $order->get_status(), array( 'quote' ) ) ) {
			$is_editable = true;
		}
		
		return $is_editable;
	}
	
	/**
	 * Register our custom post statuses, used for order status
	 */
	public static function register_post_status() {
		register_post_status( 'wc-quote', array(
			'label'                     => wc_ei_ict_t__( 'Order Status - Quote', __( 'Quote', 'wc_email_inquiry' ) ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( wc_ei_ict_t__( 'Order Status Count - Quote', __( 'Quote <span class="count">(%s)</span>', 'wc_email_inquiry' ) ),  wc_ei_ict_t__( 'Order Status Count - Quote', __( 'Quote <span class="count">(%s)</span>', 'wc_email_inquiry' ) ) )
		) );
	}
}
?>