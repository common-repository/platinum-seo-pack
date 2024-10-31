<?php

/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: Social management class
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspSocialSettings extends PspSettings {	
	 
	private static $obj_handle = null;			
	
	private $plugin_settings_tabs = array();
	 
	protected $psp_social_share_settings_group = 'psp_social_share';
    private $psp_social_settings_group = 'psp_social';	
	
	protected $psp_plugin_options_key = 'psp-social-by-techblissonline';
	protected $psp_settings_tabs = array();
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;	
	
	function __construct() {		
		
		$this->psp_settings_tabs[$this->psp_social_settings_group] = 'Social Settings';
		$this->psp_settings_tabs[$this->psp_social_share_settings_group] = 'Share Button Settings';		
		
		add_action( 'admin_init', array( &$this, 'psp_social_settings_init' ) );		
	}
	
	function psp_social_settings_init() {		
		
		$tab = isset( $_GET['pspsocial'] ) ? Sanitize_key($_GET['pspsocial']) : $this->psp_social_settings_group;
		
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));		
	
		$this->register_social_settings();		
		//$this->register_social_share_settings();
	}
	
	/*
	 * Registers the social settings for the various social sites and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_social_settings() {
		$this->psp_settings_tabs[$this->psp_social_settings_group] = 'Social Settings';		
		$psp_settings_name = "psp_social_settings";		
		
		$psp_settings = get_option($psp_settings_name);
		//$this->psp_settings_name = $psp_settings;
		
		global $pagenow;
		//enqueue javascript
		if (( $pagenow == 'admin.php' && (sanitize_key($_GET['page']) == 'psp-social-by-techblissonline'))) {
            wp_enqueue_media();	
            
		}	
		//wp_enqueue_script( 'psp-image-uploader', plugins_url( '/js/pspmediauploader.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'psp-image-uploader', plugins_url( '/js/pspmediauploader.js', __FILE__ ), array( 'jquery' ), '2.1.7' );
		wp_enqueue_script( 'psp-social', plugins_url( '/js/pspsocialhandler.js', __FILE__ ), array( 'jquery') , '2.3.5' );
		
		register_setting( $this->psp_social_settings_group, $psp_settings_name,array( &$this, 'sanitize_social_settings' ) );
		
		//Facebook Section
		$section_id = 'psp_facebook_section';		
		$section_title = esc_html__('Facebook Open Graph Sitewide Settings', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_fb_desc' ), $this->psp_social_settings_group );
		
		//Fields
		
		$og_tags_field     = array (
            'label_for' 	=> 'psp_og_tags_enabled',
            'option_name'   => $psp_settings_name.'[psp_og_tags_enabled]',
			'option_value'  => isset($psp_settings['psp_og_tags_enabled']) ? esc_attr($psp_settings['psp_og_tags_enabled']) : '',
			'checkbox_label' => esc_html__('Enable Opengraph Tags for Facebook', 'platinum-seo-pack')
        );
		
		$og_tags_field_id = 'psp_og_tags_enabled';
		$og_tags_field_title = esc_html__('Open Graph Tags: ', 'platinum-seo-pack');
		
		add_settings_field( $og_tags_field_id, $og_tags_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_social_settings_group, $section_id, $og_tags_field );
		
		//OG site name
		$psp_fb_site_name_field     = array (
            'label_for' 	=> 'psp_social_fb_site_name',
            'option_name'   => $psp_settings_name.'[fb_site_name]',
			'option_value'  => isset($psp_settings['fb_site_name']) ? esc_attr($psp_settings['fb_site_name']) : '',
			'option_description' => esc_html__( 'Enter the site name to use while sharing pages from this domain/site. For eg: "Tehblissonline" is the sitename used for the site http://techblissonline.com/. If this is left blank, then the default wordpress site name will be used.', 'platinum-seo-pack' ),
        );
		
		$psp_fb_site_name_field_id = 'psp_social_fb_site_name';	
		$psp_fb_site_name_field_title = esc_html__( 'Site Name: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_site_name_field_id, $psp_fb_site_name_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_fb_site_name_field);
		
		//Facebook admins
		$psp_fb_admins_field     = array (
            'label_for' 	=> 'psp_social_fb_admins',
            'option_name'   => $psp_settings_name.'[fb_admins]',
			'option_value'  => isset($psp_settings['fb_admins']) ? esc_attr($psp_settings['fb_admins']) : '',
			'option_description' => esc_html__( 'Enter the user ID of one or more admins for the FB app/page, if any, of your domain/website. You can enter Facebook username rather than the numeric ID, which is probably easier to debug and manage. You can find your user name on your profile page in the page URL. For eg., if you page is <code>facebook.com/johndoe</code> then your username is <code>johndoe</code>. If you want to get and use your numeric ID, you can find it by visiting <code>http://graph.facebook.com/johndoe</code>', 'platinum-seo-pack' ),
        );
		
		$psp_fb_admins_id = 'psp_social_fb_admins';	
		$psp_fb_admins_title = esc_html__( 'fb:admins (optional): ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_admins_id, $psp_fb_admins_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_fb_admins_field);
		
		//Facebook page id - deprecated
		/************
		$psp_fb_page_field     = array (
            'label_for' 	=> 'psp_social_fb_page',
            'option_name'   => $psp_settings_name.'[fb_page]',
			'option_value'  => $psp_settings['fb_page'],
			'option_description' => __( 'Enter Facebook Page ID for the facebook page,if any, created for your domain.', 'platinum-seo-pack' ),
        );
		
		$psp_fb_page_field_id = 'psp_social_fb_page';	
		$psp_fb_page_field_title = __( 'Facebook Page: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_page_field_id, $psp_fb_page_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_fb_page_field);
		***********/
		//Facebook app ID
		$psp_fb_apps_field     = array (
            'label_for' 	=> 'psp_social_fb_app',
            'option_name'   => $psp_settings_name.'[fb_app]',
			'option_value'  => isset($psp_settings['fb_app']) ? esc_attr($psp_settings['fb_app']) : '',
			'option_description' => esc_html__( 'Enter the Facebook App ID for the FB app,if any, created for your domain. This would help to get facebook domain insights about the page(s) on which it is added by way of a open graph meta tag.', 'platinum-seo-pack' ),
        );
		
		$psp_fb_apps_field_id = 'psp_social_fb_app';	
		$psp_fb_apps_field_title = esc_html__( 'Facebook Application ID: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_apps_field_id, $psp_fb_apps_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_fb_apps_field);
		
		//Facebook Secret ID
		$psp_fb_secrets_field     = array (
            'label_for' 	=> 'psp_social_fb_secret',
            'option_name'   => $psp_settings_name.'[fb_secret]',
			'option_value'  => isset($psp_settings['fb_secret']) ? esc_attr($psp_settings['fb_secret']) : '',
			'option_description' => esc_html__( 'Enter the Facebook App Secret ID for the FB app,if any, created for your domain. This would help to get facebook engagement metrics for the page(s) on which Facebook share buttons is/are added.', 'platinum-seo-pack' ),
        );
		
		$psp_fb_secret_field_id = 'psp_social_fb_secret';	
		$psp_fb_secret_field_title = esc_html__( 'Facebook Application Secret: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_secret_field_id, $psp_fb_secret_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_fb_secrets_field);
		
		//Facebook Profile ID
		$psp_fb_profile_field     = array (
            'label_for' 	=> 'psp_social_fb_profile',
            'option_name'   => $psp_settings_name.'[fb_profile]',
			'option_value'  => isset($psp_settings['fb_profile']) ? esc_attr($psp_settings['fb_profile']) : '',
			'option_description' => esc_html__( 'Enter the Facebook profile ID of a user that can be followed. Here you may even enter the page id of a facebook page that is set up for this domain/site.', 'platinum-seo-pack' ),
        );
		
		$psp_fb_profile_field_id = 'psp_social_fb_profile';	
		$psp_fb_profile_field_title = esc_html__( 'Facebook Profile ID (Optional): ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_profile_field_id, $psp_fb_profile_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_fb_profile_field);
		
		//Article Publisher
		$psp_fb_publisher_field     = array (
            'label_for' 	=> 'psp_social_fb_publisher',
            'option_name'   => $psp_settings_name.'[fb_publisher]',
			'option_value'  => isset($psp_settings['fb_publisher']) ? esc_url($psp_settings['fb_publisher']) : '',
			'option_description' => esc_html__( 'Enter the Facebook URL to the page set up for this site. For eg: https://www.facebook.com/Techblissonline', 'platinum-seo-pack' ),
        );
		
		$psp_fb_publisher_field_id = 'psp_social_fb_publisher';	
		$psp_fb_publisher_field_title = esc_html__( 'Facebook Publisher: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_publisher_field_id, $psp_fb_publisher_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_social_settings_group, $section_id,  $psp_fb_publisher_field);
		
		//Facebook og type
		$psp_og_types = array ('' => 'Select an OG type', 'website' => 'website', 'article' => 'article', 'books.author' => 'books.author', 'books.book' => 'books.book', 'books.genre' => 'books.genre', 'business.business' => 'business.business', 'fitness.course' => 'fitness.course', 'game.achievement' => 'game.achievement', 'music.album' => 'music.album', 'music.playlist' => 'music.playlist', 'music.radio_station' => 'music.radio_station', 'music.song' => 'music.song', 'place' => 'place', 'product' => 'product', 'product.group' => 'product.group', 'product.item' => 'product.item', 'profile' => 'profile', 'restaurant.menu' => 'restaurant.menu', 'restaurant.menu_item' => 'restaurant.menu_item', 'restaurant.menu_section' => 'restaurant.menu_section', 'restaurant.restaurant' => 'restaurant.restaurant', 'video.episode' => 'video.episode', 'video.movie' => 'video.movie', 'video.other' => 'video.other', 'video.tv_show' => 'video.tv_show');
		
		$psp_fb_og_type_field     = array (
            'label_for' 	=> 'psp_social_fb_og_type',
            'option_name'   => $psp_settings_name.'[fb_og_type]',
			'option_value'  => isset($psp_settings['fb_og_type']) ? esc_attr($psp_settings['fb_og_type']) : '',
			'dditems'  => $psp_og_types,
			'option_description' => esc_html__( 'Select an open graph type to be used by default for individual posts/pages of your site. For eg., if your site is a blog, you can enter <code>article</code> as the default open graph type. Note that this can be overridden through <code>Social</code> settings for your individual post in "Techblissonline Platinum SEO and Social Meta Box" on your post editor. For complete reference of open graph types refer <a href="https://ogp.me/#object-type" target="_blank">facebook developer docs reference.</a>', 'platinum-seo-pack' ),
        );
		
		$psp_fb_og_type_field_id = 'psp_social_fb_ogtype';	
		$psp_fb_og_type_field_title = esc_html__( 'Facebook Open Graph Type: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_og_type_field_id, $psp_fb_og_type_field_title, array( &$this, 'psp_add_field_dropdown' ), $this->psp_social_settings_group, $section_id,  $psp_fb_og_type_field);
		
		//OG: Locale
		$psp_locale_items = array ('en_us' => 'en_us', 'ca_es' => 'ca_es', 'cs_cz' => 'cs_cz', 'cx_ph' => 'cx_ph', 'cy_gb' => 'cy_gb', 'da_dk' => 'da_dk', 'de_de' => 'de_de', 'eu_es' => 'eu_es', 'en_pi' => 'en_pi', 'en_ud' => 'en_ud', 'es_la' => 'es_la', 'es_co' => 'es_co', 'es_es' => 'es_es', 'gn_py' => 'gn_py', 'fi_fi' => 'fi_fi', 'fr_fr' => 'fr_fr', 'gl_es' => 'gl_es', 'hu_hu' => 'hu_hu', 'it_it' => 'it_it', 'ja_jp' => 'ja_jp', 'ko_kr' => 'ko_kr', 'nb_no' => 'nb_no', 'nn_no' => 'nn_no', 'nl_nl' => 'nl_nl', 'fy_nl' => 'fy_nl', 'pl_pl' => 'pl_pl', 'pt_br' => 'pt_br', 'pt_pt' => 'pt_pt', 'ro_ro' => 'ro_ro', 'ru_ru' => 'ru_ru', 'sk_sk' => 'sk_sk', 'sl_si' => 'sl_si', 'sv_se' => 'sv_se', 'th_th' => 'th_th', 'tr_tr' => 'tr_tr', 'ku_tr' => 'ku_tr', 'zh_cn' => 'zh_cn', 'zh_hk' => 'zh_hk', 'zh_tw' => 'zh_tw', 'fb_lt' => 'fb_lt', 'af_za' => 'af_za', 'sq_al' => 'sq_al', 'hy_am' => 'hy_am', 'az_az' => 'az_az', 'be_by' => 'be_by', 'bn_in' => 'bn_in', 'bs_ba' => 'bs_ba', 'bg_bg' => 'bg_bg', 'hr_hr' => 'hr_hr', 'nl_be' => 'nl_be', 'en_gb' => 'en_gb', 'eo_eo' => 'eo_eo', 'et_ee' => 'et_ee', 'fo_fo' => 'fo_fo', 'fr_ca' => 'fr_ca', 'ka_ge' => 'ka_ge', 'el_gr' => 'el_gr', 'gu_in' => 'gu_in', 'hi_in' => 'hi_in', 'is_is' => 'is_is', 'id_id' => 'id_id', 'ga_ie' => 'ga_ie', 'jv_id' => 'jv_id', 'kn_in' => 'kn_in', 'kk_kz' => 'kk_kz', 'la_va' => 'la_va', 'lv_lv' => 'lv_lv', 'lt_lt' => 'lt_lt', 'mk_mk' => 'mk_mk', 'ms_my' => 'ms_my', 'mr_in' => 'mr_in', 'mn_mn' => 'mn_mn', 'ne_np' => 'ne_np', 'pa_in' => 'pa_in', 'sr_rs' => 'sr_rs', 'sw_ke' => 'sw_ke', 'tl_ph' => 'tl_ph', 'ta_in' => 'ta_in', 'te_in' => 'te_in', 'ml_in' => 'ml_in', 'uk_ua' => 'uk_ua', 'uz_uz' => 'uz_uz', 'vi_vn' => 'vi_vn', 'km_kh' => 'km_kh', 'tg_tj' => 'tg_tj', 'ar_ar' => 'ar_ar', 'he_il' => 'he_il', 'ur_pk' => 'ur_pk', 'fa_ir' => 'fa_ir', 'ps_af' => 'ps_af', 'si_lk' => 'si_lk', 'ja_ks' => 'ja_ks');
		
		$psp_fb_og_locale_field     = array (
            'label_for' 	=> 'psp_social_fb_locale',
            'option_name'   => $psp_settings_name.'[fb_locale]',
			'option_value'  => isset($psp_settings['fb_locale']) ? esc_attr($psp_settings['fb_locale']) : '',
			'dditems'  => $psp_locale_items,
			'option_description' => esc_html__( 'Enter the locale that the facebook object tags are marked up in. Default is en_US. For valid values refer <a href="https://techblissonline.com/facebook-locales-and-languages/" target="_blank">Techblissonline</a>', 'platinum-seo-pack' ),
        );
		
		$psp_fb_og_locale_id = 'psp_social_fb_locale';	
		$psp_fb_og_locale_title = esc_html__( 'Open Graph Locale: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_og_locale_id, $psp_fb_og_locale_title, array( &$this, 'psp_add_field_dropdown' ), $this->psp_social_settings_group, $section_id,  $psp_fb_og_locale_field);
		
		//Facebook default image
		$psp_fb_defailt_img_field     = array (
            'label_for' 	=> 'psp_social_fb_default_img',
            'option_name'   => $psp_settings_name.'[fb_default_image]',
			'option_value'  => isset($psp_settings['fb_default_image']) ? esc_url($psp_settings['fb_default_image']) : '',
			'option_description' => esc_html__( 'Enter the image URL or upload an image to be used as a default image while sharing any post/page on facebook. This will be used if a post/page does not have any image', 'platinum-seo-pack' ),
			'button' 	=> 1,
        );
		
		$psp_fb_defailt_img_field_id = 'psp_social_fb_default_img';	
		$psp_fb_defailt_img_field_title = esc_html__( 'Default image for sharing on facebook: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_fb_defailt_img_field_id, $psp_fb_defailt_img_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_social_settings_group, $section_id,  $psp_fb_defailt_img_field);
		
		//disable Article plublished, modified, update times
		$og_times_field     = array (
            'label_for' 	=> 'psp_og_times_disabled',
            'option_name'   => $psp_settings_name.'[psp_og_times_disabled]',
			'option_value'  => isset($psp_settings['psp_og_times_disabled']) ? esc_attr($psp_settings['psp_og_times_disabled']) : '',
			'checkbox_label' => esc_html__('Disable Published, Modified Dates for Articles', 'platinum-seo-pack')
        );
		
		$og_times_field_id = 'psp_og_times_disabled';
		$og_times_field_title = esc_html__('Open Graph Published and Modified Dates: ', 'platinum-seo-pack');
		
		add_settings_field( $og_times_field_id, $og_times_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_social_settings_group, $section_id, $og_times_field );
		
		//Twitter Section
		$section_id = 'psp_twitter_section';		
		$section_title = esc_html__('Twitter Card Sitewide Settings', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_twitter_desc' ), $this->psp_social_settings_group );
		
		$twitter_card_field     = array (
            'label_for' 	=> 'psp_twitter_card_enabled',
            'option_name'   => $psp_settings_name.'[psp_twitter_card_enabled]',
			'option_value'  => isset($psp_settings['psp_twitter_card_enabled']) ? esc_attr($psp_settings['psp_twitter_card_enabled']) : '',
			'checkbox_label' => esc_html__('Enable Twitter Card', 'platinum-seo-pack')
        );
		
		$twitter_card_field_id = 'psp_twitter_card_enabled';
		$twitter_card_field_title = esc_html__('Twitter Card: ', 'platinum-seo-pack');
		
		add_settings_field( $twitter_card_field_id, $twitter_card_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_social_settings_group, $section_id, $twitter_card_field );
		
		//Twitter Card type
		$psp_tc_types = array ('' => 'Select a card type', 'summary' => 'summary', 'summary_large_image' => 'summary with large image');
		
		$psp_tw_ct_type_field     = array (
            'label_for' 	=> 'psp_social_tw_ctype',
            'option_name'   => $psp_settings_name.'[tw_ct_type]',
			'option_value'  => isset($psp_settings['tw_ct_type']) ? esc_attr($psp_settings['tw_ct_type']) : '',
			'dditems'  => $psp_tc_types,
			'option_description' => esc_html__( 'Enter the twitter card type to be used by default for individual posts/pages of your site. For eg., if your site is a blog, you can enter <code>summary</code> as the default twitter card type. Note that this can be overridden through <code>Social</code> settings for your individual post in "Techblissonline Platinum SEO and Social Meta Box" on your post editor. For complete reference of twitter card types refer <a href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/getting-started" target="_blank">twitter development document reference.</a>', 'platinum-seo-pack' ),
        );
		
		$psp_tw_ct_type_field_id = 'psp_social_tw_ctype';	
		$psp_tw_ct_type_field_title = esc_html__( 'Twitter CardType: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_tw_ct_type_field_id, $psp_tw_ct_type_field_title, array( &$this, 'psp_add_field_dropdown' ), $this->psp_social_settings_group, $section_id,  $psp_tw_ct_type_field);
		
		//Twitter user
		$psp_tw_user_field     = array (
            'label_for' 	=> 'psp_social_tw_user',
            'option_name'   => $psp_settings_name.'[tw_user]',
			'option_value'  => isset($psp_settings['tw_user']) ? esc_attr($psp_settings['tw_user']) : '',
			'option_description' => esc_html__( 'The Twitter <code>@username</code> the card should be attributed to. This is usually the twitter handle created for your domain /website. However, You might even choose to use your personal twitter user id here. If you twitter user id is <code>@johndoe</code>, enter <code>johndoe</code> as the user id here. This user id is required for <a href="https://analytics.twitter.com/" target="_blank">Twitter Card analytics</a>', 'platinum-seo-pack' ),
        );
		
		$psp_tw_user_field_id = 'psp_social_tw_user';	
		$psp_tw_user_field_title = esc_html__( 'Twitter User: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_tw_user_field_id, $psp_tw_user_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_tw_user_field);
		
		//Twitter default image
		$psp_tw_defailt_img_field     = array (
            'label_for' 	=> 'psp_social_tw_default_img',
            'option_name'   => $psp_settings_name.'[tw_default_image]',
			'option_value'  => isset($psp_settings['tw_default_image']) ? esc_url($psp_settings['tw_default_image']) : '',
			'option_description' => esc_html__( 'Enter the image URL or upload an image to be used as a default image while sharing any post/page on twitter. This will be used if a post/page does not have any image', 'platinum-seo-pack' ),
			'button' 	=> 1,
        );
		
		$psp_tw_defailt_img_field_id = 'psp_social_tw_default_img';	
		$psp_tw_defailt_img_field_title = esc_html__( 'Default image for sharing on twitter: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_tw_defailt_img_field_id, $psp_tw_defailt_img_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_social_settings_group, $section_id,  $psp_tw_defailt_img_field);
		
		//Schema.org Section
		$section_id = 'psp_schema_org_section';		
		$section_title = esc_html__('Pinterest and Linkedin Settings', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_schema_org_desc' ), $this->psp_social_settings_group );
		
		//Schema.org tags
		$schema_org_field     = array (
            'label_for' 	=> 'psp_schemaorg_markup_enabled',
            'option_name'   => $psp_settings_name.'[psp_schemaorg_markup_enabled]',
			'option_value'  => isset($psp_settings['psp_schemaorg_markup_enabled']) ? esc_attr($psp_settings['psp_schemaorg_markup_enabled']) : '',
			'checkbox_label' => esc_html__('Enable markup for Pinterest and Linkedin.', 'platinum-seo-pack')
        );
		
		$schema_org_field_id = 'psp_schemaorg_markup_enabled';
		$schema_org_field_title = esc_html__('Enable Markup: ', 'platinum-seo-pack');
		
		add_settings_field( $schema_org_field_id, $schema_org_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_social_settings_group, $section_id, $schema_org_field );

		//Article Publisher
		/********
		$psp_sc_publisher_field     = array (
            'label_for' 	=> 'psp_social_sc_publisher',
            'option_name'   => $psp_settings_name.'[sc_publisher]',
			'option_value'  => isset($psp_settings['sc_publisher']) ? $psp_settings['sc_publisher'] : '',
			'option_description' => __( 'Enter the Google+ URL to the page set up for this site. For eg: https://plus.google.com/+Techblissonlinecom/about', 'platinum-seo-pack' ),
        );
		
		$psp_sc_publisher_field_id = 'psp_social_sc_publisher';	
		$psp_sc_publisher_field_title = __( 'Publisher for Google+ : ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_sc_publisher_field_id, $psp_sc_publisher_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_sc_publisher_field);
		******/
		//Article author
		/********
		$psp_sc_author_field     = array (
            'label_for' 	=> 'psp_social_sc_author',
            'option_name'   => $psp_settings_name.'[sc_author]',
			'option_value'  => $psp_settings['sc_author'],
			'option_description' => __( 'Enter the Google+ URL to the default author profile for the articles on your site. For eg: https://plus.google.com/+Techblissonlinecom/about', 'platinum-seo-pack' ),
        );
		
		$psp_sc_author_field_id = 'psp_social_sc_author';	
		$psp_sc_author_field_title = __( 'Author (Default) : ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_sc_author_field_id, $psp_sc_author_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_social_settings_group, $section_id,  $psp_sc_author_field);
		************/
	}
	
	function sanitize_social_settings($settings) {

		$psp_allowed_protocols = array('http','https');	

		if ( isset( $settings['psp_og_tags_enabled'] ) ) {
			$settings['psp_og_tags_enabled'] = !is_null(filter_var($settings['psp_og_tags_enabled'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['psp_og_tags_enabled'] : '';
		}
	
		if( isset( $settings['fb_site_name'] ) ) $settings['fb_site_name'] = sanitize_text_field( $settings['fb_site_name'] );
		if( isset( $settings['fb_admins'] ) ) $settings['fb_admins'] = sanitize_text_field( $settings['fb_admins'] );
		if( isset( $settings['fb_app'] ) ) $settings['fb_app'] = sanitize_text_field( $settings['fb_app'] );
		if( isset( $settings['fb_secret'] ) ) $settings['fb_secret'] = sanitize_text_field( $settings['fb_secret'] );		
		if( isset( $settings['fb_profile'] ) ) $settings['fb_profile'] = sanitize_text_field( $settings['fb_profile'] );
		if ( isset( $settings['fb_publisher'] ) ) {
			$settings['fb_publisher'] = esc_url_raw( $settings['fb_publisher'], $psp_allowed_protocols );
		}
		
		if ( isset( $settings['fb_og_type'] ) ) {
			$settings['fb_og_type'] =  sanitize_text_field($settings['fb_og_type']) ;
			
			$fb_og_types = array ('website', 'article', 'books.author', 'books.book', 'books.genre', 'business.business', 'fitness.course', 'game.achievement', 'music.album', 'music.playlist', 'music.radio_station', 'music.song', 'place', 'product', 'product.group', 'product.item', 'profile', 'restaurant.menu', 'restaurant.menu_item', 'restaurant.menu_section', 'restaurant.restaurant', 'video.episode', 'video.movie', 'video.other', 'video.tv_show');
			
			if (!in_array($settings['fb_og_type'], $fb_og_types)) {
				$settings['fb_og_type'] = '';
			}
		}
		
		if ( isset( $settings['fb_locale'] ) ) {
			$settings['fb_locale'] =  sanitize_text_field($settings['fb_locale']) ;
			
			$fb_locales = array ('en_us', 'ca_es', 'cs_cz', 'cx_ph', 'cy_gb', 'da_dk', 'de_de', 'eu_es', 'en_pi', 'en_ud', 'es_la', 'es_co', 'es_es', 'gn_py', 'fi_fi', 'fr_fr', 'gl_es', 'hu_hu', 'it_it', 'ja_jp', 'ko_kr', 'nb_no', 'nn_no', 'nl_nl', 'fy_nl', 'pl_pl', 'pt_br', 'pt_pt', 'ro_ro', 'ru_ru', 'sk_sk', 'sl_si', 'sv_se', 'th_th', 'tr_tr', 'ku_tr', 'zh_cn', 'zh_hk', 'zh_tw', 'fb_lt', 'af_za', 'sq_al', 'hy_am', 'az_az', 'be_by', 'bn_in', 'bs_ba', 'bg_bg', 'hr_hr', 'nl_be', 'en_gb', 'eo_eo', 'et_ee', 'fo_fo', 'fr_ca', 'ka_ge', 'el_gr', 'gu_in', 'hi_in', 'is_is', 'id_id', 'ga_ie', 'jv_id', 'kn_in', 'kk_kz', 'la_va', 'lv_lv', 'lt_lt', 'mk_mk', 'ms_my', 'mr_in', 'mn_mn', 'ne_np', 'pa_in', 'sr_rs', 'sw_ke', 'tl_ph', 'ta_in', 'te_in', 'ml_in', 'uk_ua', 'uz_uz', 'vi_vn', 'km_kh', 'tg_tj', 'ar_ar', 'he_il', 'ur_pk', 'fa_ir', 'ps_af', 'si_lk', 'ja_ks');
			
			if (!in_array($settings['fb_locale'], $fb_locales)) {
				$settings['fb_locale'] = '';
			}
		}		
		
		if ( isset( $settings['fb_default_image'] ) ) {
			$settings['fb_default_image'] = esc_url_raw( $settings['fb_default_image'], $psp_allowed_protocols );
		}
		
		if ( isset( $settings['psp_og_times_disabled'] ) ) {
			$settings['psp_og_times_disabled'] = !is_null(filter_var($settings['psp_og_times_disabled'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['psp_og_times_disabled'] : '';
		}

		if ( isset( $settings['psp_twitter_card_enabled'] ) ) {
			$settings['psp_twitter_card_enabled'] = !is_null(filter_var($settings['psp_twitter_card_enabled'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['psp_twitter_card_enabled'] : '';
		}
		
		if ( isset( $settings['tw_ct_type'] ) ) {
			$settings['tw_ct_type'] =  sanitize_text_field($settings['tw_ct_type']);
			
			$tw_card_types =  array ('summary', 'summary_large_image', 'player', 'app');
			
			if (!in_array($settings['tw_ct_type'], $tw_card_types)) {
				$settings['tw_ct_type'] = '';
			}
		}	
		
		if( isset( $settings['tw_user'] ) ) $settings['tw_user'] = sanitize_text_field( $settings['tw_user'] );
		
		if ( isset( $settings['tw_default_image'] ) ) {
			$settings['tw_default_image'] = esc_url_raw( $settings['tw_default_image'], $psp_allowed_protocols );
		}
		
		if ( isset( $settings['psp_schemaorg_markup_enabled'] ) ) {
			$settings['psp_schemaorg_markup_enabled'] = !is_null(filter_var($settings['psp_schemaorg_markup_enabled'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['psp_schemaorg_markup_enabled'] : '';
		}
		
		return $settings;

	}
	
	function section_fb_desc() {echo '';}
	function section_twitter_desc() {echo ''; }
	function section_schema_org_desc() {echo ''; }
	function section_social_share_desc() {echo ''; }
	function section_media_share_desc() {echo ''; }
	
	/*
	 * renders Plugin social settings page, checks
	 * for the active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function psp_social_options_page() {
		$tab = isset( $_GET['pspsocial'] ) ? Sanitize_key($_GET['pspsocial']) : $this->psp_social_settings_group;
		
		$psp_pre_settings = get_option('psp_pre_setting');		
		$psp_premium_valid = isset($psp_pre_settings['psp_premium_license_key_status']) ? $psp_pre_settings['psp_premium_license_key_status'] : '';
		//$psp_premium_status = isset($psp_pre_settings['psp_premium_license_key_status']) ? $psp_pre_settings['psp_premium_license_key_status'] : '';
			
		if ($tab == $this->psp_social_settings_group) {
			$psp_button = "submitsocial";
			$psp_nonce_field = "psp-social";
		} elseif ($tab == $this->psp_social_share_settings_group) {
			$psp_button = "submitsocialshare";
			$psp_nonce_field = "psp-social-share";			
		}		
		?><style>.nav-tab{background: #f1f1f1 !important; box-shadow: none !important} a.nav-tab{color:#111 !important} .description{margin-bottom: 15px !important;} .psp-bs{font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif !important;} .psp-bs h1{font-size: 23px !important; font-weight:400 !important} body{background: #fff !important;} .psp-btn {
    -webkit-box-shadow: 2px 3px 0 rgba(4, 4, 4, 0.3), inset 0 -4px 0 rgba(4,4,4,.9) !important;
    -moz-box-shadow:    2px 3px 0 rgba(4, 4, 4, 0.3), inset 0 -4px 0 rgba(4,4,4,.9) !important;
    box-shadow:         2px 3px 0 rgba(4, 4, 4, 0.3), inset 0 -4px 0 rgba(0,0,0,.9 ) !important;
    border-radius:0 !important;
    padding-bottom: 10px !important;
} </style>
		<div class="psp-bs">
		<div class="wrap">
			<h1 style='line-height:30px;'><?php esc_html_e('Techblissonline Platinum SEO Pack - 
			Social', 'platinum-seo-pack') ?></h1>
			<p style="color: red"><?php esc_html_e('You need to click the "Save Settings" button to save the changes you made to each individual tab before moving on to the next tab.', 'platinum-seo-pack') ?></p>		
			<?php $this->psp_social_tabs(); ?>
			<?php if (($tab == $this->psp_social_share_settings_group && $psp_premium_valid) || $tab == $this->psp_social_settings_group) { ?>
			<form name="platinum-seo-form" method="post" action="options.php">
				<?php wp_nonce_field( $psp_nonce_field ); ?>
				<?php settings_fields( $tab ); ?>
				<?php settings_errors(); ?>
				<?php do_settings_sections( $tab ); ?>
				<?php submit_button('Save Settings', 'primary', $psp_button); ?>
			</form>
			<?php } else { include_once( 'psp_premiumad_metabox_renderer.php' ); } ?>
			<div class="sidebar-cta">
			<h2>   
				<a class="bookmarkme" href="<?php echo 'https://techblissonline.com/tools/'; ?>" target="_blank"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-logo.png'; ?>" class="img-responsive" alt="Techblissonline Platinum SEO WordPress Tools"/></a>
				<a class="bookmarkme" href="<?php echo 'https://techblissonline.com/tools/platinum-seo-wordpress-premium/'; ?>" target="_blank"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/platinum-seo-no.jpg'; ?>" class="img-responsive" alt="Techblissonline Platinum SEO WordPress Tools"/></a>
			</h2>
			    <div class="container bg-info" id="tools" style="width:100%">
                    <div class="row"><div class="h3 col-sm-12 btn-primary psp-btn"><a class="col-sm-12" style="text-decoration:none;width:100%;color:white;" href="https://techblissonline.com/tools/platinum-seo-wordpress-premium/" target="_blank">Platinum SEO Premium for WordPress</a></div><div class="h3 col-sm-12 btn-success psp-btn"><a class="col-sm-12" style="text-decoration:none;width:100%;color:white;" href="https://techblissonline.com/tools/" target="_blank">Techblissonline Platinum SEO Audit and Analysis Tools</a></div></div> 
                </div>				
			</div>
		
		</div>
		</div>
		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * psp_options_page method.
	 */
	function psp_social_tabs() {
		$current_tab = isset( $_GET['pspsocial'] ) ? Sanitize_key($_GET['pspsocial']) : $this->psp_social_settings_group;
		//$current_tab = $active_tab;
		wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
		//wp_enqueue_style("psp-htmlsettings-css", plugins_url( '/css/psp-html-settings.css', __FILE__ ));
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
		wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), '2.3.5');
		//wp_enqueue_style("psp-settings-bswide-css", plugins_url( '/css/psp-settings-bswide.css', __FILE__ ));
		//screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->psp_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			$psp_icon = '';
			if ($tab_key == $this->psp_social_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-admin-site"></span> ';
			} 
			if ($tab_key == $this->psp_social_share_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-share"></span> ';
			} 
			echo '<a style="text-decoration:none" class="nav-tab ' . esc_attr($active) . '" href="?page=' . esc_attr($this->psp_plugin_options_key) . '&pspsocial=' . esc_attr($tab_key) . '">' .$psp_icon. esc_attr($tab_caption) . '</a>';				
		}
		echo '</h2>';
	}
}