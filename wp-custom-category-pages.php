<?php
/*
Plugin Name: WP Custom Category Pages
Plugin URI: http://geoffkenyon.com/wp-custom-category-pages
Description: WP Custom Category Pages lets you turn your category pages into useful landing pages that are good for SEO by adding custom content. Transform your category pages from thin pages full of duplicate content to user focused and SEO friendly landing pages.
Version: 1.2.1
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

require_once( dirname( __FILE__ ) . '/template_handling.php' );
require_once( dirname( __FILE__ ) . '/theme_support.php' );
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
		$my_meta->addText( 'heading', array( 'name' => __( 'Category Headline', 'wp_ccp_plugin' ) ) );
		$my_meta->addText( 'page_title', array( 'name' => __( 'Category Page Title', 'wp_ccp_plugin' ) ) );
		$my_meta->addWysiwyg( 'copy', array( 'name' => __( 'Category Copy', 'wp_ccp_plugin' ) ) );

		$my_meta->Finish();
	}
}

add_action( 'init', 'wp_ccp_plugin_admin_init' );

/**
 * Filters the plugin URL in case a bad URL is given due to symlinking
 */
function wp_ccp_plugin_filter_plugins_url( $url, $path, $plugin ) {
	$real_path = realpath( dirname( __FILE__ ) );

	$url = str_replace( $real_path, '/wp-custom-category-pages', $url );

	return $url;
}

add_filter( 'plugins_url', 'wp_ccp_plugin_filter_plugins_url', 10, 3 );

function wp_ccp_plugin_add_fields_header() {
	?>
	<tr class="form-field">
		<th scope="row" valign="top" colspan="2">
			<h2 id="custom-category-pages"><?php _e( 'WP Custom Category Pages', 'wp_ccp_plugin' ); ?></h2>
			<p><?php _e( 'Enter your custom content, headline, and page title below. Make sure that you have turned on custom content for this category to see your changes.', 'wp_ccp_plugin' ); ?></p>
		</th>
	</tr>
	<?php
}

add_action( 'category_edit_form_fields', 'wp_ccp_plugin_add_fields_header' );

function wp_ccp_plugin_main_container_class( $classes = array() ) {
	if ( ! is_array( $classes ) ) {
		$classes = array( $classes );
	}

	apply_filters( 'wp_ccp_plugin_main_container_classes', $classes );

	if ( ! empty( $classes ) ) {
		echo ' class="' . implode( ' ', $classes ) . '" ';
	}
}

function wp_ccp_plugin_wp_title( $title ) {
	if ( is_category() && wp_ccp_plugin_is_custom_content_enabled() ) {
		$new_title = get_tax_meta_strip( get_queried_object_id(), 'page_title' );
		if ( ! empty( $new_title ) ) {
			$title = $new_title;
		}
	}

	return $title;
}

add_filter( 'wp_title', 'wp_ccp_plugin_wp_title', 20 );

function wp_ccp_plugin_single_cat_title( $title ) {
	if ( is_category() && wp_ccp_plugin_is_custom_content_enabled() ) {
		$new_title = get_tax_meta_strip( get_queried_object_id(), 'heading' );
		if ( ! empty( $new_title ) ) {
			$title = $new_title;
		}
	}

	return $title;
}

add_filter( 'single_cat_title', 'wp_ccp_plugin_single_cat_title', 100 );

function wp_ccp_plugin_category_description( $description, $category_id ) {
	if ( wp_ccp_plugin_is_custom_content_enabled( $category_id ) ) {
		$new_description = get_tax_meta_strip( $category_id, 'copy' );
		if ( ! empty( $new_description ) ) {
			$description = $new_description;
		}
	}

	return $description;
}

add_filter( 'category_description', 'wp_ccp_plugin_category_description', 10, 2 );

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

function wp_ccp_modify_post_count( &$query ) {
	if ( is_category() && wp_ccp_plugin_is_custom_content_enabled() && $query->is_main_query() ) {
		$query->set( 'posts_per_page', 25 );
	}
}

add_action( 'pre_get_posts', 'wp_ccp_modify_post_count' );

function wp_ccp_plugin_enqueue_scripts() {
	if ( is_category() && wp_ccp_plugin_is_custom_content_enabled() ) {
		wp_enqueue_style(
			'wp_ccp_plugin_category_archive',
			plugins_url( 'stylesheets/category.css', __FILE__ )
		);
	}
}

add_action( 'wp_enqueue_scripts', 'wp_ccp_plugin_enqueue_scripts' );

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
		<div id="wp_ccp_plugin-settings">
			<form method="post" action="options.php">
				<?php settings_fields( 'wp_ccp_plugin_options' ); ?>
				<?php do_settings_sections( 'wp_ccp_plugin' ); ?>
				<input name="Save" type="submit" value="<?php esc_attr_e( 'Save settings', 'wp_ccp_plugin' ); ?>" />
			</form>
		</div>
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
						<span class="wp_ccp_plugin-admin-category-details-label"><?php _e( "Title tag:", 'wp_ccp_plugin' ); ?></span>
						<span class="wp_ccp_plugin-admin-category-details-value"><?php echo esc_html( get_tax_meta_strip( $category->term_id, 'page_title' ) ); ?></span>
						<span class="wp_ccp_plugin-admin-category-details-label"><?php _e( "Heading:", 'wp_ccp_plugin' ); ?></span>
						<span class="wp_ccp_plugin-admin-category-details-value"><?php echo esc_html( get_tax_meta_strip( $category->term_id, 'heading' ) ); ?></span>
						<span class="wp_ccp_plugin-admin-category-details-label"><?php _e( "Description:", 'wp_ccp_plugin' ); ?></span>
						<span class="wp_ccp_plugin-admin-category-details-value"><?php echo strip_tags( get_tax_meta_strip( $category->term_id, 'copy' ) ); ?></span>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

function wp_ccp_plugin_settings_api_init() {
	register_setting( 'wp_ccp_plugin_options', 'wp_ccp_plugin_options', 'wp_ccp_plugin_options_validate' );
	add_settings_section( 'wp_ccp_plugin_general', __( 'Settings', 'wp_ccp_plugin' ), 'wp_ccp_plugin_general_settings_text', 'wp_ccp_plugin' );
	add_settings_field( 'page_template', __( 'Page template for category pages', 'wp_ccp_plugin' ), 'wp_ccp_plugin_page_template_input', 'wp_ccp_plugin', 'wp_ccp_plugin_general' );
}

add_action( 'admin_init', 'wp_ccp_plugin_settings_api_init' );

function wp_ccp_plugin_general_settings_text() {
	?>
	<p><small><em>
		<?php _e( 'Note: Category page display is ultimately controlled by the theme.', 'wp_ccp_plugin' ) ?><br>
		<?php _e( 'Below, you can select which page template you want your category pages to use.', 'wp_ccp_plugin' ); ?><br>
		<?php _e( 'There are known issues with sidebars on some themes, which will be addressed in future updates.', 'wp_ccp_plugin' ); ?><br>
		<?php echo sprintf( __( 'If you have any questions or difficulties, feel free to post them in the %s', 'wp_ccp_plugin' ), '<a href="http://wordpress.org/support/plugin/wp-custom-category-pages">' . __( 'support forums', 'wp_ccp_plugin' ) . '</a>' ); ?>.
	</em></small></p>
	<?php
}

function wp_ccp_plugin_page_template_input() {
	$selected_page_template = wp_ccp_plugin_selected_page_template();

	$current_theme = wp_get_theme();
	$page_templates = $current_theme->get_page_templates();

	?>
	<select name="wp_ccp_plugin_options[page_template]">
		<option value=""><?php _e( 'Default', 'wp_ccp_plugin' ); ?></option>
		<?php foreach ( $page_templates as $filename => $template_name ) : ?>
			<option value="<?php echo esc_attr( $filename ); ?>" <?php selected( $selected_page_template, $filename ); ?>>
				<?php echo esc_html( $template_name ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<?php
}

function wp_ccp_plugin_options_validate( $input ) {
	$current_theme = wp_get_theme();
	$page_templates = $current_theme->get_page_templates();

	$available_templates = array_keys( $page_templates );

	$selected_template = $input['page_template'];

	if ( ! in_array( $selected_template, $available_templates ) ) {
		$selected_template = '';
	}

	$new_input['page_template'] = $selected_template;

	return $new_input;
}

function wp_ccp_plugin_selected_page_template() {
	$options = get_option( 'wp_ccp_plugin_options' );

	$selected_page_template = isset( $options['page_template'] ) ? $options['page_template'] : '';

	return $selected_page_template;
}