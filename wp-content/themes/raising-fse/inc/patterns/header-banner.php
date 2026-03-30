<?php 
/**
 * Default Header Banner
 */
return array(
	'title'      => esc_html__( 'Header Banner', 'raising-fse' ),
	'categories' => array( 'raising-fse', 'Header Banner' ),
	'content'    => '<!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"0","margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":"0px"}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="min-height:0px;margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:cover {"url":"'.esc_url(get_template_directory_uri()).'/assets/images/header-banner.jpg","id":20,"dimRatio":0,"minHeight":585,"className":"hdrbanner-BX","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"},"blockGap":"0"}}} -->
<div class="wp-block-cover hdrbanner-BX" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50);min-height:585px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background wp-image-20" alt="" src="'.esc_url(get_template_directory_uri()).'/assets/images/header-banner.jpg" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:group {"className":"bannerInfo","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":"0","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","wideSize":"1170px","justifyContent":"center","contentSize":"1170px"}} -->
<div class="wp-block-group bannerInfo" style="margin-top:0;margin-bottom:0;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:heading {"textAlign":"left","level":3,"style":{"typography":{"fontSize":"63px","textTransform":"none","fontStyle":"normal","fontWeight":"600"},"spacing":{"margin":{"bottom":"var:preset|spacing|60"}}},"textColor":"foreground","fontFamily":"playfair-display"} -->
<h3 class="wp-block-heading has-text-align-left has-foreground-color has-text-color has-playfair-display-font-family" style="margin-bottom:var(--wp--preset--spacing--60);font-size:63px;font-style:normal;font-weight:600;text-transform:none">Give A Helping<br>Hand for Children</h3>
<!-- /wp:heading -->

<!-- wp:buttons {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","justifyContent":"left"}} -->
<div class="wp-block-buttons"><!-- wp:button {"textAlign":"center","backgroundColor":"primary","textColor":"foreground","className":"is-style-fill","style":{"typography":{"fontSize":"17px","fontStyle":"normal","fontWeight":"500"},"spacing":{"padding":{"left":"var:preset|spacing|60","right":"var:preset|spacing|60","top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"border":{"radius":"0px"}},"fontFamily":"inter"} -->
<div class="wp-block-button has-custom-font-size is-style-fill has-inter-font-family" style="font-size:17px;font-style:normal;font-weight:500"><a class="wp-block-button__link has-foreground-color has-primary-background-color has-text-color has-background has-text-align-center wp-element-button" href="#" style="border-radius:0px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--60)">Discover More</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:group -->',
);