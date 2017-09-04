<?php 

$modifiers = mttr_get_template_var( 'modifiers' );

// Add spaces before modifiers
if ( $modifiers ) {

	$modifiers = '  ' . $modifiers;

}

$al_product_cat = get_terms('product_brand');
?>
<div class="search-bar<?php echo esc_html( $modifiers ); ?>  band">

	<div class="wrap">

		<ul class="listing  layout  layout--middle">

			<li class="listing__item  layout__item  search-bar__primary">

				<div class="u-negate-btm-margin">

					<h2 class="display-heading">TuffSearch</h2>
					<p class="u-text--tiny"><a href="<?php echo get_the_permalink( 38 ); ?>">Can’t find your part?</a> <strong>Call us <a class="ga-tracking--phone" href="tel:<?php echo do_shortcode( '[mttr_phone_number tel=true]' ); ?>"><?php echo do_shortcode( '[mttr_phone_number]' ); ?></a></strong></p>

				</div>

			</li><li class="listing__item  layout__item  search-bar__secondary">

				<!-- <form data-shop-page="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" class="search-bar__form   part-search" action="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" method="get"> -->

					<ul class="layout  layout--bottom  listing">

						<li class="layout__item  search-bar__fields  listing__item">

							<ul class="layout  listing  listing--small">

								<li class="listing__item  layout__item  g-one-third@lap">

									<form data-shop-page="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" class="search-bar__form   part-search" action="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" method="get">
										<a href="#" class="maker"><span class="menu-item-label">TUFFTRAC</span> Rubber Tracks</a>
											<label>Parts by make</label>
											<!--<select onchange="this.form.submit()" name="product_brand">
												<option value="">--select--</option>
												<?php
												//foreach( $al_product_cat as $brand)
													//echo '<option value="'.$brand->slug.'">'.$brand->name.'</option>';
												?>
											</select>-->
											<select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
												<option value="">Select</option>
												<option value="http://testing.socialmedia-solutions.com/tuffstuff/rubber-tracks/kubota/">Kubota</option>
												<option value=" http://testing.socialmedia-solutions.com/tuffstuff/rubber-tracks/caterpillar/">Caterpillar</option>
											</select>
									</form>
									<!-- <label>Make</label>
									<?php echo facetwp_display( 'facet', 'brands' ); ?> -->
								
								</li><li class="listing__item  layout__item  g-one-third@lap">

									<form data-shop-page="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" class="search-bar__form   part-search" action="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" method="get">
										<input type="hidden" name="product_cat" value="rubber-pads">
										<a href="#" class="maker"><span class="menu-item-label">TUFFPAD</span> Rubber Pads</a>
											<label>Parts by make</label>
											<!--<select onchange="this.form.submit()" name="product_brand">
												<option value="">--select--</option>
												<?php
												//foreach( $al_product_cat as $brand)
													//echo '<option value="'.$brand->slug.'">'.$brand->name.'</option>';
												?>
											</select>-->
											<select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
												<option value="">Select</option>
												<option value="http://testing.socialmedia-solutions.com/tuffstuff/rubber-pads/kubota/">Kubota</option>
												<option value=" http://testing.socialmedia-solutions.com/tuffstuff/rubber-pads/caterpillar/">Caterpillar</option>
											</select>
									</form>

									<!--<label>Model</label>
									<?php echo facetwp_display( 'facet', 'model' ); ?>-->

								</li>
								<li class="listing__item  layout__item  g-one-third@lap">

									<form data-shop-page="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" class="search-bar__form   part-search" action="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" method="get">
										<input type="hidden" name="product_cat" value="undercarriage">
										<a href="#" class="maker"><span class="menu-item-label">TUFFPART</span> Undercarriage</a>
											<label>Parts by make</label>
											<!--<select onchange="this.form.submit()" name="product_brand">
												<option value="">--select--</option>
												<?php
												//foreach( $al_product_cat as $brand)
													//echo '<option value="'.$brand->slug.'">'.$brand->name.'</option>';
												?>
											</select>-->
											<select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
												<option value="">Select</option>
												<option value="http://testing.socialmedia-solutions.com/tuffstuff/undercarriage/kubota/">Kubota</option>
												<option value="http://testing.socialmedia-solutions.com/tuffstuff/undercarriage/hyundai/">Hyundai</option>
											</select>
									</form>

									<!--<label>Type</label>
									<?php echo facetwp_display( 'facet', 'type' ); ?>-->

								</li>
								<li class="listing__item  layout__item  g-one-third@lap">

									<form data-shop-page="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" class="search-bar__form   part-search" action="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" method="get">
										<input type="hidden" name="product_cat" value="undercarriage">
										<a href="#" class="maker"><span class="menu-item-label">TUFFAGRI</span> Ag Tracks</a>
											<label>Parts by make</label>
											<!--<select onchange="this.form.submit()" name="product_brand">
												<option value="">--select--</option>
												<?php
												//foreach( $al_product_cat as $brand)
													//echo '<option value="'.$brand->slug.'">'.$brand->name.'</option>';
												?>
											</select>-->
											<select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
												<option value="">Select</option>
												<option value="http://testing.socialmedia-solutions.com/tuffstuff/ag-tracks/kubota/">Kubota</option>
												<option value="http://testing.socialmedia-solutions.com/tuffstuff/ag-tracks/cat/">Caterpillar</option>
											</select>
									</form>

										<a href="http://testing.socialmedia-solutions.com/tuffstuff/product-category/tyres/" target="_blank" class="maker" style="color: #ffffff !important;
    background-color: #000000;
    /* padding: 10px; */
    margin-top: 3px;
    display: inline-block;
    padding: 8px 12px;
text-decoration: none; position: relative; bottom: 150px; left: 150px;"><span class="menu-item-label">TUFFTYRE</span> Tyres</a>
										

								
								</li>
								

									<!--<label>Type</label>
									<?php echo facetwp_display( 'facet', 'type' ); ?>-->

								<!--<li class="listing__item  layout__item  g-one-third@lap">

									<form data-shop-page="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" class="search-bar__form   part-search" action="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>" method="get">
										<input type="hidden" name="product_cat" value="undercarriage">
											<label>Rubber Pads</label>
											<select onchange="this.form.submit()" name="product_brand">
												<option value="">--select--</option>
												<?php
												foreach( $al_product_cat as $brand)
													echo '<option value="'.$brand->slug.'">'.$brand->name.'</option>';
												?>
											</select>
									</form>

									<!--<label>Type</label>
									<?php echo facetwp_display( 'facet', 'type' ); ?>-->

								<!--</li>-->
							</ul>

						</li>

					</ul>

					<!-- <div style="display: none;"><?php echo facetwp_display( 'template', 'default' ); ?></div> -->

				<!-- </form> -->

			</li>

		</ul>

	</div><!-- /.wrap -->

</div><!-- /.search-bar -->