<?php 
while ( $all_posts->have_posts() ) :

    $all_posts->the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('wpz-post'); ?>>
         
            <div class="post-grid-inner">
            	
            	<div class="post-grid-thumbnail-wrap">
            		<?php $this->render_thumbnail(); ?>
            	</div>

                <div class="post-grid-content-wrap">
               		<?php $this->render_title(); ?>
	                <?php $this->render_meta(); ?>
	                <?php $this->render_excerpt(); ?>
	                <?php $this->render_readmore(); ?>
                </div>

            </div>
           
        </article>

        <?php

endwhile; 

wp_reset_postdata();