<?php
/*
Plugin Name: Cloudy Tags
Plugin URI: http://www.someblogsite.com/web-stuff/cloudy-tags
Description: Displays the tag cloud so that the tags have a cloudy appearance
Author: Some Guy
Version: 1.0
Author URI: http://www.someblogsite.com

This file is part of Cloudy Tags

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/ 

/* Load Helper Functions */
require(WP_PLUGIN_DIR.'/cloudy-tags/functions.php');

/* Load Non-Widget Tag Config Page */
include(WP_PLUGIN_DIR.'/cloudy-tags/admin.php');
add_action( 'wp_head', 'get_head_non_widget');

/* Load WP Sidebar Widget */
if (class_exists('WP_Widget')) {
    include(WP_PLUGIN_DIR.'/cloudy-tags/widget.php');
    add_action( 'wp_head', 'get_head_widget');
}


add_shortcode( 'cloudytags', 'cloudytags_shortcode' );

function cloudytags_shortcode() {
    $before = '<div class="cloudy-tags-shortcode" >';
    $after = '</div>';
    $cloud = non_widget_cloudy_tags();
    return $before . $cloud . $after;
}

register_activation_hook(__FILE__,'install_defs');
register_deactivation_hook(__FILE__,'uninstall_defs');

function CloudyTagsCss() {
    return(array(
        'ct_opt_title' => 'font-weight: bold;',  // CSS for the admin/widget options - item title
        'ct_opt_item'  => 'text-align: right;',  // CSS for the admin/widget options - item
        'ct_opt_desc'  => 'font-size:0.8em',     // CSS for the admin/widget options - item description
    ));
}
?>