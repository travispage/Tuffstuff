<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC EI Quotes Mode Send Quote Email Settings

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

class WC_EI_Quotes_Mode_New_Quote_Request_Email_Settings extends WC_Email_Inquiry_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'quotes-emails';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'wc_email_inquiry_quote_new_quote_request_email_settings';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_email_inquiry_quote_new_quote_request_email_settings';
	
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
		$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'New Quote Request Email Settings successfully saved.', 'wc_email_inquiry' ),
				'error_message'		=> __( 'Error: New Quote Request Email Settings can not save.', 'wc_email_inquiry' ),
				'reset_message'		=> __( 'New Quote Request Email Settings successfully reseted.', 'wc_email_inquiry' ),
			);
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
				
		add_action( $this->plugin_name . '_get_all_settings' , array( $this, 'get_settings' ) );

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
			'name'				=> 'new-quote-request',
			'label'				=> __( 'New Quote Request', 'wc_email_inquiry' ),
			'callback_function'	=> 'wc_ei_quotes_mode_new_quote_request_email_settings_form',
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
		
  		// Define settings			
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(
			
			array(
            	'name' 		=> __( "New Quote Request Email", 'wc_email_inquiry' ),
				'desc'		=> sprintf( __( 'Change the Recipient, Subject, Email Heading from <a href="%s" target="_blank">New Order email</a> for Quote Request.', 'wc_email_inquiry' ), admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_new_order', 'relative' ) ),
				'id'		=> 'quote_new_quote_request_email_table',
                'type' 		=> 'heading',
                'is_box'	=> true,
           	),
           	array(  
				'name' 		=> __( 'Recipient(s)', 'wc_email_inquiry' ),
				'desc' 		=> '</span><p class="description">' . __( "Enter recipients (comma separated) for this email. Defaults to <code>[default_value]</code>.", 'wc_email_inquiry' ) .'</p><span>',
				'id' 		=> 'email_recipient',
				'type' 		=> 'text',
				'default'	=> get_option('admin_email'),
			),
			array(  
				'name' 		=> __( 'Email Subject', 'wc_email_inquiry' ),
				'desc' 		=> '</span><p class="description">' . __( "This controls the email subject line. Default subject: <code>[default_value]</code>.", 'wc_email_inquiry' ) .'</p><span>',
				'id' 		=> 'email_subject',
				'type' 		=> 'text',
				'default'	=> __( '[{site_title}] New Customer Quote Request ({order_number}) - {order_date}', 'wc_email_inquiry' ),
			),
			array(  
				'name' 		=> __( 'Email Heading', 'wc_email_inquiry' ),
				'desc' 		=> '</span><p class="description">' . __( "This controls the main heading contained within the email notification. Default heading: <code>[default_value]</code>.", 'wc_email_inquiry' ) .'</p><span>',
				'id' 		=> 'email_heading',
				'type' 		=> 'text',
				'default'	=> __( 'New Customer Quote Request', 'wc_email_inquiry' ),
			),

        ));
	}

}

global $wc_ei_quotes_mode_new_quote_request_email_settings;
$wc_ei_quotes_mode_new_quote_request_email_settings = new WC_EI_Quotes_Mode_New_Quote_Request_Email_Settings();

/** 
 * wc_ei_quotes_mode_new_quote_request_email_settings_form()
 * Define the callback function to show subtab content
 */
function wc_ei_quotes_mode_new_quote_request_email_settings_form() {
	global $wc_ei_quotes_mode_new_quote_request_email_settings;
	$wc_ei_quotes_mode_new_quote_request_email_settings->settings_form();
}

?>
