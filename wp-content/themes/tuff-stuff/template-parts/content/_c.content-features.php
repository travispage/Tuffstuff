<?php

/*
*	Features
*/

// Get vars
$cta_link = mttr_get_template_var( 'cta_link' );
$cta_text = mttr_get_template_var( 'cta_text' );
$heading = mttr_get_template_var( 'heading' );
$content = mttr_get_template_var( 'content' );
$style = mttr_get_template_var( 'style' );
$wrap = mttr_get_template_var( 'wrap' );
$grid = mttr_get_template_var( 'grid' ); // Get the style of listing
$features = mttr_get_template_var( 'features' );
$modifiers = mttr_get_template_var( 'modifiers' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}

// Begin opening band class
echo '<section class="band  band--large  band--content' . esc_html( $modifiers ) . '">';

	if ( $heading || $content ) {

		echo '<div class="wrap">';

			echo '<div class="u-line-length">';

				if ( $cta_link && $cta_text ) {

					echo '<a class="btn  btn--secondary  btn--tiny  u-push-half--bottom" href="' . esc_url( $cta_link ) . '">' . esc_html( $cta_text ) . '</a>';

				}

				if ( $heading ) {

					echo '<h2 class="display-heading  display-heading--beta">';

						echo esc_html( $heading );

					echo '</h2>';

				}

				if ( $content ) {

					echo '<div class="u-soft-half--bottom">';

						echo apply_filters( 'the_content', $content );

					echo '</div>';

				}

			echo '</div>';

		echo '</div>';

	}

	if ( $features ) {

		if ( $wrap ) {

			echo '<div class="wrap">';

		}

		$listing_modifiers = 'listing--' . esc_attr( $style );
		$feature_modifiers = '';

		if ( $style == 'a' ) {

			$feature_modifiers = 'feature-a--bottom  feature-a--alt  feature-a--rectangle  feature-a--overlay';
			$listing_modifiers .= '';
			$listing_item_modifiers = 'g-one-half@palm-h  g-one-third@lap';
			$style = 'a';

		} elseif ( $style == 'b' ) {

			$listing_template = '_c.listing-grid-fifths';
			$listing_modifiers .= '  layout--tiny  listing--tiny';
			$listing_item_modifiers = 'g-one-half@palm-h  g-one-fifth@desk';
			$feature_modifiers = 'feature-b--linked  js-match-height';
			$style = 'b';

		}elseif ( $style == 'testimonial' ) {

			$listing_template = '_c.listing-grid-fifths';
			$listing_modifiers .= '';
			$listing_item_modifiers = 'g-one-half@lap';
			$feature_modifiers = '';
			$style = 'testimonial';

		} else {

			$listing_template = '_c.listing-wide';
			$listing_item_modifiers = '';
			$listing_modifiers .= '  listing--lines-between  listing--large';

		}

		$data = array(

			'features' => $features,
			'feature_modifiers' => $feature_modifiers,
			'item_modifiers' => $listing_item_modifiers,
			'modifiers' => $listing_modifiers,
			'style' => $style,

		);

		mttr_get_template( 'template-parts/content/_c.content-listing', $data );

		wp_reset_postdata();

		if ( $wrap ) {

			echo '</div><!-- /.wrap -->';

		}

	}

echo '</section><!-- /.band -->';
