<div class="band  band--large  footer">

	<div class="wrap">

		<ul class="listing  layout">

			<li class="layout__item  listing__item  footer__primary">

				<h4 class="footer__heading">Get In Touch</h4>

				<ul>

				<?php

					$footer_address = get_field( 'mttr_options_contact_physical_address', 'options');

					if ( $footer_address ) {

						echo '<li class="layout footer-menu--icon">';

							echo '<i class="icon icon--small ">'. mttr_get_icon( 'icon-home.svg' ) . '</i>';

							echo '<div>';

								echo '<h6 class="footer--title">Address</h6>';

								echo $footer_address;

							echo '</div>';

						echo '</li>';
					}


					$footer_phone = get_field ('mttr_options_contact_phone_number', 'options');

					if ( $footer_phone ) {

						echo '<li class="layout footer-menu--icon">';

							echo '<i class="icon icon--small">'. mttr_get_icon( 'icon-phone.svg' ). '</i>';

							echo '<div>';

								echo '<h6 class="footer--title">Phone</h6>';

								echo $footer_phone;

							echo '</div>';

						echo '</li>';

					}

					$footer_email = get_field ( 'mttr_options_contact_email_address', 'options');

					if ( $footer_email ) {

					echo '<li class="layout footer-menu--icon">';

						echo '<i class="icon icon--small">'. mttr_get_icon( 'icon-mail.svg' ) . '</i>';

						echo '<div>';

							echo '<h6 class="footer--title">Email</h6>';

							echo $footer_email;

						echo '</div>';

					echo '</li>';

					}

				?>

				</ul>

			</li><li class="layout__item  listing__item  footer__secondary">

				<h4 class="footer__heading">Our Products</h4>

				<?php

					wp_nav_menu(

						array(

							'theme_location' => 'footer',
							'menu_id' => 'footer-menu',
							'menu_class' => 'navigation  navigation--footer  u-flush--bottom',
						)

					);

				?>

			</li>

		</ul>

	</div><!-- /.wrap -->

</div><!-- /.band -->
