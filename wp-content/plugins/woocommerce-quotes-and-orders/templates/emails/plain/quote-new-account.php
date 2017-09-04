<?php
/**
 * Customer new account email
 *
 * @author 		A3rev
 * @package 	woocommerce-quotes-and-orders/templates/emails/plain
 * @version     2.0.0
 */

echo $email_heading . "\n\n";

echo WC_Email_Inquiry_Quote_Order_Functions::get_new_account_email_content( $email_args );

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );