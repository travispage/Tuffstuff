<?php
/*
Plugin Name: WooCommerce Quotes and Orders
Description: WooCommerce Quotes and Orders is an extension of the Email Inquiry and Cart options features and incorporates 2 new modes that the WooCommerce plugin can be operated in - Add to Quote mode or Add to Order mode.
Version: 2.2.0
Author: A3 Revolution
Author URI: http://www.a3rev.com/
License: This software is under commercial license and copyright to A3 Revolution Software Development team

	WooCommerce Quotes and Orders. Plugin for the WooCommerce shopping Cart.
	Copyright© 2011 A3 Revolution Software Development team

	A3 Revolution Software Development team
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/
?>
<?php
define('WC_EMAIL_INQUIRY_FILE_PATH', dirname(__FILE__));
define('WC_EMAIL_INQUIRY_DIR_NAME', basename(WC_EMAIL_INQUIRY_FILE_PATH));
define('WC_EMAIL_INQUIRY_FOLDER', dirname(plugin_basename(__FILE__)));
define('WC_EMAIL_INQUIRY_URL', untrailingslashit(plugins_url('/', __FILE__)));
define('WC_EMAIL_INQUIRY_DIR', WP_PLUGIN_DIR . '/' . WC_EMAIL_INQUIRY_FOLDER);
define('WC_EMAIL_INQUIRY_NAME', plugin_basename(__FILE__));
define('WC_EMAIL_INQUIRY_TEMPLATE_PATH', WC_EMAIL_INQUIRY_FILE_PATH . '/templates');
define('WC_EMAIL_INQUIRY_IMAGES_URL', WC_EMAIL_INQUIRY_URL . '/assets/images');
define('WC_EMAIL_INQUIRY_JS_URL', WC_EMAIL_INQUIRY_URL . '/assets/js');
define('WC_EMAIL_INQUIRY_CSS_URL', WC_EMAIL_INQUIRY_URL . '/assets/css');
if (!defined("WC_EMAIL_INQUIRY_MANAGER_URL")) define("WC_EMAIL_INQUIRY_MANAGER_URL", "http://a3api.com/plugins");

define('WC_EMAIL_INQUIRY_VERSION', '2.2.0' );

include ('admin/admin-ui.php');
include ('admin/admin-interface.php');

include ('classes/class-3rd-forms-functions.php');
include ('classes/class-wpml-functions.php');

include ('admin/admin-pages/admin-settings-page.php');
include ('admin/admin-pages/admin-quotes-mode-page.php');
include ('admin/admin-pages/admin-orders-mode-page.php');

include ('admin/admin-init.php');
include ('admin/less/sass.php');

include ('classes/class-wc-email-inquiry-functions.php');
include ('classes/class-wc-email-inquiry-bulk-quick-editions.php');
include ('classes/class-read-more-functions.php');
include ('classes/class-quote-order-functions.php');
include ('classes/class-wc-email-inquiry-hook.php');
include ('classes/class-quote-order-hook.php');
include ('classes/class-wc-email-inquiry-metabox.php');
include ('classes/class-send-quote-metabox.php');

include ('addons/class-gravityforms-addon.php');
include ('addons/class-contactform7-addon.php');

// Editor
include 'tinymce3/tinymce.php';

include ('admin/wc-email-inquiry-init.php');

include ('upgrade/wc-email-inquiry-upgrade.php');

if ( ! class_exists( 'a3_License_Manager_Plugin_Installer' ) ) {
	require_once ( 'a3-license-manager.php' );
}


/**
 * Call when the plugin is activated and deactivated
 */
register_activation_hook(__FILE__, 'wc_email_inquiry_install');
register_deactivation_hook(__FILE__, 'wc_email_inquiry_deactivated');

?>