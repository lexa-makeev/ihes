<!-- Rule Base Video Box -->	

	<?php if (!empty($attached_product_videos)) { ?>

		<?php foreach ($attached_product_videos as $video_id) { ?>

			<div class="gl-slider-item" id="gl-product-rule-video">

				<?php

				$videodata = get_post($video_id);

				if (!empty( $videodata ) ) {

					$afpv_product_video_type   = get_post_meta( intval($video_id), 'afpv_product_video_type', true );
					$afpv_yt_product_video_id  = get_post_meta( intval($video_id), 'afpv_yt_product_video_id', true );
					$afpv_fb_product_video_id  = get_post_meta( intval($video_id), 'afpv_fb_product_video_id', true );
					$afpv_dm_product_video_id  = get_post_meta( intval($video_id), 'afpv_dm_product_video_id', true );
					$afpv_vm_product_video_id  = get_post_meta( intval($video_id), 'afpv_vm_product_video_id', true );
					$afpv_mc_product_video_id  = get_post_meta( intval($video_id), 'afpv_mc_product_video_id', true );
					$afpv_cus_product_video_id = get_post_meta( intval($video_id), 'afpv_cus_product_video_id', true );
					$afpv_product_video_thumb  = get_post_meta( intval($video_id), 'afpv_product_video_thumb', true );

					if ('youtube' == $afpv_product_video_type ) {  // Youtube Video Came From Rule. 
						?>

						<?php if (!empty($afpv_product_video_thumb)) { ?>


							<div class="video-thumbnail" href="https://www.youtube.com/embed/<?php echo esc_attr($afpv_yt_product_video_id); ?>"  data-width="900" data-height="600">

								<img src="<?php echo esc_url($afpv_product_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" style="height:<?php echo esc_attr($afpv_tp_video_height); ?>;" class="afpv-rule-video-thumbnail-image"/>

								<div class="afpv-rule-video-play-icon">
									<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
								</div>

								<iframe id="video" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" src="https://www.youtube.com/embed/<?php echo esc_attr($afpv_yt_product_video_id); ?>?rel=<?php echo esc_attr(get_option('pv_gallery_enable_tp_show_related')); ?>&amp;controls=1&amp;showinfo=0;autoplay=<?php echo esc_attr(get_option('pv_gallery_enable_tp_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_gallery_enable_tp_is_mute')); ?>" frameborder="0"
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

							<iframe id="video" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" src="https://www.youtube.com/embed/<?php echo esc_attr($afpv_yt_product_video_id); ?>?rel=<?php echo esc_attr(get_option('pv_gallery_enable_tp_show_related')); ?>&amp;controls=1&amp;showinfo=0;autoplay=<?php echo esc_attr(get_option('pv_gallery_enable_tp_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_gallery_enable_tp_is_mute')); ?>" frameborder="0"
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

					} elseif ( 'facebook' == $afpv_product_video_type ) { // Facebook Video Came From Rule. 
						?>


						<?php if (!empty($afpv_product_video_thumb)) { ?>

							<div class="video-thumbnail" href="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $afpv_fb_product_video_id); ?>"  data-width="900" data-height="600">

								<img src="<?php echo esc_url($afpv_product_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>" style="height:<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>px;" class="afpv-rule-video-thumbnail-image"/>

								<div class="afpv-rule-video-play-icon">
									<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
								</div>

								<iframe 
									id="video" 
									src="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $afpv_fb_product_video_id); ?>&width=<?php echo esc_attr(get_option('pv_fb_featured_video_width_for_product_page')); ?>&show_text=false&height=<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>&appId&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>" 
									width="<?php echo esc_attr($afpv_fb_tp_video_width); ?>"  
									height="<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>"  
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
								src="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $afpv_fb_product_video_id); ?>&width=<?php echo esc_attr(get_option('pv_fb_featured_video_width_for_product_page')); ?>&show_text=false&height=<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>&appId&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>" 
								width="<?php echo esc_attr($afpv_fb_tp_video_width); ?>"  
								height="<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>"  
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

					} elseif ( 'dailymotion' == $afpv_product_video_type ) { // Dailymotion Video From Rule. 
						?>

						<?php if (!empty($afpv_product_video_thumb)) { ?>

							<div class="video-thumbnail" href="https://www.dailymotion.com/embed/video/<?php echo esc_attr($afpv_dm_product_video_id); ?>"  data-width="900" data-height="600">

								<img src="<?php echo esc_url($afpv_product_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px;" class="afpv-rule-video-thumbnail-image"/>

								<div class="afpv-rule-video-play-icon">
									<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
								</div>

								<iframe 
									id="video" 
									frameborder="0" 
									width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
									height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
									src="https://www.dailymotion.com/embed/video/<?php echo esc_attr($afpv_dm_product_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>"
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
								src="https://www.dailymotion.com/embed/video/<?php echo esc_attr($afpv_dm_product_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>"
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

					} elseif ( 'vimeo' == $afpv_product_video_type ) { // Vimeo Video Came From Rule. 
						?>

						<?php if (!empty($afpv_product_video_thumb)) { ?>

							<div class="video-thumbnail" href="https://player.vimeo.com/video/<?php echo esc_attr($afpv_vm_product_video_id); ?>"  data-width="900" data-height="600">

								<img 
								src="<?php echo esc_url($afpv_product_video_thumb); ?>" 
								width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
								height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
								style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px;"
								class="afpv-rule-video-thumbnail-image"/>

								<div class="afpv-rule-video-play-icon">
									<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
								</div>

								<iframe 
									id="video" 
									src="https://player.vimeo.com/video/<?php echo esc_attr($afpv_vm_product_video_id); ?>?portrait=0&byline=0&title=0&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&muted=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" 
									width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
									height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
									frameborder="1" 
									allow="autoplay; fullscreen"
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
								src="https://player.vimeo.com/video/<?php echo esc_attr($afpv_vm_product_video_id); ?>?portrait=0&byline=0&title=0&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&muted=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" 
								width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
								height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
								frameborder="1" 
								allow="autoplay; fullscreen"
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

					} elseif ( 'metacafe' ==  $afpv_product_video_type ) { // Metacafe Video Came From Rule. 
						?>

						<?php if (!empty($afpv_product_video_thumb)) { ?>

							<div class="video-thumbnail" href="http://www.metacafe.com/embed/<?php echo esc_attr($afpv_mc_product_video_id); ?>"  data-width="900" data-height="600">

								<img 
								src="<?php echo esc_url($afpv_product_video_thumb); ?>" 
								width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
								height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
								style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px;" 
								class="afpv-rule-video-thumbnail-image"/>
							
								<div class="afpv-rule-video-play-icon">
									<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
								</div>

								<iframe 
									id="video" 
									width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
									height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
									src="http://www.metacafe.com/embed/<?php echo esc_attr($afpv_mc_product_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" 
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
								src="http://www.metacafe.com/embed/<?php echo esc_attr($afpv_mc_product_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" 
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

					} elseif ( 'custom' ==  $afpv_product_video_type ) { // Custom Video Came From Rule. 
						?>

						<?php if (!empty($afpv_product_video_thumb)) { ?>

							<div class="video-thumbnail" href="<?php echo esc_url($afpv_cus_product_video_id); ?>"  data-width="900" data-height="600">

								<img 
								src="<?php echo esc_url($afpv_product_video_thumb); ?>" 
								width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
								height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
								style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px;" 
								class="afpv-rule-video-thumbnail-image"/>

								<div class="afpv-rule-video-play-icon">
									<?php include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/templates/afpv-play-video-icon.php'; ?>
								</div>

								<video 
									id="video" 
									border="1" 
									frameborder="3" 
									width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
									height="<?php echo esc_attr($afpv_tp_video_height); ?>"
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
									<source src="<?php echo esc_url($afpv_cus_product_video_id); ?>#t=0.001" type="video/mp4">
									<source src="<?php echo esc_url($afpv_cus_product_video_id); ?>#t=0.001" type="video/webm">
									<source src="<?php echo esc_url($afpv_cus_product_video_id); ?>#t=0.001" type="video/ogg">
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
								<source src="<?php echo esc_url($afpv_cus_product_video_id); ?>#t=0.001" type="video/mp4">
								<source src="<?php echo esc_url($afpv_cus_product_video_id); ?>#t=0.001" type="video/webm">
								<source src="<?php echo esc_url($afpv_cus_product_video_id); ?>#t=0.001" type="video/ogg">
							</video>

							<?php

						} 
						
					}
					
				} 
				?>

			</div>

		<?php } ?>
	
	<?php } ?>

<!-- End Rule Base Video Box -->
