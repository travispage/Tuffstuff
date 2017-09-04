<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package mttr
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<script src="//ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
<script>
  WebFont.load({
    google: {
      families: [ 'Lato:400,900', 'Black Ops One' ],
    }
  });
</script>

<!-- Google Analytics -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-90505142-1', 'auto');
  ga('send', 'pageview');

</script>
<!-- END Google Analytics -->

<!-- JSON-LD Schema -->
<script type="application/ld+json">
  {
  "@context": "http://schema.org",
  "@type": "LocalBusiness",
  "url": " http://www.tuffstuffaustralia.com.au/",
  "logo": "http://48gt4e2ljytz44jnpt16jdud.wpengine.netdna-cdn.com/wp-content/uploads/2016/03/tuff-stuff-logo-white.png",
  "image": ["http://48gt4e2ljytz44jnpt16jdud.wpengine.netdna-cdn.com/wp-content/uploads/2015/06/TuffStuff-Australia-Home-Page-Hero-TUFFTRAC-2500-768x512.jpg", "http://48gt4e2ljytz44jnpt16jdud.wpengine.netdna-cdn.com/wp-content/uploads/2016/08/Rubber-Pads-img4974-TUFFPAD.jpeg", "http://48gt4e2ljytz44jnpt16jdud.wpengine.netdna-cdn.com/wp-content/uploads/2016/08/Construction-rubber-Track-WM-800x800.jpg"], 
  "email": "contact@tuffstuffaustralia.com",
  "address": {
    	"@type": "PostalAddress",
    	"addressLocality": "Hallam",
    	"addressRegion": "VIC",
    	"postalCode":"3803",
    	"streetAddress": "25-27 Apollo Dr"
  },
  "hasMap": "https://www.google.com.au/maps/place/Tuff+Stuff+Australia/@-38.014699,145.2641053,17z/data=!3m1!4b1!4m5!3m4!1s0x6ad6112916cee5e9:0x582d68cacc4b92ae!8m2!3d-38.014699!4d145.266294",
  "geo": {
    	"@type": "GeoCoordinates",
    	"latitude": "-38.014389",
    	"longitude": "145.266357"
		 },     
  "description": "Tuff Stuff Australia supplies aftermarket replacement tracks and track system components for earthmoving and construction machinery.",
  "name": "Tuff Stuff Australia",
  "telephone": "1800-883-378",
  "openingHours": "Mo,Tu,We,Th,Fr 07:30-17:00"
  	}
</script>
<!-- END JSON-LD Schema -->

<link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon/apple-touch-icon-76x76.png">
<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon/favicon-16x16.png" sizes="16x16">
<link rel="icon" type="image/x-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon/favicon.ico">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-N9BMBX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-N9BMBX');</script>
<!-- End Google Tag Manager -->

<i class="site-blocker"></i>

<div id="page" class="hfeed site">

	<a class="skip-link  u-screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'mttr' ); ?></a>

	<?php 

		get_template_part( 'template-parts/header/_c.top', 'bar' );
		get_template_part( 'template-parts/header/_c.masthead', 'a' );

		// Mega nav
		// mttr_get_template( 'template-parts/header/_c.mega-nav', array() );

	?>

	<div id="content" class="site-content">

	<?php

		if ( is_page( 'styleguide' ) ) {

			mttr_get_template( 'template-parts/content/_c.content-styleguide' );

		}

	?>
