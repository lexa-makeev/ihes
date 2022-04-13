<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ihes
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Tenor+Sans&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	<div class="gam">
		<div class="gam_up">
			<?php echo do_shortcode('[fibosearch]'); ?>
			<img class="krest" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/krest.svg" alt="krest">
		</div>
		<?php
			$prod_cat_args = array(
				'taxonomy'    => 'product_cat',
				'orderby'     => 'id', // здесь по какому полю сортировать
				'hide_empty'  => false, // скрывать категории без товаров или нет
				'parent'      => 0 // id родительской категории
			);
			$woo_categories = get_categories( $prod_cat_args );
			foreach ( $woo_categories as $woo_cat ) {
				$woo_cat_id = $woo_cat->term_id; //category ID
				$woo_cat_name = $woo_cat->name; //category name
				if ($woo_cat_name != "Misc") {
                    echo '<a class="cat_gam" href="'.get_term_link( $woo_cat_id, 'product_cat' ).'">'.$woo_cat_name.'</a>';
				}
			}
		?>
		<a class="cart_gam" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/sbag.svg"></a>
	</div>
	<header>
        <div class="cont">
            <a class="logo" href="<?php echo get_home_url(); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo.svg" alt="logo"></a>
            <?php echo do_shortcode('[fibosearch]'); ?>
            <a class="cart_icon" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/sbag.svg"></a>
			<img class="gam_icon" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/gam.svg" alt="gam">
        </div>
    </header>
