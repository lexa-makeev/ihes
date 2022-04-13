<?php

namespace WP_Smart_Image_Resize;

use WP_Smart_Image_Resize\Quota;
use WP_Smart_Image_Resize\Utilities\Env;

/**
 * Class WP_Smart_Image_Resize\Settings
 *
 * @package WP_Smart_Image_Resize\Inc
 */

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('\WP_Smart_Image_Resize\Settings')) :
    class Admin
    {

        protected static $instance = null;

        /**
         * @return Admin
         */
        public static function get_instance()
        {
            if (is_null(static::$instance)) {
                static::$instance = new Admin;
            }

            return static::$instance;
        }

        public function init()
        {
            // Add plugin to WooCommerce menu.
            add_action('admin_menu', [$this, 'add_admin_menu']);
            add_filter('pre_update_option_wp_sir_settings', [$this, 'pre_update_settings']);
            // Show Woocommerce not installed notice.
            add_action('admin_notices', [$this, 'fileinfo_not_enabled']);
            add_action('admin_notices', [$this, 'phpversion_not_supported']);

            // Initialise settings form.
            add_action('admin_init', [$this, 'init_settings']);

            // Add settings help tab.
            add_action('load-woocommerce_smart-image-resize', [$this, 'settings_help'], 5, 3);

            add_filter('plugin_action_links_' . WP_SIR_BASENAME, [$this, 'plugin_links']);

            add_filter('admin_footer_text', [$this, 'admin_footer_text']);
        }

        function quota_exceeding_soon()
        {
            if (Quota::is_exceeding_soon()) { ?>
                <div class="notice notice-warning is-dismissible">
                    <p><?php _e(
                            'Интеллектуальное изменение размера изображения: Вы достигли предела изменения размера изображений.',
                            WP_SIR_NAME
                        ); ?>
                        <a target="_blank" href="https:/sirplugin.com/#pro?utm_source=plugin&utm_campaign=notice_limit" class="button button-default"><?php _e(
                                                                                                                                                            'Upgrade to Pro'
                                                                                                                                                        ); ?></a> для
                        неограниченные изображения.
                    </p>
                </div>
            <?php }
        }

        function quota_exceeded_notice()
        {
            if (Quota::isExceeded()) { ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e(
                            'Интеллектуальное изменение размера изображения: Вы достигли предела изменения размера изображений.',
                            WP_SIR_NAME
                        ); ?>
                        <a target="_blank" href="https:/sirplugin.com/#pro?utm_source=plugin&utm_campaign=notice_limit" class="button button-default"><?php _e(
                                                                                                                                                            'Upgrade to Pro'
                                                                                                                                                        ); ?></a> для
                        неограниченное количество изображений.
                    </p>
                </div>
            <?php }
        }

        function admin_footer_text()
        {
            $screen = get_current_screen();

            if (!function_exists('get_current_screen')) {
                return;
            }
            if ($screen->id === 'woocommerce_page_wp-smart-image-resize') { ?>
                
                
                Нужна помощь? <a href="https://sirplugin.com" target="_blank">Контактная поддержка</a>.
                
            <?php }
        }

        function plugin_links($links)
        {

            $settings_url    = admin_url('admin.php?page=wp-smart-image-resize');
            $settings_anchor = '<a href="' . $settings_url . '">' . __('Настройки') . '</a>';
            array_unshift($links, $settings_anchor);

            
            $manage_license_link    = admin_url('admin.php?page=wp-smart-image-resize&tab=manage_license');
            $manage_license_anchor = '<a href="' . $manage_license_link . '">' . __('Лицензия') . '</a>';
            $links[] = $manage_license_anchor;
            


            return $links;
        }

        function pre_update_settings($newval)
        {

            $defaults = [
                'enable'      => 0,
                'jpg_convert' => 0,
                'enable_webp' => 0,
                'enable_trim' => 0,
            ];

            if (isset($newval['processable_images']['taxonomies'])) {
                $newval['processable_images']['taxonomies'] = (array)$newval['processable_images']['taxonomies'];
            } else {
                $newval['processable_images']['taxonomies'] = [];
            }
            if (isset($newval['processable_images']['post_types'])) {
                $newval['processable_images']['post_types'] = (array)$newval['processable_images']['post_types'];
            } else {
                $newval['processable_images']['post_types'] = [];
            }

            return wp_parse_args($newval, $defaults);
        }


        public function fileinfo_not_enabled()
        {
            if (!extension_loaded('fileinfo')) : ?>
                <div class="notice notice-error  is-dismissible">
                    <p><?php _e(
                            'Интеллектуальное изменение размера изображения: Расширение PHP Fileinfo не включено, обратитесь к своему хостинг-провайдеру для его включения.',
                            WP_SIR_NAME
                        ); ?></p>
                </div>
            <?php endif;
        }

        public function phpversion_not_supported()
        {
            if (!version_compare(PHP_VERSION, '5.6.0', '>=')) : ?>
                <div class="notice notice-error  is-dismissible">
                    <p><?php _e(
                            'Для корректной работы Smart Image Resize требуется PHP 5.6.0 или выше.',
                            WP_SIR_NAME
                        ); ?></p>
                </div>
            <?php endif;
        }

        /**
         * Add plugin submenu to WooCommerce menu.
         *
         * @return void
         */
        public function add_admin_menu()
        {

            $parent_slug = 'woocommerce';
            $cap         = 'manage_woocommerce';
            if (!is_plugin_active('woocommerce/woocommerce.php')) {
                $parent_slug = 'options-general.php';
                $cap         = 'manage_options';
            }

            $page_slug = add_submenu_page(
                $parent_slug,
                'Smart Image Resize',
                'Smart Image Resize',
                $cap,
                WP_SIR_NAME,
                [$this, 'settings_page']
            );

            add_action('load-' . $page_slug, [$this, 'add_settings_help']);
        }

        /**
         * Initialize settings form.
         *
         * @return void
         */
        public function init_settings()
        {

            register_setting(WP_SIR_NAME, 'wp_sir_settings');

            // General section.
            add_settings_section('wp_sir_settings_general', 'Общий', null, WP_SIR_NAME);
            add_settings_section('wp_sir_settings_optimization', 'Оптимизация', null, WP_SIR_NAME);
            add_settings_section('wp_sir_settings_advanced', 'Расширенный', null, WP_SIR_NAME);

            // Register `Enable/Disable` plugin resize field.
            add_settings_field(
                'wp_sir_settings_enable',
                'Включить изменение размера',
                [$this, 'settings_field_enable'],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );
            add_settings_field(
                'wp_sir_settings_processable_images',
                'Изображения',
                [$this, 'settings_field_processable_images'],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Sizes` field.
            add_settings_field(
                'wp_sir_settings_sizes',
                'Размеры',
                [$this, 'settings_field_sizes'],
                WP_SIR_NAME,
                'wp_sir_settings_advanced'
            );

            // Register `Enable WebP format` field.
            add_settings_field(
                'wp_sir_settings_enable_trim',
                'Обрезать белое пространство',
                [$this, 'settings_field_enable_trim'],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Background Color` field.
            add_settings_field(
                'wp_sir_settings_bg_color',
                'Цвет фона',
                [$this, 'settings_field_bg_color'],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Image Compression` field.
            add_settings_field(
                'wp_sir_settings_image_quality',
                'Сжатие изображений',
                [$this, 'settings_field_image_quality'],
                WP_SIR_NAME,
                'wp_sir_settings_optimization'
            );

            // Register `Convert to JPG format` field.
            add_settings_field(
                'wp_sir_settings_jpg_convert',
                'Преобразование в JPEG',
                [$this, 'settings_field_jpg_convert'],
                WP_SIR_NAME,
                'wp_sir_settings_optimization'
            );

            // Register `Enable WebP format` field.
            add_settings_field(
                'wp_sir_settings_enable_webp',
                'Включить WebP',
                [$this, 'settings_field_enable_webp'],
                WP_SIR_NAME,
                'wp_sir_settings_optimization'
            );
        }

        function settings_field_enable_trim()
        {
            $settings = \wp_sir_get_settings(); ?>
            <label for="wp-sir-enable-trim">
                <input type="checkbox" name="wp_sir_settings[enable_trim]" <?php checked($settings['enable_trim'], 1); ?> id="wp-sir-enable-trim" class="wp-sir-as-toggle" value="1" />
            </label>
            <p class="description">
                <?php _e('Удалите ненужные пробелы вокруг изображения.', 'wp-smart-image-resize'); ?>
            </p>
            <div class="hidden" id="wp-sir-trim-feather-wrap" style="margin-top:10px">
                Размер границы (px) <input type="number" min="0" name="wp_sir_settings[trim_feather]" style="width:70px" value="<?php echo $settings['trim_feather'] ?>">
                <p class="description">Это оставит нетронутой "границу". вокруг изображения во время обрезки.</p>
            </div>
            <div class="hidden" id="wp-sir-trim-tolerance-wrap" style="margin-top:10px">
                Уровень толерантности (%) <input type="number" min="0" max="100" name="wp_sir_settings[trim_tolerance]" style="width:70px" value="<?php echo $settings['trim_tolerance'] ?>">
                <p class="description">Увеличьте уровень допуска, чтобы отсечь цвета, которые незначительно отличаются от чистого белого..
                    <br>
                    По умолчанию: 3 (Max: 100)
                </p>
            </div>


        <?php
        }

        function settings_field_processable_images()
        {
            $settings = \wp_sir_get_settings();

        ?>
            <div>
                <label for="wp-sir-processable-images-product" style="display: flex; align-items: center; margin-bottom: 10px">
                    <input type="checkbox" name="wp_sir_settings[processable_images][post_types][]" <?php
                                                                                                    echo in_array(
                                                                                                        'product',
                                                                                                        $settings['processable_images']['post_types'],
                                                                                                        true
                                                                                                    ) ? 'checked' : '';
                                                                                                    ?> id="wp-sir-processable-images-product" class="wp-sir-as-toggle" value="product" /> <span style="display:inline-block">Изображения продуктов</span>
                </label>
                <label for="wp-sir-processable-images-product-cat" style="display: flex; align-items: center">
                    <input type="checkbox" name="wp_sir_settings[processable_images][taxonomies][]" <?php echo in_array(
                                                                                                        'product_cat',
                                                                                                        $settings['processable_images']['taxonomies'],
                                                                                                        true
                                                                                                    ) ? 'checked' : ''; ?> id="wp-sir-processable-images-product-cat" class="wp-sir-as-toggle" value="product_cat" /> <span style="display:inline-block">Изображения категорий</span>
                </label>
            </div>
            <p class="description">
                <?php _e('Выберите изображения для изменения размера.', 'wp-smart-image-resize'); ?>
            </p>
        <?php
        }

        function settings_field_jpg_convert()
        {
            $settings = \wp_sir_get_settings(); ?>
            <label for="wp-sir-jpg-convert">
                <input type="checkbox" name="wp_sir_settings[jpg_convert]" <?php checked($settings['jpg_convert'], 1); ?> id="wp-sir-jpg-convert" class="wp-sir-as-toggle"  value="1" />
                
            </label>
            <p class="description">
                <?php _e(
                    "Преобразование изображений PNG в JPG настоятельно рекомендуется для увеличения времени загрузки страницы.",
                    WP_SIR_NAME
                ); ?>
            </p>
        <?php
        }

        function settings_field_enable_webp()
        {
            $settings = \wp_sir_get_settings(); ?>
            <label for="wp-sir-enable-webp">
                <input type="checkbox" name="wp_sir_settings[enable_webp]" <?php checked($settings['enable_webp'], 1); ?> id="wp-sir-enable-webp" class="wp-sir-as-toggle"  value="1" />
                

            </label>
            <p class="description">
                <?php _e(
                    "WebP уменьшает размер файла изображения до 90% по сравнению с PNG без потери качества.<br>ПРИМЕЧАНИЕ: Плагин будет плавно переходить на JPEG и PNG для браузеров, которые не могут отображать изображения WebP.",
                    WP_SIR_NAME
                ); ?>
            </p>
            <?php

            

            if (!Env::supports_webp()) {
                echo '<p style="wp-sir-notice-danger"><span class="dashicons dashicons-info"></span> ';
                echo _e('Формат WebP недоступен на этом сервере.', 'wp-smart-image-resize');
                echo '</p>';
            }
            
            ?>
        <?php
        }

        public function settings_field_image_quality($args)
        {
            $settings = \wp_sir_get_settings(); ?>
            <input name="wp_sir_settings[jpg_quality]" type="hidden" class="wpSirImageQuality" value="<?php echo absint($settings['jpg_quality']); ?>" />
            <div class="wpSirSlider" style="width:300px" data-input="wpSirImageQuality">
                <div class="wpSirSliderHandler ui-slider-handle ppsir-slider-handle"></div>
            </div>
        <?php
        }

        function settings_field_sizes()
        {
            $settings = \wp_sir_get_settings('view'); ?>
            <select multiple="multiple" id="wpSirResizeSizes" name="wp_sir_settings[sizes][]" data-defaults="<?php echo implode(',', _wp_sir_get_default_sizes()) ?>">
                <?php foreach (wp_sir_get_additional_sizes('view') as $key => $size) :
                ?>
                    <option value="<?php echo $key; ?>" <?php echo in_array(
                                                            $key,
                                                            $settings['sizes']
                                                        )
                                                            ? 'selected'
                                                            : ''; ?>><?php echo "$key({$size['width']}x{$size['height']})"; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                Используйте выпадающий список выше, чтобы выбрать, какие размеры изображений необходимо генерировать.
                <br>
                ПРИМЕЧАНИЕ: Для экономии дискового пространства предварительно выбираются только необходимые размеры. <button id="wpsirResetDefaultSizes" type="button" class="button-link">Сброс до предварительно выбранных размеров</button>
            </p>
        <?php
        }



        public function settings_field_bg_color($args)
        {
            $settings = \wp_sir_get_settings(); ?>
            <input name="wp_sir_settings[bg_color]" value="<?php echo $settings['bg_color']; ?>" type="text" id="wpSirColorPicker" />
            <button type="button" class="button button-default button-small" id="wp-sir-clear-bg-color" style="min-height:30px">Очистить</button>
            <p class="description">
                ПРИМЕЧАНИЕ: По умолчанию используется белый фон. Для сохранения прозрачности изображения удалите выбранный цвет, нажав кнопку "Очистить"..</p>
        <?php
        }

        public function settings_field_enable($args)
        {
            $settings = \wp_sir_get_settings(); ?>
            <label for="wp-sir-enable">
                <input type="checkbox" class="wp-sir-as-toggle wp-sir-as-toggle--large" name="wp_sir_settings[enable]" id="wp-sir-enable" value="1" <?php checked($settings['enable'], 1); ?> />
            </label>
            
<?php
        }

        public function settings_page()
        {
            include_once WP_SIR_DIR . 'templates/settings.php';
        }

        function add_settings_help()
        {

            if (!function_exists('get_current_screen')) {
                return;
            }

            $screen = get_current_screen();

            // Add one help tab
            $screen->add_help_tab(array(
                'id'      => 'wp-sir-help-tab1',
                'title'   => esc_html__('Обзор', WP_SIR_NAME),
                'content' =>
                '<p><strong>Изображения:</strong> Выберите изображения для генерации.</p>' .
                    '<p><strong>Размеры:</strong> Выберите, какие размеры генерировать.</p>' .
                    '<p><strong>Цвет фона:</strong> установить цвет появляющейся (пустой) области создаваемой миниатюры. Оставьте пустым для прозрачного фона.</p>' .
                    '<p><strong>Сжатие изображений:</strong> Сжимайте изображения, чтобы уменьшить размер файла изображения для улучшения времени загрузки страницы.</p>' .
                    '<p><strong>Обрезать пробелы:</strong> Удалите ненужные пробелы вокруг изображения, чтобы все изображения выглядели одинаково.</p>' .
                    '<p><strong>Преобразование в JPEG:</strong> Если прозрачные изображения\ не требуются, рекомендуется конвертировать их в JPG, чтобы увеличить скорость загрузки страницы..</p>' .
                    '<p><strong>Включить WebP:</strong> WebP - это рок-старт форматов изображений. Использование WebP позволяет значительно уменьшить размер файла изображения без потери его качества. WebP широко поддерживается всеми современными браузерами, в противном случае происходит автоматическое возвращение к стандартному изображению.</p>'
            ));

            
            $help_sidebar = '<p><a href="https://sirplugin.com" >Контактная поддержка</a></p>';
            

            $screen->set_help_sidebar(
                '<p><strong>' .
                    esc_html__('For more information:', WP_SIR_NAME) .
                    '</strong></p>' . $help_sidebar
            );
        }
    }
endif;
