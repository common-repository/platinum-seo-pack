jQuery(document).ready(function($) {    
   // if (typeof cm_text_settings != "undefined") wp.codeEditor.initialize($('#psp_robotstxt_content'), cm_text_settings);
   // if (typeof cm_htaccess_settings != "undefined") wp.codeEditor.initialize($('#psp_htaccess_content'), cm_htaccess_settings);
   if( $('#psp_robotstxt_content').length ) {
            var editorRobotSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorRobotSettings.codemirror = _.extend(
                {},
                editorRobotSettings.codemirror,
                {
                    autorefresh: true,
                    mode: 'text',
                }
            );
            var editor = wp.codeEditor.initialize( $('#psp_robotstxt_content'), editorRobotSettings );
        }
});