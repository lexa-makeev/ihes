<div class="gl-product-slides" id="gl-proucts">

	<?php if ( 'yes' == $afpv_enable_featured_image_as_first_img) { ?>

	<!-- Feature Image Box -->

		<div class="gl-slider-item" id="afpv_feature_img" >
			<?php the_post_thumbnail(); ?>
		</div>

	<!-- End Feature Image Box -->

	<?php } ?>


	<!-- Feature Video Box -->

		<div class="gl-slider-item" id="gl-product-video" >

			<?php if ('youtube' == $video_type) { // Youtube Video Came From Product Level. ?>

				<?php if (!empty($afpv_video_thumb)) { ?>

					<div class="video-thumbnail" href="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>"  data-width="900" data-height="600">

						<img 
						src="<?php echo esc_url($afpv_video_thumb); ?>" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>"
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						style="height: <?php echo esc_attr($afpv_tp_video_height); ?> !important;"  
						class="afpv-product-video-thumbnail-image"/>

						<div class="afpv-product-video-play-icon">
							<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
						</div>

						<iframe 
							id="video" 
							width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
							height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
							src="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>?rel=<?php echo esc_attr(get_option('pv_featured_enable_tp_show_related')); ?>&amp;controls=1&amp;showinfo=0;autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>" 
							frameborder="0"
							<?php
							
							if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) { 
									
								echo 'allowfullscreen';
								
							} else {
									
								echo 'donotallowfullscreen';
							}
							?>
						></iframe>

					</div>

				<?php } else { ?>

					<iframe 
						id="video" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						src="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>?rel=<?php echo esc_attr(get_option('pv_featured_enable_tp_show_related')); ?>&amp;controls=1&amp;showinfo=0;autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>" 
						frameborder="0"
						<?php
						
						if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) { 
								
							echo 'allowfullscreen';
							
						} else {
								
							echo 'donotallowfullscreen';
						}
						?>
					></iframe>

					<?php 
				}

			} elseif ('facebook' == $video_type) { // Facebook Video Came From Product Level. 
				?>

				<?php if (!empty($afpv_video_thumb)) { ?>

					<div class="video-thumbnail" href="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id); ?>" data-width="900" data-height="600">

						<img 
						src="<?php echo esc_url($afpv_video_thumb); ?>" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>"
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						style="height: <?php echo esc_attr($afpv_tp_video_height); ?> !important;" 
						class="afpv-product-video-thumbnail-image"/>

						<div class="afpv-product-video-play-icon">
							<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
						</div>

						<iframe 
							id="video" 
							src="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id ); ?>&width=<?php echo esc_attr(get_option('pv_fb_featured_video_width_for_product_page')); ?>&show_text=false&height=<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>&appId&mute=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>"
							width="<?php echo esc_attr($afpv_fb_tp_video_width); ?>"
							height="<?php echo esc_attr($afpv_tp_video_height); ?>"  
							scrolling="no" 
							frameborder="0" 
							allowTransparency="true" 
							allow="encrypted-media; autoplay"
							<?php

							if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {

								echo 'allowFullScreen="true"';

							} else {

								echo 'allowFullScreen="false"';

							}
								
							?>
						></iframe>



					</div>

				<?php } else { ?>

					<iframe 
						id="video" 
						src="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id); ?>&width=<?php echo esc_attr(get_option('pv_fb_featured_video_width_for_product_page')); ?>&show_text=false&height=<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>&appId&mute=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>"
						width="<?php echo esc_attr($afpv_fb_tp_video_width); ?>"
						height="<?php echo esc_attr($afpv_tp_video_height); ?>"  
						scrolling="no" 
						frameborder="0" 
						allowTransparency="true" 
						allow="encrypted-media; autoplay"
						<?php

						if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {

							echo 'allowFullScreen="true"';

						} else {

							echo 'allowFullScreen="false"';

						}

						?>
					></iframe>



					<?php 
				}
				
			} elseif ('dailymotion' == $video_type) { // Dailymotion Video Came From Product Level. 
				?>

				<?php if (!empty($afpv_video_thumb)) { ?>

					<div class="video-thumbnail" href="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>"  data-width="900" data-height="600">
						
						<img 
						src="<?php echo esc_url($afpv_video_thumb); ?>" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						style="height: <?php echo esc_attr($afpv_tp_video_height); ?> !important;" 
						class="afpv-product-video-thumbnail-image"/>

						<div class="afpv-product-video-play-icon">
							<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
						</div>

						<iframe 
							id="video" 
							frameborder="0" 
							width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
							height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
							src="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>"
							allow="autoplay"
							<?php
							
							if (1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
								
								echo 'allowfullscreen';
							
							} else {
								
								echo 'donotallowfullscreen';
							
							}

							?>
						></iframe>

					</div>

				<?php } else { ?>

					<iframe 
						id="video" 
						frameborder="0" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						src="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>"
						allow="autoplay"
						<?php
						
						if (1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
							
							echo 'allowfullscreen';
						
						} else {
							
							echo 'donotallowfullscreen';
						
						}

						?>
					></iframe>

					<?php 
				} 

			} elseif ( 'vimeo' == $video_type) { // Vimeo Video Came From Product Level. 
				?>

				<?php if (!empty($afpv_video_thumb)) { ?>

					<div class="video-thumbnail" href="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>"  data-width="900" data-height="600">

						<img 
						src="<?php echo esc_url($afpv_video_thumb); ?>" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						style="height: <?php echo esc_attr($afpv_tp_video_height); ?> !important;" 
						class="afpv-product-video-thumbnail-image"/>

						<div class="afpv-product-video-play-icon">
							<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
						</div>

						<iframe 
							id="video" 
							src="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>?portrait=0&byline=0&title=0&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>&muted=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>" 
							width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
							height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
							frameborder="1" 
							allow="autoplay; fullscreen"
							<?php
								
							if ( 1 == esc_attr( get_option('pv_featured_enable_tp_allow_full') ) ) {
									
								echo 'allowfullscreen';
								
							} else {

								echo 'donotallowfullscreen';

							}
								
							?>
						></iframe>

					</div>

				<?php } else { ?>

					<iframe 
						id="video" 
						src="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>?portrait=0&byline=0&title=0&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>&muted=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						frameborder="1" 
						allow="autoplay; fullscreen"
						<?php
							
						if ( 1 == esc_attr( get_option('pv_featured_enable_tp_allow_full') ) ) {
								
							echo 'allowfullscreen';
							
						} else {

							echo 'donotallowfullscreen';

						}

						?>
					></iframe>

					<?php 
				}

			} elseif ( 'metacafe' == $video_type) { // Metacafe Video Came From Product Level. 
				?>

				<?php if (!empty($afpv_video_thumb)) { ?>

					<div class="video-thumbnail" href="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>"  data-width="900" data-height="600">
					
						<img 
						src="<?php echo esc_url($afpv_video_thumb); ?>" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						style="height: <?php echo esc_attr($afpv_tp_video_height); ?> !important;" 
						class="afpv-product-video-thumbnail-image"/>

						<div class="afpv-product-video-play-icon">
							<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
						</div>

						<iframe 
							id="video" 
							width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
							height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
							src="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>" 
							frameborder="0"
							<?php
							
							if (1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
									
								echo 'allowfullscreen';
								
							} else {

								echo 'donotallowfullscreen';
								
							}
							?>
						></iframe>

					</div>

				<?php } else { ?>

					<iframe 
						id="video" 
						width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
						height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
						src="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_tp_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_tp_is_mute')); ?>" 
						frameborder="0"
						<?php
						
						if (1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
								
							echo 'allowfullscreen';
							
						} else {

							echo 'donotallowfullscreen';
							
						}
						?>
					></iframe>

					<?php 
				}

			} elseif ( 'custom' == $video_type) { // Custom Video Came From Product Level. 
				?>

				<?php if (!empty($afpv_video_thumb)) { ?>

					<div class="video-thumbnail" href="<?php echo esc_url($cus_video_id); ?>"  data-width="900" data-height="600">

						<img 
						src="<?php echo esc_url($afpv_video_thumb); ?>" 
						width="<?php echo esc_attr($afpv_sh_video_width); ?>" 
						height="<?php echo esc_attr($afpv_sh_video_height); ?>"  
						style="height:<?php echo esc_attr($afpv_sh_video_height); ?> !important;" 
						class="afpv-product-video-thumbnail-image"/>

						<div class="afpv-product-video-play-icon">
							<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
						</div>

						<video 
							id="video" 
							border="1" 
							frameborder="3" 
							width="<?php echo esc_attr($afpv_sh_video_width); ?>" 
							height="<?php echo esc_attr($afpv_sh_video_height); ?>"
							<?php
							if ( 1 == esc_attr(get_option('pv_featured_enable_video_controls'))) {
								echo 'controls';
							}
							?>
								<?php
								if ( 1 == esc_attr(get_option('pv_featured_enable_auto_play'))) {
									echo 'autoplay playsinline';
								}
								?>
								<?php
								if ( 1 == esc_attr(get_option('pv_featured_enable_is_mute'))) {
									echo 'muted';
								}
								?>
								<?php
								if ( 1 == esc_attr(get_option('pv_featured_enable_is_loop'))) {
									echo 'loop';
								}
								?>
							>
							<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/mp4">
							<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/webm">
							<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/ogg">
						</video>

					</div>

				<?php } else { ?>

					<video 
						id="video" 
						border="1" 
						frameborder="3" 
						width="<?php echo esc_attr($afpv_sh_video_width); ?>" 
						height="<?php echo esc_attr($afpv_sh_video_height); ?>"
						<?php
						if ( 1 == esc_attr(get_option('pv_featured_enable_video_controls'))) {
							echo 'controls';
						}
						?>
							<?php
							if ( 1 == esc_attr(get_option('pv_featured_enable_auto_play'))) {
								echo 'autoplay';
							}
							?>
							<?php
							if ( 1 == esc_attr(get_option('pv_featured_enable_is_mute'))) {
								echo 'muted';
							}
							?>
							<?php
							if ( 1 == esc_attr(get_option('pv_featured_enable_is_loop'))) {
								echo 'loop';
							}
							?>
						>
						<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/mp4">
						<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/webm">
						<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/ogg">
					</video>

					<?php 
				}

			} else { 
				?>
							
				<a href="<?php echo esc_url($image[0]); ?>" data-group="mygroup">
					<img src="<?php echo esc_url($image[0]); ?>" data-id="<?php echo intval($product->get_id()); ?>" >
				</a>
				
			<?php } ?>
						
		</div>

	<!-- End Feature Video Box -->


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

		<!-- Feature Video Thumbnail Box -->

			<?php

			if (!empty($afpv_video_thumb)) { 

				$thumbLink = $afpv_video_thumb;

			} else {

				$thumbLink = AFPV_URL . '/images/video_icon.png';

			} 

			?>

				<?php if ( 'yes' == $afpv_enable_featured_image_as_first_img) { ?>

				<!-- Feature Image Box -->

					<div class="item-slick" id="afpv_feature_img" >
						<?php the_post_thumbnail(); ?>
					</div>

				<!-- End Feature Image Box -->

				<?php } ?>
		

				<div class="item-slick">

					<img src="<?php echo esc_url($thumbLink); ?>">

				</div>

			

		<!-- End Feature Video Thumbnail Box -->


		<!-- Rule Base Video Thumbnail Box -->

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

		<!-- End Rule Base Video Thumbnail Box -->


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

		<!-- Gallery Video Thumbnail Box -->
				
		</div>
