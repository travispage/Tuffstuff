<header class="masthead" role="banner">

	<div class="masthead__body">

		<div class="masthead__content">

			<ul class="list  list--bare  masthead__toggles">

				<li class="list__item  toggle--alpha">

					<button data-toggle-class="navigation--primary-is-open  off-canvas  off-canvas--left" data-toggle-target="body" class="menu-icon  btn  btn--transparent  btn--icon  toggle  js-toggle" aria-controls="primary-menu" aria-expanded="false"><span class="menu-icon__body"><i class="menu-icon__bars"></i></span><i class="u-screen-reader-text">Toggle Menu</i></button>

				</li><li class="list__item  toggle--beta">

					<a href="tel:<?php echo do_shortcode( '[mttr_phone_number tel="true"]' ); ?>" class="btn  btn--transparent  btn--icon  toggle  js-toggle" aria-controls="primary-menu" aria-expanded="false">

						<i class="icon  u-center"><?php echo mttr_get_icon( 'icon-phone.svg' ); ?></i>
						<i class="u-screen-reader-text">Call us on <?php echo do_shortcode( '[mttr_phone_number]' ); ?></i>

					</a>

				</li><!-- /.list__item -->

			</ul><!-- /.masthead__toggles -->

		</div>


		<div class="wrap">

			<ul class="layout  layout--middle  layout--flush">

				<li class="masthead__primary  layout__item">

					<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">

						<?php mttr_brand(); ?>

					</a>

				</li><li class="masthead__secondary  layout__item">

					<ul class="layout  layout--small  layout--middle  layout--auto">

						<li class="layout__item  masthead__search">

							<?php get_search_form(); ?>

						</li><!-- /.masthead__search

						--><li class="layout__item  masthead__nav">

							<nav role="navigation">

								<?php

									wp_nav_menu(

										array(

											'theme_location' => 'primary',
											'menu_id' => 'primary-menu',
											'menu_class' => 'navigation  navigation--sliding  navigation--horizontal  navigation--primary  u-flush--bottom',
											'after' => '<span class="menu-item-trigger  js-toggle--sub-menu"></span>'
										)

									);

									wp_nav_menu(

										array(

											'theme_location' => 'secondary',
											'menu_id' => 'secondary-menu-mobile',
											'menu_class' => 'navigation  navigation--sliding  navigation--horizontal  navigation--secondary  navigation--mobile  u-flush--bottom',
											'after' => '<span class="menu-item-trigger  js-toggle--sub-menu"></span>'
										)

									);

								?><ul class="layout  u-hide@desk  navigation--mobile">

									<li class="layout__item  menu-item">

										<?php if ( is_user_logged_in() ) { ?>
										 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>"><i class="icon  icon--small  icon--before"><?php echo mttr_get_icon( 'user.svg' ); ?></i><?php _e('My Account','woothemes'); ?></a>
										<?php } 
										else { ?>
										 	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Sign Up','woothemes'); ?>"><i class="icon  icon--small  icon--before"><?php echo mttr_get_icon( 'user.svg' ); ?></i><?php _e('Login / Register','woothemes'); ?></a>
										<?php } ?>

									</li><li class="layout__item  menu-item">

										<?php global $woocommerce; ?>

										<a href="<?php echo esc_url( $woocommerce->cart->get_checkout_url() ); ?>">

											<div>

												<i class="icon  icon--small  icon--before"><?php echo mttr_get_icon( 'shopping-cart.svg' ); ?></i>View Cart

											</div>

										</a>

									</li>

								</ul>

							</nav>

						</li>

					</ul><!--/.layout -->

				</li><!-- /.masthead__secondary --><li class="layout__item  masthead__tertiary">

					<a class="btn  btn--feature  u-flush" href="tel:<?php echo do_shortcode( '[mttr_phone_number tel="true"]' ); ?>">Sales & Technical Hotline <span><?php echo do_shortcode( '[mttr_phone_number]' ); ?></span></a>

				</li>

			</ul><!--/.layout -->

		</div><!-- /.wrap -->

	</div><!-- /.masthead__body -->

</header><!-- .masthead -->
