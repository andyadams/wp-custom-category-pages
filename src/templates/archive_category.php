<?php get_header(); ?>
<div id="ccp_plugin-main-container" <?php ccp_plugin_main_container_class(); ?>>
	<h1 class="ccp_plugin-category-title"><?php single_cat_title(); ?></h1>
	<div id="ccp_plugin-category-description">
		<?php echo category_description(); ?>
	</div>
	<div id="ccp_plugin-loop">
		<?php while ( have_posts() ) : the_post(); ?>
			<h2 class="ccp_plugin-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php endwhile; ?>
	</div>
	<div id="ccp_plugin-pagination">
		<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'ccp_plugin' ) ); ?></div>
		<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'ccp_plugin' ) ); ?></div>
	</div>
</div>
<?php get_footer(); ?>