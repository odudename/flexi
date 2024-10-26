<?php
//Attach footer of gallery based based on layout selection

$footer_file = FLEXI_PLUGIN_DIR . 'public/partials/layout/gallery/' . esc_attr($layout) . '/footer.php';
if (file_exists($footer_file)) {
    require $footer_file;
}

$sidebar_widget = flexi_get_option('enable_gallery_widget', 'flexi_image_layout_settings', 0);

if ($sidebar_widget == 1 && false == $clear && is_active_sidebar('flexi-gallery-sidebar') && is_flexi_page('primary_page', 'flexi_image_layout_settings')) {

    echo "</div><div class='fl-column'><div class='flexi_gallery_sidebar'>";
    dynamic_sidebar('flexi-gallery-sidebar');
    echo "</div></div></div>";
}

//If AJAX loading is enabled

if ('scroll' == $navigation || 'button' == $navigation) {
    // AJAX lazy loading
    echo "<div style='clear:both;'></div>";
    echo "<div id='flexi_load_more' style='text-align:center'><a id='load_more_link' class='flexi_load_more " . esc_attr($style_button) . "' style='margin:5px; font-size: 80%;' href='#'>" . __("Load More", "flexi") . "</a></div>";
    echo "<div id='reset' style='display:none'>false</div>";
    echo "<a id='load_more_reset' class='flexi_load_more' style='margin:5px; font-size: 80%;' href='/admin-ajax.php?action=flexi_load_more' data-post_id='" . get_the_ID() . "' data-paged='" . $query->max_num_pages . "' data-reset='true'></a>";
?>
    <div id='flexi_loader_gallery' style='display: none;text-align:center;'>
        <img src="<?php echo esc_url_raw(FLEXI_PLUGIN_URL . '/public/images/loading.gif'); ?>">

    </div>


    <script>
        //Load first record on page load
        jQuery(document).ready(function() {
            jQuery('#load_more_link').click();

        })
    </script>
<?php
} else if ('off' == $navigation) {
    echo ''; //Turn off navigation
} else {
    //Load basic page loading with other plugin support

    echo flexi_page_navi($query);
}
echo "<div id='gallery_layout' style='display:none'>" . esc_attr($layout) . "</div>";
echo "<div id='popup' style='display:none'>" . esc_attr($popup) . "</div>";
echo "<div id='album' style='display:none'>" . esc_attr($album) . "</div>";
echo "<div id='max_paged' style='display:none'>" . esc_attr($query->max_num_pages) . "</div>";
echo "<div id='search' style='display:none'>" . esc_attr($search) . "</div>";
echo "<div id='postsperpage' style='display:none'>" . esc_attr($postsperpage) . "</div>";
echo "<div id='column' style='display:none'>" . esc_attr($column) . "</div>";
echo "<div id='orderby' style='display:none'>" . esc_attr($orderby) . "</div>";
echo "<div id='user' style='display:none'>" . esc_attr($user) . "</div>";
echo "<div id='keyword' style='display:none'>" . esc_attr($keyword) . "</div>";
echo "<div id='padding' style='display:none'>" . esc_attr($padding) . "</div>";
echo "<div id='hover_effect' style='display:none'>" . esc_attr($hover_effect) . "</div>";
echo "<div id='php_field' style='display:none'>" . esc_attr($php_field) . "</div>";
echo "<div id='hover_caption' style='display:none'>" . esc_attr($hover_caption) . "</div>";
echo "<div id='evalue' style='display:none'>" . esc_attr($evalue) . "</div>";
echo "<div id='attach' style='display:none'>" . esc_attr($attach) . "</div>";
echo "<div id='attach_id' style='display:none'>" . esc_attr($cur_page_id) . "</div>";
echo "<div id='filter' style='display:none'>" . esc_attr($filter) . "</div>";
echo "<div id='post_status' style='display:none'>" . esc_attr($post_status) . "</div>";
?>
<style>
    :root {
        --flexi_user_width: <?php echo esc_attr($width);
                            ?>px;
        --flexi_user_height: <?php echo esc_attr($height);
                                ?>px;
        --flexi_user_column: <?php echo esc_attr($column);
                                ?>;
    }
</style>