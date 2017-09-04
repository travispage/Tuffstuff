<?php

/* 
 *	Grid feature A
 */

	// Get vars
	$author = mttr_get_template_var( 'author' );
	$categories = mttr_get_template_var( 'categories' );
	$content = mttr_get_template_var( 'content' );
	$date = mttr_get_template_var( 'date' );
	$heading = mttr_get_template_var( 'heading' );
	$image = mttr_get_template_var( 'image' );
	$format = mttr_get_template_var( 'format' ); 
	$icon = mttr_get_template_var( 'icon' );
	$tags = mttr_get_template_var( 'tags' );
	$meta = mttr_get_template_var( 'meta' );
	$link = mttr_get_template_var( 'cta_link' );

	$modifiers = mttr_get_template_var( 'modifiers' );


	// Ensure an image is always used
	if ( !$image ) {

		$image = get_field( 'mttr_options_hero_default_image', 'options' );
		$image_url = wp_get_attachment_image_src( $image, 'mttr_square' );
		$image_url = $image_url[0];

	} else {

		$image_url = $image;

	}
	

	// Add spaces before modifiers
	if ( $modifiers ) {

		$modifiers = '  ' . $modifiers;

	}


?>
<a href="<?php echo esc_url( $link ); ?>" class="feature-a<?php echo $modifiers; ?>">

	<div class="feature-a__body">

		<div class="band  u-hard--top  feature-a__media" style="background-image: url( '<?php echo esc_url( $image_url ); ?>' );"></div>

		<?php if ( $icon && $format ) { ?>

			<div class="feature-a__meta">

				<i class="icon  icon--before"><?php echo mttr_get_icon( esc_html( $icon ) ); ?></i><span><?php echo esc_html( $format ); ?></span>

			</div>

		<?php } ?>

		<div class="feature-a__content">

			<h3 class="feature-a__title  u-flush--bottom"><span class="u-link--no-decoration"><?php echo esc_html( $heading ); ?></span></h3>

			<span class="feature-a__cta  nudge  nudge--right">Read More <i class="nudge__item  chevron  chevron--attention  chevron--right"></i></span>

		</div><!-- /.feature-a__content -->

	</div><!-- /.feature-a__body -->

</a><!-- /.feature-a -->
