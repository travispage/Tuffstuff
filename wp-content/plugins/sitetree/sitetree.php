<?php
/**
 * Plugin Name: SiteTree
 * Plugin URI: http://sitetreeplugin.com/
 * Description: A lightweight and user-friendly tool to enhance your WordPress site with feature-loaded Google Sitemap and Archive Page.
 * Version: 1.5.3
 * Author: Luigi Cavalieri
 * Author URI: http://cavalieri.io/
 * License: GPL v2.0
 * License URI: license.txt
 *
 *
 * @package SiteTree
 * @version 1.5.3
 * @author Luigi Cavalieri
 * @license http://opensource.org/licenses/GPL-2.0 GPLv2.0 Public license
 * 
 * 
 * Copyright (c) 2013 Luigi Cavalieri, http://cavalieri.io
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * ---------------------------------------------------------------------------------- */

if ( defined('ABSPATH') ) {
	include( dirname(__FILE__) . '/core/sitetree.class.php' );
	
	SiteTree::launch( __FILE__ );
}
?>