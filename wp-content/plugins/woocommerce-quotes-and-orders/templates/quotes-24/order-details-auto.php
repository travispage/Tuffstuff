<?php
/**
 * Order details
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$order = wc_get_order( $order_id );
?>
<h2><?php wc_ei_ict_t_e( 'Plugin Strings - Quote Details', __( 'Quote Details', 'wc_email_inquiry' ) ); ?></h2>
<table class="shop_table order_details">
	<thead>
		<tr>
			<th class="product-name"><?php wc_ei_ict_t_e( 'Plugin Strings - Product', __( 'Product', 'wc_email_inquiry' ) ); ?></th>
			<th class="product-total"><?php wc_ei_ict_t_e( 'Plugin Strings - Total', __( 'Total', 'wc_email_inquiry' ) ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			foreach( $order->get_items() as $item_id => $item ) {
				wc_get_template( 'order/order-details-item.php', array(
					'order'   => $order,
					'item_id' => $item_id,
					'item'    => $item,
					'product' => apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item )
				) );
			}
		?>
		<?php do_action( 'woocommerce_order_items_table', $order ); ?>
	</tbody>
	<tfoot>
		<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
				<tr>
					<th scope="row"><?php echo $total['label']; ?></th>
					<td><?php echo $total['value']; ?></td>
				</tr>
				<?php
			}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

<p><a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" target="_parent"><?php wc_ei_ict_t_e( 'Plugin Strings - Pay Online Now', __('Pay Online Now', 'wc_email_inquiry') ); ?></a></p>

<?php wc_get_template( 'order/order-details-customer.php', array( 'order' =>  $order ) ); ?>
