<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

// Update order statuses
$wpdb->query( "
	UPDATE {$wpdb->posts} as posts
	LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_ID
	LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
	LEFT JOIN {$wpdb->terms} AS term USING( term_id )
	SET posts.post_status = 'wc-quote'
	WHERE posts.post_type = 'shop_order'
	AND posts.post_status = 'publish'
	AND tax.taxonomy = 'shop_order_status'
	AND	term.slug LIKE 'quote';
	"
);