<?php
/**
 * The template for displaying POSTS.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package mttr
 */

get_header(); 

if ( have_posts() ) {

	$features = array();

	while ( have_posts() ) {		

		the_post(); 
		$features[] = get_the_ID();

	}				

	/*
	*	Include Content Sidebar
	*/
	echo '<main>';

		/*
		*	Search Bar
		*/
		$data = array(

			'modifiers' => 'search-bar--wide',

		);
		mttr_get_template( 'template-parts/header/_c.search-bar', $data );



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

		$menu = false;

		if ( has_nav_menu( 'sidebar' ) ) {

			$menu = array(

				'theme_location' => 'sidebar',
				'menu_class' => 'navigation  navigation--sidebar  u-flush--bottom',

			);

		}

		$main_content = false;

		if ( !( is_search() ) ) {

			$main_content = mttr_get_contextual_content();

		}

		$data = array(

			// 'sidebar' => get_field( 'mttr_options_sidebar_content', 'options', false, false ),
			// 'left_content' => $main_content,
			// 'menu' => $menu,
			'listing' => array(
				'grid' => 'wide', // Type of grid listing
				'style' => 'listing', // Feature style
				'features' => $features,
				'modifiers' => false,
			),
			
		);

		mttr_get_template( 'template-parts/content/_c.content-standard', $data );
		

	echo '</main>';

} else {

	mttr_get_template( 'template-parts/content/_c.content-none', array() );
	
}

get_footer(); 