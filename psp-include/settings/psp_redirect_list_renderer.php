<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
?>
 <?php  
 wp_enqueue_script( 'psp-redirect', plugins_url( '/js/psp-redirect.js', __FILE__ ), array( 'jquery' ), '2.2.9' );
 //wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
 wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), '2.3.5');
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
.psp-search {
    width: 90%;
}
#pspsearchfield {
    width: 50%;
}
#post-search-input{
    width: 99%;
}
 </style>

<div class="wrap">
    
<h2><?php esc_html_e('Redirection Management:', 'platinum-seo-pack'); ?></h2>
<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline&psptab=psp_permalink" ?>"> <?php esc_html_e('Redirection Settings', 'platinum-seo-pack') ?></a> | <a href="https://techblissonline.com/redirection-in-wordpress/" target="_blank" rel="noopener"><?php esc_html_e('Manage Redirections in WordPress', 'platinum-seo-pack'); ?></a> | <a href="https://techblissonline.com/http-redirection-status-codes-301-302-307-308/" target="_blank" rel="noopener"><?php esc_html_e('HTTP Redirection Status Codes', 'platinum-seo-pack'); ?></a> 
<form id="psp-search" action="" method="get">
	<div class="form-table top">				
		<div class="psp-search alignleft actions">
		    
		    <input type="hidden" name="page" id="page" value="redirectionmanager">
		    <div id="pspredirmethod" class="alignleft">
		    <select id="psp_redir_type" name="psp_redir_type"><?php $dditems = array('' => 'Posts', 'pspurls' => 'Redirected URLs', 'psplogs' => 'Redirection Logs');
		foreach($dditems as $key => $val) {    
			$selected = (isset($_GET['psp_redir_type']) && $_GET['psp_redir_type']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select></div>
		    <div id="pspfilter" class="alignleft">
			<select id="psp_filter" name="psp_filter"><?php if ($psp_redir_type == '') {
			    $dditems = array('' => 'All', 'contains' => 'that Contain',  'starts-with' => 'that Start with', 'ends-with' => 'that End With', 'equals' => 'Equal to', 'redirected' => 'Redirected', );
			} else {
			    $dditems = array('' => 'All',  'contains' => 'that Contain',  'starts-with' => 'that Start with', 'ends-with' => 'that End With', 'equals' => 'Equal to', );
			}
		foreach($dditems as $key => $val) {    
			$selected = (isset($_GET['psp_filter']) && $_GET['psp_filter']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select></div>
		    <div id="pspsearchfield" class="alignleft hidden">
			<input type="search" name="post-search-input" id="post-search-input" placeholder="Enter Word(s) in Post Title or Post Name (URL Slug)..." value="<?php echo (isset($_GET['post-search-input']) ? esc_attr(sanitize_text_field($_GET['post-search-input'])) : ''); ?>">
			</div>
			
		<div id="searchitdiv" class="alignleft"><input type="submit" name="searchit" id="searchit" class="button-secondary search" value="Search"></div>
			
		</div>

	</div>
</form>	
<br class="clear" />
<form id="psp-edit" action="" method="post">
    <div id="psp-edit-div" class="hidden">
    	<table class="form-table">	
    	   
    	    <tr  id="source-tr" class="form-field hidden">
    			<th style="width:20%;" scope="row" valign="top"><label for="source-url-input"><?php esc_html_e('Source URL: ', 'platinum-seo-pack'); ?></label></th>   
    			<td><input type="text" id="source-url-input" name="source-url-input" value="" placeholder="<?php esc_html_e('Enter the source URI or URL ', 'platinum-seo-pack'); ?>" /><input type="hidden" id="pspredirtype" name="pspredirtype" value="<?php echo $psp_redir_type ?>" /></td>
    		</tr>
    		
    		<tr  class="form-field">
    			<th style="width:20%;" scope="row" valign="top"><label for="redirect-url-input"><?php esc_html_e('Redirect to: ', 'platinum-seo-pack'); ?></label></th>   
    			<td><input type="text" id="redirect-url-input" name="redirect-url-input" value="" placeholder="<?php esc_html_e('Enter a valid Destination URL ', 'platinum-seo-pack'); ?>" /></td>
    		</tr>
    		<tr  class="form-field">
    			<th style="width:20%;" scope="row" valign="top"><label for="redirect-url-input"><?php echo esc_html__('Redirection Method: ', 'platinum-seo-pack').'<br>'.'<a href="https://techblissonline.com/http-redirection-status-codes-301-302-307-308/" target="_blank" rel="noopener">'.esc_html__('What to Select?', 'platinum-seo-pack').'</a>'; ?></label></th>
    			<td>    
    			<div class="alignleft"><select id="psp-redirect-code" name="psp-redirect-code">
    							<?php $dditems = array('' => 'Select a redirection method', '301' => '301 Moved Permanently', '302' => '302 Found', '302' => '302 Found', '303' => '303 See Other', '307' => '307 Temporary Redirect');
    							
    				foreach($dditems as $key => $val) {    
    					$selected = (isset($_POST['psp-redirect-code']) && $_POST['psp-redirect-code']==$key) ? 'selected="selected"' : '';
    					echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
    				} ?>
    					</select></div>
    			<div id="log-div" class="psp-separator"><input id="do-not-log" checked="checked" type="radio" name="psplog" value="" /><label class="psp-radio-separator psp-d" for="do-not-log">Do not log</label>
<input id="do-log" type="radio" name="psplog" value="do-log" /><label class="psp-radio-separator psp-d" for="do-log">Log Redirect</label></div>
    			<div id="updateit" class="alignleft"><input type="submit" value="<?php _e('Update'); ?>" name="updateit" class="button-secondary update hidden" />
    			</div>
    			<div id="insertit" class="alignleft"><input type="submit" value="<?php _e('Add New'); ?>" name="insertit" id="insertit" class="button-secondary update hidden" />
    			</div>
				<div id="cancelit-div" class="alignleft"><input type="reset" value="<?php _e('Cancel'); ?>" name="cancelit" id="cancelit" class="button-secondary update hidden" />
    			</div>
    			</td>
    		</tr>
    	</table>
	</div>
	<?php 
		if (empty($psp_redir_type)) {
			
			wp_nonce_field( 'do_psp_posts_redirect_actions', 'psp_posts_redirect_actions_nonce' );
			
		} else if ($psp_redir_type == "pspurls") {
			
			wp_nonce_field( 'do_psp_urls_redirect_actions', 'psp_urls_redirect_actions_nonce' );
			
		} else {
			
			wp_nonce_field( 'do_psp_logs_redirect_actions', 'psp_logs_redirect_actions_nonce' );
		}
	?>
	<div class="tablenav">
		<div class="alignleft">
			<select id="psp_action" name="psp_action">
				<?php 
				if (empty($psp_redir_type)) {
				    //$dditems = array('' => 'Bulk Actions', 'addnew' => 'Add New', 'edit' => 'Edit', 'delete' => 'Move to Trash');
					$dditems = array('' => 'Bulk Actions', 'edit' => 'Edit', 'delete' => 'Delete Selected');
					//wp_nonce_field( 'do_psp_posts_redirect_actions', 'psp_posts_redirect_actions_nonce' );
					
				} else if ($psp_redir_type == "pspurls") {
					
				   $dditems = array('' => 'Bulk Actions', 'addnew' => 'Add New', 'edit' => 'Edit', 'delete' => 'Delete Selected');
				   //wp_nonce_field( 'do_psp_urls_redirect_actions', 'psp_urls_redirect_actions_nonce' );
				   
				} else {
					
					$dditems = array('' => 'Bulk Actions', 'delete' => 'Delete Selected', 'deleteall' => 'Delete All');
					//wp_nonce_field( 'do_psp_logs_redirect_actions', 'psp_logs_redirect_actions_nonce' );
				}
				
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
	<?php if($psp_redir_type == "psplogs") { ?>
	
	<table class="widefat">
		<thead>
			<tr class="psp-header">
				<th scope="col" class=""><input onclick="checkAll(document.getElementById('psp-edit'));" type="checkbox"></th> 
				<td scope="col" class="pspth">ID</th>
				<td scope="col" class="pspth">Request URL</th>				
				<td scope="col" class="pspth">Redirect To</th>
				<td scope="col" class="pspth">Redirect Method</th>
				<th id='referrer'class='manage-column column-referrer pspth' scope="col">Referrer</th>
				<th id='ipaddress'class='manage-column column-ipaddress pspth' scope="col">IP Address</th>
				<th id='useragent'class='manage-column column-useragent pspth' scope="col">User Agent</th>			
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
			
			$psp_redirect = $bad_link->psp_redirect;
			$psp_redirect_code = $bad_link->psp_redirect_code;
			$psp_referrer = $bad_link->psp_referrer;
			$psp_ip = $bad_link->psp_ipaddress;
			$psp_ua = $bad_link->psp_useragent;
				
			$class = 'alternate' == $class ? '' : 'alternate';
			?>
			<tr id="<?php echo esc_attr($bad_link->psp_id); ?>" class="<?php echo trim( esc_attr($class) . ' author-self status-publish'); ?>" valign="top">
				<th scope="row" class=""><?php if ( current_user_can( 'edit_posts', $bad_link->psp_id ) ) { ?><input type="checkbox" name="update[]" value="<?php echo esc_attr($bad_link->psp_id); ?>" /><?php } ?></th>
				<td><strong><?php if ( current_user_can( 'edit_posts', $bad_link->psp_id ) && empty($psp_redir_type)) { ?><a class="row-title" href="post.php?action=edit&amp;post=<?php echo esc_attr($bad_link->psp_id); ?>" target="_blank" title="<?php echo esc_attr(sprintf(__('Edit "%s"'), $title)); ?>"><?php echo esc_attr($bad_link->psp_id); ?></a><?php } else { echo esc_attr($bad_link->psp_id); } ?></strong></td>
				<td><?php echo esc_attr($bad_link->psp_post_name); ?><div class="check-div"><a href="<?php if (!filter_var($bad_link->psp_post_name, FILTER_VALIDATE_URL)) { echo esc_url_raw(home_url($bad_link->psp_post_name)); }  else { echo esc_url_raw($bad_link->psp_post_name); } ?>" class="check" target="_blank" rel="noopener"><?php echo "Check Redirection"; ?></a></div></td>
				<td><?php echo !empty($psp_redirect) ? esc_url_raw($psp_redirect) : ' - '; ?></td>
				<td><?php echo !empty($psp_redirect_code) ? esc_html($psp_redirect_code) : ' - ';?></td>
				<td class='referrer'><?php echo !empty($psp_referrer) ? esc_url_raw($psp_referrer) : ' - ';?></td>
				<td class='ipaddress'><?php echo !empty($psp_ip) ? esc_html($psp_ip) : ' - ';?></td>
				<td class='useragent'><?php echo !empty($psp_ua) ? esc_html($psp_ua) : ' - ';?></td>			
			</tr>
			<?php } ?>
		</tbody>
	<?php } ?>
	</table>
	
	<?php } else { ?>
		
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" class=""><input onclick="checkAll(document.getElementById('psp-edit'));" type="checkbox"></th>
				<th scope="col">ID</th>
				<?php	if ($psp_redir_type !== '') { ?>
				<th scope="col">Request Path/URL</th>
				<?php	} else { ?>
				<th scope="col">Post Title</th>
				<?php	} ?>
				<th scope="col">Redirect To</th>
				<th scope="col">Redirect Method</th>
			<?php	if ($psp_redir_type !== '') { ?>
			        <th scope="col">Logging</th>
				<?php	} ?>
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

				$psp_redirect = ' - ';
				$psp_redirect_code =' - ';
				$title = '';

				if($sql_posts_2 != '') {
					$post_psp_id = $bad_link->psp_id;
					$post_psp = get_post($post_psp_id);
					$title = !empty($post_ps) ? $post_psp->post_title : '';
					if ($psp_redir_type == '') {
						$psp_redirect = get_metadata('platinumseo', $post_psp_id, '_techblissonline_psp_redirect_to_url', true);
					    if(!$psp_redirect) $psp_redirect = get_post_meta($post_psp_id, '_techblissonline_psp_redirect_to_url', true);
						$psp_redirect_code = get_metadata('platinumseo', $post_psp_id, '_techblissonline_psp_redirect_status_code', true);
					    if(!$psp_redirect_code) $psp_redirect_code = get_post_meta($post_psp_id, '_techblissonline_psp_redirect_status_code', true);
					} else {
					    $psp_redirect = $bad_link->psp_redirect;
					    $psp_redirect_code = $bad_link->psp_redirect_code;
					    $psp_log = $bad_link->psp_log;
					}
				} else {
					$post_psp_id = $bad_link->psp_id;
					$post_psp = get_post($post_psp_id);
					$title = !empty($post_ps) ? $post_psp->post_title : '';
					$psp_redirect = $bad_link->psp_redirect;
					$psp_redirect_code = $bad_link->psp_redirect_code;
					if ($psp_redir_type !== '') {
					    $psp_log = $bad_link->psp_log;
					}
				}
			$class = 'alternate' == $class ? '' : 'alternate';
			?>
			<tr id="<?php echo esc_attr($bad_link->psp_id); ?>" class="<?php echo trim( esc_attr($class) . ' author-self status-publish'); ?>" valign="top">
				<th scope="row" class=""><?php if ( current_user_can( 'edit_posts', $bad_link->psp_id ) ) { ?><input type="checkbox" name="update[]" value="<?php echo esc_attr($bad_link->psp_id); ?>" /><?php } ?></th>
				<td><strong><?php if ( current_user_can( 'edit_posts', $bad_link->psp_id ) && empty($psp_redir_type)) { ?><a class="row-title" href="post.php?action=edit&amp;post=<?php echo esc_attr($bad_link->psp_id); ?>" target="_blank" title="<?php echo esc_attr(sprintf(__('Edit "%s"'), $title)); ?>"><?php echo esc_attr($bad_link->psp_id); ?></a><?php } else { echo esc_attr($bad_link->psp_id); } ?></strong></td>
				<td><?php echo esc_attr($bad_link->psp_post_name); ?><div class="check-div"><a href="<?php if (!filter_var($bad_link->psp_post_name, FILTER_VALIDATE_URL)) { echo esc_url_raw(home_url($bad_link->psp_post_name)); }  else { echo esc_url_raw($bad_link->psp_post_name); } ?>" class="check" target="_blank" rel="noopener"><?php echo "Check Redirection"; ?></a></div></td>
				<td><?php echo !empty($psp_redirect) ? esc_url_raw($psp_redirect) : ' - '; ?></td>
				<td><?php echo !empty($psp_redirect_code) ? esc_html($psp_redirect_code) : ' - ';?></td>
			<?php	if ($psp_redir_type !== '') { ?>
				<td><?php echo !empty($psp_log) ? 'On' : 'Off';?></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	<?php } ?>
	</table>
	<?php } ?>
</form>

	<div class="tablenav top">

		<?php
		if ( $page_links )
				echo "<div class='tablenav-pages'>$page_links</div>";
		?>

	</div>

<br class="clear" />

</div>