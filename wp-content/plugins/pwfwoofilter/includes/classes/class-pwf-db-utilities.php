<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Db_Utilities' ) ) {

	/**
	 * Functions define here are use by many other classes
	 * @ since 1.4.8
	 */
	class Pwf_Db_Utilities {
		private function __construct() {}

		/**
		 * @return string sql where
		 */
		public static function get_on_sale_where_sql() {
			global $wpdb;

			$sql      = '';
			$sale_ids = wc_get_product_ids_on_sale();
			if ( ! empty( $sale_ids ) ) {
				$sql = " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', $sale_ids ) ) . ')';
			}

			return $sql;
		}

		/**
		 * @return string sql where
		 */
		public static function get_price_where_sql( $min_price, $max_price ) {
			global $wpdb;

			/**
			 * Adjust if the store taxes are not displayed how they are stored.
			 * Kicks in when prices excluding tax are displayed including tax.
			 */
			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				$tax_class = apply_filters( 'pwf_woocommerce_price_filter_tax_class', '' ); // Uses standard tax class.
				$tax_rates = WC_Tax::get_rates( $tax_class );

				if ( $tax_rates ) {
					$min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $min_price, $tax_rates ) );
					$max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $max_price, $tax_rates ) );
				}
			}

			$where = $wpdb->prepare(
				' AND wc_product_meta_lookup.min_price >= %f AND wc_product_meta_lookup.max_price <= %f ',
				$min_price,
				$max_price
			);

			return $where;
		}

		/**
		 * @return string sql join
		 */
		public static function get_price_join_sql() {
			global $wpdb;

			return " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}

		/**
		 * @return string sql Date sel
		 */
		public static function get_date_where_sql( $date_query ) {
			$date_query = new WP_Date_Query( $date_query );

			return $date_query->get_sql();
		}

		/**
		 * @return string sql where
		 */
		public static function get_authors_where_sql( $author_ids ) {
			global $wpdb;

			return " AND {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $author_ids ) ) . ')';
		}

		/**
		 * @return string sql where
		 */
		public static function get_out_of_stock_prdoucts_variations_where_sql( $product_ids, $stock_status ) {
			global $wpdb;

			if ( in_array( $stock_status, array( 'instock', 'outofstock' ), true ) ) {
				$operator = ' NOT IN ';
				if ( 'outofstock' === $stock_status ) {
					$operator = ' IN ';
				}

				return " AND {$wpdb->posts}.ID" . $operator . '(' . implode( ',', array_map( 'absint', $product_ids ) ) . ') ';
			}

			return '';
		}

		/**
		 * Based on WP_Query::parse_search since 3.7.0 && WC_Query::get_main_search_query_sql
		 *
		 * @return string sql where
		 */
		public static function get_search_where_sql( $search_query, $filter_id, $query_parse ) {
			global $wpdb;

			// Equal to args['sentence'] in WP_Query::parse_search
			$sentence     = apply_filters( 'pwf_terms_count_search_sentence_parm', '' );
			$exact        = apply_filters( 'pwf_terms_count_search_exact_parm', '' );
			$search_terms = stripslashes( $search_query );
			$search_terms = str_replace( array( "\r", "\n" ), '', $search_terms );

			if ( ! empty( $sentence ) ) {
				$search_terms = array( $search_terms );
			} else {
				$search_string = $search_terms;
				if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $search_terms, $matches ) ) {

					$query        = new WP_Query();
					$search_terms = $query->parse_search_terms( $matches[0] );
					// If the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence.
					if ( empty( $search_terms ) || count( $search_terms ) > 9 ) {
						$search_terms = array( $search_string );
					}
				} else {
					$search_terms = array( $search_terms );
				}
			}

			$n = ! empty( $exact ) ? '' : '%'; // used with order by title doesn't require here

			$exclusion_prefix = apply_filters( 'wp_query_search_exclusion_prefix', '-' );

			foreach ( $search_terms as $term ) {
				// If there is an $exclusion_prefix, terms prefixed with it should be excluded.
				$exclude = $exclusion_prefix && ( substr( $term, 0, 1 ) === $exclusion_prefix );
				if ( $exclude ) {
					$like_op  = 'NOT LIKE';
					$andor_op = 'AND';
					$term     = substr( $term, 1 );
				} else {
					$like_op  = 'LIKE';
					$andor_op = 'OR';
				}

				$like = $n . $wpdb->esc_like( $term ) . $n;
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$sql[] = $wpdb->prepare( "(({$wpdb->posts}.post_title $like_op %s) $andor_op ({$wpdb->posts}.post_excerpt $like_op %s) $andor_op ({$wpdb->posts}.post_content $like_op %s))", $like, $like, $like );
			}

			if ( ! empty( $sql ) && ! is_user_logged_in() ) {
				$sql[] = "($wpdb->posts.post_password = '')";
			}

			$search = ' AND ' . implode( ' AND ', $sql );

			return apply_filters( 'pwf_woo_search_where_string', $search, $filter_id, $query_parse );
		}

		/**
		 * used by many functions on shortcode calss
		 *
		 * @return string sql where
		 */
		public static function get_product_ids_where_sql( $product_ids ) {
			global $wpdb;

			return " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', $product_ids ) ) . ')';
		}
	}
}
