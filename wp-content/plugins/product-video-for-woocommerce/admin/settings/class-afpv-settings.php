<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Addify_Product_Video_Setting_Tab', false ) ) {
	return new Addify_Product_Video_Setting_Tab();
}

class Addify_Product_Video_Setting_Tab extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'afpv_settings_tab';
		$this->label = __( 'Product Videos', 'addify_videos' );

		parent::__construct();
	}


	public function get_own_sections() {
		return array(
			''             			  			 => __( 'General', 'addify_videos' ),
			'afpv_self_hosted_featured_video'    => __( 'Self Hosted (Featured Video)', 'addify_videos' ),
			'afpv_third_party_featured_video'	 => __( 'Third Party (Featured Video)', 'addify_videos' ),
			'afpv_third_party_video_gallery'     => __( 'Third Party (Video Gallery)', 'addify_videos' ),
			'afpv_ele_divi_comp' 		  		 => __( 'Elementor / Divi Builder', 'addify_videos' ),
		);
	}


		/**
	 * Get settings for the detault section.
	 *
	 * @return array
	 */
	protected function get_settings_for_default_section() {
		$settings =
			array(
				array(
					'title' => __( 'General', 'addify_videos' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'custom_woo_gallery_setting',
				),

				array(
					'title'    => __( 'Gallery Template', 'addify_videos' ),
					'desc'     => __( 'Select gallery template.', 'addify_videos' ),
					'id'       => 'pv_select_gallery_template_option',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => 'html5_lightbox_template',
					'type'     => 'select',
					'options'  => array(
						'html5_lightbox_template'  => __( 'HTML5 LightBox Gallery Template', 'addify_videos' ),
						'woo_gallery_template'   => __( 'Custom Woo Gallery Template', 'addify_videos' ),
					),
				),

				array(
					'title'    => __( 'Gallery Position', 'addify_videos' ),
					'desc'     => __( 'Select gallery thumbnail positions for Custom Woo Gallery', 'addify_videos' ),
					'id'       => 'pv_gallery_thumbnail_position',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => 'pv_gallery_thumbnail_bottom_position',
					'type'     => 'select',
					'options'  => array(
						'pv_gallery_thumbnail_top_position'  => __( 'Top', 'addify_videos' ),
						'pv_gallery_thumbnail_bottom_position'     => __( 'Bottom', 'addify_videos' ),
						'pv_gallery_thumbnail_left_position'  => __( 'Left', 'addify_videos' ),
						'pv_gallery_thumbnail_right_position'     => __( 'Right', 'addify_videos' ),
					),
				),

				array(
					'title'    => __( 'Play Icon', 'addify_videos' ),
					'desc'     => __( 'Select icon to display on thumbnail.', 'addify_videos' ),
					'id'       => 'pv_play_icon',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => 'afpv_play_icon_1',
					'type'     => 'select',
					'options'  => array(
						'afpv_play_icon_1'  => __( 'Style 1', 'addify_videos' ),
						'afpv_play_icon_2'  => __( 'Style 2', 'addify_videos' ),
						'afpv_play_icon_3'  => __( 'Style 3', 'addify_videos' ),
						'afpv_play_icon_7'  => __( 'Style 4', 'addify_videos' ),
						'afpv_play_icon_8'  => __( 'Style 5', 'addify_videos' ),
					),
				),

				array(
					'title'             => __( 'Thumbnails to show', 'addify_videos' ),
					'desc'              => __( 'Enter no of thumbnails to show in Custom Woo Gallery.', 'addify_videos' ),
					'id'                => 'pv_gallery_thumbnail_to_show',
					'type'              => 'number',
					'css'               => '',
					'placeholder' 	    => __( 'Enter some value', 'addify_videos' ),
					'custom_attributes' => array(
						'min'  => 2,
						'step' => 1,
					),
					'default'           => '4',
					'autoload'          => false,
					'class'             => 'pv_gallery_thumbnail_to_show',
				),

				array(
					'title'         => __( 'Autoplay', 'addify_videos' ),
					'desc'          => __( 'Gallery thumbnail autoplay option.', 'addify_videos' ),
					'id'            => 'pv_autoplay_gallery',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'         => __( 'Arrows', 'addify_videos' ),
					'desc'          => __( 'Enable arrows controls.', 'addify_videos' ),
					'id'            => 'pv_arrows_gallery_controller',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'         => __( 'Dots', 'addify_videos' ),
					'desc'          => __( 'Enable Dot navigation.', 'addify_videos' ),
					'id'            => 'pv_dots_gallery_controller',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'custom_woo_gallery_setting',
				),
			);

		$settings = apply_filters( 'woocommerce_products_general_settings', $settings );
		return apply_filters( 'woocommerce_product_settings', $settings );
	}

	/**
	 * Get settings for the Third Party Features Gallery section.
	 *
	 * @return array
	 */
	protected function get_settings_for_afpv_self_hosted_featured_video_section() {
		
		$settings =
			array(
				array(
					'title' => __( 'Self Hosted Product Featured Video', 'addify_videos' ),
					'type'  => 'title',
					'desc'  =>  __( 'Third Party Feature Videos Settings of Custom Woo Gallery Template also set from this tab.', 'addify_videos' ),
					'id'    => 'self_hosted_featured_video',
				),


				array(
					'title'    => __( 'Auto Play Video', 'addify_videos' ),
					'desc'     => __( 'Allow or Disallow auto play of featured video.', 'addify_videos' ),
					'id'       => 'pv_featured_enable_auto_play',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
				),

				array(
					'title'    => __( 'Is Loop', 'addify_videos' ),
					'desc'     => __( 'Allow or Disallow play featured video in loop.', 'addify_videos' ),
					'id'       => 'pv_featured_enable_is_loop',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
				),

				array(
					'title'    => __( 'Mute Video', 'addify_videos' ),
					'desc'     => __( 'If this option is set to yes, video is muted by default.', 'addify_videos' ),
					'id'       => 'pv_featured_enable_is_mute',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
				),

				array(
					'title'    => __( 'Show Video Controls', 'addify_videos' ),
					'desc'     => __( 'Show / Hide video controls on the video.', 'addify_videos' ),
					'id'       => 'pv_featured_enable_video_controls',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
				),

				array(
					'title'             => __( 'Video/Thumbnail Width ( Shop Page )', 'addify_videos' ),
					'desc'              => __( 'Width of the featured video or video thumbnail on shop page.', 'addify_videos' ),
					'id'                => 'pv_featured_video_width_shop_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_featured_video_width_shop_page',
				),

				array(
					'title'             => __( 'Video/Thumbnail Height ( Shop Page )', 'addify_videos' ),
					'desc'              => __( 'Height of the featured video or video thumbnail on shop page.', 'addify_videos' ),
					'id'                => 'pv_featured_video_height_shop_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_featured_video_height_shop_page',
				),

				array(
					'title'             => __( 'Video/Thumbnail Width ( Product Page )', 'addify_videos' ),
					'desc'              => __( 'Width of the featured video or video thumbnail on product page.', 'addify_videos' ),
					'id'                => 'pv_featured_video_width_product_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_featured_video_width_product_page',
				),

				array(
					'title'             => __( 'Video/Thumbnail Height ( Product Page )', 'addify_videos' ),
					'desc'              => __( 'Height of the featured video or video thumbnail on product page.', 'addify_videos' ),
					'id'                => 'pv_featured_video_height_product_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_featured_video_height_product_page',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'self_hosted_featured_video',
				),
			);

		return apply_filters( 'afpv_self_hosted_featured_video_settings', $settings );
	}


	/**
	 * Get settings for the Third Party Features Gallery section.
	 *
	 * @return array
	 */
	protected function get_settings_for_afpv_third_party_featured_video_section() {
		
		$settings =
			array(
				array(
					'title' => __( 'Third Party Product Featured Video', 'addify_videos' ),
					'type'  => 'title',
					'desc'  => 'Third Party Feature Videos Settings of Custom Woo Gallery Template also set from this tab.',
					'id'    => 'third_party_featured_video',
				),


				array(
					'title'    => __( 'Auto Play Video', 'addify_videos' ),
					'desc'     => __( 'Allow or Disallow auto play of gallery video when open in popup. Some browsers might block this (https://developers.google.com/web/updates/2017/09/autoplay-policy-changes)', 'addify_videos' ),
					'id'       => 'pv_featured_enable_tp_auto_play',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Allow Full Screen', 'addify_videos' ),
					'desc'     => __( 'Allow or Disallow full screen play of gallery video when open in popup. (Subject to applicability)', 'addify_videos' ),
					'id'       => 'pv_featured_enable_tp_allow_full',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Mute Video', 'addify_videos' ),
					'desc'     => __( 'Mute gallery video when open in popup. (Subject to applicability)', 'addify_videos' ),
					'id'       => 'pv_featured_enable_tp_is_mute',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Show Related Videos', 'addify_videos' ),
					'desc'     => __( 'Show related videos. (Subject to applicability)', 'addify_videos' ),
					'id'       => 'pv_featured_enable_tp_show_related',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'             => __( 'Video/Thumbnail Width ( Shop Page )', 'addify_videos' ),
					'desc'              => __( 'Width of the featured video or video thumbnail on shop page.', 'addify_videos' ),
					'id'                => 'pv_featured_tp_video_width_shop_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_featured_tp_video_width_shop_page',
				),

				array(
					'title'             => __( 'Video/Thumbnail Height ( Shop Page )', 'addify_videos' ),
					'desc'              => __( 'Height of the featured video or video thumbnail on shop page.', 'addify_videos' ),
					'id'                => 'pv_featured_tp_video_height_shop_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_featured_tp_video_height_shop_page',
				),

				array(
					'title'             => __( 'Video/Thumbnail Width ( Product Page )', 'addify_videos' ),
					'desc'              => __( 'Width of the featured video or video thumbnail on product page.', 'addify_videos' ),
					'id'                => 'pv_featured_tp_video_width_product_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_featured_tp_video_width_product_page',
				),

				array(
					'title'             => __( 'Video/Thumbnail Height ( Product Page )', 'addify_videos' ),
					'desc'              => __( 'Height of the featured video or video thumbnail on product page.', 'addify_videos' ),
					'id'                => 'pv_featured_tp_video_height_product_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_featured_tp_video_height_product_page',
				),

				array(
					'title'             => __( 'Shop Page Facebook Video Width', 'addify_videos' ),
					'desc'              => __( 'Width of the Facebook Feature videos for shop page.', 'addify_videos' ),
					'id'                => 'pv_fb_featured_video_width_for_shop_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_fb_featured_video_width_for_shop_page',
				),

				array(
					'title'             => __( 'Product Page Facebook Video Width', 'addify_videos' ),
					'desc'              => __( 'Width of the Facebook Feature videos for product page.', 'addify_videos' ),
					'id'                => 'pv_fb_featured_video_width_for_product_page',
					'type'              => 'number',
					'css'               => 'width: 200px;',
					'autoload'          => false,
					'class'             => 'pv_fb_featured_video_width_for_product_page',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'third_party_featured_video',
				),
			);

		return apply_filters( 'afpv_third_party_featured_video_settings', $settings );
	}


	/**
	 * Get settings for the Third Party Video Gallery section.
	 *
	 * @return array
	 */
	protected function get_settings_for_afpv_third_party_video_gallery_section() {
		
		$settings =
			array(
				array(
					'title' => __( 'Third Party Product Gallery Video', 'addify_videos' ),
					'type'  => 'title',
					'desc'  => 'This setting is apply on HTML5 Lighbox Gallery.',
					'id'    => 'third_party_video_gallery',
				),


				array(
					'title'    => __( 'Auto Play Video', 'addify_videos' ),
					'desc'     => __( 'Allow or Disallow auto play of gallery video when open in popup. Some browsers might block this (https://developers.google.com/web/updates/2017/09/autoplay-policy-changes)', 'addify_videos' ),
					'id'       => 'pv_gallery_enable_tp_auto_play',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Allow Full Screen', 'addify_videos' ),
					'desc'     => __( 'Allow or Disallow full screen play of gallery video when open in popup. (Subject to applicability)', 'addify_videos' ),
					'id'       => 'pv_gallery_enable_tp_allow_full',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Mute Video', 'addify_videos' ),
					'desc'     => __( 'Mute gallery video when open in popup. (Subject to applicability)', 'addify_videos' ),
					'id'       => 'pv_gallery_enable_tp_is_mute',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Show Related Videos', 'addify_videos' ),
					'desc'     => __( 'Show related videos. (Subject to applicability)', 'addify_videos' ),
					'id'       => 'pv_gallery_enable_tp_show_related',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '1',
					'type'     => 'select',
					'options'  => array(
						'1'  => __( 'Yes', 'addify_videos' ),
						'0'   => __( 'No', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'third_party_video_gallery',
				),
			);

		return apply_filters( 'afpv_third_party_video_gallery_settings', $settings );
	}


	/**
	 * Get settings for the Elementor & Divi Builder section.
	 *
	 * @return array
	 */
	protected function get_settings_for_afpv_ele_divi_comp_section() {
		
		$settings =
			array(
				array(
					'title' => __( 'Elementor / Divi Builder Edited Page', 'addify_videos' ),
					'type'  => 'title',
					'desc'  => 'This setting is apply on HTML5 Lighbox Gallery.',
					'id'    => 'elementor_divi_options',
				),


				array(
					'title'    => __( 'Page Edited by Elementor or Divi Builder', 'addify_videos' ),
					'desc'     => __( 'Select yes if your Product page is Edited with Elementor or Divi Builder.', 'addify_videos' ),
					'id'       => 'pv_product_page_edited',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:250px;',
					'default'  => '0',
					'type'     => 'select',
					'options'  => array(
						'0'  => __( 'No', 'addify_videos' ),
						'1'   => __( 'Yes', 'addify_videos' ),
					),
					'desc_tip' => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'elementor_divi_options',
				),
			);

		return apply_filters( 'afpv_ele_divi_comp_settings', $settings );
	}
}

Addify_Product_Video_Setting_Tab::init();
