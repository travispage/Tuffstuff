<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Quotes Payment Gateway
 *
 * @class 		WC_Email_Inquiry_Gateway_Quotes
 * @extends		WC_Payment_Gateway
 * @version		2.0.0
 * @package		WooCommerce/Classes/Payment
 * @author 		a3rev
 */
class WC_Email_Inquiry_Gateway_Quotes extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     *
     * @access public
     * @return void
     */
    public function __construct() {
		$this->id				= 'quote_mode';
		$this->icon 			= apply_filters('woocommerce_quote_mode_icon', '');
		$this->has_fields 		= false;
		$this->method_title     = __( 'Quote Mode', 'wc_email_inquiry' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title 			= $this->get_option( 'title' );
		$this->description      = $this->get_option( 'description' );

		// Actions
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    	add_action( 'woocommerce_thankyou_quote_mode', array( $this, 'thankyou_page' ) );

    	// Customer Emails
    	//add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 2 );
    }


    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

    	$this->form_fields = array(
			'title' => array(
							'title' => __( 'Title', 'woocommerce' ),
							'type' => 'text',
							'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
							'default' => __( 'Quote Mode', 'wc_email_inquiry' ),
							'desc_tip'      => true,
						),
			'description' => array(
							'title' => __( 'Customer Message', 'woocommerce' ),
							'type' => 'textarea',
							'description' => __( 'Give the customer instructions about Quotes gateway', 'wc_email_inquiry' ),
							'default' => __( 'default text', 'woocommerce' )
						),
			);

    }


	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
	public function admin_options() {
    	?>
    	<h3><?php _e( 'Quote Mode', 'wc_email_inquiry' ); ?></h3>
    	<table class="form-table">
    	<?php
    		// Generate the HTML For the settings form.
    		$this->generate_settings_html();
    	?>
		</table><!--/.form-table-->
    	<?php
    }


    /**
     * Output for the order received page.
     *
     * @access public
     * @return void
     */
    function thankyou_page() {
		if ( $description = $this->get_description() )
        	echo wpautop( wptexturize( wp_kses_post( $description ) ) );
    }


    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @return void
     */
    function email_instructions( $order, $sent_to_admin ) {

    	if ( $sent_to_admin ) return;
		
		if ( version_compare( WC_VERSION, '2.2', '<' ) ) {
    		if ( $order->status !== 'quote') return;
		} else {
			if ( ! $order->has_status( 'quote' ) ) return;
		}

    	if ( $order->payment_method !== 'quote_mode') return;

		if ( $description = $this->get_description() )
        	echo wpautop( wptexturize( $description ) );
    }


    /**
     * Process the payment and return the result
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    function process_payment( $order_id ) {
    	global $woocommerce;
		
		global $wc_email_inquiry_rules_roles_settings;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();

		$order = new WC_Order( $order_id );
		
		// Mark as quote (we're awaiting the payment)
		if ( $apply_auto_quote ) {
			update_post_meta( $order->id, '_email_inquiry_option_type', 'auto_quote');
							
			$order->update_status('quote', __( 'Requested Quote', 'wc_email_inquiry' ));
			
			$order->update_status('pending');
		} else { 
			update_post_meta( $order->id, '_email_inquiry_option_type', 'manual_quote');
			$order->update_status('quote', __( 'Requested Quote', 'wc_email_inquiry' ));
		}
		
		if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
			$mailer = $woocommerce->mailer();
		} else {
			$mailer = WC()->mailer();
		}
		$email = $mailer->emails['WC_Email_New_Order'];
		$email->trigger( $order_id );
		
		// Reduce stock levels
		$order->reduce_order_stock();

		// Remove cart
		if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
			$woocommerce->cart->empty_cart();
		} else {
			WC()->cart->empty_cart();
		}

		// Return thankyou redirect
		if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
			$thanks_page_url = get_permalink( woocommerce_get_page_id('thanks') );
			return array(
				'result' 	=> 'success',
				'redirect'	=> add_query_arg('key', $order->order_key, add_query_arg('order', $order->id, $thanks_page_url ) )
			);
		} else {
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}
		
    }

}