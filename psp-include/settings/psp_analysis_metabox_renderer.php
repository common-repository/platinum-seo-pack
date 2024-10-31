<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/ 
?>
<div id="pspanalysispar">
<div id="pspanalysis">
<table class="form-table">
	<tr  class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="psp_seo_meta[keywords]"><?php esc_html_e('Keywords: ', 'platinum-seo-pack'); ?></label><br /><?php if ($psp_type == "posttype" && (!$psp_posttype_metabox_advanced_hidden || is_super_admin())) { ?><?php if(isset($psp_seo_meta['disable_keywords']) && $psp_seo_meta['disable_keywords']) { $diskey_checked = ' checked="checked" '; } else { $diskey_checked =""; }
					echo "<div class='psp-bs'><input ".esc_attr($diskey_checked)." id='psp_seo_meta[disable_keywords]' name='psp_seo_meta[disable_keywords]' type='checkbox' data-toggle='toggle' data-on='Off' data-onstyle='default' data-off='On' data-offstyle='success' data-style='ios' /></div><br />";	
			?><?php } ?></th>
		<td><input type="text" name="psp_seo_meta[keywords]" id="psp_seo_meta[keywords]" value="<?php echo ( isset($psp_seo_meta['keywords']) ? esc_attr($psp_seo_meta['keywords']) : ''); ?>"><br />
		<p class="description"><?php esc_html_e('Set comma separated keywords or entities for this post/page. These are used as the Focus or primary keywords for Platinum SEO Analysis of this page. If you intend using these in meta keywords tag, which is not supported by Google and many other Search Engines, you should turn it on sitewide in Platinum SEO General setthings. By default, these are used for your internal Post/Page analysis only.', 'platinum-seo-pack'); ?></p>
		<p class="description"><strong><?php esc_html_e('Enter comma separated focus keywords in the input above and find the analysis below', 'platinum-seo-pack'); ?></strong></p>
		<div class="psp-bs"><input style="width:20%;display:block;margin:auto" id="psp_analyse_btn" class="psp_analyser btn btn-primary" type="button" value="Analyse" /></div>
		</td>
	</tr>
	</table>
	<span style="font-family: arial, sans-serif;xfont-size:medium;font-size: 20px!important;position:absolute;visibility:hidden;white-space:nowrap;" id="actualTitleSizers"></span>
<div id="techblissonlineseoanalysis"></div>
</div> <?php /***psp analysis***/ ?>
</div> <?php /***psp analysis par***/ ?>