jQuery(document).ready(function(e){e("#psp-meta-box .hidden").removeClass("hidden"),e("#psp-meta-box").tabs();var t,o,i,d=!1;if("undefined"!=typeof psp_cm_json_settings){var r=wp.codeEditor.defaultSettings?_.clone(wp.codeEditor.defaultSettings):{};r.codemirror=_.extend({},r.codemirror,{autorefresh:!0,matchBrackets:!0,autoCloseBrackets:!0,mode:"application/ld+json",lineWrapping:!0}),e(".pspjsoneditor").length&&(t=wp.codeEditor.initialize(e(".pspjsoneditor"),r))}if("undefined"!=typeof psp_cm_html_settings){var n=wp.codeEditor.defaultSettings?_.clone(wp.codeEditor.defaultSettings):{};n.codemirror=_.extend({},n.codemirror,{autorefresh:!0,mode:"text/html"}),e("#fb_ogtype_media").length&&(o=wp.codeEditor.initialize(e("#fb_ogtype_media"),n)),e("#fb_ogtype_props").length&&(i=wp.codeEditor.initialize(e("#fb_ogtype_props"),n))}if(window.wpEditorL10n&&wpEditorL10n.tinymce&&wpEditorL10n.tinymce.settings&&(d=wpEditorL10n.tinymce.settings),d&&wp.data&&wp.data.select){var p=wp.data.select("core/edit-post");wp.data.subscribe(function(){p.isSavingMetaBoxes(),t&&t.codemirror.save(),o&&o.codemirror.save(),i&&i.codemirror.save()})}});