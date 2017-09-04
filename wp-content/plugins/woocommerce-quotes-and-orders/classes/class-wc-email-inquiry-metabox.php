<?php
/**
 * WPEC PCF MetaBox
 *
 * Table Of Contents
 *
 * add_meta_boxes()
 * the_meta_forms()
 * save_meta_boxes()
 */
class WC_Email_Inquiry_MetaBox
{
	
	public static function add_meta_boxes(){
		global $post;
		$pagename = 'product';
		add_meta_box( 'wc_email_inquiry_meta', __('Email & Cart', 'wc_email_inquiry'), array('WC_Email_Inquiry_MetaBox', 'the_meta_forms'), $pagename, 'normal', 'high' );
	}
	
	public static function the_meta_forms() {
		global $post;
		global $wc_email_inquiry_rules_roles_settings;
		global $wc_email_inquiry_global_settings, $wc_email_inquiry_read_more_settings;
		global $wc_email_inquiry_contact_form_settings;
		global $wc_email_inquiry_customize_email_button;
		add_action('admin_footer', array('WC_Email_Inquiry_Hook_Filter', 'admin_footer_scripts'), 10);
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->get_names();
		$roles_hide_cart = $roles;
		unset( $roles_hide_cart['manual_quote'] );
		unset( $roles_hide_cart['auto_quote'] );
		$roles_hide_price = $roles_hide_cart;
		if ( is_array( $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] ) && count( $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] ) > 0 ) {
			foreach ( $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] as $role ) {
				unset( $roles_hide_cart[$role] );
			}
		}
		if ( is_array( $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] ) && count( $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] ) > 0 ) {
			foreach ( $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] as $role ) {
				unset( $roles_hide_cart[$role] );
			}
		}
		if ( is_array( $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] ) && count( $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] ) > 0 ) {
			foreach ( $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] as $role ) {
				unset( $roles_hide_cart[$role] );
				unset( $roles_hide_price[$role] );
			}
		}
		
		$wc_ei_cart_price_custom       = get_post_meta( $post->ID, '_wc_ei_cart_price_custom', true);
		$wc_ei_settings_custom         = get_post_meta( $post->ID, '_wc_ei_settings_custom', true);
		$wc_ei_button_custom           = get_post_meta( $post->ID, '_wc_ei_button_custom', true);
		$wc_ei_read_more_button_custom = get_post_meta( $post->ID, '_wc_ei_read_more_button_custom', true);

		if (is_array($wc_ei_cart_price_custom)) extract($wc_ei_cart_price_custom);
		if (is_array($wc_ei_button_custom)) extract($wc_ei_button_custom);
		if (is_array($wc_ei_read_more_button_custom)) extract($wc_ei_read_more_button_custom);
		if (is_array($wc_ei_settings_custom)) extract($wc_ei_settings_custom);
		
		if (!isset($wc_email_inquiry_hide_addcartbt)) $wc_email_inquiry_hide_addcartbt = $wc_email_inquiry_rules_roles_settings['hide_addcartbt'];
		else $wc_email_inquiry_hide_addcartbt = esc_attr($wc_email_inquiry_hide_addcartbt);
		
		if (!isset($wc_email_inquiry_hide_addcartbt_after_login)) $wc_email_inquiry_hide_addcartbt_after_login = $wc_email_inquiry_rules_roles_settings['hide_addcartbt_after_login'];
		else $wc_email_inquiry_hide_addcartbt_after_login = esc_attr($wc_email_inquiry_hide_addcartbt_after_login);
		
		if (!isset($wc_email_inquiry_show_button)) $wc_email_inquiry_show_button = $wc_email_inquiry_global_settings['show_button'];
		else $wc_email_inquiry_show_button = esc_attr($wc_email_inquiry_show_button);
		
		if (!isset($wc_email_inquiry_show_button_after_login)) $wc_email_inquiry_show_button_after_login = $wc_email_inquiry_global_settings['show_button_after_login'];
		else $wc_email_inquiry_show_button_after_login = esc_attr($wc_email_inquiry_show_button_after_login);
		
		if (!isset($show_read_more_button_before_login)) $show_read_more_button_before_login = $wc_email_inquiry_read_more_settings['show_read_more_button_before_login'];
		else $show_read_more_button_before_login = esc_attr($show_read_more_button_before_login);
		
		if (!isset($show_read_more_button_after_login)) $show_read_more_button_after_login = $wc_email_inquiry_read_more_settings['show_read_more_button_after_login'];
		else $show_read_more_button_after_login = esc_attr($show_read_more_button_after_login);
		
		if (!isset($wc_email_inquiry_hide_price)) $wc_email_inquiry_hide_price = $wc_email_inquiry_rules_roles_settings['hide_price'];
		else $wc_email_inquiry_hide_price = esc_attr($wc_email_inquiry_hide_price);
		
		if (!isset($wc_email_inquiry_hide_price_after_login)) $wc_email_inquiry_hide_price_after_login = $wc_email_inquiry_rules_roles_settings['hide_price_after_login'];
		else $wc_email_inquiry_hide_price_after_login = esc_attr($wc_email_inquiry_hide_price_after_login);
		
		if ( ! isset( $role_apply_hide_cart ) )  {
			$role_apply_hide_cart = (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_cart'];
		} else {
			$role_apply_hide_cart = array_diff ( $role_apply_hide_cart, (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
			$role_apply_hide_cart = array_diff ( $role_apply_hide_cart, (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] );
			$role_apply_hide_cart = array_diff ( $role_apply_hide_cart, (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] );
		}
		if ( ! isset( $role_apply_hide_price ) ) {
			$role_apply_hide_price = (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_price'];
		} else {
			$role_apply_hide_price = array_diff ( $role_apply_hide_price, (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] );
		}
		
		if (!isset($role_apply_show_inquiry_button)) $role_apply_show_inquiry_button = (array) $wc_email_inquiry_global_settings['role_apply_show_inquiry_button'];
		
		if (!isset($role_apply_show_read_more)) $role_apply_show_read_more = (array) $wc_email_inquiry_read_more_settings['role_apply_show_read_more'];
				
		if (!isset($wc_email_inquiry_email_to)) $wc_email_inquiry_email_to = $wc_email_inquiry_contact_form_settings['inquiry_email_to'];
		else $wc_email_inquiry_email_to = esc_attr($wc_email_inquiry_email_to);
		
		if (!isset($wc_email_inquiry_email_cc)) $wc_email_inquiry_email_cc = $wc_email_inquiry_contact_form_settings['inquiry_email_cc'];
		else $wc_email_inquiry_email_cc = esc_attr($wc_email_inquiry_email_cc);
		
		if (!isset($wc_email_inquiry_button_type)) $wc_email_inquiry_button_type = $wc_email_inquiry_customize_email_button['inquiry_button_type'];
		else $wc_email_inquiry_button_type = esc_attr($wc_email_inquiry_button_type);
		
		if (!isset($wc_email_inquiry_text_before)) $wc_email_inquiry_text_before = $wc_email_inquiry_customize_email_button['inquiry_text_before'];
		else $wc_email_inquiry_text_before = esc_attr($wc_email_inquiry_text_before);
		
		if (!isset($wc_email_inquiry_hyperlink_text)) $wc_email_inquiry_hyperlink_text = $wc_email_inquiry_customize_email_button['inquiry_hyperlink_text'];
		else $wc_email_inquiry_hyperlink_text = esc_attr($wc_email_inquiry_hyperlink_text);
		
		if (!isset($wc_email_inquiry_trailing_text)) $wc_email_inquiry_trailing_text = $wc_email_inquiry_customize_email_button['inquiry_trailing_text'];
		else $wc_email_inquiry_trailing_text = esc_attr($wc_email_inquiry_trailing_text);
		
		if (!isset($wc_email_inquiry_button_title)) $wc_email_inquiry_button_title = $wc_email_inquiry_customize_email_button['inquiry_button_title'];
		else $wc_email_inquiry_button_title = esc_attr($wc_email_inquiry_button_title);
		
		if (!isset($contact_form_shortcode)) $contact_form_shortcode = esc_attr( $wc_email_inquiry_global_settings['contact_form_shortcode'] );
		else $contact_form_shortcode = esc_attr($contact_form_shortcode);
		
		
		if (!isset($wc_email_inquiry_single_only)) $wc_email_inquiry_single_only = $wc_email_inquiry_global_settings['inquiry_single_only'];
		else $wc_email_inquiry_single_only = esc_attr($wc_email_inquiry_single_only);
		
		if ( !isset( $read_more_text ) ) $read_more_text = '';
		else $read_more_text = esc_attr($read_more_text);
		
		?>
        <style>
		.wc_ei_rule_after_login_container {
			margin-top:10px;
		}
		.wc_ei_tab_bar .wp-tab-bar li {
			padding:5px 8px !important;	
		}
		.wc_ei_tab_bar .wp-tab-bar li.wp-tab-active {
		}
		.wc_ei_tab_bar .wp-tab-panel {
			border-radius: 0 3px 3px 3px !important;
			-moz-border-radius: 0 3px 3px 3px !important;
			-webkit-border-radius: 0 3px 3px 3px !important;
			max-height: inherit !important;
			overflow:visible !important;
		}
		</style>
        <script>
		(function($) {
		$(document).ready(function() {
			$(document).on( "change", "input.wc_ei_rule_after_login", function() {
				if ( $(this).prop("checked") ) {
					$(this).parent('label').siblings(".wc_ei_rule_after_login_container").slideDown();
				} else {
					$(this).parent('label').siblings(".wc_ei_rule_after_login_container").slideUp();
				}
			});
			
			/* Apply Sub tab selected script */
			$('div.wc_ei_tab_bar ul.wp-tab-bar li a').click(function(){
				var clicked = $(this);
				var section = clicked.closest('.wc_ei_tab_bar');
				var target  = clicked.attr('href');
			
				section.find('li').removeClass('wp-tab-active');
			
				if ( section.find('.wp-tab-panel:visible').size() > 0 ) {
					section.find('.wp-tab-panel:visible').fadeOut( 100, function() {
						section.find( target ).fadeIn('fast');
					});
				} else {
					section.find( target ).fadeIn('fast');
				}
			
				clicked.parent('li').addClass('wp-tab-active');
			
				return false;
			});
		});
		})(jQuery);
		</script>
        <table cellspacing="0" class="form-table">
			<tbody>
            	<tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_reset_product_options"><?php _e('Reset Product Options','wc_email_inquiry'); ?></label></th>
                    <td class="forminp">
                        <fieldset><label><input type="checkbox" value="1" id="wc_email_inquiry_reset_product_options" name="wc_email_inquiry_reset_product_options" /> <?php _e('Check to reset this product setting to the Global Settings', 'wc_email_inquiry'); ?></label></fieldset>
                    </td>
                </tr>
			</tbody>
        </table>
        <div class="wc_ei_tab_bar a3rev_panel_container" style="visibility: visible; height: auto; overflow: inherit;">
        <ul class="wp-tab-bar">
			<li class="wp-tab-active"><a href="#wc_ei_cart_price"><?php echo __( 'Cart & Price', 'wc_email_inquiry' ); ?></a></li>
			<li class="hide-if-no-js"><a href="#wc_ei_email_inquiry"><?php echo __( 'Email Inquiry', 'wc_email_inquiry' ); ?></a></li>
            <li class="hide-if-no-js"><a href="#wc_ei_read_more"><?php echo __( 'Read More', 'wc_email_inquiry' ); ?></a></li>
		</ul>
        <div id="wc_ei_cart_price" class="wp-tab-panel">
        <table cellspacing="0" class="form-table">
			<tbody>
            	<tr valign="top">
                    <th class="titledesc" scope="rpw" colspan="2"><strong><?php _e( "Product Page Rule: Hide 'Add to Cart'", 'wc_email_inquiry' ); ?></strong></th>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_hide_addcartbt"><?php _e("View before log in",'wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><label><input type="checkbox" name="_wc_ei_cart_price_custom[wc_email_inquiry_hide_addcartbt]" id="wc_email_inquiry_hide_addcartbt" value="yes" <?php if ( $wc_email_inquiry_rules_roles_settings['manual_quote_rule'] == 'yes' || $wc_email_inquiry_rules_roles_settings['auto_quote_rule'] == 'yes' ||  $wc_email_inquiry_rules_roles_settings['add_to_order_rule'] == 'yes' ) { echo 'disabled="disabled"'; } else { checked( $wc_email_inquiry_hide_addcartbt, 'yes' ); } ?> /> <?php echo __( 'Check to Hide add to cart for this product', 'wc_email_inquiry' ); ?></label>
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_hide_addcartbt_after_login"><?php _e('View after login','wc_email_inquiry'); ?></label></th>
                    <td class="forminp">
                    	<label><input class="wc_ei_rule_after_login" type="checkbox" name="_wc_ei_cart_price_custom[wc_email_inquiry_hide_addcartbt_after_login]" id="wc_email_inquiry_hide_addcartbt_after_login" value="yes" <?php checked( $wc_email_inquiry_hide_addcartbt_after_login, 'yes' ); ?> /> <?php echo __( 'Check and select user roles cannot see add to cart for this product when they log in', 'wc_email_inquiry' ); ?></label>
                        <div class="wc_ei_rule_after_login_container" style=" <?php if ( $wc_email_inquiry_hide_addcartbt_after_login != 'yes' ) echo 'display: none;'; ?>">
                    	<select multiple="multiple" id="role_apply_hide_cart" name="_wc_ei_cart_price_custom[role_apply_hide_cart][]" data-placeholder="<?php _e( 'Choose Roles', 'wc_email_inquiry' ); ?>" style="display:none; width:300px;" class="chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>">
						<?php foreach ( $roles_hide_cart as $key => $val ) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( in_array($key, (array) $role_apply_hide_cart), true ); ?>><?php echo $val ?></option>
                        <?php } ?>
                        </select>
                        </div>
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw" colspan="2"><strong><?php _e( "Product Page Rule: Hide Price", 'wc_email_inquiry' ); ?></strong></th>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_hide_price"><?php _e("View before log in",'wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><label><input type="checkbox" name="_wc_ei_cart_price_custom[wc_email_inquiry_hide_price]" id="wc_email_inquiry_hide_price" value="yes"  <?php if ( $wc_email_inquiry_rules_roles_settings['manual_quote_rule'] == 'yes' || $wc_email_inquiry_rules_roles_settings['auto_quote_rule'] == 'yes' ) { echo 'disabled="disabled"  checked="checked"'; } elseif (  $wc_email_inquiry_rules_roles_settings['add_to_order_rule'] == 'yes' ) { echo 'disabled="disabled"'; } else { checked( $wc_email_inquiry_hide_price, 'yes' ); } ?> /> <?php echo __( 'Check to Hide Price for this product', 'wc_email_inquiry' ); ?></label>
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_hide_price_after_login"><?php _e('View after login','wc_email_inquiry'); ?></label></th>
                    <td class="forminp">
                    	<label><input class="wc_ei_rule_after_login" type="checkbox" name="_wc_ei_cart_price_custom[wc_email_inquiry_hide_price_after_login]" id="wc_email_inquiry_hide_price_after_login" value="yes" <?php checked( $wc_email_inquiry_hide_price_after_login, 'yes' ); ?> /> <?php echo __( 'Check and select user roles that cannot see this product price when they log in', 'wc_email_inquiry' ); ?></label>
                        <div class="wc_ei_rule_after_login_container" style=" <?php if ( $wc_email_inquiry_hide_price_after_login != 'yes' ) echo 'display: none;'; ?>">
                    	<select multiple="multiple" id="role_apply_hide_price" name="_wc_ei_cart_price_custom[role_apply_hide_price][]" data-placeholder="<?php _e( 'Choose Roles', 'wc_email_inquiry' ); ?>" style="display:none; width:300px;" class="chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>">
						<?php foreach ($roles_hide_price as $key => $val) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( in_array($key, (array) $role_apply_hide_price), true ); ?>><?php echo $val ?></option>
                        <?php } ?>
                        </select>
                        </div>
                    </td>
               	</tr>
			</tbody>
        </table>
        </div>
        <div id="wc_ei_email_inquiry" class="wp-tab-panel" style="display:none;">
        <table cellspacing="0" class="form-table">
			<tbody>
                <tr valign="top">
                    <th class="titledesc" scope="rpw" colspan="2"><strong><?php _e( "Email Inquiry Rules and Roles", 'wc_email_inquiry' ); ?></strong></th>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_show_button"><?php _e('View before log in','wc_email_inquiry'); ?></label></th>
                    <td class="forminp">
                    <label><input type="checkbox" name="_wc_ei_settings_custom[wc_email_inquiry_show_button]" id="wc_email_inquiry_show_button" value="yes" <?php checked( $wc_email_inquiry_show_button, 'yes' ); ?> /> <?php echo __( "Check to show Email Inquiry feature for this product", 'wc_email_inquiry' ); ?></label>
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_show_button_after_login"><?php _e('View after login','wc_email_inquiry'); ?></label></th>
                    <td class="forminp">
                    	<label><input class="wc_ei_rule_after_login" type="checkbox" name="_wc_ei_settings_custom[wc_email_inquiry_show_button_after_login]" id="wc_email_inquiry_show_button_after_login" value="yes" <?php checked( $wc_email_inquiry_show_button_after_login, 'yes' ); ?> /> <?php echo __( "Check and set user roles that can see Email Inquiry when they are logged in", 'wc_email_inquiry' ); ?></label>
                        <div class="wc_ei_rule_after_login_container" style=" <?php if ( $wc_email_inquiry_show_button_after_login != 'yes' ) echo 'display: none;'; ?>">
                    	<select multiple="multiple" id="role_apply_show_inquiry_button" name="_wc_ei_settings_custom[role_apply_show_inquiry_button][]" data-placeholder="<?php _e( 'Choose Roles', 'wc_email_inquiry' ); ?>" style="display:none; width:300px;" class="chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>">
						<?php foreach ($roles as $key => $val) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( in_array($key, (array) $role_apply_show_inquiry_button), true ); ?>><?php echo $val ?></option>
                        <?php } ?>
                        </select>
                        </div>
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw" colspan="2"><strong><?php _e('Product Card', 'wc_email_inquiry'); ?></strong></th>
               	</tr>
            	<tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_single_only"><?php _e('Email Inquiry Feature','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><label><input type="checkbox" name="_wc_ei_settings_custom[wc_email_inquiry_single_only]" id="wc_email_inquiry_single_only" value="no" <?php checked( $wc_email_inquiry_single_only, 'no' ); ?> /> <?php _e( "Check to show Email Inquiry feature on this Products card", 'wc_email_inquiry' ); ?></label>
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw" colspan="2"><strong><?php _e('Email Delivery Options', 'wc_email_inquiry'); ?></strong></th>
               	</tr>      
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_email_to"><?php _e('Inquiry Email goes to','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><input type="text" name="_wc_ei_settings_custom[wc_email_inquiry_email_to]" id="wc_email_inquiry_email_to" value="<?php echo $wc_email_inquiry_email_to;?>" style="min-width:300px" /> 
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_email_cc"><?php _e('Copy to','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><input type="text" name="_wc_ei_settings_custom[wc_email_inquiry_email_cc]" id="wc_email_inquiry_email_cc" value="<?php echo $wc_email_inquiry_email_cc;?>" style="min-width:300px" /> 
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw" colspan="2"><strong><?php _e('Inquiry Button / Hyperlink Options', 'wc_email_inquiry'); ?></strong></th>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label><?php _e('Button or Hyperlink Text','wc_email_inquiry'); ?></label></th>
                    <td class="forminp">
                    <label><input type="radio" name="_wc_ei_button_custom[wc_email_inquiry_button_type]" id="wc_email_inquiry_button" class="wc_email_inquiry_button_type" value="" checked="checked" /> <?php _e('Button', 'wc_email_inquiry'); ?></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <label><input type="radio" name="_wc_ei_button_custom[wc_email_inquiry_button_type]" id="wc_email_inquiry_link" class="wc_email_inquiry_button_type" value="link" <?php checked( $wc_email_inquiry_button_type, 'link' ); ?> /> <?php _e('Link', 'wc_email_inquiry'); ?></label>
                    </td>
               	</tr>
			</tbody>
        </table>
        <div class="button_type_link" style=" <?php if($wc_email_inquiry_button_type != 'link') { echo 'display:none'; } ?>">
        <table cellspacing="0" class="form-table " >
			<tbody>                
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_text_before"><?php _e('Text Before','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><input type="text" name="_wc_ei_button_custom[wc_email_inquiry_text_before]" id="wc_email_inquiry_text_before" value="<?php echo $wc_email_inquiry_text_before;?>" style="min-width:300px" /> 
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_hyperlink_text"><?php _e('Hyperlink Text','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><input type="text" name="_wc_ei_button_custom[wc_email_inquiry_hyperlink_text]" id="wc_email_inquiry_hyperlink_text" value="<?php echo $wc_email_inquiry_hyperlink_text;?>" style="min-width:300px" /> 
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_trailing_text"><?php _e('Trailing Text','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><input type="text" name="_wc_ei_button_custom[wc_email_inquiry_trailing_text]" id="wc_email_inquiry_trailing_text" value="<?php echo $wc_email_inquiry_trailing_text;?>" style="min-width:300px" /> 
                    </td>
               	</tr>
			</tbody>
        </table>
        </div>
        <div class="button_type_button" style=" <?php if($wc_email_inquiry_button_type == 'link') { echo 'display:none'; } ?>">
        <table cellspacing="0" class="form-table " >
			<tbody>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_email_inquiry_button_title"><?php _e('Button Text','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><input type="text" name="_wc_ei_button_custom[wc_email_inquiry_button_title]" id="wc_email_inquiry_button_title" value="<?php echo $wc_email_inquiry_button_title;?>" style="min-width:300px" /> 
                    </td>
               	</tr>
			</tbody>
		</table>
        </div>
        
        <table cellspacing="0" class="form-table">
			<tbody>
            	<tr valign="top">
                    <th class="titledesc" scope="rpw" colspan="2"><strong><?php _e('Contact Form from another Plugin', 'wc_email_inquiry'); ?></strong></th>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="contact_form_shortcode"><?php _e('Enter Form Shortcode','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><input type="text" name="_wc_ei_settings_custom[contact_form_shortcode]" id="contact_form_shortcode" value="<?php echo $contact_form_shortcode;?>" style="min-width:300px" /> 
                    </td>
               	</tr>
        	</tbody>
		</table>
        </div>
        <div id="wc_ei_read_more" class="wp-tab-panel" style="display:none;">
        <table cellspacing="0" class="form-table">
			<tbody>
            	<tr valign="top">
                    <th class="titledesc" scope="rpw" colspan="2"><strong><?php _e( "Read More Rules and Roles", 'wc_email_inquiry' ); ?></strong></th>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_ei_show_read_more_button_before_login"><?php _e('View before log in','wc_email_inquiry'); ?></label></th>
                    <td class="forminp">
                    <label><input type="checkbox" name="_wc_ei_read_more_button_custom[show_read_more_button_before_login]" id="wc_ei_show_read_more_button_before_login" value="yes" <?php checked( $show_read_more_button_before_login, 'yes' ); ?> /> <?php echo __('Check to show Read More Feature on this products card', 'wc_email_inquiry'); ?></label>
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_ei_show_read_more_button_after_login"><?php _e('View after login','wc_email_inquiry'); ?></label></th>
                    <td class="forminp">
                    	<label><input class="wc_ei_rule_after_login" type="checkbox" name="_wc_ei_read_more_button_custom[show_read_more_button_after_login]" id="wc_ei_show_read_more_button_after_login" value="yes" <?php checked( $show_read_more_button_after_login, 'yes' ); ?> /> <?php echo __('Check and select user the roles who can see Read More when they are logged in', 'wc_email_inquiry'); ?></label>
                        <div class="wc_ei_rule_after_login_container" style=" <?php if ( $show_read_more_button_after_login != 'yes' ) echo 'display: none;'; ?>">
                    	<select multiple="multiple" id="role_apply_show_read_more" name="_wc_ei_read_more_button_custom[role_apply_show_read_more][]" data-placeholder="<?php _e( 'Choose Roles', 'wc_email_inquiry' ); ?>" style="display:none; width:300px;" class="chzn-select <?php if ( is_rtl() ) { echo 'chzn-rtl'; } ?>">
						<?php foreach ($roles as $key => $val) { ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( in_array($key, (array) $role_apply_show_read_more), true ); ?>><?php echo $val ?></option>
                        <?php } ?>
                        </select>
                        </div>
                    </td>
               	</tr>
                <tr valign="top">
                    <th class="titledesc" scope="rpw"><label for="wc_ei_read_more_text"><?php _e('Button / Link Text','wc_email_inquiry'); ?></label></th>
                    <td class="forminp"><input type="text" name="_wc_ei_read_more_button_custom[read_more_text]" id="wc_ei_read_more_text" value="<?php echo $read_more_text;?>" style="min-width:300px" /> <p class="description"><?php echo __( 'Leave Empty and will use Global Setting Text', 'wc_email_inquiry' ); ?></p>
                    </td>
               	</tr>
			</tbody>
        </table>
        </div>
        </div>
        <?php
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wc_ei_metabox_action', 'wc_ei_metabox_nonce_field' );
		?>
        <div style="clear: both;"></div>
        
		<script type="text/javascript">
			(function($){		
				$(function(){	
					$('.wc_email_inquiry_button_type').click(function(){
						if ($("input[name='_wc_ei_button_custom[wc_email_inquiry_button_type]']:checked").val() == '') {
							$(".button_type_button").slideDown();
							$(".button_type_link").slideUp();
						} else {
							$(".button_type_link").slideDown();
							$(".button_type_button").slideUp();
						}
					});
				});		  
			})(jQuery);
		</script>
		<?php
	}
	
	public static function save_meta_boxes($post_id){

		// Check if our nonce is set.
		if ( ! isset( $_POST['wc_ei_metabox_nonce_field'] ) || ! check_admin_referer( 'wc_ei_metabox_action', 'wc_ei_metabox_nonce_field' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$post_status = get_post_status($post_id);
		$post_type = get_post_type($post_id);
		if ($post_type == 'product' && $post_status != false  && $post_status != 'inherit') {
			if (isset($_REQUEST['wc_email_inquiry_reset_product_options']) && $_REQUEST['wc_email_inquiry_reset_product_options'] == 1) {
				delete_post_meta($post_id, '_wc_ei_cart_price_custom');
				delete_post_meta($post_id, '_wc_ei_settings_custom');
				delete_post_meta($post_id, '_wc_ei_button_custom');
				delete_post_meta($post_id, '_wc_ei_read_more_button_custom');
				return;
			}
			
			$wc_ei_cart_price_custom       = $_REQUEST['_wc_ei_cart_price_custom'];
			$wc_ei_settings_custom         = $_REQUEST['_wc_ei_settings_custom'];
			$wc_ei_button_custom           = $_REQUEST['_wc_ei_button_custom'];
			$wc_ei_read_more_button_custom = $_REQUEST['_wc_ei_read_more_button_custom'];

			if ( !isset($wc_ei_cart_price_custom['wc_email_inquiry_hide_addcartbt'] ) ) {
				$wc_ei_cart_price_custom['wc_email_inquiry_hide_addcartbt'] = 'no' ;
			}
			if ( !isset($wc_ei_cart_price_custom['wc_email_inquiry_hide_addcartbt_after_login'] ) ) {
				$wc_ei_cart_price_custom['wc_email_inquiry_hide_addcartbt_after_login'] = 'no' ;
			}
			if ( !isset($wc_ei_cart_price_custom['role_apply_hide_cart'] ) ) {
				$wc_ei_cart_price_custom['role_apply_hide_cart'] = array() ;
			}
			if ( !isset($wc_ei_settings_custom['wc_email_inquiry_show_button'] ) ) {
				$wc_ei_settings_custom['wc_email_inquiry_show_button'] = 'no' ;
			}
			if ( !isset($wc_ei_settings_custom['wc_email_inquiry_show_button_after_login'] ) ) {
				$wc_ei_settings_custom['wc_email_inquiry_show_button_after_login'] = 'no' ;
			}
			if ( !isset($wc_ei_settings_custom['role_apply_show_inquiry_button'] ) ) {
				$wc_ei_settings_custom['role_apply_show_inquiry_button'] = array() ;
			}
			if ( !isset($wc_ei_read_more_button_custom['show_read_more_button_before_login'] ) ) {
				$wc_ei_read_more_button_custom['show_read_more_button_before_login'] = 'no' ;
			}
			if ( !isset($wc_ei_read_more_button_custom['show_read_more_button_after_login'] ) ) {
				$wc_ei_read_more_button_custom['show_read_more_button_after_login'] = 'no' ;
			}
			if ( !isset($wc_ei_read_more_button_custom['role_apply_show_read_more'] ) ) {
				$wc_ei_read_more_button_custom['role_apply_show_read_more'] = array() ;
			}
			if ( !isset($wc_ei_cart_price_custom['wc_email_inquiry_hide_price'] ) ) {
				$wc_ei_cart_price_custom['wc_email_inquiry_hide_price'] = 'no' ;
			}
			if ( !isset($wc_ei_cart_price_custom['wc_email_inquiry_hide_price_after_login'] ) ) {
				$wc_ei_cart_price_custom['wc_email_inquiry_hide_price_after_login'] = 'no' ;
			}
			if ( !isset($wc_ei_cart_price_custom['role_apply_hide_price'] ) ) {
				$wc_ei_cart_price_custom['role_apply_hide_price'] = array() ;
			}
			if ( !isset($wc_ei_button_custom['wc_email_inquiry_button'] ) ) {
				$wc_ei_button_custom['wc_email_inquiry_button'] = 'button' ;
			}
			if ( !isset($wc_ei_settings_custom['wc_email_inquiry_single_only'] ) ) {
				$wc_ei_settings_custom['wc_email_inquiry_single_only'] = 'yes' ;
			}

			update_post_meta($post_id, '_wc_ei_cart_price_custom', $wc_ei_cart_price_custom);
			update_post_meta($post_id, '_wc_ei_settings_custom', $wc_ei_settings_custom);
			update_post_meta($post_id, '_wc_ei_button_custom', $wc_ei_button_custom);
			update_post_meta($post_id, '_wc_ei_read_more_button_custom', $wc_ei_read_more_button_custom);
		}
	}
}
?>
