<?php 
while ( $all_posts->have_posts() ) :

    $all_posts->the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('wpz-post'); ?>>
         
            <div class="post-grid-inner">
            	
            	<?php $this->render_thumbnail(); ?>

                <div class="post-grid-text-wrap">
               		<?php $this->render_title(); ?>
	                <?php $this->render_excerpt(); ?>
	                <?php $this->render_readmore(); ?>
                </div>

                <?php $this->render_meta(); ?>

            </div><!-- .blog-inner -->
           
        </article>

        <?php

endwhile; 

wp_reset_postdata();