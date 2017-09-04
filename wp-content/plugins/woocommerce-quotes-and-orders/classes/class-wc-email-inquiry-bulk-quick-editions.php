<?php
/**
 * WC Email Inquiry Bulk Quick Editions
 *
 * Table Of Contents
 *
 *
 * create_custombox()
 * a3_people_metabox()
 */

add_action( 'manage_product_posts_custom_column', array( 'WC_Email_Inquiry_Bulk_Quick_Editions', 'column_content' ), 10, 2 );

add_action( 'bulk_edit_custom_box',  array( 'WC_Email_Inquiry_Bulk_Quick_Editions', 'admin_bulk_edit' ), 10, 2);
add_action( 'save_post', array( 'WC_Email_Inquiry_Bulk_Quick_Editions', 'admin_bulk_edit_save' ), 10, 2 );

add_action( 'quick_edit_custom_box',  array( 'WC_Email_Inquiry_Bulk_Quick_Editions', 'quick_edit' ), 10, 2 );
add_action( 'admin_enqueue_scripts', array( 'WC_Email_Inquiry_Bulk_Quick_Editions', 'quick_edit_scripts' ), 10 );
add_action( 'save_post', array( 'WC_Email_Inquiry_Bulk_Quick_Editions', 'quick_edit_save' ), 10, 2 );

class WC_Email_Inquiry_Bulk_Quick_Editions
{

	/**
	 * Custom Columns for Products
	 *
	 * @access public
	 * @param mixed $column
	 * @return void
	 */
	public static function column_content( $column_name, $post_id  ) {
		if ( $column_name == 'name' ) {
			$wc_ei_settings_custom = get_post_meta( $post_id, '_wc_ei_settings_custom', true);
			$contact_form_shortcode =  is_array( $wc_ei_settings_custom ) && isset( $wc_ei_settings_custom['contact_form_shortcode'] ) ? $wc_ei_settings_custom['contact_form_shortcode']: '';
			if ( '' == trim( $contact_form_shortcode ) ) {
				global $wc_email_inquiry_global_settings;
				$contact_form_shortcode = $wc_email_inquiry_global_settings['contact_form_shortcode'];
			}

			echo '<div class="hidden" style="display:none" id="wc_ei_contact_form_shortcode_inline_'.$post_id.'"><div class="wc_ei_contact_form_shortcode">'.esc_attr( $contact_form_shortcode ).'</div></div>';
		}
	}

	/**
	 * Custom bulk edit - form
	 *
	 * @access public
	 * @param mixed $column_name
	 * @param mixed $post_type
	 * @return void
	 */
	public static function admin_bulk_edit( $column_name, $post_type ) {
		if ( $column_name != 'name' || !in_array( $post_type, array( 'product' ) ) ) return;
		?>
		<fieldset class="inline-edit-col-left inline-edit-wc-ei-fields">
			<div id="wc-ei-fields-bulk" class="inline-edit-col">
				<h4><?php _e( 'WC Email Inquiry', 'wc_email_inquiry' ); ?></h4>
                <div class="">
                    <label class="inline-edit-tags">
                        <span class="title"><?php _e( 'Custom Form Shortcode', 'wc_email_inquiry' ); ?></span> &nbsp;&nbsp;&nbsp;
                        <span class="">
                            <select class="change_ei_contact_form_shortcode change_to" name="change_ei_contact_form_shortcode">
                            <?php
                                $options = array(
                                    '' 	=> __( '- No Change -', 'wc_email_inquiry' ),
                                    '1' => __( 'Change to:', 'wc_email_inquiry' ),
                                );
                                foreach ($options as $key => $value) {
                                    echo '<option value="' . $key . '">' . $value . '</option>';
                                }
                            ?>
                            </select>
                        </span>
                    </label>
                    <label class="wc-ei-contact-form-shortcode-value">
                        <input style="width: 99%;" type="text" class="wc_ei_contact_form_shortcode" name="_wc_ei_contact_form_shortcode" placeholder="<?php _e( 'Enter Form Shortcode', 'wc_email_inquiry' ); ?>" />
                    </label>
                </div>

				<input type="hidden" name="wc_ei_bulk_edit_nonce" value="<?php echo wp_create_nonce( 'wc_ei_bulk_edit_nonce' ); ?>" />
			</div>
		</fieldset>
		<?php
	}


	/**
	 * Custom bulk edit - save
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public static function admin_bulk_edit_save( $post_id, $post ) {

		if ( is_int( wp_is_post_revision( $post_id ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) return;
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
		if ( ! isset( $_REQUEST['wc_ei_bulk_edit_nonce'] ) || ! wp_verify_nonce( $_REQUEST['wc_ei_bulk_edit_nonce'], 'wc_ei_bulk_edit_nonce' ) ) return $post_id;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		if ( !in_array( $post->post_type, array( 'product' ) ) ) return $post_id;

		// Save fields
		if ( ! empty( $_REQUEST['change_ei_contact_form_shortcode'] ) && isset( $_REQUEST['_wc_ei_contact_form_shortcode'] ) ) {
			$wc_ei_settings_custom = get_post_meta( $post_id, '_wc_ei_settings_custom', true );
			if ( ! is_array( $wc_ei_settings_custom ) ) $wc_ei_settings_custom = array();
			$wc_ei_settings_custom['contact_form_shortcode'] = trim( $_REQUEST['_wc_ei_contact_form_shortcode'] );
			update_post_meta( $post_id, '_wc_ei_settings_custom', $wc_ei_settings_custom );
		}

	}

	/**
	 * Custom quick edit - form
	 *
	 * @access public
	 * @param mixed $column_name
	 * @param mixed $post_type
	 * @return void
	 */
	public static function quick_edit( $column_name, $post_type ) {
		if ( $column_name != 'product_cat' || !in_array( $post_type, array( 'product', 'post', 'page' ) ) ) return;
		?>
		<fieldset class="inline-edit-col-right">
			<div id="wc-ei-fields-quick" class="inline-edit-col">
				<h4><?php _e( 'WC Email Inquiry', 'wc_email_inquiry' ); ?></h4>
				<div>
					<label class="">
						<span class="title"><?php _e( 'Custom Form Shortcode', 'wc_email_inquiry' ); ?></span>
                        <span class=""><input style="width: 99%; " type="text" class="_wc_ei_contact_form_shortcode" name="_wc_ei_contact_form_shortcode" placeholder="<?php _e( 'Enter Form Shortcode', 'wc_email_inquiry' ); ?>" /></span>
					</label>
				</div>
				<input type="hidden" name="wc_ei_quick_edit_nonce" value="<?php echo wp_create_nonce( 'wc_ei_quick_edit_nonce' ); ?>" />
			</div>
		</fieldset>
		<?php
	}


	/**
	 * Custom quick edit - script
	 *
	 * @access public
	 * @param mixed $hook
	 * @return void
	 */
	public static function quick_edit_scripts( $hook ) {
		global $post_type;

		if ( $hook == 'edit.php' && in_array( $post_type, array( 'product' ) ) )
			wp_enqueue_script( 'wc_ei_quick-edit', WC_EMAIL_INQUIRY_JS_URL . '/quick-edit.js', array('jquery') );
	}


	/**
	 * Custom quick edit - save
	 *
	 * @access public
	 * @param mixed $post_id
	 * @param mixed $post
	 * @return void
	 */
	public static function quick_edit_save( $post_id, $post ) {

		if ( ! $_POST || is_int( wp_is_post_revision( $post_id ) ) || is_int( wp_is_post_autosave( $post_id ) ) ) return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;
		if ( ! isset( $_POST['wc_ei_quick_edit_nonce'] ) || ! wp_verify_nonce( $_POST['wc_ei_quick_edit_nonce'], 'wc_ei_quick_edit_nonce' ) ) return $post_id;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		if ( !in_array( $post->post_type, array( 'product' ) ) ) return $post_id;

		// Save fields
		if ( isset( $_POST['_wc_ei_contact_form_shortcode'] ) && trim( $_POST['_wc_ei_contact_form_shortcode'] ) != '' ) {
			$wc_ei_settings_custom = get_post_meta( $post_id, '_wc_ei_settings_custom', true );
			if ( ! is_array( $wc_ei_settings_custom ) ) $wc_ei_settings_custom = array();
			$wc_ei_settings_custom['contact_form_shortcode'] = trim( $_REQUEST['_wc_ei_contact_form_shortcode'] );
			update_post_meta( $post_id, '_wc_ei_settings_custom', $wc_ei_settings_custom );
		}
	}

}
?>