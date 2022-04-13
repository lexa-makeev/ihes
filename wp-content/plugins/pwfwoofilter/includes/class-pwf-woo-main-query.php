<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Woo_Main_Query' ) ) {

	class Pwf_Woo_Main_Query {

		private $filter_id;

		/**
		* The unique instance of the plugin.
		*
		* @var Pwf_Woo_Main_Query
		*/
		private static $instance;

		/**
		* The unique instance of the Pwf_Parse_Query_Vars.
		*
		* @var Pwf_Parse_Query_Vars
		*/
		private static $query_vars;

		/**
		* hook price
		*/
		private static $hook_price;

		/**
		* hook orderby
		*/
		private static $hook_orderby;

		/**
		 * Gets an instance of our plugin.
		 *
		 * @return Pwf_Woo_Main_Query
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * @since 1.0.0, 1.0.6
		 */
		private function __construct() {
			self::$query_vars   = null;
			self::$hook_price   = false;
			self::$hook_orderby = false;

			add_action( 'init', array( $this, 'init' ), 10 );
		}

		public function init() {
			$this->filter_id = get_option( 'pwf_shop_filter_id', '' );
			if ( ! empty( $this->filter_id ) ) {
				$this->filter_id = apply_filters( 'pwf_filter_id', $this->filter_id );
			}
			if ( ! empty( $this->filter_id ) ) {
				add_action( 'woocommerce_product_query', array( $this, 'woocommerce_product_query' ), 5, 1 );
				add_filter( 'the_posts', array( $this, 'remove_product_query_filters' ) );
			}
		}

		public function woocommerce_product_query( $q ) {

			// This code must be before use Pwf_Parse_Query_Vars
			$GLOBALS['pwf_main_query']['filter_id'] = absint( $this->filter_id );

			if ( is_shop() ) {
				$GLOBALS['pwf_main_query']['current_page_type'] = 'shop';
				$GLOBALS['pwf_main_query']['current_page_id']   = get_option( 'woocommerce_shop_page_id' );
			} elseif ( is_tax( get_object_taxonomies( 'product' ) ) ) {
				$GLOBALS['pwf_main_query']['current_page_type'] = get_queried_object()->taxonomy;
				$GLOBALS['pwf_main_query']['current_page_id']   = get_queried_object()->term_id;
			} else {
				return;
			}

			$GLOBALS['pwf_main_query']['shop_integrated'] = 'yes';
			$GLOBALS['pwf_main_query']['is_shop_archive'] = 'true';

			$active_items     = $this->get_active_filter_items();
			self::$query_vars = new Pwf_Parse_Query_Vars( $this->filter_id, $active_items );
			$orderby          = self::$query_vars->get_products_orderby();
			$tax_query        = self::$query_vars->get_tax_query_filter_items();
			$meta_query       = self::$query_vars->get_meta_query();
			$authors_id       = self::$query_vars->get_authors_id();
			$date_query       = self::$query_vars->get_date_query();

			$GLOBALS['pwf_main_query']['query_vars'] = self::$query_vars;

			if ( ! empty( $orderby ) && ! isset( $_GET['orderby'] ) ) {
				self::$hook_orderby = true;

				$orderby  = is_array( $orderby ) ? implode( '', $orderby ) : $orderby;
				$ordering = WC()->query->get_catalog_ordering_args( $orderby );

				$q->set( 'orderby', $ordering['orderby'] );
				$q->set( 'order', $ordering['order'] );
				if ( isset( $ordering['meta_key'] ) ) {
					$q->set( 'meta_key', $ordering['meta_key'] );
				}
			}

			if ( self::$query_vars->has_price_item() ) {
				if ( ! isset( $_GET['min_price'] ) && ! isset( $_GET['max_price'] ) ) {
					self::$hook_price = true;
					add_filter( 'posts_clauses', array( $this, 'price_filter_post_clauses' ), 10, 2 );
				}
			}

			if ( self::$query_vars->is_stock_status_active() ) {
				add_filter( 'posts_where', array( $this, 'set_stock_status_product_variations' ), 10, 2 );
			}

			if ( ! empty( self::$query_vars->has_on_sale() ) ) {
				self::$has_on_sale = true;
				add_filter( 'posts_where', array( $this, 'append_on_sale_products' ), 10, 2 );
			}

			if ( ! isset( $_GET['s'] ) && ! empty( self::$query_vars->get_search_query() ) ) {
				$q->set( 's', self::$query_vars->get_search_query() );
				$q->set( 'is_search', true );
			}

			if ( ! empty( $tax_query ) ) {
				$tax_query = array_merge( $q->get( 'tax_query' ), $tax_query );
				$q->set( 'tax_query', $tax_query );
			}

			if ( ! empty( $meta_query ) ) {
				$meta_query = array_merge( $q->get( 'meta_query' ), $meta_query );
				$q->set( 'meta_query', $meta_query );
			}

			if ( ! empty( $authors_id ) ) {
				$authors_id = array_merge( $q->get( 'author__in' ), $authors_id );
				$q->set( 'author__in', $authors_id );
			}

			if ( ! empty( $date_query ) ) {
				$q->set( 'date_query', $date_query );
			}
		}

		/**
		 * Custom query used to filter products by price.
		 *
		 * @since 3.6.0
		 *
		 * @param array    $args Query args.
		 * @param WC_Query $wp_query WC_Query object.
		 *
		 * @return array
		 */
		public function price_filter_post_clauses( $args, $wp_query ) {
			$price     = self::$query_vars->get_current_min_max_price();
			$min_price = floatval( wp_unslash( $price[0] ) );
			$max_price = floatval( wp_unslash( $price[1] ) );

			$args['join']   = $this->append_product_sorting_table_join( $args['join'] );
			$args['where'] .= Pwf_Db_Utilities::get_price_where_sql( $min_price, $max_price );

			return $args;
		}

		/**
		 * Join wc_product_meta_lookup to posts if not already joined.
		 *
		 * @param string $sql SQL join.
		 * @return string
		 */
		private function append_product_sorting_table_join( $sql ) {
			if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
				$sql .= Pwf_Db_Utilities::get_price_join_sql();
			}
			return $sql;
		}

		/**
		 * @since 1.0.0, 1.1.4
		 */
		private function get_active_filter_items() {
			$data         = array();
			$filter_items = $this->get_filter_items_url_key();
			if ( empty( $filter_items ) ) {
				return $data;
			}

			// check what item is active
			foreach ( $filter_items as $item ) {
				if ( 'priceslider' === $item['item_type'] && 'two' === $item['price_url_format'] ) {
					if ( isset( $_GET[ $item['url_key_min_price'] ] ) && isset( $_GET[ $item['url_key_max_price'] ] ) ) {
						$data[ $item['url_key'] ] = array( $_GET[ $item['url_key_min_price'] ], $_GET[ $item['url_key_max_price'] ] );
					}
				} elseif ( 'rangeslider' === $item['item_type'] && 'two' === $item['range_slider_url_format'] ) {
					if ( isset( $_GET[ $item['url_key_range_slider_min'] ] ) && isset( $_GET[ $item['url_key_range_slider_max'] ] ) ) {
						$data[ $item['url_key'] ] = array( $_GET[ $item['url_key_range_slider_min'] ], $_GET[ $item['url_key_range_slider_max'] ] );
					}
				} elseif ( 'date' === $item['item_type'] ) {
					if ( isset( $_GET[ $item['url_key_date_after'] ] ) && isset( $_GET[ $item['url_key_date_before'] ] ) ) {
						$data[ $item['url_key'] ] = array( $_GET[ $item['url_key_date_after'] ], $_GET[ $item['url_key_date_before'] ] );
					}
				} elseif ( isset( $_GET[ $item['url_key'] ] ) ) {
					$data[ $item['url_key'] ] = $_GET[ $item['url_key'] ];
				}
			}

			return $data;
		}

		private function get_filter_items_url_key() {
			$filter_items = get_post_meta( absint( $this->filter_id ), '_pwf_woo_post_filter', true );
			$filter_items = $filter_items['items'];
			if ( empty( $filter_items ) ) {
				return '';
			}
			$filter_items = Pwf_Parse_Query_Vars::get_filter_items_without_columns( $filter_items );

			return $filter_items;
		}

		/**
		 * @since 1.3.4
		 */
		public function set_stock_status_product_variations( $where, $query_obj ) {
			$product_ids  = self::$query_vars->get_out_of_stock_ptoduct_variations_ids();
			$stock_status = self::$query_vars->get_current_stock_status();
			$where       .= Pwf_Db_Utilities::get_out_of_stock_prdoucts_variations_where_sql( $product_ids, $stock_status );

			return $where;
		}

		/**
		 * @since 1.4.7
		 */
		public function append_on_sale_products( $where, $query_obj ) {
			$where .= Pwf_Db_Utilities::get_on_sale_where_sql();
			return $where;
		}

		public function remove_product_query_filters( $posts ) {
			if ( self::$hook_price ) {
				remove_filter( 'posts_clauses', array( $this, 'price_filter_post_clauses' ) );
			}

			if ( null !== self::$query_vars ) {
				if ( ! empty( self::$query_vars->has_on_sale() ) ) {
					remove_filter( 'posts_where', array( $this, 'append_on_sale_products' ), 10, 2 );
				}
				if ( self::$query_vars->is_stock_status_active() ) {
					remove_filter( 'posts_where', array( $this, 'set_stock_status_product_variations' ), 10, 2 );
				}
				if ( self::$hook_orderby ) {
					WC()->query->remove_ordering_args();
				}
			}

			return $posts;
		}
	}

	$pwf_woo_main_query = Pwf_Woo_Main_Query::get_instance();
}
