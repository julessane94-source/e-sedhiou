		</div>
	</main>

	<footer class="mairie-footer">
		<div class="mairie-footer__inner row g-4">
			<div class="mairie-footer__brand col-md-4">
				<p class="mairie-footer__title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
				<p><?php esc_html_e( 'Services municipaux en ligne pour les citoyens, les agents et l administration.', 'mairie-civique' ); ?></p>
			</div>

			<div class="mairie-footer__links col-md-4">
				<p class="mairie-footer__subtitle"><?php esc_html_e( 'Liens utiles', 'mairie-civique' ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</div>

			<div class="mairie-footer__contact col-md-4">
				<p class="mairie-footer__subtitle"><?php esc_html_e( 'Contact', 'mairie-civique' ); ?></p>
				<p><?php echo esc_html( mairie_civique_get_practical_info_value( 'phone' ) ); ?></p>
				<p><?php echo esc_html( mairie_civique_get_practical_info_value( 'email' ) ); ?></p>
			</div>
		</div>
		<p class="mairie-footer__copyright"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> · <?php echo esc_html( date_i18n( 'Y' ) ); ?></p>
	</footer>
</div>

<?php wp_footer(); ?>
</body>
</html>