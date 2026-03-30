<?php
/*
Template Name: Espace Admin
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$space = mairie_civique_get_space_config( 'admin' );
$guard = mairie_civique_get_space_guard_context( 'admin' );

get_header();
?>

<article class="mairie-space mairie-space--admin">
	<?php if ( $guard['allowed'] ) : ?>
		<section class="mairie-space-hero">
			<div>
				<span class="mairie-section__eyebrow"><?php echo esc_html( $space['eyebrow'] ); ?></span>
				<h1><?php echo esc_html( $space['title'] ); ?></h1>
				<p><?php echo esc_html( $space['description'] ); ?></p>
				<div class="mairie-space-hero__actions d-flex flex-wrap gap-2">
					<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="#priorites"><i class="bi bi-clipboard-check me-1"></i><?php echo esc_html( $space['cta_primary'] ); ?></a>
					<a class="mairie-button mairie-button--ghost btn btn-outline-secondary rounded-pill px-4" href="#contenu"><i class="bi bi-grid me-1"></i><?php echo esc_html( $space['cta_secondary'] ); ?></a>
				</div>
			</div>

			<aside class="mairie-space-panel" id="priorites">
				<h2><?php echo esc_html( $space['panel_title'] ); ?></h2>
				<ul>
					<?php foreach ( $space['panel_items'] as $item ) : ?>
						<li><i class="bi bi-check2-circle me-2 text-success"></i><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ul>
			</aside>
		</section>

		<div class="mairie-page__content" id="contenu">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	<?php else : ?>
		<section class="mairie-access-card">
			<span class="mairie-section__eyebrow"><?php esc_html_e( 'Espace securise', 'mairie-civique' ); ?></span>
			<h1><?php echo esc_html( $guard['message_title'] ); ?></h1>
			<p class="mairie-muted"><?php esc_html_e( 'Acces reserve.', 'mairie-civique' ); ?></p>
			<div class="mairie-space-hero__actions d-flex flex-wrap gap-2">
				<?php if ( ! $guard['is_logged_in'] ) : ?>
					<a class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4" href="<?php echo esc_url( $guard['login_url'] ); ?>"><i class="bi bi-box-arrow-in-right me-1"></i><?php esc_html_e( 'Se connecter', 'mairie-civique' ); ?></a>
				<?php endif; ?>
				<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="<?php echo esc_url( $guard['fallback_url'] ); ?>"><i class="bi bi-arrow-left me-1"></i><?php echo esc_html( $guard['fallback_label'] ); ?></a>
			</div>
		</section>
	<?php endif; ?>
</article>

<?php
get_footer();