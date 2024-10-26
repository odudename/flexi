<?php

/**
 * Display & process form submission with help of shortcode [flexi-form] & [flexi-form-tag]
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes
 */
class Flexi_Shortcode_Form
{
    public function __construct()
    {

        //Shortcode [flexi-form] to display form
        add_shortcode('flexi-form', array($this, 'render_form'));
        //Shortcode [flexi-tag] to render to tags
        add_shortcode('flexi-form-tag', array($this, 'render_tags'));

        //Add icon after form submitted to view post
        add_filter("flexi_submit_toolbar", array($this, 'flexi_add_icon_view_post_toolbar'), 10, 3);
        //Add icon after form submitted to post new
        add_filter("flexi_submit_toolbar", array($this, 'flexi_add_icon_submit_toolbar'), 10, 3);
    }

    public function render_form($params, $content = null)
    {
        $attr = flexi_default_args($params);
        $abc = "";
        ob_start();

        // flexi_log($params);

        //Attach form of specific post and add a gallery with it
        if (isset($params['attach']) && "true" == $params['attach']) {
            echo do_shortcode("[flexi-gallery attach='true']");
        }

        $check_enable_form = flexi_get_option('enable_form', 'flexi_form_settings', 'everyone');

        $enable_form_access = true;

        if ('everyone' == $check_enable_form) {
            $enable_form_access = true;
        } else if ('member' == $check_enable_form) {
            if (!is_user_logged_in()) {
                $enable_form_access = false;
                echo flexi_login_link();
            }
        } else if ('publish_posts' == $check_enable_form) {
            if (!is_user_logged_in()) {
                $enable_form_access = false;
                echo "<div class='flexi_alert-box flexi_error'>" . __('Disabled', 'flexi') . "</div>";
            } else {
                if (current_user_can('publish_posts')) {
                    echo "<div class='flexi_alert-box flexi_notice'>" . __('Publicly accessible disabled', 'flexi') . "</div>";
                    $enable_form_access = true;
                } else {
                    $enable_form_access = false;
                    echo "<div class='flexi_alert-box flexi_warning'>" . __('You do not have proper rights', 'flexi') . "</div>";
                }
            }
        } else {
            $enable_form_access = false;
            echo "<div class='flexi_alert-box flexi_error'>" . __('Disabled', 'flexi') . "</div>";
        }

        $edit_post = true;
        if (isset($_REQUEST["id"])) {
            $edit_post = flexi_check_rights(sanitize_text_field($_REQUEST["id"]));
            //Prevent form update if not Flexi-PRO
            if (!is_flexi_pro()) {
                $edit_post = false;
                echo flexi_pro_required();
            }
        }

        //Prevent from modification if wrong edit page & unauthorized access
        if (false == $edit_post) {
            echo "<div class='flexi_alert-box flexi_warning'>" . __('No permission to modify or update', 'flexi') . "</div>";
        }

        if ($enable_form_access && $edit_post) {
            if (isset($_POST['flexi-nonce']) && wp_verify_nonce($_POST['flexi-nonce'], 'flexi-nonce')) {
                //Check if edit form has parameter edit=true as input hidden field
                if ("false" == $_POST['edit']) {
                    $this->process_new_forms($attr);
                } else {
                    $this->process_update_forms($attr);
                }
            } else {

                //Attach form of specific post
                if (isset($params['attach']) && "true" == $params['attach']) {

?>
<script>
jQuery(document).ready(function() {
    jQuery("#flexi_form").slideUp();
});
</script>
<a id="flexi_attach_form_link" href="#admin-ajax.php?action=flexi_send_again&amp;post_id=1683"
    class="fl-button flexi_send_again"><span class="dashicons dashicons-plus"></span> New</a>

<?php
                }

                if ('false' == $attr['ajax']) {

                    echo '<div id="flexi_form"><form class="' . esc_attr($attr['class']) . '" method="post" enctype="multipart/form-data" action="">';
                } else {
                    //Submission processed at flexi_ajax_post.php
                ?>

<div id="flexi_ajax">

    <!-- Image loader -->
    <div id='flexi_loader' style='display: none;'>

        <br>
        <?php echo __("Uploading", "flexi"); ?>
        <div class="flexi_progress-bar">
            <span id="flexi_progress" class="flexi_progress-bar-load" style="width: 0%;text-align: center;"></span>
        </div>
        <br>
        <?php echo __("Processing", "flexi"); ?>
        <div class="flexi_progress-bar">
            <span id="flexi_progress_process" class="flexi_progress-bar-process"
                style="width: 0%;text-align: center;"></span>
        </div>

    </div>

    <div class='flexi_response'>

    </div>
    <div id="flexi_after_response" style='display: none;'>

    </div>

</div>

<?php
                    echo '<div id="flexi_form">
<form
id="flexi-request-form"
class="flexi_ajax_post ' . esc_attr($attr['class']) . '"
method="post"
enctype="multipart/form-data"
action="' . admin_url("admin-ajax.php") . '"
>';
                }

                if (trim($content) == "") {
                    $default = ' [flexi-form-tag type="post_title" class="fl-input" title="Title"]
     [flexi-form-tag type="file" title="Select file" required="true"]
         [flexi-form-tag type="submit" name="submit" value="Submit Now"]';
                    echo do_shortcode($default);
                } else {
                    echo do_shortcode($content);
                }
                //flexi_log($content);

                wp_nonce_field('flexi-nonce', 'flexi-nonce', false);

                echo '<input type="hidden" name="action" value="flexi_ajax_post">';
                echo '<input type="hidden" name="detail_layout" value="' . esc_attr($attr['detail_layout']) . '">';
                echo '<input type="hidden" name="edit_page" value="' . esc_attr($attr['edit_page']) . '">';
                echo '<input type="hidden" name="form_name" value="' . esc_attr($attr['name']) . '">';
                echo '<input type="hidden" name="flexi_attach_at" value="' . get_the_ID() . '">';
                echo '<input type="hidden" name="edit" value="' . esc_attr($attr['edit']) . '">';
                echo '<input type="hidden" name="type" value="' . esc_attr($attr['type']) . '">';
                echo '<input type="hidden" name="unique" value="' . esc_attr($attr['unique']) . '">';
                if (isset($_GET['id'])) {
                    echo '<input type="hidden" name="flexi_id" value="' . esc_attr($_GET['id']) . '">';
                }

                echo '<input type="hidden" name="upload_type" value="flexi">';

                echo '</form><div id="flexi_submit_notice"></div></div>';
            }
        }
        $abc = ob_get_clean();
        if (flexi_execute_shortcode()) {
            return $abc;
        } else {
            return '';
        }
    }

    //Examine & save the form submitted if ajax is off (use flexi_ajax_post.php)
    public function process_new_forms($attr)
    {
        $title = '';
        $author = '';
        $url = '';
        $email = '';
        $tags = '';
        $verify = '';
        $content = '';
        $category = '';
        $url = '';

        //var_dump($attr);

        $files = array();
        if (isset($_FILES['user-submitted-image'])) {
            $files = $_FILES['user-submitted-image'];
        }

        $detail_layout = esc_attr($attr['detail_layout']);
        $edit_page = esc_attr($attr['edit_page']);
        $title = esc_attr($attr['user-submitted-title']);
        $content = esc_attr($attr['content']);
        $category = esc_attr($attr['category']);
        $tags = esc_attr($attr['tags']);
        $url = esc_attr($attr['user-submitted-url']);

        if (isset($_POST['type'])) {
            if ('url' == $_POST['type']) {

                $result = flexi_submit_url($title, $url, $content, $category, $detail_layout, $tags, $edit_page);
            } else {

                $result = flexi_submit($title, $files, $content, $category, $detail_layout, $tags, $edit_page);
            }
        } else {

            $result = flexi_submit($title, $files, $content, $category, $detail_layout, $tags, $edit_page);
        }

        //var_dump($result);

        $post_id = false;
        if (isset($result['id'])) {
            $post_id = $result['id'];
        }

        $error = false;
        if (isset($result['error'])) {
            $error = array_filter(array_unique($result['error']));
        }

        if ($post_id) {

            $post = get_post($post_id);

            do_action("flexi_submit_complete", $post_id);

            if (flexi_get_option('publish', 'flexi_form_settings', 1) == 1) {

                echo "<div class='flexi_alert-box flexi_success'>" . __('Submission completed', 'flexi') . "</div>" . '' . flexi_get_error($result);
            } else {
                echo "<div class='flexi_alert-box flexi_warning'>" . __('Your submission is under review.', 'flexi') . "</div>" . '' . flexi_get_error($result);
            }
        } else {

            $reindex_array = array_values(array_filter($error));
            //var_dump($reindex_array);

            for ($x = 0; $x < count($reindex_array); $x++) {
                //echo $reindex_array[$x] . "-";
                // echo flexi_error_code($reindex_array[$x]);
                echo "<div class='flexi_alert-box flexi_error'>" . flexi_error_code($reindex_array[$x]) . "</div>";
            }
        }
        ?>
<div id="flexi_form">

    <?php echo flexi_post_toolbar_grid($post_id, false); ?>
</div>

<?php
    }

    //Examine & update the old form submitted
    public function process_update_forms($attr)
    {
        $title = '';
        $author = '';
        $url = '';
        $email = '';
        $tags = '';
        $verify = '';
        $content = '';
        $category = '';
        $url = '';
        if (isset($_POST['flexi_id'])) {
            $post_id = sanitize_text_field($_POST['flexi_id']);
        }

        //var_dump($attr);

        $files = array();
        if (isset($_FILES['user-submitted-image'])) {
            $files = $_FILES['user-submitted-image'];
        }

        $detail_layout = esc_attr($attr['detail_layout']);
        $edit_page = esc_attr($attr['edit_page']);
        $title = esc_attr($attr['user-submitted-title']);
        $content = esc_attr($attr['content']);
        $category = esc_attr($attr['category']);
        $tags = esc_attr($attr['tags']);
        $url = esc_attr($attr['user-submitted-url']);

        $result = flexi_update_post($post_id, $title, $files, $content, $category, $tags);

        if ($result) {
            do_action("flexi_submit_update", $post_id);

            if (flexi_get_option('publish', 'flexi_form_settings', 1) == 1) {

                echo "<div class='flexi_alert-box flexi_success'>" . __('Successfully updated', 'flexi') . "</div>";
            } else {
                echo "<div class='flexi_alert-box flexi_warning'>" . __('Your submission is under review.', 'flexi') . "</div>";
            }
        } else {
            echo "FAIL";
        }

        echo flexi_post_toolbar_grid($post_id, false);
    }

    public function render_tags($params)
    {
        $attr = shortcode_atts(array(
            'type' => 'text',
            'new_type' => 'number',
            'class' => '',
            'label_class' => 'fl-label fl-has-text-dark',
            'title' => '',
            'name' => '',
            'id' => '',
            'placeholder' => '',
            'value' => '',
            'edit' => '',
            'required' => '',
            'editor' => '',
            'type' => '',
            'rows' => '4',
            'cols' => '40',
            'checked' => '',
            'disabled' => '',
            'readonly' => '',
            'formnovalidate' => '',
            'novalidate' => '',
            'taxonomy' => 'flexi_category',
            'tag_taxonomy' => 'flexi_tag',
            'filter' => '',
            'multiple' => '',

        ), $params);
        $frm = new flexi_HTML_Form(false); // pass false for html rather than xhtml syntax
        $abc = "";

        ob_start();
        // flexi_log($attr['type']);
        if ('post_title' == $attr['type']) {
            echo '<div class="fl-field">';
            echo $frm->addLabelFor("user-submitted-title", $attr['title'], $attr['label_class']);
            echo '<div class="fl-control">';
            // arguments: type, name, value
            if ('' == $attr['edit']) {
                echo $frm->addInput('text', "user-submitted-title", $attr['value'], array('placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required'], 'disabled' => $attr['disabled']));
            } else {
                if (isset($_GET['id'])) {
                    echo $frm->addInput('text', "user-submitted-title", flexi_get_detail(esc_attr($_GET['id']), 'post_title'), array('placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'disabled' => $attr['disabled']));
                }
            }
            echo '</div>';
            echo '</div>';
        } else if ('video_url' == $attr['type']) {
            echo '<div class="fl-field">';
            echo $frm->addLabelFor("user-submitted-url", $attr['title'], $attr['label_class']);
            echo '<div class="fl-control">';
            if ('' == $attr['edit']) {
                echo $frm->addInput('url', "user-submitted-url", $attr['value'], array('placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
            } else {
                if (isset($_GET['id'])) {
                    echo $frm->addInput('url', "user-submitted-url", flexi_get_detail(esc_attr($_GET['id']), 'flexi_url'), array('placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                }
            }
            echo '</div>';
            echo '</div>';
        } else if ('category' == $attr['type']) {
            echo '<div class="fl-field">';
            echo $frm->addLabelFor('cat', $attr['title'], $attr['label_class']);
            echo '<div class="fl-control"><div class="fl-select">';
            if ('' == $attr['edit']) {
                echo flexi_droplist_album('flexi_category', '', array(), $attr['id']);
            } else {
                if (isset($_GET['id'])) {
                    $old_category_id = flexi_get_album(esc_attr($_GET['id']), 'term_id');
                    echo flexi_droplist_album('flexi_category', $old_category_id);
                }
            }
            echo '</div></div>';
            echo '</div>';
        } else if ('tag_list' == $attr['type']) {
            echo '<div class="fl-field">';
            echo $frm->addLabelFor('tags', $attr['title'], $attr['label_class']);
            echo '<div class="fl-control">';
            if ('' == $attr['edit']) {
                echo flexi_droplist_tag('flexi_tag', '', array(), $attr['id']);
            } else {
                if (isset($_GET['id'])) {
                    $old_tag_id = flexi_get_album(esc_attr($_GET['id']), 'slug', 'flexi_tag');
                    echo flexi_droplist_tag('flexi_tag', $old_tag_id);
                }
            }
            echo '</div>';
            echo '</div>';
        } else if ('tag' == $attr['type']) {
            echo '<div class="fl-field">';
            echo $frm->addLabelFor("tags", $attr['title'], $attr['label_class']);
            echo '<div class="fl-control">';
            // arguments: type, name, value
            if ('' == $attr['edit']) {
                echo $frm->addInput('', "tags", $attr['value'], array('id' => 'tags', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
            } else {
                if (isset($_GET['id'])) {
                    echo $frm->addInput('', "tags", flexi_get_taxonomy_raw(esc_attr($_GET['id']), 'flexi_tag'), array('id' => 'tags', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                }
            }
            echo " <script>
          jQuery(document).ready(function ()
          {

              jQuery('#tags').tagsInput();
          });
          </script>";
            echo '</div>';
            echo '</div>';
        } else if ('captcha' == $attr['type']) {
            echo '<div class="fl-field">';
            echo '<div class="fl-control">';
            do_action("flexi_captcha");
            echo '</div>';
            echo '</div>';
        } else if ('file' == $attr['type']) {
        ?>
<div class="fl-field">
    <div id="file-js-flexi" class="fl-file fl-has-name">
        <label class="fl-file-label">
            <input type="file" name="user-submitted-image[]" value="" id="file" class="fl-file-input" required="">
            <span class="fl-file-cta">
                <span class="fl-file-icon">
                    <i class="fas fa-upload"></i>
                </span>
                <span class="fl-file-label">
                    <?php echo $attr['title']; ?>
                </span>
            </span>
            <span class="fl-file-name">
                <?php echo __('No file selected', 'flexi'); ?>
            </span>
        </label>
    </div>
</div>
<script>
const fileInput = document.querySelector('#file-js-flexi input[type=file]');
fileInput.onchange = () => {
    if (fileInput.files.length > 0) {
        const fileName = document.querySelector('#file-js-flexi .fl-file-name');
        fileName.textContent = fileInput.files[0].name;
    }
}
</script>



<?php
        } else if ('file_multiple' == $attr['type']) {
            echo '<div class="fl-field">';
            if (is_flexi_pro()) {
                echo $frm->startTag('div', array('class' => $attr['class']));
                echo $frm->addInput('file', "user-submitted-image[]", '', array('id' => 'file', 'class' => $attr['class'] . '_hide', 'required' => $attr['required'], 'multiple' => $attr['multiple']));
                echo "<p>" . __($attr['title'], 'flexi') . "</p>";
                echo $frm->endTag('div');
            } else {
                echo "<br>Multiple upload is only available in FLEXI-PRO<br>";
            }
            echo '</div>';
        } else if ('article' == $attr['type']) {
            echo '<div class="fl-field">';
            echo $frm->addLabelFor('user-submitted-content', $attr['title'], $attr['label_class']);
            echo '<div class="fl-control">';
            // arguments: name, rows, cols, value, optional assoc. array
            if ('' == $attr['edit']) {
                echo $frm->addTextArea(
                    'user-submitted-content',
                    $attr['rows'],
                    $attr['cols'],
                    '',
                    array('id' => $attr['id'], 'placeholder' => $attr['placeholder'], 'required' => $attr['required'], 'class' => $attr['class'])
                );
            } else {
                if (isset($_GET['id'])) {
                    echo $frm->addTextArea(
                        'user-submitted-content',
                        $attr['rows'],
                        $attr['cols'],
                        flexi_get_detail(esc_attr($_GET['id']), 'post_content'),
                        array('id' => $attr['id'], 'placeholder' => $attr['placeholder'], 'required' => $attr['required'], 'class' => $attr['class'])
                    );
                }
            }
            echo '</div>';
            echo '</div>';
        } else if ('text' == $attr['type']) {
            echo '<div class="fl-field">';
            if ('' == $attr['edit']) {

                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                // arguments: type, name, value
                echo '<div class="fl-control">';
                echo $frm->addInput('text', $attr['name'], $attr['value'], array('placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                echo '</div>';
            } else {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                // arguments: type, name, value
                echo '<div class="fl-control">';
                if (isset($_GET['id'])) {
                    echo $frm->addInput('text', $attr['name'], flexi_custom_field_value(esc_attr($_GET['id']), $attr['name']), array('placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                }
                echo '</div>';
            }
            echo '</div>';
        } else if ('text_array' == $attr['type']) {
            echo '<div class="fl-field">';

            if ('' == $attr['edit']) {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                // arguments: type, name, value
                echo '<div class="fl-control">';
                echo $frm->addInput('text', $attr['name'], $attr['value'], array('type' => 'text_array', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                echo '</div>';
            } else {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                // arguments: type, name, value
                echo '<div class="fl-control">';
                echo '<div  id="link_tree_container">';
                if (isset($_GET['id'])) {
                    // flexi_log($attr['name']);
                    $value = get_post_meta(esc_attr($_GET['id']), $attr['name'], true);

                    if (is_array($value)) {
                        // flexi_log($value);
                        $i = 0;


                        foreach ($value as $x => $val) {
                            //  flexi_log($x . "=" . $val . "<br>");
                            if ($x != '') {
            ?>
<div id='link_tree_card' class='fl-card'>
    <div id='link_tree_content' class='fl-card-content'>
        <div class="fl-field fl-is-horizontal">
            <div class="fl-field-label fl-is-small">
                <label class="fl-label">Link Title</label>
            </div>
            <div class="fl-field-body">
                <div class="fl-field">
                    <div class="fl-control">
                        <input type="text" name="<?php echo $attr['name']; ?>[]" value="<?php echo $x; ?>"
                            placeholder="Link title" class="<?php echo $attr['class']; ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="fl-field fl-is-horizontal">
            <div class="fl-field-label fl-is-small">
                <label class="fl-label">Link URL</label>
            </div>
            <div class="fl-field-body">
                <div class="fl-field">
                    <div class="fl-control">
                        <input type="url" name="<?php echo $attr['name'] . "_value"; ?>[]" value="<?php echo $val; ?>"
                            placeholder="Link URL eg. https://...." class="<?php echo $attr['class']; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
                            }
                        }
                    } else {
                        ?>
<div id='link_tree_card' class='fl-card'>
    <div id='link_tree_content' class='fl-card-content'>
        <div class="fl-field fl-is-horizontal">
            <div class="fl-field-label fl-is-small">
                <label class="fl-label">Link Title</label>
            </div>
            <div class="fl-field-body">
                <div class="fl-field">
                    <div class="fl-control">
                        <input type="text" name="<?php echo $attr['name']; ?>[]" placeholder="Link title"
                            class="<?php echo $attr['class']; ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="fl-field fl-is-horizontal">
            <div class="fl-field-label fl-is-small">
                <label class="fl-label">Link URL</label>
            </div>
            <div class="fl-field-body">
                <div class="fl-field">
                    <div class="fl-control">
                        <input type="url" name="<?php echo $attr['name'] . "_value"; ?>[]"
                            placeholder="Link URL eg. https://...." class="<?php echo $attr['class']; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
                    }
                }
                echo '</div>';
                echo '</div>';
                echo '<br><div class="fl-has-text-centered"><a id="flexi-btn-copy" class="fl-button fl-is-link fl-is-light" value="link">Add New Link</a></div>';
            }
            echo '</div>';
        } else if ('crypto_array' == $attr['type']) {
            echo '<div class="fl-field">';

            if ('' == $attr['edit']) {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                // arguments: type, name, value
                echo '<div class="fl-control">';
                echo $frm->addInput('text', $attr['name'], $attr['value'], array('type' => 'text_array', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                echo '</div>';
            } else {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                // arguments: type, name, value
                echo '<div class="fl-control">';
                echo '<div  id="crypto_tree_container">';
                if (isset($_GET['id'])) {
                    // flexi_log($attr['name']);
                    $value = get_post_meta(esc_attr($_GET['id']), $attr['name'], true);

                    if (is_array($value)) {
                        // flexi_log($value);
                        $i = 0;


                        foreach ($value as $x => $val) {
                            //  flexi_log($x . "=" . $val . "<br>");

                        ?>


<div id='crypto_tree_card' class='fl-card'>

    <div class="fl-field fl-has-addons fl-has-addons-right">
        <p class="fl-control">
            <span class="fl-select">
                <select name="<?php echo $attr['name']; ?>[]">
                    <option value="btc" <?php selected($x, 'btc'); ?>>Bitcoin</option>
                    <option value="eth" <?php selected($x, 'eth'); ?>>Ethereum</option>
                    <option value="bsc" <?php selected($x, 'bsc'); ?>>BSC </option>
                    <option value="matic" <?php selected($x, 'matic'); ?>>Polygon</option>
                    <option value="fil" <?php selected($x, 'fil'); ?>>Filecoin</option>
                    <option value="zil" <?php selected($x, 'zil'); ?>>Zilliqa</option>
                    <option value="sol" <?php selected($x, 'sol'); ?>>Solana</option>
                </select>
            </span>
        </p>
        <p class="fl-control  fl-is-expanded">
            <input class="fl-input" type="text" name="<?php echo $attr['name'] . "_value"; ?>[]"
                placeholder="Crypto wallet address" value="<?php echo $val; ?>">
        </p>
    </div>

</div>

<?php

                            //  echo "<div id='link_tree' class='fl-card'>";
                            //  echo $frm->addInput('text', $attr['name'], $x, array('type' => 'text_array', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required'])) . "<br>";
                            //    echo $frm->addInput('text', $attr['name'] . "_value", $val, array('type' => 'text_array', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                            //   echo '</div>';
                        }
                    } else {
                        ?>
<div id='crypto_tree_card' class='fl-card'>

    <div class="fl-field fl-has-addons fl-has-addons-right">
        <p class="fl-control">
            <span class="fl-select">
                <select name="<?php echo $attr['name']; ?>[]">
                    <option value="btc">Bitcoin </option>
                    <option value="eth">Ethereum </option>
                    <option value="matic">Polygon</option>
                    <option value="bsc">BSC</option>
                    <option value="fil">Filecoin</option>
                    <option value="zil">Zilliqa</option>
                    <option value="sol">Solana</option>
                </select>
            </span>
        </p>
        <p class="fl-control  fl-is-expanded">
            <input class="fl-input" type="text" name="<?php echo $attr['name'] . "_value"; ?>[]"
                placeholder="Crypto wallet address">
        </p>
    </div>

</div>
<?php
                    }
                }
                echo '</div>';
                echo '</div>';
                echo '<br><div class="fl-has-text-centered"><a id="flexi-btn-copy" class="fl-button fl-is-link fl-is-light" value="crypto">Add New Crypto</a></div>';
            }
            echo '</div>';
        } else if ('social_array' == $attr['type']) {
            echo '<div class="fl-field">';

            if ('' == $attr['edit']) {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                // arguments: type, name, value
                echo '<div class="fl-control">';
                echo $frm->addInput('text', $attr['name'], $attr['value'], array('type' => 'text_array', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                echo '</div>';
            } else {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                // arguments: type, name, value
                echo '<div class="fl-control">';
                echo '<div  id="social_tree_container">';
                if (isset($_GET['id'])) {
                    // flexi_log($attr['name']);
                    $value = get_post_meta(esc_attr($_GET['id']), $attr['name'], true);

                    if (is_array($value)) {
                        // flexi_log($value);
                        $i = 0;


                        foreach ($value as $x => $val) {
                            //  flexi_log($x . "=" . $val . "<br>");

                        ?>


<div id='social_tree_card' class='fl-card'>

    <div class="fl-field fl-has-addons fl-has-addons-right">
        <p class="fl-control">
            <span class="fl-select">
                <select name="<?php echo $attr['name']; ?>[]">

                    <option value="twitter" <?php selected($x, 'twitter'); ?>>Twitter Link</option>
                    <option value="telegram" <?php selected($x, 'telegram'); ?>>Telegram Link</option>
                    <option value="facebook" <?php selected($x, 'facebook'); ?>>Facebook Link</option>
                    <option value="youtube" <?php selected($x, 'youtube'); ?>>Youtube Link</option>
                    <option value="instagram" <?php selected($x, 'instagram'); ?>>Instagram Link</option>
                    <option value="discord" <?php selected($x, 'discord'); ?>>Discord Link</option>
                </select>
            </span>
        </p>
        <p class="fl-control  fl-is-expanded">
            <input class="fl-input" type="url" name="<?php echo $attr['name'] . "_value"; ?>[]"
                placeholder="Social profile link eg. https://www......" value="<?php echo $val; ?>">
        </p>
    </div>

</div>

<?php

                            //  echo "<div id='link_tree' class='fl-card'>";
                            //  echo $frm->addInput('text', $attr['name'], $x, array('type' => 'text_array', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required'])) . "<br>";
                            //    echo $frm->addInput('text', $attr['name'] . "_value", $val, array('type' => 'text_array', 'placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                            //   echo '</div>';
                        }
                    } else {
                        ?>
<div id='social_tree_card' class='fl-card'>

    <div class="fl-field fl-has-addons fl-has-addons-right">
        <p class="fl-control">
            <span class="fl-select">
                <select name="<?php echo $attr['name']; ?>[]">

                    <option value="twitter">Twitter Link</option>
                    <option value="telegram">Telegram Link</option>
                    <option value="facebook">Facebook Link</option>
                    <option value="youtube">Youtube Link</option>
                    <option value="instagram">Instagram Link</option>
                    <option value="discord">Discord Link</option>
                </select>
            </span>
        </p>
        <p class="fl-control  fl-is-expanded">
            <input class="fl-input" type="url" name="<?php echo $attr['name'] . "_value"; ?>[]"
                placeholder="Social profile link Eg. https://www......">
        </p>
    </div>

</div>
<?php
                    }
                }
                echo '</div>';
                echo '</div>';
                echo '<br><div class="fl-has-text-centered"><a id="flexi-btn-copy" class="fl-button fl-is-link fl-is-light" value="social">Add New Social Link</a></div>';
            }
            echo '</div>';
        } else if ('other' == $attr['type']) {
            echo '<div class="fl-field">';
            if ('' == $attr['edit']) {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                echo '<div class="fl-control">';
                // arguments: type, name, value
                echo $frm->addInput($attr['new_type'], $attr['name'], $attr['value'], array('placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                echo '</div>';
            } else {
                // arguments: for (id of associated form element), text
                echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
                echo '<div class="fl-control">';
                // arguments: type, name, value
                if (isset($_GET['id'])) {
                    echo $frm->addInput($attr['new_type'], $attr['name'], flexi_custom_field_value(esc_attr($_GET['id']), $attr['name']), array('placeholder' => $attr['placeholder'], 'class' => $attr['class'], 'required' => $attr['required']));
                }
                echo '</div>';
            }
            echo '</div>';
        } else if ('submit' == $attr['type']) {
            echo '<div class="fl-field">';
            //submit
            do_action("flexi_before_submit");
            echo '<div class="fl-control">';
            echo $frm->addInput('submit', $attr['name'], $attr['value'], array('id' => $attr['name'], 'class' => $attr['class']));
            echo '</div>';
            //Generate jasvascript which will check filesize before upload.
            //Todo: Don't include it if file input button not available.
            echo flexi_javascript_file_upload('flexi_submit_notice', $attr['name']);
            do_action("flexi_after_submit");
            echo '</div>';
        } else if ('radio' == $attr['type'] || 'checkbox' == $attr['type']) {
            echo '<div class="fl-field">';
            echo '<div class="fl-control">';
            $values = explode(',', $attr['value']);
            echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
            foreach ($values as $option) {
                $val = explode(":", $option);
                $caption = isset($val[1]) ? $val[1] : $val[0];

                if ("radio" == $attr['type']) {
                    echo '<label class="fl-radio">';
                    if ('' == $attr['edit']) {
                        echo $frm->addInput('radio', $attr['name'], $val[0], array('required' => $attr['required'])) . ' ' . $caption . ' ';
                    } else {
                        if (isset($_GET['id'])) {
                            if ($val[0] == flexi_custom_field_value(esc_attr($_GET['id']), $attr['name'])) {
                                echo $frm->addInput('radio', $attr['name'], $val[0], array('required' => $attr['required'], 'checked' => 'checked')) . ' ' . $caption . ' ';
                            } else {
                                echo $frm->addInput('radio', $attr['name'], $val[0], array('required' => $attr['required'])) . ' ' . $caption . ' ';
                            }
                        }
                    }
                    echo '</label>';
                }

                if ("checkbox" == $attr['type']) {
                    echo '<label class="fl-checkbox">';
                    if ('' == $attr['edit']) {
                        echo $frm->addInput('checkbox', $val[0], $caption, array('required' => $attr['required'])) . ' ' . $caption . ' ';
                    } else {
                        if (isset($_GET['id'])) {
                            if ($val[0] == flexi_custom_field_value(esc_attr($_GET['id']), $attr['name'])) {
                                echo $frm->addInput('checkbox', $val[0], $caption, array('required' => $attr['required'], 'checked' => 'checked')) . ' ' . $caption . ' ';
                            } else {
                                echo $frm->addInput('checkbox', $val[0], $caption, array('required' => $attr['required'])) . ' ' . $caption . ' ';
                            }
                        }
                    }
                    echo '&nbsp;</label>';
                }
            }
            echo '</div>';
            echo '</div>';
        } else if ('select' == $attr['type']) {
            echo '<div class="fl-field">';
            $val = array();
            $label = array();

            $values = explode(',', $attr['value']);
            foreach ($values as $option) {
                $cap = explode(":", $option);
                array_push($val, $cap[0]);
                array_push($label, $cap[1]);
            }
            //var_dump($values);
            //var_dump($val);
            //var_dump($label);

            /** addSelectListArrays arguments:
             *   name, array containing option values, array containing option text,
             *   optional: selected option's value, header, additional attributes in associative array
             */
            echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
            if ('' == $attr['placeholder']) {
                echo $frm->addSelectListArrays($attr['name'], $val, $label, '');
            } else {
                echo $frm->addSelectListArrays($attr['name'], $val, $label, '', ' - ' . $attr['placeholder'] . ' - ', array('required' => $attr['required']));
            }
            echo '</div>';
        } else if ('textarea' == $attr['type']) {
            echo '<div class="fl-field">';
            echo $frm->addLabelFor($attr['name'], $attr['title'], $attr['label_class']);
            echo '<div class="fl-control">';
            // arguments: name, rows, cols, value, optional assoc. array
            if ('' == $attr['edit']) {
                echo $frm->addTextArea(
                    $attr['name'],
                    $attr['rows'],
                    $attr['cols'],
                    '',
                    array('id' => $attr['id'], 'placeholder' => $attr['placeholder'], 'required' => $attr['required'], 'class' => $attr['class'])
                );
            } else {
                if (isset($_GET['id'])) {
                    echo $frm->addTextArea(
                        $attr['name'],
                        $attr['rows'],
                        $attr['cols'],
                        flexi_custom_field_value(esc_attr($_GET['id']), $attr['name']),
                        array('id' => $attr['id'], 'placeholder' => $attr['placeholder'], 'required' => $attr['required'], 'class' => $attr['class'])
                    );
                }
            }
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="fl-field">';
            echo "Invalid form tag";
            echo '</div>';
        }

        $abc = ob_get_clean();
        if (is_singular() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return $abc;
        } else {
            return '';
        }
    }

    //Adds edit icon in flexi icon container.
    public function flexi_add_icon_grid_edit($icon)
    {
        global $post;
        $edit_flexi_icon = flexi_get_option('edit_flexi_icon', 'flexi_icon_settings', 1);
        // $nonce = wp_create_nonce("flexi_ajax_edit");
        $link = flexi_get_button_url($post->ID, false, 'edit_flexi_page', 'flexi_form_settings');

        //Check if special edit form for the page
        $user_edit_page = get_post_meta($post->ID, 'flexi_new_edit_page', '0');
        if (!$user_edit_page) {
            add_post_meta($post->ID, 'flexi_new_edit_page', '0');
        } else {
            //flexi_log($user_edit_page);
            if ($user_edit_page[0] != '0') {

                $link = esc_url(get_page_link($user_edit_page[0]));
                $link = esc_url(add_query_arg('id', $post->ID, $link));
                //flexi_log($link);
            } else {
                // flexi_log("sss");
                $link = flexi_get_button_url($post->ID, false, 'edit_flexi_page', 'flexi_form_settings');
            }
        }

        $extra_icon = array();

        if (get_the_author_meta('ID') == get_current_user_id()) {
            // if (isset($options['show_trash_icon'])) {
            if ("1" == $edit_flexi_icon) {
                $extra_icon = array(
                    array("far fa-edit", __('Modify', 'flexi'), $link, '#', $post->ID, 'fl-is-small flexi_css_button'),

                );
            }
            // }
        }

        // combine the two arrays
        if (is_array($extra_icon) && is_array($icon)) {
            $icon = array_merge($extra_icon, $icon);
        }

        return $icon;
    }

    //Add post again button after form submit
    public function flexi_add_icon_submit_toolbar($icon, $id = '', $bool = true)
    {

        $extra_icon = array();
        if ($bool) {
            $link = flexi_get_button_url($id, true);
            $class = 'fl-button flexi_send_again';
        } else {
            $link = flexi_get_button_url('', false);
            $class = 'fl-button';
        }

        if ("#" != $link) {
            $extra_icon = array(
                array("fas fa-plus", __('New', 'flexi'), $link, $id, $class),

            );
        }

        // combine the two arrays
        if (is_array($extra_icon) && is_array($icon)) {
            $icon = array_merge($extra_icon, $icon);
        }

        return $icon;
    }

    //Add VIEW button after form submit
    public function flexi_add_icon_view_post_toolbar($icon, $id = '', $bool = true)
    {

        if ('' != $id) {
            $popup = flexi_get_option('lightbox_switch', 'flexi_detail_settings', 1);
            if ('1' != $popup) {
                $extra_icon = array();
                $link = get_permalink($id);
                $class = "fl-button";

                if ("#" != $link) {
                    $extra_icon = array(
                        array("far fa-eye", __('View', 'flexi'), $link, $id, $class),

                    );
                }

                // combine the two arrays
                if (is_array($extra_icon) && is_array($icon)) {
                    $icon = array_merge($extra_icon, $icon);
                }
            }
        }

        return $icon;
    }
}