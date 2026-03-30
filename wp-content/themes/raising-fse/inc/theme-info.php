<?php
/**
 * Add Theme info Page
 */

function raising_fse_menu() {
	add_theme_page( esc_html__( 'Raising  FSE', 'raising-fse' ), esc_html__( 'About Raising  FSE', 'raising-fse' ), 'edit_theme_options', 'about-raising-fse', 'raising_fse_theme_page_display' );
}
add_action( 'admin_menu', 'raising_fse_menu' );

function raising_fse_admin_theme_style() {
	wp_enqueue_style('raising-fse-custom-admin-style', esc_url(get_template_directory_uri()) . '/assets/css/admin-styles.css');
}
add_action('admin_enqueue_scripts', 'raising_fse_admin_theme_style');

/**
 * Display About page
 */
function raising_fse_theme_page_display() {
	$theme = wp_get_theme();

	if ( is_child_theme() ) {
		$theme = wp_get_theme()->parent();
	} ?>

		<div class="Grace-wrapper">
			<div class="Grcae-info-holder">
				<div class="Grcae-info-holder-content">
					<div class="Grace-Welcome">
						<h1 class="welcomeTitle"><?php esc_html_e( 'About Theme Info', 'raising-fse' ); ?></h1>                        
						<div class="featureDesc">
							<?php echo esc_html__( 'The Raising FSE is free charity foundation WordPress theme for creating a NGOs, Non-profit, fundraising, charity and donation website.', 'raising-fse' ); ?>
						</div>
						
                        <h1 class="welcomeTitle"><?php esc_html_e( 'Theme Features', 'raising-fse' ); ?></h1>

                        <h2><?php esc_html_e( 'Block Compatibale', 'raising-fse' ); ?></h2>
                        <div class="featureDesc">
                            <?php echo esc_html__( 'The built-in customizer panel quickly change aspects of the design and display changes live before saving them.', 'raising-fse' ); ?>
                        </div>
                        
                        <h2><?php esc_html_e( 'Responsive Ready', 'raising-fse' ); ?></h2>
                        <div class="featureDesc">
                            <?php echo esc_html__( 'The themes layout will automatically adjust and fit on any screen resolution and looks great on any device. Fully optimized for iPhone and iPad.', 'raising-fse' ); ?>
                        </div>
                        
                        <h2><?php esc_html_e( 'Cross Browser Compatible', 'raising-fse' ); ?></h2>
                        <div class="featureDesc">
                            <?php echo esc_html__( 'Our themes are tested in all mordern web browsers and compatible with the latest version including Chrome,Firefox, Safari, Opera, IE11 and above.', 'raising-fse' ); ?>
                        </div>
                        
                        <h2><?php esc_html_e( 'E-commerce', 'raising-fse' ); ?></h2>
                        <div class="featureDesc">
                            <?php echo esc_html__( 'Fully compatible with WooCommerce plugin. Just install the plugin and turn your site into a full featured online shop and start selling products.', 'raising-fse' ); ?>
                        </div>

					</div> <!-- .Grace-Welcome -->
				</div> <!-- .Grcae-info-holder-content -->
				
				
				<div class="Grcae-info-holder-sidebar">
                        <div class="sidebarBX">
                            <h2 class="sidebarBX-title"><?php echo esc_html__( 'Get SmartTrack PRO', 'raising-fse' ); ?></h2>
                            <p><?php echo esc_html__( 'More features availbale on Premium version', 'raising-fse' ); ?></p>
                            <a href="<?php echo esc_url( 'https://gracethemes.com/themes/fundraising-wordpress-theme/' ); ?>" target="_blank" class="button"><?php esc_html_e( 'Get the PRO Version &rarr;', 'raising-fse' ); ?></a>
                        </div>


						<div class="sidebarBX">
							<h2 class="sidebarBX-title"><?php echo esc_html__( 'Important Links', 'raising-fse' ); ?></h2>

							<ul class="themeinfo-links">
                                <li>
									<a href="<?php echo esc_url( 'https://gracethemesdemo.com/raising/' ); ?>" target="_blank"><?php echo esc_html__( 'Demo Preview', 'raising-fse' ); ?></a>
								</li>                               
								<li>
									<a href="<?php echo esc_url( 'https://gracethemesdemo.com/documentation/raising/#homepage-lite' ); ?>" target="_blank"><?php echo esc_html__( 'Documentation', 'raising-fse' ); ?></a>
								</li>
								
								<li>
									<a href="<?php echo esc_url( 'https://gracethemes.com/wordpress-themes/' ); ?>" target="_blank"><?php echo esc_html__( 'View Our Premium Themes', 'raising-fse' ); ?></a>
								</li>
							</ul>
						</div>

						<div class="sidebarBX">
							<h2 class="sidebarBX-title"><?php echo esc_html__( 'Leave us a review', 'raising-fse' ); ?></h2>
							<p><?php echo esc_html__( 'If you are satisfied with Raising  FSE, please give your feedback.', 'raising-fse' ); ?></p>
							<a href="https://wordpress.org/support/theme/raising-fse/reviews/" class="button" target="_blank"><?php esc_html_e( 'Submit a review', 'raising-fse' ); ?></a>
						</div>

				</div><!-- .Grcae-info-holder-sidebar -->	

			</div> <!-- .Grcae-info-holder -->
		</div><!-- .Grace-wrapper -->
<?php } ?>