import { InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, ToggleControl, RangeControl, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const Edit = ( { attributes, setAttributes } ) => {
    return (
        <div>
            {/* Inspector Controls for the block settings */}
            <InspectorControls>
                <PanelBody title={ __( 'Gallery Settings', 'flexi-gallery-block' ) }>
                    <RangeControl
                        label={ __( 'Columns', 'flexi-gallery-block' ) }
                        value={ attributes.column }
                        onChange={ ( column ) => setAttributes( { column } ) }
                        min={ 1 }
                        max={ 6 }
                    />
                    <RangeControl
                        label={ __( 'Items per Page', 'flexi-gallery-block' ) }
                        value={ attributes.perpage }
                        onChange={ ( perpage ) => setAttributes( { perpage } ) }
                        min={ 1 }
                        max={ 20 }
                    />
                    <ToggleControl
                        label={ __( 'Enable Popup', 'flexi-gallery-block' ) }
                        checked={ attributes.popup }
                        onChange={ ( popup ) => setAttributes( { popup } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Title', 'flexi-gallery-block' ) }
                        checked={ attributes.evalue_title }
                        onChange={ ( evalue_title ) => setAttributes( { evalue_title } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Excerpt', 'flexi-gallery-block' ) }
                        checked={ attributes.evalue_excerpt }
                        onChange={ ( evalue_excerpt ) => setAttributes( { evalue_excerpt } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Category', 'flexi-gallery-block' ) }
                        checked={ attributes.evalue_category }
                        onChange={ ( evalue_category ) => setAttributes( { evalue_category } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Tag', 'flexi-gallery-block' ) }
                        checked={ attributes.evalue_tag }
                        onChange={ ( evalue_tag ) => setAttributes( { evalue_tag } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Count', 'flexi-gallery-block' ) }
                        checked={ attributes.evalue_count }
                        onChange={ ( evalue_count ) => setAttributes( { evalue_count } ) }
                    />
                    <ToggleControl
                        label={ __( 'Enable Like Button', 'flexi-gallery-block' ) }
                        checked={ attributes.evalue_like }
                        onChange={ ( evalue_like ) => setAttributes( { evalue_like } ) }
                    />
                    <ToggleControl
                        label={ __( 'Enable Unlike Button', 'flexi-gallery-block' ) }
                        checked={ attributes.evalue_unlike }
                        onChange={ ( evalue_unlike ) => setAttributes( { evalue_unlike } ) }
                    />
                    <SelectControl
                        label={ __( 'Filter By', 'flexi-gallery-block' ) }
                        value={ attributes.filter }
                        options={ [
                            { label: 'None', value: 'none' },
                            { label: 'Category', value: 'category' },
                            { label: 'Tag', value: 'tag' },
                        ] }
                        onChange={ ( filter ) => setAttributes( { filter } ) }
                    />
                    <SelectControl
                        label={ __( 'Order By', 'flexi-gallery-block' ) }
                        value={ attributes.orderby }
                        options={ [
                            { label: 'Date', value: 'date' },
                            { label: 'Title', value: 'title' },
                        ] }
                        onChange={ ( orderby ) => setAttributes( { orderby } ) }
                    />
                    <SelectControl
                        label={ __( 'Layout Style', 'flexi-gallery-block' ) }
                        value={ attributes.layout }
                        options={ [
                            { label: 'Masonry', value: 'masonry' },
                            { label: 'Grid', value: 'grid' },
                        ] }
                        onChange={ ( layout ) => setAttributes( { layout } ) }
                    />
                    <SelectControl
                        label={ __( 'Popup Style', 'flexi-gallery-block' ) }
                        value={ attributes.popup_style }
                        options={ [
                            { label: 'On', value: 'on' },
                            { label: 'Off', value: 'off' },
                        ] }
                        onChange={ ( popup_style ) => setAttributes( { popup_style } ) }
                    />
                    <SelectControl
                        label={ __( 'Hover Effect', 'flexi-gallery-block' ) }
                        value={ attributes.hover_effect }
                        options={ [
                            { label: 'None', value: '' },
                            { label: 'Zoom', value: 'zoom' },
                            { label: 'Grayscale', value: 'grayscale' },
                        ] }
                        onChange={ ( hover_effect ) => setAttributes( { hover_effect } ) }
                    />
                    <SelectControl
                        label={ __( 'Hover Caption Style', 'flexi-gallery-block' ) }
                        value={ attributes.hover_caption }
                        options={ [
                            { label: 'None', value: 'flexi_caption_none' },
                            { label: 'Caption Overlay', value: 'flexi_caption_overlay' },
                        ] }
                        onChange={ ( hover_caption ) => setAttributes( { hover_caption } ) }
                    />
                    <RangeControl
                        label={ __( 'Image Spacing', 'flexi-gallery-block' ) }
                        value={ attributes.image_spacing }
                        onChange={ ( image_spacing ) => setAttributes( { image_spacing } ) }
                        min={ 0 }
                        max={ 50 }
                    />
                    <ToggleControl
                        label={ __( 'Enable Lazy Load', 'flexi-gallery-block' ) }
                        checked={ attributes.lazy_load }
                        onChange={ ( lazy_load ) => setAttributes( { lazy_load } ) }
                    />
                    <SelectControl
                        label={ __( 'Image Size', 'flexi-gallery-block' ) }
                        value={ attributes.image_size }
                        options={ [
                            { label: 'Small', value: 'small' },
                            { label: 'Medium', value: 'medium' },
                            { label: 'Large', value: 'large' },
                        ] }
                        onChange={ ( image_size ) => setAttributes( { image_size } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Navigation Arrows', 'flexi-gallery-block' ) }
                        checked={ attributes.show_navigation }
                        onChange={ ( show_navigation ) => setAttributes( { show_navigation } ) }
                    />
                    <SelectControl
                        label={ __( 'Navigation Style', 'flexi-gallery-block' ) }
                        value={ attributes.navigation_style }
                        options={ [
                            { label: 'Dots', value: 'dots' },
                            { label: 'Arrows', value: 'arrows' },
                        ] }
                        onChange={ ( navigation_style ) => setAttributes( { navigation_style } ) }
                    />
                    <ToggleControl
                        label={ __( 'Enable Filter', 'flexi-gallery-block' ) }
                        checked={ attributes.enable_filter }
                        onChange={ ( enable_filter ) => setAttributes( { enable_filter } ) }
                    />
                    <SelectControl
                        label={ __( 'Selected Filter', 'flexi-gallery-block' ) }
                        value={ attributes.selected_filter }
                        options={ [
                            { label: 'All', value: 'all' },
                            { label: 'Featured', value: 'featured' },
                        ] }
                        onChange={ ( selected_filter ) => setAttributes( { selected_filter } ) }
                    />
                    <ToggleControl
                        label={ __( 'Enable Caption', 'flexi-gallery-block' ) }
                        checked={ attributes.enable_caption }
                        onChange={ ( enable_caption ) => setAttributes( { enable_caption } ) }
                    />
                    <ToggleControl
                        label={ __( 'Enable Grid Layout', 'flexi-gallery-block' ) }
                        checked={ attributes.enable_grid }
                        onChange={ ( enable_grid ) => setAttributes( { enable_grid } ) }
                    />

                    {/* Additional controls for new attributes */}
                    <ToggleControl
                        label={ __( 'Show Tags', 'flexi-gallery-block' ) }
                        checked={ attributes.tag_show }
                        onChange={ ( tag_show ) => setAttributes( { tag_show } ) }
                    />
                    <RangeControl
                        label={ __( 'Width', 'flexi-gallery-block' ) }
                        value={ attributes.width }
                        onChange={ ( width ) => setAttributes( { width } ) }
                        min={ 0 }
                        max={ 1000 } // Adjust this range as needed
                    />
                    <RangeControl
                        label={ __( 'Height', 'flexi-gallery-block' ) }
                        value={ attributes.height }
                        onChange={ ( height ) => setAttributes( { height } ) }
                        min={ 0 }
                        max={ 1000 } // Adjust this range as needed
                    />
                    <TextControl
                        label={ __( 'Custom Value', 'flexi-gallery-block' ) }
                        value={ attributes.evalue_custom }
                        onChange={ ( evalue_custom ) => setAttributes( { evalue_custom } ) }
                    />
                    <TextControl
                        label={ __( 'Icon', 'flexi-gallery-block' ) }
                        value={ attributes.evalue_icon }
                        onChange={ ( evalue_icon ) => setAttributes( { evalue_icon } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show At Sidebar', 'flexi-gallery-block' ) }
                        checked={ attributes.at_sidebar }
                        onChange={ ( at_sidebar ) => setAttributes( { at_sidebar } ) }
                    />
                </PanelBody>
            </InspectorControls>

            {/* Render the gallery */}
            <ServerSideRender
                block="flexi-gallery-block/gallery"
                attributes={ attributes }
            />
        </div>
    );
};

export default Edit;
