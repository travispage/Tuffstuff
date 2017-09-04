<?php


/*
*	Accordion
*/

// Get vars
$id = mttr_get_template_var( 'id' );
$heading = mttr_get_template_var( 'heading' );
$content = mttr_get_template_var( 'content' );
$image = mttr_get_template_var( 'image' );
$items = mttr_get_template_var( 'items' );
$modifiers = mttr_get_template_var( 'modifiers' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}


// Begin opening band class
echo '<section>';

	echo '<div class="wrap">';

		echo '<ul class="layout  layout--large  layout--top  listing  listing--large">';

			if ( $items || $heading || $content ) {

				echo '<li class="layout__item  listing__item  g-one-half@lap">';

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

						echo '<div class="u-keyline  u-keyline--dark">';

							echo '<ul class="listing  listing--flush  layout">';

							$itemcounter = 0;

							foreach ( $items as $item ) {

								$itemcounter++;
								
								echo '<li class="listing__item  layout__item">';

									echo '<div class="accordion-a' . esc_html( $modifiers ) . '  ' . esc_html( $id ) . '--' . $itemcounter . '">';

										if ( $item[ 'title' ] ) {

											echo '<div data-toggle-class="is-open" data-toggle-target=".' . esc_html( $id ) . '--' . $itemcounter . '" class="accordion-a__heading  js-toggle">';

												echo '<h6 class="accordion-a__title  u-flush">' . esc_html( $item[ 'title' ] ) .'</h6>';

											echo '</div>';

										}

										if ( $item[ 'content' ] ) {

											echo '<div class="accordion-a__content">';

												echo apply_filters( 'the_content', $item[ 'content' ] );

											echo '</div>';

										}

									echo '</div>';

								echo '</li><!-- /.listing__item -->';

							}

							echo '</ul><!-- /.listing -->';

						echo '</div>';

					}

				echo '</li>';

			}

			if ( $image ) {

				echo '<li class="layout__item  listing__item  g-one-half@lap">';

					echo wp_get_attachment_image( $image, 'large' );

				echo '</li>';

			}

		echo '</ul>';

	echo '</div><!-- /.wrap -->';

echo '</section><!-- /.band -->';