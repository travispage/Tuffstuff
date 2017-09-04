<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 *
 * 
 * @since 1.3
 */
class SiteTreeMetaBoxController {
	/**
	 *
	 * 
	 * @since 1.4
	 * @var object
	 */
	private $plugin;
	
	/**
	 * Database object reference.
	 * 
	 * @since 1.3
	 * @var object
	 */
	private $db;
	
	/**
	 * 
	 * @since 1.3.1
	 * @var array
	 */
	private $sections;
	
	/**
	 * Initialises the properties of the class.
	 *
	 * @since 1.3
	 * @param $db object
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->db	  = $plugin->db();
	}

	/**
	 * Registers a MetaBox into the WordPress MetaBox system.
	 *
	 * This method is hooked into the add_meta_boxes action hook.
	 *
	 * @since 1.3
	 *
	 * @param $post_type string
	 */
	public function register_meta_box( $post_type ) {
		if ( ($post_type == 'post' || $post_type == 'page') && $this->load_sections( $post_type ) )
			add_meta_box( 'sitetree', 'SiteTree', array( $this, 'render_meta_box' ), $post_type, 'side' );
	}

	/**
	 * Renders the content of a MetaBox.
	 *
	 * Callback of add_meta_box()
	 *
	 * @see register_meta_box()
	 * @since 1.3
	 *
	 * @param $post object
	 */
	public function render_meta_box($post) {
		echo '<input type="hidden" name="_sitetree_nonce" value="' . wp_create_nonce('save_post_meta') . '" />';
		
		foreach ( $this->sections as $section ) {
			echo '<p><strong>' . $section->title . '</strong></p>';
		
			foreach ( $section->fields as $field ) {
				$value = null;
				
				if ( isset( $field->config['context'] ) )
					$value = in_array( $post->ID, $this->db->getOption( $field->config['context'], array(), 'exclude' ) );
				else
					$value = $this->db->getPostMeta( $post->ID, $field->id, 'reset' );
				
				if (! class_exists( $field->class ) )
					continue;
					
				$fieldView = new $field->class( $field );
				$fieldView->setValue( $value );
				$fieldView->render();
				
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
		}
	}
	
	/**
	 * Validates a post request and saves the metadata.
	 *
	 * This method is hooked into the save_post action hook.
	 * 
	 * @since 1.3
	 *
	 * @param $post_id string
	 * @param $post object
	 */
	public function process_metadata( $post_id, $post ) {
		if ( !isset($_POST['_sitetree_nonce']) || ($post->post_status == 'auto-draft') || wp_is_post_revision($post) )
			return false;
			
		if (! ($post->post_type == 'post' || $post->post_type == 'page') )
			return false;
			
		check_admin_referer( 'save_post_meta', '_sitetree_nonce' );
			
		if ( !current_user_can( 'edit_post', $post ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		
		$input = isset( $_POST['sitetree'] ) ? $_POST['sitetree'] : array();
		
		$this->load_sections( $post->post_type );
		
		foreach ( $this->sections as $section ) {
			foreach ( $section->fields as $field ) {
				$value = isset( $input[$field->id] ) ? $input[$field->id] : null;
				$validator = new SiteTreeDataValidator( $value, $field->type, $field->default, $field->conditions );
				$value = $validator->value();
				
				// The metadata associated to the checkboxes needs to be processes separatedly.
				if ( isset( $field->config['context'] ) ) {
					$exclude = $this->db->getOption( $field->config['context'], array(), 'exclude' );
					$excluded = in_array( $post->ID, $exclude );
					
					// If the checkbox has been ticked and the ID of the current post/page was not excluded yet
					// we add it to the $exclude array
					if ( $value && !$excluded ) {
						$exclude[] = $post->ID;
						
						$this->db->invalidateCache( $field->config['context'] );
					}
					elseif ( !$value ) {
						$this->db->invalidateCache( $field->config['context'] );
						
						// New post published?
						if ( ( $post->post_date == $post->post_modified ) && ( $post->post_status == 'publish' ) && 
							 ( $field->config['context'] == 'xml' ) )
						{
							$this->plugin->pingController()->schedulePing();
						}
						
						// If it was previously excluded, we remove the post from the $exclude array.
						if ( $excluded )
							$exclude = array_diff( $exclude, array( $post->ID ) );
					}
					
					$this->db->setOption( $field->config['context'], $exclude, 'exclude' );
				}
				else {
					if ($value == 'reset')
						$this->db->deletePostMeta( $post->ID, $field->id );
					else
						$this->db->setPostMeta( $post->ID, $field->id, $value );
				}
			}
		}
	}
	
	/**
	 *
	 *
	 * @since 1.3.1
	 * @return bool
	 */
	private function load_sections( $post_type ) {
		$loaded = false;
		$this->sections = array( new SiteTreeSection( '', '', __('Exclude From', 'sitetree'), array() ) );
		
		if ( $this->db->html5Enabled() && $this->db->getOption('include', true, $post_type, 'html5') ) {
			$loaded = true;
			$this->sections[0]->fields[] = new SiteTreeField( 'exclude', 'Checkbox', 'bool', '', __('Archive Page', 'sitetree'), false, array('context' => 'html5') );
		}
		
		if ( $this->db->xmlEnabled() && $this->db->getOption('include', true, $post_type, 'xml') ) {
			$loaded = true;
			$this->sections[0]->fields[] = new SiteTreeField('xml_exclude', 'Checkbox', 'bool', '', __('Google Sitemap', 'sitetree'), false, array('context' => 'xml') );
			
			$this->sections[] = new SiteTreeSection('', '', __('Update Frequency', 'sitetree'), array(
				new SiteTreeField('changefreq', 'Dropdown', 'options', '', '', 'default', SiteTreeUtilities::frequencies( true ) ),
			));
			$this->sections[] = new SiteTreeSection('', '', __('Priority', 'sitetree'), array(
				new SiteTreeField('priority', 'Dropdown', 'options', '', '', 'default', SiteTreeUtilities::priorities( 1, true ) ),
			));
		}
		
		return $loaded;	
	}
}