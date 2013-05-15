<?php

function wp_ccp_plugin_fix_modernist_theme_after_content( $after_content ) {
	$current_theme = wp_get_theme();

	if ( 'modernist' === $current_theme->get_template() ) {
		$after_content = "</div> <hr />";
	}

	return $after_content;
}

add_action( 'wp_ccp_plugin_after_content', 'wp_ccp_plugin_fix_modernist_theme_after_content' );
