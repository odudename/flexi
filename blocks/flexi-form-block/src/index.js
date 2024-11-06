import { __ } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";
import ServerSideRender from "@wordpress/server-side-render";
import { useSelect } from "@wordpress/data";
import { Fragment } from "@wordpress/element";
import { useEffect } from "@wordpress/element";
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
	TextControl,
} from "@wordpress/components";

registerBlockType("create-block/flexi-form-block", {
	apiVersion: 2,
	title: "Flexi Form Block",
	category: "flexi",
	icon: "format-gallery",
	description: "A form for Flexi gallery block with various settings.",
	supports: {
		html: false,
	},
	attributes: {
		enable_ajax: {
			type: "boolean",
			default: true,
		},
		flexi_type: {
			type: "string",
			default: "image",
		},
		form_class: {
			type: "string",
			default: "flexi_form_style",
		},
		form_title: {
			type: "string",
			default: "My Form",
		},
		title_label: {
			type: "string",
			default: "Title",
		},
		title_placeholder: {
			type: "string",
			default: "",
		},
		button_label: {
			type: "string",
			default: "Submit",
		},
		category_label: {
			type: "string",
			default: "Select Category",
		},
		cat: {
			type: "number",
			default: 0,
		},
		tag_label: {
			type: "string",
			default: "Insert Tag",
		},
		desp_label: {
			type: "string",
			default: "Description",
		},
		desp_placeholder: {
			type: "string",
			default: "",
		},
		enable_tag: {
			type: "boolean",
			default: false,
		},
		enable_desp: {
			type: "boolean",
			default: false,
		},
		enable_category: {
			type: "boolean",
			default: false,
		},
		enable_file: {
			type: "boolean",
			default: false,
		},
		enable_bulk_file: {
			type: "boolean",
			default: false,
		},
		file_label: {
			type: "string",
			default: "Select File",
		},
		url_label: {
			type: "string",
			default: "Insert oEmbed URL",
		},
		enable_url: {
			type: "boolean",
			default: false,
		},
		enable_security: {
			type: "boolean",
			default: false,
		},
	},
	edit: ({ attributes, setAttributes }) => {
		const categoriesList = useSelect(
			(select) =>
				select("core").getEntityRecords("taxonomy", "flexi_category", {
					per_page: -1,
				}),
			[],
		);

		// Build the category options array
		const buildCategoryOptions = (categories) => {
			const categoryOptions = [{ label: "-- Select All --", value: 0 }];

			if (categories && categories.length > 0) {
				categories.forEach((category) => {
					categoryOptions.push({
						label: category.name,
						value: category.id,
					});
				});
			}

			return categoryOptions;
		};

		const categories = buildCategoryOptions(categoriesList || []);

		// Debugging: Log categories when categoriesList updates
		useEffect(() => {
			console.log("Updated Categories:", categories);
		}, [categoriesList]);

		const blockProps = useBlockProps();

		return (
			<Fragment>
				<div {...blockProps}>
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
									{
										label: "Supported Files",
										value: "image",
									},
									{
										label: "oEmbed URL",
										value: "url",
									},
								]}
								onChange={(value) => setAttributes({ flexi_type: value })}
							/>

							<TextControl
								label="Internal Form Title"
								value={attributes.form_title}
								onChange={(form_title) => setAttributes({ form_title })}
							/>

							<SelectControl
								label="Form Class Style"
								value={attributes.form_class}
								options={[
									{
										label: "Stacked",
										value: "flexi_form_style",
									},
								]}
								onChange={(value) => setAttributes({ form_class: value })}
							/>

                    </PanelBody>
					<PanelBody title={__("Title Field", "flexi")} initialOpen={false}>
							<TextControl
								label="Label of Form Title"
								value={attributes.title_label}
								onChange={(title_label) => setAttributes({ title_label })}
							/>
							<TextControl
								label="Title Placeholder"
								value={attributes.title_placeholder}
								onChange={(title_placeholder) => setAttributes({ title_placeholder })}
							/>
						</PanelBody>
						<PanelBody
							title={__("Description Field", "flexi")}
							initialOpen={false}
						>
							<ToggleControl
								label="Enable Description"
								checked={attributes.enable_desp}
								onChange={(enable_desp) => setAttributes({ enable_desp })}
							/>
							{attributes.enable_desp && (
								<TextControl
									label="Description Title"
									value={attributes.desp_label}
									onChange={(desp_label) => setAttributes({ desp_label })}
								/>
							)}
							{attributes.enable_desp && (
								<TextControl
									label="Description Placeholder"
									value={attributes.desp_placeholder}
									onChange={(desp_placeholder) => setAttributes({ desp_placeholder })}
								/>
							)}
						</PanelBody>
						<PanelBody
							title={__("Category Field", "flexi")}
							initialOpen={false}
						>
							<ToggleControl
								label="Enable Category"
								checked={attributes.enable_category}
								onChange={(enable_category) => setAttributes({ enable_category })}
							/>
							{attributes.enable_category && (
								<TextControl
									label="Category Title"
									value={attributes.category_label}
									onChange={(category_label) => setAttributes({ category_label })}
								/>
							)}
							{attributes.enable_category && (
								<SelectControl
								label="Select ParentCategory"
								value={attributes.cat}
								options={categories}
								onChange={(value) => setAttributes({ cat: Number(value) })}
							/>
							)}
						</PanelBody>
						<PanelBody title={__("Tag Field", "flexi")} initialOpen={false}>
							<ToggleControl
								label="Enable Tag"
								checked={attributes.enable_tag}
								onChange={(enable_tag) => setAttributes({ enable_tag })}
							/>
							{attributes.enable_tag && (
								<TextControl
									label="Tag Title"
									value={attributes.tag_label}
									onChange={(tag_label) => setAttributes({ tag_label })}
								/>
							)}
						</PanelBody>
						{attributes.flexi_type == "image" && (
							<PanelBody
								title={__("Image Upload Field", "flexi")}
								initialOpen={false}
							>
								<ToggleControl
									label="Enable Image Upload"
									checked={attributes.enable_file}
									onChange={(enable_file) => setAttributes({ enable_file })}
								/>
								{attributes.enable_file && (
									<ToggleControl
										label="Enable Bulk File Upload"
										checked={attributes.enable_bulk_file}
										onChange={(enable_bulk_file) => setAttributes({ enable_bulk_file })}
									/>
								)}
								{attributes.enable_file && (
									<TextControl
										label="Upload Title"
										value={attributes.file_label}
										onChange={(file_label) => setAttributes({ file_label })}
									/>
								)}
							</PanelBody>
							
						)}
						{attributes.flexi_type == "url" && (
							<PanelBody
								title={__("URL Submit Field", "flexi")}
								initialOpen={false}
							>
								<ToggleControl
									label="Enable oEmbed URL"
									checked={attributes.enable_url}
									onChange={(enable_url) => setAttributes({ enable_url })}
								/>
								{attributes.enable_url && (
									<TextControl
										label="URL Field Title"
										value={attributes.url_label}
										onChange={(url_label) => setAttributes({ url_label })}
									/>
								)}
							</PanelBody>
						)}
						<PanelBody
							title={__("Security reCaptcha Field", "flexi")}
							initialOpen={false}
						>
							<ToggleControl
								label="Enable Google reCaptcha"
								checked={attributes.enable_security}
								onChange={(enable_security) => setAttributes({ enable_security })}
							/>
						</PanelBody>
						<PanelBody title={__("Submit Button", "flexi")} initialOpen={false}>
							<TextControl
								label="Label of Submit Button"
								value={attributes.button_label}
								onChange={(button_label) => setAttributes({ button_label })}
							/>
						</PanelBody>

                </InspectorControls>

					<ServerSideRender
						block="create-block/flexi-form-block"
						attributes={attributes}
					/>
				</div>
			</Fragment>
		);
	},
	save: () => null, // Server-side rendering; no frontend output needed here
});
