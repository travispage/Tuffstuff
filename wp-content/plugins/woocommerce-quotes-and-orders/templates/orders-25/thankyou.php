<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wc_email_inquiry_order_order_received_top_message;
global $wc_email_inquiry_order_order_received_bottom_message;

if ( $order ) : ?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<p class="woocommerce-thankyou-order-failed"><?php wc_ei_ict_t_e( 'Plugin Strings - Order Declined Transaction', __( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.', 'wc_email_inquiry' ) ); ?></p>

		<p class="woocommerce-thankyou-order-failed-actions">
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My Account', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>

		<?php echo wpautop(wptexturize( $wc_email_inquiry_order_order_received_top_message )); ?>

		<ul class="woocommerce-thankyou-order-details order_details">
			<li class="order">
				<?php wc_ei_ict_t_e( 'Plugin Strings - Order Number:', __( 'Order Number:', 'wc_email_inquiry' ) ); ?>
				<strong><?php echo $order->get_order_number(); ?></strong>
			</li>
			<li class="date">
				<?php wc_ei_ict_t_e( 'Plugin Strings - Date', __( 'Date', 'wc_email_inquiry' ) ); ?>:
				<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
			</li>
			<li class="total">
				<?php wc_ei_ict_t_e( 'Plugin Strings - Total', __( 'Total', 'wc_email_inquiry' ) ); ?>:
				<strong><?php echo $order->get_formatted_order_total(); ?></strong>
			</li>
			<?php if ( $order->payment_method_title ) : ?>
			<li class="method">
				<?php wc_ei_ict_t_e( 'Plugin Strings - Payment Method', __( 'Payment Method', 'wc_email_inquiry' ) ); ?>:
				<strong><?php echo $order->payment_method_title; ?></strong>
			</li>
			<?php endif; ?>
		</ul>
		<div class="clear"></div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>
    
    <?php echo wpautop(wptexturize( $wc_email_inquiry_order_order_received_bottom_message )); ?>

<?php else : ?>

	<p class="woocommerce-thankyou-order-received"><?php wc_ei_ict_t_e( 'Plugin Strings - Thank you. Your order has been received.', __( 'Thank you. Your order has been received.', 'wc_email_inquiry' ) ); ?></p>

<?php endif; ?>
