jQuery(document).ready(function($) {    
    
	//if (typeof psp_cm_ptype_html_settings != "undefined") {
		//var cm_pt_html_editor = wp.codeEditor.initialize( $('.pspcodeeditor'), psp_cm_ptype_html_settings );
		var editorHeaderSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
		editorHeaderSettings.codemirror = _.extend(
			{},
			editorHeaderSettings.codemirror,
			{
				autorefresh: true,
				mode: 'text/html',
			}
		);
		$(".pspcodeeditor").each(function() {
		    var cm_pt_html_editor = wp.codeEditor.initialize( $(this), editorHeaderSettings );
		});
	//}
    	
});