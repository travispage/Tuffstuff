<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Customer Processing Quote Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		WC_Email_Inquiry_Customer_Processing_Quote
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		a3rev
 * @extends 	WC_Email
 */
class WC_Email_Inquiry_Customer_Processing_Quote extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id 				= 'customer_processing_quote';
		$this->customer_email   = true;
		$this->title 			= __( 'Processing Quote', 'wc_email_inquiry' );
		$this->description		= __( 'This is the Quote details sent to the customer after they complete and send their Quote request.', 'wc_email_inquiry' );

		$this->heading 			= __( 'Thank you for requested on a quote', 'wc_email_inquiry' );
		if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
			$this->subject      = __( 'Your {blogname} quote receipt from {order_date}', 'wc_email_inquiry' );
		} else {
			$this->subject      = __( 'Your {site_title} quote receipt from {order_date}', 'wc_email_inquiry' );
		}
		
		$this->email_message 	= $this->get_option( 'email_message', __( 'Add a custom message here for your customers for Manual Quotes. Go to WooCommerce | Settings | Emails (tab), Processing Quote sub menu.', 'wc_email_inquiry' ) );
		
		$this->email_message_auto_quote 	= $this->get_option( 'email_message_auto_quote', __( 'Add a custom message here for your customers for Auto Quotes. Go to WooCommerce | Settings | Emails (tab), Processing Quote sub menu.', 'wc_email_inquiry' ) );

		$this->template_html 	= 'emails/customer-processing-quote.php';
		$this->template_plain 	= 'emails/plain/customer-processing-quote.php';

		if ( version_compare( WC_VERSION, '2.5.0', '>=' ) ) {
			$this->template_html 	= 'emails/customer-processing-quote_2.5.php';
			$this->template_plain 	= 'emails/plain/customer-processing-quote_2.5.php';
		} elseif ( version_compare( WC_VERSION, '2.4.0', '>=' ) ) {
			$this->template_html 	= 'emails/customer-processing-quote_2.4.php';
			$this->template_plain 	= 'emails/plain/customer-processing-quote_2.1.php';
		} elseif ( version_compare( WC_VERSION, '2.0.0', '>' ) ) {
			$this->template_html 	= 'emails/customer-processing-quote_2.1.php';
			$this->template_plain 	= 'emails/plain/customer-processing-quote_2.1.php';
		}

		// Triggers for this email
		add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_pending_to_quote_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $order_id ) {

		if ( $order_id ) {
			$this->object 		= wc_get_order( $order_id );

			// Fixed don't resend quote email when payment is Mollie
			$isMolliePayment = get_post_meta( $order_id,'_is_mollie_payment', true );
			if ( $isMolliePayment && 'processing' == $this->object->status ) {
				return;
			}

			$this->recipient	= $this->object->billing_email;

			$this->find['order-date'] = '{order_date}';
			if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
				$this->replace['order-date'] = date_i18n( woocommerce_date_format(), strtotime( $this->object->order_date ) );
			} else {
				$this->replace['order-date'] = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
			}

			$this->find['order-number'] = '{order_number}';
			$this->replace['order-number'] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;
			
		$email_inquiry_option_type = get_post_meta($order_id, '_email_inquiry_option_type', true);
		
		if ($email_inquiry_option_type == 'add_to_order') return;

		$this->send( $this->get_recipient(), $this->format_string( $this->get_subject() ), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}
	
	function get_email_message() {
		return apply_filters( 'woocommerce_email_quote_message_' . $this->id, $this->format_string( $this->email_message ), $this->object );
	}
	
	function get_email_message_auto_quote() {
		return apply_filters( 'woocommerce_email_quote_message_' . $this->id, $this->format_string( $this->email_message_auto_quote ), $this->object );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		if ( function_exists( 'wc_get_template_html' ) ) {
			return wc_get_template_html( $this->template_html, array(
				'order' 		=> $this->object,
				'email_heading' => $this->get_heading(),
				'email_message' => $this->get_email_message(),
				'email_message_auto_quote' => $this->get_email_message_auto_quote(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'			=> $this
			) );
		} else {
			ob_start();
			if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
				woocommerce_get_template( $this->template_html, array(
					'order' 		=> $this->object,
					'email_heading' => $this->get_heading(),
					'email_message' => $this->get_email_message(),
					'email_message_auto_quote' => $this->get_email_message_auto_quote()
				) );
			} else {
				wc_get_template( $this->template_html, array(
					'order' 		=> $this->object,
					'email_heading' => $this->get_heading(),
					'email_message' => $this->get_email_message(),
					'email_message_auto_quote' => $this->get_email_message_auto_quote(),
					'sent_to_admin' => false,
					'plain_text'    => false
				) );
			}
			return ob_get_clean();
		}
	}
	
	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		if ( function_exists( 'wc_get_template_html' ) ) {
			return wc_get_template_html( $this->template_plain, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'email_message' => $this->get_email_message(),
				'email_message_auto_quote' => $this->get_email_message_auto_quote(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'			=> $this
			) );
		} else {
			ob_start();
			if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
				woocommerce_get_template( $this->template_plain, array(
					'order' 		=> $this->object,
					'email_heading' => $this->get_heading(),
					'email_message' => $this->get_email_message(),
					'email_message_auto_quote' => $this->get_email_message_auto_quote()
				) );
			} else {
				wc_get_template( $this->template_plain, array(
					'order'         => $this->object,
					'email_heading' => $this->get_heading(),
					'email_message' => $this->get_email_message(),
					'email_message_auto_quote' => $this->get_email_message_auto_quote(),
					'sent_to_admin' => false,
					'plain_text'    => true
				) );
			}
			return ob_get_clean();
		}
	}
	
	/**
     * Initialise Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {
    	$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable this email notification', 'woocommerce' ),
				'default' 		=> 'yes'
			),
			'subject' => array(
				'title' 		=> __( 'Subject', 'woocommerce' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>.', 'wc_email_inquiry' ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> '',
				'desc_tip'      => true
			),
			'heading' => array(
				'title' 		=> __( 'Email Heading', 'woocommerce' ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Defaults to <code>%s</code>.', 'wc_email_inquiry' ), $this->heading ),
				'placeholder' 	=> '',
				'default' 		=> '',
				'desc_tip'      => true
			),
			'email_message' => array(
				'title' 		=> __( 'Manual Quotes Email Message', 'woocommerce' ),
				'type' 			=> 'textarea',
				'description' 	=> __( 'Message shows at the top of the customers Manaul Quote received email.', 'wc_email_inquiry' ),
				'placeholder' 	=> '',
				'default' 		=> $this->email_message,
				'desc_tip'      => true
			),
			'email_message_auto_quote' => array(
				'title' 		=> __( 'Auto Quote Email Message', 'woocommerce' ),
				'type' 			=> 'textarea',
				'description' 	=> __( 'Message shows at the top of the customers Auto Quote email.', 'wc_email_inquiry' ),
				'placeholder' 	=> '',
				'default' 		=> $this->email_message_auto_quote,
				'desc_tip'      => true
			),
			'email_type' => array(
				'title' 		=> __( 'Email type', 'woocommerce' ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose which format of email to send.', 'woocommerce' ),
				'default' 		=> 'html',
				'class'			=> 'email_type wc-enhanced-select',
				'options'		=> $this->get_email_type_options(),
				'desc_tip'      => true
			)
		);
    }
	
	/**
	 * Admin Options
	 *
	 * Setup the gateway settings screen.
	 * Override this in your gateway.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	function admin_options() {
		global $woocommerce;

		// Handle any actions
		if ( ! empty( $this->template_html ) || ! empty( $this->template_plain ) ) {

			if ( ! empty( $_GET['move_template'] ) && ( $template = esc_attr( basename( $_GET['move_template'] ) ) ) ) {
				if ( ! empty( $this->$template ) ) {
					if (  wp_mkdir_p( dirname( get_stylesheet_directory() . '/woocommerce/' . $this->$template ) ) && ! file_exists( get_stylesheet_directory() . '/woocommerce/' . $this->$template ) ) {
						copy( WC_EMAIL_INQUIRY_TEMPLATE_PATH . '/' . $this->$template, get_stylesheet_directory() . '/woocommerce/' . $this->$template );
						echo '<div class="updated fade"><p>' . __( 'Template file copied to theme.', 'woocommerce' ) . '</p></div>';
					}
				}
			}

			if ( ! empty( $_GET['delete_template'] ) && ( $template = esc_attr( basename( $_GET['delete_template'] ) ) ) ) {
				if ( ! empty( $this->$template ) ) {
					if ( file_exists( get_stylesheet_directory() . '/woocommerce/' . $this->$template ) ) {
						unlink( get_stylesheet_directory() . '/woocommerce/' . $this->$template );
						echo '<div class="updated fade"><p>' . __( 'Template file deleted from theme.', 'woocommerce' ) . '</p></div>';
					}
				}
			}

		}

		?>
		<h3><?php echo ( ! empty( $this->title ) ) ? $this->title : __( 'Settings','woocommerce' ) ; ?></h3>

		<?php echo ( ! empty( $this->description ) ) ? wpautop( $this->description ) : ''; ?>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table>

		<?php if ( ! empty( $this->template_html ) || ! empty( $this->template_plain ) ) { ?>
			<div id="template">
			<?php
				$templates = array(
					'template_html' 	=> __( 'HTML template', 'woocommerce' ),
					'template_plain' 	=> __( 'Plain text template', 'woocommerce' )
				);
				foreach ( $templates as $template => $title ) :
					if ( empty( $this->$template ) )
						continue;

					$local_file = get_stylesheet_directory() . '/woocommerce/' . $this->$template;
					$core_file 	= WC_EMAIL_INQUIRY_TEMPLATE_PATH . '/' . $this->$template;
					?>
					<div class="template <?php echo $template; ?>">

						<h4><?php echo wp_kses_post( $title ); ?></h4>

						<?php if ( file_exists( $local_file ) ) : ?>

							<p>
								<a href="#" class="button toggle_editor"></a>

								<?php if ( is_writable( $local_file ) ) : ?>
									<?php // fixed for 4.1.2 ?>
									<a href="<?php echo remove_query_arg( array( 'move_template', 'saved' ), esc_url( add_query_arg( 'delete_template', $template ) ) ); ?>" class="delete_template button"><?php _e( 'Delete template file', 'woocommerce' ); ?></a>
								<?php endif; ?>

								<?php printf( __( 'This template has been overridden by your theme and can be found in: <code>%s</code>.', 'woocommerce' ), 'yourtheme/woocommerce/' . $this->$template ); ?>
							</p>

							<div class="editor" style="display:none">

								<textarea class="code" cols="25" rows="20" <?php if ( ! is_writable( $local_file ) ) : ?>readonly="readonly" disabled="disabled"<?php else : ?>data-name="<?php echo $template . '_code'; ?>"<?php endif; ?>><?php echo file_get_contents( $local_file ); ?></textarea>

							</div>

						<?php elseif ( file_exists( $core_file ) ) : ?>

							<p>
								<a href="#" class="button toggle_editor"></a>

								<?php if ( ( is_dir( get_stylesheet_directory() . '/woocommerce/emails/' ) && is_writable( get_stylesheet_directory() . '/woocommerce/emails/' ) ) || is_writable( get_stylesheet_directory() ) ) : ?>
									<?php // fixed for 4.1.2 ?>
									<a href="<?php echo remove_query_arg( array( 'delete_template', 'saved' ), esc_url( add_query_arg( 'move_template', $template ) ) ); ?>" class="button"><?php _e( 'Copy file to theme', 'woocommerce' ); ?></a>
								<?php endif; ?>

								<?php printf( __( 'To override and edit this email template copy <code>%s</code> to your theme folder: <code>%s</code>.', 'woocommerce' ), plugin_basename( $core_file ) , 'yourtheme/woocommerce/' . $this->$template ); ?>
							</p>

							<div class="editor" style="display:none">

								<textarea class="code" readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo file_get_contents( $core_file ); ?></textarea>

							</div>

						<?php else : ?>

							<p><?php _e( 'File was not found.1', 'woocommerce' ); ?></p>

						<?php endif; ?>

					</div>
					<?php
				endforeach;
			?>
			</div>
			<?php
			$inline_script = "
				jQuery('select.email_type').change(function(){

					var val = jQuery( this ).val();

					jQuery('.template_plain, .template_html').show();

					if ( val != 'multipart' && val != 'html' )
						jQuery('.template_html').hide();

					if ( val != 'multipart' && val != 'plain' )
						jQuery('.template_plain').hide();

				}).change();

				var view = '" . esc_js( __( 'View template', 'woocommerce' ) ) . "';
				var hide = '" . esc_js( __( 'Hide template', 'woocommerce' ) ) . "';

				jQuery('a.toggle_editor').text( view ).toggle( function() {
					jQuery( this ).text( hide ).closest('.template').find('.editor').slideToggle();
					return false;
				}, function() {
					jQuery( this ).text( view ).closest('.template').find('.editor').slideToggle();
					return false;
				} );

				jQuery('a.delete_template').click(function(){
					var answer = confirm('" . esc_js( __( 'Are you sure you want to delete this template file?', 'woocommerce' ) ) . "');

					if (answer)
						return true;

					return false;
				});

				jQuery('.editor textarea').change(function(){
					var name = jQuery(this).attr( 'data-name' );

					if ( name )
						jQuery(this).attr( 'name', name );
				});
			";
			if ( version_compare( WC_VERSION, '2.1', '<' ) ) {
				$woocommerce->add_inline_js( $inline_script );
			} else {
				wc_enqueue_js( $inline_script );
			}
		}
	}
}
