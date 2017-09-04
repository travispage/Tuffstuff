<?php 

/*
*	Listing for halves
*/

// Get vars
$features = mttr_get_template_var( 'features' );
$feature_modifiers = mttr_get_template_var( 'feature_modifiers' );
$modifiers = mttr_get_template_var( 'modifiers' );
$style = mttr_get_template_var( 'style' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}

echo '<ul class="layout  listing' . esc_html( $modifiers ) . '">';

	foreach ( $features as $feature ) {

		$post = $feature;
		setup_postdata( $post );

		$hero_image_url = false;

		if ( has_post_thumbnail( get_the_ID() ) ) {

			$hero_image = get_post_thumbnail_id( get_the_ID() );
			$hero_image_url = wp_get_attachment_image_src( $hero_image, 'mttr_square' );
			$hero_image_url = $hero_image_url[0];

		}

		$display_meta = false;

		if ( get_post_type() == 'post' ) {

			$display_meta = true;

		}

		$data = array(

			'author' => false,
			'categories' => get_the_category_list( ', ' ),
			'content' => get_the_excerpt(),
			'cta_link' => get_the_permalink(),
			'cta_text' => get_field( 'mttr_feature_cta_text' ),
			'date' => get_the_date(),
			'heading' => get_the_title(),
			'icon' => get_field( 'mttr_feature_cta_icon' ),
			'image' => $hero_image_url,
			'meta' => $display_meta,
			'tags' => get_the_tags(),
			'modifiers' => $feature_modifiers

		);

		if ( $style ) {

			echo '<li class="listing__item  layout__item  g-one-half@desk">';

				mttr_get_template( 'template-parts/feature/_c.feature-' . esc_attr( $style ), $data );

			echo '</li>';

		}

	}

	wp_reset_postdata();

echo '</ul><!-- /.listing -->';