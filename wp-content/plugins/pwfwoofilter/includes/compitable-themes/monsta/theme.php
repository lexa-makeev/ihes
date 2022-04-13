<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'pwf_monsta_theme_before_shop_loop' ) ) {
	function pwf_monsta_theme_before_shop_loop() {
		global $woocommerce_loop;

		$monsta_opt = get_option( 'monsta_opt' );

		if ( isset( $monsta_opt['shop_layout'] ) && '' !== $monsta_opt['shop_layout'] ) {
			$shoplayout = $monsta_opt['shop_layout'];
		}
		if ( isset( $_GET['layout'] ) && '' !== $_GET['layout'] ){
			$shoplayout = $_GET['layout'];
		}

		switch ( $shoplayout ) {
			case 'fullwidth':
				Monsta_Class::monsta_shop_class( 'shop-fullwidth' );
				$productcols = 4;
				break;
			default:
				Monsta_Class::monsta_shop_class( 'shop-sidebar' );
				$productcols = 3;
		}

		$monsta_viewmode             = Monsta_Class::monsta_show_view_mode();
		$woocommerce_loop['columns'] = $productcols;
		$woocommerce_loop['loop']    = 0;
	}

	add_action( 'pwf_before_shop_loop', 'pwf_monsta_theme_before_shop_loop', 10 );
}

if ( ! function_exists( 'pwf_monsta_theme_customize_product_template' ) ) {
	function pwf_monsta_theme_customize_product_template( $template, $filter_id ) {
		$template = array(
			'content',
			'product-archive',
		);

		return $template;
	}

	add_filter( 'pwf_woo_filter_product_loop_template', 'pwf_monsta_theme_customize_product_template', 10, 2 );
}
