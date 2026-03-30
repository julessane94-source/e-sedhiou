<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$header_account_url = is_user_logged_in() ? mairie_civique_get_space_url( mairie_civique_get_current_space_slug() ) : mairie_civique_get_login_page_url();
$header_account_label = is_user_logged_in() ? __( 'Mon espace', 'mairie-civique' ) : __( 'Connexion', 'mairie-civique' );
$is_logged_in = is_user_logged_in();
$signup_url = ! $is_logged_in ? add_query_arg( 'auth', 'register', mairie_civique_get_login_page_url() ) : '';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="mairie-site">
	<header class="mairie-header navbar navbar-expand-lg sticky-top">
		<div class="mairie-header__inner w-100 d-flex align-items-center gap-3 flex-wrap">
			<a class="mairie-branding navbar-brand me-auto" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span>
					<p class="mairie-branding__title"><?php bloginfo( 'name' ); ?></p>
					<p class="mairie-branding__tagline mairie-branding__tagline--scrolling"><span class="mairie-scrolling-text"><?php bloginfo( 'description' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php bloginfo( 'description' ); ?></span></p>
				</span>
			</a>

			<button class="navbar-toggler border-0 p-1" type="button"
				data-bs-toggle="collapse" data-bs-target="#mairie-primary-nav"
				aria-controls="mairie-primary-nav" aria-expanded="false"
				aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'mairie-civique' ); ?>">
				<i class="bi bi-list fs-2 text-white"></i>
			</button>

			<div class="collapse navbar-collapse" id="mairie-primary-nav">
				<nav class="mairie-nav me-auto" aria-label="<?php esc_attr_e( 'Navigation principale', 'mairie-civique' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'navbar-nav align-items-lg-center gap-1',
						'fallback_cb'    => 'mairie_civique_menu_fallback',
					)
				);
				?>
				</nav>

				<div class="mairie-header__cta-group d-flex align-items-center gap-2 ms-lg-3 mt-2 mt-lg-0">
					<?php if ( ! $is_logged_in ) : ?>
						<a class="btn btn-outline-success rounded-pill px-3" href="<?php echo esc_url( $signup_url ); ?>">
							<i class="bi bi-person-plus me-1"></i><?php esc_html_e( 'Inscription', 'mairie-civique' ); ?>
						</a>
					<?php endif; ?>
					<a class="btn btn-success rounded-pill px-4" href="<?php echo esc_url( $header_account_url ); ?>">
						<i class="bi bi-person-circle me-1"></i><?php echo esc_html( $header_account_label ); ?>
					</a>
				</div>
			</div>
		</div>
	</header>

	<main class="mairie-main">
		<div class="mairie-shell">