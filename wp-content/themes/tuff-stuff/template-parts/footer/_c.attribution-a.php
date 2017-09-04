<a target="_blank" rel="nofollow" class="attribution" href="https://www.mttr.io/"><?php 

	$file = trailingslashit( get_stylesheet_directory() ) . 'assets/img/matter-solutions-logo.svg';

	if ( file_exists( $file ) ) {

		readfile( $file );

	}

?></a>