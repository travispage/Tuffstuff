<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Name: Matter Testimonials
 * Description: This plugin adds a custom post type for testimonials.
 * Version: 1.0.0
 * Author: Matter Solutions
 * Author URI: http://www.mattersolutions.com.au
 */

// Our custom post type function
function mttr_custom_post_type_mttr_testimonials() {

	register_post_type( 'mttr_testimonials',

		// CPT Options
		array(

			'labels' => array(
				'name' => __( 'Testimonials' ),
				'singular_name' => __( 'Testimonial' )
			),
			'public' => true,
			'publicly_queryable' => false,
			'has_archive' => true,
			'description'=> '',
			'rewrite' => array( 
				'slug' => 'testimonial',
				'with_front' => false,
			),
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'menu_icon'           => 'dashicons-megaphone',
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
add_action( 'init', 'mttr_custom_post_type_mttr_testimonials' );



// Filter the columns on the testimonial listing in the admin area
function mttr_custom_columns_head( $defaults ) {
    unset( $defaults['date'] );
 
    $defaults['testimonial_date'] = __( 'Date', 'mttr' );
 
    return $defaults;
}
add_filter( 'manage_edit-mttr_testimonials_columns', 'mttr_custom_columns_head', 10 );


// Output date on the admin columns for the CPT
function mttr_custom_columns_content( $column_name, $post_id ) {
 
    if ( 'testimonial_date' == $column_name ) {

        $date = get_field( 'mttr_testimonials_date', $post_id );

        if ( $date ) {
        	
        	echo esc_html( $date );

        }

    }

}
add_action( 'manage_mttr_testimonials_posts_custom_column', 'mttr_custom_columns_content', 10, 2 );
