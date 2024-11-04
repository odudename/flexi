<?php

/**
 * Plugin Name:       Flexi Gallery Block
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       flexi-gallery-block
 *
 * @package CreateBlock
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_flexi_gallery_block_block_init()
{
	register_block_type(__DIR__ . '/build', array(
		'render_callback' => 'render_flexi_gallery_block',
	));
}
add_action('init', 'create_block_flexi_gallery_block_block_init');


function render_flexi_gallery_block($attributes)
{
	// Setting default values for all attributes
	$column = isset($attributes['column']) ? $attributes['column'] : 3;
	$lightbox_enabled = isset($attributes['lightboxEnabled']) && $attributes['lightboxEnabled'] ? '1' : '0';
	$show_captions = isset($attributes['showCaptions']) && $attributes['showCaptions'] ? '1' : '0';
	$animation_style = isset($attributes['animationStyle']) ? $attributes['animationStyle'] : 'fade';
	$category = isset($attributes['cat']) ? $attributes['cat'] : 0;
	$popup_style = isset($attributes['popup_style']) ? $attributes['popup_style'] : 'off';
	$layout = isset($attributes['layout']) ? $attributes['layout'] : 'default';
	$tag = isset($attributes['tag']) ? $attributes['tag'] : '';
	$orderby = isset($attributes['orderby']) ? $attributes['orderby'] : 'date';
	$hover_effect = isset($attributes['hover_effect']) ? $attributes['hover_effect'] : 'default';
	$hover_caption = isset($attributes['hover_caption']) ? $attributes['hover_caption'] : '';
	$width = isset($attributes['width']) ? $attributes['width'] : 'auto';
	$height = isset($attributes['height']) ? $attributes['height'] : 'auto';
	$perpage = isset($attributes['perpage']) ? $attributes['perpage'] : 10;
	$padding = isset($attributes['padding']) ? $attributes['padding'] : '10px';
	$filter = isset($attributes['filter']) ? $attributes['filter'] : '';
	$at_sidebar = isset($attributes['at_sidebar']) && $attributes['at_sidebar'] ? "clear='true'" : '';
	$evalue = '';

	// Evalue attribute assembly
	if (isset($attributes['evalue_title']) && $attributes['evalue_title']) $evalue .= "title:on,";
	if (isset($attributes['evalue_excerpt']) && $attributes['evalue_excerpt']) $evalue .= "excerpt:on,";
	if (isset($attributes['evalue_custom']) && $attributes['evalue_custom']) $evalue .= "custom:on,";
	if (isset($attributes['evalue_icon']) && $attributes['evalue_icon']) $evalue .= "icon:on,";
	if (isset($attributes['evalue_category']) && $attributes['evalue_category']) $evalue .= "category:on,";
	if (isset($attributes['evalue_tag']) && $attributes['evalue_tag']) $evalue .= "tag:on,";
	if (isset($attributes['evalue_count']) && $attributes['evalue_count']) $evalue .= "count:on,";
	if (isset($attributes['evalue_like']) && $attributes['evalue_like']) $evalue .= "like:on,";
	if (isset($attributes['evalue_unlike']) && $attributes['evalue_unlike']) $evalue .= "unlike:on,";

	// Handling category if set
	$cat = '';
	if ($category) {
		$cat_term = get_term_by('id', $category, 'flexi_category');
		if ($cat_term) {
			$cat = 'album="' . esc_attr($cat_term->slug) . '"';
		}
	}

	// Build the shortcode with all attributes
	$shortcode = 'flexi-gallery';
	$shortcode .= ' ' . $at_sidebar;
	$shortcode .= ' column="' . esc_attr($column) . '"';
	$shortcode .= ' perpage="' . esc_attr($perpage) . '"';
	$shortcode .= ' padding="' . esc_attr($padding) . '"';
	$shortcode .= ' layout="' . esc_attr($layout) . '"';
	$shortcode .= ' popup="' . esc_attr($popup_style) . '"';
	$shortcode .= ' ' . $cat;
	$shortcode .= ' tag="' . esc_attr($tag) . '"';
	$shortcode .= ' orderby="' . esc_attr($orderby) . '"';
	$shortcode .= ' tag_show="' . esc_attr($show_captions) . '"';
	$shortcode .= ' hover_effect="' . esc_attr($hover_effect) . '"';
	$shortcode .= ' hover_caption="' . esc_attr($hover_caption) . '"';
	$shortcode .= ' width="' . esc_attr($width) . '"';
	$shortcode .= ' height="' . esc_attr($height) . '"';
	$shortcode .= ' filter="' . esc_attr($filter) . '"';
	$shortcode .= ' evalue="' . esc_attr(rtrim($evalue, ',')) . '"';
	$shortcode .= '';

	// Output the shortcode with do_shortcode to render it properly
	ob_start();
	//echo $shortcode . "<hr>";

	// Display an admin preview if in REST context
	if (defined('REST_REQUEST') && REST_REQUEST) {
		echo "<small><div style='clear:both;border: 1px solid #999; background: #eee'>";
		echo "<ul><li>Save & view frontend for actual output</li>";
		echo "<li>This page only displays the shortcode which will be rendered.</li>";
		echo "<li>Some settings may not work on specific layouts.</li></ul>";
		echo '[' . esc_html($shortcode) . ']</div></small>';
	} else {
		echo do_shortcode('[' . $shortcode . ']');
	}

	return ob_get_clean();
}