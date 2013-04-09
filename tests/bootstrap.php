<?php
// Load WordPress test environment
// https://github.com/nb/wordpress-tests
//
// The path to wordpress-tests
$path = dirname( __FILE__ ) . '/../vendor/wordpress-tests/bootstrap.php';

if( file_exists( $path ) ) {
	$GLOBALS['wp_tests_options'] = array(
		'active_plugins' => array( 'wp-custom-category-pages/wp-custom-category-pages.php' )
	);
	require_once $path;
} else {
	exit( "Couldn't find path to wordpress-tests/bootstrap.php\n" );
}