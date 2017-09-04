<?php

$features = mttr_get_template_var( 'features' );
$heading = mttr_get_template_var( 'heading' );
$content = mttr_get_template_var( 'content' );
$modifiers = mttr_get_template_var( 'modifiers' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}

echo '<div class="band  band--large' . esc_html( $modifiers ) . '">';

	echo '<div class="wrap">';

		if ( $heading || $content ) {

			echo '<div class="u-line-length">';

				if ( $heading ) {

					echo '<h2 class="display-heading">';

						echo esc_html( $heading );

					echo '</h2>';

				}

				if ( $content ) {

					echo apply_filters( 'the_content', $content );

				}

			echo '</div>';

		}

		echo '<ul class="layout  listing' . esc_html( $modifiers ) . '">';

			foreach ( $features as $feature ) {

				$data = array(

					'content' => $feature[ 'content' ],
					'image' => $feature[ 'image' ],
					'subheading' => $feature[ 'heading' ],
					'modifiers' => 'feature-b--static',

				);

				echo '<li class="listing__item  layout__item  g-one-half@palm-h  g-one-quarter@desk">';

					mttr_get_template( 'template-parts/feature/_c.feature-b', $data );

				echo '</li>';

			}

			wp_reset_postdata();

		echo '</ul><!-- /.listing -->';

	echo '</div><!-- /.wrap -->';

echo '</div><!-- /.band -->';