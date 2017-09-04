<?php 
/**
 * Plugin Name: ShortPixel Image Optimizer
 * Plugin URI: https://shortpixel.com/
 * Description: ShortPixel optimizes images automatically, while guarding the quality of your images. Check your <a href="options-general.php?page=wp-shortpixel" target="_blank">Settings &gt; ShortPixel</a> page on how to start optimizing your image library and make your website load faster. 
 * Version: 4.5.3
 * Author: ShortPixel
 * Author URI: https://shortpixel.com
 * Text Domain: shortpixel-image-optimiser
 * Domain Path: /lang
 */

define('SP_RESET_ON_ACTIVATE', false); //if true TODO set false
//define('SHORTPIXEL_DEBUG', true);

define('SHORTPIXEL_PLUGIN_FILE', __FILE__);

define('SP_AFFILIATE_CODE', '');

define('SHORTPIXEL_IMAGE_OPTIMISER_VERSION', "4.5.3");
define('SP_MAX_TIMEOUT', 10);
define('SP_VALIDATE_MAX_TIMEOUT', 15);
define('SP_BACKUP', 'ShortpixelBackups');
define('MAX_API_RETRIES', 50);
define('MAX_ERR_RETRIES', 5);
define('MAX_FAIL_RETRIES', 3);
$MAX_EXECUTION_TIME = ini_get('max_execution_time');

require_once(ABSPATH . 'wp-admin/includes/file.php');

$sp__uploads = wp_upload_dir();
define('SP_UPLOADS_BASE', $sp__uploads['basedir']);
define('SP_UPLOADS_NAME', basename(is_main_site() ? SP_UPLOADS_BASE : dirname(dirname(SP_UPLOADS_BASE))));
define('SP_UPLOADS_BASE_REL', str_replace(get_home_path(),"", $sp__uploads['basedir']));
$sp__backupBase = is_main_site() ? SP_UPLOADS_BASE : dirname(dirname(SP_UPLOADS_BASE));
define('SP_BACKUP_FOLDER', $sp__backupBase . '/' . SP_BACKUP);

/*
 if ( is_numeric($MAX_EXECUTION_TIME)  && $MAX_EXECUTION_TIME > 10 )
    define('MAX_EXECUTION_TIME', $MAX_EXECUTION_TIME - 5 );   //in seconds
else
    define('MAX_EXECUTION_TIME', 25 );
*/

define('MAX_EXECUTION_TIME', 2 );
define("SP_MAX_RESULTS_QUERY", 6);    

function shortpixelInit() {
    global $pluginInstance;
    //is admin, is logged in - :) seems funny but it's not, ajax scripts are admin even if no admin is logged in.
    $prio = get_option('wp-short-pixel-priorityQueue');
    if (!isset($pluginInstance)
        && (($prio && is_array($prio) && count($prio) && get_option('wp-short-pixel-front-bootstrap'))
            || is_admin()
               && (function_exists("is_user_logged_in") && is_user_logged_in())
               && (   current_user_can( 'manage_options' )
                   || current_user_can( 'upload_files' )
                   || current_user_can( 'edit_posts' )
                  )
           )
       ) 
    {
        require_once('wp-shortpixel-req.php');
        $pluginInstance = new WPShortPixel;
    }
} 

function handleImageUploadHook($meta, $ID = null) {
    global $pluginInstance;
    if(!isset($pluginInstance)) {
        require_once('wp-shortpixel-req.php');
        $pluginInstance = new WPShortPixel;
    }
    return $pluginInstance->handleMediaLibraryImageUpload($meta, $ID);
}

function shortpixelNggAdd($image) {
    global $pluginInstance;
    if(!isset($pluginInstance)) {
        require_once('wp-shortpixel-req.php');
        $pluginInstance = new WPShortPixel;
    }
    $pluginInstance->handleNextGenImageUpload($image);
}

function shortPixelActivatePlugin () {
    require_once('wp-shortpixel-req.php');
    WPShortPixel::shortPixelActivatePlugin();    
}

function shortPixelDeactivatePlugin () {
    require_once('wp-shortpixel-req.php');
    WPShortPixel::shortPixelDeactivatePlugin();    
}


/**
* filter function to force wordpress to add our custom srcset values
* @param array  $sources {
*     One or more arrays of source data to include in the 'srcset'.
*
*     @type type array $width {
*          @type type string $url        The URL of an image source.
*          @type type string $descriptor The descriptor type used in the image candidate string,
*                                        either 'w' or 'x'.
*          @type type int    $value      The source width, if paired with a 'w' descriptor or a
*                                        pixel density value if paired with an 'x' descriptor.
*     }
* }
* @param array  $size_array    Array of width and height values in pixels (in that order).
* @param string $image_src     The 'src' of the image.
* @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
* @param int    $attachment_id Image attachment ID.
*/
function sp_add_webp_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ){
	
	// image base name		
	$image_basename = wp_basename( $image_meta['file'] );
	// upload directory info array
	$upload_dir_info_arr = wp_upload_dir();
	// base url of upload directory
	$image_baseurl = $baseurl = $upload_dir_info_arr['baseurl'];
        $image_basedir = $basedir = $upload_dir_info_arr['basedir'];
	
	// Uploads are (or have been) in year/month sub-directories.
	if ( $image_basename !== $image_meta['file'] ) {
		$dirname = dirname( $image_meta['file'] );
		
		if ( $dirname !== '.' ) {
			$image_baseurl = trailingslashit( $baseurl ) . $dirname; 
			$image_basedir = trailingslashit( $basedir ) . $dirname; 
		}
	}

	$image_baseurl = trailingslashit( $image_baseurl );
	$image_basedir = trailingslashit( $image_basedir );
	// check whether our custom image size exists in image meta
        foreach($image_meta['sizes'] as $key => $size) {
            $fn = $size['file'];
            $ext = pathinfo($fn, PATHINFO_EXTENSION);
            $webp = substr($fn, 0, strlen($fn) - strlen($ext)) . 'webp';
            if( file_exists($image_basedir .  $webp) ){
                // add source value to create srcset
                $sources[ 'webp-' . $size['width'] ] = array(
                                 'url'        => $image_baseurl .  $webp,
                                 'descriptor' => 'w',
                                 'value'      => $size['width'],
                );
            }
        }	
	//return sources with new srcset value
	return $sources;
}
//not really working, the srcset does not accept webp AND other type of image together. Below trying with <picture> ...
//add_filter( 'wp_calculate_image_srcset', 'sp_add_webp_image_srcset', 10, 5 );




//Picture generation, hooked on the_content filter
function spConvertImgToPictureAddWebp($content) {
    require_once('class/front/img-to-picture-webp.php');
    //require_once('class/responsive-image.php');
    return ImgToPictureWebp::convert($content);
}
function spAddPictureJs() {
    // Don't do anything with the RSS feed.
    if ( is_feed() || is_admin() ) { return; }
    
    echo '<script>'
       . 'var spPicTest = document.createElement( "picture" );'
       . 'if(!window.HTMLPictureElement && document.addEventListener) {'
            . 'window.addEventListener("DOMContentLoaded", function() {'
                . 'var scriptTag = document.createElement("script");'
                . 'scriptTag.src = "' . plugins_url('/res/js/picturefill.min.js', __FILE__) . '";'
                . 'document.body.appendChild(scriptTag);'
            . '});'
        . '}'
       . '</script>';
}
//function spAddPicturefillJs() {
//    wp_enqueue_script( 'picturefill', plugins_url('/res/js/picturefill.min.js', __FILE__),  null, null, true);
//}
if ( get_option('wp-short-pixel-create-webp-markup')) { 
    add_filter( 'the_content', 'spConvertImgToPictureAddWebp' );
    add_action( 'wp_head', 'spAddPictureJs');
//    add_action( 'wp_enqueue_scripts', 'spAddPicturefillJs' );
}


if ( !function_exists( 'vc_action' ) || vc_action() !== 'vc_inline' ) { //handle incompatibility with Visual Composer
    add_action( 'init',  'shortpixelInit');
    add_action('ngg_added_new_image', 'shortpixelNggAdd');
    
    $autoMediaLibrary = get_option('wp-short-pixel-auto-media-library');
    if($autoMediaLibrary) {
        add_filter( 'wp_generate_attachment_metadata', 'handleImageUploadHook', 10, 2 );
    }
    
    register_activation_hook( __FILE__, 'shortPixelActivatePlugin' );
    register_deactivation_hook( __FILE__, 'shortPixelDeactivatePlugin' );
}
?>
