<?php
function vum_get_api_params( $url ) {

	$url->lang           = get_option( 'wpm_o_lang' );
	$url->branding_img = get_option( 'wpm_o_branding_img' );
	$url->branding_logo = get_option( 'wpm_o_branding_logo' );
	$url->video_image  = get_option( 'wpm_o_custom_vid_placeholder' );

	$url_params = '';

	foreach ( $url as $k => $v ) {
		
		if ( $v === FALSE ) $v = '0';

		$url_params .= $k . ':\'' . $v . '\',';

	}

	if (substr($url_params, -1, 1) == ',') $url_params = substr_replace( $url_params, '', - 1 );
	
	return $url_params;
}

function vum_domain( $url ) {

	$url = Vum::vum_domain . $url;
	if( is_ssl() ){
		$url = str_replace( 'http://', 'https://', $url );
	}

	return $url;
}