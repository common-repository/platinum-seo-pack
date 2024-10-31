<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
?>
 <?php  
 if ($psp_meta_type == "schema" ) {
	$psp_cm_bulkeditor_json_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'json', 'codemirror'=> array('autoRefresh' => true)));
	wp_enqueue_script( 'psp-bulkeditor-cm-editors', plugins_url( '/js/cm-bulkeditor.js', __FILE__ ),array( 'jquery' ), '2.3.2', true);
	wp_localize_script('psp-bulkeditor-cm-editors', 'psp_cm_bulkeditor_json_settings', $psp_cm_bulkeditor_json_settings);
 }
 $psp_bulkedit_meta_nonce = wp_create_nonce( 'psp_bulkedit_meta_nonce' ); 
 wp_enqueue_script( 'psp-ajax-bulkedit-script', plugins_url( 'settings/js/psp-bulkedit.js', PSP_PLUGIN_SETTINGS_URL ), array('jquery'), '2.3.2' );
 wp_localize_script( 'psp-ajax-bulkedit-script', 'psp_ajax_bulkeditor_object', array( 'bedit_ajax_url' => admin_url( 'admin-ajax.php'), 'pspbeditnonce' => $psp_bulkedit_meta_nonce) );
// wp_enqueue_script( 'psp-bulkedit', plugins_url( '/js/psp-bulkedit.js', __FILE__ ), array( 'jquery' ) );
 //wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
 wp_enqueue_style("psp-settings-bs-css", plugins_url( 'settings/css/psp-settings-bs.css', PSP_PLUGIN_SETTINGS_URL ));
 wp_enqueue_style("psp-settings-css", plugins_url( 'settings/css/psp-settings.css', PSP_PLUGIN_SETTINGS_URL ), array(), '2.3.5');
 //wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), '2.2.1');
 
 ?>
 <style>
.page-numbers {
	display: inline-block;
	padding: 5px 10px;
	margin: 0 2px 0 0;
	border: 1px solid #eee;
	line-height: 1;
	text-decoration: none;
	border-radius: 2px;
	font-weight: 600;
	color:#111;

}
.page-numbers.current,
a.page-numbers:hover {
	background: grey; /*	color:#f9f9f9;*/
		color:#fff;
}
a.check {
   color:#fff;
}
a.check:hover {
		color:#0073aa;
}
.psptht {
	width: 30%;
}
.pspthe {
	width: 45%;
}
.editdiv {
	display: none;
}
.editabletext{ 
 width: 99%; 
}
.editabletextarea{ 
 width: 99%; 
 height: 50px;
}
.psp-search {
    width: 90%;
}
#pspsearchfield {
    width: 45%;
}
#post-search-input{
    width: 99%;
}
 </style>

<div class="wrap">
    
<h2><?php esc_html_e('Techblissonline Platinum SEO Bulk Editor:', 'platinum-seo-pack'); ?></h2>

<form id="psp-search" action="" method="get">
	<div class="form-table top">				
		<div class="psp-search alignleft actions">
		    
		    <input type="hidden" name="page" id="page" value="psp-tools-by-techblissonline">
		    <div id="pspmetatypes" class="alignleft">
		    <strong><?php echo "Edit " ?></strong><select id="psp_meta_type" name="psp_meta_type"><?php $dditems = array('' => 'SEO Title', 'description' => 'Meta Description', 'schema' => 'JSON Schema');
				foreach($dditems as $key => $val) {    
					$selected = (isset($_GET['psp_meta_type']) && $_GET['psp_meta_type']==$key) ? 'selected="selected"' : '';
					echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
				} ?>
			</select>
			</div>
			<div id="pspposttypes" class="alignleft">
				<strong><?php echo "of " ?></strong><select id="psp_post_type" name="psp_post_type"><?php //$dditems = array('' => 'Posts',);
					$dditems = $psp_posttypes_for_ddl;
					foreach($dditems as $key => $val) {    
						$selected = (isset($_GET['psp_post_type']) && $_GET['psp_post_type']==$key) ? 'selected="selected"' : '';
						echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
					} ?>
				</select>
			</div>
		    <div id="pspfilter" class="alignleft">
			<select id="psp_filter" name="psp_filter"><?php 
			    $dditems = array('' => 'All', 'contains' => 'that Contain',  'starts-with' => 'that Start with', 'ends-with' => 'that End With', 'equals' => 'Equal to' );
			//} else {
			   // $dditems = array('' => 'All', 'equals' => 'Equal to', 'contains' => 'that Contain',  'starts-with' => 'that Start with', 'ends-with' => 'that End With');
			//}
		foreach($dditems as $key => $val) {    
			$selected = (isset($_GET['psp_filter']) && $_GET['psp_filter']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select></div>
		    <div id="pspsearchfield" class="alignleft hidden">
			<input type="search" name="post-search-input" id="post-search-input" class="post-search-input" placeholder="Enter Word(s) in WordPress Post Title..." value="<?php echo (isset($_GET['post-search-input']) ? esc_attr(sanitize_text_field($_GET['post-search-input'])) : ''); ?>">
			</div>
			
		<div id="searchitdiv" class="alignleft"><input type="submit" name="searchit" id="searchit" class="button-secondary search" value="Search"></div>
		<div id="pspordertypes" class="alignleft">
		    <strong><?php echo " Sort By " ?></strong><select id="psp_sort_type" name="psp_sort_type"><?php $dditems = array('' => 'Post ID', 'psp_post_name' => 'WP Title');
				foreach($dditems as $key => $val) {    
					$selected = (isset($_GET['psp_sort_type']) && $_GET['psp_sort_type']==$key) ? 'selected="selected"' : '';
					echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
				} ?>
			</select>
			</div>
			<div id="pspsortby" class="alignleft">
		    <select id="psp_order_type" name="psp_order_type"><?php $dditems = array('' => 'DESC', 'asc' => 'ASC');
				foreach($dditems as $key => $val) {    
					$selected = (isset($_GET['psp_order_type']) && $_GET['psp_order_type']==$key) ? 'selected="selected"' : '';
					echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
				} ?>
			</select>
			</div>	
		</div>

	</div>
</form>	
<br class="clear" />
<form id="psp-edit" action="" method="post">
    <div id="psp-edit-div" class="hidden">
    	
	</div>
	<?php 
		if (empty($psp_meta_type)) {
			
			wp_nonce_field( 'do_psp_posts_bulkedit_title', 'psp_posts_bulkedit_title_nonce' );
			
		} else if ( $psp_meta_type == "description" ){
			wp_nonce_field( 'do_psp_posts_bulkedit_desc', 'psp_posts_bulkedit_desc_nonce' );
		} else if ( $psp_meta_type == "schema" ){
			wp_nonce_field( 'do_psp_posts_bulkedit_schema', 'psp_posts_bulkedit_schema_nonce' );
		}
	?>
	<div class="tablenav">
		<div class="alignleft">
			<select id="psp_action" name="psp_action">
				<?php 
				$dditems = array('' => 'Bulk Actions', 'delete' => 'Delete Selected');
				//$dditems = array('' => 'Bulk Actions', 'delete' => 'Delete Permanently', 'deleteall' => 'Delete All');				
				foreach($dditems as $key => $val) {    
				//$selected = (isset($_POST['psp_action']) && $_POST['psp_action']==$key) ? 'selected="selected"' : '';
				$selected = '';
				echo "<option id='$key' value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
				} ?>				
			</select>
		    </div>
			<div id="psp-delete-div" class="hidden alignleft">
			    <input type="submit" value="<?php _e('Delete'); ?>" id="deleteit" name="deleteit" class="button-secondary delete" />
			</div>
		
		<script type="text/javascript">
		<!--
		function checkAll(form) {
			for (i = 0, n = form.elements.length; i < n; i++) {
				if(form.elements[i].type == "checkbox" && !(form.elements[i].getAttribute('onclick',2))) {
					if(form.elements[i].checked == true)
						form.elements[i].checked = false;
					else
						form.elements[i].checked = true;
				}
			}
		}
		//-->
		</script>
		<?php
		if ( $page_links )
				echo "<div class='tablenav-pages alignright'>$page_links</div>";
		?>
	</div>
	<?php //if($psp_redir_type == "psplogs") { ?>
	
	<table class="widefat">
		<thead>
			<tr class="psp-header">
				<th scope="col" class=""><input onclick="checkAll(document.getElementById('psp-edit'));" type="checkbox"></th> 
				<th scope="col" class="pspth">ID</th>
				<th scope="col" class="psptht">WordPress Title</th>
				<?php if (empty($psp_meta_type)) { ?>
					<th scope="col" class="pspthe">Techblissonline Platinum SEO Title</th>				
				<?php } else if ($psp_meta_type == "description" ) { ?>
					<th scope="col" class="pspthe">Techblissonline Platinum SEO Meta Description</th>
				<?php } else if ($psp_meta_type == "schema" ) { ?>
					<th scope="col" class="pspthe">Techblissonline Platinum SEO JSON Schema</th>		
				<?php } ?>
				<th scope="col" class="pspth"></th>
			</tr>
		</thead>
		<?php
		if(count($bad_links) > 0) { ?>
		<tbody>
		<?php 
			$bgcolor = '';
			//$class = 'alternate' == $class ? '' : 'alternate';
			$class = 'alternate';
			foreach($bad_links as $bad_link){
			
			$psp_id = !empty( $bad_link->psp_id) ? $bad_link->psp_id : '';
			$psp_post_name = !empty( $bad_link->psp_post_name) ? $bad_link->psp_post_name : '';
			if (empty($psp_meta_type)) {
			    $psp_editable_text = !empty( $bad_link->psp_title) ? esc_attr($bad_link->psp_title) : '';
			} else if ($psp_meta_type == "description" ) { 
				$psp_editable_text = !empty( $bad_link->psp_description) ? esc_textarea($bad_link->psp_description) : '';
			} else if ($psp_meta_type == "schema" ) { 
				$json_schema_string = !empty( $bad_link->psp_schema) ? html_entity_decode(stripcslashes(esc_attr($bad_link->psp_schema))) : '';
				//validate it is a json object
				$schema_obj = json_decode($json_schema_string);
				if($schema_obj === null) {
					$json_schema_string = 'Invalid JSON Schema';
				}
				$psp_editable_text = stripcslashes($json_schema_string);
			}
				
			$class = 'alternate' == $class ? '' : 'alternate';
			?>
			<tr id="<?php echo $psp_id; ?>" class="<?php echo trim( esc_attr($class) . ' author-self status-publish'); ?>" valign="top">
				<th scope="row" class=""><?php if ( current_user_can( 'edit_posts', $bad_link->psp_id ) ) { ?><input type="checkbox" name="update[]" value="<?php echo $psp_id; ?>" /><?php } ?></th>
				<td><strong><?php if ( current_user_can( 'edit_posts', $bad_link->psp_id ) ) { ?><a class="row-title" href="post.php?action=edit&amp;post=<?php echo $psp_id; ?>" target="_blank" title="<?php echo esc_attr(sprintf(__('Edit "%s"'), $psp_post_name)); ?>"><?php echo $psp_id; ?></a><?php } else { echo $psp_id; } ?></strong></td>				
				<td><?php echo !empty($psp_post_name) ? esc_attr($psp_post_name) : ' - '; ?></td>
				<?php if (empty($psp_meta_type)) { ?>
				<td><div class='editdiv' ><?php echo !empty($psp_editable_text) ? ($psp_editable_text) : ' - '; ?></div><input type='text' class='editabletext' value='<?php echo $psp_editable_text; ?>' id='editabletext-<?php echo $psp_id; ?>' ></td>
				<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="psp_title" id="psp_data_update_<?php echo $psp_id; ?>" class="psp_meta_updater_btn btn btn-success" type="btn" value="Update" /><p id ="loader-<?php echo $psp_id; ?>" class="psp_data_update-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="updating..."/></p></div><br /><span id="updatedmsg-<?php echo $psp_id; ?>" class="pspmsg"></span></td>
				<?php } else if ($psp_meta_type == "description") { ?>
				<td><div class='editdiv' ><?php echo !empty($psp_editable_text) ? ($psp_editable_text) : ' - '; ?></div><textarea id=<?php echo 'editabletextarea-'.$psp_id ?> type='text' class='editabletextarea' id='editabletext_<?php echo $psp_id; ?>' ><?php echo !empty($psp_editable_text) ? ($psp_editable_text) : '  '; ?></textarea></td>
				<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="psp_description" id="psp_data_update_<?php echo $psp_id; ?>" class="psp_meta_updater_btn btn btn-success" type="btn" value="Update" /><p id ="loader-<?php echo $psp_id; ?>" class="psp_data_update-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="updating..."/></p></div><br /><span id="updatedmsg-<?php echo $psp_id; ?>" class="pspmsg"></span></td>
				<?php } else if ($psp_meta_type == "schema") { ?>
					<td><div class='editdiv' ><?php echo !empty($psp_editable_text) ? ($psp_editable_text) : ' - '; ?></div><div class='pspeditor'><textarea  id=<?php echo 'editabletextarea-'.$psp_id ?> type='text' class='editabletextarea pspjsoneditor' id='editabletext_<?php echo $psp_id; ?>' ><?php echo !empty($psp_editable_text) ? esc_textarea($psp_editable_text) : '  '; ?></textarea></div></td>
					<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="psp_schema" id="psp_data_update_<?php echo $psp_id; ?>" class="psp_meta_updater_btn btn btn-success" type="btn" value="Update" /><p id ="loader-<?php echo $psp_id; ?>" class="psp_data_update-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="updating..."/></p></div><br /><span id="updatedmsg-<?php echo $psp_id; ?>" class="pspmsg"></span></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	<?php } ?>
	</table>
	
	<?php //} ?>
</form>

	<div class="tablenav top">

		<?php
		if ( $page_links )
				echo "<div class='tablenav-pages'>$page_links</div>";
		?>

	</div>

<br class="clear" />

</div>