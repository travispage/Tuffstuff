<?php
/**
 * WC EI Send Quote MetaBox
 *
 * Table Of Contents
 *
 * add_meta_boxes()
 * the_meta_forms()
 * save_meta_boxes()
 */
class WC_EI_Send_Quote_MetaBox
{
	
	public static function add_meta_boxes(){
		$pagename = 'shop_order';
		if ( isset( $_GET['post'] ) && $_GET['post'] > 0 ) {
			if ( version_compare( WC_VERSION, '2.2', '<' ) ) {
				$terms = wp_get_object_terms( (int) $_GET['post'], 'shop_order_status', array('fields' => 'slugs') );
				if (isset($terms[0]) && in_array ($terms[0], array( 'quote' ) ) ) {
					add_meta_box( 'wc_ei_send_quote_meta', __('Send Quote', 'wc_email_inquiry'), array('WC_EI_Send_Quote_MetaBox', 'the_meta_forms'), $pagename, 'side', 'core' );
				}
			} else {
				if ( substr( get_post_status( $_GET['post'] ), 3 ) == 'quote' ) {
					add_meta_box( 'wc_ei_send_quote_meta', __('Send Quote', 'wc_email_inquiry'), array('WC_EI_Send_Quote_MetaBox', 'the_meta_forms'), $pagename, 'side', 'core' );
				}
			}
		}
	}
	
	public static function the_meta_forms() {
		global $post;
		?>
        <div class="send_quote_note_container">
        	<div class="sent_quote_success" style="display:none;"><?php _e( 'You just sent the quote to customer.', 'wc_email_inquiry' ); ?></div>
			<h4><?php _e( 'Quote Note', 'wc_email_inquiry' ); ?> <img class="help_tip" data-tip='<?php esc_attr_e( 'Edit quote prices, shipping, taxes and add a note for customer. - Send and quote is updated then emailed to customer all in one action. Change Order Status from quote to pending before sending to allow this customer to pay for this order through the checkout.', 'wc_email_inquiry' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></h4>
			<p>
				<textarea type="text" name="wc_ei_quote_note" id="wc_ei_add_quote_note" class="input-text" cols="20" rows="5" style="width:100%;"></textarea>
			</p>
			<p>
            	<label style="float:left;"><input type="checkbox" name="wc_ei_quote_copy_to_admin" id="wc_ei_quote_copy_to_admin" value="1" /> <?php _e( 'Copy to admin', 'wc_email_inquiry' ); ?></label>
            	<input style="float:right;" class="button send_quote button-primary" type="submit" name="wc_ei_send_quote_note" value="<?php _e( 'Send Quote', 'wc_email_inquiry' ); ?>"  />
			</p>
			<?php
			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'wc_ei_sendquote_metabox_action', 'wc_ei_sendquote_metabox_nonce_field' );
			?>
            <div style="clear:both;"></div>
		</div>
		<?php
	}
	
	public static function save_meta_boxes( $post_id ){

		// Check if our nonce is set.
		if ( ! isset( $_POST['wc_ei_sendquote_metabox_nonce_field'] ) || ! check_admin_referer( 'wc_ei_sendquote_metabox_action', 'wc_ei_sendquote_metabox_nonce_field' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$post_status = get_post_status( $post_id );
		$post_type = get_post_type( $post_id );
		if ( $post_type == 'shop_order' && isset( $_REQUEST['wc_ei_send_quote_note'] ) && $post_status != false  && $post_status != 'inherit') {
			
			$quote_note = wp_kses_post( trim( stripslashes( $_REQUEST['wc_ei_quote_note'] ) ) );
			
			if ( !isset($_REQUEST['wc_ei_quote_copy_to_admin'] ) ) {
				$copy_to_admin = false ;
			} else {
				$copy_to_admin = true;
			}
			
			remove_all_actions( 'woocommerce_new_customer_note', 10 );
			
			$order = new WC_Order( $post_id );
			$comment_id = $order->add_order_note( __( 'Quote Sent', 'wc_email_inquiry' ).': '.$quote_note, 1 );
			update_post_meta( $post_id, '_wc_email_inquiry_sent_quote', true );
			
			$send_quote_data = array(
				'customer_note'		=> $quote_note,
				'order_id'			=> $post_id,
				'blogname'			=> get_option('blogname'),
				'first_name'		=> $order->billing_first_name,
				'last_name'			=> $order->billing_last_name,
				'customer_email'	=> $order->billing_email,
				'sent_to_admin'		=> $copy_to_admin,
			);
			WC_EI_Send_Quote_MetaBox::send_quote_email( $send_quote_data );
		}
	}
		
	public static function send_quote_email( $email_args=array() ) {
		global $wc_email_inquiry_quote_send_quote_email_settings;
		global $quote_send_quote_email_description;
		global $woocommerce;
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
		if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
			$mailer = $woocommerce->mailer();
		} else {
			$mailer = WC()->mailer();
		}
		
		extract($email_args);
						
		include_once( WC_EMAIL_INQUIRY_FILE_PATH.'/classes/emails/class-email-send-quote.php' );
		
		$subject = $wc_email_inquiry_quote_send_quote_email_settings['email_subject'];
		$heading = $wc_email_inquiry_quote_send_quote_email_settings['email_heading'];
		
		foreach (WC_Email_Inquiry_Quote_Order_Functions::shortcode_send_quote_email() as $key=>$value) {
			$subject = str_replace('{'.$key.'}', $$key , $subject);
			$heading = str_replace('{'.$key.'}', $$key , $heading);
			$quote_send_quote_email_description = str_replace('{'.$key.'}', $$key , $quote_send_quote_email_description);
		}
		
		$subject_copy = __('[Copy]:') . ' ' . $subject;
		$admin_email = get_option('admin_email');
		
		$order = new WC_Order( $order_id );
		$email_args['email_heading'] = $heading;
		$email_args['email_description'] = $quote_send_quote_email_description;
		$email_args['order'] = $order;
				
		$email_content = WC_Email_Inquiry_Send_Quote_Email::get_email_content( $email_args );
		
		$mailer->send( $customer_email, $subject, $email_content, WC_Email_Inquiry_Send_Quote_Email::get_header() );
		
		if ( $sent_to_admin ) {
			$mailer->send( $admin_email, $subject_copy, $email_content, WC_Email_Inquiry_Send_Quote_Email::get_header() );
		}
 	}
}
?>