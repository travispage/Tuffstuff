<div class="top-bar  u-hard">

	<div class="wrap">

		<ul class="layout  layout--flush  layout--middle">

			<li class="layout__item  top-bar__primary">

				<div class="navigation-container">

				 	<nav class="flush--bottom" role="navigation">

						<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'menu_id' => 'secondary-menu', 'menu_class' => 'navigation  navigation--secondary  u-flush--bottom  list  list--inline', 'after' => '<span class="menu-item-trigger  js-toggle--sub-menu"></span>' ) ); ?>

					</nav>

				</div>

			</li><!--

			--><li class="layout__item  top-bar__secondary  u-text--right">

				<ul class="layout  list  list--inline  top-bar__contact-details">

					<li class="layout__item  top-bar__email">

						<i class="icon  icon--small  icon--before"><?php echo mttr_get_icon( 'user.svg' ); ?></i>
						<a href="http://tuffstuffaust.webninjashops.com/login" target="_blank">Dealer Login</a>

					</li><!-- 

					--><li class="layout__item  list__item  top-bar__cart  u-text--uppercase">

						<?php global $woocommerce; ?>

						<a href="<?php echo esc_url( $woocommerce->cart->get_checkout_url() ); ?>">

							<div>

								View Cart<i class="icon  icon--small  icon--after"><?php echo mttr_get_icon( 'shopping-cart.svg' ); ?></i>

							</div>

						</a>

					</li>

				</ul>

			</li>

		</ul>

	</div>

</div>

