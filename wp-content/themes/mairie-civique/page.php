<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<article class="mairie-page container-fluid py-4">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<header class="mairie-page__header">
				<h1><?php the_title(); ?></h1>
			</header>

			<div class="mairie-page__content">
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>
	<?php endif; ?>
</article>

<?php
get_footer();