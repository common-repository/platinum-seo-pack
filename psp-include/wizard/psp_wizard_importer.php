<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
?>
 <?php  
	$import_yoast_data_nonce = wp_create_nonce( 'import_yoast_data_nonce' ); 
	// in JavaScript, object properties are accessed as ajax_object.ajax_url, 
	//wp_enqueue_script( 'psp-ajax-import-script', plugins_url( '/js/psp-importer.js', __FILE__ ), array('jquery') );
	wp_enqueue_script( 'psp-ajax-import-script', plugins_url( 'settings/js/psp-importer.js', PSP_PLUGIN_SETTINGS_URL ), array('jquery'), '2.1.8' );
	wp_localize_script( 'psp-ajax-import-script', 'psp_ajax_importer_object', array( 'ajax_url' => admin_url( 'admin-ajax.php'), 'pspnonce' => $import_yoast_data_nonce) );
	//wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
	wp_enqueue_style("psp-settings-bs-css", plugins_url( 'settings/css/psp-settings-bs.css', PSP_PLUGIN_SETTINGS_URL ));
 ?>
 <style>
/*.psp-sidebar{width:15%;background-color:#fff;position:fixed!important;z-index:1;overflow:auto}*/
.psp-sidebar{background-color:#fff;z-index:1;overflow:auto}
.psp-bar-block .psp-dropdown-hover .psp-button,.psp-bar-block .psp-dropdown-click .psp-button{width:100%;text-align:left;padding:8px 16px}
.psp-bar-block .psp-bar-item{width:100%;display:block;padding:8px 16px;text-align:left;border:none;white-space:normal;float:none;outline:0;cursor:pointer;}
.psp-bar-block.psp-center .psp-bar-item{text-align:center}.psp-block{display:block;width:100%}
.psp-card,.psp-card-2{box-shadow:0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12)}
.psp-card-4,.psp-hover-shadow:hover{box-shadow:0 4px 10px 0 rgba(0,0,0,0.2),0 4px 20px 0 rgba(0,0,0,0.19)}
.psp-black,.psp-hover-black:hover{color:#fff!important;background-color:#000!important}
.psp-grey,.psp-hover-grey:hover,.psp-gray,.psp-hover-gray:hover{color:#000!important;background-color:#9e9e9e!important}
.psp-light-grey,.psp-hover-light-grey:hover,.psp-light-gray,.psp-hover-light-gray:hover{color:#000!important;background-color:#f1f1f1!important}
.psp-container:after,.psp-container:before,.psp-panel:after,.psp-panel:before,.psp-row:after,.psp-row:before,.psp-row-padding:after,.psp-row-padding:before,
/***animate***/
.psp-spin{animation:psp-spin 2s infinite linear}@keyframes psp-spin{0%{transform:rotate(0deg)}100%{transform:rotate(359deg)}}
.psp-animate-fading{animation:fading 10s infinite}@keyframes fading{0%{opacity:0}50%{opacity:1}100%{opacity:0}}
.psp-animate-opacity{animation:opac 0.8s}@keyframes opac{from{opacity:0} to{opacity:1}}
.psp-animate-top{position:relative;animation:animatetop 0.4s}@keyframes animatetop{from{top:-300px;opacity:0} to{top:0;opacity:1}}
.psp-animate-left{position:relative;animation:animateleft 0.4s}@keyframes animateleft{from{left:-300px;opacity:0} to{left:0;opacity:1}}
.psp-animate-right{position:relative;animation:animateright 0.4s}@keyframes animateright{from{right:-300px;opacity:0} to{right:0;opacity:1}}
.psp-animate-bottom{position:relative;animation:animatebottom 0.4s}@keyframes animatebottom{from{bottom:-300px;opacity:0} to{bottom:0;opacity:1}}
.psp-animate-zoom {animation:animatezoom 0.6s}@keyframes animatezoom{from{transform:scale(0)} to{transform:scale(1)}}
.psp-animate-input{transition:width 0.4s ease-in-out}.psp-animate-input:focus{width:100%!important}
.psp-opacity,.psp-hover-opacity:hover{opacity:0.60}.psp-opacity-off,.psp-hover-opacity-off:hover{opacity:1}
.psp-opacity-max{opacity:0.25}.psp-opacity-min{opacity:0.75}
/* Colors */
.psp-amber,.psp-hover-amber:hover{color:#000!important;background-color:#ffc107!important}
.psp-aqua,.psp-hover-aqua:hover{color:#000!important;background-color:#00ffff!important}
.psp-blue,.psp-hover-blue:hover{color:#fff!important;background-color:#2196F3!important}
.psp-light-blue,.psp-hover-light-blue:hover{color:#000!important;background-color:#87CEEB!important}
.psp-brown,.psp-hover-brown:hover{color:#fff!important;background-color:#795548!important}
.psp-cyan,.psp-hover-cyan:hover{color:#000!important;background-color:#00bcd4!important}
.psp-blue-grey,.psp-hover-blue-grey:hover,.psp-blue-gray,.psp-hover-blue-gray:hover{color:#fff!important;background-color:#607d8b!important}
.psp-green,.psp-hover-green:hover{color:#fff!important;background-color:#4CAF50!important}
.psp-light-green,.psp-hover-light-green:hover{color:#000!important;background-color:#8bc34a!important}
.psp-indigo,.psp-hover-indigo:hover{color:#fff!important;background-color:#3f51b5!important}
.psp-khaki,.psp-hover-khaki:hover{color:#000!important;background-color:#f0e68c!important}
.psp-lime,.psp-hover-lime:hover{color:#000!important;background-color:#cddc39!important}
.psp-orange,.psp-hover-orange:hover{color:#000!important;background-color:#ff9800!important}
.psp-deep-orange,.psp-hover-deep-orange:hover{color:#fff!important;background-color:#ff5722!important}
.psp-pink,.psp-hover-pink:hover{color:#fff!important;background-color:#e91e63!important}
.psp-import-class{margin-left:15%}
 </style>
 <h1><?php esc_html_e('Techblissonline Platinum SEO Importer', 'platinum-seo-pack'); ?></h1>
<div class="wrap">
    <div class="psp-bs">
    <div class="row">
   <div class="psp-sidebar psp-bar-block psp-light-grey psp-card col-sm-2">   
      <button class="psp-bar-item psp-button psp-tab  psp-cyan" onclick="openLink(event, 'Fade')">Yoast</button>
      <button class="psp-bar-item psp-button psp-tab" onclick="openLink(event, 'Right')">RankMath</button> 
      <button class="psp-bar-item psp-button psp-tab" onclick="openLink(event, 'Bottom')">Yoast Indexable</button>
      <button class="psp-bar-item psp-button psp-tab" onclick="openLink(event, 'Input')">All in One SEO</button>
      <button class="psp-bar-item psp-button psp-tab" onclick="openLink(event, 'Zoom')">Platinum SEO</button>
    </div>

<div class="col-sm-10">
<div id="Fade" class="psp-container plugin psp-animate-opacity">
     
<h2><?php esc_html_e('Import Yoast SEO Data into Platinum SEO:', 'platinum-seo-pack'); ?></h2>
<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('Settings', 'platinum-seo-pack') ?></a> | <a href="https://techblissonline.com/platinum-wordpress-seo-plugin/#what-is-new" target="_blank" rel="noopener"><?php esc_html_e('Platinum SEO WordPress Plugin', 'platinum-seo-pack') ?></a>

<br class="clear" />
<div id="pspimporter-1">
	<table class="form-table">
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Titles and Descriptions: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your Titles and Descriptions from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoast_title" id="import_yoast_title_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoast-titles-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
                </div>
            </td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Basic Robots Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your basic robots meta data (noindex, nofollow) from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoast_basicrobots" id="import_yoast_brobots_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoast-basicrobots-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Advanced Robots Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your advanced robots meta data from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoast_robots" id="import_yoast_robots_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoast-robots-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import other SEO and Social Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your other SEO and Social meta data from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoast_others" id="import_yoast_others_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoast-others-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import SEO and Social Meta Data for Terms: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your  SEO and Social meta data for Terms (Categories and other Taxonomies) from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoast_terms" id="import_yoast_terms_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoast-terms-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Yoast Premium Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your  Premium focus Keywords from your current Yoast Premium Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoast_premium" id="import_yoast_premium_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoast-premium-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:100%;" scope="row" valign="top"><label><?php esc_html_e('Set up the One time Configuration for Platinum SEO: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('Platinum SEO sets up the default configuration on installation and activation. If you are done with Importing, you may go to SEO Settings and Social Settings to review and make any necessary changes', 'platinum-seo-pack'); ?></p><p><a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('SEO Settings', 'platinum-seo-pack') ?></a> | <a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=psp-social-by-techblissonline" ?>"> <?php esc_html_e('Social Settings', 'platinum-seo-pack') ?></a></p></th>
		</tr>
	</table>
</div>

<br class="clear" />

</div>
<div id="Right" class="psp-container plugin psp-animate-right" style="display:none">
    
<h2><?php esc_html_e('Import RankMath SEO Data into Platinum SEO:', 'platinum-seo-pack'); ?></h2>
<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('Settings', 'platinum-seo-pack') ?></a> | <a href="https://techblissonline.com/platinum-wordpress-seo-plugin/#what-is-new" target="_blank" rel="noopener"><?php esc_html_e('Platinum SEO WordPress Plugin', 'platinum-seo-pack') ?></a>

<br class="clear" />
<div id="pspimporter-2">
	<table class="form-table">
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Titles and Descriptions: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your Titles and Descriptions from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="rankmath_title" id="import_rm_title_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="rm-titles-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p></div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Robots Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your robots meta data from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="rankmath_robots" id="import_rm_robots_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="rm-robots-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p></div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import other SEO and Social Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your other SEO and Social meta data from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="rankmath_others" id="import_rm_others_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="rm-others-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import SEO and Social Meta Data for Terms: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your  SEO and Social meta data for Terms (Categories and other Taxonomies) from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="rankmath_terms" id="import_rm_terms_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="rm-terms-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:100%;" scope="row" valign="top"><label><?php esc_html_e('Set up the One time Configuration for Platinum SEO: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('Platinum SEO sets up the default configuration on installation and activation. If you are done with Importing, you may go to SEO Settings and Social Settings to review and make any necessary changes', 'platinum-seo-pack'); ?></p><p><a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('SEO Settings', 'platinum-seo-pack') ?></a> | <a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=psp-social-by-techblissonline" ?>"> <?php esc_html_e('Social Settings', 'platinum-seo-pack') ?></a></p></th>
		</tr>
	</table>
</div>

<br class="clear" />

</div>
<div id="Bottom" class="psp-container plugin psp-animate-bottom" style="display:none">
     
<h2><?php esc_html_e('Import Yoast Indexable SEO Data into Platinum SEO:', 'platinum-seo-pack'); ?></h2>
<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('Settings', 'platinum-seo-pack') ?></a> | <a href="https://techblissonline.com/platinum-wordpress-seo-plugin/#what-is-new" target="_blank" rel="noopener"><?php esc_html_e('Platinum SEO WordPress Plugin', 'platinum-seo-pack') ?></a>

<br class="clear" />
<div id="pspimporter-3">
	<table class="form-table">
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Titles and Descriptions: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your Titles and Descriptions from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoastnew_title" id="import_yoastnew_title_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoastnew-titles-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
                </div>
            </td>
		</tr>		
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Robots Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your robots meta data from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoastnew_robots" id="import_yoastnew_robots_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoastnew-robots-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import other SEO and Social Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your other SEO and Social meta data from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoastnew_others" id="import_yoastnew_others_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoastnew-others-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import SEO and Social Meta Data for Terms: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your  SEO and Social meta data for Terms (Categories and other Taxonomies) from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="yoastnew_terms" id="import_yoastnew_terms_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="yoastnew-terms-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>		
		<tr  class="form-field">				
			<th style="width:100%;" scope="row" valign="top"><label><?php esc_html_e('Set up the One time Configuration for Platinum SEO: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('Platinum SEO sets up the default configuration on installation and activation. If you are done with Importing, you may go to SEO Settings and Social Settings to review and make any necessary changes', 'platinum-seo-pack'); ?></p><p><a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('SEO Settings', 'platinum-seo-pack') ?></a> | <a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=psp-social-by-techblissonline" ?>"> <?php esc_html_e('Social Settings', 'platinum-seo-pack') ?></a></p></th>
		</tr>
	</table>
</div>

<br class="clear" />

</div>
<div id="Input" class="psp-container plugin psp-animate-input"  style="display:none">
     
<h2><?php esc_html_e('Import ALL in One SEO Pack Data into Platinum SEO:', 'platinum-seo-pack'); ?></h2>
<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('Settings', 'platinum-seo-pack') ?></a> | <a href="https://techblissonline.com/platinum-wordpress-seo-plugin/#what-is-new" target="_blank" rel="noopener"><?php esc_html_e('Platinum SEO WordPress Plugin', 'platinum-seo-pack') ?></a>

<br class="clear" />
<div id="pspimporter-4">
	<table class="form-table">
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Titles and Descriptions: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your Titles and Descriptions from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="aioseop_title" id="import_aioseop_title_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="aioseop-titles-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
                </div>
            </td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Basic Robots Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your basic robots meta data (noindex, nofollow) from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="aioseop_basicrobots" id="import_aioseop_brobots_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="aioseop-basicrobots-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>		
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import other SEO and Social Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your other SEO and Social meta data from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="aioseop_others" id="import_aioseop_others_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="aioseop-others-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>		
		<tr  class="form-field">				
			<th style="width:100%;" scope="row" valign="top"><label><?php esc_html_e('Set up the One time Configuration for Platinum SEO: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('Platinum SEO sets up the default configuration on installation and activation. If you are done with Importing, you may go to SEO Settings and Social Settings to review and make any necessary changes', 'platinum-seo-pack'); ?></p><p><a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('SEO Settings', 'platinum-seo-pack') ?></a> | <a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=psp-social-by-techblissonline" ?>"> <?php esc_html_e('Social Settings', 'platinum-seo-pack') ?></a></p></th>
		</tr>
	</table>
</div>
<br class="clear" />

</div>
<div id="Zoom" class="psp-container plugin psp-animate-zoom" style="display:none">
     
<h2><?php esc_html_e('Import Platinum SEO Data into Platinum SEO V2.1.0 :', 'platinum-seo-pack'); ?></h2>
<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>"> <?php esc_html_e('Settings', 'platinum-seo-pack') ?></a> | <a href="https://techblissonline.com/platinum-wordpress-seo-plugin/#what-is-new" target="_blank" rel="noopener"><?php esc_html_e('Platinum SEO WordPress Plugin', 'platinum-seo-pack') ?></a>

<br class="clear" />
<div id="pspimporter-5">
	<table class="form-table">	
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import Robots Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your robots meta data from into Platinum SEO PluginV2.1.0', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="psp_robots" id="import_psp_robots_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="psp-robots-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import All SEO and Social Meta Data: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your other SEO and Social meta data from your current SEO Plugin into Platinum SEO Plugin', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="psp_others" id="import_psp_others_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="psp-others-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>
		<tr  class="form-field">				
			<th style="width:70%;" scope="row" valign="top"><label><?php esc_html_e('Import SEO and Social Meta Data for Terms: ', 'platinum-seo-pack'); ?></label>
			<p class="description"><?php esc_html_e('This will import all your  SEO and Social meta data for Terms (Categories and other Taxonomies) into Platinum SEO Plugin V2.1.0', 'platinum-seo-pack'); ?></p></th>
			<td><div class="psp-bs alignright"><input style="display:block;margin:auto" name="psp_terms" id="import_psp_terms_data_btn" class="psp_importer btn btn-success" type="btn" value="Import" /><p class="psp-terms-loader hidden"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-video-loader.gif'; ?>" class="img-responsive" alt="Loading..."/></p>
			    </div>
			</td>
		</tr>		
	</table>
</div>

<br class="clear" />

</div>
</div>

</div>
	</div>
</div>