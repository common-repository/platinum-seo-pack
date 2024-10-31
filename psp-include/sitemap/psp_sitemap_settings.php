<?php

/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: in content Links Management class
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/ 
*/

class PspSmSettings extends PspSettings {	
	 
	private static $obj_handle = null;	
	
	public $custom_taxonomies = array();
	
	// this is the URL our updater / license checker pings. 
	private static $PSPP_SITE_URL = 'https://techblissonline.com/tools/platinum-seo-wordpress-premium/'; 

	// Product Name
	private static $PSPP_ITEM_NAME = 'techblissonline_platinum_seo_premium'; 

	// the name of the settings page for the links management settings to be displayed
	private static $PSPP_LINKS_PAGE = 'pspp-links';
	
	private $psp_helper;
	private $psp_settings_instance;
	private $sitename;
	private $sitedescription;	
	
	private $plugin_settings_tabs = array();
	 
	private $psp_sm_settings_group = 'psp_sitemap';	
	
	protected $psp_plugin_options_key = 'psp-sm-by-techblissonline';
	//private $psp_plugin_lic_key = 'psp-pre-by-techblissonline';
	private $psp_settings_tabs = array();

	private $psp_sm_settings = array();
	private $psp_settings = array();
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;	
	
	function __construct() {

		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;
		
			
		$this->sitename = $psp_helper_instance->get_sitename();
		
		if (get_option("psp_tools_plugin_url")) {
		    self::$PSPP_SITE_URL = get_option("psp_tools_plugin_url");
		}
		
		$this->psp_settings_tabs[$this->psp_sm_settings_group] = 'SiteMap Settings';		
		
		//$this->psp_settings = get_option("psp_sitewide_settings");
		
		add_action( 'admin_init', array( &$this, 'psp_sm_settings_init' ) );
		//add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );		
		
	}
	
	function psp_sm_settings_init() {

		if ( null == $this->custom_taxonomies ) {
			$args = array(
							'public'   => true,
							'_builtin' => false		  
						); 			
			$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$cust_taxonomies = get_taxonomies( $args, $output, $operator );
			$this->custom_taxonomies = $cust_taxonomies;
			//error_log("custom taxonomies ".print_r($cust_taxonomies, true));
		}
		
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
	
		//$this->register_sm_settings();			
		
	}	
	
	/*
	 * Registers the Home SEO settings and appends the
	 * key to the plugin settings tabs array.
	 */
	protected function register_sm_settings() {
		$this->psp_settings_tabs[$this->psp_sm_settings_group] = 'SiteMap Settings';		
		$psp_sm_settings_name = "psp_sitemap";
		
		$psp_sm_settings = get_option($psp_sm_settings_name);
		if (!empty($psp_sm_settings)) $this->psp_sm_settings = $psp_sm_settings;
		
		//wp_enqueue_script( 'psp-input-toggler', plugins_url( '/js/pspinputtypetoggler.js', __FILE__ ), array( 'jquery' ) );
		//register
		register_setting( $this->psp_sm_settings_group, $psp_sm_settings_name, array( &$this, 'psp_sanitize_sm_settings' ) );
		//add Section
		add_settings_section( 'psp_sitemap_section', esc_html__('SiteMap Settings', 'platinum-seo-pack' ), array( &$this, 'section_sm_desc' ), $this->psp_sm_settings_group );
		
		//add fields
		
		//Enable siteMap
		$sitemap_field     = array (
            'label_for' 	=> 'psp_'.$psp_sm_settings_name.'_enable',
            'option_name'   => $psp_sm_settings_name.'[enable]',
			'option_value'  => isset($psp_sm_settings['enable']) ? $psp_sm_settings['enable'] : '',
			'checkbox_label' => esc_html__( 'Enable ', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should always remain checked if you want to create sitemaps using Platinum SEO', 'platinum-seo-pack' ),
        );			
			
		$sitemap_field_id = 'psp_'.$psp_sm_settings_name.'_enable';		
		$sitemap_field_title = esc_html__( 'Use Platinum SEO SiteMaps: ', 'platinum-seo-pack' );	
		
		add_settings_field( $sitemap_field_id, $sitemap_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $sitemap_field );
		
		//SiteMaps for Taxonomies
		$psp_taxonomies = array();		
		
		$builtin_taxonomies = array("category", "post_tag");
		$custom_taxonomies = $this->custom_taxonomies;
		$psp_all_taxonomies = array_merge((array)$builtin_taxonomies, (array)$custom_taxonomies);
		$psp_taxonomies = array_combine($psp_all_taxonomies, $psp_all_taxonomies);

		$psp_taxonomies_list_field     = array (
            'label_for' 	=> 'psp_taxonomies_list',
            'option_name'   => $psp_sm_settings_name.'[taxonomies_list][]',
			'option_value'  => isset($psp_sm_settings['taxonomies_list']) ? $psp_sm_settings['taxonomies_list'] : '',
			'checkboxitems' => $psp_taxonomies,
			'option_description' => esc_html__( 'Select the list of Taxonomies for which SiteMaps have to be generated.', 'platinum-seo-pack' ),
        );
		
		$psp_sitemap_taxonomies_field_id = 'psp_taxonomies_list';		
		$psp_sitemap_taxonomies_field_title = esc_html__('Taxonomies: ', 'platinum-seo-pack');	
		
		add_settings_field( $psp_sitemap_taxonomies_field_id, $psp_sitemap_taxonomies_field_title, array( &$this, 'psp_add_multiple_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section',  $psp_taxonomies_list_field);
		
		//SiteMaps for Post Types
		$psp_post_types = array();		
		
		$builtin_post_types = array("post", "page");
		$custom_post_types = $this->custom_post_types;
		$psp_all_posttypes = array_merge((array)$builtin_post_types, (array)$custom_post_types);
		$psp_post_types = array_combine($psp_all_posttypes, $psp_all_posttypes);
		
		$psp_posttypes_list_field     = array (
            'label_for' 	=> 'psp_posttypes_list',
            'option_name'   => $psp_sm_settings_name.'[posttypes_list][]',
			'option_value'  => isset($psp_sm_settings['posttypes_list']) ? $psp_sm_settings['posttypes_list'] : '',
			'checkboxitems' => $psp_post_types,
			'option_description' => esc_html__( 'Select the list of Post Types for which SiteMaps have to be generated.', 'platinum-seo-pack' ),
        );
		
		$psp_sitemap_posttypes_field_id = 'psp_posttypes_list';		
		$psp_sitemap_posttypes_field_title = esc_html__('Post Types: ', 'platinum-seo-pack');	
		
		add_settings_field( $psp_sitemap_posttypes_field_id, $psp_sitemap_posttypes_field_title, array( &$this, 'psp_add_multiple_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section',  $psp_posttypes_list_field);
		
		//include last modified time in Post type SiteMaps
		$sitemap_lastmod_field     = array (
            'label_for' 	=> 'psp_'.$psp_sm_settings_name.'_include_lastmod',
            'option_name'   => $psp_sm_settings_name.'[include_lastmod]',
			'option_value'  => isset($psp_sm_settings['include_lastmod']) ? $psp_sm_settings['include_lastmod'] : '',
			'checkbox_label' => esc_html__( 'Enable ', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should always remain checked if you want to include Last Modified DateTime in Post type sitemaps created using Platinum SEO', 'platinum-seo-pack' ),
        );			
			
		$sitemap_lastmod_field_id = 'psp_'.$psp_sm_settings_name.'_include_lastmod';		
		$sitemap_lastmod_field_title = esc_html__( 'Include Last Modified DateTime: ', 'platinum-seo-pack' );	
		
		add_settings_field( $sitemap_lastmod_field_id, $sitemap_lastmod_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $sitemap_lastmod_field );
		
		//include images in Post type SiteMaps
		$sitemap_image_field     = array (
            'label_for' 	=> 'psp_'.$psp_sm_settings_name.'_include_images',
            'option_name'   => $psp_sm_settings_name.'[include_images]',
			'option_value'  => isset($psp_sm_settings['include_images']) ? $psp_sm_settings['include_images'] : '',
			'checkbox_label' => esc_html__( 'Enable ', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should always remain checked if you want to include images in Post type sitemaps created using Platinum SEO', 'platinum-seo-pack' ),
        );			
			
		$sitemap_image_field_id = 'psp_'.$psp_sm_settings_name.'_include_images';		
		$sitemap_image_field_title = esc_html__( 'Include Images: ', 'platinum-seo-pack' );	
		
		add_settings_field( $sitemap_image_field_id, $sitemap_image_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $sitemap_image_field );
		
		//Asc or Desc in Post type SiteMaps
		$sitemap_sort_field     = array (
            'label_for' 	=> 'psp_'.$psp_sm_settings_name.'_sort_order',
            'option_name'   => $psp_sm_settings_name.'[sort_order]',
			'option_value'  => isset($psp_sm_settings['sort_order']) ? $psp_sm_settings['sort_order'] : '',
			'checkbox_on_value' => esc_html__( 'Ascending ', 'platinum-seo-pack' ),
			'checkbox_off_value' => esc_html__( 'Descending ', 'platinum-seo-pack' ),
			'checkbox_label' => esc_html__( 'Order of POST IDs', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'Sort order of Post Entries included in Post Type SiteMaps created using Platinum SEO. The POST entries are always sorted by Ppost ID.', 'platinum-seo-pack' ),
        );			
			
		$sitemap_sort_field_id = 'psp_'.$psp_sm_settings_name.'_sort_order';		
		$sitemap_sort_field_title = esc_html__( 'Posts Sort Order: ', 'platinum-seo-pack' );	
		
		add_settings_field( $sitemap_sort_field_id, $sitemap_sort_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $sitemap_sort_field );

		//Enable siteMap for Authors
		$sitemap_author_field     = array (
            'label_for' 	=> 'psp_'.$psp_sm_settings_name.'_enable_authors',
            'option_name'   => $psp_sm_settings_name.'[enable_authors]',
			'option_value'  => isset($psp_sm_settings['enable_authors']) ? $psp_sm_settings['enable_authors'] : '',
			'checkbox_label' => esc_html__( 'Enable ', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should be checked only if you want to create sitemaps for Author Archives', 'platinum-seo-pack' ),
        );			
			
		$sitemap_author_field_id = 'psp_'.$psp_sm_settings_name.'_enable_authors';		
		$sitemap_author_field_title = esc_html__( 'Platinum SEO SiteMap for Authors: ', 'platinum-seo-pack' );	
		
		add_settings_field( $sitemap_author_field_id, $sitemap_author_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $sitemap_author_field );	
		
		//Number of URLs per sitemap	
		$psp_url_limit_field     = array (
            'label_for' 	=> 'psp_url_limit',
            'option_name'   => $psp_sm_settings_name.'[max_urls]',
			'option_value'  => isset($psp_sm_settings['max_urls']) ? $psp_sm_settings['max_urls'] : 2000,
			'option_label' => esc_html__( 'URLs per SiteMap', 'platinum-seo-pack' ),
			'option_description' => esc_html__( 'Set the max number of URLs for a sitemap.(Highly Recommended)', 'platinum-seo-pack' ),
        );
		
		$psp_url_limit_field_id = 'psp_url_limit';
		$psp_url_limit_field_title = esc_html__('Limit number of URLs to: ', 'platinum-seo-pack');
		
		add_settings_field( $psp_url_limit_field_id, $psp_url_limit_field_title, array( &$this, 'psp_add_field_text_number' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $psp_url_limit_field);
		
		//Excluded Post IDs
		$psp_sm_posts_field_title = esc_html__( 'Excluded Posts: ', 'platinum-seo-pack' );
		
		$psp_sm_posts_field     = array (
            'label_for' 	=> 'psp_sm_posts_id',
            'option_name'   => $psp_sm_settings_name.'[excluded_post_ids]',
			'option_value'  => isset($psp_sm_settings['excluded_post_ids']) ? esc_attr($psp_sm_settings['excluded_post_ids']) : '',
			'option_description' => esc_html__( 'Enter a comma separated list of Post IDs to be excluded from SiteMap. This can be automatically updated via Platinum SEO Meta Box for individual Posts or Pages.NoIndex Pages are excluded from the sitemap by default and you need not have to enter them here again.', 'platinum-seo-pack' ),
        );
		add_settings_field( 'psp_sm_posts_id', $psp_sm_posts_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_sm_settings_group, 'psp_sitemap_section',  $psp_sm_posts_field);	

			
		//Excluded Term IDs
		$psp_sm_terms_field_title = esc_html__( 'Excluded Terms (Category IDs/Term IDs): ', 'platinum-seo-pack' );
		
		$psp_sm_terms_field     = array (
            'label_for' 	=> 'psp_sm_terms_ids',
            'option_name'   => $psp_sm_settings_name.'[excluded_term_ids]',
			'option_value'  => isset($psp_sm_settings['excluded_term_ids']) ? esc_attr($psp_sm_settings['excluded_term_ids']) : '',
			'option_description' => esc_html__( 'Enter a comma separated list of Term IDs to be excluded from SiteMap. This can be automatically updated via Platinum SEO Meta Box for individual Category or Taxonomy Terms. NoIndex Terms are excluded from the sitemap by default and you need not have to enter them here again.', 'platinum-seo-pack' ),
        );
		add_settings_field( 'psp_sm_terms_ids', $psp_sm_terms_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_sm_settings_group, 'psp_sitemap_section',  $psp_sm_terms_field);			
		
	}
	
	function section_sm_desc() {echo ''; }
	//function section_bl_mgmt_desc() {echo ''; }
	
	function psp_sanitize_sm_settings( $settings ) {
	    
	    if( ! empty( $settings['wizard'] ) ) {
			
			return $settings;
			
		}
		
		if ( isset( $settings['enable'] ) ) {
			$settings['enable'] = !is_null(filter_var($settings['enable'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['enable'] : '';						
		}
		
		//global $wp_rewrite;	
		$wp_sitemaps = new PSP_Sitemaps();
		
		if( isset( $settings['enable']) && $settings['enable'] ) {				
			$wp_sitemaps->register_rewrites();
			//$wp_rewrite -> flush_rules();
			flush_rewrite_rules( false );
		} else {
			$wp_sitemaps->unregister_rewrites();
			//$wp_rewrite -> flush_rules();
			flush_rewrite_rules( false );
		}
		
		if ( isset( $settings['sort_order'] ) ) {
			$settings['sort_order'] = !is_null(filter_var($settings['sort_order'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['sort_order'] : '';
			
		}
		
		if ( isset( $settings['include_images'] ) ) {
			$settings['include_images'] = !is_null(filter_var($settings['include_images'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['include_images'] : '';
			
		}
		
		if ( isset( $settings['include_lastmod'] ) ) {
			$settings['include_lastmod'] = !is_null(filter_var($settings['include_lastmod'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['include_lastmod'] : '';
			
		}

		if ( isset( $settings['enable_authors'] ) ) {			
			$settings['enable_authors'] = !is_null(filter_var($settings['enable_authors'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['enable_authors'] : '';			
		}	
		
		if ( isset( $settings['max_urls'] ) ) {
			$settings['max_urls'] = sanitize_text_field( $settings['max_urls'] );
			if (!filter_var($settings['max_urls'], FILTER_VALIDATE_INT) ) {
				$settings['max_urls'] = 2000;
			}			
		}
		
		if ( isset( $settings['taxonomies_list'] ) ) {
    		
			$settings['taxonomies_list'] = array_map( 'sanitize_text_field', $settings['taxonomies_list'] );
    		
    		$builtin_tax = array("category", "post_tag");
    		$custom_tax = array();
    		$psp_all_tax = array();
    		$custom_tax = $this->custom_taxonomies;
    		$psp_all_tax = array_merge((array)$builtin_tax, (array)$custom_tax);
			
			if(!empty($settings['taxonomies_list'])) {
				if (count($settings['taxonomies_list']) != count(array_intersect($settings['taxonomies_list'], $psp_all_tax))) {
					$settings['taxonomies_list'] = array();
				}
			}
			
    	}		
		
		if ( isset( $settings['posttypes_list'] ) ) {
    		
			$settings['posttypes_list'] = array_map( 'sanitize_text_field', $settings['posttypes_list'] );
    		
    		$builtin_post_types = array("post", "page");
			$custom_post_types = array();
    		$psp_all_post_types = array();
			$custom_post_types = $this->custom_post_types;    		
    		
    		$psp_all_post_types = array_merge((array)$builtin_post_types, (array)$custom_post_types);
			
			if(!empty($settings['posttypes_list'])) {
				if (count($settings['posttypes_list']) != count(array_intersect($settings['posttypes_list'], $psp_all_post_types))) {
					$settings['posttypes_list'] = array();
				}
			}
			
    	}		
		
		//if( !empty( $settings['excluded_term_ids'] ) ) $settings['excluded_term_ids'] = sanitize_text_field( $settings['excluded_term_ids'] );
		$excluded_term_ids = array();
		$excluded_term_ids = !empty( $settings['excluded_term_ids'] ) ? explode(",", $settings['excluded_term_ids']) : '';
		if( !empty( $excluded_term_ids ) ) {
		    $excluded_term_ids = array_filter(  $excluded_term_ids, 'absint' ); 
    	}
		$settings['excluded_term_ids'] = !empty( $excluded_term_ids) ? implode(",", $excluded_term_ids) : ''; 		
		     
		//if( !empty( $settings['excluded_post_ids'] ) ) $settings['excluded_post_ids'] = sanitize_text_field( $settings['excluded_post_ids'] );
		$excluded_post_ids = array();
		$excluded_post_ids = !empty( $settings['excluded_post_ids'] ) ? explode(",", $settings['excluded_post_ids']) : '';
		if( !empty( $excluded_post_ids ) ) {
		    $excluded_post_ids = array_filter(  $excluded_post_ids, 'absint' ); 
	    }
		
		$settings['excluded_post_ids'] = !empty( $excluded_post_ids) ? implode(",", $excluded_post_ids) : ''; 	
		
		return $settings;
	}	
	
	function psp_add_field_text_dd(array $args) {
	
		$option_name   = isset($args['option_name']) ? $args['option_name'] : '';
		$id     = isset($args['label_for']) ? $args['label_for'] : '';
		$option_value     = isset($args['option_value']) ? $args['option_value'] : '';
		$option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		$option_button     = isset($args['button']) ?  esc_attr($args['button']) : '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
		
		echo "<input style='width:10%;' type='number' min='0' maxlength='2' name='".esc_attr($option_name)."' id='".esc_attr($id)."' value='".esc_url($option_value)."'><br/><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
	}

	/*
	 * Callback for adding a checkbox.
	 */
	function psp_add_field_checkbox(array $args) {
	
		$option_name   = isset($args['option_name']) ? esc_attr($args['option_name']) : '';
		$id     = isset($args['label_for']) ? esc_attr($args['label_for']) : '';
		$option_value     = isset($args['option_value']) ? esc_attr( $args['option_value'] ) : '';
		//$option_value     = esc_attr( $args['option_value'] );
		$checkbox_on_value  = isset($args['checkbox_on_value']) ? esc_html($args['checkbox_on_value']) : '';
		$checkbox_off_value  = isset($args['checkbox_off_value']) ? esc_html($args['checkbox_off_value']) : '';
		$checkbox_label     = isset($args['checkbox_label']) ? esc_html($args['checkbox_label']) : '';
		$option_description  = isset($args['option_description']) ?  esc_html($args['option_description'])  : '';		
		$checked = '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
		if($option_value) { $checked = ' checked="checked" '; }
		echo "<input ".esc_attr($checked)." id='".esc_attr($id)."' name='".esc_attr($option_name)."' type='checkbox' data-toggle='toggle' data-on='".$checkbox_on_value."' data-off='".$checkbox_off_value."'/><span>&nbsp;</span><span for='".esc_attr($id)."'>".wp_kses(html_entity_decode($checkbox_label), $desc_allowed_html)."</span><br /><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";	
			
	}
	
	/*
	 * Callback for adding multiple checkboxes.
	 */
	function psp_add_multiple_checkbox(array $args) {
	
		$option_name   = isset($args['option_name']) ? esc_attr($args['option_name']) : '';
		$id     = isset($args['label_for']) ? esc_attr($args['label_for']) : '';
		$option_array_value     = isset($args['option_value']) ?  (array) $args['option_value']  : array();
		$option_array_value = array_map( 'esc_attr', $option_array_value );		
		$checkboxitems = isset($args['checkboxitems']) ? $args['checkboxitems'] : array();//array
		$option_description     = isset($args['option_description']) ? esc_attr( $args['option_description'] ) : '';
		
		$counter = 1;

		//echo "<div id='$id' class='psp-separator'>";
		echo "<div id='$id'>";
		
		foreach ( $checkboxitems as $checkboxitemkey => $checkboxitemvalue ) {
		
			$checkbox_id = esc_attr($id."-cbx-item-".$counter);
			$checked = in_array($checkboxitemkey, $option_array_value) ? 'checked="checked"' : '';
			//echo "<input id='$checkbox_id' $checked type='checkbox' name='$option_name' value='$checkboxitem' /><label class='psp-radio-separator' for='$radio_id'>$checkboxitemvalue</label>";
			echo "<input ".$checked." id='$checkbox_id' name='$option_name' value='".esc_attr($checkboxitemkey)."' type='checkbox' data-toggle='toggle' /><span>&nbsp;</span><span for='$id'>".esc_attr($checkboxitemvalue)."</span><br />";
		
			$counter = $counter + 1;
		
		}
		
		echo "</div><br /><p class='description'>".esc_html($option_description)."</p>";
		
	}	
	
	/*
	 * renders Plugin settings page, checks
	 * for the active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function psp_sm_options_page() {
		$tab = isset( $_GET['pspstab'] ) ? Sanitize_key($_GET['pspstab']) : $this->psp_sm_settings_group;
		
		$psp_pre_settings = get_option('psp_pre_setting');		
		$psp_premium_valid = isset($psp_pre_settings['psp_premium_license_key_status']) ? $psp_pre_settings['psp_premium_license_key_status'] : '';
		//$psp_premium_status = isset($psp_pre_settings['psp_premium_license_key_status']) ? $psp_pre_settings['psp_premium_license_key_status'] : '';
			
		if ($tab == $this->psp_sm_settings_group) {
			$psp_button = "submitsmsettings";
			$psp_nonce_field = "psp-sm-settings";
		} 	
		?>
		<div class="wrap">		
			<h1 style='line-height:30px;'><?php esc_html_e('Techblissonline Platinum SEO Pack - 
			SiteMaps', 'platinum-seo-pack') ?></h1>
			<p style="color: red"><?php esc_html_e('You need to click the "Save Settings" button to save the changes you made to each individual tab before moving on to the next tab.', 'platinum-seo-pack') ?></p>		
			<?php $this->psp_sm_options_tabs(); ?>
			<?php if (($tab == $this->psp_sm_settings_group) ) { ?>
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
				<a class="bookmarkme" href="<?php echo 'https://techblissonline.com/tools/'; ?>" target="_blank"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-logo.png'; ?>" class="img-responsive" alt="Techblissonline Platinum SEO Wordpress Tools"/></a>
			</h2>
			    <div class="container bg-info" id="tools" style="width:100%">
                    <div class="row"><div class="h3 col-sm-12"><a class="btn-primary col-sm-12" href="https://techblissonline.com/tools/platinum-seo-wordpress-premium/" target="_blank">Platinum SEO Premium for wordpress</a></div><div class="h3 col-sm-12"><a class="btn-success col-sm-12" href="https://techblissonline.com/tools/" target="_blank">Techblissonline Platinum SEO Audit and Analysis Tools</a></div></div> 
                </div>
				<a href="https://techblissonline.com/tools/" target="_blank">Be our Patreon and enjoy these premium Wordpress SEO tools for just $9</a>
				<div class="container" style="width:100%"><a href="https://techblissonline.com/tools/" target="_blank"><span class="col-sm-10 dashicons dashicons-thumbs-up dashicons-psp"></span></a></div>
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
	function psp_sm_options_tabs() {
		$current_tab = isset( $_GET['pspstab'] ) ? Sanitize_key($_GET['pspstab']) : $this->psp_sm_settings_group;	
		wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
		//screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->psp_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr($active) . '" href="?page=' . esc_attr($this->psp_plugin_options_key) . '&pspstab=' . esc_attr($tab_key) . '">' . esc_attr($tab_caption) . '</a>';	
		}
		echo '</h2>';
	}
}