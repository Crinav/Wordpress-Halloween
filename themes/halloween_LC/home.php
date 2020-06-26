<?php get_header(); ?>

<div class="body-img">
<embed src="http://localhost/wordpress/wp-content/uploads/2020/06/Ona_Poison.mp3" height="1" width="1">
    <h1 id="h1_scream">Tu as un choix à faire...</h1>

    <div class="site__blog">
    	<main id="admin_content" class="site__content">

            <?php if ( current_user_can( 'manage_options' ) ) { if( have_posts() ) : while( have_posts() ) : the_post(); ?>
                <div id="admin_part">
                <?php 
                    $result = $wpdb->get_results("SELECT * FROM wp_reponses INNER JOIN wp_members ON wp_reponses.id_member = wp_members.id 
                                                                            INNER JOIN wp_questions ON wp_reponses.id_question = wp_questions.id
                                                                            INNER JOIN wp_theme ON wp_questions.id_theme = wp_theme.id
                                                                            GROUP BY id_member");
                    foreach ($result as $print) {
                ?>

                <article class="post">
                    
                    <h2>Questionnaire de : <?php echo $print->email; ?></h2>
                    <h3>Thème : <?php echo $print->title; ?></h3>
                    <?php
                        $result = $wpdb->get_results("SELECT * FROM wp_reponses INNER JOIN wp_members ON wp_reponses.id_member = wp_members.id
                                                                                INNER JOIN wp_questions ON wp_reponses.id_question = wp_questions.id
                                                                                WHERE wp_reponses.id_member = $print->id_member");
                        foreach ($result as $print) {
                    ?>

                        <ul>
                            <li>
                                <p><?php echo $print->question; ?></p>
                                <p><i>"<?php echo $print->reponse; ?>"</i></p>
                            </li>
                        </ul>
                    

                
                    <?php } ?>
                    <img class="img_size" src="<?php echo $print->photo; ?>">

                </article>
                <?php } ?>
            </div>
	        <?php endwhile; endif;}?>
        </main>

        <aside class="site__sidebar">
        	<ul>
				<?php dynamic_sidebar( 'questionnaire-sidebar' ); ?>
			</ul>
			<ul>
				<?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
				<?php endwhile; endif; ?>
			</ul>
        </aside>

	</div> 
	
</div>

<?php get_footer(); ?>