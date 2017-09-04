<?php
/**
 * WC Email Inquiry Quote & Order Hook Filter
 *
 * Table Of Contents
 *
 * change_template_files()
 * woocommerce_pay_action()
 * change_page_title()
 * replace_add_to_cart_text()
 * replace_single_add_to_cart_text()
 * replace_add_message()
 * replace_woocommerce_params()
 * replace_add_error_message()
 * replace_widget_cart_title()
 * replace_all_content_widget_cart()
 * replace_order_button_text()
 * replace_checkout_fields_order_comments()
 * make_must_create_account_to_false()
 * auto_create_account()
 * show_hide_shipping_options_before()
 * show_hide_shipping_options_after()
 * show_hide_shipping_prices_on_checkout_page()
 * show_hide_shipping_prices_after_submitted()
 * change_shipping_methods_template_before()
 * change_shipping_methods_template_after()
 * custom_gateway_init()
 * add_custom_gateway()
 * show_payment_gateways_for_role()
 * custom_email_init()
 * custom_email_load()
 * add_custom_emails()
 * hide_price_in_myaccount_page()
 * add_actions_for_quote()
 * details_add_question_mark_above()
 * frontend_script_include()
 *
 */
class WC_Email_Inquiry_Quote_Order_Hook_Filter
{
	// Change template file of woocommerce by own template file of plugin
	public static function change_template_files( $template='', $template_name='', $template_path='') {
		global $wc_email_inquiry_rules_roles_settings;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $wc_email_inquiry_rules_roles_settings['use_woocommerce_css'] == 'yes' && ( $apply_request_a_quote || $apply_add_to_order ) ) {
			wp_enqueue_style( 'woocommerce-general-default', str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/css/woocommerce.css', '', WC_VERSION, 'all' );
		}
				
		if ($template_name == 'cart/cart.php') {
			$quotes_folder = 'quotes';
			$orders_folder = 'orders';
			if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
				$quotes_folder = 'quotes-25';
				$orders_folder = 'orders-25';
			} elseif ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
				$quotes_folder = 'quotes-23';
				$orders_folder = 'orders-24';
			} elseif ( version_compare( WC_VERSION, '2.3.8', '>=' ) ) {
				$quotes_folder = 'quotes-23';
				$orders_folder = 'orders-23-8';
			} elseif ( version_compare( WC_VERSION, '2.3', '>=' ) ) {
				$quotes_folder = 'quotes-23';
				$orders_folder = 'orders-23';
			} elseif ( version_compare( WC_VERSION, '2.1', '>=' ) ) {
				$quotes_folder = 'quotes-new';
				$orders_folder = 'orders-new';
			}
			if ($apply_request_a_quote) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/cart.php' );
			elseif ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/cart.php' );
		} elseif ($template_name == 'cart/cart-empty.php') {
			$quotes_folder = 'quotes';
			$orders_folder = 'orders';
			if ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
				$quotes_folder = 'quotes-24';
				$orders_folder = 'orders-24';
			} elseif ( version_compare( WC_VERSION, '2.1', '>=' ) ) {
				$quotes_folder = 'quotes-new';
				$orders_folder = 'orders-new';
			}
			if ($apply_request_a_quote) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/cart-empty.php' );
			elseif ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/cart-empty.php' );
		} elseif ($template_name == 'cart/totals.php') {
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders/totals.php' );
		} elseif ($template_name == 'cart/cart-totals.php') {
			$orders_folder = 'orders-new';
			if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
				$orders_folder = 'orders-25';
			} elseif ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
				$orders_folder = 'orders-24';
			} elseif ( version_compare( WC_VERSION, '2.3', '>=' ) ) {
				$orders_folder = 'orders-23';
			}
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/cart-totals.php' );
		} elseif ($template_name == 'checkout/form-checkout.php') {
			$quotes_folder = 'quotes';
			$orders_folder = 'orders';
			if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
				$quotes_folder = 'quotes-25';
				$orders_folder = 'orders-25';
			} elseif ( version_compare( WC_VERSION, '2.3', '>=' ) ) {
				$quotes_folder = 'quotes-23';
				$orders_folder = 'orders-23';
			} elseif ( version_compare( WC_VERSION, '2.1', '>=' ) ) {
				$quotes_folder = 'quotes-new';
				$orders_folder = 'orders-new';
			}
			if ($apply_request_a_quote) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/form-checkout.php' );
			elseif ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/form-checkout.php' );
		} elseif ($template_name == 'checkout/form-billing.php') {
			$quotes_folder = 'quotes';
			$orders_folder = 'orders';
			if ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
				$quotes_folder = 'quotes-24';
				$orders_folder = 'orders-24';
			} elseif ( version_compare( WC_VERSION, '2.1', '>=' ) ) {
				$quotes_folder = 'quotes-new';
				$orders_folder = 'orders-new';
			}
			if ($apply_request_a_quote) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/form-billing.php' );
			elseif ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/form-billing.php' );
		} elseif ($template_name == 'checkout/review-order.php') {
			$quotes_folder = 'quotes';
			$orders_folder = 'orders';
			if ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
				$quotes_folder = 'quotes-23';
				$orders_folder = 'orders-24';
			} elseif ( version_compare( WC_VERSION, '2.3', '>=' ) ) {
				$quotes_folder = 'quotes-23';
				$orders_folder = 'orders-23';
			} elseif ( version_compare( WC_VERSION, '2.2', '>=' ) ) {
				$quotes_folder = 'quotes-22';
				$orders_folder = 'orders-22';
			} elseif ( version_compare( WC_VERSION, '2.1', '>=' ) ) {
				$quotes_folder = 'quotes-new';
				$orders_folder = 'orders-new';
			}
			if ($apply_request_a_quote) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/review-order.php' );
			elseif ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/review-order.php' );
		} elseif ($template_name == 'checkout/thankyou.php') {
			if ($apply_request_a_quote) {
				if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
					if (isset($_GET['order']) && $_GET['order'] > 0) {
						$terms = wp_get_object_terms( (int) $_GET['order'], 'shop_order_status', array('fields' => 'slugs') );	
						if (isset($terms[0]) && !in_array ($terms[0], array('quote', 'pending' ) ) ) return $template; 
					}
				} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
					global $wp;
					if ( $wp && isset( $wp->query_vars['order-received'] ) ) {
						$order_id 	= absint( $wp->query_vars['order-received'] );
						$terms = wp_get_object_terms( (int) $order_id, 'shop_order_status', array('fields' => 'slugs') );	
						if (isset($terms[0]) && !in_array ($terms[0], array('quote', 'pending' ) ) ) return $template; 
					}
				} else {
					// For woo 2.2	
					global $wp;
					if ( $wp && isset( $wp->query_vars['order-received'] ) ) {
						$order_id 	= absint( $wp->query_vars['order-received'] );
						$order = wc_get_order( $order_id );
						$order_status = $order->get_status();	
						if ( ! in_array( $order_status, array('quote', 'pending' ) ) ) return $template; 
					}
				}
				if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes/thankyou.php' );
				} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-new/thankyou.php' );
				} elseif( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-22/thankyou.php' );
				} elseif( version_compare( WC_VERSION, '2.5.0', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-24/thankyou.php' );
				} else {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-25/thankyou.php' );
				}
			} elseif ($apply_add_to_order) {
				if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders/thankyou.php' );
				} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-new/thankyou.php' );	
				} elseif ( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-22/thankyou.php' );
				} elseif ( version_compare( WC_VERSION, '2.5.0', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-24/thankyou.php' );
				} else {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-25/thankyou.php' );
				}
			}
		} elseif ($template_name == 'order/order-details.php') {
			if ($apply_request_a_quote) {
				if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
					if (isset($_GET['order']) && $_GET['order'] > 0) {
						$terms = wp_get_object_terms( (int) $_GET['order'], 'shop_order_status', array('fields' => 'slugs') );	
						if (isset($terms[0]) && !in_array ($terms[0], array('quote', 'pending' ) ) ) return $template;
						$sent_quote = get_post_meta( $_GET['order'], '_wc_email_inquiry_sent_quote', true );
						if ( $terms[0] != 'quote' || $sent_quote ) {
							return WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes/order-details-manual-pending.php' );
						}
					}
				} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
					global $wp;
					if ( $wp && ( isset( $wp->query_vars['order-received'] ) || isset( $wp->query_vars['view-order'] ) ) ) {
						if ( isset( $wp->query_vars['order-received'] ) ) $order_id 	= absint( $wp->query_vars['order-received'] );
						elseif ( isset( $wp->query_vars['view-order'] ) ) $order_id 	= absint( $wp->query_vars['view-order'] );
						$terms = wp_get_object_terms( (int) $order_id, 'shop_order_status', array('fields' => 'slugs') );	
						if (isset($terms[0]) && !in_array ($terms[0], array('quote', 'pending' ) ) ) return $template;
						$sent_quote = get_post_meta( $order_id, '_wc_email_inquiry_sent_quote', true );
						if ( $terms[0] != 'quote' || $sent_quote ) {
							return WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-new/order-details-manual-pending.php' );
						}
					}
				} else {
					// For woo 2.2	
					global $wp;
					if ( $wp && ( isset( $wp->query_vars['order-received'] ) || isset( $wp->query_vars['view-order'] ) ) ) {
						if ( isset( $wp->query_vars['order-received'] ) ) $order_id 	= absint( $wp->query_vars['order-received'] );
						elseif ( isset( $wp->query_vars['view-order'] ) ) $order_id 	= absint( $wp->query_vars['view-order'] );
						$order = wc_get_order( $order_id );
						$order_status = $order->get_status();
						if ( ! in_array( $order_status, array('quote', 'pending' ) ) ) return $template;
						$sent_quote = get_post_meta( $order_id, '_wc_email_inquiry_sent_quote', true );
						if ( $order_status != 'quote' || $sent_quote ) {
							if ( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
								return WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-22/order-details-manual-pending.php' );
							} elseif ( version_compare( WC_VERSION, '2.5.0', '<' ) ) {
								return WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-24/order-details-manual-pending.php' );
							} elseif ( version_compare( WC_VERSION, '2.5.3', '<' ) ) {
								return WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-25/order-details-manual-pending.php' );
							} else {
								return WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-25-3/order-details-manual-pending.php' );
							}
						}
					}
				}
				if ( $apply_auto_quote ) {
					if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes/order-details-auto.php' );
					} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-new/order-details-auto.php' );
					} elseif ( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-22/order-details-auto.php' );
					} elseif ( version_compare( WC_VERSION, '2.5.0', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-24/order-details-auto.php' );
					} elseif ( version_compare( WC_VERSION, '2.5.3', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-25/order-details-auto.php' );
					} else {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-25-3/order-details-auto.php' );
					}
				} else {
					if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes/order-details.php' );
					} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-new/order-details.php' );
					} elseif ( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-22/order-details.php' );
					} elseif ( version_compare( WC_VERSION, '2.5.0', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-24/order-details.php' );
					} elseif ( version_compare( WC_VERSION, '2.5.3', '<' ) ) {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-25/order-details.php' );
					} else {
						$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-25-3/order-details.php' );
					}
				}
			} elseif ($apply_add_to_order) {
				if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders/order-details.php' );
				} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-new/order-details.php' );
				} elseif ( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-22/order-details.php' );
				} elseif ( version_compare( WC_VERSION, '2.5.0', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-24/order-details.php' );
				} elseif ( version_compare( WC_VERSION, '2.5.3', '<' ) ) {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-25/order-details.php' );
				} else {
					$template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'orders-25-3/order-details.php' );
				}
			}
		} elseif ($template_name == 'emails/customer-processing-quote.php') {
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/customer-processing-quote.php' );
		} elseif ($template_name == 'emails/customer-processing-quote_2.1.php') {
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/customer-processing-quote_2.1.php' );
		} elseif ($template_name == 'emails/customer-processing-quote_2.4.php') {
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/customer-processing-quote_2.4.php' );
		} elseif ($template_name == 'emails/customer-processing-quote_2.5.php') {
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/customer-processing-quote_2.5.php' );
		} elseif ($template_name == 'emails/plain/customer-processing-quote.php') {
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/customer-processing-quote.php' );
		} elseif ($template_name == 'emails/plain/customer-processing-quote_2.1.php') {
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/customer-processing-quote_2.1.php' );
		} elseif ($template_name == 'emails/plain/customer-processing-quote_2.5.php') {
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/customer-processing-quote_2.5.php' );
		} elseif ($template_name == 'emails/customer-pending-order.php') {
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/customer-pending-order.php' );
		} elseif ($template_name == 'emails/customer-pending-order_2.1.php') {
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/customer-pending-order_2.1.php' );
		} elseif ($template_name == 'emails/customer-pending-order_2.4.php') {
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/customer-pending-order_2.4.php' );
		} elseif ($template_name == 'emails/customer-pending-order_2.5.php') {
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/customer-pending-order_2.5.php' );
		} elseif ($template_name == 'emails/plain/customer-pending-order.php') {
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/customer-pending-order.php' );
		} elseif ($template_name == 'emails/plain/customer-pending-order_2.2.php') {
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/customer-pending-order_2.2.php' );
		} elseif ($template_name == 'emails/plain/customer-pending-order_2.5.php') {
			if ($apply_add_to_order) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/customer-pending-order_2.5.php' );
		} elseif ($template_name == 'emails/email-order-items.php') {
			if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
				if ( $apply_manual_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/quote-email-order-items_2.0.3.php' );
			} elseif( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
				if ( $apply_manual_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/quote-email-order-items_2.2.php' );
			} else {
				if ( $apply_manual_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/quote-email-order-items.php' );
			}
		} elseif ($template_name == 'emails/plain/email-order-items.php') {
			if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
				if ( $apply_manual_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/quote-email-order-items_2.0.3.php' );
			} elseif( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
				if ( $apply_manual_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/quote-email-order-items_2.2.php' );
			} else {
				if ( $apply_manual_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'emails/plain/quote-email-order-items.php' );
			}
		} elseif ($template_name == 'checkout/form-pay.php') {
			if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
				$terms = wp_get_object_terms( (int) $_GET['order_id'], 'shop_order_status', array('fields' => 'slugs') );
				if ( $apply_auto_quote || ( $apply_manual_quote && $terms[0] != 'quote' ) ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes/form-pay.php' );
			} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
				global $wp;
				$order_id 	= absint( $wp->query_vars['order-pay'] );
				$terms = wp_get_object_terms( (int) $order_id, 'shop_order_status', array('fields' => 'slugs') );
				if ( $apply_auto_quote || ( $apply_manual_quote && $terms[0] != 'quote' ) ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'quotes-new/form-pay.php' );
			} else {
				// For woo 2.2	
				global $wp;
				$order_id 	= absint( $wp->query_vars['order-pay'] );
				$order = wc_get_order( $order_id );
				$order_status = $order->get_status();
				if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
					$quotes_folder = 'quotes-25';
				} else {
					$quotes_folder = 'quotes-22';
				}
				if ( $apply_auto_quote || ( $apply_manual_quote && $order_status != 'quote' ) ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/form-pay.php' );
			}
		} elseif ( $template_name == 'global/quantity-input.php' ) {
			if ( $apply_request_a_quote || $apply_add_to_order ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( 'global/quantity-input.php' );
		} elseif ( $template_name == 'cart/cart-shipping.php' ) {
			$quotes_folder = 'quotes-new';
			$orders_folder = 'orders-new';
			if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
				$quotes_folder = 'quotes-25';
				$orders_folder = 'orders-25';
			} elseif ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
				$quotes_folder = 'quotes-24';
				$orders_folder = 'orders-24';
			} elseif ( version_compare( WC_VERSION, '2.3', '>=' ) ) {
				$quotes_folder = 'quotes-23';
				$orders_folder = 'orders-23';
			}
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/cart-shipping.php' );
			elseif ( $apply_add_to_order ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/cart-shipping.php' );
		} elseif ( $template_name == 'checkout/payment.php' ) {
			$quotes_folder = 'quotes-23';
			$orders_folder = 'orders-23';
			if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
				$quotes_folder = 'quotes-25';
				$orders_folder = 'orders-25';
			} elseif ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
				$quotes_folder = 'quotes-24';
				$orders_folder = 'orders-24';
			}
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/payment.php' );
			elseif ( $apply_add_to_order ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/payment.php' );
		} elseif ( $template_name == 'checkout/terms.php' ) {
			$quotes_folder = 'quotes-25';
			$orders_folder = 'orders-25';
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/terms.php' );
			elseif ( $apply_add_to_order ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/terms.php' );
		} elseif ( $template_name == 'order/order-details-customer.php' ) {
			$quotes_folder = 'quotes-24';
			$orders_folder = 'orders-24';
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/order-details-customer.php' );
			elseif ( $apply_add_to_order ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/order-details-customer.php' );
		} elseif ( $template_name == 'order/order-details-item.php' ) {
			$quotes_folder = 'quotes-24';
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/order-details-item.php' );
		} elseif ( $template_name == 'cart/proceed-to-checkout-button.php' ) {
			$quotes_folder = 'quotes-24';
			$orders_folder = 'orders-24';
			if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
				$quotes_folder = 'quotes-25';
				$orders_folder = 'orders-25';
			}
			if ( $apply_request_a_quote ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $quotes_folder . '/proceed-to-checkout-button.php' );
			elseif ( $apply_add_to_order ) $template = WC_Email_Inquiry_Quote_Order_Functions::get_template_file_path( $orders_folder . '/proceed-to-checkout-button.php' );
		}
		
		return $template;
	}
	
	// Fixed to get correct template when order total <= 0
	public static function woocommerce_cart_needs_payment( $needs_payment, $this ) {
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ($apply_request_a_quote || $apply_add_to_order) $needs_payment = true;
		
		return $needs_payment;
	}
	
	// Change Page Title for Quotes or Orders
	public static function change_page_title( $title='', $id = '' ) {
		global $wc_email_inquiry_quote_cart_page;
		global $wc_email_inquiry_order_cart_page;
		global $wc_email_inquiry_quote_checkout_page;
		global $wc_email_inquiry_order_checkout_page;
		global $wc_email_inquiry_quote_order_received_page;
		global $wc_email_inquiry_order_order_received_page;
		
		if ( !in_array( basename ($_SERVER['PHP_SELF']), array('admin-ajax.php') ) && is_admin() ) return $title;
		
		if ($id == '') return $title;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
			$cart_page_id = woocommerce_get_page_id('cart');
			$checkout_page_id = woocommerce_get_page_id('checkout');
			$cart_page_id = woocommerce_get_page_id('cart');
			$is_order_receive_page = ( ( $id == woocommerce_get_page_id('thanks') ) ? true : false );
			$is_pay_page = ( ( $id == woocommerce_get_page_id('pay') ) ? true : false );
		} else {
			$cart_page_id = wc_get_page_id('cart');
			$checkout_page_id = wc_get_page_id('checkout');
			$is_order_receive_page = is_order_received_page();
			$is_pay_page = is_checkout_pay_page();
		}
		if ( $id == $cart_page_id )	{
			if ($apply_request_a_quote) return $wc_email_inquiry_quote_cart_page['quote_cart_page_name'];
			elseif ($apply_add_to_order) return $wc_email_inquiry_order_cart_page['order_cart_page_name'];
		} elseif ( $is_order_receive_page )	{
			if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
				if (isset($_GET['order']) && $_GET['order'] > 0) {
					$terms = wp_get_object_terms( (int) $_GET['order'], 'shop_order_status', array('fields' => 'slugs') );
					if ( isset($terms[0]) && $terms[0] == 'on-hold' && $apply_add_to_order) return $wc_email_inquiry_order_order_received_page['order_order_received_page_name'];	
					if (isset($terms[0]) && !in_array ($terms[0], array('quote', 'pending' ) ) ) return $title; 
				}
			} elseif ( version_compare( WC_VERSION, '2.2', '<' ) ) {
				global $wp;
				if ( $wp && isset( $wp->query_vars['order-received'] ) ) {
					$order_id 	= absint( $wp->query_vars['order-received'] );
					$terms = wp_get_object_terms( (int) $order_id, 'shop_order_status', array('fields' => 'slugs') );	
					if ( isset($terms[0]) && $terms[0] == 'on-hold' && $apply_add_to_order && $id == $checkout_page_id ) return $wc_email_inquiry_order_order_received_page['order_order_received_page_name'];
					if (isset($terms[0]) && !in_array ($terms[0], array('quote', 'pending' ) ) ) return $title; 
				}
			} else {
				// For 2.2	
				global $wp;
				if ( $wp && isset( $wp->query_vars['order-received'] ) ) {
					$order_id 	= absint( $wp->query_vars['order-received'] );
					$order = wc_get_order( $order_id );
					$order_status = $order->get_status();
					if ( $order_status == 'on-hold' && $apply_add_to_order && $id == $checkout_page_id ) return $wc_email_inquiry_order_order_received_page['order_order_received_page_name'];
					if ( ! in_array( $order_status, array('quote', 'pending' ) ) ) return $title; 
				}
			}
			if ( version_compare( WC_VERSION, '2.1', '>=' ) && $id != $checkout_page_id ) return $title;
			if ($apply_request_a_quote) return $wc_email_inquiry_quote_order_received_page['quote_order_received_page_name'];
		} elseif ( $id == $checkout_page_id && ! $is_pay_page )	{
			if ($apply_request_a_quote) return $wc_email_inquiry_quote_checkout_page['quote_checkout_page_name'];
			elseif ($apply_add_to_order) return $wc_email_inquiry_order_checkout_page['order_checkout_page_name'];
		}/* else {
			if ($apply_request_a_quote) {
				$title = str_replace( __( 'cart', 'woocommerce' ), __( 'quote', 'wc_email_inquiry' ), $title );
				$title = str_replace( __( 'Cart', 'woocommerce' ), __( 'Quote', 'wc_email_inquiry' ), $title );
				$title = str_replace( __( 'CART', 'woocommerce' ), __( 'QUOTE', 'wc_email_inquiry' ), $title );
				$title = str_ireplace( __( 'cart', 'woocommerce' ), __( 'Quote', 'wc_email_inquiry' ), $title );
			} elseif ( $apply_add_to_order ) {
				$title = str_replace( __( 'cart', 'woocommerce' ), __( 'order', 'wc_email_inquiry' ), $title );
				$title = str_replace( __( 'Cart', 'woocommerce' ), __( 'Order', 'wc_email_inquiry' ), $title );
				$title = str_replace( __( 'CART', 'woocommerce' ), __( 'ORDER', 'wc_email_inquiry' ), $title );
				$title = str_ireplace( __( 'cart', 'woocommerce' ), __( 'Order', 'wc_email_inquiry' ), $title );
			}
		}*/
		
		return $title;
	}
	
	// For Product Page : START //
	public static function replace_add_to_cart_text( $add_to_cart='' ) {
		global $wc_email_inquiry_quote_product_page;
		global $wc_email_inquiry_order_product_page;
		
		if ( !in_array( basename ($_SERVER['PHP_SELF']), array('admin-ajax.php') ) && is_admin() ) return $add_to_cart;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
				
		if ( $apply_request_a_quote )
			$add_to_cart = $wc_email_inquiry_quote_product_page['quote_button_text'];
		elseif ( $apply_add_to_order )
			$add_to_cart = $wc_email_inquiry_order_product_page['order_button_text'];
		
		return $add_to_cart;
	}
	
	public static function replace_add_to_cart_text_woo_21( $add_to_cart='', $current_product ) {
		global $wc_email_inquiry_quote_product_page;
		global $wc_email_inquiry_order_product_page;
		
		if ( !in_array( basename ($_SERVER['PHP_SELF']), array('admin-ajax.php') ) && is_admin() ) return $add_to_cart;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $current_product && $current_product->product_type == 'simple' && $current_product->is_purchasable() && $current_product->is_in_stock() ) {
				
			if ( $apply_request_a_quote )
				$add_to_cart = $wc_email_inquiry_quote_product_page['quote_button_text'];
			elseif ( $apply_add_to_order )
				$add_to_cart = $wc_email_inquiry_order_product_page['order_button_text'];
			
		}
		
		return $add_to_cart;
	}
	
	public static function replace_single_add_to_cart_text( $add_to_cart='', $product_type ) {
		global $wc_email_inquiry_quote_product_page;
		global $wc_email_inquiry_order_product_page;
		
		if ( !in_array( basename ($_SERVER['PHP_SELF']), array('admin-ajax.php') ) && is_admin() ) return $add_to_cart;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote )
			$add_to_cart = $wc_email_inquiry_quote_product_page['quote_button_text'];
		elseif ( $apply_add_to_order )
			$add_to_cart = $wc_email_inquiry_order_product_page['order_button_text'];
		
		return $add_to_cart;
	}
	
	public static function replace_single_add_to_cart_text_woo_21( $add_to_cart='', $current_product ) {
		global $wc_email_inquiry_quote_product_page;
		global $wc_email_inquiry_order_product_page;
		
		if ( !in_array( basename ($_SERVER['PHP_SELF']), array('admin-ajax.php') ) && is_admin() ) return $add_to_cart;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $current_product && $current_product->product_type == 'external' ) return $add_to_cart;
		
		if ( $apply_request_a_quote )
			$add_to_cart = $wc_email_inquiry_quote_product_page['quote_button_text'];
		elseif ( $apply_add_to_order )
			$add_to_cart = $wc_email_inquiry_order_product_page['order_button_text'];
		
		return $add_to_cart;
	}

	// For WooCommerce 2.5.0
	public static function add_to_cart_message( $message, $product_id ) {
		global $wc_email_inquiry_quote_product_page;
		global $wc_email_inquiry_order_product_page;

		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();

		if ( ! $apply_request_a_quote && ! $apply_add_to_order ) {
			return $message;
		}

		$titles = array();

		if ( is_array( $product_id ) ) {
			foreach ( $product_id as $id ) {
				$titles[] = get_the_title( $id );
			}
		} else {
			$titles[] = get_the_title( $product_id );
		}

		$titles     = array_filter( $titles );
		if ( $apply_request_a_quote ) {
			$added_text = sprintf( $wc_email_inquiry_quote_product_page['quote_success_message'], wc_format_list_of_items( $titles ) );
		} elseif( $apply_add_to_order ) {
			$added_text = sprintf( $wc_email_inquiry_order_product_page['order_success_message'], wc_format_list_of_items( $titles ) );
		}


		// Output success messages
		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			if ( $apply_request_a_quote ) {
				$continue_shopping = $wc_email_inquiry_quote_product_page['quote_continue_button_text'];
			} elseif ( $apply_add_to_order ) {
				$continue_shopping = $wc_email_inquiry_order_product_page['order_continue_button_text'];
			}
			$return_to = apply_filters( 'woocommerce_continue_shopping_redirect', wp_get_referer() ? wp_get_referer() : home_url() );
			$message   = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( $return_to ), esc_html( $continue_shopping ), esc_html( $added_text ) );
		} else {
			if ( $apply_request_a_quote ) {
				$view_cart = $wc_email_inquiry_quote_product_page['quote_view_button_text'];
			} elseif ( $apply_add_to_order ) {
				$view_cart = $wc_email_inquiry_order_product_page['order_view_button_text'];
			}
			$message   = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html( $view_cart ), esc_html( $added_text ) );
		}

		return $message;
	}
	
	public static function replace_add_message( $success_message='' ) {
		global $wc_email_inquiry_quote_product_page;
		global $wc_email_inquiry_order_product_page;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		$product_names = '';

		if ( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
			$startpoint = strpos($success_message, '&quot;');
			if ($startpoint !== false) {
				$startpoint += 6;
				$endpoint = strrpos($success_message, '&quot;');
				if ( $endpoint !== false) {
					$product_names = substr($success_message, $startpoint , ($endpoint - $startpoint));	
				}
			}
		} else {
			$startpoint = strpos($success_message, '&ldquo;');
			if ($startpoint !== false) {
				$startpoint += 7;
				$endpoint = strrpos($success_message, '&rdquo;');
				if ( $endpoint !== false) {
					$product_names = '&ldquo;' . substr($success_message, $startpoint , ($endpoint - $startpoint)) . '&rdquo;';
				}
			}
		}

		if (trim($product_names) != '') {
			$new_success_message = str_replace( $product_names, '%s', $success_message );
			if ( version_compare( WC_VERSION, '2.4.0', '<' ) ) {
				if ( stristr( $new_success_message, __( '&quot;%s&quot; was successfully added to your cart.', 'woocommerce' ) ) !== false ) {
					if ( $apply_request_a_quote ) {
						$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_quote_product_page['quote_success_message'] );
						$success_message = str_replace( __( '&quot;%s&quot; was successfully added to your cart.', 'woocommerce' ), $replace_text, $new_success_message );
					} elseif ( $apply_add_to_order ) {
						$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_order_product_page['order_success_message'] );
						$success_message = str_replace( __( '&quot;%s&quot; was successfully added to your cart.', 'woocommerce' ), $replace_text, $new_success_message );
					}
				} if ( stristr( $new_success_message, __( 'Added &quot;%s&quot; to your cart.', 'woocommerce' ) ) !== false ) {
					if ( $apply_request_a_quote ) {
						$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_quote_product_page['quote_group_success_message'] );
						$success_message = str_replace( __( 'Added &quot;%s&quot; to your cart.', 'woocommerce' ), $replace_text, $new_success_message );
					} elseif ( $apply_add_to_order ) {
						$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_order_product_page['order_group_success_message'] );
						$success_message = str_replace( __( 'Added &quot;%s&quot; to your cart.', 'woocommerce' ), $replace_text, $new_success_message );
					}
				}
			} else {
				if ( stristr( $new_success_message, __( '%s has been added to your cart.', 'woocommerce' ) ) !== false ) {
					if ( $apply_request_a_quote ) {
						$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_quote_product_page['quote_success_message'] );
						$success_message = str_replace( __( '%s has been added to your cart.', 'woocommerce' ), $replace_text, $new_success_message );
					} elseif ( $apply_add_to_order ) {
						$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_order_product_page['order_success_message'] );
						$success_message = str_replace( __( '%s has been added to your cart.', 'woocommerce' ), $replace_text, $new_success_message );
					}
				} if ( stristr( $new_success_message, __( '%s have been added to your cart.', 'woocommerce' ) ) !== false ) {
					if ( $apply_request_a_quote ) {
						$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_quote_product_page['quote_group_success_message'] );
						$success_message = str_replace( __( '%s have been added to your cart.', 'woocommerce' ), $replace_text, $new_success_message );
					} elseif ( $apply_add_to_order ) {
						$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_order_product_page['order_group_success_message'] );
						$success_message = str_replace( __( '%s have been added to your cart.', 'woocommerce' ), $replace_text, $new_success_message );
					}
				}
			}
		}

		if ( $apply_request_a_quote ) {
			$success_message = str_replace( __( 'View Cart &rarr;', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_view_button_text'], $success_message );
			$success_message = str_replace( __( 'Continue Shopping &rarr;', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_continue_button_text'], $success_message );
		} elseif ( $apply_add_to_order ) {
			$success_message = str_replace( __( 'View Cart &rarr;', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_view_button_text'], $success_message );
			$success_message = str_replace( __( 'Continue Shopping &rarr;', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_continue_button_text'], $success_message );
		}
		
		if ( version_compare( WC_VERSION, '2.1', '>=' ) ) {
			if ( $apply_request_a_quote ) {
				$success_message = str_replace( __( 'View Cart', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_view_button_text'], $success_message );
				$success_message = str_replace( __( 'Continue Shopping', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_continue_button_text'], $success_message );
			} elseif ( $apply_add_to_order ) {
				$success_message = str_replace( __( 'View Cart', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_view_button_text'], $success_message );
				$success_message = str_replace( __( 'Continue Shopping', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_continue_button_text'], $success_message );
			}
		}
		
		return $success_message;
	}
	
	public static function replace_woocommerce_params($woocommerce_params) {
		global $wc_email_inquiry_quote_product_page;
		global $wc_email_inquiry_order_product_page;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote ) {
			if (isset($woocommerce_params['i18n_view_cart'])) $woocommerce_params['i18n_view_cart'] = $wc_email_inquiry_quote_product_page['quote_view_button_text'];
		} elseif ( $apply_add_to_order ) {
			if (isset($woocommerce_params['i18n_view_cart'])) $woocommerce_params['i18n_view_cart'] = $wc_email_inquiry_order_product_page['order_view_button_text'];
		}
		
		return $woocommerce_params;
	}
	
	public static function replace_add_error_message( $error_message='' ) {
		global $wc_email_inquiry_quote_product_page;
		global $wc_email_inquiry_order_product_page;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		$product_names = '';
		$startpoint = strpos($error_message, '&quot;');
		if ($startpoint !== false) {
			$startpoint += 6;
			$endpoint = strrpos($error_message, '&quot;');
			if ( $endpoint !== false) {
				$product_names = substr($error_message, $startpoint , ($endpoint - $startpoint));	
			}
		}
		
		if (trim($product_names) != '') {
			$error_message = str_replace( $product_names, '%s', $error_message );
			if ( stristr( $error_message, __( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'woocommerce' ) ) !== false ) {
				if ( $apply_request_a_quote )
					$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_quote_product_page['quote_error_out_stock_message'] );
				elseif ( $apply_add_to_order )
					$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_order_product_page['order_error_out_stock_message'] );
					
				$error_message = str_replace( __( 'You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'woocommerce' ), $replace_text, $error_message );
			} elseif ( stristr( $error_message, __( 'You can only have 1 %s in your cart.', 'woocommerce' ) ) !== false ) {
				if ( $apply_request_a_quote )
					$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_quote_product_page['quote_error_product_already_message'] );
				elseif ( $apply_add_to_order )
					$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_order_product_page['order_error_product_already_message'] );
					
				$error_message = str_replace( __( 'You can only have 1 %s in your cart.', 'woocommerce' ), $replace_text, $error_message );
			} elseif ( stristr( $error_message, __( 'You cannot add another &quot;%s&quot; to your cart.', 'woocommerce' ) ) !== false ) {
				if ( $apply_request_a_quote )
					$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_quote_product_page['quote_error_product_already_message'] );
				elseif ( $apply_add_to_order )
					$replace_text = str_replace( '%s', $product_names, $wc_email_inquiry_order_product_page['order_error_product_already_message'] );
					
				$error_message = str_replace( __( 'You cannot add another &quot;%s&quot; to your cart.', 'woocommerce' ), $replace_text, $error_message );
			}
		}
		
		if ( $apply_request_a_quote ) {
			$error_message = str_replace( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_error_quantity_message'], $error_message );
			$error_message = str_replace( __( 'Please choose a product to add to your cart&hellip;', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_error_no_product_add_message'], $error_message );
			$error_message = str_replace( __( 'You already have this item in your cart.', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_error_product_already_message'], $error_message );
		} elseif ( $apply_add_to_order ) {
			$error_message = str_replace( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_error_quantity_message'], $error_message );
			$error_message = str_replace( __( 'Please choose a product to add to your cart&hellip;', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_error_no_product_add_message'], $error_message );
			$error_message = str_replace( __( 'You already have this item in your cart.', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_error_product_already_message'], $error_message );
		}

		if ( $apply_request_a_quote ) {
			$error_message = str_replace( __( 'View Cart &rarr;', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_view_button_text'], $error_message );
			$error_message = str_replace( __( 'Continue Shopping &rarr;', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_continue_button_text'], $error_message );
		} elseif ( $apply_add_to_order ) {
			$error_message = str_replace( __( 'View Cart &rarr;', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_view_button_text'], $error_message );
			$error_message = str_replace( __( 'Continue Shopping &rarr;', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_continue_button_text'], $error_message );
		}
		
		if ( version_compare( WC_VERSION, '2.1', '>=' ) ) {
			if ( $apply_request_a_quote ) {
				$error_message = str_replace( __( 'View Cart', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_view_button_text'], $error_message );
				$error_message = str_replace( __( 'Continue Shopping', 'woocommerce' ), $wc_email_inquiry_quote_product_page['quote_continue_button_text'], $error_message );
			} elseif ( $apply_add_to_order ) {
				$error_message = str_replace( __( 'View Cart', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_view_button_text'], $error_message );
				$error_message = str_replace( __( 'Continue Shopping', 'woocommerce' ), $wc_email_inquiry_order_product_page['order_continue_button_text'], $error_message );
			}
		}
		
		return $error_message;
	}
	
	public static function replace_all_message( $message = '') {
		
		if ( !in_array( basename ($_SERVER['PHP_SELF']), array('admin-ajax.php') ) && is_admin()) return $message;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote ) {
			$message = str_replace( __( 'Cart updated.', 'woocommerce' ), __( 'Quote updated.', 'wc_email_inquiry' ), $message );
		} elseif ( $apply_add_to_order ) {
			$message = str_replace( __( 'Cart updated.', 'woocommerce' ), __( 'Order updated.', 'wc_email_inquiry' ), $message );
		}
		
		/*if ( $apply_request_a_quote ) {
			$message = str_replace( ' '.__( 'cart', 'woocommerce' ), ' '.__( 'quote', 'wc_email_inquiry' ), $message );
			$message = str_replace( ' '.__( 'Cart', 'woocommerce' ), ' '.__( 'Quote', 'wc_email_inquiry' ), $message );
			$message = str_replace( ' '.__( 'CART', 'woocommerce' ), ' '.__( 'QUOTE', 'wc_email_inquiry' ), $message );
			$message = str_ireplace( ' '.__( 'cart', 'woocommerce' ), ' '.__( 'Quote', 'wc_email_inquiry' ), $message );
		} else*/if ( $apply_add_to_order ) {
			$message = str_replace( ' '.__( 'cart', 'woocommerce' ), ' '.__( 'order', 'wc_email_inquiry' ), $message );
			$message = str_replace( ' '.__( 'Cart', 'woocommerce' ), ' '.__( 'Order', 'wc_email_inquiry' ), $message );
			$message = str_replace( ' '.__( 'CART', 'woocommerce' ), ' '.__( 'ORDER', 'wc_email_inquiry' ), $message );
			$message = str_ireplace( ' '.__( 'cart', 'woocommerce' ), ' '.__( 'Order', 'wc_email_inquiry' ), $message );
		}
		
		return $message;
	}
	
	// For Product Page : END //
	
	
	// For Widget Cart : START //
	public static function replace_widget_cart_title( $widget_title = '', $instance=array(), $widget_id_base='' ) {
		global $wc_email_inquiry_quote_widget_cart;
		global $wc_email_inquiry_order_widget_cart;
		
		if ( !in_array( basename ($_SERVER['PHP_SELF']), array('admin-ajax.php') ) && is_admin() ) return $widget_title;
		
		if ($widget_id_base != 'shopping_cart' && $widget_id_base != 'woocommerce_widget_cart') return $widget_title;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote )
			$widget_title = $wc_email_inquiry_quote_widget_cart['quote_widget_cart_title'];
		elseif ( $apply_add_to_order )
			$widget_title = $wc_email_inquiry_order_widget_cart['order_widget_cart_title'];
		
		return $widget_title;
	}
	
	public static function replace_all_content_widget_cart( $cart_contents = array() ) {
		global $wc_email_inquiry_quote_widget_cart;
		global $wc_email_inquiry_order_widget_cart;
				
		if (!is_array($cart_contents) || count($cart_contents) < 1) return $cart_contents;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
				
		foreach ($cart_contents as $key => $content ) {
			if ( stristr( $content, __( 'No products in the cart.', 'woocommerce' ) ) !== false ) {
				if ( $apply_request_a_quote )
					$content = str_replace( __( 'No products in the cart.', 'woocommerce' ), $wc_email_inquiry_quote_widget_cart['quote_widget_no_product'], $content );
				elseif ( $apply_add_to_order )
					$content = str_replace( __( 'No products in the cart.', 'woocommerce' ), $wc_email_inquiry_order_widget_cart['order_widget_no_product'], $content );
			}
			if ( stristr( $content, __( 'Subtotal', 'woocommerce' ) ) !== false ) {
				if ( $apply_request_a_quote )
					$content = str_replace( __( 'Subtotal', 'woocommerce' ).':', '', $content );
			}
			if ( stristr( $content, __( 'View Cart &rarr;', 'woocommerce' ) ) !== false ) {
				if ( $apply_request_a_quote )
					$content = str_replace( __( 'View Cart &rarr;', 'woocommerce' ), $wc_email_inquiry_quote_widget_cart['quote_widget_view_cart_button'], $content );
				elseif ( $apply_add_to_order )
					$content = str_replace( __( 'View Cart &rarr;', 'woocommerce' ), $wc_email_inquiry_order_widget_cart['order_widget_view_cart_button'], $content );
			}
			if ( stristr( $content, __( 'Checkout &rarr;', 'woocommerce' ) ) !== false ) {
				if ( $apply_request_a_quote )
					$content = str_replace( __( 'Checkout &rarr;', 'woocommerce' ), $wc_email_inquiry_quote_widget_cart['quote_widget_checkout_button'], $content );
				elseif ( $apply_add_to_order )
					$content = str_replace( __( 'Checkout &rarr;', 'woocommerce' ), $wc_email_inquiry_order_widget_cart['order_widget_checkout_button'], $content );
			}
			
			if ( version_compare( WC_VERSION, '2.1', '>=' ) ) {
				if ( stristr( $content, __( 'View Cart', 'woocommerce' ) ) !== false ) {
					if ( $apply_request_a_quote )
						$content = str_replace( __( 'View Cart', 'woocommerce' ), $wc_email_inquiry_quote_widget_cart['quote_widget_view_cart_button'], $content );
					elseif ( $apply_add_to_order )
						$content = str_replace( __( 'View Cart', 'woocommerce' ), $wc_email_inquiry_order_widget_cart['order_widget_view_cart_button'], $content );
				}
				if ( stristr( $content, __( 'Checkout', 'woocommerce' ) ) !== false ) {
					if ( $apply_request_a_quote )
						$content = str_replace( __( 'Checkout', 'woocommerce' ), $wc_email_inquiry_quote_widget_cart['quote_widget_checkout_button'], $content );
					elseif ( $apply_add_to_order )
						$content = str_replace( __( 'Checkout', 'woocommerce' ), $wc_email_inquiry_order_widget_cart['order_widget_checkout_button'], $content );
				}
			}
			
			$cart_contents[$key] = $content;
		}
		
		return $cart_contents;
	}
	
	public static function hide_mini_cart_subtotal( $cart_subtotal='', $compound=false, $cart_object ) {
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		
		if ( $apply_request_a_quote ) return '';
		
		return $cart_subtotal;
	}
	
	public static function hide_mini_cart_contents_total( $cart_contents_total ) {
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		
		if ( $apply_request_a_quote ) return '';
		
		return $cart_contents_total;
	}
	
	// For Widget Cart : END //
	
	
	// For Checkout Page : START //
	
	public static function replace_order_button_text( $button_text = '') {
		global $wc_email_inquiry_quote_checkout_page;
		global $wc_email_inquiry_order_checkout_page;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote ) $button_text = $wc_email_inquiry_quote_checkout_page['quote_place_order_button'];
		elseif ( $apply_add_to_order ) $button_text = $wc_email_inquiry_order_checkout_page['order_place_order_button'];
		
		return $button_text;
	}
	
	public static function replace_checkout_fields_order_comments( $checkout_fields = array() ) {
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if(!is_array($checkout_fields) || !isset($checkout_fields['order']['order_comments']) ) return $checkout_fields;	
		if ( $apply_request_a_quote ) { 
			$checkout_fields['order']['order_comments']['label'] = wc_ei_ict_t__( 'Quote Mode Checkout Page - Comment Label', __( 'Quote Notes', 'wc_email_inquiry' ) );
			$checkout_fields['order']['order_comments']['placeholder'] = wc_ei_ict_t__( 'Quote Mode Checkout Page - Comment Placeholder', __('Notes about your quote, e.g. special notes for delivery.', 'wc_email_inquiry' ) );
		}
		
		return $checkout_fields;
	}
	
	public static function make_must_create_account_to_false() {
		global $woocommerce;
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote || $apply_add_to_order ) {
			// Get checkout object
			if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
				$checkout = $woocommerce->checkout();
			} else {
				$checkout = WC()->checkout();
			}
			$checkout->must_create_account = false;
		}
	}
	
	public static function checkout_validation( $posted=array() ) {
		global $woocommerce;
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( !is_user_logged_in() && ( $apply_request_a_quote || $apply_add_to_order ) ) {
			// Check the e-mail address
			$user_id = email_exists( $posted['billing_email'] );
			if ( $user_id ) {
				if ( !WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote_by_userid( $user_id ) ) {
					if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
						$woocommerce->add_error( __( 'An account is already registered with your email address. Please login.', 'woocommerce' ) );
					} else {
						wc_add_notice( __( 'An account is already registered with your email address. Please login.', 'woocommerce' ), 'error' );
					}
				} elseif ( !WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order_by_userid( $user_id ) ) {
					if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
						$woocommerce->add_error( __( 'An account is already registered with your email address. Please login.', 'woocommerce' ) );
					} else {
						wc_add_notice( __( 'An account is already registered with your email address. Please login.', 'woocommerce' ), 'error' );
					}
				}
			}
		}
	}
	
	public static function auto_create_account( $order_id, $posted=array() ) {
		
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_enable_guest_checkout() ) return;
		
		global $woocommerce;
		global $wc_email_inquiry_order_new_account_email_settings;
		global $wc_email_inquiry_rules_roles_settings;
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( !is_user_logged_in() && ( $apply_request_a_quote || $apply_add_to_order ) ) {
			$customer_id = false;
			// Check the e-mail address
			$user_id = email_exists( $posted['billing_email'] );
			if ( $user_id ) {
				if ( WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote_by_userid( $user_id ) )
					$customer_id = $user_id;
				elseif ( WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order_by_userid( $user_id ) )
					$customer_id = $user_id;
			} else {
				$user_pass = wp_generate_password( 12, false );
				
				if ( $apply_request_a_quote ) {
					if ( $apply_auto_quote ) $new_account_role = 'auto_quote';
					else $new_account_role = 'manual_quote';
				} else {
					$new_account_role = 'customer';
					if ( isset($wc_email_inquiry_order_new_account_email_settings['order_new_account_role']) && $wc_email_inquiry_order_new_account_email_settings['order_new_account_role'] != '' ) $new_account_role = $wc_email_inquiry_order_new_account_email_settings['order_new_account_role'];
				}
				
				$new_customer_data = array(
					'user_login' => $posted['billing_email'],
					'user_pass'  => $user_pass,
					'user_email' => $posted['billing_email'],
					'role'       => $new_account_role
				);
				
				$new_customer_data = apply_filters( 'woocommerce_new_customer_data', $new_customer_data );
				$new_customer_data['role'] = $new_account_role;
				
				$customer_id = wp_insert_user( $new_customer_data );
				
				 // Set the global user object
				$current_user = get_user_by ( 'id', $customer_id );
				
				if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
					// Action
					do_action( 'woocommerce_created_customer', $customer_id );
				}

				// send the user a confirmation and their login details
				$email_args = array(
					'blogname'			=> get_option('blogname'),
					'first_name'		=> $posted['billing_first_name'],
					'last_name'			=> $posted['billing_last_name'],
					'customer_email'	=> $posted['billing_email'],
					'username'			=> $posted['billing_email'],
					'password'			=> $user_pass,
				);
				WC_Email_Inquiry_Quote_Order_Functions::create_user_email( $email_args );

				// set the WP login cookie
				$secure_cookie = is_ssl() ? true : false;
				wp_set_auth_cookie( $customer_id, true, $secure_cookie );
			}
			
			if ($customer_id != false) {
				if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
					$checkout = $woocommerce->checkout();
				} else {
					$checkout = WC()->checkout();
				}
				
				// Store user data
				if ( $checkout->checkout_fields['billing'] ) {
					foreach ( $checkout->checkout_fields['billing'] as $key => $field ) {

						// User
						if ( ! empty( $checkout->posted[ $key ] ) ) {
							update_user_meta( $customer_id, $key, $checkout->posted[ $key ] );
		
							// Special fields
							switch ( $key ) {
								case "billing_email" :
									if ( ! email_exists( $checkout->posted[ $key ] ) )
										wp_update_user( array ( 'ID' => $customer_id, 'user_email' => $checkout->posted[ $key ] ) ) ;
								break;
								case "billing_first_name" :
									wp_update_user( array ( 'ID' => $customer_id, 'first_name' => $checkout->posted[ $key ] ) ) ;
								break;
								case "billing_last_name" :
									wp_update_user( array ( 'ID' => $customer_id, 'last_name' => $checkout->posted[ $key ] ) ) ;
								break;
							}
						}
					}
				}
				
				if ( $checkout->checkout_fields['shipping'] && ( ( version_compare( $woocommerce_db_version, '2.1', '<' ) && $woocommerce->cart->needs_shipping() ) || ( version_compare( $woocommerce_db_version, '2.1', '>=' ) && WC()->cart->needs_shipping() ) || get_option('woocommerce_require_shipping_address') == 'yes' ) ) {
					foreach ( $checkout->checkout_fields['shipping'] as $key => $field ) {
						$postvalue = false;
		
						if ( version_compare( $woocommerce_db_version, '2.1', '<' ) && $checkout->posted['shiptobilling'] ) {
							if ( isset( $checkout->posted[ str_replace( 'shipping_', 'billing_', $key ) ] ) ) {
								$postvalue = $checkout->posted[ str_replace( 'shipping_', 'billing_', $key ) ];
							}
						} elseif ( version_compare( $woocommerce_db_version, '2.1', '>=' ) && $checkout->posted['ship_to_different_address'] == false ) {
							if ( isset( $checkout->posted[ str_replace( 'shipping_', 'billing_', $key ) ] ) ) {
								$postvalue = $checkout->posted[ str_replace( 'shipping_', 'billing_', $key ) ];
							}
						} else {
							$postvalue = $checkout->posted[ $key ];
						}
		
						// User
						if ( $postvalue && $customer_id )
							update_user_meta( $customer_id, $key, $postvalue );
					}
				}
				
				do_action( 'woocommerce_checkout_update_user_meta', $customer_id, $posted );
				
				update_post_meta( $order_id, '_customer_user', 			absint( $customer_id ) );
			}
		}
	}
		
	// For Checkout Page : END //
	
	// Quotes and Orders Shipping Options: START //
	
	public static function show_hide_shipping_options_before() {
		
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_hide_shipping_options() ) ob_start();
	}
	
	public static function show_hide_shipping_options_after() {
		
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_hide_shipping_options() ) ob_end_clean();
	}
	
	// Quotes and Orders Shipping Options: END //
	
	
	// Quotes and Orders Shipping Prices: START //
	
	public static function show_hide_shipping_prices_on_checkout_page( $method_label, $method ) {
		
		if ( WC_Email_Inquiry_Quote_Order_Functions::check_hide_shipping_prices() ) $method_label = $method->label;
				
		return $method_label;
	}
	
	public static function show_hide_shipping_prices_after_submitted( $shipping, $order_object ) {
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		
		$sent_quote = get_post_meta( $order_object->id, '_wc_email_inquiry_sent_quote', true );		
		if ( ! $apply_auto_quote && WC_Email_Inquiry_Quote_Order_Functions::check_hide_shipping_prices() && $order_object->status != 'pending' && ! $sent_quote ) {
			if ( $order_object->get_shipping_method() ) {
				$shipping = $order_object->get_shipping_method();
			} else {
				$shipping = __( 'Free!', 'woocommerce' );
			}
		}
				
		return $shipping;
	}
	
	// Quotes and Orders Shipping Prices: END //
	
	
	// Quotes and Orders replace Shipping methods template for woo 2.0.20: START //
	
	public static function change_shipping_methods_template_before() {
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote ) {
			ob_start();
		} elseif ( $apply_add_to_order ) {
			ob_start();
		}
	}
	
	public static function change_shipping_methods_template_after() {
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( $apply_request_a_quote ) {
			global $woocommerce;
			$available_methods = $woocommerce->shipping->get_available_shipping_methods();
			
			global $wc_email_inquiry_quote_checkout_page;
			global $wc_email_inquiry_quote_checkout_shipping_help_text;
			ob_end_clean();
		?>
        		<tr class="shipping">
					<th><?php 
						if ( trim( $wc_email_inquiry_quote_checkout_page['shipping_handling_title'] ) == '' ) 
							_e( 'Shipping', 'woocommerce' );
						else
							echo $wc_email_inquiry_quote_checkout_page['shipping_handling_title']; 
						?>
                    </th>
					<td>
                    	<strong><?php echo $wc_email_inquiry_quote_checkout_page['shipping_options_title']; ?></strong>
						<?php woocommerce_get_template( 'cart/shipping-methods.php', array( 'available_methods' => $available_methods ) ); ?>
                    </td>
				</tr>
                <tr>
                	<td colspan="2"><?php echo wpautop(wptexturize( $wc_email_inquiry_quote_checkout_shipping_help_text )); ?></td>
                </tr>
		<?php	
		} elseif ( $apply_add_to_order ) {
			global $woocommerce;
			$available_methods = $woocommerce->shipping->get_available_shipping_methods();
			
			global $wc_email_inquiry_order_checkout_page;
			global $wc_email_inquiry_order_checkout_shipping_help_text;
			ob_end_clean();
		?>
        		<tr class="shipping">
					<th><?php 
						if ( trim( $wc_email_inquiry_order_checkout_page['shipping_handling_title'] ) == '' ) 
							_e( 'Shipping', 'woocommerce' );
						else
							echo $wc_email_inquiry_order_checkout_page['shipping_handling_title']; 
						?>
                    </th>
					<td>
                    	<strong><?php echo $wc_email_inquiry_order_checkout_page['shipping_options_title']; ?></strong>
						<?php woocommerce_get_template( 'cart/shipping-methods.php', array( 'available_methods' => $available_methods ) ); ?>
                    </td>
				</tr>
                <tr>
                	<td colspan="2"><?php echo wpautop(wptexturize( $wc_email_inquiry_order_checkout_shipping_help_text )); ?></td>
                </tr>
		<?php
		}
	}
	
	// Quotes and Orders replace Shipping methods template for woo 2.0.20: END //
	
	// Quotes and Orders Gateways : START //
	
	public static function custom_gateway_init() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
		
		require_once( WC_EMAIL_INQUIRY_FILE_PATH.'/classes/gateways/class-gateway-quotes.php' );
		require_once( WC_EMAIL_INQUIRY_FILE_PATH.'/classes/gateways/class-gateway-orders.php' );
		add_filter( 'woocommerce_payment_gateways', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'add_custom_gateway' ) );
	}
	
	public static function add_custom_gateway( $methods = array()) {
		$methods[] = 'WC_Email_Inquiry_Gateway_Quotes';
		$methods[] = 'WC_Email_Inquiry_Gateway_Orders';
		
		return $methods;
	}
	
	public static function show_payment_gateways_for_role( $available_gateways = array() ) {
		global $post;
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		$new_available_gateways = array();
		$payment_gateways_class = new WC_Payment_Gateways();
		$have_custom_gateway = false;
		
		if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
			$is_pay_page = ( ( is_page() && $post->ID == woocommerce_get_page_id('pay') ) ? true : false );
		} else {
			$is_pay_page = is_checkout_pay_page();
		}
		if ( $apply_request_a_quote ) {
			if ( isset( $_POST['woocommerce_pay'] ) || ( is_page() && $is_pay_page ) ) return $available_gateways; 
			foreach ( $payment_gateways_class->payment_gateways() as $gateway ) :
				if ($gateway->id == 'quote_mode') {
					$new_available_gateways[$gateway->id] = $gateway;
					$new_available_gateways[$gateway->id]->chosen = true;
					$have_custom_gateway = true;
					break;
				}
			endforeach;
		} elseif ( $apply_add_to_order ) {
			if ( isset( $_POST['woocommerce_pay'] ) || ( is_page() && $is_pay_page ) ) return $available_gateways; 
			foreach ( $payment_gateways_class->payment_gateways() as $gateway ) :
				if ($gateway->id == 'order_mode') {
					$new_available_gateways[$gateway->id] = $gateway;
					$new_available_gateways[$gateway->id]->chosen = true;
					$have_custom_gateway = true;
					break;
				}
			endforeach;
		}
		
		if ($have_custom_gateway) $available_gateways = $new_available_gateways;
		
		return $available_gateways;
	}
	
	public static function make_available_shipping_method_for_role( $available_methods = array() ) {
		global $wc_email_inquiry_rules_roles_settings;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		$new_available_methods = array();
		$wc_shipping_class = new WC_Shipping();
		$have_custom_gateway = false;
		
		$package = array();
		$package['rates'] = array();
		if ( $apply_add_to_order ) {
			foreach ( $wc_shipping_class->load_shipping_methods( $package ) as $id => $shipping_method ) {
				if ($id == 'free_shipping') {
					// Reset Rates
					$shipping_method->rates = array();

					// Calculate Shipping for package
					$shipping_method->calculate_shipping( $package );

					// Place rates in package array
					if ( ! empty( $shipping_method->rates ) && is_array( $shipping_method->rates ) ) {
						foreach ( $shipping_method->rates as $rate ) {
							$new_available_methods[$rate->id] = $rate;
							$have_custom_gateway = true;
							break;
						}
					}
				}
			}
		}
		
		if ($have_custom_gateway) $available_methods = $new_available_methods;
				
		return $available_methods;
	}
	
	public static function make_available_shipping_method_for_role_woo_21( $package_rates, $package ) {
		return $package_rates;
		
		global $wc_email_inquiry_rules_roles_settings;
		
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		$new_available_methods = array();
		$wc_shipping_class = new WC_Shipping();
		$have_custom_gateway = false;
		
		$package = array();
		$package['rates'] = array();
		if ( $apply_add_to_order ) {
			foreach ( $wc_shipping_class->load_shipping_methods( $package ) as $id => $shipping_method ) {
				if ($id == 'free_shipping') {
					// Reset Rates
					$shipping_method->rates = array();

					// Calculate Shipping for package
					$shipping_method->calculate_shipping( $package );

					// Place rates in package array
					if ( ! empty( $shipping_method->rates ) && is_array( $shipping_method->rates ) ) {
						foreach ( $shipping_method->rates as $rate ) {
							$new_available_methods[$rate->id] = $rate;
							$have_custom_gateway = true;
							break;
						}
					}
				}
			}
		}
		
		if ($have_custom_gateway) $available_methods = $new_available_methods;
				
		return $available_methods;
	}
	
	// Quotes and Orders Gateways : END //
	
	
	// Qyotes and Orders Email : START //

	public static function email_format_string( $string, $the_order ) {
		$find                    = array();
		$find['blogname']        = '{blogname}';
		$find['site-title']      = '{site_title}';
		$find['order-date']      = '{order_date}';
		$find['order-number']    = '{order_number}';

		$replace                 = array();
		$replace['blogname']     = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$replace['site-title']   = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$replace['order-date']   = date_i18n( wc_date_format(), strtotime( $the_order->order_date ) );
		$replace['order-number'] = $the_order->get_order_number();

		return str_replace( $find, $replace, $string );
	}

	public static function quote_change_recipient_new_order( $recipient, $the_order ) {
		if ( $the_order ) {
			$email_inquiry_option_type = get_post_meta( $the_order->id, '_email_inquiry_option_type', true );
			if ( $the_order->has_status( 'quote' ) || ( 'auto_quote' == $email_inquiry_option_type && $the_order->has_status( 'pending' ) ) ) {
				global $wc_email_inquiry_quote_new_quote_request_email_settings;

				$recipient = $wc_email_inquiry_quote_new_quote_request_email_settings['email_recipient'];
			}
		}

		return $recipient;
	}

	public static function quote_change_subject_new_order( $subject, $the_order ) {
		if ( $the_order ) {
			$email_inquiry_option_type = get_post_meta( $the_order->id, '_email_inquiry_option_type', true );
			if ( $the_order->has_status( 'quote' ) || ( 'auto_quote' == $email_inquiry_option_type && $the_order->has_status( 'pending' ) ) ) {
				global $wc_email_inquiry_quote_new_quote_request_email_settings;

				$subject = $wc_email_inquiry_quote_new_quote_request_email_settings['email_subject'];
				$subject = self::email_format_string( $subject, $the_order );
			}
		}

		return $subject;
	}

	public static function quote_change_heading_new_order( $heading, $the_order ) {
		if ( $the_order ) {
			$email_inquiry_option_type = get_post_meta( $the_order->id, '_email_inquiry_option_type', true );
			if ( $the_order->has_status( 'quote' ) || ( 'auto_quote' == $email_inquiry_option_type && $the_order->has_status( 'pending' ) ) ) {
				global $wc_email_inquiry_quote_new_quote_request_email_settings;

				$heading = $wc_email_inquiry_quote_new_quote_request_email_settings['email_heading'];
				$heading = self::email_format_string( $heading, $the_order );
			}
		}

		return $heading;
	}
	
	public static function custom_email_init() {
		add_action( 'woocommerce_order_status_pending_to_quote', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'send_transactional_email') );
	}
	
	public static function send_transactional_email( $order_id ) {
		global $woocommerce;
		
		$woocommerce_db_version = get_option( 'woocommerce_db_version', null );
		
		if ( version_compare( $woocommerce_db_version, '2.1', '<' ) ) {
			$woocommerce->mailer();
		} else {
			WC()->mailer();
		}
		do_action( 'woocommerce_order_status_pending_to_quote_notification', $order_id );
	}
	
	public static function custom_email_load() {
		add_filter( 'woocommerce_email_classes', array( 'WC_Email_Inquiry_Quote_Order_Hook_Filter', 'add_custom_emails' ) );
	}
	
	public static function add_custom_emails( $emails = array()) {
		if ( ! class_exists( 'WC_Email' ) ) return;
		
		include_once( WC_EMAIL_INQUIRY_FILE_PATH.'/classes/emails/class-email-customer-processing-quote.php' );
		include_once( WC_EMAIL_INQUIRY_FILE_PATH.'/classes/emails/class-email-customer-pending-order.php' );
		$emails['WC_Email_Inquiry_Customer_Processing_Quote'] = new WC_Email_Inquiry_Customer_Processing_Quote();
		$emails['WC_Email_Inquiry_Customer_Pending_Order'] = new WC_Email_Inquiry_Customer_Pending_Order();
		
		return $emails;
	}
	
	public static function hide_price_in_myaccount_page( $formatted_total = '', $the_order ) {
		$apply_request_a_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_request_a_quote();
		$apply_manual_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_manual_quote();
		$apply_auto_quote = WC_Email_Inquiry_Quote_Order_Functions::check_apply_auto_quote();
		$apply_add_to_order = WC_Email_Inquiry_Quote_Order_Functions::check_apply_add_to_order();
		
		if ( !in_array( basename ($_SERVER['PHP_SELF']), array('admin-ajax.php') ) && is_admin() ) return $formatted_total;
		
		$sent_quote = get_post_meta( $the_order->id, '_wc_email_inquiry_sent_quote', true );
		if ( version_compare( WC_VERSION, '2.2', '<' ) ) {
			if ( $the_order->status == 'quote' && $apply_manual_quote && ! $sent_quote ) return '';
		} else {
			if ( $the_order->has_status( 'quote' ) && $apply_manual_quote && ! $sent_quote ) return '';
		}
		
		return $formatted_total; 
	}
	
	public static function add_actions_for_quote( $actions=array(), $the_order ) {
		global $post;
		if ( version_compare( WC_VERSION, '2.2', '<' ) ) {
			if ( $the_order->status != 'quote') return $actions;
		} else {
			if ( ! $the_order->has_status( 'quote' ) ) return $actions;
		}
		
		if ( ( version_compare( WC_VERSION, '2.2', '<' ) && in_array( $the_order->status, array( 'quote' ) ) ) || ( version_compare( WC_VERSION, '2.2', '>=' ) && $the_order->has_status( array( 'quote' ) ) ) ) {
			$actions['processing'] = array(
				'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce-mark-order-processing&order_id=' . $post->ID, 'relative' ), 'woocommerce-mark-order-processing' ),
				'name' 		=> __( 'Processing', 'woocommerce' ),
				'action' 	=> "processing"
			);
			
			$actions['complete'] = array(
				'url' 		=> wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce-mark-order-complete&order_id=' . $post->ID, 'relative' ), 'woocommerce-mark-order-complete' ),
				'name' 		=> __( 'Complete', 'woocommerce' ),
				'action' 	=> "complete"
			);
		}
		
		unset($actions['view']);
		$actions['view'] = array(
			'url' 		=> admin_url( 'post.php?post=' . $post->ID . '&action=edit', 'relative' ),
			'name' 		=> __( 'View', 'woocommerce' ),
			'action' 	=> "view"
		);
			
		return $actions;
	}
	
	
	// Qyotes and Orders Email : END //
	
	
	// Quotes and Orders Question mark : START //
	
	public static function details_add_question_mark_above() {
		$question_mark = '<div class="quote_mode_mark" title="How the Add to Quote">What is this?</div>';
		echo $question_mark;
	}
	
	public static function frontend_script_include() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-tooltip');
	?>
<script type="text/javascript">
(function($){
	$(function(){
		$(".quote_mode_mark").tooltip();
	});
})(jQuery);
</script>
    <?php
	}
	
	// Quotes and Orders Question mark : END //
} 
?>
