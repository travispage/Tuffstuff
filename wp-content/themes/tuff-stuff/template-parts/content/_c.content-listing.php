<?php

/*
*	Create a listing
*/

// Get vars
$features = mttr_get_template_var( 'features' );
$feature_modifiers = mttr_get_template_var( 'feature_modifiers' );
$item_modifiers = mttr_get_template_var( 'item_modifiers' );
$modifiers = mttr_get_template_var( 'modifiers' );
$style = mttr_get_template_var( 'style' );

// Add spaces before modifiers
if ( $item_modifiers ) {

	$item_modifiers = '  ' . $item_modifiers;

}

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}

echo '<ul class="layout  listing' . esc_html( $modifiers ) . '">';

	foreach ( $features as $feature ) {

		$post = $feature;
		setup_postdata( $post );

		$hero_image_url = false;

		if ( $style == 'b' ) {

			$term = get_term( $feature );
			$category_thumbnail = get_woocommerce_term_meta($term->term_id, 'thumbnail_id', true);
			$hero_image_url = wp_get_attachment_url( $category_thumbnail );

			$data = array(

				'author' => false,
				'categories' => get_the_category_list( ', ' ),
				'content' => get_the_excerpt(),
				'cta_link' => get_term_link( $term->term_id, 'product_cat' ),
				'date' => get_the_date(),
				'heading' => get_field( 'mttr_woocommerce_category_subheading', 'product_cat_' . intval( $term->term_id ) ),
				'subheading' => $term->name,
				'icon' => get_field( 'mttr_feature_cta_icon' ),
				'image' => $hero_image_url,
				//'meta' => $display_meta,
				'tags' => get_the_tags(),
				'modifiers' => $feature_modifiers

			);

		} else {

			if ( has_post_thumbnail( get_the_ID() ) ) {

				$hero_image = get_post_thumbnail_id( get_the_ID() );
				$hero_image_url = wp_get_attachment_image_src( $hero_image, 'mttr_square' );
				$hero_image_url = $hero_image_url[0];

			}

			$display_meta = false;

			if ( get_post_type() == 'post' ) {

				$display_meta = true;

			}

			if ( get_post_type() == 'post' ) {

				if ( get_post_format() == 'video' ) {

					$icon = 'video-camera.svg';

				} elseif ( get_post_format() == 'audio' ) {

					$icon = 'volume-medium.svg';

				} else {

					$icon = false;

				}

			} else {

				$icon = get_field( 'mttr_feature_cta_icon' );

			}

			$data = array(

				'author' => false,
				'categories' => get_the_category_list( ', ' ),
				'content' => get_the_excerpt(),
				'cta_link' => get_the_permalink(),
				'cta_text' => get_field( 'mttr_feature_cta_text' ),
				'date' => get_the_date(),
				'heading' => get_the_title(),
				'format' => get_post_format( get_the_ID() ),
				'icon' => $icon,
				'image' => $hero_image_url,
				'meta' => $display_meta,
				'tags' => get_the_tags(),
				'modifiers' => $feature_modifiers,
				'company_name' => get_field('mttr_testimonials_company_name')

			);

		}

		if ( $style ) {

			echo '<li class="listing__item  layout__item' . esc_html( $item_modifiers ) . '">';

				mttr_get_template( 'template-parts/feature/_c.feature-' . esc_attr( $style ), $data );

			echo '</li>';

		}

	}

	wp_reset_postdata();

echo '</ul><!-- /.listing -->';
