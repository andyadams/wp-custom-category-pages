<div id="wp_ccp_plugin-main-container" <?php wp_ccp_plugin_main_container_class(); ?>>
	<div id="wp_ccp_plugin-category-description">
		<?php echo apply_filters( 'the_content', category_description() ); ?>
	</div>
	<div id="wp_ccp_plugin-loop">
		<?php while ( have_posts() ) : the_post(); ?>
			<h2 class="wp_ccp_plugin-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php endwhile; ?>
	</div>
	<div id="wp_ccp_plugin-pagination">
		<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'wp_ccp_plugin' ) ); ?></div>
		<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'wp_ccp_plugin' ) ); ?></div>
	</div>
</div>