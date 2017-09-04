<?php
/**
 * Send quote email
 *
 * @author 		A3rev
 * @package 	woocommerce-quotes-and-orders/templates/emails
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php echo wpautop( wptexturize( $email_description ) ); ?>

<blockquote><?php echo wpautop(wptexturize( $customer_note )) ?></blockquote>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, false ); ?>

<h2><?php echo wc_ei_ict_t__( 'Plugin Strings - Quote', __( 'Quote', 'wc_email_inquiry' ) ) . ': ' . $order->get_order_number(); ?></h2>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:left;"><?php wc_ei_ict_t_e( 'Plugin Strings - Product', __( 'Product', 'wc_email_inquiry' ) ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php wc_ei_ict_t_e( 'Plugin Strings - Quantity', __( 'Quantity', 'wc_email_inquiry' ) ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php wc_ei_ict_t_e( 'Plugin Strings - Price', __( 'Price', 'wc_email_inquiry' ) ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		echo $order->email_order_items_table( array(
			'show_sku'    => false,
			'show_image'  => false,
			'image_size' => array( 32, 32 ),
			'plain_text'  => false
		) );
		?>
	</tbody>
	<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++;
					?><tr>
						<th class="td" scope="row" colspan="2" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
						<td class="td" style="text-align:left; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}
		?>
	</tfoot>
</table>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, false ); ?>

<?php
/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, false );
?>

<?php if ( $order->has_status( 'pending' ) ) { ?>
	<p> </p>
	<p><a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" target="_blank"><?php wc_ei_ict_t_e( 'Plugin Strings - Pay Online Now', __('Pay Online Now', 'wc_email_inquiry') ); ?></a></p>
<?php } ?>

<?php
/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, false );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer' );
?>
