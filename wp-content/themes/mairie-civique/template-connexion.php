<?php
/*
Template Name: Connexion
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$login_state = isset( $_GET['mairie_login_state'] ) ? sanitize_key( wp_unslash( $_GET['mairie_login_state'] ) ) : '';
$redirect_to = isset( $_GET['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_GET['redirect_to'] ), '' ) : '';
$auth_view   = isset( $_GET['auth'] ) ? sanitize_key( wp_unslash( $_GET['auth'] ) ) : '';
$auth_origin = get_permalink();
$show_register_form = 'register' === $auth_view || 0 === strpos( $login_state, 'register_' );
$messages    = array(
	'failed'     => __( 'Identifiants invalides. Verifiez votre login et votre mot de passe.', 'mairie-civique' ),
	'missing'    => __( 'Renseignez votre identifiant et votre mot de passe pour continuer.', 'mairie-civique' ),
	'citizen_required' => __( 'Vous devez vous connecter avec un compte citoyen pour deposer une demande.', 'mairie-civique' ),
	'register_success' => __( 'Votre compte citoyen a ete cree avec succes. Vous etes maintenant connecte.', 'mairie-civique' ),
	'register_exists' => __( 'Cette adresse email est deja associee a un compte.', 'mairie-civique' ),
	'register_invalid_email' => __( 'Veuillez saisir une adresse email valide.', 'mairie-civique' ),
	'register_weak_password' => __( 'Le mot de passe doit contenir au moins 8 caracteres.', 'mairie-civique' ),
	'register_password_mismatch' => __( 'Les mots de passe ne correspondent pas.', 'mairie-civique' ),
	'register_failed' => __( 'La creation du compte a echoue. Veuillez reessayer.', 'mairie-civique' ),
	'register_created_login_failed' => __( 'Compte cree, mais connexion automatique impossible. Connectez-vous manuellement.', 'mairie-civique' ),
	'logged_out' => __( 'Vous etes maintenant deconnecte.', 'mairie-civique' ),
);
$portal_links = array(
	array(
		'label' => __( 'Acceder a l espace citoyen', 'mairie-civique' ),
		'url'   => mairie_civique_get_space_url( 'espace-citoyen' ),
	),
);

if ( current_user_can( 'manage_options' ) ) {
	$portal_links[] = array(
		'label' => __( 'Acceder a l espace admin', 'mairie-civique' ),
		'url'   => mairie_civique_get_space_url( 'espace-admin' ),
	);
}

if ( current_user_can( 'edit_mairie_demandes' ) ) {
	$portal_links[] = array(
		'label' => __( 'Acceder a l espace agent', 'mairie-civique' ),
		'url'   => mairie_civique_get_space_url( 'espace-agent' ),
	);
}

get_header();
?>

<section class="mairie-page mairie-login-page">
	<div class="mairie-login-grid">
		<div class="mairie-login-intro">
			<h1><?php esc_html_e( 'Se connecter', 'mairie-civique' ); ?></h1>
		</div>

		<div class="mairie-login-panel">
			<?php if ( is_user_logged_in() ) : ?>
				<h2><?php esc_html_e( 'Vous etes deja connecte', 'mairie-civique' ); ?></h2>
				<div class="mairie-login-links">
					<?php foreach ( $portal_links as $portal_link ) : ?>
						<a class="mairie-button mairie-button--solid" href="<?php echo esc_url( $portal_link['url'] ); ?>"><?php echo esc_html( $portal_link['label'] ); ?></a>
					<?php endforeach; ?>
					<a class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" href="<?php echo esc_url( wp_logout_url( add_query_arg( 'mairie_login_state', 'logged_out', mairie_civique_get_login_page_url() ) ) ); ?>"><i class="bi bi-box-arrow-right me-1"></i><?php esc_html_e( 'Se deconnecter', 'mairie-civique' ); ?></a>
				</div>
			<?php else : ?>
				<h2><?php esc_html_e( 'Se connecter', 'mairie-civique' ); ?></h2>
				<div class="mairie-auth-switch" role="tablist" aria-label="<?php esc_attr_e( 'Connexion ou inscription', 'mairie-civique' ); ?>">
					<button class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4" type="button" data-auth-toggle="login" aria-controls="mairie-login-auth-panel" aria-expanded="true"><i class="bi bi-box-arrow-in-right me-1"></i><?php esc_html_e( 'Se connecter', 'mairie-civique' ); ?></button>
					<button class="mairie-button mairie-button--light btn btn-outline-success rounded-pill px-4" type="button" data-auth-toggle="register" aria-controls="mairie-register-auth-panel" aria-expanded="<?php echo $show_register_form ? 'true' : 'false'; ?>"><i class="bi bi-person-plus me-1"></i><?php esc_html_e( 'Creer un compte', 'mairie-civique' ); ?></button>
				</div>

				<?php if ( isset( $messages[ $login_state ] ) ) : ?>
					<?php $alert_ok = in_array( $login_state, array( 'logged_out', 'register_success' ), true ); ?>
					<div class="mairie-form-alert mairie-form-alert--<?php echo $alert_ok ? 'success' : 'error'; ?> alert alert-<?php echo $alert_ok ? 'success' : 'danger'; ?>">
						<p><?php echo esc_html( $messages[ $login_state ] ); ?></p>
					</div>
				<?php endif; ?>

				<div id="mairie-login-auth-panel" class="mairie-auth-section" data-auth-section="login">
					<form class="mairie-login-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<input type="hidden" name="action" value="mairie_civique_login">
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
						<input type="hidden" name="auth_origin" value="<?php echo esc_attr( $auth_origin ); ?>">
						<?php wp_nonce_field( 'mairie_civique_login', 'mairie_civique_login_nonce' ); ?>

						<div class="mairie-form-field">
							<label for="mairie-login-log"><?php esc_html_e( 'Email ou identifiant', 'mairie-civique' ); ?></label>
							<label class="form-label" for="mairie-login-log"><i class="bi bi-person me-1"></i><?php esc_html_e( 'Email ou identifiant', 'mairie-civique' ); ?></label>
							<input class="form-control" id="mairie-login-log" name="log" type="text" autocomplete="username" required>
						</div>

						<div class="mairie-form-field mb-3">
							<label class="form-label" for="mairie-login-pwd"><i class="bi bi-key me-1"></i><?php esc_html_e( 'Mot de passe', 'mairie-civique' ); ?></label>
							<input class="form-control" id="mairie-login-pwd" name="pwd" type="password" autocomplete="current-password" required>
						</div>

						<div class="mairie-form-actions">
							<button class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4 w-100" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i><?php esc_html_e( 'Se connecter', 'mairie-civique' ); ?></button>
						</div>
					</form>
					<p class="mairie-login-meta"><a href="<?php echo esc_url( wp_lostpassword_url( mairie_civique_get_login_page_url() ) ); ?>"><?php esc_html_e( 'Mot de passe oublie ?', 'mairie-civique' ); ?></a></p>
				</div>

				<div id="mairie-register-auth-panel" class="mairie-auth-section mairie-register-panel" data-auth-section="register" <?php echo $show_register_form ? '' : 'hidden'; ?>>
					<form class="mairie-login-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
						<input type="hidden" name="action" value="mairie_civique_register">
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
						<input type="hidden" name="auth_origin" value="<?php echo esc_attr( $auth_origin ); ?>">
						<?php wp_nonce_field( 'mairie_civique_register', 'mairie_civique_register_nonce' ); ?>

						<div class="mairie-form-field">
							<label for="mairie-register-email"><?php esc_html_e( 'Email', 'mairie-civique' ); ?></label>
							<label class="form-label" for="mairie-register-email"><i class="bi bi-envelope me-1"></i><?php esc_html_e( 'Email', 'mairie-civique' ); ?></label>
							<input class="form-control" id="mairie-register-email" name="reg_email" type="email" autocomplete="email" required>
						</div>

						<div class="mairie-form-grid row g-2">
							<div class="mairie-form-field mb-2 col-6">
								<label class="form-label" for="mairie-register-first-name"><i class="bi bi-person me-1"></i><?php esc_html_e( 'Prenom', 'mairie-civique' ); ?></label>
								<input class="form-control" id="mairie-register-first-name" name="reg_first_name" type="text" autocomplete="given-name">
							</div>
							<div class="mairie-form-field mb-2 col-6">
								<label class="form-label" for="mairie-register-last-name"><i class="bi bi-person me-1"></i><?php esc_html_e( 'Nom', 'mairie-civique' ); ?></label>
								<input class="form-control" id="mairie-register-last-name" name="reg_last_name" type="text" autocomplete="family-name">
							</div>
						</div>

						<div class="mairie-form-field mb-3">
							<label class="form-label" for="mairie-register-password"><i class="bi bi-key me-1"></i><?php esc_html_e( 'Mot de passe', 'mairie-civique' ); ?></label>
							<input class="form-control" id="mairie-register-password" name="reg_password" type="password" minlength="8" autocomplete="new-password" required>
						</div>

						<div class="mairie-form-field mb-3">
							<label class="form-label" for="mairie-register-password-confirm"><i class="bi bi-key-fill me-1"></i><?php esc_html_e( 'Confirmer', 'mairie-civique' ); ?></label>
							<input class="form-control" id="mairie-register-password-confirm" name="reg_password_confirm" type="password" minlength="8" autocomplete="new-password" required>
						</div>

						<div class="mairie-form-actions">
							<button class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4 w-100" type="submit"><i class="bi bi-person-check me-1"></i><?php esc_html_e( 'Creer mon compte', 'mairie-civique' ); ?></button>
						</div>
					</form>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		var authButtons = document.querySelectorAll('[data-auth-toggle]');
		var authSections = document.querySelectorAll('[data-auth-section]');

		if (!authButtons.length || !authSections.length) {
			return;
		}

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
	});
</script>

<?php
get_footer();
