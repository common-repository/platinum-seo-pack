<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
?>
 <?php  
 wp_enqueue_script( 'psp-404', plugins_url( '/js/psp-404.js', __FILE__ ), array( 'jquery' ) );
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
	olor:#111;

}
.page-numbers.current,
a.page-numbers:hover {
	background: grey; /*	color:#f9f9f9;*/
		color:#fff;
}
 </style>

<div class="wrap">
    
<h2><?php esc_html_e('Manage 404 Errors:', 'platinum-seo-pack'); ?></h2>
<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline&psptab=psp_permalink#psp_301_limit" ?>"> <?php esc_html_e('404 Settings', 'platinum-seo-pack') ?></a> | <a href="https://techblissonline.com/http-404-error/" target="_blank" rel="noopener"><?php esc_html_e('How to handle 404 Errors', 'platinum-seo-pack'); ?></a>
<form id="psp-search" action="" method="get">
	<div class="form-table top">				
		<div class="alignleft actions">
		    
		    <input type="hidden" name="page" id="page" value="manager404">
		    <div id="pspredirmethod" class="alignleft">
		    <select id="psp_404_type" name="psp_404_type"><?php //$dditems = array('' => 'All Errors', 'all_with_referrers' => 'Errors with referrers only','all_404' => 'All 404s', 'all_410' => 'All 410s', 'all_404_with_referrers' => 'All 404s with referrers', 'all_410_with_referrers' => 'All 410s with referrers');
		    $dditems = array('' => 'All Errors', 'all_404' => 'All 404s', 'all_410' => 'All 410s');
		foreach($dditems as $key => $val) {    
			$selected = (isset($_GET['psp_404_type']) && $_GET['psp_404_type']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select></div>
		    <div id="pspfilter" class="alignleft">
			<select id="psp_filter" name="psp_filter"><?php $dditems = array('' => 'Logged', 'equals' => 'Equal to', 'contains' => 'that Contain',  'starts-with' => 'that Start with', 'ends-with' => 'that End With');
		foreach($dditems as $key => $val) {    
			$selected = (isset($_GET['psp_filter']) && $_GET['psp_filter']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select></div>
		    <div id="pspsearchfield" class="alignleft hidden">
			<input type="search" style="width:240px;" placeholder="Enter text in Request Path(Name)" name="post-search-input" id="post-search-input" value="<?php echo (isset($_GET['post-search-input']) ? esc_attr(sanitize_text_field($_GET['post-search-input'])) : ''); ?>">
			</div>
			
		<div id="searchitdiv" class="alignleft"><input type="submit" name="searchit" id="searchit" class="button-secondary search" value="Search"></div>
			
		</div>

	</div>
</form>	
<br class="clear" />
<form id="psp-edit" action="" method="post">
    <div id="psp-edit-div" class="hidden">
    	<table class="form-table">	
    	
    	     <tr  id="id-tr" class="form-field hidden">
    			<th style="width:20%;" scope="row" valign="top"><label for="id-input"><?php esc_html_e('Source ID: ', 'platinum-seo-pack'); ?></label></th>   
    			<td><input type="hidden" id="id-input" name="id-input" value="" /></td>
    		</tr>
    	   
    	    <tr  id="source-tr" class="form-field">
    			<th style="width:20%;" scope="row" valign="top"><label for="source-url-input"><?php esc_html_e('Source URI: ', 'platinum-seo-pack'); ?></label></th>   
    			<td><input type="text" id="source-url-input" name="source-url-input" value="" placeholder="<?php esc_html_e('Enter the source URI or URL ', 'platinum-seo-pack'); ?>" /></td>
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
    			<div id="insertit-div" class="alignleft"><input type="submit" value="<?php _e('Add New'); ?>" name="insertit" id="insertit" class="button-secondary update hidden" />
    			</div>
    			<div id="cancelit-div" class="alignleft"><input type="reset" value="<?php _e('Cancel'); ?>" name="cancelit" id="cancelit" class="button-secondary update hidden" />
    			</div>
    			</td>
    		</tr>
    	</table>
	</div>
	<?php 			
		wp_nonce_field( 'do_psp_404_actions', 'psp_404_actions_nonce' );		
	?>
	<div class="tablenav">
		<div class="alignleft">
			<div id="psp-select-div" class="alignleft">
			<select id="psp_action" name="psp_action">
				<?php 
				 $dditems = array('' => 'Bulk Actions', 'addredirect' => 'Add Redirection', 'edit' => 'update Status', 'delete' => 'Delete Permanently', 'deleteall' => 'Delete All');
				foreach($dditems as $key => $val) {    
				//$selected = (isset($_POST['psp_action']) && $_POST['psp_action']==$key) ? 'selected="selected"' : '';
				$selected = '';
				echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
				} ?>				
			</select>
		    </div>
			<div id="psp-delete-div" class="hidden alignleft">
			    <input type="submit" value="<?php _e('Delete'); ?>" id="deleteit" name="deleteit" class="button-secondary delete" />
			</div>
			<div id="psp-status-div" class="hidden alignleft">
			<select id="psp_status" name="psp_status">
				<?php 
				$dditems = array('' => 'Leave it as 404', '410' => 'Change status to 410');
				foreach($dditems as $key => $val) {    
				//$selected = (isset($_POST['psp_action']) && $_POST['psp_action']==$key) ? 'selected="selected"' : '';
				$selected = '';
				echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
				} ?>				
			</select>
		    </div>
			<div id="updateit" class="alignleft hidden"><input type="submit" value="<?php _e('Update'); ?>" name="updateit" class="button-secondary update hidden" />
			</div>
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
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" class=""><input onclick="checkAll(document.getElementById('psp-edit'));" type="checkbox"></th>
				<th scope="col">ID</th>
				<th scope="col">Request Path(Name)</th>
				<th scope="col">Status</th>
				<th scope="col">Referrer</th>
				<th scope="col">Hits</th>
				<th scope="col">IP Address</th>
				<th scope="col">More</th>
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

				$post_psp_id = sanitize_key($bad_link->psp_id);
			$class = 'alternate' == $class ? '' : 'alternate';
			?>
			<tr class="<?php echo trim( esc_attr($class) . ' author-self status-publish'); ?>" valign="top">
				<th scope="row" class=""><?php if ( current_user_can( 'edit_posts', $post_psp_id ) ) { ?><input type="checkbox" name="update[]" value="<?php echo esc_attr($post_psp_id); ?>" /><input type="hidden" id="<?php echo "psp-".esc_attr($post_psp_id); ?>" name="<?php echo "psp-".esc_attr($post_psp_id); ?>" value="<?php echo esc_attr($bad_link->psp_post_name); ?>" /><?php } ?></th>
				<td><a href="<?php echo esc_url_raw($bad_link->psp_rel_url); ?>" target="_blank"><?php echo esc_attr($post_psp_id); ?></a></td>
				<td><?php echo esc_attr($bad_link->psp_post_name); ?><div class="create-div"><div class="uri hidden"><?php echo esc_attr($bad_link->psp_post_name); ?></div><a href="#" title="<?php echo esc_attr($post_psp_id); ?>" class="create"><?php echo "Create Redirection"; ?></a></div></td>
				<td><?php echo empty($bad_link->status) ? '404' : esc_html($bad_link->status); ?></td>
				<td><?php echo !empty($bad_link->referrer) ? esc_html($bad_link->referrer) : ''; ?></td>
				<td><?php echo !empty($bad_link->total_hits) ? esc_html($bad_link->total_hits) : ''; ?></td>
				<td><?php echo !empty($bad_link->ipaddress) ? esc_html($bad_link->ipaddress) : ''; ?></td>
				<td><a href="#.more" class="more button"><?php echo "..."; ?></a></td>
				</tr><tr class="moretr hidden"><td colspan="7">
				<div id="more-<?php echo esc_attr($bad_link->psp_id); ?>" class="alignleft">
				    <div class="alignleft"><b>Request URL: </b><?php echo !empty($bad_link->psp_rel_url) ? esc_html($bad_link->psp_rel_url) : ''; ?></div><br class="clear" />
					<div class="alignleft"><b>IP address: </b><?php echo !empty($bad_link->ipaddress) ? esc_html($bad_link->ipaddress) : ''; ?></div><br class="clear" />
					<div class="alignleft"><b>User Agent: </b><?php echo !empty($bad_link->user_agent) ? esc_html($bad_link->user_agent) : ''; ?></div><br class="clear" />
					<div class="alignleft"><b>Created: </b><?php echo !empty($bad_link->created) ? esc_html($bad_link->created) : ''; ?></div><br class="clear" />
					<div class="alignleft"><b>Last Logged: </b><?php echo !empty($bad_link->last_logged) ? esc_html($bad_link->last_logged) :''; ?></div>
					<div id="cancelmore-div" class="alignright"><input type="reset" value="<?php _e('Cancel'); ?>" name="cancelmore" id="cancelmore" class="button-secondary update hidden" />
    			    </div>
				</div></td>
			</tr>
			<?php } ?>
		</tbody>
	<?php } ?>
	</table>
</form>

	<div class="tablenav top">

		<?php
		if ( $page_links )
				echo "<div class='tablenav-pages'>$page_links</div>";
		?>

	</div>

<br class="clear" />

</div>