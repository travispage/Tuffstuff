<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */

/**
 *
 *
 * @since 1.4
 */
final class SiteTreePageViewController implements SiteTreeDashboardDelegateProtocol, SiteTreeTabbedPageViewDelegateProtocol {
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $plugin;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $db;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $view;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $activeTabId = '';
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $formConfigMode = false;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $dashboardIsRendering = false;
	
	/**
	 *
	 *
	 * @since 1.5
	 * @var array|bool
	 */
	private $errorMessage = false;
		
	/**
	 * @since 1.4
	 * @var object
	 */
	private static $instance;
	
	/**
	 * @since 1.4
	 * @return object
	 */
	public static function instance( $plugin ) {
		if (! self::$instance )
			self::$instance = new self( $plugin );
		
		return self::$instance;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->db = $plugin->db();
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function setActiveTab( $tab_id ) { $this->activeTabId = $tab_id; }
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function enableConfigMode() { $this->formConfigMode = true; }
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function processAction( $action, $page_id, &$input = array(), $tab_id = null  ) {
		switch ( $action ) {
			case 'update':
				$message		= $url = '';
				$dataController = $this->plugin->dataController();
				$data			= $dataController->sanitiseData( $input, $page_id, $tab_id  );
				
				if ( $dataController->isGoogleRelatedPage( $page_id ) ) {
					$message = __( 'Settings saved. %sView Sitemap%s', 'sitetree' );
					$url	 = $this->plugin->googleSitemapPermalink();
					
					if ( $this->db->setOptions( $data, true ) ) {
						$this->db->invalidateCache( 'xml' );
						
						if (! $this->db->getOption( 'ping', true ) )
							$this->plugin->pingController()->unschedulePing();
					}
				}
				else {
					$message = __( 'Settings saved. %sView Archive%s', 'sitetree' );
					$url	 = esc_url( get_permalink( $this->db->getOption( 'page_for_sitemap' ) ) );
					
					if ( $this->db->setOptions( $data, true ) )
						$this->db->invalidateCache( 'html5' );
				}
				
				$this->registerErrorMessage( sprintf( $message, '<a href="' . $url . '" target="sitetree_admin">', '</a>' ) );
				SiteTreeUtilities::adminRedirect( $page_id, array( 'tab' => $tab_id, 'settings-updated' => 'true' ) );
			
			case 'rebuild_google':
				$now = time();
				
				if ( $now - $this->db->getOption( 'date', $now, 'stats_xml' ) < 11 ) {
					$this->registerErrorMessage( __( 'No need to rebuild the Sitemap every 10 seconds. Take a break!', 'sitetree' ) );
					SiteTreeUtilities::adminRedirect( $page_id, array( 'rebuilt' => 'false' ) );
				}	
				
				$this->plugin->rebuildGoogleSitemap();
				SiteTreeUtilities::adminRedirect( $page_id );
			
			case 'rebuild_html5':
				$now = time();
				
				if ( $now - $this->db->getOption( 'date', $now, 'stats_html5' ) < 11 ) {
					$this->registerErrorMessage( __( 'No need to rebuild the Archive every 10 seconds. Take a break!', 'sitetree' ) );
					SiteTreeUtilities::adminRedirect( $page_id, array( 'rebuilt' => 'false' ) );
				}
				
				$this->plugin->rebuildHtml5Sitemap();
				SiteTreeUtilities::adminRedirect( $page_id );
				
			case 'cancel_ping':
				$this->plugin->pingController()->unschedulePing();
				SiteTreeUtilities::adminRedirect( $page_id );
				
			case 'update_config':
				// If this is the first activation, load defaults in db
				if ( $this->db->getOption( 'page_for_sitemap', null ) === null ) {
					$defaults = $this->plugin->dataController()->html5Defaults();
					
					$this->db->setOptions( $defaults, true );
					$this->plugin->rebuildHtml5Sitemap();
				}
				
				$data = $this->plugin->dataController()->sanitiseData( $input, $page_id );
				
				$this->db->setOptions( $data, true );				
				SiteTreeUtilities::adminRedirect( $page_id );
			
			case 'enable':
				// If this is the first activation, load defaults in db
				if ( $this->db->getOption( 'enable_xml', null ) === null ) {
					$defaults = $this->plugin->dataController()->googleDefaults();
					
					$this->db->setOptions( $defaults, true );
					$this->plugin->rebuildGoogleSitemap();
				}
				
				$this->db->setOption( 'enable_xml', true );
				SiteTreeUtilities::adminRedirect( $page_id );
			
			case 'disable':
				$this->db->setOption( 'enable_xml', false );
				$this->plugin->cleanUp();
				
				SiteTreeUtilities::adminRedirect( $page_id );
			
			case 'dismiss':
				$this->db->setOption( 'msg_displayed', true );
				SiteTreeUtilities::adminRedirect( $page_id );
		}
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 *
	 * @param string $msg
	 * @param string $code
	 */
	public function registerErrorMessage(  $msg, $code = 'updated' ) {
		$this->db->setTransient( 'error_message', array( 'message' => $msg, 'type' => $code ) );
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 */
	public function setupErrorMessage() {
		if ( $this->errorMessage = $this->db->getTransient( 'error_message' ) )
			$this->db->deleteTransient( 'error_message' );	
	}
	
	/**
	 * Helper method:
	 *
	 * @since 1.5
	 *
	 * @param string $dashboard_id
	 * @return string
	 */
	private function makePingNode( $dashboard_id ) {
		$status_class = '';
		$info		  = $this->plugin->pingController()->getPingInfo();
		$node		  = '<div id="sitetree-ping"';
		
		if ( $info['code'] && ( $info['code'] != 'success' ) )
			$status_class = ' class="sitetree-ping-' . $info['code'] . '"';
			
		$node .= $status_class . '>( Ping On )<div id="sitetree-ping-info"' . $status_class . '>';
		
		foreach ( $info['stati'] as $status ) {
			$node		  .= '<p '; 
			$cancel_button = '';
			
			if ( $status['can_be_cancelled'] ) {
				$node		   .= 'id="sitetree-ping-scheduled-msg" ';
				$cancel_button  = ' <a href="';
				$cancel_button .= SiteTreeUtilities::adminURL( $dashboard_id, array( 'action' => 'cancel_ping' ) );
				$cancel_button .= '" id="sitetree-cancel-ping-btn" title="' . __( 'Cancel', 'sitetree' ) . '">cancel</a>';
			}
			
			$node .= 'class="sitetree-ping-msg">' . $cancel_button . $status['message'] . '</p>';
		}
			
		$node .= '</div></div>';
		
		return $node;
	}
	
	//! ------- Delegate Methods -------
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function dashboardCanDisplayMessage( $dashboard ) {
		$first_instal = (int) $this->db->getOption( 'instal_date' );
		
		if ( ( time() - $first_instal < 864000 ) || $this->db->getOption( 'msg_displayed' ) )
			return false;
			
		$dashboard->configureMessage(array(
			'dismiss_url' => SiteTreeUtilities::adminURL( $dashboard->id(), array( 'action' => 'dismiss' ) ),
			'head_line'	  => 'Hi, ' . wp_get_current_user()->display_name . '!'
		));
			
		return true;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function dashboardCanRenderStats( $dashboard, $form_index ) {
		$render	= false;
		$this->dashboardIsRendering = true;
		
		if ( $is_google_form = ( $form_index == 0 ) ) {
			$context = 'stats_xml';
			$render  = $this->db->xmlEnabled();
		}
		else {
			$context = 'stats_html5';
			
			if (! $this->formConfigMode )
				$render = $this->db->html5Enabled();
		}
		
		if (! $render )
			return false;
		
		$date_stat = (int) $this->db->getOption( 'date', 0, $context );
		$class	   = ( time() - $date_stat < 11 ) ? 'sitetree-new-stat' : '';
				
		if ( $is_google_form ) {
			$url_stat = (int) $this->db->getOption( 'url_count', 0, $context );
			
			if ( $url_stat == 10000 ) {
				$url_stat  = '<div class="sitetree-stat-limit">' . $url_stat . '<div class="sitetree-stat-limit-msg">'
						   . 'Remarkable! Your Sitemap has as many links as each neuron in your brain has!<br>'
						   . "Unfortunately, I'm not as much complex at this time, thus, I can't add more URLs.<br>"
						   . "However, don't be upset … I'm growing like a bamboo.";
				$url_stat .= '</div></div>';
			}
			
			$dashboard->registerStat( __( 'URLs', 'sitetree' ), $url_stat, $class );
				
			if ( $this->db->getOption( 'images', true ) )
				$dashboard->registerStat( __( 'Images', 'sitetree' ), (int) $this->db->getOption( 'num_images', 0, $context ), $class );
		}
		else {
			$items_stat = (int) $this->db->getOption( 'url_count', 0, $context );
			
			if ( $items_stat === $this->db->getOption( 'items_limit', 200 ) ) {
				$items_stat  = '<div class="sitetree-stat-limit">' . $items_stat . '<div class="sitetree-stat-limit-msg">';
				$items_stat .= __( 'Beware! Your Archive is about to explode: the limit set in the '
								 . '&#8220;Under the Hood&#8221; section has been reached.', 'sitetree' );
				$items_stat .= '</div></div>';
			}
			
			$dashboard->registerStat( __( 'Items', 'sitetree' ), $items_stat, $class );
		}
		
		$dashboard->registerStat( __( 'Queries', 'sitetree' ), (int) $this->db->getOption( 'num_queries', 0, $context ), $class );
		$dashboard->registerStat( __( 'Rebuild Time', 'sitetree' ), (float) $this->db->getOption( 'runtime', 0, $context ) . 's', $class );
		$dashboard->registerStat( __( 'Rebuilt on', 'sitetree' ), SiteTreeUtilities::localDate( $date_stat ), $class );
		
		return true;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function dashboardWillRenderToolbarButtons( $dashboard, $form_index ) {
		$aux_buttons = '';
		$config		 = array();
		
		if ( $form_index == 0 && $this->db->xmlEnabled() ) {
			if ( $this->db->getOption( 'ping', true ) )
				$aux_buttons .= $this->makePingNode( $dashboard->id() );
			
			$aux_buttons .= '<input type="submit" id="sitetree-disable-tb-btn" class="sitetree-aux-tb-btn sitetree-hidden-tb-btn" '
						  . 'name="submit" value="' . __( 'Disable', 'sitetree' ) . '">';
			
			$config['rebuild_url'] = SiteTreeUtilities::adminURL( $dashboard->id(), array( 'action' => 'rebuild_google' ) );
			$config['view_url']	   = $this->plugin->googleSitemapPermalink();	
		}
		elseif ( ( $form_index != 0 ) && ( $page_for_sitemap = $this->db->getOption('page_for_sitemap') ) ) {
			$aux_buttons .= '<a href="';
			
			$config['rebuild_url'] = SiteTreeUtilities::adminURL( $dashboard->id(), array( 'action' => 'rebuild_html5' ) );
			$config['view_url']    = esc_url( get_permalink( $page_for_sitemap ) );
			
			if ( $this->formConfigMode ) {
				$config['submit_title'] = __( 'Save Changes', 'sitetree' );
				
				$aux_buttons .= SiteTreeUtilities::adminURL( $dashboard->id() );
				$aux_buttons .= '" class="sitetree-aux-tb-btn">' . __( 'Cancel', 'sitetree' );
			}
			else {
				$aux_buttons .= SiteTreeUtilities::adminURL( $dashboard->id(), array( 'edit' => 'config' ) );
				$aux_buttons .= '" class="sitetree-aux-tb-btn sitetree-hidden-tb-btn">' . __( 'Configure', 'sitetree' );
			}
			
			$aux_buttons .= '</a>';
		}
		
		$dashboard->configureToolbar( $config );
		
		return $aux_buttons;
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 * @return string
	 */
	public function dashboardWillFinishRendering( $dashboard ) {
		return '<aside id="sitetree-about-view"><h2>SiteTree</h2><a href="#" id="sitetree-about-view-close"></a>'
			 . '<ul><li>Version ' . SiteTree::VERSION . '</li><li><strong>' . __( 'Updated on', 'sitetree' ) . '</strong><br>' 
			 . SiteTreeUtilities::date( $this->db->getOption( 'last_updated' ) ) 
			 . '</li><li><strong>' . __( 'Translations', 'sitetree' ) . '</strong><ul>'
			 . '<li>' . __( 'German', 'sitetree' ) . ' (Thomas Meesters)</li>'
			 . '<li>' . __( 'Italian', 'sitetree' ) . ' (Luigi Cavalieri)</li>'
			 . '<li>' . __( 'Russian', 'sitetree' ) . ' (Oleg Vlasov)</li>'
			 . '<li>' . __( 'Swedish', 'sitetree' ) . ' (Joakim Lindskog)</li>'
			 . '</ul></li></ul><p id="sitetree-about-view-copy">Copyright &copy 2013 Luigi Cavalieri.<br>'
			 . sprintf( __( 'Licensed under %s', 'sitetree' ), '<a href="' 
			 . SiteTree::website( 'license' ) . '" target="_blank">GPL v2.0</a>' ) . '</p></aside>';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function tabbedPageViewTabUrl( $pageView, $tab_id ) {
		return  SiteTreeUtilities::adminURL( $pageView->id(), array( 'tab' => $tab_id ) );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function tabbedPageViewCanSetTabActive( $index, $tab_id ) {
		if ( !$this->activeTabId && ( $index == 0 ) ) {
			$this->activeTabId = $tab_id;
			
			return true;
		}
		
		return ( $tab_id == $this->activeTabId );
	}
	
	/**
	 *
	 *
	 * @since 1.5
	 */
	public function pageViewDidStartRendering( $pageView ) {
		if (  $this->errorMessage )
			return '<div id="setting-error-msg" class="' . $this->errorMessage['type'] 
				 . ' settings-error"><p>' . $this->errorMessage['message'] . '</p></div>';
		
		return '';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function pageViewWillRenderFormContent( $pageView ) {
		$action		   = 'update';
		$hidden_fields = wp_nonce_field( 'sitetree-options', '_sitetree_nonce', true, false );
		
		if ( $this->dashboardIsRendering ) {
			if ( $pageView->formIndex() == 0 )
				$action = $this->db->xmlEnabled() ? 'disable' : 'enable';
			else
				$action = 'update_config';
		}
		
		$hidden_fields .= '<input type="hidden" name="action" value="' . $action . '">'
		   				. '<input type="hidden" name="option_page" value="' . $pageView->id() . '">';
			   
		return $hidden_fields;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function pageViewDidRenderFormContent( $pageView ) {
		if ( $this->activeTabId )
			return '<input type="hidden" name="tab" value="' . $this->activeTabId . '">';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function pageViewFieldValue( $field, $section, $tab ) {
		$context = $group = $section->id;
		
		if ( $group && isset( $tab->context )  )
			$context = $tab->context;
		
		$value	   = $this->db->getOption( $field->id, $field->default, $group, $context );
		$validator = new SiteTreeDataValidator( $value, $field->type, $field->default, $field->conditions );
		
		return $validator->value();
	}
}
?>