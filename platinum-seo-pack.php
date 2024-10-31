<?php

/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Description: Complete SEO and Social optimization solution for your Wordpress blog/site. It is Simple, Uncomplicated and User friendly with several useful features.
Version: 2.4.0
Author: Techblissonline.com (Rajesh)
Author URI: https://techblissonline.com/
Text Domain: platinum-seo-pack
Domain Path: /languages
License: GPLv2 or later
*/

/*
Copyright (C) 2008-2020, Techblissonline (https://techblissonline.com)
- Founder - Rajesh

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

add_action( 'plugins_loaded', 'psp_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
 
 define ( 'PSP_PLUGIN_HOME', dirname ( __FILE__ ) . '/' );
 define( 'PSPINC', 'psp-include' );
 
 // Set the plugin URL root.
define( 'PSP_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
define( 'PSP_PLUGIN_SETTINGS_URL', PSP_PLUGIN_HOME. PSPINC. '/settings' );
 
function psp_load_textdomain() {
  load_plugin_textdomain( 'platinum-seo-pack', false, PSP_PLUGIN_HOME . 'languages/' ); 
}

// Load early WordPress files.
include_once( PSP_PLUGIN_HOME . '/psp_main.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/utilities/psp_helper.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/generators/psp_home_others_seo_metas.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/generators/psp_pts_seo_metas.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/generators/psp_tax_seo_metas.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/generators/psp_social_metas.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/generators/breadcrumbs.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/generators/psp_rest.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/settings/psp_settings.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/settings/psp_pre_settings.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/settings/psp_rolecap_settings.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/settings/psp_social_settings.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/settings/psp_tools_settings.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/settings/psp_redirect_404.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/sitemap/psp_sitemap_settings.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/importers/psp_import.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/wizard/psp_wizard_settings.php' );
include_once( PSP_PLUGIN_HOME . PSPINC . '/sitemap/psp_sitemap.php' );

global $psp;
$psp = PspMain::get_instance();

global $psp_version;
$psp_version = '2.4.0';

global $psp_db_version;
$psp_db_version = '2.1.0';

register_activation_hook ( __FILE__, array ($psp, 'psp_activate' ) );
register_deactivation_hook ( __FILE__, array ($psp, 'psp_deactivate' ) );
add_action( 'upgrader_process_complete', array ($psp, 'psp_plugin_upgrade'),10, 2);
register_activation_hook ( __FILE__, array ($psp, 'psp_db_install' ) );
register_activation_hook ( __FILE__, array ($psp, 'psp_version_upgrade' ) );
add_action( 'plugins_loaded', array ($psp, 'psp_version_upgrade'),10, 2);
add_action( 'plugins_loaded', array ($psp, 'psp_db_install'),10, 2);
add_action( 'plugins_loaded', array ($psp, 'psp_loaded_filter'),10, 1);

?>