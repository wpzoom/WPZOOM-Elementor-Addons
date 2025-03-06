<?php
    $wp_query = new WP_Query( $args );
?>
<div class="portfolio-archive">

    <section class="portfolio-archive-fresh">

    <?php 
        if ( ! $single_post ) {
            include( __DIR__ . '/filter.php' );
        }
    ?>

    <?php if ( $wp_query->have_posts() ) : ?>

    <div class="portfolio-grid portfolio-fresh<?php echo $always_play_background_video_class; ?>">

        <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

            <?php get_template_part( 'portfolio/content-fresh' ); ?>

        <?php endwhile; ?>

    </div>

    <?php get_template_part( 'pagination' ); ?>

    <?php else: ?>

    <?php get_template_part( 'content', 'none' ); ?>

    <?php endif; ?>

    </section><!-- .portfolio-archive-fresh -->
</div>