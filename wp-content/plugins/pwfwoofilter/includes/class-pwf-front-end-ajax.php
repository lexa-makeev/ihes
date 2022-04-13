<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Front_End_Ajax' ) ) {

	class Pwf_Front_End_Ajax {

		public static function register() {
			$plugin = new self();
			add_action( 'init', array( $plugin, 'init' ) );
		}

		function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 50 );
			add_action( 'wp_ajax_get_filter_result', array( $this, 'get_filter_result' ), 10 );
			add_action( 'wp_ajax_nopriv_get_filter_result', array( $this, 'get_filter_result' ), 10 );
		}

		function wp_enqueue_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'select2', PWF_WOO_FILTER_URI . '/assets/select2/css/select2.min.css', '', '4.0.12' );
			wp_enqueue_style( 'jquery-ui', PWF_WOO_FILTER_URI . '/assets/css/frontend/jquery-ui/jquery-ui.min.css', '', '1.12.1' );
			wp_enqueue_style( 'pwf-woo-filter', PWF_WOO_FILTER_URI . '/assets/css/frontend/style' . $suffix . '.css', '', PWF_WOO_FILTER_VER );

			wp_register_script( 'select2', PWF_WOO_FILTER_URI . '/assets/select2/js/select2.full.min.js', '', '4.0.12', true );
			wp_register_script( 'nouislider', PWF_WOO_FILTER_URI . '/assets/js/frontend/nouislider.min.js', '', '14.2.0', true );
			wp_register_script( 'moment', PWF_WOO_FILTER_URI . '/assets/js/frontend/moment.min.js', '', '2.25.3', true );
			wp_register_script( 'offcanvas', PWF_WOO_FILTER_URI . '/assets/js/frontend/js-offcanvas.pkgd.min.js', '', '1.2.11', true );
		}

		// get filter results
		public function get_filter_result() {
			check_ajax_referer( 'pwf-woocommerce-filter-nonce', 'nonce' );

			if ( ! isset( $_POST['filter_id'] ) || ! is_int( absint( $_POST['filter_id'] ) ) ) {
				wp_send_json_success(
					array(
						'message' => esc_html__( 'Filer ID must be integer.', 'pwf-woo-filter' ),
					),
					200
				);
			}

			/**
			 * Not recomended to use apply_filters using pwf_filter_id
			 * When the filter id come form ajax
			 * because it is already change before created a page
			 */
			$filter_id = absint( $_POST['filter_id'] );

			if ( isset( $_POST['current_page_type'] ) && isset( $_POST['current_page_id'] ) ) {
				$GLOBALS['pwf_main_query']['current_page_type'] = sanitize_key( $_POST['current_page_type'] );
				$GLOBALS['pwf_main_query']['current_page_id']   = absint( $_POST['current_page_id'] );
				$GLOBALS['pwf_main_query']['shop_integrated']   = sanitize_key( $_POST['shop_integrated'] );
				$GLOBALS['pwf_main_query']['is_shop_archive']   = sanitize_key( $_POST['is_shop_archive'] );
			}

			if ( isset( $_POST['rule_hidden_items'] ) && is_array( $_POST['rule_hidden_items'] ) ) {
				$GLOBALS['rule_hidden_items'] = array_map( 'esc_attr', $_POST['rule_hidden_items'] );
			}

			$query_vars = array();
			if ( isset( $_POST['query_vars'] ) && is_array( $_POST['query_vars'] ) && ! empty( $_POST['query_vars'] ) ) {
				foreach ( $_POST['query_vars'] as $key => $values ) {
					if ( ! empty( $values ) ) {
						if ( ! is_array( $values ) ) {
							$values = array( $values );
						}
						$query_vars[ sanitize_key( $key ) ] = array_map( 'esc_attr', $values );
					}
				}
			}

			$attributes = array();
			if ( isset( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) && ! empty( $_POST['attributes'] ) ) {
				foreach ( $_POST['attributes'] as $key => $value ) {
					$attributes[ sanitize_key( $key ) ] = esc_attr( $value );
				}
			}

			do_action( 'pwf_before_doing_ajax', $filter_id );

			$query_vars = new Pwf_Parse_Query_Vars( $filter_id, $query_vars );
			$orderby    = $query_vars->get_products_orderby();
			$authors_id = $query_vars->get_authors_id();
			if ( ! empty( $orderby ) ) {
				$attributes['orderby'] = is_array( $orderby ) ? implode( ',', $orderby ) : $orderby;
			}
			if ( ! empty( $authors_id ) ) {
				$attributes['author__in'] = $authors_id;
			}

			$query      = new Pwf_Filter_Products( $query_vars, $attributes );
			$products   = $query->get_content();
			$ajax_attrs = $query->get_query_info();

			if ( isset( $_POST['get_products_only'] ) && 'true' === $_POST['get_products_only'] ) {
				$filter_items_html = '';
			} else {
				$render_filter     = new Pwf_Render_Filter( $filter_id, $query_vars );
				$filter_items_html = wp_kses_post( $render_filter->get_html() );
			}

			$results = array(
				'products'    => $products,
				'attributes'  => $ajax_attrs,
				'filter_html' => $filter_items_html,
			);

			// Doing analytic
			$anlaytic = get_option( 'pwf_shop_analytics', 'disable' );
			if ( 'enable' === $anlaytic && ! isset( $_POST['get_products_only'] ) ) {
				$selected_items = $query_vars->selected_items();

				// Add default Woocommerce order menu
				if ( empty( $orderby ) && isset( $attributes['orderby'] ) ) {
					$selected_items['orderby'] = array(
						'values' => array( $attributes['orderby'] ),
						'type'   => 'orderby',
					);
				}

				if ( ! empty( $selected_items ) ) {
					$filter_data = array(
						'filter_post_id' => $filter_id,
						'products_count' => $query->get_products_count(),
						'from'           => 1,
						'query_string'   => $query_vars->get_query_string(),
					);

					$analytic_data = array(
						'filter_data'     => $filter_data,
						'selected_values' => $selected_items,
					);

					$analytic = new Pwf_Analytic_Query( $analytic_data );
				}
			}

			wp_send_json_success( $results, 200 );
		}
	}

	Pwf_Front_End_Ajax::register();
}
