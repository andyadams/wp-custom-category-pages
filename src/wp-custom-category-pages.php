<?php
/*
Plugin Name: WP Custom Category Pages
Plugin URI: #
Description: #.
Version: 1.0
Author: #
Author URI: #
License: GPL2
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

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

require_once( dirname( __FILE__ ) . '/vendor/Tax-Meta-Class/Tax-meta-class/Tax-meta-class.php' );

function ccp_admin_init() {
	if ( is_admin() ) {
		$config = array(
			'id' => 'ccp_category_meta',          // meta box id, unique per meta box
			'title' => 'Custom Category Meta',          // meta box title
			'pages' => array( 'category' ),        // taxonomy name, accept categories, post_tag and custom taxonomies
			'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
			'fields' => array(),            // list of meta fields (can be added by field arrays)
			'local_images' => true,          // Use local or hosted images (meta box images for add/remove)
			'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
		);

		$my_meta =  new Tax_Meta_Class( $config );

		$my_meta->addWysiwyg('text_field_id',array('name'=> __('My Text ','tax-meta')));

		$my_meta->Finish();
	}
}

add_action( 'init', 'ccp_admin_init' );