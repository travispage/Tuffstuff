<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wc_email_inquiry_quote_cart_page;
?>

<p><?php echo $wc_email_inquiry_quote_cart_page['quote_cart_empty']; ?></p>

<?php do_action('woocommerce_cart_is_empty'); ?>

<p><a class="button" href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"><?php wc_ei_ict_t_e( 'Plugin Strings - Return To Shop', __( '&larr; Return To Shop', 'wc_email_inquiry' ) ) ?></a></p>