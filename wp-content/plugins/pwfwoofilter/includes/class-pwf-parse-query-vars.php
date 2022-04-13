<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Parse_Query_Vars' ) ) {

	class Pwf_Parse_Query_Vars {
		protected $filter_id;
		protected $filter_setting;
		protected $filter_items;
		protected $tax_query         = array();
		protected $meta_query        = array();
		protected $has_price_item    = false;
		protected $price_item_values = '';
		protected $selected_items    = array(); // hold url key as key and selected values for item selected
		protected $orderby           = '';
		protected $authors_id        = array();
		protected $date_query        = array();
		protected $custom_tax_query  = array();
		protected $custom_meta_query = array();
		protected $tax_query_items   = array();
		protected $filter_items_key  = array(); // this include date and price hold active filter items keys used in api
		protected $search_query      = '';
		protected $query_string      = array();
		protected $outofstock_ids    = array();
		protected $outofstock_terms  = array();
		protected $has_on_sale       = false;
		protected $stock_status      = 'none'; // May be none, active, instock, outofstock

		/**
		 * @since 1.0.0, 1.2.9
		 */
		public function __construct( $filter_id, $query_vars ) {
			$this->filter_id = $filter_id;
			$meta            = get_post_meta( absint( $this->filter_id ), '_pwf_woo_post_filter', true );

			if ( false === $meta ) {
				return;
			}

			$this->filter_items   = $meta['items'];
			$this->filter_setting = $meta['setting'];

			add_action( 'pwf_init_parse_query', array( $this, 'integrate_shortcode' ), 10, 1 );

			do_action( 'pwf_init_parse_query', $this->filter_id, $meta );

			$this->parse_query_vars( $query_vars );
			$this->set_out_of_stock_product_variations_ids();
		}

		public function get_filter_items_key() {
			return $this->filter_items_key;
		}
		public function get_filter_id() {
			return $this->filter_id;
		}

		public function get_tax_query() {
			return $this->tax_query;
		}

		public function get_meta_query() {
			return $this->meta_query;
		}

		public function get_date_query() {
			return $this->date_query;
		}

		public function get_filter_items() {
			return $this->filter_items;
		}

		public function get_filter_setting() {
			return $this->filter_setting;
		}

		public function selected_items() {
			return $this->selected_items;
		}

		public function has_price_item() {
			return $this->has_price_item;
		}

		public function get_current_min_max_price() {
			return $this->price_item_values;
		}

		public function get_products_orderby() {
			return $this->orderby;
		}

		public function get_authors_id() {
			return $this->authors_id;
		}

		public function get_tax_query_filter_items() {
			return $this->tax_query_items;
		}

		/**
		 * @since 1.4.7
		 */
		public function has_on_sale() {
			return $this->has_on_sale;
		}

		/**
		 * Used to get tax query with product visibilty
		 * and add current archive product page like category, tag, taxonomy
		 */
		public function get_custom_tax_query() {
			return $this->custom_tax_query;
		}

		public function get_custom_meta_query() {
			return $this->custom_meta_query;
		}

		public function get_search_query() {
			return $this->search_query;
		}

		public function get_query_string() {
			return implode( '&', $this->query_string );
		}

		public function get_out_of_stock_ptoduct_variations_ids() {
			if ( ! empty( $this->outofstock_ids ) ) {
				$this->outofstock_ids = array_unique( $this->outofstock_ids, SORT_NUMERIC );
			}
			return $this->outofstock_ids;
		}

		public function get_current_stock_status() {
			return $this->stock_status;
		}

		/**
		 * Check stock status
		 */
		public function is_stock_status_active() {
			$is_active    = false;
			$stock_status = $this->get_current_stock_status();
			$product_ids  = $this->get_out_of_stock_ptoduct_variations_ids();
			if ( in_array( $stock_status, array( 'instock', 'outofstock' ), true ) && ! empty( $product_ids ) ) {
				$is_active = true;
			}

			return $is_active;
		}

		private function get_current_page_tax_query() {
			$tax_query = array();
			if ( 'yes' === $GLOBALS['pwf_main_query']['shop_integrated'] && ! empty( $GLOBALS['pwf_main_query']['current_page_type'] ) && ! empty( $GLOBALS['pwf_main_query']['current_page_id'] ) ) {
				if ( in_array( $GLOBALS['pwf_main_query']['current_page_type'], get_object_taxonomies( 'product' ), true ) ) {
					$taxonomy_id   = absint( $GLOBALS['pwf_main_query']['current_page_id'] );
					$taxonomy_name = $GLOBALS['pwf_main_query']['current_page_type'];
					$tax_query[]   = array(
						'taxonomy'         => esc_attr( $taxonomy_name ),
						'field'            => 'term_id',
						'terms'            => $taxonomy_id,
						'operator'         => 'IN',
						'include_children' => true,
					);
				}
			}

			return $tax_query;
		}

		private function append_custom_tax_query( $filter_tax_query ) {
			$product_visibility   = self::get_product_visibility();
			$current_shop_archive = $this->get_current_page_tax_query();

			$this->custom_tax_query = apply_filters( 'pwf_parse_taxonomy_query', array_merge( $product_visibility, $current_shop_archive ), $this->filter_id );

			$tax_query = array_merge( $this->custom_tax_query, $filter_tax_query );

			return $tax_query;
		}

		private function append_custom_meta_query( $filter_meta_query ) {
			$meta_query = array();
			$meta_query = apply_filters( 'pwf_parse_meta_query', $meta_query, $this->filter_id );
			if ( ! empty( $meta_query ) ) {
				$meta_query['relation'] = 'AND';
			}
			$this->custom_meta_query = $meta_query;
			$filter_meta_query       = array_merge( $this->custom_meta_query, $filter_meta_query );
			if ( ! empty( $filter_meta_query ) ) {
				if ( ! isset( $filter_meta_query['relation'] ) ) {
					$filter_meta_query['relation'] = 'AND';
				}
			}

			return $filter_meta_query;
		}

		public static function get_product_visibility() {

			$tax_query['relation'] = 'AND';

			$exclude_from_catalog = array(
				'taxonomy'         => 'product_visibility',
				'terms'            => array( 'exclude-from-catalog' ),
				'field'            => 'slug',
				'operator'         => 'NOT IN',
				'include_children' => true,
			);

			$tax_query[] = $exclude_from_catalog;

			/* this doesnt do any thing
			 * if yes to get option
			 */
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$tax_query[] = self::get_tax_query_out_of_stock();
			}

			return $tax_query;
		}

		public static function get_tax_query_out_of_stock() {
			return array(
				'taxonomy'         => 'product_visibility',
				'terms'            => array( 'outofstock' ),
				'field'            => 'slug',
				'operator'         => 'NOT IN',
				'include_children' => true,
			);
		}

		/**
		 * parse query get by frontend
		 *
		 * set $meta_query
		 * set $tax_query
		 * set price_query
		 *
		 * @since 1.0.0, 1.2.8
		 */
		private function parse_query_vars( $query_vars ) {
			$tax_query    = array();
			$meta_query   = array();
			$filter_items = self::get_filter_items_without_columns( $this->filter_items );

			if ( ! empty( $query_vars ) ) {
				foreach ( $filter_items as $item ) {
					if ( ! isset( $item['url_key'] ) || empty( $item['url_key'] ) ) {
						continue;
					}

					// used if request come from api
					$is_price_item   = false;
					$is_date_item    = false;
					$is_range_slider = false;
					if ( 'priceslider' === $item['item_type'] && 'two' === $item['price_url_format'] ) {
						if ( array_key_exists( $item['url_key_min_price'], $query_vars ) || array_key_exists( $item['url_key_max_price'], $query_vars ) ) {
							$is_price_item = true;
						}
					}

					if ( 'date' === $item['item_type'] ) {
						if ( array_key_exists( $item['url_key_date_after'], $query_vars ) || array_key_exists( $item['url_key_date_before'], $query_vars ) ) {
							$is_date_item = true;
						}
					}

					if ( 'rangeslider' === $item['item_type'] && 'two' === $item['range_slider_url_format'] ) {
						if ( array_key_exists( $item['url_key_range_slider_min'], $query_vars ) || array_key_exists( $item['url_key_range_slider_max'], $query_vars ) ) {
							$is_range_slider = true;
						}
					}

					if ( ( array_key_exists( $item['url_key'], $query_vars ) && ! empty( $query_vars[ $item['url_key'] ] ) ) || $is_price_item || $is_date_item || $is_range_slider ) {
						$url_key = $item['url_key'];
						if ( 'priceslider' !== $item['item_type'] && 'date' !== $item['item_type'] ) {
							$values = $query_vars[ $url_key ];
						}

						if ( 'priceslider' === $item['item_type'] ) {
							if ( 'two' === $item['price_url_format'] ) {
								if ( array_key_exists( $item['url_key_min_price'], $query_vars ) || array_key_exists( $item['url_key_max_price'], $query_vars ) ) {
									$values    = array();
									$min_price = ( $query_vars[ $item['url_key_min_price'] ] ) ?? 0;
									$max_price = ( $query_vars[ $item['url_key_max_price'] ] ) ?? PHP_INT_MAX;
									$values    = array( $min_price, $max_price );
								} elseif ( ! is_array( $query_vars[ $url_key ] ) ) {
									$values = explode( ',', $query_vars[ $url_key ] );
								} else {
									$values = $query_vars[ $url_key ];
								}
							} else {
								if ( ! is_array( $query_vars[ $url_key ] ) ) {
									$values = explode( '-', $query_vars[ $url_key ] );
								} else {
									$values = $query_vars[ $url_key ];
								}
							}
						} elseif ( 'rangeslider' === $item['item_type'] ) {
							if ( 'two' === $item['range_slider_url_format'] ) {
								if ( array_key_exists( $item['url_key_range_slider_min'], $query_vars ) || array_key_exists( $item['url_key_range_slider_max'], $query_vars ) ) {
									$values    = array();
									$min_value = $query_vars[ $item['url_key_range_slider_min'] ];
									$max_value = $query_vars[ $item['url_key_range_slider_max'] ];
									$values    = array( $min_value, $max_value );
								} elseif ( ! is_array( $query_vars[ $url_key ] ) ) {
									$values = explode( ',', $query_vars[ $url_key ] );
								} else {
									$values = $query_vars[ $url_key ];
								}
							} else {
								if ( ! is_array( $query_vars[ $url_key ] ) ) {
									$values = explode( '-', $query_vars[ $url_key ] );
								} else {
									$values = $query_vars[ $url_key ];
								}
							}
						} elseif ( 'date' === $item['item_type'] ) {
							// need to add if request come from ajax or API
							if ( array_key_exists( $item['url_key_date_after'], $query_vars ) || array_key_exists( $item['url_key_date_before'], $query_vars ) ) {
								$after  = $query_vars[ $item['url_key_date_after'] ];
								$before = $query_vars[ $item['url_key_date_before'] ];
								$values = array( $after, $before );
							} elseif ( is_array( $query_vars[ $url_key ] ) ) {
								// from ajax
								$values = $query_vars[ $url_key ];
							}
						} elseif ( ! is_array( $values ) ) {
							$values = explode( ',', $query_vars[ $url_key ] );
						}

						/**
						 * check item price slider with dash split it into array
						 */
						$values = array_map( 'esc_attr', $values );

						if ( 'priceslider' === $item['item_type'] ) {
							if ( count( $values ) === 2 ) {
								$this->has_price_item             = true;
								$values                           = array_map( 'absint', $values );
								$this->price_item_values          = $values;
								$this->selected_items[ $url_key ] = array(
									'values' => $values,
									'type'   => 'price',
								);
								if ( 'two' === $item['price_url_format'] ) {
									array_push( $this->filter_items_key, $item['url_key_min_price'] );
									array_push( $this->filter_items_key, $item['url_key_max_price'] );
									$this->build_query_string( $item['url_key_min_price'], $values[0] );
									$this->build_query_string( $item['url_key_max_price'], $values[1] );
								} else {
									array_push( $this->filter_items_key, $url_key );
									$this->build_query_string( $url_key, $values[0] . '-' . $values[1] );
								}
							}
						} elseif ( 'rangeslider' === $item['item_type'] ) {
							if ( count( $values ) === 2 ) {
								$values = array( floatval( wc_clean( wp_unslash( $values[0] ) ) ), floatval( wc_clean( wp_unslash( $values[1] ) ) ) );
								if ( $values[0] && $values[1] ) {
									if ( 'meta' === $item['source_of_options'] ) {

										if ( 'custom' === $item['range_slider_meta_source'] ) {
											$meta_key = $item['meta_key'];
										} else {
											$meta_key = $item['range_slider_meta_source'];
										}

										$meta = array(
											'key'     => esc_attr( $meta_key ),
											'value'   => $values,
											'compare' => 'BETWEEN',
											'type'    => 'NUMERIC',
										);

										array_push( $meta_query, $meta );

										if ( '_wc_average_rating' === $meta_key ) {
											$rating_ids = array_map( 'absint', $values );
											$rating_ids = range( $values[0], $values[1] );

											$this->selected_items[ $url_key ] = array(
												'values'   => $values,
												'term_ids' => $this->get_rating_term_ids( $rating_ids ),
												'key'      => 'product_visibility',
												'type'     => 'rating',
											);
										} else {
											$this->selected_items[ $url_key ] = array(
												'values' => $values,
												'key'    => $meta_key,
												'type'   => 'rangeslider',
												'title'  => $item['title'], // used with analytic class
											);
										}
									} else {
										$tax = self::get_range_slider_tax_query( $item, $values );
										array_push( $tax_query, $tax );
										$this->selected_items[ $url_key ] = array(
											'values' => $values,
											'key'    => $tax['taxonomy'],
											'type'   => 'taxonomy',
										);
									}

									if ( 'two' === $item['range_slider_url_format'] ) {
										array_push( $this->filter_items_key, $item['url_key_range_slider_min'] );
										array_push( $this->filter_items_key, $item['url_key_range_slider_max'] );
										$this->build_query_string( $item['url_key_range_slider_min'], $values[0] );
										$this->build_query_string( $item['url_key_range_slider_max'], $values[1] );
									} else {
										array_push( $this->filter_items_key, $url_key );
										$this->build_query_string( $url_key, $values[0] . '-' . $values[1] );
									}
								}
							}
						} elseif ( 'date' === $item['item_type'] && count( $values ) === 2 ) {
							$date_from = $values[0];
							$date_to   = $values[1];
							if ( self::check_is_date( $date_from ) && self::check_is_date( $date_to ) ) {
								$year_form  = gmdate( 'Y', strtotime( $date_from ) );
								$month_form = gmdate( 'm', strtotime( $date_from ) );
								$day_form   = gmdate( 'd', strtotime( $date_from ) );
								$year_to    = gmdate( 'Y', strtotime( $date_to ) );
								$month_to   = gmdate( 'm', strtotime( $date_to ) );
								$day_to     = gmdate( 'd', strtotime( $date_to ) );
								$date_query = array(
									'relation' => 'AND',
									array(
										'after'     => array(
											'year'  => absint( $year_form ),
											'month' => absint( $month_form ),
											'day'   => absint( $day_form ),
										),
										'before'    => array(
											'year'  => absint( $year_to ),
											'month' => absint( $month_to ),
											'day'   => absint( $day_to ),
										),
										'inclusive' => true,
									),
								);

								$this->date_query                 = $date_query;
								$this->selected_items[ $url_key ] = array(
									'values' => $values,
									'type'   => 'date',
								);
								array_push( $this->filter_items_key, $item['url_key_date_after'] );
								array_push( $this->filter_items_key, $item['url_key_date_before'] );
								$this->build_query_string( $item['url_key_date_after'], $values[0] );
								$this->build_query_string( $item['url_key_date_before'], $values[1] );
							}
						} elseif ( 'search' === $item['item_type'] ) {
							$this->search_query               = esc_attr( implode( ' ', $values ) );
							$this->selected_items[ $url_key ] = array(
								'values' => array_map( 'esc_attr', $values ),
								'type'   => 'search',
							);
							array_push( $this->filter_items_key, $url_key );
							$this->build_query_string( $url_key, $this->search_query );
						} elseif ( 'rating' === $item['item_type'] ) {
							$terms           = array();
							$selected_values = $values;
							if ( 'on' === $item['up_text'] ) {
								$selected_values = explode( '-', $values[0] );
								$selected_values = range( $selected_values[0], $selected_values[1] );
								if ( 1 === count( $selected_values ) ) {
									continue;
								}
							}

							$selected_values = array_map( 'absint', $selected_values );
							$terms           = $this->get_rating_term_ids( $selected_values );

							$tax = array(
								'taxonomy'      => 'product_visibility',
								'field'         => 'term_taxonomy_id',
								'terms'         => $terms,
								'operator'      => 'IN',
								'rating_filter' => true,
							);
							array_push( $tax_query, $tax );

							if ( 'on' === $item['up_text'] ) {
								$values = array( esc_attr( $values[0] ) );
							} else {
								$values = array_map( 'absint', $values );
							}

							$this->selected_items[ $url_key ] = array(
								'values'   => $values,
								'term_ids' => $terms,
								'key'      => 'product_visibility',
								'type'     => 'rating',
							);
							array_push( $this->filter_items_key, $url_key );
							$this->build_query_string( $url_key, implode( ',', $values ) );
						} elseif ( 'stock_status' === $item['source_of_options'] ) {
							if ( 1 < count( $values ) ) {
								// Maybe end user select instock and outstock
								$this->stock_status = 'none';
							} else {
								if ( in_array( 'instock', $values, true ) ) {
									$this->stock_status = 'instock';
								} else {
									$this->stock_status = 'outofstock';
								}
							}

							$this->selected_items[ $url_key ] = array(
								'values' => $values,
								'type'   => 'stock_status',
							);
							array_push( $this->filter_items_key, $url_key );
							$this->build_query_string( $url_key, implode( ',', $values ) );
						} elseif ( 'meta' === $item['source_of_options'] ) {
							$meta_values = $this->get_meta_values( $values, $item );
							if ( ! empty( $meta_values ) ) {
								$selected_meta = array();
								foreach ( $meta_values as $meta_option ) {
									$selected_meta[] = array(
										'key'     => $item['meta_key'],
										'value'   => $meta_option['value'],
										'compare' => $item['meta_compare'],
										'type'    => $item['meta_type'],
									);
								}
								if ( count( $meta_values ) > 1 ) {
									if ( isset( $item['query_type'] ) && 'or' !== $item['query_type'] ) {
										$selected_meta['relation'] = 'AND';
									} else {
										$selected_meta['relation'] = 'OR';
									}
								}

								$this->selected_items[ $url_key ] = array(
									'values'          => $values,
									'key'             => $item['meta_key'],
									'type'            => 'meta',
									'selected_values' => $meta_values,
									'title'           => $item['title'], // used with analytic class
								);

								/**
								 * If meta is rating manipulate it as taxonomy for analytic data
								 */
								if ( '_wc_average_rating' === $item['meta_key'] ) {
									if ( strpos( $values[0], '-' ) !== false ) {
										$values = explode( '-', $values[0] );
										$values = range( $values[0], $values[1] );
									}

									$values = array_map( 'absint', $values );

									$this->selected_items[ $url_key ]['term_ids'] = $this->get_rating_term_ids( $values );
									$this->selected_items[ $url_key ]['key']      = 'product_visibility';
									$this->selected_items[ $url_key ]['type']     = 'rating';
								}
								array_push( $meta_query, $selected_meta );
								array_push( $this->filter_items_key, $url_key );
								$this->build_query_string( $url_key, implode( ',', $values ) );
							}
						} elseif ( 'orderby' === $item['source_of_options'] ) {
							$this->orderby                    = $values;
							$this->selected_items[ $url_key ] = array(
								'values' => $values,
								'type'   => 'orderby',
							);
							array_push( $this->filter_items_key, $url_key );
							$this->build_query_string( $url_key, implode( ',', $values ) );
						} elseif ( 'author' === $item['source_of_options'] ) {
							$values                           = array_map( 'absint', $values );
							$this->authors_id                 = array_merge( $this->authors_id, $values );
							$this->selected_items[ $url_key ] = array(
								'values' => $values,
								'type'   => 'vendor',
							);
							array_push( $this->filter_items_key, $url_key );
							$this->build_query_string( $url_key, implode( ',', $this->get_users_nicename( $values ) ) );
						} elseif ( 'featured' === $item['source_of_options'] ) {
							if ( is_int( $values[0] ) ) {
								$values = array_map( 'absint', $values );
							} else {
								// if values come from url directly
								$product_visibility_term_ids = wc_get_product_visibility_term_ids();
								$values                      = array( absint( $product_visibility_term_ids['featured'] ) );
							}

							$tax = array(
								'taxonomy' => 'product_visibility',
								'field'    => 'term_taxonomy_id',
								'terms'    => $values,
								'operator' => 'IN',
							);

							$this->selected_items[ $url_key ] = array(
								'values' => $values,
								'key'    => 'product_visibility',
								'type'   => 'taxonomy',
							);
							array_push( $tax_query, $tax );
							array_push( $this->filter_items_key, $url_key );
							$this->build_query_string( $url_key, 'yes' );
						} elseif ( 'on_sale' === $item['source_of_options'] ) {
							if ( 'yes' === $values[0] ) {
								$this->has_on_sale = true;

								$this->selected_items[ $url_key ] = array(
									'values' => array( 'yes' ),
									'key'    => 'on_sale',
									'type'   => 'on_sale',
								);
								array_push( $this->filter_items_key, $url_key );
								$this->build_query_string( $url_key, 'yes' );
							}
						} else {
							$operator = 'IN';
							if ( isset( $item['query_type'] ) && 'or' !== $item['query_type'] ) {
								$operator = 'AND';
							}

							if ( 'category' === $item['source_of_options'] ) {
								$taxonomy = 'product_cat';
							} elseif ( 'attribute' === $item['source_of_options'] ) {
								$taxonomy = $item['item_source_attribute'];
							} elseif ( 'taxonomy' === $item['source_of_options'] ) {
								$taxonomy = $item['item_source_taxonomy'];
							} elseif ( 'tag' === $item['source_of_options'] ) {
								$taxonomy = 'product_tag';
							}

							$values = $this->check_is_multiselect( $item, $values );
							$values = array_map( 'absint', $this->convert_terms_slug_to_id( $values, $taxonomy ) );
							$tax    = array(
								'taxonomy'         => $taxonomy,
								'field'            => 'term_id',
								'terms'            => $values,
								'operator'         => $operator,
								'include_children' => true,
							);

							if ( 'attribute' === $item['source_of_options'] ) {
								if ( isset( $item['product_variations'] ) && 'on' === $item['product_variations'] ) {
									if ( Pwf_Filter_Post_Type::is_plugin_activated() || Pwf_Filter_Post_Type::is_development_site() ) {
										array_push( $this->outofstock_terms, $tax );
									}
								}
							}

							$this->selected_items[ $url_key ] = array(
								'values' => $values,
								'key'    => $taxonomy,
								'type'   => 'taxonomy',
							);
							array_push( $this->filter_items_key, $url_key );
							array_push( $tax_query, $tax );
							$this->build_query_string( $url_key, implode( ',', $this->convert_term_ids_to_slug( $values, $taxonomy ) ) );
						}
					}
				}
			}

			$this->tax_query_items = $tax_query;
			$this->tax_query       = $this->append_custom_tax_query( $tax_query );
			$this->meta_query      = $this->append_custom_meta_query( $meta_query );
		}

		/**
		 * Count number of values and return one or more depend on field type and multi select
		 * Reutn values array depend on filter type and multi select
		 */
		private function check_is_multiselect( $item, $values ) {
			if ( 'radiolist' === $item['item_type'] ) {
				// return array contain one value
				if ( is_array( $values ) ) {
					if ( 1 === count( $values ) ) {
						return $values;
					} else {
						return array( $values[0] );
					}
				} else {
					return array( $values );
				}
			}

			$multiselect_fields = array( 'colorlist', 'boxlist', 'textlist' );
			if ( in_array( $item['item_type'], $multiselect_fields, true ) && 'on' !== $item['multi_select'] ) {
				if ( is_array( $values ) ) {
					return array( $values[0] );
				} else {
					return array( $values );
				}
			}

			return $values;
		}

		private function convert_terms_slug_to_id( $terms, $taxonomy ) {
			$the_terms = array();
			if ( ! is_numeric( $terms[0] ) ) {
				foreach ( $terms as $term ) {
					$the_term = get_term_by( 'slug', $term, $taxonomy );
					if ( false !== $the_term ) {
						$the_terms[] = $the_term->term_id;
					}
				}
				$terms = $the_terms;
			} else {
				// check if the term slug is number not string useful for size taxonomy is number
				$check_term_exist = get_term_by( 'slug', $terms[0], $taxonomy );
				if ( false !== $check_term_exist ) {
					foreach ( $terms as $term ) {
						$the_term = get_term_by( 'slug', $term, $taxonomy );
						if ( false !== $the_term ) {
							$the_terms[] = $the_term->term_id;
						}
					}
					$terms = $the_terms;
				}
			}

			return $terms;
		}

		/**
		 * return items in filter post
		 * without columns or button that hasn't url_key
		 *
		 * return array
		 */
		public static function get_filter_items_without_columns( $filter_items ) {
			$items = array();
			foreach ( $filter_items as $item ) {
				if ( 'column' === $item['item_type'] ) {
					if ( ! empty( $item['children'] ) ) {
						$children = self::get_filter_items_without_columns( $item['children'] );
						$items    = array_merge( $items, $children );
					}
				} elseif ( 'button' !== $item['item_type'] ) {
					array_push( $items, $item );
				}
			}
			return $items;
		}

		/**
		* @param string
		*
		* @return bool
		*/
		private static function check_is_date( $date ) {
			if ( false !== DateTime::createFromFormat( 'Y-m-d', $date ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * @since version 1.1.2, 1.3.8
		 */
		public function integrate_shortcode( $filter_id ) {

			$defaults = array(
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

			if ( isset( $this->filter_setting['is_shortcode'] ) && 'on' === $this->filter_setting['is_shortcode'] ) {
				if ( ! empty( $this->filter_setting['shortcode_string'] ) ) {
					$shortcode  = $this->filter_setting['shortcode_string'];
					$first_char = substr( $shortcode, 0, 1 );
					if ( '[' === $first_char ) {
						$shortcode = substr( $shortcode, 1 );
					}
					$last_char = substr( $shortcode, -1, 1 );
					if ( ']' === $last_char ) {
						$len       = strlen( $shortcode ) - 1;
						$shortcode = substr( $shortcode, 0, $len );
					}

					if ( 'products' !== substr( $shortcode, 0, strlen( 'products' ) ) ) {
						return;
					}

					$shortcode = str_replace( 'products', '', $shortcode );
					$atts      = shortcode_parse_atts( $shortcode );

					if ( ! empty( $atts ) && is_array( $atts ) ) {
						$atts = wp_parse_args( $atts, $defaults );

						$customize_shortcode = new Pwf_Integrate_Shortcode( $filter_id, $atts );
					}
				}
			}
		}

		/**
		 * Get taxonmy query for range slider
		 *
		 * @since 1.1.4
		 *
		 * @param array $filteritem filter item options
		 * @param array $values selected values for filter item
		 *
		 * @return array taxonomy query
		 */
		public static function get_range_slider_tax_query( $filter_item, $values ) {
			$used_terms   = array();
			$min_value    = $values[0];
			$max_value    = $values[1];
			$item_display = $filter_item['item_display'] ?? '';

			if ( 'attribute' === $filter_item['source_of_options'] ) {
				$taxonomy = $filter_item['item_source_attribute'];
			} elseif ( 'taxonomy' === $filter_item['source_of_options'] ) {
				$taxonomy = $filter_item['item_source_taxonomy'];
			}

			$args = array(
				'taxonomy'   => esc_attr( $taxonomy ),
				'hide_empty' => true,
			);

			if ( 'selected' === $item_display && ! empty( $item['include'] ) ) {
				$args['include'] = array_map( 'absint', $item['include'] );
			} elseif ( 'except' === $item_display && ! empty( $item['exclude'] ) ) {
				$args['include'] = array_map( 'absint', $item['exclude'] );

				$term_ids = get_terms(
					array(
						'taxonomy'   => $args['taxonomy'],
						'hide_empty' => false,
						'fields'     => 'ids',
					)
				);
				foreach ( $term_ids as $key => $term_id ) {
					if ( in_array( $term_id, $args['exclude'], true ) ) {
						unset( $term_ids[ $key ] );
					}
				}
				$args['include'] = $term_ids;
			}

			$terms = get_terms( $args );
			foreach ( $terms as $term ) {
				if ( $term->name >= $min_value && $term->name <= $max_value ) {
					array_push( $used_terms, $term->term_id );
				}
			}

			$tax_query = array(
				'taxonomy' => esc_attr( $taxonomy ),
				'field'    => 'term_id',
				'terms'    => $used_terms,
				'operator' => 'IN',
			);

			return $tax_query;
		}

		/**
		 * Processing the user selected metas to Understandable values to WordPress
		 *
		 * @param array $slugs realted to end user selected values
		 * @param array $item represnt meta field
		 *
		 * @since 1.2.4
		 * @return array foreach meta option with values, value maybe string or array
		 */
		private function get_meta_values( $slugs, $item ) {
			$result         = array();
			$value_is_array = array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );
			$numeric_tyepe  = array( 'NUMERIC', 'DECIMAL', 'SIGNED', 'UNSIGNED' );
			$meta_data      = $item['metafield'];

			foreach ( $meta_data as $meta ) {
				if ( ! isset( $meta['slug'] ) ) {
					$meta['slug'] = $meta['value']; // fix version before 1.2.2
				}
				if ( in_array( $meta['slug'], $slugs, true ) ) {
					if ( in_array( $item['meta_compare'], $value_is_array, true ) ) {
						$value = explode( ',', $meta['value'] );

						if ( in_array( $item['meta_type'], $numeric_tyepe, true ) ) {
							$value = array_map( 'floatval', $value );
						} else {
							$value = array_map( 'esc_attr', $value );
						}
					} else {
						if ( in_array( $item['meta_type'], $numeric_tyepe, true ) ) {
							$value = floatval( $meta['value'] );
						} else {
							$value = esc_attr( $meta['value'] );
						}
					}

					$result[] = array(
						'slug'  => $meta['slug'],
						'value' => $value,
						'label' => $meta['label'], // used with analytic class
					);
				}
			}
			return $result;
		}

		/**
		 * Get ids for the rating
		 * If meta or range slider is rating
		 * manipulate it as taxonomy for analytic data
		 *
		 * @since 1.2.8
		 *
		 * @return Array For rating term ID
		 */
		private function get_rating_term_ids( $values ) {
			$terms                    = array();
			$product_visibility_terms = wc_get_product_visibility_term_ids();
			foreach ( $values as $value ) {
				array_push( $terms, $product_visibility_terms[ 'rated-' . $value ] );
			}

			return $terms;
		}

		protected function build_query_string( $url_key, $value ) {
			$this->query_string[] = $url_key . '=' . $value;
		}

		protected function get_users_nicename( $ids ) {
			$users = get_users(
				array(
					'include' => $ids,
					'fields'  => array( 'user_nicename' ),
				)
			);

			$nice_names = array();
			foreach ( $users as $user ) {
				array_push( $nice_names, $user->user_nicename );
			}

			return $nice_names;
		}

		protected function convert_term_ids_to_slug( $values, $taxonomy_name ) {
			$slugs = get_terms(
				array(
					'taxonomy' => $taxonomy_name,
					'include'  => $values,
					'fields'   => 'slugs',
				)
			);

			return $slugs;
		}

		/**
		 * Get out of stock products IDS foreach product variations
		 * @since 1.4.8
		 *
		 * @return array product_ids
		 */
		protected function set_out_of_stock_product_variations_ids() {
			if ( empty( $this->outofstock_terms ) ) {
				return;
			}

			$args = array(
				'values'       => array(),
				'attrs_filter' => array(),
				'direct_db'    => true,
				'filter_id'    => $this->filter_id,
			);

			foreach ( $this->outofstock_terms as $term ) {
				$slugs = array();
				foreach ( $term['terms'] as $term_id ) {
					$current_term = get_term_by( 'id', $term_id, $term['taxonomy'], ARRAY_A );
					if ( $current_term ) {
						array_push( $args['values'], $current_term['slug'] );
						array_push( $slugs, $current_term['slug'] );
					}
				}
				if ( ! empty( $slugs ) ) {
					$args['attrs_filter'][ $term['taxonomy'] ] = array(
						'slugs'    => $slugs,
						'operator' => $term['operator'],
					);
				}
			}

			if ( 1 !== count( $args['values'] ) ) {
				foreach ( $args['attrs_filter'] as $key => $attr ) {
					if ( 'IN' === $attr['operator'] ) {
						$args['direct_db'] = false;
						break;
					}
				}
			}

			$this->outofstock_ids = $this->get_out_of_stock_product_variations_ids( $args );
			if ( ! empty( $this->outofstock_ids ) && 'none' === $this->stock_status ) {
				$this->stock_status = 'active';
			}
		}

		/**
		 * Set database query to get out of stock product IDs
		 * @param array $args have all selected attributes
		 * @since 1.4.8
		 *
		 * @return array out of stock product_ids
		 */
		protected function get_out_of_stock_product_variations_ids( $args ) {
			global $wpdb;

			$meta_query     = new WP_Meta_Query( $this->get_meta_query() ); // Use this when you need to filter with post meta
			$tax_query      = new WP_Tax_Query( $this->get_tax_query() );
			$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );

			$sql_where  = $tax_query_sql['where'];
			$sql_where .= $meta_query_sql['where'];

			if ( ! empty( $this->get_date_query() ) ) {
				$sql_where .= Pwf_Db_Utilities::get_date_where_sql( $this->get_date_query() );
			}
			if ( ! empty( $this->get_search_query() ) ) {
				$sql_where .= Pwf_Db_Utilities::get_search_where_sql( $this->get_search_query(), $this->filter_id, $this );
			}
			if ( ! empty( $this->get_authors_id() ) ) {
				$sql_where .= Pwf_Db_Utilities::get_authors_where_sql( $this->get_authors_id() );
			}
			if ( $this->has_on_sale() ) {
				$sql_where .= Pwf_Db_Utilities::get_on_sale_where_sql();
			}

			$sql_join = $tax_query_sql['join'] . $meta_query_sql['join'];

			if ( $this->has_price_item() ) {
				$price      = $this->get_current_min_max_price();
				$sql_join  .= Pwf_Db_Utilities::get_price_join_sql();
				$sql_where .= Pwf_Db_Utilities::get_price_where_sql( $price[0], $price[1] );
			}

			$query = "
				WITH view_refrence AS (
					SELECT wp_posts.post_parent, wp_posts.ID AS post_id, pwf_postmeta.meta_key, pwf_postmeta.meta_value, ss.stock_status
					FROM {$wpdb->posts} AS wp_posts
					JOIN {$wpdb->postmeta} AS pwf_postmeta ON wp_posts.ID = pwf_postmeta.post_id AND pwf_postmeta.meta_key LIKE 'attribute_pa_%' AND pwf_postmeta.meta_value IN ('"
					. implode( "','", array_map( 'esc_attr', $args['values'] ) ) . "')
					JOIN (
						SELECT wp_posts.post_parent AS parent_id, wp_posts.ID AS ppost_id, wp_postmeta.meta_value AS stock_status
						FROM {$wpdb->posts} AS wp_posts
						JOIN {$wpdb->postmeta} AS wp_postmeta ON wp_posts.ID = wp_postmeta.post_id AND wp_postmeta.meta_key = '_stock_status'
						WHERE wp_posts.post_type = 'product_variation' AND wp_posts.post_status = 'publish'
					) AS ss ON ss.parent_id = wp_posts.post_parent AND ss.ppost_id = wp_posts.ID
					WHERE wp_posts.post_type = 'product_variation' AND wp_posts.post_status = 'publish'
					AND post_parent IN (
						SELECT DISTINCT {$wpdb->posts}.ID
						FROM {$wpdb->posts}"
						. $sql_join
						. " WHERE {$wpdb->posts}.post_type = 'product' AND {$wpdb->posts}.post_status = 'publish'"
						. $sql_where
						. '
					)
					GROUP BY post_parent, post_id, meta_key, meta_value
				)';
			if ( $args['direct_db'] ) {
				$query .= ' SELECT DISTINCT view_refrence.post_parent ';
			} else {
				$query .= ' SELECT view_refrence.*, atrr_count.count_attr, count_out_of_stock ';
			}
			$query .= "
				FROM view_refrence
				JOIN (
					SELECT post_parent, meta_key, meta_value, count(*) AS count_attr
					FROM view_refrence
					GROUP BY post_parent, meta_key, meta_value
				) AS atrr_count ON view_refrence.post_parent = atrr_count.post_parent AND view_refrence.meta_key = atrr_count.meta_key AND view_refrence.meta_value = atrr_count.meta_value
				LEFT JOIN (
					SELECT post_parent, meta_key, meta_value, count(*) AS count_out_of_stock
					FROM view_refrence
					WHERE stock_status = 'outofstock'
					GROUP BY post_parent, meta_key, meta_value
				) AS out_of_stock_count ON view_refrence.post_parent = out_of_stock_count.post_parent AND view_refrence.meta_key = out_of_stock_count.meta_key AND view_refrence.meta_value = out_of_stock_count.meta_value";
			if ( $args['direct_db'] ) {
				$query .= " WHERE stock_status = 'outofstock' AND count_attr = count_out_of_stock";
			}
			$query .= ' ORDER BY post_parent';

			// We have a query - let's see if cached results of this query already exist.
			$query_hash = md5( $query );

			// Maybe store a transient of the count values.
			$cache = apply_filters( 'pwf_woo_filter_count_maybe_cache', false );
			if ( true === $cache ) {
				$cached_counts = (array) get_transient( 'pwf_woo_filter_item_get_outofstock_variation' );
			} else {
				$cached_counts = array();
			}

			if ( ! isset( $cached_counts[ $query_hash ] ) ) {
				$results                      = $wpdb->get_results( $query, ARRAY_A ); // @codingStandardsIgnoreLine
				if ( ! $args['direct_db'] ) {
					$products      = self::build_products_hierarchical( $results );
					$out_of_stocks = array();
					foreach ( $args['attrs_filter'] as $arg ) {
						$out_of_stocks = array_merge( $out_of_stocks, self::filtering_out_of_stock_ids( $products, $arg ) );
					}
					$counts = array_unique( $out_of_stocks );
				} else {
					$counts = array_map( 'absint', wp_list_pluck( $results, 'post_parent' ) );
				}
				$cached_counts[ $query_hash ] = $counts;
				if ( true === $cache ) {
					set_transient( 'pwf_woo_filter_item_get_outofstock_variation', $cached_counts, self::transient_time() );
				}
			}

			return array_map( 'absint', (array) $cached_counts[ $query_hash ] );
		}

		/**
		 * Build nested array Each parent product have childern 'childern is prodcut_variations
		 * @since 1.4.8
		 *
		 *  @return array product childern inside parent product
		 */
		protected static function build_products_hierarchical( $products ) {
			$results = array();

			foreach ( $products as $product ) {
				$results[ 'parent_id_' . $product['post_parent'] ][] = $product;
			}

			return $results;
		}

		/**
		 * Check if the operator is IN OR AND for each attribue
		 * If is operator is 'IN' means this product contain out of stock for all attributes so we add ti to out_of_stocks variable
		 * @since 1.4.8
		 *
		 * @return array out of stock product_ids
		 */
		protected static function filtering_out_of_stock_ids( &$products, $args ) {
			$out_of_stock_ids = array();

			foreach ( $products as $key => $children ) {
				if ( 1 === count( $children ) ) {
					if ( 'outofstock' === $children[0]['stock_status'] ) {
						array_push( $out_of_stock_ids, $children[0]['post_parent'] );
						unset( $products[ 'parent_id_' . $children[0]['post_parent'] ] );
					}
				} else {
					$check_is_out = array();
					foreach ( $children as $product ) {
						if ( in_array( $product['meta_value'], $args['slugs'], true ) ) {
							if ( 'outofstock' === $product['stock_status'] && $product['count_attr'] === $product['count_out_of_stock'] ) {
								if ( 'IN' === $args['operator'] ) {
									array_push( $check_is_out, $product['meta_value'] );
								} else {
									array_push( $out_of_stock_ids, $children[0]['post_parent'] );
									unset( $products[ 'parent_id_' . $children[0]['post_parent'] ] );
								}
							}
						}
					}

					if ( ! empty( $check_is_out ) && count( array_unique( $check_is_out ) ) === count( $args['slugs'] ) ) {
						array_push( $out_of_stock_ids, $product['post_parent'] );
						unset( $products[ 'parent_id_' . $product['post_parent'] ] );
					}
				}
			}

			return $out_of_stock_ids;
		}
	}
}
