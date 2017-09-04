<?php


/*
*	Shortcode for the head office phone number
*/

add_shortcode( 'mttr_phone_number', 'mttr_phone_number_shortcode' );

function mttr_phone_number_shortcode( $atts ) {

	$phone_number = mttr_get_phone_number( );

	$a = shortcode_atts( array(
		'tel' => '',
		'number' => $phone_number,
	), $atts );

	if ( $phone_number && $a['tel'] != '' ) {

		return mttr_tel_filter_phone_number( esc_attr( $a[ 'number' ] ) );

	}

	return esc_attr( $a[ 'number' ] );

}




/*
*	Shortcode for the head office email address
*/

add_shortcode( 'mttr_email_address', 'mttr_email_address_shortcode' );

function mttr_email_address_shortcode( $atts ) {

	$email_address = mttr_get_email_address( );

	$a = shortcode_atts( array(
		'mailto' => '',
		'address' => $email_address,
	), $atts );

	if ( $email_address && $a['mailto'] != '' ) {

		return mttr_tel_filter_phone_number( esc_attr( $a[ 'address' ] ) );

	}

	return esc_attr( $a[ 'address' ] );

}



/*
*	Shortcode for the head office fax number
*/

add_shortcode( 'mttr_fax_number', 'mttr_fax_number_shortcode' );

function mttr_fax_number_shortcode( $atts ) {

	$fax_number = mttr_get_fax_number( );

	$a = shortcode_atts( array(
		'tel' => '',
		'number' => $fax_number,
	), $atts );

	if ( $fax_number  &&  $a['tel'] != '' ) {

		return mttr_tel_filter_phone_number( esc_attr( $a[ 'number' ] ) );

	}

	return esc_attr( $a[ 'number' ] );

}



/*
*	Shortcode for the head office physical address
*/

add_shortcode( 'mttr_address', 'mttr_address_shortcode' );

function mttr_address_shortcode( $atts ) {

	$detail = get_field( 'mttr_options_contact_physical_address', 'options' );

	$a = shortcode_atts( array(
		'tel' => '',
		'address' => $detail,
	), $atts );

	return $a[ 'address' ];

}




/*
*	Shortcode for adding icons - this is connected with the JS SVG Injector
*/
add_shortcode( 'mttr_icon', 'mttr_icon_shortcode' );

function mttr_icon_shortcode( $atts ) {

	$a = shortcode_atts( array(
		'icon' => '',
		'size' => '',
		'before' => '',
		'align' => '',
	), $atts );



	if ( $a[ 'icon' ] != '' ) {

		$icon = '<i class="icon';

			if ( $a[ 'size' ] != '' ) {

				$icon .= '  icon--' . esc_attr( $a[ 'size' ] );

			}

			if ( $a[ 'before' ] != '' ) {

				$icon .= '  icon--before';

			}

			if ( $a[ 'align' ] != '' ) {

				$icon .= '  icon--' . esc_attr( $a[ 'align' ] );

			}

			$icon .= '">';

			$icon .= mttr_get_icon( esc_attr( $a[ 'icon' ] ) . '.svg' );

		$icon .= '</i>';

		return $icon;

	} else {

		return false;

	}

}