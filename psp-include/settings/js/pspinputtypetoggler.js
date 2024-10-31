jQuery(document).ready(function(){
	/* Toggle Visibility of OAUTH2 ID/Client Secret and API Keys (for viewing/troubleshooting) */
	//jQuery( 'body' ).on( 'click' , '.view-obfuscated-text' , function() {
	if (jQuery('.view-obfuscated-text').length) {
	//jQuery('.view-obfuscated-text').live('click', function(event){
	jQuery('body').on( 'click', '.view-obfuscated-text', function (event) {
		event.preventDefault();
		//alert "Type ".Query(this).prev().attr("type");
		if( jQuery( this ).prev().attr( 'type' ) == 'password' ) {
			jQuery( this ).prev().removeAttr( 'type' );
			jQuery( this ).prev().attr( 'type' , 'text' );
			jQuery( this ).removeClass('dashicons-hidden');
			jQuery( this ).addClass('dashicons-visibility');
			
		} else {
			jQuery( this ).prev().removeAttr( 'type' );
			jQuery( this ).prev().attr( 'type' , 'password' );
			jQuery( this ).removeClass('dashicons-visibility');
			jQuery( this ).addClass('dashicons-hidden');
		}
	});
	}
});