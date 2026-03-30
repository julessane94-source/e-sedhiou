<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article <?php post_class( 'mairie-article' ); ?>>
			<header class="mairie-article__header">
				<span class="mairie-section__eyebrow"><?php esc_html_e( 'Actualite', 'mairie-civique' ); ?></span>
				<h1><?php the_title(); ?></h1>
				<p class="mairie-article__meta">
					<?php echo esc_html( get_the_date() ); ?>
					<?php if ( get_the_author() ) : ?>
						<?php echo esc_html( ' · ' . get_the_author() ); ?>
					<?php endif; ?>
				</p>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="mairie-article__media">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>

			<div class="mairie-article__content">
				<?php the_content(); ?>
			</div>

			<footer class="mairie-article__footer">
				<div class="mairie-article__nav">
					<div><?php previous_post_link( '%link', esc_html__( 'Article precedent', 'mairie-civique' ) ); ?></div>
					<div><?php next_post_link( '%link', esc_html__( 'Article suivant', 'mairie-civique' ) ); ?></div>
				</div>
				<a class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/actualites/' ) ); ?>"><?php esc_html_e( 'Retour aux actualites', 'mairie-civique' ); ?></a>
			</footer>
		</article>
	<?php endwhile; ?>
<?php endif; ?>

<?php
get_footer();
