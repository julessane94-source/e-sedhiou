<?php
/**
 * Template Name: Services municipaux
 * Template Post Type: page
 *
 * Les services sont définis dans functions.php via mairie_civique_get_municipal_services().
 * Modifiez ce tableau pour ajouter, supprimer ou réordonner les services.
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
		<p class="mairie-eyebrow"><?php esc_html_e( 'Votre mairie', 'mairie-civique' ); ?></p>
		<h1><?php the_title(); ?></h1>
	</div>
</div>

<div class="mairie-shell mairie-institutional-page">

	<section class="mairie-section">
		<p class="mairie-section__eyebrow"><?php esc_html_e( 'Découvrez nos services', 'mairie-civique' ); ?></p>
		<h2><?php esc_html_e( 'Services municipaux', 'mairie-civique' ); ?></h2>
		<p class="mairie-section__lead">
			<?php esc_html_e( 
				'Accédez aux principaux services offerts par la municipalité. Chaque service vous propose une interface dédiée pour faciliter vos démarches et demandes en ligne.', 
				'mairie-civique' 
			); ?>
		</p>
	</section>

	<?php $services = mairie_civique_get_municipal_services(); ?>

	<?php if ( ! empty( $services ) ) : ?>
	<div class="mairie-service-grid row row-cols-3 g-4">
			<?php foreach ( $services as $service ) : ?>
				<div class="mairie-service-card card h-100 border-0 shadow-sm">
					<div class="mairie-service-card__icon" aria-hidden="true"><?php echo $service['icon']; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- emoji */ ?></div>

					<div class="mairie-service-card__header">
						<h2 class="mairie-service-card__title"><?php echo esc_html( $service['title'] ); ?></h2>
						<p class="mairie-service-card__desc"><?php echo esc_html( $service['description'] ); ?></p>
					</div>

					<?php if ( ! empty( $service['details'] ) ) : ?>
						<ul class="mairie-service-card__list">
							<?php foreach ( $service['details'] as $detail ) : ?>
								<li><?php echo esc_html( $detail ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $service['url'] ) ) : ?>
						<div class="mairie-service-card__footer">
							<a href="<?php echo esc_url( $service['url'] ); ?>" class="mairie-service-card__link btn btn-outline-success btn-sm rounded-pill">
								<?php echo esc_html( $service['url_label'] ); ?> &rarr;
							</a>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div>

<?php
get_footer();
