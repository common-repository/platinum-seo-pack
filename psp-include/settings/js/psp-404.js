jQuery(document).ready(function($) {
	jQuery('#psp_action').on('change', function (){
	    $("#source-url-input").val('');
		$( "#psp-edit-div" ).slideUp( "slow", function() {
			$("#psp-edit-div").addClass('hidden');					
			$("#insertit").addClass('hidden'); 
			$("#cancelit").addClass('hidden');
			$("#source-tr").removeClass('hidden'); 
			//$("#updateit").addClass('hidden');
			//$("#psp-delete-div").removeClass('hidden');
		}); 
	    
		if($(this).val() == 'addredirect') {
		    
			$( "#psp-edit-div" ).slideDown( "slow", function() {   			  
			    $("#source-url-input").val('');
			    $("#source-tr").addClass('hidden');
				$("#insertit").removeClass('hidden'); 
				$("#cancelit").removeClass('hidden');
				$("#psp-edit-div").removeClass('hidden'); 
			});
		    
		} 
		if($(this).val() == 'edit') {
		    $("#psp-status-div").removeClass('hidden');	
		    $("#updateit").removeClass('hidden');
		} else {
		    $("#psp-status-div").addClass('hidden'); 
			$("#updateit").addClass('hidden');
		}
		
		if($(this).val() == 'delete' || $(this).val() == 'deleteall') {
		    $("#psp-delete-div").removeClass('hidden');			    
		} else {
		    $("#psp-delete-div").addClass('hidden'); 			    
		}
	});
	$('#cancelit').on('click', function (){
	    	$( "#psp-edit-div" ).slideUp( "slow", function() {
			$("#psp-edit-div").addClass('hidden');					
			$("#insertit").addClass('hidden'); 
			$("#cancelit").addClass('hidden');
			//$("#updateit").addClass('hidden');
			//$("#psp-delete-div").removeClass('hidden');
		}); 
	});
	$('a.create').on('click', function (){
	   $('#id-input').val( $(this).attr('title')); 
	   $('#source-url-input').val( $(this).siblings("div").text());
	   	$( "#psp-edit-div" ).slideDown( "slow", function() {    			     $("#source-tr").removeClass('hidden');
				$("#insertit").removeClass('hidden');
				$("#cancelit").removeClass('hidden'); 
				$("#psp-edit-div").removeClass('hidden'); 
			});
	});
	$('.moretr').on('click', function (){
	    $(this).toggle('slow');
	});
	$('a.more').on('click', function (){
	    $(this).closest("tr").next('tr').toggle('slow');
	});
	if($('#psp_filter').val() === '') {
    	      $( "#pspsearchfield" ).addClass('hidden');
    	} else {
    	    $( "#pspsearchfield" ).removeClass('hidden');
    	}    
	jQuery('#psp_filter').on('change', function (){
    	if($(this).val() === '') {
    	      $( "#pspsearchfield" ).addClass('hidden');
    	} else {
    	    $( "#pspsearchfield" ).removeClass('hidden');
    	}    
	});  
});