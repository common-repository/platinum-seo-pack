<?php

/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Description: Complete SEO solution for your Wordpress blog.
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspSocialMetas {
	private static $instance = null;
	
	//public $default_image = "";
	//public $apply_the_content = false;
	
	// Facebook additional
	public $ogtags_active = false;
	public $fbadmins = array();	
	public $fbapp = "";
	public $fb_profile = "";
	public $fb_locale = "";
	public $fb_publisher = "";
	//public $fb_og_type = "";	
	public $fb_site_name = "";
	public $fb_default_image = "";
	
	// Twitter Cards
	public $twitter_cards_active = false;
	//public $card_type = "";
	public $twitter_user = "";
	public $tw_default_image = "";
		
	// Google Schema.org
	public $google_authorship = false;
	public $google_markup = false;
	public $google_publisher = "";
	//public $google_author = "";
	
	//social_metas
	public $psp_social_metas = array();
	
	//SEO metas
	public $psp_seo_title = "";
	public $psp_seo_description = "";
	public $psp_can_link = "";
	public $psp_type = "";
	
	protected $sitename = "";
	protected $sitedescription = "";
	
	// Post defaults
	private $post_title = "";
	private $post_description = "";
	private $post_image = "";
	private $post_url = "";
	private $site_name = "";
	
	private $fb_og_type = "";
	private $fb_title = "";
	private $fb_description = "";
	private $fb_image = array();
	private $fb_ogtype_properties = array();
	private $fb_media_properties = "";
	private $fb_video_url = "";
	private $fb_video_h = "";
	private $fb_video_w = "";
	
	private $tw_card_type = "";
	private $twitter_title = "";
	private $twitter_description = "";
	private $twitter_image = array();
	private $tw_label_1 = "";
	private $tw_data_1 = "";
	private $tw_label_2 = "";
	private $tw_data_2 = "";
	private $tw_imagealt = "";
	private $tw_content_creator = "";
	private $tw_player = "";
	private $tw_player_stream = "";
	private $tw_player_px_width = "";
	private $tw_player_px_height = "";
	private $tw_app_country = "";
	private $tw_app_name_iphone = "";
	private $tw_app_id_iphone = "";
	private $tw_app_url_iphone = "";
	private $tw_app_name_ipad = "";
	private $tw_app_id_ipad = "";
	private $tw_app_url_ipad = "";
	private $tw_app_name_googleplay = "";
	private $tw_app_id_googleplay = "";
	private $tw_app_url_googleplay = "";
	
	private $sc_title = "";
	private $sc_description = "";
	private $sc_image = array();
	
	private $sso_active = false;
	
	private $options;
	
	private $meta = array();
	
	protected $psp_helper;
	
	public static function get_instance() {
	
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	
		return self::$instance;
	
	} // end get_instance;
	
	function __construct() {
		//$essb_options = EasySocialShareButtons_Options::get_instance();
		//$this->options = $essb_options->options;
		
		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;
		
		$this->sitename = $psp_helper_instance->get_sitename();
		$this->sitedescription = $psp_helper_instance->get_sitedescription();
		
		$this->load_social_defaults();
		
		if ($this->sso_active) {
			add_filter ( 'language_attributes', array ($this, 'insert_language_attributes' ) );
			//add_action ( 'wp_head', array ($this, 'outputMeta' ) );
		}
	}
	
	public function psp_get_fb_title() {
		return $this->fb_title;
	}
	
	public function psp_get_fb_description() {
		return $this->fb_description;
	}
	
	public function psp_get_fb_image() {
		return $this->fb_image;
	}
	
	private function load_social_defaults() {
	
		$psp_settings_name = "psp_social_settings";		
		$psp_social_settings = get_option($psp_settings_name);		
		
		
		$opengraph_tags = isset ( $psp_social_settings['psp_og_tags_enabled'] ) ? $psp_social_settings['psp_og_tags_enabled'] : '';
		//$this->default_image = isset ( $this->options ['sso_default_image'] ) ? $this->options ['sso_default_image'] : '';
		$this->fb_default_image = isset ( $psp_social_settings['fb_default_image'] ) ? esc_url($psp_social_settings['fb_default_image']) : '';
		$this->tw_default_image = isset ( $psp_social_settings['tw_default_image'] ) ? esc_url($psp_social_settings['tw_default_image']) : '';
		
		//if ($opengraph_tags == 'true') {
		if ($opengraph_tags) {
			$this->ogtags_active = true;
			
			
			//$this->fbadmins = isset ( $psp_social_settings['fb_admins'] ) ? $psp_social_settings['fb_admins'] : '';
			if (isset($psp_social_settings['fb_admins']) && !empty($psp_social_settings['fb_admins'])) {
					if (strpos($psp_social_settings['fb_admins'], ",")) {
						$this->fbadmins = explode(",", $psp_social_settings['fb_admins']);
					} else {
						//$this->fbadmins[] = esc_url($psp_social_settings['fb_admins']);
						$this->fbadmins[] = $psp_social_settings['fb_admins'];
					}
			}
			$this->fb_profile = isset ( $psp_social_settings['fb_profile'] ) ? $psp_social_settings['fb_profile'] : '';
			$this->fbapp = isset ( $psp_social_settings['fb_app'] ) ? $psp_social_settings['fb_app'] : '';
			$this->fb_locale = isset ( $psp_social_settings['fb_locale'] ) ? $psp_social_settings['fb_locale'] : '';
			$this->fb_og_type = isset ( $psp_social_settings['fb_og_type'] ) ? $psp_social_settings['fb_og_type'] : '';			
			$this->fb_publisher = isset ( $psp_social_settings['fb_publisher'] ) ? $psp_social_settings['fb_publisher'] : '';
			$this->fb_site_name = isset ( $psp_social_settings['fb_site_name'] ) ? $psp_social_settings['fb_site_name'] : '';		
			
		}
		
		$twitter_card_active = isset ( $psp_social_settings['psp_twitter_card_enabled'] ) ? $psp_social_settings['psp_twitter_card_enabled'] : '';
		
		//if ($twitter_card_active == "true") {
		if ($twitter_card_active) {
			$this->twitter_cards_active = true;
		
			$this->tw_card_type = isset ( $psp_social_settings['tw_ct_type'] ) ? $psp_social_settings['tw_ct_type'] : '';
			$this->twitter_user = isset ( $psp_social_settings['tw_user'] ) ? $psp_social_settings['tw_user'] : '';
		}
	
		//$this->google_author = isset ( $psp_social_settings['sc_author'] ) ? $psp_social_settings['sc_author'] : '';
		$this->google_publisher = isset ( $psp_social_settings['sc_publisher'] ) ? $psp_social_settings['sc_publisher'] : '';
		$google_markup = isset ( $psp_social_settings['psp_schemaorg_markup_enabled'] ) ? $psp_social_settings['psp_schemaorg_markup_enabled'] : ''; 
		//if ($google_markup == "true") {
		if ($google_markup) {
			$this->google_markup = true;
		}
		
		if ($this->ogtags_active || $this->twitter_cards_active || $this->google_markup) {
			$this->sso_active = true;
		}
	}
	
	private function load_content_metas() {
		global $post;
		
		$psp_social_metas = $this->psp_social_metas;
		
			if ($this->ogtags_active) {
				
				if (isset($psp_social_metas['fb_og_type']) && !empty($psp_social_metas['fb_og_type'])) {
					$this->fb_og_type = $psp_social_metas['fb_og_type'];
				}
				
				if (isset($psp_social_metas['fb_title']) && !empty($psp_social_metas['fb_title'])) {
					$this->fb_title = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters_full_length($this->psp_helper->internationalize($psp_social_metas['fb_title']))));
				}
				
				if (isset($psp_social_metas['fb_description']) && !empty($psp_social_metas['fb_description'])) {				
					$this->fb_description = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters_full_length($this->psp_helper->internationalize($psp_social_metas['fb_description']))));
				}
				
				if (isset($psp_social_metas['fb_image']) && !empty($psp_social_metas['fb_image'])) {
					if (strpos($psp_social_metas['fb_image'], ",")) {
						$this->fb_image = explode(",", $psp_social_metas['fb_image']);
					} else {
						$this->fb_image[0] = esc_url($psp_social_metas['fb_image']);
					}
				}
				
				if (isset($psp_social_metas['fb_ogtype_properties']) && !empty($psp_social_metas['fb_ogtype_properties'])) {				
					
					
					//$this->fb_ogtype_properties = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($psp_social_metas['fb_ogtype_properties']))));
					//parse_str($psp_social_metas['fb_ogtype_properties'], $this->fb_ogtype_properties);
					//$this->fb_ogtype_properties = $psp_social_metas['fb_ogtype_properties'];
					//$this->fb_ogtype_properties = isset($psp_social_metas['fb_ogtype_properties'][0]) ? unserialize($psp_social_metas['fb_ogtype_properties'][0]) : array();
					$psp_post_fb_ogtype_properties = isset($psp_social_metas['fb_ogtype_properties']) ? unserialize($psp_social_metas['fb_ogtype_properties']) : array();
					$psp_post_fb_ogtype_properties = array_map( 'esc_attr', $psp_post_fb_ogtype_properties );
					$this->fb_ogtype_properties = array_map( 'html_entity_decode', $psp_post_fb_ogtype_properties );
				}
				
				if (isset($psp_social_metas['fb_media_properties']) && !empty($psp_social_metas['fb_media_properties'])) {				
					
					//$this->fb_media_properties = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($psp_social_metas['fb_media_properties']))));
					//parse_str($psp_social_metas['fb_media_properties'], $this->fb_media_properties);
					//$this->fb_media_properties = $psp_social_metas['fb_media_properties'];
					
					$psp_post_fb_media_properties = isset($psp_social_metas['fb_media_properties']) ? unserialize($psp_social_metas['fb_media_properties']) : array();
					$psp_post_fb_media_properties = array_map( 'esc_attr', $psp_post_fb_media_properties );
					$this->fb_media_properties = array_map( 'html_entity_decode', $psp_post_fb_media_properties );
				}
							
				if ($this->fb_description == "") {
					$this->fb_description = $this->psp_seo_description;
				}				
				if ($this->fb_title == "") {
					$this->fb_title = $this->psp_seo_title;
				}
				if (empty($this->fb_image) && !empty($this->post_image)) {
					$this->fb_image[0] = esc_url($this->post_image);
				}
				if (empty($this->fb_image)) {
					$this->fb_image[0] = $this->fb_default_image;
				}
			}
			
			if ($this->twitter_cards_active) {
			
			
				if (isset($psp_social_metas['tw_card_type']) && !empty($psp_social_metas['tw_card_type'])) {
					$this->tw_card_type = $psp_social_metas['tw_card_type'];
				}
				
				if (isset($psp_social_metas['tw_title']) && !empty($psp_social_metas['tw_title'])) {
					$this->twitter_title = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters_full_length($this->psp_helper->internationalize($psp_social_metas['tw_title']))));
				}
				
				if (isset($psp_social_metas['tw_description']) && !empty($psp_social_metas['tw_description'])) {				
					$this->twitter_description = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters_full_length($this->psp_helper->internationalize($psp_social_metas['tw_description']))));
				}
				
				if (isset($psp_social_metas['tw_image']) && !empty($psp_social_metas['tw_image'])) {
				
					$this->twitter_image[0] = esc_url($psp_social_metas['tw_image']);
					
				}
				
				if ( $this->tw_card_type == "gallery" ) {
				
					if (isset($psp_social_metas['tw_image_1']) && !empty($psp_social_metas['tw_image_1'])) {
					
						$this->twitter_image[1] = esc_url($psp_social_metas['tw_image_1']);
						
					}
					
					if (isset($psp_social_metas['tw_image_2']) && !empty($psp_social_metas['tw_image_2'])) {
					
						$this->twitter_image[2] = esc_url($psp_social_metas['tw_image_2']);
						
					}
					
					if (isset($psp_social_metas['tw_image_3']) && !empty($psp_social_metas['tw_image_3'])) {
					
						$this->twitter_image[3] = esc_url($psp_social_metas['tw_image_3']);
						
					}
				
				}
				
				if ( $this->tw_card_type == "product" ) {
					$this->tw_label_1 = $psp_social_metas['tw_label_1'];
					$this->tw_data_1 = $psp_social_metas['tw_data_1'];
					$this->tw_label_2 = $psp_social_metas['tw_label_2'];
					$this->tw_data_2 = $psp_social_metas['tw_data_2'];
				}
				
				if (isset($psp_social_metas['tw_creator']) && !empty($psp_social_metas['tw_creator'])) {
					$this->tw_content_creator = trim(stripcslashes($psp_social_metas['tw_creator']));
				}
				
				if (isset($psp_social_metas['tw_imagealt']) && !empty($psp_social_metas['tw_imagealt'])) {				
					$this->tw_imagealt = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters_full_length($this->psp_helper->internationalize($psp_social_metas['tw_imagealt']))));
				}
				
				if ( $this->tw_card_type == "player" ) {
				
					if (isset($psp_social_metas['tw_player']) && !empty($psp_social_metas['tw_player'])) {
					
						$this->tw_player = esc_url($psp_social_metas['tw_player']);
						
					}
					
					if (isset($psp_social_metas['tw_player_stream']) && !empty($psp_social_metas['tw_player_stream'])) {
					
						$this->tw_player_stream = esc_url($psp_social_metas['tw_player_stream']);
						
					}
					
					$this->tw_player_width = $psp_social_metas['tw_player_width'];
					$this->tw_player_height = $psp_social_metas['tw_player_height'];
				
				}
				
				if ( $this->tw_card_type == "app" ) {
				
					if (isset($psp_social_metas['tw_app_country']) && !empty($psp_social_metas['tw_app_country'])) {
					
						$this->tw_app_country = trim(stripcslashes($psp_social_metas['tw_app_country']));
						
					}
					
					if (isset($psp_social_metas['tw_app_name_iphone']) && !empty($psp_social_metas['tw_app_name_iphone'])) {
					
						$this->tw_app_name_iphone = trim(stripcslashes($psp_social_metas['tw_app_name_iphone']));
						
					}
					
					if (isset($psp_social_metas['tw_app_id_iphone']) && !empty($psp_social_metas['tw_app_id_iphone'])) {
					
						$this->tw_app_id_iphone = trim(stripcslashes($psp_social_metas['tw_app_id_iphone']));
						
					}
					
					
					if (isset($psp_social_metas['tw_app_url_iphone']) && !empty($psp_social_metas['tw_app_url_iphone'])) {
					
						$this->tw_app_url_iphone = esc_attr($psp_social_metas['tw_app_url_iphone']);
						
					}
					
					if (isset($psp_social_metas['tw_app_name_ipad']) && !empty($psp_social_metas['tw_app_name_ipad'])) {
					
						$this->tw_app_name_ipad = trim(stripcslashes($psp_social_metas['tw_app_name_ipad']));
						
					}
					
					if (isset($psp_social_metas['tw_app_id_ipad']) && !empty($psp_social_metas['tw_app_id_ipad'])) {
					
						$this->tw_app_id_ipad = trim(stripcslashes($psp_social_metas['tw_app_id_ipad']));
						
					}
					
					
					if (isset($psp_social_metas['tw_app_url_ipad']) && !empty($psp_social_metas['tw_app_url_ipad'])) {
					
						$this->tw_app_url_ipad = esc_attr($psp_social_metas['tw_app_url_ipad']);
						
					}
					
					if (isset($psp_social_metas['tw_app_name_googleplay']) && !empty($psp_social_metas['tw_app_name_googleplay'])) {
					
						$this->tw_app_name_googleplay = trim(stripcslashes($psp_social_metas['tw_app_name_googleplay']));
						
					}
					
					if (isset($psp_social_metas['tw_app_id_googleplay']) && !empty($psp_social_metas['tw_app_id_googleplay'])) {
					
						$this->tw_app_id_googleplay = trim(stripcslashes($psp_social_metas['tw_app_id_googleplay']));
						
					}
					
					
					if (isset($psp_social_metas['tw_app_url_googleplay']) && !empty($psp_social_metas['tw_app_url_googleplay'])) {
					
						$this->tw_app_url_googleplay = esc_url($psp_social_metas['tw_app_url_googleplay']);
						
					}				
				
				}
				
				if ($this->twitter_title == "") {
					$this->twitter_title = $this->fb_title;
				}
				
				if ($this->twitter_description == "") {
					$this->twitter_description = $this->fb_description;
				}
				
				if (empty($this->twitter_image)) {
					$this->twitter_image = $this->fb_image;
				}
				
				if ($this->twitter_title == "") {
					$this->twitter_title = $this->psp_seo_title;
				}							
				
				if ($this->twitter_description == "") {
					$this->twitter_description = $this->psp_seo_description;
				}
				
				if (empty($this->twitter_image) && !empty($this->post_image)) {
					$this->twitter_image[0] = esc_url($this->post_image);
				}

				if (empty($this->twitter_image)) {
					$this->twitter_image[0] = $this->tw_default_image;
				}
			}
			
			if ($this->google_markup) {
				
				if (isset($psp_social_metas['sc_title']) && !empty($psp_social_metas['sc_title'])) {
					$this->sc_title = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters_full_length($this->psp_helper->internationalize($psp_social_metas['sc_title']))));
				}
				
				if (isset($psp_social_metas['sc_description']) && !empty($psp_social_metas['sc_description'])) {				
					$this->sc_description = trim(stripcslashes($this->psp_helper->trim_excerpt_without_filters_full_length($this->psp_helper->internationalize($psp_social_metas['sc_description']))));
				}
				
				if (isset($psp_social_metas['sc_image']) && !empty($psp_social_metas['sc_image'])) {
					if (strpos($psp_social_metas['sc_image'], ",")) {
						$this->sc_image = explode(",", $psp_social_metas['sc_image']);
					} else {
						$this->sc_image[0] = esc_url($psp_social_metas['sc_image']);
					}
				}
				
				if ($this->sc_title == "") {
					$this->sc_title = $this->fb_title;
				}
				if ($this->sc_description == "") {
					$this->sc_description = $this->fb_description;
				}				
				
				if (empty($this->sc_image)) {
					$this->sc_image = $this->fb_image;
				}
				
				if ($this->sc_title == "") {
					$this->sc_title = $this->psp_seo_title;
				}
				
				if ($this->sc_description == "") {
					$this->sc_description = $this->psp_seo_description;
				}
				
				if (empty($this->sc_image) && !empty($this->post_image)) {
					$this->sc_image[0] = esc_url($this->post_image);
				}
				
				if (empty($this->sc_image)) {
					$this->sc_image[0] = $this->fb_default_image; //use fb default image
				}
			}
			
		
		
		//}
		
	}
	
	public function insert_language_attributes($content) {
		if ($this->ogtags_active) {
			if (!empty($this->fbadmins) || $this->fbapp != '') {
			//if ($this->fbadmins != '' || $this->fbapp != '') {
				$content .= ' prefix="og: http://ogp.me/ns#  fb: http://ogp.me/ns/fb#"';
			} else {
				$content .= ' prefix="og: http://ogp.me/ns#"';
			}
		}
		
		if ($this->google_markup && is_singular () && $this->fb_og_type == "article" ) {
			$content .= ' itemscope itemtype="http://schema.org/Article"';
		}

		return $content;
	}
	
	public function psp_set_post_image($psp_post = null) {

		$post_image = "";
		$post_id = "";
		
		if ($psp_post) $post_id = $psp_post->ID;

		if (!$post_image && $post_id) {
			/* Check for a post image ID (set by WP as a custom field). */
			$post_thumbnail_id = get_post_thumbnail_id( $post_id );

			if ( !$post_thumbnail_id ) {
				$post_image = "";
			} else {
				$post_image = wp_get_attachment_image_src ( $post_thumbnail_id, 'full' );
				if ($post_image) {
					$this->post_image = $post_image[0];
				}
			}
		}

		if (!$post_image && $post_id) {

			/* Get attachments for the inputted $post_id. */
			$image_attachments = get_children(
				array(
					'post_parent' => $post_id,
					'post_status' => 'inherit',
					'post_type' => 'attachment',
					'post_mime_type' => 'image',
					'order' => 'ASC',
					'orderby' => 'menu_order ID',
					'suppress_filters' => true
				)
			);
			
			if ( !empty( $image_attachments ) ) {
				foreach ( $image_attachments as $id => $attachment ) {
					$attachment_id = $id;
					break;
				}
				
				if (!empty( $attachment_id )) {
					$post_image = wp_get_attachment_image_src ( $attachment_id, 'full' );
					if ($post_image) {
						$this->post_image = $post_image[0];
					}
				}
			}
		}

		if (!$post_image && $post_id) {
			/* Search the post's content for the <img /> tag and get its URL. */
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', get_post_field( 'post_content', $post_id ), $post_images );

			/* If there is a match for the image, return its URL. */
			if ( isset( $post_images ) && !empty( $post_images[1][0] ) )
				$post_image = $post_images[1][0];
			
			if ($post_image) {
				$this->post_image = $post_image;
			}
		}

	}
	
	public function psp_get_social_metas() {
		global $post;
	
		$this->load_content_metas();
		
		if ($this->google_markup) {
			$this->buildGoogleMeta();
		}
		
		if ($this->ogtags_active) {
			$this->buildFacebookMeta();
		}
		if ($this->twitter_cards_active) {
			$this->buildTwitterMeta();
		}	
		
		
		$output_meta = "";
		
		//echo "\r\n";
		foreach ($this->meta as $single) {
			$output_meta .= $single."\r\n";
		}
		return $output_meta;
	
	}
	
	// Google
	
	private function buildGoogleMeta() {
	
		if ($this->fb_og_type == "article") {
			$this->googleAuthorshipBuilder('publisher', $this->google_publisher);
		}
		
		if ($this->google_markup) {
			$this->googleMetaBuilder('name', $this->sc_title, true);
			$this->googleMetaBuilder('description', $this->sc_description, true);
			$this->googleMetaBuilder('image', $this->sc_image[0]);
		}
	}
	
	private function googleMetaBuilder($property = '', $value = '', $apply_filters = false) {
		if ($apply_filters) {
			//$value = str_replace ( '\'', "&#8217;", $value );
			//$value = str_replace ( '"', "&qout;", $value );
				
			//$value = addslashes(strip_tags($value));
		}
		
		if ($property != '' && $value != '') {
			$this->meta[] = '<meta itemprop="'.esc_attr($property).'" content="'.esc_attr($value).'" />';
		}
	}
	
	private function googleAuthorshipBuilder($property = '', $value = '') {
		if ($property != '' && $value != '') {
			$this->meta[] = '<link rel="'.$property.'" href="'.$value.'"/>';
		}
	}
	
	// Twitter
	
	private function buildTwitterMeta() {
		if ($this->tw_card_type == "") {
			$this->tw_card_type = "summary";
		}		
		
		$this->twitterMetaTagBuilder('card', $this->tw_card_type);
		if ($this->twitter_user != '') { 
			$this->twitterMetaTagBuilder('site', '@'.$this->twitter_user);
		}
		$this->twitterMetaTagBuilder('title', $this->twitter_title, true);
		$this->twitterMetaTagBuilder('description', $this->twitter_description, true);
		
		$this->twitterMetaTagBuilder('image:alt', $this->tw_imagealt, false);
		
		if ($this->tw_content_creator != '') { 
			$this->twitterMetaTagBuilder('site', '@'.$this->tw_content_creator);
		}
		
		if ($this->tw_card_type == "player") {
			if (isset($this->tw_player) && !empty($this->tw_player)) {
				$this->twitterMetaTagBuilder('player', $this->tw_player);
			}
			if (isset($this->tw_player_stream) && !empty($this->tw_player_stream)) {
				$this->twitterMetaTagBuilder('player:stream', $this->tw_player_stream);
			}
			if (isset($this->tw_player_width) && !empty($this->tw_player_width)) {
				$this->twitterMetaTagBuilder('player:width', $this->tw_player_width);
			}
			if (isset($this->tw_player_height) && !empty($this->tw_player_height)) {
				$this->twitterMetaTagBuilder('player:height', $this->tw_player_height);
			}
		}
		
		if ($this->tw_card_type == "app") {
			if (isset($this->tw_app_country) && !empty($this->tw_app_country)) {
				$this->twitterMetaTagBuilder('app:country', $this->tw_app_country);
			}
			if (isset($this->tw_app_name_iphone) && !empty($this->tw_app_name_iphone)) {
				$this->twitterMetaTagBuilder('app:name:iphone', $this->tw_app_name_iphone);
			}
			if (isset($this->tw_app_id_iphone) && !empty($this->tw_app_id_iphone)) {
				$this->twitterMetaTagBuilder('app:id:iphone', $this->tw_app_id_iphone);
			}
			if (isset($this->tw_app_url_iphone) && !empty($this->tw_app_url_iphone)) {
				$this->twitterMetaTagBuilder('app:url:iphone', $this->tw_app_url_iphone);
			}
			if (isset($this->tw_app_name_ipad) && !empty($this->tw_app_name_ipad)) {
				$this->twitterMetaTagBuilder('app:name:ipad', $this->tw_app_name_ipad);
			}
			if (isset($this->tw_app_id_ipad) && !empty($this->tw_app_id_ipad)) {
				$this->twitterMetaTagBuilder('app:id:ipad', $this->tw_app_id_ipad);
			}
			if (isset($this->tw_app_url_ipad) && !empty($this->tw_app_url_ipad)) {
				$this->twitterMetaTagBuilder('app:url:ipad', $this->tw_app_url_ipad);
			}
			if (isset($this->tw_app_name_googleplay) && !empty($this->tw_app_name_googleplay)) {
				$this->twitterMetaTagBuilder('app:name:googleplay', $this->tw_app_name_googleplay);
			}
			if (isset($this->tw_app_id_googleplay) && !empty($this->tw_app_id_googleplay)) {
				$this->twitterMetaTagBuilder('app:id:googleplay', $this->tw_app_id_googleplay);
			}
			if (isset($this->tw_app_url_googleplay) && !empty($this->tw_app_url_googleplay)) {
				$this->twitterMetaTagBuilder('app:url:googleplay', $this->tw_app_url_googleplay);
			}
		}
		
		if ($this->tw_card_type == "gallery") {
			if (isset($this->twitter_image[0]) && !empty($this->twitter_image[0])) {
				$this->twitterMetaTagBuilder('image0', $this->twitter_image[0]);
			}
			if (isset($this->twitter_image[1]) && !empty($this->twitter_image[1])) {
				$this->twitterMetaTagBuilder('image1', $this->twitter_image[1]);
			}
			if (isset($this->twitter_image[2]) && !empty($this->twitter_image[2])) {
				$this->twitterMetaTagBuilder('image2', $this->twitter_image[2]);
			}
			if (isset($this->twitter_image[3]) && !empty($this->twitter_image[3])) {
				$this->twitterMetaTagBuilder('image3', $this->twitter_image[3]);
			}
		} else {
			if (isset($this->twitter_image[0]) && !empty($this->twitter_image[0])) {
				//$this->twitterMetaTagBuilder('image:src', $this->twitter_image[0]);
				$this->twitterMetaTagBuilder('image', $this->twitter_image[0]);
			}
		}
		
		
		
		if ($this->tw_card_type == "product") {
			if ($this->tw_label_1 != "" && $this->tw_data_1 != "") {
				$this->twitterMetaTagBuilder('label1', $this->tw_label_1);
				$this->twitterMetaTagBuilder('data1', $this->tw_data_1);
			}
			if ($this->tw_label_2 != "" && $this->tw_data_2 != "") {
				$this->twitterMetaTagBuilder('label2', $this->tw_label_2);
				$this->twitterMetaTagBuilder('data2', $this->tw_data_2);
			}
		}
		
		$this->twitterMetaTagBuilder('url', $this->psp_can_link);		
		
		$this->twitterMetaTagBuilder('domain', $this->sitename, true);
	}
	
	private function twitterMetaTagBuilder($property = '', $value = '', $apply_filters = false, $prefix = 'twitter') {
		if ($apply_filters) {
			//$value = str_replace ( '\'', "&#8217;", $value );
			//$value = str_replace ( '"', "&qout;", $value );
			
			//$value = addslashes(strip_tags($value));
		}
		
		if ($property != '' && $value != '') {
			$this->meta[] = '<meta property="'.esc_attr($prefix).':'.esc_attr($property).'" content="'.esc_attr($value).'" />';
		}
	}
	
	// Facebook
	
	private function buildFacebookMeta() {
	
		$media_property_types = array("video", "video:url","video:secure_url", "video:type", "video:width", "video:height", "audio", "audio:url", "audio:secure_url", "audio:type");
		
		$media_property_url_types = array("video", "video:url", "video:secure_url", "audio", "audio:url", "audio:secure_url");
		$fburl = $this->psp_can_link . '?utm_medium=tbpsp&utm_source=facebook';
		$this->openGraphMetaTagBuilder('url', $fburl);
		
		$fb_site_name = $this->fb_site_name;
		if (empty($fb_site_name)) $fb_site_name = $this->sitename;
		$this->openGraphMetaTagBuilder('site_name', $fb_site_name, true);
		$fbapp = (isset($this->fbapp) && !empty($this->fbapp)) ? $this->fbapp : "";
		$this->openGraphMetaTagBuilder('app_id', $fbapp, false, 'fb');
		//$this->openGraphMetaTagBuilder('admins', $this->fbadmins[0], false, 'fb');
		$fbadmins = (isset($this->fbadmins[0]) && !empty($this->fbadmins[0])) ? $this->fbadmins[0] : "";
		$this->openGraphMetaTagBuilder('admins', $fbadmins, false, 'fb');	
		$this->openGraphMetaTagBuilder('profile_id', $this->fb_profile, false, 'fb');
		
		if (empty($this->fb_og_type)) $this->fb_og_type = "article";
		if (is_front_page()) $this->fb_og_type = "Website";
		if (is_post_type_archive()) $this->fb_og_type = "Website";
		$content_type = $this->fb_og_type;
		
		//$content_type = (is_single () || is_page ()) ? "article" : "website";
		$this->openGraphMetaTagBuilder('type', $content_type);
		
		//$this->openGraphMetaTagBuilder('publisher', $this->fb_publisher, false, 'fb');
		
		$this->openGraphMetaTagBuilder('title', $this->fb_title, true);
		$this->openGraphMetaTagBuilder('description', $this->fb_description, true);
		$this->openGraphMetaTagBuilder('locale', $this->fb_locale);		
		//$this->openGraphMetaTagBuilder('image', esc_url($this->fb_image[0]));
		foreach ($this->fb_image as $fb_each_image) {
			$this->openGraphMetaTagBuilder('image', esc_url($fb_each_image));
		}
		
		if (!empty($this->fb_ogtype_properties)) {			
			
			//$ogtype_properties = explode('&', $this->fb_ogtype_properties);
			$ogtype_properties = $this->fb_ogtype_properties;
			foreach ($ogtype_properties as $ogtype_property_name => $ogtype_property_value) {
		
				//list($ogtype_property_name, $ogtype_property_value) = explode('=', $ogtype_property, 2);
				$ogtype_property_name = trim($ogtype_property_name);
				$ogtype_property_value = trim($ogtype_property_value);
				
				if (strpos($ogtype_property_value, ",")) {
					$ogtype_property_arr_value = explode(",", $ogtype_property_value);
					foreach ($ogtype_property_arr_value as $ogtype_property_arr_value_each) {
					
						$this->openGraphMetaTagBuilder($ogtype_property_name, html_entity_decode($ogtype_property_arr_value_each), false, "none");
					}
				} else { 
				
					$this->openGraphMetaTagBuilder($ogtype_property_name, html_entity_decode($ogtype_property_value), false, "none");
				
				}
				
				
		
			}
		
		}
		
		if (!empty($this->fb_media_properties)) {			
			
			//$ogtype_properties = explode('&', $this->fb_ogtype_properties);
			$media_properties = $this->fb_media_properties;
		
			foreach ($media_properties as $media_property_name => $media_property_value) {
		
				//list($ogtype_property_name, $ogtype_property_value) = explode('=', $ogtype_property, 2);
				$media_property_name = esc_attr(trim($media_property_name));
				$media_property_value = esc_attr(trim($media_property_value));
				
				if ( in_array($media_property_name, $media_property_url_types) ) {
				    $media_property_value = esc_url(trim($media_property_value));
				}
				
				if (strpos($media_property_value, ",")) {
					$media_property_arr_value = explode(",", $media_property_value);
					foreach ($media_property_arr_value as $media_property_arr_value_each) {
					
						$this->openGraphMetaTagBuilder($media_property_name, html_entity_decode($media_property_arr_value_each), false, "none");
					}
				} else { 
				
					$this->openGraphMetaTagBuilder($media_property_name, html_entity_decode($media_property_value), false, "none");
				
				}
				
				
		
			}
		
		}
		/**
		if (!empty($this->fb_media_properties)) {
		
			$media_properties = explode('&', $this->fb_media_properties);
		
			foreach ($media_properties as $media_property) {
		
				list($media_property_name, $media_property_value) = explode('=', $media_property, 2);
				$media_property_name = trim($media_property_name);
				$media_property_value = trim($media_property_value);
				
				if ( in_array($media_property_name, $media_property_types) ) {
				
					//if ($media_property_name == "video" || $media_property_name == "video:url" || $media_property_name == "video:secure_url" || $media_property_name == "audio" || $media_property_name == "audio:url" || $media_property_name == "audio:secure_url") {
					if ( in_array($media_property_name, $media_property_url_types) ) {
					
						$media_property_value = esc_attr($media_property_value);
					}
					
					if ($media_property_name == "video:width" || $media_property_name == "video:height") {
						$media_property_value = esc_attr($media_property_value);
					}
					
					$this->openGraphMetaTagBuilder($media_property_name, $media_property_value);
				
				}
		
			}			
		
		}
		**/
		
		// @since 2.0 output video meta tags
		/**************
		if ($this->fb_video_url != '') {
			$this->openGraphMetaTagBuilder('video', esc_url($this->fb_video_url), false);
			$this->openGraphMetaTagBuilder('video:height', esc_attr($this->fb_video_h), false);
			$this->openGraphMetaTagBuilder('video:width', esc_attr($this->fb_video_w), false);
			$this->openGraphMetaTagBuilder('video:type', 'application/x-shockwave-flash', false);
		}
		**************/
		// only for posts
		if (is_singular () && !is_front_page() && $this->fb_og_type == "article" ) {
			//$this->og_tags();
			//$this->og_category();
			$psp_settings_name = "psp_social_settings";		
			$psp_social_settings = get_option($psp_settings_name);
			$psp_og_times_disabled = !empty($psp_social_settings['psp_og_times_disabled']) ? $psp_social_settings['psp_og_times_disabled'] : '';
			if (!$psp_og_times_disabled ) {
				$this->og_publish_date();
			}
			//if ($this->fbpage != '') {
			//	$this->openGraphMetaTagBuilder('publisher', $this->fbpage, false, 'article');
			//}		
		}
	}
	
	private function openGraphMetaTagBuilder($property = '', $value = '', $apply_filters = false, $prefix = 'og') {
		if ($apply_filters) {
			
			//$value = str_replace ( '\'', "&#8217;", $value );
			//$value = str_replace ( '"', "&qout;", $value );
			//$value = addslashes(strip_tags($value));
		}
		
		if ($property != '' && $value != '') {
			if ($prefix == "none") {
				$this->meta[] = '<meta property="'.esc_attr($property).'" content="'.esc_attr($value).'" />';
			} else {
				$this->meta[] = '<meta property="'.esc_attr($prefix).':'.$property.'" content="'.esc_attr($value).'" />';
			}
		}
		
	}
	
	function og_tags() {
		if (! is_singular ()) {
			return;
		}
	
		$tags = get_the_tags ();
		if (! is_wp_error ( $tags ) && (is_array ( $tags ) && $tags !== array ())) {
			foreach ( $tags as $tag ) {
				$this->openGraphMetaTagBuilder('tag', $tag->name, false, 'article');
			}
		}
	}
	
	public function og_category() {
		if ( ! is_singular() ) {
			return;
		}
	
		$terms = get_the_category();
		if ( ! is_wp_error( $terms ) && ( is_array( $terms ) && $terms !== array() ) ) {
			foreach ( $terms as $term ) {
				$this->openGraphMetaTagBuilder('section', $term->name, false, 'article');
			}
		}
	}
	
	public function og_publish_date() {
		if ( ! is_singular() ) {
			return;
		}
	
		$pub = get_the_date( 'c' );
		$this->openGraphMetaTagBuilder('published_time', $pub, false, 'article');
	
		$mod = get_the_modified_date( 'c' );
		if ( $mod != $pub ) {
			$this->openGraphMetaTagBuilder('modified_time', $mod, false, 'article');
			$this->openGraphMetaTagBuilder('updated_time', $mod);
		}
	}
}

?>