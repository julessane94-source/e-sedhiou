<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$posts_page_id = (int) get_option( 'page_for_posts' );

$news_query     = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
	)
);
	/* Mapping mairie-button→Bootstrap pour le loop des CTA */
	$_bs_btn = static function ( $c ) {
		if ( 'mairie-button--solid' === $c ) {
			return 'btn btn-success rounded-pill px-4';
		}
		if ( 'mairie-button--light' === $c ) {
			return 'btn btn-outline-success rounded-pill px-4';
		}
		return 'btn btn-outline-secondary rounded-pill px-3';
	};

$posts_page_url = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/actualites/' );
$login_url      = mairie_civique_get_login_page_url();
$signup_url     = add_query_arg( 'auth', 'register', $login_url );
$contact_url    = mairie_civique_get_space_url( 'contact' );
$services_url   = mairie_civique_get_space_url( 'services' );
$phone          = mairie_civique_get_practical_info_value( 'phone' );
$email_contact  = mairie_civique_get_practical_info_value( 'email' );
$address        = mairie_civique_get_practical_info_value( 'address' );

/* ── Contexte utilisateur ─────────────────────────────────────────────── */
$is_logged_in = is_user_logged_in();
$is_admin     = $is_logged_in && current_user_can( 'manage_options' );
$is_agent     = $is_logged_in && ! $is_admin && mairie_civique_is_agent_user();
$is_citizen   = $is_logged_in && ! $is_admin && ! $is_agent && mairie_civique_user_is_citizen();

/* ── URLs par rôle ────────────────────────────────────────────────────── */
$my_space_url   = '';
$my_space_label = '';
$my_space_eyebrow = '';
$hero_cta       = array();  // [ ['url', 'label', 'class'], ... ]
$quick_cards    = array();  // [ ['title', 'url', 'link_label'], ... ]

if ( $is_admin ) {
	$my_space_url     = mairie_civique_get_space_url( 'espace-admin' );
	$my_space_label   = __( 'Espace administration', 'mairie-civique' );
	$my_space_eyebrow = __( 'Administrateur', 'mairie-civique' );
	$hero_cta         = array(
		array( 'url' => $my_space_url, 'label' => __( 'Tableau de bord', 'mairie-civique' ), 'class' => 'mairie-button--solid' ),
		array( 'url' => $services_url, 'label' => __( 'Voir les services', 'mairie-civique' ), 'class' => 'mairie-button--ghost-dark' ),
	);
	$quick_cards      = array(
		array( 'title' => __( 'Administration', 'mairie-civique' ), 'url' => $my_space_url, 'link_label' => __( 'Ouvrir', 'mairie-civique' ), 'icon' => 'bi-speedometer2', 'desc' => __( 'Piloter les espaces et les comptes.', 'mairie-civique' ) ),
		array( 'title' => __( 'Services', 'mairie-civique' ), 'url' => $services_url, 'link_label' => __( 'Ouvrir', 'mairie-civique' ), 'icon' => 'bi-grid-1x2', 'desc' => __( 'Consulter les demarches disponibles.', 'mairie-civique' ) ),
		array( 'title' => __( 'Actualites', 'mairie-civique' ), 'url' => $posts_page_url, 'link_label' => __( 'Voir', 'mairie-civique' ), 'icon' => 'bi-megaphone', 'desc' => __( 'Suivre les annonces municipales.', 'mairie-civique' ) ),
		array( 'title' => __( 'Contact', 'mairie-civique' ), 'url' => $contact_url, 'link_label' => __( 'Contacter', 'mairie-civique' ), 'icon' => 'bi-envelope-paper', 'desc' => __( 'Joindre rapidement les services.', 'mairie-civique' ) ),
	);
} elseif ( $is_agent ) {
	$my_space_url     = mairie_civique_get_space_url( 'espace-agent' );
	$my_space_label   = __( 'Espace agent', 'mairie-civique' );
	$my_space_eyebrow = __( 'Agent municipal', 'mairie-civique' );
	$hero_cta         = array(
		array( 'url' => $my_space_url, 'label' => __( 'Mon espace agent', 'mairie-civique' ), 'class' => 'mairie-button--solid' ),
		array( 'url' => $services_url, 'label' => __( 'Voir les services', 'mairie-civique' ), 'class' => 'mairie-button--ghost-dark' ),
	);
	$quick_cards      = array(
		array( 'title' => __( 'Espace agent', 'mairie-civique' ), 'url' => $my_space_url, 'link_label' => __( 'Ouvrir', 'mairie-civique' ), 'icon' => 'bi-briefcase', 'desc' => __( 'Retrouver vos dossiers et suivis.', 'mairie-civique' ) ),
		array( 'title' => __( 'Services', 'mairie-civique' ), 'url' => $services_url, 'link_label' => __( 'Ouvrir', 'mairie-civique' ), 'icon' => 'bi-grid-1x2', 'desc' => __( 'Acceder aux procedures de la mairie.', 'mairie-civique' ) ),
		array( 'title' => __( 'Actualites', 'mairie-civique' ), 'url' => $posts_page_url, 'link_label' => __( 'Voir', 'mairie-civique' ), 'icon' => 'bi-megaphone', 'desc' => __( 'Suivre les communications internes.', 'mairie-civique' ) ),
		array( 'title' => __( 'Contact', 'mairie-civique' ), 'url' => $contact_url, 'link_label' => __( 'Contacter', 'mairie-civique' ), 'icon' => 'bi-envelope-paper', 'desc' => __( 'Joindre rapidement l accueil.', 'mairie-civique' ) ),
	);
} elseif ( $is_citizen ) {
	$my_space_url     = mairie_civique_get_space_url( 'espace-citoyen' );
	$mes_demandes_url = mairie_civique_get_space_url( 'mes-demandes' );
	$my_space_label   = __( 'Espace citoyen', 'mairie-civique' );
	$my_space_eyebrow = __( 'Citoyen connecte', 'mairie-civique' );
	$hero_cta         = array(
		array( 'url' => $my_space_url, 'label' => __( 'Mon espace citoyen', 'mairie-civique' ), 'class' => 'mairie-button--solid' ),
		array( 'url' => $mes_demandes_url, 'label' => __( 'Mes demandes', 'mairie-civique' ), 'class' => 'mairie-button--light' ),
		array( 'url' => $services_url, 'label' => __( 'Voir les services', 'mairie-civique' ), 'class' => 'mairie-button--ghost-dark' ),
	);
	$quick_cards      = array(
		array( 'title' => __( 'Espace citoyen', 'mairie-civique' ), 'url' => $my_space_url, 'link_label' => __( 'Ouvrir', 'mairie-civique' ), 'icon' => 'bi-person-circle', 'desc' => __( 'Retrouver votre espace personnel.', 'mairie-civique' ) ),
		array( 'title' => __( 'Mes demandes', 'mairie-civique' ), 'url' => $mes_demandes_url, 'link_label' => __( 'Suivre', 'mairie-civique' ), 'icon' => 'bi-folder-check', 'desc' => __( 'Suivre vos demarches en cours.', 'mairie-civique' ) ),
		array( 'title' => __( 'Services', 'mairie-civique' ), 'url' => $services_url, 'link_label' => __( 'Ouvrir', 'mairie-civique' ), 'icon' => 'bi-grid-1x2', 'desc' => __( 'Voir les services disponibles.', 'mairie-civique' ) ),
		array( 'title' => __( 'Actualites', 'mairie-civique' ), 'url' => $posts_page_url, 'link_label' => __( 'Voir', 'mairie-civique' ), 'icon' => 'bi-megaphone', 'desc' => __( 'Suivre les informations utiles.', 'mairie-civique' ) ),
		array( 'title' => __( 'Contact', 'mairie-civique' ), 'url' => $contact_url, 'link_label' => __( 'Contacter', 'mairie-civique' ), 'icon' => 'bi-envelope-paper', 'desc' => __( 'Ecrire rapidement a la mairie.', 'mairie-civique' ) ),
	);
} else {
	/* Visiteur non connecté */
	$citizen_url  = mairie_civique_get_space_url( 'espace-citoyen' );
	$my_space_eyebrow = __( 'Mairie', 'mairie-civique' );
	$hero_cta     = array(
		array( 'url' => $services_url, 'label' => __( 'Voir les services', 'mairie-civique' ), 'class' => 'mairie-button--solid' ),
		array( 'url' => $contact_url, 'label' => __( 'Nous contacter', 'mairie-civique' ), 'class' => 'mairie-button--light' ),
	);
	$quick_cards  = array(
		array( 'title' => __( 'Services', 'mairie-civique' ), 'url' => $services_url, 'link_label' => __( 'Ouvrir', 'mairie-civique' ), 'icon' => 'bi-grid-1x2', 'desc' => __( 'Explorer les demarches utiles.', 'mairie-civique' ) ),
		array( 'title' => __( 'Actualites', 'mairie-civique' ), 'url' => $posts_page_url, 'link_label' => __( 'Voir', 'mairie-civique' ), 'icon' => 'bi-megaphone', 'desc' => __( 'Suivre la vie de la commune.', 'mairie-civique' ) ),
		array( 'title' => __( 'Contact', 'mairie-civique' ), 'url' => $contact_url, 'link_label' => __( 'Contacter', 'mairie-civique' ), 'icon' => 'bi-envelope-paper', 'desc' => __( 'Joindre facilement les services.', 'mairie-civique' ) ),
		array( 'title' => __( 'Équipe municipale', 'mairie-civique' ), 'url' => mairie_civique_get_space_url( 'equipe-municipale' ), 'link_label' => __( 'Découvrir', 'mairie-civique' ), 'icon' => 'bi-people', 'desc' => __( 'Connaitre les elus et responsables.', 'mairie-civique' ) ),
	);
}

$home_highlights = array(
	array( 'value' => __( '24/7', 'mairie-civique' ), 'label' => __( 'Acces aux informations', 'mairie-civique' ) ),
	array( 'value' => count( $quick_cards ), 'label' => __( 'Acces rapides', 'mairie-civique' ) ),
	array( 'value' => $news_query->found_posts ? (string) $news_query->found_posts : '0', 'label' => __( 'Actualites mises en avant', 'mairie-civique' ) ),
);

get_header();
?>

<section class="mairie-hero mairie-hero--blocksy">
	<div class="mairie-hero__grid row g-4 align-items-center">
		<div class="col-lg-6">
			<span class="mairie-hero__eyebrow"><?php echo esc_html( $my_space_eyebrow ); ?></span>
			<h1><?php esc_html_e( 'Portail de la mairie', 'mairie-civique' ); ?></h1>
			<p class="mairie-hero__lead"><?php esc_html_e( 'Accedez aux services municipaux, suivez les actualites de la commune et joignez les services utiles depuis une interface claire et rapide.', 'mairie-civique' ); ?></p>
			<?php if ( $is_logged_in ) : ?>
				<p class="mairie-hero__welcome">
					<?php
					$current_wp_user = wp_get_current_user();
					printf(
						/* translators: %s: prénom de l'utilisateur */
						esc_html__( 'Bienvenue, %s', 'mairie-civique' ),
						esc_html( $current_wp_user->first_name ?: $current_wp_user->display_name )
					);
					?>
				</p>
			<?php endif; ?>
			<div class="mairie-hero__actions d-flex flex-wrap gap-2 mt-3">
				<?php foreach ( $hero_cta as $cta ) : ?>
					<a class="mairie-button <?php echo esc_attr( $cta['class'] ); ?> <?php echo esc_attr( $_bs_btn( $cta['class'] ) ); ?>" href="<?php echo esc_url( $cta['url'] ); ?>">
						<?php echo esc_html( $cta['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
			<div class="mairie-home-kpis">
				<?php foreach ( $home_highlights as $highlight ) : ?>
					<div class="mairie-home-kpi">
						<strong><?php echo esc_html( $highlight['value'] ); ?></strong>
						<span><?php echo esc_html( $highlight['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="mairie-hero-badges">
				<span><i class="bi bi-check2-circle"></i><?php esc_html_e( 'Services disponibles', 'mairie-civique' ); ?></span>
				<span><i class="bi bi-shield-check"></i><?php esc_html_e( 'Acces securise', 'mairie-civique' ); ?></span>
				<span><i class="bi bi-clock-history"></i><?php esc_html_e( 'Reponses plus rapides', 'mairie-civique' ); ?></span>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="mairie-hero-panel card border-0 shadow-sm p-4">
				<?php if ( $is_logged_in && $my_space_url ) : ?>
					<div class="mairie-hero-feature">
						<strong><?php echo esc_html( $my_space_label ?: __( 'Mon espace', 'mairie-civique' ) ); ?></strong>
						<p><?php esc_html_e( 'Retrouvez vos outils, vos demandes et vos raccourcis prioritaires.', 'mairie-civique' ); ?></p>
						<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="<?php echo esc_url( $my_space_url ); ?>"><?php esc_html_e( 'Ouvrir mon espace', 'mairie-civique' ); ?></a>
					</div>
				<?php else : ?>
					<div class="mairie-hero-feature">
						<strong><?php esc_html_e( 'Equipe municipale', 'mairie-civique' ); ?></strong>
						<p><?php esc_html_e( 'Decouvrez les responsables et les interlocuteurs de votre commune.', 'mairie-civique' ); ?></p>
						<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="<?php echo esc_url( mairie_civique_get_space_url( 'equipe-municipale' ) ); ?>"><?php esc_html_e( 'Decouvrir notre equipe', 'mairie-civique' ); ?></a>
					</div>
				<?php endif; ?>
				<div class="mairie-hero-feature">
					<strong><?php esc_html_e( 'Contact direct', 'mairie-civique' ); ?></strong>
					<p><?php esc_html_e( 'Telephone, email et formulaire centralises pour joindre la mairie sans attente inutile.', 'mairie-civique' ); ?></p>
					<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Contacter la mairie', 'mairie-civique' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
// Slides du diaporama (images optionnelles dans wp-content/uploads)
// Placez vos fichiers dans le dossier uploads avec ces noms pour activer automatiquement les photos.
$upload = wp_upload_dir();
$slides = array(
	array(
		'key'   => 'citoyennete',
		'title' => __( 'Citoyenneté active', 'mairie-civique' ),
		'text'  => __( 'Participer à la vie locale, respecter les règles communes et renforcer la solidarité.', 'mairie-civique' ),
		'file'  => 'mairie-slides/citoyennete.jpg',
	),
	array(
		'key'   => 'patriotisme',
		'title' => __( 'Patriotisme respectueux', 'mairie-civique' ),
		'text'  => __( 'Aimer le territoire, soutenir l’esprit de service public et transmettre la mémoire collective.', 'mairie-civique' ),
		'file'  => 'mairie-slides/patriotisme.jpg',
	),
	array(
		'key'   => 'olympisme',
		'title' => __( 'Olympisme au quotidien', 'mairie-civique' ),
		'text'  => __( 'L’excellence avec respect, l’amitié et la discipline au service des autres.', 'mairie-civique' ),
		'file'  => 'mairie-slides/olympisme.jpg',
	),
);

foreach ( $slides as &$s ) {
	$relative = ltrim( (string) $s['file'], '/' );
	$disk     = trailingslashit( (string) $upload['basedir'] ) . $relative;
	$url      = trailingslashit( (string) $upload['baseurl'] ) . $relative;
	$s['img'] = is_string( $disk ) && file_exists( $disk ) ? $url : '';
}
unset( $s );
?>

<section class="mairie-section mairie-section--contrast mairie-values" id="mairie-values">
	<div class="mairie-section__grid row g-4 align-items-center">
		<div class="col-lg-6">
			<span class="mairie-section__eyebrow"><?php esc_html_e( 'Citoyenneté & valeurs', 'mairie-civique' ); ?></span>
			<h2><?php esc_html_e( 'Patriotisme, olympisme et engagement citoyen', 'mairie-civique' ); ?></h2>
			<p class="mairie-section__lead">
				Notre mairie s’appuie sur trois repères : la citoyenneté active, l’amour du territoire, et l’olympisme comme école du fair-play.
				Ensemble, nous favorisons le respect, la solidarité et le dépassement de soi au service de la commune.
			</p>

			<div class="mairie-values__bullets">
				<div class="mairie-values__bullet">
					<i class="bi bi-people-fill" aria-hidden="true"></i>
					<span><?php esc_html_e( 'Agir localement : participer, coopérer, s’entraider.', 'mairie-civique' ); ?></span>
				</div>
				<div class="mairie-values__bullet">
					<i class="bi bi-flag-fill" aria-hidden="true"></i>
					<span><?php esc_html_e( 'Transmettre : respecter les symboles, construire la mémoire.', 'mairie-civique' ); ?></span>
				</div>
				<div class="mairie-values__bullet">
					<i class="bi bi-trophy-fill" aria-hidden="true"></i>
					<span><?php esc_html_e( 'Vivre l’éthique sportive : excellence, amitié, respect.', 'mairie-civique' ); ?></span>
				</div>
			</div>

			<div class="mairie-values__cta d-flex flex-wrap gap-2 mt-3">
				<a class="mairie-button mairie-button--solid" href="<?php echo esc_url( $services_url ); ?>">
					<?php esc_html_e( 'Découvrir les services', 'mairie-civique' ); ?>
				</a>
				<a class="mairie-button mairie-button--ghost" href="<?php echo esc_url( $contact_url ); ?>">
					<?php esc_html_e( 'Nous contacter', 'mairie-civique' ); ?>
				</a>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="mairie-values-carousel" role="region" aria-label="<?php esc_attr_e( 'Diaporama : citoyenneté, patriotisme et olympisme', 'mairie-civique' ); ?>">
				<div class="mairie-values-carousel__viewport">
					<div class="mairie-values-carousel__track" data-carousel-track>
						<div class="mairie-values-carousel__slide" data-slide="0" aria-hidden="false">
							<div class="mairie-values-carousel__card">
								<div class="mairie-values-carousel__visual" aria-hidden="true">
									<?php if ( ! empty( $slides[0]['img'] ) ) : ?>
										<img class="mairie-values-carousel__img" src="<?php echo esc_url( $slides[0]['img'] ); ?>" alt="<?php echo esc_attr( $slides[0]['title'] ); ?>" loading="lazy" decoding="async">
									<?php else : ?>
									<svg viewBox="0 0 320 180" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
										<defs>
											<linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
												<stop offset="0" stop-color="#34d399" stop-opacity="0.35"/>
												<stop offset="1" stop-color="#60a5fa" stop-opacity="0.25"/>
											</linearGradient>
										</defs>
										<rect x="18" y="18" width="284" height="144" rx="18" fill="url(#g1)"/>
										<circle cx="90" cy="78" r="26" fill="rgba(255,255,255,0.22)"/>
										<circle cx="230" cy="78" r="26" fill="rgba(255,255,255,0.18)"/>
										<path d="M120 116 L160 86 L200 116" stroke="rgba(255,255,255,0.75)" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M132 122 C142 134 178 134 188 122" stroke="rgba(255,255,255,0.45)" stroke-width="8" stroke-linecap="round"/>
										<path d="M150 55 L170 55" stroke="rgba(255,255,255,0.6)" stroke-width="10" stroke-linecap="round"/>
										<path d="M140 62 C148 72 172 72 180 62" stroke="rgba(255,255,255,0.35)" stroke-width="8" stroke-linecap="round"/>
									</svg>
									<?php endif; ?>
								</div>
								<div class="mairie-values-carousel__content">
									<h3><?php echo esc_html( $slides[0]['title'] ); ?></h3>
									<p><?php echo esc_html( $slides[0]['text'] ); ?></p>
								</div>
							</div>
						</div>

						<div class="mairie-values-carousel__slide" data-slide="1" aria-hidden="true">
							<div class="mairie-values-carousel__card">
								<div class="mairie-values-carousel__visual" aria-hidden="true">
									<?php if ( ! empty( $slides[1]['img'] ) ) : ?>
										<img class="mairie-values-carousel__img" src="<?php echo esc_url( $slides[1]['img'] ); ?>" alt="<?php echo esc_attr( $slides[1]['title'] ); ?>" loading="lazy" decoding="async">
									<?php else : ?>
									<svg viewBox="0 0 180 320" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
										<!-- Drapeau du Sénégal : Patriotisme respectueux (position verticale) -->
										<rect x="18" y="18" width="144" height="284" rx="18" fill="#ffffff"/>
										<!-- Bande verte (vert sénégalais) - gauche -->
										<rect x="18" y="18" width="48" height="284" rx="18" fill="#007A5E" opacity="0.9"/>
										<!-- Bande jaune (or sénégalais) - centre -->
										<rect x="66" y="18" width="48" height="284" fill="#FCED00" opacity="0.9"/>
										<!-- Bande rouge (rouge sénégalais) - droite -->
										<rect x="114" y="18" width="48" height="284" rx="18" fill="#CE1126" opacity="0.9"/>
										<!-- Étoile verte au centre -->
										<g transform="translate(90, 160)">
											<polygon points="0,-18 4,-6 18,-6 8,4 12,16 0,10 -12,16 -8,4 -18,-6 -4,-6" fill="#00A651" opacity="0.95"/>
										</g>
									</svg>
									<?php endif; ?>
								</div>
								<div class="mairie-values-carousel__content">
									<h3><?php echo esc_html( $slides[1]['title'] ); ?></h3>
									<p><?php echo esc_html( $slides[1]['text'] ); ?></p>
								</div>
							</div>
						</div>

						<div class="mairie-values-carousel__slide" data-slide="2" aria-hidden="true">
							<div class="mairie-values-carousel__card">
								<div class="mairie-values-carousel__visual" aria-hidden="true">
									<?php if ( ! empty( $slides[2]['img'] ) ) : ?>
										<img class="mairie-values-carousel__img" src="<?php echo esc_url( $slides[2]['img'] ); ?>" alt="<?php echo esc_attr( $slides[2]['title'] ); ?>" loading="lazy" decoding="async">
									<?php else : ?>
									<svg viewBox="0 0 320 180" width="100%" height="auto" xmlns="http://www.w3.org/2000/svg">
										<!-- Anneaux Olympiques complets -->
										<defs>
											<linearGradient id="g3" x1="0" y1="0" x2="1" y2="1">
												<stop offset="0" stop-color="#60a5fa" stop-opacity="0.15"/>
												<stop offset="1" stop-color="#ef4444" stop-opacity="0.10"/>
											</linearGradient>
										</defs>
										<rect x="18" y="18" width="284" height="144" rx="18" fill="url(#g3)"/>
										
										<!-- Bande grise transparente en fond -->
										<rect x="30" y="65" width="260" height="50" rx="12" fill="rgba(255,255,255,0.08)"/>
										
										<!-- Anneaux olympiques -->
										<!-- Anneau bleu -->
										<circle cx="80" cy="90" r="14" fill="none" stroke="#0085C7" stroke-width="5" opacity="0.95"/>
										
										<!-- Anneau noir -->
										<circle cx="115" cy="90" r="14" fill="none" stroke="#000000" stroke-width="5" opacity="0.95"/>
										
										<!-- Anneau rouge -->
										<circle cx="150" cy="90" r="14" fill="none" stroke="#DF0024" stroke-width="5" opacity="0.95"/>
										
										<!-- Anneau jaune -->
										<circle cx="185" cy="90" r="14" fill="none" stroke="#F4C300" stroke-width="5" opacity="0.95"/>
										
										<!-- Anneau vert -->
										<circle cx="220" cy="90" r="14" fill="none" stroke="#00A651" stroke-width="5" opacity="0.95"/>
										
										<!-- Décoration: petite torche en bas -->
										<g transform="translate(160, 125)">
											<rect width="8" height="10" fill="rgba(255,255,255,0.4)" rx="2"/>
											<path d="M-4 -3 C -4 -8 -1 -12 0 -14 C 1 -12 4 -8 4 -3" fill="rgba(255,180,0,0.5)" opacity="0.6"/>
										</g>
									</svg>
									<?php endif; ?>
								</div>
								<div class="mairie-values-carousel__content">
									<h3><?php echo esc_html( $slides[2]['title'] ); ?></h3>
									<p><?php echo esc_html( $slides[2]['text'] ); ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="mairie-values-carousel__controls">
					<button type="button" class="mairie-values-carousel__btn" data-prev aria-label="<?php esc_attr_e( 'Slide précédente', 'mairie-civique' ); ?>">
						&#10094;
					</button>
					<button type="button" class="mairie-values-carousel__btn" data-next aria-label="<?php esc_attr_e( 'Slide suivante', 'mairie-civique' ); ?>">
						&#10095;
					</button>
				</div>

				<div class="mairie-values-carousel__dots" role="tablist" aria-label="<?php esc_attr_e( 'Choisir une valeur', 'mairie-civique' ); ?>">
					<button type="button" class="mairie-values-carousel__dot is-active" data-dot="0" role="tab" aria-current="true" aria-label="<?php esc_attr_e( 'Citoyenneté active', 'mairie-civique' ); ?>"></button>
					<button type="button" class="mairie-values-carousel__dot" data-dot="1" role="tab" aria-current="false" aria-label="<?php esc_attr_e( 'Patriotisme respectueux', 'mairie-civique' ); ?>"></button>
					<button type="button" class="mairie-values-carousel__dot" data-dot="2" role="tab" aria-current="false" aria-label="<?php esc_attr_e( 'Olympisme au quotidien', 'mairie-civique' ); ?>"></button>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="mairie-section mairie-section--news">
	<div class="mairie-section__grid row g-4 align-items-start">
		<div class="col-lg-4">
			<span class="mairie-section__eyebrow"><?php esc_html_e( 'Actualites', 'mairie-civique' ); ?></span>
			<h2><?php esc_html_e( 'Actualites', 'mairie-civique' ); ?></h2>
			<a class="mairie-button mairie-button--solid btn btn-success rounded-pill px-4" href="<?php echo esc_url( $posts_page_url ); ?>"><?php esc_html_e( 'Toutes les actualites', 'mairie-civique' ); ?></a>
		</div>

		<div class="mairie-news-grid col-lg-12 row row-cols-3 g-3">
			<?php if ( $news_query->have_posts() ) : ?>
				<?php while ( $news_query->have_posts() ) : ?>
					<?php $news_query->the_post(); ?>
					<article class="mairie-news-card">
						<p class="mairie-news-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt() ?: wp_strip_all_tags( get_the_content() ), 22 ) ); ?></p>
						<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Lire l actualite', 'mairie-civique' ); ?></a>
					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<article class="mairie-news-card mairie-news-card--empty">
					<p class="mairie-news-card__meta"><?php esc_html_e( 'Actualites', 'mairie-civique' ); ?></p>
					<h3><?php esc_html_e( 'Aucune actualite', 'mairie-civique' ); ?></h3>
					<a class="mairie-card__link btn btn-outline-success btn-sm rounded-pill mt-2" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php esc_html_e( 'Creer une actualite', 'mairie-civique' ); ?></a>
				</article>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="mairie-section">
	<div class="mairie-section__grid row g-4 align-items-start">
		<div class="col-lg-6">
			<span class="mairie-section__eyebrow"><?php esc_html_e( 'Contact', 'mairie-civique' ); ?></span>
			<h2><?php esc_html_e( 'Contact', 'mairie-civique' ); ?></h2>
			<p class="mairie-section__lead"><?php echo esc_html( $address ); ?></p>
		</div>

		<div class="mairie-practical-grid col-lg-6">
			<article class="mairie-practical-card">
				<h3><?php esc_html_e( 'Joindre la mairie', 'mairie-civique' ); ?></h3>
				<div class="mairie-practical-card__rows">
					<div class="mairie-practical-card__row">
						<strong><?php esc_html_e( 'Telephone', 'mairie-civique' ); ?></strong>
						<span><?php echo esc_html( $phone ); ?></span>
					</div>
					<div class="mairie-practical-card__row">
						<strong><?php esc_html_e( 'Email', 'mairie-civique' ); ?></strong>
						<span><?php echo esc_html( $email_contact ); ?></span>
					</div>
				</div>
			</article>
			<article class="mairie-practical-card">
				<h3><?php esc_html_e( 'Demarches utiles', 'mairie-civique' ); ?></h3>
				<p><?php esc_html_e( 'Un point d entree unique pour vos demandes, vos informations et vos prises de contact.', 'mairie-civique' ); ?></p>
				<a class="mairie-card__link btn btn-success rounded-pill px-4" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Envoyer un message', 'mairie-civique' ); ?></a>
			</article>
		</div>
	</div>
</section>

<script>
(function () {
	const root = document.querySelector('.mairie-values-carousel');
	if (!root) return;

	const slides = Array.from(root.querySelectorAll('[data-slide]'));
	const dots = Array.from(root.querySelectorAll('[data-dot]'));
	const prevBtn = root.querySelector('[data-prev]');
	const nextBtn = root.querySelector('[data-next]');
	const track = root.querySelector('[data-carousel-track]');

	if (!slides.length || !track) return;

	let index = 0;

	const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function render() {
		slides.forEach(function (slide, i) {
			slide.setAttribute('aria-hidden', i === index ? 'false' : 'true');
			slide.classList.toggle('is-active', i === index);
		});

		dots.forEach(function (dot, i) {
			const active = i === index;
			dot.classList.toggle('is-active', active);
			dot.setAttribute('aria-current', active ? 'true' : 'false');
		});

		track.style.transform = 'translateX(' + (-index * 100) + '%)';
	}

	function go(nextIndex) {
		index = (nextIndex + slides.length) % slides.length;
		render();
	}

	prevBtn && prevBtn.addEventListener('click', function () { go(index - 1); });
	nextBtn && nextBtn.addEventListener('click', function () { go(index + 1); });
	dots.forEach(function (dot, i) { dot.addEventListener('click', function () { go(i); }); });

	render();
	if (prefersReduced) return;

	window.setInterval(function () { go(index + 1); }, 6500);
})();
</script>

<?php
get_footer();
