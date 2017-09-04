<?php
/**
 * The FULL WIDTH template
 *
 * This is the template that displays the home page
 *
 * @package mttr
 */

get_header(); 

if ( have_posts() ) {			

	/*
	*	Include Content Sidebar
	*/
	echo '<main>';

		/*
		*	Setup Hero Image Data
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

			'content' => get_field( 'mttr_hero_content' ),
			'image' => $hero_image_url,
			'image_mobile' => $hero_image_mobile_url,
			'identifier' => 'main',
			'indicator' => '.search-bar',
			'modifiers' => 'hero-standard--wide  hero-standard--center  hero-standard--home  hero-standard--overlay  overlay--heavy',

		);

		mttr_get_template( 'template-parts/hero/_c.hero-standard', $data );


		// Pull through search bar
		$data = array(

			'modifiers' => 'search-bar--offset-top',

		);
		mttr_get_template( 'template-parts/header/_c.search-bar', $data );



		/*
		*	Content Data
		*/
		$data = array(

			'left_content' => get_the_content(),
			'obj' => get_the_ID( get_option( 'page_for_posts' ) ),

		);

		mttr_get_template( 'template-parts/content/_c.content-flexible', $data );

	echo '</main>';

}

get_footer(); 