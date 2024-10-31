<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/ 
?>
<p class="description"><?php esc_html_e( 'These are some settings specific to this page (post). If these are not set, appropriate defaults will be used for meta tags.However, you need to manually set the combined Json Schema for this page in the below mentioned option for Json schema support. ' , 'platinum-seo-pack'); ?> </p>
<table class="form-table"> 
	<tr class="form-field">
    <th scope="row" valign="top"><label for="psp_index"><?php esc_html_e('Add index/noindex:', 'platinum-seo-pack') ?> </label></th>
    <td><?php if ( !empty($psp_seo_meta['noindex'])) { $noindex_checked = ' checked="checked" '; } else { $noindex_checked =""; }
					echo "<div class='psp-bs'><input ".esc_attr($noindex_checked)." id='psp_seo_meta[noindex]' name='psp_seo_meta[noindex]' type='checkbox' data-toggle='toggle' data-on='NoIndex' data-off='Index' data-onstyle='danger' data-offstyle='primary' data-width='100' /></div>";	
			?>
    <p class="description"><?php esc_html_e('noindex - Tells search engines to not include this page in the index or to not have a copy of this page in their database. It thus tells search engines to not consider this page for showing to their users on their SERPS.', 'platinum-seo-pack'); ?></p>
    </td>
    </tr>
    
   <tr class="form-field">
    <th scope="row" valign="top"><label for="psp_index"><?php esc_html_e('Add follow/nofollow:', 'platinum-seo-pack') ?> </label></th>
    <td><?php if ( !empty($psp_seo_meta['nofollow'])) { $nofollow_checked = ' checked="checked" '; } else { $nofollow_checked =""; }
					echo "<div class='psp-bs'><input ".esc_attr($nofollow_checked)." id='psp_seo_meta[nofollow]' name='psp_seo_meta[nofollow]' type='checkbox' data-toggle='toggle' data-on='NoFollow' data-off='Follow' data-onstyle='warning' data-offstyle='primary' data-width='100' /></div>";	
			?>
    <p class="description"><?php esc_html_e('nofollow - Tells search engines to not follow links on this page, meaning to not pass on link credits, if any, assigned by the search engines to the link assignee.', 'platinum-seo-pack'); ?></p>
    </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="psp_seo_meta[noarchive]"><?php esc_html_e('Add noarchive: ', 'platinum-seo-pack'); ?></label></th>	
		<td>
		<?php if ( isset($psp_seo_meta['noarchive']) && $psp_seo_meta['noarchive']) { $noarchive_checked = ' checked="checked" '; } else { $noarchive_checked = ""; }
					echo "<div class='psp-bs'><input ".esc_attr($noarchive_checked)." id='psp_seo_meta[noarchive]' name='psp_seo_meta[noarchive]' type='checkbox' data-toggle='toggle' data-onstyle='warning'/></div>";	
			?>
			<p class="description"><?php esc_html_e('noarchive - Tells search engines to not show Cached link in SERPS (Search Engine Result Pages) for this page. It thus tells search engines not to store a cached copy of the page.', 'platinum-seo-pack'); ?></p>
        </td> 		
    </tr>	
    <tr class="form-field">	
		<th scope="row" valign="top"><label for="psp_seo_meta[nosnippet]"><?php esc_html_e('Add nosnippet: ', 'platinum-seo-pack'); ?></label></th>	
		<td>
		<?php if ( isset($psp_seo_meta['nosnippet']) && $psp_seo_meta['nosnippet']) { $nosnippet_checked = ' checked="checked" '; } else { $nosnippet_checked = ""; }
					echo "<div class='psp-bs'><input ".esc_attr($nosnippet_checked)." id='psp_seo_meta[nosnippet]' name='psp_seo_meta[nosnippet]' type='checkbox' data-toggle='toggle' data-onstyle='warning'/></div>";	
			?>	
        <p class="description"><?php esc_html_e('nosnippet - Tells search engines to not show snippet (description) in SERPS (Search Engine Result Pages) for this page. It also tells search engines not to show a cached link in SERPS for this page.', 'platinum-seo-pack'); ?></p>			
		</td> 		
    </tr>
	<tr class="form-field">	
		<th scope="row" valign="top"><label for="psp_seo_meta[noimageindex]"><?php esc_html_e('Add noimageindex: ', 'platinum-seo-pack'); ?></label></th>
        <td>	
		<?php if ( isset($psp_seo_meta['noimageindex']) && $psp_seo_meta['noimageindex']) { $noimgidx_checked = ' checked="checked" '; } else { $noimgidx_checked = ""; }
					echo "<div class='psp-bs'><input ".esc_attr($noimgidx_checked)." id='psp_seo_meta[noimageindex]' name='psp_seo_meta[noimageindex]' type='checkbox' data-toggle='toggle' data-onstyle='warning'/></div>";	
			?>
        <p class="description"><?php esc_html_e('noimageindex - Tells search engines like google to not index any image  on this page. It must however be remembered that if any of the image is linked to by some other page, it will get indexed.', 'platinum-seo-pack'); ?></p>			
		</td> 		
    </tr>
    <tr  class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[maxvideo]"><?php esc_html_e('Max Video Preview: ', 'platinum-seo-pack'); ?></label></th>
		<td><input style="width:15%;" type="number" min="-1" name="psp_seo_meta[maxvideo]" id="psp_seo_meta[maxvideo]" size="3" maxlength="3" value="<?php echo ( isset($psp_seo_meta['maxvideo']) ? ($psp_seo_meta['maxvideo'] == 'zero' ? 0 : esc_attr($psp_seo_meta['maxvideo'])) : ''); ?>" /><?php esc_html_e(' Seconds. [Optional]', 'platinum-seo-pack'); ?><br />
		<p class="description"><?php echo esc_html__('Set max video preview length directive to Google for this post/page.', 'platinum-seo-pack').' <a href="https://techblissonline.com/" target="_blank" rel="noopener">'.esc_html__('Read more', 'platinum-seo-pack').'...</a>'; ?></p>
		</td>
	</tr>
	<tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[maximage]"><?php esc_html_e('Max Image Preview [optional]: ', 'platinum-seo-pack'); ?></label></th>
		<td><select id="psp_seo_meta[maximage]" name="psp_seo_meta[maximage]"><?php $dditems = array ('' => 'Select ', 'none' => 'None', 'standard' => 'Standard', 'large' => 'Large');
		foreach($dditems as $key => $val) {
			$selected = (isset($psp_seo_meta['maximage']) && $psp_seo_meta['maximage']==$key) ? esc_attr('selected="selected"') : '';
			echo "<option value='".esc_attr($key)."'".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select><p class="description"><?php esc_html_e(' Select the max image preview directive to Google for this post/page.', 'platinum-seo-pack'); ?></p>		
		</td>
	</tr>
	<tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[canonical_url]"><?php esc_html_e('Canonical URL: ', 'platinum-seo-pack'); ?></label><br /><?php if ($psp_type == "posttype" && (!$psp_posttype_metabox_advanced_hidden || is_super_admin())) { ?><?php if( isset($psp_seo_meta['disable_canonical']) && $psp_seo_meta['disable_canonical']) { $discan_checked = ' checked="checked" '; } { $discan_checked = ""; }
					echo "<div class='psp-bs'><input ".esc_attr($discan_checked)." id='psp_seo_meta[disable_canonical]' name='psp_seo_meta[disable_canonical]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' data-style='ios' /></div>";	
			?><?php } ?></th>
		<td><input type="text" name="psp_seo_meta[canonical_url]" id="psp_seo_meta[canonical_url]" value="<?php echo (isset($psp_seo_meta['canonical_url']) ? esc_url($psp_seo_meta['canonical_url']) : ''); ?>"><br />
		<p class="description"><?php esc_html_e('Set the canonical URL to be used for this page. Leave this empty for canonical URL to default to permalink of this page. Cross domain canonical URL may also be set here to handle duplicates.When this is switched Off, Canonical meta tag will not be generated by Platinum SEO for this page. ', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	 <tr class="form-field">
        <th scope="row" valign="top"><label for="psp_seo_meta[nositemap]"><?php esc_html_e('Exclude from sitemap: ', 'platinum-seo-pack'); ?></label></th>	
		<td>
		<?php if ( isset($psp_seo_meta['nositemap']) && $psp_seo_meta['nositemap']) { $nositemap_checked = ' checked="checked" '; } else { $nositemap_checked = ""; }
					echo "<div class='psp-bs'><input ".esc_attr($nositemap_checked)." id='psp_seo_meta[nositemap]' name='psp_seo_meta[nositemap]' type='checkbox' data-toggle='toggle' data-onstyle='warning' /></div>";	
			?>	
			<p class="description"><?php esc_html_e('Switching this ON tells sitemap generator to not include this page in the sitemap. All NoIndex Pages are excluded from the Platinum SEO WordPress sitemap by default and you need not have to enable this again.', 'platinum-seo-pack'); ?></p>
        </td> 		
    </tr>
	<tr  class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[schema_string]"><?php esc_html_e('JSON_LD Schema String: ', 'platinum-seo-pack'); ?></label><br /><br /><i><a class="permalink" id="schemagenerator" href="https://techblissonline.com/tools/json-schema-generator/" target="_blank" rel="noopener"><?php esc_html_e('Techblissonline Json Schema Generator Tool', 'platinum-seo-pack'); ?></a></i><br /><br /><i><a class="permalink" id="joinschemas" href="https://techblissonline.com/combine-json-schema/" target="_blank" rel="noopener"><?php esc_html_e('How to combine several Json schema?', 'platinum-seo-pack'); ?></a></i></th>
		<td><div class='pspeditor'><textarea name="psp_seo_meta[schema_string]" id="psp_seo_meta[schema_string]" class="pspjsoneditor"><?php echo ( isset($psp_seo_meta['schema_string']) ? (esc_textarea(stripcslashes($psp_seo_meta['schema_string']))) : ' '); ?></textarea></div><br />
		<p class="description"><?php echo esc_html__('Set JSON_LD Schema String (without the script tag) for this post/page. Use', 'platinum-seo-pack').' <a href="https://techblissonline.com/tools/json-schema-generator/" target="_blank" rel="noopener">'.esc_html__('Techblissonline Schema Generator Tool', 'platinum-seo-pack').'</a>'; ?></p>
		</td>
	</tr>

        <tr class="form-field">				
		<th scope="row" valign="top"><label for=""><?php esc_html_e('Redirect Settings ', 'platinum-seo-pack'); ?></label></th>
		<td></td>
	</tr>	
	<tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[redirect_to_url]"><?php esc_html_e('Redirect To: ', 'platinum-seo-pack'); ?></label></th>
		<td><input type="text" name="psp_seo_meta[redirect_to_url]" id="psp_seo_meta[redirect_to_url]" value="<?php echo (isset($psp_seo_meta['redirect_to_url']) ? esc_url_raw($psp_seo_meta['redirect_to_url']) : ''); ?>"><br />
		<p class="description"><?php esc_html_e('Set the URL to redirect users and crawlers landing on this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[redirect_status_code]"><?php echo esc_html__('Use: ', 'platinum-seo-pack').'<br>'.'<a href="https://techblissonline.com/http-redirection-status-codes-301-302-307-308/" target="_blank" rel="noopener">'.esc_html__('What to Select?', 'platinum-seo-pack').'</a>'; ?></label></th>
		<td><select id="psp_seo_meta[redirect_status_code]" name="psp_seo_meta[redirect_status_code]"><?php $dditems = array('' => 'Select a redirection method', '301' => '301 Moved Permanently', '302' => '302 Found', '303' => '303 See Other', '307' => '307 Temporary Redirect');
		foreach($dditems as $key => $val) {    
			$selected = (isset($psp_seo_meta['redirect_status_code']) && $psp_seo_meta['redirect_status_code']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_html($val)."</option>";
		} ?></select><p class="description"><?php esc_html_e(' Set the HTTP status code to use for this redirection. It is highly recommended to use 301 redirects in most cases, except where the redirection is of temporary nature.', 'platinum-seo-pack'); ?></p>		
		</td>
	</tr>
	<?php if ($psp_type == "posttype" && 1 == 0) { ?>
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
   <?php } ?>
   <?php if (1 == 0) { ?>
    <tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for=""><?php esc_html_e('Disable Settings ', 'platinum-seo-pack'); ?></label></th>
		<td></td>
	</tr>	
	<tr class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[disable_title]"><?php esc_html_e('Platinum SEO Title: ', 'platinum-seo-pack'); ?></label></th>
		<td><?php if( isset($psp_seo_meta['disable_title']) && $psp_seo_meta['disable_title']) { $distit_checked = ' checked="checked" '; } else { $distit_checked = ""; }
					echo "<input ".esc_attr($distit_checked)." id='psp_seo_meta[disable_title]' name='psp_seo_meta[disable_title]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' /><br />";	
			?>
		<p class="description"><?php esc_html_e('Switching this Off Disables Platinum SEO title for this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[disable_description]"><?php esc_html_e('Platinum SEO Meta Description: ', 'platinum-seo-pack'); ?></label></th>
		<td><?php if( isset($psp_seo_meta['disable_description']) && $psp_seo_meta['disable_description']) { $disdes_checked = ' checked="checked" '; } else { $disdes_checked = ""; }
					echo "<input ".esc_attr($disdes_checked)." id='psp_seo_meta[disable_description]' name='psp_seo_meta[disable_description]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' /><br />";	
			?>
		<p class="description"><?php esc_html_e('Switching this Off Disables Platinum SEO meta description for this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[disable_keywords]"><?php esc_html_e('Platinum SEO Meta Keywords: ', 'platinum-seo-pack'); ?></label></th>
		<td><?php if(isset($psp_seo_meta['disable_keywords']) && $psp_seo_meta['disable_keywords']) { $diskey_checked = ' checked="checked" '; } else { $diskey_checked =""; }
					echo "<input ".esc_attr($diskey_checked)." id='psp_seo_meta[disable_keywords]' name='psp_seo_meta[disable_keywords]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' /><br />";	
			?>
		<p class="description"><?php esc_html_e('Switching this Off Disables Platinum SEO meta keywords for this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[disable_canonical]"><?php esc_html_e('Platinum SEO Canonical URL: ', 'platinum-seo-pack'); ?></label></th>
		<td><?php if( isset($psp_seo_meta['disable_canonical']) && $psp_seo_meta['disable_canonical']) { $discan_checked = ' checked="checked" '; } { $discan_checked = ""; }
					echo "<input ".esc_attr($discan_checked)." id='psp_seo_meta[disable_canonical]' name='psp_seo_meta[disable_canonical]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' /><br />";	
			?>
		<p class="description"><?php esc_html_e('Switching this Off Disables canonical URL generated by Platinum SEO for this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[disable_title_format]"><?php esc_html_e('Platinum SEO Title Format Settings: ', 'platinum-seo-pack'); ?></label></th>
		<td><?php if ( isset($psp_seo_meta['disable_title_format']) && $psp_seo_meta['disable_title_format']) { $distif_checked = ' checked="checked" '; } else { $distif_checked =""; }
					echo "<input ".esc_attr($distif_checked)." id='psp_seo_meta[disable_title_format]' name='psp_seo_meta[disable_title_format]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' /><br />";	
			?>
		<p class="description"><?php esc_html_e('Switching this Off Disables Platinum SEO title format settings for this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[disable_desc_format]"><?php esc_html_e('Platinum SEO Description Format Settings: ', 'platinum-seo-pack'); ?></label></th>
		<td><?php if ( isset($psp_seo_meta['disable_desc_format']) && $psp_seo_meta['disable_desc_format']) { $disdef_checked = ' checked="checked" '; } else { $disdef_checked = ""; }
					echo "<input ".esc_attr($disdef_checked)." id='psp_seo_meta[disable_desc_format]' name='psp_seo_meta[disable_desc_format]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' /><br />";	
			?>
		<p class="description"><?php esc_html_e('Switching this Off Disables Platinum SEO meta description format settings for this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[disable_psp]"><?php esc_html_e('Platinum SEO Settings: ', 'platinum-seo-pack'); ?></label></th>
		<td><?php if ( isset($psp_seo_meta['disable_psp']) && $psp_seo_meta['disable_psp']) { $dispsp_checked = ' checked="checked" '; } else { $dispsp_checked = ""; }
					echo "<input ".esc_attr($dispsp_checked)." id='psp_seo_meta[disable_psp]' name='psp_seo_meta[disable_psp]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' /><br />";	
			?>
			<p class="description"><?php esc_html_e('Switching this Off Disables ALL Platinum SEO settings for this page.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<?php } ?>
</table>
<?php } else { ?>
</table>
<?php } ?>