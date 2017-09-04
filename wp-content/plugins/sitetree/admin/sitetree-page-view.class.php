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
abstract class SiteTreePageView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $delegate;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $page;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $sections;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $section;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected $field;

	/**
	 *
	 *
	 * @since 1.4
	 */
	public function __construct( $page ) {
		$this->page		= $page;
		$this->sections = &$page->sections;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function setDelegate( SiteTreePageViewDelegateProtocol $delegate ) {
		$this->delegate = $delegate;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function id() {
		return $this->page->id;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function render() {
		ob_start();
		
		echo '<div class="wrap"><div id="icon-' . $this->page->icon . '" class="icon32"><br /></div>'
		   . '<h2>' . esc_attr( $this->page->title ) . '</h2>';
		
		echo $this->delegate->pageViewDidStartRendering( $this );		
		//settings_errors( $this->page->id );
		
		$this->beforeFormRenders();
		$this->renderForm();
		$this->afterFormRenders();
		
		ob_end_flush();
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function formTagAtts( $atts ) {
		$output = '';
		
		foreach ( $atts as $attr => $value )
			$output .= ' ' . $attr . '="' . $value . '"';
		
		return $output; 
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function beforeFormRenders() {}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function renderForm() {
		echo '<form method="post"' . $this->formTagAtts( array( 'id' => ($this->page->id . '-form') ) ) . '>';
		
		echo $this->delegate->pageViewWillRenderFormContent( $this );  
		
		$this->renderFormContent();
		
		echo $this->delegate->pageViewDidRenderFormContent( $this );
		
		echo '</form>';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function afterFormRenders() {
		echo '</div>';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	abstract protected function renderFormContent();
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function renderSections( $args = null ) {
		if (! $this->sections )
			return $this->error( 'failed to load the content of this page.' );
			
		foreach ( $this->sections as $this->section ) {
			if ( $this->section->title )
				echo '<h3>' . esc_attr( $this->section->title ) . '</h3>';
				
			echo '<table class="form-table"><tbody>';
			
			foreach ( $this->section->fields as $this->field ) {
				echo '<tr valign="top"><th scope="row">' . esc_attr( $this->field->title ) . '</th><td>';
				
				$this->renderField( $args );
				
				echo '</td></tr>';
			}
			
			echo '</tbody></table>';
		}
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function renderField( $args = null ) {
		if (! class_exists( $this->field->class ) )
			return false;
		
		$fieldView = new $this->field->class( $this->field, $this->section->id );
		$value	   = $this->delegate->pageViewFieldValue( $this->field, $this->section, $args );
		
		if ( $value === null )
			$value = $this->field->default;
		
		$fieldView->setValue( $value );
		$fieldView->render();
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function error( $msg ) {
		echo '<p>' . sprintf( '%sError:%s ', '<strong>', '</strong>' ) . $msg . '</p>';
		
		return false;
	}
}


/**
 *
 *
 * @since 1.4
 */
final class SiteTreeDashboard extends SiteTreePageView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $formIndex;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $formsData;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $formConfig;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $stats = array();
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $messageBox = array(
		'dismiss_url' => '',
		'head_line'	  => ''
	);
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $toolbar = array(
		'view_url'	   => '',
		'rebuild_url'  => '',
		'submit_title' => '',
	);
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $renderStats;
	
	/**
	 *
	 *
	 * @since 1.5
	 */
	public function registerStat( $title, $value, $class = '' ) {
		if (! $class )
			$class = 'sitetree-stat';
	
		$this->stats[] = array(
			'title' => $title,
			'value' => $value,
			'class' => $class
		);
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function configureMessage( $config ) {
		$this->messageBox = array_merge( $this->messageBox, $config );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function configureToolbar( $config ) {
		$this->toolbar = array_merge( $this->toolbar, $config );
		
		if (! $this->toolbar['submit_title'] )
			$this->toolbar['submit_title'] = __( 'Enable', 'sitetree' );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function setDelegate( SiteTreePageViewDelegateProtocol $delegate ) {
		parent::setDelegate( $delegate );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function formIndex() { return $this->formIndex; }
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function beforeFormRenders() {
		$this->formsData[] = array( 'title' => __( 'Google Sitemap', 'sitetree' ) );
		
		$this->formsData[] = array( 'title' => __( 'Archive Page', 'sitetree' ) );
		
		echo '<div id="sitetree-dashboard-wrapper" class="sitetree-clear">';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function renderForm() {
		if ( $this->delegate->dashboardCanDisplayMessage( $this ) ) {
			echo '<div id="sitetree-msg-box"><h3>' . $this->messageBox['head_line'] . '</h3>'
			   . '<p>Some time has gone since you first installed SiteTree and the reading of this short message should mean '
			   . 'you are appreciating the code I have written so far, so … Thank You!.</p><p>As you know (or might have noticed '
			   . 'from the amount of changes made with the latest updates), SiteTree is a very young piece of software still barely '
			   . "known to the WordPress community, that's why I would ask you a couple of minutes of your time to rate it on "
			   . "WordPress.org. You'll just need to log-in into your account and leave your rating with a comment — that's all. "
			   . 'Thank you for your help.</p><p id="sitetree-msg-box-sign">Luigi Cavalieri</p>'
			   . '<a href="' . $this->messageBox['dismiss_url'] . '" id="sitetree-msg-box-close">Dismiss</a>'
			   . '<p id="sitetree-msg-box-rate"><a href="http://wordpress.org/support/view/plugin-reviews/sitetree" '
			   . 'class="sitetree-primary-btn" id="sitetree-msg-box-rate-btn" target="_blank">Rate on WordPress.org</a></p></div>';
		}
		
		echo '<div id="sitetree-dashboard">';
		
		foreach ( $this->formsData as $this->formIndex => $this->formConfig ) {
			$this->renderStats = $this->delegate->dashboardCanRenderStats( $this, $this->formIndex );
			
			parent::renderForm();
		}
		
		echo '</div><aside id="sitetree-dashboard-sidebar"><h4>' . __( 'Useful Resources', 'sitetree' ) . '</h4><ul>'
		   . '<li><a href="' . SiteTree::WEBSITE . '">' . __( 'Website', 'sitetree' ) . '</a></li>'
		   . '<li><a href="' . SiteTree::WEBSITE . 'blog/">' . __( 'Blog', 'sitetree' ) . '</a></li>'
		   . '<li><a href="' . SiteTree::website( 'faqs' ) . '">' . __( 'FAQs', 'sitetree' ) . '</a></li>'
		   . '<li><a href="' . SiteTree::WEBSITE . 'support/">' . __( 'Support', 'sitetree' ) . '</a></li>'
		   . '<li><a href="' . SiteTree::WEBSITE . 'version-history/">' . __( 'Version History', 'sitetree' ) . '</a></li>'
		   . '</ul><h4>' . __( 'Contribute', 'sitetree' ) . '</h4><ul>'
		   . '<li><a href="' . SiteTree::website( 'feedback' ) . '" target="_blank">' . __( 'Provide Feedback', 'sitetree' ) . '</a></li>'
		   . '<li><a href="' . SiteTree::website( 'bug_report' ) .  '" target="_blank">' . __( 'Report Bug', 'sitetree' ) . '</a></li>'
		   . '<li><a href="' . SiteTree::website( 'l10n' ) . '" target="_blank">' . __( 'Translate', 'sitetree' ) . '</a></li>'
		   . '</ul><a href="#" id="sitetree-info-btn" title="' . sprintf( __( 'About %s', 'sitetree' ), 'SiteTree' ) . '">i</a></aside>';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function renderFormContent() {
		echo '<div class="sitetree-toolbar"><span>' . $this->formConfig['title'] . '</span>';
		
		echo $this->delegate->dashboardWillRenderToolbarButtons( $this, $this->formIndex );
		
		if ( $this->renderStats ) {
			$num_stats    = count( $this->stats );
			$last_item_id = $num_stats - 1;
			
			echo '<a href="' . $this->toolbar['view_url'] .'" class="sitetree-button sitetree-tb-btn" '
			   . 'target="sitetree_admin">' . __( 'View', 'sitetree' ) . '</a>'
			   . '<a href="' . $this->toolbar['rebuild_url'] .'" class="sitetree-button sitetree-tb-btn">' 
			   . __( 'Rebuild', 'sitetree' ) . '</a></div>';
			 
			echo '<ul class="sitetree-clear';
			
			if ( $num_stats == 5 )
				echo ' sitetree-five-stats';
				
			echo '">';
			
			for ( $i = 0; $i < $num_stats; $i++ ) {
				echo '<li><div class="sitetree-stat-container';
				
				if ( $i == $last_item_id )
					echo ' sitetree-last-stat';
				
				echo '">' . $this->stats[$i]['title'] . '<div class="' . $this->stats[$i]['class']
				   . '">' . $this->stats[$i]['value'] . '</div></div></li>';
			}
			
			echo '</ul>';
			
			// Reset $stats array after rendering;
			$this->stats = array();
		}
		else {
			echo '<input type="submit" class="sitetree-primary-btn" name="submit" value="' . $this->toolbar['submit_title'] . '"></div>';
			
			if ( $this->formIndex == 0 ) {
				echo '<p id="sitetree-enable-sitemap-msg">' . __( 'Your Google Sitemap is almost ready &mdash; just click &#8220;Enable&#8221;. '
				   . 'You will be able to adjust it to your needs later on.', 'sitetree' ) . '</p>';
			}
			else { $this->renderSections(); }
		}
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function afterFormRenders() {
		echo $this->delegate->dashboardWillFinishRendering( $this );
		echo '</div>';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function formTagAtts( $atts ) { return ''; }
}


/**
 *
 *
 * @since 1.4
 */
class SiteTreeTabbedPageView extends SiteTreePageView {
	/**
	 *
	 *
	 * @since 1.4
	 */
	private $tab;
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function setDelegate( SiteTreePageViewDelegateProtocol $delegate ) {
		parent::setDelegate( $delegate );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function formTagAtts( $atts ) {
		$atts['id'] = 'settings-form';
		
		return parent::formTagAtts( $atts );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function beforeFormRenders() {
		echo '<h2 class="nav-tab-wrapper">';
		
		foreach ( $this->page->tabs as $index => $tab ) {
			$url = $this->delegate->tabbedPageViewTabUrl( $this, $tab->id );
			
			if (! $url ) $url = '#';
			
			echo '<a href="' . $url . '" class="nav-tab';
			
			if ( $this->delegate->tabbedPageViewCanSetTabActive( $index, $tab->id ) ) {
				$this->tab = $tab;
				echo ' nav-tab-active';
			}
			
			echo '">'. esc_attr( $tab->title ) . '</a>';
		}
		
		echo '</h2>';
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	protected function renderFormContent() {
		if ( !( $this->tab && isset($this->page->sections[ $this->tab->id ]) ) )
			return $this->error( 'unable to find the content of this tab.' );
		
		$this->sections = &$this->page->sections[ $this->tab->id ];
		
		$this->renderSections( $this->tab );
		
		submit_button();
	}
}
?>