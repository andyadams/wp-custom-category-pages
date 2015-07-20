<?php
/*
Plugin Name: WP Custom Category Pages
Plugin URI: http://geoffkenyon.com/wp-custom-category-pages
Description: WP Custom Category Pages lets you turn your category pages into useful landing pages that are good for SEO by adding custom content. Transform your category pages from thin pages full of duplicate content to user focused and SEO friendly landing pages.
Version: 2.0
Author: Geoff Kenyon
Author URI: http://geoffkenyon.com
License: GPL2
*/

/*  Copyright 2013  Geoff Kenyon  (email: geoff@geoffkenyon.com)

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

function wp_ccp_plugin_admin_init() {
	if ( is_admin() ) {
		$config = array(
			'id' => 'wp_ccp_plugin_category_meta',
			'title' => 'Custom Category Meta',
			'pages' => array( 'category' ),
			'context' => 'normal',
			'fields' => array(),
			'local_images' => true,
			'use_with_theme' => false
		);

		$my_meta =  new Tax_Meta_Class( $config );

	    $my_meta->addSelect(
			'custom_content_enabled',
			array(
				'1' => __( 'Yes', 'wp_ccp_plugin' ),
				'0'=> __( 'No', 'wp_ccp_plugin' )
			),
			array(
				'name' => __( 'Use Custom Category Pages content for this category?', 'wp_ccp_plugin' ),
				'std' => array( '0' )
			)
		);

		$my_meta->addWysiwyg( 'copy', array( 'name' => __( 'Category Copy', 'wp_ccp_plugin' ) ) );

		$my_meta->Finish();
	}
}

add_action( 'init', 'wp_ccp_plugin_admin_init' );

function wp_ccp_plugin_add_fields_header() {
	?>
	<tr class="form-field">
		<th scope="row" valign="top" colspan="2">
			<h2 id="custom-category-pages"><?php _e( 'WP Custom Category Pages', 'wp_ccp_plugin' ); ?></h2>
			<p><?php _e( 'Enter your custom content below. Make sure that you have turned on custom content for this category to see your changes.', 'wp_ccp_plugin' ); ?></p>
		</th>
	</tr>
	<?php
}

add_action( 'category_edit_form_fields', 'wp_ccp_plugin_add_fields_header' );

function wp_ccp_plugin_is_custom_content_enabled( $category_id = NULL ) {
	if ( NULL === $category_id ) {
		$category_id = get_queried_object_id();
	}

	$is_enabled = get_tax_meta( $category_id, 'custom_content_enabled' );

	if ( '' === $is_enabled ) {
		// If no value is set, then by default it will be enabled
		$is_enabled = true;
	}

	return (boolean) $is_enabled;
}

function wp_ccp_plugin_admin_enqueue_scripts() {
	global $pagenow;

	if ( ( isset( $_REQUEST['page'] ) && 'wp_ccp_plugin' == $_REQUEST['page'] ) || ( 'edit-tags.php' == $pagenow && isset( $_REQUEST['taxonomy'] ) && 'category' == $_REQUEST['taxonomy'] ) ) {
		wp_enqueue_style(
			'wp_ccp_plugin_admin',
			plugins_url( 'stylesheets/admin.css', __FILE__ )
		);
	}
}

add_action( 'admin_enqueue_scripts', 'wp_ccp_plugin_admin_enqueue_scripts' );

function wp_ccp_register_menu_pages() {
	$title = __( 'WP Custom Category Pages', 'wp_ccp_plugin' );
	add_menu_page( $title, $title, 'manage_categories', 'wp_ccp_plugin', 'wp_ccp_plugin_settings_page' );
}

add_action( 'admin_menu', 'wp_ccp_register_menu_pages' );

function wp_ccp_plugin_settings_page() {
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2><?php _e( 'WP Custom Category Pages', 'wp_ccp_plugin' ); ?></h2>
		<p><?php echo sprintf( __( 'Plugin by: %s', 'wp_ccp_plugin' ), '<a href="http://geoffkenyon.com">Geoff Kenyon</a>' ); ?></p>
		<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) : ?>
			<div class="updated">
				<p><strong><?php _e( 'Settings updated.', 'iproperty' ); ?></strong></p>
			</div>
		<?php endif; ?>
		<div id="wp_ccp_plugin-admin-description">
			<p>
				<?php _e( 'To get started creating awesome category pages, simply click edit for the category that you would like to modify. There you can enter a custom headline and page title along with custom content by using the WordPress editor.', 'wp_ccp_plugin' ); ?>
			</p>
		</div>
		<div id="wp_ccp_plugin-admin-category-list-container">
			<h3><?php _e( 'Categories', 'wp_ccp_plugin' ); ?></h3>
			<?php $categories = get_categories( array( 'hide_empty' => false ) ); ?>
			<?php foreach ( $categories as $category ) : ?>
				<div class="wp_ccp_plugin-admin-single-category clearfix">
					<h4><?php echo esc_html( $category->name ); ?> <a href="<?php echo esc_url( admin_url( 'edit-tags.php?action=edit&taxonomy=category&tag_ID=' . intval( $category->term_id ) ) ); ?>#custom-category-pages"><?php _e( 'edit this category', 'wp_ccp_plugin' ); ?></a></h4>
					<div class="wp_ccp_plugin-admin-category-details-container">
						<span class="wp_ccp_plugin-admin-category-details-label"><?php _e( "WPCCP enabled:", 'wp_ccp_plugin' ); ?></span>
						<?php $enabled = get_tax_meta_strip( $category->term_id, 'custom_content_enabled' ); ?>
						<span class="wp_ccp_plugin-admin-category-details-value"><?php echo esc_html( $enabled ? __( 'Yes', 'wp_ccp_plugin' ) : __( 'No', 'wp_ccp_plugin' ) ); ?></span>
						<span class="wp_ccp_plugin-admin-category-details-label"><?php _e( "Category Copy:", 'wp_ccp_plugin' ); ?></span>
						<span class="wp_ccp_plugin-admin-category-details-value"><?php echo strip_tags( get_tax_meta_strip( $category->term_id, 'copy' ) ); ?></span>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

function wp_ccp_plugin_is_custom_category_page() {
	return is_category() && wp_ccp_plugin_is_custom_content_enabled();
}

function wp_ccp_category_content( $category_id = NULL ) {
	if ( empty( $category_id ) ) {
		$category_id = get_queried_object_id();
	}

	if ( wp_ccp_plugin_is_custom_category_page() ) {
		?>
		<div class="wp-ccp-content">
			<?php echo get_tax_meta_strip( $category_id, 'copy' ); ?>
		</div>
		<?php
	}
}

add_action( 'wp_ccp_category_content', 'wp_ccp_category_content' );