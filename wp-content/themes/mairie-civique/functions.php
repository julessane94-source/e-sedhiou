<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mairie_civique_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Navigation principale', 'mairie-civique' ),
			'footer'  => __( 'Navigation de pied de page', 'mairie-civique' ),
		)
	);
}
	add_action( 'after_setup_theme', 'mairie_civique_setup' );

function mairie_civique_get_request_capabilities() {
	return array(
		'edit_post'              => 'edit_mairie_demande',
		'read_post'              => 'read_mairie_demande',
		'delete_post'            => 'delete_mairie_demande',
		'edit_posts'             => 'edit_mairie_demandes',
		'edit_others_posts'      => 'edit_others_mairie_demandes',
		'publish_posts'          => 'publish_mairie_demandes',
		'read_private_posts'     => 'read_private_mairie_demandes',
		'delete_posts'           => 'delete_mairie_demandes',
		'delete_private_posts'   => 'delete_private_mairie_demandes',
		'delete_published_posts' => 'delete_published_mairie_demandes',
		'delete_others_posts'    => 'delete_others_mairie_demandes',
		'edit_private_posts'     => 'edit_private_mairie_demandes',
		'edit_published_posts'   => 'edit_published_mairie_demandes',
		'create_posts'           => 'create_mairie_demandes',
	);
}

function mairie_civique_get_agent_request_capabilities() {
	return array(
		'edit_mairie_demande',
		'read_mairie_demande',
		'edit_mairie_demandes',
		'edit_others_mairie_demandes',
		'edit_published_mairie_demandes',
	);
}

function mairie_civique_register_roles() {
	$agent_role = get_role( 'mairie_agent' );
	$citizen_role = get_role( 'mairie_citoyen' );

	if ( ! $agent_role ) {
		$agent_role = add_role(
			'mairie_agent',
			__( 'Agent municipal', 'mairie-civique' ),
			array(
				'read' => true,
			)
		);
	}

	if ( ! $citizen_role ) {
		add_role(
			'mairie_citoyen',
			__( 'Citoyen', 'mairie-civique' ),
			array(
				'read' => true,
			)
		);
	}

	$request_capabilities = array_values( mairie_civique_get_request_capabilities() );
	$request_capabilities = array_unique( $request_capabilities );
	$agent_capabilities   = mairie_civique_get_agent_request_capabilities();

	$administrator_role = get_role( 'administrator' );
	if ( $administrator_role instanceof WP_Role ) {
		foreach ( $request_capabilities as $capability ) {
			$administrator_role->add_cap( $capability );
		}
	}

	$agent_role = get_role( 'mairie_agent' );
	if ( $agent_role instanceof WP_Role ) {
		foreach ( $request_capabilities as $capability ) {
			if ( in_array( $capability, $agent_capabilities, true ) ) {
				$agent_role->add_cap( $capability );
				continue;
			}

			$agent_role->remove_cap( $capability );
		}
	}
}
	add_action( 'init', 'mairie_civique_register_roles', 5 );

function mairie_civique_enable_citizen_self_registration() {
	if ( (int) get_option( 'users_can_register' ) !== 1 ) {
		update_option( 'users_can_register', 1 );
	}

	if ( 'mairie_citoyen' !== get_option( 'default_role' ) ) {
		update_option( 'default_role', 'mairie_citoyen' );
	}
}
	add_action( 'init', 'mairie_civique_enable_citizen_self_registration', 6 );

function mairie_civique_get_laravel_settings_defaults() {
	return array(
		'site_name'        => '',
		'site_description' => '',
		'contact_email'    => '',
		'contact_phone'    => '',
		'contact_address'  => '',
		'allow_register'   => 1,
		'maintenance_mode' => 0,
	);
}

function mairie_civique_get_laravel_settings_path() {
	return wp_normalize_path( trailingslashit( ABSPATH ) . 'backend-laravel/storage/app/portal-settings.json' );
}

function mairie_civique_get_laravel_settings() {
	static $settings = null;

	if ( null !== $settings ) {
		return $settings;
	}

	$settings = mairie_civique_get_laravel_settings_defaults();
	$path     = mairie_civique_get_laravel_settings_path();

	if ( ! file_exists( $path ) || ! is_readable( $path ) ) {
		return $settings;
	}

	$raw = file_get_contents( $path );

	if ( false === $raw || '' === trim( $raw ) ) {
		return $settings;
	}

	$decoded = json_decode( $raw, true );

	if ( ! is_array( $decoded ) ) {
		return $settings;
	}

	$settings = array_replace( $settings, $decoded );

	return $settings;
}

function mairie_civique_get_laravel_setting( $key ) {
	$settings = mairie_civique_get_laravel_settings();

	return $settings[ $key ] ?? '';
}

function mairie_civique_filter_blogname_with_laravel( $value ) {
	$site_name = sanitize_text_field( mairie_civique_get_laravel_setting( 'site_name' ) );

	return '' !== $site_name ? $site_name : $value;
}
	add_filter( 'option_blogname', 'mairie_civique_filter_blogname_with_laravel' );

function mairie_civique_filter_blogdescription_with_laravel( $value ) {
	$site_description = sanitize_textarea_field( mairie_civique_get_laravel_setting( 'site_description' ) );

	return '' !== $site_description ? $site_description : $value;
}
	add_filter( 'option_blogdescription', 'mairie_civique_filter_blogdescription_with_laravel' );

function mairie_civique_register_request_post_type() {
	register_post_type(
		'mairie_demande',
		array(
			'labels' => array(
				'name'          => __( 'Demandes citoyennes', 'mairie-civique' ),
				'singular_name' => __( 'Demande citoyenne', 'mairie-civique' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-media-text',
			'supports'     => array( 'title', 'editor' ),
			'capability_type' => array( 'mairie_demande', 'mairie_demandes' ),
			'capabilities' => mairie_civique_get_request_capabilities(),
			'map_meta_cap' => true,
		)
	);
}
	add_action( 'init', 'mairie_civique_register_request_post_type' );

function mairie_civique_register_contact_message_post_type() {
	register_post_type(
		'mairie_contact_msg',
		array(
			'labels'       => array(
				'name'               => __( 'Messages de contact', 'mairie-civique' ),
				'singular_name'      => __( 'Message de contact', 'mairie-civique' ),
				'menu_name'          => __( 'Messages contact', 'mairie-civique' ),
				'add_new_item'       => __( 'Ajouter un message', 'mairie-civique' ),
				'edit_item'          => __( 'Voir le message', 'mairie-civique' ),
				'new_item'           => __( 'Nouveau message', 'mairie-civique' ),
				'view_item'          => __( 'Voir le message', 'mairie-civique' ),
				'all_items'          => __( 'Tous les messages', 'mairie-civique' ),
				'search_items'       => __( 'Rechercher des messages', 'mairie-civique' ),
				'not_found'          => __( 'Aucun message de contact.', 'mairie-civique' ),
				'not_found_in_trash' => __( 'Aucun message dans la corbeille.', 'mairie-civique' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-email-alt',
			'supports'     => array( 'title', 'editor' ),
			'rewrite'      => false,
		)
	);
}
	add_action( 'init', 'mairie_civique_register_contact_message_post_type', 11 );

function mairie_civique_contact_message_columns( $columns ) {
	return array(
		'cb'                    => $columns['cb'] ?? '<input type="checkbox" />',
		'title'                 => __( 'Objet', 'mairie-civique' ),
		'mairie_contact_sender' => __( 'Expediteur', 'mairie-civique' ),
		'mairie_contact_email'  => __( 'Email', 'mairie-civique' ),
		'mairie_contact_status' => __( 'Notification', 'mairie-civique' ),
		'date'                  => $columns['date'] ?? __( 'Date', 'mairie-civique' ),
	);
}
	add_filter( 'manage_mairie_contact_msg_posts_columns', 'mairie_civique_contact_message_columns' );

function mairie_civique_render_contact_message_column( $column, $post_id ) {
	if ( 'mairie_contact_sender' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_mairie_contact_name', true ) ?: '—' );
		return;
	}

	if ( 'mairie_contact_email' === $column ) {
		$email = sanitize_email( get_post_meta( $post_id, '_mairie_contact_email', true ) );

		if ( $email ) {
			echo '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>';
			return;
		}

		echo '—';
		return;
	}

	if ( 'mairie_contact_status' === $column ) {
		$admin_mail_sent = '1' === get_post_meta( $post_id, '_mairie_contact_admin_mail_sent', true );
		$status_label    = $admin_mail_sent
			? __( 'Email envoye', 'mairie-civique' )
			: __( 'Consulter dans WordPress', 'mairie-civique' );

		echo esc_html( $status_label );
	}
}
	add_action( 'manage_mairie_contact_msg_posts_custom_column', 'mairie_civique_render_contact_message_column', 10, 2 );

function mairie_civique_register_elu_post_type() {
	register_post_type(
		'mairie_elu',
		array(
			'labels'       => array(
				'name'          => __( 'Élus', 'mairie-civique' ),
				'singular_name' => __( 'Élu', 'mairie-civique' ),
				'add_new'       => __( 'Ajouter un élu', 'mairie-civique' ),
				'add_new_item'  => __( 'Nouvel élu', 'mairie-civique' ),
				'edit_item'     => __( 'Modifier l\'élu', 'mairie-civique' ),
				'all_items'     => __( 'Tous les élus', 'mairie-civique' ),
				'search_items'  => __( 'Rechercher des élus', 'mairie-civique' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-groups',
			'supports'     => array( 'title', 'thumbnail', 'excerpt' ),
			'show_in_rest' => true,
			'rewrite'      => false,
		)
	);
}
	add_action( 'init', 'mairie_civique_register_elu_post_type' );

function mairie_civique_add_elu_meta_box() {
	add_meta_box(
		'mairie_elu_details',
		__( 'Détails du mandat', 'mairie-civique' ),
		'mairie_civique_render_elu_meta_box',
		'mairie_elu',
		'side',
		'default'
	);
}
	add_action( 'add_meta_boxes', 'mairie_civique_add_elu_meta_box' );

function mairie_civique_render_elu_meta_box( $post ) {
	wp_nonce_field( 'mairie_elu_save_meta', 'mairie_elu_meta_nonce' );

	$role    = get_post_meta( $post->ID, '_mairie_elu_role', true );
	$periode = get_post_meta( $post->ID, '_mairie_elu_periode', true );
	$parti   = get_post_meta( $post->ID, '_mairie_elu_parti', true );
	?>
	<p>
		<label for="mairie_elu_role"><strong><?php esc_html_e( 'Rôle', 'mairie-civique' ); ?></strong></label><br>
		<select id="mairie_elu_role" name="mairie_elu_role" style="width:100%">
			<option value="maire" <?php selected( $role, 'maire' ); ?>><?php esc_html_e( 'Maire', 'mairie-civique' ); ?></option>
			<option value="adjoint" <?php selected( $role, 'adjoint' ); ?>><?php esc_html_e( '1er Adjoint / Adjoint', 'mairie-civique' ); ?></option>
			<option value="conseiller" <?php selected( $role, 'conseiller' ); ?>><?php esc_html_e( 'Conseiller municipal', 'mairie-civique' ); ?></option>
			<option value="ancien_maire" <?php selected( $role, 'ancien_maire' ); ?>><?php esc_html_e( 'Ancien Maire', 'mairie-civique' ); ?></option>
		</select>
	</p>
	<p>
		<label for="mairie_elu_periode"><strong><?php esc_html_e( 'Période de mandat', 'mairie-civique' ); ?></strong></label><br>
		<input type="text" id="mairie_elu_periode" name="mairie_elu_periode" value="<?php echo esc_attr( $periode ); ?>" placeholder="ex: 2020–2026" style="width:100%">
	</p>
	<p>
		<label for="mairie_elu_parti"><strong><?php esc_html_e( 'Affiliation', 'mairie-civique' ); ?></strong></label><br>
		<input type="text" id="mairie_elu_parti" name="mairie_elu_parti" value="<?php echo esc_attr( $parti ); ?>" placeholder="ex: Liste unitaire locale" style="width:100%">
	</p>
	<?php
}

function mairie_civique_save_elu_meta( $post_id ) {
	if ( ! isset( $_POST['mairie_elu_meta_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mairie_elu_meta_nonce'] ) ), 'mairie_elu_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$allowed_roles = array( 'maire', 'adjoint', 'conseiller', 'ancien_maire' );
	$role          = isset( $_POST['mairie_elu_role'] ) ? sanitize_text_field( wp_unslash( $_POST['mairie_elu_role'] ) ) : '';

	if ( in_array( $role, $allowed_roles, true ) ) {
		update_post_meta( $post_id, '_mairie_elu_role', $role );
	}

	$periode = isset( $_POST['mairie_elu_periode'] ) ? sanitize_text_field( wp_unslash( $_POST['mairie_elu_periode'] ) ) : '';
	update_post_meta( $post_id, '_mairie_elu_periode', $periode );

	$parti = isset( $_POST['mairie_elu_parti'] ) ? sanitize_text_field( wp_unslash( $_POST['mairie_elu_parti'] ) ) : '';
	update_post_meta( $post_id, '_mairie_elu_parti', $parti );
}
	add_action( 'save_post_mairie_elu', 'mairie_civique_save_elu_meta' );

function mairie_civique_enqueue_assets() {
	wp_enqueue_style(
		'bootstrap',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
		array(),
		'5.3.3'
	);

	wp_enqueue_style(
		'bootstrap-icons',
		'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
		array(),
		'1.11.3'
	);

	wp_enqueue_style(
		'mairie-civique-fonts',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;700;800&display=swap',
		array( 'bootstrap' ),
		null
	);

	wp_enqueue_style(
		'mairie-civique-style',
		get_stylesheet_uri(),
		array( 'mairie-civique-fonts', 'bootstrap', 'bootstrap-icons' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'bootstrap',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
		array(),
		'5.3.3',
		true
	);
}
	add_action( 'wp_enqueue_scripts', 'mairie_civique_enqueue_assets' );

function mairie_civique_get_practical_info_defaults() {
	return array(
		'address'          => '',
		'phone'            => '',
		'email'            => sanitize_email( get_option( 'admin_email' ) ),
		'hours_week'       => '',
		'hours_secondary'  => '',
		'emergency_notice' => '',
		'appointment'      => '',
		'map_url'          => '',
	);
}

function mairie_civique_get_practical_info_value( $key ) {
	$defaults = mairie_civique_get_practical_info_defaults();
	$laravel_practical_map = array(
		'address' => 'contact_address',
		'phone'   => 'contact_phone',
		'email'   => 'contact_email',
	);

	if ( isset( $laravel_practical_map[ $key ] ) ) {
		$laravel_value = mairie_civique_get_laravel_setting( $laravel_practical_map[ $key ] );

		if ( is_string( $laravel_value ) && '' !== trim( $laravel_value ) ) {
			$value = $laravel_value;

			if ( 'email' === $key ) {
				return sanitize_email( $value );
			}

			return sanitize_textarea_field( $value );
		}
	}

	$value    = get_theme_mod( 'mairie_practical_' . $key, $defaults[ $key ] ?? '' );

	if ( 'email' === $key ) {
		return sanitize_email( $value );
	}

	if ( 'map_url' === $key ) {
		return esc_url_raw( $value );
	}

	return sanitize_textarea_field( $value );
}

function mairie_civique_get_practical_info_cards() {
	$address          = mairie_civique_get_practical_info_value( 'address' );
	$phone            = mairie_civique_get_practical_info_value( 'phone' );
	$email            = mairie_civique_get_practical_info_value( 'email' );
	$hours_week       = mairie_civique_get_practical_info_value( 'hours_week' );
	$hours_secondary  = mairie_civique_get_practical_info_value( 'hours_secondary' );
	$emergency_notice = mairie_civique_get_practical_info_value( 'emergency_notice' );
	$appointment      = mairie_civique_get_practical_info_value( 'appointment' );
	$map_url          = mairie_civique_get_practical_info_value( 'map_url' );

	return array(
		array(
			'title'       => __( 'Coordonnees mairie', 'mairie-civique' ),
			'description' => __( 'Les informations essentielles pour joindre la mairie sans passer par plusieurs pages.', 'mairie-civique' ),
			'items'       => array(
				array(
					'label' => __( 'Adresse', 'mairie-civique' ),
					'value' => $address,
				),
				array(
					'label' => __( 'Telephone', 'mairie-civique' ),
					'value' => $phone,
					'url'   => 0 === strpos( preg_replace( '/\s+/', '', $phone ), '+' ) || preg_match( '/\d/', $phone ) ? 'tel:' . preg_replace( '/[^\d+]/', '', $phone ) : '',
				),
				array(
					'label' => __( 'Email', 'mairie-civique' ),
					'value' => $email,
					'url'   => $email ? 'mailto:' . $email : '',
				),
			),
			'action_url'   => $map_url,
			'action_label' => __( 'Ouvrir le plan d acces', 'mairie-civique' ),
		),
		array(
			'title'       => __( 'Horaires et accueil', 'mairie-civique' ),
			'description' => __( 'Les habitants trouvent tout de suite les heures d ouverture et les modalites de rendez-vous.', 'mairie-civique' ),
			'items'       => array(
				array(
					'label' => __( 'Accueil principal', 'mairie-civique' ),
					'value' => $hours_week,
				),
				array(
					'label' => __( 'Permanences', 'mairie-civique' ),
					'value' => $hours_secondary,
				),
				array(
					'label' => __( 'Rendez-vous', 'mairie-civique' ),
					'value' => $appointment,
					'url'   => mairie_civique_get_space_url( 'espace-citoyen' ) . '#demandes',
				),
			),
		),
		array(
			'title'       => __( 'Infos utiles et alertes', 'mairie-civique' ),
			'description' => __( 'Une zone courte pour les messages prioritaires et les demarches urgentes.', 'mairie-civique' ),
			'items'       => array(
				array(
					'label' => __( 'Information prioritaire', 'mairie-civique' ),
					'value' => $emergency_notice,
				),
				array(
					'label' => __( 'Demarches rapides', 'mairie-civique' ),
					'value' => __( 'Demandes d etat civil, certificats et depot de demande en ligne.', 'mairie-civique' ),
					'url'   => mairie_civique_get_space_url( 'espace-citoyen' ) . '#demandes',
				),
				array(
					'label' => __( 'Actualites', 'mairie-civique' ),
					'value' => __( 'Consultez les annonces municipales et informations de service.', 'mairie-civique' ),
					'url'   => get_permalink( (int) get_option( 'page_for_posts' ) ) ?: home_url( '/actualites/' ),
				),
			),
		),
	);
}

function mairie_civique_customize_register( $wp_customize ) {
	/* ═══════════════════════════════════════════════════════════════════════════════
	   SECTION 1 : COULEURS & DESIGN
	   ═══════════════════════════════════════════════════════════════════════════════ */
	$wp_customize->add_section(
		'mairie_civique_colors',
		array(
			'title'       => __( 'Couleurs & Design', 'mairie-civique' ),
			'description' => __( 'Personnalisez les couleurs, la typographie et le design global du site.', 'mairie-civique' ),
			'priority'    => 20,
		)
	);

	$color_settings = array(
		'primary_color' => array(
			'label'   => __( 'Couleur primaire (CTA, liens)', 'mairie-civique' ),
			'default' => '#047857',
		),
		'primary_dark_color' => array(
			'label'   => __( 'Couleur primaire foncée (hover)', 'mairie-civique' ),
			'default' => '#065f46',
		),
		'secondary_color' => array(
			'label'   => __( 'Couleur secondaire (accents)', 'mairie-civique' ),
			'default' => '#10b981',
		),
		'accent_color' => array(
			'label'   => __( 'Couleur accent (alertes)', 'mairie-civique' ),
			'default' => '#b20000',
		),
		'background_color' => array(
			'label'   => __( 'Couleur de fond primaire', 'mairie-civique' ),
			'default' => '#f0fdf4',
		),
		'text_color' => array(
			'label'   => __( 'Couleur du texte principal', 'mairie-civique' ),
			'default' => '#1c2434',
		),
		'text_muted_color' => array(
			'label'   => __( 'Couleur du texte secondaire', 'mairie-civique' ),
			'default' => '#5b6475',
		),
	);

	foreach ( $color_settings as $color_key => $color_config ) {
		$wp_customize->add_setting(
			'mairie_color_' . $color_key,
			array(
				'default'           => $color_config['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'mairie_color_' . $color_key,
				array(
					'label'   => $color_config['label'],
					'section' => 'mairie_civique_colors',
				)
			)
		);
	}

	/* ═══════════════════════════════════════════════════════════════════════════════
	   SECTION 2 : TYPOGRAPHIE
	   ═══════════════════════════════════════════════════════════════════════════════ */
	$wp_customize->add_section(
		'mairie_civique_typography',
		array(
			'title'    => __( 'Typographie', 'mairie-civique' ),
			'priority' => 21,
		)
	);

	$typography_settings = array(
		'heading_font_size' => array(
			'label'       => __( 'Taille des titres (H1)', 'mairie-civique' ),
			'description' => __( 'Utiliser la fonction clamp() : clamp(min, calc, max)', 'mairie-civique' ),
			'default'     => 'clamp(2.15rem, 4.6vw, 3.55rem)',
		),
		'body_font_family' => array(
			'label'       => __( 'Police du corps de texte', 'mairie-civique' ),
			'description' => __( 'Ex: Manrope, Segoe UI, etc.', 'mairie-civique' ),
			'default'     => '"Manrope", "Segoe UI", "Helvetica Neue", Arial, sans-serif',
		),
	);

	foreach ( $typography_settings as $typo_key => $typo_config ) {
		$wp_customize->add_setting(
			'mairie_typo_' . $typo_key,
			array(
				'default'           => $typo_config['default'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'mairie_typo_' . $typo_key,
			array(
				'label'       => $typo_config['label'],
				'description' => $typo_config['description'] ?? '',
				'section'     => 'mairie_civique_typography',
				'type'        => 'text',
			)
		);
	}

	/* ═══════════════════════════════════════════════════════════════════════════════
	   SECTION 3 : BOUTONS & CTA
	   ═══════════════════════════════════════════════════════════════════════════════ */
	$wp_customize->add_section(
		'mairie_civique_buttons',
		array(
			'title'       => __( 'Boutons & Appels à l\'action', 'mairie-civique' ),
			'description' => __( 'Personnalisez l\'apparence des boutons et des CTA.', 'mairie-civique' ),
			'priority'    => 22,
		)
	);

	$button_settings = array(
		'button_padding' => array(
			'label'       => __( 'Padding des boutons (px)', 'mairie-civique' ),
			'description' => __( 'Ex: 12px 24px', 'mairie-civique' ),
			'default'     => '12px 24px',
		),
		'button_border_radius' => array(
			'label'       => __( 'Rayon des coins des boutons (px)', 'mairie-civique' ),
			'description' => __( 'Ex: 999px (boutons arrondis)', 'mairie-civique' ),
			'default'     => '999px',
		),
		'button_font_weight' => array(
			'label'       => __( 'Poids de la police des boutons', 'mairie-civique' ),
			'description' => __( 'Ex: 600 ou 700', 'mairie-civique' ),
			'default'     => '600',
		),
	);

	foreach ( $button_settings as $btn_key => $btn_config ) {
		$wp_customize->add_setting(
			'mairie_btn_' . $btn_key,
			array(
				'default'           => $btn_config['default'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'mairie_btn_' . $btn_key,
			array(
				'label'       => $btn_config['label'],
				'description' => $btn_config['description'] ?? '',
				'section'     => 'mairie_civique_buttons',
				'type'        => 'text',
			)
		);
	}

	/* ═══════════════════════════════════════════════════════════════════════════════
	   SECTION 4 : MENUS & NAVIGATION
	   ═══════════════════════════════════════════════════════════════════════════════ */
	$wp_customize->add_section(
		'mairie_civique_navigation',
		array(
			'title'       => __( 'Menus & Navigation', 'mairie-civique' ),
			'description' => __( 'Configurez les menus et la navigation du site.', 'mairie-civique' ),
			'priority'    => 23,
		)
	);

	$wp_customize->add_setting(
		'mairie_nav_sticky_header',
		array(
			'default'           => '1',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'mairie_nav_sticky_header',
		array(
			'label'   => __( 'En-tête fixe au scroll', 'mairie-civique' ),
			'section' => 'mairie_civique_navigation',
			'type'    => 'checkbox',
		)
	);

	/* ═══════════════════════════════════════════════════════════════════════════════
	   SECTION 5 : INFORMATIONS PRATIQUES (EXISTANT)
	   ═══════════════════════════════════════════════════════════════════════════════ */
	$wp_customize->add_section(
		'mairie_civique_practical_info',
		array(
			'title'       => __( 'Informations pratiques mairie', 'mairie-civique' ),
			'description' => __( 'Coordonnees, horaires et messages utiles affiches sur la page d accueil.', 'mairie-civique' ),
			'priority'    => 35,
		)
	);

	$fields = array(
		'address' => array(
			'label'             => __( 'Adresse de la mairie', 'mairie-civique' ),
			'type'              => 'textarea',
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'phone' => array(
			'label'             => __( 'Telephone principal', 'mairie-civique' ),
			'type'              => 'text',
			'sanitize_callback' => 'sanitize_text_field',
		),
		'email' => array(
			'label'             => __( 'Email d accueil', 'mairie-civique' ),
			'type'              => 'email',
			'sanitize_callback' => 'sanitize_email',
		),
		'hours_week' => array(
			'label'             => __( 'Horaires accueil principal', 'mairie-civique' ),
			'type'              => 'textarea',
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'hours_secondary' => array(
			'label'             => __( 'Permanences et rendez-vous', 'mairie-civique' ),
			'type'              => 'textarea',
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'emergency_notice' => array(
			'label'             => __( 'Message prioritaire / alerte', 'mairie-civique' ),
			'type'              => 'textarea',
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'appointment' => array(
			'label'             => __( 'Informations de rendez-vous', 'mairie-civique' ),
			'type'              => 'textarea',
			'sanitize_callback' => 'sanitize_textarea_field',
		),
		'map_url' => array(
			'label'             => __( 'Lien plan d acces', 'mairie-civique' ),
			'type'              => 'url',
			'sanitize_callback' => 'esc_url_raw',
		),
	);

	$defaults = mairie_civique_get_practical_info_defaults();

	foreach ( $fields as $field_key => $field_config ) {
		$wp_customize->add_setting(
			'mairie_practical_' . $field_key,
			array(
				'default'           => $defaults[ $field_key ] ?? '',
				'sanitize_callback' => $field_config['sanitize_callback'],
			)
		);

		$wp_customize->add_control(
			'mairie_practical_' . $field_key,
			array(
				'label'   => $field_config['label'],
				'section' => 'mairie_civique_practical_info',
				'type'    => $field_config['type'],
			)
		);
	}
}
add_action( 'customize_register', 'mairie_civique_customize_register' );

/**
 * Injecte les couleurs personnalisées dans le CSS
 */
function mairie_civique_customize_css_output() {
	$primary = get_theme_mod( 'mairie_color_primary_color', '#047857' );
	$primary_dark = get_theme_mod( 'mairie_color_primary_dark_color', '#065f46' );
	$secondary = get_theme_mod( 'mairie_color_secondary_color', '#10b981' );
	$accent = get_theme_mod( 'mairie_color_accent_color', '#b20000' );
	$bg = get_theme_mod( 'mairie_color_background_color', '#f0fdf4' );
	$text = get_theme_mod( 'mairie_color_text_color', '#1c2434' );
	$text_muted = get_theme_mod( 'mairie_color_text_muted_color', '#5b6475' );
	$heading_size = get_theme_mod( 'mairie_typo_heading_font_size', 'clamp(2.15rem, 4.6vw, 3.55rem)' );
	$btn_padding = get_theme_mod( 'mairie_btn_button_padding', '12px 24px' );
	$btn_radius = get_theme_mod( 'mairie_btn_button_border_radius', '999px' );
	$btn_weight = get_theme_mod( 'mairie_btn_button_font_weight', '600' );

	$css = ':root {
		--mairie-primary: ' . esc_attr( $primary ) . ';
		--mairie-primary-strong: ' . esc_attr( $primary_dark ) . ';
		--mairie-secondary: ' . esc_attr( $secondary ) . ';
		--mairie-accent: ' . esc_attr( $accent ) . ';
		--mairie-bg: ' . esc_attr( $bg ) . ';
		--mairie-ink: ' . esc_attr( $text ) . ';
		--mairie-muted: ' . esc_attr( $text_muted ) . ';
		--mairie-h1-size: ' . esc_attr( $heading_size ) . ';
		--mairie-btn-padding: ' . esc_attr( $btn_padding ) . ';
		--mairie-btn-radius: ' . esc_attr( $btn_radius ) . ';
		--mairie-btn-weight: ' . esc_attr( $btn_weight ) . ' !important;
	}

	:root {
		--bs-primary: ' . esc_attr( $primary ) . ';
		--bs-primary-rgb: ' . mairie_civique_hex_to_rgb( $primary ) . ';
		--bs-success: ' . esc_attr( $primary ) . ';
		--bs-success-rgb: ' . mairie_civique_hex_to_rgb( $primary ) . ';
		--bs-link-color: ' . esc_attr( $primary ) . ';
		--bs-link-color-rgb: ' . mairie_civique_hex_to_rgb( $primary ) . ';
		--bs-link-hover-color: ' . esc_attr( $primary_dark ) . ';
	}

	.mairie-button {
		padding: ' . esc_attr( $btn_padding ) . ' !important;
		border-radius: ' . esc_attr( $btn_radius ) . ' !important;
		font-weight: ' . esc_attr( $btn_weight ) . ' !important;
	}';

	wp_register_style( 'mairie-civique-custom', false );
	wp_enqueue_style( 'mairie-civique-custom' );
	wp_add_inline_style( 'mairie-civique-custom', $css );
}
add_action( 'wp_enqueue_scripts', 'mairie_civique_customize_css_output' );
add_action( 'admin_enqueue_scripts', 'mairie_civique_customize_css_output' );

/**
 * Convertir un code hex en RGB
 */
function mairie_civique_hex_to_rgb( $color ) {
	$color = str_replace( '#', '', $color );

	if ( 6 !== strlen( $color ) ) {
		return '0, 0, 0';
	}

	$rgb = sscanf( $color, '%02x%02x%02x' );
	return ( $rgb[0] ?? 0 ) . ', ' . ( $rgb[1] ?? 0 ) . ', ' . ( $rgb[2] ?? 0 );
}

function mairie_civique_get_login_page_url( $redirect_to = '' ) {
	if ( mairie_civique_is_laravel_portal_enabled() ) {
		$routes = mairie_civique_get_laravel_portal_routes();
		$url    = $routes['connexion'];

		if ( $redirect_to ) {
			$url = add_query_arg( 'redirect_to', $redirect_to, $url );
		}

		return $url;
	}

	$page = get_page_by_path( 'connexion' );
	$url  = $page instanceof WP_Post ? get_permalink( $page ) : site_url( 'wp-login.php', 'login' );

	if ( $redirect_to ) {
		$url = add_query_arg( 'redirect_to', $redirect_to, $url );
	}

	return $url;
}

function mairie_civique_filter_login_url( $login_url, $redirect, $force_reauth ) {
	$admin_login_url = site_url( 'wp-login.php', 'login' );

	if ( is_string( $redirect ) && false !== strpos( $redirect, '/wp-admin' ) ) {
		if ( $redirect ) {
			$admin_login_url = add_query_arg( 'redirect_to', $redirect, $admin_login_url );
		}

		if ( $force_reauth ) {
			$admin_login_url = add_query_arg( 'reauth', '1', $admin_login_url );
		}

		return $admin_login_url;
	}

	$custom_login_url = mairie_civique_get_login_page_url( $redirect );

	if ( $force_reauth ) {
		$custom_login_url = add_query_arg( 'reauth', '1', $custom_login_url );
	}

	return $custom_login_url;
}
	add_filter( 'login_url', 'mairie_civique_filter_login_url', 10, 3 );

function mairie_civique_get_default_login_redirect( $user = null ) {
	if ( $user instanceof WP_User ) {
		if ( user_can( $user, 'manage_options' ) ) {
			return mairie_civique_get_space_url( 'espace-admin' );
		}

		if ( user_can( $user, 'edit_mairie_demandes' ) ) {
			return mairie_civique_get_space_url( 'espace-agent' );
		}

		return mairie_civique_get_space_url( 'espace-citoyen' );
	}

	if ( current_user_can( 'manage_options' ) ) {
		return mairie_civique_get_space_url( 'espace-admin' );
	}

	if ( current_user_can( 'edit_mairie_demandes' ) ) {
		return mairie_civique_get_space_url( 'espace-agent' );
	}

	return mairie_civique_get_space_url( 'espace-citoyen' );
}

function mairie_civique_handle_frontend_login() {
	if ( ! isset( $_POST['action'] ) || 'mairie_civique_login' !== $_POST['action'] ) {
		return;
	}

	if ( ! isset( $_POST['mairie_civique_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mairie_civique_login_nonce'] ) ), 'mairie_civique_login' ) ) {
		wp_die( esc_html__( 'La verification de securite a echoue.', 'mairie-civique' ) );
	}

	$redirect_to = isset( $_POST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), '' ) : '';
	$auth_origin = isset( $_POST['auth_origin'] ) ? wp_validate_redirect( wp_unslash( $_POST['auth_origin'] ), '' ) : '';
	$login_page  = mairie_civique_get_login_page_url( $redirect_to );
	$feedback_url = $auth_origin ? $auth_origin : $login_page;

	if ( is_user_logged_in() ) {
		wp_safe_redirect( $redirect_to ? $redirect_to : mairie_civique_get_default_login_redirect( wp_get_current_user() ) );
		exit;
	}

	$login_input = isset( $_POST['log'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['log'] ) ) ) : '';
	$password = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '';
	$remember = ! empty( $_POST['rememberme'] );

	if ( '' === $login_input || '' === $password ) {
		wp_safe_redirect( add_query_arg( 'mairie_login_state', 'missing', $feedback_url ) );
		exit;
	}

	$username = $login_input;
	if ( is_email( $login_input ) ) {
		$user_from_email = get_user_by( 'email', $login_input );
		if ( $user_from_email instanceof WP_User ) {
			$username = $user_from_email->user_login;
		}
	}

	$user = wp_signon(
		array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => $remember,
		),
		is_ssl()
	);

	if ( is_wp_error( $user ) ) {
		// Si l'utilisateur n'existe pas encore dans WordPress mais existe dans Laravel,
		// on le provisionne puis on reconnecte côté WordPress.
		if ( is_email( $login_input ) ) {
			$laravel_login = mairie_laravel_login_with_profile( $login_input, $password );

			if ( ! is_wp_error( $laravel_login ) ) {
				$laravel_user  = $laravel_login['user'];
				$laravel_email = isset( $laravel_user['email'] ) ? sanitize_email( (string) $laravel_user['email'] ) : '';
				$laravel_role  = isset( $laravel_user['role'] ) ? sanitize_key( (string) $laravel_user['role'] ) : 'citoyen';
				$first_name    = isset( $laravel_user['first_name'] ) ? sanitize_text_field( (string) $laravel_user['first_name'] ) : '';
				$last_name     = isset( $laravel_user['last_name'] ) ? sanitize_text_field( (string) $laravel_user['last_name'] ) : '';
				$display_name  = isset( $laravel_user['name'] ) ? sanitize_text_field( (string) $laravel_user['name'] ) : trim( $first_name . ' ' . $last_name );

				if ( $laravel_email ) {
					$wp_user = get_user_by( 'email', $laravel_email );

					if ( ! ( $wp_user instanceof WP_User ) ) {
						$wp_username = mairie_civique_generate_unique_username_from_email( $laravel_email, 'utilisateur' );
						$wp_user_id  = wp_create_user( $wp_username, $password, $laravel_email );
						if ( ! is_wp_error( $wp_user_id ) ) {
							$wp_user = get_user_by( 'id', $wp_user_id );
						}
					}

					if ( $wp_user instanceof WP_User ) {
						mairie_civique_sync_wp_user_with_laravel_profile( $wp_user->ID, $laravel_user );
						wp_set_password( $password, $wp_user->ID );

						$user = wp_signon(
							array(
								'user_login'    => $wp_user->user_login,
								'user_password' => $password,
								'remember'      => $remember,
							),
							is_ssl()
						);

						if ( ! is_wp_error( $user ) ) {
							mairie_laravel_save_user_jwt( $user->ID, (string) $laravel_login['token'] );
							wp_safe_redirect( $redirect_to ? $redirect_to : mairie_civique_get_default_login_redirect( $user ) );
							exit;
						}
					}
				}
			}
		}

		wp_safe_redirect( add_query_arg( 'mairie_login_state', 'failed', $feedback_url ) );
		exit;
	}

	// Synchronisation JWT Laravel : on obtient et sauvegarde le token immédiatement.
	$_laravel_jwt = mairie_laravel_get_jwt_token( $user->user_email, $password );
	if ( ! is_wp_error( $_laravel_jwt ) ) {
		mairie_laravel_save_user_jwt( $user->ID, $_laravel_jwt );
		$_laravel_profile = mairie_laravel_get_my_profile( $_laravel_jwt );
		if ( is_array( $_laravel_profile ) ) {
			mairie_civique_sync_wp_user_with_laravel_profile( $user->ID, $_laravel_profile );
		}
	}

	wp_safe_redirect( $redirect_to ? $redirect_to : mairie_civique_get_default_login_redirect( $user ) );
	exit;
}
	add_action( 'admin_post_nopriv_mairie_civique_login', 'mairie_civique_handle_frontend_login' );
	add_action( 'admin_post_mairie_civique_login', 'mairie_civique_handle_frontend_login' );

function mairie_civique_generate_unique_username_from_email( $email, $fallback = 'utilisateur' ) {
	$local_part = sanitize_user( strstr( $email, '@', true ) ?: '', true );

	if ( '' === $local_part ) {
		$local_part = sanitize_user( $fallback, true );
	}

	if ( '' === $local_part ) {
		$local_part = 'utilisateur';
	}

	$username = $local_part;
	$suffix   = 1;

	while ( username_exists( $username ) ) {
		$username = $local_part . $suffix;
		$suffix++;
	}

	return $username;
}

function mairie_civique_handle_citizen_registration() {
	if ( ! isset( $_POST['action'] ) || 'mairie_civique_register' !== $_POST['action'] ) {
		return;
	}

	if ( ! isset( $_POST['mairie_civique_register_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mairie_civique_register_nonce'] ) ), 'mairie_civique_register' ) ) {
		wp_die( esc_html__( 'La verification de securite a echoue.', 'mairie-civique' ) );
	}

	$redirect_to = isset( $_POST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), '' ) : '';
	$auth_origin = isset( $_POST['auth_origin'] ) ? wp_validate_redirect( wp_unslash( $_POST['auth_origin'] ), '' ) : '';
	$login_page  = mairie_civique_get_login_page_url( $redirect_to );
	$feedback_url = $auth_origin ? $auth_origin : $login_page;

	if ( is_user_logged_in() ) {
		wp_safe_redirect( $redirect_to ? $redirect_to : mairie_civique_get_default_login_redirect() );
		exit;
	}

	$email            = isset( $_POST['reg_email'] ) ? sanitize_email( wp_unslash( $_POST['reg_email'] ) ) : '';
	$password         = isset( $_POST['reg_password'] ) ? (string) wp_unslash( $_POST['reg_password'] ) : '';
	$password_confirm = isset( $_POST['reg_password_confirm'] ) ? (string) wp_unslash( $_POST['reg_password_confirm'] ) : '';
	$first_name       = isset( $_POST['reg_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['reg_first_name'] ) ) : '';
	$last_name        = isset( $_POST['reg_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['reg_last_name'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'mairie_login_state', 'register_invalid_email', $feedback_url ) );
		exit;
	}

	if ( email_exists( $email ) ) {
		wp_safe_redirect( add_query_arg( 'mairie_login_state', 'register_exists', $feedback_url ) );
		exit;
	}

	if ( strlen( $password ) < 8 ) {
		wp_safe_redirect( add_query_arg( 'mairie_login_state', 'register_weak_password', $feedback_url ) );
		exit;
	}

	if ( $password !== $password_confirm ) {
		wp_safe_redirect( add_query_arg( 'mairie_login_state', 'register_password_mismatch', $feedback_url ) );
		exit;
	}

	$username = mairie_civique_generate_unique_username_from_email( $email, 'citoyen' );
	$user_id  = wp_create_user( $username, $password, $email );

	if ( is_wp_error( $user_id ) ) {
		wp_safe_redirect( add_query_arg( 'mairie_login_state', 'register_failed', $feedback_url ) );
		exit;
	}

	wp_update_user(
		array(
			'ID'         => $user_id,
			'first_name' => $first_name,
			'last_name'  => $last_name,
		)
	);

	$user = get_user_by( 'id', $user_id );
	if ( $user instanceof WP_User ) {
		$user->set_role( get_role( 'mairie_citoyen' ) ? 'mairie_citoyen' : 'subscriber' );
	}

	$auth_user = wp_signon(
		array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => true,
		),
		is_ssl()
	);

	if ( is_wp_error( $auth_user ) ) {
		wp_safe_redirect( add_query_arg( 'mairie_login_state', 'register_created_login_failed', $feedback_url ) );
		exit;
	}

	// Créer le compte citoyen dans Laravel et sauvegarder le JWT.
	$_laravel_jwt = mairie_laravel_register_citizen( $email, $password, $first_name, $last_name );
	if ( ! is_wp_error( $_laravel_jwt ) ) {
		mairie_laravel_save_user_jwt( $auth_user->ID, $_laravel_jwt );
		$_laravel_profile = mairie_laravel_get_my_profile( $_laravel_jwt );
		if ( is_array( $_laravel_profile ) ) {
			mairie_civique_sync_wp_user_with_laravel_profile( $auth_user->ID, $_laravel_profile );
		}
	}

	$citizen_url = mairie_civique_get_space_url( 'espace-citoyen' ) . '#demandes';
	$citizen_url = add_query_arg( 'mairie_login_state', 'register_success', $citizen_url );

	wp_safe_redirect( $redirect_to ? $redirect_to : $citizen_url );
	exit;
}
	add_action( 'admin_post_nopriv_mairie_civique_register', 'mairie_civique_handle_citizen_registration' );
	add_action( 'admin_post_mairie_civique_register', 'mairie_civique_handle_citizen_registration' );

function mairie_civique_get_create_agent_admin_url( $args = array() ) {
	$url = admin_url( 'users.php?page=mairie-civique-create-agent' );

	if ( ! empty( $args ) ) {
		$url = add_query_arg( $args, $url );
	}

	return $url;
}

function mairie_civique_register_agent_admin_page() {
	add_users_page(
		__( 'Creer un agent', 'mairie-civique' ),
		__( 'Creer un agent', 'mairie-civique' ),
		'manage_options',
		'mairie-civique-create-agent',
		'mairie_civique_render_agent_admin_page'
	);
}
	add_action( 'admin_menu', 'mairie_civique_register_agent_admin_page' );

function mairie_civique_handle_agent_admin_creation() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Vous n avez pas les droits pour creer un agent.', 'mairie-civique' ) );
	}

	if ( ! isset( $_POST['action'] ) || 'mairie_civique_create_agent' !== $_POST['action'] ) {
		return;
	}

	if ( ! isset( $_POST['mairie_civique_create_agent_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mairie_civique_create_agent_nonce'] ) ), 'mairie_civique_create_agent' ) ) {
		wp_die( esc_html__( 'La verification de securite a echoue.', 'mairie-civique' ) );
	}

	$email            = isset( $_POST['agent_email'] ) ? sanitize_email( wp_unslash( $_POST['agent_email'] ) ) : '';
	$password         = isset( $_POST['agent_password'] ) ? (string) wp_unslash( $_POST['agent_password'] ) : '';
	$password_confirm = isset( $_POST['agent_password_confirm'] ) ? (string) wp_unslash( $_POST['agent_password_confirm'] ) : '';
	$first_name       = isset( $_POST['agent_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['agent_first_name'] ) ) : '';
	$last_name        = isset( $_POST['agent_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['agent_last_name'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_safe_redirect( mairie_civique_get_create_agent_admin_url( array( 'state' => 'invalid_email' ) ) );
		exit;
	}

	if ( email_exists( $email ) ) {
		wp_safe_redirect( mairie_civique_get_create_agent_admin_url( array( 'state' => 'email_exists' ) ) );
		exit;
	}

	if ( strlen( $password ) < 8 ) {
		wp_safe_redirect( mairie_civique_get_create_agent_admin_url( array( 'state' => 'weak_password' ) ) );
		exit;
	}

	if ( $password !== $password_confirm ) {
		wp_safe_redirect( mairie_civique_get_create_agent_admin_url( array( 'state' => 'password_mismatch' ) ) );
		exit;
	}

	$username = mairie_civique_generate_unique_username_from_email( $email, 'agent' );
	$user_id  = wp_create_user( $username, $password, $email );

	if ( is_wp_error( $user_id ) ) {
		wp_safe_redirect( mairie_civique_get_create_agent_admin_url( array( 'state' => 'creation_failed' ) ) );
		exit;
	}

	wp_update_user(
		array(
			'ID'         => $user_id,
			'first_name' => $first_name,
			'last_name'  => $last_name,
		)
	);

	$user = get_user_by( 'id', $user_id );
	if ( $user instanceof WP_User ) {
		$user->set_role( 'mairie_agent' );
	}

	wp_safe_redirect(
		mairie_civique_get_create_agent_admin_url(
			array(
				'state'    => 'created',
				'username' => rawurlencode( $username ),
			)
		)
	);
	 exit;
}
	add_action( 'admin_post_mairie_civique_create_agent', 'mairie_civique_handle_agent_admin_creation' );

function mairie_civique_render_agent_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Vous n avez pas les droits pour acceder a cette page.', 'mairie-civique' ) );
	}

	$state    = isset( $_GET['state'] ) ? sanitize_key( wp_unslash( $_GET['state'] ) ) : '';
	$username = isset( $_GET['username'] ) ? sanitize_user( wp_unslash( $_GET['username'] ) ) : '';
	$messages = array(
		'created'           => __( 'Le compte agent a ete cree avec succes.', 'mairie-civique' ),
		'invalid_email'     => __( 'Veuillez saisir une adresse email valide.', 'mairie-civique' ),
		'email_exists'      => __( 'Cette adresse email est deja utilisee.', 'mairie-civique' ),
		'weak_password'     => __( 'Le mot de passe doit contenir au moins 8 caracteres.', 'mairie-civique' ),
		'password_mismatch' => __( 'Les mots de passe ne correspondent pas.', 'mairie-civique' ),
		'creation_failed'   => __( 'La creation du compte agent a echoue.', 'mairie-civique' ),
	);
	?>
	<div class="wrap mairie-agent-admin-page">
		<h1><?php esc_html_e( 'Creer un agent', 'mairie-civique' ); ?></h1>
		<p><?php esc_html_e( 'Utilisez ce formulaire pour creer un compte agent. Le role agent est attribue automatiquement et reste reserve a l administration.', 'mairie-civique' ); ?></p>

		<?php if ( isset( $messages[ $state ] ) ) : ?>
			<div class="notice <?php echo 'created' === $state ? 'notice-success' : 'notice-error'; ?> is-dismissible">
				<p>
					<?php echo esc_html( $messages[ $state ] ); ?>
					<?php if ( 'created' === $state && $username ) : ?>
						<?php echo ' ' . esc_html( sprintf( __( 'Identifiant genere: %s', 'mairie-civique' ), $username ) ); ?>
					<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>

		<div class="mairie-agent-admin-card">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="mairie_civique_create_agent">
				<?php wp_nonce_field( 'mairie_civique_create_agent', 'mairie_civique_create_agent_nonce' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><label for="agent_email"><?php esc_html_e( 'Email', 'mairie-civique' ); ?></label></th>
							<td><input name="agent_email" type="email" id="agent_email" class="regular-text" required></td>
						</tr>
						<tr>
							<th scope="row"><label for="agent_first_name"><?php esc_html_e( 'Prenom', 'mairie-civique' ); ?></label></th>
							<td><input name="agent_first_name" type="text" id="agent_first_name" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="agent_last_name"><?php esc_html_e( 'Nom', 'mairie-civique' ); ?></label></th>
							<td><input name="agent_last_name" type="text" id="agent_last_name" class="regular-text"></td>
						</tr>
						<tr>
							<th scope="row"><label for="agent_password"><?php esc_html_e( 'Mot de passe', 'mairie-civique' ); ?></label></th>
							<td><input name="agent_password" type="password" id="agent_password" class="regular-text" minlength="8" required></td>
						</tr>
						<tr>
							<th scope="row"><label for="agent_password_confirm"><?php esc_html_e( 'Confirmer le mot de passe', 'mairie-civique' ); ?></label></th>
							<td><input name="agent_password_confirm" type="password" id="agent_password_confirm" class="regular-text" minlength="8" required></td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Creer le compte agent', 'mairie-civique' ) ); ?>
			</form>
		</div>
	</div>
	<?php
}

function mairie_civique_create_core_pages() {
	$pages = array(
		array(
			'title'    => 'Espace Admin',
			'slug'     => 'espace-admin',
			'template' => 'template-espace-admin.php',
			'content'  => '',
		),
		array(
			'title'    => 'Espace Agent',
			'slug'     => 'espace-agent',
			'template' => 'template-espace-agent.php',
			'content'  => '',
		),
		array(
			'title'    => 'Espace Citoyen',
			'slug'     => 'espace-citoyen',
			'template' => 'template-espace-citoyen.php',
			'content'  => '',
		),
		array(
			'title'    => 'Connexion',
			'slug'     => 'connexion',
			'template' => 'template-connexion.php',
			'content'  => '',
		),
		array(
			'title'    => 'Anciens Maires',
			'slug'     => 'anciens-maires',
			'template' => 'template-anciens-maires.php',
			'content'  => '',
		),
		array(
			'title'    => 'Historique de la ville',
			'slug'     => 'historique',
			'template' => 'template-historique.php',
			'content'  => '',
		),
		array(
			'title'    => 'Équipe municipale',
			'slug'     => 'equipe-municipale',
			'template' => 'template-equipe-municipale.php',
			'content'  => '',
		),
		array(
			'title'    => 'Services municipaux',
			'slug'     => 'services',
			'template' => 'template-services.php',
			'content'  => '<p>La mairie met à votre disposition de nombreux services pour simplifier vos démarches quotidiennes.</p>',
		),
		array(
			'title'    => 'Contact',
			'slug'     => 'contact',
			'template' => 'template-contact.php',
			'content'  => '<p>Contactez les services de la mairie par téléphone, par email ou en vous rendant directement à l\'accueil.</p>',
		),
		array(
			'title'    => 'Mes Demandes',
			'slug'     => 'mes-demandes',
			'template' => 'template-mes-demandes.php',
			'content'  => '',
		),
	);

	foreach ( $pages as $page_definition ) {
		$existing_page = get_page_by_path( $page_definition['slug'] );

		if ( $existing_page instanceof WP_Post ) {
			update_post_meta( $existing_page->ID, '_wp_page_template', $page_definition['template'] );
			continue;
		}

		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => $page_definition['title'],
				'post_name'    => $page_definition['slug'],
				'post_status'  => 'publish',
				'post_content' => $page_definition['content'],
			),
			true
		);

		if ( ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page_definition['template'] );
		}
	}
}
	add_action( 'after_switch_theme', 'mairie_civique_create_core_pages' );

function mairie_civique_sync_core_pages() {
	$core_pages_version = (int) get_option( 'mairie_civique_core_pages_version', 0 );

	if ( $core_pages_version >= 4 ) {
		return;
	}

	mairie_civique_create_core_pages();
	update_option( 'mairie_civique_core_pages_version', 4 );
}
	add_action( 'init', 'mairie_civique_sync_core_pages', 20 );

function mairie_civique_get_protected_space_rules() {
	return array(
		'admin' => array(
			'template' => 'template-espace-admin.php',
			'capability' => 'manage_options',
		),
		'agent' => array(
			'template' => 'template-espace-agent.php',
			'capability' => 'edit_mairie_demandes',
		),
		'citoyen' => array(
			'template' => 'template-espace-citoyen.php',
			'callback' => 'mairie_civique_user_is_citizen',
		),
	);
}

function mairie_civique_user_can_access_space( $space ) {
	$rules = mairie_civique_get_protected_space_rules();

	if ( ! isset( $rules[ $space ] ) ) {
		return true;
	}

	if ( isset( $rules[ $space ]['callback'] ) && is_callable( $rules[ $space ]['callback'] ) ) {
		return (bool) call_user_func( $rules[ $space ]['callback'] );
	}

	return current_user_can( $rules[ $space ]['capability'] );
}

function mairie_civique_get_current_space_slug() {
	if ( current_user_can( 'manage_options' ) ) {
		return 'espace-admin';
	}

	if ( mairie_civique_is_agent_user() ) {
		return 'espace-agent';
	}

	if ( mairie_civique_user_is_citizen() ) {
		return 'espace-citoyen';
	}

	return 'connexion';
}

function mairie_civique_get_space_guard_context( $space ) {
	$space_slug = 'admin' === $space ? 'espace-admin' : ( 'agent' === $space ? 'espace-agent' : 'espace-citoyen' );
	$allowed    = mairie_civique_user_can_access_space( $space );
	$logged_in  = is_user_logged_in();
	$fallback_slug = $logged_in ? mairie_civique_get_current_space_slug() : 'connexion';
	$fallback_label = $logged_in ? __( 'Aller vers mon espace', 'mairie-civique' ) : __( 'Aller vers la connexion', 'mairie-civique' );

	return array(
		'allowed'         => $allowed,
		'is_logged_in'    => $logged_in,
		'login_url'       => mairie_civique_get_login_page_url( mairie_civique_get_space_url( $space_slug ) ),
		'fallback_url'    => mairie_civique_get_space_url( $fallback_slug ),
		'fallback_label'  => $fallback_label,
		'message_title'   => $logged_in
			? __( 'Acces restreint', 'mairie-civique' )
			: __( 'Connexion requise', 'mairie-civique' ),
		'message_body'    => $logged_in
			? __( 'Acces reserve a votre profil.', 'mairie-civique' )
			: __( 'Connectez-vous pour continuer.', 'mairie-civique' ),
	);
}

function mairie_civique_filter_menu_items_by_access( $items ) {
	if ( ! is_array( $items ) || empty( $items ) ) {
		return $items;
	}

	$rules          = mairie_civique_get_protected_space_rules();
	$template_space = array();

	foreach ( $rules as $space => $rule ) {
		$template_space[ $rule['template'] ] = $space;
	}

	$filtered_items = array();

	foreach ( $items as $item ) {
		if ( isset( $item->object_id ) && 'page' === $item->object ) {
			$template = get_page_template_slug( (int) $item->object_id );

			if ( $template && isset( $template_space[ $template ] ) && ! mairie_civique_user_can_access_space( $template_space[ $template ] ) ) {
				continue;
			}
		}

		$filtered_items[] = $item;
	}

	return $filtered_items;
}
	add_filter( 'wp_nav_menu_objects', 'mairie_civique_filter_menu_items_by_access' );

function mairie_civique_menu_fallback() {
	$items = array(
		array(
			'label' => __( 'Espace admin', 'mairie-civique' ),
			'url'   => mairie_civique_get_space_url( 'espace-admin' ),
			'space' => 'admin',
		),
		array(
			'label' => __( 'Espace agent', 'mairie-civique' ),
			'url'   => mairie_civique_get_space_url( 'espace-agent' ),
			'space' => 'agent',
		),
		array(
			'label' => __( 'Actualités', 'mairie-civique' ),
			'url'   => home_url( '/actualites/' ),
		),
		array(
			'label' => __( 'Historique', 'mairie-civique' ),
			'url'   => mairie_civique_get_space_url( 'historique' ),
		),
		array(
			'label' => __( 'Équipe municipale', 'mairie-civique' ),
			'url'   => mairie_civique_get_space_url( 'equipe-municipale' ),
		),
		array(
			'label' => __( 'Services', 'mairie-civique' ),
			'url'   => mairie_civique_get_space_url( 'services' ),
		),
		array(
			'label' => __( 'Contact', 'mairie-civique' ),
			'url'   => mairie_civique_get_space_url( 'contact' ),
		),
	);

	if ( is_user_logged_in() ) {
		$items[] = array(
			'label' => __( 'Deconnexion', 'mairie-civique' ),
			'url'   => wp_logout_url( mairie_civique_get_login_page_url() ),
		);
	} else {
		$items[] = array(
			'label' => __( 'Connexion', 'mairie-civique' ),
			'url'   => mairie_civique_get_login_page_url(),
		);
		$items[] = array(
			'label' => __( 'Inscription', 'mairie-civique' ),
			'url'   => add_query_arg( 'auth', 'register', mairie_civique_get_login_page_url() ),
		);
	}

	echo '<ul>';

	foreach ( $items as $item ) {
		if ( isset( $item['space'] ) && ! mairie_civique_user_can_access_space( $item['space'] ) ) {
			continue;
		}

		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}

	echo '</ul>';
}

function mairie_civique_get_space_url( $slug ) {
	$portal_routes = mairie_civique_get_laravel_portal_routes();
	if ( isset( $portal_routes[ $slug ] ) ) {
		return $portal_routes[ $slug ];
	}

	$page = get_page_by_path( $slug );

	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}

	return home_url( '/' . trim( $slug, '/' ) . '/' );
}

function mairie_civique_get_laravel_app_url() {
	$default = home_url();

	if ( defined( 'MAIRIE_LARAVEL_APP_URL' ) && MAIRIE_LARAVEL_APP_URL ) {
		return untrailingslashit( esc_url_raw( MAIRIE_LARAVEL_APP_URL ) );
	}

	return untrailingslashit( esc_url_raw( get_option( 'mairie_laravel_app_url', $default ) ) );
}

function mairie_civique_is_laravel_portal_enabled() {
	return '' !== mairie_civique_get_laravel_app_url();
}

function mairie_civique_get_laravel_portal_routes() {
	if ( ! mairie_civique_is_laravel_portal_enabled() ) {
		return array();
	}

	$base = mairie_civique_get_laravel_app_url();

	return array(
		'connexion'      => $base . '/connexion',
		'espace-citoyen' => $base . '/portail/citoyen',
		'mes-demandes'   => $base . '/portail/citoyen',
		'espace-agent'   => $base . '/portail/agent',
		'espace-admin'   => $base . '/portail/admin',
	);
}

function mairie_civique_redirect_legacy_wp_mairie_path() {
	if ( is_admin() || wp_doing_ajax() || ! mairie_civique_is_laravel_portal_enabled() ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$request_path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

	if ( '' === $request_path ) {
		return;
	}

	$segments = explode( '/', $request_path );
	$last_segment = end( $segments );

	if ( 'wp_mairie' !== $last_segment ) {
		return;
	}

	$routes = mairie_civique_get_laravel_portal_routes();
	if ( empty( $routes['connexion'] ) ) {
		return;
	}

	wp_safe_redirect( $routes['connexion'], 301 );
	exit;
}
	add_action( 'template_redirect', 'mairie_civique_redirect_legacy_wp_mairie_path', 0 );

function mairie_civique_redirect_protected_spaces_to_laravel() {
	if ( is_admin() || wp_doing_ajax() || ! is_page() || ! mairie_civique_is_laravel_portal_enabled() ) {
		return;
	}

	$queried_object = get_queried_object();
	if ( ! ( $queried_object instanceof WP_Post ) ) {
		return;
	}

	$slug   = $queried_object->post_name;
	$routes = mairie_civique_get_laravel_portal_routes();

	if ( ! isset( $routes[ $slug ] ) ) {
		return;
	}

	wp_safe_redirect( $routes[ $slug ] );
	exit;
}
	add_action( 'template_redirect', 'mairie_civique_redirect_protected_spaces_to_laravel', 1 );

function mairie_civique_allow_laravel_redirect_host( $hosts ) {
	$host = wp_parse_url( mairie_civique_get_laravel_app_url(), PHP_URL_HOST );
	if ( $host && ! in_array( $host, $hosts, true ) ) {
		$hosts[] = $host;
	}

	return $hosts;
}
	add_filter( 'allowed_redirect_hosts', 'mairie_civique_allow_laravel_redirect_host' );

function mairie_civique_get_space_config( $space ) {
	$config = array(
		'admin'   => array(
			'eyebrow'       => __( 'Pilotage communal', 'mairie-civique' ),
			'title'         => __( 'Espace administration', 'mairie-civique' ),
			'description'   => __( 'Pilotage, comptes et suivi.', 'mairie-civique' ),
			'panel_title'   => __( 'Fonctions', 'mairie-civique' ),
			'panel_items'   => array(
				__( 'Comptes agents', 'mairie-civique' ),
				__( 'Demandes', 'mairie-civique' ),
				__( 'Pilotage', 'mairie-civique' ),
			),
			'cta_primary'   => __( 'Voir les actions', 'mairie-civique' ),
			'cta_secondary' => __( 'Contenu', 'mairie-civique' ),
		),
		'agent'   => array(
			'eyebrow'       => __( 'Opérations de terrain', 'mairie-civique' ),
			'title'         => __( 'Espace agent', 'mairie-civique' ),
			'description'   => __( 'Traitement et suivi des demandes.', 'mairie-civique' ),
			'panel_title'   => __( 'Fonctions', 'mairie-civique' ),
			'panel_items'   => array(
				__( 'Demandes recues', 'mairie-civique' ),
				__( 'Statuts', 'mairie-civique' ),
				__( 'Suivi', 'mairie-civique' ),
			),
			'cta_primary'   => __( 'Voir le suivi', 'mairie-civique' ),
			'cta_secondary' => __( 'Contenu', 'mairie-civique' ),
		),
		'citoyen' => array(
			'eyebrow'       => __( 'Services publics numériques', 'mairie-civique' ),
			'title'         => __( 'Espace citoyen', 'mairie-civique' ),
			'description'   => __( 'Demandes, messages et suivi.', 'mairie-civique' ),
			'panel_title'   => __( 'Actions', 'mairie-civique' ),
			'panel_items'   => array(
				__( 'Faire une demande', 'mairie-civique' ),
				__( 'Envoyer un message', 'mairie-civique' ),
				__( 'Suivre ses demandes', 'mairie-civique' ),
			),
			'cta_primary'   => __( 'Demandes', 'mairie-civique' ),
			'cta_secondary' => __( 'Mes demandes', 'mairie-civique' ),
		),
	);

	return $config[ $space ] ?? $config['citoyen'];
}

function mairie_civique_get_request_types() {
	return array(
		'copie-extrait'      => __( 'Copie d’extrait', 'mairie-civique' ),
		'certificat-mariage' => __( 'Certificat de mariage', 'mairie-civique' ),
		'declaration-naissance' => __( 'Déclarer une naissance', 'mairie-civique' ),
		'certificat-deces'   => __( 'Certificat de décès', 'mairie-civique' ),
		'autre'              => __( 'Autre demande', 'mairie-civique' ),
	);
}

function mairie_civique_get_request_statuses() {
	return array(
		'pending'     => __( 'En attente', 'mairie-civique' ),
		'in_progress' => __( 'En cours', 'mairie-civique' ),
		'completed'   => __( 'Traitee', 'mairie-civique' ),
		'rejected'    => __( 'Rejetee', 'mairie-civique' ),
	);
}

function mairie_civique_get_request_status_label( $status ) {
	$statuses = mairie_civique_get_request_statuses();

	return $statuses[ $status ] ?? $statuses['pending'];
}

function mairie_civique_get_request_status_class( $status ) {
	$classes = array(
		'pending'     => 'pending',
		'in_progress' => 'progress',
		'completed'   => 'completed',
		'rejected'    => 'rejected',
	);

	return $classes[ $status ] ?? $classes['pending'];
}

function mairie_civique_get_empty_request_values() {
	return array(
		'request_type'          => '',
		'email'                 => '',
		'first_name'            => '',
		'last_name'             => '',
		'birth_date'            => '',
		'birth_place'           => '',
		'register_number'       => '',
		'address'               => '',
		'parent_one_first_name' => '',
		'parent_one_last_name'  => '',
		'parent_two_first_name' => '',
		'parent_two_last_name'  => '',
		'details'               => '',
	);
}

function mairie_civique_store_request_feedback( $payload ) {
	$key = wp_generate_password( 12, false, false );
	set_transient( 'mairie_civique_request_' . $key, $payload, 10 * MINUTE_IN_SECONDS );

	return $key;
}

function mairie_civique_get_request_feedback() {
	$key = isset( $_GET['mairie_request_state'] ) ? sanitize_key( wp_unslash( $_GET['mairie_request_state'] ) ) : '';

	if ( '' === $key ) {
		return null;
	}

	$payload = get_transient( 'mairie_civique_request_' . $key );
	delete_transient( 'mairie_civique_request_' . $key );

	return is_array( $payload ) ? $payload : null;
}

function mairie_civique_get_request_submission_url() {
	return admin_url( 'admin-post.php' );
}

function mairie_civique_user_is_citizen() {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( current_user_can( 'manage_options' ) || current_user_can( 'edit_mairie_demandes' ) ) {
		return false;
	}

	$user = wp_get_current_user();
	if ( ! ( $user instanceof WP_User ) ) {
		return false;
	}

	$roles = (array) $user->roles;

	return in_array( 'mairie_citoyen', $roles, true ) || in_array( 'subscriber', $roles, true );
}

function mairie_civique_get_laravel_api_endpoint() {
	$default = 'http://127.0.0.1:8000/api/v1/demandes';

	if ( defined( 'MAIRIE_LARAVEL_API_URL' ) && MAIRIE_LARAVEL_API_URL ) {
		return esc_url_raw( MAIRIE_LARAVEL_API_URL );
	}

	return esc_url_raw( get_option( 'mairie_laravel_api_url', $default ) );
}

function mairie_civique_get_laravel_api_token() {
	if ( defined( 'MAIRIE_LARAVEL_API_TOKEN' ) && MAIRIE_LARAVEL_API_TOKEN ) {
		return sanitize_text_field( MAIRIE_LARAVEL_API_TOKEN );
	}

	return sanitize_text_field( get_option( 'mairie_laravel_api_token', '' ) );
}

function mairie_civique_is_laravel_backend_enabled() {
	return '' !== mairie_civique_get_laravel_api_endpoint() && '' !== mairie_civique_get_laravel_api_token();
}

function mairie_civique_send_request_to_laravel( $values, $attachment_id = 0 ) {
	$endpoint = mairie_civique_get_laravel_api_endpoint();
	$token    = mairie_civique_get_laravel_api_token();

	if ( '' === $endpoint || '' === $token ) {
		return new WP_Error( 'laravel_not_configured', __( 'Le backend Laravel n est pas configure.', 'mairie-civique' ) );
	}

	$payload = $values;
	$payload['source'] = 'wordpress';

	if ( $attachment_id ) {
		$payload['attachment_url']  = wp_get_attachment_url( $attachment_id ) ?: '';
		$payload['attachment_name'] = wp_basename( get_attached_file( $attachment_id ) ?: '' );
	}

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 20,
			'headers' => array(
				'Content-Type'  => 'application/json',
				'X-Mairie-Token' => $token,
			),
			'body'    => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$status_code = (int) wp_remote_retrieve_response_code( $response );
	$body        = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $status_code < 200 || $status_code >= 300 ) {
		$message = is_array( $body ) && isset( $body['message'] ) ? (string) $body['message'] : __( 'Erreur lors de l envoi vers Laravel.', 'mairie-civique' );
		return new WP_Error( 'laravel_api_error', $message );
	}

	if ( ! is_array( $body ) ) {
		return new WP_Error( 'laravel_api_invalid_response', __( 'Reponse invalide de Laravel.', 'mairie-civique' ) );
	}

	mairie_laravel_flush_user_cache();

	return $body;
}

function mairie_civique_send_contact_message_to_laravel( $values, $wp_contact_message_id = 0 ) {
	$token = mairie_civique_get_laravel_api_token();
	$base  = mairie_laravel_api_base();

	if ( '' === $base ) {
		$legacy_endpoint = mairie_civique_get_laravel_api_endpoint();
		$base            = preg_replace( '#/api/v1/demandes$#', '', (string) $legacy_endpoint );
		$base            = rtrim( (string) $base, '/' );
	}

	if ( '' === $base || '' === $token ) {
		return new WP_Error( 'laravel_not_configured', __( 'Le backend Laravel n est pas configure.', 'mairie-civique' ) );
	}

	$payload = array(
		'wp_contact_message_id' => $wp_contact_message_id ? (int) $wp_contact_message_id : null,
		'name'                  => sanitize_text_field( (string) ( $values['name'] ?? '' ) ),
		'email'                 => sanitize_email( (string) ( $values['email'] ?? '' ) ),
		'subject'               => sanitize_text_field( (string) ( $values['subject'] ?? '' ) ),
		'message'               => sanitize_textarea_field( (string) ( $values['message'] ?? '' ) ),
		'source'                => sanitize_key( (string) ( $values['source'] ?? 'wordpress' ) ),
		'source_url'            => esc_url_raw( (string) ( $values['source_url'] ?? '' ) ),
		'sender_ip'             => sanitize_text_field( (string) ( $values['sender_ip'] ?? '' ) ),
		'user_agent'            => sanitize_text_field( (string) ( $values['user_agent'] ?? '' ) ),
		'received_at'           => gmdate( 'Y-m-d H:i:s' ),
	);

	$response = wp_remote_post(
		$base . '/api/v1/contact-messages/wordpress',
		array(
			'timeout' => 20,
			'headers' => array(
				'Content-Type'   => 'application/json',
				'Accept'         => 'application/json',
				'X-Mairie-Token' => $token,
			),
			'body'    => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$status_code = (int) wp_remote_retrieve_response_code( $response );
	$body        = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $status_code < 200 || $status_code >= 300 ) {
		$message = is_array( $body ) && isset( $body['message'] ) ? (string) $body['message'] : __( 'Erreur lors de l envoi du message contact vers Laravel.', 'mairie-civique' );
		return new WP_Error( 'laravel_contact_sync_error', $message );
	}

	return is_array( $body ) ? $body : array();
}

function mairie_civique_schedule_laravel_retry( $delay = 300 ) {
	$delay = max( 60, (int) $delay );

	if ( ! wp_next_scheduled( 'mairie_civique_retry_laravel_request_sync' ) ) {
		wp_schedule_single_event( time() + $delay, 'mairie_civique_retry_laravel_request_sync' );
	}
}

function mairie_civique_mark_request_sync_pending( $request_id, $values, $attachment_id, $reason = '' ) {
	update_post_meta( $request_id, '_mairie_laravel_sync_status', 'pending' );
	update_post_meta( $request_id, '_mairie_laravel_sync_payload', wp_json_encode( $values ) );
	update_post_meta( $request_id, '_mairie_laravel_sync_attachment_id', (int) $attachment_id );
	update_post_meta( $request_id, '_mairie_laravel_sync_reason', sanitize_text_field( (string) $reason ) );
	update_post_meta( $request_id, '_mairie_laravel_sync_last_try', time() );
	mairie_civique_schedule_laravel_retry( 120 );
}

function mairie_civique_try_sync_request_to_laravel( $request_id ) {
	$request_id = (int) $request_id;

	if ( $request_id < 1 || ! mairie_civique_is_laravel_backend_enabled() ) {
		return new WP_Error( 'laravel_sync_disabled', __( 'Synchronisation Laravel indisponible.', 'mairie-civique' ) );
	}

	$payload_raw = (string) get_post_meta( $request_id, '_mairie_laravel_sync_payload', true );
	$attachment_id = (int) get_post_meta( $request_id, '_mairie_laravel_sync_attachment_id', true );

	if ( '' === $payload_raw ) {
		return new WP_Error( 'laravel_sync_missing_payload', __( 'Aucune donnee de synchronisation disponible.', 'mairie-civique' ) );
	}

	$values = json_decode( $payload_raw, true );
	if ( ! is_array( $values ) ) {
		return new WP_Error( 'laravel_sync_invalid_payload', __( 'Donnees de synchronisation invalides.', 'mairie-civique' ) );
	}

	$result = mairie_civique_send_request_to_laravel( $values, $attachment_id );
	update_post_meta( $request_id, '_mairie_laravel_sync_last_try', time() );

	if ( is_wp_error( $result ) ) {
		update_post_meta( $request_id, '_mairie_laravel_sync_status', 'pending' );
		update_post_meta( $request_id, '_mairie_laravel_sync_reason', sanitize_text_field( $result->get_error_message() ) );
		mairie_civique_schedule_laravel_retry( 300 );
		return $result;
	}

	if ( isset( $result['id'] ) ) {
		update_post_meta( $request_id, '_mairie_laravel_id', (int) $result['id'] );
	}

	if ( isset( $result['reference'] ) ) {
		update_post_meta( $request_id, '_mairie_laravel_reference', sanitize_text_field( $result['reference'] ) );
	}

	update_post_meta( $request_id, '_mairie_laravel_sync_status', 'synced' );
	delete_post_meta( $request_id, '_mairie_laravel_sync_reason' );
	delete_post_meta( $request_id, '_mairie_laravel_sync_payload' );
	delete_post_meta( $request_id, '_mairie_laravel_sync_attachment_id' );

	return $result;
}

function mairie_civique_retry_pending_laravel_requests() {
	if ( ! mairie_civique_is_laravel_backend_enabled() ) {
		return;
	}

	$pending = get_posts(
		array(
			'post_type'      => 'mairie_demande',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
			'fields'         => 'ids',
			'meta_key'       => '_mairie_laravel_sync_status',
			'meta_value'     => 'pending',
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);

	if ( empty( $pending ) ) {
		return;
	}

	$still_pending = false;

	foreach ( $pending as $request_id ) {
		$result = mairie_civique_try_sync_request_to_laravel( (int) $request_id );
		if ( is_wp_error( $result ) ) {
			$still_pending = true;
		}
	}

	if ( $still_pending ) {
		mairie_civique_schedule_laravel_retry( 300 );
	}
}

add_action( 'mairie_civique_retry_laravel_request_sync', 'mairie_civique_retry_pending_laravel_requests' );

function mairie_civique_handle_citizen_request() {
	if ( ! isset( $_POST['action'] ) || 'mairie_civique_submit_request' !== $_POST['action'] ) {
		return;
	}

	if ( ! isset( $_POST['mairie_civique_request_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mairie_civique_request_nonce'] ) ), 'mairie_civique_submit_request' ) ) {
		wp_die( esc_html__( 'La verification de securite a echoue.', 'mairie-civique' ) );
	}

	$referer = wp_get_referer();
	if ( ! $referer ) {
		$referer = home_url( '/' );
	}

	if ( ! is_user_logged_in() ) {
		$login_url = mairie_civique_get_login_page_url( $referer );
		$login_url = add_query_arg( 'mairie_login_state', 'citizen_required', $login_url );

		wp_safe_redirect( $login_url );
		exit;
	}

	if ( ! mairie_civique_user_is_citizen() ) {
		$state = mairie_civique_store_request_feedback(
			array(
				'status'  => 'error',
				'message' => __( 'Un compte citoyen est requis pour deposer une demande.', 'mairie-civique' ),
				'errors'  => array(),
				'values'  => mairie_civique_get_empty_request_values(),
			)
		);

		wp_safe_redirect( add_query_arg( 'mairie_request_state', $state, $referer ) );
		exit;
	}

	$values = mairie_civique_get_empty_request_values();

	foreach ( $values as $field => $default_value ) {
		$raw_value = isset( $_POST[ $field ] ) ? wp_unslash( $_POST[ $field ] ) : '';
		$values[ $field ] = 'details' === $field || 'address' === $field ? sanitize_textarea_field( $raw_value ) : sanitize_text_field( $raw_value );
	}

	$request_types = mairie_civique_get_request_types();
	$errors        = array();

	if ( ! isset( $request_types[ $values['request_type'] ] ) ) {
		$errors['request_type'] = __( 'Veuillez choisir un type de demande.', 'mairie-civique' );
	}

	$required_fields = array(
		'email'                 => __( 'L email est obligatoire.', 'mairie-civique' ),
		'first_name'            => __( 'Le prenom est obligatoire.', 'mairie-civique' ),
		'last_name'             => __( 'Le nom est obligatoire.', 'mairie-civique' ),
		'birth_date'            => __( 'La date de naissance est obligatoire.', 'mairie-civique' ),
		'birth_place'           => __( 'Le lieu de naissance est obligatoire.', 'mairie-civique' ),
		'register_number'       => __( 'Le numero de registre est obligatoire.', 'mairie-civique' ),
		'address'               => __( 'L adresse est obligatoire.', 'mairie-civique' ),
		'parent_one_first_name' => __( 'Le prenom du parent 1 est obligatoire.', 'mairie-civique' ),
		'parent_one_last_name'  => __( 'Le nom du parent 1 est obligatoire.', 'mairie-civique' ),
		'parent_two_first_name' => __( 'Le prenom du parent 2 est obligatoire.', 'mairie-civique' ),
		'parent_two_last_name'  => __( 'Le nom du parent 2 est obligatoire.', 'mairie-civique' ),
	);

	foreach ( $required_fields as $field => $message ) {
		if ( '' === $values[ $field ] ) {
			$errors[ $field ] = $message;
		}
	}

	if ( '' !== $values['email'] && ! is_email( $values['email'] ) ) {
		$errors['email'] = __( 'Veuillez saisir une adresse email valide.', 'mairie-civique' );
	}

	if ( 'autre' === $values['request_type'] && '' === $values['details'] ) {
		$errors['details'] = __( 'Veuillez preciser votre autre demande.', 'mairie-civique' );
	}

	$attachment_id = 0;

	if ( empty( $errors ) && isset( $_FILES['supporting_document'] ) && ! empty( $_FILES['supporting_document']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$uploaded = wp_handle_upload( $_FILES['supporting_document'], array( 'test_form' => false ) );

		if ( isset( $uploaded['error'] ) ) {
			$errors['supporting_document'] = __( 'Le document n a pas pu etre televerse.', 'mairie-civique' );
		} elseif ( isset( $uploaded['file'], $uploaded['type'], $uploaded['url'] ) ) {
			$file_name   = wp_basename( $uploaded['file'] );
			$attachment_id = wp_insert_attachment(
				array(
					'post_mime_type' => $uploaded['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
					'post_status'    => 'inherit',
				),
				$uploaded['file']
			);

			if ( ! is_wp_error( $attachment_id ) ) {
				$attachment_meta = wp_generate_attachment_metadata( $attachment_id, $uploaded['file'] );
				wp_update_attachment_metadata( $attachment_id, $attachment_meta );
			} else {
				$errors['supporting_document'] = __( 'Le document a ete recu mais pas enregistre dans WordPress.', 'mairie-civique' );
				$attachment_id                 = 0;
			}
		}
	}

	if ( ! empty( $errors ) ) {
		$state = mairie_civique_store_request_feedback(
			array(
				'status' => 'error',
				'message' => __( 'Le formulaire contient des champs a corriger.', 'mairie-civique' ),
				'errors' => $errors,
				'values' => $values,
			)
		);

		wp_safe_redirect( add_query_arg( 'mairie_request_state', $state, $referer ) );
		exit;
	}

	$laravel_submission = null;

	$request_title = sprintf(
		/* translators: 1: request type, 2: last name, 3: first name */
		__( '%1$s - %2$s %3$s', 'mairie-civique' ),
		$request_types[ $values['request_type'] ],
		$values['last_name'],
		$values['first_name']
	);

	$request_content = implode(
		"\n",
		array(
			'Canal: ' . ( is_user_logged_in() ? __( 'Compte connecte', 'mairie-civique' ) : __( 'Sans compte (visiteur)', 'mairie-civique' ) ),
			'Type: ' . $request_types[ $values['request_type'] ],
			'Email: ' . $values['email'],
			'Prenom: ' . $values['first_name'],
			'Nom: ' . $values['last_name'],
			'Date de naissance: ' . $values['birth_date'],
			'Lieu de naissance: ' . $values['birth_place'],
			'Numero de registre: ' . $values['register_number'],
			'Adresse: ' . $values['address'],
			'Parent 1: ' . trim( $values['parent_one_first_name'] . ' ' . $values['parent_one_last_name'] ),
			'Parent 2: ' . trim( $values['parent_two_first_name'] . ' ' . $values['parent_two_last_name'] ),
			'Details complementaires: ' . ( $values['details'] ? $values['details'] : '-' ),
		)
	);

	$request_id = wp_insert_post(
		array(
			'post_type'    => 'mairie_demande',
			'post_status'  => 'publish',
			'post_title'   => $request_title,
			'post_content' => $request_content,
		),
		true
	);

	if ( is_wp_error( $request_id ) ) {
		$state = mairie_civique_store_request_feedback(
			array(
				'status'  => 'error',
				'message' => __( 'La demande n a pas pu etre enregistree.', 'mairie-civique' ),
				'errors'  => array(),
				'values'  => $values,
			)
		);

		wp_safe_redirect( add_query_arg( 'mairie_request_state', $state, $referer ) );
		exit;
	}

	foreach ( $values as $meta_key => $meta_value ) {
		update_post_meta( $request_id, '_mairie_' . $meta_key, $meta_value );
	}

	$sync_pending = false;
	$sync_notice  = '';

	if ( mairie_civique_is_laravel_backend_enabled() ) {
		update_post_meta( $request_id, '_mairie_laravel_sync_payload', wp_json_encode( $values ) );
		update_post_meta( $request_id, '_mairie_laravel_sync_attachment_id', (int) $attachment_id );

		$laravel_submission = mairie_civique_try_sync_request_to_laravel( $request_id );

		if ( is_wp_error( $laravel_submission ) ) {
			$sync_pending = true;
			$sync_notice  = __( 'Votre demande est enregistree. La synchronisation vers le backend est en attente et sera relancee automatiquement.', 'mairie-civique' );
			mairie_civique_mark_request_sync_pending( $request_id, $values, $attachment_id, $laravel_submission->get_error_message() );
		}
	}

	update_post_meta( $request_id, '_mairie_submission_origin', 'citizen_account' );
	update_post_meta( $request_id, '_mairie_requester_user_id', get_current_user_id() );

	update_post_meta( $request_id, '_mairie_status', 'pending' );

	if ( $attachment_id ) {
		update_post_meta( $request_id, '_mairie_supporting_document_id', $attachment_id );
		set_post_thumbnail( $request_id, $attachment_id );
	}

	mairie_civique_send_request_submission_emails( $request_id, $values );

	$state = mairie_civique_store_request_feedback(
		array(
			'status'  => $sync_pending ? 'warning' : 'success',
			'message' => $sync_pending
				? $sync_notice
				: __( 'Votre demande a bien ete enregistree. Les services municipaux peuvent maintenant la traiter.', 'mairie-civique' ),
			'errors'  => array(),
			'values'  => mairie_civique_get_empty_request_values(),
		)
	);

	wp_safe_redirect( add_query_arg( 'mairie_request_state', $state, $referer ) );
	exit;
}
	add_action( 'admin_post_nopriv_mairie_civique_submit_request', 'mairie_civique_handle_citizen_request' );
	add_action( 'admin_post_mairie_civique_submit_request', 'mairie_civique_handle_citizen_request' );

function mairie_civique_filter_editable_roles( $roles ) {
	if ( current_user_can( 'manage_options' ) ) {
		return $roles;
	}

	unset( $roles['mairie_agent'] );
	unset( $roles['administrator'] );

	return $roles;
}
	add_filter( 'editable_roles', 'mairie_civique_filter_editable_roles' );

function mairie_civique_lock_agent_role_assignment( $user_id, $role ) {
	if ( 'mairie_agent' !== $role ) {
		return;
	}

	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	$user = get_user_by( 'id', $user_id );
	if ( $user instanceof WP_User ) {
		$user->set_role( 'mairie_citoyen' );
	}
}
	add_action( 'set_user_role', 'mairie_civique_lock_agent_role_assignment', 10, 2 );

function mairie_civique_send_request_submission_emails( $request_id, $values ) {
	$request_types = mairie_civique_get_request_types();
	$status_label  = mairie_civique_get_request_status_label( 'pending' );
	$type_label    = $request_types[ $values['request_type'] ] ?? $values['request_type'];
	$admin_email   = get_option( 'admin_email' );

	$admin_subject = sprintf(
		/* translators: %s: request type */
		__( 'Nouvelle demande citoyenne: %s', 'mairie-civique' ),
		$type_label
	);

	$admin_message = implode(
		"\n",
		array(
			'Une nouvelle demande citoyenne vient d etre deposee.',
			'',
			'Numero interne: ' . $request_id,
			'Type: ' . $type_label,
			'Statut initial: ' . $status_label,
			'Demandeur: ' . trim( $values['first_name'] . ' ' . $values['last_name'] ),
			'Email: ' . $values['email'],
			'Numero registre: ' . $values['register_number'],
			'',
			'Consultez la demande dans WordPress pour la traiter.',
		)
	);

	if ( $admin_email ) {
		wp_mail( $admin_email, $admin_subject, $admin_message );
	}

	if ( ! empty( $values['email'] ) ) {
		$user_subject = __( 'Confirmation de reception de votre demande', 'mairie-civique' );
		$user_message = implode(
			"\n",
			array(
				'Bonjour ' . trim( $values['first_name'] . ' ' . $values['last_name'] ) . ',',
				'',
				'Votre demande a bien ete recue par la mairie.',
				'Type de demande: ' . $type_label,
				'Statut initial: ' . $status_label,
				'Reference interne: ' . $request_id,
				'',
				'Vous serez recontacte si des informations complementaires sont necessaires.',
			)
		);

		wp_mail( $values['email'], $user_subject, $user_message );
	}
}

function mairie_civique_send_request_status_update_email( $request_id, $old_status, $new_status ) {
	$email = sanitize_email( get_post_meta( $request_id, '_mairie_email', true ) );

	if ( ! $email || $old_status === $new_status ) {
		return;
	}

	$first_name    = get_post_meta( $request_id, '_mairie_first_name', true );
	$last_name     = get_post_meta( $request_id, '_mairie_last_name', true );
	$request_type  = get_post_meta( $request_id, '_mairie_request_type', true );
	$request_types = mairie_civique_get_request_types();
	$type_label    = $request_types[ $request_type ] ?? get_the_title( $request_id );
	$old_label     = mairie_civique_get_request_status_label( $old_status );
	$new_label     = mairie_civique_get_request_status_label( $new_status );

	$subject = sprintf(
		/* translators: %s: new request status */
		__( 'Mise a jour de votre demande: %s', 'mairie-civique' ),
		$new_label
	);

	$message = implode(
		"\n",
		array(
			'Bonjour ' . trim( $first_name . ' ' . $last_name ) . ',',
			'',
			'Le statut de votre demande a ete mis a jour par la mairie.',
			'Type de demande: ' . $type_label,
			'Reference interne: ' . $request_id,
			'Ancien statut: ' . $old_label,
			'Nouveau statut: ' . $new_label,
			'',
			'En cas de besoin, vous pouvez reprendre contact avec les services municipaux.',
		)
	);

	wp_mail( $email, $subject, $message );
}

function mairie_civique_add_request_meta_boxes() {
	add_meta_box(
		'mairie_demande_infos',
		__( 'Informations de la demande', 'mairie-civique' ),
		'mairie_civique_render_request_details_meta_box',
		'mairie_demande',
		'normal',
		'default'
	);

	add_meta_box(
		'mairie_demande_statut',
		__( 'Traitement de la demande', 'mairie-civique' ),
		'mairie_civique_render_request_status_meta_box',
		'mairie_demande',
		'side',
		'default'
	);
}
	add_action( 'add_meta_boxes_mairie_demande', 'mairie_civique_add_request_meta_boxes' );

function mairie_civique_render_request_details_meta_box( $post ) {
	$fields = array(
		'_mairie_request_type'          => __( 'Type', 'mairie-civique' ),
		'_mairie_email'                 => __( 'Email', 'mairie-civique' ),
		'_mairie_first_name'            => __( 'Prenom', 'mairie-civique' ),
		'_mairie_last_name'             => __( 'Nom', 'mairie-civique' ),
		'_mairie_birth_date'            => __( 'Date de naissance', 'mairie-civique' ),
		'_mairie_birth_place'           => __( 'Lieu de naissance', 'mairie-civique' ),
		'_mairie_register_number'       => __( 'Numero registre', 'mairie-civique' ),
		'_mairie_address'               => __( 'Adresse', 'mairie-civique' ),
		'_mairie_parent_one_first_name' => __( 'Prenom parent 1', 'mairie-civique' ),
		'_mairie_parent_one_last_name'  => __( 'Nom parent 1', 'mairie-civique' ),
		'_mairie_parent_two_first_name' => __( 'Prenom parent 2', 'mairie-civique' ),
		'_mairie_parent_two_last_name'  => __( 'Nom parent 2', 'mairie-civique' ),
		'_mairie_details'               => __( 'Details complementaires', 'mairie-civique' ),
	);

	$request_types = mairie_civique_get_request_types();
	echo '<div class="mairie-request-admin-grid">';

	foreach ( $fields as $meta_key => $label ) {
		$value = get_post_meta( $post->ID, $meta_key, true );

		if ( '_mairie_request_type' === $meta_key && isset( $request_types[ $value ] ) ) {
			$value = $request_types[ $value ];
		}

		echo '<div class="mairie-request-admin-field">';
		echo '<strong>' . esc_html( $label ) . '</strong>';
		echo '<p>' . ( $value ? nl2br( esc_html( $value ) ) : '&mdash;' ) . '</p>';
		echo '</div>';
	}

	$attachment_id = (int) get_post_meta( $post->ID, '_mairie_supporting_document_id', true );
	if ( $attachment_id ) {
		$attachment_url = wp_get_attachment_url( $attachment_id );
		if ( $attachment_url ) {
			echo '<div class="mairie-request-admin-field">';
			echo '<strong>' . esc_html__( 'Document joint', 'mairie-civique' ) . '</strong>';
			echo '<p><a href="' . esc_url( $attachment_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Ouvrir le document', 'mairie-civique' ) . '</a></p>';
			echo '</div>';
		}
	}

	echo '</div>';
}

function mairie_civique_render_request_status_meta_box( $post ) {
	$current_status = get_post_meta( $post->ID, '_mairie_status', true );
	$statuses       = mairie_civique_get_request_statuses();

	wp_nonce_field( 'mairie_civique_save_request_status', 'mairie_civique_request_status_nonce' );

	echo '<p>' . esc_html__( 'Mettez a jour l etat de traitement de cette demande.', 'mairie-civique' ) . '</p>';
	echo '<p><label class="screen-reader-text" for="mairie_request_status">' . esc_html__( 'Statut', 'mairie-civique' ) . '</label>';
	echo '<select name="mairie_request_status" id="mairie_request_status" style="width:100%;">';

	foreach ( $statuses as $status_key => $status_label ) {
		echo '<option value="' . esc_attr( $status_key ) . '" ' . selected( $current_status ? $current_status : 'pending', $status_key, false ) . '>' . esc_html( $status_label ) . '</option>';
	}

	echo '</select></p>';
}

function mairie_civique_save_request_status( $post_id ) {
	if ( ! isset( $_POST['mairie_civique_request_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mairie_civique_request_status_nonce'] ) ), 'mairie_civique_save_request_status' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$new_status = isset( $_POST['mairie_request_status'] ) ? sanitize_key( wp_unslash( $_POST['mairie_request_status'] ) ) : '';
	$statuses   = mairie_civique_get_request_statuses();
	$old_status = get_post_meta( $post_id, '_mairie_status', true );

	if ( isset( $statuses[ $new_status ] ) ) {
		update_post_meta( $post_id, '_mairie_status', $new_status );

		if ( $old_status && $old_status !== $new_status ) {
			mairie_civique_send_request_status_update_email( $post_id, $old_status, $new_status );
		}
	}
}
	add_action( 'save_post_mairie_demande', 'mairie_civique_save_request_status' );

function mairie_civique_add_request_columns( $columns ) {
	$columns['mairie_type']   = __( 'Type', 'mairie-civique' );
	$columns['mairie_email']  = __( 'Email', 'mairie-civique' );
	$columns['mairie_status'] = __( 'Statut', 'mairie-civique' );

	return $columns;
}
	add_filter( 'manage_mairie_demande_posts_columns', 'mairie_civique_add_request_columns' );

function mairie_civique_render_request_columns( $column, $post_id ) {
	if ( 'mairie_type' === $column ) {
		$request_type  = get_post_meta( $post_id, '_mairie_request_type', true );
		$request_types = mairie_civique_get_request_types();
		echo esc_html( $request_types[ $request_type ] ?? '—' );
	}

	if ( 'mairie_email' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_mairie_email', true ) ?: '—' );
	}

	if ( 'mairie_status' === $column ) {
		$status = get_post_meta( $post_id, '_mairie_status', true );
		echo '<span class="mairie-status-badge mairie-status-badge--' . esc_attr( mairie_civique_get_request_status_class( $status ) ) . '">' . esc_html( mairie_civique_get_request_status_label( $status ) ) . '</span>';
	}
}
	add_action( 'manage_mairie_demande_posts_custom_column', 'mairie_civique_render_request_columns', 10, 2 );

function mairie_civique_get_request_counts() {
	global $wpdb;

	$counts   = array_fill_keys( array_keys( mairie_civique_get_request_statuses() ), 0 );
	$results  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT pm.meta_value AS status, COUNT(*) AS total
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
			WHERE p.post_type = %s
			AND p.post_status = 'publish'
			AND pm.meta_key = %s
			GROUP BY pm.meta_value",
			'mairie_demande',
			'_mairie_status'
		),
		OBJECT_K
	);

	if ( is_array( $results ) ) {
		foreach ( $results as $status => $result ) {
			if ( isset( $counts[ $status ] ) ) {
				$counts[ $status ] = (int) $result->total;
			}
		}
	}

	return $counts;
}

function mairie_civique_get_recent_requests( $limit = 8 ) {
	return get_posts(
		array(
			'post_type'      => 'mairie_demande',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}

function mairie_civique_get_current_user_requests( $limit = 10 ) {
	if ( ! is_user_logged_in() ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => 'mairie_demande',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => array(
				array(
					'key'   => '_mairie_requester_user_id',
					'value' => get_current_user_id(),
					'compare' => '=',
					'type' => 'NUMERIC',
				),
			),
		)
	);
}

function mairie_civique_render_request_admin_styles() {
	$screen = get_current_screen();

	if ( ! $screen || 'mairie_demande' !== $screen->post_type ) {
		return;
	}
	?>
	<style>
		.mairie-status-badge {
			display:inline-flex;
			align-items:center;
			justify-content:center;
			padding:4px 10px;
			border-radius:999px;
			font-size:12px;
			font-weight:700;
			white-space:nowrap;
		}
		.mairie-status-badge--pending { background:rgba(201,123,50,.14); color:#8f5318; }
		.mairie-status-badge--progress { background:rgba(11,110,79,.14); color:#084c3a; }
		.mairie-status-badge--completed { background:rgba(19,41,61,.12); color:#13293d; }
		.mairie-status-badge--rejected { background:rgba(160,44,44,.12); color:#8a1f1f; }
		.mairie-request-admin-grid {
			display:grid;
			grid-template-columns:repeat(2,minmax(0,1fr));
			gap:12px;
		}
		.mairie-request-admin-field {
			padding:12px;
			border:1px solid #dcdcde;
			border-radius:10px;
			background:#f6f7f7;
		}
		.mairie-request-admin-field strong {
			display:block;
			margin-bottom:4px;
		}
		.mairie-request-admin-field p {
			margin:0;
			word-break:break-word;
		}
	</style>
	<?php
}
	add_action( 'admin_head', 'mairie_civique_render_request_admin_styles' );

function mairie_civique_is_agent_user() {
	if ( ! is_user_logged_in() || current_user_can( 'manage_options' ) ) {
		return false;
	}

	$user = wp_get_current_user();

	return $user instanceof WP_User && in_array( 'mairie_agent', (array) $user->roles, true );
}

function mairie_civique_get_agent_admin_url() {
	return admin_url( 'edit.php?post_type=mairie_demande' );
}

function mairie_civique_limit_agent_admin_access() {
	if ( ! is_admin() || ! mairie_civique_is_agent_user() || wp_doing_ajax() ) {
		return;
	}

	global $pagenow;

	if ( 'profile.php' === $pagenow || 'async-upload.php' === $pagenow ) {
		return;
	}

	if ( 'edit.php' === $pagenow ) {
		$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : 'post';

		if ( 'mairie_demande' === $post_type ) {
			return;
		}
	}

	if ( 'post.php' === $pagenow ) {
		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;
		$post    = $post_id ? get_post( $post_id ) : null;

		if ( $post instanceof WP_Post && 'mairie_demande' === $post->post_type ) {
			return;
		}
	}

	wp_safe_redirect( mairie_civique_get_agent_admin_url() );
	exit;
}
	add_action( 'admin_init', 'mairie_civique_limit_agent_admin_access' );

function mairie_civique_cleanup_agent_admin_menu() {
	if ( ! mairie_civique_is_agent_user() ) {
		return;
	}

	$menu_pages = array(
		'index.php',
		'edit.php',
		'upload.php',
		'edit.php?post_type=page',
		'edit-comments.php',
		'themes.php',
		'plugins.php',
		'tools.php',
		'options-general.php',
		'users.php',
	);

	foreach ( $menu_pages as $menu_page ) {
		remove_menu_page( $menu_page );
	}
}
	add_action( 'admin_menu', 'mairie_civique_cleanup_agent_admin_menu', 999 );

// ─────────────────────────────────────────────────────────────────────────────
// Pages institutionnelles — données
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Événements pour la timeline de la page Historique.
 * Modifiez ce tableau pour personnaliser les événements affichés.
 */
function mairie_civique_get_history_events() {
	return array(
		array(
			'year'    => '1789',
			'title'   => 'Création de la commune',
			'content' => 'Naissance officielle de la commune.',
		),
		array(
			'year'    => '1832',
			'title'   => 'Construction de la mairie',
			'content' => 'Construction du batiment communal.',
		),
		array(
			'year'    => '1880',
			'title'   => 'Ouverture de l\'école publique',
			'content' => 'Ouverture de la premiere ecole publique.',
		),
		array(
			'year'    => '1918',
			'title'   => 'Mémorial aux héros',
			'content' => 'Inauguration du memorial communal.',
		),
		array(
			'year'    => '1960',
			'title'   => 'Développement résidentiel',
			'content' => 'Extension de nouveaux quartiers.',
		),
		array(
			'year'    => '2000',
			'title'   => 'Rénovation du centre-bourg',
			'content' => 'Reamenagement du centre-ville.',
		),
		array(
			'year'    => '2024',
			'title'   => 'Portail numérique citoyen',
			'content' => 'Lancement du portail citoyen.',
		),
	);
}

/**
 * Services municipaux affichés sur la page Services.
 * Modifiez ce tableau pour ajouter, supprimer ou réordonner les services.
 */
function mairie_civique_get_municipal_services() {
	return array(
		array(
			'icon'        => '📜',
			'title'       => 'État civil',
			'description' => 'Actes et certificats.',
			'details'     => array(
				'Déclaration de naissance (sous 5 jours)',
				'Mariage civil et certificat de célibat',
				'Déclaration de décès',
				'Copie intégrale ou extrait d\'acte',
				'Légalisation de signature',
			),
			'url'       => '',
			'url_label' => '',
		),
		array(
			'icon'        => '🏗️',
			'title'       => 'Urbanisme',
			'description' => 'Permis et autorisations.',
			'details'     => array(
				'Permis de construire et d\'aménager',
				'Déclaration préalable de travaux',
				'Certificat d\'urbanisme',
				'Plan local d\'urbanisme (PLU)',
				'Autorisation de division de terrain',
			),
			'url'       => '',
			'url_label' => '',
		),
		array(
			'icon'        => '🚮',
			'title'       => 'Propreté & Environnement',
			'description' => 'Dechets et environnement.',
			'details'     => array(
				'Calendrier de collecte des déchets',
				'Point de dépôt des encombrants',
				'Déchetterie communale',
				'Collecte sélective verre, papier, plastique',
				'Signalement de dépôts sauvages',
			),
			'url'       => '',
			'url_label' => '',
		),
		array(
			'icon'        => '⚽',
			'title'       => 'Sports & Associations',
			'description' => 'Sports et vie associative.',
			'details'     => array(
				'Réservation des salles et gymnases',
				'Annuaire des associations communales',
				'Dossier de demande de subvention',
				'Programme des activités municipales',
				'Stades et terrains extérieurs',
			),
			'url'       => '',
			'url_label' => '',
		),
		array(
			'icon'        => '🎭',
			'title'       => 'Culture & Patrimoine',
			'description' => 'Culture et patrimoine.',
			'details'     => array(
				'Médiathèque et bibliothèque municipale',
				'Agenda des manifestations culturelles',
				'Visites du patrimoine historique',
				'Salle des fêtes et location de matériel',
				'Archives communales',
			),
			'url'       => '',
			'url_label' => '',
		),
		array(
			'icon'        => '👶',
			'title'       => 'Petite enfance & Éducation',
			'description' => 'Ecoles et petite enfance.',
			'details'     => array(
				'Inscriptions scolaires maternelle et primaire',
				'Restaurant scolaire et cantine',
				'Accueil périscolaire et centre de loisirs',
				'Crèche et halte-garderie municipale',
				'Transport scolaire',
			),
			'url'       => '',
			'url_label' => '',
		),
	);
}

/**
 * Traitement du formulaire de contact.
 */
function mairie_civique_handle_contact_form() {
	if (
		! isset( $_POST['mairie_civique_contact_nonce'] ) ||
		! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['mairie_civique_contact_nonce'] ) ),
			'mairie_civique_contact_form'
		)
	) {
		wp_die( esc_html__( 'La vérification de sécurité a échoué.', 'mairie-civique' ) );
	}

	$referer = wp_get_referer();
	if ( ! $referer ) {
		$referer = home_url( '/' );
	}

	$raw_name    = trim( (string) wp_unslash( isset( $_POST['contact_name'] ) ? $_POST['contact_name'] : '' ) );
	$raw_email   = trim( (string) wp_unslash( isset( $_POST['contact_email'] ) ? $_POST['contact_email'] : '' ) );
	$raw_subject = trim( (string) wp_unslash( isset( $_POST['contact_subject'] ) ? $_POST['contact_subject'] : '' ) );
	$raw_message = trim( (string) wp_unslash( isset( $_POST['contact_message'] ) ? $_POST['contact_message'] : '' ) );

	$name    = sanitize_text_field( $raw_name );
	$email   = sanitize_email( $raw_email );
	$subject = sanitize_text_field( $raw_subject );
	$message = sanitize_textarea_field( $raw_message );

	if ( '' === $raw_name || '' === $raw_email || '' === $raw_message ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'mairie_contact_state' => 'error',
					'mairie_contact_error' => 'missing_required',
					'contact_name'        => $raw_name,
					'contact_email'       => $raw_email,
					'contact_subject'     => $raw_subject,
					'contact_message'     => $raw_message,
				),
				$referer
			)
		);
		exit;
	}

	if ( ! is_email( $email ) ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'mairie_contact_state' => 'error',
					'mairie_contact_error' => 'invalid_email',
					'contact_name'        => $raw_name,
					'contact_email'       => $raw_email,
					'contact_subject'     => $raw_subject,
					'contact_message'     => $raw_message,
				),
				$referer
			)
		);
		exit;
	}

	$contact_recipient_email = mairie_civique_get_contact_recipient_email();
	$mail_subject = sprintf(
		/* translators: 1: message subject, 2: sender name */
		'[%1$s] %2$s — %3$s',
		get_bloginfo( 'name' ),
		$subject ? $subject : __( 'Message de contact', 'mairie-civique' ),
		$name
	);

	$mail_body = implode(
		"\n",
		array(
			'Message reçu depuis le site de la mairie.',
			'',
			'De : ' . $name . ' (' . $email . ')',
			'Objet : ' . ( $subject ? $subject : '–' ),
			'',
			$message,
		)
	);

	$message_title = $subject
		? $subject
		: sprintf(
			/* translators: %s: sender name. */
			__( 'Message de contact — %s', 'mairie-civique' ),
			$name
		);

	$contact_message_id = wp_insert_post(
		array(
			'post_type'    => 'mairie_contact_msg',
			'post_status'  => 'publish',
			'post_title'   => wp_trim_words( $message_title, 10, '...' ),
			'post_content' => $message,
		),
		true
	);
	$message_saved      = ! is_wp_error( $contact_message_id ) && (bool) $contact_message_id;

	if ( ! $message_saved ) {
		$contact_message_id = 0;
	}

	if ( $message_saved ) {
		update_post_meta( $contact_message_id, '_mairie_contact_name', $name );
		update_post_meta( $contact_message_id, '_mairie_contact_email', $email );
		update_post_meta( $contact_message_id, '_mairie_contact_subject', $subject );
		update_post_meta( $contact_message_id, '_mairie_contact_recipient_email', $contact_recipient_email );
	}

	$admin_mail_sent = false;
	$admin_mail_error = '';
	if ( $contact_recipient_email ) {
		delete_transient( 'mairie_last_mail_error' );
		$admin_mail_sent = wp_mail( $contact_recipient_email, $mail_subject, $mail_body, array( 'Reply-To: ' . $email ) );

		if ( ! $admin_mail_sent ) {
			$mail_failure = get_transient( 'mairie_last_mail_error' );

			if ( is_array( $mail_failure ) && ! empty( $mail_failure['message'] ) ) {
				$admin_mail_error = sanitize_text_field( $mail_failure['message'] );
			}
		}
	}

	if ( $message_saved ) {
		update_post_meta( $contact_message_id, '_mairie_contact_admin_mail_sent', $admin_mail_sent ? '1' : '0' );

		if ( $admin_mail_error ) {
			update_post_meta( $contact_message_id, '_mairie_contact_admin_mail_error', $admin_mail_error );
		}
	}

	$auto_reply_sent = false;
	if ( $email ) {
		$auto_reply = implode(
			"\n",
			array(
				'Bonjour ' . $name . ',',
				'',
				'Votre message a bien été transmis aux services de la mairie.',
				'Nous vous répondrons dans les meilleurs délais.',
				'',
				get_bloginfo( 'name' ),
			)
		);

		$auto_reply_sent = wp_mail( $email, __( 'Votre message a bien été reçu', 'mairie-civique' ), $auto_reply );
	}

	if ( $message_saved ) {
		update_post_meta( $contact_message_id, '_mairie_contact_auto_reply_sent', $auto_reply_sent ? '1' : '0' );
	}

	$laravel_sync_result = null;
	if ( $message_saved ) {
		$laravel_sync_result = mairie_civique_send_contact_message_to_laravel(
			array(
				'name'       => $name,
				'email'      => $email,
				'subject'    => $subject,
				'message'    => $message,
				'source'     => 'wordpress_contact_form',
				'source_url' => $referer,
				'sender_ip'  => sanitize_text_field( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) ),
				'user_agent' => sanitize_text_field( (string) ( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ),
			),
			$contact_message_id
		);

		if ( is_wp_error( $laravel_sync_result ) ) {
			update_post_meta( $contact_message_id, '_mairie_contact_laravel_sync_status', 'failed' );
			update_post_meta( $contact_message_id, '_mairie_contact_laravel_sync_error', sanitize_text_field( $laravel_sync_result->get_error_message() ) );
		} else {
			update_post_meta( $contact_message_id, '_mairie_contact_laravel_sync_status', 'synced' );

			if ( is_array( $laravel_sync_result ) && isset( $laravel_sync_result['id'] ) ) {
				update_post_meta( $contact_message_id, '_mairie_contact_laravel_id', absint( $laravel_sync_result['id'] ) );
			}
		}
	}

	$wp_mail_state = $admin_mail_sent ? 'success' : 'saved';
	if ( ! $message_saved && ! $admin_mail_sent ) {
		$wp_mail_state = 'error';
	}

	$redirect_args = array(
		'mairie_contact_state' => $wp_mail_state,
	);

	if ( $contact_message_id ) {
		$redirect_args['mairie_contact_ref'] = $contact_message_id;
	}

	if ( ! $admin_mail_sent ) {
		if (
			false !== stripos( $admin_mail_error, 'instancier la fonction mail' ) ||
			false !== stripos( $admin_mail_error, 'fonction mail' ) ||
			false !== stripos( $admin_mail_error, 'instantiate mail function' )
		) {
			$redirect_args['mairie_contact_error'] = 'mail_not_configured';
		} elseif ( $admin_mail_error ) {
			$redirect_args['mairie_contact_error'] = 'mail_failed';
		}
	}

	if ( ! $message_saved && ! $admin_mail_sent ) {
		$redirect_args['mairie_contact_error'] = 'delivery_failed';
	} elseif ( ! $message_saved ) {
		$redirect_args['mairie_contact_error'] = 'save_failed';
	} elseif ( is_wp_error( $laravel_sync_result ) ) {
		$redirect_args['mairie_contact_error'] = 'laravel_sync_failed';
	}

	wp_safe_redirect(
		add_query_arg(
			$redirect_args,
			$referer
		)
	);
	exit;
}
	add_action( 'admin_post_nopriv_mairie_civique_contact_form', 'mairie_civique_handle_contact_form' );
	add_action( 'admin_post_mairie_civique_contact_form', 'mairie_civique_handle_contact_form' );

function mairie_civique_get_contact_recipient_email() {
	$contact_email = mairie_civique_get_practical_info_value( 'email' );

	if ( $contact_email && is_email( $contact_email ) ) {
		return $contact_email;
	}

	$admin_email = sanitize_email( get_option( 'admin_email' ) );

	if ( $admin_email && is_email( $admin_email ) ) {
		return $admin_email;
	}

	return '';
}

function mairie_civique_get_mail_from_email() {
	$contact_email = mairie_civique_get_contact_recipient_email();

	if ( $contact_email && is_email( $contact_email ) ) {
		return $contact_email;
	}

	$admin_email = sanitize_email( get_option( 'admin_email' ) );
	if ( $admin_email && is_email( $admin_email ) ) {
		return $admin_email;
	}

	return 'no-reply@example.com';
}

function mairie_civique_filter_wp_mail_from( $from_email ) {
	$configured = mairie_civique_get_mail_from_email();

	return $configured && is_email( $configured ) ? $configured : $from_email;
}
	add_filter( 'wp_mail_from', 'mairie_civique_filter_wp_mail_from', 20 );

function mairie_civique_filter_wp_mail_from_name( $from_name ) {
	$site_name = sanitize_text_field( get_bloginfo( 'name' ) );

	return '' !== $site_name ? $site_name : $from_name;
}
	add_filter( 'wp_mail_from_name', 'mairie_civique_filter_wp_mail_from_name', 20 );

function mairie_civique_capture_wp_mail_failure( $wp_error ) {
	if ( ! ( $wp_error instanceof WP_Error ) ) {
		return;
	}

	set_transient(
		'mairie_last_mail_error',
		array(
			'message'   => sanitize_text_field( $wp_error->get_error_message() ),
			'timestamp' => time(),
		),
		5 * MINUTE_IN_SECONDS
	);
}
	add_action( 'wp_mail_failed', 'mairie_civique_capture_wp_mail_failure' );

/*
|--------------------------------------------------------------------------
| Intégration Laravel JWT — fonctions utilitaires pour WordPress
|--------------------------------------------------------------------------
*/

/**
 * Retourne l'URL de base de l'API Laravel.
 */
function mairie_laravel_api_base(): string {
	$base = '';

	if ( defined( 'MAIRIE_LARAVEL_API_BASE_URL' ) ) {
		$base = (string) MAIRIE_LARAVEL_API_BASE_URL;
	} elseif ( defined( 'MAIRIE_LARAVEL_API_URL' ) ) {
		$base = (string) MAIRIE_LARAVEL_API_URL;
	}

	$base = rtrim( $base, '/' );

	// Compatibilite: accepte une URL historique finissant par /api/v1/demandes/wordpress.
	$base = preg_replace( '#/api/v1/demandes/wordpress$#', '', $base );
	$base = preg_replace( '#/demandes/wordpress$#', '', $base );
	$base = preg_replace( '#/api/v1$#', '', $base );

	return rtrim( (string) $base, '/' );
}

function mairie_laravel_get_cache_ttl(): int {
	return 60;
}

function mairie_laravel_get_user_cache_key( string $suffix, int $wp_user_id = 0 ): string {
	$wp_user_id = $wp_user_id > 0 ? $wp_user_id : get_current_user_id();

	if ( $wp_user_id < 1 ) {
		return 'mairie_laravel_' . md5( $suffix );
	}

	return 'mairie_laravel_u' . $wp_user_id . '_' . md5( $suffix );
}

function mairie_laravel_flush_user_cache( int $wp_user_id = 0, int $demande_id = 0 ): void {
	$wp_user_id = $wp_user_id > 0 ? $wp_user_id : get_current_user_id();

	if ( $wp_user_id < 1 ) {
		return;
	}

	delete_transient( mairie_laravel_get_user_cache_key( 'profile', $wp_user_id ) );
	delete_transient( mairie_laravel_get_user_cache_key( 'mes_demandes', $wp_user_id ) );

	if ( $demande_id > 0 ) {
		delete_transient( mairie_laravel_get_user_cache_key( 'demande_' . $demande_id, $wp_user_id ) );
	}
}

function mairie_civique_map_laravel_role_to_wp_role( string $laravel_role ): string {
	if ( 'admin' === $laravel_role ) {
		return 'administrator';
	}

	if ( 'agent' === $laravel_role ) {
		return get_role( 'mairie_agent' ) ? 'mairie_agent' : 'subscriber';
	}

	return get_role( 'mairie_citoyen' ) ? 'mairie_citoyen' : 'subscriber';
}

function mairie_civique_sync_wp_user_with_laravel_profile( int $wp_user_id, array $laravel_user ): void {
	$wp_user = get_user_by( 'id', $wp_user_id );

	if ( ! ( $wp_user instanceof WP_User ) ) {
		return;
	}

	$first_name   = isset( $laravel_user['first_name'] ) ? sanitize_text_field( (string) $laravel_user['first_name'] ) : '';
	$last_name    = isset( $laravel_user['last_name'] ) ? sanitize_text_field( (string) $laravel_user['last_name'] ) : '';
	$display_name = isset( $laravel_user['name'] ) ? sanitize_text_field( (string) $laravel_user['name'] ) : trim( $first_name . ' ' . $last_name );
	$laravel_role = isset( $laravel_user['role'] ) ? sanitize_key( (string) $laravel_user['role'] ) : 'citoyen';
	$wp_role      = mairie_civique_map_laravel_role_to_wp_role( $laravel_role );

	wp_update_user(
		array(
			'ID'           => $wp_user_id,
			'first_name'   => $first_name,
			'last_name'    => $last_name,
			'display_name' => $display_name ?: $wp_user->display_name,
		)
	);

	if ( ! in_array( $wp_role, (array) $wp_user->roles, true ) ) {
		$wp_user->set_role( $wp_role );
	}

	update_user_meta( $wp_user_id, '_mairie_laravel_role', $laravel_role );
	update_user_meta( $wp_user_id, '_mairie_laravel_role_synced_at', time() );
}

function mairie_laravel_get_my_profile( string $jwt_token ) {
	$base      = mairie_laravel_api_base();
	$endpoint  = $base . '/api/v1/auth/me';
	$cache_key = mairie_laravel_get_user_cache_key( 'profile' );
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . $jwt_token,
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code || ! is_array( $body ) || empty( $body['user'] ) || ! is_array( $body['user'] ) ) {
		return new WP_Error( 'laravel_profile_fetch_failed', $body['message'] ?? __( 'Impossible de recuperer le profil Laravel.', 'mairie-civique' ) );
	}

	set_transient( $cache_key, $body['user'], 5 * MINUTE_IN_SECONDS );

	return $body['user'];
}

function mairie_civique_maybe_sync_logged_in_user_with_laravel(): void {
	if ( is_admin() || wp_doing_ajax() || ! is_user_logged_in() ) {
		return;
	}

	$wp_user_id = get_current_user_id();
	$last_sync  = (int) get_user_meta( $wp_user_id, '_mairie_laravel_role_synced_at', true );

	if ( $last_sync > 0 && ( time() - $last_sync ) < 300 ) {
		return;
	}

	$jwt = mairie_laravel_get_user_jwt( $wp_user_id );

	if ( '' === $jwt ) {
		return;
	}

	$laravel_user = mairie_laravel_get_my_profile( $jwt );

	if ( is_wp_error( $laravel_user ) || ! is_array( $laravel_user ) ) {
		return;
	}

	mairie_civique_sync_wp_user_with_laravel_profile( $wp_user_id, $laravel_user );
}
	add_action( 'init', 'mairie_civique_maybe_sync_logged_in_user_with_laravel', 20 );

/**
 * Authentifie un utilisateur WordPress auprès de Laravel et retourne son token JWT.
 * Utile pour afficher les demandes d'un citoyen WP depuis Laravel.
 *
 * @param string $email    Email de l'utilisateur.
 * @param string $password Mot de passe en clair (utilisé uniquement lors de la première connexion).
 * @return string|WP_Error Token JWT ou WP_Error.
 */
function mairie_laravel_get_jwt_token( string $email, string $password ) {
	$base     = mairie_laravel_api_base();
	$endpoint = $base . '/api/v1/auth/login';

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array( 'Content-Type' => 'application/json', 'Accept' => 'application/json' ),
			'body'    => wp_json_encode( array( 'email' => $email, 'password' => $password ) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code !== 200 || empty( $body['token'] ) ) {
		return new WP_Error( 'laravel_auth_failed', $body['message'] ?? __( 'Authentification Laravel échouée.', 'mairie-civique' ) );
	}

	return (string) $body['token'];
}

/**
 * Authentifie auprès de Laravel et retourne le payload complet (user + token).
 *
 * @param string $email
 * @param string $password
 * @return array|WP_Error
 */
function mairie_laravel_login_with_profile( string $email, string $password ) {
	$base     = mairie_laravel_api_base();
	$endpoint = $base . '/api/v1/auth/login';

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array( 'Content-Type' => 'application/json', 'Accept' => 'application/json' ),
			'body'    => wp_json_encode( array( 'email' => $email, 'password' => $password ) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code !== 200 || empty( $body['token'] ) || empty( $body['user'] ) || ! is_array( $body['user'] ) ) {
		return new WP_Error( 'laravel_auth_failed', $body['message'] ?? __( 'Authentification Laravel echouee.', 'mairie-civique' ) );
	}

	return $body;
}

/**
 * Récupère les demandes d'un citoyen depuis Laravel en utilisant son token JWT.
 * Le token doit être stocké en session ou meta utilisateur.
 *
 * @param string $jwt_token Token JWT du citoyen.
 * @return array|WP_Error   Données paginées ou WP_Error.
 */
function mairie_laravel_get_mes_demandes( string $jwt_token ) {
	$base     = mairie_laravel_api_base();
	$endpoint = $base . '/api/v1/mes-demandes';
	$cache_key = mairie_laravel_get_user_cache_key( 'mes_demandes' );
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . $jwt_token,
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code !== 200 ) {
		return new WP_Error( 'laravel_fetch_failed', $body['message'] ?? __( 'Impossible de récupérer vos demandes.', 'mairie-civique' ) );
	}

	set_transient( $cache_key, $body, mairie_laravel_get_cache_ttl() );

	return $body;
}

/**
 * Stocke/récupère le token JWT du citoyen en meta utilisateur WordPress.
 * Le token est chiffré avec AUTH_KEY pour éviter exposition directe en BDD.
 */
function mairie_laravel_encrypt_jwt( string $token ): string {
	if ( '' === $token ) {
		return '';
	}

	if ( function_exists( 'openssl_encrypt' ) ) {
		$key = hash( 'sha256', (string) AUTH_KEY . (string) AUTH_SALT, true );
		$iv  = function_exists( 'random_bytes' ) ? random_bytes( 16 ) : openssl_random_pseudo_bytes( 16 );
		$enc = openssl_encrypt( $token, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );

		if ( false !== $enc ) {
			return 'enc:' . base64_encode( $iv . $enc );
		}
	}

	// Fallback legacy if OpenSSL unavailable.
	return base64_encode( $token );
}

function mairie_laravel_decrypt_jwt( string $stored ): string {
	if ( '' === $stored ) {
		return '';
	}

	if ( 0 === strpos( $stored, 'enc:' ) ) {
		$raw = base64_decode( substr( $stored, 4 ), true );
		if ( false === $raw || strlen( $raw ) <= 16 ) {
			return '';
		}

		$iv     = substr( $raw, 0, 16 );
		$cipher = substr( $raw, 16 );
		$key    = hash( 'sha256', (string) AUTH_KEY . (string) AUTH_SALT, true );
		$plain  = openssl_decrypt( $cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );

		return false === $plain ? '' : (string) $plain;
	}

	// Legacy tokens already stored as base64(token).
	$legacy = base64_decode( $stored, true );
	return false === $legacy ? '' : (string) $legacy;
}

function mairie_laravel_request_sync_jwt_by_email( string $email ) {
	$email = sanitize_email( $email );
	$base  = mairie_laravel_api_base();
	$token = mairie_civique_get_laravel_api_token();

	if ( '' === $email || '' === $base || '' === $token ) {
		return new WP_Error( 'laravel_sync_token_unavailable', __( 'Renouvellement JWT indisponible.', 'mairie-civique' ) );
	}

	$response = wp_remote_post(
		$base . '/api/v1/auth/sync-token',
		array(
			'timeout' => 10,
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
				'X-Mairie-Token' => $token,
			),
			'body'    => wp_json_encode( array( 'email' => $email ) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code || ! is_array( $body ) || empty( $body['token'] ) ) {
		return new WP_Error( 'laravel_sync_token_failed', $body['message'] ?? __( 'Renouvellement JWT echoue.', 'mairie-civique' ) );
	}

	return (string) $body['token'];
}

function mairie_laravel_save_user_jwt( int $wp_user_id, string $token ): void {
	update_user_meta( $wp_user_id, '_mairie_laravel_jwt', mairie_laravel_encrypt_jwt( $token ) );
	update_user_meta( $wp_user_id, '_mairie_laravel_jwt_time', time() );
	mairie_laravel_flush_user_cache( $wp_user_id );
}

function mairie_laravel_get_user_jwt( int $wp_user_id ): string {
	$stored = (string) get_user_meta( $wp_user_id, '_mairie_laravel_jwt', true );
	$time   = (int) get_user_meta( $wp_user_id, '_mairie_laravel_jwt_time', true );

	// Token valide ~50 min localement, puis renouvellement automatique.
	if ( $stored && ( time() - $time ) <= 3000 ) {
		$token = mairie_laravel_decrypt_jwt( $stored );
		if ( '' !== $token ) {
			return $token;
		}
	}

	$user = get_user_by( 'id', $wp_user_id );
	if ( ! ( $user instanceof WP_User ) || '' === $user->user_email ) {
		return '';
	}

	$refresh = mairie_laravel_request_sync_jwt_by_email( $user->user_email );
	if ( is_wp_error( $refresh ) ) {
		return '';
	}

	mairie_laravel_save_user_jwt( $wp_user_id, (string) $refresh );

	return (string) $refresh;
}

/**
 * Inscrit un citoyen dans Laravel et retourne son token JWT.
 * Appelé immédiatement après la création WP lors de l'inscription.
 *
 * @param string $email
 * @param string $password     Mot de passe en clair (transmis une seule fois lors de l'inscription).
 * @param string $first_name
 * @param string $last_name
 * @return string|WP_Error  Token JWT ou WP_Error.
 */
function mairie_laravel_register_citizen( string $email, string $password, string $first_name, string $last_name ) {
	$base     = mairie_laravel_api_base();
	$endpoint = $base . '/api/v1/auth/register';

	$full_name = trim( $first_name . ' ' . $last_name );
	if ( '' === $full_name ) {
		$full_name = strstr( $email, '@', true ) ?: 'citoyen';
	}

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array( 'Content-Type' => 'application/json', 'Accept' => 'application/json' ),
			'body'    => wp_json_encode( array(
				'name'                  => $full_name,
				'first_name'            => $first_name,
				'last_name'             => $last_name,
				'email'                 => $email,
				'password'              => $password,
				'password_confirmation' => $password,
			) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code !== 201 || empty( $body['token'] ) ) {
		return new WP_Error( 'laravel_register_failed', $body['message'] ?? __( 'Inscription Laravel échouée.', 'mairie-civique' ) );
	}

	return (string) $body['token'];
}

/**
 * Récupère le détail d'une demande (avec messages) depuis Laravel.
 *
 * @param string $jwt_token
 * @param int    $demande_id
 * @return array|WP_Error
 */
function mairie_laravel_get_demande_detail( string $jwt_token, int $demande_id ) {
	$base     = mairie_laravel_api_base();
	$endpoint = $base . '/api/v1/demandes/' . $demande_id;
	$cache_key = mairie_laravel_get_user_cache_key( 'demande_' . $demande_id );
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . $jwt_token,
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code !== 200 ) {
		return new WP_Error( 'laravel_demande_not_found', $body['message'] ?? __( 'Demande introuvable.', 'mairie-civique' ) );
	}

	set_transient( $cache_key, $body, mairie_laravel_get_cache_ttl() );

	return $body;
}

/**
 * Envoie un message sur une demande depuis WordPress vers Laravel.
 *
 * @param string $jwt_token
 * @param int    $demande_id
 * @param string $body_text  Contenu du message (déjà sanitisé).
 * @return array|WP_Error
 */
function mairie_laravel_send_message( string $jwt_token, int $demande_id, string $body_text ) {
	$base     = mairie_laravel_api_base();
	$endpoint = $base . '/api/v1/demandes/' . $demande_id . '/messages';

	$response = wp_remote_post(
		$endpoint,
		array(
			'timeout' => 10,
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . $jwt_token,
			),
			'body'    => wp_json_encode( array( 'body' => $body_text ) ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code !== 201 ) {
		return new WP_Error( 'laravel_message_failed', $body['message'] ?? __( 'Envoi du message échoué.', 'mairie-civique' ) );
	}

	mairie_laravel_flush_user_cache( get_current_user_id(), $demande_id );

	return $body;
}

/**
 * Convertit un statut Laravel en classe CSS du badge.
 *
 * @param string $status  pending | assigned | processing | completed | rejected
 * @return string  Suffixe CSS (pending, assigned, progress, completed, rejected)
 */
function mairie_laravel_get_status_badge_class( string $status ): string {
	$map = array(
		'pending'    => 'pending',
		'assigned'   => 'assigned',
		'processing' => 'progress',
		'completed'  => 'completed',
		'rejected'   => 'rejected',
	);

	return $map[ $status ] ?? 'pending';
}

/**
 * Retourne le libellé français d'un statut Laravel.
 *
 * @param string $status
 * @return string
 */
function mairie_laravel_get_status_label( string $status ): string {
	$labels = array(
		'pending'    => __( 'En attente', 'mairie-civique' ),
		'assigned'   => __( 'Assignée', 'mairie-civique' ),
		'processing' => __( 'En cours', 'mairie-civique' ),
		'completed'  => __( 'Traitée', 'mairie-civique' ),
		'rejected'   => __( 'Rejetée', 'mairie-civique' ),
	);

	return $labels[ $status ] ?? __( 'En attente', 'mairie-civique' );
}

/**
 * Gestionnaire de la soumission du formulaire de réponse à une demande.
 * Action : admin_post_mairie_laravel_send_message
 */
function mairie_laravel_handle_send_message(): void {
	if (
		! isset( $_POST['mairie_laravel_msg_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mairie_laravel_msg_nonce'] ) ), 'mairie_laravel_send_message' )
	) {
		wp_die( esc_html__( 'La vérification de sécurité a échoué.', 'mairie-civique' ) );
	}

	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( mairie_civique_get_login_page_url() );
		exit;
	}

	$demande_id = isset( $_POST['demande_id'] ) ? absint( $_POST['demande_id'] ) : 0;
	$body_text  = isset( $_POST['message_body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message_body'] ) ) : '';
	$redirect   = isset( $_POST['redirect_to'] ) ? wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), '' ) : '';

	if ( ! $redirect ) {
		$redirect = mairie_civique_get_space_url( 'mes-demandes' );
	}

	if ( ! $demande_id || '' === $body_text ) {
		wp_safe_redirect( add_query_arg( array( 'demande_id' => $demande_id, 'msg_state' => 'empty' ), $redirect ) );
		exit;
	}

	$jwt = mairie_laravel_get_user_jwt( get_current_user_id() );

	if ( ! $jwt ) {
		wp_safe_redirect( add_query_arg( array( 'demande_id' => $demande_id, 'msg_state' => 'no_jwt' ), $redirect ) );
		exit;
	}

	$result = mairie_laravel_send_message( $jwt, $demande_id, $body_text );

	if ( is_wp_error( $result ) ) {
		wp_safe_redirect( add_query_arg( array( 'demande_id' => $demande_id, 'msg_state' => 'error' ), $redirect ) );
		exit;
	}

	wp_safe_redirect( add_query_arg( array( 'demande_id' => $demande_id, 'msg_state' => 'sent' ), $redirect ) );
	exit;
}
	add_action( 'admin_post_mairie_laravel_send_message', 'mairie_laravel_handle_send_message' );

/**
 * Sécurité inscriptions : forcer un mot de passe fort lors de la création
 * d'un compte citoyen (registration_errors + user_profile_update_errors).
 * Règles : ≥ 8 caractères, au moins 1 majuscule, 1 chiffre, 1 caractère spécial.
 */
function mairie_civique_enforce_strong_password( WP_Error $errors, $sanitized_user_login, $user_email ) {
	if ( ! isset( $_POST['pass1'] ) ) {
		return $errors;
	}

	$password = wp_unslash( $_POST['pass1'] );

	if ( '' === $password ) {
		return $errors;
	}

	$messages = array();

	if ( strlen( $password ) < 8 ) {
		$messages[] = __( 'Le mot de passe doit contenir au moins 8 caractères.', 'mairie-civique' );
	}

	if ( ! preg_match( '/[A-Z]/', $password ) ) {
		$messages[] = __( 'Le mot de passe doit contenir au moins une lettre majuscule.', 'mairie-civique' );
	}

	if ( ! preg_match( '/[0-9]/', $password ) ) {
		$messages[] = __( 'Le mot de passe doit contenir au moins un chiffre.', 'mairie-civique' );
	}

	if ( ! preg_match( '/[^A-Za-z0-9]/', $password ) ) {
		$messages[] = __( 'Le mot de passe doit contenir au moins un caractère spécial (ex : @, #, !, $).', 'mairie-civique' );
	}

	foreach ( $messages as $message ) {
		$errors->add( 'weak_password', $message );
	}

	return $errors;
}
	add_filter( 'registration_errors', 'mairie_civique_enforce_strong_password', 10, 3 );