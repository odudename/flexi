<?php
// Submits new post.
// $title = Title of post
// $files = files selected
// $content= Description
// category =Album name
// $detail_layout = layout name for post detail page.
function flexi_submit($title, $files, $content, $category, $detail_layout, $tags = '', $edit_page = '0', $unique = '')
{
    $tags         = strtolower($tags);
    $post_type    = 'flexi';
    $taxonomy     = 'flexi_category';
    $tag_taxonomy = 'flexi_tag';
    $newPost      = array();
    $newPost      = array(
        'id'     => false,
        'error'  => false,
        'notice' => false,
    );
    if (has_filter('flexi_verify_submit')) {
        $newPost = apply_filters('flexi_verify_submit', $newPost);
    }
    $newPost['error'][] = '';
    $file_count         = 0;
    if (empty($title)) {
        $newPost['error'][] = 'required-title';
    }

    // if (empty($content))  $newPost['error'][] = 'required-description';
    // $newPost['error'][] = apply_filters('flexi_verify_submit', "");

    if (isset($files['tmp_name'][0])) {
        $check_file_exist = $files['tmp_name'][0];
    } else {
        $check_file_exist = '';
    }


    $file_count = flexi_upload_get_file_count($files);


    if ($unique != '') {
        // Checking duplicate or unique entry based on for parameter

        if ($unique == 'post_title') {
            $args = array(
                'post_type' => 'flexi',
                'title'     => sanitize_text_field($_POST['user-submitted-title']),
            );
        } else {
            $args = array(
                'post_type'  => 'flexi',
                'meta_key'   => $unique,
                'meta_value' => sanitize_text_field($_POST[$unique]),
            );
        }

        $wp_query = new WP_Query($args);
        if ($wp_query->have_posts()) {
            // do not insert
            $newPost['error'][] = 'duplicate-entry';
        }
        wp_reset_query();
    }

    foreach ($newPost['error'] as $e) {

        if (!empty($e)) {
            // flexi_log("Error: " . $e);
            unset($newPost['id']);
            return $newPost;
        }
    }

    $postData = flexi_prepare_post($title, $content, $post_type);
    do_action('flexi_insert_before', $postData);
    // Include important files required during upload
    flexi_include_deps();
    $i = 0;
    if (0 == $file_count) {
        // Execute loop at least once
        $file_count = 1;
    }

    for ($x = 1; $x <= $file_count; $x++) {

        $newPost['id'] = wp_insert_post($postData);
        if ($newPost['id']) {
            // echo "Successfully added $x <hr>";
            $post_id = $newPost['id'];
            // Submit extra fields data
            for ($z = 1; $z <= 30; $z++) {
                if (isset($_POST['flexi_field_' . $z])) {
                    add_post_meta($post_id, 'flexi_field_' . $z, sanitize_textarea_field($_POST['flexi_field_' . $z]));
                }
            }
            // Ended to submit extra fields

            if ('' != $category) {
                wp_set_object_terms($post_id, array($category), $taxonomy);
            }

            if (taxonomy_exists($tag_taxonomy)) {
                // Set TAGS
                if ('' != $tags) {
                    wp_set_object_terms($post_id, explode(',', $tags), $tag_taxonomy);
                }
            }

            // Assign detail_layout
            add_post_meta($post_id, 'flexi_layout', $detail_layout);


            // Assign edit_page
            add_post_meta($post_id, 'flexi_new_edit_page', $edit_page);

            $attach_ids = array();
            // Execute only if files is available
            if ($files && !empty($check_file_exist)) {
                $key = apply_filters('flexi_file_key', 'user-submitted-image-{$i}');

                $_FILES[$key]             = array();
                $_FILES[$key]['name']     = $files['name'][$i];
                $_FILES[$key]['tmp_name'] = $files['tmp_name'][$i];
                $_FILES[$key]['type']     = $files['type'][$i];
                $_FILES[$key]['error']    = $files['error'][$i];
                $_FILES[$key]['size']     = $files['size'][$i];

                // Check the file before processing
                $file_data = flexi_check_file($_FILES[$key]);
                // $newPost['error'] = array_unique(array_merge($file_data['error'], $newPost['error']));

                // flexi_log($file_data);

                $attach_id = media_handle_upload($key, $post_id);

                // wp_attachment_is('image', $post)
                // if (!is_wp_error($attach_id) && wp_attachment_is_image($attach_id)) {

                if (!is_wp_error($attach_id) & ('' == trim($file_data['error'][0]))) {

                    $attach_ids[] = $attach_id;

                    // Attach ID of the post where it is submitted
                    if (isset($_POST['flexi_attach_at'])) {
                        add_post_meta($post_id, 'flexi_attach_at', sanitize_text_field($_POST['flexi_attach_at']));
                    }

                    if (wp_attachment_is('image', $attach_id)) {
                        add_post_meta($post_id, 'flexi_type', 'image');
                        add_post_meta($post_id, 'flexi_image_id', $attach_id);
                        add_post_meta($post_id, 'flexi_image', wp_get_attachment_url($attach_id));
                    } elseif (wp_attachment_is('video', $attach_id)) {
                        add_post_meta($post_id, 'flexi_type', 'video');
                        add_post_meta($post_id, 'flexi_file_id', $attach_id);
                        add_post_meta($post_id, 'flexi_file', wp_get_attachment_url($attach_id));
                    } elseif (wp_attachment_is('audio', $attach_id)) {
                        add_post_meta($post_id, 'flexi_type', 'audio');
                        add_post_meta($post_id, 'flexi_file_id', $attach_id);
                        add_post_meta($post_id, 'flexi_file', wp_get_attachment_url($attach_id));
                    } else {
                        add_post_meta($post_id, 'flexi_type', 'other');
                        add_post_meta($post_id, 'flexi_file_id', $attach_id);
                        add_post_meta($post_id, 'flexi_file', wp_get_attachment_url($attach_id));
                    }
                } else {
                    if (!is_wp_error($attach_id)) {
                        // Delete attachment if uploaded
                        wp_delete_attachment($attach_id);
                    }
                    // Delete post if error found
                    wp_delete_post($post_id, true);
                    $newPost['error'][]  = $file_data['error'][0];
                    $newPost['notice'][] = sanitize_file_name($_FILES[$key]['name']);

                    // unset($newPost['id']);
                    // return $newPost;
                }

                $i++;
            }
        } else {
            $newPost['error'][] = 'post-fail';
        }
    }
    // flexi_log('all finished');
    // flexi_log($newPost);
    do_action('flexi_insert_after', $newPost);
    return $newPost;
}

function flexi_upload_get_file_count($files)
{
    $temp = false;
    if (isset($files['tmp_name'])) {
        $temp = array_filter($files['tmp_name']);
    }

    $file_count = 0;
    if (!empty($temp)) {
        foreach ($temp as $key => $value) {
            if (is_uploaded_file($value)) {
                $file_count++;
            }
        }
    }
    return $file_count;
}

// Check the file before upload or processing
function flexi_check_file($files)
{
    $enable_mime        = flexi_get_option('enable_mime_type', 'flexi_extension', 0);
    $error              = array();
    $error[0]           = '';
    $notice             = array();
    $notice[0]          = '';
    $uploaded_file_type = $files['type'];
    if ('0' == $enable_mime) {
        $allowed_file_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');
    } else {
        $allowed            = flexi_get_option('flexi_mime_type_list', 'flexi_mime_type', '');
        $allowed_file_types = array();
        if (is_array($allowed)) {

            $allowed_file_types = $allowed;
        }
        // Merge comma separated mime type into array
        $allowed_textarea = flexi_get_option('flexi_extra_mime', 'flexi_mime_type', '');
        if ('' != $allowed_textarea && is_flexi_pro()) {
            $str_arr            = explode(',', $allowed_textarea);
            $allowed_file_types = array_merge($str_arr, $allowed_file_types);
        }
        // flexi_log($allowed_file_types);

    }
    if (in_array($uploaded_file_type, $allowed_file_types)) {
        $error[0]  = '';
        $notice[0] = '';
    } else {
        $error[0]  = 'file-type';
        $notice[0] = $files['name'] . ' invalid file';
    }

    // Check file size allowed
    $allowed_file_size  = (int) flexi_get_option('upload_file_size', 'flexi_form_settings', 5);
    $uploaded_file_size = (int) (($files['size'] / 1024) / 1024);
    // flexi_log($uploaded_file_size . '>=' . $allowed_file_size);
    if ($uploaded_file_size >= $allowed_file_size) {
        $error[0]  = 'max_size';
        $notice[0] = $files['size'] . ' large size';
        // flexi_log("error recorded");
    }

    $file_data = array(
        'error'  => $error,
        'notice' => $notice,
    );

    return $file_data;
}

// During image upload process, it check the file is valid image type.
function flexi_check_images($files)
{
    $temp = false;
    $errr  = false;
    $error = array();

    if (isset($files['tmp_name'])) {
        $temp = array_filter($files['tmp_name']);
    }

    if (isset($files['error'])) {
        $errr = array_filter($files['error']);
    }

    $file_count = 0;
    if (!empty($temp)) {
        foreach ($temp as $key => $value) {
            if (is_uploaded_file($value)) {
                $file_count++;
            }
        }
    }
    if (true) {

        $i = 0;
    } else {
        $files = false;
    }
    $file_data = array(
        'error'      => $error,
        'file_count' => $file_count,
    );

    // error_log("file count ".$file_count);

    return $file_data;
}

// Before posting, assigning required metadata to the post.
function flexi_prepare_post($title, $content, $post_type = 'flexi')
{
    $postData                = array();
    $postData['post_title']   = $title;
    $postData['post_content'] = $content;
    $postData['post_author']  = flexi_get_author();
    $postData['post_type']    = 'flexi';

    if (flexi_get_option('publish', 'flexi_form_settings', 1) == 1) {
        $postData['post_status'] = 'publish';
    }

    return apply_filters('flexi_post_data', $postData);
}

// Including the files used during the time of file upload. It is required to get default WordPress file handling.
function flexi_include_deps()
{
    if (!function_exists('media_handle_upload')) {
        require_once ABSPATH . '/wp-admin/includes/media.php';
        require_once ABSPATH . '/wp-admin/includes/file.php';
        require_once ABSPATH . '/wp-admin/includes/image.php';
    }
}

// Generate javascript while uploading file
// $id= id of the submit button
function flexi_javascript_file_upload($id = 'flexi_submit_notice', $button_id = 'flexi_submit_button')
{
    $allowed_file_size     = (int) (flexi_get_option('upload_file_size', 'flexi_form_settings', 5));
    $allowed_file_size_byte = ($allowed_file_size * 1024) * 1024;

    ob_start();
?>
<script>
jQuery(document).ready(function() {
    var error = false;
    jQuery("form input").change(function(e) {
        if (this.files && this.files.length) {
            var file_size = this.files[0].size;
            // console.log(this.files);
            var max = <?php echo esc_attr($allowed_file_size_byte); ?>;
            jQuery("form p").text(this.files.length + " file(s) selected");
            var notices = "";
            for (i = 0; i < this.files.length; i++) {
                error = false;
                document.getElementById("<?php echo esc_attr($id); ?>").innerHTML = "";
                var cur = this.files[i].size;
                var name = this.files[i].name;
                //console.log(cur+"--"+max);
                if (cur > max) {
                    //console.log(e);
                    error = true;
                    //console.log(name+" - "+cur);
                    notices += "<div class=\"flexi_alert-box flexi_error\">" + name + " - " + ((cur /
                            1024) / 1024).toFixed(2) +
                        "MB <?php echo '(' . $allowed_file_size . 'MB)'; ?></div>";

                }
            }
            document.getElementById("<?php echo esc_attr($id); ?>").innerHTML = notices;
        }
        if (error) {
            //console.log("Disable Form");
            //Disable Submit Button
            jQuery("#<?php echo esc_attr($button_id); ?>").attr("disabled", true);
        } else {
            //console.log("Enable Form");
            //Disable Submit Button
            jQuery("#<?php echo esc_attr($button_id); ?>").removeAttr("disabled");
        }
    });

});
</script>

<?php

    return ob_get_clean();
}