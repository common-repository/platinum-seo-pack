jQuery(document).ready(function(e){var t;jQuery(".upload_image_button").length&&jQuery("body").on("click",".upload_image_button",function(a){a.preventDefault(),imagetextID=jQuery(this).prev().attr("id"),t?t.open():((t=wp.media.frames.file_frame=wp.media({title:"Techblissonline Platinum SEO and Social Pack Media Uploader:",button:{text:"Select Image"},multiple:!1})).on("select",function(){attachment=t.state().get("selection").first().toJSON(),0>e.trim(attachment.url.length)||(imagedetail=attachment.url,jQuery("#"+imagetextID).val(imagedetail))}),t.open())})});