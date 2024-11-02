import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    SelectControl,
    ToggleControl,
    TextControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';

registerBlockType('create-block/flexi-gallery-block', {
    title: 'Gallery Block',
    category: 'media',
    attributes: {
        columns: {
            type: 'number',
            default: 3,
        },
        cat: {
            type: 'number',
            default: 0,
        },
        tag: {
            type: 'string',
            default: '',
        },
        filter: {
            type: 'string',
            default: 'none',
        },
        orderby: {
            type: 'string',
            default: 'date',
        },
        layout: {
            type: 'string',
            default: 'masonry',
        },
        popup_style: {
            type: 'string',
            default: 'on',
        },
        column: {
            type: 'number',
            default: 3,
        },
        perpage: {
            type: 'number',
            default: 10,
        },
        padding: {
            type: 'number',
            default: 5,
        },
        popup: {
            type: 'boolean',
            default: false,
        },
        hover_effect: {
            type: 'string',
            default: '',
        },
        hover_caption: {
            type: 'string',
            default: 'flexi_caption_none',
        },
        evalue_title: {
            type: 'boolean',
            default: false,
        },
        evalue_excerpt: {
            type: 'boolean',
            default: false,
        },
        evalue_category: {
            type: 'boolean',
            default: false,
        },
        evalue_tag: {
            type: 'boolean',
            default: false,
        },
        evalue_count: {
            type: 'boolean',
            default: false,
        },
        evalue_like: {
            type: 'boolean',
            default: false,
        },
        evalue_unlike: {
            type: 'boolean',
            default: false,
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();

        return (
            <Fragment>
                <div {...blockProps}>
                    <InspectorControls>
                        <PanelBody title="Gallery Settings">
                            <SelectControl
                                label="Select Category"
                                value={attributes.cat}
                                options={[
                                    { label: "-- Select All --", value: 0 },
                                    // Add dynamic categories if available
                                ]}
                                onChange={(value) => setAttributes({ cat: Number(value) })}
                            />
                            <TextControl
                                label="Tag slug name separated by commas"
                                value={attributes.tag}
                                onChange={(tag) => setAttributes({ tag })}
                            />
                            <SelectControl
                                label="Filter gallery contains"
                                value={attributes.filter}
                                options={[
                                    { label: "Show All", value: "none" },
                                    { label: "Image", value: "image" },
                                    { label: "Video URL", value: "url" },
                                    { label: "Audio File", value: "audio" },
                                    { label: "Other Documents", value: "other" },
                                ]}
                                onChange={(filter) => setAttributes({ filter })}
                            />
                            <SelectControl
                                label="Order By"
                                value={attributes.orderby}
                                options={[
                                    { label: "Title", value: "title" },
                                    { label: "Recent Date", value: "date" },
                                    { label: "Modified Date", value: "modified" },
                                    { label: "Flexi ID", value: "id" },
                                    { label: "Random", value: "rand" },
                                ]}
                                onChange={(orderby) => setAttributes({ orderby })}
                            />
                        </PanelBody>
                        <PanelBody title="Layout Controls">
                            <SelectControl
                                label="Select Layout"
                                value={attributes.layout}
                                options={[
                                    { label: "Masonry", value: "masonry" },
                                    { label: "Portfolio", value: "portfolio" },
                                    { label: "Regular", value: "regular" },
                                    { label: "Wide", value: "wide" },
                                    { label: "Basic", value: "basic" },
                                ]}
                                onChange={(layout) => setAttributes({ layout })}
                            />
                            <SelectControl
                                label="Popup Style"
                                value={attributes.popup_style}
                                options={[
                                    { label: "Regular", value: "on" },
                                    { label: "Simple", value: "simple" },
                                    { label: "Custom", value: "custom" },
                                ]}
                                onChange={(popup_style) => setAttributes({ popup_style })}
                            />
                            <RangeControl
                                label="Columns"
                                value={attributes.column}
                                onChange={(column) => setAttributes({ column })}
                                min={1}
                                max={5}
                            />
                            <RangeControl
                                label="Post Per Page"
                                value={attributes.perpage}
                                onChange={(perpage) => setAttributes({ perpage })}
                                min={1}
                                max={100}
                            />
                        </PanelBody>
                        <PanelBody title="Toggle Controls">
                            <ToggleControl
                                label="Enable Popup Lightbox"
                                checked={attributes.popup}
                                onChange={(popup) => setAttributes({ popup })}
                            />
                            <ToggleControl
                                label="Display title"
                                checked={attributes.evalue_title}
                                onChange={(evalue_title) => setAttributes({ evalue_title })}
                            />
                            <ToggleControl
                                label="Display Excerpt"
                                checked={attributes.evalue_excerpt}
                                onChange={(evalue_excerpt) => setAttributes({ evalue_excerpt })}
                            />
                        </PanelBody>
                    </InspectorControls>
                    <p>Gallery Block--- columns</p>
                </div>
            </Fragment>
        );
    },
    save: () => null, // Server-side rendering; no frontend output needed here
});
