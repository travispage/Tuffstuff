<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC EI Orders Mode Global Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WC_EI_Orders_Mode_Global_Settings extends WC_Email_Inquiry_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'settings';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'wc_email_inquiry_orders_mode_global_settings';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_email_inquiry_orders_mode_global_settings';
	
	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;
	
	/**
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * @var array
	 */
	public $form_messages = array();
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->init_form_fields();
		//$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Orders Mode Settings successfully saved.', 'wc_email_inquiry' ),
				'error_message'		=> __( 'Error: Orders Mode Settings can not save.', 'wc_email_inquiry' ),
				'reset_message'		=> __( 'Orders Mode Settings successfully reseted.', 'wc_email_inquiry' ),
			);
			
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
				
		add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );

		add_action( $this->plugin_name . '_settings_' . 'wc_ei_order_activation_status_box' . '_start', array( $this, 'order_activation_status' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {
		
		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wc_ei_admin_interface;
		
		$wc_ei_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wc_ei_admin_interface;
		
		$wc_ei_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}
	
	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array ( 
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {
		
		$subtab_data = array( 
			'name'				=> 'global-settings',
			'label'				=> __( 'Settings', 'wc_email_inquiry' ),
			'callback_function'	=> 'wc_ei_orders_mode_global_settings_form',
		);
		
		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {
	
		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();
		
		return $subtabs_array;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wc_ei_admin_interface;
		
		$output = '';
		$output .= $wc_ei_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );
		
		return $output;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
		
			array(
            	'name' 		=> __( 'Activation Status', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_order_activation_status_box',
                'is_box'	=> true,
           	),

			array(
            	'name' 		=> __( 'Order Mode Payment Gateway', 'wc_email_inquiry' ),
				'desc'		=> ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) ? sprintf( __('Orders Mode payment gateway is automatically activated when the feature is activated. Go to <a href="%s">WooCommerce Payment Gateways</a> to customize.', 'wc_email_inquiry'), admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Email_Inquiry_Gateway_Orders', 'relative' ) ) : sprintf( __('Orders Mode payment gateway is automatically activated when the feature is activated. Go to <a href="%s">WooCommerce Checkout</a> to customize.', 'wc_email_inquiry'), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_email_inquiry_gateway_orders', 'relative' ) ) ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_order_payment_box',
                'is_box'	=> true,
           	),
			
			array(
            	'name' 		=> __( 'Shipping Options', 'wc_email_inquiry' ),
				'desc'		=> sprintf( __( 'Configure store Shipping Options at <a href="%s">WooCommerce Shipping</a> and set Orders mode Shipping visibility options below.', 'wc_email_inquiry'), ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) ? admin_url( 'admin.php?page=woocommerce_settings&tab=shipping', 'relative' ) : admin_url( 'admin.php?page=wc-settings&tab=shipping', 'relative' ) ) ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_order_shipping_options_box',
                'is_box'	=> true,
           	),
			
			array(
            	'name' 		=> __( 'Order Shipping', 'wc_email_inquiry' ),
				'desc'		=> __( 'Settings apply to Checkout, Order Received pages and Customer Order email. Shipping is not shown the Cart Page.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_order_shipping_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( 'Display Shipping Options', 'wc_email_inquiry' ),
				'class'		=> 'order_display_shipping_options',
				'id' 		=> 'order_display_shipping_options',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'order_quotes_display_shipping_prices_container',
           	),
			array(  
				'name' 		=> __( 'Display Shipping Prices', 'wc_email_inquiry' ),
				'class'		=> 'order_quotes_display_shipping_prices',
				'id' 		=> 'order_quotes_display_shipping_prices',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			
        ));
	}

	public function order_activation_status() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->get_names();

		$rules_roles_settings = get_option( 'wc_email_inquiry_rules_roles_settings', array(
			'add_to_order_rule'                   => 'no',
			'activate_order_logged_in'            => 'no',
			'role_apply_activate_order_logged_in' => array(),
		) );
?>
<tr valign="top">
	<th class="titledesc" scope="row">
    	<label><?php echo __( 'Add to Order', 'wc_email_inquiry' ); ?></label>
	</th>
	<td class="forminp">
		<style type="text/css">
		.activation-status {
			padding: 0;
			margin: 5px 0;
			list-style: inside none disc;
		}
		.activation-status li {
			font-size: 25px;
			line-height: 20px;
		}
		.rule-activated {
			color: #27B263;
		}
		.rule-deactivated {
			color: #CB0904;
		}
		.rule-activated span, .rule-deactivated span {
			color: #000;
			font-size: 13px;
			vertical-align: top;
			line-height: 22px;
			margin-left: -5px;
		}
		</style>
		<ul class="activation-status">
		<?php
		if ( 'no' != $rules_roles_settings['add_to_order_rule'] || ( 'no' != $rules_roles_settings['activate_order_logged_in'] && count( $rules_roles_settings['role_apply_activate_order_logged_in'] ) > 0 ) ) {
			$before_login_class     = 'rule-deactivated';
			$after_login_class      = 'rule-deactivated';
			$after_login_roles_name = array();
			$after_login_roles      = '';

			if ( 'no' != $rules_roles_settings['add_to_order_rule'] ) {
				$before_login_class = 'rule-activated';
			}
			if ( 'no' != $rules_roles_settings['activate_order_logged_in'] && count( $rules_roles_settings['role_apply_activate_order_logged_in'] ) > 0 ) {
				$after_login_class = 'rule-activated';
				foreach ( $rules_roles_settings['role_apply_activate_order_logged_in'] as $role_added ) {
					$after_login_roles_name[] = $roles[$role_added];
				}
				$after_login_roles = implode( ', ', $after_login_roles_name );
				if ( trim( $after_login_roles ) != '' ) $after_login_roles = ' - ' . $after_login_roles;
			}
			echo '<li class="'.$before_login_class.'"><span>' . __( 'Before Login', 'wc_email_inquiry' ) .'</span></li>';
			echo '<li class="'.$after_login_class.'"><span>' . __( 'After Login', 'wc_email_inquiry' ) .$after_login_roles.'</span></li>';
		} else {
			echo '<li class="rule-deactivated"><span>' . __( 'Rule Not Active - Please go to Settings Menu to switch ON', 'wc_email_inquiry' ) .'</span></li>';
		} ?>
		</ul>
	</td>
</tr>
<?php
	}
	
	public function include_script() {
	?>
<script>
(function($) {
	
	$(document).ready(function() {
		
		if ( $("input.order_display_shipping_options:checked").val() == 'yes') {
			$(".order_quotes_display_shipping_prices_container").css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
		} else {
			$(".order_quotes_display_shipping_prices_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} );
		}
			
		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.order_display_shipping_options', function( event, value, status ) {
			$(".order_quotes_display_shipping_prices_container").hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
			if ( status == 'true' ) {
				$(".order_quotes_display_shipping_prices_container").slideDown();
			} else {
				$(".order_quotes_display_shipping_prices_container").slideUp();
			}
		});
		
	});
	
})(jQuery);
</script>
    <?php	
	}
}

global $wc_ei_orders_mode_global_settings;
$wc_ei_orders_mode_global_settings = new WC_EI_Orders_Mode_Global_Settings();

/** 
 * wc_ei_orders_mode_global_settings_form()
 * Define the callback function to show subtab content
 */
function wc_ei_orders_mode_global_settings_form() {
	global $wc_ei_orders_mode_global_settings;
	$wc_ei_orders_mode_global_settings->settings_form();
}

?>