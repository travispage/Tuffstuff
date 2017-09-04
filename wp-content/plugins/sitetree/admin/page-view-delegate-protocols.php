<?php
/**
 * @package SiteTree
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * -------------------------------------------------------------------------- */


/**
 * The delegate protocol a PageView Controller must implement.
 *
 * @since 1.4
 */
interface SiteTreePageViewDelegateProtocol {
	/**
	 *
	 *
	 * @since 1.5
	 *
	 * @param object $pageView
	 * @return string Any data to echo
	 */
	public function pageViewDidStartRendering( $pageView );
	
	/**
	 * 
	 *
	 * @since 1.4
	 *
	 * @param object $field
	 * @param object $section
	 * @param mixed $args
	 * @return mixed The value to display in the field that is rendering.
	 */
	public function pageViewFieldValue( $field, $section, $args );
	
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param object $pageView
	 * @return string Any data to echo
	 */
	public function pageViewWillRenderFormContent( $pageView );
	
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param object $pageView
	 * @return string Any data to echo
	 */
	public function pageViewDidRenderFormContent( $pageView );
}


/**
 * Delegate protocol the Dashboard controller must implement.
 *
 * @since 1.4
 */
interface SiteTreeDashboardDelegateProtocol extends SiteTreePageViewDelegateProtocol {
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param object $dashboard
	 * @return bool
	 */
	public function dashboardCanDisplayMessage( $dashboard );
	
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param object $dashboard
	 * @param int $form_index
	 * @return bool
	 */
	public function dashboardCanRenderStats( $dashboard, $form_index );
	
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param object $dashboard
	 * @param int $form_index
	 * @return string Any data to echo
	 */
	public function dashboardWillRenderToolbarButtons( $dashboard, $form_index );
	
	/**
	 *
	 *
	 * @since 1.5
	 *
	 * @param object $dashboard
	 * @return string Any data to echo
	 */
	public function dashboardWillFinishRendering( $dashboard );
}


/**
 * Delegate protocol a TabbedPageView controller must implement.
 *
 * @since 1.4
 */
interface SiteTreeTabbedPageViewDelegateProtocol extends SiteTreePageViewDelegateProtocol {
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param int $index Index of the tab item is rendering.
	 * @param string $tab_id
	 * @return bool
	 */
	public function tabbedPageViewCanSetTabActive( $index, $tab_id );
	
	/**
	 *
	 *
	 * @since 1.4
	 *
	 * @param object $pageView
	 * @param string $tab_id
	 * @return string The href value of the tab item is rendering.
	 */
	public function tabbedPageViewTabUrl( $pageView, $tab_id );
}
?>