<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_EI_3RD_Contact_Form_Settings
{
	/**
	 * @var array
	 */
	public $form_fields = array();

	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->init_form_fields();
	}

	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {

  		// Define settings
     	$this->form_fields = array(

			array(
            	'name' 		=> __( 'Form Shortcode', 'wc_email_inquiry' ),
				'desc'		=> __( 'Create a contact form that applies to all Products by adding a form shortcode from the Contact Form 7 or Gravity Forms plugins.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_3rd_form_container',
                'id'		=> 'wc_ei_form_shortcode_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Enter Global Form Shortcode', 'wc_email_inquiry' ),
				'desc'		=> __( 'Can add unique form shortcode on each product page.', 'wc_email_inquiry' ),
				'id' 		=> 'contact_form_shortcode',
				'type' 		=> 'text',
				'default'	=> '',
			),

			array(
            	'name' 		=> __( 'Email Inquiry Shortcode Page', 'wc_email_inquiry' ),
				'desc'		=> sprintf( __("A 'Email Inquiry' page with the shortcode %s inserted should have been auto created on install. If not you need to manually create a new page and add the shortcode. Then set that page below so the plugin knows where to find it.", 'wc_email_inquiry'), WC_Email_Inquiry_3RD_ContactForm_Functions::$page_shortcode_1 ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_3rd_form_container',
                'id'		=> 'wc_ei_shortcode_page_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Email Inquiry Page', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Page contents:', 'wc_email_inquiry' ).' ' . WC_Email_Inquiry_3RD_ContactForm_Functions::$page_shortcode_1,
				'id' 		=> WC_Email_Inquiry_3RD_ContactForm_Functions::$page_option_key_1,
				'type' 		=> 'single_select_page',
				'default'	=> '',
				'separate_option'	=> true,
				'placeholder'		=> __( 'Select Page', 'wc_email_inquiry' ),
				'css'		=> 'width:300px;',
			),

			array(
            	'name' 		=> __( 'Form Header Message', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_3rd_form_container',
                'id'		=> 'wc_ei_custom_form_header_box',
                'is_box'	=> true,
           	),
           	array(
				'name' 		=> __( 'Header Meassage', 'wc_email_inquiry' ),
				'desc'		=> __( 'empty and no message will show at the top of the form', 'wc_email_inquiry' ),
				'id' 		=> 'custom_contact_form_heading',
				'type' 		=> 'text',
			),
			array(
				'name' 		=> __( 'Meassage Font', 'wc_email_inquiry' ),
				'id' 		=> 'custom_contact_form_heading_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '18px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#000000' )
			),

			array(
				'name' 		=> __( 'Form Open Options', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_3rd_form_container',
                'id'		=> 'wc_ei_custom_form_options_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Product Page', 'wc_email_inquiry' ),
				'class'		=> 'product_page_open_form_type',
				'id' 		=> 'product_page_open_form_type',
				'type' 		=> 'onoff_radio',
				'default' 	=> 'new_page',
				'onoff_options' => array(
					array(
						'val' 				=> 'new_page',
						'text' 				=> __( 'Open contact form on new page', 'wc_email_inquiry' ) . ' - ' . __( 'new window', 'wc_email_inquiry' ) . '<span class="description">(' . __( 'Default', 'wc_email_inquiry' ) . ')</span>',
						'checked_label'		=> __( 'ON', 'wc_email_inquiry') ,
						'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry') ,
					),
					array(
						'val' 				=> 'new_page_same_window',
						'text' 				=> __( 'Open contact form on new page', 'wc_email_inquiry' ) . ' - ' . __( 'same window', 'wc_email_inquiry' ),
						'checked_label'		=> __( 'ON', 'wc_email_inquiry') ,
						'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry') ,
					),
					array(
						'val' 				=> 'popup',
						'text' 				=> __( 'Open contact form by Pop-up', 'wc_email_inquiry' ),
						'checked_label'		=> __( 'ON', 'wc_email_inquiry') ,
						'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry') ,
					),
					array(
						'val' 				=> 'inner_page',
						'text' 				=> __( 'Open contact form on page (form opens by ajax under the inquiry button).', 'wc_email_inquiry' ),
						'checked_label'		=> __( 'ON', 'wc_email_inquiry') ,
						'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry') ,
					),
				),
			),
			array(
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_enable_button_on_cards_containers',
           	),
			array(
				'name' 		=> __( 'Product Card', 'wc_email_inquiry' ),
				'class'		=> 'category_page_open_form_type',
				'id' 		=> 'category_page_open_form_type',
				'type' 		=> 'onoff_radio',
				'default' 	=> 'new_page',
				'onoff_options' => array(
					array(
						'val' 				=> 'new_page',
						'text' 				=> __( 'Open contact form on new page', 'wc_email_inquiry' ) . ' - ' . __( 'new window', 'wc_email_inquiry' ) . '<span class="description">(' . __( 'Default', 'wc_email_inquiry' ) . ')</span>',
						'checked_label'		=> __( 'ON', 'wc_email_inquiry') ,
						'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry') ,
					),
					array(
						'val' 				=> 'new_page_same_window',
						'text' 				=> __( 'Open contact form on new page', 'wc_email_inquiry' ) . ' - ' . __( 'same window', 'wc_email_inquiry' ),
						'checked_label'		=> __( 'ON', 'wc_email_inquiry') ,
						'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry') ,
					),
					array(
						'val' 				=> 'popup',
						'text' 				=> __( 'Open contact form by Pop-up', 'wc_email_inquiry' ),
						'checked_label'		=> __( 'ON', 'wc_email_inquiry') ,
						'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry') ,
					),
				),
			),

        );
	}

}

global $wc_ei_3rd_contact_form_settings;
$wc_ei_3rd_contact_form_settings = new WC_EI_3RD_Contact_Form_Settings();


?>