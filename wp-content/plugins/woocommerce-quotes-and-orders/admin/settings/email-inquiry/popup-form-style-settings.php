<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_EI_Popup_Form_Style_Settings
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
            	'name' 		=> __( 'Default Form Background Colour', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_default_form_container',
                'id'		=> 'wc_ei_form_bg_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Background Colour', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'inquiry_form_bg_colour',
				'type' 		=> 'color',
				'default'	=> '#FFFFFF'
			),

			array(
            	'name' 		=> __( 'Default Form Fonts and Text', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_default_form_container',
                'id'		=> 'wc_ei_form_title_box',
                'is_box'	=> true,
           	),
           	array(
            	'name' 		=> __( 'Form Heading Text', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
           	),
			array(
				'name' 		=> __( 'Heading Text', 'wc_email_inquiry' ),
				'desc' 		=> __( "Leave Empty and the form title will default to Product Inquiry", 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_contact_heading',
				'type' 		=> 'text',
				'default'	=> ''
			),
			array(
				'name' 		=> __( 'Text Font', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_contact_heading_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '18px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#000000' )
			),

			array(
            	'name' 		=> __( 'Product Name', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
           	),
			array(
				'name' 		=> __( 'Product Name Font', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_form_product_name_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '26px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#29577F' )
			),

			array(
            	'name' 		=> __( 'Product URL', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
           	),
			array(
				'name' 		=> __( 'Product URL Font', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_form_product_url_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '12px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#29577F' )
			),

			array(
            	'name' 		=> __( 'Form Content Font', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
           	),
			array(
				'name' 		=> __( 'Content Font', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_contact_popup_text',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '12px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#000000' )
			),

			array(
            	'name' 		=> __( 'Email Subject Name', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
           	),
			array(
				'name' 		=> __( 'Subject Name Font', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_form_subject_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '12px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#000000' )
			),

			array(
            	'name' 		=> __( 'Default Form Text Input Fileds', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_default_form_container',
                'id'		=> 'wc_ei_form_input_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Background Colour', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'inquiry_input_bg_colour',
				'type' 		=> 'color',
				'default'	=> '#FAFAFA'
			),
			array(
				'name' 		=> __( 'Font Colour', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'inquiry_input_font_colour',
				'type' 		=> 'color',
				'default'	=> '#000000'
			),
			array(
				'name' 		=> __( 'Input Field Borders', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_input_border',
				'type' 		=> 'border',
				'default'	=> array( 'width' => '1px', 'style' => 'solid', 'color' => '#CCCCCC', 'corner' => 'square' , 'rounded_value' => 0 ),
			),

			array(
            	'name' 		=> __( 'Default Form SEND Button Style', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_default_form_container',
                'id'		=> 'wc_ei_form_button_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Send Button Text', 'wc_email_inquiry' ),
				'desc' 		=> __( "Leave empty and button will show SEND", 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_contact_text_button',
				'type' 		=> 'text',
				'default'	=> __( 'SEND', 'wc_email_inquiry' ),
			),
			array(
				'name' 		=> __( 'Button Font', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_contact_button_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '12px', 'face' => 'Arial, sans-serif', 'style' => 'normal', 'color' => '#FFFFFF' )
			),
			array(
				'name' 		=> __( 'Background Colour', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'inquiry_contact_button_bg_colour',
				'type' 		=> 'color',
				'default'	=> '#EE2B2B'
			),
			array(
				'name' 		=> __( 'Background Colour Gradient From', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'inquiry_contact_button_bg_colour_from',
				'type' 		=> 'color',
				'default'	=> '#FBCACA'
			),
			array(
				'name' 		=> __( 'Background Colour Gradient To', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'inquiry_contact_button_bg_colour_to',
				'type' 		=> 'color',
				'default'	=> '#EE2B2B'
			),
			array(
				'name' 		=> __( 'Button Border', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_contact_button_border',
				'type' 		=> 'border',
				'default'	=> array( 'width' => '1px', 'style' => 'solid', 'color' => '#EE2B2B', 'corner' => 'rounded' , 'rounded_value' => 3 ),
			),
			array(
				'name' => __( 'Button Shadow', 'wc_email_inquiry' ),
				'id' 		=> 'inquiry_contact_button_shadow',
				'type' 		=> 'box_shadow',
				'default'	=> array( 'enable' => 0, 'h_shadow' => '5px' , 'v_shadow' => '5px', 'blur' => '2px' , 'spread' => '2px', 'color' => '#999999', 'inset' => '' )
			),

        );
	}

}

global $wc_ei_popup_form_style_settings;
$wc_ei_popup_form_style_settings = new WC_EI_Popup_Form_Style_Settings();

?>