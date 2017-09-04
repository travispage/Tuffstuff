<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_EI_Fancybox_Popup_Settings
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
            	'name' 		=> __( 'Fancybox Pop-Up Settings', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_fancybox_settings_container',
                'id'		=> 'wc_ei_fancybox_settings_box',
                'is_box'	=> true,
           	),
           	array(
				'name' 		=> __( 'Pop-Up Maximum Width', 'wc_email_inquiry' ),
				'id' 		=> 'wc_email_inquiry_fancybox_popup_settings[fancybox_popup_width]',
				'desc'		=> 'px',
				'type' 		=> 'slider',
				'default'	=> 600,
				'min'		=> 520,
				'max'		=> 800,
				'increment'	=> 10,
				'separate_option'	=> true,
			),
			array(
				'name' 		=> __( 'Pop-Up Maximum Height', 'wc_email_inquiry' ),
				'id' 		=> 'wc_email_inquiry_fancybox_popup_settings[fancybox_popup_height]',
				'desc'		=> 'px',
				'type' 		=> 'slider',
				'default'	=> 500,
				'min'		=> 300,
				'max'		=> 600,
				'increment'	=> 10,
				'separate_option'	=> true,
			),
			array(
				'name' 		=> __( "Fix Position on Scroll", 'wc_email_inquiry' ),
				'id' 		=> 'wc_email_inquiry_fancybox_popup_settings[fancybox_center_on_scroll]',
				'type' 		=> 'onoff_radio',
				'default'	=> 'true',
				'onoff_options' => array(
					array(
						'val' 				=> 'true',
						'text'				=> __( 'Pop-up stays centered in screen while page scrolls behind it.', 'wc_email_inquiry' ) ,
						'checked_label'		=> 'ON',
						'unchecked_label' 	=> 'OFF',
					),

					array(
						'val' 				=> 'false',
						'text' 				=> __( 'Pop-up scrolls up and down with the page.', 'wc_email_inquiry' ) ,
						'checked_label'		=> 'ON',
						'unchecked_label' 	=> 'OFF',
					)
				),
				'separate_option'	=> true,
			),
			array(
				'name' 		=> __( 'Open Transition Effect', 'wc_email_inquiry' ),
				'desc' 		=> __( "Choose a pop-up opening effect. Default - None.", 'wc_email_inquiry' ),
				'id' 		=> 'wc_email_inquiry_fancybox_popup_settings[fancybox_transition_in]',
				'css' 		=> 'width:80px;',
				'type' 		=> 'select',
				'default'	=> 'none',
				'options'	=> array(
						'none'			=> __( 'None', 'wc_email_inquiry' ) ,
						'fade'			=> __( 'Fade', 'wc_email_inquiry' ) ,
						'elastic'		=> __( 'Elastic', 'wc_email_inquiry' ) ,
					),
				'separate_option'	=> true,
			),
			array(
				'name' 		=> __( 'Close Transistion Effect', 'wc_email_inquiry' ),
				'desc' 		=> __( "Choose a pop-up closing effect. Default - None.", 'wc_email_inquiry' ),
				'id' 		=> 'wc_email_inquiry_fancybox_popup_settings[fancybox_transition_out]',
				'css' 		=> 'width:80px;',
				'type' 		=> 'select',
				'default'	=> 'none',
				'options'	=> array(
						'none'			=> __( 'None', 'wc_email_inquiry' ) ,
						'fade'			=> __( 'Fade', 'wc_email_inquiry' ) ,
						'elastic'		=> __( 'Elastic', 'wc_email_inquiry' ) ,
					),
				'separate_option'	=> true,
			),
			array(
				'name' 		=> __( 'Opening Speed', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Milliseconds when open pop-up', 'wc_email_inquiry' ),
				'id' 		=> 'wc_email_inquiry_fancybox_popup_settings[fancybox_speed_in]',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
				'default'	=> '300',
				'separate_option'	=> true,
			),
			array(
				'name' 		=> __( 'Closing Speed', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Milliseconds when close pop-up', 'wc_email_inquiry' ),
				'id' 		=> 'wc_email_inquiry_fancybox_popup_settings[fancybox_speed_out]',
				'type' 		=> 'text',
				'css' 		=> 'width:40px;',
				'default'	=> '0',
				'separate_option'	=> true,
			),
			array(
				'name' 		=> __( 'Background Overlay Colour', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Select a colour or empty for no colour.', 'wc_email_inquiry' ). ' ' . __( 'Default', 'wc_email_inquiry' ). ' [default_value]',
				'id' 		=> 'wc_email_inquiry_fancybox_popup_settings[fancybox_overlay_color]',
				'type' 		=> 'color',
				'default'	=> '#666666',
				'separate_option'	=> true,
			),

        );
	}
}

global $wc_ei_fancybox_popup_settings;
$wc_ei_fancybox_popup_settings = new WC_EI_Fancybox_Popup_Settings();

?>