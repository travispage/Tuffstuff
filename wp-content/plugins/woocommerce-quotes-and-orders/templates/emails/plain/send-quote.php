<?php
/**
 * Send quote email
 *
 * @author 		A3rev
 * @package 	woocommerce-quotes-and-orders/templates/emails/plain
 * @version     2.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$woocommerce_db_version = get_option( 'woocommerce_db_version', null );

echo $email_heading . "\n\n";

echo wptexturize( $email_description ) . "\n\n";

echo "----------\n\n";

echo wptexturize( $customer_note ) . "\n\n";

echo "****************************************************\n\n";

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, true );

echo sprintf( wc_ei_ict_t__( 'Plugin Strings - Quote number: %s', __( 'Quote number: %s', 'wc_email_inquiry') ), $order->get_order_number() ) . "\n";
echo sprintf( wc_ei_ict_t__( 'Plugin Strings - Date: %s', __( 'Date: %s', 'wc_email_inquiry') ), date_i18n( ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) ? woocommerce_date_format() : wc_date_format() ), strtotime( $order->order_date ) ) ) . "\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, true );

echo "\n" . $order->email_order_items_table( $order->is_download_permitted(), true, '', '', '', true );

echo "----------\n\n";

if ( $totals = $order->get_order_item_totals() ) {
	foreach ( $totals as $total ) {
		echo $total['label'] . "\t " . $total['value'] . "\n";
	}
}

echo "\n****************************************************\n\n";

do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, true );

if ( ( version_compare( WC_VERSION, '2.2', '<' ) && $order->status == 'pending' ) || ( version_compare( WC_VERSION, '2.2', '>=' ) && $order->has_status( 'pending' ) ) ) {
	echo wc_ei_ict_t__( 'Plugin Strings - Pay Online Now', __('Pay Online Now', 'wc_email_inquiry') ). ': '; echo esc_url( $order->get_checkout_payment_url() ). "\n\n";
}

echo wc_ei_ict_t__( 'Plugin Strings - Your details', __( 'Your details', 'wc_email_inquiry' ) ) . "\n\n";

if ( $order->billing_email )
	echo wc_ei_ict_t__( 'Plugin Strings - Email', __( 'Email', 'wc_email_inquiry' ) ).': '; echo $order->billing_email. "\n";

if ( $order->billing_phone )
	echo wc_ei_ict_t__( 'Plugin Strings - Tel', __( 'Tel', 'wc_email_inquiry' ) ).':'; ?> <?php echo $order->billing_phone. "\n";

if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
	woocommerce_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) ); 
} else {
	wc_get_template( 'emails/plain/email-addresses.php', array( 'order' => $order ) );
}

echo "\n****************************************************\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );