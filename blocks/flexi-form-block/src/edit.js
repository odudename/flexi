import { InspectorControls } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { PanelBody, ToggleControl, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const Edit = ( { attributes, setAttributes } ) => {
    return (
        <div>
            {/* Inspector Controls for the block settings */}
            <InspectorControls>
                <PanelBody title={__("Form Settings", "flexi")} initialOpen={false}>
                    <ToggleControl
                        label="Enable Ajax Submission"
                        checked={attributes.enable_ajax}
                        onChange={(enable_ajax) => setAttributes({ enable_ajax })}
                    />
                    <SelectControl
                        label="Submission Content Type"
                        value={attributes.flexi_type}
                        options={[
                            { label: "Supported Files", value: "image" },
                            { label: "oEmbed URL", value: "url" },
                        ]}
                        onChange={(value) => setAttributes({ flexi_type: value })}
                    />
                    <TextControl
                        label="Internal Form Title"
                        value={attributes.form_title}
                        onChange={(form_title) => setAttributes({ form_title })}
                    />
                </PanelBody>
                <PanelBody title={__("Title Field", "flexi")} initialOpen={false}>
                    <TextControl
                        label="Label of Form Title"
                        value={attributes.title_label}
                        onChange={(title_label) => setAttributes({ title_label })}
                    />
                </PanelBody>
                <PanelBody title={__("Description Field", "flexi")} initialOpen={false}>
                    <ToggleControl
                        label="Enable Description"
                        checked={attributes.enable_desp}
                        onChange={(enable_desp) => setAttributes({ enable_desp })}
                    />
                </PanelBody>
                {attributes.flexi_type === "image" && (
                    <PanelBody title={__("Image Upload Field", "flexi")} initialOpen={false}>
                        <ToggleControl
                            label="Enable Image Upload"
                            checked={attributes.enable_file}
                            onChange={(enable_file) => setAttributes({ enable_file })}
                        />
                    </PanelBody>
                )}
                {attributes.flexi_type === "url" && (
                    <PanelBody title={__("URL Submit Field", "flexi")} initialOpen={false}>
                        <ToggleControl
                            label="Enable oEmbed URL"
                            checked={attributes.enable_url}
                            onChange={(enable_url) => setAttributes({ enable_url })}
                        />
                    </PanelBody>
                )}
                <PanelBody title={__("Security reCaptcha Field", "flexi")} initialOpen={false}>
                    <ToggleControl
                        label="Enable Google reCaptcha"
                        checked={attributes.enable_security}
                        onChange={(enable_security) => setAttributes({ enable_security })}
                    />
                </PanelBody>
            </InspectorControls>
            {/* Render the gallery */}
            <ServerSideRender
                block="flexi-form-block/form"
                attributes={ attributes }
            />
        </div>
    );
};

export default Edit;
