<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */

if (! defined( 'ABSPATH' ) ) exit;

// --------------------------------------------------------------------------


// Collection of messages used more than once — just a way to cut down on unnecessary function calls.
// The elements of type Array contain the title of the field at index 0 and its description/tooltip at index 1.
$common_l10n = array(
	'include'	 => array( __( 'Include', 'sitetree' ), __( 'Check to include', 'sitetree' ) ),
	'title'		 => __( 'List title', 'sitetree' ),
	'show_count' => __( 'Posts count', 'sitetree' ),
	'exclude'	 => array( __( 'Exclude', 'sitetree' ), __( 'Type in a comma separated list of IDs.', 'sitetree' ) ),
	'orderby'	 => __( 'Order by', 'sitetree' )
);

// Common values.
$list_style_options = array(
	'1' => __( 'Hierarchical', 'sitetree' ),
	'0' => __( 'Flat', 'sitetree' )
);

$orderby_options = array(
	'name'	=> __( 'Name', 'sitetree' ),
	'count' => __( 'Most used', 'sitetree' )
);

// --------------------------------------------------------------------------

$this->pages[$page_number]->sections['general'] = array(
	new SiteTreeSection( 'authors', '', __( 'Authors', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1] ),
		new SiteTreeField( 'title', 'TextField', 'text', $common_l10n['title'], '', __( 'Authors', 'sitetree' ) ),
		new SiteTreeField( 'show_count', 'Checkbox', 'bool', $common_l10n['show_count'],
			__( 'Show the number of posts published by each author.', 'sitetree' ), true
		),
		new SiteTreeField( 'show_avatar', 'Checkbox', 'bool', __( 'Avatar', 'sitetree' ), __("Show the author's avatar.", 'sitetree') ),
		new SiteTreeField( 'avatar_size', 'NumberField', 'positiveNumber', __( 'Avatar size', 'sitetree' ), 
			__( 'Choose a value within the range 20px - 512px.', 'sitetree' ), 60, array( 'min_value' => 20, 'max_value' => 512 )
		),
		new SiteTreeField( 'show_bio', 'Checkbox', 'bool', __( 'Biographical info', 'sitetree' ), 
			sprintf( __("Show the biographical info set in the author's %sprofile page%s.", 'sitetree'), 
					 '<a href="' . admin_url( 'users.php' ) . '">', '</a>' )
		),
		new SiteTreeField( 'exclude', 'TextField', 'listOfAuthors', $common_l10n['exclude'][0], 
			__( 'Type in a comma separated list of public names.', 'sitetree' ), ''
		),
		new SiteTreeField( 'orderby', 'Dropdown', 'options', $common_l10n['orderby'], '', 'display_name', array(
			'display_name'	=> __( 'Name', 'sitetree' ),
			'posts_count'	=> __( 'Published posts', 'sitetree' )
		)),
	)),
	new SiteTreeSection( 'page', 'pages', __( 'Pages', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1], true ),
		new SiteTreeField( 'title', 'TextField', 'text', $common_l10n['title'], '', __( 'Pages', 'sitetree' ) ),
		new SiteTreeField( 'show_home', 'Checkbox', 'bool', __( 'Home page', 'sitetree' ), 
			__( 'Show a &#8220;Home&#8221; link at the top of the list.', 'sitetree' )
		),
		new SiteTreeField( 'list_style', 'Dropdown', 'options', __( 'List style', 'sitetree' ), '', '1', $list_style_options ),
		new SiteTreeField( 'depth', 'Dropdown', 'options', __( 'Levels to show', 'sitetree' ), '', 0, $this->pageDepthOptions() ),
	)),
	new SiteTreeSection( 'archives', '', __( 'Archives', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1], true ),
		new SiteTreeField( 'title', 'TextField', 'text', $common_l10n['title'], '', __( 'Archives', 'sitetree' ) ),
		new SiteTreeField( 'show_count', 'Checkbox', 'bool', $common_l10n['show_count'], 
			__( 'Show the number of posts each archive stores.', 'sitetree' ), true
		)
	)),
	new SiteTreeSection( 'categories', '', __( 'Categories', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1] ),
		new SiteTreeField( 'title', 'TextField', 'text', $common_l10n['title'], '', __( 'Categories', 'sitetree' ) ),
		new SiteTreeField( 'exclude', 'TextField', 'listOfNum', $common_l10n['exclude'][0], $common_l10n['exclude'][1], '' ),
		new SiteTreeField( 'show_count', 'Checkbox', 'bool', $common_l10n['show_count'], 
			__( 'Show the number of posts published under each category.', 'sitetree' ), true
		),
		new SiteTreeField( 'feed_text', 'TextField', 'text', __("Text of the link to each category's RSS feed", 'sitetree'), 
			__( 'Leave empty to hide the link.', 'sitetree' ), 'RSS', 'small-text'
		),
		new SiteTreeField( 'list_style', 'Dropdown', 'options', __( 'List style', 'sitetree' ), '', '1', $list_style_options ),
		new SiteTreeField( 'orderby', 'Dropdown', 'options', $common_l10n['orderby'], '', 'name', $orderby_options ),
	)),
	new SiteTreeSection( 'tags', '', __( 'Tags', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1] ),
		new SiteTreeField( 'title', 'TextField', 'text', $common_l10n['title'], '', __( 'Tags', 'sitetree' ) ),
		new SiteTreeField( 'exclude', 'TextField', 'listOfNum', $common_l10n['exclude'][0], $common_l10n['exclude'][1], '' ),
		new SiteTreeField( 'show_count', 'Checkbox', 'bool', $common_l10n['show_count'],
			__( 'Show the number of posts published under each tag.', 'sitetree' )
		),
		new SiteTreeField( 'orderby', 'Dropdown', 'options', $common_l10n['orderby'], '', 'name', $orderby_options ),
	)),
	new SiteTreeSection( 'post', 'posts', __( 'Posts', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1], true ),
		new SiteTreeField( 'title', 'TextField', 'text', $common_l10n['title'], '', __( 'Posts', 'sitetree' ) ),
		new SiteTreeField( 'groupby', 'Dropdown', 'options', __( 'Group by', 'sitetree' ), '', 'none', array(
			'none'		=> '-', 
			'date'		=> __( 'Date', 'sitetree' ),
			'category'	=> __( 'Category', 'sitetree' ),
			'author'	=> __( 'Author', 'sitetree' )
		)),
		new SiteTreeField( 'category_label', 'TextField', 'restrictedHtml', __( 'Category label', 'sitetree' ),  
			sprintf( __( 'Allowed tags: %s, %s, %s.', 'sitetree' ), '<code>&lt;em&gt;</code>', 
				'<code>&lt;span&gt;</code>', '<code>&lt;strong&gt;</code>'
			), 
			'', null, array( 'strong' => true, 'span' => true, 'em' => true )
		),
		new SiteTreeField( 'orderby', 'Dropdown', 'options', $common_l10n['orderby'], '', 'post_date', array(
			'post_date'		=> __( 'Most recent', 'sitetree' ),
			'comment_count' => __( 'Most popular', 'sitetree' ),
			'post_title'	=> __( 'Title', 'sitetree' )
		)),
		new SiteTreeField( 'show_comments_count', 'Checkbox', 'bool', __( 'Comments count', 'sitetree' ), 
			__( 'Show&mdash;if there are any&mdash;the number of comments submitted for each post.', 'sitetree' )
		),
		new SiteTreeField( 'show_date', 'Checkbox', 'bool', __( 'Published date', 'sitetree' ), 
			sprintf( __('Show the date of publication for each post (the format is set by the &#8220;Date format&#8221; ' 
					  . 'setting in the %sGeneral Settings%s page).', 'sitetree'), '<a href="' . admin_url('options-general.php') . '">', '</a>'
			)
		),
		new SiteTreeField( 'limit', 'NumberField', 'positiveNumber', __( 'Max. number of entries', 'sitetree' ), 
			sprintf( __( 'The total limit set in the %sUnder the Hood tab%s takes priority.', 'sitetree'), 
				'<a href="' . SiteTreeUtilities::adminURL( $this->page( 'html5', false )->id, array( 'tab' => 'hood' ) ) . '">', '</a>'
			), 
			200, array( 'min_value' => 5, 'max_value' => 1000 )
		),		
	))
);

$this->pages[$page_number]->sections['hood'] = array(
	new SiteTreeSection( '', '', '', array(
		new SiteTreeField( 'items_limit', 'NumberField', 'positiveNumber', __( 'Max. number of items', 'sitetree' ), 
			__( "To keep high the user experience, it's recommended to stay under 300 items.", 'sitetree' ), 200, 
			array( 'min_value' => 50, 'max_value' => 1000 )
		),
		new SiteTreeField( 'title_tag', 'Dropdown', 'options', __( 'Heading tag', 'sitetree' ), 
			__( 'Tag applied to each title&mdash;if any has been set.', 'sitetree' ), 'h3', array( 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4' )
		),
		new SiteTreeField( 'list_wrapper', 'Dropdown', 'options', __( 'Wrapper tag', 'sitetree' ), 
			__( 'Each list will be enclosed within this tag.', 'sitetree' ), '0', array('0' => '-', 'div' => 'div', 'section' => 'section')
		),
		new SiteTreeField( 'trailing_html', 'Textarea', 'html', __( 'Additional HTML code to put at the end of each list', 'sitetree' ), 
			__( 'You can use all the tags allowed in the post / page content editor.', 'sitetree' ), ''
		),
		new SiteTreeField( 'css_code', 'Textarea', 'plainText', __( 'Type in a few lines of CSS to style your Archive', 'sitetree' ),
			sprintf( __("You don't need to markup the code with the %s tag.", 'sitetree'), '<code>&lt;style&gt;</code>'), 
			'#sitetree-credits {font-size:90%; text-align:right;}'
		)
	))
);
?>