function openLink(evt, animName) {
  var i, x, psptabs;
  x = document.getElementsByClassName("plugin");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  psptabs = document.getElementsByClassName("psp-tab");
  for (i = 0; i < x.length; i++) {
    psptabs[i].className = psptabs[i].className.replace(" psp-cyan", "");
  }
  document.getElementById(animName).style.display = "block";
  evt.currentTarget.className += " psp-cyan";
}

function import_seo_data(title, loader) {
     //alert(title);
	var yoast_data = {
			action: 'import_yoast_data',
			ajax_nonce: psp_ajax_importer_object.pspnonce,
			title: title,
		};
		
	jQuery.post(psp_ajax_importer_object.ajax_url, yoast_data, function(response) {
	    jQuery(loader).addClass('hidden');
		alert('Import Status: ' + response);
	});	
	/**
	jQuery.post( psp_ajax_importer_object.ajax_url, yoast_data ).error( 
                    function() {
                        alert('error');
                    }).success( function() {
                        alert('success');   
                    });
					
	**/
                    return false; 

}

function import_rankmath_data() {

	var rm_data = {
			action: 'psp_rm_importer',	
			ajax_nonce: psp_ajax_importer_object.nonce,
		};
	jQuery.post( psp_ajax_importer_object.ajax_url, rm_data ).error( 
                    function() {
                        alert('error');
                    }).success( function() {
                        alert('success');   
                    });
                    return false; 

}

jQuery(document).ready(function($) {
	jQuery('#import_yoast_title_data_btn').on('click', function (){
	    //jQuery(".psp-titles-loader").show();
	    $btn = $(".yoast-titles-loader");
	    $(".yoast-titles-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_yoast_brobots_data_btn').on('click', function (){
	    //jQuery(".psp-titles-loader").show();
	    $btn = $(".yoast-basicrobots-loader");
	    $(".yoast-basicrobots-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_yoast_robots_data_btn').on('click', function (){
	    $btn = $(".yoast-robots-loader");
	    $(".yoast-robots-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_yoast_others_data_btn').on('click', function (){
	    $btn = $(".yoast-others-loader");
	    $(".yoast-others-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_yoast_terms_data_btn').on('click', function (){
	    $btn = $(".yoast-terms-loader");
	    $(".yoast-terms-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_yoast_premium_data_btn').on('click', function (){
	    $btn = $(".yoast-premium-loader");
	    $(".yoast-premium-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);	
	});
	
	jQuery('#import_rm_title_data_btn').on('click', function (){
	    $btn = $(".rm-titles-loader");
	    $(".rm-titles-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);	
	});
	jQuery('#import_rm_others_data_btn').on('click', function (){
	    $btn = $(".rm-others-loader");
	    $(".rm-others-loader").removeClass('hidden');
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_rm_robots_data_btn').on('click', function (){
	    $btn = $(".rm-robots-loader");
	    $(".rm-robots-loader").removeClass('hidden');
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_rm_terms_data_btn').on('click', function (){
	    $btn = $(".rm-terms-loader");
	    $(".rm-terms-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_yoastnew_title_data_btn').on('click', function (){
	    //jQuery(".psp-titles-loader").show();
	    $btn = $(".yoastnew-titles-loader");
	    $(".yoastnew-titles-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});	
	jQuery('#import_yoastnew_robots_data_btn').on('click', function (){
	    $btn = $(".yoastnew-robots-loader");
	    $(".yoastnew-robots-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_yoastnew_others_data_btn').on('click', function (){
	    $btn = $(".yoastnew-others-loader");
	    $(".yoastnew-others-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_yoastnew_terms_data_btn').on('click', function (){
	    $btn = $(".yoastnew-terms-loader");
	    $(".yoastnew-terms-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_aioseop_title_data_btn').on('click', function (){
	    //jQuery(".psp-titles-loader").show();
	    $btn = $(".aioseop-titles-loader");
	    $(".aioseop-titles-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_aioseop_brobots_data_btn').on('click', function (){
	    //jQuery(".psp-titles-loader").show();
	    $btn = $(".aioseop-basicrobots-loader");
	    $(".aioseop-basicrobots-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_aioseop_others_data_btn').on('click', function (){
	    $btn = $(".aioseop-others-loader");
	    $(".aioseop-others-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_psp_robots_data_btn').on('click', function (){
	    $btn = $(".psp-robots-loader");
	    $(".psp-robots-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_psp_others_data_btn').on('click', function (){
	    $btn = $(".psp-others-loader");
	    $(".psp-others-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
	jQuery('#import_psp_terms_data_btn').on('click', function (){
	    $btn = $(".psp-terms-loader");
	    $(".psp-terms-loader").removeClass('hidden'); 
	    import_seo_data(this.name, $btn);		
	});
});