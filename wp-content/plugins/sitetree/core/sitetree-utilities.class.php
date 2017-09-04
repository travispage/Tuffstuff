<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */

/**
 * Static class: it's a wrapper for a collection of globally accessible methods.
 *
 * @since 1.4
 */
class SiteTreeUtilities {
	/**
	 *
	 *
	 * @since 1.4
	 */
	private static $xmlTags = array(
		//'urlset'		=> array( 'xmlns' => true, 'xmlns:image' => true ),
		'url'		 	=> true,
		'loc'		 	=> true,
		'changefreq' 	=> true,
		'lastmod'		=> true,
		'priority'		=> true,
		'image:image'	=> true,
		'image:title'	=> true,
		'image:loc'		=> true,
		'image:caption' => true
	);

	/**
	 * This is a "just in case" contructor: we don't want to instantiate this class by accident.
	 *
	 * @since 1.4
	 */
	private function __construct() {}
	
	/**
	 * Sanitises a fragment of HTML5 code.
	 *
	 * @since 1.4
	 *
	 * @param string $fraqment
	 * @return string
	 */
	public static function ksesHTML5( $fraqment ) {
		global $allowedposttags;
		
		$allowed_tags = $allowedposttags;
		$allowed_tags['time']		= array( 'datetime' => true );
		$allowed_tags['section']	= array( 'id' => true );
		$allowed_tags['p']['id']	= true;
		$allowed_tags['div']['id']	= true;
		
		return wp_kses( $fraqment, $allowed_tags );
	}
	
	/**
	 * Sanitises a fragment of XML code.
	 *
	 * @since 1.4
	 *
	 * @param string $xml
	 * @return string
	 */
	public static function ksesXML( $xml ) {
		$xml = wp_kses_no_null( $xml );
		$xml = wp_kses_js_entities( $xml );
		$xml = wp_kses_normalize_entities( $xml );
		
		return preg_replace_callback( '%(<[^>]*(>|$)|>)%', array( 'self', 'kses_split' ), $xml );
	}
	
	/** 
	 * This is a modified version of the WordPress function wp_kses_split2.
	 *
	 * @since 1.4
	 *
	 * @param array $match
	 * @return string Fixed HTML element
	 */
	private static function kses_split( $match ) {
		$string = wp_kses_stripslashes( $match[0] );
		
		// Encode the ">" character
		if ( substr($string, 0, 1) != '<' )
			return '&gt;';
			
		// Do not allow HTML comments
		if ( '<!--' == substr( $string, 0, 4 ) )
			return '';
		
		// It's seriously malformed
		if (! preg_match( '%^<\s*(/\s*)?([a-zA-Z0-9:]+)([^>]*)>?$%', $string, $matches ) )
			return '';
		
		$slash	  = trim( $matches[1] );
		$elem	  = $matches[2];
		//$attrlist = $matches[3];
		
		// They are using a not allowed HTML element
		if (! isset( self::$xmlTags[strtolower($elem)] ) )
			return '';
			
		return ( $slash ? "</$elem>" : "<$elem>" );
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 *
	 * @param int $timestamp
	 * @param string $format
	 * @return string
	 */
	public static function date( $timestamp, $format = '' ) {
		if ( $timestamp < 946684800 ) // 2000-01-01
			return '-';
			
		if (! $format )
			$format = get_option('date_format') . ' @ ' . get_option('time_format');
			
		return gmdate( $format, $timestamp );
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 *
	 * @param int $timestamp
	 * @param string $format
	 * @return string
	 */
	public static function localDate( $timestamp, $format = '' ) {
		return self::date( $timestamp + ( get_option('gmt_offset') * 3600 ), $format );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param string $date
	 * @param string $format
	 * @return string
	 */
	public static function mysqlToDate( $date, $format = '' ) {
		if (! $format )
			$format = get_option('date_format');
		
		return mysql2date($format, $date);
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 *
	 * @param int $number
	 * @return string|int
	 */
	public static function numberToOrdinal( $number ) {
		$ordinals = array(
			__( 'first', 'sitetree' ),
			__( 'second', 'sitetree' ),
			__( 'third', 'sitetree' )
		);
		
		return isset( $ordinals[--$number] ) ? $ordinals[$number] : ++$number;
	}
	
	/**
	 * Sanitises a string that must be used in the attribute 'title' of an HTML tag.
	 *
	 * @since 1.4
	 *
	 * @param string $title
	 * @return string
	 */
	public static function escTitleAttr( $title ) {
		return esc_attr( strip_tags($title) );
	}
	
	/**
	 * Returns the array of options used to render the drop-down menu of priorities.
	 *
	 * @since 1.4
	 *
	 * @param int $start_value
	 * @param bool $add_reset
	 * @return array
	 */
	public static function &priorities( $start_value = 1, $add_reset = false ) {
		$priorities = array( 'none' => '-' );
		
		if ( $add_reset )
			$priorities['reset'] = __('Default', 'sitetree');
			
			
		for ( $i = $start_value; $i > 0; $i -= 0.1 ) {
			$num = number_format($i, 1);
			$priorities[$num] = ($num * 100) . '%';
		}
		
		return $priorities;
	}
	
	/**
	 * Returns the array of options used to render the drop-down menu of frequencies.
	 *
	 * @since 1.4
	 *
	 * @param bool $add_reset
	 * @return array
	 */
	public static function &frequencies( $add_reset = false ) {
		$frequencies = array( 'none' => '-' );
		
		if ( $add_reset )
			$frequencies['reset'] = __('Default', 'sitetree');
			 
		$frequencies['hourly']	= __('Hourly', 'sitetree'); 
		$frequencies['daily']	= __('Daily', 'sitetree'); 
		$frequencies['weekly']	= __('Weekly', 'sitetree'); 
		$frequencies['monthly'] = __('Monthly', 'sitetree'); 
		$frequencies['yearly']	= __('Yearly', 'sitetree');
		$frequencies['always']	= __('Always', 'sitetree'); 
		$frequencies['never']	= __('Never', 'sitetree');
		
		return $frequencies;
	}
	
	/**
	 * @since 1.4
	 *
	 * @param 
	 * @return string
	 */
	public static function adminURL( $page_id, $query_args = array() ) {
		$args = array( 'page' => $page_id );
		
		if ( $query_args ) {
			$args += $query_args;
			
			if ( isset( $args['action'] ) )
				$args['_sitetree_nonce'] = wp_create_nonce( $args['action'] );
		}	
		
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public static function adminRedirect( $page_id, $query_args = array() ) {
		wp_redirect( self::adminURL( $page_id, $query_args ) );
		exit;
	}
}
?>