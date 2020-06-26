<?php get_header(); ?>
    <h1>Blogiiiii</h1>

    <div class="site__blog">
    	<main class="site__content">

            <?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
                <article class="post">
                    <h2><?php the_title(); ?></h2>
            
                    <?php the_post_thumbnail(); ?>
                    
                    <p class="post__meta">
                        Publié le <?php the_time( get_option( 'date_format' ) ); ?> 
                        par <?php the_author(); ?> • <?php comments_number(); ?>
                    </p>
                    
                    <?php the_excerpt(); ?>
                    
                    <p>
                        <a href="<?php the_permalink(); ?>" class="post__link">Lire la suite</a>
                    </p>
                </article>
	        <?php endwhile; endif; ?>
        </main>

        
	</div> 

<?php get_footer(); ?>