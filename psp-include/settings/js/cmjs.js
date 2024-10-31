jQuery(document).ready(function($) {    
    //if (typeof cm_json_settings != "undefined") wp.codeEditor.initialize($('#psp_ga_tracking_code_id'), cm_json_settings);
    if( $('#psp_ga_tracking_code_id').length ) {
            var editorGaSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorGaSettings.codemirror = _.extend(
                {},
                editorGaSettings.codemirror,
                {
                    autorefresh: true,
                    mode: 'text',
                }
            );
            var editor = wp.codeEditor.initialize( $('#psp_ga_tracking_code_id'), editorGaSettings );
        }
});