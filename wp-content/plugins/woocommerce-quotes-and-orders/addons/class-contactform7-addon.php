<?php
/**
 * WC Email Inquiry ContactFrom7 Addon
 *
 * Table Of Contents
 *
 * show_inquiry_form_from_shortcode()
 */

/* Shortcode handler */
add_action( 'wpcf7_init', array('WC_Email_Inquiry_ContactForm7_Addon', 'wpcf7_add_shortcode_custom'), 5 );

/* Tag generator */
add_action( 'admin_init', array('WC_Email_Inquiry_ContactForm7_Addon', 'add_tag_generator_custom'), 16 );

class WC_Email_Inquiry_ContactForm7_Addon 
{	
	public static $product_id = 0;
	
	public static function show_inquiry_form_from_shortcode( $shortcode='', $product_id=0 ) {
		self::$product_id = $product_id;
		
		return do_shortcode($shortcode);
	}
	
	public static function wpcf7_add_shortcode_custom() {
		if ( ! function_exists( 'wpcf7_add_shortcode' ) )
			return;

		wpcf7_add_shortcode( array('inquiry_product_name', 'inquiry_product_url'), array( 'WC_Email_Inquiry_ContactForm7_Addon', 'wpcf7_custom_shortcode_handler'), true );
	}
	
	public static function wpcf7_custom_shortcode_handler( $tag ) {
		$tag = new WPCF7_Shortcode( $tag );
	
		if ( empty( $tag->name ) )
			return '';
	
		$validation_error = wpcf7_get_validation_error( $tag->name );
	
		$class = wpcf7_form_controls_class( $tag->type );
	
		if ( $validation_error )
			$class .= ' wpcf7-not-valid';
	
		$atts = array();
	
		$atts['id'] = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
	
		$atts['name'] = $tag->name;
		$atts['type'] = 'hidden';
		
		$text = '';
		if ( $tag->basetype == 'inquiry_product_name') {
			$atts['value'] = esc_html( get_the_title( self::$product_id ) );
			$text = esc_html( get_the_title( self::$product_id ) );
		} elseif ( $tag->basetype == 'inquiry_product_url') {
			$atts['value'] = esc_url( get_permalink( self::$product_id ) );
			$text = '<a href="'.esc_url( get_permalink( self::$product_id ) ).'" target="_blank" title="" >'.esc_url( get_permalink( self::$product_id ) ).'</a>';
		}
	
		$atts = wpcf7_format_atts( $atts );
		
		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s %2$s">%3$s<input %4$s />%5$s</span>',
			$tag->name, $tag->get_class_option( $class ), $text, $atts, $validation_error );
	
		return $html;
	}
	
	public static function add_tag_generator_custom() {
		if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
			return;
			
		wpcf7_add_tag_generator( 'inquiry_product_name', __( 'Product Name', 'wc_email_inquiry' ), 'wpcf7-tg-pane-inquiry_product_name', array( 'WC_Email_Inquiry_ContactForm7_Addon', 'wpcf7_tg_pane_inquiry_product_name') );
		
		wpcf7_add_tag_generator( 'inquiry_product_url', __( 'Product URL', 'wc_email_inquiry' ), 'wpcf7-tg-pane-inquiry_product_url', array( 'WC_Email_Inquiry_ContactForm7_Addon', 'wpcf7_tg_pane_inquiry_product_url') );
	}
	
	public static function wpcf7_tg_pane_inquiry_product_name( $contact_form, $args = '' ) {
		if ( defined('WPCF7_VERSION') && version_compare( WPCF7_VERSION, '4.2.0',  '>' ) ) {
			self::wpcf7_tag_generator_custom( 'inquiry_product_name', $contact_form, $args );
		} else {
			self::wpcf7_tg_pane_custom( 'inquiry_product_name', $contact_form );
		}
	}
	
	public static function wpcf7_tg_pane_inquiry_product_url( $contact_form, $args = '' ) {
		if ( defined('WPCF7_VERSION') && version_compare( WPCF7_VERSION, '4.2.0',  '>' ) ) {
			self::wpcf7_tag_generator_custom( 'inquiry_product_url', $contact_form, $args );
		} else {
			self::wpcf7_tg_pane_custom( 'inquiry_product_url', $contact_form );
		}
	}

	public static function wpcf7_tag_generator_custom( $type = 'inquiry_product_name', $contact_form, $args = '' ) {
		$args = wp_parse_args( $args, array() );
		$type = $args['id'];

		if ( ! in_array( $type, array( 'inquiry_product_url' ) ) ) {
			$type = 'inquiry_product_name';
		}

		if ( 'inquiry_product_name' == $type ) {
			$description = __( "Generate a form-tag for show current Product Name.", 'wc_email_inquiry' );
		} elseif ( 'inquiry_product_url' == $type ) {
			$description = __( "Generate a form-tag for show currrent Product URI.", 'wc_email_inquiry' );
		}

	?>
<div class="control-box">
<fieldset>
<legend><?php echo esc_html( $description ); ?></legend>

<table class="form-table">
<tbody>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>

</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>

	<?php
	}

	public static function wpcf7_tg_pane_custom( $type = 'inquiry_product_name', $contact_form ) {
	?>
	<div id="wpcf7-tg-pane-<?php echo $type; ?>" class="hidden">
	<form action="">
	<table>
	<tr><td><?php echo esc_html( __( 'Name', 'wpcf7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td><td></td></tr>
	</table>
	
	<table>
	<tr>
	<td><code>id</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
	<input type="text" name="id" class="idvalue oneline option" /></td>
	
	<td><code>data class</code> (<?php echo esc_html( __( 'optional', 'wpcf7' ) ); ?>)<br />
	<input type="text" name="class" class="classvalue oneline option" /></td>
	</tr>
	</table>
	
	<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'wpcf7' ) ); ?><br /><input type="text" name="<?php echo $type; ?>" class="tag" readonly="readonly" onfocus="this.select()" /></div>
	
	<div class="tg-mail-tag"><?php echo esc_html( __( "And, put this code into the Mail fields below.", 'wpcf7' ) ); ?><br /><span class="arrow">&#11015;</span>&nbsp;<input type="text" class="mail-tag" readonly="readonly" onfocus="this.select()" /></div>
	</form>
	</div>
	<?php
	}
}
?>