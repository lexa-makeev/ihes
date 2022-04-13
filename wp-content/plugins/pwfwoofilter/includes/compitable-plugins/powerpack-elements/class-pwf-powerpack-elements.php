<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Powerpack_Elements' ) ) {
	/**
	 * @since 1.3.9
	 */
	class Pwf_Powerpack_Elements {

		private $filter_id;
		private $filter_settings;
		private $powerpack_settings;
		private $is_shop_archive = false;
		private $is_shortcode    = false;
		private $exclude_terms   = array();

		public function __construct() {
			add_action( 'pwf_init_parse_query', array( $this, 'is_page_has_powerpack' ), 10, 2 );
		}

		protected static function get_powerpack_default_settings() {
			$defaults = array(
				'_skin'                          => 'skin-1',
				'sale_badge_position'            => 'left',
				'sale_badge_custom_text'         => '',
				'featured_badge_position'        => '',
				'featured_badge_custom_text'     => '',
				'top_rating_badge_position'      => '',
				'top_rating_badge_custom_text'   => '',
				'number_of_ratings'              => 4,
				'best_selling_badge_position'    => '',
				'best_selling_badge_custom_text' => '',
				'number_of_sales'                => '1',
				'link_image'                     => 'yes',
				'products_hover_style'           => '',
				'show_category'                  => 'yes',
				'show_title'                     => 'yes',
				'link_title'                     => 'yes',
				'link_title_target'              => 'no',
				'show_ratings'                   => 'yes',
				'show_price'                     => 'yes',
				'show_short_desc'                => '',
				'show_add_cart'                  => 'yes',
				'link_image_target'              => 'no',
				'thumbnail_size'                 => '',
				'pagination_type'                => '',
				'products_columns'               => 4,
				'products_per_page'              => 8,
				'source'                         => 'all', // Query loop options
				'category_filter_rule'           => 'IN',
				'category_filter'                => '',
				'tag_filter_rule'                => 'IN',
				'tag_filter'                     => '',
				'offset'                         => 0,
				'query_manual_ids'               => '',
				'query_exclude_ids'              => '',
				'query_exclude_current'          => '',
				'filter_by'                      => '',
				'orderby'                        => 'date',
				'order'                          => 'DESC',
			);

			return $defaults;
		}

		protected function init_powerpack_elements() {
			if ( $this->is_shortcode ) {
				$this->set_powerpack_shortcode_settings();
				$this->add_to_shortcode();
			} elseif ( $this->is_shop_archive ) {
				$this->init_powerpack_settings_for_shop_archive();
				add_filter( 'pwf_wc_setup_loop_args', array( $this, 'setup_loop_args_for_archive' ), 10, 2 );
			}

			if ( $this->is_shop_archive || $this->is_shortcode ) {
				add_filter( 'pwf_woo_filter_product_loop_template', array( $this, 'change_woo_loop_template_name' ), 10, 2 );
				add_filter( 'wc_get_template_part', array( $this, 'change_loop_template_path' ), 10, 3 );
				add_filter( 'pwf_html_pagination', array( $this, 'customize_pagination' ), 10, 3 );
			}
		}

		/**
		 * see /powerpack-elements/classes/class-pp-woo-builder.php
		 * Method name pp_product_archive_template
		 */
		protected function init_powerpack_settings_for_shop_archive() {
			$product_achive_custom_page_id = get_option( 'pp_woo_template_product_archive' );

			$widget_id = sanitize_key( $_POST['powerpackforelementor']['data-id'] );
			if ( empty( $widget_id ) ) {
				return;
			}
			$elementor   = \Elementor\Plugin::$instance;
			$meta        = $elementor->documents->get( $product_achive_custom_page_id )->get_elements_data();
			$widget_data = $this->find_element_recursive( $meta, $widget_id );
			if ( $widget_data ) {
				$this->powerpack_settings = wp_parse_args( $widget_data['settings'], self::get_powerpack_default_settings() );
			}
		}

		/**
		 * see /powerpack-elements/modules/woocommerce/module.php
		 * Method name get_product_data
		 *
		 * @since 1.3.9
		 */
		protected function set_powerpack_shortcode_settings() {
			$post_id     = '';
			$widget_data = false;

			if ( isset( $_POST['powerpackforelementor'] ) ) {
				$post_id   = absint( $_POST['current_page_id'] );
				$widget_id = sanitize_key( $_POST['powerpackforelementor']['data-id'] );
			} else {
				$post_id = get_the_ID();
			}

			if ( ! empty( $post_id ) ) {
				$elementor = \Elementor\Plugin::$instance;
				$meta      = $elementor->documents->get( $post_id )->get_elements_data();

				if ( wp_doing_ajax() ) {
					if ( ! empty( $widget_id ) ) {
						$widget_data = $this->find_element_recursive( $meta, $widget_id );
					}
				} else {
					// used to hook count options in the filter
					$widget_data = $this->find_element_by_widget_type( $meta, 'pp-woo-products' );
				}

				if ( $widget_data ) {
					$this->powerpack_settings = wp_parse_args( $widget_data['settings'], self::get_powerpack_default_settings() );
				}
			}
		}

		/**
		 * see /powerpack-elements/modules/woocommerce/widgets/woo-products.php
		 * Method name query_posts
		 */
		protected function add_to_shortcode() {
			if ( ! $this->powerpack_settings ) {
				return;
			}

			$args     = array();
			$settings = $this->powerpack_settings;

			if ( 'related' === $settings['source'] || 'main' === $settings['source'] ) {
				return;
			}

			$args['columns'] = $settings['products_columns'];
			$args['limit']   = $settings['products_per_page'];
			$args['order']   = $settings['order'];
			$args['orderby'] = $settings['orderby'];

			if ( '' !== $settings['pagination_type'] ) {
				$args['paginate'] = true;
			}

			if ( 'sale' === $settings['filter_by'] ) {
				$args['on_sale'] = true;
			} elseif ( 'featured' === $settings['filter_by'] ) {
				$args['visibility'] = 'featured';
			} elseif ( 'top_rated' === $settings['filter_by'] ) {
				$args['top_rated'] = true;
			} elseif ( 'best_selling' === $settings['filter_by'] ) {
				$args['best_selling'] = true;
			}

			if ( 'custom' === $settings['source'] ) {
				if ( ! empty( $settings['category_filter'] ) ) {
					$args['cat_operator'] = $settings['category_filter_rule'];
					if ( 'IN' === $args['cat_operator'] ) {
						$args['category'] = implode( ',', $settings['category_filter'] );
					} else {
						$terms = get_terms(
							array(
								'taxonomy' => 'product_cat',
								'slug'     => array_map( 'esc_attr', $settings['category_filter'] ),
							)
						);
						if ( ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								$this->exclude_terms['product_cat'][] = $term->term_id;
							}
						}
					}
				}

				if ( ! empty( $settings['tag_filter'] ) ) {
					$args['tag_operator'] = $settings['tag_filter_rule'];
					if ( 'IN' === $args['tag_operator'] ) {
						$args['tag'] = implode( ',', $settings['tag_filter'] );
					} else {
						$terms = get_terms(
							array(
								'taxonomy' => 'product_tag',
								'slug'     => array_map( 'esc_attr', $settings['tag_filter'] ),
							)
						);
						if ( ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								$this->exclude_terms['product_tag'][] = $term->term_id;
							}
						}
					}
				}
			}

			if ( 'manual' === $settings['source'] ) {
				$args['ids'] = implode( ',', $settings['query_manual_ids'] );
			}

			if ( ! empty( $this->exclude_terms ) ) {
				add_filter( 'pwf_parse_taxonomy_query', array( $this, 'exclude_terms' ), 10, 2 );
			}

			// Append Query like a default Woocommerce shortcode
			$shortcode = new Pwf_Integrate_Shortcode( $this->filter_id, $args );
		}

		public function exclude_terms( $terms, $filter_id ) {
			foreach ( $this->exclude_terms as $taxonomy => $ids ) {
				$terms[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $ids,
					'operator' => 'NOT IN',
				);
			}

			return $terms;
		}

		/**
		 * return widget ID by path widget type
		 */
		protected function find_element_by_widget_type( $elements, $widget_name ) {

			foreach ( $elements as $element ) {
				if ( 'widget' === $element['elType'] && $widget_name === $element['widgetType'] ) {
					return $element;
				}

				if ( ! empty( $element['elements'] ) ) {
					$element = $this->find_element_by_widget_type( $element['elements'], $widget_name );

					if ( $element ) {
						return $element;
					}
				}
			}

			return false;
		}

		/**
		 * see /powerpack-elements/modules/woocommerce/module.php
		 * Method name find_element_recursive
		 *
		 * @since 1.3.9
		 */
		protected function find_element_recursive( $elements, $form_id ) {

			foreach ( $elements as $element ) {
				if ( $form_id === $element['id'] ) {
					return $element;
				}

				if ( ! empty( $element['elements'] ) ) {
					$element = $this->find_element_recursive( $element['elements'], $form_id );

					if ( $element ) {
						return $element;
					}
				}
			}

			return false;
		}

		/**
		 * Define if a shortcode or archive page using powerpack
		 */
		public function is_page_has_powerpack( $filter_id, $meta ) {
			$this->filter_id       = $filter_id;
			$this->filter_settings = $meta['setting'];
			$init_powerpack        = false;
			$require_js_code       = false;

			if ( 'on' === $this->filter_settings['is_shortcode'] && 'powerpack_for_elementor' === $this->filter_settings['shortcode_type'] ) {
				$init_powerpack     = true;
				$this->is_shortcode = true;
				$require_js_code    = true;
			} elseif ( is_shop() || is_tax( get_object_taxonomies( 'product' ) ) ) {
				// init js for shop archive
				if ( get_option( 'pp_woo_builder_enable' ) ) {
					$product_achive_custom_page_id = get_option( 'pp_woo_template_product_archive' );
					if ( ! empty( $product_achive_custom_page_id ) ) {
						$require_js_code = true;
					}
				}
			} elseif ( wp_doing_ajax() && isset( $_POST['powerpackforelementor'] ) ) {

				// check if shop archive build using powerpack
				$require_js_code = false;

				if ( 'true' === $GLOBALS['pwf_main_query']['is_shop_archive'] ) {
					if ( get_option( 'pp_woo_builder_enable' ) ) {
						$product_achive_custom_page_id = get_option( 'pp_woo_template_product_archive' );
						if ( ! empty( $product_achive_custom_page_id ) ) {
							$init_powerpack        = true;
							$this->is_shop_archive = true;
						}
					}
				}
			}

			if ( $require_js_code ) {
				$this->add_js_code();
			}

			if ( $init_powerpack ) {
				$this->init_powerpack_elements();
			}
		}

		public function add_js_code() {
			add_action( 'wp_footer', array( $this, 'append_js_code' ), 1000 );
		}

		public function change_woo_loop_template_name( $template, $filter_id ) {
			if ( $this->powerpack_settings ) {
				$template = array(
					'content',
					'powerpack-product',
				);
			}
			return $template;
		}

		public function change_loop_template_path( $template, $slug, $name ) {
			if ( 'powerpack-product' === $name ) {
				$template = '';
				$settings = $this->powerpack_settings;
				include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/content-product-' . esc_attr( $settings['_skin'] ) . '.php';
			}

			return $template;
		}

		public function setup_loop_args_for_archive( $args ) {
			if ( $this->powerpack_settings ) {
				$args['columns']  = $this->powerpack_settings['products_columns'];
				$args['per_page'] = $this->powerpack_settings['products_per_page'];
			}

			return $args;
		}

		public function customize_pagination( $html, $filter_id, $args ) {
			$output = paginate_links(
				apply_filters(
					'pp_woocommerce_pagination_args',
					array( // WPCS: XSS ok.
						'base'      => $args['base'],
						'format'    => '',
						'add_args'  => false,
						'current'   => max( 1, $args['current'] ),
						'total'     => $args['total'],
						'prev_text' => '&larr;',
						'next_text' => '&rarr;',
						'type'      => 'list',
						'end_size'  => 3,
						'mid_size'  => 3,
					)
				)
			);

			if ( ! empty( $output ) ) {
				$output = '<nav class="pp-woocommerce-pagination">' . $output . '</nav>';
			}

			return $output;
		}

		public function append_js_code() {
			?>
			<script type="text/javascript">
				(function( $ ) {
					"use strict";
					if ( typeof pwfWooHooks !== "undefined" && typeof pwffilterVariables !== "undefined" ) {
						function powerpackElementsCusotmizeDataBeforeAjax( data ) {
							let productsContainer =  pwffilterVariables.filter_setting.products_container_selector;
							if ( '' !== productsContainer ) {
								let dataID = $( productsContainer ).closest('.elementor-element').attr('data-id');
								if ( '' !== dataID ) {
									data['powerpackforelementor'] = {
										'data-id' : dataID,
									};
								}
							}
							return data;
						}
						pwfWooHooks.add_filter( 'pwf_before_send_ajax_data', powerpackElementsCusotmizeDataBeforeAjax, 10 );
					}
				})(jQuery);
			</script>
			<?php
		}
	}
}
