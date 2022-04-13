<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Pwf_Admin_Setting' ) ) {

	class Pwf_Admin_Setting {

		public static function register() {
			$plugin = new self();
			add_action( 'init', array( $plugin, 'init' ) );
		}

		public function init() {
			add_filter( 'woocommerce_get_sections_products', array( $this, 'add_section' ), 10, 1 );
			add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );
			add_action( 'woocommerce_update_options_settings_tab_pwfwoofilter', 'update_settings' );
			add_action( 'woocommerce_admin_field_purchasecode', array( $this, 'output_purchase_code_fields' ), 10, 1 );
			add_action( 'admin_notices', array( $this, 'admin_notice_license_activation' ), 10 );
			add_action( 'admin_init', array( $this, 'notice_dismissed' ), 10 );
			add_filter( 'woocommerce_admin_settings_sanitize_option_envato_purchase_code_28181010', array( $this, 'sanitize_purchase_code' ), 10, 3 );
		}

		private function get_filter_posts() {
			$results = array();
			$filters = Pwf_Filter_Widget::get_filters();
			if ( is_array( $filters ) && '' === $filters[0]['id'] ) {
				$results[ $filters[0]['id'] ] = $filters[0]['title'];
			} else {
				$results[] = esc_html__( 'None', 'pwf-woo-filter' );
				foreach ( $filters as $filter ) {
					$results[ absint( $filter['id'] ) ] = esc_html( $filter['title'] );
				}
			}

			return $results;
		}

		public static function verify_code( $code, $action = '' ) {
			$end_point  = 'https://verifysales.mostafaa.net/wp-json/evanto-verify-sale/v1/checkpurchasekey/';
			$end_point .= '?code=' . sanitize_key( $code );
			$end_point .= '&site_url=' . Pwf_Filter_Post_Type::get_site_url();
			$end_point .= '&item_slug=pwfwoo';

			if ( ! empty( $action ) ) {
				$end_point .= '&action=' . $action;
			}

			$args = array(
				'headers' => array(
					'Content-Type' => 'application/json',
				),
			);

			$response = wp_remote_get( $end_point, $args );

			$response_code = absint( wp_remote_retrieve_response_code( $response ) );

			if ( 500 === $response_code || 0 === $response_code ) {
				$body = array(
					'code'    => 500,
					'message' => 'Our server is busy, please try again later',
				);
			} else {
				$body = json_decode( wp_remote_retrieve_body( $response ), true );
			}

			return $body;
		}

		public function add_section( $sections ) {
			$sections['pwfwoofilter'] = esc_html__( 'Filter', 'pwf-woo-filter' );
			return $sections;
		}

		public function add_settings( $settings, $current_section ) {
			if ( 'pwfwoofilter' === $current_section ) {
				$settings_filter   = array();
				$settings_filter[] = array(
					'name' => esc_html__( 'Select a filter to integrate with shop archive page', 'pwf-woo-filter' ),
					'type' => 'title',
					'id'   => 'pwf_title',
				);
				$settings_filter[] = array(
					'name'    => esc_html__( 'Filter ID', 'pwf-woo-filter' ),
					'id'      => 'pwf_shop_filter_id',
					'type'    => 'select',
					'options' => $this->get_filter_posts(),
				);
				$settings_filter[] = array(
					'name'              => esc_html__( 'Transient time', 'pwf-woo-filter' ),
					'desc'              => esc_html__( 'Set transient time in seconds.', 'pwf-woo-filter' ),
					'id'                => 'pwf_transient_time',
					'autoload'          => false,
					'default'           => '86400',
					'type'              => 'number',
					'custom_attributes' => array(
						'min'  => 60,
						'step' => 60,
					),
				);
				$settings_filter[] = array(
					'name'    => esc_html__( 'Theme Comitable', 'pwf-woo-filter' ),
					'id'      => 'pwf_shop_theme_compitable',
					'type'    => 'select',
					'options' => array(
						'enable'  => esc_html__( 'Enable', 'pwf-woo-filter' ),
						'disable' => esc_html__( 'Disable', 'pwf-woo-filter' ),
					),
				);
				$settings_filter[] = array(
					'name'    => esc_html__( 'Analytics', 'pwf-woo-filter' ),
					'id'      => 'pwf_shop_analytics',
					'type'    => 'select',
					'options' => array(
						'enable'  => esc_html__( 'Enable', 'pwf-woo-filter' ),
						'disable' => esc_html__( 'Disable', 'pwf-woo-filter' ),
					),
				);
				$settings_filter[] = array(
					'name'    => esc_html__( 'User ID', 'pwf-woo-filter' ),
					'desc'    => esc_html__( 'Save The ID for a login user.', 'pwf-woo-filter' ),
					'id'      => 'pwf_shop_analytics_save_user_id',
					'type'    => 'select',
					'options' => array(
						'enable'  => esc_html__( 'Enable', 'pwf-woo-filter' ),
						'disable' => esc_html__( 'Disable', 'pwf-woo-filter' ),
					),
				);
				$settings_filter[] = array(
					'name'        => esc_html__( 'Default loader', 'pwf-woo-filter' ),
					'description' => esc_html__( 'Default HTML loader template uses by Ajax.', 'pwf-woo-filter' ),
					'id'          => 'pwf_woo_loader_default',
					'type'        => 'textarea',
				);
				$settings_filter[] = array(
					'name'        => esc_html__( 'Button loader', 'pwf-woo-filter' ),
					'description' => esc_html__( 'HTML loader template uses by load more button.', 'pwf-woo-filter' ),
					'id'          => 'pwf_woo_loader_load_more',
					'type'        => 'textarea',
				);
				$settings_filter[] = array(
					'name'        => esc_html__( 'Infinite Loader', 'pwf-woo-filter' ),
					'description' => esc_html__( 'HTML loader template uses by infinite scroll.', 'pwf-woo-filter' ),
					'id'          => 'pwf_woo_loader_infinite',
					'type'        => 'textarea',
				);

				if ( ! Pwf_Filter_Post_Type::is_development_site() ) {
					$settings_filter[] = array(
						'name'     => esc_html__( 'Envato Purchase Code', 'pwf-woo-filter' ),
						'desc'     => esc_html__( 'Please insert your Envato Purchase Code.', 'pwf-woo-filter' ),
						'id'       => 'envato_purchase_code_28181010',
						'type'     => 'purchasecode',
						'desc_tip' => esc_html__( 'Confirm that, according to the Envato License Terms, each license entitles one person for a single project. Creating multiple unregistered installations is a copyright violation.', 'woocommerce' ),
					);
				}

				$settings_filter[] = array(
					'type' => 'sectionend',
					'id'   => 'pwfwoofilter',
				);

				return $settings_filter;
			} else {
				return $settings;
			}
		}

		public function output_purchase_code_fields( $value ) {

			if ( ! isset( $value['class'] ) ) {
				$value['class'] = '';
			}
			if ( ! isset( $value['css'] ) ) {
				$value['css'] = '';
			}
			if ( ! isset( $value['default'] ) ) {
				$value['default'] = '';
			}
			if ( ! isset( $value['desc'] ) ) {
				$value['desc'] = '';
			}
			if ( ! isset( $value['desc_tip'] ) ) {
				$value['desc_tip'] = false;
			}
			if ( ! isset( $value['placeholder'] ) ) {
				$value['placeholder'] = '';
			}
			if ( ! isset( $value['suffix'] ) ) {
				$value['suffix'] = '';
			}

			$value['value']    = Pwf_Filter_Post_Type::get_purchase_code();
			$field_description = WC_Admin_Settings::get_field_description( $value );
			$description       = '<p class="description">' . wp_kses_post( $value['desc'] ) . '</p>';
			$tooltip_html      = $field_description['tooltip_html'];
			$option_value      = $value['value'];

			if ( ! empty( $value['value'] ) ) {
				$option_value = substr( $value['value'], 0, 5 ) . 'xxxxx';
				$description  = '<p class="description">You can delete the purchase code by empty this field and click save changes.</p>';
			}

			?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo $tooltip_html; // WPCS: XSS ok. ?></label>
				</th>
				<td class="forminp forminp-text forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
					<input
						name="<?php echo esc_attr( $value['id'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						type="text"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						value="<?php echo esc_attr( $option_value ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
						/><?php echo esc_html( $value['suffix'] ); ?> <?php echo $description; // WPCS: XSS ok. ?>
				</td>
			</tr>
			<?php
		}

		/**
		 * @since 1.4.4
		 */
		public function sanitize_purchase_code( $value, $option, $raw_value ) {
			$saved_purchase_code = Pwf_Filter_Post_Type::get_purchase_code();

			if ( ( empty( $value ) && empty( $saved_purchase_code ) ) || ( ! empty( $value ) && 'xxxxx' === substr( $value, 5 ) ) ) {
				return '';
			}

			$action = '';
			if ( empty( $value ) && ! empty( $saved_purchase_code ) ) {
				$action = 'delete';
				$value  = $saved_purchase_code;
			} elseif ( ! empty( $value ) && ! empty( $saved_purchase_code ) ) {
				$action = false; // Add another purchase code without delete old
			}

			if ( false === $action ) {
				WC_Admin_Settings::add_error( 'You are try to add another purchase code before deleting the old purchase code.' );
			} else {
				self::process_purchase_code( $value, $action );
			}

			return ''; // set this option to empty envato_purchase_code_28181010
		}

		public static function process_purchase_code( $value = '', $action = '' ) {
			$results = self::verify_code( $value, $action );
			if ( 200 === $results['code'] ) {
				if ( 'delete' === $action ) {
					$value = '';
				}
				Pwf_Filter_Post_Type::set_purchase_code( $value );
			}

			WC_Admin_Settings::add_error( $results['message'] );

			return $results;
		}

		public function admin_notice_license_activation() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( Pwf_Filter_Post_Type::is_plugin_activated() || Pwf_Filter_Post_Type::is_development_site() ) {
				return;
			}

			if ( get_transient( 'dismissed_plugin_pwf_woo_filter' ) ) {
				return;
			}

			$option_page = admin_url( 'admin.php?page=wc-settings&tab=products&section=pwfwoofilter' );

			echo '<div class="notice notice-warning is-dismissible">
			<p><strong>Hi, Would you like to unlock premium features, Please activate your copy of <a href="' . esc_url( $option_page ) . '">PWF WooCommerce Product Filters</strong></a>.
			<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;"><strong><a href="?pwf-woocommerce-plugin-dismissed">Dismiss this notice</a></strong></span></p></div>';
		}

		public function notice_dismissed() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( isset( $_GET['pwf-woocommerce-plugin-dismissed'] ) ) {
				set_transient( 'dismissed_plugin_pwf_woo_filter', 1, 864000 );
			}
		}
	}

	Pwf_Admin_Setting::register();
}
