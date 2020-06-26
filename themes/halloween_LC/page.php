<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div id="page_page">

			<h1 class="h1_page"><?php the_title(); ?></h1>

			<?php the_content(); ?>


			<?php if (is_page('poser-une-question')) { ?>
				<div class="site__sidebar__pose_question">
					<?php dynamic_sidebar('question-sidebar'); ?>
				</div>
			<?php } ?>

			<?php if (is_page('theme')) { ?>
				<div id="theme_div" class="container w-25">
					<h2 id="titre_theme">Je te laisse choisir ton destin</h2>
					<ul class="list-group">
						<?php
						global $wpdb;
						$result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}theme");
						foreach ($result as $print) {

						?>
							<a id="list_theme" href="repondre?id=<?php echo $print->id; ?>">
								<li class="list-group-item d-flex justify-content-between align-items-center">
									<?php echo $print->title; ?>
									<span class="badge badge-danger badge-pill"><?php echo $print->nb_question; ?></span>
								</li>
							</a>

						<?php } ?>

					</ul>
				</div>
			<?php } ?>

			<?php if (is_page('repondre')) { ?>
				<div id="questionnaire_style" class="container w-50">
					<?php
					$id_theme = $_GET["id"];
					$resultats = $wpdb->get_row(
						$wpdb->prepare("SELECT title FROM {$wpdb->prefix}theme WHERE id=$id_theme")
					);
					$title_theme = $resultats->title;
					?>
					<h2>Questionnaire : <?= $title_theme ?></h2><br>

					<ul class="list-group">
						<form action="" method="post">

							<?php
							$result = $wpdb->get_results("SELECT * FROM wp_questions WHERE id_theme=$id_theme");
							foreach ($result as $key => $print) {
							?>

								<div class="form-group">
									<label for="question<?= $print->id ?>"><?= $print->question ?></label>
									<input type="text" class="form-control" name="<?= $key ?>" required>

									<input type="hidden" name="id_question<?= $key ?>" value="<?= $print->id ?>">
								</div>
							<?php } ?>

							<button type="submit" class="btn btn-dark">Valider</button>
						</form>
					</ul>
				</div>
			<?php } ?>

			<div>

		<?php endwhile;
endif; ?>

		<?php get_footer(); ?>