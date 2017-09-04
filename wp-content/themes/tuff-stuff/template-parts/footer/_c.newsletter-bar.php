<?php 

$modifiers = mttr_get_template_var( 'modifiers' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}


?>
<div class="newsletter-bar<?php echo esc_html( $modifiers ); ?>  band">

	<div class="wrap">

		<?php 

			// Output the gravity form
			gravity_form( 3, true, true, false, null, true );

		?>

	</div>

</div>