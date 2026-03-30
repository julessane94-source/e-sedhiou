<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts_page_id    = (int) get_option( 'page_for_posts' );
$archive_title    = $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Actualites municipales', 'mairie-civique' );
$archive_excerpt  = $posts_page_id ? get_post_field( 'post_excerpt', $posts_page_id ) : '';
$archive_lead     = $archive_excerpt ? $archive_excerpt : '';

get_header();
?>

<section class="mairie-page mairie-page--archive">
	<div class="mairie-page__hero">
		<h1><?php echo esc_html( $archive_title ); ?></h1>
		<?php if ( '' !== $archive_lead ) : ?>
			<p class="mairie-page__lead"><?php echo esc_html( $archive_lead ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( have_posts() ) : ?>
		<div class="mairie-post-grid">
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<article <?php post_class( 'mairie-post-card' ); ?>>
					<a class="mairie-post-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'large' ); ?>
						<?php else : ?>
							<span class="mairie-post-card__placeholder"></span>
						<?php endif; ?>
					</a>
					<div class="mairie-post-card__body">
						<p class="mairie-post-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: wp_strip_all_tags( get_the_content() ), 28 ) ); ?></p>
						<a class="mairie-card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Lire l actualite', 'mairie-civique' ); ?></a>
					</div>
				</article>
			<?php endwhile; ?>
		</div>

		<div class="mairie-pagination">
			<?php
			echo wp_kses_post(
				paginate_links(
					array(
						'type'      => 'list',
						'prev_text' => __( 'Precedent', 'mairie-civique' ),
						'next_text' => __( 'Suivant', 'mairie-civique' ),
					)
				)
			);
			?>
		</div>
	<?php else : ?>
		<div class="mairie-card">
			<h2><?php esc_html_e( 'Aucune actualite pour le moment', 'mairie-civique' ); ?></h2>
		</div>
	<?php endif; ?>
</section>

<?php
get_footer();
