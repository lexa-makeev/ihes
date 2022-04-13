<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package ihes
 */

?>

	<footer>
        <div class="chel_block">
            <img class="chel" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/chel.png" alt="chel">
        </div>
        <div class="cont">
            <div class="up_block">
                <a class="logo_f" href="#"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/zmey.png" alt="logo"></a>
                <div class="r_block_info">
                    <div class="r_block">
                        <h1>Social</h1>
                        <a class="instagram" href="https://www.instagram.com/ihesihes/" target="_blank">Instagram</a>
                        <a class="telegram" href="#">Telegram</a>
                    </div>
                </div>
            </div>
            <div class="down_block">
                <p class="copy">Copyright © 2022 Ihes</p>
                <a class="bobr" href="https://freelance.ru/makeevip" target="_blank">Development: Bobr Team</a>
            </div>
        </div>
    </footer>
<?php wp_footer(); ?>

</body>
</html>
