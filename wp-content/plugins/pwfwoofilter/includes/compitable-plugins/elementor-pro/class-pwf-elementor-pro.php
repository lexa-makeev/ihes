<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Elementor_Pro' ) ) {

	/**
	 * @since 1.3.9
	 */
	class Pwf_Elementor_Pro {

		private $filter_id;
		private $filter_settings;
		private $widget_settings;
		private $is_shop_archive = false;
		private $is_shortcode    = false;
		private $include_terms   = array(); // like brand this option availabel at plugin
		private $exclude_terms   = array();
		private $widget_elements = array(); // hold all widgets that has elementor products widget

		public function __construct() {
			add_action( 'pwf_init_parse_query', array( $this, 'is_page_has_elementor' ), 10, 2 );
		}

		protected static function get_elementor_pro_default_settings() {
			$defaults = array(
				'columns'                => 4,
				'rows'                   => 4,
				'paginate'               => '',
				'query_orderby'          => 'date',
				'query_order'            => 'desc',
				'filter_by'              => '',
				'query_post_type'        => '', // featured, sale, current_query, by_id, empty is latestproduct
				'query_posts_ids'        => '', // array
				'query_include'          => array(), // array('terms')
				'query_include_term_ids' => array(), // array term_ids
				'query_exclude'          => array(), // current_post, manual_selection, terms
				'query_exclude_term_ids' => array(),
			);

			return $defaults;
		}

		protected function init_elementor_pro() {
			if ( $this->is_shortcode ) {
				$this->set_elementor_pro_settings();
				$this->add_to_shortcode();
			} elseif ( $this->is_shop_archive && wp_doing_ajax() ) {
				/**
				 * doesn't require any code when using ajax
				 */
				add_filter( 'pwf_wc_setup_loop_args', array( $this, 'setup_loop_args_for_archive' ), 10, 2 );
			}
		}

		protected function set_elementor_pro_settings() {
			$post_id     = '';
			$widget_data = false;

			if ( isset( $_POST['elementor_pro'] ) ) {
				$post_id   = absint( $_POST['current_page_id'] );
				$widget_id = esc_attr( $_POST['elementor_pro']['data-id'] );
			} else {
				$post_id = get_the_ID();
			}

			if ( ! empty( $post_id ) ) {
				$elementor = \Elementor\Plugin::$instance;
				$meta      = $elementor->documents->get( $post_id )->get_elements_data();

				if ( wp_doing_ajax() ) {
					$widget_id = sanitize_key( $_POST['elementor_pro']['data-id'] );
					if ( ! empty( $widget_id ) ) {
						$widget_data = $this->find_element_recursive( $meta, $widget_id );
					}
				} else {
					// used to hook count options in the filter
					$widget_data = $this->find_element_by_widget_type( $meta, 'woocommerce-products' );
					if ( ! empty( $this->widget_elements ) ) {
						if ( count( $this->widget_elements ) > 1 ) {
							$widget_data = $this->get_valid_widget_data();
						} else {
							$widget_data = $this->widget_elements[0];
						}
					}
				}

				if ( $widget_data ) {
					$this->widget_settings = wp_parse_args( $widget_data['settings'], self::get_elementor_pro_default_settings() );
				}
			}
		}

		protected function add_to_shortcode() {
			if ( ! $this->widget_settings || 'current_query' === $this->widget_settings['query_post_type'] ) {
				return;
			}

			$settings = $this->widget_settings;
			$args     = array(
				'columns'   => $settings['columns'],
				'limit'     => $settings['columns'] * $settings['rows'],
				'order'     => $settings['query_order'],
				'orderby'   => $settings['query_orderby'],
				'category'  => '',
				'attribute' => '',
				'tag'       => '',
			);

			if ( '' !== $settings['paginate'] ) {
				$args['paginate'] = true;
			}

			if ( 'sale' === $this->widget_settings['query_post_type'] ) {
				$args['on_sale'] = true;
			} elseif ( 'featured' === $settings['query_post_type'] ) {
				$args['visibility'] = 'featured';
			} elseif ( 'top_rated' === $settings['query_post_type'] ) {
				$args['top_rated'] = true;
			} elseif ( 'best_selling' === $settings['query_post_type'] ) {
				$args['best_selling'] = true;
			}

			if ( '' !== $settings['query_posts_ids'] && is_array( $settings['query_posts_ids'] ) ) {
				$args['ids'] = implode( ',', $settings['query_posts_ids'] );
			}

			if ( 'sale' === $this->widget_settings['query_post_type'] ) {
				$args['ids'] = '';
			} elseif ( 'by_id' === $this->widget_settings['query_post_type'] ) {
				$args['on_sale'] = false;
			}

			if ( is_array( $settings['query_include_term_ids'] ) && ! empty( $settings['query_include_term_ids'] ) ) {
				$terms = get_terms( array( 'include' => array_map( 'absint', $settings['query_include_term_ids'] ) ) );
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
							} else {
								$this->include_terms[ $term->taxonomy ][] = $term->term_id;
							}
						}
					}
				}
			}

			if ( in_array( 'terms', $settings['query_exclude'], true ) && ! empty( $settings['query_exclude_term_ids'] ) ) {
				$terms = get_terms( array( 'include' => array_map( 'absint', $settings['query_exclude_term_ids'] ) ) );
				if ( ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						if ( isset( $term->taxonomy ) ) {
							$this->exclude_terms[ $term->taxonomy ][] = $term->term_id;
						}
					}
				}
			}

			// Append Query like a default Woocommerce shortcode
			if ( ! empty( $this->include_terms ) ) {
				add_filter( 'pwf_parse_taxonomy_query', array( $this, 'append_include_terms' ), 10, 2 );
			}

			if ( ! empty( $this->exclude_terms ) ) {
				add_filter( 'pwf_parse_taxonomy_query', array( $this, 'append_exclude_terms' ), 10, 2 );
			}
			//$this->add_include_terms();
			$shortcode = new Pwf_Integrate_Shortcode( $this->filter_id, $args );
		}

		protected function append_include_terms( $terms, $filter_id ) {
			foreach ( $this->include_terms as $taxonomy => $ids ) {
				$terms[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $ids,
				);
			}

			return $terms;
		}

		protected function append_exclude_terms( $terms, $filter_id ) {
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
		 * If page contain more than elementor woo products
		 * Get widget data that has has-pwf class
		 */
		protected function get_valid_widget_data() {
			foreach ( $this->widget_elements as $widget ) {
				$settings = $widget['settings'];
				if ( isset( $settings['custom_css'] ) ) {
					if ( strpos( $settings['custom_css'], 'use-pwf-filter' ) !== false ) {
						return $widget;
					}
				}
			}
			return false;
		}

		/**
		 * return widget ID by path widget type
		 */
		protected function find_element_by_widget_type( $elements, $widget_name ) {
			$widgets = array();
			foreach ( $elements as $element ) {
				if ( 'widget' === $element['elType'] && $widget_name === $element['widgetType'] ) {
					return $element;
				}

				if ( ! empty( $element['elements'] ) ) {
					$element = $this->find_element_by_widget_type( $element['elements'], $widget_name );

					if ( $element ) {
						array_push( $this->widget_elements, $element );
					}
				}
			}
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

		protected function add_js_code() {
			add_action( 'wp_footer', array( $this, 'append_js_code' ), 1000 );
		}

		/**
		 * Define if a shortcode or archive page using powerpack
		 */
		public function is_page_has_elementor( $filter_id, $meta ) {
			$this->filter_id       = $filter_id;
			$this->filter_settings = $meta['setting'];
			$has_elementor_pro     = false;
			$require_js_code       = false;
			if ( 'on' === $this->filter_settings['is_shortcode'] && 'elementor_pro' === $this->filter_settings['shortcode_type'] ) {
				$has_elementor_pro  = true;
				$this->is_shortcode = true;
				$require_js_code    = true;
			} elseif ( is_shop() || is_tax( get_object_taxonomies( 'product' ) ) ) {
				$require_js_code       = true;
				$this->is_shop_archive = true;
			} elseif ( wp_doing_ajax() && isset( $_POST['elementor_pro'] ) ) {
				$require_js_code   = false;
				$has_elementor_pro = true;
				if ( 'true' === $GLOBALS['pwf_main_query']['is_shop_archive'] ) {
					$this->is_shop_archive = true;
				}
			}

			if ( $require_js_code ) {
				$this->add_js_code();
			}

			if ( $has_elementor_pro ) {
				$this->init_elementor_pro();
			}
		}

		public function setup_loop_args_for_archive( $args ) {
			$args['columns'] = absint( get_option( 'woocommerce_catalog_columns', 4 ) );

			return $args;
		}

		public function append_js_code() {
			?>
			<script type="text/javascript">
				(function( $ ) {
					"use strict";

					if ( typeof pwfWooHooks !== "undefined" && typeof pwffilterVariables !== "undefined" ) {
						let archiveTemplateID = '';
						function elementorProCusotmizeDataBeforeAjax( data ) {
							if ( '' === archiveTemplateID ) {
								let isProductArchive  = $('div[data-elementor-type=product-archive]');
								if ( isProductArchive.length > 0 ) {
									archiveTemplateID = $(isProductArchive).attr('data-elementor-id');
									if ( undefined === archiveTemplateID ) {
										archiveTemplateID = '';
									}
								}
							}

							let productsContainer =  pwffilterVariables.filter_setting.products_container_selector;
							if ( '' !== productsContainer ) {
								let dataID = $( productsContainer ).closest('.elementor-element').attr('data-id');
								if ( '' !== dataID ) {
									data['elementor_pro'] = {
										'data-id' : dataID,
										'template-id': archiveTemplateID,
									};
								}
							}
							return data;
						}
						pwfWooHooks.add_filter( 'pwf_before_send_ajax_data', elementorProCusotmizeDataBeforeAjax, 10 );
					}
				})(jQuery);
			</script>
			<?php
		}
	}
}
