<?php

/**
 * Register Gutenberg block on server-side.
 *
 * Register the block on server-side to ensure that the block
 * scripts and styles for both frontend and backend are
 * enqueued when the editor loads.
 *
 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
 * @since 1.16.0
 */
register_block_type(
  'cgb/block-flexi-block',
  array(
    // Enqueue blocks.style.build.css on both frontend & backend.
    'style'           => 'flexi_block-cgb-style-css',
    // Enqueue blocks.build.js in the editor only.
    'editor_script'   => 'flexi_block-cgb-block-js',
    // Enqueue blocks.editor.build.css in the editor only.
    'editor_style'    => 'flexi_block-cgb-block-editor-css',
    'attributes'      => array(
      'layout'          => array(
        'type'    => 'string',
        'default' => 'masonry',
      ),
      'column'          => array(
        'type'    => 'integer',
        'default' => 2,
      ),
      'cat'             => array(
        'type'    => 'integer',
        'default' => 0,
      ),
      'perpage'         => array(
        'type'    => 'integer',
        'default' => 8,
      ),
      'padding'         => array(
        'type'    => 'integer',
        'default' => 1,
      ),
      'popup'           => array(
        'type'    => 'boolean',
        'default' => false,
      ),
      'tag_show'        => array(
        'type'    => 'boolean',
        'default' => false,
      ),
      'orderby'         => array(
        'type'    => 'string',
        'default' => 'asc',
      ),
      'tag'             => array(
        'type'    => 'string',
        'default' => '',
      ),
      'filter'          => array(
        'type'    => 'string',
        'default' => 'none',
      ),
      'hover_effect'    => array(
        'type'    => 'string',
        'default' => '',
      ),
      'hover_caption'   => array(
        'type'    => 'string',
        'default' => 'flexi_caption_none',
      ),
      'width'           => array(
        'type'    => 'integer',
        'default' => 150,
      ),
      'height'          => array(
        'type'    => 'integer',
        'default' => 150,
      ),
      'evalue_title'    => array(
        'type'    => 'boolean',
        'default' => true,
      ),
      'evalue_excerpt'  => array(
        'type'    => 'boolean',
        'default' => false,
      ),
      'evalue_custom'   => array(
        'type'    => 'boolean',
        'default' => false,
      ),
      'evalue_icon'     => array(
        'type'    => 'boolean',
        'default' => true,
      ),
      'evalue_tag'      => array(
        'type'    => 'boolean',
        'default' => true,
      ),
      'evalue_count'      => array(
        'type'    => 'boolean',
        'default' => true,
      ),
      'evalue_like'      => array(
        'type'    => 'boolean',
        'default' => true,
      ),
      'evalue_unlike'      => array(
        'type'    => 'boolean',
        'default' => true,
      ),
      'evalue_category' => array(
        'type'    => 'boolean',
        'default' => true,
      ),
      'at_sidebar'      => array(
        'type'    => 'boolean',
        'default' => true,
      ),
      'popup_style'   => array(
        'type'    => 'string',
        'default' => 'on',
      ),
    ),
    'render_callback' => 'flexi_gallery_render_callback',
  )
);

function flexi_gallery_render_callback($args)
{

  // generate the output html
  ob_start();
  $shortcode = '[flexi-gallery]';

  /**
   * Use attribute from the block
   */
  if (isset($args['column'])) {

    if (isset($args['popup']) && '1' == $args['popup']) {
      $popup = $args['popup_style'];
    } else {
      $popup = "off";
    }

    if (isset($args['tag_show']) && '1' == $args['tag_show']) {
      $tag_show = "on";
    } else {
      $tag_show = "off";
    }

    $evalue = "";

    if (isset($args['evalue_title']) && '1' == $args['evalue_title']) {
      $evalue .= "title:on,";
    }
    if (isset($args['evalue_excerpt']) && '1' == $args['evalue_excerpt']) {
      $evalue .= "excerpt:on,";
    }
    if (isset($args['evalue_custom']) && '1' == $args['evalue_custom']) {
      $evalue .= "custom:on,";
    }
    if (isset($args['evalue_icon']) && '1' == $args['evalue_icon']) {
      $evalue .= "icon:on,";
    }
    if (isset($args['evalue_category']) && '1' == $args['evalue_category']) {
      $evalue .= "category:on,";
    }
    if (isset($args['evalue_tag']) && '1' == $args['evalue_tag']) {
      $evalue .= "tag:on,";
    }
    if (isset($args['evalue_count']) && '1' == $args['evalue_count']) {
      $evalue .= "count:on,";
    }
    if (isset($args['evalue_like']) && '1' == $args['evalue_like']) {
      $evalue .= "like:on,";
    }
    if (isset($args['evalue_unlike']) && '1' == $args['evalue_unlike']) {
      $evalue .= "unlike:on,";
    }


    if (isset($args['at_sidebar']) && '1' == $args['at_sidebar']) {
      $at_sidebar = "clear='true'";
    } else {
      $at_sidebar = '';
    }

    if (isset($args['filter']) && 'none' == $args['filter']) {
      $filter = '';
    } else {
      $filter = 'filter="' . $args['filter'] . '"';
    }

    $cat = get_term_by('id', $args['cat'], 'flexi_category');
    if ($cat) {
      $cat = 'album="' . $cat->slug . '"';
    } else {
      $cat = "";
    }

    $shortcode = '[flexi-gallery
  ' . $at_sidebar . '
  column="' . $args['column'] . '"
  perpage="' . $args['perpage'] . '"
  padding="' . $args['padding'] . '"
  layout="' . $args['layout'] . '"
  popup="' . $popup . '"
  ' . $cat . '
  tag="' . $args['tag'] . '"
  orderby="' . $args['orderby'] . '"
  tag_show="' . $tag_show . '"
  hover_effect="' . $args['hover_effect'] . '"
  hover_caption="' . $args['hover_caption'] . '"
  width="' . $args['width'] . '"
  height="' . $args['height'] . '"
  ' . $filter . '
  evalue="' . $evalue . '"
  ] ';
  }
  //print_r($args);

  echo do_shortcode($shortcode);
  //echo $shortcode;
  if (defined('REST_REQUEST') && REST_REQUEST) {
    echo "<small><div style='clear:both;border: 1px solid #999; background: #eee'>";
    echo "<ul><li>Preview is for reference and may not view same.
  <li> Ajax function like page load & popup will not be executed.
  <li>Some settings may not work on specific layout.</ul>";
    echo '' . $shortcode . '</div></small>';


    wp_enqueue_script('flexi_fancybox', plugin_dir_url(__FILE__) . 'js/jquery.fancybox.min.js', array('jquery'), FLEXI_VERSION, false);
    wp_enqueue_style('flexi_public_layout', FLEXI_PLUGIN_URL . '/public/partials/layout/gallery/' . $args['layout'] . '/style.css', array(),  FLEXI_VERSION, 'all');
    wp_enqueue_style('flexi_min', plugin_dir_url(__FILE__) . 'css/flexi-public-min.css', array(),  FLEXI_VERSION, 'all');
    wp_enqueue_style('flexi', plugin_dir_url(__FILE__) . 'css/flexi-public.css', array(), FLEXI_VERSION, 'all');
    wp_enqueue_style('flexi_fancybox', plugin_dir_url(__FILE__) . 'css/jquery.fancybox.min.css', array(), FLEXI_VERSION, 'all');
  }

  return ob_get_clean();
}
