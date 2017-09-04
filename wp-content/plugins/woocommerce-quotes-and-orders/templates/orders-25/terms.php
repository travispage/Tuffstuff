<?php
/**
 * Checkout terms and conditions checkbox
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) : ?>
    <p class="form-row terms wc-terms-and-conditions">
		<input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
        <label for="terms" class="checkbox"><?php wc_ei_ict_t_e( 'Plugin Strings - Read And Accept', __( 'I have read and accept the', 'wc_email_inquiry' ) ); ?> <a href="<?php echo esc_url( wc_get_page_permalink( 'terms' ) ); ?>" target="_blank"><?php wc_ei_ict_t_e( 'Plugin Strings - terms &amp; conditions', __( 'terms &amp; conditions', 'wc_email_inquiry' ) ); ?></a> <span class="required">*</span></label>
        <input type="hidden" name="terms-field" value="1" />
    </p>
<?php endif; ?>
