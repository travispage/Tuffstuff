<?php
/**
 * Order Customer Details
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header><h2><?php wc_ei_ict_t_e( 'Plugin Strings - Customer Details', __( 'Customer Details', 'wc_email_inquiry' ) ); ?></h2></header>

<table class="shop_table shop_table_responsive customer_details">
	<?php if ( $order->customer_note ) : ?>
		<tr>
			<th><?php _e( 'Note:', 'woocommerce' ); ?></th>
			<td><?php echo wptexturize( $order->customer_note ); ?></td>
		</tr>
	<?php endif; ?>

	<?php if ( $order->billing_email ) : ?>
		<tr>
			<th><?php wc_ei_ict_t_e( 'Plugin Strings - Email', __( 'Email', 'wc_email_inquiry' ) ); ?>:</th>
			<td><?php echo esc_html( $order->billing_email ); ?></td>
		</tr>
	<?php endif; ?>

	<?php if ( $order->billing_phone ) : ?>
		<tr>
			<th><?php wc_ei_ict_t_e( 'Plugin Strings - Telephone', __( 'Telephone', 'wc_email_inquiry' ) ); ?>:</th>
			<td><?php echo esc_html( $order->billing_phone ); ?></td>
		</tr>
	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
</table>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) : ?>

<div class="col2-set addresses">
	<div class="col-1">

<?php endif; ?>

<header class="title">
	<h3><?php wc_ei_ict_t_e( 'Plugin Strings - Billing Address', __( 'Billing Address', 'wc_email_inquiry' ) ); ?></h3>
</header>
<address>
	<?php echo ( $address = $order->get_formatted_billing_address() ) ? $address : wc_ei_ict_t__( 'Plugin Strings - N/A', __( 'N/A', 'wc_email_inquiry' ) ); ?>
</address>

<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ) : ?>

	</div><!-- /.col-1 -->
	<div class="col-2">
		<header class="title">
			<h3><?php wc_ei_ict_t_e( 'Plugin Strings - Shipping Address', __( 'Shipping Address', 'wc_email_inquiry' ) ); ?></h3>
		</header>
		<address>
			<?php echo ( $address = $order->get_formatted_shipping_address() ) ? $address : wc_ei_ict_t__( 'Plugin Strings - N/A', __( 'N/A', 'wc_email_inquiry' ) ); ?>
		</address>
	</div><!-- /.col-2 -->
</div><!-- /.col2-set -->

<?php endif; ?>
