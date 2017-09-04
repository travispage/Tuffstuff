<?php

/*
*	Standard content
*/

// Get vars
$content = mttr_get_template_var( 'content' );
$sidebar = mttr_get_template_var( 'sidebar' );
$listing = mttr_get_template_var( 'listing' );
$menu = mttr_get_template_var( 'menu' );
$modifiers = mttr_get_template_var( 'modifiers' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}


// Ensure SOME form of content is provided
if ( $content || $sidebar || $listing || is_single() ) {

?><section class="band  band--large<?php echo $modifiers; ?>  band--content">

<?php

	echo '<div class="wrap">';

		echo '<div class="layout  layout--large  listing  listing--large">';

			if ( $content || $listing || is_single() ) {

				echo '<div class="layout__item  listing__item  g-two-thirds@lap  g-three-quarters@desk">';

					if ( $content ) { 

						echo '<div class="u-negate-btm-margin">';

							echo apply_filters( 'the_content', $content );

						echo '</div>';

					}

					if ( $listing ) {

						mttr_get_template( 'template-parts/content/_c.content-features', $listing );
						mttr_paged_navigation();

					}

				echo '</div>';

			}

			if ( $sidebar || $menu ) {

				echo '<div class="layout__item  listing__item  g-one-third@lap  g-one-quarter@desk">';

					if ( $menu ) {

						wp_nav_menu( $menu );

					}

					if ( $sidebar ) {

						echo '<div class="u-negate-btm-margin">';

							echo apply_filters( 'the_content', $sidebar );

						echo '</div>';

					}

				echo '</div>';

			}

		echo '</div>';	

	echo '</div><!-- /.wrap -->';

 ?>

</section>

<?php } // end if have any content