<?php
/*
Template Name: Espace Agent
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$space = mairie_civique_get_space_config( 'agent' );
$guard = mairie_civique_get_space_guard_context( 'agent' );

$request_counts  = array();
$recent_requests = array();
$request_types   = array();

if ( $guard['allowed'] ) {
	$request_counts  = mairie_civique_get_request_counts();
	$recent_requests = mairie_civique_get_recent_requests();
	$request_types   = mairie_civique_get_request_types();
}

get_header();
?>

<article class="mairie-space mairie-space--agent">
	<?php if ( $guard['allowed'] ) : ?>
		<section class="mairie-space-hero">
			<div>
				<span class="mairie-section__eyebrow"><?php echo esc_html( $space['eyebrow'] ); ?></span>
				<h1><?php echo esc_html( $space['title'] ); ?></h1>
				<div class="mairie-space-hero__actions d-flex flex-wrap gap-2">
					<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="#suivi-demandes"><i class="bi bi-speedometer2 me-1"></i><?php esc_html_e( 'Voir le suivi', 'mairie-civique' ); ?></a>
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

		<section id="suivi-demandes" class="mairie-request-section">
			<div class="mairie-request-section__intro">
				<span class="mairie-section__eyebrow"><?php esc_html_e( 'Suivi des demandes', 'mairie-civique' ); ?></span>
				<h2><?php esc_html_e( 'Tableau de bord agent.', 'mairie-civique' ); ?></h2>
			</div>

			<div class="mairie-stats-grid row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3">
				<div class="mairie-stat-card">
					<div class="d-flex align-items-center gap-3">
						<span class="mairie-stat-card__icon text-warning"><i class="bi bi-hourglass-split"></i></span>
						<div><strong><?php echo esc_html( (string) ( $request_counts['pending'] ?? 0 ) ); ?></strong><span><?php esc_html_e( 'Demandes en attente', 'mairie-civique' ); ?></span></div>
					</div>
				</div>
				<div class="mairie-stat-card">
					<div class="d-flex align-items-center gap-3">
						<span class="mairie-stat-card__icon text-primary"><i class="bi bi-arrow-repeat"></i></span>
						<div><strong><?php echo esc_html( (string) ( $request_counts['in_progress'] ?? 0 ) ); ?></strong><span><?php esc_html_e( 'Demandes en cours', 'mairie-civique' ); ?></span></div>
					</div>
				</div>
				<div class="mairie-stat-card">
					<div class="d-flex align-items-center gap-3">
						<span class="mairie-stat-card__icon text-success"><i class="bi bi-check-circle"></i></span>
						<div><strong><?php echo esc_html( (string) ( $request_counts['completed'] ?? 0 ) ); ?></strong><span><?php esc_html_e( 'Demandes traitees', 'mairie-civique' ); ?></span></div>
					</div>
				</div>
				<div class="mairie-stat-card">
					<div class="d-flex align-items-center gap-3">
						<span class="mairie-stat-card__icon text-danger"><i class="bi bi-x-circle"></i></span>
						<div><strong><?php echo esc_html( (string) ( $request_counts['rejected'] ?? 0 ) ); ?></strong><span><?php esc_html_e( 'Demandes rejetees', 'mairie-civique' ); ?></span></div>
					</div>
				</div>
			</div>

			<div class="mairie-table-card">
				<div class="mairie-table-card__header">
					<h2><?php esc_html_e( 'Dernieres demandes recues', 'mairie-civique' ); ?></h2>
					<?php if ( current_user_can( 'edit_mairie_demandes' ) ) : ?>
						<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill" href="<?php echo esc_url( admin_url( 'edit.php?post_type=mairie_demande' ) ); ?>"><i class="bi bi-box-arrow-up-right me-1"></i><?php esc_html_e( 'Ouvrir le back-office', 'mairie-civique' ); ?></a>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $recent_requests ) ) : ?>
					<div class="mairie-table-wrap">
						<table class="mairie-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Date', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Demandeur', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Type', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Statut', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Contact', 'mairie-civique' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $recent_requests as $request ) : ?>
									<?php
									$request_type = get_post_meta( $request->ID, '_mairie_request_type', true );
									$status       = get_post_meta( $request->ID, '_mairie_status', true );
									$first_name   = get_post_meta( $request->ID, '_mairie_first_name', true );
									$last_name    = get_post_meta( $request->ID, '_mairie_last_name', true );
									$email        = get_post_meta( $request->ID, '_mairie_email', true );
									?>
									<tr>
										<td><?php echo esc_html( get_the_date( 'd/m/Y', $request ) ); ?></td>
										<td><?php echo esc_html( trim( $first_name . ' ' . $last_name ) ); ?></td>
										<td><?php echo esc_html( $request_types[ $request_type ] ?? '—' ); ?></td>
										<td><span class="mairie-status-badge mairie-status-badge--<?php echo esc_attr( mairie_civique_get_request_status_class( $status ) ); ?>"><?php echo esc_html( mairie_civique_get_request_status_label( $status ) ); ?></span></td>
										<td><?php echo esc_html( $email ?: '—' ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else : ?>
					<p class="mairie-muted"><?php esc_html_e( 'Aucune demande.', 'mairie-civique' ); ?></p>
				<?php endif; ?>
			</div>
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
			<p class="mairie-muted"><?php echo esc_html( $guard['message_body'] ); ?></p>
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