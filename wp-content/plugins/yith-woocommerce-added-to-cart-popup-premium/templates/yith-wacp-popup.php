<?php
/**
 * Popup bone template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

defined( 'YITH_WACP' ) || exit; // Exit if accessed directly.
?>
<?php
/**
 * Popup product template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

defined( 'YITH_WACP' ) || exit; // Exit if accessed directly.

// Get cart.
$cart = WC()->cart->get_cart();
// Get current cart item.
$cart_item = WC()->cart->get_cart_item( $last_cart_item_key );
if ( ! $cart_item ) {
	foreach ( WC()->cart->get_cart_contents() as $key => $item ) {
		$p_id = $product instanceof WC_Product ? $product->get_id() : false;
		if ( $item['product_id'] === $p_id || $item['variation_id'] === $p_id ) {
			$cart_item = $item;
			break;
		}
	}
}

?>

<div id="yith-wacp-popup" class="<?php echo esc_attr( $animation ); ?>">

	<div class="yith-wacp-overlay">
		<div class="yith-wacp-wrapper woocommerce">

			<div class="yith-wacp-main">

				<div class="yith-wacp-head">
				</div>

				<div class="yith-wacp-content"></div>

			</div>

		</div>
	</div>


</div>
