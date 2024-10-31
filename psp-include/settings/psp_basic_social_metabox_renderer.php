<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
?>
<tr  class="form-field">			
		<th scope="row" valign="top"><h3><strong><?php esc_html_e('Meta Tags for Social Media - Facebook, Twitter, Pinterest and LinkedIn: ', 'platinum-seo-pack'); ?></strong></h3></th></tr>
<p class="description"><?php echo esc_html__('If the following title and description fields are not filled in, corresponding Techblissonline Platinum SEO meta data will be used to render social meta tags for social sites. For more advanced control on social meta tags i.e open graph, twitter cards and schema.org tags for individual social sites, use the advanced social tab.', 'platinum-seo-pack').'<a href="https://techblissonline.com/facebook-optimization/" target="_blank">'.esc_html__('Facebook Optimization Guide', 'platinum-seo-pack').'</a>'.'  |  '.' <a href="https://techblissonline.com/twitter-card-optimization/" target="_blank">'.esc_html__('Twitter Card Optimization Guide', 'platinum-seo-pack').'</a>'; ?></p>
<table class="form-table">	
    	<?php 	if ($psp_premium_valid && $psp_premium_status) { 
			//do nothing;
			} else { ?>
	<tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="facebook_og_type_id"><?php esc_html_e('Open Graph Object Type: ', 'platinum-seo-premium'); ?></label></th>
		<td><select id="facebook_og_type_id" name="psp_social_meta[fb_og_type]"><?php $dditems = array ('' => 'Select an OG type', 'website' => 'website', 'article' => 'article', 'books.author' => 'books.author', 'books.book' => 'books.book', 'books.genre' => 'books.genre', 'business.business' => 'business.business', 'fitness.course' => 'fitness.course', 'game.achievement' => 'game.achievement', 'music.album' => 'music.album', 'music.playlist' => 'music.playlist', 'music.radio_station' => 'music.radio_station', 'music.song' => 'music.song', 'place' => 'place', 'product' => 'product', 'product.group' => 'product.group', 'product.item' => 'product.item', 'profile' => 'profile', 'restaurant.menu' => 'restaurant.menu', 'restaurant.menu_item' => 'restaurant.menu_item', 'restaurant.menu_section' => 'restaurant.menu_section', 'restaurant.restaurant' => 'restaurant.restaurant', 'video.episode' => 'video.episode', 'video.movie' => 'video.movie', 'video.other' => 'video.other', 'video.tv_show' => 'video.tv_show');
		foreach($dditems as $key => $val) {
			$selected = (isset($psp_social_meta['fb_og_type']) && $psp_social_meta['fb_og_type']==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_attr($val)."</option>";
		} ?></select><p class="description"><?php echo esc_html__(' Select the facebook Open Graph object type to be used for this page. If nothing is selected here, sitewide default setting will be used provided Open Graph tags for facebook had been enabled in Social settings and the sitewide default setting is not empty.', 'platinum-seo-premium').' - <a href="https://indiafascinates.com/wp-admin/admin.php?page=psp-social-by-techblissonline">Social</a>';; ?></p>		
		</td>
	</tr>
	<tr class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="twitter_card_type_id"><?php esc_html_e('Twitter Card Type: ', 'platinum-seo-premium'); ?></label></th>
		<td><select id="twitter_card_type_id" name="psp_social_meta[tw_card_type]"><?php $dditems = array ('' => 'Select a card type', 'summary' => 'summary', 'summary_large_image' => 'summary with large image');
		foreach($dditems as $key => $val) {
			$selected = (isset($psp_social_meta['tw_card_type']) && $psp_social_meta['tw_card_type']==$key) ? 'selected="selected"' : '';
			//echo "<option value='$key' $selected>$val</option>";
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_attr($val)."</option>";
		} ?></select><p class="description"><?php echo esc_html__(' Select the twitter card type to be used for this page. If nothing is selected here, sitewide default setting will be used only when twitter card had been enabled in Social settings.', 'platinum-seo-premium').' - <a href="https://indiafascinates.com/wp-admin/admin.php?page=psp-social-by-techblissonline">Social</a>'; ?></p>		
		</td>
	</tr>	
			<?php } ?>
	<tr  class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="fb_title"><?php esc_html_e('Title for Social media : ', 'platinum-seo-pack'); ?></label></th>
		<td><input type="text" name="psp_social_meta[fb_title]" id="fb_title" value="<?php echo (isset($psp_social_meta['fb_title']) ? html_entity_decode(stripcslashes(esc_attr($psp_social_meta['fb_title']))) : ''); ?>"><br />
		<p class="description"><?php esc_html_e('Set title to be used when this page is shared on faceook and other social media sites.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr  class="form-field">
		<th style="width:20%;" scope="row" valign="top"><label for="fb_description"><?php esc_html_e('Description for Social media : ', 'platinum-seo-pack'); ?></label></th>
		<td><textarea name="psp_social_meta[fb_description]" id="fb_description"><?php echo (isset($psp_social_meta['fb_description']) ? html_entity_decode(stripcslashes(esc_textarea($psp_social_meta['fb_description']))) : ''); ?></textarea><br />
		<p class="description"><?php esc_html_e('Set description to be used when this page is shared on faceook and other social media sites.', 'platinum-seo-pack'); ?></p>
		</td>
	</tr>
	<tr  class="form-field">				
		<th style="width:20%;" scope="row" valign="top"><label for="fb_image"><?php esc_html_e('Images for Social media : ', 'platinum-seo-pack'); ?></label></th>
		<td><input style="width:75%;" type="text" name="psp_social_meta[fb_image]" id="fb_image" value="<?php echo (isset($psp_social_meta['fb_image']) ? esc_url($psp_social_meta['fb_image']) : ''); ?>"><input style="width:20%;" id="fb_image_btn" class="upload_image_button" type="button" value="Upload" /><br />
		<p class="description"><?php echo esc_html__('Enter an URL to an image or upload an image to be used when this page is shared on faceook and other social media sites.', 'platinum-seo-pack').'<a href="https://techblissonline.com/facebook-optimization/#image" target="_blank">'.esc_html__('Image Guidelines','platinum-seo-pack').'</a>'; ?></p>
		</td>
	</tr>
</table>