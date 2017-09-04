<?php
/*
 *	Testimonials
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
	$company = mttr_get_template_var( 'company_name' );


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

		echo '<div class="feature-testimonial' . esc_html( $modifiers ) . '">';

			echo '<div class="feature-testimonial__body">';

				echo '<ul class="layout  layout--small  layout--middle">';

					echo '<li class="feature-testimonial__primary  layout__item">';

						echo '<blockquote class="feature-testimonial__content blockquote-a ">';

							echo apply_filters('the_content', $content);

						echo '</blockquote><!-- /.feature-testimonial__content -->';

						echo '<div class="feature-testimonial__secondary-content">';

								echo esc_html( $heading ) ;

						echo '</div>';
						echo '<div class="feature-testimonial__tertiary-content">';

								echo esc_html( $company ) ;

						echo '</div>';

					echo '</li>';

				echo '</ul>';

			echo '</div><!-- /.feature-testimonial__body -->';

		echo '</div><!-- /.feature-testimonial -->';

}
