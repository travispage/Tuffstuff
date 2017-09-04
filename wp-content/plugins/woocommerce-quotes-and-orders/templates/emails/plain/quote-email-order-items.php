<?php
/**
 * Email Order Items (plain)
 *
 * @author 		A3rev
 * @package 	woocommerce-quotes-and-orders/templates/emails/plain
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

foreach ( $items as $item_id => $item ) :
	$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
	$item_meta    = new WC_Order_Item_Meta( $item, $_product );

	if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {

		// Title
		echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item, false );

		// SKU
		if ( $show_sku && $_product->get_sku() ) {
			echo ' (#' . $_product->get_sku() . ')';
		}

		// allow other plugins to add additional product information here
		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order );

		// Variation
		echo ( $item_meta_content = $item_meta->display( true, true ) ) ? "\n" . $item_meta_content : '';

	// Quantity
	echo "\n" . sprintf( wc_ei_ict_t__( 'Plugin Strings - Quantity: %s', __( 'Quantity: %s', 'wc_email_inquiry' ) ), apply_filters( 'woocommerce_email_order_item_quantity', $item['qty'], $item ) );

	// Cost
	if ( $order->has_status( 'quote' ) ) echo "\n" . sprintf( wc_ei_ict_t__( 'Plugin Strings - Cost: %s', __( 'Cost: %s', 'wc_email_inquiry' ) ), $order->get_formatted_line_subtotal( $item ) );

		// Download URLs
		if ( $show_download_links && $_product->exists() && $_product->is_downloadable() ) {
			$download_files = $order->get_item_downloads( $item );
			$i              = 0;

			foreach ( $download_files as $download_id => $file ) {
				$i++;

				if ( count( $download_files ) > 1 ) {
					$prefix = sprintf( wc_ei_ict_t__( 'Plugin Strings - Download %d:', __('Download %d:', 'wc_email_inquiry' ) ), $i );
				} elseif ( $i == 1 ) {
					$prefix = wc_ei_ict_t__( 'Plugin Strings - Download', __( 'Download', 'wc_email_inquiry' ) );
				}

				echo "\n" . $prefix . '(' . esc_html( $file['name'] ) . '): ' . esc_url( $file['download_url'] );
			}
		}

		// allow other plugins to add additional product information here
		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order );

	}

	// Note
	if ( $show_purchase_note && ( $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) ) {
		echo "\n" . do_shortcode( wp_kses_post( $purchase_note ) );
	}

	echo "\n\n";

endforeach;
