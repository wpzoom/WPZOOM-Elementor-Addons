<?php
	$category = get_term_by( 'slug', $category, 'portfolio' );
	$category = isset( $category->term_id ) ? $category->term_id : '';
?>
<nav class="portfolio-archive-taxonomies">
	<ul class="portfolio-taxonomies portfolio-taxonomies-filter-by">
		<li <?php echo( ! empty( $category ) ? 'data-subcategory="' . esc_attr( $category ) . '"' : '' ); ?>
			class="cat-item cat-item-all current-cat" 
			data-counter="<?php echo esc_attr( $count ); ?>"
		>
			<a href="<?php echo esc_url( get_page_link( option::get( 'portfolio_url' ) ) ); ?>"><?php esc_html_e( 'All', 'wpzoom-elementor-addons' ); ?></a>
		</li>
		<?php 
			$args = array(
				'title_li'     => '',
				'hierarchical' => false,
				'taxonomy'     => 'portfolio',
				'child_of'     => $category
			);
			if( 'yes' == $settings['hide_sub_categories'] ) {
				$args['hierarchical'] = true;
				$args['depth'] = 1;
			}
			wp_list_categories( $args ); 
		?>
	</ul>
</nav>