<?php
/*
Template Name: Espace Citoyen
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$space              = mairie_civique_get_space_config( 'citoyen' );
$guard              = mairie_civique_get_space_guard_context( 'citoyen' );
$request_types      = mairie_civique_get_request_types();
$feedback           = mairie_civique_get_request_feedback();
$form_values        = is_array( $feedback ) && isset( $feedback['values'] ) && is_array( $feedback['values'] ) ? $feedback['values'] : mairie_civique_get_empty_request_values();
$form_errors        = is_array( $feedback ) && isset( $feedback['errors'] ) && is_array( $feedback['errors'] ) ? $feedback['errors'] : array();
$is_citizen_user    = mairie_civique_user_is_citizen();
$citizen_auth_url   = mairie_civique_get_space_url( 'espace-citoyen' );
$login_state        = isset( $_GET['mairie_login_state'] ) ? sanitize_key( wp_unslash( $_GET['mairie_login_state'] ) ) : '';
$show_register_form = 0 === strpos( $login_state, 'register_' );
$auth_messages      = array(
	'failed'                        => __( 'Identifiants invalides.', 'mairie-civique' ),
	'missing'                       => __( 'Saisissez vos identifiants.', 'mairie-civique' ),
	'citizen_required'              => __( 'Connectez-vous avec un compte citoyen.', 'mairie-civique' ),
	'register_success'              => __( 'Compte cree. Vous etes connecte.', 'mairie-civique' ),
	'register_exists'               => __( 'Cet email existe deja.', 'mairie-civique' ),
	'register_invalid_email'        => __( 'Email invalide.', 'mairie-civique' ),
	'register_weak_password'        => __( '8 caracteres minimum.', 'mairie-civique' ),
	'register_password_mismatch'    => __( 'Les mots de passe ne correspondent pas.', 'mairie-civique' ),
	'register_failed'               => __( 'Creation impossible. Reessayez.', 'mairie-civique' ),
	'register_created_login_failed' => __( 'Compte cree. Connectez-vous maintenant.', 'mairie-civique' ),
	'logged_out'                    => __( 'Vous etes deconnecte.', 'mairie-civique' ),
);
$citizen_requests   = $guard['allowed'] ? mairie_civique_get_current_user_requests() : array();

get_header();
?>

<article class="mairie-space mairie-space--citoyen">
	<?php if ( $guard['allowed'] ) : ?>
		<section class="mairie-space-hero">
			<div>
				<span class="mairie-section__eyebrow"><?php echo esc_html( $space['eyebrow'] ); ?></span>
				<h1><?php echo esc_html( $space['title'] ); ?></h1>
				<p><?php esc_html_e( 'Faire une demande, envoyer un message et suivre ses dossiers.', 'mairie-civique' ); ?></p>
				<div class="mairie-space-hero__actions d-flex flex-wrap gap-2">
					<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="#demandes"><i class="bi bi-file-earmark-plus me-1"></i><?php esc_html_e( 'Faire une demande', 'mairie-civique' ); ?></a>
					<a class="mairie-button mairie-button--ghost-dark btn btn-outline-secondary rounded-pill px-4" href="#mes-demandes"><i class="bi bi-folder2-open me-1"></i><?php esc_html_e( 'Mes demandes', 'mairie-civique' ); ?></a>
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

		<section class="mairie-section">
			<div class="mairie-grid row row-cols-1 row-cols-md-3 g-3">
				<article class="mairie-card">
					<h3><i class="bi bi-file-earmark-plus me-2 text-success"></i><?php esc_html_e( 'Nouvelle demande', 'mairie-civique' ); ?></h3>
					<p><?php esc_html_e( 'Deposer une demande.', 'mairie-civique' ); ?></p>
					<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="#demandes"><?php esc_html_e( 'Commencer', 'mairie-civique' ); ?></a>
				</article>

				<article class="mairie-card">
					<h3><i class="bi bi-chat-dots me-2 text-success"></i><?php esc_html_e( 'Envoyer un message', 'mairie-civique' ); ?></h3>
					<p><?php esc_html_e( 'Contacter la mairie.', 'mairie-civique' ); ?></p>
					<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="<?php echo esc_url( mairie_civique_get_space_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contacter', 'mairie-civique' ); ?></a>
				</article>

				<article class="mairie-card">
					<h3><i class="bi bi-folder-check me-2 text-success"></i><?php esc_html_e( 'Suivi', 'mairie-civique' ); ?></h3>
					<p><?php esc_html_e( 'Voir l etat des demandes.', 'mairie-civique' ); ?></p>
					<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="#mes-demandes"><?php esc_html_e( 'Ouvrir', 'mairie-civique' ); ?></a>
				</article>
			</div>
		</section>

		<section id="demandes" class="mairie-request-section">
			<div class="mairie-request-section__intro">
				<span class="mairie-section__eyebrow"><?php esc_html_e( 'Demandes d etat civil', 'mairie-civique' ); ?></span>
				<h2><?php esc_html_e( 'Faire une demande', 'mairie-civique' ); ?></h2>
				<p class="mairie-muted"><?php esc_html_e( 'Compte citoyen requis.', 'mairie-civique' ); ?></p>
			</div>

			<div class="mairie-request-grid row g-3">
				<div class="mairie-space-panel">
					<h2><i class="bi bi-list-check me-2 text-success"></i><?php esc_html_e( 'Types proposes', 'mairie-civique' ); ?></h2>
					<ul>
						<?php foreach ( $request_types as $label ) : ?>
							<li><i class="bi bi-dot me-1"></i><?php echo esc_html( $label ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="mairie-space-panel">
					<h2><i class="bi bi-info-circle me-2 text-success"></i><?php esc_html_e( 'Informations demandees', 'mairie-civique' ); ?></h2>
					<ul>
						<li><i class="bi bi-dot me-1"></i><?php esc_html_e( 'Identite et contact', 'mairie-civique' ); ?></li>
						<li><i class="bi bi-dot me-1"></i><?php esc_html_e( 'Naissance et registre', 'mairie-civique' ); ?></li>
						<li><i class="bi bi-dot me-1"></i><?php esc_html_e( 'Adresse et parents', 'mairie-civique' ); ?></li>
						<li><i class="bi bi-dot me-1"></i><?php esc_html_e( 'Details et document joint si besoin', 'mairie-civique' ); ?></li>
					</ul>
				</div>
			</div>

			<?php if ( is_array( $feedback ) && ! empty( $feedback['message'] ) ) : ?>
				<div class="mairie-form-alert mairie-form-alert--<?php echo 'success' === $feedback['status'] ? 'success' : 'error'; ?> alert alert-<?php echo 'success' === $feedback['status'] ? 'success' : 'danger'; ?>">
					<p><?php echo esc_html( $feedback['message'] ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( $is_citizen_user ) : ?>
				<form class="mairie-request-form" action="<?php echo esc_url( mairie_civique_get_request_submission_url() ); ?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="action" value="mairie_civique_submit_request">
					<?php wp_nonce_field( 'mairie_civique_submit_request', 'mairie_civique_request_nonce' ); ?>

					<div class="mairie-form-grid mairie-form-grid--wide">
						<div class="mairie-form-field <?php echo isset( $form_errors['request_type'] ) ? 'has-error' : ''; ?>">
							<label class="form-label" for="request_type"><?php esc_html_e( 'Type', 'mairie-civique' ); ?></label>
							<select class="form-select" id="request_type" name="request_type" required data-request-type>
								<option value=""><?php esc_html_e( 'Selectionner', 'mairie-civique' ); ?></option>
								<?php foreach ( $request_types as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $form_values['request_type'], $value ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
							<?php if ( isset( $form_errors['request_type'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['request_type'] ); ?></p>
							<?php endif; ?>
						</div>
					</div>

					<div class="mairie-form-grid">
						<div class="mairie-form-field <?php echo isset( $form_errors['email'] ) ? 'has-error' : ''; ?>">
							<label for="email"><?php esc_html_e( 'Email', 'mairie-civique' ); ?></label>
							<input id="email" name="email" type="email" value="<?php echo esc_attr( $form_values['email'] ); ?>" required>
							<?php if ( isset( $form_errors['email'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['email'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['first_name'] ) ? 'has-error' : ''; ?>">
							<label for="first_name"><?php esc_html_e( 'Prenom', 'mairie-civique' ); ?></label>
							<input id="first_name" name="first_name" type="text" value="<?php echo esc_attr( $form_values['first_name'] ); ?>" required>
							<?php if ( isset( $form_errors['first_name'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['first_name'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['last_name'] ) ? 'has-error' : ''; ?>">
							<label for="last_name"><?php esc_html_e( 'Nom', 'mairie-civique' ); ?></label>
							<input id="last_name" name="last_name" type="text" value="<?php echo esc_attr( $form_values['last_name'] ); ?>" required>
							<?php if ( isset( $form_errors['last_name'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['last_name'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['birth_date'] ) ? 'has-error' : ''; ?>">
							<label for="birth_date"><?php esc_html_e( 'Naissance', 'mairie-civique' ); ?></label>
							<input id="birth_date" name="birth_date" type="date" value="<?php echo esc_attr( $form_values['birth_date'] ); ?>" required>
							<?php if ( isset( $form_errors['birth_date'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['birth_date'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['birth_place'] ) ? 'has-error' : ''; ?>">
							<label for="birth_place"><?php esc_html_e( 'Lieu', 'mairie-civique' ); ?></label>
							<input id="birth_place" name="birth_place" type="text" value="<?php echo esc_attr( $form_values['birth_place'] ); ?>" required>
							<?php if ( isset( $form_errors['birth_place'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['birth_place'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['register_number'] ) ? 'has-error' : ''; ?>">
							<label for="register_number"><?php esc_html_e( 'Registre', 'mairie-civique' ); ?></label>
							<input id="register_number" name="register_number" type="text" value="<?php echo esc_attr( $form_values['register_number'] ); ?>" required>
							<?php if ( isset( $form_errors['register_number'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['register_number'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field mairie-form-field--full <?php echo isset( $form_errors['address'] ) ? 'has-error' : ''; ?>">
							<label for="address"><?php esc_html_e( 'Adresse', 'mairie-civique' ); ?></label>
							<textarea id="address" name="address" rows="3" required><?php echo esc_textarea( $form_values['address'] ); ?></textarea>
							<?php if ( isset( $form_errors['address'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['address'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['parent_one_first_name'] ) ? 'has-error' : ''; ?>">
							<label for="parent_one_first_name"><?php esc_html_e( 'Prenom parent 1', 'mairie-civique' ); ?></label>
							<input id="parent_one_first_name" name="parent_one_first_name" type="text" value="<?php echo esc_attr( $form_values['parent_one_first_name'] ); ?>" required>
							<?php if ( isset( $form_errors['parent_one_first_name'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['parent_one_first_name'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['parent_one_last_name'] ) ? 'has-error' : ''; ?>">
							<label for="parent_one_last_name"><?php esc_html_e( 'Nom parent 1', 'mairie-civique' ); ?></label>
							<input id="parent_one_last_name" name="parent_one_last_name" type="text" value="<?php echo esc_attr( $form_values['parent_one_last_name'] ); ?>" required>
							<?php if ( isset( $form_errors['parent_one_last_name'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['parent_one_last_name'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['parent_two_first_name'] ) ? 'has-error' : ''; ?>">
							<label for="parent_two_first_name"><?php esc_html_e( 'Prenom parent 2', 'mairie-civique' ); ?></label>
							<input id="parent_two_first_name" name="parent_two_first_name" type="text" value="<?php echo esc_attr( $form_values['parent_two_first_name'] ); ?>" required>
							<?php if ( isset( $form_errors['parent_two_first_name'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['parent_two_first_name'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field <?php echo isset( $form_errors['parent_two_last_name'] ) ? 'has-error' : ''; ?>">
							<label for="parent_two_last_name"><?php esc_html_e( 'Nom parent 2', 'mairie-civique' ); ?></label>
							<input id="parent_two_last_name" name="parent_two_last_name" type="text" value="<?php echo esc_attr( $form_values['parent_two_last_name'] ); ?>" required>
							<?php if ( isset( $form_errors['parent_two_last_name'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['parent_two_last_name'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field mairie-form-field--full <?php echo isset( $form_errors['details'] ) ? 'has-error' : ''; ?>">
							<label for="details"><?php esc_html_e( 'Details', 'mairie-civique' ); ?></label>
							<textarea id="details" name="details" rows="4" placeholder="<?php esc_attr_e( 'Preciser la demande.', 'mairie-civique' ); ?>"><?php echo esc_textarea( $form_values['details'] ); ?></textarea>
							<?php if ( isset( $form_errors['details'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['details'] ); ?></p>
							<?php endif; ?>
						</div>

						<div class="mairie-form-field mairie-form-field--full <?php echo isset( $form_errors['supporting_document'] ) ? 'has-error' : ''; ?>" data-upload-field <?php echo 'autre' === $form_values['request_type'] ? '' : 'hidden'; ?>>
							<label for="supporting_document"><?php esc_html_e( 'Document', 'mairie-civique' ); ?></label>
							<input id="supporting_document" name="supporting_document" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
							<p class="mairie-form-help"><?php esc_html_e( 'Piece jointe si besoin.', 'mairie-civique' ); ?></p>
							<?php if ( isset( $form_errors['supporting_document'] ) ) : ?>
								<p class="mairie-form-error"><?php echo esc_html( $form_errors['supporting_document'] ); ?></p>
							<?php endif; ?>
						</div>
					</div>

					<div class="mairie-form-actions">
						<button class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4" type="submit"><i class="bi bi-send me-1"></i><?php esc_html_e( 'Envoyer', 'mairie-civique' ); ?></button>
					</div>
				</form>
			<?php endif; ?>
		</section>

		<section id="mes-demandes" class="mairie-request-section">
			<div class="mairie-request-section__intro">
				<span class="mairie-section__eyebrow"><?php esc_html_e( 'Suivi', 'mairie-civique' ); ?></span>
				<h2><?php esc_html_e( 'Mes demandes', 'mairie-civique' ); ?></h2>
			</div>

			<?php
			$_laravel_jwt    = mairie_laravel_get_user_jwt( get_current_user_id() );
			$_laravel_data   = $_laravel_jwt ? mairie_laravel_get_mes_demandes( $_laravel_jwt ) : null;
			$_laravel_items  = ( is_array( $_laravel_data ) && ! empty( $_laravel_data['data'] ) ) ? $_laravel_data['data'] : array();
			$_mes_demandes_url = mairie_civique_get_space_url( 'mes-demandes' );
			?>

			<div class="mairie-table-card">
				<?php if ( ! $_laravel_jwt ) : ?>
					<p class="mairie-muted"><?php esc_html_e( 'Reconnectez-vous pour synchroniser vos demandes avec le service en ligne.', 'mairie-civique' ); ?></p>
				<?php elseif ( is_wp_error( $_laravel_data ) ) : ?>
					<p class="mairie-muted"><?php esc_html_e( 'Le service est temporairement indisponible. Réessayez dans quelques instants.', 'mairie-civique' ); ?></p>
				<?php elseif ( ! empty( $_laravel_items ) ) : ?>
					<div class="mairie-table-wrap">
						<table class="mairie-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Référence', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Date', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Type', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Statut', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Agent', 'mairie-civique' ); ?></th>
									<th><?php esc_html_e( 'Détail', 'mairie-civique' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( array_slice( $_laravel_items, 0, 5 ) as $_dem ) : ?>
									<?php
									$_ref    = esc_html( $_dem['reference'] ?? '—' );
									$_date   = isset( $_dem['created_at'] ) ? date_i18n( 'd/m/Y', strtotime( $_dem['created_at'] ) ) : '—';
									$_type   = esc_html( $request_types[ $_dem['request_type'] ?? '' ] ?? ( $_dem['request_type'] ?? '—' ) );
									$_status = $_dem['status'] ?? 'pending';
									$_agent  = '';
									if ( ! empty( $_dem['agent'] ) ) {
										$_agent = trim( ( $_dem['agent']['first_name'] ?? '' ) . ' ' . ( $_dem['agent']['last_name'] ?? '' ) );
										if ( '' === $_agent ) {
											$_agent = $_dem['agent']['name'] ?? '';
										}
									}
									$_detail_url = add_query_arg( 'demande_id', (int) ( $_dem['id'] ?? 0 ), $_mes_demandes_url );
									?>
									<tr>
										<td><?php echo $_ref; ?></td>
										<td><?php echo esc_html( $_date ); ?></td>
										<td><?php echo $_type; ?></td>
										<td>
											<span class="mairie-status-badge mairie-status-badge--<?php echo esc_attr( mairie_laravel_get_status_badge_class( $_status ) ); ?>">
												<?php echo esc_html( mairie_laravel_get_status_label( $_status ) ); ?>
											</span>
										</td>
										<td><?php echo $_agent ? esc_html( $_agent ) : '<span class="mairie-muted">—</span>'; ?></td>
										<td>
											<a class="mairie-button mairie-button--light btn btn-outline-success btn-sm rounded-pill" href="<?php echo esc_url( $_detail_url ); ?>">
												<?php esc_html_e( 'Voir', 'mairie-civique' ); ?>
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php if ( count( $_laravel_items ) > 5 || ! empty( $_mes_demandes_url ) ) : ?>
						<div class="mairie-table-footer">
							<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="<?php echo esc_url( $_mes_demandes_url ); ?>">
								<?php esc_html_e( 'Voir tous mes dossiers', 'mairie-civique' ); ?>
							</a>
						</div>
					<?php endif; ?>
				<?php else : ?>
					<p class="mairie-muted"><?php esc_html_e( 'Aucune demande pour le moment.', 'mairie-civique' ); ?></p>
				<?php endif; ?>
			</div>
		</section>
	<?php else : ?>
		<section class="mairie-access-card mairie-access-card--auth">
			<span class="mairie-section__eyebrow"><?php esc_html_e( 'Espace securise', 'mairie-civique' ); ?></span>
			<h1><?php esc_html_e( 'Espace citoyen', 'mairie-civique' ); ?></h1>
			<p class="mairie-muted"><?php esc_html_e( 'Se connecter ou creer un compte.', 'mairie-civique' ); ?></p>

			<?php if ( isset( $auth_messages[ $login_state ] ) ) : ?>
				<div class="mairie-form-alert mairie-form-alert--<?php echo in_array( $login_state, array( 'logged_out', 'register_success' ), true ) ? 'success' : 'error'; ?> alert alert-<?php echo in_array( $login_state, array( 'logged_out', 'register_success' ), true ) ? 'success' : 'danger'; ?>">
					<p><?php echo esc_html( $auth_messages[ $login_state ] ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( ! $guard['is_logged_in'] ) : ?>
				<div class="mairie-auth-switch" role="tablist" aria-label="<?php esc_attr_e( 'Acces citoyen', 'mairie-civique' ); ?>">
					<button class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4" type="button" data-auth-toggle="login" aria-controls="mairie-auth-login" aria-expanded="true"><i class="bi bi-box-arrow-in-right me-1"></i><?php esc_html_e( 'Se connecter', 'mairie-civique' ); ?></button>
					<button class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" type="button" data-auth-toggle="register" aria-controls="mairie-auth-register" aria-expanded="<?php echo $show_register_form ? 'true' : 'false'; ?>"><i class="bi bi-person-plus me-1"></i><?php esc_html_e( 'Creer un compte', 'mairie-civique' ); ?></button>
				</div>

				<div id="mairie-auth-login" class="mairie-auth-section" data-auth-section="login">
					<form class="mairie-login-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<input type="hidden" name="action" value="mairie_civique_login">
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $citizen_auth_url . '#demandes' ); ?>">
						<input type="hidden" name="auth_origin" value="<?php echo esc_attr( $citizen_auth_url ); ?>">
						<?php wp_nonce_field( 'mairie_civique_login', 'mairie_civique_login_nonce' ); ?>

						<div class="mairie-form-field">
							<label for="mairie-citizen-login-log"><?php esc_html_e( 'Email ou identifiant', 'mairie-civique' ); ?></label>
							<input id="mairie-citizen-login-log" name="log" type="text" autocomplete="username" required>
						</div>

						<div class="mairie-form-field">
							<label for="mairie-citizen-login-pwd"><?php esc_html_e( 'Mot de passe', 'mairie-civique' ); ?></label>
							<input id="mairie-citizen-login-pwd" name="pwd" type="password" autocomplete="current-password" required>
						</div>

						<div class="mairie-form-actions">
							<button class="mairie-button mairie-button--solid" type="submit"><?php esc_html_e( 'Se connecter', 'mairie-civique' ); ?></button>
						</div>
					</form>
				</div>

				<div id="mairie-auth-register" class="mairie-auth-section" data-auth-section="register" <?php echo $show_register_form ? '' : 'hidden'; ?>>
					<form class="mairie-login-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<input type="hidden" name="action" value="mairie_civique_register">
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $citizen_auth_url . '#demandes' ); ?>">
						<input type="hidden" name="auth_origin" value="<?php echo esc_attr( $citizen_auth_url ); ?>">
						<?php wp_nonce_field( 'mairie_civique_register', 'mairie_civique_register_nonce' ); ?>

						<div class="mairie-form-field">
							<label for="mairie-citizen-register-email"><?php esc_html_e( 'Email', 'mairie-civique' ); ?></label>
							<input id="mairie-citizen-register-email" name="reg_email" type="email" autocomplete="email" required>
						</div>

						<div class="mairie-form-grid">
							<div class="mairie-form-field">
								<label for="mairie-citizen-register-first-name"><?php esc_html_e( 'Prenom', 'mairie-civique' ); ?></label>
								<input id="mairie-citizen-register-first-name" name="reg_first_name" type="text" autocomplete="given-name">
							</div>
							<div class="mairie-form-field">
								<label for="mairie-citizen-register-last-name"><?php esc_html_e( 'Nom', 'mairie-civique' ); ?></label>
								<input id="mairie-citizen-register-last-name" name="reg_last_name" type="text" autocomplete="family-name">
							</div>
						</div>

						<div class="mairie-form-field">
							<label for="mairie-citizen-register-password"><?php esc_html_e( 'Mot de passe', 'mairie-civique' ); ?></label>
							<input id="mairie-citizen-register-password" name="reg_password" type="password" minlength="8" autocomplete="new-password" required>
						</div>

						<div class="mairie-form-field">
							<label for="mairie-citizen-register-password-confirm"><?php esc_html_e( 'Confirmer', 'mairie-civique' ); ?></label>
							<input id="mairie-citizen-register-password-confirm" name="reg_password_confirm" type="password" minlength="8" autocomplete="new-password" required>
						</div>

						<div class="mairie-form-actions">
							<button class="mairie-button mairie-button--solid" type="submit"><?php esc_html_e( 'Creer mon compte', 'mairie-civique' ); ?></button>
						</div>
					</form>
				</div>
			<?php endif; ?>

			<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="<?php echo esc_url( $guard['fallback_url'] ); ?>"><i class="bi bi-arrow-left me-1"></i><?php echo esc_html( $guard['fallback_label'] ); ?></a>
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

<script>
	document.addEventListener('DOMContentLoaded', function () {
		var authButtons = document.querySelectorAll('[data-auth-toggle]');
		var authSections = document.querySelectorAll('[data-auth-section]');
		var requestTypeField = document.querySelector('[data-request-type]');
		var uploadField = document.querySelector('[data-upload-field]');

		if (authButtons.length && authSections.length) {
			var syncAuthSections = function (target) {
				authSections.forEach(function (section) {
					section.hidden = section.getAttribute('data-auth-section') !== target;
				});

				authButtons.forEach(function (button) {
					var isActive = button.getAttribute('data-auth-toggle') === target;
					button.setAttribute('aria-expanded', isActive ? 'true' : 'false');
					button.classList.toggle('mairie-button--solid', isActive);
					button.classList.toggle('mairie-button--light', !isActive);
					button.classList.toggle('btn-success', isActive);
					button.classList.toggle('btn-outline-success', !isActive);
				});
			};

			authButtons.forEach(function (button) {
				button.addEventListener('click', function () {
					syncAuthSections(button.getAttribute('data-auth-toggle'));
				});
			});

			syncAuthSections(document.querySelector('[data-auth-section="register"]:not([hidden])') ? 'register' : 'login');
		}

		if (!requestTypeField || !uploadField) {
			return;
		}

		var syncUploadVisibility = function () {
			uploadField.hidden = requestTypeField.value !== 'autre';
		};

		requestTypeField.addEventListener('change', syncUploadVisibility);
		syncUploadVisibility();
	});
</script>

<?php
get_footer();
