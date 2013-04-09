<?php

class OutputTest extends WP_UnitTestCase {
	public function testPageTitleChangedOnCategoryPages() {
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

	public function testHeadingChangedOnCategoryPages() {
		$category = wp_insert_term( 'Category One', 'category' );

		$this->go_to( get_term_link( $category['term_id'], 'category' ) );

		$original_heading = single_cat_title( '', false );

		update_tax_meta( $category['term_id'], 'heading', 'Overwritten Heading' );

		$heading = single_cat_title( '', false );

		$this->assertEquals( 'Overwritten Heading', $heading );

		update_tax_meta( $category['term_id'], 'custom_content_enabled', 0 );

		$heading = single_cat_title( '', false );

		$this->assertEquals( $original_heading, $heading );
	}

	public function testDescriptionIsOverwrittenOnCategoryPages() {
		$category = wp_insert_term( 'Category One', 'category', array( 'description' => 'Original description!' ) );

		$this->go_to( get_term_link( $category['term_id'], 'category' ) );

		$original_copy = category_description();

		update_tax_meta( $category['term_id'], 'copy', '<p>Overwritten copy!</p>' );

		$copy = category_description();

		$this->assertEquals( '<p>Overwritten copy!</p>', $copy );

		update_tax_meta( $category['term_id'], 'custom_content_enabled', 0 );

		$copy = category_description();

		$this->assertEquals( $original_copy, $copy );
	}
}