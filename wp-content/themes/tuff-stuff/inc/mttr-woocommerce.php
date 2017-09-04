<?php

/*
*	Woocommerce setup etc
*/

// Declare Woocommerce Support
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
	add_theme_support( 'woocommerce' );
}

// Remove WooCommerce image sizes (we already have our own)
add_filter( 'intermediate_image_sizes_advanced', 'mttr_remove_woocommerce_image_sizes' );
function mttr_remove_woocommerce_image_sizes() {
	unset( $sizes['shop_thumbnail'] );
	unset( $sizes['shop_catalog'] );
	unset( $sizes['shop_single'] );
}


// Remove Woocommerce select2
add_action( 'wp_enqueue_scripts', 'mttr_dequeue_woocommerce_select2', 100 );

function mttr_dequeue_woocommerce_select2() {

	if ( class_exists( 'woocommerce' ) ) {

		wp_dequeue_style( 'select2' );
		wp_deregister_style( 'select2' );

		wp_dequeue_script( 'select2' );
		wp_deregister_script( 'select2' );

	} 

} 




/*
*	Returns a primary product ID, OR the standard ID if none is set
*/
function mttr_primary_product_id( $id = null ) {

	// Set the ID for information
	if ( empty( $id ) ) {

		$id = get_the_ID();

	}

	$parent_product = mttr_has_primary_product( $id );

	// If a parent product has been defined, set the ID
	if ( $parent_product ) {

		$id = $parent_product;

	}

	return $id;

}



/*
*	Returns teh primary product ID if set
*/
function mttr_has_primary_product( $id = null ) {

	// Set the ID for information
	if ( empty( $id ) ) {

		$id = get_the_ID();

	}

	$parent_product = get_field( 'mttr_primary_product', $id );

	// If a parent product has been defined, set the ID
	if ( $parent_product ) {

		return $parent_product;

	}

	return false;

}




/*
*	Get a photo associated with the product
*	This falls back to a placeholder if none supplied
*	If a product has an image set at the individual level, that will be served
*/
function mttr_get_primary_product_image( $id ) {

	// See if the product has an image
	if ( has_post_thumbnail( $id ) ) {

		$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'mttr_square' );

		return '<img alt="' . get_the_title( get_the_ID() ) . '" src="' . $image[0] . '">';

	// Include the post thumbnail from the current ID or PARENT product
	} elseif ( has_post_thumbnail( mttr_has_primary_product( $id ) ) ) {

		$image = wp_get_attachment_image_src( get_post_thumbnail_id( mttr_has_primary_product( $id ) ), 'mttr_square' );

		return '<img alt="' . get_the_title( $id ) . '" src="' . $image[0] . '">';

	} else {

		return '<img alt="' . get_the_title( $id ) . '" src="' . mttr_woocommerce_placeholder_img_src() . '">';

	}

}




/*
*	Woocommerce Category Pages
*/

// Add the data for Woocommerce categories
add_action( 'woocommerce_archive_description', 'mttr_woocommerce_category_content', 10 );
function mttr_woocommerce_category_content() {

	global $wp_query;
	$cat = $wp_query->get_queried_object();

	if ( is_shop() ) {

		$obj = get_the_ID( get_option( 'woocommerce_shop_page_id' ) );

	} elseif ( !empty( $cat->taxonomy ) ) {

		$obj = $cat;

	}


	// Don't output the top section for brands
	if ( ( !empty( $cat->taxonomy ) && $cat->taxonomy != 'product_brand' ) && !is_shop() ) {

		if ( is_shop() ) {

			$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );

		} elseif ( !empty( $cat->name ) ) {

			$title = $cat->name;

		} 
	

		/*
		*	Hero Data
		*/
		$data = array(

			'title' => $title,
			'subheading' => get_field( 'mttr_woocommerce_category_subheading', $obj ),
			'modifiers' => 'band  band--large  u-hard--bottom',

		);

		if ( have_rows( 'mttr_content_flexible', $obj ) ) {

			mttr_get_template( 'template-parts/hero/_c.hero-b', $data );

		}



		/*
		*	Content Data
		*/
		$data = array(

			'obj' => $obj,

		);

		// Check for flexible content ON the category
		if ( have_rows( 'mttr_content_flexible', $obj ) ) {

			mttr_get_template( 'template-parts/content/_c.content-flexible', $data );

		}

	}

}

add_filter( 'woocommerce_show_page_title', 'mttr_filter_woocommerce_category_title', 10 );
function mttr_filter_woocommerce_category_title() {

	return false;

}



/*
*	Product category archive listing
*/

// Add opening wrap
add_action( 'woocommerce_before_shop_loop', 'mttr_woocommerce_open_shop_loop_wrap', 10 );
function mttr_woocommerce_open_shop_loop_wrap() {

	echo '<div class="band  band--large  band--content">';

		echo '<div class="wrap">';

}



// Add heading
add_action( 'woocommerce_before_shop_loop', 'mttr_woocommerce_before_shop_loop', 11 );
function mttr_woocommerce_before_shop_loop() {

	global $wp_query;
	$cat = $wp_query->get_queried_object();

	if ( is_shop() ) {

		$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );

	} else {

		$title = $cat->name;

	}

	echo '<h1 class="display-heading  display-heading--beta  u-flush--bottom">' . esc_html( $title ) . '</h1>';

}



// Add breadcrumbs
add_action( 'woocommerce_before_shop_loop', 'woocommerce_breadcrumb', 12 );
add_action( 'woocommerce_before_shop_loop', 'mttr_sidebar_cta_button', 13 );



// Add FAB for Filters
add_action( 'woocommerce_archive_description', 'mttr_woocommerce_fab_scrollto_filter', 8 );
function mttr_woocommerce_fab_scrollto_filter() {

	if ( is_tax( 'product_cat' ) ) {

		echo '<div class="btn--left">';

			echo '<a data-scroll-target=".facet-filter" class="btn  btn--secondary  js-smooth-scroll"><i class="icon  icon--small  icon--before">' . mttr_get_icon( 'magnifying-glass.svg' ) . '</i>Parts Search</a>';
		
		echo '</div>';

	}

}


// Add sidebar filters
add_action( 'woocommerce_before_shop_loop', 'mttr_woocommerce_sidebar_filter', 15 );
function mttr_woocommerce_sidebar_filter() {

	echo '<ul class="layout  listing  u-push--top">';

		echo '<li class="layout__item  listing__item  g-one-third@lap  g-one-quarter@desk">';

			global $wp_query;
			$cat = $wp_query->get_queried_object();

			$output_brands = true;

			if( property_exists( $cat, 'taxonomy' ) ) {

				if ( $cat->taxonomy == 'product_brand' ) {

					$output_brands = false;

				}

			}

			// We will hide the filters from the sidebar unless explicitly set as false (ie not set is true)
			$output_filters = get_field( 'mttr_display_filters', "{$cat->taxonomy}_{$cat->term_id}" ) !== NULL ? get_field( 'mttr_display_filters', "{$cat->taxonomy}_{$cat->term_id}" ) : true;

			/*
			*	Output facet accordion list
			*/
			echo '<ul class="listing  listing--tiny">';

				if( $output_filters ) {

					echo '<li class="listing__item">';

						echo '<div class="facet-filter">';

							echo '<div class="facet-filter__content">';

								echo '<h4 class="display-heading  display-heading--small  u-flush--bottom">Find Your Part Here</h4>';

								echo '<button class="filter-reset  subheading  cta-link  nudge  nudge--right" onclick="FWP.reset()">Reset All Filters <i class="nudge__item  chevron  chevron--dark  chevron--right"></i></button>';

							echo '</div><!-- /.box__content -->';

						echo '</div><!-- /.box -->';

					echo '</li>';


					// Don't output the brand filter for brand pages (cos duh)
					if ( $output_brands == true ) { 

						echo '<li class="listing__item">';

							echo '<div class="facet-filter">';

								echo '<div class="facet-filter__content">';

									echo '<h6 class="facet-filter__title">Parts By Make</h6>';
									//echo facetwp_display( 'facet', 'brands' );

									echo '<select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
												<option value="">Select</option>
												<option value="http://testing.socialmedia-solutions.com/tuffstuff/rubber-tracks/kubota/">Kubota</option>
												<option value=" http://testing.socialmedia-solutions.com/tuffstuff/rubber-tracks/caterpillar/">Caterpillar</option>
											</select>';

								echo '</div>';

							echo '</div>';

						echo '</li>';



					}

					/*echo '<li class="listing__item">';

						echo '<div class="facet-filter">';

							echo '<div class="facet-filter__content">';

								echo '<h6 class="facet-filter__title">Parts By Model</h6>';
								echo facetwp_display( 'facet', 'model' );

							echo '</div><!-- /.facet-filter__content -->';

						echo '</div><!-- /.facet-filter -->';

					echo '</li>';

					echo '<li class="listing__item">';

						echo '<div class="facet-filter">';

							echo '<div class="facet-filter__content">';

								echo '<h6 class="facet-filter__title">Parts By Type</h6>';
								echo facetwp_display( 'facet', 'type' );

							echo '</div>';

						echo '</div>';

					echo '</li>'; */

				}

				echo '<li class="listing__item">';

					echo '<div class="facet-filter">';

						echo '<div class="facet-filter__content">';

							echo '<a href="' . get_the_permalink( 38 ) . '">Can’t find your part?</a>';

						echo '</div>';

					echo '</div>';

				echo '</li>';

			echo '</ul>';

		echo '</li>';

		echo '<li class="layout__item  listing__item  g-two-thirds@lap  g-three-quarters@desk  facetwp-template">';

}





// Add FacetWP pagination and ordering BEFORE
add_action( 'woocommerce_before_shop_loop', 'mttr_woocommerce_pagination', 20 );

// Add FacetWP pagination and ordering AFTER
add_action( 'woocommerce_after_shop_loop', 'mttr_woocommerce_pagination', 28 );

function mttr_woocommerce_pagination() {

	echo '<ul class="layout  layout--middle  listing">';

		echo '<li class="layout__item  listing__item  g-two-thirds@lap">';

			echo facetwp_display( 'pager' );

		echo '</li><!-- /.layout__item -->';

		echo '<li class="layout__item  listing__item  g-one-third@lap">';

			echo do_shortcode( '[facetwp sort="true"]' );

		echo '</li><!-- /.layout__item -->';

	echo '</ul><!-- /.layout -->';

}



function woocommerce_product_loop_start( $echo = true ) {

	echo '<div class="band">';

		echo '<ul class="listing  layout">';

}



	/*
	*	Filter the Woocommerce product thumbnail image
	*/
	function woocommerce_get_product_thumbnail( $size = 'shop_catalog' ) {

		// Get parent product ID
		$id = mttr_primary_product_id();

		if ( has_post_thumbnail( $id ) ) {

			return get_the_post_thumbnail( $id, $size );

		} elseif ( wc_placeholder_img_src() ) {

			return wc_placeholder_img( $size );

		}

	}


	add_filter( 'woocommerce_placeholder_img_src', 'mttr_woocommerce_placeholder_img_src', 100 );
	function mttr_woocommerce_placeholder_img_src() {

		return get_field( 'mttr_woocommerce_placeholder_image', 'options' );

	}



	// Change listing image ratio to use square image
	function woocommerce_template_loop_product_thumbnail() {

		echo woocommerce_get_product_thumbnail( 'mttr_square' );

	}


	// Product Loop Title
	function woocommerce_template_loop_product_title() {

		echo '<h3 class="js-match-height">' . get_the_title() . '</h3>';
		echo '<span class="cta-link  nudge  nudge--right">Read More<i class="nudge__item  arrow  arrow--right"></i></span>';

	}



	// Add 'read more' button after quote button
	function mttr_quick_quote() {

		echo '<a data-product-src="Enquiry regarding - ' . esc_html( get_the_title() ) . '" data-popup-target="#quote-popup" class="btn  btn--secondary  btn--fill  js-popup  js-quote-popup" href="#">Quick Quote</a>';

	}

	// Adds the quick quote button to listings
	add_action( 'woocommerce_after_shop_loop_item', 'mttr_quick_quote', 8 );
	// Adds the quick quote button to the end of the single product content. If the priority is made larger it will be full width
	add_action( 'woocommerce_single_product_summary', 'mttr_quick_quote', 50 );


	function mttr_quick_quote_popup() {

		echo '<div class="popup  popup--quick-quote" id="quote-popup">';

			echo '<div data-toggle-target="#quote-popup" data-toggle-class="popup--is-active" class="popup__blocker  js-toggle"></div>';

			echo '<div class="popup__body">';

				echo '<div class="popup__content">';

					echo '<button data-toggle-target="#quote-popup" data-toggle-class="popup--is-active" class="btn  btn--secondary  popup__close  js-toggle"><span class="u-screen-reader-text">Close Popup</span></button>';

					echo '<div class="wrap">';

						echo '<div class="popup__call">';

							echo '<a class="btn" href="tel:' . do_shortcode( '[mttr_phone_number tel=true]' ) . '">' . do_shortcode( '[mttr_icon icon=phone-rev align=middle size=small before=true]' ) . 'Call us on ' . do_shortcode( '[mttr_phone_number]' ) . '</a>';

						echo '</div>';

						gravity_form( 4, true, true, false, null, true );

					echo '</div><!-- /.wrap -->';

				echo '</div><!-- /.popup__content -->';

			echo '</div><!-- /.popup__body -->';

		echo '</div><!-- /.popup -->';

	}

	add_action( 'wp_footer', 'mttr_quick_quote_popup', 1 );



	// Remove price and ratings
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );


function woocommerce_product_loop_end( $echo = true ) {

		echo '</ul>';

	echo '</div><!-- /.band -->';

}


// Filter the product listing ITEMS
add_filter( 'post_class', 'mttr_filter_product_listing_items' );
function mttr_filter_product_listing_items( $classes ) {

	if( !is_admin() ) {

		global $post;
		global $wp_query;

		$cat = $wp_query->get_queried_object();

		// Add item to each product
		if ( is_product_category()  ||  ( property_exists( $cat, 'taxonomy' ) ? $cat->taxonomy == 'product_brand' : false )  ||  is_shop() ) {

			$classes[] = 'layout__item  listing__item  product-listing__item  g-one-half@palm-h  g-one-third@desk';

		}

	}
		
	return $classes;

}



// Filter the product listing ITEMS
add_filter( 'body_class', 'mttr_filter_body_class_fixed_btn' );
function mttr_filter_body_class_fixed_btn( $classes ) {

	global $post;
	global $wp_query;

	$cat = $wp_query->get_queried_object();

	// Add item to each product
	if ( is_product_category()  ||  ( property_exists( 'cat', 'taxonomy' ) ? $cat->taxonomy == 'product_brand' : false )  ||  is_product() ) {

		$classes[] = 'fixed-button--offset';

	}

	return $classes;

}





// Close sidebar filters
add_action( 'woocommerce_after_shop_loop', 'mttr_woocommerce_sidebar_filter_close', 29 );
function mttr_woocommerce_sidebar_filter_close() {

		echo '</li><!-- /.layout__item -->';

	echo '</ul><!-- /.layout -->';

}



// Close opening wrap
add_action( 'woocommerce_after_shop_loop', 'mttr_woocommerce_close_shop_loop_wrap', 30 );
function mttr_woocommerce_close_shop_loop_wrap() {

		echo '</div><!-- /.wrap -->';

	echo '</div><!-- /.band -->';

}




/*
*	Global Woocommerce
*/

// Remove WooCommerce Pagination and catalog ordering
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


// Remove all WooCommerce styling (YAY and all in one line!)
add_filter( 'woocommerce_enqueue_styles', '__return_false' );


// Get rid of that unsightly sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );


// Remove the breadcrumbs from the category page
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );


// Adjust Breadcrumb output
add_filter( 'woocommerce_breadcrumb_defaults', 'mttr_woocommerce_breadcrumbs' );
function mttr_woocommerce_breadcrumbs() {

	$breadcrumb_beginning = '';

	if ( !is_shop() ) {

		$breadcrumb_beginning = '<a href="' . get_the_permalink( get_option( 'woocommerce_shop_page_id' ) ) . '">' . get_the_title( get_option( 'woocommerce_shop_page_id' ) ) . '</a> <i class="breadcrumb__arrow  arrow  arrow--right  arrow--dark"></i> ';

	}

	return array(
		'delimiter'   => ' <i class="breadcrumb__arrow  arrow  arrow--right  arrow--dark"></i> ',
		'wrap_before' => '<nav class="breadcrumb" itemprop="breadcrumb"><strong class="breadcrumb__title">You are here:</strong> ' . $breadcrumb_beginning,
		'wrap_after'  => '</nav>',
		'before'      => '',
		'after'       => '',
		'home'        => _x( '', 'breadcrumb', 'woocommerce' ),
	);

}





/*
*	Product category PRODUCT items
*/

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
add_action( 'woocommerce_before_shop_loop_item', 'mttr_woocommerce_product_link_open', 10 );

function mttr_woocommerce_product_link_open() {

	echo '<a class="product-feature" href="' . get_the_permalink( ) . '">';

}




/*
*	Facet WP
*/

// Unhook facet filters CSS
add_filter( 'facetwp_load_css', 'mttr_unhook_facetwp_css' );
function mttr_unhook_facetwp_css() {

	return false;

}

// Filter facet pagination
add_filter( 'facetwp_pager_html', 'mttr_facetwp_pager_html', 10, 2 );
function mttr_facetwp_pager_html( $output, $params ) {

	if ( $params[ 'total_pages' ] == 1 ) {

		$output = 'Page 1 of 1';

	}

	return $output;

}











/*
*	Product Detail Page
*/

// Open message wrappers
add_action( 'woocommerce_before_single_product', 'mttr_open_notices_wrap', 9 );
function mttr_open_notices_wrap() {

	// // Check for WooCommerce notices
	if ( wc_notice_count() != 0 ) {

		echo '<div class="band  band--message">';

			echo '<div class="wrap">';

				// Output notices
				wc_print_notices();

			echo '</div><!-- /.wrap -->';

		echo '</div><!-- /.band -->';

	}

}

// Remove WooCommm notices because we've hooked them elsewhere
remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );




// Open breadcrubm wrapper
add_action( 'woocommerce_before_single_product', 'mttr_open_breadcrumb_wrap', 12 );
function mttr_open_breadcrumb_wrap() {

	echo '<div class="band  u-hard--bottom">';

		echo '<div class="wrap">';

}


// Add breadcrumbs
add_action( 'woocommerce_before_single_product', 'woocommerce_breadcrumb', 13 );


// close breadcrubm wrapper
add_action( 'woocommerce_before_single_product', 'mttr_close_breadcrumb_wrap', 14 );
function mttr_close_breadcrumb_wrap() {

		echo '</div><!-- /.wrap -->';

	echo '</div><!-- /.band -->';

}

// Add CTA button
add_action( 'woocommerce_before_single_product', 'mttr_sidebar_cta_button', 15 );



// Open wrapper around top part of product detail page (images + summary)
add_action( 'woocommerce_before_single_product', 'mttr_open_product_detail_wrap', 20 );
function mttr_open_product_detail_wrap() {

	echo '<div class="band  band--large  band--content">';

		echo '<div class="wrap">';

}




	// Wrap around product images
	add_action( 'woocommerce_before_single_product_summary', 'mttr_woocommerce_product_images_wrap_open', 1 );
	function mttr_woocommerce_product_images_wrap_open() {

		echo '<ul class="layout  layout--product-detail  listing  listing--large">';

			echo '<li class="layout__item  listing__item  g-one-half@lap">';

	}



		// Filter the product images
		add_filter( 'woocommerce_single_product_image_html', 'mttr_product_image_filter' );
		function mttr_product_image_filter( $output ) {

			$id = get_the_ID();
			echo mttr_get_primary_product_image( $id );

		}


		// Output an image disclaimer
		add_action( 'woocommerce_product_thumbnails', 'mttr_image_disclaimer', 70 );
		function mttr_image_disclaimer() {

			echo '<p class="u-text--tiny  u-push--top  image-disclaimer  u-flush--bottom">Pictures used are for illustrative purposes only and may vary in design, specification and/or tread pattern. Confirm at time of purchase if you are unsure.</p>';

		}


		// Remove the gallery thumbnails
		remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );


		// Remove the WooCommerce Gallery from the admin
		if ( is_admin() ) {

			add_action( 'add_meta_boxes' , 'mttr_remove_woocommerce_gallery', 40 );
			function mttr_remove_woocommerce_gallery() {

				remove_meta_box( 'woocommerce-product-images',  'product', 'side');
			
			}

		}



	// Wrap before product summary and AFTER the product images
	add_action( 'woocommerce_single_product_summary', 'mttr_woocommerce_product_summary_wrap_open', 1 );
	function mttr_woocommerce_product_summary_wrap_open() {

			echo '</li><li class="layout__item  listing__item  g-one-half@lap">';

	}


		// Add a subheading above the title
		add_action( 'woocommerce_single_product_summary', 'mttr_woocommerce_single_subheading', 4 );
		function mttr_woocommerce_single_subheading() {

			$terms = get_the_terms( get_the_ID(), 'product_cat' );

			if ( $terms ) {

				$term_subheading_name = get_field( 'mttr_woocommerce_category_subheading', $terms[ 0 ] );

				if ( $term_subheading_name ) {

					echo '<h6 class="subheading  u-soft-half--bottom">' . esc_html( $term_subheading_name ) . '</h6>';

				}

			}

			

		}


		// Change single title template to output nicer
		function woocommerce_template_single_title() {
	
			echo '<h1 class="display-heading  display-heading--beta">' . get_the_title(). '</h1>';			

		}


		// Output the brand meta
		add_action( 'woocommerce_single_product_summary', 'mttr_woocommerce_brand_meta', 6 );
		function mttr_woocommerce_brand_meta() {

			global $product;

			$brands = get_brands( get_the_ID() );
			$sku = $product->get_sku();

			echo '<div class="u-push--bottom">';

				if ( $brands ) {

					echo '<ul class="layout  layout--small  layout--middle  layout--auto">';

						if ( $brands ) {

							echo '<li class="layout__item">';

								echo '<h6 class="subheading  u-flush--bottom">Brands:</h6>';

							echo '</li>';

							echo '<li class="layout__item  u-text--tiny">';

								echo $brands;

							echo '</li>';

						}

					echo '</ul><!-- /.layout -->';

				}


				if ( $sku ) {

					echo '<ul class="layout  layout--small  layout--middle  layout--auto  u-push--bottom">';

						echo '<li class="layout__item">';

							echo '<h6 class="subheading  u-flush--bottom">Part #</h6>';

						echo '</li>';

						echo '<li class="layout__item  u-text--tiny">';

							echo esc_html( $sku );

						echo '</li>';

					echo '</ul><!-- /.layout -->';

				}

			echo '</div>';

		}



		// Remove old add to cart
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

		// Remove some of the WooCommerce things
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

		// Add add to cart, with wrapper
		add_action( 'woocommerce_single_product_summary', 'mttr_woocommerce_single_add_cart_wrap_open', 10 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 10 );
		add_action( 'woocommerce_single_product_summary', 'mttr_woocommerce_single_add_cart_wrap_close', 10 );

		function mttr_woocommerce_single_add_cart_wrap_open() {

			echo '<div class="u-soft--bottom">';

		}

		function mttr_woocommerce_single_add_cart_wrap_close() {

			echo '</div>';

		}


		// Add product description
		add_action( 'woocommerce_single_product_summary', 'mttr_woocommerce_product_summary', 20 );

		function mttr_woocommerce_product_summary() {

			// Get parent product ID
			$id = mttr_primary_product_id();

			if ( '' != get_post_field( 'post_content', get_the_ID() ) ) {

				the_content();

			} else {

				// Output the page content
				echo apply_filters( 'the_content', get_post_field( 'post_content', $id ) );

			}

		}


		// Add phone number after cart button
		add_action( 'woocommerce_after_add_to_cart_button', 'mttr_woocommerce_tel_button', 80 );
		function mttr_woocommerce_tel_button() {

			echo do_shortcode( '<a class="btn  btn--secondary  ga--phone-number  ga--phone-product" href="tel:[mttr_phone_number tel=true]">[mttr_icon icon=phone-rev align=middle size=small before=true][mttr_phone_number]</a>' );

		}


	// Wrap before product summary and AFTER the product images
	add_action( 'woocommerce_single_product_summary', 'mttr_woocommerce_product_summary_wrap_close', 80 );
	function mttr_woocommerce_product_summary_wrap_close() {

			echo '</li>';

		echo '</ul><!-- /.layout -->';

	}



	// Remove tabs etc
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );


// Close wrapper around bottom part of product detail page (images + summary)
add_action( 'woocommerce_after_single_product', 'mttr_close_product_detail_wrap', 30 );
function mttr_close_product_detail_wrap() {

		echo '</div><!-- /.wrap -->';

	echo '</div><!-- /.band -->';

}



// Add category feature details
add_action( 'woocommerce_after_single_product', 'mttr_woocommerce_category_features', 39 );
function mttr_woocommerce_category_features() {

	$terms = get_the_terms( get_the_ID(), 'product_cat' );

	// First get the categories
	if ( $terms ) {

		// Check for flexible content ON the category
		if ( have_rows( 'mttr_content_flexible', $terms[ 0 ] ) ) {

			// Loop through content
			while ( have_rows( 'mttr_content_flexible', $terms[ 0 ] ) ) {

				the_row();

				// Check that we ONLY want the static features
				if ( get_row_layout() == 'static_features' ) {

					$items = get_sub_field( 'items' );

					if ( $items ) {

						$data = array(

							'features' => $items,
							'heading' => get_sub_field( 'heading' ),
							'content' => get_sub_field( 'content' ),
							'modifiers' => 'band--grey',

						);

						mttr_get_template( 'template-parts/content/_c.content-static-features', $data );

					}

				}

			}

		}

	}

}



// Add shipping details
add_action( 'woocommerce_after_single_product', 'mttr_woocommerce_shipping_info', 40 );
function mttr_woocommerce_shipping_info() {

	// Get parent product ID
	$id = mttr_primary_product_id();

	$shipping_info = get_field( 'mttr_primary_product_shipping', $id );

	if ( $shipping_info ) {

		echo '<section class="band  band--large  band--content">';

			echo '<div class="wrap">';

				echo '<div class="u-line-length">';

					echo apply_filters( 'the_content', $shipping_info );

				echo '</div>';

			echo '</div><!-- /.wrap -->';

		echo '</section><!-- /.band -->';

	}

}






function mttr_sidebar_cta_button() {

	echo do_shortcode( '<a href="tel:[mttr_phone_number tel=true]" class="u-dock@wide  u-dock--right  btn--side  btn  btn--feature-secondary">' );

		echo do_shortcode( '[mttr_icon icon=phone-rev size=large align=middle]' );

		echo 'Can’t find it? Call us ';

		echo '<span>' . do_shortcode( '[mttr_phone_number]' ) . '</span>';

	echo '</a>';

}





/*
*	Cart styles (yey)
*/

add_action( 'woocommerce_before_checkout_form', 'mttr_woocommerce_checkout_login_form_open_wrap', 9 );
function mttr_woocommerce_checkout_login_form_open_wrap() {

	echo '<div class="band  u-hard--top">';

}

// Remove the login form from this weird position
// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );

add_action( 'woocommerce_before_checkout_form', 'mttr_woocommerce_checkout_login_form_close_wrap', 11 );
function mttr_woocommerce_checkout_login_form_close_wrap() {

	echo '</div><!-- /.band -->';

}


/**
 * Get an attachment ID given a URL.
 * From: http://wpscholar.com/blog/get-attachment-id-from-wp-image-url/
 * 
 * @param string $url
 *	
 * @return int Attachment ID on success, 0 on failure
 */
function get_attachment_id( $url ) {
	$attachment_id = 0;
	$dir = wp_upload_dir();
	if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
		$file = basename( $url );
		$query_args = array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'value'   => $file,
					'compare' => 'LIKE',
					'key'     => '_wp_attachment_metadata',
				),
			)
		);
		$query = new WP_Query( $query_args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post_id ) {
				$meta = wp_get_attachment_metadata( $post_id );
				$original_file       = basename( $meta['file'] );
				$cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
				if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
					$attachment_id = $post_id;
					break;
				}
			}
		}

		wp_reset_postdata();
	}
	return $attachment_id;
}




// Filter the product images
add_filter( 'woocommerce_cart_item_thumbnail', 'mttr_product_cart_image_filter', 10, 2 );
function mttr_product_cart_image_filter( $output, $cart_item ) {

	$id = $cart_item[ 'data' ]->id;
	echo mttr_get_primary_product_image( $id );

}




/*
*	Show the add to cart button even when the price is 0 (we're using quotes)
*/
// function mttr_woocommerce_show_price_on_zero( $purchasable, $product ){

//     if ( $product->get_price() == 0 ||  $product->get_price() == '' ) {

//     	$purchasable = true;
//     }

//     return $purchasable;

// }
// add_filter( 'woocommerce_is_purchasable', 'mttr_woocommerce_show_price_on_zero', 10, 2 );



// Remove the counts from the facet filters
add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );