<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */
 
if (! defined( 'ABSPATH' ) ) exit;

// --------------------------------------------------------------------------


$this->pages[$page_number]->sections = array(
	new SiteTreeSection( '', '', '', array(
		new SiteTreeField( 'page_for_sitemap', 'Dropdown', 'positiveNumber', 
			__( 'Choose the page where you want to show your Archive', 'sitetree' ), '', 0, $this->listOfPagesOptions()
		),
		new SiteTreeField( 'show_credits', 'Checkbox', 'bool', __( 'An hand to spread the word?', 'sitetree' ), 
			sprintf( __( 'The text %sPowered by %s will appear just below the Archive.', 'sitetree' ), '<em>&quot;', 'SiteTree&quot;</em>' )
		),
	))
);
?>