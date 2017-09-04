<?php
/**
 * mttr functions and definitions
 *
 * @package mttr
 */

if ( ! function_exists( 'mttr_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function mttr_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on mttr, use a find and replace
	 * to change 'mttr' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'mttr', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'mttr' ),
		'secondary' => esc_html__( 'Secondary', 'mttr' ),
		'sidebar' => esc_html__( 'Sidebar', 'mttr' ),
		'footer' => esc_html__( 'Footer', 'mttr' ),
		'legal' => esc_html__( 'Legal', 'mttr' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Layout specific image sizes
	add_image_size( 'mttr_hero', 1500, 800, true );
	add_image_size( 'mttr_square', 800, 800, true );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'mttr_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // mttr_setup
add_action( 'after_setup_theme', 'mttr_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function mttr_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'mttr_content_width', 640 );
}
add_action( 'after_setup_theme', 'mttr_content_width', 0 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function mttr_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'mttr' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'mttr_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function mttr_scripts() {

	if (!is_admin()) {

		wp_deregister_script( 'jquery' ); 
		wp_register_script( 'jquery', '//code.jquery.com/jquery-1.11.3.min.js', array(), '1.3.2' ); 
		wp_enqueue_script( 'jquery' );

	}

	// Enqueue styletile css for the styleguide page
	if ( is_page( 'styleguide') ) {

		wp_enqueue_style( 'mttr-styletile-font', '//fonts.googleapis.com/css?family=Montserrat:700,400' );
		wp_enqueue_style( 'mttr-styletile-style', get_template_directory_uri() . '/assets/css/styletile.css' );

	}

	// Base Stylesheet
	wp_enqueue_style( 'mttr-style', get_template_directory_uri() . '/assets/css/main.css' );

	// SVG Injector
	wp_enqueue_script( 'svg-injector', get_template_directory_uri() . '/assets/js/vendor/svg-injector.min.js', array(), '20150710', true );

	// Base Scripts
	wp_enqueue_script( 'mttr-scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', array('jquery','svg-injector'), '20150623', true );

	//wp_enqueue_script( 'mttr-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	//wp_enqueue_script( 'mttr-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		//wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mttr_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Matter Kit Base Theme Functions
 */
require get_template_directory() . '/inc/mttr-functions.php';

/**
 * Matter Kit Component API
 */
require get_template_directory() . '/inc/mttr-component-layer.php';

/**
 * Matter Kit Base Shortcodes
 */
require get_template_directory() . '/inc/mttr-shortcodes.php';


/**
 * Woocommerce hook info
 */
require get_template_directory() . '/inc/mttr-woocommerce.php';


/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';



//* Add ACF options page...
if ( function_exists( 'acf_add_options_page' ) ) {

	acf_add_options_page( array(
		'page_title' 	=> 'Theme Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> 'General Theme Settings'
	) );

	acf_add_options_sub_page( array(
		'page_title' 	=> 'General Theme Settings',
		'menu_title'	=> 'General Theme Settings',
		'parent_slug'	=> 'theme-general-settings',
	) );

	acf_add_options_sub_page( array(
		'page_title' 	=> 'Contact Theme Settings',
		'menu_title'	=> 'Contact Theme Settings',
		'parent_slug'	=> 'theme-general-settings',
	) );

}


// Push Gravity Forms to the footer
add_filter( 'gform_init_scripts_footer', '__return_true' );

// Set the tabindex to false
add_filter( 'gform_tabindex', '__return_false' );



// Add excerpt support to pages
add_action( 'init', 'mttr_add_excerpts_to_pages' );
function mttr_add_excerpts_to_pages() {

     add_post_type_support( 'page', 'excerpt' );

}


/*
*	Change the default excerpt length
*/
function mttr_excerpt_length( $length ) {
    
    return 30;
}
add_filter( 'excerpt_length', 'mttr_excerpt_length' );



/*
*	Change the default excerpt [...]
*/
function mttr_excerpt_more($excerpt) {

	return '...';

}
add_filter( 'excerpt_more', 'mttr_excerpt_more' );



// Remove emoji scripts
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );



// Output the GA code, if supplied
add_action( 'wp_footer', 'mttr_google_analytics' );
function mttr_google_analytics() {

	$analytics_ga_code = get_field( 'mttr_options_google_analytics' );

	if ( $analytics_ga_code ) {

	?><script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', '<?php echo esc_html( $analytics_ga_code ); ?>', 'auto');
	  ga('send', 'pageview');

	</script><?php

	}

}



// Output OG tags
add_action( 'wp_head', 'mttr_open_graph_tags' );
function mttr_open_graph_tags() {

	if ( !is_archive() ) {

		$hero_image = get_field( 'mttr_options_hero_default_image', 'options' );

		// Main front page
		if ( is_front_page() ) {

		// Blog
		} elseif ( is_home() ) {

			if ( has_post_thumbnail( get_option( 'page_for_posts' ) ) ) {

				$hero_image = get_post_thumbnail_id( get_option( 'page_for_posts' ) );

			}

		// Everything else
		} else {
			
			if ( has_post_thumbnail( get_the_ID() ) ) {

				$hero_image = get_post_thumbnail_id( );

			}

		}

		// Get the image URL
		$hero_image_url = wp_get_attachment_image_src( $hero_image, 'hero' );

		if ( $hero_image_url ) {

			$hero_image_url = $hero_image_url[0];

		}

		echo '<meta property="og:title" content="' . get_the_title() . '">';
		echo '<meta property="og:site_name" content="' . get_the_title() . '">';
		echo '<meta property="og:url" content="' . get_the_permalink() . '">';
		echo '<meta property="og:image" content="' . esc_url( $hero_image_url ) . '">';

	}

}


/*
*	Add a dropdown to the editor
*/
function mttr_text_editor_add_style_dropdown( $buttons ) {

	array_unshift( $buttons, 'styleselect' );
	return $buttons;

}
add_filter( 'mce_buttons_2', 'mttr_text_editor_add_style_dropdown' );





/*
* Callback function to filter the MCE settings
*/
function mttr_editor_before_init_insert_class_options( $settings ) {  


	// Create array of new styles
		$new_styles = array(

			array(

				'title'	=> __( 'Text', 'mttr-text-styles' ),
				'items'	=> array(

					array(

						'title' => __( 'Display Heading','mttr-text-styles' ),  
						'block' => 'h3',
						'classes' => 'display-heading',
						'wrapper' => false,
						
					), 

					array(

						'title' => __( 'Large Display Heading','mttr-text-styles' ), 
						'block' => 'h2', 
						'classes' => 'display-heading  display-heading--beta',
						'wrapper' => false,
						
					), 

					array(  

						'title' => 'Lede',  
						'block' => 'p',  
						'classes' => 'lede',
						'wrapper' => false,

					),

					array(  

						'title' => 'Blockquote',  
						'block' => 'blockquote',  
						'classes' => 'blockquote-a',
						'wrapper' => false,

					),
				),
			),

			array(

				'title'	=> __( 'Buttons', 'mttr-buttons' ),
				'items'	=> array(

					array(  

						'title' => 'Primary Button',  
						'selector' => 'a',  
						'classes' => 'btn  btn--primary',
						'wrapper' => false,

					),

					array(  

						'title' => 'Secondary Button',  
						'selector' => 'a',  
						'classes' => 'btn  btn--ghost',
						'wrapper' => false,

					),

				),
			),
		);

		// Merge old & new styles
		$settings['style_formats_merge'] = false;

		// Add new styles
		$settings['style_formats'] = json_encode( $new_styles );

		// Return New Settings
		return $settings;
  
} 

// Attach callback to 'tiny_mce_before_init' 
add_filter( 'tiny_mce_before_init', 'mttr_editor_before_init_insert_class_options' ); 

add_editor_style( get_stylesheet_directory_uri() . '/assets/css/editor.css' );





/*
*	Filter Wordpress Galleries
*/
function mttr_wp_gallery( $output = '', $atts, $instance ) {
	
	$return = $output; // fallback

	// Check to see that there are IDs available
	if ( $atts['ids'] ) {

		$gallery_images = explode( ',', $atts['ids'] );

		$return .= '<ul class="layout  listing  u-soft--bottom  js-popup-gallery">';

			foreach( $gallery_images as $gallery_item ) {

				$image_src = wp_get_attachment_image_src( $gallery_item, 'large' );
				$image_thumb = wp_get_attachment_image_src( $gallery_item, 'mttr_square' );

				// Check to make sure the image src is available
				if ( is_array( $image_src ) ) {
					
					$return .= '<li class="layout__item  listing__item  g-one-third  g-one-fifth@lap">';
						
						$return .= '<a class="image-link  image-link--zoom  overlay" href="' . esc_url( $image_src[0] ) . '">';
							
							$return .=  '<img class="image-link__media" src="' . esc_url( $image_thumb[0] ) . '" alt="" />';

							$return .= '<div class="image-link__content  u-center">';

								$return .= '<i class="image-link__icon  icon  icon--large  overlay__body">' . mttr_get_icon( 'icon-search-masthead.svg' ) . '</i>';

							$return .= '</div>';
						
						$return .= '</a><!-- /.image-link -->';

					$return .= '</li><!-- /.layout__item -->';

				}

			}

		$return .= '</ul><!-- /.layout -->';

	}

	return $return;
}

add_filter( 'post_gallery', 'mttr_wp_gallery', 10, 3 );



function mttr_get_contextual_title() {

	$title = get_the_title();

	if ( is_archive() ) {

		if ( is_category() ) { 

			$title = single_cat_title( '', false );

		} elseif( is_tag() ) { 

			$title = single_tag_title( '', false );

		} elseif ( is_day() ) { 

			$title = 'Archive for ' . date();

		} elseif ( is_month() ) { 

			$title = 'Archive for ' . date( 'F, Y' );

		} elseif( is_year() ) {

			$title = 'Archive for ' . date( 'Y' );

		}

	}


	// Blog main
	if ( is_home() && get_option( 'page_for_posts' ) ) {

		$title = get_the_title( get_option( 'page_for_posts' ) );

	}


	// 404
	if ( is_404() ) {

		$title = 'Page not found';

	}


	// Search
	if ( is_search() ) {

		$title = 'Search Results for: ' . get_search_query();

	}


	// Nothing found
	if ( is_search()  &&  !have_posts() ) {

		$title = 'Nothing found';

	}

	return $title;

}


function mttr_get_contextual_content() {

	$content = get_the_excerpt();

	if ( is_archive() ) {

		if ( is_category() ) { 

			$content = category_description();

		}

	}


	// Blog main
	if ( is_home() && get_option( 'page_for_posts' ) ) {

		$content = get_the_content( get_option( 'page_for_posts' ) );

	}


	// 404
	if ( is_404() ) {

		$content = 'Sorry, this page doesn\'t seem to exist!';

	}


	// Nothing found
	if ( is_search()  &&  !have_posts() ) {

		$content = 'Please try another search';

	}

	return $content;

}






/**
 * Extend WordPress search to include custom fields
 *
 * http://adambalee.com
 * http://adambalee.com/search-wordpress-by-custom-fields-without-a-plugin/
 */

/**
 * Join posts and postmeta tables
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
 */
function cf_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {    
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    
    return $join;
}
add_filter('posts_join', 'cf_search_join' );

/**
 * Modify the search query with posts_where
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
 */
function cf_search_where( $where ) {
    global $pagenow, $wpdb;
   
    if ( is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

    return $where;
}
add_filter( 'posts_where', 'cf_search_where' );

/**
 * Prevent duplicates
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
 */
function cf_search_distinct( $where ) {
    global $wpdb;

    if ( is_search() ) {
        return "DISTINCT";
    }

    return $where;
}
add_filter( 'posts_distinct', 'cf_search_distinct' );





/*
*	Permissions for editors
*/

function mttr_add_editor_permissions() {

    $role = get_role( 'editor' );

    if ( !$role->has_cap( 'gform_full_access' ) ) {
   
   		$role->add_cap( 'gform_full_access' );

   	}

   	if ( !$role->has_cap( 'edit_theme_options' ) ) {
   
   		$role->add_cap( 'edit_theme_options' );

   	}

}
 
add_action( 'admin_init', 'mttr_add_editor_permissions' );