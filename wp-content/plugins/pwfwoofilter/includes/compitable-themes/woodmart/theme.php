<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'pwf_woodmart_theme_before_doing_ajax' ) ) {
	function pwf_woodmart_theme_before_doing_ajax() {
		//$_REQUEST['woo_ajax'] = 'woo_ajax';
		add_action( 'woocommerce_before_shop_loop', 'woodmart_setup_loop', 10 );
	}

	add_action( 'pwf_before_doing_ajax', 'pwf_woodmart_theme_before_doing_ajax', 10 );
}

if ( ! function_exists( 'pwf_woodmart_theme_js_code' ) ) {
	function pwf_woodmart_theme_js_code() {
		?>
	<script type="text/javascript">
		(function( $ ) {
			"use strict";

			$('body').removeClass('woodmart-ajax-shop-on');
			$(document).on('click', '.wd-shop-tools a', function( event ) {
				if ( $(this).hasClass('per-page-variation') ) {
					event.preventDefault();
					let perPage = $(this).find('span').text();
					$(this).closest('.woodmart-products-per-page').find('a').removeClass('current-variation');
					$(this).addClass('current-variation');
					$( document.body ).trigger('pwfTriggerPostPerPage', perPage );
				}
			});

			$('.woocommerce-ordering').on('change', 'select.orderby', function( event ) {
				event.preventDefault();
			});

			$( document.body ).on( "pwf_filter_js_ajax_done", function() {
				let pwfFilterSetting  = pwffilterVariables.filter_setting;
				let productsContainer = pwfFilterSetting.products_container_selector;
				let products          = $(productsContainer).find('.product');
				$(products).each(function(){
					let $el = $(this);
					var heightHideInfo = $el.find('.fade-in-block').outerHeight();

					$el.find('.content-product-imagin').css({
						marginBottom: -heightHideInfo
					});

					$el.addClass('hover-ready');
				});

			});
		})(jQuery);
	</script>
		<?php
	}

	add_action( 'wp_footer', 'pwf_woodmart_theme_js_code', 500 );
}
