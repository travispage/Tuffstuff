<?php


/*
*	Image Content
*/

// Get vars
$content = mttr_get_template_var( 'content' );
$image = mttr_get_template_var( 'image' );


// Begin opening band class
echo '<section class="band  band--large  band--content">';

	echo '<div class="wrap">';

	echo '<ul class="layout image-content ">';

	if ( $image ) {

		$image_url = wp_get_attachment_image_src( $image, 'large', false );

		echo '<li style="background-image: url( '. esc_url( $image_url[0] ) .'" class="layout__item image-content__image u-ratio--image-content">';

			//echo '<li class="listing__item  layout__item">';

				echo '<ul class="layout  layout--middle  layout--large  listing">';

					echo '<li class="layout__item  listing__item">';

						//echo '<img alt="" src="' . esc_url( $image_url[ 0 ] ) . '">';

					echo '</li>';



					echo '</ul><!-- /.layout -->';

					echo '</li><!-- /.layout__item -->';


			echo '</li><!-- /.listing__item -->';

	}


		echo '<li class="layout__item image-content__content ">';

		if ( $content ) {

			echo '<div class="u-line-length">';

				if ( $content ) {

					echo '<div class="u-soft-half--bottom">';

						echo apply_filters( 'the_content', $content );

					echo '</div>';

				}

			echo '</div>';

		}

		echo '</li>';




	echo '</ul><!-- /.wrap -->';

	echo '</div><!-- /.wrap -->';

echo '</section><!-- /.band -->';
