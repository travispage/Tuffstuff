<?php
/**
 * Call this function when plugin is deactivated
 */
function wc_email_inquiry_deactivated(){
	global $wc_ei_admin_init;
	delete_transient( $wc_ei_admin_init->version_transient );
}

function wc_email_inquiry_install(){
	update_option( 'a3rev_wc_orders_quotes_version', '2.2.0' );
	update_option( 'a3rev_wc_order_quotes_updated_for_woo22', true );

	// Set Settings Default from Admin Init
	global $wc_ei_admin_init;
	$wc_ei_admin_init->set_default_settings();

	// Build sass
	global $wc_email_inquiry_less;
	$wc_email_inquiry_less->plugin_build_sass();

	$woocommerce_db_version = get_option( 'woocommerce_db_version' );
	if ( version_compare( $woocommerce_db_version, '2.2', '<' ) ) {
		// Create quote for shop order status
		WC_Email_Inquiry_Quote_Order_Functions::create_quote_order_status();
	}

	WC_Email_Inquiry_Quote_Order_Functions::create_quote_roles();

	WC_Email_Inquiry_3RD_ContactForm_Functions::install_3rd_contactform();

	delete_metadata( 'user', 0, $wc_ei_admin_init->plugin_name . '-' . 'plugin_framework_global_box' . '-' . 'opened', '', true );

	delete_option('wc_email_inquiry_ultimate_clean_on_deletion');
	delete_option('wc_email_inquiry_pro_clean_on_deletion');
	delete_option('wc_email_inquiry_lite_clean_on_deletion');

	delete_transient( $wc_ei_admin_init->version_transient );

	WC_Email_Inquiry_3RD_ContactForm_Functions::add_endpoints();
	flush_rewrite_rules();

	update_option('a3rev_wc_orders_quotes_just_installed', true);
}

update_option('a3rev_wc_orders_quotes_plugin', 'wc_orders_quotes');

/**
 * Load languages file
 */
function wc_email_inquiry_init() {
	if ( get_option('a3rev_wc_orders_quotes_just_installed') ) {
		delete_option('a3rev_wc_orders_quotes_just_installed');
		wp_redirect( admin_url( 'admin.php?page=quotes-orders-mode', 'relative' ) );
		exit;
	}
	load_plugin_textdomain( 'wc_email_inquiry', false, WC_EMAIL_INQUIRY_FOLDER.'/languages' );
}
// Add language
add_action('init', 'wc_email_inquiry_init');

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( 'WC_Email_Inquiry_Hook_Filter', 'a3_wp_admin' ) );

// Add admin sidebar menu css
add_action( 'admin_enqueue_scripts', array( 'WC_Email_Inquiry_Hook_Filter', 'admin_sidebar_menu_css' ) );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('WC_Email_Inquiry_Hook_Filter', 'plugin_extra_links'), 10, 2 );

// Need to call Admin Init to show Admin UI
global $wc_ei_admin_init;
$wc_ei_admin_init->init();

// Add extra link on left of Deactivate link on Plugin manager page
add_action('plugin_action_links_' . WC_EMAIL_INQUIRY_NAME, array( 'WC_Email_Inquiry_Hook_Filter', 'settings_plugin_links' ) );

// Add upgrade notice to Dashboard pages
add_filter( $wc_ei_admin_init->plugin_name . '_plugin_extension_boxes', array( 'WC_Email_Inquiry_Hook_Filter', 'plugin_extension_box' ) );

$woocommerce_db_version = get_option( 'woocommerce_db_version', null );

global $wc_ei_read_more_functions;
$GLOBALS['wc_ei_read_more_functions'] = new WC_EI_Read_More_Functions();

// Include style into header
add_action('wp_enqueue_scripts', array('WC_Email_Inquiry_Hook_Filter', 'add_style_header') );

// Include google fonts into header
add_action( 'wp_enqueue_scripts', array( 'WC_Email_Inquiry_Hook_Filter', 'add_google_fonts'), 9 );

// Include script into footer
add_action('wp_footer', array('WC_Email_Inquiry_Hook_Filter', 'script_contact_popup'), 20 );

// Shortcode For Email Inquiry Page
add_shortcode( 'wc_email_inquiry_page', array('WC_Email_Inquiry_3RD_ContactForm_Functions', 'wc_email_inquiry_page') );

// Change item meta value as long url to short url
add_filter('woocommerce_order_item_display_meta_value', array('WC_Email_Inquiry_Hook_Filter', 'change_order_item_display_meta_value' ) );

// Orders and Quotes Include script into footer
//add_action('get_footer', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'frontend_script_include'), 1);

// AJAX hide yellow message dontshow
add_action('wp_ajax_wc_ei_yellow_message_dontshow', array('WC_Email_Inquiry_Functions', 'wc_ei_yellow_message_dontshow') );
add_action('wp_ajax_nopriv_wc_ei_yellow_message_dontshow', array('WC_Email_Inquiry_Functions', 'wc_ei_yellow_message_dontshow') );

// AJAX hide yellow message dismiss
add_action('wp_ajax_wc_ei_yellow_message_dismiss', array('WC_Email_Inquiry_Functions', 'wc_ei_yellow_message_dismiss') );
add_action('wp_ajax_nopriv_wc_ei_yellow_message_dismiss', array('WC_Email_Inquiry_Functions', 'wc_ei_yellow_message_dismiss') );

// AJAX wc_email_inquiry contact popup
add_action('wp_ajax_wc_email_inquiry_popup', array('WC_Email_Inquiry_Hook_Filter', 'wc_email_inquiry_popup') );
add_action('wp_ajax_nopriv_wc_email_inquiry_popup', array('WC_Email_Inquiry_Hook_Filter', 'wc_email_inquiry_popup') );

// AJAX wc_email_inquiry_action
add_action('wp_ajax_wc_email_inquiry_action', array('WC_Email_Inquiry_Hook_Filter', 'wc_email_inquiry_action') );
add_action('wp_ajax_nopriv_wc_email_inquiry_action', array('WC_Email_Inquiry_Hook_Filter', 'wc_email_inquiry_action') );

// Hide Add to Cart button on Shop page
add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_before_hide_add_to_cart_button'), 100, 4 );
add_action('woocommerce_after_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_after_hide_add_to_cart_button'), 1, 4 );

// Hide Add to Cart button on Details page
add_action('woocommerce_before_add_to_cart_button', array('WC_Email_Inquiry_Hook_Filter', 'details_before_hide_add_to_cart_button'), 100 );
add_action('woocommerce_after_add_to_cart_button', array('WC_Email_Inquiry_Hook_Filter', 'details_after_hide_add_to_cart_button'), 1 );

// Hide Quantity Control and Add to Cart button for Child Product of Grouped Product Type in Details Page
add_action('woocommerce_before_add_to_cart_form', array('WC_Email_Inquiry_Hook_Filter', 'grouped_product_hide_add_to_cart_style'), 100 );
add_filter('single_add_to_cart_text', array('WC_Email_Inquiry_Hook_Filter', 'grouped_product_hide_add_to_cart'), 100, 2 );
add_filter('woocommerce_product_single_add_to_cart_text', array('WC_Email_Inquiry_Hook_Filter', 'grouped_product_hide_add_to_cart'), 100, 2 ); // for Woo 2.1
add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'before_grouped_product_hide_quatity_control'), 100, 4 );
add_action('woocommerce_after_template_part', array('WC_Email_Inquiry_Hook_Filter', 'after_grouped_product_hide_quatity_control'), 1, 4 );

// Add question mark
//add_action('woocommerce_before_add_to_cart_button', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'details_add_question_mark_above'), 1000 );

// Hide Price on Shop page and Details page
add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_before_hide_price'), 100, 4 );
add_action('woocommerce_after_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_after_hide_price'), 1, 4 );

// Hide Price
add_filter('woocommerce_get_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
add_filter('woocommerce_variation_sale_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
add_filter('woocommerce_variation_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
add_filter('woocommerce_variation_free_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
add_filter('woocommerce_variation_empty_price_html', array('WC_Email_Inquiry_Hook_Filter', 'global_hide_price'), 100, 2);
if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
	add_filter('woocommerce_cart_item_price_html', array('WC_Email_Inquiry_Hook_Filter', 'hide_price_from_mini_cart'), 100, 3);
} else {
	add_filter('woocommerce_cart_item_price', array('WC_Email_Inquiry_Hook_Filter', 'hide_price_from_mini_cart'), 100, 3); // for Woo 2.1
}
add_filter('woocommerce_widget_cart_item_quantity', array('WC_Email_Inquiry_Hook_Filter', 'remove_x_character_mini_cart'), 100, 3);
add_filter('woocommerce_cart_product_subtotal', array('WC_Email_Inquiry_Hook_Filter', 'hide_cart_product_subtotal'), 100, 4 );

// Add Email Inquiry Button on Shop page
$wc_email_inquiry_customize_email_button_settings = get_option( 'wc_email_inquiry_customize_email_button', array( 'inquiry_button_position' => 'below' ) );
$wc_email_inquiry_button_position = $wc_email_inquiry_customize_email_button_settings['inquiry_button_position'];
if ($wc_email_inquiry_button_position == 'above' )
	add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'shop_add_email_inquiry_button_above'), 9, 4);
else
	add_action('woocommerce_after_shop_loop_item', array('WC_Email_Inquiry_Hook_Filter', 'shop_add_email_inquiry_button_below'), 12);

// Add Email Inquiry Button on Product Details page
if ($wc_email_inquiry_button_position == 'above' )
	add_action('woocommerce_before_template_part', array('WC_Email_Inquiry_Hook_Filter', 'details_add_email_inquiry_button_above'), 9, 4 );
else
	add_action('woocommerce_after_template_part', array('WC_Email_Inquiry_Hook_Filter', 'details_add_email_inquiry_button_below'), 2, 4);


// Replace 'Add to Cart' by 'Add to Quote' or 'Add to Order' for Product Page
add_filter('add_to_cart_text', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_add_to_cart_text'), 101 );
add_filter('woocommerce_product_add_to_cart_text', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_add_to_cart_text_woo_21'), 101, 2 ); // for Woo 2.1
add_filter('single_add_to_cart_text', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_single_add_to_cart_text'), 101, 2 );
add_filter('woocommerce_product_single_add_to_cart_text', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_single_add_to_cart_text_woo_21'), 100, 2 ); // for Woo 2.1

// Replace Add to Cart message for Product Page
add_filter('woocommerce_add_message', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_add_message'), 1);
add_filter('wc_add_to_cart_message', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'add_to_cart_message'), 1, 2);

// Replace View Cart on Shop & Category Page
add_filter('woocommerce_params', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_woocommerce_params'), 101);
add_filter('wc_add_to_cart_params', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_woocommerce_params'), 101); // For woo 2.1

// Replace Add to Cart error message for Product Page
add_filter('woocommerce_add_error', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_add_error_message'), 1);

// Replace for all text
add_filter('woocommerce_add_message', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_all_message'), 101);
add_filter('woocommerce_add_error', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_all_message'), 101);

// Replace Widget Cart Title
add_filter('widget_title', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_widget_cart_title'), 10, 3 );

// Replace Content of Cart Widget
add_filter('add_to_cart_fragments', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_all_content_widget_cart'), 1, 1 );
add_filter('woocommerce_cart_subtotal', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'hide_mini_cart_subtotal'), 101, 3);
add_filter('woocommerce_cart_contents_total', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'hide_mini_cart_contents_total'), 101 );

// Replace Content of Cart Widget for WC 2.3
add_filter('woocommerce_add_to_cart_fragments', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_all_content_widget_cart'), 1, 1 );

// Change Page Title for Quotes or Orders
add_filter('the_title', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'change_page_title') , 100, 2);

// Change template of woocommerce by own template of Quotes or Orders
add_filter('woocommerce_locate_template', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'change_template_files'), 1001, 3 );

// Replace Place Order button
add_filter('woocommerce_order_button_text', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_order_button_text'), 101);

// Replace Order Notes in Checkout page
add_filter('woocommerce_checkout_fields', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'replace_checkout_fields_order_comments'), 101);

// Filter to change notification email to Admin when new order is quote
add_filter( 'woocommerce_email_recipient_new_order', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'quote_change_recipient_new_order'), 101, 2 );
add_filter( 'woocommerce_email_subject_new_order', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'quote_change_subject_new_order'), 101, 2 );
add_filter( 'woocommerce_email_heading_new_order', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'quote_change_heading_new_order'), 101, 2 );

// Make auto create account for Quotes
add_action('woocommerce_checkout_process', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'make_must_create_account_to_false'), 101 );
add_action('woocommerce_after_checkout_validation', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'checkout_validation') );
add_action('woocommerce_checkout_order_processed', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'auto_create_account'), 1, 2 );

// Add Quotes Gateway
add_action('plugins_loaded', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'custom_gateway_init' ), 0 );

// Just show correct Gateway payment for each Roles
add_filter('woocommerce_available_payment_gateways', array('WC_Email_Inquiry_Quote_Order_Hook_Filter', 'show_payment_gateways_for_role'), 101 );

// Show or Hide Shipping Options
add_action( 'woocommerce_review_order_before_shipping', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'show_hide_shipping_options_before' ), 1 );
add_action( 'woocommerce_review_order_after_shipping', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'show_hide_shipping_options_after' ), 101 );

// Show or Hide Shipping Prices
add_filter( 'woocommerce_cart_shipping_method_full_label', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'show_hide_shipping_prices_on_checkout_page' ), 101, 2 );
add_filter( 'woocommerce_order_shipping_to_display', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'show_hide_shipping_prices_after_submitted' ), 101, 2 );

// Add help text to shipping template for woo 2.0.20 apply for all rules
if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
	add_action( 'woocommerce_review_order_before_shipping', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'change_shipping_methods_template_before' ), 1 );
	add_action( 'woocommerce_review_order_after_shipping', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'change_shipping_methods_template_after' ), 101 );
}

// Fixed to get correct template when order total <= 0
add_filter( 'woocommerce_cart_needs_payment', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'woocommerce_cart_needs_payment' ), 101, 2 );

// Add Processing Quote Email
add_action('init', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'custom_email_init' ), 0 );
add_action('plugins_loaded', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'custom_email_load' ) );

// Filter to show some actions for Quote status
add_filter('woocommerce_admin_order_actions',  array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'add_actions_for_quote' ),10 , 2 );

// Hide Prices on Order List in My account page
add_filter('woocommerce_get_formatted_order_total', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'hide_price_in_myaccount_page' ),10 , 2 );


// Work compatibility with Avada theme http://theme-fusion.com/avada/
add_action( 'init', 'avada_theme_compatibility' );
function avada_theme_compatibility() {
	if ( function_exists( 'avada_woocommerce_view_order' ) ) {
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();

		if ( $apply_request_a_quote || $apply_add_to_order || $apply_auto_quote ) {
			remove_action('woocommerce_thankyou', 'avada_woocommerce_view_order', 10);
			remove_action('woocommerce_view_order', 'avada_woocommerce_view_order', 10);

			add_action('woocommerce_thankyou', 'woocommerce_order_details_table', 11);
			add_action('woocommerce_view_order', 'woocommerce_order_details_table', 11);
		}
	}
}


// Add meta boxes to product page
add_action( 'admin_menu', array('WC_Email_Inquiry_MetaBox', 'add_meta_boxes') );
if ( is_admin() ) {
	add_action('save_post', array('WC_Email_Inquiry_MetaBox','save_meta_boxes' ) );
}

// Add send quote box to Product Order
add_action( 'admin_menu', array('WC_EI_Send_Quote_MetaBox', 'add_meta_boxes') );
if ( is_admin() ) {
	add_action('save_post', array('WC_EI_Send_Quote_MetaBox','save_meta_boxes' ), 1001 );
}

// Include script admin plugin
if (in_array(basename($_SERVER['PHP_SELF']), array('post.php', 'page.php', 'page-new.php', 'post-new.php'))){
	add_action('admin_footer', array('WC_Email_Inquiry_Hook_Filter', 'admin_footer_scripts'));
}

// Include style admin plugin
if ( in_array( basename ($_SERVER['PHP_SELF']), array('admin.php', 'edit.php') ) && isset( $_REQUEST['post_type'] ) && in_array( $_REQUEST['post_type'], array('shop_order') ) ) {
	add_action('admin_head', array('WC_Email_Inquiry_Hook_Filter', 'admin_header_quote_icon_style'));
}

if ( version_compare( $woocommerce_db_version, '2.2', '>=' ) ) {
	// Quote Mode add Order Status - Quote
	add_filter( 'wc_order_statuses', array( 'WC_Email_Inquiry_Quote_Order_Functions', 'add_quote_order_satus' ) );
	add_filter( 'wc_order_is_editable', array( 'WC_Email_Inquiry_Quote_Order_Functions', 'add_quote_status_as_editable' ), 100, 2 );
	add_action( 'init', array( 'WC_Email_Inquiry_Quote_Order_Functions', 'register_post_status' ), 11 );
}

// Check upgrade functions
add_action( 'init', 'a3rev_ei_pro_upgrade_plugin' );
function a3rev_ei_pro_upgrade_plugin() {
	// Upgrade to 1.0.3
	if ( version_compare( get_option( 'a3rev_wc_email_inquiry_version' ), '1.0.3' ) === -1 ) {
		include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-1.0.3.php' );
		update_option('a3rev_wc_email_inquiry_version', '1.0.3');
	}

	// First Upgrade from WooCommerce Email Inquiry plugin to WooCommerce Quotes and Orders plugin
	if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '1.0.0' ) === -1 ) {
		include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-1.0.0.php' );
		update_option('a3rev_wc_orders_quotes_version', '1.0.0');
	}

	// Upgrade to 1.0.4
	if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '1.0.4' ) === -1 ) {
		update_option('a3rev_wc_orders_quotes_version', '1.0.4');
		add_action( 'init', array('WC_Email_Inquiry_3RD_ContactForm_Functions', 'install_3rd_contactform'), 11 );
	}

	if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '1.1.1' ) === -1 ) {
		include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-1.1.1.php' );
		update_option('a3rev_wc_orders_quotes_version', '1.1.1');
	}

	if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '1.1.2' ) === -1 ) {
		$wc_email_inquiry_page_id = WC_Email_Inquiry_Functions::get_page_id_from_shortcode( 'wc_email_inquiry_page' , 'wc_email_inquiry_page_id');
		WC_Email_Inquiry_Functions::auto_create_page_for_wpml( $wc_email_inquiry_page_id, 'email-inquiry-form', __('Email Inquiry Form', 'wc_email_inquiry'), '[wc_email_inquiry_page]' );
		update_option('a3rev_wc_orders_quotes_version', '1.1.2');
	}

	if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '1.1.4.1' ) === -1 ) {
		include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-1.1.4.1.php' );
		update_option('a3rev_wc_orders_quotes_version', '1.1.4.1');
	}

	if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '1.2.0' ) === -1 || get_option( 'a3rev_wc_order_quotes_updated_for_woo22', false ) == false ) {
		if ( version_compare( WC()->version, '2.2.0', '>=' ) ) {
			include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-1.2.0.php' );
			update_option( 'a3rev_wc_order_quotes_updated_for_woo22', true );
		}

		if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '1.2.0' ) === -1 ) {
			update_option('a3rev_wc_orders_quotes_version', '1.2.0');

			// Build sass
			global $wc_email_inquiry_less;
			$wc_email_inquiry_less->plugin_build_sass();
		}
	}

	if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '1.3.0' ) === -1 ) {
		update_option('a3rev_wc_orders_quotes_version', '1.3.0');

		global $wc_ei_admin_init;
		$wc_ei_admin_init->set_default_settings();
	}

	if ( version_compare( get_option( 'a3rev_wc_orders_quotes_version' ), '2.0.0' ) === -1 ) {
		update_option('a3rev_wc_orders_quotes_version', '2.0.0');

		include( WC_EMAIL_INQUIRY_DIR. '/includes/updates/update-2.0.php' );
	}

	// Upgrade to 2.1.1
	if ( version_compare(get_option('a3rev_wc_orders_quotes_version'), '2.1.1') === -1 ) {
		update_option('a3rev_wc_orders_quotes_version', '2.1.1');
		update_option('wc_orders_quotes_style_version', time() );
	}

	// Upgrade to 2.2.0
	if(version_compare(get_option('a3rev_wc_orders_quotes_version'), '2.2.0') === -1){
		update_option('a3rev_wc_orders_quotes_version', '2.2.0');
		global $wc_ei_admin_init;

		update_option( 'a3_' . get_option('a3rev_wc_orders_quotes_plugin') . '_pin', sha1(md5('a3rev.com_'.str_replace( array( 'www.', 'http://', 'https://' ), '', get_option('siteurl') ).'_wc_orders_quotes')) );
		update_option( 'a3_' . get_option('a3rev_wc_orders_quotes_plugin') . '_license', get_option( 'a3rev_auth_wc_orders_quotes', '' ) );
		delete_transient( $wc_ei_admin_init->version_transient );
	}

	update_option('a3rev_wc_orders_quotes_version', '2.2.0');

}

function wc_orders_quotes_check_pin() {
	return true;
}

?>