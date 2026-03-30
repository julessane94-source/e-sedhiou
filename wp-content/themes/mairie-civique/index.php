<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="mairie-page">
	<h1><?php esc_html_e( 'Actualités et contenus', 'mairie-civique' ); ?></h1>

	<?php if ( have_posts() ) : ?>
		<div class="mairie-grid row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<article class="mairie-card">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
					<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Lire la suite', 'mairie-civique' ); ?></a>
				</article>
			<?php endwhile; ?>
		</div>
	<?php else : ?>
		<p class="mairie-muted"><?php esc_html_e( 'Aucun contenu publié pour le moment.', 'mairie-civique' ); ?></p>
	<?php endif; ?>
</section>

<?php
get_footer();