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

registerBlockType("create-block/flexi-gallery-block", {
	apiVersion: 2,
	title: "Flexi Gallery Block",
	category: "flexi",
	icon: "format-gallery",
	description: "A FLEXI Gallery block with various settings.",
	supports: {
		html: false,
	},
	attributes: {
		column: {
			type: "number",
			default: 3,
		},
		cat: {
			type: "number",
			default: 0,
		},
		tag: {
			type: "string",
			default: "",
		},
		filter: {
			type: "string",
			default: "",
		},
		orderby: {
			type: "string",
			default: "date",
		},
		layout: {
			type: "string",
			default: "masonry",
		},
		popup_style: {
			type: "string",
			default: "on",
		},
		perpage: {
			type: "number",
			default: 10,
		},
		padding: {
			type: "number",
			default: 5,
		},
		hover_effect: {
			type: "string",
			default: "",
		},
		hover_caption: {
			type: "string",
			default: "flexi_caption_none",
		},
		evalue_title: {
			type: "boolean",
			default: false,
		},
		evalue_excerpt: {
			type: "boolean",
			default: false,
		},
		evalue_category: {
			type: "boolean",
			default: false,
		},
		evalue_tag: {
			type: "boolean",
			default: false,
		},
		evalue_count: {
			type: "boolean",
			default: false,
		},
		evalue_like: {
			type: "boolean",
			default: false,
		},
		evalue_unlike: {
			type: "boolean",
			default: false,
		},
		at_sidebar: {
			type: "boolean",
			default: true,
		},
		evalue_icon: {
			type: "boolean",
			default: true,
		},
		evalue_custom: {
			type: "boolean",
			default: false,
		},
		width: {
			type: "number",
			default: 150,
		},
		height: {
			type: "number",
			default: 150,
		},
		tag_show: {
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
						<PanelBody title="Gallery Settings">
							<SelectControl
								label="Select Category"
								value={attributes.cat}
								options={categories}
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
									{ label: "Show All", value: "" },
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
                                    { label: "Off", value: "off" },
								]}
								onChange={(popup_style) => setAttributes({ popup_style })}
							/>
							<SelectControl
									label="Image Hover Effect"
									value={attributes.hover_effect}
									options={[
										{
											label: "-- None --",
											value: "",
										},
										{
											label: "Blur",
											value: "flexi_effect_1",
										},
										{
											label: "Grayscale",
											value: "flexi_effect_2",
										},
										{
											label: "Zoom",
											value: "flexi_effect_3",
										},
									]}
									onChange={(value) => setAttributes({ hover_effect: value })}
								/>
								<SelectControl
									label="Image Hover Caption"
									value={attributes.hover_caption}
									options={[
										{
											label: "-- None --",
											value: "flexi_caption_none",
										},
										{
											label: "Slide Left",
											value: "flexi_caption_1",
										},
										{
											label: "Pull up with Info",
											value: "flexi_caption_2",
										},
										{
											label: "Slide Right with Info",
											value: "flexi_caption_3",
										},
										{
											label: "Pull Up",
											value: "flexi_caption_4",
										},
										{
											label: "Top & Bottom",
											value: "flexi_caption_5",
										},
									]}
									onChange={(value) => setAttributes({ hover_caption: value })}
								/>
							<RangeControl
								label="Columns"
								value={attributes.column}
								onChange={(column) => setAttributes({ column })}
								min={1}
								max={5}
							/>
									<RangeControl
								label="Padding"
								value={attributes.padding}
								onChange={(padding) => setAttributes({ padding })}
								min={1}
								max={10}
							/>
							<RangeControl
								label="Posts Per Page"
								value={attributes.perpage}
								onChange={(perpage) => setAttributes({ perpage })}
								min={1}
								max={100}
							/>
							<RangeControl
								label="Width"
								value={attributes.width}
								onChange={(width) => setAttributes({ width })}
								min={50}
								max={500}
							/>
							<RangeControl
								label="Height"
								value={attributes.height}
								onChange={(height) => setAttributes({ height })}
								min={50}
								max={500}
							/>
						</PanelBody>
						<PanelBody title="Toggle Controls">
							<ToggleControl
								label="Display Title"
								checked={attributes.evalue_title}
								onChange={(evalue_title) => setAttributes({ evalue_title })}
							/>
							<ToggleControl
								label="Display Excerpt"
								checked={attributes.evalue_excerpt}
								onChange={(evalue_excerpt) => setAttributes({ evalue_excerpt })}
							/>
							<ToggleControl
								label="Display Category"
								checked={attributes.evalue_category}
								onChange={(evalue_category) =>
									setAttributes({ evalue_category })
								}
							/>
							<ToggleControl
								label="Display Tag"
								checked={attributes.evalue_tag}
								onChange={(evalue_tag) => setAttributes({ evalue_tag })}
							/>
							<ToggleControl
								label="Display Count"
								checked={attributes.evalue_count}
								onChange={(evalue_count) => setAttributes({ evalue_count })}
							/>
							<ToggleControl
								label="Enable Like Button"
								checked={attributes.evalue_like}
								onChange={(evalue_like) => setAttributes({ evalue_like })}
							/>
							<ToggleControl
								label="Enable Unlike Button"
								checked={attributes.evalue_unlike}
								onChange={(evalue_unlike) => setAttributes({ evalue_unlike })}
							/>
							<ToggleControl
								label="Show Tags...."
								checked={attributes.tag_show}
								onChange={(tag_show) => setAttributes({ tag_show })}
							/>
                            	<ToggleControl
								label="Show Icon Grid"
								checked={attributes.evalue_icon}
								onChange={(evalue_icon) => setAttributes({ evalue_icon })}
							/>
						</PanelBody>
					</InspectorControls>
					<ServerSideRender
						block="create-block/flexi-gallery-block"
						attributes={attributes}
					/>
				</div>
			</Fragment>
		);
	},
	save: () => null, // Server-side rendering; no frontend output needed here
});
