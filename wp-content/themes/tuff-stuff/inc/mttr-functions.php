<?php

/*
*	Output the brand image
*/
function mttr_brand( $alt = false ) {

	$brand = mttr_get_brand( $alt );

	if ( $brand ) {

		echo $brand;

	}

	return false;

}






/*
*	Get the brand image
*/
function mttr_get_brand( $alt = false ) {

	if ( $alt ) {

		$logo = get_field( 'mttr_options_brand_logo_alt', 'options' );

	} else {

		$logo = get_field( 'mttr_options_brand_logo', 'options' );

	}

	if ( $logo ) {

		return '<img class="brand-image" alt="' . get_bloginfo( 'name' ) . ' Logo" src="' . esc_url( $logo ) . '">';

	} else {

		return '<img class="brand-image" alt="' . get_bloginfo( 'name' ) . ' Logo" src="' . get_stylesheet_directory_uri( ) . '/assets/img/matter-solutions-logo.svg">';

	}

}






/*
*	Return the file size of a given file
*/
function mttr_get_file_size( $file ) {

	$bytes = filesize( $file );
	$s = array('b', 'Kb', 'Mb', 'Gb');
	$e = floor( log( $bytes ) / log( 1024 ) );

	return sprintf( '%.2f '.$s[$e], ( $bytes / pow( 1024, floor( $e ) ) ) );

}






/*
*	Output the social menu
*/
function mttr_social_menu( ) {

	$social_menu = get_field( 'mttr_option_social_media', 'options' );

	if ( $social_menu ) {

		$output = '';

		$output .= '<ul class="social-menu  layout  layout--auto  layout--small  list  list--inline">';

			foreach( $social_menu as $social_media ) {

				$output .= '<li class="layout__item  list__item  social-menu__item">';

					$output .= '<a href="' . esc_url( $social_media['url'] ) . '">';

						$output .= '<i class="icon">';

							$output .= mttr_get_icon( esc_html( $social_media['account_type'] ) . '.svg' );

						$output .= '</i>';

						if ( !empty( $social_media['name'] ) ) {

							$output .= '<span class="u-screen-reader-text">';

								$output .= esc_html( $social_media['name'] );

							$output .= '</span>';

						}

					$output .= '</a>';

				$output .= '</li>';

			}

		$output .= '</ul>';

		return $output;

	}

	return false;

}







/*
*	Output a link to the HEAD OFFICE phone number
*/
function mttr_phone_number( $icon = false ) {

	$detail = mttr_get_phone_number( );

	if ( $detail ) {

		echo '<a href="tel:' . esc_html( mttr_tel_filter_phone_number( $detail ) ) . '">';

			if ( $icon ) mttr_icon( 'icon-phone.svg' );
			echo $detail;

		echo '</a>';

		return true;

	}

	return false;

}






/*
*	Return the HEAD OFFICE phone number
*/
function mttr_get_phone_number( ) {

	$detail = get_field( 'mttr_options_contact_phone_number', 'options' );

	if ( $detail ) {

		return $detail;

	}

	return false;

}






/*
*	Run a tel: filter over the phone number
*/
function mttr_tel_filter_phone_number( $phone_number ) {

	return preg_replace( '/[^0-9]/', '', $phone_number );

}






/*
*	Output the fax number
*/
function mttr_fax_number( $icon = false ) {

	$detail = mttr_get_fax_number( );

	if ( $detail ) {

		echo '<a href="tel:' . esc_html( mttr_tel_filter_phone_number( $detail ) ) . '">';

			if ( $icon ) mttr_icon( 'icon-fax.svg' );
			echo $detail;

		echo '</a>';

		return true;

	}

	return false;

}







/*
*	Get the fax number
*/
function mttr_get_fax_number( ) {

	$detail = get_field( 'mttr_options_contact_fax_number', 'options' );

	if ( $detail ) {

		return $detail;

	}

	return false;

}






/*
*	Output a link to the HEAD OFFICE email address
*/
function mttr_email_address( $icon = false ) {

	$detail = mttr_get_email_address( );

	if ( $detail ) {

		echo '<a href="mailto:' . antispambot( $detail ) . '">';

			if ( $icon ) mttr_icon( 'icon-mail.svg' );
			echo $detail;

		echo '</a>';

		return true;

	}

	return false;

}






/*
*	Get the HEAD OFFICE email address
*/
function mttr_get_email_address( ) {

	$detail = get_field( 'mttr_options_contact_email_address', 'options' );

	if ( $detail ) {

		return $detail;

	}

	return false;

} 






/*
*	Output the HEAD OFFICE physical address
*/
function mttr_physical_address( $icon = false ) {

	$detail = mttr_get_physical_address( );

	if ( $detail ) {

		if ( $icon ) mttr_icon( 'icon-location.svg' );
		echo apply_filters( 'the_content', $detail );

		return true;

	}

	return false;

}






/*
*	Get the HEAD OFFICE physical address
*/
function mttr_get_physical_address( $address ) {

	$detail = get_field( 'mttr_options_contact_physical_address', 'options' );

	if ( $detail ) {

		return $detail;

	}

	return false;

}





/*
*	Unformat an address (remove tags and BRs)
*/
function mttr_unformat_address( $address ) {

	if ( $address ) {

		$address = strip_tags( $address );
		$address = str_replace( '<br>', '', $address );
		$address = str_replace( '<br />', '', $address );

		return $address;

	}

	return false;

}





/*
*	Get the link to the google maps directions
*/
function mttr_get_directions_uri( $address ) {

	$address = mttr_unformat_address( mttr_get_physical_address( ) );

	if ( $address ) {

		return esc_url( 'https://maps.google.com?daddr=' . urlencode( $address ) );

	}

	return false;

}





/*
*	Output an icon 
*/
function mttr_get_icon( $icon, $fallback = false ) {

	if ( $icon ) {

		// Check to see if the icon exists
		$file = 'assets/img/icons/' . esc_html( $icon );

		$stylesheet_directory = trailingslashit( get_stylesheet_directory() );
		$stylesheet_directory_uri = trailingslashit( get_stylesheet_directory_uri() );
		

		// If file exists, create new path
		if ( file_exists( ( $stylesheet_directory . $file ) ) ) {

			$file_path_info = pathinfo( ( $stylesheet_directory_uri . $file ) );
			$file_uri = $stylesheet_directory_uri . 'assets/img/icons/' . esc_html( $icon );

			if ( strtolower( $file_path_info['extension'] ) == 'svg' ) {

				$file_fallback = 'assets/img/icons/png/' . esc_html( $file_path_info['filename'] . '.png' );

				if ( file_exists( ( $stylesheet_directory . $file_fallback ) ) && $fallback ) {

					return '<img src="' . ( $stylesheet_directory_uri . $file_fallback ) . '" class="js-inject-svg" alt="" data-src="' . ( $stylesheet_directory_uri . $file ) . '">';

				} else {

					return '<img src="" class="js-inject-svg" alt="" data-src="' . ( $stylesheet_directory_uri . $file ) . '">';

				}
				

			} else {

				return '<img alt="" src="' . ( $stylesheet_directory_uri . $file ) . '">';

			}

		}

	}

	return false;

}





/*
*	Output an icon
*/
function mttr_icon( $icon, $fallback = false ) {

	echo mttr_get_icon( $icon, $fallback );

}





/*
*	Useful if you need to limit words for an area
*/
function mttr_excerpt( $text, $trim_length = 30 ) {

	return wp_trim_words( $text, $trim_length, '' );

}





/*
*	Get featured image URL
*/
function mttr_get_post_thumbnail_url( $id, $imgsize = 'mttr_hero' ){

	if ( has_post_thumbnail( $id ) ) {
		
		$img_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), $imgsize );

		if ( isset( $img_arr[0] ) ) {

			return $img_arr[0];

		}
		
		return false;		

	} else {

		return false;

	}

}






/*
*	Get the GLOBAL default featured image URL
*/
function mttr_get_default_image( $imgsize = 'mttr_hero' ){

	$default_hero = get_field( 'mttr_options_hero_default_image', 'options' );

	if ( $default_hero ) {
		
		$img_arr = wp_get_attachment_image_src( get_field( 'mttr_options_hero_default_image', 'options' ), $imgsize );

		if ( isset( $img_arr[0] ) ) {

			return $img_arr[0];

		}
		
		return false;		

	} else {

		return false;

	}

}