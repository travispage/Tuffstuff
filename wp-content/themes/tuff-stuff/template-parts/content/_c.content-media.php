<?php


/*
*	Features
*/

// Get vars
$heading = mttr_get_template_var( 'heading' );
$content = mttr_get_template_var( 'content' );
$items = mttr_get_template_var( 'items' );
$modifiers = mttr_get_template_var( 'modifiers' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}

// Begin opening band class
echo '<section class="band  band--large  band--content' . esc_html( $modifiers ) . '">';

	echo '<div class="wrap">';

		if ( $heading || $content ) {		

			echo '<div class="u-line-length">';

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

		}

		if ( $items ) {

			echo '<ul class="listing  listing--huge  layout  layout--alternate">';

			foreach ( $items as $item ) {

				$image_url = wp_get_attachment_image_src( $item[ 'image' ], 'large' );

				echo '<li class="listing__item  layout__item">';

					echo '<ul class="layout  layout--middle  layout--large  listing">';

						echo '<li class="layout__item  listing__item  g-one-third@lap">';

							echo '<img alt="" src="' . esc_url( $image_url[ 0 ] ) . '">';

						echo '</li>';

						echo '<li class="layout__item  listing__item  g-two-thirds@lap">';

							echo '<div class="u-negate-btm-margin">';

								echo apply_filters( 'the_content', $item[ 'content' ] );

							echo '</div><!-- /.u-negate-btm-margin -->';

						echo '</li><!-- /.layout__item -->';

					echo '</ul><!-- /.layout -->';

				echo '</li><!-- /.listing__item -->';

			}

			echo '</ul><!-- /.listing -->';

		}

	echo '</div><!-- /.wrap -->';

echo '</section><!-- /.band -->';