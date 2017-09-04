<?php
/**
 * Order details
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$order = new WC_Order( $order_id );
?>
<h2><?php wc_ei_ict_t_e( 'Plugin Strings - Quote Details', __( 'Quote Details', 'wc_email_inquiry' ) ); ?></h2>
<table class="shop_table order_details">
	<thead>
		<tr>
			<th class="product-name"><?php wc_ei_ict_t_e( 'Plugin Strings - Product', __( 'Product', 'wc_email_inquiry' ) ); ?></th>
			<th class="product-total"><?php wc_ei_ict_t_e( 'Plugin Strings - Total', __( 'Total', 'wc_email_inquiry' ) ); ?></th>
		</tr>
	</thead>
    <tfoot>
	<?php
		if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $key => $total ) :
			?>
			<tr>
				<th scope="row"><?php echo $total['label']; ?></th>
				<td><?php echo $total['value']; ?></td>
			</tr>
			<?php
		endforeach;
	?>
	</tfoot>
	<tbody>
		<?php
		if (sizeof($order->get_items())>0) {

			foreach($order->get_items() as $item) {

				if ( version_compare( WC()->version, '2.2.0', '<' ) ) {
					$_product = get_product( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );
				} else {
					$_product = wc_get_product( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );
				}

				echo '
					<tr class = "' . esc_attr( apply_filters( 'woocommerce_order_table_item_class', 'order_table_item', $item, $order ) ) . '">
						<td class="product-name">' .
							apply_filters( 'woocommerce_order_table_product_title', '<a href="' . get_permalink( $item['product_id'] ) . '">' . $item['name'] . '</a>', $item ) . ' ' .
							apply_filters( 'woocommerce_order_table_item_quantity', '<strong class="product-quantity">&times; ' . $item['qty'] . '</strong>', $item );

				$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );
				$item_meta->display();

				if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

					$download_file_urls = $order->get_downloadable_file_urls( $item['product_id'], $item['variation_id'], $item );

					$i     = 0;
					$links = array();

					foreach ( $download_file_urls as $file_url => $download_file_url ) {

						$filename = woocommerce_get_filename_from_url( $file_url );

						$links[] = '<small><a href="' . $download_file_url . '">' . sprintf( wc_ei_ict_t__( 'Plugin Strings - Download file%s', __( 'Download file%s', 'wc_email_inquiry' ) ), ( count( $download_file_urls ) > 1 ? ' ' . ( $i + 1 ) . ': ' : ': ' ) ) . $filename . '</a></small>';

						$i++;
					}

					echo implode( '<br/>', $links );
				}

				echo '</td><td class="product-total">' . $order->get_formatted_line_subtotal( $item ) . '</td></tr>';

				// Show any purchase notes
				if ($order->status=='completed' || $order->status=='processing') {
					if ($purchase_note = get_post_meta( $_product->id, '_purchase_note', true))
						echo '<tr class="product-purchase-note"><td colspan="3">' . apply_filters('the_content', $purchase_note) . '</td></tr>';
				}

			}
		}

		do_action( 'woocommerce_order_items_table', $order );
		?>
	</tbody>
</table>

<?php if ( get_option('woocommerce_allow_customers_to_reorder') == 'yes' && $order->status=='completed' ) : ?>
	<p class="order-again">
		<a href="<?php echo esc_url( $woocommerce->nonce_url( 'order_again', add_query_arg( 'order_again', $order->id, add_query_arg( 'order', $order->id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) ) ) ) ); ?>" class="button"><?php wc_ei_ict_t_e( 'Plugin Strings - Order Again', __( 'Order Again', 'wc_email_inquiry' ) ); ?></a>
	</p>
<?php endif; ?>

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

<p><a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" target="_parent"><?php wc_ei_ict_t_e( 'Plugin Strings - Pay Online Now', __('Pay Online Now', 'wc_email_inquiry') ); ?></a></p>

<header>
	<h2><?php wc_ei_ict_t_e( 'Plugin Strings - Customer details', __( 'Customer details', 'wc_email_inquiry' ) ); ?></h2>
</header>
<dl class="customer_details">
<?php
	if ($order->billing_email) echo '<dt>'.wc_ei_ict_t__( 'Plugin Strings - Email', __( 'Email', 'wc_email_inquiry' ) ).':</dt><dd>'.$order->billing_email.'</dd>';
	if ($order->billing_phone) echo '<dt>'.wc_ei_ict_t__( 'Plugin Strings - Telephone', __( 'Telephone', 'wc_email_inquiry' ) ).':</dt><dd>'.$order->billing_phone.'</dd>';
?>
</dl>

<?php if (get_option('woocommerce_ship_to_billing_address_only')=='no') : ?>

<div class="col2-set addresses">

	<div class="col-1">

<?php endif; ?>

		<header class="title">
			<h3><?php wc_ei_ict_t_e( 'Plugin Strings - Billing Address', __( 'Billing Address', 'wc_email_inquiry' ) ); ?></h3>
		</header>
		<address><p>
			<?php
				if (!$order->get_formatted_billing_address()) wc_ei_ict_t_e( 'Plugin Strings - N/A', __( 'N/A', 'wc_email_inquiry' ) ); else echo $order->get_formatted_billing_address();
			?>
		</p></address>

<?php if (get_option('woocommerce_ship_to_billing_address_only')=='no') : ?>

	</div><!-- /.col-1 -->

	<div class="col-2">

		<header class="title">
			<h3><?php wc_ei_ict_t_e( 'Plugin Strings - Shipping Address', __( 'Shipping Address', 'wc_email_inquiry' ) ); ?></h3>
		</header>
		<address><p>
			<?php
				if (!$order->get_formatted_shipping_address()) wc_ei_ict_t_e( 'Plugin Strings - N/A', __( 'N/A', 'wc_email_inquiry' ) ); else echo $order->get_formatted_shipping_address();
			?>
		</p></address>

	</div><!-- /.col-2 -->

</div><!-- /.col2-set -->

<?php endif; ?>

<div class="clear"></div>