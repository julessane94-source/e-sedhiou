<?php
/**
 * Template Name: Équipe municipale
 * Template Post Type: page
 *
 * Gérez les élus depuis Tableau de bord › Élus.
 * Rôles disponibles : maire, adjoint, conseiller, ancien_maire.
 * Seuls les rôles « maire », « adjoint » et « conseiller » sont affichés ici.
 *
 * @package mairie-civique
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mayor = new WP_Query(
	array(
		'post_type'      => 'mairie_elu',
		'posts_per_page' => 1,
		'meta_query'     => array(
			array(
				'key'   => '_mairie_elu_role',
				'value' => 'maire',
			),
		),
	)
);

$adjoints = new WP_Query(
	array(
		'post_type'      => 'mairie_elu',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'   => '_mairie_elu_role',
				'value' => 'adjoint',
			),
		),
		'orderby' => 'title',
		'order'   => 'ASC',
	)
);

$conseillers = new WP_Query(
	array(
		'post_type'      => 'mairie_elu',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'   => '_mairie_elu_role',
				'value' => 'conseiller',
			),
		),
		'orderby' => 'title',
		'order'   => 'ASC',
	)
);

get_header();
?>

<div class="mairie-page-hero">
	<div class="mairie-shell">
		<p class="mairie-eyebrow"><?php esc_html_e( 'Gouvernance locale', 'mairie-civique' ); ?></p>
		<h1><?php the_title(); ?></h1>
		<p class="mairie-muted mb-0"><?php esc_html_e( 'Découvrez les responsables municipaux, leurs rôles et leurs mandats.', 'mairie-civique' ); ?></p>
	</div>
</div>

<div class="mairie-shell mairie-institutional-page">

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php if ( get_the_content() ) : ?>
			<div class="mairie-institutional-intro">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>
	<?php endwhile; endif; ?>

	<?php // ── Maire ──────────────────────────────────────────────────────────── ?>
	<?php if ( $mayor->have_posts() ) : $mayor->the_post(); ?>
		<div class="mairie-team-featured">
			<div class="mairie-team-featured__photo">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'medium' ); ?>
				<?php else : ?>
					<div class="mairie-team-featured__placeholder d-flex align-items-center justify-content-center" aria-hidden="true"><i class="bi bi-person-badge fs-1 text-success"></i></div>
				<?php endif; ?>
			</div>

			<div class="mairie-team-featured__content">
				<p class="mairie-team-featured__eyebrow"><i class="bi bi-building me-1"></i><?php esc_html_e( 'Maire de la commune', 'mairie-civique' ); ?></p>
				<h2 class="mairie-team-featured__name"><?php the_title(); ?></h2>

				<?php if ( has_excerpt() ) : ?>
					<p class="mairie-team-featured__role"><?php the_excerpt(); ?></p>
				<?php endif; ?>

				<?php $periode = get_post_meta( get_the_ID(), '_mairie_elu_periode', true ); ?>
				<?php if ( $periode ) : ?>
					<p class="mairie-team-featured__periode">
						<?php
						printf(
							/* translators: %s: mandate period */
							esc_html__( 'Mandat : %s', 'mairie-civique' ),
							esc_html( $periode )
						);
						?>
					</p>
				<?php endif; ?>
			</div>
		</div>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<?php // ── Adjoints ───────────────────────────────────────────────────────── ?>
	<?php if ( $adjoints->have_posts() ) : ?>
		<h2 class="mairie-team-section-title"><i class="bi bi-people-fill me-2 text-success"></i><?php esc_html_e( 'Adjoints au maire', 'mairie-civique' ); ?></h2>
		<div class="mairie-team-grid">
			<?php while ( $adjoints->have_posts() ) : $adjoints->the_post(); ?>
				<article class="mairie-team-card card border-0 shadow-sm h-100">
					<div class="mairie-team-card__photo">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'thumbnail' ); ?>
						<?php else : ?>
							<div class="mairie-team-card__placeholder d-flex align-items-center justify-content-center" aria-hidden="true"><i class="bi bi-person fs-2 text-success"></i></div>
						<?php endif; ?>
					</div>
					<h3 class="mairie-team-card__name"><?php the_title(); ?></h3>
					<p class="mairie-team-card__role"><?php esc_html_e( 'Adjoint au maire', 'mairie-civique' ); ?></p>
					<?php $periode = get_post_meta( get_the_ID(), '_mairie_elu_periode', true ); ?>
					<?php if ( $periode ) : ?>
						<p class="mairie-team-card__periode"><?php echo esc_html( $periode ); ?></p>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	<?php endif; ?>

	<?php // ── Conseillers ────────────────────────────────────────────────────── ?>
	<?php if ( $conseillers->have_posts() ) : ?>
		<h2 class="mairie-team-section-title"><i class="bi bi-person-lines-fill me-2 text-success"></i><?php esc_html_e( 'Conseillers municipaux', 'mairie-civique' ); ?></h2>
		<div class="mairie-team-grid">
			<?php while ( $conseillers->have_posts() ) : $conseillers->the_post(); ?>
				<article class="mairie-team-card card border-0 shadow-sm h-100">
					<div class="mairie-team-card__photo">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'thumbnail' ); ?>
						<?php else : ?>
							<div class="mairie-team-card__placeholder d-flex align-items-center justify-content-center" aria-hidden="true"><i class="bi bi-person fs-2 text-success"></i></div>
						<?php endif; ?>
					</div>
					<h3 class="mairie-team-card__name"><?php the_title(); ?></h3>
					<p class="mairie-team-card__role"><?php esc_html_e( 'Conseiller municipal', 'mairie-civique' ); ?></p>
					<?php $periode = get_post_meta( get_the_ID(), '_mairie_elu_periode', true ); ?>
					<?php if ( $periode ) : ?>
						<p class="mairie-team-card__periode"><?php echo esc_html( $periode ); ?></p>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! $mayor->post_count && ! $adjoints->post_count && ! $conseillers->post_count ) : ?>
		<div class="mairie-empty-state">
			<p><?php esc_html_e( 'Aucun membre de l\'équipe enregistré pour le moment.', 'mairie-civique' ); ?></p>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<p>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mairie_elu' ) ); ?>" class="mairie-button mairie-button--primary btn btn-success rounded-pill px-4">
						<i class="bi bi-plus-circle me-1"></i>
						<?php esc_html_e( 'Ajouter un élu', 'mairie-civique' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>

<?php
get_footer();
