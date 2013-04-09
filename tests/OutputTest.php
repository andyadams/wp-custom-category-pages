<?php

class OutputTest extends WP_UnitTestCase {
	public function testTitleChangedOnCategoryPages() {
		$category = wp_insert_term( 'Category One', 'category' );

		update_tax_meta( $category['term_id'], 'page_title', 'Overwritten Title' );

		$this->go_to( get_term_link( $category['term_id'], 'category' ) );

		$wp_title = wp_title( '', false );

		$this->assertEquals( 'Overwritten Title', $wp_title );
	}
}