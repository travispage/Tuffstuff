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
class SiteTreePage {
	/**
	 * @since 1.4
	 * @var string
	 */
	public $id;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	private $_id;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	public $title;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	public $menu_title;
	
	/**
	 * The name of a SiteTreePageView child class.
	 *
	 * @since 1.4
	 * @var string
	 */
	public $class;
	
	/**
	 * The name of a SiteTreePageView child class.
	 *
	 * @since 1.4
	 * @var string
	 */
	public $icon;
	
	/**
	 * Collection of SiteTreeTab objects.
	 * 
	 * @since 1.4
	 * @var array
	 */
	public $tabs;
	
	/**
	 * Collection of SiteTreeSection objects.
	 *
	 * @since 1.4
	 * @var array
	 */
	public $sections = array();
	
	/**
	 * Constructor method.
	 *
	 * @since 1.4
	 */
	public function __construct( $id, $title, $menu_title, $tabs = array(), $class = 'TabbedPageView', $icon = 'sitetree-general' ) {
		$this->_id		  = $id;
		$this->id		  = 'sitetree-' . $id;
		$this->title	  = $title;
		$this->menu_title = $menu_title;
		$this->class	  = 'SiteTree' . $class;
		$this->tabs		  = $tabs;
		$this->icon		  = $icon;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function rawId() { return $this->_id; }
}


/**
 *
 *
 * @since 1.4
 */
class SiteTreeTab {
	/**
	 * @since 1.4
	 * @var string
	 */
	public $id;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	public $title;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	public $context;
	
	/**
	 * Constructor method.
	 *
	 * @since 1.4
	 */
	public function __construct( $id, $title, $context = '' ) {
		$this->id	   = $id;
		$this->title   = $title;
		$this->context = $context;
	}
}


/**
 *
 *
 * @since 1.3
 */
class SiteTreeSection {
	/**
	 * @since 1.3
	 * @var string
	 */
	public $id;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	public $callback;
	
	/**
	 * @since 1.3
	 * @var string
	 */
	public $title;
	
	/**
	 * @since 1.3
	 * @var array
	 */
	public $fields;
	
	/**
	 * Constructor method.
	 *
	 * @since 1.3
	 */
	public function __construct( $id, $callback, $title, $fields ) {
		$this->id		= $id;
		$this->callback = $callback ? $callback : $id;
		$this->title	= $title;
		$this->fields	= $fields;
	}
}


/**
 *
 *
 * @since 1.3
 */
class SiteTreeField {
	/**
	 * @since 1.4
	 * @var string
	 */
	public $id;
	
	/**
	 * The name of a SiteTreeFieldView child class.
	 *
	 * @since 1.4
	 * @var string
	 */
	public $class;
	
	/**
	 * @since 1.3
	 * @var string
	 */
	public $type;
	
	/**
	 * @since 1.3
	 * @var string
	 */
	public $title;

	/**
	 * @since 1.3
	 * @var string
	 */
	public $tooltip;
	
	/**
	 * @since 1.3
	 * @var mixed
	 */
	public $default;
	
	/**
	 * @since 1.4
	 * @var mixed
	 */
	public $config;
	
	/**
	 * @since 1.4
	 * @var mixed
	 */
	public $conditions;
	
	/**
	 * Constructor method.
	 *
	 * @since 1.3
	 *
	 * @param $id string
	 * @param $callback string
	 * @param $valid_callback string
	 * @param $title string
	 * @param $tooltip string
	 * @param $default mixed
	 * @param $args mixed
	 */
	public function __construct( $id, $class, $type, $title, $tooltip = '', $default = false, $config = null, $conditions = null ) {
		$this->id	   = $id;
		$this->class   = 'SiteTree' . $class;
		$this->type	   = $type;
		$this->title   = $title;
		$this->tooltip = $tooltip;
		$this->default = $default;
		$this->config  = $config;
		
		if ( $conditions === null )
			$this->conditions = &$this->config;
		else
		 	$this->conditions = $conditions;
	}
}


/**
 *
 *
 * @since 1.4
 */
class SiteTreeDataController {
	/**
	 * @since 1.4
	 * @var string
	 */
	private $pages;
	
	/**
	 * @since 1.4
	 * @var string
	 */
	private $pagesIndex = array();
	
	/**
	 * Constructor method.
	 *
	 * @since 1.4
	 */
	public function __construct() {
		$this->pages = array(
			new SiteTreePage( 'dashboard', sprintf( __('%s Dashboard', 'sitetree'), 'SiteTree' ), __('Dashboard', 'sitetree'), 
				array(), 'Dashboard', 'index'
			),
			new SiteTreePage( 'google', __('Google Sitemap Settings', 'sitetree'), __('Google Sitemap', 'sitetree'), array(
				new SiteTreeTab( 'general', __('General', 'sitetree'), 'xml' ),
				new SiteTreeTab( 'advanced', __('Advanced', 'sitetree') )
			)),
			new SiteTreePage( 'html5', __('Archive Page Settings', 'sitetree'), __('Archive Page', 'sitetree'), array(
				new SiteTreeTab( 'general', __('General', 'sitetree'), 'html5' ),
				new SiteTreeTab( 'hood', __('Under the Hood', 'sitetree') )
			)),
		);
		
		// Init index
		foreach ( $this->pages as $page_number => &$page ) {
			$entry = array( 'page_num' => $page_number, 'page' => $page );
			
			$this->pagesIndex[ $page->id ]		= $entry;
			$this->pagesIndex[ $page->rawId() ] = $entry;
		}
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function &pages( $include_google, $include_html5 ) {
		$pages = $this->pages;
		
		if (! $include_html5 )
			unset( $pages[ $this->pagesIndex['html5']['page_num'] ] );
			
		if (! $include_google )
			unset( $pages[ $this->pagesIndex['google']['page_num'] ] );
			
		return $pages;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function page( $id, $load_sections = true ) {
		if (! $this->pageExists( $id ) )
			return false;
		
		$page = $this->pagesIndex[$id]['page'];
			
		if ( $load_sections )
			$this->loadPageSections( $page );
		
		return $page;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	private function loadPageSections( &$page ) {
		if ( $page->sections )
			return 2;
		
		$page_number = $this->pagesIndex[$page->id]['page_num'];
		
		return @include( $page->rawId() . '-page-data.php' );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function pageExists( $id ) {
		return isset( $this->pagesIndex[$id] );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function tab( $page_id, $tab_id ) {
		$page = null;
		
		if ( is_object( $page_id ) )
			$page = $page_id;
		else
			$page = $this->page( $page_id, false );
			
		foreach ( $page->tabs as &$tab )
			if ( $tab->id == $tab_id ) return $tab;
			
		return false;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function tabExists( $tab_id, $page_id ) {
		return (bool) $this->tab( $page_id, $tab_id );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function isGoogleRelatedPage( $page_id ) {
		return ( $page_id == 'sitetree-google' );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function &googleDefaults() {
		return $this->defaults( 'google' );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function &html5Defaults() {
		return $this->defaults( 'html5' );
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function &defaults( $page_id = '' ) {
		$defaults = $pages = array();
		
		if ( $page_id )
			$pages[] = $this->page( $page_id );
		else
			$pages = &$this->pages;
		
		foreach ( $pages as $page ) {
			$this->loadPageSections( $page );
		
			foreach ( $page->sections as $index => $_section ) {
				// It's not a section but a group of sections instead.
				if ( is_array( $_section ) ) {
					$tab = $this->tab( $page, $index );
					
					foreach ( $_section as $section ) {
						if ( $section->id && isset( $tab->context ) ) {
							$defaults[$tab->context][$section->id]['content_type'] = $section->id;
							$defaults[$tab->context][$section->id]['callback']	   = $section->callback;
							
							foreach ( $section->fields as $field )
								$defaults[$tab->context][$section->id][$field->id] = $field->default;
								
							continue;
						}
						
						foreach ( $section->fields as $field )
							$defaults[$field->id] = $field->default;
					}
					
					continue;
				}
				
				foreach ( $_section->fields as $field )
					$defaults[$field->id] = $field->default;
			}
		}
			
		return $defaults;
	}
	
	/**
	 *
	 *
	 * @since 1.4
	 */
	public function &sanitiseData( &$data, $page_id, $tab_id = null ) {
		$output   = array();
		$tab	  = null;
		$page	  = $this->page( $page_id );
		$sections = &$page->sections;
		
		if ( $tab_id ) {
			$tab	  = $this->tab( $page, $tab_id );
			$sections = &$page->sections[ $tab->id ];
		}
		
		foreach ( $sections as $section ) {
			if ( $section->id && isset( $tab->context ) ) {
				$output[$tab->context][$section->id]['content_type'] = $section->id;
				$output[$tab->context][$section->id]['callback']	 = $section->callback;
				
				foreach ( $section->fields as $field ) {
					$value	   = isset( $data[$section->id][$field->id] ) ? $data[$section->id][$field->id] : null;
					$validator = new SiteTreeDataValidator( $value, $field->type, $field->default, $field->conditions );
					
					$output[$tab->context][$section->id][$field->id] = $validator->value();
				}
				
				continue;
			}
			
			foreach ( $section->fields as $field ) {
				$value	   = isset( $data[$field->id] ) ? $data[$field->id] : null;
				$validator = new SiteTreeDataValidator( $value, $field->type, $field->default, $field->conditions );
				
				$output[$field->id] = $validator->value();
			}
		}
		
		return $output;
	}
	
	/**
	 * Returns an array of 'page ID' => 'page title' pairs used 
	 * to render the drop-down menu of pages.
	 *
	 * @since 1.4
	 * @return array
	 */
	private function &listOfPagesOptions() {
		$indent		 = '';
		$indent_step = '&nbsp;&nbsp;&nbsp;';
		$pages		 = &get_pages( array( 'sort_column' => 'menu_order, post_title' ) );
		$options	 = array( __('&mdash; Select &mdash;', 'sitetree') );
		
		foreach ( $pages as $page ) {
			if ( $page->post_parent == 0 )
				$indent = '';
			else
				$indent .= $indent_step;
			
			$options[$page->ID] =  $indent . $page->post_title;
		}
		
		return $options;
	}
	
	/**
	 * Returns an array of 'page depth' => 'level title' pairs used 
	 * to render the drop-down menu of page levels.
	 *
	 * @since 1.4
	 * @return array
	 */
	private function &pageDepthOptions() {
		$options = array( __('All', 'sitetree') );
		$labels  = array(
			__('Main', 'sitetree'), __('First', 'sitetree'), __('Second', 'sitetree'), 
			__('Third', 'sitetree'), __('Fourth', 'sitetree'), __('Fifth', 'sitetree')
		);
		
		$depth = $max_depth = 0;
		$pages = &get_pages();
		
		foreach ( $pages as $page ) {
			if ( ( $page->post_parent > 0 ) && ( ++$depth > $max_depth ) ) {
				$options[] = $labels[$max_depth++];
			}
			else { $depth = 0; }
		}
		
		return $options;
	}
}

/**
 *
 *
 * @since 1.4
 */
class SiteTreeDataValidator {
	/**
	 * @since 1.4
	 * @var mixed
	 */
	public $value;

	/**
	 * @since 1.4
	 * @var mixed
	 */
	public $default;

	/**
	 * @since 1.4
	 * @var mixed
	 */
	public $conditions;
	
	/**
	 * Constructor method.
	 *
	 * @since 1.4
	 *
	 */
	public function __construct( $value, $type, $default, $conditions ) {
		if ( method_exists( $this, $type ) ) {
			$this->value = $value;
			$this->default = $default;
			$this->conditions = $conditions;
			
			$this->{$type}();		
		}
	}
	
	/**
	 * Constructor method.
	 *
	 * @since 1.4
	 * @return mixed
	 */
	public function value() {
		return $this->value;
	}
	
	/**
	 * Sanitises and validates a limited positive number.
	 *
	 * @since 1.4
	 */
	private function positiveNumber() {
		$this->value = abs( (int) $this->value );
		
		if ( isset( $this->conditions['min_value'] ) )
			$this->value = max( $this->value, $this->conditions['min_value'] );
		
		if ( isset( $this->conditions['max_value'] ) )
			$this->value = min( $this->value, $this->conditions['max_value'] );
	}
	
	/**
	 * Validates a 'select' option by checking whether or not the 
	 * received value exists in the list of options.
	 *
	 * @since 1.4
	 */
	private function options() {
		if (! isset( $this->conditions[$this->value] ) )
			$this->value = $this->default;
	}
	
	/**
	 * Sanitises and validates html code.
	 *
	 * @since 1.4
	 */
	private function html() {
		$this->value = force_balance_tags( wp_kses_post($this->value) );
	}
	
	/**
	 * Sanitises html code with only specific tags allowed.
	 *
	 * @since 1.4
	 */
	private function restrictedHtml() {
		$this->value = force_balance_tags( wp_kses( $this->value, $this->conditions ) );
	}
	
	/**
	 * Sanitises html code with only specific tags allowed.
	 *
	 * @since 1.4
	 */
	private function plainText() {
		$this->value = wp_kses( $this->value, array() );
	}
	
	/**
	 * Sanitises a boolean value.
	 *
	 * @since 1.4
	 */
	private function bool() {
		$this->value = (bool) $this->value;
	}
	
	/**
	 * Validates and sanitises a comma separated list of integers.
	 *
	 * @since 1.4
	 */
	private function listOfNum() {
		if ( !( $ids = explode( ',', (string) $this->value ) ) )
			return ( $this->value =  '');
			
		$whitelist = array();
		
		foreach ( $ids as $id ) {
			$num = abs( (int) $id );
			
			if ( $num > 0 ) $whitelist[] = $num;
		}
		
		$this->value = implode( ', ', $whitelist );
	}
	
	/**
	 * Sanitises plain text.
	 *
	 * @since 1.4
	 */
	private function text() {
		$this->value = sanitize_text_field( $this->value );
	}
	
	/**
	 * Sanitises a filename without extension.
	 *
	 * @since 1.5
	 */
	private function filename() {
		$this->value = strtolower( preg_replace( array( '/\.[^.]+$/', '/[^a-z0-9\-]/i' ), '', $this->value ) );
		
		// Empty string not allowed
		if (! $this->value )
			$this->value = $this->default;
		
		// Max. 20 characters
		elseif ( strlen( $this->value ) > 20 )
			$this->value = substr( $this->value, 0, 20 );
	}
	
	/**
	 * Sanitises and validates a comma separated list of author's public names.
	 *
	 * @since 1.4
	 */
	private function listOfAuthors() {
		$whitelist = array();
		$query	   = new WP_User_Query( array( 'who' => 'authors' ) );
		$authors   = $query->get_results();
		
		foreach ( $authors as $author ) {
			if ( stripos( $this->value, $author->display_name ) !== false )
				$whitelist[] = $author->display_name;
		}
		
		$this->value = implode( ', ', $whitelist );
	}
}
?>