<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_EI_Read_More_Under_Image_Style_Settings
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
     	$this->form_fields = apply_filters( 'wc_ei_read_more_under_image_style' . '_settings_fields', array(

			array(
            	'name' 		=> __( 'Button/Hyperlink Show under Image', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'class'		=> 'wc_ei_read_more_under_image_container',
                'id'		=> 'wc_ei_read_more_under_image_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Button or Hyperlink Type', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_bt_type',
				'class' 	=> 'under_image_bt_type',
				'type' 		=> 'switcher_checkbox',
				'default'	=> 'button',
				'checked_value'		=> 'button',
				'unchecked_value'	=> 'link',
				'checked_label'		=> __( 'Button', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'Hyperlink', 'wc_email_inquiry' ),
			),
			array(
				'name' 		=> __( 'Relative Position', 'wc_email_inquiry' ),
				'desc'		=> __( 'Position relative to Add to Cart button location', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_bt_position',
				'type' 		=> 'switcher_checkbox',
				'default'	=> 'below',
				'checked_value'		=> 'below',
				'unchecked_value'	=> 'above',
				'checked_label' 	=> __( 'Below', 'wc_email_inquiry' ),
				'unchecked_label'	=> __( 'Above', 'wc_email_inquiry' ),
			),
			array(
				'name' 		=> __( 'Button or Hyperlink Magrin', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_bt_margin',
				'type' 		=> 'array_textfields',
				'ids'		=> array(
	 								array(
											'id' 		=> 'under_image_bt_margin_top',
	 										'name' 		=> __( 'Top', 'wc_email_inquiry' ),
	 										'css'		=> 'width:40px;',
	 										'default'	=> 5 ),

	 								array(  'id' 		=> 'under_image_bt_margin_bottom',
	 										'name' 		=> __( 'Bottom', 'wc_email_inquiry' ),
	 										'css'		=> 'width:40px;',
	 										'default'	=> 5 ),

									array(
											'id' 		=> 'under_image_bt_margin_left',
	 										'name' 		=> __( 'Left', 'wc_email_inquiry' ),
	 										'css'		=> 'width:40px;',
	 										'default'	=> 0 ),

									array(
											'id' 		=> 'under_image_bt_margin_right',
	 										'name' 		=> __( 'Right', 'wc_email_inquiry' ),
	 										'css'		=> 'width:40px;',
	 										'default'	=> 0 ),
	 							)
			),

			array(
            	'name' 		=> __( 'Hyperlink Styling', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
          		'class'		=> 'show_under_image_hyperlink_styling',
          		'id'		=> 'wc_ei_read_more_under_image_hyperlink_box',
          		'is_box'	=> true,
           	),
			array(
				'name' => __( 'Hyperlink Text', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Text for Hyperlink show under image', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_link_text',
				'type' 		=> 'text',
				'default'	=> __('Read More', 'wc_email_inquiry')
			),
			array(
				'name' 		=> __( 'Hyperlink Font', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_link_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '12px', 'face' => 'Arial', 'style' => 'bold', 'color' => '#000000' )
			),

			array(
				'name' 		=> __( 'Hyperlink hover Colour', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_link_font_hover_color',
				'type' 		=> 'color',
				'default'	=> '#999999'
			),

			array(
            	'name' 		=> __( 'Read More Under Image Button Style', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
          		'class' 	=> 'show_under_image_button_styling',
          		'id'		=> 'wc_ei_read_more_under_image_button_box',
          		'is_box'	=> true,
           	),
			array(
				'name' 		=> __( 'Button Text', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Text for Button show under image', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_bt_text',
				'type' 		=> 'text',
				'default'	=> __('Read More', 'wc_email_inquiry')
			),
			array(
				'name' 		=> __( 'Button Font', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_bt_font',
				'type' 		=> 'typography',
				'default'	=> array( 'size' => '12px', 'face' => 'Arial', 'style' => 'bold', 'color' => '#FFFFFF' )
			),
			array(
				'name' 		=> __( 'Button Padding', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Padding from Button text to Button border show under image', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_bt_padding',
				'type' 		=> 'array_textfields',
				'ids'		=> array(
	 								array(  'id' 		=> 'under_image_bt_padding_tb',
	 										'name' 		=> __( 'Top/Bottom', 'wc_email_inquiry' ),
	 										'class' 	=> '',
	 										'css'		=> 'width:40px;',
	 										'default'	=> '7' ),

	 								array(  'id' 		=> 'under_image_bt_padding_lr',
	 										'name' 		=> __( 'Left/Right', 'wc_email_inquiry' ),
	 										'class' 	=> '',
	 										'css'		=> 'width:40px;',
	 										'default'	=> '8' ),
	 							)
			),
			array(
				'name' 		=> __( 'Background Colour', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'under_image_bt_bg',
				'type' 		=> 'color',
				'default'	=> '#EE2B2B'
			),
			array(
				'name' 		=> __( 'Background Colour Gradient From', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'under_image_bt_bg_from',
				'type' 		=> 'color',
				'default'	=> '#FBCACA'
			),

			array(
				'name' 		=> __( 'Background Colour Gradient To', 'wc_email_inquiry' ),
				'desc' 		=> __( 'Default', 'wc_email_inquiry' ) . ' [default_value]',
				'id' 		=> 'under_image_bt_bg_to',
				'type' 		=> 'color',
				'default'	=> '#EE2B2B'
			),
			array(
				'name' 		=> __( 'Button Border', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_bt_border',
				'type' 		=> 'border',
				'default'	=> array( 'width' => '1px', 'style' => 'solid', 'color' => '#EE2B2B', 'corner' => 'rounded' , 'rounded_value' => 3 ),
			),
			array(
				'name' => __( 'Button Shadow', 'wc_email_inquiry' ),
				'id' 		=> 'under_image_bt_shadow',
				'type' 		=> 'box_shadow',
				'default'	=> array( 'enable' => 0, 'h_shadow' => '5px' , 'v_shadow' => '5px', 'blur' => '2px' , 'spread' => '2px', 'color' => '#999999', 'inset' => '' )
			),

        ));
	}
}

global $wc_ei_read_more_under_image_style_settings;
$wc_ei_read_more_under_image_style_settings = new WC_EI_Read_More_Under_Image_Style_Settings();

?>