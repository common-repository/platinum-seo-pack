<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
?>
 <?php  
 //wp_enqueue_script( 'psp-thickbox', plugins_url( '/js/psp-thickbox.js', __FILE__ ), array( 'jquery' ) );
 wp_enqueue_script( 'thickbox', '', array( 'jquery' ) );
  wp_enqueue_style( 'thickbox' );
 ?>
 <div id='psp-preview-box-tbx'>
<div id="psp-preview-box">
    <ul class="psp-preview-tabs" id="psp-preview-tabs">
				<li class="desktop"><a href="#desktop" title="Desktop Preview"><span class="dashicons dashicons-desktop"></span><?php esc_html_e( ' ', 'platinum-seo-pack' ); ?></a></li>				
				<li class="mobile"><a href="#mobile" title="Mobile Preview"><span class="dashicons dashicons-smartphone"></span><?php esc_html_e( ' ', 'platinum-seo-pack' ); ?></a></li>
				<li class="tbicon"><a href="#psp-basic" class="basic " style="text-decoration:none;" title="Techblissonline Platinum SEO Settings"><span class=""><?php echo '<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />';  ?></span></a></li>
			</ul>
			<br /><br />
<div id="desktop" class="psptab">
<div id="TechblissonlineGoogleSnippet"><div style="max-width:600px;font-family: arial, sans-serif;padding-left: 2px; padding-top:5px;padding-bottom:10px;overflow-x:auto;">
<div style="xcolor:#093; color: #202124; padding-bottom: 2px; font-size: 14px !important;font-style: normal; xline-height: 1.2;line-height: 1.3;text-overflow: ellipsis;    overflow: hidden;white-space: nowrap;"><img style="margin: 0 0 0 0; padding: 0 0 0 0px; margin-right: 9px;height='16';width='16';" src="<?php $domainurl = home_url(); echo "https://www.google.com/s2/favicons?domain=".esc_url($domainurl); ?>" /></span><span id="techblissonlineSnippetUrl"></span><img style="margin: 0 0 -2px 0; padding: 0 0 0 4px;" src="<?php $imgurl = plugins_url('snippet-cache.png', __FILE__); echo esc_url($imgurl); ?>" /></div>
<a href="#TB_inline?height=600&amp;width=750&amp;inlineId=psp-preview-box-tbx" class="tbpsp thickbox" style="text-decoration:none;" title="Platinum SEO Preview"><div style="xtext-decoration: underline; text-decoration: none; xcolor: #1a0dab; color: #1a0dab; margin-bottom: 3px;padding-top: 2px; xfont-size: medium; font-size: 20px !important; xline-height: 1.3;line-height: 1.3 !important;max-width:584px;text-overflow: ellipsis;white-space: nowrap;"><span id="techblissonlineSnippetTitle"></span></div></a>
<div id="authorPhoto"></div>
<div style="xfont-size: small; font-size: 14px !important; xcolor: #545454;color: #3c4043; xline-height: 1.24;line-height: 1.57;width:584px;"><span style="color: #808080;" id="techblissonlineSnippetDate"></span><span id="techblissonlineSnippetDescription"></span></div>
</div>
<div style="float:left;margin-top:5px;max-width:600px;"><div class="serpInfo" style="font-size: 13px !important; font-weight: bold;"><?php esc_html_e('Desktop Metrics: ', 'platinum-seo-pack'); ?></div><div class="serpInfo"><?php esc_html_e('Permalink Metrics: ', 'platinum-seo-pack'); ?> <span id="urlInfo"></span></div><div class="serpInfo"><?php esc_html_e('Title Metrics: ', 'platinum-seo-pack'); ?> <span id="titleInfo"></span></div><div class="serpInfo"><?php esc_html_e('Meta Description Metrics: ', 'platinum-seo-pack'); ?> <span id="descriptionInfo"></span></div><a class="permalink" id="tools" href="https://techblissonline.com/tools/" target="_blank">All SEO Tools</a> -  
<a class="permalink" id="schemagenerator" href="https://techblissonline.com/tools/json-schema-generator/" target="_blank"><?php esc_html_e('Techblissonline Json Schema Editor', 'platinum-seo-pack'); ?></a>| <a class="permalink" id="seoanalysis" href="https://techblissonline.com/tools/seo-analysis/" target="_blank"><?php esc_html_e('Techblissonline Onpage SEO analysis Tool', 'platinum-seo-pack'); ?></a></div>
</div>
</div>
<div id="mobile" class="psptab">
<div id="TechblissonlinemGoogleSnippet"><div style="max-width:270px;font-family: Roboto,HelveticaNeue,Arial,sans-serif;padding: 15px;text-overflow:ellipsis;border-style: solid;
  border-color: lightgrey;">
<div style="xcolor:#006621; color: #3C4043; font-size: 12px !important; xline-height: 1.2;line-height: 20px;;text-overflow: ellipsis;    overflow: hidden;white-space: nowrap;"><img style="margin: 0 0 -2px 0; padding: 1 0 0 0px; margin-right: 12px;height='16';width='16';" src="<?php $domainurl = home_url(); echo "https://www.google.com/s2/favicons?domain=".esc_url($domainurl); ?>" /></span><span id="techblissonlineMSnippetUrl"></span></div>
<a href="#TB_inline?height=600&amp;width=750&amp;inlineId=psp-preview-box-tbx" class="tbpsp thickbox" style="text-decoration:none;" title="Platinum SEO Preview"><div style="xtext-decoration: underline; text-decoration: none; xcolor: #1a0dab; color: #1a0dab; xfont-size: medium; font-size: 16px !important; xline-height: 1.3;line-height: 20px !important;padding-top: 1px; margin-bottom: -1px;"><span id="techblissonlineMSnippetTitle"></span></div></a>
<div id="MauthorPhoto"></div>
<div style="xfont-size: small; font-size: 14px !important; xcolor: #444;color: #545454; xline-height: 1.24;line-height: 20px !important;margin-bottom: -1px;padding-top: 1px;"><span style="color: #808080;" id="techblissonlineMSnippetDate"></span><span id="techblissonlineMSnippetDescription"></span></div>
<div style="float:left; margin-top:5px;max-width:600px;"><div class="serpInfo" style="font-size: 13px !important; font-weight: bold;margin-top: 16px;"><?php esc_html_e('Mobile Metrics: ', 'platinum-seo-pack'); ?></div><div class="serpInfo"><?php esc_html_e('Permalink Metrics: ', 'platinum-seo-pack'); ?> <span id="murlInfo"></span></div><div class="serpInfo"><?php esc_html_e('Title Metrics: ', 'platinum-seo-pack'); ?> <span id="mtitleInfo"></span></div><div class="serpInfo"><?php esc_html_e('Meta Description Metrics: ', 'platinum-seo-pack'); ?> <span id="mdescriptionInfo"></span></div><a class="permalink" id="tools" href="https://techblissonline.com/tools/" target="_blank">All SEO Tools</a> -  
<a class="permalink" id="mschemagenerator" href="https://techblissonline.com/tools/json-schema-generator/" target="_blank"><?php esc_html_e('Techblissonline Json Schema Editor', 'platinum-seo-pack'); ?></a>| <a class="permalink" id="mseoanalysis" href="https://techblissonline.com/tools/seo-analysis/" target="_blank"><?php esc_html_e('Techblissonline Onpage SEO analysis Tool', 'platinum-seo-pack'); ?></a></div></div>

</div>
</div>
</div></div><?php /***psp preview box***/ ?>
<span style="font-family: arial, sans-serif;xfont-size:medium;font-size: 20px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="titleSizer"></span><span style="font-family: arial, sans-serif;xfont-size:medium;font-size: 20px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="actualTitleSizer"></span><span style="font-family: arial, sans-serif;xfont-size:medium;font-size: 20px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="mtitleSizer"></span><span style="font-family: arial, sans-serif;xfont-size:small;font-size: 13px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="descriptionSizer"></span><span style="font-family: arial, sans-serif;xfont-size:small;font-size: 13px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="mdescriptionSizer"></span><span style="font-family: arial, sans-serif;xfont-size:small;font-size: 16px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="permalinkSizer"></span><span style="font-family: arial, sans-serif;xfont-size:small;font-size: 16px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="urlSizer"></span><span style="font-family: arial, sans-serif;xfont-size:small;font-size: 16px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="murlSizer"></span><span style="font-family: arial, sans-serif;font-size:small;position:absolute;visibility:hidden;white-space:nowrap;" id="pspHomePermalink"><?php echo esc_url(home_url('/')); ?></span><span style="font-family: arial, sans-serif;font-size:small;position:absolute;visibility:hidden;white-space:nowrap;" id="pspPostPermalink"><?php echo esc_url(isset($_GET['post']) ? get_permalink( sanitize_key($_GET['post']) ) : (isset($_GET['tag_ID'], $_GET['taxonomy']) ? get_term_link(get_term_by('id', sanitize_key($_GET['tag_ID']), sanitize_key($_GET['taxonomy']))) : '')); ?></span><span style="font-family: arial, sans-serif;font-size:small;position:absolute;visibility:hidden;white-space:nowrap;" id="pspTaxPermalink"><?php echo esc_url(isset($_GET['tag_ID'], $_GET['taxonomy']) ? get_term_link(get_term_by('id', sanitize_key($_GET['tag_ID']), sanitize_key($_GET['taxonomy']))) : ''); ?></span><span style="font-family: arial, sans-serif;font-size:small;position:absolute;visibility:hidden;white-space:nowrap;" id="pspPostTypeFormat"><?php $psp_pt_instance = PspPtsSeoMetas::get_instance(); $psp_tax_instance = PspTaxSeoMetas::get_instance();	 global $pagenow;$seo_title = (isset($pagenow) && $pagenow == 'post.php' ? $psp_pt_instance->get_pt_psp_title($post) : (isset($pagenow) && $pagenow == 'term.php' ? $psp_tax_instance->get_tax_psp_title() : $psp_pt_instance->get_pt_psp_title($post))); echo (isset($seo_title) ? esc_html($seo_title) : ''); ?></span><span style="font-family: arial, sans-serif;font-size:small;position:absolute;visibility:hidden;white-space:nowrap;" id="pspPostTypeArrayFormat"><?php $psp_pt_instance = PspPtsSeoMetas::get_instance(); $psp_tax_instance = PspTaxSeoMetas::get_instance();	 $seo_title_arr = (isset($pagenow) && $pagenow == 'post.php' ? $psp_pt_instance->get_pt_psp_title($post, true) : (isset($pagenow) && $pagenow == 'term.php' ? $psp_tax_instance->get_tax_psp_title(true) : $psp_pt_instance->get_pt_psp_title($post, true))); echo (isset($seo_title_arr) ? json_encode($seo_title_arr) : ''); ?></span><span style="font-family: arial, sans-serif;font-size:small;position:absolute;visibility:hidden;white-space:nowrap;" id="pspPostPermalinkStruct"><?php
   
   global $wp_rewrite;
    $permalink_structure_for_posts = $wp_rewrite->permalink_structure;
   echo esc_html($permalink_structure_for_posts);
   wp_enqueue_script( 'psp-bs-js', plugins_url( '/js/pspbsjs.js', __FILE__ ) );
   wp_enqueue_script( 'psp-snippet', plugins_url( '/js/pspsnippet.js', __FILE__ ), array('jquery', 'jquery-ui-tabs' ));
   wp_enqueue_script( 'psp-bs-toggler-js', plugins_url( '/js/pspbstoggler.js', __FILE__ ) );
    wp_enqueue_style("'psp-bs-toggler-css", plugins_url( '/css/psp-bs-toggle.css', __FILE__ ));
    wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
	wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), '2.3.5');
	//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
?></span>
<style>
label {
	font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
	font-size: 14 px;
}
.pspeditor {
    max-width: 400px;
}
.psp-preview-tabs, .psp-preview {
    float:right;
}

#TB_ajaxWindowTitle {
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
	font-weight: bold;
	font-size: 20 px;
}

.thickbox-loading {
    min-height: 450px!Important;
    min-width: 600px!Important;
    
}

#techblissonlineseoanalysis {
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
	font-size: 20 px;
}

.dashicons-yes-alt {
    color: green !important;
}

.psptip {
    color: #555 !important;
    text-decoration: none;
}

.dashicons-dismiss {
    color: red !important;
}

.psp-info {
    color: #3073AC !important;
}

#psp-meta-box .psp-metabox-tabs, #psp-preview-box .psp-preview-tabs {
    background: #fff;
    border-width: 0px 1px 1px 1px; 
}
.psp-metabox-tabs .ui-state-active a {
    color:white !important;
    border-color: #3c8243 !important;
    background: #0073aa !important;
   
}
.psp-preview-tabs .ui-state-active a {
    color:white !important;
    border-color: #3c8243 !important;
    background: #0073aa !important;
   
}
</style>
<div id="psp-basic">
<div id="psp-basic-settings">
<input type="hidden" name="psp_terms_json_str" id="psp_terms_json_str" value='<?php echo ( isset($psp_bc_tax_terms_string) ? $psp_bc_tax_terms_string : ''); ?>' />    
<table class="form-table">
	<tr  class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[title]"><?php esc_html_e('SEO Title: ', 'platinum-seo-pack'); ?></label><br /><?php if ($psp_type == "posttype" && (!$psp_posttype_metabox_advanced_hidden || is_super_admin())) { ?><?php if( isset($psp_seo_meta['disable_title']) && $psp_seo_meta['disable_title']) { $distit_checked = ' checked="checked" '; } else { $distit_checked = ""; }
					echo "<div class='psp-bs'><input ".esc_attr($distit_checked)." id='psp_seo_meta[disable_title]' name='psp_seo_meta[disable_title]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' data-style='ios'/></div>";	
			?><?php } ?></th>
		<td><input type="text" name="psp_seo_meta[title]" placeholder="<?php esc_html_e('Write a brief unique title satisfying user query intent', 'platinum-seo-pack');?>" id="psp_seo_meta[title]" value="<?php echo ( isset($psp_seo_meta['title']) ? html_entity_decode(stripcslashes(esc_attr($psp_seo_meta['title']))) : ''); ?>"><br />
		<p class="description"><?php esc_html_e('Set SEO Title to be used for this page. Switching this off will mean the default WordPress title will be used.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr  class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[titleformat]"><?php esc_html_e('Title Format: ', 'platinum-seo-pack'); ?></label><br /><?php if ($psp_type == "posttype" && (!$psp_posttype_metabox_advanced_hidden || is_super_admin())) { ?><?php if ( isset($psp_seo_meta['disable_title_format']) && $psp_seo_meta['disable_title_format']) { $distif_checked = ' checked="checked" '; } else { $distif_checked =""; }
					echo "<div class='psp-bs'><input ".esc_attr($distif_checked)." id='psp_seo_meta[disable_title_format]' name='psp_seo_meta[disable_title_format]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' data-style='ios'/></div><br />";	
			?><?php } ?></th>
		<td><input type="text" name="psp_seo_meta[titleformat]" id="psp_seo_meta[titleformat]" value="<?php echo ( isset($psp_seo_meta['titleformat']) ? esc_attr($psp_seo_meta['titleformat']) : ''); ?>" readonly><br />
		<p class="description"><?php esc_html_e('Set SEO Title Format to be used for this post/page.Default is sitewide format set for this post type.', 'platinum-seo-pack'); ?></p>
		<?php	if ( ! empty( $pspavailableTags ) ) :	?>
        	    <p><?php esc_html_e( 'Available tags:', 'platinum-seo-pack' ); ?></p>
            	<ul role="list">
            		<?php 
            		foreach ( $pspavailableTags as $tag ) {
            			?>
            			<li class="psp">
            				<button type="button" data-added="<?php echo esc_attr( $tag );  ?>" data-id="<?php echo esc_attr( 'psp_seo_meta[titleformat]' );  ?>"
            						class="pspbutton button button-secondary">
            					<?php echo '%' . esc_html($tag) . '%'; ?>
            				</button>
            			</li>
            			<?php
            		}
            		?>
            	</ul>
            <?php endif; ?>
		</td>
	</tr>
	<tr  class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[description]"><?php esc_html_e('Description: ', 'platinum-seo-pack'); ?></label><br /><?php if ($psp_type == "posttype" && (!$psp_posttype_metabox_advanced_hidden || is_super_admin())) { ?><?php if( isset($psp_seo_meta['disable_description']) && $psp_seo_meta['disable_description']) { $disdes_checked = ' checked="checked" '; } else { $disdes_checked = ""; }
					echo "<div class='psp-bs'><input ".esc_attr($disdes_checked)." id='psp_seo_meta[disable_description]' name='psp_seo_meta[disable_description]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' data-style='ios' /></div><br />";	
			?><?php } ?></th>
		<td><textarea name="psp_seo_meta[description]" placeholder="<?php esc_html_e("Write a unique description that is short enough to fit search results snippet for this page. An ideal meta description is one that has a brief content matching what the user is searching for and inducing him to click through.", 'platinum-seo-pack');?>" rows="5" id="psp_seo_meta[description]"><?php echo ( isset($psp_seo_meta['description']) ? html_entity_decode(stripcslashes(esc_textarea($psp_seo_meta['description']))) : ''); ?></textarea><br />
		<p class="description"><?php esc_html_e('Set meta desciption to be used for this post/page.Switching this Off will mean this description will not be used and an auto-generated description will be used, if that had been turned on sitewide in Platinum SEO General Settings.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr  class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[maxsnippet]"><?php esc_html_e('Max Snippet: ', 'platinum-seo-pack'); ?></label></th>
		<td><input style="width:15%;" type="number" min="-1" name="psp_seo_meta[maxsnippet]" id="psp_seo_meta[maxsnippet]" maxlength="3" value="<?php echo ( isset($psp_seo_meta['maxsnippet']) ? ($psp_seo_meta['maxsnippet'] == 'zero' ? 0: esc_attr($psp_seo_meta['maxsnippet'])) : ''); ?>" /><?php esc_html_e(' Characters. [Optional]', 'platinum-seo-pack'); ?><br />
		<p class="description"><?php echo esc_html__('Set max snippet length directive to Google for this post/page.', 'platinum-seo-pack').' <a href="https://techblissonline.com/" target="_blank">'.esc_html__('Read more', 'platinum-seo-pack').'...</a>'; ?></p>
		</td>
	</tr>
	<?php if ($psp_type == "posttype" && (!$psp_posttype_metabox_advanced_hidden || is_super_admin())) { ?>
	<?php global $post_type; if ( 'page' != $post_type ) { ?>
	<tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[preferred_tax]"><?php esc_html_e('Preferred Taxonomy for Breadcrumb Trail: ', 'platinum-seo-pack'); ?></label></th>
		<td><select id="psp_seo_meta[preferred_tax]" name="psp_seo_meta[preferred_tax]"><?php 
		foreach($psp_bc_taxonomies as $key => $val) {  
			$selected = (isset($psp_seo_meta['preferred_tax']) && $psp_seo_meta['preferred_tax']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select><p class="description"><?php esc_html_e(' Select the preferred taxonomy whose term you prefer to use for this page, if the page is tagged to multiple taxonomies.', 'platinum-seo-pack'); ?></p>		
		</td>
	</tr>
	<tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[preferred_term]"><?php esc_html_e('Preferred Term for this Page: ', 'platinum-seo-pack'); ?></label></th>
		<td><select id="psp_seo_meta[preferred_term]" name="psp_seo_meta[preferred_term]" oldValue=""><?php 
		foreach($psp_bc_tax_term_items as $key => $val) {  
			$selected = (isset($psp_seo_meta['preferred_term']) && $psp_seo_meta['preferred_term']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select><p class="description"><?php esc_html_e(' Select the preferred Term under the selected Preferred Taxonomy that you like to use for this page.', 'platinum-seo-pack'); ?></p>		
		</td>
	</tr>
	<?php }} ?>
	<?php if ($psp_type == "posttype" && (!$psp_posttype_metabox_advanced_hidden || is_super_admin())) { ?>
	<tr class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[disable_psp]"><?php esc_html_e('Platinum SEO Settings: ', 'platinum-seo-pack'); ?></label></th>
		<td><?php if ( isset($psp_seo_meta['disable_psp']) && $psp_seo_meta['disable_psp']) { $dispsp_checked = ' checked="checked" '; } else { $dispsp_checked = ""; }
					echo "<div class='psp-bs'><input ".esc_attr($dispsp_checked)." id='psp_seo_meta[disable_psp]' name='psp_seo_meta[disable_psp]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' data-width='100' /></div>";	
			?>
			<p class="description"><?php esc_html_e('Switching this Off Disables ALL Platinum SEO settings for this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<?php } ?>
</table>
</div></div><?php /***psp basic***/ ?>