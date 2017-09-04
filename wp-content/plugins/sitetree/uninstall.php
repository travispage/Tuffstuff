<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */
 
if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE `meta_key` LIKE '\_sitetree\_%'" );

	delete_option( 'sitetree' );
	delete_option( '_sitetree_backup' );
	delete_transient( 'sitetree_html5' );
	delete_transient( 'sitetree_xml' );
}
?>