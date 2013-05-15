<?php

function wp_ccp_plugin_set_page_template() {
	if ( wp_ccp_plugin_is_custom_category_page() ) {
		global $wp_query;

		if ( isset( $wp_query->posts[0] ) ) {
			$content = '';

			ob_start();

			include( dirname( __FILE__ ) . '/templates/archive_category.php' );

			$content = ob_get_contents();
			ob_end_clean();

			$wp_query->posts[0]->wp_ccp_plugin_fake_title = single_cat_title( '', false );
			$wp_query->posts[0]->wp_ccp_plugin_fake_content = $content;
			$wp_query->posts[0]->comment_status = 'closed';

			$page_template = wp_ccp_plugin_get_theme_template();

			if ( file_exists( $page_template ) ) {
				include $page_template;
				exit;
			}
		}
	}
}

add_action( 'template_redirect', 'wp_ccp_plugin_set_page_template' );

function wp_ccp_plugin_modify_title( $title ) {
	global $post;

	if ( isset( $post->wp_ccp_plugin_fake_title ) ) {
		$title = $post->wp_ccp_plugin_fake_title;

		$post->wp_ccp_plugin_fake_title = NULL;
	}

	return $title;
}

add_filter( 'get_the_title', 'wp_ccp_plugin_modify_title', 99999 );
add_filter( 'the_title', 'wp_ccp_plugin_modify_title', 99999 );

function wp_ccp_plugin_fix_fake_content( $content ) {
	global $post;

	if ( isset( $post->wp_ccp_plugin_fake_content ) ) {
		$content = $post->wp_ccp_plugin_fake_content;

		$post->wp_ccp_plugin_fake_content = NULL;

		global $wp_query;

		// Force an end to the loop
		$wp_query->current_post = ( $wp_query->post_count - 1 );

		$before_content = apply_filters( 'wp_ccp_plugin_before_content', '' );
		$after_content = apply_filters( 'wp_ccp_plugin_after_content', '' );

		$content = $before_content . $content . $after_content;
	}

	return $content;
}

add_filter( 'the_content', 'wp_ccp_plugin_fix_fake_content', 99999 );
add_filter( 'the_excerpt', 'wp_ccp_plugin_fix_fake_content', 99999 );
add_filter( 'get_the_excerpt', 'wp_ccp_plugin_fix_fake_content', 99999 );
add_filter( 'wp_trim_words', 'wp_ccp_plugin_fix_fake_content', 99999 );

function wp_ccp_plugin_is_custom_category_page() {
	return is_category() && wp_ccp_plugin_is_custom_content_enabled();
}

function wp_ccp_plugin_get_theme_template() {
	$page_template = get_template_directory() . '/page.php';

	apply_filters( 'wp_ccp_plugin_theme_template', $page_template );

	if ( ! file_exists( $page_template ) ) {
		$page_template = get_template_directory() . '/index.php';
	}

	return $page_template;
}