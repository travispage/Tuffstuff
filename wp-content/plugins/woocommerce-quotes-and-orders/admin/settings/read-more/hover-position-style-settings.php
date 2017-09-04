<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_EI_Read_More_Hover_Style_Settings
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
     	$this->form_fields = apply_filters( 'wc_ei_read_more_hover_position_style' . '_settings_fields', array(

			array(
            	'name' 		=> __( 'Button Show On Hover', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_read_more_on_hover_container',
                'id'		=> 'wc_ei_read_more_on_hover_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Button Text', 'wc_email_inquiry' ),
				'desc' 		=> __('Text for Read More Button Show On Hover', 'wc_email_inquiry'),
				'id' 		=> 'hover_bt_text',
				'type' 		=> 'text',
				'default'	=> __('Read More', 'wc_email_inquiry')
			),
			array(
				'name' 		=> __( 'Button Font', 'wc_email_inquiry' ),
				'id' 		=> 'hover_bt_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '14px', 'face' => 'Arial', 'style' => 'normal', 'color' => '#FFFFFF' )
			),
			array(
				'name' 		=> __( 'Button Align', 'wc_email_inquiry' ),
				'id' 		=> 'hover_bt_alink',
				'css' 		=> 'width:80px;',
				'type' 		=> 'select',
				'default'	=> 'center',
				'options'	=> array(
						'top'			=> __( 'Top', 'wc_email_inquiry' ) ,
						'center'		=> __( 'Center', 'wc_email_inquiry' ) ,
						'bottom'		=> __( 'Bottom', 'wc_email_inquiry' ) ,
					),
			),
			array(
				'name' => __( 'Button Padding', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Padding from Button text to Button border Show On Hover', 'wc_email_inquiry' ),
				'id' 		=> 'hover_bt_padding',
				'type' 		=> 'array_textfields',
				'ids'		=> array(
	 								array(  'id' 		=> 'hover_bt_padding_tb',
	 										'name' 		=> __( 'Top/Bottom', 'wc_email_inquiry' ),
	 										'class' 	=> '',
	 										'css'		=> 'width:40px;',
	 										'default'	=> '7' ),

	 								array(  'id' 		=> 'hover_bt_padding_lr',
	 										'name' 		=> __( 'Left/Right', 'wc_email_inquiry' ),
	 										'class' 	=> '',
	 										'css'		=> 'width:40px;',
	 										'default'	=> '17' ),
	 							)
			),

			array(
				'name' 		=> __( 'Background Colour', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry') . ' [default_value]',
				'id' 		=> 'hover_bt_bg',
				'type' 		=> 'color',
				'default'	=> '#999999'
			),
			array(
				'name' 		=> __( 'Background Colour Gradient From', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ). ' [default_value]',
				'id' 		=> 'hover_bt_bg_from',
				'type' 		=> 'color',
				'default'	=> '#999999'
			),
			array(
				'name' 		=> __( 'Background Colour Gradient To', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ). ' [default_value]',
				'id' 		=> 'hover_bt_bg_to',
				'type' 		=> 'color',
				'default'	=> '#999999'
			),
			array(
				'name' 		=> __( 'Button Transparency', 'wc_email_inquiry' ),
				'id' 		=> 'hover_bt_transparent',
				'desc'		=> '%',
				'type' 		=> 'slider',
				'default'	=> 50,
				'min'		=> 0,
				'max'		=> 100,
				'increment'	=> 10
			),
			array(
				'name' 		=> __( 'Button Border', 'wc_email_inquiry' ),
				'id' 		=> 'hover_bt_border',
				'type' 		=> 'border',
				'default'	=> array( 'width' => '1px', 'style' => 'solid', 'color' => '#FFFFFF', 'corner' => 'rounded' , 'rounded_value' => 3 ),
			),
			array(
				'name' => __( 'Button Shadow', 'wc_email_inquiry' ),
				'id' 		=> 'hover_bt_shadow',
				'type' 		=> 'box_shadow',
				'default'	=> array( 'enable' => 0, 'h_shadow' => '5px' , 'v_shadow' => '5px', 'blur' => '2px' , 'spread' => '2px', 'color' => '#999999', 'inset' => '' )
			),

        ));
	}
}

global $wc_ei_read_more_hover_style_settings;
$wc_ei_read_more_hover_style_settings = new WC_EI_Read_More_Hover_Style_Settings();

?>