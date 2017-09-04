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

	// Get/prep product data
	$_product 	= $order->get_product_from_item( $item );
	$item_meta 	= new WC_Order_Item_Meta( $item['item_meta'] );

	// Title, sku, qty, price
	echo apply_filters( 'woocommerce_order_product_title', $item['name'], $_product );
	echo $show_sku && $_product->get_sku() ? ' (#' . $_product->get_sku() . ')' : '';

	// Variation
	echo $item_meta->meta ? "\n" . $item_meta->display( true, true ) : '';

	// Quantity
	echo "\n" . sprintf( wc_ei_ict_t__( 'Plugin Strings - Quantity: %s', __( 'Quantity: %s', 'wc_email_inquiry' ) ), $item['qty'] );

	// Cost
	if ( $order->status != 'quote' ) echo "\n" . sprintf( wc_ei_ict_t__( 'Plugin Strings - Cost: %s', __( 'Cost: %s', 'wc_email_inquiry' ) ), $order->get_formatted_line_subtotal( $item ) );

	// Download URLs
	if ( $show_download_links && $_product->exists() && $_product->is_downloadable() )
		echo "\n" . implode( "\n", $order->get_downloadable_file_urls( $item['product_id'], $item['variation_id'], $item ) );

	// Note
	if ( $show_purchase_note && $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) )
		echo "\n" . $purchase_note;

	echo "\n\n";

endforeach;
