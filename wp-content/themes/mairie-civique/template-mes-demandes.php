<?php
/*
Template Name: Mes Demandes Laravel
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── Garde d'accès ─────────────────────────────────────────────────────────── */
if ( ! is_user_logged_in() || ! mairie_civique_user_is_citizen() ) {
	wp_safe_redirect( mairie_civique_get_login_page_url( get_permalink() ) );
	exit;
}

/* ── Paramètres GET ─────────────────────────────────────────────────────────── */
$current_filter = isset( $_GET['filtre'] ) ? sanitize_key( wp_unslash( $_GET['filtre'] ) ) : 'tous';
$demande_id     = isset( $_GET['demande_id'] ) ? absint( $_GET['demande_id'] ) : 0;
$msg_state      = isset( $_GET['msg_state'] ) ? sanitize_key( wp_unslash( $_GET['msg_state'] ) ) : '';

$allowed_filters = array( 'tous', 'pending', 'assigned', 'processing', 'completed', 'rejected' );
if ( ! in_array( $current_filter, $allowed_filters, true ) ) {
	$current_filter = 'tous';
}

/* ── JWT + données ──────────────────────────────────────────────────────────── */
$jwt          = mairie_laravel_get_user_jwt( get_current_user_id() );
$all_demandes = array();
$demande      = null;
$messages     = array();
$api_error    = '';

if ( ! $jwt ) {
	$api_error = __( 'Votre session est expirée. Reconnectez-vous pour accéder à vos dossiers.', 'mairie-civique' );
} else {
	$raw = mairie_laravel_get_mes_demandes( $jwt );
	if ( is_wp_error( $raw ) ) {
		$api_error = $raw->get_error_message();
	} elseif ( is_array( $raw ) && ! empty( $raw['data'] ) ) {
		$all_demandes = $raw['data'];
	}

	// Détail d'une demande spécifique
	if ( $demande_id ) {
		$raw_detail = mairie_laravel_get_demande_detail( $jwt, $demande_id );
		if ( ! is_wp_error( $raw_detail ) && is_array( $raw_detail ) ) {
			$demande  = $raw_detail['demande'] ?? $raw_detail;
			$messages = $demande['messages'] ?? array();
		}
	}
}

/* ── Filtrage côté client ───────────────────────────────────────────────────── */
$filtered_demandes = array();
foreach ( $all_demandes as $d ) {
	if ( 'tous' === $current_filter || ( $d['status'] ?? '' ) === $current_filter ) {
		$filtered_demandes[] = $d;
	}
}

/* ── URL de la page courante ────────────────────────────────────────────────── */
$page_url = get_permalink();

/* ── Libellés d'état ────────────────────────────────────────────────────────── */
$filter_labels = array(
	'tous'       => __( 'Toutes', 'mairie-civique' ),
	'pending'    => __( 'En attente', 'mairie-civique' ),
	'assigned'   => __( 'Assignées', 'mairie-civique' ),
	'processing' => __( 'En cours', 'mairie-civique' ),
	'completed'  => __( 'Traitées', 'mairie-civique' ),
	'rejected'   => __( 'Rejetées', 'mairie-civique' ),
);

$msg_state_labels = array(
	'sent'   => __( 'Message envoyé avec succès.', 'mairie-civique' ),
	'error'  => __( 'Erreur lors de l\'envoi du message. Réessayez.', 'mairie-civique' ),
	'empty'  => __( 'Le message ne peut pas être vide.', 'mairie-civique' ),
	'no_jwt' => __( 'Votre session est expirée. Reconnectez-vous.', 'mairie-civique' ),
);

$request_types = mairie_civique_get_request_types();

get_header();
?>

<article class="mairie-space mairie-space--citoyen">

	<section class="mairie-space-hero">
		<div>
			<span class="mairie-section__eyebrow"><?php esc_html_e( 'Services numériques', 'mairie-civique' ); ?></span>
			<h1><?php esc_html_e( 'Mes dossiers', 'mairie-civique' ); ?></h1>
			<p><?php esc_html_e( 'Consultez et suivez toutes vos demandes en cours ou terminées.', 'mairie-civique' ); ?></p>
			<div class="mairie-space-hero__actions">
				<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="<?php echo esc_url( mairie_civique_get_space_url( 'espace-citoyen' ) . '#demandes' ); ?>">
					<i class="bi bi-file-earmark-plus me-1"></i>
					<?php esc_html_e( 'Faire une demande', 'mairie-civique' ); ?>
				</a>
			</div>
		</div>
	</section>

	<?php if ( $api_error ) : ?>
		<div class="mairie-form-alert mairie-form-alert--error alert alert-danger">
			<p><?php echo esc_html( $api_error ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $demande_id && $demande ) : ?>
		<?php /* ══════════════ VUE DÉTAIL ══════════════ */ ?>

		<section class="mairie-request-section">
			<div class="mairie-demande-detail__nav">
				<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="<?php echo esc_url( $page_url ); ?>">
					<i class="bi bi-arrow-left me-1"></i>
					&larr; <?php esc_html_e( 'Retour à la liste', 'mairie-civique' ); ?>
				</a>
			</div>

			<?php if ( $msg_state && isset( $msg_state_labels[ $msg_state ] ) ) : ?>
				<div class="mairie-form-alert mairie-form-alert--<?php echo 'sent' === $msg_state ? 'success' : 'error'; ?> alert alert-<?php echo 'sent' === $msg_state ? 'success' : 'danger'; ?>">
					<p><?php echo esc_html( $msg_state_labels[ $msg_state ] ); ?></p>
				</div>
			<?php endif; ?>

			<div class="mairie-demande-detail">
				<div class="mairie-demande-detail__header">
					<div>
						<h2><?php echo esc_html( $demande['reference'] ?? __( 'Dossier', 'mairie-civique' ) ); ?></h2>
						<p class="mairie-muted">
							<?php
							echo esc_html(
								$request_types[ $demande['request_type'] ?? '' ] ?? ( $demande['request_type'] ?? '—' )
							);
							?>
							&bull;
							<?php
							echo esc_html(
								isset( $demande['created_at'] )
									? date_i18n( 'd/m/Y', strtotime( $demande['created_at'] ) )
									: '—'
							);
							?>
						</p>
					</div>
					<span class="mairie-status-badge mairie-status-badge--<?php echo esc_attr( mairie_laravel_get_status_badge_class( $demande['status'] ?? 'pending' ) ); ?>">
						<?php echo esc_html( mairie_laravel_get_status_label( $demande['status'] ?? 'pending' ) ); ?>
					</span>
				</div>

				<?php if ( ! empty( $demande['agent'] ) ) : ?>
					<div class="mairie-demande-detail__agent">
						<strong><?php esc_html_e( 'Agent assigné :', 'mairie-civique' ); ?></strong>
						<?php
						$_a = $demande['agent'];
						$_aname = trim( ( $_a['first_name'] ?? '' ) . ' ' . ( $_a['last_name'] ?? '' ) );
						echo esc_html( $_aname ?: ( $_a['name'] ?? '—' ) );
						?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $demande['agent_notes'] ) ) : ?>
					<div class="mairie-demande-detail__notes">
						<strong><?php esc_html_e( 'Notes de traitement :', 'mairie-civique' ); ?></strong>
						<p><?php echo nl2br( esc_html( $demande['agent_notes'] ) ); ?></p>
					</div>
				<?php endif; ?>

				<?php /* ── Fil de messages ── */ ?>
				<div class="mairie-messages">
					<h3><?php esc_html_e( 'Messagerie', 'mairie-civique' ); ?></h3>

					<?php if ( empty( $messages ) ) : ?>
						<p class="mairie-muted"><?php esc_html_e( 'Aucun message pour ce dossier.', 'mairie-civique' ); ?></p>
					<?php else : ?>
						<div class="mairie-messages__thread">
							<?php foreach ( $messages as $msg ) :
								$is_mine = (int) ( $msg['sender_id'] ?? 0 ) === (int) get_current_user_id();
								// Note: sender_id est l'ID Laravel, pas l'ID WordPress — on l'utilise uniquement pour différencier visuel
								$sender_name = '';
								if ( isset( $msg['sender'] ) ) {
									$s = $msg['sender'];
									$sender_name = trim( ( $s['first_name'] ?? '' ) . ' ' . ( $s['last_name'] ?? '' ) );
									if ( '' === $sender_name ) {
										$sender_name = $s['name'] ?? __( 'Inconnu', 'mairie-civique' );
									}
								}
								?>
								<div class="mairie-message mairie-message--<?php echo $is_mine ? 'mine' : 'theirs'; ?>">
									<div class="mairie-message__meta">
										<span class="mairie-message__sender"><?php echo esc_html( $sender_name ); ?></span>
										<span class="mairie-message__date mairie-muted">
											<?php echo esc_html(
												isset( $msg['created_at'] )
													? date_i18n( 'd/m/Y H:i', strtotime( $msg['created_at'] ) )
													: ''
											); ?>
										</span>
									</div>
									<div class="mairie-message__body">
										<?php echo nl2br( esc_html( $msg['body'] ?? '' ) ); ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php /* ── Formulaire de réponse ── */ ?>
				<?php if ( in_array( $demande['status'] ?? '', array( 'pending', 'assigned', 'processing' ), true ) ) : ?>
					<div class="mairie-message-form">
						<h3><?php esc_html_e( 'Envoyer un message', 'mairie-civique' ); ?></h3>
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<input type="hidden" name="action" value="mairie_laravel_send_message">
							<input type="hidden" name="demande_id" value="<?php echo esc_attr( $demande_id ); ?>">
							<input type="hidden" name="redirect_to" value="<?php echo esc_attr( add_query_arg( 'demande_id', $demande_id, $page_url ) ); ?>">
							<?php wp_nonce_field( 'mairie_laravel_send_message', 'mairie_laravel_msg_nonce' ); ?>
							<div class="mairie-form-field">
								<label class="form-label" for="mairie-msg-body"><?php esc_html_e( 'Votre message', 'mairie-civique' ); ?></label>
								<textarea class="form-control" id="mairie-msg-body" name="message_body" rows="4" required placeholder="<?php esc_attr_e( 'Écrivez votre message ici…', 'mairie-civique' ); ?>"></textarea>
							</div>
							<div class="mairie-form-actions">
								<button class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4" type="submit">
									<i class="bi bi-send me-1"></i>
									<?php esc_html_e( 'Envoyer', 'mairie-civique' ); ?>
								</button>
							</div>
						</form>
					</div>
				<?php endif; ?>

			</div>
		</section>

	<?php else : ?>
		<?php /* ══════════════ VUE LISTE ══════════════ */ ?>

		<section class="mairie-request-section">

			<?php /* ── Onglets filtres ── */ ?>
			<nav class="mairie-demande-filters d-flex flex-wrap gap-2" aria-label="<?php esc_attr_e( 'Filtrer les demandes', 'mairie-civique' ); ?>">
				<?php foreach ( $filter_labels as $fkey => $flabel ) :
					$furl = 'tous' === $fkey
						? $page_url
						: add_query_arg( 'filtre', $fkey, $page_url );
					$is_active = $current_filter === $fkey;
					?>
					<a
						class="mairie-button <?php echo $is_active ? 'mairie-button--solid btn btn-success' : 'mairie-button--light btn btn-outline-success'; ?> rounded-pill px-3"
						href="<?php echo esc_url( $furl ); ?>"
						<?php echo $is_active ? 'aria-current="page"' : ''; ?>
					>
						<?php echo esc_html( $flabel ); ?>
						<?php if ( 'tous' !== $fkey ) :
							$fcount = count( array_filter( $all_demandes, static function ( $d ) use ( $fkey ) {
								return ( $d['status'] ?? '' ) === $fkey;
							} ) );
							if ( $fcount > 0 ) : ?>
								<span class="mairie-filter-count"><?php echo (int) $fcount; ?></span>
							<?php endif; ?>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="mairie-table-card">
				<?php if ( $api_error ) : ?>
					<?php /* déjà affiché plus haut */ ?>
				<?php elseif ( empty( $filtered_demandes ) ) : ?>
					<p class="mairie-muted">
						<?php
						'tous' === $current_filter
							? esc_html_e( 'Aucune demande pour le moment.', 'mairie-civique' )
							: printf(
								/* translators: %s: filtre label */
								esc_html__( 'Aucune demande avec le statut « %s ».', 'mairie-civique' ),
								esc_html( $filter_labels[ $current_filter ] )
							);
						?>
					</p>
				<?php else : ?>
					<div class="mairie-table-wrap">
						<table class="mairie-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Référence', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Date', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Type', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Statut', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Agent', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Messages', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Action', 'mairie-civique' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $filtered_demandes as $d ) :
									$_ref    = $d['reference'] ?? '—';
									$_date   = isset( $d['created_at'] ) ? date_i18n( 'd/m/Y', strtotime( $d['created_at'] ) ) : '—';
									$_type   = $request_types[ $d['request_type'] ?? '' ] ?? ( $d['request_type'] ?? '—' );
									$_status = $d['status'] ?? 'pending';
									$_agent_name = '';
									if ( ! empty( $d['agent'] ) ) {
										$_a = $d['agent'];
										$_agent_name = trim( ( $_a['first_name'] ?? '' ) . ' ' . ( $_a['last_name'] ?? '' ) );
										if ( '' === $_agent_name ) {
											$_agent_name = $_a['name'] ?? '';
										}
									}
									$_msg_count   = ! empty( $d['messages_count'] ) ? (int) $d['messages_count'] : ( ! empty( $d['messages'] ) ? count( $d['messages'] ) : 0 );
									$_unread      = (int) ( $d['unread_messages_count'] ?? 0 );
									$_detail_url  = add_query_arg( 'demande_id', (int) ( $d['id'] ?? 0 ), $page_url );
									?>
									<tr>
										<td><?php echo esc_html( $_ref ); ?></td>
										<td><?php echo esc_html( $_date ); ?></td>
										<td><?php echo esc_html( $_type ); ?></td>
										<td>
											<span class="mairie-status-badge mairie-status-badge--<?php echo esc_attr( mairie_laravel_get_status_badge_class( $_status ) ); ?>">
												<?php echo esc_html( mairie_laravel_get_status_label( $_status ) ); ?>
											</span>
										</td>
										<td><?php echo $_agent_name ? esc_html( $_agent_name ) : '<span class="mairie-muted">—</span>'; ?></td>
										<td>
											<?php if ( $_msg_count > 0 ) : ?>
												<span class="mairie-msg-count">
													<?php echo (int) $_msg_count; ?>
													<?php if ( $_unread > 0 ) : ?>
														<span class="mairie-unread-badge"><?php echo (int) $_unread; ?></span>
													<?php endif; ?>
												</span>
											<?php else : ?>
												<span class="mairie-muted">—</span>
											<?php endif; ?>
										</td>
										<td>
											<a class="mairie-button mairie-button--light btn btn-outline-success btn-sm rounded-pill" href="<?php echo esc_url( $_detail_url ); ?>">
												<?php esc_html_e( 'Ouvrir', 'mairie-civique' ); ?>
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>

		</section>

	<?php endif; ?>

	<div class="mairie-page__content" id="contenu">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<?php the_content(); ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>

</article>

<?php get_footer(); ?>
