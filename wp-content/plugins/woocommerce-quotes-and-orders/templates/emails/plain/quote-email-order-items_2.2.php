<?php
/**
 * Email Order Items (plain)
 *
 * @author 		A3rev
 * @package 	woocommerce-quotes-and-orders/templates/emails/plain
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

foreach ( $items as $item ) :
	$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
	$item_meta    = new WC_Order_Item_Meta( $item['item_meta'] );

	// Title
	echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );

	// SKU
	if ( $show_sku && $_product->get_sku() )
		echo ' (#' . $_product->get_sku() . ')';

	// Variation
	echo $item_meta->meta ? "\n" . $item_meta->display( true, true ) : '';

	// Quantity
	echo "\n" . sprintf( wc_ei_ict_t__( 'Plugin Strings - Quantity: %s', __( 'Quantity: %s', 'wc_email_inquiry' ) ), $item['qty'] );

	// Cost
	if ( version_compare( WC_VERSION, '2.2', '<' ) ) {
		if ( $order->status != 'quote' ) echo "\n" . sprintf( wc_ei_ict_t__( 'Plugin Strings - Cost: %s', __( 'Cost: %s', 'wc_email_inquiry' ) ), $order->get_formatted_line_subtotal( $item ) );
	} else {
		if ( $order->has_status( 'quote' ) ) echo "\n" . sprintf( wc_ei_ict_t__( 'Plugin Strings - Cost: %s', __( 'Cost: %s', 'wc_email_inquiry' ) ), $order->get_formatted_line_subtotal( $item ) );
	}

	// Download URLs
	if ( $show_download_links && $_product->exists() && $_product->is_downloadable() ) { 
		$download_files = $order->get_item_downloads( $item );
		$i              = 0;

		foreach ( $download_files as $download_id => $file ) {
			$i++;

			if ( count( $download_files ) > 1 ) {
				$prefix = sprintf( wc_ei_ict_t__( 'Plugin Strings - Download %d:', __('Download %d:', 'wc_email_inquiry' ) ), $i );
			} elseif ( $i == 1 )
				$prefix = wc_ei_ict_t__( 'Plugin Strings - Download', __( 'Download', 'wc_email_inquiry' ) );

			echo "\n" . $prefix . '(' . esc_html( $file['name'] ) . '): ' . esc_url( $file['download_url'] );
		}
	}

	// Note
	if ( $show_purchase_note && $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) )
		echo "\n" . $purchase_note;

	echo "\n\n";

endforeach;