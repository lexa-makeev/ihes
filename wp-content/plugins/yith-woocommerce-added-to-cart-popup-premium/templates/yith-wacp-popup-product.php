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
<?php if ( $product_info ) : ?>
	<div class="container">
                <h1 class="tb">Товар добавлен в корзину</h1>
                <div class="rec"></div>
                <div class="tovar">
                    <div class="img2">
						<?php
							echo '<a href="'.esc_url( $product->get_permalink() ).'">'.wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ).'</a>';
						?>
					</div>
                    <div class="osnova">
                        <div class="up_blockbag">
                            <div class="shirt">
                                <p class="name"><?php echo $product->get_name(); ?></p>
                                <p class="art">Артикул: <?php echo $product->get_sku(); ?></p>
                            </div>
                            <p class="price"><?php echo $product->get_price_html(); ?></p>
                        </div>
							<?php
							if ( $product->is_type( 'variation' ) && get_option( 'yith-wacp-show-product-variation', 'yes' ) === 'yes' ) :
								$variation_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->variation_id;
								?>
									<?php
									if ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] === $variation_id ) {
										echo yith_wacp_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
							<?php endif; ?>
                    </div>
                </div>
                <div class="rec"></div>
						<?php 
							$cross_sell_ids = $product->get_cross_sell_ids(); 
							if ($cross_sell_ids) {
								$i = 0;
								echo '
								<h1 class="ib">Собери свой образ:</h1>
									<div class="image">
										<div class="image1">';
								foreach($cross_sell_ids as $id) {
									$i += 1;
									if ($i < 3) {
										$crosssellProduct = wc_get_product( $id );
										echo '  <div class="l">
													<a class="img2" href="'.$crosssellProduct->get_permalink().'">'.wp_get_attachment_image( $crosssellProduct->get_image_id(),'thumbnail').'</a>
													<div class="lt">
														<div class="text">
															<a href="'.$crosssellProduct->get_permalink().'">'.$crosssellProduct->get_title().'</a>
															<p>'.$crosssellProduct->get_price_html().'</p>
														</div>
														<a class="bbg" href="'.$crosssellProduct->get_permalink().'"><img src="'.get_stylesheet_directory_uri().'/assets/img/bsk_main.svg" alt="bag"></a>
													</div>
												</div>';
									}
									else {
										break;
									}
								}
								if ($i == 2) {
									echo '<div class="l"><div class="img2"></div></div>';
								}
								echo '
									</div>
								</div>';
							}
							else {
								$args = array(
									'post_type' => 'product',
									'posts_per_page' => 3,
									'tax_query' => array(
											array(
												'taxonomy' => 'product_visibility',
												'field'    => 'name',
												'terms'    => 'featured',
											),
										),
									);
								$loop = new WP_Query( $args );
								if ( $loop->have_posts() ) {
									$i = 0;
									echo '
									<h1 class="ib">Актуальная коллекция:</h1>
										<div class="image">
											<div class="image1">';
									while( $loop->have_posts() ) {
										$loop->the_post();
										$i += 1;
										$image = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->post->ID ), 'single-post-thumbnail' );
										echo '  <div class="l">
													<a class="img2" href="'.get_permalink().'"><img src="'.$image[0].'" alt="baba"></a>
													<div class="lt">
														<div class="text">
															<a href="'.get_permalink().'">'.get_the_title().'</a>
															<p>'.$product->get_price_html().'</p>
														</div>
														<a class="bbg" href="'.get_permalink().'"><img src="'.get_stylesheet_directory_uri().'/assets/img/bsk_main.svg" alt="bag"></a>
													</div>
												</div>';
									}
									if ($i == 2) {
										echo '<div class="l"><div class="img2"></div></div>';
									}
									echo '
										</div>
									</div>';
								}
								wp_reset_postdata();
							}
						?>
                <div class="but_block">
					
                    <a class="cshop yith-wacp-close" href="#">Продолжить покупки</a>
                    <a class="cshop" href="<?php echo esc_url( wc_get_cart_url() ); ?>">Перейти в корзину</a>
                </div>
            </div>
<?php endif; ?>
