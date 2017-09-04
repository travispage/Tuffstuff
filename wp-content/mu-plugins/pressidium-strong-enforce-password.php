<?php
/**
Plugin Name: Enforce Strong Password
Plugin URI: http://wordpress.org/extend/plugins/enforce-strong-password
Description: Forces all users to have a strong password when they're changing it on their profile page.
Version: 1.3.5
Author: Zaantar
Author URI: http://zaantar.eu
Donate Link: http://zaantar.eu/financni-prispevek
License: GPL2
*/

/*
    Copyright (c) Zaantar (email: zaantar@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Load the 'Enforce Strong Password' plugin
if ( defined( 'PRESSIDIUM_FORCE_STRONG_PASSWORDS' ) && PRESSIDIUM_FORCE_STRONG_PASSWORDS ) {
    require_once(WPMU_PLUGIN_DIR.'/enforce-strong-password/enforce-strong-password.php');
}

