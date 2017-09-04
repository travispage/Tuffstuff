<?php
/**
 * The template for displaying Woocommerce pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * Template Name: Text Hero
 * @package mttr
 */

get_header(); 

if ( have_posts() ) {			

	/*
	*	Include Content Sidebar
	*/
	echo '<main>';


		/*
		*	Hero Data
		*/
		$data = array(

			'title' => mttr_get_contextual_title(),
			'modifiers' => 'band  band--large  u-hard--bottom  u-negate-btm-margin  u-overhang--bottom',

		);

		mttr_get_template( 'template-parts/hero/_c.hero-b', $data );



		/*
		*	Content Data
		*/

		$data = array(

			'left_content' => get_the_content(),
			'columns' => 1,
			'obj' => get_the_ID(),
			
		);

		mttr_get_template( 'template-parts/content/_c.content-wide', $data );

	echo '</main>';

}

get_footer(); 