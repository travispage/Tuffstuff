<?php
/**
 * Email Order Items
 *
 * @author 		A3rev
 * @package 	woocommerce-quotes-and-orders/templates/emails
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

foreach ( $items as $item ) :
	$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
	$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );
	?>
	<tr>
		<td style="text-align:left; vertical-align:middle; border: 1px solid #eee; word-wrap:break-word;"><?php

			// Show title/image etc
			if ( $show_image ) {
				echo apply_filters( 'woocommerce_order_item_thumbnail', '<img src="' . ( $_product->get_image_id() ? current( wp_get_attachment_image_src( $_product->get_image_id(), 'thumbnail') ) : wc_placeholder_img_src() ) .'" alt="' . __( 'Product Image', 'woocommerce' ) . '" height="' . esc_attr( $image_size[1] ) . '" width="' . esc_attr( $image_size[0] ) . '" style="vertical-align:middle; margin-right: 10px;" />', $item );
			}

			// Product name
			echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );

			// SKU
			if ( $show_sku && is_object( $_product ) && $_product->get_sku() ) {
				echo ' (#' . $_product->get_sku() . ')';
			}

			// File URLs
			if ( $show_download_links && is_object( $_product ) && $_product->exists() && $_product->is_downloadable() ) {

				$download_files = $order->get_item_downloads( $item );
				$i              = 0;

				foreach ( $download_files as $download_id => $file ) {
					$i++;

					if ( count( $download_files ) > 1 ) {
						$prefix = sprintf( wc_ei_ict_t__( 'Plugin Strings - Download %d:', __('Download %d:', 'wc_email_inquiry' ) ), $i );
					} elseif ( $i == 1 )
						$prefix = wc_ei_ict_t__( 'Plugin Strings - Download', __( 'Download', 'wc_email_inquiry' ) ).':';

					echo '<br/><small>' . $prefix . ' <a href="' . esc_url( $file['download_url'] ) . '" target="_blank">' . esc_html( $file['name'] ) . '</a></small>';
				}
			}

			// Variation
			if ( $item_meta->meta ) {
				echo '<br/><small>' . nl2br( $item_meta->display( true, true ) ) . '</small>';
			}

		?></td>
		<td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo $item['qty'] ;?></td>
        <?php if ( version_compare( WC_VERSION, '2.2', '<' ) ) { ?>
		<td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php if ( $order->status != 'quote' ) echo $order->get_formatted_line_subtotal( $item ); ?></td>
        <?php } else { ?>
        <td style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php if ( ! $order->has_status( 'quote' ) ) echo $order->get_formatted_line_subtotal( $item ); ?></td>
        <?php } ?>
	</tr>

	<?php if ( $show_purchase_note && is_object( $_product ) && $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) : ?>
		<tr>
			<td colspan="3" style="text-align:left; vertical-align:middle; border: 1px solid #eee;"><?php echo wpautop( do_shortcode( $purchase_note ) ); ?></td>
		</tr>
	<?php endif; ?>

<?php endforeach; ?>
