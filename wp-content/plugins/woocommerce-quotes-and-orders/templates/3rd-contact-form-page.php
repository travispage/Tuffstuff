<?php
/**
 * The Template for compare products
 *
 * Override this template by copying it to yourtheme/woocommerce/product-compare.php
 *
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wp_query;

?>
<!doctype html>
<html>
<head>
<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> -->
<?php global $post; ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width; initial-scale=0.8; maximum-scale=1.0; minimum-scale=0.5;">
<title><?php echo addslashes( strip_tags( $post->post_title) ); ?> | <?php bloginfo('name'); ?></title>
<meta name="description" content="Default Description" />
<meta name="keywords" content="<?php bloginfo('name'); ?>" />
<meta name="robots" content="INDEX,FOLLOW" />
<style>
#wpadminbar, #back-top { display:none !important; }
html { margin:0 !important; background: #FFFFFF !important; }
body { background: #FFFFFF !important; }
</style>
<?php wp_head(); ?>
</head>
<body>
<?php
$product_id = 0;
if ( isset( $wp_query->query_vars['product-id'] ) )
	$product_id = $wp_query->query_vars['product-id'];
?>
<div id="page_3rd_contact_form_container">
<?php echo WC_Email_Inquiry_3RD_ContactForm_Functions::show_inquiry_form( $product_id, 1, 'popup' ); ?>
</div>
<?php wp_footer(); ?>
</body>
</html>