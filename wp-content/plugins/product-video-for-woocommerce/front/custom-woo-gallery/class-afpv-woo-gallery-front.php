<?php 

	global $product, $woocommerce;

	$yt_video_id                             = get_post_meta( intval($product->get_id()), 'afpv_yt_featured_video_id', true );
	$fb_video_id                             = get_post_meta( intval($product->get_id()), 'afpv_fb_featured_video_id', true );
	$dm_video_id                             = get_post_meta( intval($product->get_id()), 'afpv_dm_featured_video_id', true );
	$vm_video_id                             = get_post_meta( intval($product->get_id()), 'afpv_vm_featured_video_id', true );
	$mc_video_id                             = get_post_meta( intval($product->get_id()), 'afpv_mc_featured_video_id', true );
	$cus_video_id                            = get_post_meta( intval($product->get_id()), 'afpv_cus_featured_video_id', true );
	$video_type                              = get_post_meta( intval($product->get_id()), 'afpv_featured_video_type', true );
	$afpv_video_thumb                        = get_post_meta( intval($product->get_id()), 'afpv_video_thumb', true );
	$afpv_enable_featured_video              = get_post_meta( $product->get_id(), 'afpv_enable_featured_video', true );
	$afpv_enable_featured_video_product_page = get_post_meta( intval($product->get_id()), 'afpv_enable_featured_video_product_page', true );
	$afpv_enable_featured_image_as_first_img = get_post_meta( intval($product->get_id()), 'afpv_enable_featured_image_as_first_img', true );

	$afpv_sh_video_width    = get_option('pv_featured_video_width_product_page');
	$afpv_sh_video_height   = get_option('pv_featured_video_height_product_page');
	$afpv_tp_video_width    = get_option('pv_featured_tp_video_width_product_page');
	$afpv_tp_video_height   = get_option('pv_featured_tp_video_height_product_page');
	$afpv_fb_tp_video_width = get_option('pv_fb_featured_video_width_for_product_page');

	$afpv_sh_video_width    = '' == $afpv_sh_video_width ? '100%' : $afpv_sh_video_width . '%';
	$afpv_sh_video_height   = '' == $afpv_sh_video_height ? '100%' : $afpv_sh_video_height . 'px';
	$afpv_tp_video_width    = '' == $afpv_tp_video_width 	? '100%' : $afpv_tp_video_width . '%';
	$afpv_fb_tp_video_width = '' == $afpv_fb_tp_video_width  ? '100%' : $afpv_fb_tp_video_width;
	$afpv_tp_video_height   = '' == $afpv_tp_video_height ? '100%' : $afpv_tp_video_height;

	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );

	//Get Global Videos
	$args = array(
		'post_type' => 'af_product_videos',
		'post_status' => 'publish',
		'numberposts' => -1
	);

	$attached_product_videos = array();

	$allvideos = get_posts($args);

	foreach ($allvideos as $vid) {

		if ( is_serialized( $vid->afpv_applied_products ) ) {

			$products_attached = unserialize( $vid->afpv_applied_products);

			if ( in_array( $product->get_id(), $products_attached ) ) {

				$attached_product_videos[] = $vid->ID;
			}
		}
		
	}

	?>

<?php 

	$afpv_gallery_thumbnail_position = get_option( 'pv_gallery_thumbnail_position' );

if ( 'pv_gallery_thumbnail_left_position' == $afpv_gallery_thumbnail_position ) { 
	?>

		<div class="woocommerce-product-gallery images gl-product-slider-left">
		
	<?php } elseif ( 'pv_gallery_thumbnail_right_position' == $afpv_gallery_thumbnail_position) { ?>

		<div class="woocommerce-product-gallery images gl-product-slider-right">
		
	<?php } elseif ( 'pv_gallery_thumbnail_top_position' == $afpv_gallery_thumbnail_position) { ?>

		<div class="woocommerce-product-gallery images gl-product-slider-top">

	<?php } elseif ( 'pv_gallery_thumbnail_bottom_position' == $afpv_gallery_thumbnail_position) { ?>

		<div class="woocommerce-product-gallery images gl-product-slider-bottom">
		
	<?php } else { ?>

		<div class="woocommerce-product-gallery images gl-product-slider-bottom">

	<?php } ?>
	
	<?php 
	if ( 1 == $afpv_enable_featured_video && 'yes' == $afpv_enable_featured_video_product_page) { 

		// If Video add on Product Level.
		include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-product-level-video-temp.php';

	} else { 
									
		// If Video add on Rule Level.
		include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-rule-level-video-temp.php';

	} 
	?>
	

</div>
