 <div class="gl-product-slides" id="gl-proucts">

	<!-- Default Product Feature Image If Feature Video Disable. -->

	<div class="gl-slider-item">

		<?php the_post_thumbnail(); ?>

	</div>

	<!-- End Default Product Feature Image If Feature Video Disable. -->
	

	<!-- Rule Base Video Box -->	

	<?php 

		require AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-rule-base-video-box.php';

	?>

	<!-- End Rule Base Video Box -->


	<!-- Gallery Images Box -->

	<?php

		$attachment_ids = $product->get_gallery_image_ids();
		
		$loop = 0;

	if ( $attachment_ids ) {

		foreach ( $attachment_ids as $attachment_id ) {

			$classes = array( 'zoom' );
							
			$image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_single' ) );

			$image_title = esc_attr( get_the_title( $attachment_id ) );

			$image_link = wp_get_attachment_url( $attachment_id );

			$image_class = esc_attr( implode( ' ', $classes ) );

			?>

					<div class="gl-slider-item">
						<a href="<?php echo esc_url( $image_link ); ?>">
							<div class="gl-product-thumbnail-zoom">
					<?php echo wp_kses_post($image, '', ''); ?>
							</div>
						</a>
					</div>

				<?php

				$loop++;

		}
	}

	?>

	<!-- End Gallery Images Box -->

</div>


<!-- Gallery Nav -->

<?php

$afpv_gallery_thumbnail_position = get_option( 'pv_gallery_thumbnail_position' );

if ( 'pv_gallery_thumbnail_left_position' == $afpv_gallery_thumbnail_position ) { 
	?>

<div class="gl-product-slider-left-nav">
	
<?php } elseif ( 'pv_gallery_thumbnail_right_position' == $afpv_gallery_thumbnail_position) { ?>

<div class="gl-product-slider-right-nav">
	
<?php } elseif ( 'pv_gallery_thumbnail_top_position' == $afpv_gallery_thumbnail_position) { ?>

<div class="gl-product-slider-top-nav">

<?php } elseif ( 'pv_gallery_thumbnail_bottom_position' == $afpv_gallery_thumbnail_position) { ?>

<div class="gl-product-slider-bottom-nav">
		
<?php } else { ?>

<div class="gl-product-slider-bottom-nav">

<?php } ?>
	
	<div class="item-slick">

		<?php the_post_thumbnail(); // Product Feature Image If Feature Video Disable. ?>

	</div>

	<!-- Rule Base Video Thumbnial Box -->

	<?php

	if (!empty($attached_product_videos)) {

		foreach ($attached_product_videos as $video_id) {

			$videodata = get_post($video_id);

			if (!empty( $videodata ) ) {

				$afpv_product_video_type = get_post_meta( intval($video_id), 'afpv_product_video_type', true );

				$afpv_yt_product_video_id  = get_post_meta( intval($video_id), 'afpv_yt_product_video_id', true );
				$afpv_fb_product_video_id  = get_post_meta( intval($video_id), 'afpv_fb_product_video_id', true );
				$afpv_dm_product_video_id  = get_post_meta( intval($video_id), 'afpv_dm_product_video_id', true );
				$afpv_vm_product_video_id  = get_post_meta( intval($video_id), 'afpv_vm_product_video_id', true );
				$afpv_mc_product_video_id  = get_post_meta( intval($video_id), 'afpv_mc_product_video_id', true );
				$afpv_cus_product_video_id = get_post_meta( intval($video_id), 'afpv_cus_product_video_id', true );
				$afpv_product_video_thumb  = get_post_meta( intval($video_id), 'afpv_product_video_thumb', true );

				if (!empty($afpv_product_video_thumb)) { 

					$thumbLink = $afpv_product_video_thumb;

				} else {

					$thumbLink = AFPV_URL . '/images/video_icon.png';

				} 
				?>

					<div class="item-slick">

						<img src="<?php echo wp_kses_post($thumbLink); ?>">

					</div>

				<?php 
							
			}

		}

	} 
	
	?>

	<!-- End Rule Base Video Thumbnial Box -->


	<!-- Gallery Video Thumbnail Box -->

	<?php 

		$attachment_ids = $product->get_gallery_image_ids();

		$loop = 0;

	if ( $attachment_ids ) {

		foreach ( $attachment_ids as $attachment_id ) {

			$classes = array( 'zoom' );
					
			$image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_single' ) );

			$image_title = esc_attr( get_the_title( $attachment_id ) );

			$image_link = wp_get_attachment_url( $attachment_id );

			$image_class = esc_attr( implode( ' ', $classes ) );

			?>

				<div class="item-slick">
					<?php echo wp_kses_post($image, '', ''); ?>
				</div>

				<?php

				$loop++;

		}

	}

	?>

	<!-- End Gallery Video Thumbnail Box -->
			
</div>


