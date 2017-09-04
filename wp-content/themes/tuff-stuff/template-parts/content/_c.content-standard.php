<?php

/*
*	Standard content
*/

// Get vars
$left_content = mttr_get_template_var( 'left_content' );
$right_content = mttr_get_template_var( 'right_content' );
$listing = mttr_get_template_var( 'listing' ); // Listing only outputs in a single column
$columns = mttr_get_template_var( 'columns' );
$image = mttr_get_template_var( 'image' );
$modifiers = mttr_get_template_var( 'modifiers' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}

if ( $left_content ||  $right_content || $listing ) {


// Begin opening band class
echo '<section class="band  band--large' . esc_html( $modifiers );

		if ( $image ) {

			echo '  band--hero  overlay';

		} else {

			echo '  band--content';

		}

	echo '"';

		if ( $image ) {

			echo ' style="background-image: url( \'' . esc_url( $image ) .'\');"';

		}

	echo '>'; // End opening band class

	
	// Begin opening band__body class
	echo '<div class="band__body';

		if ( $image ) {

			echo '  overlay__body';

		}

	echo '">'; // End opening band__body class

		echo '<div class="wrap">';

			if ( $columns != 'two' ) {

				echo '<div class="u-line-length  u-negate-btm-margin">';

			}

				if ( $left_content || $right_content ) {

					echo '<div class="entry-content  u-negate-btm-margin">';

				}

					if ( $columns == 'two' ) {

						echo '<ul class="layout  layout--large  layout--top  listing  listing--large">';

							echo '<li class="layout__item  listing__item  g-one-half@lap">';

								echo apply_filters( 'the_content', $left_content );

							echo '</li>';

							echo '<li class="layout__item  listing__item  g-one-half@lap">';

								echo apply_filters( 'the_content', $right_content );

							echo '</li>';

						echo '</ul><!-- /.layout -->';

					} else {

						echo apply_filters( 'the_content', $left_content );

					}	

				if ( $left_content || $right_content ) {

					echo '</div><!-- /.entry-content -->';

				}

			if ( $columns != 'two' ) {

				if ( $left_content || $right_content ) {

					echo '</div><!-- /.u-line-length -->';

				}

				if ( $listing ) {

					mttr_get_template( 'template-parts/content/_c.content-features', $listing );
					mttr_paged_navigation();

				}

			}

		echo '</div><!-- /.wrap -->';

	echo '</div><!-- /.band__body -->';

echo '</section><!-- /.band -->';

} // End check for content