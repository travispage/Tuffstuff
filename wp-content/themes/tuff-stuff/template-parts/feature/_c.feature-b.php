<?php

/* 
 *	Grid feature B
 */

	// Get vars
	$author = mttr_get_template_var( 'author' );
	$categories = mttr_get_template_var( 'categories' );
	$content = mttr_get_template_var( 'content' );
	$date = mttr_get_template_var( 'date' );
	$heading = mttr_get_template_var( 'heading' );
	$subheading = mttr_get_template_var( 'subheading' );
	$image = mttr_get_template_var( 'image' );
	$tags = mttr_get_template_var( 'tags' );
	$meta = mttr_get_template_var( 'meta' );
	$link = mttr_get_template_var( 'cta_link' );

	$modifiers = mttr_get_template_var( 'modifiers' );


	// Ensure an image is always used
	if ( !$image ) {

		$image = get_field( 'mttr_options_hero_default_image', 'options' );
		$image_url = wp_get_attachment_image_src( $image, 'mttr_square' );
		$image_url = $image_url[0];

	} else {

		$image_url = $image;

	}
	

	// Add spaces before modifiers
	if ( $modifiers ) {

		$modifiers = '  ' . $modifiers;

	}


	// Output template
	if ( $link ) {

		echo '<a href="' . esc_url( $link ) . '" class="feature-b' . esc_html( $modifiers ) . '  nudge  nudge--right  u-link--no-decoration">';

	} else {

		echo '<div class="feature-b' . esc_html( $modifiers ) . '">';

	}

		echo '<div class="feature-b__body">';

			echo '<ul class="layout  layout--small  layout--middle">';

				echo '<li class="feature-b__primary  layout__item">';

					if ( $image ) {

						echo '<div class="feature-b__media">';

							echo '<div class="ratio  ratio--square  band  u-hard--top" style="background-image: url( \'' . esc_url( $image_url ) . '\' );"></div>';

						echo '</div>';
					}

				echo '</li>';

				echo '<li class="feature-b__secondary  layout__item">';

					echo '<div class="feature-b__content">';

					if ( $heading ) {

						echo '<h3 class="display-heading  feature-b__heading  u-text--uppercase">' . esc_html( $heading ) . '</h3>';

					}

					if ( $subheading ) {

						echo '<h6 class="subheading  feature-b__subheading  u-flush--bottom">';
						
							echo esc_html( $subheading );

							if ( $link ) {

								echo ' <i class="chevron  chevron--right  nudge__item"></i>';

							}

						echo '</h3>';

					}

					if ( $content ) {

						echo '<div class="u-negate-btm-margin">';

							echo apply_filters ( 'the_content', $content );

						echo '</div>';

					}

				echo '</div><!-- /.feature-b__content -->';

			echo '</li>';

		echo '</ul>';

	echo '</div><!-- /.feature-b__body -->';

if ( $link ) {

	echo '</a><!-- /.feature-b -->';

} else {

	echo '</div><!-- /.feature-b -->';

}