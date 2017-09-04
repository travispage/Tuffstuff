<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC EI Read More Global Settings

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

class WC_EI_Read_More_Settings extends WC_Email_Inquiry_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'read-more';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = 'wc_email_inquiry_read_more_settings';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_email_inquiry_read_more_settings';
	
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
		$custom_type = array( 'hide_read_more_yellow_message' );
		
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
				'success_message'	=> __( 'Read More Settings successfully saved.', 'wc_email_inquiry' ),
				'error_message'		=> __( 'Error: Read More Settings can not save.', 'wc_email_inquiry' ),
				'reset_message'		=> __( 'Read More Settings successfully reseted.', 'wc_email_inquiry' ),
			);
			
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );
			
		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );
		
		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'after_save_settings' ) );
		
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
	/* after_save_settings()
	/* Process when clean on deletion option is un selected */
	/*-----------------------------------------------------------------------------------*/
	public function after_save_settings() {
		if ( isset( $_POST['bt_save_settings'] ) && isset( $_POST['wc_ei_product_reset_read_more_settings'] ) ) {
			delete_option( 'wc_ei_product_reset_read_more_settings' );
			WC_Email_Inquiry_Functions::reset_products_read_more_button_settings();
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
			'name'				=> 'settings',
			'label'				=> __( 'Settings', 'wc_email_inquiry' ),
			'callback_function'	=> 'wc_ei_read_more_settings_form',
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
		$this->form_fields = array();

  		include_once( $this->admin_plugin_dir() . '/settings/read-more/global-settings.php' );
		global $wc_ei_read_more_global_settings;
		$this->form_fields = array_merge( $this->form_fields, $wc_ei_read_more_global_settings->form_fields );

		include_once( $this->admin_plugin_dir() . '/settings/read-more/hover-position-style-settings.php' );
		global $wc_ei_read_more_hover_style_settings;
		$this->form_fields = array_merge( $this->form_fields, $wc_ei_read_more_hover_style_settings->form_fields );

		include_once( $this->admin_plugin_dir() . '/settings/read-more/under-image-style-settings.php' );
		global $wc_ei_read_more_under_image_style_settings;
		$this->form_fields = array_merge( $this->form_fields, $wc_ei_read_more_under_image_style_settings->form_fields );

		$this->form_fields = array_merge( $this->form_fields, array(
			array(
				'name'		=> __( 'Read More Global Reset', 'wc_email_inquiry' ),
                'type' 		=> 'heading',
                'id'		=> 'wc_ei_rule_read_more_reset_box',
                'is_box'	=> true,
           	),
			array(
				'name' 		=> __( "Reset All Products", 'wc_email_inquiry' ),
				'desc' 		=> __( "Switch ON and Save Changes to reset ALL Product Cards Read More button or hyperlink settings back to the settings here on this tab.", 'wc_email_inquiry' )
				.'<br />'.__( "<strong>Important</strong> Clear your cache after so that visitors see changes.", 'wc_email_inquiry' ),
				'id' 		=> 'wc_ei_product_reset_read_more_settings',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'no',
				'separate_option'	=> true,
				'checked_value'		=> 'yes',
				'unchecked_value' 	=> 'no',
				'checked_label'		=> __( 'ON', 'wc_email_inquiry' ),
				'unchecked_label' 	=> __( 'OFF', 'wc_email_inquiry' ),
			),
		) );

	}
	
	public function hide_read_more_yellow_message( $value ) {
		$customized_settings = get_option( $this->option_name, array() );
	?>
    	<tr valign="top" class="hide_read_more_yellow_message_tr" style=" ">
			<th scope="row" class="titledesc">&nbsp;</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
            <?php 
				$hide_read_more_blue_message = '<div><strong>'.__( 'Tip', 'wc_email_inquiry' ).':</strong> '.__( "The 'Read More' Button / Text Link shows on the Product Cards only.  Can be individually customized for each product from the product edit page > Email & Cart Meta > Read More tab.", 'wc_email_inquiry' ).'</div>
                <div style="clear:both"></div>
                <a class="hide_read_more_yellow_message_dontshow" style="float:left;" href="javascript:void(0);">'.__( "Don't show again", 'wc_email_inquiry' ).'</a>
                <a class="hide_read_more_yellow_message_dismiss" style="float:right;" href="javascript:void(0);">'.__( "Dismiss", 'wc_email_inquiry' ).'</a>
                <div style="clear:both"></div>';
            	echo $this->blue_message_box( $hide_read_more_blue_message, '600px' ); 
			?>
<style>
.a3rev_panel_container .hide_read_more_yellow_message_container {
<?php if ( $customized_settings['show_read_more_button_before_login'] == 'no' && $customized_settings['show_read_more_button_after_login'] == 'no' ) echo 'display: none;'; ?>
<?php if ( get_option( 'wc_ei_hide_read_more_message_dontshow', 0 ) == 1 ) echo 'display: none !important;'; ?>
<?php if ( !isset($_SESSION) ) { @session_start(); } if ( isset( $_SESSION['wc_ei_hide_read_more_message_dismiss'] ) ) echo 'display: none !important;'; ?>
}
</style>
<script>
(function($) {
$(document).ready(function() {
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.show_read_more_button_after_login', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".hide_read_more_yellow_message_container").slideDown();
		} else if( $("input.show_read_more_button_before_login").prop( "checked" ) == false ) {
			$(".hide_read_more_yellow_message_container").slideUp();
		}
	});
	$(document).on( "a3rev-ui-onoff_checkbox-switch", '.show_read_more_button_before_login', function( event, value, status ) {
		if ( status == 'true' ) {
			$(".hide_read_more_yellow_message_container").slideDown();
		} else if( $("input.show_read_more_button_after_login").prop( "checked" ) == false ) {
			$(".hide_read_more_yellow_message_container").slideUp();
		}
	});
	
	$(document).on( "click", ".hide_read_more_yellow_message_dontshow", function(){
		$(".hide_read_more_yellow_message_tr").slideUp();
		$(".hide_read_more_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dontshow",
				option_name: 	"wc_ei_read_more_button_message_dontshow",
				security: 		"<?php echo wp_create_nonce("wc_ei_yellow_message_dontshow"); ?>"
			};
		$.post( "<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>", data);
	});
	
	$(document).on( "click", ".hide_read_more_yellow_message_dismiss", function(){
		$(".hide_read_more_yellow_message_tr").slideUp();
		$(".hide_read_more_yellow_message_container").slideUp();
		var data = {
				action: 		"wc_ei_yellow_message_dismiss",
				session_name: 	"wc_ei_hide_read_more_message_dismiss",
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
.yellow_message_container {
	margin-top: -15px;	
}
.yellow_message_container a {
	text-decoration:none;	
}
.yellow_message_container th, .yellow_message_container td, .show_read_more_button_after_login_container th, .show_read_more_button_after_login_container td {
	padding-top: 0 !important;
	padding-bottom: 0 !important;
}
</style>
<script>
(function($) {
	
	$(document).ready(function() {
		
		if ( $("input.show_read_more_button_after_login:checked").val() != 'yes') {
			$('.show_read_more_button_after_login_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		}

		if ( $("input.under_image_bt_type:checked").val() == 'button') {
			$(".show_under_image_hyperlink_styling").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		} else {
			$(".show_under_image_button_styling").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		}

		if ( $("input.read_more_display_type:checked").val() == 'under') {
			$(".wc_ei_read_more_on_hover_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		} else {
			$(".wc_ei_read_more_under_image_container").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
			$(".show_under_image_hyperlink_styling").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
			$(".show_under_image_button_styling").css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px'} );
		}
		
		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.rules_roles_explanation', function( event, value, status ) {
			if ( status == 'true' ) {
				$(".rules_roles_explanation_container").slideDown();
			} else {
				$(".rules_roles_explanation_container").slideUp();
			}
		});
			
			
		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.show_read_more_button_after_login', function( event, value, status ) {
			$('.show_read_more_button_after_login_container').attr('style','display:none;');
			if ( status == 'true' ) {
				$(".show_read_more_button_after_login_container").slideDown();
			} else {
				$(".show_read_more_button_after_login_container").slideUp();
			}
		});
		

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.under_image_bt_type', function( event, value, status ) {
			$(".show_under_image_button_styling").attr('style','display:none;');
			$(".show_under_image_hyperlink_styling").attr('style','display:none;');
			if ( status == 'true') {
				$(".show_under_image_button_styling").slideDown();
				$(".show_under_image_hyperlink_styling").slideUp();
			} else {
				$(".show_under_image_button_styling").slideUp();
				$(".show_under_image_hyperlink_styling").slideDown();
			}
		});

		$(document).on( "a3rev-ui-onoff_radio-switch", '.read_more_display_type', function( event, value, status ) {
			$(".wc_ei_read_more_on_hover_container").attr('style','display:none;');
			$(".wc_ei_read_more_under_image_container").attr('style','display:none;');
			$(".show_under_image_hyperlink_styling").attr('style','display:none;');
			$(".show_under_image_button_styling").attr('style','display:none;');
			if ( value == 'under' && status == 'true' ) {
				$(".wc_ei_read_more_on_hover_container").slideUp();
				$(".wc_ei_read_more_under_image_container").slideDown();
				if ( $("input.under_image_bt_type:checked").val() == 'button') {
					$(".show_under_image_hyperlink_styling").slideUp();
					$(".show_under_image_button_styling").slideDown();
				} else {
					$(".show_under_image_hyperlink_styling").slideDown();
					$(".show_under_image_button_styling").slideUp();
				}
			} else if ( status == 'true' ) {
				$(".wc_ei_read_more_on_hover_container").slideDown();
				$(".wc_ei_read_more_under_image_container").slideUp();
				$(".show_under_image_hyperlink_styling").slideUp();
				$(".show_under_image_button_styling").slideUp();
			}
		});

	});
	
})(jQuery);
</script>
    <?php
	}
	
}

global $wc_ei_read_more_settings;
$wc_ei_read_more_settings = new WC_EI_Read_More_Settings();

/** 
 * wc_ei_read_more_settings_form()
 * Define the callback function to show subtab content
 */
function wc_ei_read_more_settings_form() {
	global $wc_ei_read_more_settings;
	$wc_ei_read_more_settings->settings_form();
}

?>
