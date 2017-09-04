<style>
.quote_mode_mark {
	padding:0; 
	margin:0;
	border:none;
	background:none;
	z-index:10001;
	cursor:help;
}
.ui-tooltip {
	padding: 8px;
	position: absolute;
	z-index: 9999;
	max-width: 300px;
	-webkit-box-shadow: 0 0 5px #aaa;
	box-shadow: 0 0 5px #aaa;
}
body .ui-tooltip {
	border-width: 2px;
}
.wc_email_inquiry_expand_text {
	min-width:20px;
	display:inline-block;	
}
#page_3rd_contact_form_container {
	margin:0;
	display:block;		
}
.wc_email_inquiry_custom_form_container {
	position:relative !important;	
}
<?php
global $wc_ei_admin_interface, $wc_ei_fonts_face;

// Email Inquiry Button Style
global $wc_email_inquiry_customize_email_button;
extract($wc_email_inquiry_customize_email_button);
?>
@charset "UTF-8";
/* CSS Document */

/* Email Inquiry Button Style */
.wc_email_inquiry_button_container { 
	margin-bottom: <?php echo $inquiry_button_margin_bottom; ?>px !important;
	margin-top: <?php echo $inquiry_button_margin_top; ?>px !important;
	margin-left: <?php echo $inquiry_button_margin_left; ?>px !important;
	margin-right: <?php echo $inquiry_button_margin_right; ?>px !important;
}
body .wc_email_inquiry_button_container .wc_email_inquiry_button, body .wc_email_inquiry_button_container .wc_email_inquiry_popup_button, body .wc_email_inquiry_button_container .wc_email_inquiry_button_3rd {
	position: relative !important;
	cursor:pointer;
	display: inline-block !important;
	line-height: 1 !important;
}
body .wc_email_inquiry_button_container .wc_email_inquiry_email_button {
	padding: <?php echo $inquiry_button_padding_tb; ?>px <?php echo $inquiry_button_padding_lr; ?>px !important;
	margin:0;
	
	/*Background*/
	background-color: <?php echo $inquiry_button_bg_colour; ?> !important;
	background: -webkit-gradient(
					linear,
					left top,
					left bottom,
					color-stop(.2, <?php echo $inquiry_button_bg_colour_from; ?>),
					color-stop(1, <?php echo $inquiry_button_bg_colour_to; ?>)
				) !important;;
	background: -moz-linear-gradient(
					center top,
					<?php echo $inquiry_button_bg_colour_from; ?> 20%,
					<?php echo $inquiry_button_bg_colour_to; ?> 100%
				) !important;;
	
		
	/*Border*/
	<?php echo $wc_ei_admin_interface->generate_border_css( $inquiry_button_border ); ?>
	
	/* Shadow */
	<?php echo $wc_ei_admin_interface->generate_shadow_css( $inquiry_button_shadow ); ?>
	
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $inquiry_button_font ); ?>
	
	text-align: center !important;
	text-shadow: 0 -1px 0 hsla(0,0%,0%,.3);
	text-decoration: none !important;
}

body .wc_email_inquiry_button_container .wc_email_inquiry_hyperlink_text {
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $inquiry_hyperlink_font ); ?>
}

body .wc_email_inquiry_button_container .wc_email_inquiry_hyperlink_text:hover {
	color: <?php echo $inquiry_hyperlink_hover_color ; ?> !important;	
}

<?php
// Email Inquiry Form Button Style
global $wc_email_inquiry_global_settings;
extract($wc_email_inquiry_global_settings);
?>

/* Email Inquiry Form Style */
.wc_email_inquiry_form * {
	box-sizing:content-box !important;
	-moz-box-sizing:content-box !important;
	-webkit-box-sizing:content-box !important;	
}
.email_inquiry_cb #cboxLoadedContent, .wc_email_inquiry_form, #fancybox-content > div {
	background-color: <?php echo $inquiry_form_bg_colour; ?> !important;	
}
body .wc_email_inquiry_form, .wc_email_inquiry_form, .wc_email_inquiry_form .wc_email_inquiry_field, body .wc_email_inquiry_field {
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $inquiry_contact_popup_text ); ?>
}
.wc_email_inquiry_custom_form_product_heading {
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $inquiry_form_product_name_font ); ?>
	
	clear:none !important;
	margin-top:5px !important;
	padding-top:0 !important;	
}
a.wc_email_inquiry_custom_form_product_url {
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $inquiry_form_product_url_font ); ?>
}
.wc_email_inquiry_subject {
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $inquiry_form_subject_font ); ?>
}

.wc_email_inquiry_field input, .wc_email_inquiry_field textarea{
	/*Border*/
	<?php echo $wc_ei_admin_interface->generate_border_css( $inquiry_input_border ); ?>
	
	/*Background*/
	background-color: <?php echo $inquiry_input_bg_colour; ?> !important;
	
	/* Font */
	color: <?php echo $inquiry_input_font_colour; ?> !important;
}

/* Email Inquiry Form Button Style */
body .wc_email_inquiry_form_button, .wc_email_inquiry_form_button {
	position: relative !important;
	cursor:pointer;
	display: inline-block !important;
}
body .wc_email_inquiry_form_button, .wc_email_inquiry_form_button {
	padding: 7px 10px !important;
	margin:0;
	
	/*Background*/
	background-color: <?php echo $inquiry_contact_button_bg_colour; ?> !important;
	background: -webkit-gradient(
					linear,
					left top,
					left bottom,
					color-stop(.2, <?php echo $inquiry_contact_button_bg_colour_from; ?>),
					color-stop(1, <?php echo $inquiry_contact_button_bg_colour_to; ?>)
				) !important;;
	background: -moz-linear-gradient(
					center top,
					<?php echo $inquiry_contact_button_bg_colour_from; ?> 20%,
					<?php echo $inquiry_contact_button_bg_colour_to; ?> 100%
				) !important;;
	
	/*Border*/
	<?php echo $wc_ei_admin_interface->generate_border_css( $inquiry_contact_button_border ); ?>
	
	/* Shadow */
	<?php echo $wc_ei_admin_interface->generate_shadow_css( $inquiry_contact_button_shadow ); ?>
	
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $inquiry_contact_button_font ); ?>
		
	text-align: center !important;
	text-shadow: 0 -1px 0 hsla(0,0%,0%,.3);
	text-decoration: none !important;
}

/* Contact Form Heading */
h1.wc_email_inquiry_result_heading {
	<?php echo $wc_ei_fonts_face->generate_font_css( $inquiry_contact_heading_font ); ?>
}

/* Custom Form Heading */
.wc_email_inquiry_custom_form_heading {
	<?php echo $wc_ei_fonts_face->generate_font_css( $custom_contact_form_heading_font ); ?>
}

<?php
// Colorbox Style
global $wc_email_inquiry_colorbox_popup_settings;
extract($wc_email_inquiry_colorbox_popup_settings);
?>
/* Colorbox Background */
#cboxOverlay.email_inquiry_cb{ 
	background:<?php echo $colorbox_overlay_color;?> !important;
}

<?php
// Read More Hover Button Style
global $wc_email_inquiry_read_more_settings;
extract($wc_email_inquiry_read_more_settings);
?>
/* Read More Hover Button */
body .wc_ei_read_more_hover_container, .wc_ei_read_more_hover_container {
	display:none;
	text-align:<?php echo $hover_bt_alink;?>;
	clear:both;
	width:100%;
}
body .product_hover .wc_ei_read_more_hover_container, .product_hover .wc_ei_read_more_hover_container {
	display: block;
	left: 0;
	position: absolute;
	z-index:999;
}
body .wc_ei_read_more_hover_container .wc_ei_read_more_hover_content, .wc_ei_read_more_hover_container .wc_ei_read_more_hover_content {
	width: 100%;
	text-align:center;
	clear:both;
	height:auto;
	max-height:100%;
	position:relative;
	display:block;
}
body .wc_ei_read_more_hover_container .wc_ei_read_more_hover_button, .wc_ei_read_more_hover_container .wc_ei_read_more_hover_button {
	display: inline-block !important;
	vertical-align:middle;
	white-space: nowrap;
	text-decoration:none !important;
	position: relative;
	cursor: pointer;
	z-index:1;
	
	text-align:<?php echo $hover_bt_alink;?> !important;
	padding: <?php echo $hover_bt_padding_tb; ?>px <?php echo $hover_bt_padding_lr; ?>px !important;
	
	/*Background*/
	background-color: <?php echo $hover_bt_bg; ?> !important;
	background: -webkit-gradient(
					linear,
					left top,
					left bottom,
					color-stop(.2, <?php echo $hover_bt_bg_from; ?>),
					color-stop(1, <?php echo $hover_bt_bg_to; ?>)
				) !important;;
	background: -moz-linear-gradient(
					center top,
					<?php echo $hover_bt_bg_from; ?> 20%,
					<?php echo $hover_bt_bg_to; ?> 100%
				) !important;;
	
	/*Border*/
	<?php echo $wc_ei_admin_interface->generate_border_css( $hover_bt_border ); ?>
	
	/* Shadow */
	<?php echo $wc_ei_admin_interface->generate_shadow_css( $hover_bt_shadow ); ?>
	
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $hover_bt_font ); ?>
	
	<?php if ($hover_bt_transparent == 100 ) { ?>
	opacity:1 !important;
	<?php } else { ?>
	opacity:0.<?php echo round( $hover_bt_transparent / 10 );?> !important;;
	<?php } ?>
	filter:alpha(opacity=<?php echo $hover_bt_transparent;?>) !important;;
}

/* Read More Button Under Image - Button Type */
body .wc_ei_read_more_button_container {
	display:inline-block;
	margin-bottom: <?php echo $under_image_bt_margin_bottom; ?>px !important;
	margin-top: <?php echo $under_image_bt_margin_top; ?>px !important;
	margin-left: <?php echo $under_image_bt_margin_left; ?>px !important;
	margin-right: <?php echo $under_image_bt_margin_right; ?>px !important;	
}
body .wc_ei_read_more_button_container .wc_ei_read_more_button_type, .wc_ei_read_more_button_container .wc_ei_read_more_button_type {
	position: relative !important;
	cursor:pointer;
	display: inline-block !important;
	
	padding: <?php echo $under_image_bt_padding_tb; ?>px <?php echo $under_image_bt_padding_lr; ?>px !important;
	margin:0;
	
	/*Background*/
	background-color: <?php echo $under_image_bt_bg; ?> !important;
	background: -webkit-gradient(
					linear,
					left top,
					left bottom,
					color-stop(.2, <?php echo $under_image_bt_bg_from; ?>),
					color-stop(1, <?php echo $under_image_bt_bg_to; ?>)
				) !important;;
	background: -moz-linear-gradient(
					center top,
					<?php echo $under_image_bt_bg_from; ?> 20%,
					<?php echo $under_image_bt_bg_to; ?> 100%
				) !important;;
	
		
	/*Border*/
	<?php echo $wc_ei_admin_interface->generate_border_css( $under_image_bt_border ); ?>
	
	/* Shadow */
	<?php echo $wc_ei_admin_interface->generate_shadow_css( $under_image_bt_shadow ); ?>
	
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $under_image_bt_font ); ?>
	
	text-align: center !important;
	text-shadow: 0 -1px 0 hsla(0,0%,0%,.3);
	text-decoration: none !important;
}
body .wc_ei_read_more_button_container .wc_ei_read_more_link_type, .wc_ei_read_more_button_container .wc_ei_read_more_link_type {
	text-decoration:underline;
	cursor:pointer;	
	/* Font */
	<?php echo $wc_ei_fonts_face->generate_font_css( $under_image_link_font ); ?>
}

body .wc_ei_read_more_button_container .wc_ei_read_more_link_type:hover, .wc_ei_read_more_button_container .wc_ei_read_more_link_type:hover {
	color: <?php echo $under_image_link_font_hover_color ; ?> !important;	
}
</style>
