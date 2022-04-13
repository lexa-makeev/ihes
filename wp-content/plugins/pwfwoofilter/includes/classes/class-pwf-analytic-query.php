<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Analytic_Query' ) ) {

	/**
	 * @ since 1.2.8
	 */
	class Pwf_Analytic_Query {

		protected $filter_data;
		protected $selected_items;
		protected $filter_group_id;

		public function __construct( array $analytic_data ) {
			$this->filter_data    = $analytic_data['filter_data'];
			$this->selected_items = $analytic_data['selected_values'];

			add_action( 'shutdown', array( $this, 'init' ) );
		}

		public function init() {
			self::check_db_tables();

			$this->filter_group_id = $this->insert_filter();

			if ( false === $this->filter_group_id ) {
				return;
			}

			$this->insert_selected_items();
		}

		protected function insert_filter() {
			global $wpdb;

			$user_id        = null;
			$enable_user_id = get_option( 'pwf_shop_analytics_save_user_id', 'disable' );
			if ( 'enable' === $enable_user_id ) {
				if ( ! empty( $GLOBALS['pwf_main_query']['user_id'] ) ) {
					$user_id = absint( $GLOBALS['pwf_main_query']['user_id'] );
				} elseif ( get_current_user_id() ) {
					$user_id = get_current_user_id();
				}
			}

			if ( ! empty( $GLOBALS['pwf_main_query']['lang'] ) ) {
				$lang = $GLOBALS['pwf_main_query']['lang'];
			} elseif ( defined( 'ICL_LANGUAGE_CODE' ) ) {
				$lang = ICL_LANGUAGE_CODE;
			} else {
				$lang = get_locale();
			}

			if ( ! empty( $GLOBALS['pwf_main_query']['current_page_id'] ) ) {
				$page_type = esc_sql( $GLOBALS['pwf_main_query']['current_page_type'] );
				$page_id   = absint( $GLOBALS['pwf_main_query']['current_page_id'] );
			} else {
				$page_type = null;
				$page_id   = null;
			}

			$wpdb->insert(
				$wpdb->prefix . 'wc_pwf_filters',
				array(
					'filter_post_id' => absint( $this->filter_data['filter_post_id'] ),
					'lang'           => esc_sql( $lang ),
					'from'           => absint( $this->filter_data['from'] ),
					'products_count' => absint( $this->filter_data['products_count'] ),
					'user_id'        => $user_id,
					'user_request'   => 0,
					'page_type'      => $page_type,
					'page_id'        => $page_id,
					'query_string'   => esc_sql( $this->filter_data['query_string'] ),
					'date'           => gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) ), // @codingStandardsIgnoreLine
				),
				array(
					'%d',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%d',
					'%s',
					'%s',
				)
			);

			return $wpdb->insert_id;
		}

		protected function insert_selected_items() {
			foreach ( $this->selected_items as $item ) {
				$term_ids = array();

				// Don't add date option to analytic query
				if ( 'date' === $item['type'] ) {
					continue;
				}

				if ( 'meta' === $item['type'] ) {
					$meta_item = $this->prepare_meta_before_insert( $item );
					$term_ids  = $this->update_terms( $meta_item );
				} else {
					switch ( $item['type'] ) {
						case 'rating':
							$item['values'] = $item['term_ids'];
							break;
						case 'vendor':
							$item['key'] = 'vendor';
							break;
						case 'orderby':
							$item['key'] = 'orderby';
							break;
						case 'stock_status':
							$item['key'] = 'stock_status';
							break;
						case 'price':
							$item['key']             = $item['type'];
							$item['selected_values'] = $item['values'];
							$item['values']          = array( $item['type'] );
							break;
						case 'rangeslider':
							$item['selected_values'] = $item['values'];
							$item['values']          = array( $item['type'] );
							$this->save_meta_title_to_range_slider( $item );
							break;
					}

					if ( 'search' !== $item['type'] ) {
						$term_ids = $this->update_terms( $item );
					}
				}

				if ( 'search' === $item['type'] ) {
					$effected_rows = $this->insert_values_in_table_searches( $item['values'][0] );
				} elseif ( ! empty( $term_ids ) ) {
					if ( 'price' === $item['type'] || 'rangeslider' === $item['type'] ) {
						$effected_rows = $this->insert_values_in_table_range_sliders( $term_ids[0], $item['selected_values'] );
					} else {
						$effected_rows = $this->insert_terms_in_table_filters_terms( $term_ids );
					}
				}
			}
		}

		/**
		 * Check if Terms exists in the table wc_pwf_terms [ taxonomy - metakey - stock status - orderby - vendor]
		 * Exists return terms_id
		 * Not exist create it
		 * @param array
		 *
		 * @return Array term_ids
		 */
		protected function update_terms( $item ) {
			global $wpdb;

			$term_ids  = array();
			$where_sql = array();
			foreach ( $item['values'] as $value ) {
				$where_sql[] = "term_value = '" . esc_attr( $value ) . "'";
			}

			$where_sql    = implode( ' OR ', $where_sql );
			$get_term_ids = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, term_value FROM %1s WHERE {$where_sql} AND term_key = '%2s' AND term_type = '%3s'", // @codingStandardsIgnoreLine
					$wpdb->prefix . 'wc_pwf_terms',
					esc_attr( $item['key'] ),
					esc_attr( $item['type'] )
				),
				'ARRAY_A'
			);

			if ( empty( $get_term_ids ) ) {
				foreach ( $item['values'] as $value ) {
					$term = array(
						'value' => $value,
						'key'   => $item['key'],
						'type'  => $item['type'],
					);
					$id   = $this->insert_term_in_table_terms( $term );
					if ( false !== $id ) {
						$term_ids[] = $id;
					}
				}
			} else {
				foreach ( $item['values'] as $value ) {
					//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					$id_exist = array_search( $value, array_column( $get_term_ids, 'term_value' ) );
					if ( false !== $id_exist ) {
						$term_ids[] = $get_term_ids[ $id_exist ]['id'];
					} else {
						$term = array(
							'value' => $value,
							'key'   => $item['key'],
							'type'  => $item['type'],
						);
						$id   = $this->insert_term_in_table_terms( $term );
						if ( false !== $id ) {
							$term_ids[] = $id;
						}
					}
				}
			}

			return $term_ids;
		}

		/**
		 * Insert a term in table wc_pwf_terms
		 *
		 * @param array contain the term data
		 *
		 * @return int The ID for row
		 */
		protected function insert_term_in_table_terms( $term ) {
			global $wpdb;
			$wpdb->insert(
				$wpdb->prefix . 'wc_pwf_terms',
				array(
					'term_value' => $term['value'],
					'term_key'   => $term['key'],
					'term_type'  => $term['type'],
				),
				array(
					'%s',
					'%s',
					'%s',
				)
			);

			return $wpdb->insert_id;
		}

		protected function prepare_meta_before_insert( $item ) {
			$values = array();
			foreach ( $item['selected_values'] as $meta_option ) {
				$value = $meta_option['value'];
				if ( is_array( $value ) ) {
					$value = implode( ',', $value );
				}
				array_push( $values, $value );
			}

			$meta_item = array(
				'values' => $values,
				'key'    => $item['key'],
				'type'   => 'meta',
			);
			$this->save_meta_label( $item );

			return $meta_item;
		}

		protected function save_meta_label( $item ) {
			$meta_key     = esc_attr( $item['key'] );
			$title        = esc_attr( $item['title'] );
			$meta_options = $item['selected_values'];

			$saved_meta = get_option( 'pwf_woocommerce_analytic_meta_labels', array() );

			if ( empty( $saved_meta ) || ! isset( $saved_meta[ $meta_key ] ) ) {
				$data = array();
				foreach ( $meta_options as $meta ) {
					$value = $meta['value'];
					if ( is_array( $value ) ) {
						$value = implode( ',', $value );
					}
					$data[ esc_attr( $value ) ] = esc_attr( $meta['label'] );
				}

				$saved_meta[ $meta_key ] = array(
					'title' => $title,
					'data'  => $data,
				);
			} else {
				// meta key is exist, check options ( values )
				$data = $saved_meta[ $meta_key ]['data'] ?? array();
				foreach ( $meta_options as $meta ) {
					$value = $meta['value'];
					if ( is_array( $value ) ) {
						$value = implode( ',', $value );
					}
					$value = esc_attr( $value );
					if ( ! isset( $saved_meta[ $meta_key ]['data'][ $value ] ) || empty( $saved_meta[ $meta_key ]['data'][ $value ] ) ) {
						$saved_meta[ $meta_key ]['data'][ $value ] = esc_attr( $meta['label'] );
					}
				}
			}

			update_option( 'pwf_woocommerce_analytic_meta_labels', $saved_meta, false );
		}

		function save_meta_title_to_range_slider( $item ) {

			$saved_meta = get_option( 'pwf_woocommerce_analytic_meta_range_slider_labels', array() );
			if ( ! isset( $saved_meta[ $item['key'] ] ) ) {
				$saved_meta[ esc_attr( $item['key'] ) ] = esc_attr( $item['title'] );
				update_option( 'pwf_woocommerce_analytic_range_slider_meta_labels', $saved_meta, false );
			}
		}

		/**
		 * Insert the filter values in table wc_pwf_filters_terms
		 * Accepted Multiable insert
		 *
		 * @param array contain the selected filter items by client
		 *
		 * @return int The number of effected rows or empty
		 */
		protected function insert_terms_in_table_filters_terms( $term_ids ) {
			global $wpdb;

			$values = array();
			foreach ( $term_ids as $term_id ) {
				$values[] = '(' . $this->filter_group_id . ', ' . $term_id . ')';
			}

			$effected_rows = $wpdb->query(
				$wpdb->prepare(
					'INSERT INTO %1s ( group_id, term_id ) VALUES %2s', // @codingStandardsIgnoreLine
					$wpdb->prefix . 'wc_pwf_filters_terms',
					implode( ', ', $values ),
				)
			);

			return $effected_rows;
		}

		/**
		 * Insert the filter item price values in table wc_pwf_range_sliders
		 *
		 * @param array contain the selected filter items by client
		 *
		 * @return int The number of inserted row
		 */
		protected function insert_values_in_table_range_sliders( $term_id, $values ) {
			global $wpdb;

			$wpdb->insert(
				$wpdb->prefix . 'wc_pwf_range_sliders',
				array(
					'group_id' => $this->filter_group_id,
					'term_id'  => $term_id,
					'min'      => $values[0],
					'max'      => $values[1],
				),
				array(
					'%d',
					'%d',
					'%d',
					'%d',
				)
			);

			return $wpdb->insert_id;
		}

		protected function insert_values_in_table_searches( $value ) {
			global $wpdb;

			$wpdb->insert(
				$wpdb->prefix . 'wc_pwf_searches',
				array(
					'group_id'   => $this->filter_group_id,
					'search_for' => esc_attr( $value ),
				),
				array(
					'%d',
					'%s',
				)
			);

			return $wpdb->insert_id;
		}

		/**
		 * Check the plugin custom database table exists
		 * @since 1.2.8
		 */
		public static function check_db_tables() {
			global $wpdb;

			$wpdb->hide_errors();
			$has_db    = true;
			$db_tables = self::get_db_tables();
			foreach ( $db_tables as $table ) {
				if ( ! $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}{$table}';" ) ) { // @codingStandardsIgnoreLine
					$has_db = false;
				}
			}

			if ( ! $has_db ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( self::get_schema() );
				update_option( 'pwf_woocommerce_db_version', PWF_WOO_FILTER_DB_VERSION, false );
				$has_db = true;
			}
		}

		/**
		 * Get Table schema.
		 *
		 * @since 1.2.8
		 * @return string
		 */
		private static function get_schema() {
			global $wpdb;

			$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

			/**
			 * Table wc_pwf_filters
			 * The from column define from where filter comee 1 is ajax and 2 is API
			 * the column user_request 0 no 1 yes Yes mean the user need email alert
			 * when this group of filter has products
			 */
			$tables = "
				CREATE TABLE {$wpdb->prefix}wc_pwf_filters (
					group_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					filter_post_id BIGINT NOT NULL,
					lang VARCHAR(20) NULL,
					`from` TINYINT(1) NOT NULL DEFAULT 1,
					products_count INT NOT NULL DEFAULT 0,
					user_id BIGINT NULL,
					user_request TINYINT(1) NOT NULL DEFAULT 0,
					page_type VARCHAR(255) NULL,
					page_id BIGINT NULL,
					query_string VARCHAR(1000) NULL,
					date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (group_id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}wc_pwf_terms ( 
					`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					`term_value` VARCHAR(255),
					`term_key` VARCHAR(255),
					`term_type` VARCHAR(12) NOT NULL,
					PRIMARY KEY (id)
				) $collate;
					CREATE TABLE {$wpdb->prefix}wc_pwf_filters_terms (
					id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					group_id BIGINT NOT NULL,
					term_id BIGINT NOT NULL,
					PRIMARY KEY (id)
				) $collate;
					CREATE TABLE {$wpdb->prefix}wc_pwf_range_sliders (
					id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					group_id BIGINT NOT NULL,
					term_id BIGINT NOT NULL,
					min decimal(19,4) NOT NULL,
					max decimal(19,4) NOT NULL,
					PRIMARY KEY (id)
				) $collate;
				CREATE TABLE {$wpdb->prefix}wc_pwf_searches (
					id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
					group_id BIGINT NOT NULL,
					search_for VARCHAR(255) Not NULL,
					PRIMARY KEY (id)
				) $collate;
			";

			return $tables;
		}

		public static function get_db_tables() {
			$tables = array(
				'wc_pwf_filters',
				'wc_pwf_terms',
				'wc_pwf_filters_terms',
				'wc_pwf_range_sliders',
				'wc_pwf_searches',
			);

			return $tables;
		}
	}
}
