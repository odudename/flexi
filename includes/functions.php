<?php
// Functions used during submission of image only
require_once plugin_dir_path(__FILE__) . 'functions_post_image.php';
// Functions used during submission of URL only
require_once plugin_dir_path(__FILE__) . 'functions_post_url.php';
// Functions related to media only
require_once plugin_dir_path(__FILE__) . 'functions_for_media.php';

//combine array
function flexi_combine_arr($a, $b)
{
    $acount = count($a);
    $bcount = count($b);
    $size = ($acount > $bcount) ? $bcount : $acount;
    $a = array_slice($a, 0, $size);
    $b = array_slice($b, 0, $size);
    return array_combine($a, $b);
}

// Gets link of post author with it's avatar icon.
function flexi_author($author = '', $redirect = true, $image = true)
{
    if ('' == $author) {
        $author = get_user_by('id', get_the_author_meta('ID'));
    } else {
        $author = get_user_by('login', $author);
    }

    if (flexi_get_option('primary_page', 'flexi_image_layout_settings', 0) != '0') {
        if (flexi_get_option('enable_ultimate_member', 'flexi_extension', 0) == '1' && function_exists('um_user_profile_url') && $redirect) {
            $linku = um_user_profile_url($author->ID);
            // $linku = esc_url(add_query_arg('profiletab', 'flexi', $linku));
        } elseif (flexi_get_option('enable_buddypress', 'flexi_extension', 0) == '1' && function_exists('bp_core_get_user_domain') && $redirect) {
            $linku = bp_core_get_user_domain($author->ID);
        } else {
            $linku = get_permalink(flexi_get_option('primary_page', 'flexi_image_layout_settings', 0));
            $linku = add_query_arg('flexi_user', $author->user_nicename, $linku);
        }
    } else {
        $linku = '';
    }

    if ($image) {
        $style_text_color = flexi_get_option('flexi_style_text_color', 'flexi_app_style_settings', '');
        return '
                <div class="fl-media fl-mb-1">
                    <div class="fl-media-left">
                        <figure class="fl-image fl-is-48x48">
                            <a href="' . esc_url($linku) . '"><img src="' . get_avatar_url($author->user_email, $size = '50') . '" width="50" alt="' . $author->display_name . '" /></a>
                        </figure>
                    </div>
                    <div class="fl-media-content">
                        <p class="fl-title fl-is-6 ' . $style_text_color . '">' . $author->first_name . ' ' . $author->last_name . '</p>
                        <p class="fl-subtitle fl-is-7 ' . $style_text_color . '">@' . $author->user_login . '</p>
                    </div>
                </div>';
    } else {
        return '<a href="' . esc_url($linku) . '"> ' . $author->first_name . ' ' . $author->last_name . '</a>';
    }
}

// Custom field get id
function flexi_custom_field_value($post_id, $field_name)
{
    $value = get_post_meta(esc_attr($post_id), esc_attr($field_name), '');
    if (is_array($value)) {
        if (isset($value[0]) && '' != $value[0]) {
            return $value[0];
        }
    } else {
        return '';
    }
}


function flexi_custom_field_value_array($post_id, $field_name)
{
    $value = get_post_meta(esc_attr($post_id), esc_attr($field_name), true);
    //flexi_log($value);
    if (is_array($value)) {
        return $value[0];
    } else {
        return '';
    }
}
/**
 * Sample template tag function for outputting a cmb2 file_list
 *
 * @param  string $post_id
 * @param  string $img_size           Size of image to show
 */
function flexi_standalone_gallery($post_id, $img_size = 'thumbnail', $width = 150, $height = 150, $trash = false)
{
    $output = '';

    $output .= '<style>.flexi-image-wrapper-icon {
    width: ' . esc_attr($width) . 'px;
    height: ' . esc_attr($height) . 'px;
    border: 1px solid #eee;
  }
  .flexi-image-wrapper-icon img {
    object-fit: cover;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 100%;
  }
  </style>';
    // Get the list of files
    $files = get_post_meta($post_id, 'flexi_standalone_gallery', 1);
    // flexi_log($files);

    // Loop through them and output an image
    if (!empty($files)) {
        foreach ((array) $files as $attachment_id => $attachment_url) {

            $image_alt = flexi_get_attachment($attachment_id);
            if (!empty($image_alt)) {
                // flexi_log($image_alt);
                $output .= '<div id="flexi_media_' . absint($image_alt['id']) . '" class="flexi_responsive_fixed" style="text-align:center;"><div class="flexi_gallery_grid"><div class="flexi-image-wrapper-icon"><a data-fancybox="flexi_standalone_gallery" href="' . wp_get_attachment_image_src($attachment_id, 'flexi_large')[0] . '" data-caption="' . sanitize_text_field($image_alt['title']) . '" border="0">';
                $output .= '<img src="' . wp_get_attachment_image_src($attachment_id, $img_size)[0] . '" large-src="' . wp_get_attachment_image_src($attachment_id, 'flexi_large')[0] . '">';
                $output .= '</a></div></div>';
                if ($trash) {
                    $nonce = wp_create_nonce('flexi_ajax_delete');
                    $output .= '<a href="#" class="flexi_css_button" id="flexi_ajax_delete" data-nonce="' . $nonce . '" data-media_id="' . esc_attr($image_alt['id']) . '" data-post_id="' . esc_attr($post_id) . '" title="Delete"><span class="flexi_css_button-border"><span class="flexi_icon_trash"></span></span></a>';
                }
                $output .= '</div>';
            }
        }
    }
    return $output;
}

// Custom Fields
function flexi_custom_field_loop($post, $page = 'detail', $count = 30, $css = true)
{
    $link = get_permalink(flexi_get_option('primary_page', 'flexi_image_layout_settings', 0));
    $record_count = false;

    $group = '';

    $c = 1;
    for ($x = 1; $x <= 30; $x++) {
        $label = flexi_get_option('flexi_field_' . $x . '_label', 'flexi_custom_fields', '');
        $display = flexi_get_option('flexi_field_' . $x . '_display', 'flexi_custom_fields', '');
        $value = get_post_meta($post->ID, 'flexi_field_' . $x, '');
        // flexi_log($value);

        if (!$value) {
            $value[0] = '';
        }
        if (is_array($display)) {
            if (in_array($page, $display)) {
                if ('' != $value[0]) {
                    if ($css) {
                        // If enabled search at custom field settings
                        $record_count = true;
                        $enable_link = flexi_get_option('flexi_field_' . $x . '_link', 'flexi_custom_fields', '');

                        if (in_array('link', $display)) {
                            $link = add_query_arg('search', 'flexi_field_' . $x . ':' . $value[0], $link);
                            $group .= '<li><label>' . sanitize_text_field($label) . '<span class="dashicons dashicons-arrow-right"></span></label><span><a href="' . esc_url($link) . '">' . sanitize_text_field($value[0]) . '</a></span></li>';
                        } else {
                            $group .= '<li><label>' . sanitize_text_field($label) . '<span class="dashicons dashicons-arrow-right"></span></label><span>' . sanitize_text_field($value[0]) . '</span></li>';
                        }
                    } else {
                        $group .= sanitize_text_field($label) . ': ' . sanitize_text_field($value[0]) . ' ';
                    }

                    if ($count == $c) {
                        break;
                    }
                    $c++;
                }
            }
        }
    }

    if ($record_count && $css) {
        $group = '<ul class="flexi_list">' . $group . '</ul>';
    }

    return $group;
}

// Page Number
// flexi-pagination is same as woocommerce-pagination css
function flexi_page_navi($query, $class = 'flexi-pagination')
{
    $big = 999999999; // need an unlikely integer
    $pages = paginate_links(
        array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $query->max_num_pages,
            'type' => 'array', // default it will return anchor
        )
    );

    $pager = '';
    if ($pages) {
        $pager .= '<nav class="' . $class . '"><ul class="page-numbers">';
        foreach ($pages as $page) {
            $pager .= "<li>$page</li>";
        }
        $pager .= '</ul></nav>';
    }
    return $pager;
}

// Check if user have editing rights
function flexi_check_rights($post_id = 0)
{
    $edit_post = true;
    if (is_user_logged_in()) {

        $post_author_id = get_post_field('post_author', $post_id);
        if (get_current_user_id() == $post_author_id) {
            $edit_post = true;
        } else {
            $edit_post = false;
        }
    } else {
        $edit_post = false;
    }

    /*
    //Check if page is edit page to prevent from spam
    if (!is_flexi_page('edit_flexi_page', 'flexi_form_settings')) {
    $edit_post = false;
    }
     */
    return $edit_post;
}
// Return album name with and without link
function flexi_get_album($post_id, $type, $field = 'flexi_category')
{
    // $type values can be term_id,slug,name,url
    $categories = get_the_terms(esc_attr($post_id), $field);
    foreach ((array) $categories as $category) {
        if ('url' == $type) {
            return flexi_get_category_page_link($category, $field);
        } else {
            if (isset($category->$type)) {
                return $category->$type;
            } else {
                return '';
            }
        }
    }
}

// returns album/category url. 'flexi_category' is $taxonomy. $term is a album slug name.
function flexi_get_category_page_link($term, $taxonomy)
{
    $link = '/';

    if (flexi_get_option('primary_page', 'flexi_image_layout_settings', 0) > 0) {

        $flexi_link_sub_cate = get_term_meta($term->term_id, 'flexi_link_sub_cate', true);
        if ('on' == $flexi_link_sub_cate) {
            $category_page_link = get_permalink(flexi_get_option('category_page', 'flexi_categories_settings', 0));
            $link = add_query_arg($taxonomy, $term->slug, $category_page_link);
        } else {
            $link = get_permalink(flexi_get_option('primary_page', 'flexi_image_layout_settings', 0));
            $link = add_query_arg($taxonomy, $term->slug, $link);
        }
    }

    return $link;
}

// Get details of post based on post_id
function flexi_get_detail($post_id, $field)
{
    $post = get_post(esc_attr($post_id));
    if ($post) {
        return $post->$field;
    } else {
        return '';
    }
}

// display taxonomy terms without links: separated with commas
function flexi_get_taxonomy_raw($post_id, $taxonomy_name)
{
    $terms = wp_get_post_terms(esc_attr($post_id), $taxonomy_name);
    $count = count($terms);
    $data = '';
    if ($count > 0) {
        foreach ($terms as $term) {
            $data .= $term->slug . ',';
        }
    }
    return rtrim($data, ',') . ',';
}

// Generate gallery_tags link for above gallery
function flexi_generate_tags($tags_array, $flexi_tag_class = 'fl-is-medium', $filter_class = 'filter_tag')
{
    $taglink = '';
    if (count($tags_array) > 1) {
        $taglink .= '<div class="fl-tags" id="flexi_tag_filter">';

        $taglink .= '<div id="show_all" class="' . esc_attr($filter_class) . ' fl-tag ' . esc_attr($flexi_tag_class) . ' fl-has-text-weight-bold">' . __('Show all', 'flexi') . '</div> ';
        if (count($tags_array) > 1) {
            foreach ($tags_array as $tags => $value) {
                $taglink .= '<div id="' . esc_attr($tags) . '" class="' . esc_attr($filter_class) . ' fl-tag ' . esc_attr($flexi_tag_class) . ' ">' . ucfirst($value) . '</div> ';
            }
        }

        $taglink .= '</div>';
    }

    return $taglink;
}

// Flexi List TAGs & Category
function flexi_list_tags($post, $class1 = 'fl-icon-text', $class2 = 'fl-icon', $icon = 'fas fa-arrow-right', $type = 'flexi_tag')
{
    // Returns All Term Items for "my_taxonomy"
    $term_list = wp_get_post_terms($post->ID, $type, array('fields' => 'all'));
    // var_dump($term_list);
    $output = '';
    if (count($term_list) > 0) {
        $output .= '<span class="' . esc_attr($class1) . '">
        <span class="' . esc_attr($class2) . '">
        <i class="' . esc_attr($icon) . '"></i>
        </span><span>';

        for ($x = 0; $x < count($term_list); $x++) {

            $link = get_permalink(flexi_get_option('primary_page', 'flexi_image_layout_settings', 0));
            $link = add_query_arg(esc_attr($type), $term_list[$x]->slug, $link);

            $output .= '<a href="' . esc_url($link) . '">' . esc_attr($term_list[$x]->name) . '</a> ';
        }

        $output .= '</span></span>';
    }

    return $output;
}

// Get single album with title
function flexi_album_single($term_slug, $class = 'flexi_user-list')
{
    $term = get_term_by('slug', esc_attr($term_slug), 'flexi_category');
    if ($term) {
        $link = get_permalink(flexi_get_option('primary_page', 'flexi_image_layout_settings', 0));
        $link = add_query_arg('flexi_category', $term->slug, $link);
        $style_text_color = flexi_get_option('flexi_style_text_color', 'flexi_app_style_settings', '');

        return '
        <div class="fl-media fl-mb-1">
            <div class="fl-media-left">
                <figure class="fl-image fl-is-48x48">
                    <a href="' . esc_url($link) . '"><img class="fl-is-rounded" src="' . flexi_album_image($term_slug) . '" width="50" alt="' . $term->name . '" /></a>
                </figure>
            </div>
            <div class="fl-media-content">
                <p class="fl-title fl-is-6 ' . esc_attr($style_text_color) . '">' . esc_attr($term->name) . '</p>
                <p class="fl-subtitle fl-is-7 ' . esc_attr($style_text_color) . '"> <span class="icon-text">
                <span class="icon">
                  <i class="fas fa-folder"></i>
                </span>
                <span> ' . __('Category', 'flexi') . '</span>
              </span></p>
            </div>
        </div>';
    } else {
        return '';
    }
}

// Get album image
function flexi_album_image($term_slug)
{
    $cate_thumb_pic = plugins_url('../public/images/noimg_thumb.jpg', __FILE__);

    $term = get_term_by('slug', $term_slug, 'flexi_category');
    if ('' != $term_slug && true == $term) {

        $cate_thumb_pic = get_term_meta($term->term_id, 'flexi_image_category', true);
        if (!$cate_thumb_pic) {
            $cate_thumb_pic = plugins_url('../public/images/noimg_thumb.jpg', __FILE__);
        }
    }
    return $cate_thumb_pic;
}

// Layout List select box
// parameters 'selected layout',"input field name","form | media | grid",'show eye icon true | false'
function flexi_layout_list($args = '')
{
    $defaults = array(
        'folder' => 'detail',
        'name' => 'layout_name',
        'id' => '',
        'class' => '',
        'value_field' => 'ID',
    );

    $parsed_args = wp_parse_args($args, $defaults);

    $output = '';
    // Back-compat with old system where both id and name were based on $name argument
    if (empty($parsed_args['id'])) {
        $parsed_args['id'] = $parsed_args['name'];
    }

    $output = "<select name='" . esc_attr($parsed_args['name']) . "' id='" . esc_attr($parsed_args['id']) . "'>\n";
    $value = $args['selected'];
    $dir = FLEXI_BASE_DIR . 'public/partials/layout/' . $parsed_args['folder'] . '/';
    $filelist = '';
    $files = array_map('htmlspecialchars', scandir($dir));
    // echo $dir;
    if (isset($parsed_args['show_option_none'])) {
        $output .= '<option value="' . $parsed_args['option_none_value'] . '" ' . selected($value, $value, false) . '>' . $parsed_args['show_option_none'] . '</option>';
    }

    foreach ($files as $file) {
        if (!strpos($file, '.') && '.' != $file && '..' != $file) {
            $output .= sprintf('<option value="%s" %s >%s layout</option>' . PHP_EOL, $file, selected($value, $file, false), $file);
        }
    }

    $output .= "</select>\n";

    return $output;
}

// Displays login link
function flexi_login_link()
{
    $output = '';
    $style_base_color = flexi_get_option('flexi_style_base_color', 'flexi_app_style_settings', '');
    $style_text_color = flexi_get_option('flexi_style_text_color', 'flexi_app_style_settings', '');

    $output .= "<div class='flexi_alert-box flexi_notice'>" . __('Login', 'flexi') . '</div>';
    $output .= "<div class='fl-box " . esc_attr($style_base_color) . ' ' . esc_attr($style_text_color) . "' style='padding:30px;'>";
    $args = array(
        'echo' => false,
        //'redirect' => flexi_get_button_url('', false, 'my_gallery', 'flexi_user_dashboard_settings'),
        'form_id' => 'loginform',
        'label_username' => __('Username', 'flexi'),
        'label_password' => __('Password', 'flexi'),
        'label_remember' => __('Remember Me', 'flexi'),
        'label_log_in' => __('Login', 'flexi'),
        'id_username' => 'user_login',
        'id_password' => 'user_pass',
        'id_remember' => 'rememberme',
        'id_submit' => 'wp-submit',
        'remember' => true,
        'value_username' => null,
        'value_remember' => false,
    );

    $output .= wp_login_form($args);
    do_action('flexi_login_form');
    $output .= '</div>';
    return $output;
}

// Get post button link
function flexi_get_button_url($param = '', $ajax = true, $type = 'submission_form', $setting_tab = 'flexi_form_settings')
{
    $url = '#';
    if ($ajax) {
        $url = '#admin-ajax.php?action=flexi_send_again&post_id=' . $param;
    } else {
        $default_post = flexi_get_option($type, $setting_tab, '0');

        if ('0' != $default_post && '' != $default_post) {
            $flexi_post = get_post($default_post);
            if ($flexi_post && 0 != $default_post) {
                if ('' == $param) {
                    $url = esc_url(get_page_link($default_post));
                } else {
                    $url = esc_url(add_query_arg('id', $param, get_page_link($default_post)));
                }
            } else {

                // flexi_missing_pages($type);
            }
        } else {
            // flexi_missing_pages($type);
            $url = '#';
        }
    }
    return esc_url($url);
}

// Default reference replaced by settings and attributes 
function flexi_default_args($params)
{
    $value = array(
        'class' => 'flexi_form_style',
        'title' => 'Submit',
        'detail_layout' => 'default',
        'edit_page' => '0',
        'name' => '',
        'id' => get_the_ID(),
        'taxonomy' => 'flexi_category',
        'tag_taxonomy' => 'flexi_tag',
        'upload_type' => 'flexi',
        'ajax' => 'true',
        'media_private' => 'false',
        'edit' => 'false',
        'type' => 'image',
        'unique' => '',
    );
    if (isset($_POST['user-submitted-title'])) {
        $value['user-submitted-title'] = sanitize_text_field($_POST['user-submitted-title']);
    } else {
        $value['user-submitted-title'] = '';
    }

    if (isset($_POST['user-submitted-url'])) {
        $value['user-submitted-url'] = sanitize_text_field($_POST['user-submitted-url']);
    } else {
        $value['user-submitted-url'] = '';
    }

    if (isset($_POST['user-submitted-content'])) {
        $content = flexi_sanitize_content($_POST['user-submitted-content']);
        $content = str_replace('[', '[[', $content);
        $content = str_replace(']', ']]', $content);
        $value['content'] = sanitize_textarea_field($content);
    } else {
        //Leave as content avaible by admin
        $value['content'] = 'web3yak';
    }

    if (isset($_POST['cat'])) {
        $value['category'] = intval($_POST['cat']);
        if (empty(intval($_POST['cat']))) {
            $value['category'] = flexi_get_option('global_album', 'flexi_categories_settings', '');
        }
    } else {
        $value['category'] = flexi_get_option('global_album', 'flexi_categories_settings', '');
    }

    if (isset($_POST['tags'])) {
        $value['tags'] = sanitize_text_field($_POST['tags']);
    } else {
        $value['tags'] = '';
    }

    if (isset($_POST['edit'])) {
        $value['edit'] = sanitize_text_field($_POST['edit']);
    }

    if (isset($_POST['detail_layout'])) {
        $value['detail_layout'] = sanitize_text_field($_POST['detail_layout']);
    }

    if (isset($_POST['unique'])) {
        $value['unique'] = sanitize_text_field($_POST['unique']);
    }

    if (isset($_POST['edit_page'])) {
        $value['edit_page'] = sanitize_text_field($_POST['edit_page']);
    }

    if (isset($_POST['type'])) {
        $value['type'] = sanitize_text_field($_POST['type']);
    }

    if (isset($_POST['flexi_id'])) {
        $value['flexi_id'] = sanitize_text_field($_POST['flexi_id']);
    } else {
        $value['flexi_id'] = '0';
    }

    return shortcode_atts($value, $params);
}

// Update/edit the post with reference of post ID
function flexi_update_post($post_id, $title, $files, $content, $category, $tags = '')
{
    $updatePost['error'][] = array(
        'id' => false,
        'error' => false,
    );
    $updatePost['error'][] = '';

    if (empty($title)) {
        $updatePost['error'][] = 'required-title';
    }

    $updatePost['error'][] = apply_filters('flexi_verify_submit', '');
    $file_count = 0;

    // If update title is disabled
    if (flexi_get_option('update_title', 'flexi_form_settings', 0) == 1) {
        // Automatically publish post as soon as user submit
        if (flexi_get_option('publish', 'flexi_form_settings', 1) == 1) {

            //web3yak is content means it will not update anything front frontend and leaving text entered at backend as it is.
            if ($content == 'web3yak') {
                $new_post = array(
                    'ID' => $post_id,
                    'post_status' => 'publish',
                );
            } else {
                $new_post = array(
                    'ID' => $post_id,
                    'post_content' => $content,
                    'post_status' => 'publish',
                );
            }
        } else {
            $new_post = array(
                'ID' => $post_id,
                'post_content' => $content,
                'post_status' => 'draft',
            );
        }
    } else {

        // Automatically publish post as soon as user submit
        if (flexi_get_option('publish', 'flexi_form_settings', 1) == 1) {
            $new_post = array(
                'ID' => $post_id,
                'post_title' => $title,
                'post_content' => $content,
                'post_status' => 'publish',
            );
        } else {
            $new_post = array(
                'ID' => $post_id,
                'post_title' => $title,
                'post_content' => $content,
                'post_status' => 'draft',
            );
        }
    }
    // Update the post into the database
    $pid = wp_update_post($new_post);
    if (is_wp_error($pid)) {
        return false;
    } else {
        // Update post meta fields
        for ($x = 1; $x <= 30; $x++) {

            // If edit field is disabled from settings
            $display = flexi_get_option('flexi_field_' . $x . '_display', 'flexi_custom_fields', '');
            $value = get_post_meta($post_id, 'flexi_field_' . $x, '');
            if (!$value) {
                $value[0] = '';
            }

            if (is_array($display)) {
                if (in_array('edit_disable', $display)) {
                    // restricted to update.
                    // flexi_log('Do not update ' . $x);
                } else {
                    //flexi_log('Update ' . $x);
                    if (isset($_POST['flexi_field_' . $x])) {
                        if (is_array($_POST['flexi_field_' . $x])) {

                            $data = $_POST['flexi_field_' . $x];
                            $data_value = array();
                            //  flexi_log('am here');
                            if (is_array($data)) {

                                //Get _value of same field
                                if (isset($_POST['flexi_field_' . $x . '_value'])) {
                                    $data_value = $_POST['flexi_field_' . $x . '_value'];
                                    // flexi_log($data_value);
                                }

                                // flexi_log($data);
                            }
                            $i = 0;
                            $result = array();
                            /*
                            while ($i < count($data)) {
                                $result[$i] = $data;
                                $result[$i] = $data_value;
                                $i++;
                            }
                            */
                            // flexi_log($data + $data_value);
                            //$result = array_merge($data, $data_value);
                            // $result = array_combine($data, $data_value);


                            $result = flexi_combine_arr($data, $data_value);
                            //flexi_log($result);

                            update_post_meta($post_id, 'flexi_field_' . $x, $result);
                        } else {
                            // flexi_log('am here but am not array');
                            update_post_meta($post_id, 'flexi_field_' . $x, sanitize_textarea_field($_POST['flexi_field_' . $x]));
                        }
                    } else {
                        update_post_meta($post_id, 'flexi_field_' . $x, '');
                    }
                }
            } else {
                // Ignore other fields not included in form
            }
        }

        // If update category is enabled
        if (flexi_get_option('update_cate', 'flexi_form_settings', 0) == 0) {
            // Set category
            if ('' != $category) {
                wp_set_object_terms($post_id, array($category), 'flexi_category');
            }
        }

        // If update tags is enabled
        if (flexi_get_option('update_tag', 'flexi_form_settings', 0) == 0) {
            // Set TAGS
            // if($tags!='')
            wp_set_object_terms($post_id, explode(',', $tags), 'flexi_tag');
            // flexi_log($tags . "---");
        }
        foreach ($updatePost['error'] as $e) {
            if (!empty($e)) {
                unset($updatePost['id']);
            }
        }

        return $updatePost;
    }
}

// Sanitize the $content.
function flexi_sanitize_content($content)
{
    $allowed_tags = wp_kses_allowed_html('post');
    return wp_kses(stripslashes($content), $allowed_tags);
}

// Required Flexi-PRO
function flexi_pro_required()
{
    return "<div class='flexi_alert-box flexi_warning'>" . __('Required Flexi-PRO', 'flexi') . '</div>';
}

// Return array of hidden category
function flexi_hidden_album()
{
    // if ( get_post_meta( term_id, 'flexi_show_cate', 1 ) )
    $skip = array();
    $categories = get_categories(
        array(
            'taxonomy' => 'flexi_category',
            'hide_empty' => 0,
        )
    );

    foreach ($categories as $category) {
        $flexi_hide_cate = get_term_meta($category->term_id, 'flexi_hide_cate', true);
        if ('on' == $flexi_hide_cate) {
            array_push($skip, $category->term_id);
        }
    }
    return $skip;
}

// Drop down list of albums
function flexi_droplist_album($taxonomy = 'flexi_category', $selected_album = '', $skip = array(), $parent = '')
{
    if (empty($skip)) {
        $skip = flexi_hidden_album();
    }

    if (0 == $parent) {
        $parent = '';
    }

    $dropdown_args = array(

        'selected' => esc_attr($selected_album),
        'name' => 'cat',
        'id' => '',
        'echo' => 1,
        'orderby' => 'name',
        'order' => 'ASC',
        'show_count' => 0,
        'hierarchical' => 1,
        'taxonomy' => esc_attr($taxonomy),
        'value_field' => 'term_id',
        'hide_empty' => 0,
        'exclude' => $skip,
        'child_of' => $parent,
        'show_option_none' => '-- ' . __('None', 'flexi') . ' --',
        'option_none_value' => '',
    );

    wp_dropdown_categories($dropdown_args);

    // var_dump($dropdown_args);
}

// Drop down list of albums
function flexi_droplist_tag($taxonomy = 'flexi_tag', $selected_tag = '', $skip = array(), $parent = '')
{
    if (empty($skip)) {
        $skip = flexi_hidden_album();
    }

    if (0 == $parent) {
        $parent = '';
    }

    $dropdown_args = array(

        'selected' => esc_attr($selected_tag),
        'name' => 'tags',
        'id' => '',
        'echo' => 1,
        'orderby' => 'name',
        'order' => 'ASC',
        'show_count' => 0,
        'hierarchical' => 1,
        'taxonomy' => esc_attr($taxonomy),
        'value_field' => 'slug',
        'hide_empty' => 0,
        'exclude' => $skip,
        'child_of' => $parent,
        'show_option_none' => '-- ' . __('None', 'flexi') . ' --',
        'option_none_value' => '',
    );

    wp_dropdown_categories($dropdown_args);

    // var_dump($dropdown_args);
}

// log_me('This is a message for debugging purposes. works if debug is enabled.');
function flexi_log($message)
{
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }

        error_log('------------------------------------------');
    }
}

// Returns author ID of logged in user. If not returns the id of default user in UPG settings.
function flexi_get_author()
{
    if (is_user_logged_in()) {
        $author_id = get_current_user_id();
    } else {
        $the_user = get_user_by('login', flexi_get_option('default_user', 'flexi_form_settings', '0'));
        if (!empty($the_user)) {
            $author_id = $the_user->ID;
        } else {
            $author_id = 0;
        }
    }

    return $author_id;
}

// Check if current page equals to selected page
function is_flexi_page($field_name, $section_name)
{
    // $current_page_id = get_the_ID();
    $current_page_id = get_queried_object_id();
    $test_page_id = flexi_get_option($field_name, $section_name, 0);

    if ($current_page_id == $test_page_id) {
        return true;
    }
    return false;
}

// Check If Flexi-PRO
function is_flexi_pro()
{
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    $a = get_option('FLEXI_PRO', 'FAIL');
    if (is_plugin_active('flexi-pro/flexi-pro.php') && 'FAIL' != $a && defined('FLEXI_PRO_VERSION')) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get default plugin settings.
 *
 * @since  1.0.0
 * @return array $defaults Array of plugin settings.
 */
function flexi_get_default_settings()
{
    // Update values for old Flexi versions
    /*
    $old_version = get_option('flexi_version');
    if ('1.0.600' == $old_version) {
    //execute if update required
    }
     */

    // Dynamic Language translate common terms
    __('Insert tag', 'flexi');
    __('Tags', 'flexi');
    __('Description', 'flexi');
    __('Select file', 'flexi');

    // update flexi version to current one
    update_option('flexi_version', FLEXI_VERSION);

    // Lightbox Enabled
    // flexi_set_option('lightbox_switch', 'flexi_detail_settings', 1);
    return;
}

/**
 * Get the value of a settings field
 *
 * @param string $field_name settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function flexi_get_option($field_name, $section = 'flexi_detail_settings', $default = '')
{
    // Example
    // flexi_get_option('field_name', 'setting_name', 'default_value');

    $options = (array) get_option($section);

    if (isset($options[$field_name])) {
        return $options[$field_name];
    } else {
        // Set the default value if not found
        flexi_set_option($field_name, $section, $default);
    }

    return $default;
}

// Set options in settings
function flexi_set_option($field_name, $section = 'flexi_general_settings', $default = '')
{
    // Example
    // flexi_set_option('field_name', 'setting_name', 'default_value');
    $options = (array) get_option($section);
    $options[$field_name] = $default;
    update_option($section, $options);

    return;
}

function flexi_missing_pages()
{
    $flexi_pages_created = get_option('flexi_pages_created');

    if ($flexi_pages_created != 'created') {
        // flexi_log("checking missing pages =" . $flexi_pages_created);

        $colors = array('category_page', 'primary_page', 'submission_form', 'my_gallery', 'edit_flexi_page');

        foreach ($colors as $value) {
            // flexi_log($value);
            flexi_missing_page_create($value);
        }
        add_option('flexi_pages_created', 'created');
    }

    // flexi_log("checking missing pages =" . $flexi_pages_created);
}

function flexi_missing_page_create($lost_file)
{
    if ($lost_file == 'category_page') {
        // flexi_log('create file: ' . $lost_file);
        $cat_id = wp_insert_post(
            array(
                'post_title' => 'Flexi Category',
                'post_content' => '[flexi-category parent="" padding="10"]',
                'post_type' => 'page',
                'post_status' => 'publish',
            )
        );
        flexi_set_option('category_page', 'flexi_categories_settings', $cat_id);
    } elseif ($lost_file == 'primary_page') {
        // flexi_log('create file: ' . $lost_file);
        $aid = wp_insert_post(
            array(
                'post_title' => 'Primary Gallery',
                'post_content' => '[flexi-common-toolbar] [flexi-primary]',
                'post_type' => 'page',
                'post_status' => 'publish',
            )
        );
        flexi_set_option('primary_page', 'flexi_image_layout_settings', $aid);
    } elseif ($lost_file == 'submission_form') {
        $str_post_image = '
        [flexi-common-toolbar]
        [flexi-form class="flexi_form_style" title="Submit to Flexi" name="my_form" ajax="true"][flexi-form-tag type="post_title" class="fl-input" title="Title" value="" required="true"][flexi-form-tag type="category" title="Select category"][flexi-form-tag type="tag" title="Insert tag"][flexi-form-tag type="article" class="fl-textarea" title="Description" ][flexi-form-tag type="file" title="Select file" required="true"][flexi-form-tag type="submit" name="submit" value="Submit Now"]
        [/flexi-form]
        ';

        $bid = wp_insert_post(
            array(
                'post_title' => 'Post Image',
                'post_content' => $str_post_image,
                'post_type' => 'page',
                'post_status' => 'publish',
            )
        );
        flexi_set_option('submission_form', 'flexi_form_settings', $bid);
    } elseif ($lost_file == 'my_gallery') {
        $did = wp_insert_post(
            array(
                'post_title' => 'User Dashboard',
                'post_content' => '[flexi-common-toolbar] [flexi-user-dashboard]',
                'post_type' => 'page',
                'post_status' => 'publish',
            )
        );
        flexi_set_option('my_gallery', 'flexi_user_dashboard_settings', $did);
    } elseif ($lost_file == 'edit_flexi_page') {

        $str_edit_image = '
        [flexi-common-toolbar]
        [flexi-standalone edit="true"]
        [flexi-form class="flexi_form_style" title="Update Flexi" name="my_form" ajax="true" edit="true"]
        [flexi-form-tag type="post_title" class="fl-input" title="Title" edit="true" required="true"]
        [flexi-form-tag type="category" title="Select category" edit="true"]
        [flexi-form-tag type="tag" title="Insert tag" edit="true"]
        [flexi-form-tag type="article" class="fl-textarea" title="Description" placeholder="Content" edit="true"]
        [flexi-form-tag type="submit" name="submit" value="Update Now"]
        [/flexi-form]
            ';

        $eid = wp_insert_post(
            array(
                'post_title' => 'Edit Flexi Post',
                'post_content' => $str_edit_image,
                'post_type' => 'page',
                'post_status' => 'publish',
            )
        );
        flexi_set_option('edit_flexi_page', 'flexi_form_settings', $eid);
    } else {
        // flexi_log("no file");
    }
}

// Create all required pages
function flexi_create_pages()
{
    global $wpdb;

    // Set default image sizes
    flexi_set_option('t_width', 'flexi_media_settings', 150);
    flexi_set_option('t_height', 'flexi_media_settings', 150);
    flexi_set_option('m_width', 'flexi_media_settings', 300);
    flexi_set_option('m_height', 'flexi_media_settings', 300);
    flexi_set_option('l_width', 'flexi_media_settings', 600);
    flexi_set_option('l_height', 'flexi_media_settings', 400);

    if (!$wpdb->get_var("select id from {$wpdb->prefix}posts where post_content like '%[flexi-%'")) {

        // Assign category page
        flexi_missing_pages();
        // flexi_missing_pages('category_page');
        // flexi_missing_pages('primary_page');
        // flexi_missing_pages('submission_form');
        // flexi_missing_pages('my_gallery');
        // flexi_missing_pages('edit_flexi_page');

        add_option('flexi_pages_created', true);
    }
}

// Flexi Excerpt Function ;)
function flexi_excerpt($limit = null, $separator = null, $post = null)
{
    if (null == $post) {
        global $post;
    }

    // Set standard words limit
    if (is_null($limit)) {
        $limit = $gallery_layout = flexi_get_option('excerpt_length', 'flexi_gallery_appearance_settings', '5');
        $excerpt = explode(' ', get_the_excerpt($post), $limit);
    } else {
        $excerpt = explode(' ', get_the_excerpt($post), $limit);
    }

    // Set standard separator
    if (is_null($separator)) {
        $separator = ' ...';
    }

    // Excerpt Generator
    if (count($excerpt) >= $limit) {
        array_pop($excerpt);
        $excerpt = implode(' ', $excerpt) . $separator;
    } else {
        $excerpt = implode(' ', $excerpt);
    }
    $excerpt = preg_replace('`\[[^\]]*\]`', '', $excerpt);
    return $excerpt;
}

// Add on grid
function flexi_show_addon_gallery($evalue, $id, $layout)
{
    // 0-div_class,
    $group = array();
    $list = '';

    if (has_filter('flexi_addon_gallery_' . $layout)) {
        $group = apply_filters('flexi_addon_gallery_' . $layout, $group, $evalue, $id, $layout);
    }

    if (count($group) > 0) {
        $list .= '<div class="' . $group[0][0] . '">';
    }

    for ($r = 0; $r < count($group); $r++) {
        if (isset($group[$r][1])) {
            $list .= $group[$r][1];
        }
    }

    if (count($group) > 0) {
        $list .= '</div>';
    }
    return $list;
}

// Icon container. Eg. Author icon, Delete icon, Edit icon
function flexi_show_icon_grid()
{
    $icon = array();

    $list = '';

    if (has_filter('flexi_add_icon_grid')) {
        $icon = apply_filters('flexi_add_icon_grid', $icon);
    }

    if (count($icon) > 0) {
        $list .= '<div class="fl-field fl-buttons" id="flexi_' . get_the_ID() . '">';
    }

    for ($r = 0; $r < count($icon); $r++) {
        $nonce = wp_create_nonce($icon[$r][3]);

        if (!isset($icon[$r][5])) {
            $icon[$r][5] = '';
        }

        if (!isset($icon[$r][6])) {
            $icon[$r][6] = '';
        }
        // 0-icon,1-title,2-url,3-argument or nonce,4-5-class_a,6-parameter
        if ('' != $icon[$r][0]) {
            $style_css = flexi_get_option('flexi_style_icon_grid', 'flexi_app_style_settings', $icon[$r][5]);

            $list .= '<a href="' . esc_url($icon[$r][2]) . '" class="fl-button ' . esc_attr($style_css) . '" ' . esc_attr($icon[$r][6]) . '="' . esc_attr($icon[$r][3]) . '" data-nonce="' . $nonce . '" data-post_id="' . esc_attr($icon[$r][4]) . '" title="' . esc_attr($icon[$r][1]) . '"><span class="fl-icon fl-is-small"><i class="' . esc_attr($icon[$r][0]) . '"></i></span></a>';
        }
    }
    if (count($icon) > 0) {
        $list .= '</div>';
    }
    return $list;
}

// Flexi activated
function flexi_install_complete()
{
    $flexi_activated = get_option('flexi_activated');
    if ($flexi_activated) {
        if (get_option('flexi_activated', false)) {
            delete_option('flexi_activated');
            delete_option('flexi_pages_created');
        }
    }
}

// button toolbar to display after user submit post
function flexi_post_toolbar_grid($id, $bool)
{
    global $post;
    $icon = array();

    $list = '';

    if (has_filter('flexi_submit_toolbar')) {
        $icon = apply_filters('flexi_submit_toolbar', $icon, $id, $bool);
    }

    if (count($icon) > 0) {
        $list .= '<div class="fl-buttons flexi_post_toolbar_group" role="toolbar" id="flexi_toolbar_' . esc_attr($id) . '">';
    }

    for ($r = 0; $r < count($icon); $r++) {

        if ('' != $icon[$r][0]) {
            $list .= '<a href="' . esc_url($icon[$r][2]) . '" class="' . esc_attr($icon[$r][4]) . '">
                        <span class="fl-icon">
                             <i class="' . esc_attr($icon[$r][0]) . '"></i>
                        </span>
                        <span>' . esc_attr($icon[$r][1]) . '</span>
                    </a>';
        }
    }
    if (count($icon) > 0) {
        $list .= '</div>';
    }
    return $list;
}

// Error Code
function flexi_error_code($err)
{
    $msg = '';
    if ('required-title' == $err) {
        $msg .= __('Title cannot be blank.', 'flexi');
    } elseif ('file-type' == $err) {
        $msg .= __('Invalid file type', 'flexi');
    } elseif ('exif_imagetype' == $err) {
        $msg .= __('Only image files are allowed.', 'flexi');
    } elseif ('max_size' == $err) {
        $msg .= __('File size is too big.', 'flexi');
    } elseif ('invalid-captcha' == $err) {
        $msg .= __('Captcha security code is invalid.', 'flexi');
    } else {
        $msg .= $err;
    }
    // $msg .= "</div>" . flexi_post_toolbar_grid('', true);
    // $msg .= "</div>";
    return esc_attr($msg);
}
/**
 * Adding your own post state (label)
 * Example: Checking if this is a Product and if it's on Sale
 *          Show the users all WC products on sale
 *
 * @param array   $states Array of all registered states.
 * @param WP_Post $post   Post object that we can use.
 */
function flexi_page_post_state_label($states, $post)
{
    $primary_page = flexi_get_option('primary_page', 'flexi_image_layout_settings', 0);
    if (0 != $primary_page) {
        if ($primary_page == $post->ID) {
            $states['flexi-primary'] = __('Flexi', 'flexi') . ' ' . __('Primary Gallery', 'flexi');
        }
    }

    $my_gallery = flexi_get_option('my_gallery', 'flexi_user_dashboard_settings', 0);
    if (0 != $my_gallery) {
        if ($my_gallery == $post->ID) {
            $states['flexi-user-dashboard'] = __('Flexi', 'flexi') . ' ' . __('User Dashboard', 'flexi');
        }
    }

    $submission_form = flexi_get_option('submission_form', 'flexi_form_settings', 0);
    if (0 != $submission_form) {
        if ($submission_form == $post->ID) {
            $states['flexi-submission-form'] = __('Flexi', 'flexi') . ' ' . __('Submission Form', 'flexi');
        }
    }

    $category_page = flexi_get_option('category_page', 'flexi_categories_settings', 0);
    if (0 != $category_page) {
        if ($category_page == $post->ID) {
            $states['flexi-edit'] = __('Flexi', 'flexi') . ' ' . __('Category', 'flexi');
        }
    }

    $edit_flexi_page = flexi_get_option('edit_flexi_page', 'flexi_form_settings', 0);
    if (0 != $edit_flexi_page) {
        if ($edit_flexi_page == $post->ID) {
            $states['flexi-edit'] = __('Flexi', 'flexi') . ' ' . __('Edit/Modify', 'flexi');
        }
    }

    return $states;
}
add_filter('display_post_states', 'flexi_page_post_state_label', 20, 2);

// Shortcode evalue set into array
function flexi_evalue_setarray($evalue)
{
    $main = array();

    $values = explode(',', $evalue);
    foreach ($values as $option) {
        $cap = explode(':', $option);
        if (isset($cap[1])) {
            if ('' != $cap[0]) {
                $main[$cap[0]] = trim($cap[1]);
            }
        }
    }
    return $main;
}

// Determine to show div element
function flexi_evalue_toggle($key, $evalue)
{
    $extra_param = flexi_evalue_setarray($evalue);
    // flexi_log($extra_param);
    if (isset($extra_param[$key]) && 'on' == $extra_param[$key]) {
        return '';
    } else {
        return 'display:none';
    }
}

// Get parameter for php function with parameters
// 0=function, 1-parameter1 (label), 2-parameter2 3-parameter3, 4-parameter4
function flexi_php_field_value($php_field, $index = 0)
{
    // Add label as function into array
    $field_param = array();

    $values = explode(',', $php_field);
    foreach ($values as $option) {
        $cap = explode(':', $option);
        if (isset($cap[$index])) {
            array_push($field_param, trim($cap[$index]));
        }
    }
    return $field_param;
}

// PHP field execute function sent in parameter
function flexi_php_field_execute($func_name, $param_1, $param_2, $param_3)
{
    $func_name = trim($func_name);
    if ($func_name != '') {
        if (function_exists($func_name)) {
            return $func_name($param_1, $param_2, $param_3);
        } else {
            return $func_name . "('" . $param_1 . "','" . $param_2 . ",'" . $param_3 . "') is invalid PHP function";
        }
    }

    return $func_name;
}

// Get parameter value from long string
function flexi_get_param_value($key, $search)
{
    $extra_param = flexi_evalue_setarray($search);
    // flexi_log($extra_param);
    if (isset($extra_param[$key])) {
        return $extra_param[$key];
    } else {
        return '';
    }
}

// Get error code while submitting form
function flexi_get_error($result)
{
    $notice = '';
    if (isset($result['notice'][0])) {
        $reindex_array = array_values(array_filter($result['error']));
        $notice_array = array_values(array_filter($result['notice']));
        for ($x = 0; $x < count($reindex_array); $x++) {
            // $err .= $reindex_array[$x] . "  ";
            $notice .= "<div class='flexi_alert-box flexi_error'>" . flexi_error_code($reindex_array[$x]) . ' : ' . esc_attr($notice_array[$x]) . '</div>';
        }
    }
    return $notice;
}

// Display gallery & form to specific page only
function flexi_execute_shortcode()
{
    $boo = false;
    if (is_archive() || is_singular() || is_home() || (defined('REST_REQUEST') && REST_REQUEST)) {
        $boo = true;
    }

    if (did_action('elementor/loaded')) {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $boo = true;
        }
    }
    return $boo;
}

// Assign default parameters (widgets)
function flexi_set_value($key, $value, $instance)
{
    if (!isset($instance[$key])) {
        $output = $value;
    } else {
        $output = $instance[$key];
    }
    return sanitize_text_field($output);
}

// Count album category

function flexi_total_cat_post_count($id, $old_count)
{
    $count = 0;
    $taxonomy = 'flexi_category';
    $args = array(
        'child_of' => $id,
        'pad_counts' => 1,
    );
    $tax_terms = get_terms($taxonomy, $args);
    foreach ($tax_terms as $tax_term) {
        $count += $tax_term->count;
    }
    if ($count == 0) {
        return $old_count;
    }
    return absint($count);
}
