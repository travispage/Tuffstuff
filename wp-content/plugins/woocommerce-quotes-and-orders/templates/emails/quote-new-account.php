<?php
/**
 * Customer new account email
 *
 * @author 		A3rev
 * @package 	woocommerce-quotes-and-orders/templates/emails
 * @version     1.6.4
 */

?>

<?php do_action( 'woocommerce_email_header', $email_heading ); //woocommerce_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );?>

<?php echo WC_Email_Inquiry_Quote_Order_Functions::get_new_account_email_content( $email_args); ?>

<?php do_action('woocommerce_email_footer'); //woocommerce_get_template( 'emails/email-footer.php' ); ?>