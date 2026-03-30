<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 *
 * @package mairie-civique
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact_state = isset( $_GET['mairie_contact_state'] ) ? sanitize_key( wp_unslash( $_GET['mairie_contact_state'] ) ) : '';
$contact_error = isset( $_GET['mairie_contact_error'] ) ? sanitize_key( wp_unslash( $_GET['mairie_contact_error'] ) ) : '';
$form_name     = isset( $_GET['contact_name'] ) ? sanitize_text_field( wp_unslash( $_GET['contact_name'] ) ) : '';
$form_email    = isset( $_GET['contact_email'] ) ? sanitize_email( wp_unslash( $_GET['contact_email'] ) ) : '';
$form_subject  = isset( $_GET['contact_subject'] ) ? sanitize_text_field( wp_unslash( $_GET['contact_subject'] ) ) : '';
$form_message  = isset( $_GET['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_GET['contact_message'] ) ) : '';
$address       = mairie_civique_get_practical_info_value( 'address' );
$phone         = mairie_civique_get_practical_info_value( 'phone' );
$email_contact = mairie_civique_get_practical_info_value( 'email' );
$hours_week    = mairie_civique_get_practical_info_value( 'hours_week' );
$hours_sec     = mairie_civique_get_practical_info_value( 'hours_secondary' );
$map_url       = mairie_civique_get_practical_info_value( 'map_url' );

get_header();
?>

<div class="mairie-page-hero">
	<div class="mairie-shell">
		<p class="mairie-eyebrow"><?php esc_html_e( 'Mairie', 'mairie-civique' ); ?></p>
		<h1><?php the_title(); ?></h1>
	</div>
</div>

<div class="mairie-shell mairie-institutional-page">

	<div class="mairie-contact-grid">

		<?php // ── Left: Coordonnées ──────────────────────────────────────────── ?>
		<div class="mairie-contact-info col-lg-5">

			<div class="mairie-contact-block">
				<h2><?php esc_html_e( 'Coordonnées', 'mairie-civique' ); ?></h2>
				<dl>
					<?php if ( $address ) : ?>
						<dt><?php esc_html_e( 'Adresse', 'mairie-civique' ); ?></dt>
						<dd><?php echo nl2br( esc_html( $address ) ); ?></dd>
					<?php endif; ?>

					<?php if ( $phone ) : ?>
						<dt><?php esc_html_e( 'Tél.', 'mairie-civique' ); ?></dt>
						<dd>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>">
								<?php echo esc_html( $phone ); ?>
							</a>
						</dd>
					<?php endif; ?>

					<?php if ( $email_contact ) : ?>
						<dt><?php esc_html_e( 'Email', 'mairie-civique' ); ?></dt>
						<dd>
							<a href="mailto:<?php echo esc_attr( $email_contact ); ?>">
								<?php echo esc_html( $email_contact ); ?>
							</a>
						</dd>
					<?php endif; ?>
				</dl>
			</div>

			<?php if ( $hours_week || $hours_sec ) : ?>
				<div class="mairie-contact-block">
					<h2><?php esc_html_e( 'Horaires d\'accueil', 'mairie-civique' ); ?></h2>
					<dl>
						<?php if ( $hours_week ) : ?>
							<dt><?php esc_html_e( 'Semaine', 'mairie-civique' ); ?></dt>
							<dd><?php echo nl2br( esc_html( $hours_week ) ); ?></dd>
						<?php endif; ?>
						<?php if ( $hours_sec ) : ?>
							<dt><?php esc_html_e( 'Permanences', 'mairie-civique' ); ?></dt>
							<dd><?php echo nl2br( esc_html( $hours_sec ) ); ?></dd>
						<?php endif; ?>
					</dl>
				</div>
			<?php endif; ?>

			<div class="mairie-contact-map">
				<?php if ( $map_url ) : ?>
					<iframe
						src="<?php echo esc_url( $map_url ); ?>"
						allowfullscreen=""
						loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						title="<?php esc_attr_e( 'Plan d\'accès à la mairie', 'mairie-civique' ); ?>"
					></iframe>
				<?php else : ?>
					<div class="mairie-contact-map__placeholder">
						<p aria-hidden="true">🗺️</p>
						<p><?php esc_html_e( 'Plan indisponible.', 'mairie-civique' ); ?></p>
					</div>
				<?php endif; ?>
			</div>

		</div>

		<?php // ── Right: Formulaire ──────────────────────────────────────────── ?>
		<div class="mairie-contact-form-panel col-lg-7">
			<h2><?php esc_html_e( 'Envoyer un message', 'mairie-civique' ); ?></h2>

			<?php if ( 'success' === $contact_state ) : ?>
				<div class="mairie-notice mairie-notice--success alert alert-success">
					<p><?php esc_html_e( 'Message envoye et notifie au service destinataire.', 'mairie-civique' ); ?></p>
				</div>
			<?php elseif ( 'saved' === $contact_state ) : ?>
				<div class="mairie-notice mairie-notice--success alert alert-success">
					<p>
						<?php
						if ( 'mail_not_configured' === $contact_error ) {
							esc_html_e( 'Message enregistre. La notification email est indisponible car le serveur mail n est pas encore configure.', 'mairie-civique' );
						} else {
							esc_html_e( 'Message enregistre. Il reste visible dans l administration WordPress, meme si la notification email n a pas pu etre confirmee.', 'mairie-civique' );
						}
						?>
					</p>
				</div>
			<?php elseif ( 'error' === $contact_state ) : ?>
				<div class="mairie-notice mairie-notice--error alert alert-danger">
					<p>
						<?php
						if ( 'missing_required' === $contact_error ) {
							esc_html_e( 'Merci de renseigner votre nom, votre email et votre message.', 'mairie-civique' );
						} elseif ( 'invalid_email' === $contact_error ) {
							esc_html_e( 'L adresse email saisie n est pas valide.', 'mairie-civique' );
						} elseif ( 'save_failed' === $contact_error ) {
							esc_html_e( 'Le message a ete transmis, mais son enregistrement interne n a pas pu etre confirme.', 'mairie-civique' );
						} elseif ( 'delivery_failed' === $contact_error ) {
							esc_html_e( 'Le message n a pas pu etre transmis pour le moment. Merci de reessayer dans un instant.', 'mairie-civique' );
						} elseif ( 'mail_not_configured' === $contact_error ) {
							esc_html_e( 'Le serveur mail n est pas configure pour l envoi. Votre message est conserve dans WordPress.', 'mairie-civique' );
						} elseif ( 'mail_failed' === $contact_error ) {
							esc_html_e( 'La notification email a echoue. Votre message est conserve dans WordPress.', 'mairie-civique' );
						} elseif ( 'laravel_sync_failed' === $contact_error ) {
							esc_html_e( 'Message enregistre dans WordPress, mais la synchronisation vers le portail Laravel est temporairement indisponible.', 'mairie-civique' );
						} else {
							esc_html_e( 'Veuillez verifier le formulaire.', 'mairie-civique' );
						}
						?>
					</p>
				</div>
			<?php endif; ?>

			<form class="mairie-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'mairie_civique_contact_form', 'mairie_civique_contact_nonce' ); ?>
				<input type="hidden" name="action" value="mairie_civique_contact_form">

				<div class="mairie-field mb-3">
					<label class="form-label" for="contact_name"><i class="bi bi-person me-1"></i><?php esc_html_e( 'Nom', 'mairie-civique' ); ?></label>
					<input class="form-control" type="text" id="contact_name" name="contact_name" value="<?php echo esc_attr( $form_name ); ?>" required autocomplete="name">
				</div>

				<div class="mairie-field mb-3">
					<label class="form-label" for="contact_email"><i class="bi bi-envelope me-1"></i><?php esc_html_e( 'Email', 'mairie-civique' ); ?></label>
					<input class="form-control" type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr( $form_email ); ?>" required autocomplete="email">
				</div>

				<div class="mairie-field mb-3">
					<label class="form-label" for="contact_subject"><i class="bi bi-chat-text me-1"></i><?php esc_html_e( 'Objet', 'mairie-civique' ); ?></label>
					<input class="form-control" type="text" id="contact_subject" name="contact_subject" value="<?php echo esc_attr( $form_subject ); ?>">
				</div>

				<div class="mairie-field mb-3">
					<label class="form-label" for="contact_message"><i class="bi bi-pencil me-1"></i><?php esc_html_e( 'Message', 'mairie-civique' ); ?></label>
					<textarea class="form-control" id="contact_message" name="contact_message" required><?php echo esc_textarea( $form_message ); ?></textarea>
				</div>

				<button type="submit" class="mairie-button mairie-button--primary btn btn-success rounded-pill px-4 w-100">
					<i class="bi bi-send me-1"></i><?php esc_html_e( 'Envoyer', 'mairie-civique' ); ?>
				</button>
			</form>
		</div>

	</div>

</div>

<?php
get_footer();
