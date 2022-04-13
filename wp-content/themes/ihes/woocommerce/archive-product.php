<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );?>
<div class="cont categories_sec">
<?php
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
?>
        <?php
            foreach (get_queried_object() as $key => $value) {
                if ($key == "term_id") {
                    $term_cat_id = $value;
                    break;
                }
            }
			$prod_cat_args = array(
				'taxonomy'    => 'product_cat',
				'orderby'     => 'id', // здесь по какому полю сортировать
				'hide_empty'  => false, // скрывать категории без товаров или нет
				'parent'      => $term_cat_id // id родительской категории
			);
			$cat_ids_sec = array();
			$woo_categories = get_categories( $prod_cat_args );
            if ($woo_categories) {
                echo '<div class="slider">
                        <img class="arrow_left" src="'.get_stylesheet_directory_uri().'/assets/img/arrow_left.svg" alt="arrow_left"/>
                        <img class="arrow_right" src="'.get_stylesheet_directory_uri().'/assets/img/arrow_right.svg" alt="arrow_right"/>
                        <nav class="subcategories">';
                foreach ( $woo_categories as $woo_cat ) {
                    $woo_cat_id = $woo_cat->term_id; //category ID
                    $woo_cat_name = $woo_cat->name; //category name
                    if ($woo_cat_name != "Misc") {
                        array_push($cat_ids_sec, $woo_cat_id);
                        echo '<a href="'.get_term_link( $woo_cat_id, 'product_cat' ).'">'.$woo_cat_name.'</a>';
                    }
                }
                echo '  </nav>
                    </div>';
            }
		?>
    <?php
    if ( woocommerce_product_loop() ) {

        woocommerce_product_loop_start();
        echo '<div class="products_main">';
        if ( wc_get_loop_prop( 'total' ) ) {
            while ( have_posts() ) {
                the_post();
                global $product;
                ?>
                <div class="undercard">
                    <div class="card_main card_category"><?php woocommerce_show_product_sale_flash( $post, $product ); ?>
                        <a href='<?php echo get_permalink( $post->ID ) ?>'>
                            <?php
                                if (has_post_thumbnail( $post->ID )) echo '<img src="'.get_the_post_thumbnail_url('','medium').'" alt="" />';
                                else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" />';
                            ?>
                        </a>
                        <div class="info_prod">
                            <div class="left_info_prod">
                                <a href='<?php echo get_permalink( $post->ID ) ?>' class='name'><?php the_title(); ?></a>
                                <p class="price"><?php echo $product->get_price_html(); ?></p>
                            </div>
                            <a href='<?php echo get_permalink( $post->ID ) ?>'>
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/bsk_main.svg" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        echo '</div>';

        woocommerce_product_loop_end();

        /**
         * Hook: woocommerce_after_shop_loop.
         *
         * @hooked woocommerce_pagination - 10
         */
        do_action( 'woocommerce_after_shop_loop' );
    } else {
        /**
         * Hook: woocommerce_no_products_found.
         *
         * @hooked wc_no_products_found - 10
         */
        do_action( 'woocommerce_no_products_found' );
    }

    /**
     * Hook: woocommerce_after_main_content.
     *
     * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
     */
    do_action( 'woocommerce_after_main_content' );

    /**
     * Hook: woocommerce_sidebar.
     *
     * @hooked woocommerce_get_sidebar - 10
     */
    do_action( 'woocommerce_sidebar' );?>
</div>
<?php
    get_footer( 'shop' );