<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://nabillemsieh.com
 * @since      1.0.0
 *
 * @package    WP_Smart_Image_Resize
 * @subpackage WP_Smart_Image_Resize/templates
 */

$current_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

?>
<div class="wrap">
    
    <h1>Smart Image Resize PRO</h1>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=wp-smart-image-resize&tab=general"
           class="nav-tab <?php echo $current_tab === 'general' ? 'nav-tab-active' : '' ?>">Настройки</a>
        <a href="?page=wp-smart-image-resize&tab=regenerate_thumbnails"
           class="nav-tab <?php echo $current_tab === 'regenerate_thumbnails' ? 'nav-tab-active' : '' ?>">Регенерировать
            миниатюры</a>
        
        <a href="?page=wp-smart-image-resize&tab=manage_license"
           class="nav-tab <?php echo $current_tab === 'manage_license' ? 'nav-tab-active' : '' ?>">Управление лицензией</a>
        
    </h2>

    <?php if ( $current_tab === 'general' ): ?>

        <div class="wpsirSettingsContainer">
            <div>
                <form method="post" action="options.php">
                    <?php
                    settings_fields( WP_SIR_NAME );
                    do_settings_sections( WP_SIR_NAME );
                    submit_button();
                    ?>
                </form>
            </div>
            <div>
                <div class="wpsirInfoBox">
                    <h3>Ресурсы</h3>
                    <ul>
                        <li><a target="_blank" href="https://sirplugin.com"><i aria-hidden="true"
                                                                               class="dashicons dashicons-external"></i>
                                Сайт</a></li>
                        <li><a target="_blank" href="https://sirplugin.com/guide.html"><i aria-hidden="true"
                                                                                          class="dashicons dashicons-external"></i>
                                Документация</a></li>
                        <li><a target="_blank" href="https://sirplugin.com/contact.html"><i aria-hidden="true"
                                                                                            class="dashicons dashicons-external"></i>
                                Поддержка</a></li>

                        
                    </ul>
                </div>
            </div>
        </div>

    <?php endif;
    if ( $current_tab === 'regenerate_thumbnails' ):
        ?>
        <div class="wp-sir-regenerate-thumbnails" style="padding:10px">
            <p style="margin-bottom:5px">Выполните следующие действия, чтобы изменить размер уже загруженных изображений в соответствии с вашими настройками.</p>
            <ol>
                <?php if ( !wp_sir_regen_thumb_active() ): ?>
                    <li>Установите <a
                                href="<?php echo admin_url( 'plugin-install.php?s=Regenerate+Thumbnails&tab=search&type=term' ) ?>">Плагин Regenerate Thumbnails</a>.
                    </li>
                <?php endif; ?>
                <li>Перейдите в раздел
                    <?php if ( wp_sir_regen_thumb_active() ): ?>
                        <a href="<?php echo admin_url() ?>tools.php?page=regenerate-thumbnails">Инструменты → Регенерация
                            Миниатюр</a>
                    <?php else: ?>
                        Инструменты > Регенерировать миниатюры.
                    <?php endif; ?>
                </li>
                <li>Нажмите кнопку <b>Регенерировать миниатюры для всех вложений</b> кнопка для начала изменения размера</li>
            </ol>
            <p>
                <b>NOTE:</b> Убедитесь, что вы очистили кэш, если старые изображения все еще появляются, включая браузер, кэширование
                плагин, и Cloudflare.
            </p>
          
        </div>
    <?php endif; ?>
    <?php
    
    if ( $current_tab === 'manage_license' ):
        ?>
        <div>
            <?php do_action( 'wp_sir_manage_license' ); ?>
        </div>
    <?php
    endif;
    
    ?>

</div>


