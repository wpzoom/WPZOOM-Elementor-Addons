<nav class="portfolio-archive-taxonomies">
	<ul class="portfolio-taxonomies portfolio-taxonomies-filter-by">
		<li <?php echo( ! empty( $category ) ? 'data-subcategory="' . esc_attr( $category ) . '"' : '' ); ?>
			class="cat-item cat-item-all current-cat" 
			data-counter="<?php echo esc_attr( $count ); ?>"
		>
			<a href="<?php echo esc_url( get_page_link( option::get( 'portfolio_url' ) ) ); ?>"><?php esc_html_e( 'All', 'wpzoom-elementor-addons' ); ?></a>
		</li>
		<?php 
			wp_list_categories( array(
				'title_li'     => '',
				'hierarchical' => false,
				'taxonomy'     => 'portfolio',
				'child_of'     => $category
			) ); 
		?>
	</ul>
</nav>