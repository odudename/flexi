(()=>{"use strict";var e={n:l=>{var t=l&&l.__esModule?()=>l.default:()=>l;return e.d(t,{a:t}),t},d:(l,t)=>{for(var a in t)e.o(t,a)&&!e.o(l,a)&&Object.defineProperty(l,a,{enumerable:!0,get:t[a]})},o:(e,l)=>Object.prototype.hasOwnProperty.call(e,l)};const l=window.wp.i18n,t=window.wp.blocks,a=window.wp.blockEditor,o=window.wp.serverSideRender;var n=e.n(o);const r=window.wp.data,i=window.wp.element,s=window.wp.components,d=window.ReactJSXRuntime;(0,t.registerBlockType)("create-block/flexi-form-block",{apiVersion:2,title:"Flexi Form Block",category:"media",icon:"format-gallery",description:"A form for Flexi gallery block with various settings.",supports:{html:!1},attributes:{enable_ajax:{type:"boolean",default:!0},flexi_type:{type:"string",default:"image"},form_class:{type:"string",default:"flexi_form_style"},form_title:{type:"string",default:"My Form"},title_label:{type:"string",default:"Title"},title_placeholder:{type:"string",default:""},button_label:{type:"string",default:"Submit"},category_label:{type:"string",default:"Select Category"},cat:{type:"number",default:0},tag_label:{type:"string",default:"Insert Tag"},desp_label:{type:"string",default:"Description"},desp_placeholder:{type:"string",default:""},enable_tag:{type:"boolean",default:!1},enable_desp:{type:"boolean",default:!1},enable_category:{type:"boolean",default:!1},enable_file:{type:"boolean",default:!1},enable_bulk_file:{type:"boolean",default:!1},file_label:{type:"string",default:"Select File"},url_label:{type:"string",default:"Insert oEmbed URL"},enable_url:{type:"boolean",default:!1},enable_security:{type:"boolean",default:!1}},edit:({attributes:e,setAttributes:t})=>{const o=(0,r.useSelect)((e=>e("core").getEntityRecords("taxonomy","flexi_category",{per_page:-1})),[]),b=(e=>{const l=[{label:"-- Select All --",value:0}];return e&&e.length>0&&e.forEach((e=>{l.push({label:e.name,value:e.id})})),l})(o||[]);(0,i.useEffect)((()=>{console.log("Updated Categories:",b)}),[o]);const p=(0,a.useBlockProps)();return(0,d.jsx)(i.Fragment,{children:(0,d.jsxs)("div",{...p,children:[(0,d.jsxs)(a.InspectorControls,{children:[(0,d.jsxs)(s.PanelBody,{title:(0,l.__)("Form Settings","flexi"),initialOpen:!1,children:[(0,d.jsx)(s.ToggleControl,{label:"Enable Ajax Submission",checked:e.enable_ajax,onChange:e=>t({enable_ajax:e})}),(0,d.jsx)(s.SelectControl,{label:"Submission Content Type",value:e.flexi_type,options:[{label:"Supported Files",value:"image"},{label:"oEmbed URL",value:"url"}],onChange:e=>t({flexi_type:e})}),(0,d.jsx)(s.TextControl,{label:"Internal Form Title",value:e.form_title,onChange:e=>t({form_title:e})}),(0,d.jsx)(s.SelectControl,{label:"Form Class Style",value:e.form_class,options:[{label:"Stacked",value:"flexi_form_style"}],onChange:e=>t({form_class:e})})]}),(0,d.jsxs)(s.PanelBody,{title:(0,l.__)("Title Field","flexi"),initialOpen:!1,children:[(0,d.jsx)(s.TextControl,{label:"Label of Form Title",value:e.title_label,onChange:e=>t({title_label:e})}),(0,d.jsx)(s.TextControl,{label:"Title Placeholder",value:e.title_placeholder,onChange:e=>t({title_placeholder:e})})]})]}),(0,d.jsx)(n(),{block:"create-block/flexi-form-block",attributes:e})]})})},save:()=>null})})();