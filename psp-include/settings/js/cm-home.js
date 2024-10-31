jQuery(document).ready(function($) {    
    if( $('#psp_home_additional_headers').length ) {
		if (typeof psp_cm_home_html_settings != "undefined") {
			//var cm_html_editor = wp.codeEditor.initialize( $('#psp_home_additional_headers'), psp_cm_home_html_settings );
			var editorHeaderSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorHeaderSettings.codemirror = _.extend(
                {},
                editorHeaderSettings.codemirror,
                {
                    autorefresh: true,
                    mode: 'text/html',
                }
            );
            var cm_html_editor = wp.codeEditor.initialize( $('#psp_home_additional_headers'), editorHeaderSettings );
            
		}
    }
	if( $('#psp_home_schemas').length ) {
		if (typeof psp_cm_home_json_settings != "undefined") {
			//var cm_json_editor = wp.codeEditor.initialize( $('#psp_home_schemas'), psp_cm_home_json_settings );
			var editorSchemaSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorSchemaSettings.codemirror = _.extend(
                {},
                editorSchemaSettings.codemirror,
                {
                    autorefresh: true,
                    matchBrackets: true,
                    autoCloseBrackets: true,
                    mode: "application/ld+json",
                    lineWrapping: true
                }
            );
            var cm_json_editor = wp.codeEditor.initialize( $('#psp_home_schemas'), editorSchemaSettings );
			
		}
    }
});