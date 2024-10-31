jQuery(document).ready(function($) {    
    //if (typeof cm_json_settings != "undefined") wp.codeEditor.initialize($('#psp_htaccess_content'), cm_json_settings);
    if( $('#psp_htaccess_content').length ) {
            var editorHtaccessSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorHtaccessSettings.codemirror = _.extend(
                {},
                editorHtaccessSettings.codemirror,
                {
                    autorefresh: true,
                    mode: 'text/nginx',
                }
            );
            var editor = wp.codeEditor.initialize( $('#psp_htaccess_content'), editorHtaccessSettings );
        }
});