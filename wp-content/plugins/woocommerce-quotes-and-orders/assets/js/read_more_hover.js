// JavaScript Document
jQuery(document).ready(function() {
	jQuery(document).on("mouseenter", ".products .product", function(){
		jQuery(this).addClass("product_hover");
		var bt_position = jQuery(this).find(".wc_ei_read_more_hover_container").attr('position');
		var read_more_margin = 0;
		var thumbnail = jQuery(this).find('.thumbnail').outerHeight();
		if(thumbnail <= 0){ thumbnail = jQuery(this).find('img').outerHeight(); }
		if( bt_position == 'center' ){
			read_more_margin = ( thumbnail- jQuery(this).find(".wc_ei_read_more_hover_content").height())/ 2;
			jQuery(this).find(".wc_ei_read_more_hover_container").css('top',read_more_margin+"px");
		}
		if( bt_position == 'bottom' ){
			read_more_margin = ( thumbnail - jQuery(this).find(".wc_ei_read_more_hover_content").height() );
			jQuery(this).find(".wc_ei_read_more_hover_container").css('top',read_more_margin+"px");
		}
		if( bt_position == 'top' ){
			jQuery(this).find(".wc_ei_read_more_hover_container").css('top',"0px");
		}
	});
	jQuery(document).on("mouseleave", ".products .product", function(){
		jQuery(this).removeClass("product_hover");	
	});
	
	jQuery(document).on("mouseenter", ".shop-product", function(){
		jQuery(this).addClass("product_hover");
		var bt_position = jQuery(this).find(".wc_ei_read_more_hover_container").attr('position');
		var read_more_margin = 0;
		var thumbnail = jQuery(this).find('.thumbnail').outerHeight();
		if(thumbnail <= 0){ thumbnail = jQuery(this).find('img').outerHeight(); }
		if( bt_position == 'center' ){
			read_more_margin = ( thumbnail- jQuery(this).find(".wc_ei_read_more_hover_content").height())/ 2;
			jQuery(this).find(".wc_ei_read_more_hover_container").css('top',read_more_margin+"px");
		}
		if( bt_position == 'bottom' ){
			read_more_margin = ( thumbnail - jQuery(this).find(".wc_ei_read_more_hover_content").height() );
			jQuery(this).find(".wc_ei_read_more_hover_container").css('top',read_more_margin+"px");
		}
		if( bt_position == 'top' ){
			jQuery(this).find(".wc_ei_read_more_hover_container").css('top',"0px");
		}
	});
	jQuery(document).on("mouseleave", ".shop-product", function(){
		jQuery(this).removeClass("product_hover");	
	});
	
	jQuery(document).on("click", ".wc_ei_read_more_hover_button", function() {
		var product_link = jQuery(this).attr('product-link');
		window.open( product_link, '_parent' );
		
		return false;
	});
	
});	
