<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC EI Rules & Roles Settings

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

class WC_EI_Rules_Roles_Settings extends WC_Email_Inquiry_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'rules-roles';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'wc_email_inquiry_rules_roles_settings';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_email_inquiry_rules_roles_settings';
	
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
	
	public function custom_types() {
		$custom_type = array( 'hide_addtocart_yellow_message', 'hide_price_yellow_message', 'manual_quote_yellow_message', 'use_woocommerce_css_yellow_message' );
		
		return $custom_type;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		// add custom type
		foreach ( $this->custom_types() as $custom_type ) {
			add_action( $this->plugin_name . '_admin_field_' . $custom_type, array( $this, $custom_type ) );
		}
		
		$this->init_form_fields();
		//$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Rules & Roles Settings successfully saved.', 'wc_email_inquiry' ),
				'error_message'		=> __( 'Error: Rules & Roles Settings can not save.', 'wc_email_inquiry' ),
				'reset_message'		=> __( 'Rules & Roles Settings successfully reseted.', 'wc_email_inquiry' ),
			);
		
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
		add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );
		
		add_action( $this->plugin_name . '-' . trim( $this->form_key ) . '_settings_init', array( $this, 'after_settings_save' ) );
		
		add_action( $this->plugin_name . '-' . trim( $this->form_key ) . '_before_settings_save', array( $this, 'before_settings_save' ) );
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
	/* before_settings_save()
	/* Process before settings is saved */
	/*-----------------------------------------------------------------------------------*/
	public function before_settings_save() {
		$validate_roles = true;
		$error_message = '';
		global $wc_ei_admin_interface;
		
		if ( isset( $_POST['bt_save_settings'] ) ) {
			if ( ! isset( $_POST[$this->option_name]['role_apply_hide_cart'] ) ) $_POST[$this->option_name]['role_apply_hide_cart'] = array();
			if ( ! isset( $_POST[$this->option_name]['role_apply_hide_price'] ) ) $_POST[$this->option_name]['role_apply_hide_price'] = array();
			if ( ! isset( $_POST[$this->option_name]['role_apply_manual_quote'] ) ) $_POST[$this->option_name]['role_apply_manual_quote'] = array();
			if ( ! isset( $_POST[$this->option_name]['role_apply_auto_quote'] ) ) $_POST[$this->option_name]['role_apply_auto_quote'] = array();
			if ( ! isset( $_POST[$this->option_name]['role_apply_activate_order_logged_in'] ) ) $_POST[$this->option_name]['role_apply_activate_order_logged_in'] = array();
			
			/*
			 * Rules & Roles Schema when javascript is has error
			 */
			// Process for Auto Quote rule
			$_POST[$this->option_name]['role_apply_auto_quote'] = array_diff ( (array) $_POST[$this->option_name]['role_apply_auto_quote'], (array) $_POST[$this->option_name]['role_apply_manual_quote'] );
			
			// Process for Add to Order rule
			$_POST[$this->option_name]['role_apply_activate_order_logged_in'] = array_diff ( (array) $_POST[$this->option_name]['role_apply_activate_order_logged_in'], (array) $_POST[$this->option_name]['role_apply_manual_quote'] );
			$_POST[$this->option_name]['role_apply_activate_order_logged_in'] = array_diff ( (array) $_POST[$this->option_name]['role_apply_activate_order_logged_in'], (array) $_POST[$this->option_name]['role_apply_auto_quote'] );
			
			// Process for Hide Cart rule
			$_POST[$this->option_name]['role_apply_hide_cart'] = array_diff ( (array) $_POST[$this->option_name]['role_apply_hide_cart'], (array) $_POST[$this->option_name]['role_apply_manual_quote'] );
			$_POST[$this->option_name]['role_apply_hide_cart'] = array_diff ( (array) $_POST[$this->option_name]['role_apply_hide_cart'], (array) $_POST[$this->option_name]['role_apply_auto_quote'] );
			$_POST[$this->option_name]['role_apply_hide_cart'] = array_diff ( (array) $_POST[$this->option_name]['role_apply_hide_cart'], (array) $_POST[$this->option_name]['role_apply_activate_order_logged_in'] );
			
			// Process for Hide Price rule
			$_POST[$this->option_name]['role_apply_hide_price'] = array_diff ( (array) $_POST[$this->option_name]['role_apply_hide_price'], (array) $_POST[$this->option_name]['role_apply_activate_order_logged_in'] );
			
		}
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* after_settings_save()
	/* Process after settings is saved */
	/*-----------------------------------------------------------------------------------*/
	public function after_settings_save() {
		if ( isset( $_POST['bt_save_settings'] ) && isset( $_POST['wc_ei_product_reset_cart_price_options'] ) ) {
			delete_option( 'wc_ei_product_reset_cart_price_options' );
			WC_Email_Inquiry_Functions::reset_products_cart_price();
		}
		if ( get_option( 'wc_email_inquiry_clean_on_deletion' ) == 0  )  {
			$uninstallable_plugins = (array) get_option('uninstall_plugins');
			unset($uninstallable_plugins[WC_EMAIL_INQUIRY_NAME]);
			update_option('uninstall_plugins', $uninstallable_plugins);
		}
		if ( isset( $_POST['bt_save_settings'] ) ) {
			$customized_settings = get_option( $this->option_name, array() );
			
			if ( ! in_array( 'manual_quote', $customized_settings['role_apply_manual_quote'] ) )  {
				$customized_settings['role_apply_manual_quote'][] = 'manual_quote';
			}
				
			if ( ! in_array( 'auto_quote', $customized_settings['role_apply_auto_quote'] ) )  {
				$customized_settings['role_apply_auto_quote'][] = 'auto_quote';
			}
				
			if ( $customized_settings['manual_quote_rule'] == 'yes' ) {
				$customized_settings['hide_addcartbt'] = 'no';
				$customized_settings['hide_price'] = 'yes';
			} elseif ( $customized_settings['auto_quote_rule'] == 'yes' ) {
				$customized_settings['hide_addcartbt'] = 'no';
				$customized_settings['hide_price'] = 'yes';
			} elseif ( $customized_settings['add_to_order_rule'] == 'yes' ) {
				$customized_settings['hide_addcartbt'] = 'no';
				$customized_settings['hide_price'] = 'no';
			}
				
			update_option( $this->option_name, $customized_settings );
		}
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
			'name'				=> 'rules-roles',
			'label'				=> __( 'Settings', 'wc_email_inquiry' ),
			'callback_function'	=> 'wc_ei_rules_roles_settings_form',
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
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->get_names();
		$roles_hide_cart = $roles;
		unset( $roles_hide_cart['manual_quote'] );
		unset( $roles_hide_cart['auto_quote'] );
		$roles_activate_order = $roles_auto_quote = $roles_manual_quote = $roles_hide_price = $roles_hide_cart;
		$roles_manual_quote = array_merge( array( 'manual_quote' => __( 'Manual Quote', 'wc_email_inquiry' ) ), $roles_manual_quote );
		$roles_auto_quote = array_merge( array( 'auto_quote' => __( 'Auto Quote', 'wc_email_inquiry' ) ), $roles_auto_quote );
		
  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
		
			array(
            	'name' 		=> __( 'Plugin Framework Global Settings', 'wc_email_inquiry' ),
            	'id'		=> 'plugin_framework_global_box',
                'type' 		=> 'heading',
                'first_open'=> true,
                'is_box'	=> true,
           	),
           	array(
           		'name'		=> __( 'Manual Check For New Plugin Version', 'wc_email_inquiry' ),
           		'desc'		=> __( 'This plugin supports auto upgrades via your WordPress auto updates. Updates show within 24 hours of release. This feature allows you to call any new version for immediate upgrades instead of having to wait until they show in your WordPress updates.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
           	),
           	array(
				'type' 		=> 'manual_check_version',
			),
           	array(
           		'name'		=> __( 'Customize Admin Setting Box Display', 'wc_email_inquiry' ),
           		'desc'		=> __( 'By default each admin panel will open with all Setting Boxes in the CLOSED position.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
           	),
           	array(
				'type' 		=> 'onoff_toggle_box',
			),
           	array(
           		'name'		=> __( 'Google Fonts', 'wc_email_inquiry' ),
           		'desc'		=> __( 'By Default Google Fonts are pulled from a static JSON file in this plugin. This file is updated but does not have the latest font releases from Google.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
           	),
           	array(
                'type' 		=> 'google_api_key',
           	),
           	array(
            	'name' 		=> __( 'House Keeping', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
            ),
			array(
				'name' 		=> __( 'Clean up on Deletion', 'wc_email_inquiry' ),
				'desc' 		=> __( 'On deletion (not deactivate) the plugin will completely remove all tables and data it created, leaving no trace it was ever here.', 'wc_email_inquiry'),
				'id' 		=> 'wc_email_inquiry_clean_on_deletion',
				'type' 		=> 'onoff_checkbox',
				'default'	=> '0',
				'separate_option'	=> true,
				'free_version'		=> true,
				'checked_value'		=> '1',
				'unchecked_value'	=> '0',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),

			array(
            	'name' 		=> __( 'Help Notes', 'wc_email_inquiry' ),
            	'class'		=> 'help_notes_container',
                'type' 		=> 'heading',
            ),
			array(
				'name' 		=> __( "Rules & Roles Help Notes", 'wc_email_inquiry' ),
				'class'		=> 'rules_roles_explanation',
				'id' 		=> 'rules_roles_explanation',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'hide',
				'checked_value'		=> 'show',
				'unchecked_value' 	=> 'hide',
				'checked_label'		=> __( 'SHOW', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'HIDE', 'wc_email_inquiry' ),
				'desc' 		=> '</span></td></tr><tr><td colspan="2"><div class="rules_roles_explanation_container">
<div>' . __( "The Quotes and Orders plugin enables you to", 'wc_email_inquiry' ) . '</div>
<ul style="padding-left: 40px;">
	<li>* ' . __( "Set Rules that apply to Product Pages or your entire store.", 'wc_email_inquiry' ) . '</li>
	<li>* ' . __( "The Rules are applied to users who are NOT Logged in and Rules for when they login in.", 'wc_email_inquiry' ) . '</li>
	<li>* ' . __( "Different Rules can be applied to logged in users based upon their user Role e.g. what users with the Customer Role see verses what users with the Subscriber role see.", 'wc_email_inquiry' ) . '</li>
</ul>
<div style="margin-bottom: 20px;">' . __( "<strong>Important!</strong> When an admin sets a Rule for NOT logged in users if they then check the front end to see the new Rule they will not see it, because they are logged in as administrator and have not applied that Rule to their role (this catches a lot of first time users who think the plugin is not working because they can't see the rule applied while they are logged in but can when they log out).", 'wc_email_inquiry' ) . '</div>
				</div><span>',
			),
			array(
				'name' 		=> __( "No Conflict Rules", 'wc_email_inquiry' ),
				'class'		=> 'no_conflict_explanation',
				'id' 		=> 'no_conflict_explanation',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'hide',
				'checked_value'		=> 'show',
				'unchecked_value' 	=> 'hide',
				'checked_label'		=> __( 'SHOW', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'HIDE', 'wc_email_inquiry' ),
				'desc' 		=> '</span></td></tr><tr><td colspan="2"><div class="no_conflict_explanation_container">
<div>' . __( "Quotes and Orders uses conditional logic to ensure that admins can never set a conflicting rule. That works in 2 ways", 'wc_email_inquiry' ) . '</div>
<ul style="padding-left: 40px;">
	<li>* ' . __( "Switching ON any Quote or Order Rule for NOT logged in users auto deactivates any activated Rules that conflict.", 'wc_email_inquiry' ) . '</li>
	<li>* ' . __( "The plugin will not allow admin to assign any Rule to a Role that has previously been assigned to a conflicting Rule. When trying to assign a Rule to a role if it appears greyed out it means it is assigned to a conflicting rule. Find that Rule and remove the role and it is instantly available for assigning to a new Rule.", 'wc_email_inquiry' ) . '</li>
</ul>
				</div><span>',
			),
			array(
				'name' 		=> __( "Product Page Rules Explanation", 'wc_email_inquiry' ),
				'class'		=> 'product_page_explanation',
				'id' 		=> 'product_page_explanation',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'hide',
				'checked_value'		=> 'show',
				'unchecked_value' 	=> 'hide',
				'checked_label'		=> __( 'SHOW', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'HIDE', 'wc_email_inquiry' ),
				'desc' 		=> '</span></td></tr><tr><td colspan="2"><div class="product_page_explanation_container">
<div style="margin-bottom: 20px;">' . __( "Product Page Rules apply a single action Rule to all product pages which can be filtered on a per User Role basis. These Rules can also be varied on a product by product basis from each product edit page", 'wc_email_inquiry' ) . '</div>
				</div><span>',
			),
			array(
				'name' 		=> __( "Store Rules Explanation", 'wc_email_inquiry' ),
				'class'		=> 'store_rules_explanation',
				'id' 		=> 'store_rules_explanation',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'hide',
				'checked_value'		=> 'show',
				'unchecked_value' 	=> 'hide',
				'checked_label'		=> __( 'SHOW', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'HIDE', 'wc_email_inquiry' ),
				'desc' 		=> '</span></td></tr><tr><td colspan="2"><div class="store_rules_explanation_container">
<div style="margin-bottom: 20px;">' . __( "The Store Rules are Manual Quotes, Auto Quotes and Orders. Switching on those Rules will  determine how users see your entire store BEFORE and then AFTER they log in. The store Rules are applied to ALL products in the store.", 'wc_email_inquiry' ) . '</div>
				</div><span>',
			),
			array(
				'name' 		=> __( "Troubleshooting", 'wc_email_inquiry' ),
				'class'		=> 'troubleshooting_explanation',
				'id' 		=> 'troubleshooting_explanation',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'hide',
				'checked_value'		=> 'show',
				'unchecked_value' 	=> 'hide',
				'checked_label'		=> __( 'SHOW', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'HIDE', 'wc_email_inquiry' ),
				'desc' 		=> '</span></td></tr><tr><td colspan="2"><div class="troubleshooting_explanation_container">
<div>' . __( "Below is a list of common issues we see", 'wc_email_inquiry' ) . '</div>
<div style="margin-top: 20px;">' . __( "<strong>Don't See Rules Applied on front end.</strong>", 'wc_email_inquiry' ) . '
<ul style="padding-left: 40px;">
	<li>* ' . __( "This is because you have not applied the Rule to your logged in user role (administrator). Either apply your role to the rule or check it in another browser where you are not logged in.", 'wc_email_inquiry' ) . '</li>
</ul>
</div>
<div>' . __( "<Strong>'Add to' Button and or Email Inquiry, Read More button does not show on some products.</strong>", 'wc_email_inquiry' ) . '</div>
<ul style="padding-left: 40px;">
	<li>* ' . __( "The 'Add to' Button e.g. Add to Quote button is the WooCommerce Add to Cart Button.", 'wc_email_inquiry' ) . '</li>
	<li>* ' . __( "The Email Inquiry and Read More Buttons are hooked to the add to cart button.", 'wc_email_inquiry' ) . '</li>
</ul>
<div style="padding-left: 40px;">' . __( "There are 2 circumstances that WooCommerce removes the 'Add to Cart' function from the Product card and Product page. If either of these apply to a product then Quotes and Orders has nothing to hook to and cannot work. Those 2 are:", 'wc_email_inquiry' ) . '
<ul style="padding-left: 40px;">
	<li>1. ' . __( "IF a product has no price entered.", 'wc_email_inquiry' ) . '</li>
	<li>2. ' . __( "If 'Inventory Management' is ON and the product is 'out of Stock'.", 'wc_email_inquiry' ) . '</li>
</ul>
</div>
<div style="margin-top: 20px;">' . __( "<strong>Cart page, Checkout page style is broken</strong>", 'wc_email_inquiry' ) . '
<ul style="padding-left: 40px;">
	<li>* ' . __( "If the plugin messes up your Bespoke themes WooCommerce template please use the + Shop Display Templates setting box further down this tab for a built in fix for that.", 'wc_email_inquiry' ) . '</li>
</ul>
</div>
				</div><span>',
			),

			array(
            	'name' 		=> __( "Product Page Rule: Hide 'Add to Cart'", 'wc_email_inquiry' ),
            	'desc'		=> __( 'This Rule hides the add to cart button on all products. Hide or show add to cart can be set independently of these global settings from each product edit page. Note - Any Store Rule set will always take priority over this Product page rule setting', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_hide_add_to_cart_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( "View before log in", 'wc_email_inquiry' ),
				'desc'		=> __( 'ON and your stores add to cart button will be hidden from all users before they log in.', 'wc_email_inquiry' ),
				'class'		=> 'hide_addcartbt_before_login',
				'id' 		=> 'hide_addcartbt',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			array(  
				'name' 		=> __( "View after login", 'wc_email_inquiry' ),
				'desc'		=> __( 'Select user roles that you do not want to see add to cart when they log in.', 'wc_email_inquiry' ),
				'class'		=> 'hide_addcartbt_after_login',
				'id' 		=> 'hide_addcartbt_after_login',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			array(
				'class'		=> 'hide_addcartbt_after_login_container',
                'type' 		=> 'heading',
           	),
			array(  
				'class' 	=> 'chzn-select role_apply_hide_cart',
				'id' 		=> 'role_apply_hide_cart',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Roles', 'wc_email_inquiry' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $roles_hide_cart,
			),
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container hide_addtocart_yellow_message_container',
           	),
			array(
                'type' 		=> 'hide_addtocart_yellow_message',
           	),
			
			array(
				'name' 		=> __( "Product Page Rule: Hide Price", 'wc_email_inquiry' ),
				'desc'		=> __( 'This Rule hides product prices on all products. Hide or show price can be set independently of these global settings from each product edit page. Note - Any Store Rule set will always take priority over this Product page rule setting', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_hide_price_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( "View before log in", 'wc_email_inquiry' ),
				'desc'		=> __( 'ON all product prices will be hidden from all users before they log in.', 'wc_email_inquiry' ),
				'class'		=> 'email_inquiry_hide_price_before_login',
				'id' 		=> 'hide_price',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			array(  
				'name' 		=> __( "View after login", 'wc_email_inquiry' ),
				'desc'		=> __( 'Select user roles that you do not want to product prices when they log in.', 'wc_email_inquiry' ),
				'class'		=> 'email_inquiry_hide_price_after_login',
				'id' 		=> 'hide_price_after_login',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			array(
				'class'		=> 'email_inquiry_hide_price_after_login_container',
                'type' 		=> 'heading',
           	),
			array(  
				'class' 	=> 'chzn-select role_apply_hide_price',
				'id' 		=> 'role_apply_hide_price',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Roles', 'wc_email_inquiry' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $roles_hide_price,
			),
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container hide_price_yellow_message_container',
           	),
			array(
                'type' 		=> 'hide_price_yellow_message',
           	),
			array(
				'name'		=> __( 'Product Page Rules Reset', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_rule_reset_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( "Reset All Products", 'wc_email_inquiry' ),
				'desc' 		=> __( "Switch ON and Save Changes to reset all custom Hide cart and Hide Price rules and roles that have been set from product edit pages back to the settings here on this tab.", 'wc_email_inquiry' )
				.'<br />'.__( "<strong>Important</strong> Clear your cache after so that visitors see changes.", 'wc_email_inquiry' ),
				'id' 		=> 'wc_ei_product_reset_cart_price_options',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'separate_option'	=> true,
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			
			array(
            	'name' 		=> __( 'Store Rule: Manual Quote', 'wc_email_inquiry' ),
            	'desc'		=> __( 'Manual Quotes Rule hides prices everywhere including quote request emails. Quote Request is created as WooCommerce order that can be edited and sent to the customer by email from the Order edit page. Note - Manual Quotes Rule over rides any conflicting Product Page Rules that have been set.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_manual_quote_mode_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( "View before log in", 'wc_email_inquiry' ),
				'desc'		=> __( 'ON hides all Product Prices and changes all add to cart functions to add to quote.', 'wc_email_inquiry' ) ,
				'desc_tip'	=> __( 'Hide prices everywhere including on order email and order details. If you have shipping costs configured it does not hide shipping costs.', 'wc_email_inquiry' ),
				'class'		=> 'apply_manual_quote_rule quote_mode_rule',
				'id' 		=> 'manual_quote_rule',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
				
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'enable_guest_checkout_container manual_quote_enable_guest_checkout_container',
           	),
			array(  
				'name' 		=> __( "Enable guest checkout", 'wc_email_inquiry' ),
				'desc'		=> __( 'ON to Request a Quote without auto creating an account with Manual Quote Role.', 'wc_email_inquiry' ) ,
				'id' 		=> 'manual_quote_enable_guest_checkout',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
				
			),
			
			array(
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'View after login', 'wc_email_inquiry' ),
				'desc' 		=> '</span><div>' .__( 'Select user roles that you want Manual Quotes Rules applied to when they log in.', 'wc_email_inquiry' ) .'</div><span>',
				'class'		=> 'chzn-select role_apply_manual_quote',
				'id' 		=> 'role_apply_manual_quote',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Roles', 'wc_email_inquiry' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $roles_manual_quote,
			),
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container manual_quote_yellow_message_container',
           	),
			array(
                'type' 		=> 'manual_quote_yellow_message',
           	),
			array(
				'name' 		=> __( 'Store Rule: Auto Quote', 'wc_email_inquiry' ),
				'desc'		=> __( 'This rule is called Auto Quotes because unlike manual quotes where the order must be edited and manually submitted to the customer - with auto quotes when a quote request is submitted the customer email shows itemized product prices, shipping and taxes and can click through to pay for the order via your sites configured payment gateway.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_auto_quote_mode_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( "View before log in", 'wc_email_inquiry' ),
				'desc'		=> __( 'ON to Hide Prices but auto include product prices in the quote request email.', 'wc_email_inquiry' ) ,
				'desc_tip'	=> __( 'Hide prices on shop page, product detail page, sidebar, cart widget, cart page, checkout page. Prices including shipping show in order email, order details when subscriber send the quote request.', 'wc_email_inquiry' ),
				'class'		=> 'apply_auto_quote_rule quote_mode_rule',
				'id' 		=> 'auto_quote_rule',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'enable_guest_checkout_container auto_quote_enable_guest_checkout_container',
           	),
			array(  
				'name' 		=> __( "Enable guest checkout", 'wc_email_inquiry' ),
				'desc'		=> __( 'ON to Request a Quote without auto creating an account with Auto Quote Role.', 'wc_email_inquiry' ) ,
				'id' 		=> 'auto_quote_enable_guest_checkout',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
				
			),
			
			array(
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( 'View after login', 'wc_email_inquiry' ),
				'desc' 		=> '',
				'class' 	=> 'chzn-select role_apply_auto_quote',
				'id' 		=> 'role_apply_auto_quote',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Roles', 'wc_email_inquiry' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $roles_auto_quote,
			),
			
			array(
				'name' 		=> __( 'Store Rule: Add to Order', 'wc_email_inquiry' ),
				'desc'		=> __( 'Orders Rule can be used 2 ways. Business wanting to allow customers to place real online orders and for admins who want Request a Quote but want their Product Prices visible (just edit the Orders Templates to say Request a Quote instead of Add to Order). All Orders / Quotes are created as WooCommerce Orders.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_order_mode_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( "View before log in", 'wc_email_inquiry' ),
				'desc'		=> __( 'Converts All WooCommerce add to cart functions to an online order generator.', 'wc_email_inquiry' ),
				'class'		=> 'apply_add_to_order_rule',
				'id' 		=> 'add_to_order_rule',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'enable_guest_checkout_container order_mode_enable_guest_checkout_container',
           	),
			array(  
				'name' 		=> __( "Enable guest checkout", 'wc_email_inquiry' ),
				'desc'		=> __( 'ON to Add to Order without auto creating an account with Customer Role.', 'wc_email_inquiry' ) ,
				'id' 		=> 'order_mode_enable_guest_checkout',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
				
			),
			
			array(
                'type' 		=> 'heading',
           	),
			array(  
				'name' 		=> __( "View after login", 'wc_email_inquiry' ),
				'class' 	=> 'activate_order_logged_in',
				'id' 		=> 'activate_order_logged_in',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			array(
				'class'		=> 'role_apply_activate_order_logged_in_container',
                'type' 		=> 'heading',
           	),
			array(  
				'class' 	=> 'chzn-select role_apply_activate_order_logged_in',
				'id' 		=> 'role_apply_activate_order_logged_in',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Roles', 'wc_email_inquiry' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $roles_activate_order,
			),
			
			array(
            	'name' 		=> __( 'Shop CSS Templates Compatibility', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_use_wc_css_box',
                'is_box'	=> true,
           	),
			array(  
				'name' 		=> __( "Use WooCommerce CSS", 'wc_email_inquiry' ),
				'desc'		=> __( 'Turn on if the cart Page, Checkout Page or Order Received Page CSS is broken when a Store Rule is activated.', 'wc_email_inquiry' ) ,
				'id' 		=> 'use_woocommerce_css',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
				
			),
			
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container use_woocommerce_css_yellow_message_container',
           	),
			array(
                'type' 		=> 'use_woocommerce_css_yellow_message',
           	),

        ));
	}
	
	public function hide_addtocart_yellow_message( $value ) {
		$customized_settings = get_option( $this->option_name, array() );
	?>
    	<tr valign="top" class="hide_addtocart_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$hide_addtocart_blue_message = '<div><strong>'.__( 'Note', 'wc_email_inquiry' ).':</strong> '.__( "If you do not apply Rules to your role i.e. 'administrator' you will need to either log out or open the site in another browser where you are not logged in to see the Rule feature is activated.", 'wc_email_inquiry' ).'</div>
                <div style="clear:both"></div>
                <a class="hide_addtocart_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'wc_email_inquiry' ).'</a>
                <a class="hide_addtocart_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'wc_email_inquiry' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $hide_addtocart_blue_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .hide_addtocart_yellow_message_container {
<?php if ( $customized_settings['hide_addcartbt'] == 'no' && $customized_settings['hide_addcartbt_after_login'] == 'no' ) echo 'display: none;'; ?>
<?php if ( get_option( 'wc_ei_hide_addtocart_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ei_hide_addtocart_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.hide_addcartbt_after_login', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".hide_addtocart_yellow_message_container").slideDown();
		} else if( $("input.hide_addcartbt_before_login").prop( "checked" ) == false ) {
			$(".hide_addtocart_yellow_message_container").slideUp();
		}
	});
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.hide_addcartbt_before_login', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".hide_addtocart_yellow_message_container").slideDown();
		} else if( $("input.hide_addcartbt_after_login").prop( "checked" ) == false ) {
			$(".hide_addtocart_yellow_message_container").slideUp();
		}
	});
	
	$(document).on( "click", ".hide_addtocart_yellow_message_dontshow", function(){
		$(".hide_addtocart_yellow_message_tr").slideUp();
		$(".hide_addtocart_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dontshow",
				option_name: 	"wc_ei_hide_addtocart_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".hide_addtocart_yellow_message_dismiss", function(){
		$(".hide_addtocart_yellow_message_tr").slideUp();
		$(".hide_addtocart_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dismiss",
				session_name: 	"wc_ei_hide_addtocart_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}
		
	public function hide_price_yellow_message( $value ) {
		$customized_settings = get_option( $this->option_name, array() );
	?>
    	<tr valign="top" class="hide_price_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$hide_inquiry_button_blue_message = '<div><strong>'.__( 'Note', 'wc_email_inquiry' ).':</strong> '.__( "If you do not apply Rules to your role i.e. 'administrator' you will need to either log out or open the site in another browser where you are not logged in to see the Rule feature is activated.", 'wc_email_inquiry' ).'</div>
                <div style="clear:both"></div>
                <a class="hide_price_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'wc_email_inquiry' ).'</a>
                <a class="hide_price_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'wc_email_inquiry' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $hide_inquiry_button_blue_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .hide_price_yellow_message_container {
<?php if ( $customized_settings['hide_price'] == 'no' && $customized_settings['hide_price_after_login'] == 'no' ) echo 'display: none;'; ?>
<?php if ( get_option( 'wc_ei_hide_price_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ei_hide_price_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.email_inquiry_hide_price_after_login', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".hide_price_yellow_message_container").slideDown();
		} else if( $("input.email_inquiry_hide_price_before_login").prop( "checked" ) == false ) {
			$(".hide_price_yellow_message_container").slideUp();
		}
	});
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.email_inquiry_hide_price_before_login', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".hide_price_yellow_message_container").slideDown();
		} else if( $("input.email_inquiry_hide_price_after_login").prop( "checked" ) == false ) {
			$(".hide_price_yellow_message_container").slideUp();
		}
	});
	
	$(document).on( "click", ".hide_price_yellow_message_dontshow", function(){
		$(".hide_price_yellow_message_tr").slideUp();
		$(".hide_price_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dontshow",
				option_name: 	"wc_ei_hide_price_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".hide_price_yellow_message_dismiss", function(){
		$(".hide_price_yellow_message_tr").slideUp();
		$(".hide_price_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dismiss",
				session_name: 	"wc_ei_hide_price_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}
	
	public function manual_quote_yellow_message( $value ) {
	?>
    	<tr valign="top" class="manual_quote_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$manual_quote_blue_message = '<div><strong>'.__( 'Tip', 'wc_email_inquiry' ).':</strong> '.__( "When you assign the Administrator Role to Manual Quotes and create a test Manual Quote Request you will get 2 Quote Request Received emails - the site admins copy and the customers copy", 'wc_email_inquiry' ).'. <strong>'.__( 'Note', 'wc_email_inquiry' ).':</strong> '.__( "The admin email shows the order sub total amount. This is not a bug. Check the customers copy and you will see it shows no prices for each product and no sub total amount.", 'wc_email_inquiry' ).'</div>
				<div style="clear:both"></div>
                <a class="manual_quote_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'wc_email_inquiry' ).'</a>
                <a class="manual_quote_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'wc_email_inquiry' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $manual_quote_blue_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .manual_quote_yellow_message_container {
<?php if ( get_option( 'wc_ei_manual_quote_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ei_manual_quote_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	
	$(document).on( "click", ".manual_quote_yellow_message_dontshow", function(){
		$(".manual_quote_yellow_message_tr").slideUp();
		$(".manual_quote_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dontshow",
				option_name: 	"wc_ei_manual_quote_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".manual_quote_yellow_message_dismiss", function(){
		$(".manual_quote_yellow_message_tr").slideUp();
		$(".manual_quote_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dismiss",
				session_name: 	"wc_ei_manual_quote_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}
	
	public function use_woocommerce_css_yellow_message( $value ) {
	?>
    	<tr valign="top" class="use_woocommerce_css_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$use_woocommerce_css_yellow_message = '<div><div><strong>'.__( 'Tip', 'wc_email_inquiry' ).':</strong></div><div>'.__( "This only applies if you are using a Bespoke theme and the Theme developer has removed the woocommerce.css and replaced it with a custom template that uses a different HTML structure. This is very bad practise but there are plenty of Bespoke Theme developers who do it. You will know if you have such a theme because in Quotes or Orders mode on the Cart page, Checkout page and Order received pages layout and style will be broken.", 'wc_email_inquiry' ).'</div><div>'.__( "If you have this issue and after activating this feature you still have issues with the WooCommerce page layouts and style it will be because the custom HTML structure of the theme is over riding the woocommerce.css. If using WooCommerce Quotes and Orders is important to your stores functionality you should do one of 2 things.", 'wc_email_inquiry' ).'</div><div>'.__( "1. Contact the theme developer and ask them to fix their code.", 'wc_email_inquiry' ).'</div><div>'.__( "2. Ask for a refund and choose a theme that is 100% WooCommerce Compatible (It is not hard the Default WordPress themes are all 100% compatible).", 'wc_email_inquiry' ).'</div></div>
				<div style="clear:both"></div>
                <a class="use_woocommerce_css_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'wc_email_inquiry' ).'</a>
                <a class="use_woocommerce_css_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'wc_email_inquiry' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $use_woocommerce_css_yellow_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .use_woocommerce_css_yellow_message_container {
<?php if ( get_option( 'wc_ei_use_woocommerce_css_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ei_use_woocommerce_css_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	
	$(document).on( "click", ".use_woocommerce_css_yellow_message_dontshow", function(){
		$(".use_woocommerce_css_yellow_message_tr").slideUp();
		$(".use_woocommerce_css_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dontshow",
				option_name: 	"wc_ei_use_woocommerce_css_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".use_woocommerce_css_yellow_message_dismiss", function(){
		$(".use_woocommerce_css_yellow_message_tr").slideUp();
		$(".use_woocommerce_css_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dismiss",
				session_name: 	"wc_ei_use_woocommerce_css_message_dismiss",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dismiss"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
});
})(jQuery);
</script>
			</td>
		</tr>
    <?php
	
	}
	
	public function include_script() {
	?>
<style>
.help_notes_container table th {
	padding-top:12px;
	padding-bottom:12px;
}
.help_notes_container table td {
	padding-top:8px;
	padding-bottom:8px;
}
.yellow_message_container {
	margin-top: -15px;	
}
.yellow_message_container a {
	text-decoration:none;	
}
.yellow_message_container th, .yellow_message_container td, .hide_addcartbt_after_login_container th, .hide_addcartbt_after_login_container td,  .email_inquiry_hide_price_after_login_container th, .email_inquiry_hide_price_after_login_container td, .role_apply_activate_order_logged_in_container th, .role_apply_activate_order_logged_in_container td {
	padding-top: 0 !important;
	padding-bottom: 0 !important;
}
</style>
<script>
(function($) {
	
	a3revEIRulesRoles = {
		
		initRulesRoles: function () {
			// Disabled Manual Quote role for Manual Quote rule to admin can't remove this role for Manual Quote rule
			$("select.role_apply_manual_quote option:first").attr('disabled', 'disabled');
			
			// Disabled Auto Quote role for Auto Quote rule to admin can't remove this role for Auto Quote rule
			$("select.role_apply_auto_quote option:first").attr('disabled', 'disabled');
			
			if ( $("input.rules_roles_explanation").is(':checked') == false ) {
				$(".rules_roles_explanation_container").hide();
			}

			if ( $("input.no_conflict_explanation").is(':checked') == false ) {
				$(".no_conflict_explanation_container").hide();
			}

			if ( $("input.product_page_explanation").is(':checked') == false ) {
				$(".product_page_explanation_container").hide();
			}

			if ( $("input.store_rules_explanation").is(':checked') == false ) {
				$(".store_rules_explanation_container").hide();
			}

			if ( $("input.troubleshooting_explanation").is(':checked') == false ) {
				$(".troubleshooting_explanation_container").hide();
			}
			
			$('.enable_guest_checkout_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} );
			
			/* 
			 * Condition logic for not logged in users
			 * Apply when page is loaded
			 */
			if ( $("input.apply_manual_quote_rule:checked").val() == 'yes' ) {
				$('input.hide_addcartbt_before_login').removeAttr('checked').attr('checkbox-disabled', 'true');
				$('input.email_inquiry_hide_price_before_login').attr('checked', 'checked').attr('checkbox-disabled', 'true');
				$('.manual_quote_enable_guest_checkout_container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
					
			} else if ( $("input.apply_auto_quote_rule:checked").val() == 'yes' ) {
				$('input.hide_addcartbt_before_login').removeAttr('checked').attr('checkbox-disabled', 'true');
				$('input.email_inquiry_hide_price_before_login').removeAttr('checked').attr('checkbox-disabled', 'true');
				$('.auto_quote_enable_guest_checkout_container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
					
			} else if ( $("input.apply_add_to_order_rule:checked").val() == 'yes' ) {
				$('input.hide_addcartbt_before_login').removeAttr('checked').attr('checkbox-disabled', 'true');
				$('input.email_inquiry_hide_price_before_login').removeAttr('checked').attr('checkbox-disabled', 'true');
				$('.order_mode_enable_guest_checkout_container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );

			}
			
			/* 
			 * Condition logic for activate apply rule to logged in users
			 * Show Roles dropdown for : Hide Add to Cart, Show Email Inquiry Button, Hide Price, Add to Order rules
			 * Apply when page is loaded
			 */
			if ( $("input.hide_addcartbt_after_login:checked").val() == 'yes' ) {
				$('.hide_addcartbt_after_login_container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
			} else {
				$('.hide_addcartbt_after_login_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} );
			}
			if ( $("input.email_inquiry_hide_price_after_login:checked").val() == 'yes') {
				$('.email_inquiry_hide_price_after_login_container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
			} else {
				$('.email_inquiry_hide_price_after_login_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} );
			}
			if ( $("input.activate_order_logged_in:checked").val() == 'yes') {
				$('.role_apply_activate_order_logged_in_container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
			} else {
				$('.role_apply_activate_order_logged_in_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} );
			}
			
		},
		
		conditionLogicEvent: function () {
			
			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.rules_roles_explanation', function( event, value, status ) {
				if ( status == 'true' ) {
					$(".rules_roles_explanation_container").slideDown();
				} else {
					$(".rules_roles_explanation_container").slideUp();
				}
			});

			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.no_conflict_explanation', function( event, value, status ) {
				if ( status == 'true' ) {
					$(".no_conflict_explanation_container").slideDown();
				} else {
					$(".no_conflict_explanation_container").slideUp();
				}
			});

			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.product_page_explanation', function( event, value, status ) {
				if ( status == 'true' ) {
					$(".product_page_explanation_container").slideDown();
				} else {
					$(".product_page_explanation_container").slideUp();
				}
			});

			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.store_rules_explanation', function( event, value, status ) {
				if ( status == 'true' ) {
					$(".store_rules_explanation_container").slideDown();
				} else {
					$(".store_rules_explanation_container").slideUp();
				}
			});

			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.troubleshooting_explanation', function( event, value, status ) {
				if ( status == 'true' ) {
					$(".troubleshooting_explanation_container").slideDown();
				} else {
					$(".troubleshooting_explanation_container").slideUp();
				}
			});
			
			/* 
			 * Condition logic for not logged in users
			 */
			// Manual Quote Rule is activated :
			// deactivate Auto Quote Rule, Add to Order Rule
			// deactivate Hide Add to Cart Rule, activated Hide Price Rule and disabled both to admin can't change the status
			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.apply_manual_quote_rule', function( event, value, status ) {
				$('.enable_guest_checkout_container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
				if ( status == 'true' ) {
					$('input.apply_auto_quote_rule').removeAttr('checked').iphoneStyle("refresh");
					$('input.apply_add_to_order_rule').removeAttr('checked').iphoneStyle("refresh");
					
					$('input.hide_addcartbt_before_login').removeAttr('checked').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').attr('checked', 'checked').iphoneStyle("refresh");
					
					$('input.hide_addcartbt_before_login').attr('checkbox-disabled', 'true').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').attr('checkbox-disabled', 'true').iphoneStyle("refresh");
					
					$('.manual_quote_enable_guest_checkout_container').slideDown();
				} else {
					$('input.hide_addcartbt_before_login').removeAttr('checkbox-disabled').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').removeAttr('checkbox-disabled').iphoneStyle("refresh");
				}
			});
			
			// Auto Quote Rule is activated :
			// deactivate Manual Quote Rule, Add to Order Rule
			// deactivate Hide Add to Cart Rule, activated Hide Price Rule and disabled both to admin can't change the status
			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.apply_auto_quote_rule', function( event, value, status ) {
				$('.enable_guest_checkout_container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
				if ( status == 'true' ) {
					$('input.apply_manual_quote_rule').removeAttr('checked').iphoneStyle("refresh");
					$('input.apply_add_to_order_rule').removeAttr('checked').iphoneStyle("refresh");
					
					$('input.hide_addcartbt_before_login').removeAttr('checked').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').attr('checked', 'checked').iphoneStyle("refresh");
					
					$('input.hide_addcartbt_before_login').attr('checkbox-disabled', 'true').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').attr('checkbox-disabled', 'true').iphoneStyle("refresh");
					
					$('.auto_quote_enable_guest_checkout_container').slideDown();
				} else {
					$('input.hide_addcartbt_before_login').removeAttr('checkbox-disabled').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').removeAttr('checkbox-disabled').iphoneStyle("refresh");
				}
			});
			
			// Add to Order Rule is activated :
			// deactivate Manual Quote Rule, Auto Quote Rule
			// deactivate Hide Add to Cart Rule, Hide Price Rule and disabled them
			$(document).on( "a3rev-ui-onoff_checkbox-switch-end", '.apply_add_to_order_rule', function( event, value, status ) {
				$('.enable_guest_checkout_container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
				if ( status == 'true' ) {
					$('input.apply_manual_quote_rule').removeAttr('checked').iphoneStyle("refresh");
					$('input.apply_auto_quote_rule').removeAttr('checked').iphoneStyle("refresh");
					
					$('input.hide_addcartbt_before_login').removeAttr('checked').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').removeAttr('checked').iphoneStyle("refresh");
					
					$('input.hide_addcartbt_before_login').attr('checkbox-disabled', 'true').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').attr('checkbox-disabled', 'true').iphoneStyle("refresh");
					
					$('.order_mode_enable_guest_checkout_container').slideDown();
				} else {
					$('input.hide_addcartbt_before_login').removeAttr('checkbox-disabled').iphoneStyle("refresh");
					$('input.email_inquiry_hide_price_before_login').removeAttr('checkbox-disabled').iphoneStyle("refresh");
				}
			});

			
			/* 
			 * Condition logic for activate apply rule to logged in users
			 * Show Roles dropdown for : Hide Add to Cart, Show Email Inquiry Button, Hide Price, Add to Order rules
			 */
			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.hide_addcartbt_after_login', function( event, value, status ) {
				$('.hide_addcartbt_after_login_container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
				if ( status == 'true' ) {
					$(".hide_addcartbt_after_login_container").slideDown();
				} else {
					$(".hide_addcartbt_after_login_container").slideUp();
				}
			});
			
			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.email_inquiry_hide_price_after_login', function( event, value, status ) {
				$('.email_inquiry_hide_price_after_login_container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
				if ( status == 'true' ) {
					$(".email_inquiry_hide_price_after_login_container").slideDown();
				} else {
					$(".email_inquiry_hide_price_after_login_container").slideUp();
				}
			});
			$(document).on( "a3rev-ui-onoff_checkbox-switch", '.activate_order_logged_in', function( event, value, status ) {
				$('.role_apply_activate_order_logged_in_container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
				if ( status == 'true' ) {
					$(".role_apply_activate_order_logged_in_container").slideDown();
				} else {
					$(".role_apply_activate_order_logged_in_container").slideUp();
				}
			});
		},
		
		/* 
		 * Rules & Roles Schema
		 */
		rulesRolesSchema: function () {
			
			var role_manual_quote = $("select.role_apply_manual_quote").val();
			var role_auto_quote = $("select.role_apply_auto_quote").val();
			var role_add_to_order = $("select.role_apply_activate_order_logged_in").val();
			var role_hide_cart = $("select.role_apply_hide_cart").val();
			var role_hide_price = $("select.role_apply_hide_price").val();
			
			role_auto_quote = $(role_auto_quote).not(role_manual_quote).get();
			role_add_to_order = $(role_add_to_order).not(role_manual_quote).not(role_auto_quote).get();
			
			$("select.role_apply_hide_cart option").removeAttr("disabled");
			$("select.role_apply_hide_cart option").filter(function () {
			   if( $.inArray( $(this).val(), role_manual_quote) != -1 ) return true;
			   if( $.inArray( $(this).val(), role_auto_quote) != -1 ) return true;
			   if( $.inArray( $(this).val(), role_add_to_order) != -1 ) return true;
			}).removeAttr("selected").attr("disabled", "disabled");
			
			$("select.role_apply_hide_price option").removeAttr("disabled");
			$("select.role_apply_hide_price option").filter(function () {
			   if( $.inArray( $(this).val(), role_add_to_order) != -1 ) return true;
			}).removeAttr("selected").attr("disabled", "disabled");
			
			$("select.role_apply_activate_order_logged_in option").removeAttr("disabled");
			$("select.role_apply_activate_order_logged_in option").filter(function () {
			   if( $.inArray( $(this).val(), role_manual_quote) != -1 ) return true;
			   if( $.inArray( $(this).val(), role_auto_quote) != -1 ) return true;
			}).removeAttr("selected").attr("disabled", "disabled");
			
			$("select.role_apply_auto_quote option").not(":first").removeAttr("disabled");
			$("select.role_apply_auto_quote option").filter(function () {
			   if( $.inArray( $(this).val(), role_manual_quote) != -1 ) return true;
			   if( $.inArray( $(this).val(), role_add_to_order) != -1 ) return true;
			}).removeAttr("selected").attr("disabled", "disabled");
			
			$("select.role_apply_manual_quote option").not(":first").removeAttr("disabled");
			$("select.role_apply_manual_quote option").filter(function () {
			  if( $.inArray( $(this).val(), role_auto_quote) != -1 ) return true;
			   if( $.inArray( $(this).val(), role_add_to_order) != -1 ) return true;
			}).removeAttr("selected").attr("disabled", "disabled");
			
		},
		
		rulesRolesSchemaEvent: function () {
			
			$("select.role_apply_manual_quote").on( 'change', function() {
				a3revEIRulesRoles.rulesRolesSchema();
				$("select.role_apply_auto_quote").trigger("chosen:updated");
				$("select.role_apply_activate_order_logged_in").trigger("chosen:updated");
				$("select.role_apply_hide_cart").trigger("chosen:updated");
				$("select.role_apply_hide_price").trigger("chosen:updated");
			});
			
			$("select.role_apply_auto_quote").on( 'change', function() {
				a3revEIRulesRoles.rulesRolesSchema();
				$("select.role_apply_manual_quote").trigger("chosen:updated");
				$("select.role_apply_activate_order_logged_in").trigger("chosen:updated");
				$("select.role_apply_hide_cart").trigger("chosen:updated");
				$("select.role_apply_hide_price").trigger("chosen:updated");
			});
			
			$("select.role_apply_activate_order_logged_in").on( 'change', function() {
				a3revEIRulesRoles.rulesRolesSchema();
				$("select.role_apply_manual_quote").trigger("chosen:updated");
				$("select.role_apply_auto_quote").trigger("chosen:updated");
				$("select.role_apply_hide_cart").trigger("chosen:updated");
				$("select.role_apply_hide_price").trigger("chosen:updated");
			});
			
			$("select.role_apply_hide_cart").on( 'change', function() {
				a3revEIRulesRoles.rulesRolesSchema();
				$("select.role_apply_manual_quote").trigger("chosen:updated");
				$("select.role_apply_auto_quote").trigger("chosen:updated");
				$("select.role_apply_activate_order_logged_in").trigger("chosen:updated");
				$("select.role_apply_hide_price").trigger("chosen:updated");
			});
			
			$("select.role_apply_hide_price").on( 'change', function() {
				a3revEIRulesRoles.rulesRolesSchema();
				$("select.role_apply_manual_quote").trigger("chosen:updated");
				$("select.role_apply_auto_quote").trigger("chosen:updated");
				$("select.role_apply_activate_order_logged_in").trigger("chosen:updated");
				$("select.role_apply_hide_cart").trigger("chosen:updated");
			});
		}
	}
	
	$(document).ready(function() {
		
		a3revEIRulesRoles.initRulesRoles();
		a3revEIRulesRoles.conditionLogicEvent();
		
		a3revEIRulesRoles.rulesRolesSchema();
		a3revEIRulesRoles.rulesRolesSchemaEvent();
		
	});
	
})(jQuery);
</script>
    <?php	
	}
}

global $wc_ei_rules_roles_settings;
$wc_ei_rules_roles_settings = new WC_EI_Rules_Roles_Settings();

/** 
 * wc_ei_rules_roles_settings_form()
 * Define the callback function to show subtab content
 */
function wc_ei_rules_roles_settings_form() {
	global $wc_ei_rules_roles_settings;
	$wc_ei_rules_roles_settings->settings_form();
}

?>
