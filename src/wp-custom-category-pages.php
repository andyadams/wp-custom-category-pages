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

	    $my_meta->addSelect(
			'custom_content_enabled',
			array(
				'1' => __( 'Yes', 'ccp_plugin' ),
				'0'=> __( 'No', 'ccp_plugin' )
			),
			array(
				'name' => __( 'Use Custom Category Pages content for this category?', 'ccp_plugin' ),
				'std' => array( '0' )
			)
		);
		$my_meta->addText( 'heading', array( 'name' => __( 'Category Headline', 'ccp_plugin' ) ) );
		$my_meta->addText( 'page_title', array( 'name' => __( 'Category Page Title', 'ccp_plugin' ) ) );
		$my_meta->addWysiwyg( 'copy', array( 'name' => __( 'Category Copy', 'ccp_plugin' ) ) );

		$my_meta->Finish();
	}
}

add_action( 'init', 'ccp_plugin_admin_init' );

/**
 * Filters the plugin URL in case a bad URL is given due to symlinking
 */
function ccp_plugin_filter_plugins_url( $url, $path, $plugin ) {
	$real_path = realpath( dirname( __FILE__ ) );

	$url = str_replace( $real_path, '/wp-custom-category-pages', $url );

	return $url;
}

add_filter( 'plugins_url', 'ccp_plugin_filter_plugins_url', 10, 3 );

function ccp_plugin_add_fields_header() {
	?>
	<tr class="form-field">
		<th scope="row" valign="top" colspan="2"><h2 id="custom-category-pages"><?php _e( 'WP Custom Category Pages', 'ccp_plugin' ); ?></h2></th>
	</tr>
	<?php
}

add_action( 'category_edit_form_fields', 'ccp_plugin_add_fields_header' );

function ccp_plugin_main_container_class( $classes = array() ) {
	if ( ! is_array( $classes ) ) {
		$classes = array( $classes );
	}

	/*
	$current_theme = wp_get_theme();

	if ( 'twentytwelve' == $current_theme->get_template() ) {
		$classes[] = 'site-content';
	}
	*/

	apply_filters( 'ccp_plugin_main_container_classes', $classes );

	if ( ! empty( $classes ) ) {
		echo ' class="' . implode( ' ', $classes ) . '" ';
	}
}

function ccp_plugin_wp_title( $title ) {
	if ( is_category() && ccp_plugin_is_custom_content_enabled() ) {
		$new_title = get_tax_meta_strip( get_queried_object_id(), 'page_title' );
		if ( ! empty( $new_title ) ) {
			$title = $new_title;
		}
	}

	return $title;
}

add_filter( 'wp_title', 'ccp_plugin_wp_title', 20 );

function ccp_plugin_single_cat_title( $title ) {
	if ( is_category() && ccp_plugin_is_custom_content_enabled() ) {
		$new_title = get_tax_meta_strip( get_queried_object_id(), 'heading' );
		if ( ! empty( $new_title ) ) {
			$title = $new_title;
		}
	}

	return $title;
}

add_filter( 'single_cat_title', 'ccp_plugin_single_cat_title', 100 );

function ccp_plugin_category_description( $description, $category_id ) {
	if ( ccp_plugin_is_custom_content_enabled( $category_id ) ) {
		$new_description = get_tax_meta_strip( $category_id, 'copy' );
		if ( ! empty( $new_description ) ) {
			$description = $new_description;
		}
	}

	return $description;
}

add_filter( 'category_description', 'ccp_plugin_category_description', 10, 2 );

function ccp_plugin_is_custom_content_enabled( $category_id = NULL ) {
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

function ccp_plugin_template_redirect() {
	if ( is_category() && ccp_plugin_is_custom_content_enabled() ) {
		include( dirname( __FILE__ ) . '/templates/archive_category.php' );
		exit;
	}
}

add_action( 'template_redirect', 'ccp_plugin_template_redirect' );

function ccp_modify_post_count( &$query ) {
	if ( is_category() && ccp_plugin_is_custom_content_enabled() ) {
		$query->set( 'posts_per_page', 25 );
	}
}

add_action( 'pre_get_posts', 'ccp_modify_post_count' );

function ccp_plugin_enqueue_scripts() {
	if ( is_category() && ccp_plugin_is_custom_content_enabled() ) {
		wp_enqueue_style(
			'ccp_plugin_category_archive',
			plugins_url( 'stylesheets/category.css', __FILE__ )
		);
	}
}

add_action( 'wp_enqueue_scripts', 'ccp_plugin_enqueue_scripts' );

function ccp_plugin_admin_enqueue_scripts() {
	if ( is_admin() && 'ccp_plugin' == $_REQUEST['page'] ) {
		wp_enqueue_style(
			'ccp_plugin_admin',
			plugins_url( 'stylesheets/admin.css', __FILE__ )
		);
	}
}

add_action( 'admin_enqueue_scripts', 'ccp_plugin_admin_enqueue_scripts' );

function ccp_register_menu_pages() {
	$title = __( 'WP Custom Category Pages', 'ccp_plugin' );
	add_menu_page( $title, $title, 'manage_categories', 'ccp_plugin', 'ccp_plugin_settings_page' );
}

add_action( 'admin_menu', 'ccp_register_menu_pages' );

function ccp_plugin_settings_page() {
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2><?php _e( 'WP Custom Category Pages', 'ccp_plugin' ); ?></h2>
		<p><?php echo sprintf( __( 'By: %s', 'ccp_plugin' ), '<a href="#">Author here</a>' ); ?></p>
		<div id="ccp_plugin-admin-description">
			<p>
				<?php _e( 'This plugin allows you to add custom copy and headlines to the top of your category pages.', 'ccp_plugin' ); ?>
			</p>
		</div>
		<div id="ccp_plugin-admin-category-list-container">
			<h3><?php _e( 'Categories', 'ccp_plugin' ); ?></h3>
			<?php $categories = get_categories( array( 'hide_empty' => false ) ); ?>
			<?php foreach ( $categories as $category ) : ?>
				<div class="ccp_plugin-admin-single-category clearfix">
					<h4><?php echo esc_html( $category->name ); ?> <a href="<?php echo esc_url( admin_url( 'edit-tags.php?action=edit&taxonomy=category&tag_ID=' . intval( $category->term_id ) ) ); ?>#custom-category-pages"><?php _e( 'edit this category', 'ccp_plugin' ); ?></a></h4>
					<div class="ccp_plugin-admin-category-details-container">
						<span class="ccp_plugin-admin-category-details-label"><?php _e( "Title tag:", 'ccp_plugin' ); ?></span>
						<span class="ccp_plugin-admin-category-details-value"><?php echo esc_html( get_tax_meta_strip( $category->term_id, 'page_title' ) ); ?></span>
						<span class="ccp_plugin-admin-category-details-label"><?php _e( "Heading:", 'ccp_plugin' ); ?></span>
						<span class="ccp_plugin-admin-category-details-value"><?php echo esc_html( get_tax_meta_strip( $category->term_id, 'heading' ) ); ?></span>
						<span class="ccp_plugin-admin-category-details-label"><?php _e( "Description:", 'ccp_plugin' ); ?></span>
						<span class="ccp_plugin-admin-category-details-value"><?php echo strip_tags( get_tax_meta_strip( $category->term_id, 'copy' ) ); ?></span>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}