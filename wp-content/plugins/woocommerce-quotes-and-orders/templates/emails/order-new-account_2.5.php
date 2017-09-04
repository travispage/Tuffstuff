<?php
/**
 * Customer new account email
 *
 * @author 		A3rev
 * @package 	woocommerce-quotes-and-orders/templates/emails
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php echo WC_Email_Inquiry_Quote_Order_Functions::get_new_account_email_content( $email_args); ?>

<?php do_action( 'woocommerce_email_footer' ); ?>