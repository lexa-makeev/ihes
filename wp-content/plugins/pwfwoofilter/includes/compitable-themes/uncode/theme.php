<?php
defined( 'ABSPATH' ) || exit;

/**
 * uncode with shortcode Doesn't work
 * Select 2 CSS issue
 *
 * Theme develover requires to add some code insdie template content-product.php
 * $layout = apply_filters( 'uncode_content_product_layout', $layout );
 *
 * Archive template Products container selector = .isotope-container && Pagination selector .row-navigation .pagination
 * Archive template Products container selector = .isotope-container && Pagination selector .isotope-footer .pagination
 */

add_action( 'pwf_init_parse_query', 'uncode_block_content_shortcode_pwf_init_parse_query', 10, 1 );

function uncode_block_content_shortcode_pwf_init_parse_query( $filter_id ) {
	$meta = get_post_meta( absint( $filter_id ), '_pwf_woo_post_filter', true );

	if ( false === $meta ) {
		return;
	}

	$filter_items   = $meta['items'];
	$filter_setting = $meta['setting'];

	if ( isset( $filter_setting['is_shortcode'] ) && 'on' === $filter_setting['is_shortcode'] ) {
		if ( ! empty( $filter_setting['shortcode_string'] ) ) {
			$shortcode  = $filter_setting['shortcode_string'];
			$first_char = substr( $shortcode, 0, 1 );
			if ( '[' === $first_char ) {
				$shortcode = substr( $shortcode, 1 );
			}
			$last_char = substr( $shortcode, -1, 1 );
			if ( ']' === $last_char ) {
				$len       = strlen( $shortcode ) - 1;
				$shortcode = substr( $shortcode, 0, $len );
			}

			if ( 'pwfuncode' !== substr( $shortcode, 0, strlen( 'pwfuncode' ) ) ) {
				return;
			}

			$shortcode = str_replace( 'pwfuncode', '', $shortcode );
			$atts      = shortcode_parse_atts( $shortcode );
			if ( ! empty( $atts ) && is_array( $atts ) && isset( $atts['contentblockid'] ) && ! empty( $atts['contentblockid'] ) ) {
				$uncode_block = get_uncode_shortcode_block_content( $atts['contentblockid'] );
				$loop_parse   = uncode_parse_loop_data( $uncode_block['loop'] );

				if ( ! empty( $loop_parse ) && is_array( $loop_parse ) && 'product' === $loop_parse['post_type'] ) {
					global $pwf_uncode_block;

					$pwf_uncode_block = $uncode_block;

					$args = array(
						'limit'          => '',
						'columns'        => '',
						'paginate'       => false,
						'skus'           => '',
						'ids'            => '',
						'on_sale'        => false,
						'best_selling'   => false,
						'top_rated'      => false,
						'category'       => '',
						'cat_operator'   => 'IN',
						'tag'            => '',
						'tag_operator'   => 'IN',
						'attribute'      => '',
						'terms'          => '',
						'terms_operator' => 'IN',
						'visibility'     => '',
						'order'          => '',
						'orderby'        => '',
					);

					if ( isset( $pwf_uncode_block['pagination'] ) && 'yes' === $pwf_uncode_block['pagination'] ) {
						$args['paginate'] = true;
					}

					if ( isset( $loop_parse['size'] ) ) {
						$args['limit'] = absint( $loop_parse['size'] );
					}

					if ( isset( $loop_parse['order_by'] ) ) {
						$args['orderby'] = absint( $loop_parse['order_by'] );
					}

					if ( isset( $loop_parse['order'] ) ) {
						$args['order'] = absint( $loop_parse['order'] );
					}

					if ( isset( $loop_parse['tax_query'] ) && ! empty( $loop_parse['tax_query'] ) ) {
						$tax_ids = explode( ',', $loop_parse['tax_query'] );
						$terms   = get_terms( array( 'include' => array_map( 'absint', $tax_ids ) ) );

						if ( ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								if ( 'product_cat' === $term->taxonomy ) {
									if ( isset( $args['category'] ) && ! empty( $args['category'] ) ) {
										$args['category'] .= ',';
									}
									$args['category'] .= $term->slug;
								} elseif ( 'product_tag' === $term->taxonomy ) {
									if ( isset( $args['tag'] ) && ! empty( $args['tag'] ) ) {
										$args['tag'] .= ',';
									}
									$args['tag'] .= $term->slug;
								} else {
									if ( strpos( $term->taxonomy, 'pa_' ) !== false ) {
										if ( isset( $args['attribute'] ) && ! empty( $args['attribute'] ) ) {
											$args['attribute'] .= ',';
										}
										$args['attribute'] .= $term->slug;
									}
								}
							}
						}
					}

					if ( isset( $loop_parse['product_type'] ) ) {
						if ( 'on_sale' === $loop_parse['product_type'] ) {
							$args['on_sale'] = true;
						} elseif ( 'featured' === $loop_parse['product_type'] ) {
							$args['visibility'] = 'featured';
						} elseif ( 'top_rated' === $loop_parse['product_type'] ) {
							$args['top_rated'] = true;
						} elseif ( 'best_selling' === $loop_parse['product_type'] ) {
							$args['best_selling'] = true;
						}
					}

					$pwf_uncode_block['post_blocks']['uncode_product'] = uncode_flatArray( vc_sorted_list_parse_value( $pwf_uncode_block['product_items'] ) );

					$customize_shortcode = new Pwf_Integrate_Shortcode( $filter_id, $args );
					uncode_pwf_add_hooks_to_content_block();
				}
			}
		}
	}
}

add_action( 'pwf_before_doing_ajax', 'uncode_pwf_pwf_before_doing_ajax', 10, 1 );
add_action( 'wp_head', 'pwf_uncode_theme_css_code', 1000 );
add_action( 'wp_footer', 'pwf_uncode_theme_js_code', 1000 );
add_filter( 'pwf_woo_filter_js_variables', 'uncode_pwf_woo_filter_js_variables', 10, 1 );

function uncode_pwf_pwf_before_doing_ajax( $filter_id ) {
	$page_type = $GLOBALS['pwf_main_query']['current_page_type'];
	if ( '' !== $page_type && 'page' === $page_type && 'post' === $page_type ) {
		// this is shop archive
		$generic_body_content_block = ot_get_option( '_uncode_product_index_content_block' );
		if ( '' !== $generic_body_content_block ) {
			uncode_pwf_add_hooks_to_content_block();
		} else {
			add_filter( 'pwf_html_pagination', 'uncode_pwf_customize_pagination_default_archive_template', 10, 3 );
			add_filter( 'uncode_content_product_layout', 'uncode_pwf_content_product_layout', 10, 1 );
			add_action( 'pwf_before_shop_loop_item', 'set_query_vars', 10 );
		}
	}
}

function uncode_pwf_add_hooks_to_content_block() {
	add_filter( 'pwf_woo_filter_product_loop_template', 'uncode_pwf_loop_template', 10, 2 );
	add_filter( 'wc_get_template_part', 'uncode_pwf_change_path_loop_template', 10, 3 );
	add_filter( 'pwf_html_pagination', 'uncode_pwf_customize_pagination_for_uncode_blocks', 10, 3 );
	add_action( 'pwf_before_shop_loop', 'uncode_pwf_set_block_global_variables', 10, 1 );
	add_action( 'pwf_before_shop_loop_item', 'uncode_pwf_count_products', 10 );
}

function uncode_pwf_woo_filter_js_variables( $filter_js_variables ) {
	$generic_body_content_block = ot_get_option( '_uncode_product_index_content_block' );
	if ( ! empty( $generic_body_content_block ) ) {
		$filter_js_variables['shop_integrated'] = 'no';
	}

	return $filter_js_variables;
}

/**
 * Uncode theme uses the condition is_shop() and it doesn't work with ajax
 */
function uncode_pwf_content_product_layout( $layout ) {
	$layout['media'] = array(
		'', // featured
		'', // onpost
		'', // original
		'show-sale',
		'', // enhanced-atc
		'', // inherit-w-atc
		'', // atc-typo-default
		'', // show-atc
	);

	$layout['quick-view-button'] = array();
	$layout['wishlist-button']   = array();

	return $layout;
}
/**
 * see template archive-product.php inside unicode
 * Th main shop used default shop template
 * this code to fix isotop pagination when use UNCODE.isotopeLayout();
 */
function set_query_vars() {
	global $wp_query;
	$single_post_width = ot_get_option( '_uncode_product_index_single_width' );
	set_query_var( 'single_post_width', $single_post_width );
}

function uncode_pwf_count_products() {
	global $uncode_pwf_count;
	if ( empty( $uncode_pwf_count ) && 0 !== $uncode_pwf_count ) {
		$uncode_pwf_count = 0;
	} else {
		$uncode_pwf_count++;
	}
}

function uncode_pwf_loop_template( $custom_pwf_loop_template, $filter_id ) {
	$template = array(
		'content',
		'uncode-product-template',
	);
	return $template;
}

function uncode_pwf_change_path_loop_template( $template, $slug, $name ) {
	if ( 'uncode-product-template' === $name ) {
		$template = PWF_WOO_FILTER_DIR . 'includes/compitable-themes/uncode/' . $slug . '-' . $name . '.php';
	}

	return $template;
}

if ( ! function_exists( 'uncode_pwf_customize_pagination_for_uncode_blocks' ) ) {
	function uncode_pwf_customize_pagination_for_uncode_blocks( $output, $filter_id, $args ) {

		$total   = $args['total'];
		$current = $args['current'];
		$base    = $args['base'];
		$format  = '';

		$paginate_links = paginate_links(
			apply_filters(
				'woocommerce_pagination_args',
				array(
					'base'      => $base,
					'format'    => $format,
					'current'   => max( 1, $current ),
					'total'     => $total,
					'show_all'  => false,
					'prev_next' => false,
					'type'      => 'array',
				)
			)
		);

		if ( is_array( $paginate_links ) ) {
			$output = "<ul class='pagination'>";
			if ( $current > 1 ) {
				$output .= '<li class="page-prev"><a class="btn btn-link text-default-color" href="/page/' . ( $current - 1 ) . '/"><i class="fa fa-angle-left"></i></a></li>';
			} else {
				$output .= '<li class="page-prev"><span class="btn btn-link btn-disable-hover"><i class="fa fa-angle-left"></i></a></li>';
			}

			foreach ( $paginate_links as $page ) {
				$output .= '<li><span class="btn btn-link text-default-color">' . $page . '</span></li>';
			}

			if ( $current < $total ) {
				$output .= '<li class="page-next"><a class="btn btn-link text-default-color" href="/page/' . ( $current + 1 ) . '/"><i class="fa fa-angle-right"></i></a></li>';
			} else {
				$output .= '<li class="page-next"><span class="btn btn-link btn-disable-hover"><i class="fa fa-angle-right"></i></a></li>';
			}

			$output .= '</ul>';
		}

		return wp_kses_post( $output );
	}
}

if ( ! function_exists( 'uncode_pwf_customize_pagination_default_archive_template' ) ) {
	function uncode_pwf_customize_pagination_default_archive_template( $output, $filter_id, $args ) {

		$total   = $args['total'];
		$current = $args['current'];
		$base    = $args['base'];
		$format  = '';

		$paginate_links = paginate_links(
			apply_filters(
				'woocommerce_pagination_args',
				array(
					'base'      => $base,
					'format'    => $format,
					'current'   => max( 1, $current ),
					'total'     => $total,
					'prev_next' => false,
					'type'      => 'array',
					'end_size'  => 3,
					'mid_size'  => 3,
				)
			)
		);

		if ( is_array( $paginate_links ) ) {
			$output = "<ul class='pagination'>";
			$prev   = get_previous_posts_link( '<i class="fa-fw fa fa-angle-left"></i>' );
			if ( null !== $prev ) {
				$output .= '<li class="page-prev">' . $prev . '</li>';
			} else {
				$output .= '<li class="page-prev"><span class="btn btn-link text-gray-x11-color btn-icon-left btn-disable-hover"><i class="fa-fw fa fa-angle-left"></i></span></li>';
			}

			foreach ( $paginate_links as $page ) {
				$output .= '<li><span class="btn-container">' . $page . '</span></li>';
			}
			$next = get_next_posts_link( '<i class="fa-fw fa fa-angle-right"></i>' );
			if ( null !== $next ) {
				$output .= '<li class="page-next">' . $next . '</li>';
			} else {
				$output .= '<li class="page-next"><span class="btn btn-link text-gray-x11-color btn-icon-right btn-disable-hover"><i class="fa-fw fa fa-angle-right"></i></span></li>';
			}

			$output .= '</ul>';
		}

		return wp_kses_post( $output );
	}
}

function uncode_pwf_set_block_global_variables( $filter_id ) {
	global $pwf_uncode_block;
	if ( ! isset( $pwf_uncode_block ) ) {
		$pwf_uncode_block = get_uncode_shortcode_block_content( ot_get_option( '_uncode_product_index_content_block' ) );

		$pwf_uncode_block['post_blocks']['uncode_product'] = uncode_flatArray( vc_sorted_list_parse_value( $pwf_uncode_block['product_items'] ) );
	}
}

function get_uncode_shortcode_block_content( $block_id ) {
	$uncode_block = get_post_field( 'post_content', absint( $block_id ) );

	$regex      = '/\[uncode_index(.*?)\]/';
	$regex_attr = '/(.*?)=\"(.*?)\"/';
	preg_match_all( $regex, $uncode_block, $matches, PREG_SET_ORDER );
	$block_shortcode = $matches[0][0];

	$first_char = substr( $block_shortcode, 0, 1 );
	$last_char  = substr( $block_shortcode, -1, 1 );
	if ( '[' === $first_char ) {
		$block_shortcode = substr( $block_shortcode, 1 );
	}
	if ( ']' === $last_char ) {
		$len             = strlen( $block_shortcode ) - 1;
		$block_shortcode = substr( $block_shortcode, 0, $len );
	}

	$block_shortcode  = str_replace( 'uncode_index', '', $block_shortcode );
	$atts             = shortcode_parse_atts( $block_shortcode );
	$pwf_uncode_block = wp_parse_args( $atts, uncode_attributes_first() );

	return $pwf_uncode_block;
}

function pwf_uncode_theme_css_code() {
	?>
	<style>
		.pwf-woo-filter select:not([multiple]) {
			background-image: none;
			display: inline;
		}
		.pwf-woo-filter .select2-search input {
			padding:0 !important;
			border-color: transparent !important;
		}
		.pwf-woo-filter .pwf-items-dropdown-has-select2 .pwf-select::after {
			top: 0;
		}
	</style>
	<?php
}
function pwf_uncode_theme_js_code() {
	// see https://support.undsgn.com/hc/en-us/articles/360014001898
	?>
	<script type="text/javascript">
		(function( $ ) {
			"use strict";

			var initIsoTopeData = false;
			var $itemSelector   = '.tmb-iso';
			var isOriginLeft    = $('body').hasClass('rtl') ? false : true;
			var transitionDuration;
			var isotopeContainersArray = [];
			var $items;
			var itemMargin;
			var typeGridArray = [];
			var screenLgArray = [];
			var screenMdArray = [];
			var screenSmArray = [];

			var onLayout = function(isotopeObj, startIndex, needsReload) {
				var needsReload = needsReload ? true : false;

				window.uncode_textfill();
				isotopeObj.css('opacity', 1);
				isotopeObj.closest('.isotope-system').find('.isotope-footer').css('opacity', 1);

				requestTimeout(function() {
					if (startIndex > 0) {
						reloadIsotope(isotopeObj);
						if (SiteParameters.dynamic_srcset_active === '1') {
							UNCODE.refresh_dynamic_srcset_size(isotopeObj);
							UNCODE.adaptive_srcset(isotopeObj);
						}
						// window.dispatchEvent(UNCODE.boxEvent);
					} else if (needsReload) {
						reloadIsotope(isotopeObj);
					}

					UNCODE.adaptive();
					if (SiteParameters.dynamic_srcset_active === '1' && startIndex === 0) {
						UNCODE.refresh_dynamic_srcset_size(isotopeObj);
					}
					$(isotopeObj).find('audio,video').each(function() {
						$(this).mediaelementplayer({
							pauseOtherPlayers: false,
						});
					});
					if ($(isotopeObj).find('.nested-carousel').length) {
						UNCODE.carousel($(isotopeObj).find('.nested-carousel'));
						requestTimeout(function() {
							boxAnimation($('.tmb-iso', isotopeObj), startIndex, true, isotopeObj);
						}, 200);
					} else {
						boxAnimation($('.tmb-iso', isotopeObj), startIndex, true, isotopeObj);
					}
					isotopeObj.trigger('isotope-layout-complete');

				}, 100);

			};
			var boxAnimation = function(items, startIndex, sequential, container) {
				var $allItems = items.length - startIndex,
					showed = 0,
					index = 0;
				if (container.closest('.owl-item').length == 1) return false;
				$.each(items, function(index, val) {
					var $this = $(val),
						elInner = $('> .t-inside', val);
					if (UNCODE.isUnmodalOpen && !val.closest('#unmodal-content')) {
						return;
					}
					if (val[0]) val = val[0];
					if (elInner.hasClass('animate_when_almost_visible') && !elInner.hasClass('force-anim')) {
						new Waypoint({
							context: UNCODE.isUnmodalOpen ? document.getElementById('unmodal-content') : window,
							element: val,
							handler: function() {
								var element = $('> .t-inside', this.element),
									parent = $(this.element),
									currentIndex = parent.index();
								var delay = (!sequential) ? index : ((startIndex !== 0) ? currentIndex - $allItems : currentIndex),
									delayAttr = parseInt(element.attr('data-delay'));
								if (isNaN(delayAttr)) delayAttr = 100;
								delay -= showed;
								var objTimeout = requestTimeout(function() {
									element.removeClass('zoom-reverse').addClass('start_animation');
									showed = parent.index();
									container.isotope('layout');
								}, delay * delayAttr)
								parent.data('objTimeout', objTimeout);
								if (!UNCODE.isUnmodalOpen) {
									this.destroy();
								}
							},
							offset: '100%'
						})
					} else {
						if (elInner.hasClass('force-anim')) {
							elInner.addClass('start_animation');
						} else {
							elInner.css('opacity', 1);
						}
						container.isotope('layout');
					}

					index++;
				});
			};
			var reloadIsotope = function(isotopeObj) {
				var isoIndex = $(isotopeObj).attr('data-iso-index');
				var $layoutMode = ($(isotopeObj).data('layout'));
				if ($layoutMode === undefined) {
					$layoutMode = 'masonry';
				}
				if (isotopeObj.data('isotope')) {
					isotopeObj.isotope({
						itemSelector: $itemSelector,
						layoutMode: $layoutMode,
						transitionDuration: transitionDuration[isoIndex],
						masonry: {
							columnWidth: colWidth(isoIndex)
						},
						vertical: {
							horizontalAlignment: 0.5,
						},
						sortBy: 'original-order',
						isOriginLeft: isOriginLeft
					});
				}
			};

			var colWidth = function(index) {
				$(isotopeContainersArray[index]).width('');
				var isPx = $(isotopeContainersArray[index]).parent().hasClass('px-gutter'),
					widthAvailable = $(isotopeContainersArray[index]).width(),
					columnNum = 12,
					columnWidth = 0,
					data_vp_height = $(isotopeContainersArray[index]).attr('data-vp-height'),
					consider_menu = $(isotopeContainersArray[index]).attr('data-vp-menu'),
					winHeight = UNCODE.wheight - UNCODE.adminBarHeight,
					$rowContainer,
					paddingRow,
					$colContainer,
					paddingCol;

				if ( consider_menu )
					winHeight = winHeight - UNCODE.menuHeight;

				if ( data_vp_height === '1' ) {
					$rowContainer = $(isotopeContainersArray[index]).parents('.row-parent').eq(0),
					paddingRow = parseInt($rowContainer.css('padding-top')) + parseInt($rowContainer.css('padding-bottom')),
					$colContainer = $(isotopeContainersArray[index]).parents('.uncell').eq(0),
					paddingCol = parseInt($colContainer.css('padding-top')) + parseInt($colContainer.css('padding-bottom'));
					winHeight = winHeight - ( paddingRow + paddingCol );
				}

				if (isPx) {
					columnWidth = Math.ceil(widthAvailable / columnNum);
					$(isotopeContainersArray[index]).width(columnNum * Math.ceil(columnWidth));
				} else {
					columnWidth = ($('html.firefox').length) ? Math.floor(widthAvailable / columnNum) : widthAvailable / columnNum;
				}
				$items = $(isotopeContainersArray[index]).find('.tmb-iso:not(.tmb-carousel)');
				itemMargin = parseInt($(isotopeContainersArray[index]).find('.t-inside').css("margin-top"));
				for (var i = 0, len = $items.length; i < len; i++) {
					var $item = $($items[i]),
						multiplier_w = $item.attr('class').match(/tmb-iso-w(\d{0,2})/),
						multiplier_h = $item.attr('class').match(/tmb-iso-h(\d{0,3})/),
						multiplier_fixed = multiplier_h !== null ? multiplier_h[1] : 1;


					if (multiplier_w != null && multiplier_w[1] !== undefined && multiplier_w[1] == 15) {
						multiplier_w[1] = 2.4; // 20/(100/12) - 5 columns
					}

					if (multiplier_h != null && multiplier_h[1] !== undefined && multiplier_h[1] == 15) {
						multiplier_h[1] = 2.4; // 20/(100/12) - 5 columns
					}

					if (widthAvailable >= screenMdArray[index] && widthAvailable < screenLgArray[index]) {
						if (multiplier_w != null && multiplier_w[1] !== undefined) {
							switch (parseInt(multiplier_w[1])) {
								case (5):
								case (4):
								case (3):
									if (typeGridArray[index]) multiplier_h[1] = (6 * multiplier_h[1]) / multiplier_w[1];
									multiplier_w[1] = 6;
									break;
								case (2):
								case (1):
									if (typeGridArray[index]) multiplier_h[1] = (3 * multiplier_h[1]) / multiplier_w[1];
									multiplier_w[1] = 3;
									break;
								default:
									if (typeGridArray[index]) multiplier_h[1] = (12 * multiplier_h[1]) / multiplier_w[1];
									multiplier_w[1] = 12;
									break;
							}

							if (multiplier_w[1] == 2.4) { // 5 columns
								if (typeGridArray[index]) multiplier_h[1] = (6 * multiplier_h[1]) / multiplier_w[1];
								multiplier_w[1] = 6;
							}
						}
					} else if (widthAvailable >= screenSmArray[index] && widthAvailable < screenMdArray[index]) {
						if (multiplier_w != null && multiplier_w[1] !== undefined) {
							switch (parseInt(multiplier_w[1])) {
								case (5):
								case (4):
								case (3):
								case (2):
								case (1):
									if (typeGridArray[index]) multiplier_h[1] = (6 * multiplier_h[1]) / multiplier_w[1];
									multiplier_w[1] = 6;
									break;
								default:
									if (typeGridArray[index]) multiplier_h[1] = (12 * multiplier_h[1]) / multiplier_w[1];
									multiplier_w[1] = 12;
									break;
							}

							if (multiplier_w[1] == 2.4) { // 5 columns
								if (typeGridArray[index]) multiplier_h[1] = (6 * multiplier_h[1]) / multiplier_w[1];
								multiplier_w[1] = 6;
							}
						}
					} else if (widthAvailable < screenSmArray[index]) {
						if (multiplier_w != null && multiplier_w[1] !== undefined) {
							//if (typeGridArray[index]) multiplier_h[1] = (12 * multiplier_h[1]) / multiplier_w[1];
							multiplier_w[1] = 12;
							if (typeGridArray[index]) multiplier_h[1] = 12;
						}
					}
					var width = multiplier_w ? Math.floor(columnWidth * multiplier_w[1]) : columnWidth,
						height;

					if ( data_vp_height === '1' && typeof multiplier_h[1] !== 'undefined' ) {
						height = multiplier_h ? Math['ceil'](winHeight / (100 / multiplier_fixed) ) - itemMargin : columnWidth;
						if ( widthAvailable < screenSmArray[index] ) {
							height = Math['ceil']((2 * Math.ceil(columnWidth / 2)) * 12) - itemMargin;
						}
					} else {
						height = multiplier_h ? Math['ceil']((2 * Math.ceil(columnWidth / 2)) * multiplier_h[1]) - itemMargin : columnWidth;
					}

					if (width >= widthAvailable) {
						$item.css({
							width: widthAvailable
						});
						if (typeGridArray[index]) {
							$item.children().add($item.find('.backimg')).css({
								height: height
							});
						}
					} else {
						$item.css({
							width: width
						});
						if (typeGridArray[index]) {
							$item.children().add($item.find('.backimg')).css({
								height: height
							});
						}
					}
				}
				return columnWidth / 60; // least common multiple for 12 (regular columns) and 10 (5 columns)
			}


			$('.isotope-system').off('click', '.pagination .page-prev a, .pagination .page-next a');

			$( document.body ).on('click', '.pagination .page-prev a, .pagination .page-next a', function( e ) {
				let link       = $(this).attr('href');
				let pageNum    = link.match(/\?upage=\d+/);
				if ( null !== pageNum ) {
					pageNum = parseInt( pageNum[0].match( new RegExp("\\d+") )[0] );
					$( document.body ).trigger('pwfTriggerPageNumber', [{pageNum:pageNum}]);
				}
			});

			function getAttributes ( $node ) {
				var attrs = {};
				$.each( $node[0].attributes, function ( index, attribute ) {
					attrs[attribute.name] = attribute.value;
				} );

				return attrs;
			}

			$( document.body ).on( 'pwf_filter_js_ajax_done', function(  event, data ) {
				if ( typeof pwfGetJsUsedFuntion !== 'undefined' && '' !== pwfGetJsUsedFuntion ) {
					// variable is defined
					let pwfFilterSetting  = pwffilterVariables.filter_setting;
					let productsContainer = pwfFilterSetting.products_container_selector;

					if ( 'numbers' === pwfGetJsUsedFuntion.getPaginationType() || ( data.queryArgs.hasOwnProperty('attributes') && ! data.queryArgs.attributes.hasOwnProperty('page') ) ) {
						$(productsContainer).closest('.isotope-system').removeClass('isotope-processed');
						$(productsContainer).removeClass('un-isotope-init');
						$(productsContainer).removeAttr('data-iso-index').removeAttr('style');
						if ( $(productsContainer).data( 'isotope' ) ) {
							$(productsContainer).isotope( 'destroy' );
						}
						UNCODE.isotopeLayout();
					} else {
						if ( false === initIsoTopeData ) {
							transitionDuration = [ $(productsContainer).find('.t-inside.animate_when_almost_visible').length > 0 ? 0 : '0.5s' ];
							isotopeContainersArray = [ $( productsContainer ) ];
							var isoData =  $(productsContainer).data();
							var $data_lg,
							$data_md,
							$data_sm;
							if (isoData.type == 'metro') typeGridArray.push(true);
							else typeGridArray.push(false);
							if (isoData.lg !== undefined) $data_lg = $( productsContainer ).attr('data-lg');
							else $data_lg = '1000';
							if (isoData.md !== undefined) $data_md = $( productsContainer ).attr('data-md');
							else $data_md = '600';
							if (isoData.sm !== undefined) $data_sm = $( productsContainer ).attr('data-sm');
							else $data_sm = '480';
							screenLgArray.push($data_lg);
							screenMdArray.push($data_md);
							screenSmArray.push($data_sm);
							initIsoTopeData = true;
						}

						let newItems  = $(productsContainer).find('.pwf-new-product-added');
						let isotopeId = $(productsContainer).closest('.isotope-system').attr('id');
						$(productsContainer).find('.pwf-new-product-added').addClass('tmb-iso');

						let isotope = $( $(productsContainer) );
						isotope.isotope('reloadItems', onLayout(isotope, newItems.length));
						let getLightbox = UNCODE.lightboxArray['ilightbox_' + isotopeId];
						if (typeof getLightbox === 'object') getLightbox.refresh();
						if ( typeof twttr !== 'undefined' )
							twttr.widgets.load(isotopeContainersArray[i]);
					}
				}
				$('.isotope-system').off('click', '.pagination .page-prev a, .pagination .page-next a');
			});

			function customizePagination() {
				let pwfFilterSetting   = pwffilterVariables.filter_setting;
				let paginationSelector = pwfFilterSetting.pagination_selector;
				if ( '.row-navigation .pagination' === paginationSelector && 'numbers' !== pwfGetJsUsedFuntion.getPaginationType() ) {
					$('.row-navigation > .row').appendTo('.post-content');
				}
			}

			if ( typeof pwfGetJsUsedFuntion !== 'undefined' && '' !== pwfGetJsUsedFuntion ) {
				customizePagination();
			} else {
				$( document.body ).on( 'pwf_filter_js_init_end', function(  event, data ) {
					if ( typeof pwfGetJsUsedFuntion !== 'undefined' && '' !== pwfGetJsUsedFuntion ) {
						customizePagination();
					}
				});
			}
		})(jQuery);
	</script>
	<?php
}

// phpcs:disable
// see uncode_index.php
function uncode_attributes_first() {
	// @codingStandardsIgnoreLine
	$attributes_first = array(
		'uncode_shortcode_id' => '',
		'title' => '',
		'col_width' => '12',
		'index_type' => 'isotope',
		'isotope_mode' => 'masonry',
		'index_back_color' => '',
		'index_back_color_type' => '',
		'index_back_color_solid' => '',
		'index_back_color_gradient' => '',
		'items' => '',
		'filtering' => '',
		'show_extra_filters' => '',
		'show_woo_sorting' => '',
		'show_woo_result_count' => '',
		'woo_sorting_default_text' => '',
		'hide_woo_sorting_icon' => '',
		'woo_sorting_skin' => '',
		'woo_sorting_shadow' => '',
		'show_widgetized_content_block' => '',
		'widgetized_content_block_id' => '',
		'widgetized_content_block_toggle_text' => '',
		'hide_widgetized_content_block_icon' => '',
		'filter_hide_cats' => '',
		'filter_typography' => '',
		'filter_style' => 'light',
		'filter_back_color' => '',
		'filter_back_color_type' => '',
		'filter_back_color_solid' => '',
		'filter_back_color_gradient' => '',
		'filtering_full_width' => '',
		'filtering_position' => 'left',
		'filtering_uppercase' => '',
		'filter_all_opposite' => '',
		'filter_all_text' => '',
		'filter_mobile' => '',
		'filter_mobile_align' => 'center',
		'filter_mobile_wrapper' => '',
		'filter_mobile_wrapper_text' => esc_html__( 'Filters', 'uncode' ),
		'filter_mobile_dropdown' => '',
		'filter_mobile_dropdown_text' => esc_html__( 'Categories', 'uncode' ),
		'filter_scroll' => '',
		'filter_sticky' => '',
		'footer_style' => 'light',
		'footer_back_color' => '',
		'footer_back_color_type' => '',
		'footer_back_color_solid' => '',
		'footer_back_color_gradient' => '',
		'footer_full_width' => '',
		'pagination' => '',
		'infinite' => '',
		'infinite_hover_fx' => '',
		'infinite_button' => '',
		'infinite_button_text' => '',
		'infinite_button_shape' => '',
		'infinite_button_outline' => '',
		'infinite_button_color' => '',
		'infinite_button_color_type' => '',
		'infinite_button_color_solid' => '',
		'infinite_button_color_gradient' => '',
		'style_preset' => 'masonry',
		'images_size' => '',
		'thumb_size' => '',
		'single_width' => '4',
		'single_height' => '4',
		'single_height_viewport' => '',
		'single_height_viewport_minus' => '',
		'single_back_color' => '',
		'single_shape' => '',
		'radius' => '',
		'single_text' => 'under',
		'single_image_position' => '',
		'single_vertical_text' => '',
		'single_image_size' => '6',
		'single_lateral_responsive' => 'yes',
		'single_elements_click' => '',
		'single_text_visible' => 'no',
		'single_text_anim' => 'yes',
		'single_text_anim_type' => '',
		'single_overlay_visible' => 'no',
		'single_overlay_anim' => 'yes',
		'single_image_coloration' => '',
		'single_image_color_anim' => '',
		'single_image_anim' => 'yes',
		'single_image_magnetic' => '',
		'single_secondary' => '',
		'single_reduced' => '',
		'single_reduced_mobile' => '',
		'single_padding' => '',
		'single_padding_vertical' => '',
		'single_text_reduced' => '',
		'single_h_align' => 'left',
		'single_h_align_mobile' => '',
		'single_v_position' => 'middle',
		'single_h_position' => 'left',
		'single_style' => 'light',
		'single_overlay_color' => '',
		'single_overlay_coloration' => '',
		'single_overlay_blend' => '',
		'single_overlay_opacity' => 50,
		'single_shadow' => '',
		'shadow_weight' => '',
		'shadow_darker' => '',
		'single_border' => '',
		'single_icon' => '',
		'single_title_transform' => '',
		'single_title_weight' => '',
		'single_title_family' => '',
		'single_title_dimension' => '',
		'single_title_semantic' => 'h3',
		'single_title_height' => '',
		'single_title_space' => '',
		'single_text_lead' => '',
		'single_meta_custom_typo' => '',
		'single_meta_size' => '',
		'single_meta_weight' => '',
		'single_meta_transform' => '',
		'single_css_animation' => '',
		'single_animation_delay' => '',
		'single_animation_speed' => '',
		'single_animation_first' => '',
		'single_parallax_intensity' => '',
		'single_parallax_centered' => '',
		'carousel_height' => 'auto',
		'carousel_v_align' => '',
		'carousel_type' => '',
		'carousel_interval' => 3000,
		'carousel_navspeed' => 400,
		'carousel_loop' => '',
		'carousel_nav' => '',
		'carousel_nav_skin' => 'light',
		'carousel_nav_mobile' => '',
		'carousel_dots' => '',
		'carousel_dots_space' => '',
		'carousel_dots_mobile' => '',
		'carousel_dots_inside' => '',
		'carousel_dot_position' => '',
		'carousel_dot_width' => '',
		'column_width_use_pixel' => '',
		'carousel_width_percent' => '',
		'carousel_width_pixel' => '',
		'carousel_dot_padding' => '2',
		'carousel_autoh' => '',
		'carousel_lg' => '',
		'carousel_md' => '',
		'carousel_sm' => '',
		'gutter_size' => 3,
		'stage_padding' => 0,
		'carousel_overflow' => '',
		'carousel_half_opacity' => '',
		'carousel_scaled' => '',
		'carousel_pointer_events' => '',
		'inner_padding' => '',
		'post_items' => 'media|featured|onpost|original,title,category|nobg,date,text|excerpt,link|default,author,sep-one|full,extra',
		'page_items' => 'media|featured,title,type,category,text',
		'product_items' => 'media|featured,title,type,category,text,price',
		'uncode_taxonomy_items' => 'media|featured|onpost|original,title,count|nobg|relative|hide-label',
		'off_grid' => '',
		'off_grid_element' => 'odd',
		'off_grid_custom' => '0,2',
		'off_grid_val' => '2',
		'off_grid_all' => '',
		'screen_lg' => 1000,
		'screen_md' => 600,
		'screen_sm' => 480,
		'filter' => '',
		'el_id' => '',
		'lbox_skin' => '',
		'lbox_dir' => '',
		'lbox_title' => '',
		'lbox_caption' => '',
		'lbox_social' => '',
		'lbox_deep' => '',
		'lbox_no_tmb' => '',
		'lbox_no_arrows' => '',
		'no_double_tap' => '',
		'el_class' => '',
		'custom_cursor' => '',
		'orderby' => NULL,
		'order' => 'DESC',
		'custom_order' => '',
		'order_ids' => '',
		'loop' => 'size:10|order_by:date|post_type:post',
		'offset' => '',
		'using_plugin' => '',
		'css_class' => '',
		'post_matrix' => '',
		'matrix_amount' => 5,
		'matrix_items' => '',
		'single_fluid_height' => '33',
		'carousel_height_viewport' => '100',
		'carousel_height_viewport_minus' => '',
		'auto_query' => '',
		'auto_query_type' => '',
		'pagination_disable_history' => '',
		'parent_id' => false,
	);

	return $attributes_first;
}
