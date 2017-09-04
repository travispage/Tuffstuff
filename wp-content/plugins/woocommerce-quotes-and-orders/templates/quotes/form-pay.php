<?php
/**
 * Pay for order form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>
<form id="order_review" method="post">

	<table class="shop_table">
		<thead>
			<tr>
				<th class="product-name"><?php wc_ei_ict_t_e( 'Plugin Strings - Product', __( 'Product', 'wc_email_inquiry' ) ); ?></th>
				<th class="product-quantity"><?php wc_ei_ict_t_e( 'Plugin Strings - Qty', __( 'Qty', 'wc_email_inquiry' ) ); ?></th>
				<th class="product-total"><?php wc_ei_ict_t_e( 'Plugin Strings - Totals', __( 'Totals', 'wc_email_inquiry' ) ); ?></th>
			</tr>
		</thead>
		<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
				?>
				<tr>
					<th scope="row" colspan="2"><?php echo $total['label']; ?></th>
					<td class="product-total"><?php echo $total['value']; ?></td>
				</tr>
				<?php
			endforeach;
		?>
		</tfoot>
		<tbody>
			<?php
			if (sizeof($order->get_items())>0) :
				foreach ($order->get_items() as $item) :
					echo '
						<tr>
							<td class="product-name">'.$item['name'].'</td>
							<td class="product-quantity">'.$item['qty'].'</td>
							<td class="product-subtotal">' . $order->get_formatted_line_subtotal($item) . '</td>
						</tr>';
				endforeach;
			endif;
			?>
		</tbody>
	</table>

	<div id="payment">
		<ul class="payment_methods methods">
			<?php
				if ( $available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways() ) {
					// Chosen Method
					if ( sizeof( $available_gateways ) )
						current( $available_gateways )->set_current();

					foreach ( $available_gateways as $gateway ) {
						?>
						<li>
							<input type="radio" id="payment_method_<?php echo $gateway->id; ?>" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php if ($gateway->chosen) echo 'checked="checked"'; ?> />
							<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
							<?php
								if ( $gateway->has_fields() || $gateway->get_description() ) {
									echo '<div class="payment_box payment_method_' . $gateway->id . '" style="display:none;">';
									$gateway->payment_fields();
									echo '</div>';
								}
							?>
						</li>
						<?php
					}
				} else {

					echo '<p>'.wc_ei_ict_t__( 'Plugin Strings - Form Pay Error', __( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'wc_email_inquiry' ) ).'</p>';

				}
			?>
		</ul>

		<div class="form-row">
			<?php $woocommerce->nonce_field('pay')?>
			<input type="submit" class="button alt" id="place_order" value="<?php wc_ei_ict_t_e( 'Plugin Strings - Pay for order', __( 'Pay for order', 'wc_email_inquiry' ) ); ?>" />
			<input type="hidden" name="woocommerce_pay" value="1" />
		</div>

	</div>

</form>