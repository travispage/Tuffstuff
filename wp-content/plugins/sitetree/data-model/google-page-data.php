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
	'priority'	 => __( 'Priority', 'sitetree' ),
	'changefreq' => __( 'Update frequency', 'sitetree' ),
	'exclude'	 => array( __( 'Exclude', 'sitetree' ), __( 'Type in a comma separated list of IDs.', 'sitetree' ) )
);

// Common values.
$frequencies = SiteTreeUtilities::frequencies();
$prio_80	 = SiteTreeUtilities::priorities( 0.8 );
$prio_90	 = SiteTreeUtilities::priorities( 0.9 );

// --------------------------------------------------------------------------

$this->pages[$page_number]->sections['general'] = array(
	new SiteTreeSection( '', '', '', array(
		new SiteTreeField( 'home_changefreq', 'Dropdown', 'options', __( 'Home page update frequency', 'sitetree' ), '', 'daily', $frequencies ),
		new SiteTreeField( 'images', 'Checkbox', 'bool', __( 'Images', 'sitetree' ), 
			__( 'Include all the images attached to posts and pages.', 'sitetree' ), true
		),
	)),
	new SiteTreeSection( 'page', 'posts', __( 'Pages', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1], true ),
		new SiteTreeField( 'priority', 'Dropdown', 'options', $common_l10n['priority'], '', '0.8', $prio_90 ),
		new SiteTreeField( 'changefreq', 'Dropdown', 'options', $common_l10n['changefreq'], '', 'weekly', $frequencies ),
	)),
	new SiteTreeSection( 'post', 'posts', __( 'Posts', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1], true ),
		new SiteTreeField( 'priority', 'Dropdown', 'options', $common_l10n['priority'], '', '0.6', $prio_90 ),
		new SiteTreeField( 'changefreq', 'Dropdown', 'options', $common_l10n['changefreq'], '', 'monthly', $frequencies ),
	)),
	new SiteTreeSection( 'category', 'taxonomies', __( 'Categories', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1] ),
		new SiteTreeField( 'priority', 'Dropdown', 'options', $common_l10n['priority'], '', 'none', $prio_80 ),
		new SiteTreeField( 'changefreq', 'Dropdown', 'options', $common_l10n['changefreq'], '', 'none', $frequencies ),
		new SiteTreeField( 'exclude', 'TextField', 'listOfNum', $common_l10n['exclude'][0], $common_l10n['exclude'][1], '' ),
	)),
	new SiteTreeSection( 'authors', '', __( 'Authors', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1] ),
		new SiteTreeField( 'priority', 'Dropdown', 'options', $common_l10n['priority'], '', 'none', $prio_80 ),
		new SiteTreeField( 'changefreq', 'Dropdown', 'options', $common_l10n['changefreq'], '', 'none', $frequencies ),
	)),
	new SiteTreeSection( 'archives', '', __( 'Archives', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1] ),
		new SiteTreeField( 'priority', 'Dropdown', 'options', $common_l10n['priority'], '', 'none', $prio_80 ),
		new SiteTreeField( 'changefreq', 'Dropdown', 'options', $common_l10n['changefreq'], '', 'none', $frequencies ),
	)),
	new SiteTreeSection( 'post_tag', 'taxonomies', __( 'Tags', 'sitetree' ), array(
		new SiteTreeField( 'include', 'Checkbox', 'bool', $common_l10n['include'][0], $common_l10n['include'][1] ),
		new SiteTreeField( 'priority', 'Dropdown', 'options', $common_l10n['priority'], '', 'none', $prio_80 ),
		new SiteTreeField( 'changefreq', 'Dropdown', 'options', $common_l10n['changefreq'], '', 'none', $frequencies ),
		new SiteTreeField( 'exclude', 'TextField', 'listOfNum', $common_l10n['exclude'][0], $common_l10n['exclude'][1], '' ),
	)),
);

$this->pages[$page_number]->sections['advanced'] = array(
	new SiteTreeSection( '', '', '', array(
		new SiteTreeField( 'filename', 'TextField', 'filename', __( 'Filename', 'sitetree' ),
			__( 'Whenever you change it, remember to redirect (with a 301 status code) the old permalink to the new one '
			  . 'and to update your Google and Bing Webmaster Accounts.', 'sitetree' ), 'sitemap', array( 'tooltip_pos' => 'bottom' )
		),
		new SiteTreeField( 'ping', 'Checkbox', 'bool', __( 'Ping search engines', 'sitetree' ),
			__( 'Notify Google, Bing and Yahoo when a new post/page is added to the Sitemap.', 'sitetree' ), true
		),
		new SiteTreeField( 'do_robots', 'Checkbox', 'bool', __( 'Add excluded content to Robots.txt', 'sitetree' ),
			__( 'List all the posts and the pages excluded from the Sitemap in the Robots.txt file created by WordPress.', 'sitetree' )
		),
		new SiteTreeField( 'permalink_in_robots', 'Checkbox', 'bool', __( 'Add permalink to Robots.txt', 'sitetree' ),
			__( 'Append the location of the Sitemap to the Robots.txt file created by WordPress.', 'sitetree' )
		),
	)),
);
?>