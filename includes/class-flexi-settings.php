<?php

/**
 * Flexi Admin setting page
 *
 * @link       https://odude.com/
 * @since      1.0.0
 * @author     ODude <navneet@odude.com>
 * @package    Flexi
 * @subpackage Flexi/includes
 */

// Exit if accessed directly
if (!defined('WPINC')) {
    die;
}

class FLEXI_Admin_Settings
{

    /**
     * Settings tabs array.
     *
     * @since  1.0.0
     * @access protected
     * @var    array
     */
    protected $tabs = array();

    /**
     * Settings sections array.
     *
     * @since  1.0.0
     * @access protected
     * @var    array
     */
    protected $sections = array();

    /**
     * Settings fields array
     *
     * @since  1.0.0
     * @access protected
     * @var    array
     */
    protected $fields = array();

    /**
     * Add a settings menu for the plugin.
     *
     * @since 1.0.0
     */
    public function admin_menu()
    {
        add_submenu_page(
            'flexi',
            __('Flexi', 'flexi') . ' - ' . __('Settings', 'flexi'),
            __('Settings', 'flexi'),
            'manage_options',
            'flexi_settings',
            array($this, 'gallery_settings_form')
        );
    }

    /**
     * gallery settings form.
     *
     * @since 1.0.0
     */
    public function gallery_settings_form()
    {
        require FLEXI_PLUGIN_DIR . 'admin/partials/settings.php';
    }

    /**
     * Initiate settings.
     *
     * @since 1.0.0
     */
    public function admin_init()
    {
        $this->tabs = $this->get_tabs();
        $this->sections = $this->get_sections();
        $this->fields = $this->get_fields();

        // Initialize settings
        $this->initialize_settings();
    }

    /**
     * Get settings tabs.
     *
     * @since  1.0.0
     * @return array $tabs Setting tabs array.
     */
    public function get_tabs()
    {
        $tabs = array(
            'general' => __('General', 'flexi'),
            'gallery' => __('Gallery', 'flexi'),
            'form' => __('Form', 'flexi'),
            'detail' => __('Detail', 'flexi'),
            'extension' => __('Addon', 'flexi'),
        );

        return apply_filters('flexi_settings_tabs', $tabs);
    }

    /**
     * Get settings sections.
     *
     * @since  1.0.0
     * @return array $sections Setting sections array.
     */
    public function get_sections()
    {
        $category_help = ' <a style="text-decoration: none;" href="https://odude.com/docs/flexi-gallery/shortcode/flexi-category/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';

        $sections = array(
            array(
                'id' => 'flexi_icon_settings',
                'title' => __('Icons & user access settings', 'flexi'),
                'tab' => 'general',
            ),
            array(
                'id' => 'flexi_media_settings',
                'title' => __('Media settings', 'flexi'),
                'description' => __('The sizes listed below determine only the image container size. It do not affect original sizes.', 'flexi'),
                'tab' => 'general',
            ),

            array(
                'id' => 'flexi_image_layout_settings',
                'title' => __('Gallery Settings', 'flexi'),
                'description' => __('Settings will be applied on <code>[flexi-primary] & [flexi-gallery]</code> shortcodes.<br>Specific settings will be inactive if same attribute is used in shortcode.<br>It is advisable use one gallery per page to avoid conflict.<br>Settings will be not implemented, if shortcode contains specific attributes. (evalue) are option which can be passed via shortcode. <br><code>[flexi-common-toolbar]</code> can be added by editing gallery page if dashboard buttons required. ', 'flexi'),
                'tab' => 'gallery',
            ),
            array(
                'id' => 'flexi_gallery_appearance_settings',
                'title' => __('Gallery appearance', 'flexi'),
                'tab' => 'gallery',
            ),

            array(
                'id' => 'flexi_form_settings',
                'title' => __('Submission Form Settings', 'flexi'),
                'tab' => 'form',
            ),
            array(
                'id' => 'flexi_categories_settings',
                'title' => __('Category & Tags', 'flexi'),
                'description' => __('Categories and tags management', 'flexi') . ' ' . $category_help,
                'tab' => 'gallery',
            ),

            array(
                'id' => 'flexi_detail_settings',
                'title' => __('Detail Page Settings', 'flexi'),
                'description' => __('Detail & Popup page displays full content.', 'flexi'),
                'tab' => 'detail',
            ),
            array(
                'id' => 'flexi_permalink_settings',
                'title' => __('Permalink URL Slugs', 'flexi'),
                'description' => __('NOTE: Just make sure that, after updating the fields in this section, you flush the rewrite rules by visiting "Settings > Permalinks". Otherwise you\'ll still see the old links.', 'flexi'),
                'tab' => 'detail',
            ),
            array(
                'id' => 'flexi_extension',
                'title' => __('Extension Management', 'flexi'),
                'description' => __('A new sub-tab is created as soon as extension is enabled.<br>Click on blue tools icon to navigate<br><b>Note:</b> Disable any extension, will not delete the old settings. It is just hidden.', 'flexi'),
                'tab' => 'extension',
            ),
        );

        return apply_filters('flexi_settings_sections', $sections);
    }

    /**
     * Get settings fields.
     *
     * @since  1.0.0
     * @return array $fields Setting fields array.
     */
    public function get_fields()
    {

        if (is_flexi_pro()) {
            $file_size_limit = 9999;
        } else {
            $file_size_limit = 100;
        }

        $primary_help = ' <a style="text-decoration: none;" href="https://odude.com/docs/flexi-gallery/information/primary-gallery-page/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';
        $popup_help = ' <a style="text-decoration: none;" href="https://odude.com/docs/flexi-gallery/tutorial/customize-lightbox-or-popup/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';
        $submission_help = ' <a style="text-decoration: none;" href="https://odude.com/docs/flexi-gallery/shortcode/flexi-form/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';
        $edit_help = ' <a style="text-decoration: none;" href="https://odude.com/docs/flexi-gallery/tutorial/modify-submission-form/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';
        $category_help = ' <a style="text-decoration: none;" href="https://odude.com/docs/flexi-gallery/shortcode/flexi-category/" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>';

        //Import layout page link
        $layout_page = admin_url('admin.php?page=flexi');
        $layout_page = add_query_arg('tab', 'layout', $layout_page);

        //User dashboard link
        $user_dashboard_page_link = admin_url('admin.php?page=flexi_settings&tab=general&section=flexi_user_dashboard_settings');

        $fields = array(

            'flexi_gallery_appearance_settings' => array(
                array(
                    'name' => 'image_space',
                    'label' => __('Space between images', 'flexi'),
                    'description' => __('Padding between images. Set shortcode [flexi-gallery padding="0"] for none', 'flexi'),
                    'type' => 'number',
                    'size' => 'small',
                    'min' => '0',
                    'max' => '10',
                    'step' => '1',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'excerpt_length',
                    'label' => __('Excerpt Length', 'flexi'),
                    'description' => __('Number of words of short description', 'flexi'),
                    'type' => 'number',
                    'size' => 'small',
                    'min' => '5',
                    'max' => '30',
                    'step' => '1',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'hover_effect',
                    'label' => __('Thumbnail hover effect', 'flexi'),
                    'description' => __('Effect on mouse over image.', 'flexi'),
                    'type' => 'select',
                    'options' => array(
                        'flexi_effect_none' => __('None', 'flexi'),
                        'flexi_effect_1' => __('Blur', 'flexi'),
                        'flexi_effect_2' => __('Grayscale', 'flexi'),
                        'flexi_effect_3' => __('Zoom In', 'flexi'),
                    ),
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'hover_caption',
                    'label' => __('Thumbnail hover style', 'flexi'),
                    'description' => __('Display title or icon on mouse over image', 'flexi'),
                    'type' => 'select',
                    'options' => array(
                        'flexi_caption_none' => __('None', 'flexi'),
                        'flexi_caption_1' => __('Slide title', 'flexi'),
                        'flexi_caption_2' => __('Pull up card', 'flexi'),
                        'flexi_caption_3' => __('Slide right', 'flexi'),
                        'flexi_caption_4' => __('Pull up title', 'flexi'),
                        'flexi_caption_5' => __('Top & Bottom', 'flexi'),
                    ),
                    'sanitize_callback' => 'sanitize_key',
                ),
            ),

            'flexi_image_layout_settings' => array(

                array(
                    'name' => 'primary_page',
                    'label' => __('Primary Gallery Page', 'flexi'),
                    'description' => __('Flexi home page with shortcode [flexi-common-toolbar] [flexi-primary]', 'flexi') . ' ' . $primary_help,
                    'type' => 'pages',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'gallery_layout',
                    'label' => __('Select gallery layout', 'flexi'),
                    'description' => __('Selected layout will be used as default layout, if not specified in shortcode parameter.', 'flexi') . ' <a href="' . esc_url($layout_page) . '">' . __("Import Layout", "flexi") . '</a>',
                    'type' => 'layout',
                    'sanitize_callback' => 'sanitize_key',
                    'step' => 'gallery',
                ),
                array(
                    'name' => 'enable_gallery',
                    'label' => __('Primary gallery access', 'flexi'),
                    'description' => __('It will enable/disable primary gallery page based on selection', 'flexi'),
                    'type' => 'select',
                    'options' => array(
                        'everyone' => __('Everyone', 'flexi'),
                        'member' => __('Only members', 'flexi'),
                        'publish_posts' => __('Only with Publish Post rights', 'flexi'),
                        'disable_gallery' => __('Disable gallery', 'flexi'),
                    ),
                    'sanitize_callback' => 'sanitize_key',
                ),

                array(
                    'name' => 'perpage',
                    'label' => __('Post per page', 'flexi'),
                    'description' => __('Number of images/post/videos to be shown at a time.', 'flexi'),
                    'type' => 'number',
                    'size' => 'small',
                    'min' => '1',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'column',
                    'label' => __('Number of Columns', 'flexi'),
                    'description' => __('Maximum number of post to be shown horizontally. Works only on masonry, portfolio, wide layout', 'flexi'),
                    'type' => 'number',
                    'size' => 'small',
                    'min' => '1',
                    'max' => '10',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'navigation',
                    'label' => __('Navigation Style', 'flexi'),
                    'description' => '',
                    'type' => 'radio',
                    'options' => array(
                        'page' => __('Page Number', 'flexi'),
                        'button' => __('Load More Button', 'flexi'),
                        'scroll' => __(' Mouse Scroll', 'flexi'),
                    ),
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'user_dashboard_link',
                    'label' => __('User Dashboard', 'flexi'),
                    'description' => "<a href='" . $user_dashboard_page_link . "'>" . __('Separate dashboard settings', 'flexi') . '</a>',
                    'type' => 'html',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'enable_gallery_widget',
                    'label' => __('Enable Sidebar Widget', 'flexi'),
                    'description' => __('Enable primary gallery & category page sidebar widget', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'gallery_tags',
                    'label' => __('Gallery sorting tags', 'flexi'),
                    'description' => __('Shows tags above gallery, only if few tags available.', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_title',
                    'label' => __('Display post title', 'flexi') . ' (evalue)',
                    'description' => __('Title of the flexi post', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_excerpt',
                    'label' => __('Display post excerpt', 'flexi') . ' (evalue)',
                    'description' => __('Short description', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_custom',
                    'label' => __('Display custom fields', 'flexi') . ' (evalue)',
                    'description' => __('Associated custom fields enabled', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_icon',
                    'label' => __('Display icon toolbar', 'flexi') . ' (evalue)',
                    'description' => __('Icons like profile, trash, download etc.', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_category',
                    'label' => __('Display category', 'flexi') . ' (evalue)',
                    'description' => __('Associated category of post with link', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_tag',
                    'label' => __('Display tag', 'flexi') . ' (evalue)',
                    'description' => __('Associated tag of post with link', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_date',
                    'label' => __('Display date', 'flexi') . ' (evalue)',
                    'description' => __('Published date', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_author',
                    'label' => __('Display author name', 'flexi') . ' (evalue)',
                    'description' => __('Publisher name', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'evalue_profile_icon',
                    'label' => __('Display profile avatar', 'flexi') . ' (evalue)',
                    'description' => __('Profile avatar with link', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),

            ),
            'flexi_form_settings' => array(
                array(
                    'name' => 'enable_form',
                    'label' => __('Form submission access', 'flexi'),
                    'description' => __('It will enable/disable frontend form as specified.', 'flexi'),
                    'type' => 'select',
                    'options' => array(
                        'everyone' => __('Everyone', 'flexi'),
                        'member' => __('Only members', 'flexi'),
                        'publish_posts' => __('Only with Publish Post rights', 'flexi'),
                        'disable_form' => __('Disable submission', 'flexi'),
                    ),
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'publish',
                    'label' => __('Auto approve post', 'flexi'),
                    'description' => __('Automatically publish Post as soon as user submit.', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),

                array(
                    'name' => 'upload_file_size',
                    'label' => __('File size allowed (MB)', 'flexi'),
                    'description' => __('Maximum file size allowed to upload by visitor. Your sever limit is:', 'flexi') . ' <b>' . size_format(wp_max_upload_size()) . '</b>',
                    'type' => 'number',
                    'size' => 'small',
                    'min' => '1',
                    'max' => $file_size_limit,
                    'sanitize_callback' => 'sanitize_key',
                ),

                array(
                    'name' => 'default_user',
                    'label' => __('Assign default user', 'flexi'),
                    'description' => __('Type the username to assign for guest submit else no author is assigned.', 'flexi'),
                    'type' => 'text',
                    'size' => '20',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'submission_form',
                    'label' => __('Submission form', 'flexi'),
                    'description' => __('Page which will be used at frontend to let users to submit flexi post. Link this page in your frontend menu.', 'flexi') . ' ' . $submission_help,
                    'type' => 'pages',
                    'sanitize_callback' => 'sanitize_key',
                ),

                array(
                    'name' => 'edit_flexi_page',
                    'label' => __('Edit Flexi Post Page', 'flexi'),
                    'description' => __('Page with shortcode [flexi-form] with edit="true" as parameter. Lets visitor to edit submitted post.', 'flexi') . ' ' . $edit_help,
                    'type' => 'pages',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'update_title',
                    'label' => __('Disable title', 'flexi'),
                    'description' => __('This will restrict field to get updated while using', 'flexi') . ' "' . __('Edit Flexi Post Page', 'flexi') . '"',
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'update_cate',
                    'label' => __('Disable category', 'flexi'),
                    'description' => __('This will restrict field to get updated while using', 'flexi') . ' "' . __('Edit Flexi Post Page', 'flexi') . '"',
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'update_tag',
                    'label' => __('Disable tags', 'flexi'),
                    'description' => __('This will restrict field to get updated while using', 'flexi') . ' "' . __('Edit Flexi Post Page', 'flexi') . '"',
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),

            ),
            'flexi_icon_settings' => array(
                array(
                    'name' => 'edit_flexi_icon',
                    'label' => __('Edit submission button', 'flexi') . '<span class="dashicons dashicons-edit"></span>',
                    'description' => __('Edit icon at gallery & detail page.', 'flexi') . ' ' . $edit_help,
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'delete_flexi_icon',
                    'label' => __('Delete submission button', 'flexi') . '<span class="dashicons dashicons-trash"></span>',
                    'description' => __('Trash icon at gallery & detail page.', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'user_flexi_icon',
                    'label' => __('User gallery button', 'flexi') . '<span class="dashicons dashicons-admin-users"></span>',
                    'description' => __('Profile icon at gallery & detail page.', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'download_flexi_icon',
                    'label' => __('Download media button', 'flexi') . '<span class="dashicons dashicons-download"></span>',
                    'description' => __('Download icon at gallery & detail page.', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
            ),
            'flexi_detail_settings' => array(
                array(
                    'name' => 'lightbox_switch',
                    'label' => __('Enable Lightbox or Popup', 'flexi'),
                    'description' => __('If popup is unchecked, It will open content in single dedicated page.', 'flexi') . ' ' . $popup_help,
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),
                array(
                    'name' => 'popup_style',
                    'label' => __('Popup style', 'flexi'),
                    'description' => __('Layout of lightbox', 'flexi'),
                    'type' => 'select',
                    'options' => array(
                        'on' => __('Regular', 'flexi'),
                        'simple' => __('Simple', 'flexi'),
                        'simple_info' => __('Simple with info', 'flexi'),
                        'custom' => __('Custom', 'flexi'),
                    ),
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'detail_layout',
                    'label' => __('Select Detail Layout', 'flexi'),
                    'description' => __('Selected layout will be used as default layout, if not specified in shortcode parameter.', 'flexi'),
                    'type' => 'layout',
                    'step' => 'detail', //step is used to select folder
                    'sanitize_callback' => 'sanitize_key',
                ),

            ),
            'flexi_categories_settings' => array(
                array(
                    'name' => 'global_album',
                    'label' => __('Default Post Category', 'flexi'),
                    'description' => __('This category will be selected if no category is assigned by visitor while submitting form.', 'flexi'),
                    'type' => 'category',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'category_page',
                    'label' => __('Category Page', 'flexi'),
                    'description' => __('Page which will be used to display albums. Should contain [flexi-category] as shortcode.', 'flexi') . ' ' . $category_help,
                    'type' => 'pages',
                    'sanitize_callback' => 'sanitize_key',
                ),
                array(
                    'name' => 'category_count',
                    'label' => __('Category count', 'flexi'),
                    'description' => __('Display post count of specific category at category page.', 'flexi'),
                    'type' => 'checkbox',
                    'sanitize_callback' => 'intval',
                ),

            ),

            'flexi_media_settings' => array(
                array(
                    'name' => 't_width',
                    'name2' => 't_height',
                    'label' => __('Thumbnail size', 'flexi'),
                    'label_1' => __('Width', 'flexi'),
                    'label_2' => __('Height', 'flexi'),
                    'description' => __('Applied at gallery page', 'flexi'),
                    'type' => 'double_input',
                    'type_2' => 'number',
                    'max' => '500',
                    'min' => '50',
                    'step' => '1',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                /*
                array(
                'name'              => 'crop_thumbnail',
                'label'             => __('', 'flexi'),
                'description'       => __('Crop thumbnail to exact dimensions (normally thumbnails are proportional)', 'flexi'),
                'type'              => 'checkbox',
                'sanitize_callback' => 'intval',
                ),
                 */
                array(
                    'name' => 'm_width',
                    'name2' => 'm_height',
                    'label' => __('Medium size', 'flexi'),
                    'label_1' => __('Width', 'flexi'),
                    'label_2' => __('Height', 'flexi'),
                    'description' => 'Pixel size',
                    'type' => 'double_input',
                    'type_2' => 'number',
                    'max' => '1024',
                    'min' => '200',
                    'step' => '1',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                array(
                    'name' => 'l_width',
                    'name2' => 'l_height',
                    'label' => __('Large size', 'flexi'),
                    'label_1' => __('Width', 'flexi'),
                    'label_2' => __('Height', 'flexi'),
                    'description' => __('Specially applied at detail page', 'flexi'),
                    'type' => 'double_input',
                    'type_2' => 'number',
                    'max' => '1500',
                    'min' => '300',
                    'step' => '1',
                    'sanitize_callback' => 'sanitize_text_field',
                ),

            ),

            'flexi_permalink_settings' => array(
                array(
                    'name' => 'slug',
                    'label' => __('Image Detail Page', 'flexi'),
                    'description' => __('Replaces the SLUG value used by custom post type "flexi".', 'flexi'),
                    'type' => 'text',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
            'flexi_extension' => array(),
        );

        return apply_filters('flexi_settings_fields', $fields);
    }

    /**
     * Initialize and registers the settings sections and fields to WordPress.
     *
     * @since 1.0.0
     */
    public function initialize_settings()
    {
        // Register settings sections & fields
        foreach ($this->sections as $section) {
            $page_hook = $section['id'];

            // Sections
            if (false == get_option($section['id'])) {
                add_option($section['id']);
            }

            if (isset($section['description']) && !empty($section['description'])) {
                $callback = array($this, 'settings_section_callback');
            } elseif (isset($section['callback'])) {
                $callback = $section['callback'];
            } else {
                $callback = null;
            }

            add_settings_section($section['id'], $section['title'], $callback, $page_hook);

            // Fields
            $fields = $this->fields[$section['id']];

            foreach ($fields as $option) {
                $name = $option['name'];
                $type = isset($option['type']) ? $option['type'] : 'text';
                $label = isset($option['label']) ? $option['label'] : '';
                $callback = isset($option['callback']) ? $option['callback'] : array($this, 'callback_' . $type);
                $args = array(
                    'id' => $name,
                    'class' => isset($option['class']) ? $option['class'] : $name,
                    'label_for' => "{$section['id']}[{$name}]",
                    'description' => isset($option['description']) ? $option['description'] : '',
                    'name' => $label,
                    'section' => $section['id'],
                    'size' => isset($option['size']) ? $option['size'] : null,
                    'options' => isset($option['options']) ? $option['options'] : '',
                    'sanitize_callback' => isset($option['sanitize_callback']) ? $option['sanitize_callback'] : '',
                    'type' => $type,
                    'placeholder' => isset($option['placeholder']) ? $option['placeholder'] : '',
                    'min' => isset($option['min']) ? $option['min'] : '',
                    'max' => isset($option['max']) ? $option['max'] : '',
                    'step' => isset($option['step']) ? $option['step'] : '',
                    'name2' => isset($option['name2']) ? $option['name2'] : '',
                    'label_1' => isset($option['label_1']) ? $option['label_1'] : '',
                    'label_2' => isset($option['label_2']) ? $option['label_2'] : '',
                    'type_2' => isset($option['type_2']) ? $option['type_2'] : '',
                );

                add_settings_field("{$section['id']}[{$name}]", $label, $callback, $page_hook, $section['id'], $args);
            }

            // Creates our settings in the options table
            register_setting($page_hook, $section['id'], array($this, 'sanitize_options'));
        }
    }

    /**
     * gallerys a section description.
     *
     * @since 1.0.0
     * @param array $args Settings section args.
     */
    public function settings_section_callback($args)
    {
        foreach ($this->sections as $section) {
            if ($section['id'] == $args['id']) {
                printf('<div class="inside">%s</div>', '<div class="flexi_card">' . $section['description'] . '</div>');
                break;
            }
        }
    }

    public function allowed_html($html)
    {
        $allowed_html = array(
            'input' => array(
                'type' => array(),
                'name' => array(),
                'value' => array(),
                'checked' => array(),
                'class' => array(),
                'id' => array(),
                'min' => array(),
                'max' => array(),
                'step' => array(),
                'checked' => array(),
            ),
            'p' => array(
                'class' => array(),
            ),
            'pre' => array(
                'class' => array(),
            ),
            'select' => array(
                'class' => array(),
                'name' => array(),
                'id' => array(),
            ),
            'option' => array(
                'value' => array(),
                'selected' => array(),
            ),
            'a' => array(
                'href' => array(),
                'title' => array(),
                'style' => array(),
                'target' => array(),
            ),
            'textarea' => array(
                'rows' => array(),
                'id' => array(),
                'class' => array(),
                'cols' => array(),
                'name' => array(),
            ),
            'span' => array(
                'class' => array(),
            ),
            'label' => array(
                'for' => array(),
            ),
            'img' => array(
                'title' => array(),
                'src' => array(),
                'alt' => array(),
                'class' => array(),
                'id' => array(),
                'size' => array(),
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'fieldset' => array(),
        );
        return wp_kses($html, $allowed_html);
    }
    /**
     * gallerys a text field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_text($args)
    {
        $value = esc_attr($this->get_option($args['id'], $args['section'], ''));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $type = isset($args['type']) ? $args['type'] : 'text';
        $placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';

        $html = sprintf('<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    //Image display
    public function callback_image($args)
    {

        $src = FLEXI_ROOT_URL;
        $html = sprintf('<img src="' . $src . '%1$s" class="%2$s" id="%3$s" size="%4$s"/>', $args['id'], $args['class'], $args['id'], $args['size']);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a url field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_url($args)
    {
        $this->callback_text($args);
    }

    /**
     * gallerys a number field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_number($args)
    {
        $value = esc_attr($this->get_option($args['id'], $args['section'], 0));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $type = isset($args['type']) ? $args['type'] : 'number';
        $placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';
        $min = empty($args['min']) ? '' : ' min="' . $args['min'] . '"';
        $max = empty($args['max']) ? '' : ' max="' . $args['max'] . '"';
        $step = empty($args['max']) ? '' : ' step="' . $args['step'] . '"';

        $html = sprintf('<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder, $min, $max, $step);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    public function callback_double_input($args)
    {
        $value = esc_attr($this->get_option($args['id'], $args['section'], 0));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $type = isset($args['type']) ? 'text' : 'number';
        $placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';
        $min = empty($args['min']) ? '' : ' min="' . $args['min'] . '"';
        $max = empty($args['max']) ? '' : ' max="' . $args['max'] . '"';
        $step = empty($args['max']) ? '' : ' step="' . $args['step'] . '"';
        $name2 = empty($args['name2']) ? '' : $args['name2'];
        $label_1 = empty($args['label_1']) ? '' : $args['label_1'];
        $label_2 = empty($args['label_2']) ? '' : $args['label_2'];
        $type_2 = empty($args['type_2']) ? '' : $args['type_2'];

        $t_width = flexi_get_option($args['id'], $args['section'], 0);
        $t_height = flexi_get_option($name2, $args['section'], 0);

        $html = $label_1 . " " . sprintf('<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/> ', $type_2, $size, $args['section'], $args['id'], $t_width, $placeholder, $min, $max, $step);
        $html .= $label_2 . " " . sprintf('<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/> ', $type_2, $size, $args['section'], $name2, $t_height, $placeholder, $min, $max, $step);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a checkbox for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_checkbox($args)
    {
        $value = esc_attr($this->get_option($args['id'], $args['section'], 0));

        $html = '<fieldset>';
        $html .= sprintf('<label for="%1$s[%2$s]">', $args['section'], $args['id']);
        $html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="0" />', $args['section'], $args['id']);
        $html .= sprintf('<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="1" %3$s />', $args['section'], $args['id'], checked($value, 1, false));
        $html .= sprintf('%1$s</label>', $args['description']);
        $html .= '</fieldset>';

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a multicheckbox for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_multicheck($args)
    {
        $value = $this->get_option($args['id'], $args['section'], array());

        $html = '<fieldset>';
        $html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="" />', $args['section'], $args['id']);
        foreach ($args['options'] as $key => $label) {
            $checked = in_array($key, $value) ? 'checked="checked"' : '';
            $html .= sprintf('<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key);
            $html .= sprintf('<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, $checked);
            $html .= sprintf('%1$s</label><br>', $label);
        }
        $html .= $this->get_field_description($args);
        $html .= '</fieldset>';

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a radio button for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_radio($args)
    {
        $value = $this->get_option($args['id'], $args['section'], '');

        $html = '<fieldset>';
        foreach ($args['options'] as $key => $label) {
            $html .= sprintf('<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key);
            $html .= sprintf('<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked($value, $key, false));
            $html .= sprintf('%1$s</label><br>', $label);
        }
        $html .= $this->get_field_description($args);
        $html .= '</fieldset>';

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a selectbox for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_select($args)
    {
        $value = esc_attr($this->get_option($args['id'], $args['section'], ''));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

        $html = sprintf('<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id']);
        foreach ($args['options'] as $key => $label) {
            $html .= sprintf('<option value="%s"%s>%s</option>', $key, selected($value, $key, false), $label);
        }
        $html .= sprintf('</select>');
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * layout selection a selectbox for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_layout($args)
    {
        $dropdown_args = array(
            'show_option_none' => '-- ' . __('Select layout', 'flexi') . ' --',
            'option_none_value' => '',
            'selected' => esc_attr($this->get_option($args['id'], $args['section'], '')),
            'name' => $args['section'] . '[' . $args['id'] . ']',
            'id' => $args['section'] . '[' . $args['id'] . ']',
            'echo' => 0,
            'folder' => isset($args['step']) && !is_null($args['step']) ? $args['step'] : 'gallery',

        );
        // echo $args['name'] . "--------";
        //var_dump($args);
        $html = flexi_layout_list($dropdown_args);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a textarea for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_textarea($args)
    {
        $value = esc_textarea($this->get_option($args['id'], $args['section'], ''));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $placeholder = empty($args['placeholder']) ? '' : ' placeholder="' . $args['placeholder'] . '"';

        $html = sprintf('<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>', $size, $args['section'], $args['id'], $placeholder, $value);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * gallerys the html for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_html($args)
    {
        echo $this->get_field_description($args);
    }

    /**
     * gallerys a rich text textarea for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_wysiwyg($args)
    {
        $value = $this->get_option($args['id'], $args['section'], '');
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : '500px';

        echo '<div style="max-width: ' . esc_attr($size) . ';">';
        $editor_settings = array(
            'teeny' => true,
            'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
            'textarea_rows' => 10,
        );
        if (isset($args['options']) && is_array($args['options'])) {
            $editor_settings = array_merge($editor_settings, $args['options']);
        }
        wp_editor($value, $args['section'] . '-' . $args['id'], $editor_settings);
        echo '</div>';
        echo $this->get_field_description($args);
    }

    /**
     * gallerys a file upload field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_file($args)
    {
        $value = esc_attr($this->get_option($args['id'], $args['section'], ''));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $id = $args['section'] . '[' . $args['id'] . ']';
        $label = isset($args['options']['button_label']) ? $args['options']['button_label'] : __('Choose File', 'flexi');

        $html = sprintf('<input type="text" class="%1$s-text flexi-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
        $html .= '<input type="button" class="button flexi-browse" value="' . $label . '" />';
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a password field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_password($args)
    {
        $value = esc_attr($this->get_option($args['id'], $args['section'], ''));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

        $html = sprintf('<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a color picker field for a settings field.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_color($args)
    {
        $value = esc_attr($this->get_option($args['id'], $args['section'], '#ffffff'));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

        $html = sprintf('<input type="text" class="%1$s-text flexi-color-picker" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, '#ffffff');
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * gallerys a select box for creating the pages select box.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_pages($args)
    {
        $dropdown_args = array(
            'show_option_none' => '-- ' . __('Select a page', 'flexi') . ' --',
            'option_none_value' => '',
            'selected' => esc_attr($this->get_option($args['id'], $args['section'], '')),
            'name' => $args['section'] . '[' . $args['id'] . ']',
            'id' => $args['section'] . '[' . $args['id'] . ']',
            'echo' => 0,
        );

        $html = wp_dropdown_pages($dropdown_args);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * List categories
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function callback_category($args)
    {
        $dropdown_args = array(
            'show_option_none' => '-- ' . __('Select category', 'flexi') . ' --',
            'option_none_value' => '',
            'selected' => esc_attr($this->get_option($args['id'], $args['section'], '')),
            'name' => $args['section'] . '[' . $args['id'] . ']',
            'id' => $args['section'] . '[' . $args['id'] . ']',
            'echo' => 0,
            'show_count' => 1,
            'hierarchical' => 1,
            'taxonomy' => 'flexi_category',
            'value_field' => 'slug',
            'hide_empty' => 0,

        );

        $html = wp_dropdown_categories($dropdown_args);
        $html .= $this->get_field_description($args);

        echo $this->allowed_html($html);
    }

    /**
     * Get field description for gallery.
     *
     * @since 1.0.0
     * @param array $args Settings field args.
     */
    public function get_field_description($args)
    {
        if (!empty($args['description'])) {
            if ('wysiwyg' == $args['type']) {
                $description = sprintf('<pre>%s</pre>', $args['description']);
            } else {
                $description = sprintf('<p class="description">%s</p>', $args['description']);
            }
        } else {
            $description = '';
        }

        return $description;
    }

    /**
     * Sanitize callback for Settings API.
     *
     * @since  1.0.0
     * @param  array $options The unsanitized collection of options.
     * @return                The collection of sanitized values.
     */
    public function sanitize_options($options)
    {
        if (!$options) {
            return $options;
        }

        foreach ($options as $option_slug => $option_value) {
            $sanitize_callback = $this->get_sanitize_callback($option_slug);

            // If callback is set, call it
            if ($sanitize_callback) {
                $options[$option_slug] = call_user_func($sanitize_callback, $option_value);
                continue;
            }
        }

        return $options;
    }

    /**
     * Get sanitization callback for given option slug.
     *
     * @since  1.0.0
     * @param  string $slug Option slug.
     * @return mixed        String or bool false.
     */
    public function get_sanitize_callback($slug = '')
    {
        if (empty($slug)) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback
        foreach ($this->fields as $section => $options) {
            foreach ($options as $option) {
                if ($option['name'] != $slug) {
                    continue;
                }

                // Return the callback name
                return isset($option['sanitize_callback']) && is_callable($option['sanitize_callback']) ? $option['sanitize_callback'] : false;
            }
        }

        return false;
    }

    /**
     * Get the value of a settings field.
     *
     * @since  1.0.0
     * @param  string $option  Settings field name.
     * @param  string $section The section name this field belongs to.
     * @param  string $default Default text if it's not found.
     * @return string
     */
    public function get_option($option, $section, $default = '')
    {
        $options = get_option($section);

        if (!empty($options[$option])) {
            return $options[$option];
        }

        return $default;
    }
}