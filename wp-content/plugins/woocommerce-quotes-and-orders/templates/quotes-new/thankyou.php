<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
global $wc_email_inquiry_quote_order_received_top_message;
global $wc_email_inquiry_quote_order_received_bottom_message;

if ( $order ) : ?>

	<?php if ( in_array( $order->status, array( 'failed' ) ) ) : ?>

		<p><?php wc_ei_ict_t_e( 'Plugin Strings - Declined Transaction', __( 'Unfortunately your request for a quote cannot be processed as the originating bank/merchant has declined your transaction.', 'wc_email_inquiry' ) ); ?></p>

		<p><?php
			if ( is_user_logged_in() )
				wc_ei_ict_t_e( 'Plugin Strings - Please attempt your purchase again or go to your account page.', __( 'Please attempt your purchase again or go to your account page.', 'wc_email_inquiry' ) );
			else
				wc_ei_ict_t_e( 'Plugin Strings - Please attempt your purchase again.', __( 'Please attempt your purchase again.', 'wc_email_inquiry' ) );
		?></p>

	<?php else : ?>

		<?php echo wpautop(wptexturize( $wc_email_inquiry_quote_order_received_top_message )); ?>

		<ul class="order_details">
			<li class="order">
				<?php wc_ei_ict_t_e( 'Plugin Strings - Quote', __( 'Quote', 'wc_email_inquiry' ) ); ?>:
				<strong><?php echo $order->get_order_number(); ?></strong>
			</li>
			<li class="date">
				<?php wc_ei_ict_t_e( 'Plugin Strings - Date', __( 'Date', 'wc_email_inquiry' ) ); ?>:
				<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
			</li>
			<?php if ( $order->payment_method_title ) : ?>
			<li class="method">
				<?php wc_ei_ict_t_e( 'Plugin Strings - Payment method', __( 'Payment method', 'wc_email_inquiry' ) ); ?>:
				<strong><?php echo $order->payment_method_title; ?></strong>
			</li>
			<?php endif; ?>
		</ul>
		<div class="clear"></div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>
    
    <?php echo wpautop(wptexturize( $wc_email_inquiry_quote_order_received_bottom_message )); ?>

<?php else : ?>

	<p><?php wc_ei_ict_t_e( 'Plugin Strings - Thanks for request a quote', __( 'Thank you. Your request for a quote has been received.', 'wc_email_inquiry' ) ); ?></p>

<?php endif; ?>
