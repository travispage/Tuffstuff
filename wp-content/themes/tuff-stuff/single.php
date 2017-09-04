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

	while ( have_posts() ) {

		the_post(); 


		/*
		*	Hero Data
		*/

		$hero_image_url = false;

		if ( has_post_thumbnail( get_the_ID() ) ) {

			$hero_image = get_post_thumbnail_id( get_the_ID() );

			$hero_image_url = wp_get_attachment_image_src( $hero_image, 'mttr_hero' );
			$hero_image_mobile_url = wp_get_attachment_image_src( $hero_image, 'mttr_feature' );

			$hero_image_url = $hero_image_url[0];			
			$hero_image_mobile_url = $hero_image_mobile_url[0];

		}

		$data = array(

			'title' => mttr_get_contextual_title(),
			'image' => $hero_image_url,
			'image_mobile' => $hero_image_mobile_url,
			'identifier' => 'main',
			'modifiers' => 'hero-standard--wide  hero-standard--left  hero-standard--blog  hero-standard--overlay  overlay--light',

		);

		mttr_get_template( 'template-parts/hero/_c.hero-standard', $data );



		/*
		*	Include Content Sidebar
		*/
		echo '<main>';

			// Get sidebar nav (if set)
			$menu = false;

			if ( has_nav_menu( 'sidebar' ) ) {

				$menu = array(

					'theme_location' => 'sidebar',
					'menu_class' => 'navigation  navigation--sidebar  u-flush--bottom',

				);

			}

			// Output data
			$data = array(

				'sidebar' => get_field( 'mttr_options_sidebar_content', 'options', false, false ),
				'menu' => $menu,
				'left_content' => get_the_content(),
				
			);

			mttr_get_template( 'template-parts/content/_c.content-standard', $data );

			// After content hook
			do_action( 'mttr_after_content' );

		echo '</main>';

	}

}

get_footer(); 