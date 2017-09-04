<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_EI_Read_More_Global_Settings
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
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->get_names();

  		// Define settings
     	$this->form_fields = apply_filters( 'wc_email_inquiry_read_more_settings' . '_settings_fields', array(

			array(
            	'name' 		=> __( "Read More Rules and Roles", 'wc_email_inquiry' ),
            	'desc'		=> __( 'Creates Read More button or linked text on Product Cards that links through to the product page. Option to independently set these options from each products edit page.', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_rule_read_more_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( "View before log in", 'wc_email_inquiry' ),
				'desc'		=> __( 'ON and users will see Read More on product cards before they login.', 'wc_email_inquiry' ),
				'class'		=> 'show_read_more_button_before_login',
				'id' 		=> 'show_read_more_button_before_login',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			array(
				'name' 		=> __( "View after login", 'wc_email_inquiry' ),
				'desc'		=> __( 'Select user roles that will see Read More on product cards when they log in.', 'wc_email_inquiry' ),
				'class'		=> 'show_read_more_button_after_login',
				'id' 		=> 'show_read_more_button_after_login',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
			array(
				'class'		=> 'show_read_more_button_after_login_container',
                'type' 		=> 'heading',
           	),
			array(
				'desc' 		=> '',
				'id' 		=> 'role_apply_show_read_more',
				'type' 		=> 'multiselect',
				'placeholder' => __( 'Choose Roles', 'wc_email_inquiry' ),
				'css'		=> 'width:600px; min-height:80px;',
				'options'	=> $roles,
			),
			array(
                'type' 		=> 'heading',
				'class'		=> 'yellow_message_container hide_read_more_yellow_message_container',
           	),
			array(
                'type' 		=> 'hide_read_more_yellow_message',
           	),

			array(
            	'name' 		=> __( 'Set Display Type', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_read_more_display_type_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( "Show Read More Button as", 'wc_email_inquiry' ),
				'class'		=> 'read_more_display_type',
				'id' 		=> 'display_type',
				'type' 		=> 'onoff_radio',
				'default'	=> 'under',
				'onoff_options' => array(
					array(
						'val' 				=> 'hover',
						'text'				=> __( 'Button that shows on mouse hover on product image.', 'wc_email_inquiry' ) ,
						'checked_label'		=> 'ON',
						'unchecked_label' 	=> 'OFF',
					),

					array(
						'val' 				=> 'under',
						'text' 				=> __( 'Show as button or link text above or below the Add to Cart button position.', 'wc_email_inquiry' ) ,
						'checked_label'		=> 'ON',
						'unchecked_label' 	=> 'OFF',
					)
				),
			),

        ));
	}

}

global $wc_ei_read_more_global_settings;
$wc_ei_read_more_global_settings = new WC_EI_Read_More_Global_Settings();

?>
