<?php
/**
 * The template for displaying product content within loops
 *
 * Theme uncode/vc_template/uncode_index.php
 *
 * search for change this line when theme developer update this template
 */

defined( 'ABSPATH' ) || exit;

global $product, $post, $pwf_uncode_block, $uncode_pwf_count;

/**
 * this code is important to instance class
 * from content.php
 */
global $uncode_index_map;
$uncode_index_instance = new uncode_index( $uncode_index_map );

// this code from vc_templates uncode_index.php
//$post                  = new stdClass(); // change this line.
$post->id              = $product->get_ID();
$post->title           = $product->get_title();
$post->type            = 'product';
$post->format          = ( $post->type === 'post' ) ? get_post_format( $post->id ) : ''; // phpcs:ignore
$post->link            = get_permalink( $post->id );
$post->content         = get_the_content();
$post_category         = $uncode_index_instance->getCategoriesCss( $post->id ); // change this line
$post->categories_css  = $post_category['cat_css'];
$post->categories_name = $post_category['cat_name'];
$post->tags_name       = $post_category['tag'];
$post->categories_id   = $post_category['cat_id'];
$post->taxonomy_type   = $post_category['taxonomy'];

extract( $pwf_uncode_block ); // phpcs:ignore

if ( 'carousel' === $index_type ) {
	return;
}

$post_type = 'product'; // change this line or here this is new line add

// phpcs:disable
$general_width = $single_width;
$general_height = $single_height;
$general_fluid_height = $single_fluid_height;
$general_shape = $single_shape;

$stylesArray = array(
	'light',
	'dark'
);
$general_style = ot_get_option('_uncode_general_style');
$general_iso_style = $single_style;
$general_overlay_color = $single_overlay_color;
$general_overlay_coloration = $single_overlay_coloration;
$general_overlay_opacity = $single_overlay_opacity;
$general_overlay_blend = $single_overlay_blend;
$general_text = $single_text;
$general_image_position = $single_image_position;
$general_vertical_text = $single_vertical_text;
$general_image_size = $single_image_size;
$general_lateral_responsive = $single_lateral_responsive;
$general_elements_click = $single_elements_click;
$general_text_visible = $single_text_visible;
$general_text_anim = $single_text_anim;
$general_text_anim_type = $single_text_anim_type;
$general_overlay_visible = $single_overlay_visible;
$general_overlay_anim = $single_overlay_anim;
$general_image_coloration = $single_image_coloration;
$general_image_color_anim = $single_image_color_anim;
$general_image_anim = $single_image_anim;
$general_image_magnetic = $single_image_magnetic;
$general_secondary = $single_secondary;
$general_reduced = $single_reduced;
$general_reduced_mobile = $single_reduced_mobile;
$general_padding = $single_padding;
$general_padding_vertical = $single_padding_vertical;
$general_text_reduced = $single_text_reduced;
$general_h_align = $single_h_align;
$general_h_align_mobile = $single_h_align_mobile;
$general_v_position = $single_v_position;
$general_h_position = $single_h_position;
$general_shadow = $single_shadow;
$general_shadow_weight = $shadow_weight;
$general_shadow_darker = $shadow_darker;
$general_border = $single_border;
$general_icon = $single_icon;
$general_back_color = $single_back_color;
$general_title_transform = $single_title_transform;
$general_title_weight = $single_title_weight;
$general_title_family = $single_title_family;
$general_title_dimension = $single_title_dimension;
$general_title_semantic = $single_title_semantic;
$general_title_height = $single_title_height;
$general_title_space = $single_title_space;
$general_text_lead = $single_text_lead;
$general_meta_custom_typo = $single_meta_custom_typo;
$general_meta_size = $single_meta_size;
$general_meta_weight = $single_meta_weight;
$general_meta_transform = $single_meta_transform;
$general_css_animation = $single_css_animation;
$general_animation_delay = $single_animation_delay;
$general_animation_speed = $single_animation_speed;
$general_parallax_intensity = $single_parallax_intensity;
$general_parallax_centered = $single_parallax_centered;
$general_images_size = $images_size;
$changer_back_color_column       = '';
$changer_back_color_column_inner = '';
$is_tax_query                    = false;

/*** data module preparation ***/
$div_data                   = array();
$div_data['data-type']      = $style_preset;
$div_data['data-layout']    = $isotope_mode;
$div_data['data-lg']        = $screen_lg;
$div_data['data-md']        = $screen_md;
$div_data['data-sm']        = $screen_sm;
$div_data['data-vp-height'] = $single_height_viewport === 'yes' && $index_type === 'isotope' && $style_preset === 'metro';
if ( $single_height_viewport_minus === 'yes' ) {
	$div_data['data-vp-menu'] = 'true';
}

if ( 'custom' === $off_grid_element ) {
	$off_grid_arr = explode( ',', $off_grid_custom );
}

/**
 * From here we copy code start
 * 
 * foreach ( $posts as $key_post => $post ):
 * 	if (!$is_tax_query && !in_array($post->type, $post_types)) {
 *					continue;
 *					} 
 */
$block_data      = array();
$tmb_data_parent = array();
$tmb_data        = array();
$item_thumb_id   = '';

if ($index_type === 'carousel') {
	$block_classes = array('tmb', 'tmb-carousel');
} else {
	$block_classes = array('tmb');
}

// change this line $key_post === uncode_pwf_count
if ( isset( $off_grid_arr ) && is_array( $off_grid_arr ) && ! empty( $off_grid_arr ) && in_array( $uncode_pwf_count % ( 12 / $single_width ), $off_grid_arr ) ) { // phpcs:ignore
	$block_classes[] = 'off-grid-custom-item';
}

$title_classes    = array();
$lightbox_classes = array();

if (!empty($post->format)) {
	$block_classes[] = 'tmb-format-' . $post->format;
}

if ( $post_matrix === 'matrix' ) {
	// change this line uncode_pwf_count == $i_matrix
	$matrix_amount = intval( $matrix_amount ) == 0 ? 1 : intval( $matrix_amount );
	$item_prop     = ( isset( $matrix_items[ ( $i_matrix % $matrix_amount ) . '_i' ] ) ) ? $matrix_items[ ( $i_matrix % $matrix_amount ) . '_i' ] : array();
} else {
	$item_prop = ( isset( $items[ $product->get_id() . '_i' ] ) ) ? $items[ $product->get_id() . '_i' ] : array();
}

if ($post->type === 'product') {
	$block_classes[] = 'tmb-woocommerce';
	$block_data['product'] = true;
} else {
	$block_data['product'] = false;
}

$typeLayout    = $post_blocks['uncode_product']; // ' . str_replace( '-', '_', $post->type )
$single_layout = 'single_layout_product_items';

if ( isset( $item_prop[ $single_layout ] ) ) {
	$typeLayout = uncode_flatArray( vc_sorted_list_parse_value( $item_prop[ $single_layout ] ) );
}

if (
	( isset($typeLayout['media']) && isset($typeLayout['media'][4]) && $typeLayout['media'][4] === 'enhanced-atc')
	||
	( ot_get_option('_uncode_woocommerce_enhanced_atc') === 'on' && ( ! isset($typeLayout['media']) || ! isset($typeLayout['media'][4]) || $typeLayout['media'][4] === '' || $typeLayout['media'][4] === 'inherit-atc' ) )
) {
	$block_classes[] = 'enhanced-atc';
}

if (
	( isset($typeLayout['media']) && isset($typeLayout['media'][5]) && $typeLayout['media'][5] === 'auto-w-atc')
	||
	( ot_get_option('_uncode_woocommerce_width_atc') === 'on' && ( ! isset($typeLayout['media']) || ! isset($typeLayout['media'][5]) || $typeLayout['media'][5] === '' || $typeLayout['media'][5] === 'inherit-w-atc' ) )
) {
	$block_classes[] = 'auto-width-atc';
}

if (isset($typeLayout['price']) && isset($typeLayout['price'][0]) && $typeLayout['price'][0] === 'inline') {
	$block_data['price_inline'] = 'yes';
}

if ( isset($typeLayout['media']) && isset($typeLayout['media'][6]) && $typeLayout['media'][6] === 'atc-typo-column') {
	$block_data['atc_column_typography'] = 'yes';
} else {
	$block_data['atc_column_typography'] = 'no';
}

$block_classes[] = 'atc-typography-inherit';

if ( isset($typeLayout['media']) && isset($typeLayout['media'][7]) && $typeLayout['media'][7] === 'hide-atc') {
	$block_data['show_atc'] = 'no';
} else {
	$block_data['show_atc'] = 'yes';
}

$single_text               = ( isset( $item_prop['single_text'] ) ) ? $item_prop['single_text'] : $general_text;
$single_image_position     = ( isset( $item_prop['single_image_position'] ) ) ? $item_prop['single_image_position'] : $general_image_position;
$single_vertical_text      = ( isset( $item_prop['single_vertical_text'] ) ) ? $item_prop['single_vertical_text'] : $general_vertical_text;
$single_image_size         = ( isset( $item_prop['single_image_size'] ) ) ? $item_prop['single_image_size'] : $general_image_size;
$single_lateral_responsive = ( isset( $item_prop['single_lateral_responsive'] ) ) ? $item_prop['single_lateral_responsive'] : $general_lateral_responsive;

if ($index_type !== 'carousel') {
	$single_width = (isset($item_prop['single_width'])) ? $item_prop['single_width'] : $general_width;
	$block_classes[] = 'tmb-iso-w' . $single_width;
	if ( $single_width == 15 ) {
		$single_width = 3;
	}
} else {
	$single_width = floor( ( intval( $col_width ) / 12 ) * ( 1 / intval( $carousel_lg ) ) * 12 );
}

$single_height       = ( isset( $item_prop['single_height'] ) ) ? $item_prop['single_height'] : $general_height;
$single_fluid_height = ( isset( $item_prop['single_fluid_height'] ) ) ? $item_prop['single_fluid_height'] : $general_fluid_height;

if ( isset($div_data['data-vp-height']) && $div_data['data-vp-height'] ){
	$single_height = $single_fluid_height;
}
$block_classes[] = 'tmb-iso-h' . $single_height;

$images_size       = (isset($item_prop['images_size'])) ? $item_prop['images_size'] : $general_images_size;
$single_back_color = (isset($item_prop['single_back_color'])) ? $item_prop['single_back_color'] : $general_back_color;
$single_shape      = (isset($item_prop['single_shape'])) ? $item_prop['single_shape'] : $general_shape;

if ($single_shape !== '') {
	$block_classes[] = ($single_back_color === '' || (count($typeLayout) === 1 && array_key_exists('media',$typeLayout))) ? 'img-' . $single_shape : 'tmb-' . $single_shape;
}

if ( $single_shape === 'round' && $radius !== '' ){
	$block_classes[] = 'img-round-' . $radius;
}

if (!array_key_exists('media',$typeLayout) && $single_text === 'overlay' && $style_preset === 'masonry') {
	$block_classes[] = 'tmb-no-media';
}

$single_style = (isset($item_prop['single_style'])) ? $item_prop['single_style'] : $general_iso_style;
$block_classes[] = 'tmb-' . $single_style;

if ($index_back_color === '' && $single_back_color === '' && $changer_back_color_column === true && $changer_back_color_column_inner === true) {
	$tmb_data_parent['data-skin-change'] = 'tmb-' . $single_style;
}

$single_overlay_color = (isset($item_prop['single_overlay_color']) && $item_prop['single_overlay_color'] !== '') ? $item_prop['single_overlay_color'] : $general_overlay_color;
$overlay_style = $stylesArray[!array_search($single_style, $stylesArray) ];

if ($single_overlay_color === '') {
	if ($overlay_style === 'light'){
		$single_overlay_color = 'light';
	} else {
		$single_overlay_color = 'dark';
	}
}

$single_overlay_color = 'style-' . $single_overlay_color .'-bg';

$single_overlay_coloration = (isset($item_prop['single_overlay_coloration'])) ? $item_prop['single_overlay_coloration'] : $general_overlay_coloration;
switch ($single_overlay_coloration) {
	case 'top_gradient':
		$block_classes[] = 'tmb-overlay-gradient-top';
		break;
	case 'bottom_gradient':
		$block_classes[] = 'tmb-overlay-gradient-bottom';
		break;
}

$single_overlay_opacity = (isset($item_prop['single_overlay_opacity'])) ? $item_prop['single_overlay_opacity'] : $general_overlay_opacity;

$single_overlay_blend = (isset($item_prop['single_overlay_blend'])) ? $item_prop['single_overlay_blend'] : $general_overlay_blend;

$single_elements_click = (isset($item_prop['single_elements_click'])) ? $item_prop['single_elements_click'] : $general_elements_click;

$single_h_align = (isset($item_prop['single_h_align'])) ? $item_prop['single_h_align'] : $general_h_align;
$single_h_align_mobile = (isset($item_prop['single_h_align_mobile'])) ? $item_prop['single_h_align_mobile'] : $general_h_align_mobile;

$single_text_visible = (isset($item_prop['single_text_visible'])) ? $item_prop['single_text_visible'] : $general_text_visible;
if ($single_text_visible === 'yes') {
	$block_classes[] = 'tmb-text-showed';
}

$single_text_anim = (isset($item_prop['single_text_anim'])) ? $item_prop['single_text_anim'] : $general_text_anim;
if ($single_text_anim === 'yes') {
	$block_classes[] = 'tmb-overlay-text-anim';
}

$single_text_anim_type = (isset($item_prop['single_text_anim_type'])) ? $item_prop['single_text_anim_type'] : $general_text_anim_type;
if ($single_text_anim_type === 'btt') {
	$block_classes[] = 'tmb-reveal-bottom';
}

$single_overlay_visible = (isset($item_prop['single_overlay_visible'])) ? $item_prop['single_overlay_visible'] : $general_overlay_visible;
if ($single_overlay_visible === 'yes') {
	$block_classes[] = 'tmb-overlay-showed';
}

$single_overlay_anim = (isset($item_prop['single_overlay_anim'])) ? $item_prop['single_overlay_anim'] : $general_overlay_anim;
if ($single_overlay_anim === 'yes') {
	$block_classes[] = 'tmb-overlay-anim';
}

if ($single_text === 'overlay') {

	$single_h_position = (isset($item_prop['single_h_position'])) ? $item_prop['single_h_position'] : $general_h_position;

	$single_reduced = (isset($item_prop['single_reduced'])) ? $item_prop['single_reduced'] : $general_reduced;
	$single_reduced_mobile = (isset($item_prop['single_reduced_mobile'])) ? $item_prop['single_reduced_mobile'] : $general_reduced_mobile;
	if ($single_reduced !== '') {
		switch ($single_reduced) {
			case 'three_quarter':
				$block_classes[] = 'tmb-overlay-text-reduced';
				break;
			case 'half':
				$block_classes[] = 'tmb-overlay-text-reduced-2';
				break;
			case 'limit-width':
				$block_data['limit-width'] = true;
				$single_h_position = 'center';
				break;
		}
		if ($single_h_position !== '') {
			$block_classes[] = 'tmb-overlay-' . $single_h_position;
		}
		if ($single_reduced_mobile !== '') {
			$block_classes[] = 'tmb-overlay-text-wide-sm';
		}
	}

	$single_v_position = (isset($item_prop['single_v_position'])) ? $item_prop['single_v_position'] : $general_v_position;
	if ($single_v_position !== '') {
		$block_classes[] = 'tmb-overlay-' . $single_v_position;
	}
	if ($single_h_align !== '') {
		$block_classes[] = 'tmb-overlay-text-' . $single_h_align;
	}
	if ($single_h_align_mobile !== '') {
		$block_classes[] = 'tmb-overlay-text-mobile-' . $single_h_align_mobile;
	}
} else {
	if ( $single_text === 'lateral' ) {
		$single_image_position = $single_image_position == '' ? 'left' : $single_image_position;
		$single_vertical_text = $single_vertical_text == '' ? 'top' : $single_vertical_text;
		$block_classes[] = 'tmb-content-lateral-' . $single_image_position;
		$block_classes[] = 'tmb-content-vertical-' . $single_vertical_text;
		$block_classes[] = 'tmb-content-size-' . intval( $single_image_size );
		if ( $single_lateral_responsive === 'yes' ) {
			$block_classes[] = 'tmb-content-lateral-responsive';
		}
	}

	$block_classes[] = 'tmb-content-' . $single_h_align;
	if ($single_h_align_mobile !== '') {
		$block_classes[] = 'tmb-content-mobile-' . $single_h_align_mobile;
	}
}

$single_text_reduced = (isset($item_prop['single_text_reduced'])) ? $item_prop['single_text_reduced'] : $general_text_reduced;
if ($single_text_reduced === 'yes') {
	$block_classes[] = 'tmb-text-space-reduced';
}

$single_image_coloration = (isset($item_prop['single_image_coloration'])) ? $item_prop['single_image_coloration'] : $general_image_coloration;
if ($single_image_coloration === 'desaturated') {
	$block_classes[] = 'tmb-desaturated';
}

$single_image_color_anim = (isset($item_prop['single_image_color_anim'])) ? $item_prop['single_image_color_anim'] : $general_image_color_anim;
if ($single_image_color_anim === 'yes') {
	$block_classes[] = 'tmb-image-color-anim';
}

$single_image_anim = (isset($item_prop['single_image_anim'])) ? $item_prop['single_image_anim'] : $general_image_anim;
if ($single_image_anim === 'yes') {
	$single_image_magnetic = (isset($item_prop['single_image_magnetic'])) ? $item_prop['single_image_magnetic'] : $general_image_magnetic;
	if ($single_image_magnetic === 'yes') {
		$block_classes[] = 'tmb-image-anim-magnetic';
	} else {
		$block_classes[] = 'tmb-image-anim';
	}
}

$single_secondary = (isset($item_prop['single_secondary'])) ? $item_prop['single_secondary'] : $general_secondary;
if ($single_secondary === 'yes') {
	$block_classes[] = 'tmb-show-secondary';
}

$single_icon = (isset($item_prop['single_icon'])) ? $item_prop['single_icon'] : $general_icon;

$single_shadow = (isset($item_prop['single_shadow'])) ? $item_prop['single_shadow'] : $general_shadow;
$shadow_weight = (isset($item_prop['shadow_weight'])) ? $item_prop['shadow_weight'] : $general_shadow_weight;
$shadow_darker = (isset($item_prop['shadow_darker'])) ? $item_prop['shadow_darker'] : $general_shadow_darker;

if ($single_shadow === 'yes') {
	$block_classes[] = 'tmb-shadowed';

	$shadow_out = $shadow_weight;
	if ( $shadow_weight === '' ){
		$shadow_out = 'xs';
	}
	if ( $shadow_darker !== '' ) {
		$shadow_out = 'darker-' . $shadow_out;
	}

	$block_classes[] = 'tmb-shadowed-' . $shadow_out;
}

$single_title_semantic = (isset($item_prop['single_title_semantic'])) ? $item_prop['single_title_semantic'] : $general_title_semantic;
if ($single_title_semantic !== '') {
	$block_data['tag'] = $single_title_semantic;
}

$single_border = (isset($item_prop['single_border'])) ? $item_prop['single_border'] : $general_border;
if ($single_border !== 'yes') {
	$block_classes[] = 'tmb-bordered';
}

$single_title_transform = (isset($item_prop['single_title_transform'])) ? $item_prop['single_title_transform'] : $general_title_transform;
if ($single_title_transform !== '') {
	$block_classes[] = 'tmb-entry-title-' . $single_title_transform;
}

$single_title_family = (isset($item_prop['single_title_family'])) ? $item_prop['single_title_family'] : $general_title_family;
if ($single_title_family !== '') {
	$title_classes[] = $single_title_family;
}

$single_title_dimension = (isset($item_prop['single_title_dimension'])) ? $item_prop['single_title_dimension'] : $general_title_dimension;
if ($single_title_dimension !== '') {
	$title_classes[] = $single_title_dimension;
} else {
	if ($style_preset === 'metro') {
		switch ($single_width) {
			case 1:
			case 2:
				$title_classes[] = 'h6';
				break;
			case 3:
				$title_classes[] = 'h5';
				break;
			case 4:
				$title_classes[] = 'h4';
				break;
			case 6:
			case 7:
			case 8:
				$title_classes[] = 'h3';
				break;
			case 9:
			case 10:
				$title_classes[] = 'h2';
				break;
			case 11:
			case 12:
				$title_classes[] = 'h1';
				break;
		}
	} else {
		$title_classes[] = 'h6';
	}
}

$single_title_weight = (isset($item_prop['single_title_weight'])) ? $item_prop['single_title_weight'] : $general_title_weight;
if ($single_title_weight !== '') {
	$title_classes[] = 'font-weight-' . $single_title_weight;
}

$single_title_height = (isset($item_prop['single_title_height'])) ? $item_prop['single_title_height'] : $general_title_height;
if ($single_title_height !== '') {
	$title_classes[] = $single_title_height;
}

$single_title_space = (isset($item_prop['single_title_space'])) ? $item_prop['single_title_space'] : $general_title_space;
if ($single_title_space !== '') {
	$title_classes[] = $single_title_space;
}

$single_text_lead = (isset($item_prop['single_text_lead'])) ? $item_prop['single_text_lead'] : $general_text_lead;
if ($single_text_lead === 'yes') {
	$block_data['text_lead'] = 'yes';
} else if ($single_text_lead === 'small') {
	$block_data['text_lead'] = 'small';
}

$single_meta_custom_typo = (isset($item_prop['single_meta_custom_typo'])) ? $item_prop['single_meta_custom_typo'] : $general_meta_custom_typo;

if ( $single_meta_custom_typo === 'yes' ) {

	$single_meta_size = (isset($item_prop['single_meta_size'])) ? $item_prop['single_meta_size'] : $general_meta_size;
	if ( $single_meta_size !== '' ) {
		$block_classes[] = 'tmb-meta-size-' . $single_meta_size;
	}

	$single_meta_weight = (isset($item_prop['single_meta_weight'])) ? $item_prop['single_meta_weight'] : $general_meta_weight;
	if ( $single_meta_weight !== '' ) {
		$block_classes[] = 'tmb-meta-weight-' . $single_meta_weight;
	}

	$single_meta_transform = (isset($item_prop['single_meta_transform'])) ? $item_prop['single_meta_transform'] : $general_meta_transform;
	if ( $single_meta_transform !== '' ) {
		$block_classes[] = 'tmb-meta-transform-' . $single_meta_transform;
	}
}

$single_animation_delay = (isset($item_prop['single_animation_delay'])) ? $item_prop['single_animation_delay'] : $general_animation_delay;
$single_animation_speed = (isset($item_prop['single_animation_speed'])) ? $item_prop['single_animation_speed'] : $general_animation_speed;
$single_css_animation = (isset($item_prop['single_css_animation'])) ? $item_prop['single_css_animation'] : $general_css_animation;

if ($single_css_animation !== '') {
	if ( $single_css_animation === 'parallax' ) {
		$single_parallax_intensity = ( isset( $item_prop['single_parallax_intensity'] ) ) ? $item_prop['single_parallax_intensity'] : $general_parallax_intensity;
		$single_parallax_centered = ( isset( $item_prop['single_parallax_centered'] ) ) ? $item_prop['single_parallax_centered'] : $general_parallax_centered;
		$block_data['parallax'] = $single_parallax_intensity;
		$block_data = array_merge( $block_data, uncode_get_parallax_div_data( $single_parallax_intensity, $single_parallax_centered ) );
	} else {
		$block_data['animation'] = ' animate_when_almost_visible ' . $single_css_animation;
		if ($single_animation_delay !== '') {
			$tmb_data['data-delay'] = $single_animation_delay;
		}
		if ($single_animation_speed !== '') {
			$tmb_data['data-speed'] = $single_animation_speed;
		}
	}
}

if ( $custom_cursor !== '' ) {
	$tmb_data['data-cursor'] = 'icon-' . esc_attr( $custom_cursor );
}

if (isset($typeLayout['media']) && isset($typeLayout['media'][0])) {
	switch ($typeLayout['media'][0]) {
		case 'featured':
			if ( $is_tax_query ) {
				$item_thumb_id = uncode_get_term_featured_thumbnail_id( $post->id );
			} else {
				$item_thumb_id = get_post_thumbnail_id($post->id);
				if ( $item_thumb_id === '' || $item_thumb_id == 0 ) {
					$item_thumb_id = get_post_meta( $post->id, '_uncode_featured_media', 1);
					$medias = explode(',', $item_thumb_id);
					if (is_array($medias) && isset($medias[0])) {
						$item_thumb_id = $medias[0];
					}
				}
			}
			break;
		case 'media':
			$item_thumb_id = get_post_meta( $post->id, '_uncode_featured_media', 1);
			if ( $item_thumb_id === '' || $item_thumb_id == 0 ) {
				$item_thumb_id = get_post_thumbnail_id($post->id);
			}
			break;
		case 'custom':
			if (!$is_tax_query && isset($item_prop['back_image'])) {
				$item_thumb_id = $item_prop['back_image'];
			}
			break;
	}
}

if (isset($typeLayout['media']) && ( $item_thumb_id === '' || $item_thumb_id == 0 ) && $single_text !== 'overlay') {
	if ($post->type === 'product' && isset( $typeLayout['media'][0] ) && $typeLayout['media'][0] === 'featured') {
		$typeLayout['media'][0] = 'placeholder';
	} else {
		unset($typeLayout['media']);
		if ($single_back_color === '' && isset($item_prop) && is_array($item_prop)) {
			$item_prop['single_padding'] = 0;
		}
	}
}

$block_classes[] = $post->categories_css;
if ($no_double_tap === 'yes') {
	$block_classes[] = 'tmb-no-double-tap';
}

if ( $is_tax_query ) {
	$block_classes[] = 'tmb-term-id-' . $post->id;
} else {
	$block_classes[] = 'tmb-id-' . $post->id;
}

$block_data['id'] = $post->id;
$block_data['content'] = $post->content;
$block_data['classes'] = $block_classes;
$block_data['tmb_data'] = $tmb_data;
$block_data['tmb_data_parent'] = $tmb_data_parent;
$block_data['media_id'] = $item_thumb_id;
$block_data['images_size'] = $images_size;
$block_data['single_style'] = $single_style;
$block_data['single_text'] = $single_text;
if ( $single_text !== 'lateral' ) {
	$single_image_size = 1;
}
$block_data['single_image_size'] = $single_image_size;
$block_data['single_image_position'] = $single_image_position;
$block_data['single_elements_click'] = $single_elements_click;
$block_data['single_secondary'] = $single_secondary;
$block_data['overlay_opacity'] = $single_overlay_opacity;
$block_data['overlay_blend'] = $single_overlay_blend;
$block_data['overlay_color'] = $single_overlay_color;
$block_data['overlay_style'] = $overlay_style;
$block_data['thumb_size'] = $thumb_size;
$block_data['single_width'] = $single_height_viewport === 'yes' || $thumb_size === 'fluid' ? '12' : $single_width;
$block_data['single_height'] = $single_height_viewport === 'yes' || $thumb_size === 'fluid' || ( $style_preset === 'metro' && $single_text === 'lateral' ) ? '' : $single_height;
$block_data['single_back_color'] = $single_back_color;
$block_data['single_icon'] = $single_icon;
$block_data['single_title'] = $post->title;

$single_padding = (isset($item_prop['single_padding'])) ? $item_prop['single_padding'] : $general_padding;
$single_padding_vertical = (isset($item_prop['single_padding_vertical'])) ? $item_prop['single_padding_vertical'] : $general_padding_vertical;
switch ($single_padding) {
	case 0:
		$block_data['text_padding'] = 'no-block-padding';
		break;
	case 1:
		$block_data['text_padding'] = 'half-block-padding';
		break;
	case 2:
	default:
		$block_data['text_padding'] = 'single-block-padding';
		break;
	case 3:
		$block_data['text_padding'] = 'double-block-padding';
		break;
	case 4:
		$block_data['text_padding'] = 'triple-block-padding';
		break;
	case 5:
		$block_data['text_padding'] = 'quad-block-padding';
		break;
}

if ( $single_padding_vertical !== '' ) {
	$block_data['text_padding'] .= ' single-h-padding';
}

if (isset($item_prop['text_length'])) {
	$block_data['text_length'] = $item_prop['text_length'];
}
if (isset($item_prop['read_more_text'])) {
	$block_data['read_more_text'] = $item_prop['read_more_text'];
}

if (!$is_tax_query && isset($item_prop['single_link']) && $item_prop['single_link'] != '') {
	$post->link = $item_prop['single_link'];
	$link = vc_build_link( $item_prop['single_link'] );
	$post->link = $link['url'];
	$a_title = $link['title'];
	$a_target = $link['target'];
	$block_data['link'] = $link;
} else {
	$block_data['link'] = array(
		'url' => $post->link,
		'target' => '_self'
	);
}


$block_data['title_classes'] = $title_classes;
if ($single_text === 'overlay' && $single_elements_click !== 'yes') {
	$block_data['single_categories'] = $post->categories_name;
	$block_data['single_tags'] = $post->tags_name;
} else {
	$block_data['single_categories'] = $uncode_index_instance->getCategoriesLink( $post->id ); // change this line
}


$single_categories = $block_data['single_categories'];
$single_categories_tax = array();
$single_categories_tag = array();
foreach ($single_categories as $key => $value) {
	if ( isset($value['tax']) && $value['tax'] == 'category' ) {
		$single_categories_tax[] = $value;
		unset($single_categories[$key]);
	} elseif ( isset($value['tax']) && $value['tax'] == 'post_tag' ) {
		$single_categories_tag[] = $value;
		unset($single_categories[$key]);
	}
}

$single_categories = array_merge($single_categories, $single_categories_tax);
$single_categories = array_merge($single_categories, $single_categories_tag);
$block_data['single_categories'] = $single_categories;

$block_data['taxonomy_type'] = $post->taxonomy_type;
foreach ( $block_data['taxonomy_type'] as $key_tax => $value_tax ) {
	if ( $value_tax === 'product_type' || $value_tax === 'product_visibility' || $value_tax === 'product_tag' ) {
		unset( $block_data['taxonomy_type'][$key_tax] );
	}
}
$single_categories_id = $post->categories_id;
$single_categories_id_tax = array();
$single_categories_id_tag = array();
foreach ($single_categories_id as $key => $tax) {
	$term = get_term($tax);
	if ( $term->taxonomy == 'category' ) {
		$single_categories_id_tax[] = $tax;
		unset($single_categories_id[$key]);
	} elseif ( $term->taxonomy == 'post_tag' ) {
		$single_categories_id_tag[] = $tax;
		unset($single_categories_id[$key]);
	}
}

$single_categories_id = array_merge($single_categories_id, $single_categories_id_tax);
$single_categories_id = array_merge($single_categories_id, $single_categories_id_tag);
$block_data['single_categories_id'] = $single_categories_id;

if (isset($typeLayout['media'][1]) && $typeLayout['media'][1] === 'lightbox') {
	if ($lbox_skin !== '') {
		$lightbox_classes['data-skin'] = $lbox_skin;
	}
	if ($lbox_title !== '') {
		$lightbox_classes['data-title'] = true;
	}
	if ($lbox_caption !== '') {
		$lightbox_classes['data-caption'] = true;
	}
	if ($lbox_dir !== '') {
		$lightbox_classes['data-dir'] = $lbox_dir;
	}
	if ($lbox_social !== '') {
		$lightbox_classes['data-social'] = true;
	}
	if ($lbox_deep !== '') {
		$lightbox_classes['data-deep'] = $el_id;
	}
	if ($lbox_no_tmb !== '') {
		$lightbox_classes['data-notmb'] = true;
	}
	if ($lbox_no_arrows !== '') {
		$lightbox_classes['data-noarr'] = true;
	}
	if (count($lightbox_classes) === 0) {
		$lightbox_classes['data-active'] = true;
	}
} elseif (isset($typeLayout['media'][1]) && $typeLayout['media'][1] === 'nolink') {
	$block_data['link_class'] = 'inactive-link';
	$block_data['link'] = '#';
}

if (isset($typeLayout['media'][2]) && $typeLayout['media'][2] === 'poster') {
	$block_data['poster'] = true;
} else {
	$block_data['poster'] = false;
}

if (isset($typeLayout['icon'][0]) && $typeLayout['icon'][0] !== '') {
	$block_data['icon_size'] = ' t-icon-size-' . $typeLayout['icon'][0];
}

//$block_data['lb_index'] = $no_album_counter;
//$no_album_counter++;                           // change this line
$block_data['lb_index'] = $uncode_pwf_count;     // change this line

$block_data['parent_id'] = $parent_id;

// change this line  change blck of code
/*if ( $is_tax_query ) {
	$block_data['is_tax_block'] = true;
	$block_data['tax_queried'] = $tax_queried;
}*/

// Pass layout type
$block_data['is_isotope']  = $index_type === 'isotope' ? true : false;
$block_data['is_carousel'] = $index_type === 'carousel' ? true : false;

echo uncode_create_single_block($block_data, $el_id, $style_preset, $typeLayout, $lightbox_classes, 'no', true);
