jQuery(document).ready(function($) {
   	
	jQuery('#psp_action').on('change', function (){
	    
		$( "#psp-edit-div" ).slideUp( "slow", function() {
			$("#psp-edit-div").addClass('hidden');
			$("#updateit").addClass('hidden');
			$("#source-tr").addClass('hidden'); 
			$("#insertit").addClass('hidden'); 
			$("#log-div").addClass('hidden');
			$("#cancelit").addClass('hidden');
		}); 
	    
		if($(this).val() == 'edit' || $(this).val() == 'addnew') {
		    if($(this).val() == 'edit') {
    		    $( "#psp-edit-div" ).slideDown( "slow", function() {
    		        if( $( "#psp_redir_type" ).val() !== "") {
    		            $("#log-div").removeClass('hidden');
    		        }
    			     $("#updateit").removeClass('hidden'); 
    			    $("#psp-edit-div").removeClass('hidden');
					$("#cancelit").removeClass('hidden');
    			    
    		    });
		    }
		    if($(this).val() == 'addnew') {
    		    $( "#psp-edit-div" ).slideDown( "slow", function() {
    			    $("#source-tr").removeClass('hidden'); 
    			    $("#insertit").removeClass('hidden'); 
    			    $("#psp-edit-div").removeClass('hidden'); 
					$("#cancelit").removeClass('hidden');
    			   if( $( "#psp_redir_type" ).val() !== "") {
    		            $("#log-div").removeClass('hidden');
    		        }
    		    });
		    }
		} 		
		
		if($(this).val() == 'delete' || $(this).val() == 'deleteall') {
		   
			    $("#psp-delete-div").removeClass('hidden'); 
		  
		} else {
		    
			    $("#psp-delete-div").addClass('hidden'); 
		    
		}
	});
	jQuery('#psp_redir_type').on('change', function (){	
	   
	   // if($(this).val() !== '') {
	   //     $('select[id="psp_filter"]').find('option[value=""]').text("All");
	   //} else {
	   //    $('select[id="psp_filter"]').find('option[value=""]').text("Redirected");
	    //}
		
		 if($(this).val() !== '') {
	        $('select[id="psp_filter"]').find('option[value="redirected"]').remove();
			$('#psp_filter').val('');
			$("#psp_filter").prop("disabled", true);
			$( "#pspsearchfield" ).addClass('hidden');
	   } else {
	       $('select[id="psp_filter"]').append('<option value="redirected">Redirected</option>');	
		   $('#psp_filter').val('');
			$("#psp_filter").prop("disabled", false);
			//$( "#pspsearchfield" ).removeClass('hidden');
	    }
	    
	    if($(this).val() === 'pspurls') {
	        $("#psp_action").append('<option id="addnew" value="addnew">Add New</option>');
	    } else {
	      $("#psp_action").find('option[value="addnew"]').remove();
	    }
	    
	    if($(this).val() !== $("#pspredirtype").val()) { 
	        $( "#psp-edit-div" ).slideUp( "slow");
	        $("#psp_action").val('');
	        $("#psp_action").addClass('hidden');
	         $("#psp-delete-div").addClass('hidden'); 
	    } else {
	         $("#psp_action").removeClass('hidden');
	         if($("#psp_action").val() == 'delete' || $("#psp_action").val() == 'deleteall') {
	            $("#psp-delete-div").removeClass('hidden'); 
	         }
	    }
	   
	});
	
	$('#cancelit').on('click', function (){
	    	$( "#psp-edit-div" ).slideUp( "slow", function() {
			$("#psp-edit-div").addClass('hidden');					
			$("#insertit").addClass('hidden'); 
			$("#cancelit").addClass('hidden');
		}); 
	})
	if($('#psp_filter').val() === '' || $('#psp_filter').val() === 'redirected') {
    	      $( "#pspsearchfield" ).addClass('hidden');
	} else {
		$( "#pspsearchfield" ).removeClass('hidden');
	}  
	if($('#psp_redir_type').val() !== '') {
		$('#psp_filter').val("");
		$('#post-search-input').val("");
		$( "#pspsearchfield" ).addClass('hidden');
		$("#psp_filter").prop("disabled", true);
	}
		
	jQuery('#psp_filter').on('change', function (){
    	if($('#psp_filter').val() === '' || $('#psp_filter').val() === 'redirected') {
    	      $( "#pspsearchfield" ).addClass('hidden');
    	} else {
    	    $( "#pspsearchfield" ).removeClass('hidden');
    	}    
	});  
	jQuery('#psp_filter').on('change', function (){	
		$('#post-search-input').val("");
	});
	if (jQuery('#ipaddress-hide').is(":not(:checked)")) {
	    $( "#ipaddress" ).addClass('hidden');
	    $( ".ipaddress" ).addClass('hidden');
	}
	if (jQuery('#referrer-hide').is(":not(:checked)")) {
        $( "#referrer" ).addClass('hidden');
        $( ".referrer" ).addClass('hidden');
	}
	if (jQuery('#useragent-hide').is(":not(:checked)")) {
	    $( "#useragent" ).addClass('hidden');
	    $( ".useragent" ).addClass('hidden');
	}
	jQuery('#ipaddress-hide').click(function(){
            if($(this).is(":checked")){                
				$( "#ipaddress" ).removeClass('hidden');
				$( ".ipaddress" ).removeClass('hidden');
            }
            else if($(this).is(":not(:checked)")){               
				$( "#ipaddress" ).addClass('hidden');
				$( ".ipaddress" ).addClass('hidden');
            }
        });
		
jQuery('#useragent-hide').click(function(){
            if($(this).is(":checked")){                
				$( "#useragent" ).removeClass('hidden');
				$( ".useragent" ).removeClass('hidden');
            }
            else if($(this).is(":not(:checked)")){                
				$( "#useragent" ).addClass('hidden');
				$( ".useragent" ).addClass('hidden');
            }
        });
		
jQuery('#referrer-hide').click(function(){
            if($(this).is(":checked")){                
				$( "#referrer" ).removeClass('hidden');
				$( ".referrer" ).removeClass('hidden');
            }
            else if($(this).is(":not(:checked)")){                
				$( "#referrer" ).addClass('hidden');
				$( ".referrer" ).addClass('hidden');
            }
        });
   
});