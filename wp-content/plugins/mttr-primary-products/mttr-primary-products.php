<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Name: Matter Primary Products
 * Description: This plugin adds a custom post type for primary products.
 * Version: 1.0.0
 * Author: Matter Solutions
 * Author URI: http://www.mattersolutions.com.au
 */

// Our custom post type function
function mttr_custom_post_type_mttr_primary_products() {

	register_post_type( 'mttr_par_products',

		// CPT Options
		array(

			'labels' => array(
				'name' => __( 'Primary Products' ),
				'singular_name' => __( 'Primary Product' )
			),
			'public' => true,
			'publicly_queryable' => false,
			'has_archive' => true,
			'description'=> '',
			'rewrite' => array( 
				'slug' => 'primary-product',
				'with_front' => false,
			),
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'menu_icon'           => 'dashicons-info',
			'show_in_menu' => true,
			'supports' => array(
	            'editor',
	            'excerpt',
	            'thumbnail',
	            'title',
	        ),

		)

	);

}

// Hooking up our function to theme setup
add_action( 'init', 'mttr_custom_post_type_mttr_primary_products' );




// Add a shortcode for the name of the product
function mttr_shortcode_product_name( $atts ){

	$a = shortcode_atts( array(
    	'id' => get_the_ID(),
    ), $atts );

	return get_the_title( $id );

}
add_shortcode( 'mttr_product_name', 'mttr_shortcode_product_name' );