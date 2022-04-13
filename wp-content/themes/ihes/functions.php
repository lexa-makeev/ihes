<?php
/**
 * ihes functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ihes
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function ihes_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on ihes, use a find and replace
		* to change 'ihes' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'ihes', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'ihes' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'ihes_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'ihes_setup' );



/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function ihes_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'ihes_content_width', 640 );
}
add_action( 'after_setup_theme', 'ihes_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function ihes_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'ihes' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'ihes' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'ihes_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function ihes_scripts() {
	if (is_page(70)) {
		wp_enqueue_style( 'ihes-style-b', get_template_directory_uri() . '/assets/css/basket.css?'.time(), array(), _S_VERSION );
	}
	
	wp_enqueue_style( 'ihes-style-glider', get_template_directory_uri() . '/assets/css/glider.css?'.time(), array(), _S_VERSION );
	wp_enqueue_style( 'ihes-style', get_template_directory_uri() . '/assets/css/index.css?'.time(), array(), _S_VERSION );
	wp_enqueue_style( 'ihes-style-cart', get_template_directory_uri() . '/assets/css/basket.css?'.time(), array(), _S_VERSION );
	wp_enqueue_style( 'ihes-style-checkout', get_template_directory_uri() . '/assets/css/checkout.css?'.time(), array(), _S_VERSION );
	wp_enqueue_style( 'ihes-style-card', get_template_directory_uri() . '/assets/css/cardprod.css?'.time(), array(), _S_VERSION );
	wp_style_add_data( 'ihes-style', 'rtl', 'replace' );

	wp_enqueue_script( 'ihes-gam', get_template_directory_uri() . '/assets/js/gam.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'ihes-glider', get_template_directory_uri() . '/assets/js/glider.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'ihes-slider', get_template_directory_uri() . '/assets/js/slider.js?'.time(), array(), _S_VERSION, true );
	wp_enqueue_script( 'ihes-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ihes_scripts' );
add_filter ("woocommerce_cart_needs_payment", "__return_false");
function mytheme_add_woocommerce_support() {
	add_theme_support('woocommerce');
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

add_theme_support( 'wc-product-gallery-lightbox' ); //для включения лайтбокса
add_theme_support( 'wc-product-gallery-slider' ); //для включения слайдера

add_action('just_bread','woocommerce_breadcrumb', 1);
add_action('woocommerce_before_single_product','upper_woocommerce_breadcrumb', 11);
function upper_woocommerce_breadcrumb()
{
	echo '<div class="upper_bread">';
	do_action('just_bread');
	echo '</div>';
}

remove_action('woocommerce_before_main_content','woocommerce_output_content_wrapper', 10);
add_action( 'woocommerce_single_product_summary', 'add_head_product', 4);
function add_head_product()
{
	echo '<div class="bread_art">';
	global $product;
	do_action( 'woocommerce_before_main_content' );
	if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
	<?php endif;
	echo '</div>';
}
remove_action('woocommerce_single_product_summary','woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt', 20);
add_action('woocommerce_single_product_summary','woocommerce_template_single_excerpt', 9);
remove_action('woocommerce_single_product_summary','woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary','woocommerce_template_single_sharing', 50);
add_action('woocommerce_single_product_summary','add_attr_close_product', 61);
function add_attr_close_product()
{
	global $product;
	$attributes = $product->get_attributes();
	if (!empty($attributes)) {
		echo '<div class="details">
				<div class="row_detail head_detail">
					<p class="det">Детали</p>
					<p class="har">Характеристики</p>
				</div>';
		foreach ( $attributes as $attribute ) :
			if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
			  continue;
			}
			?>
			<div class="row_detail">
				<p><?php echo wc_attribute_label( $attribute['name'] ); ?></p><?php
					if ( $attribute['is_taxonomy'] ) {
			
					$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
			
					} else {
			
					// Convert pipes to commas and display values
					$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );
			
					}?>
			</div>
			<?php endforeach;
		echo '</div>';
	}
	
	echo '</div>';
}
remove_action('woocommerce_after_single_product_summary','woocommerce_output_product_data_tabs', 10);
remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products', 20);
add_action('woocommerce_after_single_product_summary','output_related_products', 20);
remove_filter( 'the_content', 'wpautop' );// для контента
remove_filter( 'the_excerpt', 'wpautop' );// для анонсов
function output_related_products()
{
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	  }
	  
	  global $product, $woocommerce_loop;
	  
	  if ( empty( $product ) || ! $product->exists() ) {
		return;
	  }
	  
	  if ( ! $related = $product->get_related( $posts_per_page ) ) {
		return;
	  }
	  
	  // Get ID of current product, to exclude it from the related products query
	  $current_product_id = $product->get_id();
	  
	  $cats_array = array(0);
	  
	  // get categories
	  $terms = wp_get_post_terms( $product->id, 'product_cat' );
	  
	  // select only the category which doesn't have any children
	  foreach ( $terms as $term ) {
		$children = get_term_children( $term->term_id, 'product_cat' );
		if ( !sizeof( $children ) )
		$cats_array[] = $term->term_id;
	  }
	  
	  $args = apply_filters( 'woocommerce_related_products_args', array(
		'post_type' => 'product',
		'post__not_in' => array( $current_product_id ),   // exclude current product
		'ignore_sticky_posts' => 1,
		'no_found_rows' => 1,
		'posts_per_page' => $posts_per_page,
		'orderby' => $orderby,
		'tax_query' => array(
		  array(
			  'taxonomy' => 'product_cat',
			  'field' => 'id',
			  'terms' => $cats_array
		  ),
		)
	  ));
	  
	  $products                    = new WP_Query( $args );
	  $woocommerce_loop['name']    = 'related';
	  $woocommerce_loop['columns'] = apply_filters( 'woocommerce_related_products_columns', $columns );
	  
	  if ( $products->have_posts() ) : ?>
	  
		<section class="related products">
			<h2>Собери свой образ</h2>
		  	<?php woocommerce_product_loop_start(); ?>
		  				<?php 
							$cross_sell_ids = $product->get_cross_sell_ids(); 
							if ($cross_sell_ids) {
								$i = 0;
								foreach($cross_sell_ids as $id) {
										$crosssellProduct = wc_get_product( $id );
										echo '
										<div class="undercard">
											<div class="card_main"><a href="'.$crosssellProduct->get_permalink().'">
												'.wp_get_attachment_image( $crosssellProduct->get_image_id() ,'thumbnail').'
												</a>
												<div class="info_prod">
													<div class="left_info_prod">
														<a href="'.$crosssellProduct->get_permalink().'" class="name">'.$crosssellProduct->get_title().'</a>
														<p class="price">'.$crosssellProduct->get_price_html().'</p>
													</div>
													<a href="'.$crosssellProduct->get_permalink().'">
														<img src="'.get_stylesheet_directory_uri().'/assets/img/bsk_main.svg" alt="">
													</a>
												</div>
											</div>
										</div>';
								}
							}
						?>
		  <?php woocommerce_product_loop_end(); ?>
		</section>
	  
	  <?php endif;
	  
	  wp_reset_postdata();
}

add_action(
    'wp_footer',
    function() {
        ?>
        <script>
        jQuery( function( $ ) {
            $( document.body ).on( '.single_add_to_cart_button', function( a, b ) {
                   var tpl = '';
                    tpl += '<h1>Товар добавлен в корзину</h1>';
                    tpl += '<p>' + product_title + '</p>';
                    tpl += '<div>';
                    tpl += '<a class="btn btn-default" onclick="jQuery.unblockUI();">Продолжить покупки</a>';
                    tpl += '<a href="/shop/cart/" class="btn btn-primary">Оформить заказ</a>';
                    tpl += '</div>';
                    tpl += '<span class="close" onclick="jQuery.unblockUI();">&times;</span>';
                    alert(tpl);    
            });
        } );
        </script>
        <?php
    }
);

add_filter('woocommerce_return_to_shop_redirect', 'shopping_redirect_url');
function shopping_redirect_url($url) {
	return get_home_url();
}

add_filter( 'woocommerce_checkout_fields', 'new_fields', 25 );
 
function new_fields( $fields ) {
	
  $fields[ 'billing' ][ 'billing_first_name' ][ 'label' ] = 'Имя*';
  $fields[ 'billing' ][ 'billing_last_name' ][ 'label' ] = 'Фамилия*';
  $fields[ 'billing' ][ 'billing_phone' ][ 'label' ] = 'Телефон*';
  $fields[ 'billing' ][ 'billing_email' ][ 'label' ] = 'Email*';
  $fields['order']['order_comments'][ 'label' ] = "Комментарий";
  return $fields;
}

add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
	return array(
		'width' => 137,
		'height' => 169,
		'crop' => 0,
	);
} );

add_filter( 'woocommerce_get_image_size_single', function( $size ) {
	return array(
		'width' => 471,
		'height' => 581,
		'crop' => 1,
	);
} );
add_filter( 'woocommerce_get_image_size_shop_thumbnail',    'force_crop_woocommerce' );
add_filter( 'woocommerce_get_image_size_shop_catalog',      'force_crop_woocommerce' );
add_filter( 'woocommerce_get_image_size_shop_single',       'force_crop_woocommerce' );


function force_crop_woocommerce( $size ){
    $size['crop'] = 1;
    return $size;
}
/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

add_filter( 'woocommerce_endpoint_order-received_title', 'true_custom_order_received_h1', 25 );
 
function true_custom_order_received_h1( $title ) {
 
	return " ";
 
}


	
add_image_size( 'main-size', 1000, 1234, true);


function tb_change_text( $translated_text ) {
	if ( $translated_text == 'Главная' ) {
		$translated_text = 'Main';
	}
	return $translated_text;
}
add_filter( 'gettext', 'tb_change_text', 20 );