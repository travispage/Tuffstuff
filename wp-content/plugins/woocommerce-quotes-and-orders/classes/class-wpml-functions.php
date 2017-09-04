<?php
/**
 * WC Email Inquiry WPML Functions
 *
 * Table Of Contents
 *
 * plugins_loaded()
 * wpml_register_string()
 */
class WC_Email_Inquiry_WPML_Functions
{	
	public $plugin_wpml_name = 'WC Quotes & Orders';
	
	public function __construct() {
		
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		
		$this->wpml_ict_t();
		
	}
	
	/** 
	 * Register WPML String when plugin loaded
	 */
	public function plugins_loaded() {
		$this->wpml_register_dynamic_string();
		$this->wpml_register_static_string();
	}
	
	/** 
	 * Get WPML String when plugin loaded
	 */
	public function wpml_ict_t() {
		
		$plugin_name = 'wc_orders_quotes';
		
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_contact_form_settings' . '_get_settings', array( $this, 'ict_t_default_form_settings' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_global_settings' . '_get_settings', array( $this, 'ict_t_contact_form_style' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_contact_success' . '_get_setting', array( $this, 'ict_t_contact_success' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_customize_email_button' . '_get_settings', array( $this, 'ict_t_inquiry_button_style' ) );
		
		// For Read More Button
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_read_more_settings' . '_get_settings', array( $this, 'ict_t_read_more_style' ) );
		
		// For Quote Mode Settings
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_product_page' . '_get_settings', array( $this, 'ict_t_quote_product_page' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_widget_cart' . '_get_settings', array( $this, 'ict_t_quote_widget_cart' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_cart_page' . '_get_settings', array( $this, 'ict_t_quote_cart_page' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_cart_note' . '_get_setting', array( $this, 'ict_t_quote_cart_note' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_checkout_page' . '_get_settings', array( $this, 'ict_t_quote_checkout_page' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_checkout_top_message' . '_get_setting', array( $this, 'ict_t_quote_checkout_top_message' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_checkout_shipping_help_text' . '_get_setting', array( $this, 'ict_t_quote_checkout_shipping_help_text' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_checkout_bottom_message' . '_get_setting', array( $this, 'ict_t_quote_checkout_bottom_message' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_order_received_page' . '_get_settings', array( $this, 'ict_t_quote_order_received_page' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_order_received_top_message' . '_get_setting', array( $this, 'ict_t_quote_order_received_top_message' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_order_received_bottom_message' . '_get_setting', array( $this, 'ict_t_quote_order_received_bottom_message' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_new_account_email_settings' . '_get_settings', array( $this, 'ict_t_quote_new_account_email_settings' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_quote_new_account_email_content' . '_get_setting', array( $this, 'ict_t_quote_new_account_email_content' ) );
		
		//Add filter for Processing Quote Email
		add_filter( 'woocommerce_email_subject_' . 'customer_processing_quote', array( $this, 'ict_t_processing_quote_email_subject' ), 10, 2 );
		add_filter( 'woocommerce_email_heading_' . 'customer_processing_quote', array( $this, 'ict_t_processing_quote_email_heading' ), 10, 2 );
		add_filter( 'woocommerce_email_quote_message_' . 'customer_processing_quote', array( $this, 'ict_t_processing_quote_email_message' ), 10, 2 );
		
		// For Order Mode Settings
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_product_page' . '_get_settings', array( $this, 'ict_t_order_product_page' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_widget_cart' . '_get_settings', array( $this, 'ict_t_order_widget_cart' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_cart_page' . '_get_settings', array( $this, 'ict_t_order_cart_page' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_cart_note' . '_get_setting', array( $this, 'ict_t_order_cart_note' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_checkout_page' . '_get_settings', array( $this, 'ict_t_order_checkout_page' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_checkout_top_message' . '_get_setting', array( $this, 'ict_t_order_checkout_top_message' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_checkout_shipping_help_text' . '_get_setting', array( $this, 'ict_t_order_checkout_shipping_help_text' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_checkout_bottom_message' . '_get_setting', array( $this, 'ict_t_order_checkout_bottom_message' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_order_received_page' . '_get_settings', array( $this, 'ict_t_order_order_received_page' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_order_received_top_message' . '_get_setting', array( $this, 'ict_t_order_order_received_top_message' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_order_received_bottom_message' . '_get_setting', array( $this, 'ict_t_order_order_received_bottom_message' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_new_account_email_settings' . '_get_settings', array( $this, 'ict_t_order_new_account_email_settings' ) );
		add_filter( $plugin_name . '_' . 'wc_email_inquiry_order_new_account_email_content' . '_get_setting', array( $this, 'ict_t_order_new_account_email_content' ) );
		
		//Add filter for Peding Order Email
		add_filter( 'woocommerce_email_subject_' . 'customer_pending_order', array( $this, 'ict_t_pending_order_email_subject' ), 10, 2 );
		add_filter( 'woocommerce_email_heading_' . 'customer_pending_order', array( $this, 'ict_t_pending_order_email_heading' ), 10, 2 );
		add_filter( 'woocommerce_email_quote_message_' . 'customer_pending_order', array( $this, 'ict_t_pending_order_email_message' ), 10, 2 );
		
		//Add filter for Quotes & Orders Mode Gateways
		add_filter( 'woocommerce_gateway_title' , array( $this, 'ict_t_gateway_title' ), 10, 2 );
		add_filter( 'woocommerce_gateway_description' , array( $this, 'ict_t_gateway_description' ), 10, 2 );
		
	}
	
	// Registry Dynamic String for WPML
	public function wpml_register_dynamic_string() {
		global $wc_ei_admin_interface;
		$wc_email_inquiry_contact_form_settings = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_contact_form_settings', array() ) );
		$wc_email_inquiry_read_more_settings = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_read_more_settings', array() ) );
		$wc_email_inquiry_global_settings = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_global_settings', array() ) );
		$wc_email_inquiry_contact_success = esc_attr( stripslashes( get_option( 'wc_email_inquiry_contact_success', '' ) ) );
		$wc_email_inquiry_customize_email_button = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_customize_email_button', array() ) );

		if ( function_exists('icl_register_string') ) {
			
			// Default Form
			icl_register_string($this->plugin_wpml_name, 'Default Form - From Name', $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'] );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Header Title', $wc_email_inquiry_global_settings['inquiry_contact_heading'] );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Send Button Title', $wc_email_inquiry_global_settings['inquiry_contact_text_button'] );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Success Message', $wc_email_inquiry_contact_success );

			// Custom Form
			icl_register_string($this->plugin_wpml_name, 'Custom Form - Contact Form Title', $wc_email_inquiry_global_settings['custom_contact_form_heading'] );
			
			// Email Inquiry Button Title
			icl_register_string($this->plugin_wpml_name, 'Email Inquiry Button Title', $wc_email_inquiry_customize_email_button['inquiry_button_title'] );
			
			// Email Inquiry Button Hyperlink
			icl_register_string($this->plugin_wpml_name, 'Email Inquiry Hyperlink - Text Before', $wc_email_inquiry_customize_email_button['inquiry_text_before'] );
			icl_register_string($this->plugin_wpml_name, 'Email Inquiry Hyperlink - Hyperlink Text', $wc_email_inquiry_customize_email_button['inquiry_hyperlink_text'] );
			icl_register_string($this->plugin_wpml_name, 'Email Inquiry Hyperlink - Trailing Text', $wc_email_inquiry_customize_email_button['inquiry_trailing_text'] );
			
			// Read More Hover Button Text
			icl_register_string($this->plugin_wpml_name, 'Read More Hover Button Text', $wc_email_inquiry_read_more_settings['hover_bt_text'] );
			
			// Read More Link Text Under Image
			icl_register_string($this->plugin_wpml_name, 'Read More Link Text', $wc_email_inquiry_read_more_settings['under_image_link_text'] );
			
			// Read More Button Text Under Image
			icl_register_string($this->plugin_wpml_name, 'Read More Button Text', $wc_email_inquiry_read_more_settings['under_image_bt_text'] );
			
			$wc_email_inquiry_quote_product_page = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_quote_product_page', array() ) );
			// Quote Mode - Product Page Settings
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - Button Text', $wc_email_inquiry_quote_product_page['quote_button_text'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - View Quote Button Text', $wc_email_inquiry_quote_product_page['quote_view_button_text'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - Continue Request Button Text', $wc_email_inquiry_quote_product_page['quote_continue_button_text'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - Success Message', $wc_email_inquiry_quote_product_page['quote_success_message'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - Group Added Success Message', $wc_email_inquiry_quote_product_page['quote_group_success_message'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - Quantity Error', $wc_email_inquiry_quote_product_page['quote_error_quantity_message'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - No Product Added Error', $wc_email_inquiry_quote_product_page['quote_error_no_product_add_message'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - Product Is Out of Stock Error', $wc_email_inquiry_quote_product_page['quote_error_out_stock_message'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Product Page - Product Existed In Quote', $wc_email_inquiry_quote_product_page['quote_error_product_already_message'] );
			
			$wc_email_inquiry_quote_widget_cart = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_quote_widget_cart', array() ) );
			// Quote Mode - Cart Widget Settings
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Widget Cart - Title', $wc_email_inquiry_quote_widget_cart['quote_widget_cart_title'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Widget Cart - View Quote Button', $wc_email_inquiry_quote_widget_cart['quote_widget_view_cart_button'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Widget Cart - Send Quote', $wc_email_inquiry_quote_widget_cart['quote_widget_checkout_button'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Widget Cart - No Product Text', $wc_email_inquiry_quote_widget_cart['quote_widget_no_product'] );
			
			$wc_email_inquiry_quote_cart_page = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_quote_cart_page', array() ) );
			$wc_email_inquiry_quote_cart_note = esc_attr( stripslashes( get_option( 'wc_email_inquiry_quote_cart_note', '' ) ) );
			// Quote Mode - Cart Page Settings
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Cart Page - Page Title', $wc_email_inquiry_quote_cart_page['quote_cart_page_name'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Cart Page - Update Quote Button', $wc_email_inquiry_quote_cart_page['quote_update_cart_button'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Cart Page - Details and Send Button', $wc_email_inquiry_quote_cart_page['quote_goto_checkout'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Cart Page - Quote Note', $wc_email_inquiry_quote_cart_note );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Cart Page - Empty Cart Message', $wc_email_inquiry_quote_cart_page['quote_cart_empty'] );
			
			$wc_email_inquiry_quote_checkout_page = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_quote_checkout_page', array() ) );
			$wc_email_inquiry_quote_checkout_top_message = esc_attr( stripslashes( get_option( 'wc_email_inquiry_quote_checkout_top_message', '' ) ) );
			$wc_email_inquiry_quote_checkout_shipping_help_text = esc_attr( stripslashes( get_option( 'wc_email_inquiry_quote_checkout_shipping_help_text', '' ) ) );
			$wc_email_inquiry_quote_checkout_bottom_message = esc_attr( stripslashes( get_option( 'wc_email_inquiry_quote_checkout_bottom_message', '' ) ) );
			// Quote Mode - Checkout Page Settings
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Page Title', $wc_email_inquiry_quote_checkout_page['quote_checkout_page_name'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Send Quote Request', $wc_email_inquiry_quote_checkout_page['quote_place_order_button'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Top of page Message', $wc_email_inquiry_quote_checkout_top_message );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Shipping help text', $wc_email_inquiry_quote_checkout_shipping_help_text );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Shipping and Handling Title', $wc_email_inquiry_quote_checkout_page['shipping_handling_title'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Shipping Options Title', $wc_email_inquiry_quote_checkout_page['shipping_options_title'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Bottom of page Message', $wc_email_inquiry_quote_checkout_bottom_message );
			
			$wc_email_inquiry_quote_order_received_page = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_quote_order_received_page', array() ) );
			$wc_email_inquiry_quote_order_received_top_message = esc_attr( stripslashes( get_option( 'wc_email_inquiry_quote_order_received_top_message', '' ) ) );
			$wc_email_inquiry_quote_order_received_bottom_message = esc_attr( stripslashes( get_option( 'wc_email_inquiry_quote_order_received_bottom_message', '' ) ) );
			// Quote Mode - Order Received Page Settings
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Order Received Page - Page Title', $wc_email_inquiry_quote_order_received_page['quote_order_received_page_name'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Order Received Page - Top of page Message', $wc_email_inquiry_quote_order_received_top_message );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Order Received Page - Bottom of page Message', $wc_email_inquiry_quote_order_received_bottom_message );
			
			$wc_email_inquiry_quote_new_account_email_settings = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_quote_new_account_email_settings', array() ) );
			$wc_email_inquiry_quote_new_account_email_content = esc_attr( stripslashes( get_option( 'wc_email_inquiry_quote_new_account_email_content', '' ) ) );
			// Quote Mode - New Account Email Settings
			icl_register_string($this->plugin_wpml_name, 'Quote Mode New Account Email - Email Subject', $wc_email_inquiry_quote_new_account_email_settings['quote_new_account_email_subject'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode New Account Email - Email Heading', $wc_email_inquiry_quote_new_account_email_settings['quote_new_account_email_heading'] );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode New Account Email - Email Content', $wc_email_inquiry_quote_new_account_email_content );
			
			// Quote Mode - Processing Quote Email Settings
			$customer_processing_quote = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'woocommerce_customer_processing_quote_settings', array() ) );
			if ( is_array( $customer_processing_quote ) && isset( $customer_processing_quote['subject'] ) ) {
				icl_register_string($this->plugin_wpml_name, 'Quote Mode Processing Quote Email - Email Subject', $customer_processing_quote['subject'] );
				icl_register_string($this->plugin_wpml_name, 'Quote Mode Processing Quote Email - Email Heading', $customer_processing_quote['heading'] );
				icl_register_string($this->plugin_wpml_name, 'Quote Mode Processing Quote Email - Email Content', $customer_processing_quote['email_message'] );
			}
			
			$wc_email_inquiry_order_product_page = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_order_product_page', array() ) );
			// Order Mode - Product Page Settings
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - Button Text', $wc_email_inquiry_order_product_page['order_button_text'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - View Order Button Text', $wc_email_inquiry_order_product_page['order_view_button_text'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - Continue Request Button Text', $wc_email_inquiry_order_product_page['order_continue_button_text'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - Success Message', $wc_email_inquiry_order_product_page['order_success_message'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - Group Added Success Message', $wc_email_inquiry_order_product_page['order_group_success_message'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - Quantity Error', $wc_email_inquiry_order_product_page['order_error_quantity_message'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - No Product Added Error', $wc_email_inquiry_order_product_page['order_error_no_product_add_message'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - Product Is Out of Stock Error', $wc_email_inquiry_order_product_page['order_error_out_stock_message'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Product Page - Product Existed In Order', $wc_email_inquiry_order_product_page['order_error_product_already_message'] );
			
			$wc_email_inquiry_order_widget_cart = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_order_widget_cart', array() ) );
			// Order Mode - Cart Widget Settings
			icl_register_string($this->plugin_wpml_name, 'Order Mode Widget Cart - Title', $wc_email_inquiry_order_widget_cart['order_widget_cart_title'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Widget Cart - View Order Button', $wc_email_inquiry_order_widget_cart['order_widget_view_cart_button'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Widget Cart - Send Order', $wc_email_inquiry_order_widget_cart['order_widget_checkout_button'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Widget Cart - No Product Text', $wc_email_inquiry_order_widget_cart['order_widget_no_product'] );
			
			$wc_email_inquiry_order_cart_page = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_order_cart_page', array() ) );
			$wc_email_inquiry_order_cart_note = esc_attr( stripslashes( get_option( 'wc_email_inquiry_order_cart_note', '' ) ) );
			// Order Mode - Cart Page Settings
			icl_register_string($this->plugin_wpml_name, 'Order Mode Cart Page - Page Title', $wc_email_inquiry_order_cart_page['order_cart_page_name'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Cart Page - Update Order Button', $wc_email_inquiry_order_cart_page['order_update_cart_button'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Cart Page - Details and Send Button', $wc_email_inquiry_order_cart_page['order_goto_checkout'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Cart Page - Order Note', $wc_email_inquiry_order_cart_note );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Cart Page - Empty Cart Message', $wc_email_inquiry_order_cart_page['order_cart_empty'] );
			
			$wc_email_inquiry_order_checkout_page = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_order_checkout_page', array() ) );
			$wc_email_inquiry_order_checkout_top_message = esc_attr( stripslashes( get_option( 'wc_email_inquiry_order_checkout_top_message', '' ) ) );
			$wc_email_inquiry_order_checkout_shipping_help_text = esc_attr( stripslashes( get_option( 'wc_email_inquiry_order_checkout_shipping_help_text', '' ) ) );
			$wc_email_inquiry_order_checkout_bottom_message = esc_attr( stripslashes( get_option( 'wc_email_inquiry_order_checkout_bottom_message', '' ) ) );
			// Order Mode - Checkout Page Settings
			icl_register_string($this->plugin_wpml_name, 'Order Mode Checkout Page - Page Title', $wc_email_inquiry_order_checkout_page['order_checkout_page_name'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Checkout Page - Send Order Request', $wc_email_inquiry_order_checkout_page['order_place_order_button'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Checkout Page - Top of page Message', $wc_email_inquiry_order_checkout_top_message );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Checkout Page - Shipping help text', $wc_email_inquiry_order_checkout_shipping_help_text );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Checkout Page - Shipping and Handling Title', $wc_email_inquiry_order_checkout_page['shipping_handling_title'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Checkout Page - Shipping Options Title', $wc_email_inquiry_order_checkout_page['shipping_options_title'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Checkout Page - Bottom of page Message', $wc_email_inquiry_order_checkout_bottom_message );
			
			$wc_email_inquiry_order_order_received_page = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_order_order_received_page', array() ) );
			$wc_email_inquiry_order_order_received_top_message = esc_attr( stripslashes( get_option( 'wc_email_inquiry_order_order_received_top_message', '' ) ) );
			$wc_email_inquiry_order_order_received_bottom_message = esc_attr( stripslashes( get_option( 'wc_email_inquiry_order_order_received_bottom_message', '' ) ) );
			// Order Mode - Order Received Page Settings
			icl_register_string($this->plugin_wpml_name, 'Order Mode Order Received Page - Page Title', $wc_email_inquiry_order_order_received_page['order_order_received_page_name'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Order Received Page - Top of page Message', $wc_email_inquiry_order_order_received_top_message );
			icl_register_string($this->plugin_wpml_name, 'Order Mode Order Received Page - Bottom of page Message', $wc_email_inquiry_order_order_received_bottom_message );
			
			$wc_email_inquiry_order_new_account_email_settings = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'wc_email_inquiry_order_new_account_email_settings', array() ) );
			$wc_email_inquiry_order_new_account_email_content = esc_attr( stripslashes( get_option( 'wc_email_inquiry_order_new_account_email_content', '' ) ) );
			// Order Mode - New Account Email Settings
			icl_register_string($this->plugin_wpml_name, 'Order Mode New Account Email - Email Subject', $wc_email_inquiry_order_new_account_email_settings['order_new_account_email_subject'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode New Account Email - Email Heading', $wc_email_inquiry_order_new_account_email_settings['order_new_account_email_heading'] );
			icl_register_string($this->plugin_wpml_name, 'Order Mode New Account Email - Email Content', $wc_email_inquiry_order_new_account_email_content );
			
			// Order Mode - Pending Order Email Settings
			$customer_pending_order = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'woocommerce_customer_pending_order_settings', array() ) );
			if ( is_array( $customer_pending_order ) && isset( $customer_pending_order['subject'] ) ) {
				icl_register_string($this->plugin_wpml_name, 'Order Mode Pending Order Email - Email Subject', $customer_pending_order['subject'] );
				icl_register_string($this->plugin_wpml_name, 'Order Mode Pending Order Email - Email Heading', $customer_pending_order['heading'] );
				icl_register_string($this->plugin_wpml_name, 'Order Mode Pending Order Email - Email Content', $customer_pending_order['email_message'] );
			}
			
			// Quote Mode Gateway Settings
			$quote_mode_gateway_settings = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'woocommerce_quote_mode_settings', array() ) );
			if ( is_array( $quote_mode_gateway_settings ) && isset( $quote_mode_gateway_settings['title'] ) ) {
				icl_register_string($this->plugin_wpml_name, 'Quote Mode Gateway - Title', $quote_mode_gateway_settings['title'] );
				icl_register_string($this->plugin_wpml_name, 'Quote Mode Gateway - Description', $quote_mode_gateway_settings['description'] );
			}
			// Order Mode Gateway Settings
			$order_mode_gateway_settings = array_map( array( $wc_ei_admin_interface, 'admin_stripslashes' ), get_option( 'woocommerce_order_mode_settings', array() ) );
			if ( is_array( $order_mode_gateway_settings ) && isset( $order_mode_gateway_settings['title'] ) ) {
				icl_register_string($this->plugin_wpml_name, 'Order Mode Gateway - Title', $order_mode_gateway_settings['title'] );
				icl_register_string($this->plugin_wpml_name, 'Order Mode Gateway - Description', $order_mode_gateway_settings['description'] );
			}
		}
	}
	
	// Registry Static String for WPML
	public function wpml_register_static_string() {
		if ( function_exists('icl_register_string') ) {
			
			// Default Form
			icl_register_string($this->plugin_wpml_name, 'Default Form - Default Header Title', __( 'Product Inquiry', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Name', __( 'Name', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Email', __( 'Email', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Phone', __( 'Phone', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Subject', __( 'Subject', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Product Name', __( 'Product Name', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Message', __( 'Message', 'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Send Copy', __( 'Send a copy of this email to myself.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Email Subject', __( 'Email inquiry for',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Copy Email Subject', __( '[Copy]: Email inquiry for',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Send Copy', __( 'Send a copy of this email to myself.',  'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Name Error', __( 'Please enter your Name',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Email Error', __( 'Please enter valid Email addres',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Phone Error', __( 'Please enter your Phone',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Default Form - Contact Not Allow', __( "Sorry, this product don't enable email inquiry.", 'wc_email_inquiry' ) );			
			
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Comment Label', __( 'Quote Notes',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Quote Mode Checkout Page - Comment Placeholder', __( 'Notes about your quote, e.g. special notes for delivery.',  'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Quote Number:', __( 'Quote Number:',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Product', __( 'Product',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Quantity', __( 'Quantity',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Qty', __( 'Qty',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Price', __( 'Price',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Pay Online Now', __( 'Pay Online Now',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Customer Details', __( 'Customer Details',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Email', __( 'Email',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Tel', __( 'Tel',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Telephone', __( 'Telephone',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Your details', __( 'Your details',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Quote number: %s', __( 'Quote number: %s',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Date: %s', __( 'Date: %s',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Total', __( 'Total',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Totals', __( 'Totals',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order Totals', __( 'Order Totals',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order Total', __( 'Order Total',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order Subtotal', __( 'Order Subtotal',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Cart Subtotal', __( 'Cart Subtotal',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Cart Discount', __( 'Cart Discount',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order Discount', __( 'Order Discount',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - [Remove]', __( '[Remove]',  'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - (Includes %s)', __( '(Includes %s)',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Quote Details', __( 'Quote Details',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Download file%s', __( 'Download file%s',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order Again', __( 'Order Again',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Billing Details', __( 'Billing Details',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Shipping Address', __( 'Shipping Address',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - N/A', __( 'N/A',  'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Shipping', __( 'Shipping',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - see available payment methods', __( 'Please fill in your details above to see available payment methods.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - no available payment methods', __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Place order', __( 'Place order',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Read And Accept', __( 'I have read and accept the',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Declined Transaction', __( 'Unfortunately your request for a quote cannot be processed as the originating bank/merchant has declined your transaction.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Please attempt your purchase again or go to your account page.', __( 'Please attempt your purchase again or go to your account page.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Please attempt your purchase again.', __( 'Please attempt your purchase again.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Date', __( 'Date',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Payment', __( 'Payment',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Payment Method', __( 'Payment Method',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Thanks for request a quote', __( 'Thank you. Your request for a quote has been received.',  'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order', __( 'Order',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order Number:', __( 'Order Number:',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Product is not available', __( 'This product is no longer available',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Download %d:', __( 'Download %d:',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Download', __( 'Download',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order number: %s', __( 'Order number: %s',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order date: %s', __( 'Order date: %s',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Your details', __( 'Your details',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Quantity: %s', __( 'Quantity: %s',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Cost: %s', __( 'Cost: %s',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Return To Shop', __( '&larr; Return To Shop',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Remove this item', __( 'Remove this item',  'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Pay for order', __( 'Pay for order',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Form Pay Error', __( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Billing &amp; Shipping', __( 'Billing &amp; Shipping',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Your order', __( 'Your order',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Thank you. Your order has been received.', __( 'Thank you. Your order has been received.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order Details', __( 'Order Details',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Order Declined Transaction', __( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - terms &amp; conditions', __( 'terms &amp; conditions',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - terms &amp; conditions', __( 'terms &amp; conditions',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Plugin Strings - Available on backorder', __( 'Available on backorder',  'wc_email_inquiry' ) );
			
			icl_register_string($this->plugin_wpml_name, 'Order Status - Quote', __( 'Quote',  'wc_email_inquiry' ) );
			icl_register_string($this->plugin_wpml_name, 'Order Status Count - Quote', __( 'Quote <span class="count">(%s)</span>', 'wc_email_inquiry' ) );
		}
	}
	
	// Default Form Settings
	public function ict_t_default_form_settings( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_email_from_name'] ) ) 
			$current_settings['inquiry_email_from_name'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Default Form - From Name', $current_settings['inquiry_email_from_name'] ) : $current_settings['inquiry_email_from_name'] );
		
		return $current_settings;
	}
	
	// Default Form Style
	public function ict_t_contact_form_style( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_contact_heading'] ) ) 
			$current_settings['inquiry_contact_heading'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Default Form - Header Title', $current_settings['inquiry_contact_heading'] ) : $current_settings['inquiry_contact_heading'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_contact_text_button'] ) ) 
			$current_settings['inquiry_contact_text_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Default Form - Send Button Title', $current_settings['inquiry_contact_text_button'] ) : $current_settings['inquiry_contact_text_button'] );

		if ( is_array( $current_settings ) && isset( $current_settings['custom_contact_form_heading'] ) ) 
			$current_settings['custom_contact_form_heading'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Custom Form - Contact Form Title', $current_settings['custom_contact_form_heading'] ) : $current_settings['custom_contact_form_heading'] );
		
		return $current_settings;
	}
	
	// Default Form Contact Success Message
	public function ict_t_contact_success( $current_setting ) {
			$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Default Form - Contact Success Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Email Inquiry Button Title / Hyperlink
	public function ict_t_inquiry_button_style( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_button_title'] ) ) 
			$current_settings['inquiry_button_title'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Email Inquiry Button Title', $current_settings['inquiry_button_title'] ) : $current_settings['inquiry_button_title'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_text_before'] ) ) 
			$current_settings['inquiry_text_before'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Email Inquiry Hyperlink - Text Before', $current_settings['inquiry_text_before'] ) : $current_settings['inquiry_text_before'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_hyperlink_text'] ) ) 
			$current_settings['inquiry_hyperlink_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Email Inquiry Hyperlink - Hyperlink Text', $current_settings['inquiry_hyperlink_text'] ) : $current_settings['inquiry_hyperlink_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['inquiry_trailing_text'] ) ) 
			$current_settings['inquiry_trailing_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Email Inquiry Hyperlink - Trailing Text', $current_settings['inquiry_trailing_text'] ) : $current_settings['inquiry_trailing_text'] );
		
		return $current_settings;
	}
	
	// Read More Button
	public function ict_t_read_more_style( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['hover_bt_text'] ) ) 
			$current_settings['hover_bt_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Read More Hover Button Text', $current_settings['hover_bt_text'] ) : $current_settings['hover_bt_text'] );
		
		if ( is_array( $current_settings ) && isset( $current_settings['under_image_bt_text'] ) ) 
			$current_settings['under_image_bt_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Read More Button Text', $current_settings['under_image_bt_text'] ) : $current_settings['under_image_bt_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['under_image_link_text'] ) ) 
			$current_settings['under_image_link_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Read More Link Text', $current_settings['under_image_link_text'] ) : $current_settings['under_image_link_text'] );

		return $current_settings;
	}
	
	// Quote Product Page Settings
	public function ict_t_quote_product_page( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['quote_button_text'] ) ) 
			$current_settings['quote_button_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - Button Text', $current_settings['quote_button_text'] ) : $current_settings['quote_button_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_view_button_text'] ) ) 
			$current_settings['quote_view_button_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - View Quote Button Text', $current_settings['quote_view_button_text'] ) : $current_settings['quote_view_button_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_continue_button_text'] ) ) 
			$current_settings['quote_continue_button_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - Continue Request Button Text', $current_settings['quote_continue_button_text'] ) : $current_settings['quote_continue_button_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_success_message'] ) ) 
			$current_settings['quote_success_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - Success Message', $current_settings['quote_success_message'] ) : $current_settings['quote_success_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_group_success_message'] ) ) 
			$current_settings['quote_group_success_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - Group Added Success Message', $current_settings['quote_group_success_message'] ) : $current_settings['quote_group_success_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_error_quantity_message'] ) ) 
			$current_settings['quote_error_quantity_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - Quantity Error', $current_settings['quote_error_quantity_message'] ) : $current_settings['quote_error_quantity_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_error_no_product_add_message'] ) ) 
			$current_settings['quote_error_no_product_add_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - No Product Added Error', $current_settings['quote_error_no_product_add_message'] ) : $current_settings['quote_error_no_product_add_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_error_out_stock_message'] ) ) 
			$current_settings['quote_error_out_stock_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - Product Is Out of Stock Error', $current_settings['quote_error_out_stock_message'] ) : $current_settings['quote_error_out_stock_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_error_product_already_message'] ) ) 
			$current_settings['quote_error_product_already_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Product Page - Product Existed In Quote', $current_settings['quote_error_product_already_message'] ) : $current_settings['quote_error_product_already_message'] );
		
		return $current_settings;
	}
	
	// Quote Widget Cart Settings
	public function ict_t_quote_widget_cart( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['quote_widget_cart_title'] ) ) 
			$current_settings['quote_widget_cart_title'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Widget Cart - Title', $current_settings['quote_widget_cart_title'] ) : $current_settings['quote_widget_cart_title'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_widget_view_cart_button'] ) ) 
			$current_settings['quote_widget_view_cart_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Widget Cart - View Quote Button', $current_settings['quote_widget_view_cart_button'] ) : $current_settings['quote_widget_view_cart_button'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_widget_checkout_button'] ) ) 
			$current_settings['quote_widget_checkout_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Widget Cart - Send Quote', $current_settings['quote_widget_checkout_button'] ) : $current_settings['quote_widget_checkout_button'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_widget_no_product'] ) ) 
			$current_settings['quote_widget_no_product'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Widget Cart - No Product Text', $current_settings['quote_widget_no_product'] ) : $current_settings['quote_widget_no_product'] );
		
		return $current_settings;
	}
	
	// Quote Cart Page Settings
	public function ict_t_quote_cart_page( $current_settings = array() ) {
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_cart_page_name'] ) ) 
			$current_settings['quote_cart_page_name'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Cart Page - Page Title', $current_settings['quote_cart_page_name'] ) : $current_settings['quote_cart_page_name'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_update_cart_button'] ) ) 
			$current_settings['quote_update_cart_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Cart Page - Update Quote Button', $current_settings['quote_update_cart_button'] ) : $current_settings['quote_update_cart_button'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_goto_checkout'] ) ) 
			$current_settings['quote_goto_checkout'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Cart Page - Details and Send Button', $current_settings['quote_goto_checkout'] ) : $current_settings['quote_goto_checkout'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_cart_empty'] ) ) 
			$current_settings['quote_cart_empty'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Cart Page - Empty Cart Message', $current_settings['quote_cart_empty'] ) : $current_settings['quote_cart_empty'] );
		
		return $current_settings;
	}
	
	// Quote Cart Page Note
	public function ict_t_quote_cart_note( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Cart Page - Quote Note', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote Checkout Page Settings
	public function ict_t_quote_checkout_page( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['quote_checkout_page_name'] ) ) 
			$current_settings['quote_checkout_page_name'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Checkout Page - Page Title', $current_settings['quote_checkout_page_name'] ) : $current_settings['quote_checkout_page_name'] );
		if ( is_array( $current_settings ) && isset( $current_settings['quote_place_order_button'] ) ) 
			$current_settings['quote_place_order_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Checkout Page - Send Quote Request', $current_settings['quote_place_order_button'] ) : $current_settings['quote_place_order_button'] );
		if ( is_array( $current_settings ) && isset( $current_settings['shipping_handling_title'] ) ) 
			$current_settings['shipping_handling_title'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Checkout Page - Shipping and Handling Title', $current_settings['shipping_handling_title'] ) : $current_settings['shipping_handling_title'] );
		if ( is_array( $current_settings ) && isset( $current_settings['shipping_options_title'] ) ) 
			$current_settings['shipping_options_title'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Checkout Page - Shipping Options Title', $current_settings['shipping_options_title'] ) : $current_settings['shipping_options_title'] );
		
		return $current_settings;
	}
	
	// Quote Checkout Page -  Top Message
	public function ict_t_quote_checkout_top_message( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Checkout Page - Top of page Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote Checkout Page -  Shipping Help Text
	public function ict_t_quote_checkout_shipping_help_text( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Checkout Page - Shipping help text', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote Checkout Page -  Bottom Message
	public function ict_t_quote_checkout_bottom_message( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Checkout Page - Bottom of page Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote Mode Order Received Page Settings
	public function ict_t_quote_order_received_page( $current_settings = array() ) {
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_order_received_page_name'] ) ) 
			$current_settings['quote_order_received_page_name'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Order Received Page - Page Title', $current_settings['quote_order_received_page_name'] ) : $current_settings['quote_order_received_page_name'] );
		
		return $current_settings;
	}
	
	// Quote Mode Order Received Page -  Top Message
	public function ict_t_quote_order_received_top_message( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Order Received Page - Top of page Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote Mode Order Received Page -  Bottom Message
	public function ict_t_quote_order_received_bottom_message( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Order Received Page - Bottom of page Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote New Account Email Settings
	public function ict_t_quote_new_account_email_settings( $current_settings = array() ) {
	
		if ( is_array( $current_settings ) && isset( $current_settings['quote_new_account_email_subject'] ) ) 
			$current_settings['quote_new_account_email_subject'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode New Account Email - Email Subject', $current_settings['quote_new_account_email_subject'] ) : $current_settings['quote_new_account_email_subject'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['quote_new_account_email_heading'] ) ) 
			$current_settings['quote_new_account_email_heading'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode New Account Email - Email Heading', $current_settings['quote_new_account_email_heading'] ) : $current_settings['quote_new_account_email_heading'] );
		
		return $current_settings;
	}
	
	// Quote New Account Email Settings -  Email Message
	public function ict_t_quote_new_account_email_content( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode New Account Email - Email Content', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote Processing Quote Email -  Email Subject
	public function ict_t_processing_quote_email_subject( $current_setting, $an_object ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Processing Quote Email - Email Subject', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote Processing Quote Email -  Email Heading
	public function ict_t_processing_quote_email_heading( $current_setting, $an_object ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Processing Quote Email - Email Heading', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote Processing Quote Email -  Email Content
	public function ict_t_processing_quote_email_message( $current_setting, $an_object ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Processing Quote Email - Email Content', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Product Page Settings
	public function ict_t_order_product_page( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['order_button_text'] ) ) 
			$current_settings['order_button_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - Button Text', $current_settings['order_button_text'] ) : $current_settings['order_button_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_view_button_text'] ) ) 
			$current_settings['order_view_button_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - View Order Button Text', $current_settings['order_view_button_text'] ) : $current_settings['order_view_button_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_continue_button_text'] ) ) 
			$current_settings['order_continue_button_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - Continue Request Button Text', $current_settings['order_continue_button_text'] ) : $current_settings['order_continue_button_text'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_success_message'] ) ) 
			$current_settings['order_success_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - Success Message', $current_settings['order_success_message'] ) : $current_settings['order_success_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_group_success_message'] ) ) 
			$current_settings['order_group_success_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - Group Added Success Message', $current_settings['order_group_success_message'] ) : $current_settings['order_group_success_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_error_quantity_message'] ) ) 
			$current_settings['order_error_quantity_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - Quantity Error', $current_settings['order_error_quantity_message'] ) : $current_settings['order_error_quantity_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_error_no_product_add_message'] ) ) 
			$current_settings['order_error_no_product_add_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - No Product Added Error', $current_settings['order_error_no_product_add_message'] ) : $current_settings['order_error_no_product_add_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_error_out_stock_message'] ) ) 
			$current_settings['order_error_out_stock_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - Product Is Out of Stock Error', $current_settings['order_error_out_stock_message'] ) : $current_settings['order_error_out_stock_message'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_error_product_already_message'] ) ) 
			$current_settings['order_error_product_already_message'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Product Page - Product Existed In Order', $current_settings['order_error_product_already_message'] ) : $current_settings['order_error_product_already_message'] );
		
		return $current_settings;
	}
	
	// Order Widget Cart Settings
	public function ict_t_order_widget_cart( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['order_widget_cart_title'] ) ) 
			$current_settings['order_widget_cart_title'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Widget Cart - Title', $current_settings['order_widget_cart_title'] ) : $current_settings['order_widget_cart_title'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_widget_view_cart_button'] ) ) 
			$current_settings['order_widget_view_cart_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Widget Cart - View Order Button', $current_settings['order_widget_view_cart_button'] ) : $current_settings['order_widget_view_cart_button'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_widget_checkout_button'] ) ) 
			$current_settings['order_widget_checkout_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Widget Cart - Send Order', $current_settings['order_widget_checkout_button'] ) : $current_settings['order_widget_checkout_button'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_widget_no_product'] ) ) 
			$current_settings['order_widget_no_product'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Widget Cart - No Product Text', $current_settings['order_widget_no_product'] ) : $current_settings['order_widget_no_product'] );
		
		return $current_settings;
	}
	
	// Order Cart Page Settings
	public function ict_t_order_cart_page( $current_settings = array() ) {
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_cart_page_name'] ) ) 
			$current_settings['order_cart_page_name'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Cart Page - Page Title', $current_settings['order_cart_page_name'] ) : $current_settings['order_cart_page_name'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_update_cart_button'] ) ) 
			$current_settings['order_update_cart_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Cart Page - Update Order Button', $current_settings['order_update_cart_button'] ) : $current_settings['order_update_cart_button'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_goto_checkout'] ) ) 
			$current_settings['order_goto_checkout'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Cart Page - Details and Send Button', $current_settings['order_goto_checkout'] ) : $current_settings['order_goto_checkout'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_cart_empty'] ) ) 
			$current_settings['order_cart_empty'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Cart Page - Empty Cart Message', $current_settings['order_cart_empty'] ) : $current_settings['order_cart_empty'] );
		
		return $current_settings;
	}
	
	// Order Cart Page Note
	public function ict_t_order_cart_note( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Cart Page - Order Note', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Checkout Page Settings
	public function ict_t_order_checkout_page( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['order_checkout_page_name'] ) ) 
			$current_settings['order_checkout_page_name'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Checkout Page - Page Title', $current_settings['order_checkout_page_name'] ) : $current_settings['order_checkout_page_name'] );
		if ( is_array( $current_settings ) && isset( $current_settings['order_place_order_button'] ) ) 
			$current_settings['order_place_order_button'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Checkout Page - Send Order Request', $current_settings['order_place_order_button'] ) : $current_settings['order_place_order_button'] );
		if ( is_array( $current_settings ) && isset( $current_settings['shipping_handling_title'] ) ) 
			$current_settings['shipping_handling_title'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Checkout Page - Shipping and Handling Title', $current_settings['shipping_handling_title'] ) : $current_settings['shipping_handling_title'] );
		if ( is_array( $current_settings ) && isset( $current_settings['shipping_options_title'] ) ) 
			$current_settings['shipping_options_title'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Checkout Page - Shipping Options Title', $current_settings['shipping_options_title'] ) : $current_settings['shipping_options_title'] );
		
		return $current_settings;
	}
	
	// Order Checkout Page -  Top Message
	public function ict_t_order_checkout_top_message( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Checkout Page - Top of page Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Checkout Page -  Shipping Help Text
	public function ict_t_order_checkout_shipping_help_text( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Checkout Page - Shipping help text', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Checkout Page -  Bottom Message
	public function ict_t_order_checkout_bottom_message( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Checkout Page - Bottom of page Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Mode Order Received Page Settings
	public function ict_t_order_order_received_page( $current_settings = array() ) {
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_order_received_page_name'] ) ) 
			$current_settings['order_order_received_page_name'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Order Received Page - Page Title', $current_settings['order_order_received_page_name'] ) : $current_settings['order_order_received_page_name'] );
		
		return $current_settings;
	}
	
	// Order Mode Order Received Page -  Top Message
	public function ict_t_order_order_received_top_message( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Order Received Page - Top of page Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Mode Order Received Page -  Bottom Message
	public function ict_t_order_order_received_bottom_message( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Order Received Page - Bottom of page Message', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order New Account Email Settings
	public function ict_t_order_new_account_email_settings( $current_settings = array() ) {
	
		if ( is_array( $current_settings ) && isset( $current_settings['order_new_account_email_subject'] ) ) 
			$current_settings['order_new_account_email_subject'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode New Account Email - Email Subject', $current_settings['order_new_account_email_subject'] ) : $current_settings['order_new_account_email_subject'] );
			
		if ( is_array( $current_settings ) && isset( $current_settings['order_new_account_email_heading'] ) ) 
			$current_settings['order_new_account_email_heading'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode New Account Email - Email Heading', $current_settings['order_new_account_email_heading'] ) : $current_settings['order_new_account_email_heading'] );
		
		return $current_settings;
	}
	
	// Order New Account Email Settings -  Email Message
	public function ict_t_order_new_account_email_content( $current_setting ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode New Account Email - Email Content', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Processing Quote Email -  Email Subject
	public function ict_t_pending_order_email_subject( $current_setting, $an_object ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Pending Order Email - Email Subject', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Processing Quote Email -  Email Heading
	public function ict_t_pending_order_email_heading( $current_setting, $an_object ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Pending Order Email - Email Heading', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Order Processing Quote Email -  Email Content
	public function ict_t_pending_order_email_message( $current_setting, $an_object ) {
		$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Pending Order Email - Email Content', $current_setting ) : $current_setting );
		
		return $current_setting;
	}
	
	// Quote & Order Mode Gateway - Title
	public function ict_t_gateway_title( $current_setting, $gateway_id ) {
		if ( $gateway_id == 'quote_mode' ) {
			$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Gateway - Title', $current_setting ) : $current_setting );
		} elseif ( $gateway_id == 'order_mode' ) {
			$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Gateway - Title', $current_setting ) : $current_setting );
		}
		
		return $current_setting;
	}
	
	// Quote & Order Mode Gateway - Title
	public function ict_t_gateway_description( $current_setting, $gateway_id ) {
		if ( $gateway_id == 'quote_mode' ) {
			$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Quote Mode Gateway - Description', $current_setting ) : $current_setting );
		} elseif ( $gateway_id == 'order_mode' ) {
			$current_setting = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'Order Mode Gateway - Description', $current_setting ) : $current_setting );
		}
		
		return $current_setting;
	}
	
}

global $wc_ei_wpml;
$wc_ei_wpml = new WC_Email_Inquiry_WPML_Functions();

function wc_ei_ict_t_e( $name, $string ) {
	global $wc_ei_wpml;
	$string = ( function_exists('icl_t') ? icl_t( $wc_ei_wpml->plugin_wpml_name, $name, $string ) : $string );
	
	echo $string;
}

function wc_ei_ict_t__( $name, $string ) {
	global $wc_ei_wpml;
	$string = ( function_exists('icl_t') ? icl_t( $wc_ei_wpml->plugin_wpml_name, $name, $string ) : $string );
	
	return $string;
}
?>