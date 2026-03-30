<?php
/**
 * Template Name: Historique de la ville
 * Template Post Type: page
 *
 * Les événements de la timeline sont définis dans functions.php
 * via la fonction mairie_civique_get_history_events().
 * Modifiez ce tableau pour personnaliser les événements affichés.
 *
 * @package mairie-civique
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="mairie-page-hero">
	<div class="mairie-shell">
		<p class="mairie-eyebrow"><?php esc_html_e( 'Patrimoine local', 'mairie-civique' ); ?></p>
		<h1><?php the_title(); ?></h1>
		<p class="mairie-muted mb-0"><?php esc_html_e( 'Les repères historiques et les moments qui ont façonné la commune.', 'mairie-civique' ); ?></p>
	</div>
</div>

<div class="mairie-shell mairie-institutional-page">

	<h2 class="mairie-team-section-title"><i class="bi bi-clock-history me-2 text-success"></i><?php esc_html_e( 'Les grandes dates', 'mairie-civique' ); ?></h2>

	<?php $events = mairie_civique_get_history_events(); ?>

	<?php if ( ! empty( $events ) ) : ?>
		<div class="mairie-timeline">
			<?php foreach ( $events as $event ) : ?>
				<div class="mairie-timeline__item">
					<div class="mairie-timeline__year"><i class="bi bi-calendar3 me-2 text-success"></i><?php echo esc_html( $event['year'] ); ?></div>
					<div class="mairie-timeline__dot" aria-hidden="true"></div>
					<div class="mairie-timeline__body">
						<h3 class="mairie-timeline__title"><?php echo esc_html( $event['title'] ); ?></h3>
						<p class="mairie-timeline__text"><?php echo esc_html( $event['content'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="alert alert-light border rounded-4">
			<?php esc_html_e( 'Aucun événement historique n’est encore renseigné.', 'mairie-civique' ); ?>
		</div>
	<?php endif; ?>

</div>

<?php
get_footer();
