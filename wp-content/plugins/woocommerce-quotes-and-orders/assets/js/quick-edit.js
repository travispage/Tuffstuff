jQuery(document).ready(function(){
    jQuery('#the-list').on('click', '.editinline', function(){

		inlineEditPost.revert();

		var post_id = jQuery(this).closest('tr').attr('id');

		post_id = post_id.replace("post-", "");

		var $wc_ei_contact_form_shortcode_inline_data = jQuery('#wc_ei_contact_form_shortcode_inline_' + post_id );

		var wc_ei_contact_form_shortcode 				= $wc_ei_contact_form_shortcode_inline_data.find('.wc_ei_contact_form_shortcode').text();

		jQuery('#wc-ei-fields-quick input[name="_wc_ei_contact_form_shortcode"]', '.inline-edit-row').val(wc_ei_contact_form_shortcode);

    });

    jQuery('#wpbody').on('click', '#doaction, #doaction2', function(){

		jQuery('select, input.text', '.inline-edit-row').val('');
		jQuery('select option', '.inline-edit-row').removeAttr('checked');
		jQuery('#wc-ei-fields-bulk .wc-ei-contact-form-shortcode-value').hide();

	});

	 jQuery('#wpbody').on('change', '#wc-ei-fields-bulk .change_to', function(){

    	if (jQuery(this).val() > 0) {
    		jQuery(this).closest('div').find('.wc-ei-contact-form-shortcode-value').show();
    	} else {
    		jQuery(this).closest('div').find('.wc-ei-contact-form-shortcode-value').hide();
    	}

    });
});