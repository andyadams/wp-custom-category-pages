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

function ccp_plugin_admin_init() {
	if ( is_admin() ) {
		$config = array(
			'id' => 'ccp_plugin_category_meta',
			'title' => 'Custom Category Meta',
			'pages' => array( 'category' ),
			'context' => 'normal',
			'fields' => array(),
			'local_images' => true,
			'use_with_theme' => false
		);

		$my_meta =  new Tax_Meta_Class( $config );

		$my_meta->addText( 'headline', array( 'name' => __( 'Category Headline', 'ccp_plugin' ) ) );
		$my_meta->addText( 'page_title', array( 'name' => __( 'Category Page Title', 'ccp_plugin' ) ) );
		$my_meta->addWysiwyg( 'copy', array( 'name' => __( 'Category Copy', 'ccp_plugin' ) ) );

		$my_meta->Finish();
	}
}

add_action( 'init', 'ccp_plugin_admin_init' );

function ccp_plugin_add_fields_header() {
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><h3><?php _e( 'Custom Category Pages', 'ccp_plugin' ); ?></h3></th>
	</tr>
	<?php
}

add_action( 'category_edit_form_fields', 'ccp_plugin_add_fields_header' );
