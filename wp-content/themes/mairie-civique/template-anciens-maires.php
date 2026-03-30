<?php
/**
 * Template Name: Anciens Maires
 * Template Post Type: page
 *
 * Gérez les anciens maires depuis Tableau de bord › Élus.
 * Créez chaque élu avec le rôle « maire » ou « ancien_maire », ajoutez
 * une photo (image à la une) et renseignez la période dans la boîte méta.
 *
 * @package mairie-civique
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="mairie-page-hero">
	<div class="mairie-shell">
		<p class="mairie-eyebrow"><?php esc_html_e( 'Histoire municipale', 'mairie-civique' ); ?></p>
		<h1><?php the_title(); ?></h1>
		<p class="mairie-muted mb-0"><?php esc_html_e( 'Retrouvez les figures qui ont marqué la vie municipale au fil des mandats.', 'mairie-civique' ); ?></p>
	</div>
</div>

<div class="mairie-shell mairie-institutional-page">

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<?php if ( get_the_content() ) : ?>
			<div class="mairie-institutional-intro">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>
	<?php endwhile; endif; ?>

	<?php
	$mayors = new WP_Query(
		array(
			'post_type'      => 'mairie_elu',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_mairie_elu_role',
					'value'   => array( 'maire', 'ancien_maire' ),
					'compare' => 'IN',
				),
			),
			'meta_key' => '_mairie_elu_periode',
			'orderby'  => 'meta_value',
			'order'    => 'DESC',
		)
	);
	?>

	<?php if ( $mayors->have_posts() ) : ?>
		<div class="mairie-mayor-grid">
			<?php while ( $mayors->have_posts() ) : $mayors->the_post(); ?>
				<article class="mairie-mayor-card card border-0 shadow-sm h-100">
					<div class="mairie-mayor-card__photo">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'medium' ); ?>
						<?php else : ?>
							<div class="mairie-mayor-card__placeholder" aria-hidden="true">
								<span><i class="bi bi-person-badge fs-1 text-success"></i></span>
							</div>
						<?php endif; ?>
					</div>

					<div class="mairie-mayor-card__body">
						<h2 class="mairie-mayor-card__name"><?php the_title(); ?></h2>

						<?php $periode = get_post_meta( get_the_ID(), '_mairie_elu_periode', true ); ?>
						<?php if ( $periode ) : ?>
							<p class="mairie-mayor-card__periode"><?php echo esc_html( $periode ); ?></p>
						<?php endif; ?>

						<?php $parti = get_post_meta( get_the_ID(), '_mairie_elu_parti', true ); ?>
						<?php if ( $parti ) : ?>
							<p class="mairie-mayor-card__parti"><?php echo esc_html( $parti ); ?></p>
						<?php endif; ?>

						<?php if ( has_excerpt() ) : ?>
							<p class="mairie-mayor-card__bio"><?php the_excerpt(); ?></p>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>

	<?php else : ?>
		<div class="mairie-empty-state">
			<p><?php esc_html_e( 'Aucun élu enregistré pour le moment.', 'mairie-civique' ); ?></p>
			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<p>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mairie_elu' ) ); ?>" class="mairie-button mairie-button--primary btn btn-success rounded-pill px-4">
						<i class="bi bi-plus-circle me-1"></i>
						<?php esc_html_e( 'Ajouter un élu', 'mairie-civique' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</div>

<?php
get_footer();
