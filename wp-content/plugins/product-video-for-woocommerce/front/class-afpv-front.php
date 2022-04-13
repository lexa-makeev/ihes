<?php 

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( !class_exists( 'Addify_Product_Videos_Front' ) ) {

	class Addify_Product_Videos_Front extends Addify_Product_Videos {

		public function __construct() {
			add_action( 'woocommerce_before_single_product', array( $this, 'afpv_show_featured_video' ));

			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'afpv_wc_template_loop_product_replaced_thumb'), 10 );

			add_action( 'wp_enqueue_scripts', array($this, 'afpv_front_script'));
			
		}

		public function afpv_front_script() {

			if ( is_post_type_archive( 'product' ) || is_product() ) {

				wp_enqueue_script('jquery');

				wp_enqueue_style( 'frontcss', plugins_url( 'css/afpv_front.css', __FILE__ ), false, '3.4.1' );

				wp_enqueue_script( 'afpv-front', plugins_url( '/js/afpv_front.js', __FILE__ ), array( 'jquery' ), '1.0.0', false );

				wp_enqueue_script( 'html5lightbox', plugins_url( '/js/html5lightbox.js', __FILE__ ), array( 'jquery' ), '1.0.0', false );

				$afpv_gallery_option = get_option('pv_select_gallery_template_option');

				if ( 'woo_gallery_template' == $afpv_gallery_option ) {

					wp_enqueue_style( 'upload)_files_l_ty', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.css', false, '1.0', false );

					wp_enqueue_style( 'upload)_files_li_ty', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', false, '1.0', false );

					wp_enqueue_style( 'upload)_files_link_ty', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick-theme.min.css', false, '1.0', false );

					wp_enqueue_style( 'upload)_files_link_typ', 'https://mreq.github.io/slick-lightbox/dist/slick-lightbox.css', false, '1.0', false );

					wp_enqueue_script( 'upload)_files_link_ty', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', false, '1.0', false );

					wp_enqueue_script( 'upload)_files_link_typ', 'https://mreq.github.io/slick-lightbox/dist/slick-lightbox.js', false, '1.0', false );

				}

				$afpv_dot_cont = get_option('pv_dots_gallery_controller');

				if ('yes' != $afpv_dot_cont ) {
				
					$afpv_dot_cont = 'false';

				}

				$pv_default_gallery_thumbnail_to_show = get_option('pv_gallery_thumbnail_to_show');

				$pv_de_gallery_thumbnail_to_show = 4;

				if ( '' == $pv_default_gallery_thumbnail_to_show ) {
				
					$pv_default_gallery_thumbnail_to_show = $pv_de_gallery_thumbnail_to_show;
			
				}

				wp_localize_script( 'afpv-front', 'afpv_gallery_thumb_setting', 
					array( 
						'afpv_gallery_pos' => get_option('pv_gallery_thumbnail_position'),
						'afpv_gallery_thumbnail_to_show' => $pv_default_gallery_thumbnail_to_show,
						'afpv_autoplay_gallery' => get_option('pv_autoplay_gallery'),
						'afpv_arrows_gallery_controller' => get_option('pv_arrows_gallery_controller'),
						'afpv_dots_gallery_controller' => $afpv_dot_cont
					) 
				);

				$addify_mini_cart_ajax_data = array(
					'admin_url' => admin_url( 'admin-ajax.php' ),
					'nonce'     => wp_create_nonce( 'afpv_setting_nonce' ),
				);
				wp_localize_script( 'ps_mini_cart_admin_js', 'k_php_var', $addify_mini_cart_ajax_data );

			} 

		}

		public function afpv_show_featured_video() { 

			global $product;
			$afpv_enable_featured_video              = get_post_meta( $product->get_id(), 'afpv_enable_featured_video', true );
			$afpv_enable_featured_video_product_page = get_post_meta( intval($product->get_id()), 'afpv_enable_featured_video_product_page', true );
			$args                                    = array(
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
			
			if ( is_single() ) { 

				$select_gallery_temp = get_option('pv_select_gallery_template_option');

				if ( ( 1 == $afpv_enable_featured_video && 'yes' == $afpv_enable_featured_video_product_page ) || count($attached_product_videos ) > 0 ) {

					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
					
					if ( '1' == get_option('pv_product_page_edited') ) {

						add_filter('woocommerce_single_product_image_thumbnail_html', array($this, 'afpv_remove_thumbnail_html'));
						
						add_action( 'woocommerce_product_thumbnails', array($this, 'afpv_woo_display_embed_video_elementor'), 20 );
					} 

					if ( 'woo_gallery_template' == $select_gallery_temp ) {

						add_action( 'woocommerce_before_single_product_summary', array($this, 'afpv_custom_woo_gallery_template'), 20 );

					} else {

						add_action( 'woocommerce_before_single_product_summary', array($this, 'afpv_woo_display_embed_video'), 20 );

					}

				}
			 
			}
			
		}

		public function afpv_remove_thumbnail_html( $html ) {
			$html = '';
			return $html;
		}

		public function afpv_custom_woo_gallery_template( $html ) {

			include AFPV_PLUGIN_DIR . '/front/custom-woo-gallery/class-afpv-woo-gallery-front.php';
		
		}

		public function afpv_woo_display_embed_video( $html ) {
	
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
			$afpv_sh_video_height   = '' == $afpv_sh_video_height ? '100%' : $afpv_sh_video_height;
			$afpv_tp_video_width    = '' == $afpv_tp_video_width 	? '100%' : $afpv_tp_video_width . '%';
			$afpv_tp_video_height   = '' == $afpv_tp_video_height ? '100%' : $afpv_tp_video_height;
			$afpv_fb_tp_video_width = '' == $afpv_fb_tp_video_width  ? '100%' : $afpv_fb_tp_video_width;

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
			<div class="woocommerce-product-gallery images">

			<div class="feat_image">
				<?php if ( 'yes' == $afpv_enable_featured_image_as_first_img ) { ?>

						<a href="<?php echo esc_url(get_the_post_thumbnail_url()); ?>" class="html5lightbox" data-width="900" data-height="600">

							<img class="pro" src="<?php echo esc_url( get_the_post_thumbnail_url()); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
						</a>
						<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

					<?php } elseif ( 1 == $afpv_enable_featured_video && 'yes' == $afpv_enable_featured_video_product_page ) { ?>

					

					<?php if ('youtube' == $video_type) { ?>

						<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">

							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
						</a>
						<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

						<?php } else { ?>

							<iframe 
							width="<?php echo esc_attr($afpv_tp_video_width); ?>" 
							height="<?php echo esc_attr($afpv_tp_video_height); ?>" 
							src="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>?rel=<?php echo esc_attr(get_option('pv_featured_enable_tp_show_related')); ?>&amp;controls=1&amp;showinfo=0;autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" 
							frameborder="0"
									<?php
									if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) { 
										echo 'allowfullscreen';
									} else {
										echo 'donotallowfullscreen';
									}
									?>
							></iframe>

						<?php } ?>

					<?php } elseif ('facebook' == $video_type) { ?>

							<?php
							if (!empty($afpv_video_thumb)) {
								?>

							<a href="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
								<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
							</a>
							<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

						<?php } else { ?>

							<iframe 
							src="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id); ?>&width=<?php echo esc_attr(get_option('pv_fb_featured_video_width_for_product_page')); ?>&show_text=false&height=<?php echo esc_attr(get_option('pv_featured_tp_video_height_product_page')); ?>&appId&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>" 
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


						<?php } ?>

					<?php } elseif ('dailymotion' == $video_type) { ?>

						<?php if (!empty($afpv_video_thumb)) { ?>

							<a href="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
								<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
							</a>
							<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

						<?php } else { ?>

						<iframe frameborder="0" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" src="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>"
							<?php
							if (1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
								echo 'allowfullscreen';
							} else {
								echo 'donotallowfullscreen';
							}
							?>
							allow="autoplay" >
								
							</iframe>

						<?php } ?>

					<?php } elseif ( 'vimeo' == $video_type) { ?>

						<?php if (!empty($afpv_video_thumb)) { ?>

							<a href="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
								<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
							</a>
							<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

						<?php } else { ?>

						<iframe src="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>?portrait=0&byline=0&title=0&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&muted=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" frameborder="1" allow="autoplay; fullscreen"
							<?php
							if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
								echo 'allowfullscreen';
							} else {
								echo 'donotallowfullscreen';
							}
							?>
							>
								
							</iframe>

						<?php } ?>

					<?php } elseif ( 'metacafe' == $video_type) { ?>

						<?php if (!empty($afpv_video_thumb)) { ?>

							<a href="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
								<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr(get_option('pv_featured_tp_video_height_productpage')); ?>" />
							</a>
							<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

						<?php } else { ?>

						<iframe width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" src="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" frameborder="0"
							<?php
							if (1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
								echo 'allowfullscreen';
							} else {
								echo 'donotallowfullscreen';
							}
							?>
							>
								
							</iframe>

						<?php } ?>

					<?php } elseif ( 'custom' == $video_type) { ?>

						<?php if (!empty($afpv_video_thumb)) { ?>

							<a href="<?php echo esc_url($cus_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
								<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_sh_video_width); ?>" height="<?php echo esc_attr($afpv_sh_video_height); ?>" />
							</a>

						<?php } else { ?>

						<video id="" border="1" frameborder="3" width="<?php echo esc_attr($afpv_sh_video_width); ?>" height="<?php echo esc_attr($afpv_sh_video_height); ?>"
							<?php
							if ( 1 == esc_attr(get_option('pv_featured_enable_video_controls'))) { 
								echo 'controls';
							}
							?>
							<?php
							if (1 == esc_attr(get_option('pv_featured_enable_auto_play'))) { 
								echo 'autoplay playsinline';
							}
							?>
							<?php 
							if (1 == esc_attr(get_option('pv_featured_enable_is_mute'))) {
								echo 'muted';
							}
							?>
							<?php
							if (1 == esc_attr(get_option('pv_featured_enable_is_loop'))) {
								echo 'loop';
							}
							?>
							>
							<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/mp4">
							<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/webm">
							<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/ogg">
						</video>

						<?php } ?>

					<?php } else { ?>
						
						<a href="<?php echo esc_url($image[0]); ?>" class="html5lightbox" data-group="mygroup">
							<img src="<?php echo esc_url($image[0]); ?>" data-id="<?php echo intval($product->get_id()); ?>" >
						</a>
						
					<?php } } else { ?>
						<a href="<?php echo esc_url($image[0]); ?>" class="html5lightbox" data-group="mygroup">
							<img src="<?php echo esc_url($image[0]); ?>" data-id="<?php echo intval($product->get_id()); ?>" class="html5lightbox" data-group="mygroup">
						</a>
					<?php } ?>

			</div>
			
			<?php 
			$attachment_ids = $product->get_gallery_image_ids();

			$af_old_gal_thumb_width = get_option('pv_product_page_thumbnails_width');

			$af_old_gal_thumb_width = '' == $af_old_gal_thumb_width ? '100%' : $af_old_gal_thumb_width . '%';
			
			$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );

			$newhtml = '';
			$loop    = 0;
			?>
				<style>
					.pv-thumbnail-width{
						display: inline-block !important;
						vertical-align: top !important;
						margin-top: 2.8%;
						margin-bottom: 0 !important;
						margin-right: 1% !important;
						float: none !important;
						width: 23%;
					}

					.Addify_Product_Videos-thumbnails img{
						width: <?php echo esc_attr($af_old_gal_thumb_width); ?> !important;
					}

					
				</style>
			
				<div class="thumbnails Addify_Product_Videos-thumbnails holo columns-<?php echo esc_attr($columns); ?> ">

					<?php 

					global $product, $woocommerce;

					if ( 'yes' == $afpv_enable_featured_image_as_first_img) { 

						$afpv_product_video_type =  get_post_meta( intval($product->get_id()), 'afpv_featured_video_type', true );

						if (!empty( $afpv_product_video_type ) ) {

							$afpv_product_video_thumb = get_post_meta( intval($product->get_id()), 'afpv_product_video_thumb', true );

							$yt_video_idd  = get_post_meta( intval($product->get_id()), 'afpv_yt_featured_video_id', true );
							$fb_video_idd  = get_post_meta( intval($product->get_id()), 'afpv_fb_featured_video_id', true );
							$dm_video_idd  = get_post_meta( intval($product->get_id()), 'afpv_dm_featured_video_id', true );
							$vm_video_idd  = get_post_meta( intval($product->get_id()), 'afpv_vm_featured_video_id', true );
							$mc_video_idd  = get_post_meta( intval($product->get_id()), 'afpv_mc_featured_video_id', true );
							$cus_video_idd = get_post_meta( intval($product->get_id()), 'afpv_cus_featured_video_id', true );

							if ('youtube' == $afpv_product_video_type ) {

								$vid_link = 'https://www.youtube.com/embed/' . esc_attr($yt_video_idd) . '?rel=' . esc_attr(get_option('pv_gallery_enable_tp_show_related')) . '&controls=1&showinfo=0&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));

							} elseif ( 'facebook' == $afpv_product_video_type ) {

								$vid_link = '//www.facebook.com/plugins/video.php?href=' . esc_attr( $fb_video_idd) . '&show_text=false&appId&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play'));
										
							} elseif ( 'dailymotion' == $afpv_product_video_type ) {

								$vid_link = 'https://www.dailymotion.com/embed/video/' . esc_attr($dm_video_idd) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));

							} elseif ( 'vimeo' == $afpv_product_video_type ) {

								$vid_link = 'https://player.vimeo.com/video/' . esc_attr($vm_video_idd) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&muted=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&portrait=0&byline=0&title=0';

							} elseif ( 'metacafe' ==  $afpv_product_video_type ) {

								$vid_link = 'http://www.metacafe.com/embed/' . esc_attr($mc_video_idd) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) ;

							} elseif ( 'custom' ==  $afpv_product_video_type ) {

								$vid_link = esc_url($cus_video_idd);
							}

							if (!empty($afpv_product_video_thumb)) { 

								$thumbLink = $afpv_product_video_thumb;

							} else {

								$thumbLink = AFPV_URL . '/images/video_icon.png';
							}
			
							?>
								<a href="<?php echo esc_url( $vid_link ); ?>" class="zoom html5lightbox pv-thumbnail-width" data-group="mygroup" title="Image Caption">
									<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="auto" height="auto" />
								</a>
									
							<?php 

						}

					} else { 
						?>

						<a href="<?php echo esc_url( get_the_post_thumbnail_url()); ?>" class="zoom html5lightbox pv-thumbnail-width afpv-product-feature-img" data-group="mygroup" title="Image Caption">
							<img src="<?php echo esc_url( get_the_post_thumbnail_url()); ?>" width="auto" height="auto" />
						</a>

						<?php 
					}

					if ( $attachment_ids ) {

						foreach ( $attachment_ids as $attachment_id ) {

							$classes = array( 'zoom' );
							if ( 0 == $loop || 0 == $loop % $columns ) {
								$classes[] = 'first';
							}
							if ( 0 == ( $loop + 1 ) % $columns ) {
								$classes[] = 'last';
							}
								
							$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
							$image_title = esc_attr( get_the_title( $attachment_id ) );
							$image_link  = wp_get_attachment_url( $attachment_id );
							$image_class = esc_attr( implode( ' ', $classes ) );

							?>
								
								<a href="<?php echo esc_url($image_link); ?>" class="<?php echo esc_attr($image_class); ?> html5lightbox pv-thumbnail-width"  data-group="mygroup"><?php echo wp_kses_post($image, '', ''); ?></a>
								
								<?php 
								$loop++;

						}
					}
					

					if (!empty($attached_product_videos)) {

						foreach ($attached_product_videos as $video_id) {

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

								if ('youtube' == $afpv_product_video_type ) {

									$vid_link = 'https://www.youtube.com/embed/' . esc_attr($afpv_yt_product_video_id) . '?rel=' . esc_attr(get_option('pv_gallery_enable_tp_show_related')) . '&controls=1&showinfo=0&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));
								} elseif ( 'facebook' == $afpv_product_video_type ) {

									$vid_link = '//www.facebook.com/plugins/video.php?href=' . esc_attr( $afpv_fb_product_video_id) . '&show_text=false&appId&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play'));
										
								} elseif ( 'dailymotion' == $afpv_product_video_type ) {

									$vid_link = 'https://www.dailymotion.com/embed/video/' . esc_attr($afpv_dm_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));
								} elseif ( 'vimeo' == $afpv_product_video_type ) {

									$vid_link = 'https://player.vimeo.com/video/' . esc_attr($afpv_vm_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&muted=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&portrait=0&byline=0&title=0';
								} elseif ( 'metacafe' ==  $afpv_product_video_type ) {

									$vid_link = 'http://www.metacafe.com/embed/' . esc_attr($afpv_mc_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) ;
								} elseif ( 'custom' ==  $afpv_product_video_type ) {

									$vid_link = esc_url($afpv_cus_product_video_id);
								}

								if (!empty($afpv_product_video_thumb)) { 

									$thumbLink = $afpv_product_video_thumb;
								} else {

									$thumbLink = AFPV_URL . '/images/video_icon.png';
								}
										
								?>
									<a href="<?php echo esc_url($vid_link); ?>" class="zoom 555 html5lightbox pv-thumbnail-width" data-group="mygroup" title="Image Caption">
										<img width="auto" height="auto" src="<?php echo esc_url($thumbLink); ?>" />
									</a>
									
									<?php 
							}
					  
						}
					}

					?>
				</div>

			</div>

			<?php

			
		}

		public function afpv_woo_display_embed_video_elementor( $html ) {

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

			$afpv_sh_video_width  = get_option('pv_featured_video_width_product_page');
			$afpv_sh_video_height = get_option('pv_featured_video_height_product_page');
			$afpv_tp_video_width  = get_option('pv_featured_tp_video_width_product_page');
			$afpv_tp_video_height = get_option('pv_featured_tp_video_height_product_page');

			$afpv_sh_video_width  = '' == $afpv_sh_video_width ? '100%' : $afpv_sh_video_width . '%';
			$afpv_sh_video_height = '' == $afpv_sh_video_height ? '100%' : $afpv_sh_video_height;
			$afpv_tp_video_width  = '' == $afpv_tp_video_width ? '100%' : $afpv_tp_video_width . '%';
			$afpv_tp_video_height = '' == $afpv_tp_video_height ? '100%' : $afpv_tp_video_height;



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
			<div class="feat_image">
				<?php if ( 1 == $afpv_enable_featured_video && 'yes' == $afpv_enable_featured_video_product_page) { ?>
					<?php if ('youtube' == $video_type) { ?>

						<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">

							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
						</a>
						<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

					<?php } else { ?>


					<iframe width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" src="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>?rel=<?php echo esc_attr(get_option('pv_featured_enable_tp_show_related')); ?>&amp;controls=1&amp;showinfo=0;autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" frameborder="0"
							<?php
							if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) { 
								echo 'allowfullscreen';
							} else {
								echo 'donotallowfullscreen';
							}
							?>
					>
						
					</iframe>


					<?php } ?>

				<?php } elseif ('facebook' == $video_type) { ?>

						<?php
						if (!empty($afpv_video_thumb)) {

							?>

						<a href="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id ); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
						</a>
						<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

					<?php } else { ?>

					<iframe src="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id); ?>&width=100%&show_text=false&height=100%&appId&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="auto"  scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media; autoplay"
							<?php
							if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full')) ) {
								echo 'allowFullScreen = "true"'; 
							} else {
								echo 'allowFullScreen = "false"';
							}
							?>
							>
							
						</iframe>

					<?php } ?>

				<?php } elseif ('dailymotion' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
						</a>
						<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

					<?php } else { ?>

					<iframe frameborder="0" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" src="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>"
						<?php
						if (1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
							echo 'allowfullscreen';
						} else {
							echo 'donotallowfullscreen';
						}
						?>
						allow="autoplay" >
							
						</iframe>

					<?php } ?>

				<?php } elseif ( 'vimeo' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" />
						</a>
						<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

					<?php } else { ?>

					<iframe src="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&portrait=0&byline=0&title=0&muted=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" frameborder="1" allow="autoplay; fullscreen"
						<?php
						if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
							echo 'allowfullscreen';
						} else {
							echo 'donotallowfullscreen';
						}
						?>
						>
							
						</iframe>

					<?php } ?>

				<?php } elseif ( 'metacafe' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr(get_option('pv_featured_tp_video_height_productpage')); ?>" />
						</a>
						<a href="<?php echo esc_url(get_permalink($post->ID)); ?>">

					<?php } else { ?>

					<iframe width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>" src="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" frameborder="0"
						<?php
						if (1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
							echo 'allowfullscreen';
						} else {
							echo 'donotallowfullscreen';
						}
						?>
						>
							
						</iframe>

					<?php } ?>

				<?php } elseif ( 'custom' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="<?php echo esc_url($cus_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_sh_video_width); ?>" height="<?php echo esc_attr($afpv_sh_video_height); ?>" />
						</a>

					<?php } else { ?>

					<video id="" border="1" frameborder="3" width="<?php echo esc_attr($afpv_sh_video_width); ?>" height="<?php echo esc_attr($afpv_sh_video_height); ?>"
						<?php
						if ( 1 == esc_attr(get_option('pv_featured_enable_video_controls'))) { 
							echo 'controls';
						}
						?>
						<?php
						if (1 == esc_attr(get_option('pv_featured_enable_auto_play'))) { 
							echo 'autoplay playsinline';
						}
						?>
						<?php 
						if (1 == esc_attr(get_option('pv_featured_enable_is_mute'))) {
							echo 'muted';
						}
						?>
						<?php
						if (1 == esc_attr(get_option('pv_featured_enable_is_loop'))) {
							echo 'loop';
						}
						?>
						>
						<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/mp4">
						<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/webm">
						<source src="<?php echo esc_url($cus_video_id); ?>#t=0.001" type="video/ogg">
					</video>

					<?php } ?>

				<?php } else { ?>
					
					<a href="<?php echo esc_url($image[0]); ?>" class="html5lightbox" data-group="mygroup">
						<img src="<?php echo esc_url($image[0]); ?>" data-id="<?php echo intval($product->get_id()); ?>" >
					</a>
					
				<?php } } else { ?>
					<a href="<?php echo esc_url($image[0]); ?>" class="html5lightbox" data-group="mygroup">
						<img src="<?php echo esc_url($image[0]); ?>" data-id="<?php echo intval($product->get_id()); ?>" class="html5lightbox" data-group="mygroup">
					</a>
				<?php } ?>

			</div>
			
			<?php 
			$attachment_ids = $product->get_gallery_image_ids();
			
			$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );

			$newhtml = '';
			$loop    = 0;
			$newhtml = '<div class="thumbnails Addify_Product_Videos-thumbnails solo columns-' . $columns . '">';
			if ( $attachment_ids ) {

				foreach ( $attachment_ids as $attachment_id ) {

					$classes = array( 'zoom' );
					if ( 0 == $loop || 0 == $loop % $columns ) {
						$classes[] = 'first';
					}
					if ( 0 == ( $loop + 1 ) % $columns ) {
						$classes[] = 'last';
					}
					
					$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
					$image_title = esc_attr( get_the_title( $attachment_id ) );
					$image_link  = wp_get_attachment_url( $attachment_id );
					$image_class = esc_attr( implode( ' ', $classes ) );

					$newhtml .= '<a href="' . esc_url($image_link) . '" class="' . esc_attr($image_class) . ' html5lightbox"  data-group="mygroup">' . wp_kses_post($image, '', '') . '</a>';
					$loop++;

				}
			}
			

			if (!empty($attached_product_videos)) {

				foreach ($attached_product_videos as $video_id) {

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

						if ('youtube' == $afpv_product_video_type ) {

							$vid_link = 'https://www.youtube.com/embed/' . esc_attr($afpv_yt_product_video_id) . '?rel=' . esc_attr(get_option('pv_gallery_enable_tp_show_related')) . '&controls=1&showinfo=0&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));
						} elseif ( 'facebook' == $afpv_product_video_type ) {

							$vid_link = '//www.facebook.com/plugins/video.php?href=' . esc_attr( $afpv_fb_product_video_id) . '&show_text=false&appId&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play'));
						} elseif ( 'dailymotion' == $afpv_product_video_type ) {

							$vid_link = 'https://www.dailymotion.com/embed/video/' . esc_attr($afpv_dm_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));
						} elseif ( 'vimeo' == $afpv_product_video_type ) {

							$vid_link = 'https://player.vimeo.com/video/' . esc_attr($afpv_vm_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&muted=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&portrait=0&byline=0&title=0';
						} elseif ( 'metacafe' ==  $afpv_product_video_type ) {

							$vid_link = 'http://www.metacafe.com/embed/' . esc_attr($afpv_mc_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) ;
						} elseif ( 'custom' ==  $afpv_product_video_type ) {

							$vid_link = esc_url($afpv_cus_product_video_id);
						}


						if (!empty($afpv_product_video_thumb)) { 

							$thumbLink = $afpv_product_video_thumb;
						} else {

							$thumbLink = AFPV_URL . '/images/video_icon.png';
						}

						$newhtml .= '<a href="' . esc_url($vid_link) . '" class="zoom html5lightbox" data-group="mygroup"><img width="auto" height="auto" src="' . esc_url($thumbLink) . '" /></a>';
						
					}
		  
				}
			}

			$newhtml .= '</div>'; 

			echo wp_kses_post($newhtml, '', '');

			
		}
		

		public function afpv_wc_template_loop_product_replaced_thumb() {
			

			add_filter('woocommerce_product_get_image' , function( $image = '' ) {
				$image_st = '';
				$image_en = '';
				if ('flatsome' == get_template()) {
					$class = 'flatitemv';
				} else {
					$class = '';
				}
				$image_st = '<div class="woocommerce-product-gallery' . esc_attr($class) . '" >';
				$image_en = '</div>';
				global $product;
				$afpv_enable_featured_video           = get_post_meta( $product->get_id(), 'afpv_enable_featured_video', true );
				$afpv_enable_featured_video_shop_page = get_post_meta( $product->get_id(), 'afpv_enable_featured_video_shop_page', true );
				if ('1' == $afpv_enable_featured_video && 'yes' == $afpv_enable_featured_video_shop_page) {
					$image = $this->afpv_custom_action();
					return $image_st . $image . $image_en;
				} else {
					return $image;
				}
			});
		}


		public function afpv_custom_action() {
			global $product;
			
			//Flatsome theme
			

			$afpv_enable_featured_video = get_post_meta( $product->get_id(), 'afpv_enable_featured_video', true );
			if ( 1 == $afpv_enable_featured_video) {


				$yt_video_id      = get_post_meta( intval($product->get_id()), 'afpv_yt_featured_video_id', true );
				$fb_video_id      = get_post_meta( intval($product->get_id()), 'afpv_fb_featured_video_id', true );
				$dm_video_id      = get_post_meta( intval($product->get_id()), 'afpv_dm_featured_video_id', true );
				$vm_video_id      = get_post_meta( intval($product->get_id()), 'afpv_vm_featured_video_id', true );
				$mc_video_id      = get_post_meta( intval($product->get_id()), 'afpv_mc_featured_video_id', true );
				$cus_video_id     = get_post_meta( intval($product->get_id()), 'afpv_cus_featured_video_id', true );
				$video_type       = get_post_meta( intval($product->get_id()), 'afpv_featured_video_type', true );
				$afpv_video_thumb = get_post_meta( intval($product->get_id()), 'afpv_video_thumb', true );

				$afpv_sh_video_width      = get_option('pv_featured_video_width_shop_page');
				$afpv_sh_video_height     = get_option('pv_featured_video_height_shop_page');
				$afpv_tp_video_width      = get_option('pv_featured_tp_video_width_shop_page');
				$afpv_tp_video_height     = get_option('pv_featured_tp_video_height_shop_page');
				$afpv_fb_shop_video_width = get_option('pv_fb_featured_video_width_for_shop_page');
				
				$afpv_sh_video_width      = '' == $afpv_sh_video_width ? '100%' : $afpv_sh_video_width . '%';
				$afpv_sh_video_height     = '' == $afpv_sh_video_height ? '100%' : $afpv_sh_video_height;
				$afpv_tp_video_width      = '' == $afpv_tp_video_width 	? '100%' : $afpv_tp_video_width . '%';
				$afpv_fb_shop_video_width = '' == $afpv_fb_shop_video_width  ? '100%' : $afpv_fb_shop_video_width;
				$afpv_tp_video_height     = '' == $afpv_tp_video_height ? '100%' : $afpv_tp_video_height;

				ob_start();
				?>

			

				<?php if ( 'youtube' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">

							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="<?php echo esc_attr($afpv_tp_video_height); ?>"  style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px !important;"/>
						</a>
						<a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">

					<?php } else { ?>

					<iframe width="<?php echo esc_attr($afpv_tp_video_width); ?>" height="<?php echo esc_attr($afpv_tp_video_height); ?>"  src="https://www.youtube.com/embed/<?php echo esc_attr($yt_video_id); ?>?rel=<?php echo esc_attr(get_option('pv_featured_enable_tp_show_related')); ?>&amp;controls=1&amp;showinfo=0;autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" frameborder="0"
						<?php 
						if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
							echo 'allowfullscreen';
						} else {
							echo 'donotallowfullscreen';
						}
						?>
						>
							
						</iframe>

					<?php } ?>

				<?php } elseif ( 'facebook' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="<?php echo esc_attr($afpv_tp_video_height); ?>" style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px !important;" />
						</a>
						<a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">

					<?php } else { ?>

					<iframe src="//www.facebook.com/plugins/video.php?href=<?php echo esc_url( $fb_video_id); ?>&width=<?php echo esc_attr(get_option('pv_fb_featured_video_width_for_shop_page')); ?>&show_text=false&height=<?php echo esc_attr(get_option('pv_featured_tp_video_height_shop_page')); ?>&appId&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>" 
						width="<?php echo esc_attr($afpv_fb_shop_video_width); ?>"  
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
						>
							
						</iframe>

					<?php } ?>

				<?php } elseif ( 'dailymotion' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="<?php echo esc_attr($afpv_tp_video_height); ?>" style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px !important;" />
						</a>
						<a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">

					<?php } else { ?>

					<iframe frameborder="0" width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="<?php echo esc_attr($afpv_tp_video_height); ?>"  src="https://www.dailymotion.com/embed/video/<?php echo esc_attr($dm_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>"
						<?php
						if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
							echo 'allowfullscreen';
						} else {
							echo 'donotallowfullscreen';
						}
						?>
						allow="autoplay">
							
						</iframe>

					<?php } ?>

				<?php } elseif ( 'vimeo' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="<?php echo esc_attr($afpv_tp_video_height); ?>" style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px !important;" />
						</a>
						<a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">

					<?php } else { ?>

					<iframe src="https://player.vimeo.com/video/<?php echo esc_attr($vm_video_id); ?>?&portrait=0&byline=0&title=0&autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&muted=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="<?php echo esc_attr($afpv_tp_video_height); ?>"  frameborder="1" allow="autoplay; fullscreen"
						<?php
						if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
							echo 'allowfullscreen';
						} else {
							echo 'donotallowfullscreen';
						}
						?>
						>
							
						</iframe>

					<?php } ?>

				<?php } elseif ( 'metacafe' == $video_type) { ?>

					<?php if (!empty($afpv_video_thumb)) { ?>

						<a href="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="<?php echo esc_attr($afpv_tp_video_height); ?>" style="height:<?php echo esc_attr($afpv_tp_video_height); ?>px !important;" />
						</a>
						<a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">

					<?php } else { ?>

					<iframe width="<?php echo esc_attr($afpv_tp_video_width); ?>"  height="<?php echo esc_attr($afpv_tp_video_height); ?>"  src="http://www.metacafe.com/embed/<?php echo esc_attr($mc_video_id); ?>?autoplay=<?php echo esc_attr(get_option('pv_featured_enable_auto_play')); ?>&mute=<?php echo esc_attr(get_option('pv_featured_enable_is_mute')); ?>" frameborder="0"
						<?php
						if ( 1 == esc_attr(get_option('pv_featured_enable_tp_allow_full'))) {
							echo 'allowfullscreen';
						} else {
							echo 'donotallowfullscreen';
						}
						?>
						>
							
						</iframe>

					<?php } ?>

				<?php } elseif ( 'custom' == $video_type) { ?>

					<?php
					if (!empty($afpv_video_thumb)) {
						?>

						<a href="<?php echo esc_url($cus_video_id); ?>" class="html5lightbox" data-width="900" data-height="600">
							<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="<?php echo esc_attr($afpv_sh_video_width); ?>" height="<?php echo esc_attr($afpv_sh_video_height); ?>" style="height: <?php echo esc_attr($afpv_sh_video_height); ?>px !important;" />
						</a>
						<a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">

					<?php } else { ?>

					<video id="" border="1" frameborder="3" width="<?php echo esc_attr($afpv_sh_video_width); ?>" height="<?php echo esc_attr($afpv_sh_video_height); ?>"
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

					<?php } ?>

				<?php } ?>

				<?php 
				return ob_get_clean();
			}
		}


		//Flatsome theme function
		public function afpv_for_flatsome_theme_video_gallery() {

			global $product, $woocommerce;
			//Featured Video
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

			$attachment_ids = $product->get_gallery_image_ids();
			$columns        = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

			$newhtml = '';
			$loop    = 0;
			$newhtml = '<div class="thumbnails columns-' . $columns . '">';

			//Featured Video
			if (1 == $afpv_enable_featured_video && 'yes' == $afpv_enable_featured_video_product_page) {
				if ('youtube' == $video_type ) {

					$vid_link = 'https://www.youtube.com/embed/' . esc_attr($yt_video_id) . '?rel=' . esc_attr(get_option('pv_gallery_enable_tp_show_related')) . '&controls=1&showinfo=0&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));
				} elseif ( 'facebook' == $video_type ) {

					$vid_link = '//www.facebook.com/plugins/video.php?href=' . esc_attr( $fb_video_id) . '&show_text=false&appId&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play'));
				} elseif ( 'dailymotion' == $video_type ) {

					$vid_link = 'https://www.dailymotion.com/embed/video/' . esc_attr($dm_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));
				} elseif ( 'vimeo' == $video_type ) {

					$vid_link = 'https://player.vimeo.com/video/' . esc_attr($vm_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&muted=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&portrait=0&byline=0&title=0';
				} elseif ( 'metacafe' ==  $video_type ) {

					$vid_link = 'http://www.metacafe.com/embed/' . esc_attr($mc_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) ;
				} elseif ( 'custom' ==  $video_type ) {

					$vid_link = esc_url($cus_video_id);
				}


				if (!empty($afpv_video_thumb)) { 

					$thumbLink = $afpv_video_thumb;
				} else {

					$thumbLink = AFPV_URL . '/images/video_icon.png';
				}
			


				$newhtml .= '<div class="flatvideos"><a href="' . esc_url($vid_link) . '" class="zoom html5lightbox" data-group="mygroup"><img src="' . esc_url($thumbLink) . '" /></a></div>';
			}


			if (!empty($attached_product_videos)) {

				foreach ($attached_product_videos as $video_id) {

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

						if ('youtube' == $afpv_product_video_type ) {

							$vid_link = 'https://www.youtube.com/embed/' . esc_attr($afpv_yt_product_video_id) . '?rel=' . esc_attr(get_option('pv_gallery_enable_tp_show_related')) . '&controls=1&showinfo=0&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));
						} elseif ( 'facebook' == $afpv_product_video_type ) {

							$vid_link = '//www.facebook.com/plugins/video.php?href=' . esc_attr( $afpv_fb_product_video_id) . '&show_text=false&appId&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play'));
						} elseif ( 'dailymotion' == $afpv_product_video_type ) {

							$vid_link = 'https://www.dailymotion.com/embed/video/' . esc_attr($afpv_dm_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute'));
						} elseif ( 'vimeo' == $afpv_product_video_type ) {

							$vid_link = 'https://player.vimeo.com/video/' . esc_attr($afpv_vm_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&muted=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) . '&portrait=0&byline=0&title=0';
						} elseif ( 'metacafe' ==  $afpv_product_video_type ) {

							$vid_link = 'http://www.metacafe.com/embed/' . esc_attr($afpv_mc_product_video_id) . '?autoplay=' . esc_attr(get_option('pv_gallery_enable_tp_auto_play')) . '&mute=' . esc_attr(get_option('pv_gallery_enable_tp_is_mute')) ;
						} elseif ( 'custom' ==  $afpv_product_video_type ) {

							$vid_link = esc_url($afpv_cus_product_video_id);
						}


						if (!empty($afpv_product_video_thumb)) { 

							$thumbLink = $afpv_product_video_thumb;
						} else {

							$thumbLink = AFPV_URL . '/images/video_icon.png';
						}

						$newhtml .= '<div class="flatvideos"><a href="' . esc_url($vid_link) . '" class="zoom html5lightbox" data-group="mygroup"><img  src="' . esc_url($thumbLink) . '" /></a></div>';
						
					}
		  
				}
			}

			$newhtml .= '</div>'; 

			echo wp_kses_post($newhtml, '', '');

		}

	}

	new Addify_Product_Videos_Front();

}





