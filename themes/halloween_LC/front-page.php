<?php get_header(); ?>


--------login front-page---------

	<?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>

		<div id="login_page">
		
			<h1><?php the_title(); ?></h1>
		
			<div class="login_content">
				<?php the_content(); ?>
				<div class="site__sidebar__login">
					<ul>
						<?php dynamic_sidebar( 'login-sidebar' ); ?>
					</ul>
				</div>
			</div>

		</div>

	<?php endwhile; endif; ?>


<?php get_footer(); ?>