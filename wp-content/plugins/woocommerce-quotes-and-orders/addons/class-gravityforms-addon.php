<?php
/**
 * WC Email Inquiry GravityForms Addon
 *
 * Table Of Contents
 *
 * show_inquiry_form_from_shortcode()
 * auto_replace_info()
 * add_custom_group()
 * add_custom_tooltips()
 * add_field_title()
 * add_field_input()
 * hook_into_gform_editor_js()
 * set_default_values()
 * add_advanced_settings()
 */
add_action( 'wp_enqueue_scripts', array( 'WC_Email_Inquiry_GravityForms_Addon', 'call_gravity_scripts' ), 11 );

add_filter( 'gform_shortcode_form', array('WC_Email_Inquiry_GravityForms_Addon', 'replace_product_information'), 1, 3 );
add_filter( 'gform_shortcode_conditional', array('WC_Email_Inquiry_GravityForms_Addon', 'replace_product_information'), 1, 3 );

// Add a custom field button to the custom group to the field editor
add_filter( 'gform_add_field_buttons', array('WC_Email_Inquiry_GravityForms_Addon', 'add_custom_group') );

// Adds title to GF custom field
add_filter( 'gform_field_type_title' , array('WC_Email_Inquiry_GravityForms_Addon', 'add_field_title'), 10, 2 );

// Add custom group tooltip
add_filter( 'gform_tooltips', array('WC_Email_Inquiry_GravityForms_Addon', 'add_custom_tooltips') );

// Adds the input hidden to the external side
add_action( 'gform_field_input' , array('WC_Email_Inquiry_GravityForms_Addon', 'add_field_input'), 10, 5 );

// Execute some javascript technicalitites for the field to load correctly
add_action( 'gform_editor_js', array('WC_Email_Inquiry_GravityForms_Addon', 'hook_into_gform_editor_js') );

// Set Default value and Label
add_action( 'gform_editor_js_set_default_values', array('WC_Email_Inquiry_GravityForms_Addon', 'set_default_values') );

// Add a custom setting to the advanced settings
add_action( 'gform_field_advanced_settings' , array('WC_Email_Inquiry_GravityForms_Addon', 'add_advanced_settings'), 10, 2 );


class WC_Email_Inquiry_GravityForms_Addon 
{	
	public static $product_id = 0;
	
	public static function call_gravity_scripts() {
		
		global $wc_email_inquiry_contact_form_settings, $wc_email_inquiry_global_settings;
		global $wp_query;
		$product_id = 0;
		if ( isset( $wp_query->query_vars['product-id'] ) ) {
			$product_id = $wp_query->query_vars['product-id'];
		} elseif ( is_singular( 'product' ) ) {
			global $post;
			$product_id = $post->ID;
			$open_type = $wc_email_inquiry_global_settings['product_page_open_form_type'];
			if ( $open_type != 'inner_page') return;
		}
			
		if ( $product_id == 0 || $product_id == '' ) return;
		
		if ( ! WC_Email_Inquiry_Functions::check_add_email_inquiry_button( $product_id ) ) return;
		if ( ! WC_Email_Inquiry_3RD_ContactForm_Functions::check_enable_3rd_contact_form() ) return;
		
		$wc_ei_settings_custom = get_post_meta( $product_id, '_wc_ei_settings_custom', true);
			
		if ( ! isset($wc_ei_settings_custom['contact_form_shortcode'] ) ) $contact_form_shortcode = trim( $wc_email_inquiry_global_settings['contact_form_shortcode'] );
		else $contact_form_shortcode = trim( esc_attr( $wc_ei_settings_custom['contact_form_shortcode'] ) );
		
		if ( trim( $contact_form_shortcode ) == '' ) $contact_form_shortcode = trim( $wc_email_inquiry_global_settings['contact_form_shortcode'] );
		
		if ( trim( $contact_form_shortcode ) == '' ) return;
		
		if ( stristr( $contact_form_shortcode, '[gravityform ' ) === false ) return;
		if ( ! class_exists( 'GFFormDisplay') ) return;
		
		$is_ajax = false;
        $forms = GFFormDisplay::get_embedded_forms( $contact_form_shortcode, $is_ajax );
		
		foreach ( $forms as $form ) {
			GFFormDisplay::enqueue_form_scripts( $form, $is_ajax );
        }
	}
	
	public static function show_inquiry_form_from_shortcode( $shortcode='', $product_id=0 ) {
		WC_Email_Inquiry_GravityForms_Addon::$product_id = $product_id;
		
		return do_shortcode($shortcode);
	}
	
	public static function replace_product_information( $shortcode_string, $attributes, $content ) {
		
		if ( !isset($attributes['product_id']) ) return $shortcode_string;
		
		$product_id = $attributes['product_id'];
		$shortcode_string = str_replace( '{wc_email_inquiry_form_product_name}', esc_html( get_the_title($product_id) ), $shortcode_string);
		$shortcode_string = str_replace( '{wc_email_inquiry_form_product_url}', esc_url( get_permalink($product_id) ), $shortcode_string);
		
		return $shortcode_string;
	}
	
	public static function add_custom_group( $field_groups ) {
		$custom_fields = array(
			array("class"=>"button", "value" => __('Product Name', 'wc_email_inquiry'), "onclick" => "StartAddField('wc_email_inquiry_product_name');"),
			array("class"=>"button", "value" => __('Product URL', 'wc_email_inquiry'), "onclick" => "StartAddField('wc_email_inquiry_product_url');"),
		);
																
		$field_groups[] = array("name" => "wc_email_inquiry", "label"=> __('Product Email Inquiry', 'wc_email_inquiry'), "fields" => $custom_fields);
		
		return $field_groups;
	}
	
	public static function add_custom_tooltips( $gf_tooltips ) {
		$gf_tooltips["form_wc_email_inquiry"] = "<h6>" . __('Product Email Inquiry', 'wc_email_inquiry') . "</h6>" . __("Email Inquiry fields allow you to add fields to your form that show the current product information.", "wc_email_inquiry");
		$gf_tooltips["form_field_wc_email_inquiry_input_css_class"] = "<h6>" . __('CSS Class Value', 'wc_email_inquiry') . "</h6>" . __("Enter the CSS class name you would like to use for value show in this field.", "wc_email_inquiry");
		
		return $gf_tooltips;
	}
	
	public static function add_field_title( $type_return, $type_check ) {
		if ( $type_check == 'wc_email_inquiry_product_name' )
			return __( 'Product Name' , 'wc_email_inquiry' );
		elseif ( $type_check == 'wc_email_inquiry_product_url' )
			return __( 'Product URL' , 'wc_email_inquiry' );
		else
			return $type_return;
	}
	
	public static function add_field_input( $input, $field, $value, $lead_id, $form_id ) {
		
		if ( $field["type"] == "wc_email_inquiry_product_name" ) {
			$field_type = IS_ADMIN ? "hidden" : "hidden";
			$class_attribute = IS_ADMIN ? "" : "gform_hidden";
			$disabled = (IS_ADMIN && RG_CURRENT_VIEW != "entry") ? "disabled='disabled'" : "";
			$css = isset( $field['wc_email_inquiry_input_cssClass'] ) ? $field['wc_email_inquiry_input_cssClass'] : '';
			
			if (WC_Email_Inquiry_GravityForms_Addon::$product_id != 0)
				$value = esc_html( get_the_title( WC_Email_Inquiry_GravityForms_Addon::$product_id ) );
			elseif ( trim($value) == '' )
				$value = '{wc_email_inquiry_form_product_name}';
			
			$text = IS_ADMIN ? "" : sprintf("<span class='%s'>%s</span>", 'wc_email_inquiry_form_product_name '.esc_attr($css), $value);

			$input = sprintf("<div class='ginput_container'>%s<input name='input_%d' id='product_name_%d' type='$field_type' class='large %s' value='%s' %s/></div>", $text, $field['id'], $field['id'], $class_attribute, $value, $disabled);
			
		} elseif ( $field["type"] == "wc_email_inquiry_product_url" ) {
			$field_type = IS_ADMIN ? "hidden" : "hidden";
			$class_attribute = IS_ADMIN ? "" : "gform_hidden";
			$disabled = (IS_ADMIN && RG_CURRENT_VIEW != "entry") ? "disabled='disabled'" : "";
			$css = isset( $field['wc_email_inquiry_input_cssClass'] ) ? $field['wc_email_inquiry_input_cssClass'] : '';
			
			if (WC_Email_Inquiry_GravityForms_Addon::$product_id != 0)
				$value = esc_url( get_permalink( WC_Email_Inquiry_GravityForms_Addon::$product_id ) );
			elseif ( trim($value) == '' )
				$value = '{wc_email_inquiry_form_product_url}';
			
			$text = IS_ADMIN ? "" : sprintf("<span class='%s'>%s</span>", 'wc_email_inquiry_form_product_url '.esc_attr($css), '<a href="'.$value.'" target="_blank" title="" >'.$value.'</a>');

			$input = sprintf("<div class='ginput_container'>%s<input name='input_%d' id='product_url_%d' type='$field_type' class='large %s' value='%s' %s/></div>", $text, $field['id'], $field['id'], $class_attribute, $value, $disabled);
		}
	
		return $input;
	}
	
	public static function hook_into_gform_editor_js(){
	?>
 
<script type='text/javascript'>
 
    jQuery(document).ready(function($) {
        fieldSettings["wc_email_inquiry_product_name"] = ".label_setting, .description_setting, .css_class_setting, .wc_email_inquiry_css_input_setting";
		fieldSettings["wc_email_inquiry_product_url"] = ".label_setting, .description_setting, .css_class_setting, .wc_email_inquiry_css_input_setting";
		
		//binding to the load field settings event to initialize the checkbox
        $(document).bind("gform_load_field_settings", function(event, field, form){
			jQuery("#field_wc_email_inquiry_input_css_class").val(field.wc_email_inquiry_input_cssClass == undefined ? "" : field.wc_email_inquiry_input_cssClass);
        });
    });
 
</script>
	<?php
	}
	
	public static function set_default_values() {
	?>
		case "wc_email_inquiry_product_name" :
			field.label = '<?php _e("Product Name", "wc_email_inquiry")?>';
			
            break;
            
		case "wc_email_inquiry_product_url" :
			field.label = '<?php _e("Product URL", "wc_email_inquiry")?>';
			
            break;
    <?php
	}
	
	public static function add_advanced_settings( $position, $form_id ) {
		// Create settings on position 325 (right after CSS Class Name)
		if ( $position == 325 ) {
	?>
		<li class="wc_email_inquiry_css_input_setting field_setting">
			<label for="field_wc_email_inquiry_input_css_class">
			<?php _e("CSS Class Value", "wc_email_inquiry"); ?>
			<?php gform_tooltip("form_field_wc_email_inquiry_input_css_class") ?>
			</label>
			<input type="text" id="field_wc_email_inquiry_input_css_class" size="30" onkeyup="SetFieldProperty('wc_email_inquiry_input_cssClass', this.value);"/>
		</li>
	<?php
		}
	}
}
?>