<?php
/**
 * Exclusion products tab array
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup Premium
 * @version 1.0.0
 */

defined( 'YITH_WACP' ) || exit; // Exit if accessed directly.

return array(
	'exclusions-prod' => array(
		array(
			'type'   => 'custom_tab',
			'action' => 'yith_wacp_exclusions_prod_table',
		),
	),
);
