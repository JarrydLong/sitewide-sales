!function(e){var t={};function l(s){if(t[s])return t[s].exports;var n=t[s]={i:s,l:!1,exports:{}};return e[s].call(n.exports,n,n.exports,l),n.l=!0,n.exports}l.m=e,l.c=t,l.d=function(e,t,s){l.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:s})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,t){if(1&t&&(e=l(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var s=Object.create(null);if(l.r(s),Object.defineProperty(s,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)l.d(s,n,function(t){return e[t]}.bind(null,n));return s},l.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(t,"a",t),t},l.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},l.p="",l(l.s=2)}([function(e,t){e.exports=window.wp.element},function(e,t){wp.i18n.setLocaleData({"":{}},"sitewide-sales")},function(e,t,l){"use strict";l.r(t);l(1);var s=l(0),n=wp.i18n.__,i=wp.blocks.registerBlockType,r=wp.components,a=r.PanelBody,o=r.SelectControl,c=wp.blockEditor,u=c.InspectorControls,d=c.InnerBlocks,p=(i("swsales/sale-content",{title:n("Sale Content","sitewide-sales"),description:n("Build your Sitewide Sale landing page with blocks. Place blocks within this section to conditionally show the content before, during, or after the sale.","sitewide-sales"),category:"swsales",icon:{background:"#FFFFFF",foreground:"#1A688B",src:"visibility"},keywords:[n("sale visibility","sitewide-sales"),n("before sale","sitewide-sales"),n("after sale","sitewide-sales"),n("sale content","sitewide-sales")],attributes:{period:{type:"string",default:""}},supports:{anchor:!0},edit:function(e){var t=e.attributes.period,l=e.setAttributes,i=e.isSelected;return[i&&Object(s.createElement)(u,null,Object(s.createElement)(a,null,Object(s.createElement)("p",null,Object(s.createElement)("strong",null,n("Sale Period","sitewide-sales"))),Object(s.createElement)(o,{value:t,help:n("Select the sale period this content is visible for.","sitewide-sales"),options:[{label:n("Before Sale","sitewide-sales"),value:"pre-sale"},{label:n("During Sale","sitewide-sales"),value:"sale"},{label:n("After Sale","sitewide-sales"),value:"post-sale"}],onChange:function(e){return l({period:e})}}))),i&&Object(s.createElement)("div",{className:"swsales-wrapper-block"},Object(s.createElement)("span",{className:"swsales-block-title"},n("Sitewide Sale Content","sitewide-sales")),Object(s.createElement)(d,{renderAppender:function(){return Object(s.createElement)(d.ButtonBlockAppender,null)},templateLock:!1})),!i&&Object(s.createElement)("div",{className:"swsales-wrapper-block"},Object(s.createElement)("span",{className:"swsales-block-title"},n("Sitewide Sale Content","sitewide-sales")),Object(s.createElement)(d,{renderAppender:function(){return Object(s.createElement)(d.ButtonBlockAppender,null)},templateLock:!1}))]},save:function(e){var t=e.className;return Object(s.createElement)("div",{className:t},Object(s.createElement)(d.Content,null))}}),wp.i18n.__),b=wp.hooks.addFilter,w=wp.element.Fragment,f=wp.blockEditor.InspectorAdvancedControls,m=wp.compose.createHigherOrderComponent,v=wp.components.SelectControl;b("editor.BlockEdit","swsales/sale-period-setting",m((function(e){return function(t){var l=t.attributes,n=t.setAttributes,i=t.isSelected,r=l.sale_period_visibility;return Object(s.createElement)(w,null,Object(s.createElement)(e,t),i&&Object(s.createElement)(f,null,Object(s.createElement)(v,{value:r,help:p("Select the sale period this content is visible for.","sitewide-sales"),options:[{label:p("Always","sitewide-sales"),value:""},{label:p("Before Sale","sitewide-sales"),value:"pre-sale"},{label:p("During Sale","sitewide-sales"),value:"sale"},{label:p("After Sale","sitewide-sales"),value:"post-sale"}],label:p("Sale Period Visibity"),onChange:function(e){return n({sale_period_visibility:e})}})))}}),"withAdvancedControls")),b("blocks.registerBlockType","swsales/sale-period-setting",(function(e){return e.attributes=Object.assign(e.attributes,{sale_period_visibility:{type:"string",default:""}}),e}))}]);