<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

    <section id="basket">
        <div class="cont shop_table shop_table_responsive cart woocommerce-cart-form__contents">
                <?php do_action( 'woocommerce_before_cart_contents' ); ?>
                <?php
                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                    $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                        $image_id  = $_product->get_image_id();
                        ?>
                        <div class="bask_prod">
                            <a class="thumb" href="<?php echo esc_url( $product_permalink );?>">
                                <img src="<?php echo wp_get_attachment_image_url( $image_id, 'full' ); ?>" alt="">
                            </a>
                            <div class="inf_prod">
                                <div class="up_prod">
                                    <div class="name_art">
                                        <a class="name_prod" href="<?php echo esc_url( $product_permalink );?>"><?php echo $_product->get_title(); ?></a>
                                        <p class="art_prod">Артикул: <?php echo $_product->get_sku(); ?></p>
                                    </div>
                                    <div class="cost_col">
                                        <p class="cost"><?php echo $_product->get_price_html();?></p>
                                        <div class="col woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                                            <label for="quantity_<?php echo $cart_item_key; ?>">Количество</label>
                                            <input type="number" id="quantity_<?php echo $cart_item_key; ?>" class="input-text qty text" step="1" min="1" 
                                            max="<?php $_product->get_max_purchase_quantity(); ?>" name="cart[<?php echo $cart_item_key; ?>][qty]" value="<?php echo $cart_item['quantity']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="down_prod">
                                    <p class="attr_prod">Размер: <span><?php echo $cart_item['variation']['attribute_pa_size'];?></span></p>
                                    <?php
                                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" aria-label="%s" data-product_id="%s" data-product_sku="%s">Удалить</a>',
                                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                esc_html__( 'Remove this item', 'woocommerce' ),
                                                esc_attr( $product_id ),
                                                esc_attr( $_product->get_sku() )
                                            ),
                                            $cart_item_key
                                        );
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <?php
                    global $woocommerce;
                ?>
                <div class="oform">
                    <p class="itog">Итоговая сумма: <span><?php echo $woocommerce->cart->get_total(); ?></span></p>
                    <a href="<?php echo wc_get_checkout_url(); ?>">ОФОРМИТЬ</a>
                </div>

                <?php do_action( 'woocommerce_cart_contents' ); ?>

                <tr>
                    <td colspan="6" class="actions">

                        <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
                        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                    </td>
                </tr>

                <?php do_action( 'woocommerce_after_cart_contents' ); ?>
        </div>
    </section>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
<?php do_action( 'woocommerce_after_cart' ); ?>