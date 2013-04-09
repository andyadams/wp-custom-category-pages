<?php

class OutputTest extends WP_UnitTestCase {
	public function testTitleChangedOnCategoryPages() {
		$category = wp_insert_term( 'Category One', 'category' );

		$this->go_to( get_term_link( $category['term_id'], 'category' ) );

		$original_wp_title = wp_title( '', false );

		update_tax_meta( $category['term_id'], 'page_title', 'Overwritten Title' );

		$wp_title = wp_title( '', false );

		$this->assertEquals( 'Overwritten Title', $wp_title );

		update_tax_meta( $category['term_id'], 'custom_content_enabled', 0 );

		$wp_title = wp_title( '', false );

		$this->assertEquals( $original_wp_title, $wp_title );
	}
}