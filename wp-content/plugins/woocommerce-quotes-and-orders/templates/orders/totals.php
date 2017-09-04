<?php
/**
 * Cart totals
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

?>
<div class="cart_totals <?php if ( $woocommerce->customer->has_calculated_shipping() ) echo 'calculated_shipping'; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

		<h2><?php wc_ei_ict_t_e( 'Plugin Strings - Order Totals', __( 'Order Totals', 'wc_email_inquiry' ) ); ?></h2>

		<table cellspacing="0">
			<tbody>

				<tr class="cart-subtotal">
					<th><strong><?php wc_ei_ict_t_e( 'Plugin Strings - Order Subtotal', __( 'Order Subtotal', 'wc_email_inquiry' ) ); ?></strong></th>
					<td><strong><?php echo $woocommerce->cart->get_cart_subtotal(); ?></strong></td>
				</tr>

				<?php if ( $woocommerce->cart->get_discounts_before_tax() ) : ?>

					<tr class="discount">
						<th><?php wc_ei_ict_t_e( 'Plugin Strings - Cart Discount', __( 'Cart Discount', 'wc_email_inquiry' ) ); ?> <a href="<?php echo add_query_arg( 'remove_discounts', '1', $woocommerce->cart->get_cart_url() ) ?>"><?php wc_ei_ict_t_e( 'Plugin Strings - [Remove]', __( '[Remove]', 'wc_email_inquiry' ) ); ?></a></th>
						<td>-<?php echo $woocommerce->cart->get_discounts_before_tax(); ?></td>
					</tr>

				<?php endif; ?>

				<?php foreach ( $woocommerce->cart->get_fees() as $fee ) : ?>

					<tr class="fee fee-<?php echo $fee->id ?>">
						<th><?php echo $fee->name ?></th>
						<td><?php
							if ( $woocommerce->cart->tax_display_cart == 'excl' )
								echo woocommerce_price( $fee->amount );
							else
								echo woocommerce_price( $fee->amount + $fee->tax );
						?></td>
					</tr>

				<?php endforeach; ?>

				<?php
					// Show the tax row if showing prices exclusive of tax only
					if ( $woocommerce->cart->tax_display_cart == 'excl' ) {
						foreach ( $woocommerce->cart->get_tax_totals() as $code => $tax ) {
							echo '<tr class="tax-rate tax-rate-' . $code . '">
								<th>' . $tax->label . '</th>
								<td>' . $tax->formatted_amount . '</td>
							</tr>';
						}
					}
				?>

				<?php if ( $woocommerce->cart->get_discounts_after_tax() ) : ?>

					<tr class="discount">
						<th><?php wc_ei_ict_t_e( 'Plugin Strings - Order Discount', __( 'Order Discount', 'wc_email_inquiry' ) ); ?> <a href="<?php echo add_query_arg( 'remove_discounts', '2', $woocommerce->cart->get_cart_url() ) ?>"><?php wc_ei_ict_t_e( 'Plugin Strings - [Remove]', __( '[Remove]', 'wc_email_inquiry' ) ); ?></a></th>
						<td>-<?php echo $woocommerce->cart->get_discounts_after_tax(); ?></td>
					</tr>

				<?php endif; ?>

			</tbody>
		</table>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>