<?php get_header(); ?>
<div id="ccp_plugin-main-container">
	<h1><?php single_cat_title(); ?></h1>
	<div id="ccp_plugin-category-description">
		<?php echo category_description(); ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>