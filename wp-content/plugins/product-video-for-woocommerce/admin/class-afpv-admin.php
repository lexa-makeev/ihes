<?php 

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( !class_exists( 'Addify_Product_Videos_Admin' ) ) {

	class Addify_Product_Videos_Admin extends Addify_Product_Videos {

		public function __construct() {

			add_action( 'admin_enqueue_scripts', array( $this, 'afpv_admin_scripts' ) );

			add_action( 'add_meta_boxes', array( $this, 'afpv_vidoes_metaboxes' ), 10 );

			add_action( 'save_post', array($this, 'afpv_meta_box_save' ));

			add_action( 'wp_ajax_afpv_search_product', array( $this, 'afpv_apply_product' ) );

			add_filter( 'manage_af_product_videos_posts_columns', array( $this, 'afpv_custom_columns' ) );

			add_action( 'manage_af_product_videos_posts_custom_column' , array($this, 'afpv_custom_column'), 10, 2 );

			add_action('admin_menu', array($this, 'afpv_add_menu_item_to_woo_cb'));

			add_action('woocommerce_get_settings_pages', array($this, 'afpv_add_setting_page'));

		}

		public function afpv_admin_scripts() {

			$screen = get_current_screen();

			if ('af_product_videos_page_afpv-settings' == $screen->id || 'edit-af_product_videos' == $screen->id || 'af_product_videos' == $screen->id || 'product' == $screen->id) {
			
				wp_enqueue_style( 'afpv-admin', plugins_url( '/css/afpv_admin.css', __FILE__ ), '1.0.0', true  );
				wp_enqueue_style( 'select2', plugins_url( '/css/select2.css', __FILE__ ), '1.0.0', true  );
				wp_enqueue_script( 'select3', plugins_url( '/js/select2.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
				wp_enqueue_script( 'afpv-admin', plugins_url( '/js/afpv_admin.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
				wp_enqueue_style('thickbox');
				wp_enqueue_script('thickbox');
				wp_enqueue_script('media-upload'); 
				wp_enqueue_media();

				$addify_mini_cart_ajax_data = array(
				'admin_url' => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'afpv_setting_nonce' ),
				);
				wp_localize_script( 'afpv-admin', 'k_php_var', $addify_mini_cart_ajax_data );
			}
			
		}

		public function afpv_add_menu_item_to_woo_cb() {
			add_submenu_page(
				'woocommerce',
				__('Product Videos', 'addify_videos'),
				__('Product Videos', 'addify_videos'),
				'manage_options',
				'edit.php?post_type=af_product_videos',
				''
			);
		}

	
		public function afpv_add_setting_page( $settings ) {
			$settings[] = include_once AFPV_PLUGIN_DIR . '/admin/settings/class-afpv-settings.php';
			return $settings;
		}


		public function afpv_custom_columns( $columns) {

			unset($columns['date']);
			$columns['afpv_video_type'] = esc_html__( 'Video Type', 'addify_videos' );
			$columns['date']            = esc_html__( 'Date Published', 'addify_videos' );

			return $columns;
		}

		public function afpv_custom_column( $column, $post_id ) {
			$afrfq_post = get_post($post_id);
			switch ( $column ) {
				case 'afpv_video_type':
					$afpv_video_type = get_post_meta($post_id, 'afpv_product_video_type', true);
					
					echo esc_attr( $afpv_video_type );

					break;
			}
		}


		public function pv_product_page_edited_callback( $args ) { 
			?>
		   
			<select id="pv_product_page_edited" class="login_title" name="pv_product_page_edited">
				
				<option value="0" 
				<?php 
				if (0 === get_option('pv_product_page_edited')) {
					echo 'selected';
				} 
				?>
				><?php echo esc_html__('No', 'addify_videos'); ?></option>
				<option value="1"  
				<?php 
				if (1 == get_option('pv_product_page_edited')) {
					echo 'selected';
				} 
				?>
				><?php echo esc_html__('Yes', 'addify_videos'); ?></option>
			</select>
			<p class="description pv_gallery_enable_tp_show_related"> <?php echo esc_attr($args[0]); ?> </p>
			
			<?php      
		} // end afpv_gallery_enable_tp_show_related_callback 



		

		public function afpv_vidoes_metaboxes() {

			add_meta_box( 'afpv_featured_video', esc_html__( 'Featured Video', 'addify_videos' ), array( $this, 'afpv_featured_video_callback' ), 'product', 'normal', 'high' );

			add_meta_box( 'afpv_product_video', esc_html__( 'Video', 'addify_videos' ), array( $this, 'afpv_product_video_callback' ), 'af_product_videos', 'normal', 'high' );
			
		}

		public function afpv_featured_video_callback() { 

			global $post;
			wp_nonce_field( basename( __FILE__ ), 'afpv_nonce_field' );
			$afpv_enable_featured_video              = get_post_meta( intval($post->ID), 'afpv_enable_featured_video', true );
			$afpv_enable_featured_video_shop_page    = get_post_meta( intval($post->ID), 'afpv_enable_featured_video_shop_page', true );
			$afpv_enable_featured_video_product_page = get_post_meta( intval($post->ID), 'afpv_enable_featured_video_product_page', true );
			$afpv_enable_featured_image_as_first_img = get_post_meta( intval($post->ID), 'afpv_enable_featured_image_as_first_img', true );
			$afpv_featured_video_type                = get_post_meta( intval($post->ID), 'afpv_featured_video_type', true );
			$afpv_yt_featured_video_id               = get_post_meta( intval($post->ID), 'afpv_yt_featured_video_id', true );
			$afpv_fb_featured_video_id               = get_post_meta( intval($post->ID), 'afpv_fb_featured_video_id', true );
			$afpv_dm_featured_video_id               = get_post_meta( intval($post->ID), 'afpv_dm_featured_video_id', true );
			$afpv_vm_featured_video_id               = get_post_meta( intval($post->ID), 'afpv_vm_featured_video_id', true );
			$afpv_mc_featured_video_id               = get_post_meta( intval($post->ID), 'afpv_mc_featured_video_id', true );
			$afpv_cus_featured_video_id              = get_post_meta( intval($post->ID), 'afpv_cus_featured_video_id', true );
			$afpv_video_thumb                        = get_post_meta( intval($post->ID), 'afpv_video_thumb', true );

			?>
			<input type="hidden" name="afpv_hidden_flag" value="true" />
			<div class="meta_field_full">
				
				<label for="afpv_enable_featured_video"><?php echo esc_html__('Enable Featured Video', 'addify_videos'); ?></label>
				<select name="afpv_enable_featured_video" id="afpv_enable_featured_video" class="afpv_field_select">
					<option value="1" <?php echo selected(esc_attr($afpv_enable_featured_video), '1'); ?>><?php echo esc_html__('Yes', 'addify_videos'); ?></option>
					<option value="0" <?php echo selected(esc_attr($afpv_enable_featured_video), '0'); ?>><?php echo esc_html__('No', 'addify_videos'); ?></option>
				</select>
				<p><?php echo esc_html__('If featured video is enabled then featured image will be replaced with featured video.', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full">
				
				<label for="afpv_enable_featured_video_shop_page"><?php echo esc_html__('Enable Featured Video On Shop Page', 'addify_videos'); ?></label>
				<p>
				<input type="checkbox" name="afpv_enable_featured_video_shop_page" value="yes" <?php echo checked(esc_attr($afpv_enable_featured_video_shop_page), 'yes'); ?> >
				<?php echo esc_html__('Check this option if you want to enable featured video on shop page, this will replace featured image with featured video.', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full">
				
				<label for="afpv_enable_featured_video_product_page"><?php echo esc_html__('Enable Featured Video On Product Page', 'addify_videos'); ?></label>
				<p>
				<input type="checkbox" name="afpv_enable_featured_video_product_page" id="afpv_enable_featured_video_product_page" value="yes" <?php echo checked(esc_attr($afpv_enable_featured_video_product_page), 'yes'); ?> >
				<?php echo esc_html__('Check this option if you want to enable featured video on Product page, this will replace featured image with featured video.', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full">
				
				<label for="afpv_enable_featured_image_as_first_img"><?php echo esc_html__('Enable Featured Image as First Gallery Image', 'addify_videos'); ?></label>
				<p>
				<input type="checkbox" name="afpv_enable_featured_image_as_first_img" id="afpv_enable_featured_image_as_first_img" value="yes" <?php echo checked(esc_attr($afpv_enable_featured_image_as_first_img), 'yes'); ?> >
				<?php echo esc_html__('Check this option if you want existing featured image as first gallery image when featured video is added.', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full">
				
				<div class="imgdis" id="logodisplay">
					<?php if (!empty($afpv_video_thumb)) { ?>
					<label for="afpv_video_thumb"><?php echo esc_html__('Current Video Thumbnail', 'addify_videos'); ?></label>
					<img src="<?php echo esc_url($afpv_video_thumb); ?>" width="200" />
					<?php } ?>
				
				</div>

				<label for="afpv_video_thumb"><?php echo esc_html__('Video Thumbnail', 'addify_videos'); ?></label>
				<input type="hidden" value="<?php echo esc_url($afpv_video_thumb); ?>" name="afpv_video_thumb" id="afpv_thumb_url" class="login_title">
				<input onClick="afpv_image()" type="button" name="upload-btn" id="upload-image-btn" class="button-secondary" value="<?php echo esc_html__('Upload Image', 'addify_videos'); ?>">
				<input onClick="afpv_clear_image()" type="button" name="upload-btn" id="clear-image-btn" class="button-secondary" value="<?php echo esc_html__('Remove Image', 'addify_videos'); ?>">
				<p><?php echo esc_html__('If thumbnail is added for the video then video is played in popup when click on this thumbnail.', 'addify_videos'); ?></p>
				
			</div>


			<div class="meta_field_full">
				<label for="afpv_featured_video_type"><?php echo esc_html__('Featured Video Type', 'addify_videos'); ?></label>
				<select name="afpv_featured_video_type" id="afpv_featured_video_type" class="afpv_field_select" onchange="getVideoType(this.value)">
					<option value="youtube" <?php echo selected(esc_attr($afpv_featured_video_type), 'youtube'); ?>><?php echo esc_html__('YouTube', 'addify_videos'); ?></option>
					<option value="facebook" <?php echo selected(esc_attr($afpv_featured_video_type), 'facebook'); ?>><?php echo esc_html__('Facebook', 'addify_videos'); ?></option>
					<option value="dailymotion" <?php echo selected(esc_attr($afpv_featured_video_type), 'dailymotion'); ?>><?php echo esc_html__('Dailymotion', 'addify_videos'); ?></option>
					<option value="vimeo" <?php echo selected(esc_attr($afpv_featured_video_type), 'vimeo'); ?>><?php echo esc_html__('Vimeo', 'addify_videos'); ?></option>
					<option value="metacafe" <?php echo selected(esc_attr($afpv_featured_video_type), 'metacafe'); ?>><?php echo esc_html__('Metacafe', 'addify_videos'); ?></option>
					<option value="custom" <?php echo selected(esc_attr($afpv_featured_video_type), 'custom'); ?>><?php echo esc_html__('Custom Upload', 'addify_videos'); ?></option>
					
				</select>
			</div>

			<div class="meta_field_full" id="youtube">
				<label for="afpv_yt_featured_video_id"><?php echo esc_html__('YouTube Video ID', 'addify_videos'); ?></label>
				<input type="text" name="afpv_yt_featured_video_id" id="afpv_yt_featured_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_yt_featured_video_id); ?>" />
				<p><?php echo esc_html__('Add your YouTube video ID like (6lt2JfJdGSY). Do not put complete video URL only enter video ID that come after this URL (https://www.youtube.com/watch?v=)', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full" id="facebook">
				<label for="afpv_fb_featured_video_id"><?php echo esc_html__('Facebook Video URL', 'addify_videos'); ?></label>
				<input type="text" name="afpv_fb_featured_video_id" id="afpv_fb_featured_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_fb_featured_video_id); ?>" />
				<p><?php echo esc_html__('Add facebook video link here, e.g (https://www.facebook.com/facebook/videos/XXXXXXXXXXXXX)', 'addify_videos'); ?></p>

			</div>

			<div class="meta_field_full" id="dailymotion">
				<label for="afpv_dm_featured_video_id"><?php echo esc_html__('Dailymotion Video ID', 'addify_videos'); ?></label>
				<input type="text" name="afpv_dm_featured_video_id" id="afpv_dm_featured_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_dm_featured_video_id); ?>" />
				<p><?php echo esc_html__('Add dailymotion video id here e.g (x5z1gzv). Do not put complete video URL only enter video ID that come after this URL (https://www.dailymotion.com/embed/video/)', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full" id="vimeo">
				<label for="afpv_vm_featured_video_id"><?php echo esc_html__('Vimeo Video ID', 'addify_videos'); ?></label>
				<input type="text" name="afpv_vm_featured_video_id" id="afpv_vm_featured_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_vm_featured_video_id); ?>" />
				<p><?php echo esc_html__('Add vimeo video id here e.g (217936008). Do not put complete video URL only enter video ID that come after this URL (https://vimeo.com/)', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full" id="metacafe">
				<label for="afpv_mc_featured_video_id"><?php echo esc_html__('Metacafe Video ID', 'addify_videos'); ?></label>
				<input type="text" name="afpv_mc_featured_video_id" id="afpv_mc_featured_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_mc_featured_video_id); ?>" />
				<p><?php echo esc_html__('Add metacafe video id here e.g (11858937/top-benefits-of-woocommerce/). Do not put complete video URL only enter video ID that come after this URL (http://www.metacafe.com/watch/)', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full" id="custom">
				
				<div class="imgdis" id="afpv-videp-id">
					<?php if (!empty($afpv_cus_featured_video_id)) { ?>
					<label for="afpv_cus_featured_video_id"><?php echo esc_html__('Current Video', 'addify_videos'); ?></label>
					<video  frameborder="0" controls width="500">
						<source src="<?php echo esc_url($afpv_cus_featured_video_id); ?>" type="video/mp4">
					</video>
					<?php } else { ?>

						<video  frameborder="0" controls width="500" class="afpv-custom-vid-product-level">
							<source src="" type="video/mp4">
						</video>

					<?php } ?>
				</div>

				<label for="afpv_cus_featured_video_id"><?php echo esc_html__('Custom Video', 'addify_videos'); ?></label>
				<input onClick="afpv_video()"   type="button" name="upload-btn" id="upload-video-btn" class="button-secondary" value="<?php echo esc_html__('Upload Video', 'addify_videos'); ?>">
				<input type="text" value="<?php echo esc_url($afpv_cus_featured_video_id); ?>" name="afpv_cus_featured_video_id" id="afpv_video_url" class="login_title" readonly>
				
			</div>

			<?php
		}

		public function afpv_meta_box_save( $post_id ) {


			// Checks save status - overcome autosave, etc.
			$is_autosave    = wp_is_post_autosave( $post_id );
			$is_revision    = wp_is_post_revision( $post_id );
			$is_valid_nonce = ( isset( $_POST[ 'afpv_nonce_field' ] ) && wp_verify_nonce( sanitize_text_field($_POST[ 'afpv_nonce_field' ]), basename( __FILE__ ) ) ) ? 'true' : 'false';
		 
			// Exits script depending on save status
			if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
				return;
			}

			if (isset($_REQUEST['action']) && 'woocommerce_do_ajax_product_import' == $_REQUEST['action']) {

				return;
			}
			
		   
			if (isset($_POST['afpv_hidden_flag'])) {

				if ( isset( $_POST['afpv_enable_featured_video'] ) ) { 

					
					update_post_meta( intval($post_id), 'afpv_enable_featured_video', sanitize_text_field( $_POST['afpv_enable_featured_video'] ) );
				}

				if ( isset( $_POST['afpv_enable_featured_video_shop_page'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_enable_featured_video_shop_page', sanitize_text_field( $_POST['afpv_enable_featured_video_shop_page'] ) );
				} else {

					delete_post_meta( intval($post_id), 'afpv_enable_featured_video_shop_page');   
				}

				if ( isset( $_POST['afpv_enable_featured_video_product_page'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_enable_featured_video_product_page', sanitize_text_field( $_POST['afpv_enable_featured_video_product_page'] ) );
				} else {

					delete_post_meta( intval($post_id), 'afpv_enable_featured_video_product_page' );   
				}

				if ( isset( $_POST['afpv_enable_featured_image_as_first_img'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_enable_featured_image_as_first_img', sanitize_text_field( $_POST['afpv_enable_featured_image_as_first_img'] ) );
				} else {

					delete_post_meta( intval($post_id), 'afpv_enable_featured_image_as_first_img' );   
				}	

				if ( isset( $_POST['afpv_video_thumb'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_video_thumb', sanitize_text_field( $_POST['afpv_video_thumb'] ) );
				}

				if ( isset( $_POST['afpv_featured_video_type'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_featured_video_type', sanitize_text_field( $_POST['afpv_featured_video_type'] ) );
				}

				//YouTube
				if ( isset( $_POST['afpv_yt_featured_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_yt_featured_video_id', sanitize_text_field( $_POST['afpv_yt_featured_video_id'] ) );
				}

				//Faebook
				if ( isset( $_POST['afpv_fb_featured_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_fb_featured_video_id', sanitize_text_field( $_POST['afpv_fb_featured_video_id'] ) );
				}

				//Dailymotion
				if ( isset( $_POST['afpv_dm_featured_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_dm_featured_video_id', sanitize_text_field( $_POST['afpv_dm_featured_video_id'] ) );
				}

				//Vimeo
				if ( isset( $_POST['afpv_vm_featured_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_vm_featured_video_id', sanitize_text_field( $_POST['afpv_vm_featured_video_id'] ) );
				}

				//Metacafe
				if ( isset( $_POST['afpv_mc_featured_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_mc_featured_video_id', sanitize_text_field( $_POST['afpv_mc_featured_video_id'] ) );
				}

				//Custom
				if ( isset( $_POST['afpv_cus_featured_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_cus_featured_video_id', sanitize_text_field( $_POST['afpv_cus_featured_video_id'] ) );
				}

				//Product Video Gallery


				if ( isset( $_POST['afpv_product_video_thumb'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_product_video_thumb', sanitize_text_field( $_POST['afpv_product_video_thumb'] ) );
				}

				if ( isset( $_POST['afpv_product_video_type'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_product_video_type', sanitize_text_field( $_POST['afpv_product_video_type'] ) );
				}

				//YouTube
				if ( isset( $_POST['afpv_yt_product_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_yt_product_video_id', sanitize_text_field( $_POST['afpv_yt_product_video_id'] ) );
				}

				//Faebook
				if ( isset( $_POST['afpv_fb_product_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_fb_product_video_id', sanitize_text_field( $_POST['afpv_fb_product_video_id'] ) );
				}

				//Dailymotion
				if ( isset( $_POST['afpv_dm_product_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_dm_product_video_id', sanitize_text_field( $_POST['afpv_dm_product_video_id'] ) );
				}

				//Vimeo
				if ( isset( $_POST['afpv_vm_product_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_vm_product_video_id', sanitize_text_field( $_POST['afpv_vm_product_video_id'] ) );
				}

				//Metacafe
				if ( isset( $_POST['afpv_mc_product_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_mc_product_video_id', sanitize_text_field( $_POST['afpv_mc_product_video_id'] ) );
				}

				//Custom
				if ( isset( $_POST['afpv_cus_product_video_id'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_cus_product_video_id', sanitize_text_field( $_POST['afpv_cus_product_video_id'] ) );
				}

				//Applied Products
				if ( isset( $_POST['afpv_applied_products'] ) ) { 
					update_post_meta( intval($post_id), 'afpv_applied_products', serialize(sanitize_meta('afpv_applied_products', $_POST['afpv_applied_products'], '' ) ));
				} else {
					update_post_meta( $post_id, 'afpv_applied_products', array() );
				}

			}

			
		}

		public function afpv_product_video_callback() {

			global $post;
			wp_nonce_field('afpv_nonce_action', 'afpv_nonce_field');
			
			$afpv_product_video_type   = get_post_meta( intval($post->ID), 'afpv_product_video_type', true );
			$afpv_yt_product_video_id  = get_post_meta( intval($post->ID), 'afpv_yt_product_video_id', true );
			$afpv_fb_product_video_id  = get_post_meta( intval($post->ID), 'afpv_fb_product_video_id', true );
			$afpv_dm_product_video_id  = get_post_meta( intval($post->ID), 'afpv_dm_product_video_id', true );
			$afpv_vm_product_video_id  = get_post_meta( intval($post->ID), 'afpv_vm_product_video_id', true );
			$afpv_mc_product_video_id  = get_post_meta( intval($post->ID), 'afpv_mc_product_video_id', true );
			$afpv_cus_product_video_id = get_post_meta( intval($post->ID), 'afpv_cus_product_video_id', true );
			$afpv_product_video_thumb  = get_post_meta( intval($post->ID), 'afpv_product_video_thumb', true );

			if ( is_serialized( get_post_meta( intval($post->ID), 'afpv_applied_products', true ) ) ) { 

				$afpv_applied_products = unserialize( get_post_meta( intval($post->ID), 'afpv_applied_products', true ));
				
			}

			?>
			<input type="hidden" name="afpv_hidden_flag" value="true" />
			<div class="meta_field_full">
				<label for="afpv_product_video_type"><?php echo esc_html__('Product Video Type', 'addify_videos'); ?></label>
				<select name="afpv_product_video_type" id="afpv_product_video_type" class="afpv_field_select" onchange="getVideoType(this.value)">
					<option value="youtube" <?php echo selected(esc_attr($afpv_product_video_type), 'youtube'); ?>><?php echo esc_html__('YouTube', 'addify_videos'); ?></option>
					<option value="facebook" <?php echo selected(esc_attr($afpv_product_video_type), 'facebook'); ?>><?php echo esc_html__('Facebook', 'addify_videos'); ?></option>
					<option value="dailymotion" <?php echo selected(esc_attr($afpv_product_video_type), 'dailymotion'); ?>><?php echo esc_html__('Dailymotion', 'addify_videos'); ?></option>
					<option value="vimeo" <?php echo selected(esc_attr($afpv_product_video_type), 'vimeo'); ?>><?php echo esc_html__('Vimeo', 'addify_videos'); ?></option>
					<option value="metacafe" <?php echo selected(esc_attr($afpv_product_video_type), 'metacafe'); ?>><?php echo esc_html__('Metacafe', 'addify_videos'); ?></option>
					<option value="custom" <?php echo selected(esc_attr($afpv_product_video_type), 'custom'); ?>><?php echo esc_html__('Custom Upload', 'addify_videos'); ?></option>
					
				</select>
			</div>

			<div class="meta_field_full" id="youtube">
				<label for="afpv_yt_product_video_id"><?php echo esc_html__('YouTube Video ID', 'addify_videos'); ?></label>

				<input type="text" name="afpv_yt_product_video_id" id="afpv_yt_product_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_yt_product_video_id); ?>" />
				<p><?php echo esc_html__('Add your YouTube video ID like (6lt2JfJdGSY). Do not put complete video URL only enter video ID that come after this URL (https://www.youtube.com/watch?v=)', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full" id="facebook">
				<label for="afpv_fb_product_video_id"><?php echo esc_html__('Facebook Video URL', 'addify_videos'); ?></label>

				<input type="text" name="afpv_fb_product_video_id" id="afpv_fb_product_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_fb_product_video_id); ?>" />
				<p><?php echo esc_html__('Add facebook video link here, e.g (https://www.facebook.com/facebook/videos/XXXXXXXXXXXXX)', 'addify_videos'); ?></p>			
			</div>

			<div class="meta_field_full" id="dailymotion">
				<label for="afpv_dm_product_video_id"><?php echo esc_html__('Dailymotion Video ID', 'addify_videos'); ?></label>

				<input type="text" name="afpv_dm_product_video_id" id="afpv_dm_product_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_dm_product_video_id); ?>" />
				<p><?php echo esc_html__('Add dailymotion video id here e.g (x5z1gzv). Do not put complete video URL only enter video ID that come after this URL (https://www.dailymotion.com/embed/video/)', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full" id="vimeo">
				<label for="afpv_vm_product_video_id"><?php echo esc_html__('Vimeo Video ID', 'addify_videos'); ?></label>

				<input type="text" name="afpv_vm_product_video_id" id="afpv_vm_product_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_vm_product_video_id); ?>" />
				<p><?php echo esc_html__('Add vimeo video id here e.g (217936008). Do not put complete video URL only enter video ID that come after this URL (https://vimeo.com/)', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full" id="metacafe">
				<label for="afpv_mc_product_video_id"><?php echo esc_html__('Metacafe Video ID', 'addify_videos'); ?></label>

				<input type="text" name="afpv_mc_product_video_id" id="afpv_mc_product_video_id" class="afpv_field_text" value="<?php echo esc_attr($afpv_mc_product_video_id); ?>" />
				<p><?php echo esc_html__('Add metacafe video id here e.g (11858937/top-benefits-of-woocommerce/). Do not put complete video URL only enter video ID that come after this URL (http://www.metacafe.com/watch/)', 'addify_videos'); ?></p>
			</div>

			<div class="meta_field_full" id="custom">
					
				<div class="imgdis" id="afpv-videp-id">

					<?php if (!empty($afpv_cus_product_video_id)) { ?>

						<label for="afpv_cus_product_video_id"><?php echo esc_html__('Current Video', 'addify_videos'); ?></label>
						<video frameborder="0" controls width="500">
							<source src="<?php echo esc_url($afpv_cus_product_video_id); ?>" type="video/mp4">
						</video>

					<?php } else { ?>

						<video frameborder="0" controls width="500" class="afpv-custom-vid">
							<source src="" type="video/mp4">
						</video>

					<?php } ?>

				</div>
				
				<label for="afpv_cus_product_video_id"><?php echo esc_html__('Custom Video', 'addify_videos'); ?></label>
				<input onClick="afpv_video()" type="button" name="upload-btn" id="upload-video-btn" class="button-secondary" value="<?php echo esc_html__('Upload Video', 'addify_videos'); ?>">

				<input type="text" value="<?php echo esc_url($afpv_cus_product_video_id); ?>" name="afpv_cus_product_video_id" id="afpv_video_url" class="login_title" readonly>
				
				
			</div>

			<div class="meta_field_full">
				
				<div class="imgdis" id="logodisplay">
					
					<?php if (!empty($afpv_product_video_thumb)) { ?>
					<label for="afpv_product_video_thumb"><?php echo esc_html__('Current Video Thumbnail', 'addify_videos'); ?></label>
					<img src="<?php echo esc_url($afpv_product_video_thumb); ?>" width="200" />
					<?php } ?>
				
				</div>
				
				<label for="afpv_product_video_thumb"><?php echo esc_html__('Video Thumbnail', 'addify_videos'); ?></label>
				<input type="hidden" value="<?php echo esc_url($afpv_product_video_thumb); ?>" name="afpv_product_video_thumb" id="afpv_thumb_url" class="login_title">
				<input onClick="afpv_image()" type="button" name="upload-btn" id="upload-image-btn" class="button-secondary" value="<?php echo esc_html__('Upload Image', 'addify_videos'); ?>">
				<input onClick="afpv_clear_image()" type="button" name="upload-btn" id="clear-image-btn" class="button-secondary" value="<?php echo esc_html__('Remove Image', 'addify_videos'); ?>">
				<p><?php echo esc_html__('This thumbnail will be shown in the product gallery.', 'addify_videos'); ?></p>
				
			</div>

			<div class="meta_field_full">
				
				<label for="afpv_vm_product_video_id"><?php echo esc_html__('Attach With Products', 'addify_videos'); ?></label>
				
				<select class="select_box wc-enhanced-select afpv_applied_products" name="afpv_applied_products[]" id="afpv_applied_products"  multiple>

					<?php

					if ( ! empty( $afpv_applied_products ) ) {
						foreach ( $afpv_applied_products as $select_product ) {
							$prod_post = wc_get_product( $select_product );

							if (! is_object($prod_post)) {
									
								continue;
							}
							?>
								<option value="<?php echo intval( $select_product ); ?>" selected="selected"><?php echo esc_attr( $prod_post->get_name() ); ?></option>
							<?php
						}
					}
						
					?>
					
				</select>
				<p><?php echo esc_html__('Select products with which you want to attach these videos.', 'addify_videos'); ?></p>
				
			</div>


			<?php
		}


		// Select Product on admin side Callback
		public function afpv_apply_product() {

			if ( isset( $_POST['nonce'] ) && '' != $_POST['nonce'] ) {

				$nonce = sanitize_text_field( $_POST['nonce'] );

			} else {

				$nonce = 0;

			}

			if ( isset( $_POST['q'] ) && '' != $_POST['q'] ) {

				if ( ! wp_verify_nonce( $nonce, 'afpv_setting_nonce' ) ) {

					die( 'Failed ajax security check!' );

				}

				$pro = sanitize_text_field( $_POST['q'] );
				
			} else {

				$pro = '';

			}

			$data_array = array();

			$args = array(
			   'post_type'   => 'product',
			   'post_status' => 'publish',
			   'numberposts' => 50,
			   's'           => $pro,
			);
			$pros = get_posts( $args );

			if ( ! empty( $pros ) ) {
				foreach ( $pros as $proo ) {
					$title        = ( mb_strlen( $proo->post_title ) > 50 ) ? mb_substr( $proo->post_title, 0, 49 ) . '...' : $proo->post_title;
					$data_array[] = array( $proo->ID, $title );             // array( Post ID, Post Title )
				}
			}

			echo wp_json_encode( $data_array );

			die();
		}
	}

	new Addify_Product_Videos_Admin();

}
