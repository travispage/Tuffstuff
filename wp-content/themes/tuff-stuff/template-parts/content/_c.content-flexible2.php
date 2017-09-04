<?php

/*
*	The standard flexible content areas
*/

$obj = mttr_get_template_var( 'obj' );

if ( have_rows( 'mttr_content_flexible', $obj ) ) {

	$identifier = 0;

	while ( have_rows( 'mttr_content_flexible', $obj ) ) {

		the_row();
		$identifier++;


		/*
		*	Standard content
		*/
		if ( get_row_layout() == 'standard_content' ) {

			$image = get_sub_field( 'background' );

			if ( $image ) {

				$image = get_sub_field( 'background_image' );
				$image = wp_get_attachment_image_src( $image, 'mttr_hero' );

				if ( $image ) {

					$image = $image[0];

				}

			}

			$data = array(

				'left_content' => get_sub_field( 'content', false, false ),
				'right_content' => get_sub_field( 'content_right_column', false, false ),
				'image' => $image,
				'columns' => get_sub_field( 'columns' ),

			);

			mttr_get_template( 'template-parts/content/_c.content-standard', $data );

		}




		/*
		*	Content Sidebar
		*/
		if ( get_row_layout() == 'content_sidebar' ) {

			$data = array(

				'sidebar' => get_sub_field( 'sidebar', false, false ),
				'content' => get_sub_field( 'content', false, false ),

			);

			mttr_get_template( 'template-parts/content/_c.content-sidebar', $data );


		}


		/*
		*	Image Content
		*/
		if ( get_row_layout() == 'image_content' ) {

			$data = array(

				'heading' => get_sub_field( 'image_content_heading'),
				'content' => get_sub_field( 'image_content_text_area' ),
				'image' => get_sub_field( 'image_content_image' ),

			);

			mttr_get_template( 'template-parts/content/_c.content-image', $data );


		}



		/*
		*	Media Listing
		*/
		if ( get_row_layout() == 'media_listing' ) {

			$data = array(

				'heading' => get_sub_field( 'section_title' ),
				'content' => get_sub_field( 'section_content', false, false ),
				'items' => get_sub_field( 'items' ),

			);

			mttr_get_template( 'template-parts/content/_c.content-media', $data );

		}



		/*
		*	Accordion Listing
		*/
		if ( get_row_layout() == 'accordion' ) {

			$data = array(

				'heading' => get_sub_field( 'section_title' ),
				'content' => get_sub_field( 'section_content', false, false ),
				'items' => get_sub_field( 'items' ),
				'image' => get_sub_field( 'image' ),
				'id' => 'accordion-' . $identifier,

			);

			mttr_get_template( 'template-parts/content/_c.content-accordion', $data );

		}



		/*
		*	Static Features
		*/
		if ( get_row_layout() == 'static_features' ) {

			$items = get_sub_field( 'items' );

			if ( $items ) {

				$data = array(

					'features' => $items,
					'modifiers' => get_sub_field( 'modifiers' ) . '  band--content',

				);

				mttr_get_template( 'template-parts/content/_c.content-static-features', $data );

			}

		}





		/*
		*	Features
		*/
		if ( get_row_layout() == 'features' ) {

			// Check to see if we're showing blog posts instead of specific features
			if ( get_sub_field( 'display_posts' ) ) {

				$posts_per_page = get_sub_field( 'display_posts_number' );

				if ( $posts_per_page ) {

					$posts_per_page = $posts_per_page;

				} else {

					$posts_per_page = get_option( 'posts_per_page' );

				}

				$features = array();

				$args = array(

					'post_type' => 'post',
					'posts_per_page' => intval( $posts_per_page ),

				);

				// The Query
				$the_query = new WP_Query( $args );

				// The Loop
				if ( $the_query->have_posts() ) {

					// Setup new features, using latest posts
					while ( $the_query->have_posts() ) {

						$the_query->the_post();

						$features[] = get_the_ID();

					}

				}

				wp_reset_query();

			// We're displaying specific features
			} elseif ( get_sub_field( 'feature_style' ) == 'b' ) {

				$features = get_sub_field( 'product_category_features' );

			} else {

				$features = get_sub_field( 'features' );

			}

			$data = array(

				'cta_link' => get_sub_field( 'cta_link' ),
				'cta_text' => get_sub_field( 'cta_text' ),
				'heading' => get_sub_field( 'section_title' ),
				'content' => get_sub_field( 'section_content', false, false ),
				'style' => get_sub_field( 'feature_style' ),
				'wrap' => get_sub_field( 'wrap' ),
				'features' => $features,
				'modifiers' => get_sub_field( 'modifiers' ),

			);

			mttr_get_template( 'template-parts/content/_c.content-features', $data );

		}




		/*
		*	Map
		*/
		if ( get_row_layout() == 'map' ) {

			// Enqueue google maps if not enqueued already
			if ( !wp_script_is( 'google-maps', 'enqueued' ) ) {

				wp_register_script( 'google-maps', '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false', array( 'jquery' ), NULL, true );
				wp_enqueue_script( 'google-maps' );

			}


			$map_location = get_sub_field( 'locations' );

			if ( !$map_location ) {

				$map_location = get_field( 'mttr_options_contact_map_location', 'options' );

			}

			echo '<div class="js-google-map  map">';

				echo '<div class="map__body">';

					if ( is_array( $map_location ) ) {

						foreach( $map_location as $location ) {

							echo '<div class="marker" data-marker-image="' . get_stylesheet_directory_uri() . '/assets/img/location-pin.png" data-lat="' . $location['location']['lat'] . '" data-lng="' . $location['location']['lng'] . '">';

								echo '<div class="marker__body">';

									echo '<h4 class="marker__title">' . esc_html( $location['location_name'] ) . '</h4>';

									echo '<div class="marker__address">';

										echo apply_filters( 'the_content', $location['postal_address'] );

									echo '</div>';

									echo '<ul class="u-flush--bottom  list list--bare  marker__details">';

									if ( $location['phone_number'] ) {

										echo '<li><strong>Phone:</strong> <a href="tel:' . mttr_tel_filter_phone_number( do_shortcode( esc_html( $location['phone_number'] ) ) ) . '">' . do_shortcode( esc_html( $location['phone_number'] ) ) . '</a></li>';

									}

									if ( $location['fax_number'] ) {

										echo '<li><strong>Fax:</strong> <a href="tel:' . mttr_tel_filter_phone_number( do_shortcode( esc_html( $location['fax_number'] ) ) ) . '">' . do_shortcode( esc_html( $location['fax_number'] ) ) . '</a></li>';

									}

									if ( $location['email_address'] ) {

										echo '<li><strong>Email:</strong> <a href="mailto:' . antispambot( do_shortcode( esc_html( $location['email_address'] ) ) ) . '">' . antispambot( do_shortcode( esc_html( $location['email_address'] ) ) ) . '</a></li>';

									}

									echo '</ul>';

								echo '</div>';

							echo '</div><!-- /.marker -->';

						}

					} else {

						echo '<div class="marker" data-marker-image="' . get_stylesheet_directory_uri() . '/assets/img/location-pin.png" data-lat="' . $map_location['lat'] . '" data-lng="' . $map_location['lng'] . '">';

							echo '<div class="marker__body">';

								echo '<h4 class="flush--top">' . get_bloginfo( 'name' ) . '</h4>';
								echo do_shortcode( '[mttr_address]' );

								echo '<ul class="flush--bottom  list  list--bare">';
									echo '<li><strong>Phone:</strong> <a href="tel:' . do_shortcode( '[mttr_phone_number tel="true"]' ) . '">' . do_shortcode( '[mttr_phone_number]' ) . '</a></li>';
									echo '<li><strong>Email:</strong> <a href="mailto:' . do_shortcode( '[mttr_email_address]' ) . '">' . do_shortcode( '[mttr_email_address]' ) . '</a></li>';
								echo '</ul>';

							echo '</div>';

						echo '</div><!-- /.marker -->';

					}

				echo '</div><!-- /.map__body -->';

			echo '</div><!-- /.map -->';

		}  // map


	}


/*
*	Use the content-sidebar layout if no flexible content is found
*/
} else if ( !empty( get_the_content() ) ) {


	$data = array(

		'sidebar' => get_field( 'mttr_options_sidebar_content', 'options', false, false ),
		'left_content' => get_the_content(),

	);

	mttr_get_template( 'template-parts/content/_c.content-standard', $data );


/*
*	In the event that NO content is found, pull the 'no content' block
*/
} else {

	mttr_get_template( 'template-parts/content/_c.content-none' );

}
