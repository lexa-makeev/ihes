<?php 

$afpv_video_icon_val = get_option('pv_play_icon');

if ( 'afpv_play_icon' == $afpv_video_icon_val ) { ?>

	<img src="<?php echo esc_url( WP_PLUGIN_URL . '/product-video-for-woocommerce/images/play-icon.png' ); ?>">

<?php } elseif ( 'afpv_play_icon_1' == $afpv_video_icon_val ) { ?>

	<img src="<?php echo esc_url( WP_PLUGIN_URL . '/product-video-for-woocommerce/images/play-icon-1.png' ); ?>">

<?php } elseif ( 'afpv_play_icon_3' == $afpv_video_icon_val ) { ?>

	<img src="<?php echo esc_url( WP_PLUGIN_URL . '/product-video-for-woocommerce/images/play-icon-3.png' ); ?>">

<?php } elseif ( 'afpv_play_icon_2' == $afpv_video_icon_val ) { ?>

	<img src="<?php echo esc_url( WP_PLUGIN_URL . '/product-video-for-woocommerce/images/play-icon-2.png' ); ?>">

<?php } elseif ( 'afpv_play_icon_7' == $afpv_video_icon_val ) { ?>

	<img src="<?php echo esc_url( WP_PLUGIN_URL . '/product-video-for-woocommerce/images/play-icon-7.png' ); ?>">

<?php } elseif ( 'afpv_play_icon_8' == $afpv_video_icon_val ) { ?>

	<img src="<?php echo esc_url( WP_PLUGIN_URL . '/product-video-for-woocommerce/images/play-icon-8.png' ); ?>">

<?php } elseif ( 'afpv_play_icon_9' == $afpv_video_icon_val ) { ?>

	<img src="<?php echo esc_url( WP_PLUGIN_URL . '/product-video-for-woocommerce/images/play-icon-9.png' ); ?>">

<?php } else { ?>

	<img src="<?php echo esc_url( WP_PLUGIN_URL . '/product-video-for-woocommerce/images/play-icon.png' ); ?>">
		
<?php } ?>
