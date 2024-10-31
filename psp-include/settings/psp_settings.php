<?php

/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: Complete SEO and Social optimization solution for your Wordpress blog/site.
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspSettings {	
	 
	private static $obj_handle = null;	
	
	public $custom_taxonomies = array();
	public $custom_post_types = array();
	public $psp_bc_tax_term_items = array();
	public $psp_wmt_instance;
	public $psp_tools_instance;
	public $psp_pre_instance;
	public $psp_rcap_instance;
	public $psp_redirect_instance;
	public $psp_social_instance;
	public $psp_import_instance;
	public $psp_sitemap_instance;
	public $psp_wizard_instance;
	
	private $psp_helper;	
	private $sitename;
	private $sitedescription;	
	 
	private $psp_general_settings_group = 'psp_general';
	private $psp_home_settings_group = 'psp_home';
	private $psp_taxonomy_settings_group = 'psp_taxonomy';
	private $psp_posttype_settings_group = 'psp_pt';	
	private $psp_archives_settings_group = 'psp_archive';
	private $psp_permalink_settings_group = 'psp_permalink';
	private $psp_social_settings_group = 'psp_social';
	private $psp_other_settings_group = 'psp_others';
	private $psp_breadcrumb_settings_group = 'psp_breadcrumb';
	private $psp_sm_settings_group = 'psp_sitemap';
	
	protected $psp_plugin_options_key = 'platinum-seo-social-pack-by-techblissonline';
	private $psp_settings_tabs = array();
	
	private $psp_post_meta_original = array();
	private $psp_post_social_meta_original = array();
	
	private $psp_taxonomy_meta_original = array();
	private $psp_taxonomy_social_meta_original = array();
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;
	
	
	function __construct() {
		//add_action( 'init', array( &$this, 'load_settings' ) );
		//$psp_taxonomy_class = PSP_Tax_Seo_Metas::get_handle();
		//$this->custom_taxonomies = $psp_taxonomy_class->get_cust_taxonomies();
		
		// Add a query variable for redirecting categories with "category" base.
		//add_filter('query_vars', array( &$this, 'psp_set_category_base_redir_var'));
		// Redirect if 'techblissonline_psp_category_redirect' is set
		//add_filter('request', array( &$this, 'psp_redirect_category_base_request'));
		//
		//add_action('init', array( &$this, 'psp_set_no_base_extra_permastruct'));
		
		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;
		
		//$psp_wmt_instance = PspWebMasterToolSettings::get_instance();		
		//$this->psp_wmt_instance = $psp_wmt_instance; 
		
		$psp_pre_instance = PspPreSettings::get_instance();	
		$this->psp_pre_instance = $psp_pre_instance;
		
		$psp_rcap_instance = PspRcapSettings::get_instance();		
		$this->psp_rcap_instance = $psp_rcap_instance;
		
		$psp_tools_instance = PspToolSettings::get_instance();		
		$this->psp_tools_instance = $psp_tools_instance;
		
		$psp_social_instance = PspSocialSettings::get_instance();		
		$this->psp_social_instance = $psp_social_instance;
		
		$psp_redirect_instance = PspRedirections::get_instance();		
		$this->psp_redirect_instance = $psp_redirect_instance;  
		$this->sitename = $psp_helper_instance->get_sitename();
		
		$this->psp_import_instance = PspImporter::get_instance();		
		
		$this->psp_sitemap_instance = PspSmSettings::get_instance();
		
		//$this->psp_wizard_instance = PspWizard::get_instance();
		$this->psp_wizard_instance = PspWizardSettings::get_instance();
		$this->psp_wizard_instance->psp_settings_instance = $this;
		
		
		$psp_do_rewrite_rules = false;
		
		$cust_taxonomies = array();
				
		if ( null == $this->custom_taxonomies ) {
			$args = array(
							'public'   => true,
							'_builtin' => false		  
						); 			
			$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$cust_taxonomies = get_taxonomies( $args, $output, $operator );
			$this->custom_taxonomies = $cust_taxonomies;
		}	
        /**
		$psp_permalink_settings = get_option('psp_permalink_settings');
		
		if (isset($psp_permalink_settings['category']) && $psp_permalink_settings['category']) {
			$psp_do_rewrite_rules = true;
			add_filter( 'category_rewrite_rules', array( &$this, 'psp_category_rewrite_rules' )); //add rewrite rules
		}			
		
		foreach($cust_taxonomies as $cust_taxonomy) {
		    
			if (isset($psp_permalink_settings[$cust_taxonomy]) && $psp_permalink_settings[$cust_taxonomy]) {
				$psp_filter = $cust_taxonomy."_rewrite_rules";
				
				add_filter( $psp_filter, array( &$this, 'psp_category_rewrite_rules' )); //add rewrite rules
				$psp_do_rewrite_rules = true;
			}
		}**/
		
		//if ($psp_do_rewrite_rules) {
		
			// Add a query variable for redirecting categories with "category" base.
			add_filter('query_vars', array( &$this, 'psp_set_category_base_redir_var'));
			//Redirect if 'techblissonline_psp_category_redirect' is set
			add_filter('request', array( &$this, 'psp_redirect_category_base_request'));
			//
			add_action('created_category', array( &$this, 'psp_refresh_rewrite_rules'));
			add_action('edited_category', array( &$this, 'psp_refresh_rewrite_rules'));
			add_action('delete_category', array( &$this, 'psp_refresh_rewrite_rules'));
			//
			add_action('init', array( &$this, 'psp_set_no_base_extra_permastruct'));
		//}
				
		//$this->rewrite_rules_hooks_init();
		
		//$this->custom_post_types = get_post_types( array ( '_builtin' => FALSE ) );
		
		add_action( 'admin_init', array( &$this, 'psp_admin_settings_init' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ), 9 );	
		
		//Rajesh - 22/06/2017
		$psp_settings = get_option('psp_pre_setting');		
		$psp_premium_valid = isset($psp_settings['premium']) ? $psp_settings['premium'] : '';
		$psp_premium_status = isset($psp_settings['psp_premium_license_key_status']) ? $psp_settings['psp_premium_license_key_status'] : '';
		
		$this->psp_settings_tabs[$this->psp_general_settings_group] = 'General';
		$this->psp_settings_tabs[$this->psp_home_settings_group] = 'Home';
		$this->psp_settings_tabs[$this->psp_taxonomy_settings_group] = 'Taxonomies';
		$this->psp_settings_tabs[$this->psp_posttype_settings_group] = 'Post Types';		
		//$this->psp_settings_tabs[$this->psp_home_settings_group] = 'Home';
		$this->psp_settings_tabs[$this->psp_archives_settings_group] = 'Archives';	
		$this->psp_settings_tabs[$this->psp_permalink_settings_group] = 'Permalinks';
		$this->psp_settings_tabs[$this->psp_sm_settings_group] = 'Sitemap';
		//if (!$psp_premium_status) $this->psp_settings_tabs[$this->psp_social_settings_group] = 'Social';
		$this->psp_settings_tabs[$this->psp_other_settings_group] = 'Others';
		$this->psp_settings_tabs[$this->psp_breadcrumb_settings_group] = 'Breadcrumbs';
		//error_log(print_r($this->psp_settings_tabs, true));
		foreach ($this->psp_settings_tabs as $psp_group => $psp_group_name) {
		    //Allow psp_capability
		    add_filter( 'option_page_capability_'.$psp_group, function( $capability ){
		        //error_log("psp const cap ".'psp_capability');
                return 'edit_posts';
            } );
		}
		
		
		//add extra fields to category edit form hook
		//add_action ( 'edit_category_form_fields', array( &$this, 'psp_extra_category_fields' ));
		
		// save extra category extra fields hook		
		//add_action ( 'edited_category', array( &$this, 'psp_save_extra_category_fields' ));	
		
	}
	
	/*
	* Check user access capability
	*/
	protected function user_has_access ( $tab ) {
	    
	    if ( is_super_admin() ) return true;

    	$psp_rcap_settings = get_option("psp_rolecap");
    
    	$currentuser_roles = array();
    	$capabilities = array();
    
    	if( is_user_logged_in() ) {
    		$user = wp_get_current_user();
    		$currentuser_roles = ( array ) $user->roles;
    		//error_log (print_r($currentuser_roles, true));
    	} else {
    		return false;
    	}
    
    	if ( !empty( $currentuser_roles ) ) {
    
    		foreach ( $currentuser_roles as $role ) {
    		
    			if ( $role === "administrator" ) return true;
    		
    			if ( !empty ($psp_rcap_settings[$role]) ) {				
    				$capabilities = $psp_rcap_settings[$role];
    				
    				if ( ! in_array($tab, $capabilities) ) {
    						return false;
    				} else {
    					return true;
    				}	
    				
    			} else {
    				return false;
    			}
    		
    		}
    	} else {
    		return false;
    	}
	}
	
	/*
	 * Registers settings 	
	 */
	function psp_admin_settings_init() {	

		if ( null == $this->custom_taxonomies ) {
			$args = array(
							'public'   => true,
							'_builtin' => false		  
						); 			
			$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$cust_taxonomies = get_taxonomies( $args, $output, $operator );
			$this->custom_taxonomies = $cust_taxonomies;
		}
		//add filter to modify post meta values 
		add_filter( 'wp_insert_post_data', array( &$this, 'psp_slash' ), -99, 2 );
		
		//add_action( 'wp_enqueue_scripts', 'add_thickbox', -100 );

		//add psp seo metabox to post types
		add_action( 'add_meta_boxes', array( &$this, 'do_psp_meta_boxes' ), -99, 2 );
		//save psp seo metabox data
		add_action( 'save_post', array( &$this, 'psp_save_seo_meta_box_data' ) );
		//add_action( 'edit_post', array( &$this, 'psp_save_seo_meta_box_data' ) );
		//add_action( 'publish_post', array( &$this, 'psp_save_seo_meta_box_data' ) );
		//add_action( 'edit_page_form', array( &$this, 'psp_save_seo_meta_box_data' ) );
		
		//initilize rewrite rules filters
		$this->rewrite_rules_hooks_init();		
		
		$tab = isset( $_GET['psptab'] ) ? sanitize_key($_GET['psptab']) : $this->psp_general_settings_group;
		
		global $pagenow;
		 $psp_pages = array('platinum-seo-social-pack-by-techblissonline', 'psp-social-by-techblissonline', 'psp-tools-by-techblissonline', 'pspp-licenses');
		if ( $pagenow == 'admin.php' && in_array(sanitize_key($_GET['page']), $psp_pages))  {
		
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
		
		}
	
		//$this->register_general_settings();
		$this->register_general_settings('sitewide');
		$this->register_home_settings();
		$this->register_breadcrumb_settings();
		
		$this->register_taxonomy_settings('category');		
		
		//add extra fields to category edit form hook
		add_action ( 'category_edit_form_fields', array( &$this, 'psp_extra_category_fields' ));
		
		// save extra category extra fields hook		
		add_action ( 'edited_category', array( &$this, 'psp_save_extra_category_fields' ));
		
		$this->register_taxonomy_settings('tag');
		//$this->register_taxonomy_settings('post_format');
		
		//add extra fields to tag edit form hook
		$tag_action_name = 'edit_tag_form_fields';
		add_action ( 'edit_tag_form_fields', array( &$this, 'psp_extra_taxonomy_fields' ));
		
		//add_action ( 'edited_category', 'psp_save_extra_category_fields');
		$tag_action_name = 'edited_tag';
		add_action ( $tag_action_name, array( &$this, 'psp_save_extra_taxonomy_fields' ));
		
		$cust_taxonomies = $this->custom_taxonomies;
		
		foreach($cust_taxonomies as $cust_taxonomy) {
			//register taxonomies
			$this->register_taxonomy_settings($cust_taxonomy);
			/***this part looks to be tiggered elsewhere****
			//add extra fields to taxonomy edit form hook
			//$taxonomy_action_name = $cust_taxonomy.'_edit_form_fields';
			//add_action ( $taxonomy_action_name, array( &$this, 'psp_extra_taxonomy_fields' ));
		
			// save extra taxonomy extra fields hook
			//add_action ( 'edited_category', 'psp_save_extra_category_fields');
			//$taxonomy_action_name = 'edited_'.$cust_taxonomy;
			//add_action ( $taxonomy_action_name, array( &$this, 'psp_save_extra_taxonomy_fields' ));
			/***looks to be tiggered elsewhere****/
			$taxonomy_action_name = $cust_taxonomy.'_edit_form_fields';
			//add_action ( $taxonomy_action_name, array( &$this, 'psp_extra_taxonomy_fields' ));
			$taxonomy_action_name = 'edited_'.$cust_taxonomy;
			add_action ( $taxonomy_action_name, array( &$this, 'psp_save_extra_taxonomy_fields' ));
		}
		
		$this->register_posttype_settings('post');
		$this->register_posttype_settings('page');
		$this->register_posttype_settings('attachment');	

		//$this->custom_post_types = get_post_types( array ( '_builtin' => FALSE ) );		
		//$cust_post_types = $this->custom_post_types;
		
		$cust_post_types = get_post_types( array ( '_builtin' => FALSE ) );	
		$this->custom_post_types = $cust_post_types;
		
		foreach($cust_post_types as $cust_post_type) {
			$this->register_posttype_settings($cust_post_type);
		}
		
		$this->psp_sitemap_instance->register_sm_settings();	
		
		//if ($tab == $this->psp_archives_settings_group) {
			$this->register_archive_settings('date_archive');
			$this->register_archive_settings('author_archive');
			$this->register_archive_settings('posttype_archive');
			
			$this->register_other_settings('search_result');
			$this->register_other_settings('404_page');	
		//}
		
		//if ($tab == $this->psp_permalink_settings_group) {
			$this->register_permalink_settings();	
		//}			
		//Rajesh - 22/06/2017
		$psp_settings = get_option('psp_pre_setting');		
		$psp_premium_valid = isset($psp_settings['premium']) ? $psp_settings['premium'] : '';
		$psp_premium_status = isset($psp_settings['psp_premium_license_key_status']) ? $psp_settings['psp_premium_license_key_status'] : '';
		
		
		//$psp_premium_valid = 1;
		//$psp_premium_status = 1;
		//if ($tab == $this->psp_social_settings_group) {
		//if(!$psp_premium_status)	$this->register_social_settings();
		//}
		
			$this->register_kg_settings();
		
	}
	/*
	* Initialize all hooks for rewrite rules
	*/
	function rewrite_rules_hooks_init() {
		
		$psp_do_rewrite_rules = false;
		
		$psp_permalink_settings = get_option('psp_permalink_settings');
		
		if (isset($psp_permalink_settings['category']) && $psp_permalink_settings['category']) {
			$psp_do_rewrite_rules = true;
			add_filter( 'category_rewrite_rules', array( &$this, 'psp_category_rewrite_rules' )); //add rewrite rules
		}	
		
		$cust_taxonomies = $this->custom_taxonomies;
		
		
		foreach($cust_taxonomies as $cust_taxonomy) {
		     
			//$psp_settings_name = "psp_".$cust_taxonomy."_settings";		
			//$psp_tax_settings = get_option($psp_settings_name);
			
			if (isset($psp_permalink_settings[$cust_taxonomy]) && $psp_permalink_settings[$cust_taxonomy]) {
				$psp_filter = $cust_taxonomy."_rewrite_rules";
				add_filter( $psp_filter, array( &$this, 'psp_category_rewrite_rules' )); //add rewrite rules
				$psp_do_rewrite_rules = true;
			}
		}
		
		if ($psp_do_rewrite_rules) {
		
			// Add a query variable for redirecting categories with "category" base.
			//add_filter('query_vars', array( &$this, 'psp_set_category_base_redir_var'));
			// Redirect if 'techblissonline_psp_category_redirect' is set
			//add_filter('request', array( &$this, 'psp_redirect_category_base_request'));
			//
			add_action('created_category', array( &$this, 'psp_refresh_rewrite_rules'));
			add_action('edited_category', array( &$this, 'psp_refresh_rewrite_rules'));
			add_action('delete_category', array( &$this, 'psp_refresh_rewrite_rules'));
			//
			//add_action('init', array( &$this, 'psp_set_no_base_extra_permastruct'));
		}
	
	}	
	
	/*
	 * Registers the Home SEO settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_home_settings() {
		$this->psp_settings_tabs[$this->psp_home_settings_group] = 'Home';		
		$psp_home_settings_name = "psp_home_settings";
		
		$psp_home_settings = get_option($psp_home_settings_name);
		
		
		//$this->psp_home_settings = $psp_home_settings;
		//register
		register_setting( $this->psp_home_settings_group, $psp_home_settings_name, array( &$this, 'sanitize_home_settings' ) );
		
		//add Section
		add_settings_section( 'psp_section_home', esc_html__('Home Page SEO Settings', 'platinum-seo-pack' ), array( &$this, 'section_home_desc' ), $this->psp_home_settings_group );
		//add fields
		//canonical
		$use_front_page_field     = array (
            'label_for' 	=> 'psp_home_use_front_page',
            'option_name'   => $psp_home_settings_name.'[use_front_page]',
			'option_value'  => isset($psp_home_settings['use_front_page']) ? $psp_home_settings['use_front_page'] : '',
			'option_description'  => esc_html__( 'You don\'t have to enable this even if you use a Static Page as Home Page, unless you need it for other reasons. If you enable this, the Platinum SEO Attributes (SEO title, Description, Schema etc.) of the Page chose as Static Front Page will be used and the Home Page settings you enter below will be overridden (ignored). If this option is selected, you will have to make sure that the Canonical URL is correctly set for the page chosen as the Static page For Home. If needed, make sure that you set it correctly in the Platinum SEO Metabox of the page chosen as static Page.', 'platinum-seo-pack' ),
			'checkbox_label' => esc_html__( 'Use the Static  Front page\'s Platinum SEO Attributes', 'platinum-seo-pack' )
        );			
			
		$use_front_page_field_id = 'psp_home_use_front_page';		
		$use_front_page_field_title = esc_html__( 'Use the Static  Front page\'s Platinum SEO Attributes: ', 'platinum-seo-pack' );	
		
		//add_settings_field( $use_front_page_field_id, $use_front_page_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_home_settings_group, $section_id, $use_front_page_field );
		
		//Home page title
		$title_field     = array (
            'label_for' 	=> 'psp_home_title',
            'option_name'   => $psp_home_settings_name.'[title]',
			'option_value'  => isset($psp_home_settings['title']) ? stripcslashes(esc_attr($psp_home_settings['title'])) : '',
			'option_description'  => esc_html__( 'Enter a title in plain text. Do not use any tags.', 'platinum-seo-pack' )
        );
		
		$desc_field     = array (
            'label_for' 	=> 'psp_home_description',
            'option_name'   => $psp_home_settings_name.'[description]',
			'option_value'  => isset($psp_home_settings['description']) ? stripcslashes(esc_attr($psp_home_settings['description'])) : '',
			'option_description'  => esc_html__( 'Enter a meta description in plain text. Do not use any tags.', 'platinum-seo-pack' )
        );

		$keywords_field     = array (
            'label_for' 	=> 'psp_home_keywords',
            'option_name'   => $psp_home_settings_name.'[keywords]',
			'option_value'  => isset($psp_home_settings['keywords']) ? stripcslashes(esc_attr($psp_home_settings['keywords'])) : '',
			'option_description'  => esc_html__( 'Google and most other Search engines do not use or support the meta keywords tag. If you still find it necessary to use this tag for any specific reason of yours, you may add comma separated primary entities or keywords for the Home page here.These will be displayed as meta keywords tag. Leaving it empty will disable this tag for the Home Page.', 'platinum-seo-pack' ),
        );
        
        $home_header_metas = isset($psp_home_settings['headers']) ? html_entity_decode(stripcslashes(esc_attr($psp_home_settings['headers']))) : '';
        //validate headers
		if( !empty( $home_header_metas ) ) {
    	
    		$allowed_html = array(
    			'meta' => array(
    				'name' => array(),
    				'property' => array(),
    				'itemprop' => array(),
    				'content' => array(),
    			),    
    		);
    
    		$home_header_metas = wp_kses($home_header_metas, $allowed_html);
		}
		
		$additional_headers_field     = array (
            'label_for' 	=> 'psp_home_additional_headers',
            'option_name'   => $psp_home_settings_name.'[headers]',
			'option_value'  => $home_header_metas,
			'option_description'  => esc_html__( 'Here you may add all the webmaster tools verification meta tag codes for google, bing, yandex, alexa and for any other search engine.If you had already verified with the webmaster tools, you might choose to ignore adding them here. Check ', 'platinum-seo-pack' ).' <br> <a href="https://www.google.com/webmasters/verification/verification?hl=en&siteUrl='.trailingslashit(get_home_url()).'" target="_blank">Google Webmaster Tools</a><br> <a href="http://www.bing.com/webmaster/?rfp=1#/Dashboard/?url='.substr(get_home_url(), 8).'" target="_blank">Bing Webmaster Tools</a>;',
			'parent_classname'  => 'pspeditor',
        );
        
        $json_schema_string = isset($psp_home_settings['schema']) ? html_entity_decode(stripcslashes(esc_attr($psp_home_settings['schema']))) : '';
        //validate it is a json object
		$schema_obj = json_decode($json_schema_string);
		if($schema_obj === null) {
		    $json_schema_string = 'Invalid JSON Schema';
		}
        
        $schema_field     = array (
            'label_for' 	=> 'psp_home_schemas',
            'option_name'   => $psp_home_settings_name.'[schema]',
			'option_value'  =>  $json_schema_string,
			'option_description'  => esc_html__( 'Here you may add all the JSON Schemas for the Home page', 'platinum-seo-pack' ),
			'parent_classname'  => 'pspeditor',
        );
		
		//add_settings_field( 'psp_home_title', '<a href="'.home_url().'" target="_blank">'.__('Home Page Title: ', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_text' ), $this->psp_home_settings_group, 'psp_section_home',  $title_field);
		//add_settings_field( 'psp_home_description', '<a href="'.home_url().'" target="_blank">'.__('Home Page Meta Description: ', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_textarea' ), $this->psp_home_settings_group, 'psp_section_home', $desc_field );
		//add_settings_field( 'psp_home_keywords', '<a href="'.home_url().'" target="_blank">'.__('Home Page Meta Keywords: ', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_text' ), $this->psp_home_settings_group, 'psp_section_home', $keywords_field );
		//add_settings_field( 'psp_home_additional_headers', '<a href="'.home_url().'" target="_blank">'.__('Additional Home Page Headers: ', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_textarea' ), $this->psp_home_settings_group, 'psp_section_home', $additional_headers_field );
		add_settings_field( $use_front_page_field_id, $use_front_page_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_home_settings_group, 'psp_section_home', $use_front_page_field );
		add_settings_field( 'psp_home_title', esc_html__('Home Page Title: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_text' ), $this->psp_home_settings_group, 'psp_section_home',  $title_field);
		add_settings_field( 'psp_home_description', esc_html__('Home Page Meta Description: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_textarea' ), $this->psp_home_settings_group, 'psp_section_home', $desc_field );
		add_settings_field( 'psp_home_keywords', esc_html__('Home Page Meta Keywords: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_text' ), $this->psp_home_settings_group, 'psp_section_home', $keywords_field );
		add_settings_field( 'psp_home_additional_headers', esc_html__('Additional Home Page Headers: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_textarea' ), $this->psp_home_settings_group, 'psp_section_home', $additional_headers_field );
		add_settings_field( 'psp_home_schemas', esc_html__('Schemas >> ', 'platinum-seo-pack').'<a href="https://techblissonline.com/tools/schema-markup-generator/" target="_blank">'.esc_html__('Generate here', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_textarea' ), $this->psp_home_settings_group, 'psp_section_home', $schema_field );
	}
	
	function sanitize_home_settings($settings) {
	    
	    if( ! empty( $settings['wizard'] ) ) {
			
			return $settings;
			
		}
		
		if( isset( $settings['use_front_page'] ) ) {
			$settings['use_front_page'] = !is_null(filter_var($settings['use_front_page'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_front_page'] : '';
		}
		if( isset( $settings['title'] ) ) $settings['title'] = sanitize_text_field( $settings['title'] );
		if( isset( $settings['description'] ) ) $settings['description'] = sanitize_textarea_field( $settings['description'] );
		if( isset( $settings['keywords'] ) ) $settings['keywords'] = sanitize_text_field( $settings['keywords'] );
		//validate headers
		if( isset( $settings['headers'] ) ) {
		
			$allowed_html = array(
				'meta' => array(
					'name' => array(),
					'property' => array(),
					'itemprop' => array(),
					'content' => array(),
				),    
			);

			$settings['headers'] = wp_kses($settings['headers'], $allowed_html);
			$settings['headers'] = sanitize_textarea_field( htmlentities($settings['headers']) );
		};
		
    	if ( isset( $settings['schema'] ) ) {
    			
        	$json_schema_str = ( $settings['schema'] );	
			$json_schema_str = strip_tags($json_schema_str, '<br>');
        	if ( "[" !== mb_substr($json_schema_str, 0, 1) ) {
        	    	$json_schema_str = "[".	$json_schema_str."]";
        	}	
        	$schema_obj = json_decode(stripcslashes($json_schema_str));
        	//validate it is a json object
        	if($schema_obj === null) {
        		// $schema_obj is null because the json cannot be decoded
        		$settings['schema'] = '';                 
        	} else {
        		$settings['schema'] = sanitize_textarea_field( htmlentities($json_schema_str) );
        	   
        	}
        }
		
		return $settings;
	}
	
	/*
	 * Registers the general settings and appends the
	 * key to the plugin settings tabs array. -$others_name = sitewide_meta
	 */
	private function register_general_settings($setting_name) {
		$this->psp_settings_tabs[$this->psp_general_settings_group] = 'General';		
		$psp_settings_name = "psp_".$setting_name."_settings";
		$setting_name_text = str_replace( "_", " ", $setting_name );
		$setting_name_text = ucwords($setting_name_text);
		
		$psp_settings = get_option($psp_settings_name);
		//$this->psp_settings_name = $psp_settings;	
		
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
		
		register_setting( $this->psp_general_settings_group, $psp_settings_name, array( &$this, 'sanitize_general_settings' ) );
		
		//Section
		$section_id = 'psp_separator_section';		
		$section_title =  esc_html__( 'Sitewide Title Settings', 'platinum-seo-pack' );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_separator_desc' ), $this->psp_general_settings_group );
		
		//field				
		
		//Separator (Can be used in title and Description formats by specifying %sep%)		
		//$psp_separators = array ('&dash;', '&ndash;', '&mdash;', '&middot;', '&bull;', '*', '&#8902;', '|', '~', '&laquo;', '&raquo;', '&lt;', '&gt;'); &sstarf; &hyphen; &dash;
		//$psp_separators = array ('' => 'None', '-' => '-', '&ndash;' => '&ndash;', '&mdash;' => '&mdash;', '&middot;' => '&middot;', '&bull;' => '&bull;', '*' => '*', '|' => '|', '~' => '~', '&laquo;' => '&laquo;', '&raquo;' => '&raquo;', '&lt;' => '&lt;', '&gt;' => '&gt;', '&tilde;' => '&tilde;', '&hearts;' => '&hearts;', '&clubs;' => '&clubs;');
		$psp_separators = array ('' => 'None', '-' => '-', '&ndash;' => '&ndash;', '&mdash;' => '&mdash;', '&middot;' => '&middot;', '&bull;' => '&bull;', '*' => '*', '|' => '|', '~' => '~', '&laquo;' => '&laquo;', '&raquo;' => '&raquo;', '&lt;' => '&lt;', '&gt;' => '&gt;', '&tilde;' => '&tilde;', '&hearts;' => '&hearts;', '&clubs;' => '&clubs;', ':' => ':', '★' => '★');
		
		$psp_separator_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_separator',
            'option_name'   => $psp_settings_name.'[separator]',
			'option_value'  => isset($psp_settings['separator']) ? $psp_settings['separator'] : '',
			'radioitems' => $psp_separators,
			'option_description' => esc_html__( ' Can be used in title and description formats by specifying ', 'platinum-seo-pack' ). '<code>%sep%</code>.', 
        );	        		
			
		$psp_separator_field_id = 'psp_'.$setting_name.'_separator';		
		$psp_separator_field_title = 'Title Separator: ';	
		
		add_settings_field( $psp_separator_field_id, $psp_separator_field_title, array( &$this, 'psp_add_field_radiobuttons' ), $this->psp_general_settings_group, $section_id, $psp_separator_field );
		
		//rewrite titles
		$rewrite_titles_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_rewrite_titles',
            'option_name'   => $psp_settings_name.'[rewrite_titles]',
			'option_value'  => isset($psp_settings['rewrite_titles']) ? $psp_settings['rewrite_titles'] : '',
			'checkbox_label' => esc_html__( 'Do rewrite titles using Platinum SEO', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should always remain checked if you want to rewrite titles using Platinum SEO', 'platinum-seo-pack' ),
        );			
			
		$rewrite_titles_field_id = 'psp_'.$setting_name.'_rewrite_titles';		
		$rewrite_titles_field_title = esc_html__( 'Use title rewriter: ', 'platinum-seo-pack' );	
		
		add_settings_field( $rewrite_titles_field_id, $rewrite_titles_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $rewrite_titles_field );	

		//force rewrite titles
		$f_rewrite_titles_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_force_psp_titles',
            'option_name'   => $psp_settings_name.'[force_psp_titles]',
			'option_value'  => isset($psp_settings['force_psp_titles']) ? $psp_settings['force_psp_titles'] : '',
			'checkbox_label' => esc_html__( 'Force rewrite titles using Platinum SEO', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should remain unchecked by default and should only be checked if you have issues with Platinum SEO plugin Title rewriting, despite the above option "Use title rewriter" remaining checked.', 'platinum-seo-pack' ),
        );			
			
		$f_rewrite_titles_field_id = 'psp_'.$setting_name.'_force_psp_titles';		
		$f_rewrite_titles_field_title = esc_html__( 'Force Rewrite Title: ', 'platinum-seo-pack' );	
		
		add_settings_field( $f_rewrite_titles_field_id, $f_rewrite_titles_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $f_rewrite_titles_field );
		
		//paged title format
		$psp_paged_title_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_paged_title_format',
            'option_name'   => $psp_settings_name.'[paged_title_format]',
			'option_value'  => isset($psp_settings['paged_title_format']) ? esc_attr($psp_settings['paged_title_format']) : '',
			'option_description' => '<code>%page%</code>'.esc_html__( ' - Page number. "Page" is the pagination base and it can be changed to anything you want.', 'platinum-seo-pack' ),
        );
		
		$paged_title_field_id = 'psp_'.$setting_name.'_paged_title_format';	
		$paged_title_field_title = esc_html__( 'Paged title Format: ', 'platinum-seo-pack' );	
		
		add_settings_field( $paged_title_field_id, $paged_title_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_general_settings_group, $section_id,  $psp_paged_title_field);
		
		//Section
		$section_id = 'psp_'.$setting_name.'_section';
		//$section_title = $setting_name_text.' Settings';
		$section_title = sprintf( esc_html__( 'Other %s Settings', 'platinum-seo-pack' ), $setting_name_text );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_sitewidemeta_desc' ), $this->psp_general_settings_group );
		
		//Fields		
		
		//Enable platinum seo
		$platinum_seo_enable_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_enable_psp',
            'option_name'   => $psp_settings_name.'[enable_psp]',
			'option_value'  => isset($psp_settings['enable_psp']) ? $psp_settings['enable_psp'] : '',
			'checkbox_label' => '<code>Enable</code>',
			'option_description' => esc_html__( 'Switching this OFF will disable generation of Platinum SEO titles, meta tags (except structured data module i.e. JSON schema markups for all posts, pages, taxonomies and archives on the frontend. You can however use the Platinum SEO schema (Structured Data), redirections, 404 error detection modules and sitemaps, if the corresponding modules are enabled.', 'platinum-seo-pack' )
        );			
			
		$platinum_seo_enable_id = 'psp_'.$setting_name.'_enable_psp';		
		$platinum_seo_enable_title = esc_html__( 'Platinum SEO: ', 'platinum-seo-pack' );		
		
		add_settings_field( $platinum_seo_enable_id, $platinum_seo_enable_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $platinum_seo_enable_field );
		
		//Enable platinum seo structured data
		$psp_schema_enable_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_enable_schema',
            'option_name'   => $psp_settings_name.'[enable_schema]',
			'option_value'  => isset($psp_settings['enable_schema']) ? $psp_settings['enable_schema'] : '',
			'checkbox_label' => '<code>Enable</code>',
			'option_description' => esc_html__( 'Switching this ON will enable Platinum SEO structured data module i.e. JSON schema markups for all posts, pages, taxonomies and Home Page/Front Page.', 'platinum-seo-pack' )
        );			
			
		$psp_schema_enable_id = 'psp_'.$setting_name.'_enable_schema';		
		$psp_schema_enable_title = esc_html__( 'Platinum SEO Schema (Structured Data): ', 'platinum-seo-pack' );		
		
		add_settings_field( $psp_schema_enable_id, $psp_schema_enable_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $psp_schema_enable_field );
		
		//Noindex subpages
		$noindex_subpages_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_noindex_subpages',
            'option_name'   => $psp_settings_name.'[noindex_subpages]',
			'option_value'  => isset($psp_settings['noindex_subpages']) ? $psp_settings['noindex_subpages'] : '',
			'checkbox_label' => '<code>Noindex</code>',
        );			
			
		$noindex_subpages_field_id = 'psp_'.$setting_name.'_noindex_subpages';		
		$noindex_subpages_field_title = esc_html__( 'Subpages of Home, taxonomies and all archves: ', 'platinum-seo-pack' );	
		
		add_settings_field( $noindex_subpages_field_id, $noindex_subpages_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $noindex_subpages_field );
		
		//Noindex RSS feeds
		$noindex_rss_feeds_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_noindex_rss_feeds',
            'option_name'   => $psp_settings_name.'[noindex_rss_feeds]',
			'option_value'  => isset($psp_settings['noindex_rss_feeds']) ? $psp_settings['noindex_rss_feeds'] : '',
			'checkbox_label' => '<code>Noindex</code>',
        );			
			
		$noindex_rss_feeds_field_id = 'psp_'.$setting_name.'_noindex_rss_feeds';		
		$noindex_rss_feeds_field_title = esc_html__( 'All RSS Feeds: ', 'platinum-seo-pack' );		
		
		add_settings_field( $noindex_rss_feeds_field_id, $noindex_rss_feeds_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $noindex_rss_feeds_field );
		
		//Noindex comment pages
		$noindex_comment_pages_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_noindex_pt_comment_pages',
            'option_name'   => $psp_settings_name.'[noindex_pt_comment_pages]',
			'option_value'  => isset($psp_settings['noindex_pt_comment_pages']) ? $psp_settings['noindex_pt_comment_pages'] : '',
			'checkbox_label' => '<code>Noindex</code>',
        );			
			
		$noindex_comment_pages_field_id = 'psp_'.$setting_name.'_noindex_pt_comment_pages';		
		$noindex_comment_pages_field_title = esc_html__( 'Comment pages of all post types: ', 'platinum-seo-pack' );		
		
		add_settings_field( $noindex_comment_pages_field_id, $noindex_comment_pages_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $noindex_comment_pages_field );
		
		//Noindex post type sub pages created using next page quicktag
		$noindex_pt_paginations_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_noindex_pt_paginations',
            'option_name'   => $psp_settings_name.'[noindex_pt_paginations]',
			'option_value'  => isset($psp_settings['noindex_pt_paginations']) ? $psp_settings['noindex_pt_paginations'] : '',
			'checkbox_label' => '<code>Noindex</code>',
        );			
			
		$noindex_pt_paginations_field_id = 'psp_'.$setting_name.'_noindex_pt_paginations';		
		$noindex_pt_paginations_field_title = esc_html__( 'Subpages of all post types created using Next Page quicktag: ', 'platinum-seo-pack' );			
		
		add_settings_field( $noindex_pt_paginations_field_id, $noindex_pt_paginations_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $noindex_pt_paginations_field );
		
		//noodp
		$use_meta_noodp_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_use_meta_noodp',
            'option_name'   => $psp_settings_name.'[use_meta_noodp]',
			'option_value'  => isset($psp_settings['use_meta_noodp']) ? $psp_settings['use_meta_noodp'] : '',
			'checkbox_label' => esc_html__( 'Use meta robots tag ', 'platinum-seo-pack' ).'<code> noodp</code>',
        );			
			
		$use_meta_noodp_field_id = 'psp_'.$setting_name.'_use_meta_noodp';		
		$use_meta_noodp_field_title = esc_html__( 'Use noodp: ', 'platinum-seo-pack' );
		
		//add_settings_field( $use_meta_noodp_field_id, $use_meta_noodp_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $use_meta_noodp_field );
		
		//noydir
		$use_meta_noydir_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_use_meta_noydir',
            'option_name'   => $psp_settings_name.'[use_meta_noydir]',
			'option_value'  => isset($psp_settings['use_meta_noydir']) ? $psp_settings['use_meta_noydir'] : '',
			'checkbox_label' => esc_html__( 'Use meta robots tag ', 'platinum-seo-pack' ).'<code> noydir</code>',
        );			
			
		$use_meta_noydir_field_id = 'psp_'.$setting_name.'_use_meta_noydir';		
		$use_meta_noydir_field_title = esc_html__( 'Use noydir: ', 'platinum-seo-pack' );
		
		//add_settings_field( $use_meta_noydir_field_id, $use_meta_noydir_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $use_meta_noydir_field );
		
		//autogenerate description
		$autogenerate_desc_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_autogenerate_description',
            'option_name'   => $psp_settings_name.'[autogenerate_description]',
			'option_value'  => isset($psp_settings['autogenerate_description']) ? $psp_settings['autogenerate_description'] : '',
			'checkbox_label' => esc_html__( 'Autogenerate description for all post types', 'platinum-seo-pack' ),
			'option_description' => esc_html__( 'If no SEO description is set for any post, an auto-generated description will be set against the meta description tag for the Post or Page. Post Excerpt, if it exists, will be used. If not, the first few words from the Post or Page will be used.', 'platinum-seo-pack' )
        );			
			
		$autogenerate_desc_field_id = 'psp_'.$setting_name.'_autogenerate_description';		
		$autogenerate_desc_field_title = esc_html__( 'Use description autogenerator: ', 'platinum-seo-pack' );	
		
		add_settings_field( $autogenerate_desc_field_id, $autogenerate_desc_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $autogenerate_desc_field );
		
		//canonical
		$use_meta_canonical_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_use_canonical',
            'option_name'   => $psp_settings_name.'[use_canonical]',
			'option_value'  => isset($psp_settings['use_canonical']) ? $psp_settings['use_canonical'] : '',
			'checkbox_label' => esc_html__( 'Use canonical tags generated by Platinum SEO', 'platinum-seo-pack' )
        );			
			
		$use_meta_canonical_field_id = 'psp_'.$setting_name.'_use_canonical';		
		$use_meta_canonical_field_title = esc_html__( 'Use canonical tags: ', 'platinum-seo-pack' );	
		
		add_settings_field( $use_meta_canonical_field_id, $use_meta_canonical_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $use_meta_canonical_field );
		
		//meta keywords
		$use_meta_keywords_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_use_meta_keywords',
            'option_name'   => $psp_settings_name.'[use_meta_keywords]',
			'option_value'  => isset($psp_settings['use_meta_keywords']) ? $psp_settings['use_meta_keywords'] : '',
			'checkbox_label' => esc_html__( 'Use meta keywords tag.', 'platinum-seo-pack' ),
			'option_description' => esc_html__( 'Note that Google and many other Search engines do not read/use this tag. If for any specific reason, you intend to display meta keywords tag on your site, you may enable this. Your focus or primary keywords will be shown against this tag on the respective Post or Page.', 'platinum-seo-pack' )
        );			
			
		$use_meta_keywords_field_id = 'psp_'.$setting_name.'_use_meta_keywords';		
		$use_meta_keywords_field_title = esc_html__( 'Use meta keywords tag: ', 'platinum-seo-pack' );	
		
		add_settings_field( $use_meta_keywords_field_id, $use_meta_keywords_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $use_meta_keywords_field );		

		//use PSP template placeholder script for platinum seo meta tags
		$use_psp_template_tags_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_use_psp_template_script',
            'option_name'   => $psp_settings_name.'[use_psp_template_script]',
			'option_value'  => isset($psp_settings['use_psp_template_script']) ? $psp_settings['use_psp_template_script'] : '',
			'checkbox_label' => esc_html__( 'Use Platinum SEO template placeholder script for meta tags', 'platinum-seo-pack' ),
			'option_description' => esc_html__( 'Place the template acript in the theme <code>header.php</code> file where you want the Platinum SEO plugin meta tags to appear.', 'platinum-seo-pack' )
        );			
			
		$use_psp_template_tags_field_id = 'psp_'.$setting_name.'_use_psp_template_script';		
		$use_psp_template_tags_field_title = esc_html__( 'Use Template Script: ', 'platinum-seo-pack' );	
		
		add_settings_field( $use_psp_template_tags_field_id, $use_psp_template_tags_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $use_psp_template_tags_field );	

		//Hide Advanced tab of platinum seo metabox on post type editor.
		$psp_hide_advanced_tab_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_hide_metabox_advanced',
            'option_name'   => $psp_settings_name.'[hide_metabox_advanced]',
			'option_value'  => isset($psp_settings['hide_metabox_advanced']) ? $psp_settings['hide_metabox_advanced'] : '',
			'checkbox_label' => esc_html__( 'Disable advanced tab', 'platinum-seo-pack' ),
			'option_description' => esc_html__( 'Checking this will disable Techblissonline Platinum SEO and Social Meta Box, on Post/Page Editor, for all users other than the users with administrative privileges .', 'platinum-seo-pack' )
        );			
			
		$psp_hide_advanced_tab_id = 'psp_'.$setting_name.'_hide_metabox_advanced';		
		$psp_hide_advanced_tab_title = esc_html__( 'Platinum Seo and Social Meta Box Advanced Tab: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_hide_advanced_tab_id, $psp_hide_advanced_tab_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $psp_hide_advanced_tab_field );	
		
		//Section: Nofollow Settings
		$section_id = 'psp_'.'nofollow'.'_section';
		//$section_title = 'Nofollow Settings';
		$section_title = '<code>Nofollow</code>' .esc_html__( ' Settings', 'platinum-seo-pack' );
		add_settings_section( $section_id, $section_title, array( &$this, 'section_nofollow_desc' ), $this->psp_general_settings_group );
		
		//nofollow external links on front page
		/*$nofollow_ext_links_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_nofollow_external_links_frontpage',
            'option_name'   => $psp_settings_name.'[nofollow_external_links_frontpage]',
			'option_value'  => $psp_settings['nofollow_external_links_frontpage'],
			'checkbox_label' => __( 'Nofollow External links on Home (Front) page', 'platinum-seo-pack' )
        );			
			
		$nofollow_ext_links_id = 'psp_'.$setting_name.'_nofollow_external_links_frontpage';		
		$nofollow_ext_links_title = '';	
		
		add_settings_field( $nofollow_ext_links_id, $nofollow_ext_links_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $nofollow_ext_links_field );*/
		
		//nofollow links to tag pages
		$nofollow_tag_links_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_nofollow_tag_links',
            'option_name'   => $psp_settings_name.'[nofollow_tag_links]',
			'option_value'  => isset($psp_settings['nofollow_tag_links']) ? $psp_settings['nofollow_tag_links'] : '',
			'checkbox_label' => '<code>Nofollow</code>',
        );			
			
		$nofollow_tag_links_id = 'psp_'.$setting_name.'_nofollow_tag_links';		
		$nofollow_tag_links_title = esc_html__( 'Links to tag pages: ', 'platinum-seo-pack' );	
		
		add_settings_field( $nofollow_tag_links_id, $nofollow_tag_links_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $nofollow_tag_links_field );
		
		//nofollow links to archive pages
		/*$nofollow_archive_links_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_nofollow_archive_links',
            'option_name'   => $psp_settings_name.'[nofollow_archive_links]',
			'option_value'  => $psp_settings['nofollow_archive_links'],
			'checkbox_label' => __( 'Nofollow Archive links on all Post types', 'platinum-seo-pack' )
        );			
			
		$nofollow_archive_links_id = 'psp_'.$setting_name.'_nofollow_archive_links';		
		$nofollow_archive_links_title = '';	
		
		add_settings_field( $nofollow_archive_links_id, $nofollow_archive_links_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $nofollow_archive_links_field );*/
		
		//nofollow login and registration links
		$nofollow_logreg_links_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_nofollow_loginregn_links',
            'option_name'   => $psp_settings_name.'[nofollow_loginregn_links]',
			'option_value'  => isset($psp_settings['nofollow_loginregn_links']) ? $psp_settings['nofollow_loginregn_links'] : '',
			'checkbox_label' => '<code>Nofollow</code>',
        );			
			
		$nofollow_logreg_links_id = 'psp_'.$setting_name.'_nofollow_loginregn_links';		
		$nofollow_logreg_links_title = esc_html__( 'Login and registration links: ', 'platinum-seo-pack' );	
		
		add_settings_field( $nofollow_logreg_links_id, $nofollow_logreg_links_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $nofollow_logreg_links_field );
		
		$this->psp_head_cleaner_settings($setting_name, $psp_settings);
		$this->psp_comment_cleaner_settings($setting_name, $psp_settings);
		$this->psp_sitelinks_search_box_settings($setting_name, $psp_settings);
	}	
	
	/*
	* Wordpress sitelinks search box settings
	*/
	function psp_sitelinks_search_box_settings($setting_name, $psp_settings) {
	
		$psp_settings_name = "psp_".$setting_name."_settings";
	    /************
		//sitelinks searchbox Section
		$section_id = 'psp_sitelinks_search_section';		
		$section_title =  __( 'Sitelinks Search Box in Google:', 'platinum-seo-pack' );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_sitelinks_searchbox_desc' ), $this->psp_general_settings_group );
		
		//Enable sitelinks searchbox
		$sitelinks_search_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_sitelinks_search',
			'option_name'   => $psp_settings_name.'[sitelinks_search_box]',
			'option_value'  => $psp_settings['sitelinks_search_box'],
			'checkbox_label' => __( 'Enable', 'platinum-seo-pack' ),			
		);			
			
		$sitelinks_search_id = 'psp_'.$setting_name.'_sitelinks_search';		
		$sitelinks_search_title = __( 'Sitelinks Searchbox: ', 'platinum-seo-pack' );	
		
		add_settings_field( $sitelinks_search_id, $sitelinks_search_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $sitelinks_search_field );
		
		//Sitelinks search box URL parameter		
		$psp_sitelinks_target_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_sitelinks_searchbox_target',
            'option_name'   => $psp_settings_name.'[sitelinks_searchbox_target]',
			'option_value'  => $psp_settings['sitelinks_searchbox_target'],
			'option_description' => __( 'Here you can specify a search URL pattern for sending queries to your site search engine. You need to chane this only if the URL is different from what is defined above. Formost wordpress sites, leaving this unchanged would work.', 'platinum-seo-pack' ),
        );
		
		$psp_sitelinks_target_field_id = 'psp_'.$setting_name.'_sitelinks_searchbox_target';	
		$psp_sitelinks_target_field_title = __( 'Sitelinks Search box Target URL: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_sitelinks_target_field_id, $psp_sitelinks_target_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_general_settings_group, $section_id,  $psp_sitelinks_target_field);
		*******************/
		//Credits Section
		$section_id = 'psp_credit_section';		
		$section_title =  esc_html__( 'Credits:', 'platinum-seo-pack' );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_empty_desc' ), $this->psp_general_settings_group );
		
		//Enable credits
		$credits_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_credits',
			'option_name'   => $psp_settings_name.'[credits]',
			'option_value'  => isset($psp_settings['credits']) ? $psp_settings['credits'] : '',
			'checkbox_label' => esc_html__( 'Link To Platinum SEO', 'platinum-seo-pack' ),			
		);			
			
		$credits_id = 'psp_'.$setting_name.'_credits';		
		$credits_title = esc_html__( 'Credits: ', 'platinum-seo-pack' );	
		
		add_settings_field( $credits_id, $credits_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $credits_field );
	
	}	
	
	/*
	* Wordpress Head cleaner settings
	*/
	function psp_head_cleaner_settings($setting_name, $psp_settings) {
	
		$psp_settings_name = "psp_".$setting_name."_settings";
		
		//Section
		$section_id = 'psp_cleanup_head_section';		
		$section_title =  esc_html__( 'Clean Up HTML Head Section', 'platinum-seo-pack' );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_cleanup_head_desc' ), $this->psp_general_settings_group );
		
		//field			
		
		//Hide Extra Feed Links
		$hide_feed_links_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_feed_links',
			'option_name'   => $psp_settings_name.'[hide_feed_links]',
			'option_value'  => isset($psp_settings['hide_feed_links']) ? $psp_settings['hide_feed_links'] : '',
			'checkbox_label' => esc_html__( 'Remove extra feed links from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_feed_links_field_id = 'psp_'.$setting_name.'_hide_feed_links';		
		$hide_feed_links_field_title = esc_html__( 'Extra Feed Links: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_feed_links_field_id, $hide_feed_links_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_feed_links_field );
		
		//Hide RSD link 
		$hide_rsd_link_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_rsd_link',
			'option_name'   => $psp_settings_name.'[hide_rsd_link]',
			'option_value'  => isset($psp_settings['hide_rsd_link']) ? $psp_settings['hide_rsd_link'] : '',
			'checkbox_label' => esc_html__( 'Remove RSD link from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_rsd_link_id = 'psp_'.$setting_name.'_hide_rsd_link';		
		$hide_rsd_link_title = esc_html__( 'RSD Link: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_rsd_link_id, $hide_rsd_link_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_rsd_link_field );
		
		//Hide wp shortlink 
		$hide_wp_shortlink_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_wp_shortlink',
			'option_name'   => $psp_settings_name.'[hide_wp_shortlink_wp_head]',
			'option_value'  => isset($psp_settings['hide_wp_shortlink_wp_head']) ? $psp_settings['hide_wp_shortlink_wp_head'] : '',
			'checkbox_label' => esc_html__( 'Remove wordpress shortlink from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_wp_shortlink_link_id = 'psp_'.$setting_name.'_hide_wp_shortlink';		
		$hide_wp_shortlink_link_title = esc_html__( 'WP shortlink: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_wp_shortlink_link_id, $hide_wp_shortlink_link_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_wp_shortlink_field );
		
		//Hide wlw manifest link (Windows live writer link)
		$hide_wlw_manifest_link_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_wlw_manifest_link',
			'option_name'   => $psp_settings_name.'[hide_wlw_manifest_link]',
			'option_value'  => isset($psp_settings['hide_wlw_manifest_link']) ? $psp_settings['hide_wlw_manifest_link'] : '',
			'checkbox_label' => esc_html__( 'Remove wlwmanifest link from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_wlw_manifest_link_id = 'psp_'.$setting_name.'_hide_wlw_manifest_link';		
		$hide_wlw_manifest_link_title = esc_html__( 'WLWmanifest link: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_wlw_manifest_link_id, $hide_wlw_manifest_link_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_wlw_manifest_link_field );
		
		//Hide index rel link 
		$hide_index_rel_link_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_index_rel_link',
			'option_name'   => $psp_settings_name.'[hide_index_rel_link]',
			'option_value'  => isset($psp_settings['hide_index_rel_link']) ? $psp_settings['hide_index_rel_link'] : '',
			'checkbox_label' => esc_html__( 'Remove index rel link from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_index_rel_link_id = 'psp_'.$setting_name.'_hide_index_rel_link';		
		$hide_index_rel_link_title = esc_html__( 'Index rel Link: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_index_rel_link_id, $hide_index_rel_link_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_index_rel_link_field );
		
		//Hide adjacent posts link 
		$hide_adjacent_posts_rel_link_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_adjacent_posts_rel_link',
			'option_name'   => $psp_settings_name.'[hide_adjacent_posts_rel_link_wp_head]',
			'option_value'  => isset($psp_settings['hide_adjacent_posts_rel_link_wp_head']) ? $psp_settings['hide_adjacent_posts_rel_link_wp_head'] : '',
			'checkbox_label' => esc_html__( 'Remove adjacent posts rel link from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_adjacent_posts_rel_link_id = 'psp_'.$setting_name.'_hide_adjacent_posts_rel_link';		
		$hide_adjacent_posts_rel_link_title = esc_html__( 'Adjacent posts rel Link: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_adjacent_posts_rel_link_id, $hide_adjacent_posts_rel_link_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_adjacent_posts_rel_link_field );
		
		//Hide parent post rel link 
		$hide_parent_rel_link_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_parent_post_rel_link',
			'option_name'   => $psp_settings_name.'[hide_parent_post_rel_link]',
			'option_value'  => isset($psp_settings['hide_parent_post_rel_link']) ? $psp_settings['hide_parent_post_rel_link'] : '',
			'checkbox_label' => esc_html__( 'Remove parent post rel link from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_parent_rel_link_id = 'psp_'.$setting_name.'_hide_parent_post_rel_link';		
		$hide_parent_rel_link_title = esc_html__( 'Parent post rel Link: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_parent_rel_link_id, $hide_parent_rel_link_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_parent_rel_link_field );
		
		//Hide start post rel link 
		$hide_start_post_rel_link_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_start_post_rel_link',
			'option_name'   => $psp_settings_name.'[hide_start_post_rel_link]',
			'option_value'  => isset($psp_settings['hide_start_post_rel_link']) ? $psp_settings['hide_start_post_rel_link'] : '',
			'checkbox_label' => esc_html__( 'Remove start post rel link from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_start_post_rel_link_id = 'psp_'.$setting_name.'_hide_start_post_rel_link';		
		$hide_start_post_rel_link_title = esc_html__( 'Start post rel Link: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_start_post_rel_link_id, $hide_start_post_rel_link_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_start_post_rel_link_field );
		
		//Hide wp generator
		$hide_wp_generator_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_hide_wp_generator',
			'option_name'   => $psp_settings_name.'[hide_wp_generator]',
			'option_value'  => isset($psp_settings['hide_wp_generator']) ? $psp_settings['hide_wp_generator'] : '',
			'checkbox_label' => esc_html__( 'Remove wordpress version information from head section', 'platinum-seo-pack' ),			
		);			
			
		$hide_wp_generator_id = 'psp_'.$setting_name.'_hide_wp_generator';		
		$hide_wp_generator_title = esc_html__( 'WP generator: ', 'platinum-seo-pack' );	
		
		add_settings_field( $hide_wp_generator_id, $hide_wp_generator_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $hide_wp_generator_field );
	
	}
	
	/*
	*/
	function psp_comment_cleaner_settings($setting_name, $psp_settings) {
	
		$psp_settings_name = "psp_".$setting_name."_settings";
		//Section
		$section_id = 'psp_cleanup_comment_section';		
		$section_title =  esc_html__( 'Clean Up Comments Section', 'platinum-seo-pack' );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_cleanup_comment_desc' ), $this->psp_general_settings_group );
		
		//fields
		
		//Strip HTML in comment text
		$comment_text_nohtml_kses_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_comment_text_nohtml_kses',
			'option_name'   => $psp_settings_name.'[comment_text_nohtml_kses]',
			'option_value'  => isset($psp_settings['comment_text_nohtml_kses']) ? $psp_settings['comment_text_nohtml_kses'] : '',
			'checkbox_label' => esc_html__( 'Strip HTML in comment text', 'platinum-seo-pack' ),			
		);			
			
		$comment_text_nohtml_kses_id = 'psp_'.$setting_name.'_comment_text_nohtml_kses';		
		$comment_text_nohtml_kses_title = esc_html__( 'Strip HTML in comment text: ', 'platinum-seo-pack' );	
		
		add_settings_field( $comment_text_nohtml_kses_id, $comment_text_nohtml_kses_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $comment_text_nohtml_kses_field );
		
		//Strip HTML in comment text RSS
		$comment_text_rss_nohtml_kses_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_comment_text_rss_nohtml_kses',
			'option_name'   => $psp_settings_name.'[comment_text_rss_nohtml_kses]',
			'option_value'  => isset($psp_settings['comment_text_rss_nohtml_kses']) ? $psp_settings['comment_text_rss_nohtml_kses'] : '',
			'checkbox_label' => esc_html__( 'Strip HTML from comment text RSS', 'platinum-seo-pack' ),			
		);			
			
		$comment_text_rss_nohtml_kses_id = 'psp_'.$setting_name.'_comment_text_rss_nohtml_kses';		
		$comment_text_rss_nohtml_kses_title = esc_html__( 'Strip HTML in comment text RSS: ', 'platinum-seo-pack' );	
		
		add_settings_field( $comment_text_rss_nohtml_kses_id, $comment_text_rss_nohtml_kses_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $comment_text_rss_nohtml_kses_field );
		
		//Strip HTML in comment excerpt
		$comment_excerpt_nohtml_kses_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_comment_excerpt_nohtml_kses',
			'option_name'   => $psp_settings_name.'[comment_excerpt_nohtml_kses]',
			'option_value'  => isset($psp_settings['comment_excerpt_nohtml_kses']) ? $psp_settings['comment_excerpt_nohtml_kses'] : '',
			'checkbox_label' => esc_html__( 'Strip HTML from comment Excerpt', 'platinum-seo-pack' ),			
		);			
			
		$comment_excerpt_nohtml_kses_id = 'psp_'.$setting_name.'_comment_excerpt_nohtml_kses';		
		$comment_excerpt_nohtml_kses_title = esc_html__( 'Strip HTML in comment Excerpt: ', 'platinum-seo-pack' );	
		
		add_settings_field( $comment_excerpt_nohtml_kses_id, $comment_excerpt_nohtml_kses_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $comment_excerpt_nohtml_kses_field );
		
		//Make links in comments not clickable
		$comment_text_no_make_clickable_field     = array (
			'label_for' 	=> 'psp_'.$setting_name.'_comment_text_no_make_clickable',
			'option_name'   => $psp_settings_name.'[comment_text_no_make_clickable]',
			'option_value'  => isset($psp_settings['comment_text_no_make_clickable']) ? $psp_settings['comment_text_no_make_clickable'] : '',
			'checkbox_label' => esc_html__( 'Make links in comments not clickable i.e remove links', 'platinum-seo-pack' ),			
		);			
			
		$comment_text_no_make_clickable_id = 'psp_'.$setting_name.'_comment_text_no_make_clickable';		
		$comment_text_no_make_clickable_title = esc_html__( 'Anchor tags in comments ', 'platinum-seo-pack' );	
		
		add_settings_field( $comment_text_no_make_clickable_id, $comment_text_no_make_clickable_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_general_settings_group, $section_id, $comment_text_no_make_clickable_field );		
		
	}
	
	function sanitize_general_settings($settings) {
	    
	    //error_log(print_r(	$settings, true ));
	    
	    if( ! empty( $settings['wizard'] ) ) {
			
			return $settings;
			
		}
		
		//error_log('settings not returned' );
	
	    if( isset( $settings['separator'] ) ) {
			
			$settings['separator'] = sanitize_text_field( htmlentities($settings['separator']) );
			
			//$psp_separators = array ( '-', '&ndash;', '&mdash;', '&middot;', '&bull;', '*', '|', '~', '&laquo;', '&raquo;', '&lt;', '&gt;', '&tilde;', '&hearts;', '&clubs;');	
			$psp_separators = array ( '-', '&ndash;', '&mdash;', '&middot;', '&bull;', '*', '|', '~', '&laquo;', '&raquo;', '&lt;', '&gt;', '&tilde;', '&hearts;', '&clubs;', ':', '★');
			
			if (!in_array($settings['separator'], $psp_separators)) {
				$settings['separator'] = '';
			}
		}
	
		if( isset( $settings['paged_title_format'] ) ) {
			$settings['paged_title_format'] = sanitize_text_field( $settings['paged_title_format'] );			
		}
		
		if( isset( $settings['rewrite_titles'] ) ) {		
			$settings['rewrite_titles'] = !is_null(filter_var($settings['rewrite_titles'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['rewrite_titles'] : '';		
		}
		
		if( isset( $settings['force_psp_titles'] ) ) {
			$settings['force_psp_titles'] = !is_null(filter_var($settings['force_psp_titles'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['force_psp_titles'] : '';
		}
		
		if( isset( $settings['noindex_subpages'] ) ) {
			$settings['noindex_subpages'] = !is_null(filter_var($settings['noindex_subpages'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['noindex_subpages'] : '';
		}
		
		if( isset( $settings['noindex_rss_feeds'] ) ) {
			$settings['noindex_rss_feeds'] = !is_null(filter_var($settings['noindex_rss_feeds'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['noindex_rss_feeds'] : '';
		}
		
		if( isset( $settings['noindex_pt_comment_pages'] ) ) {
			$settings['noindex_pt_comment_pages'] = !is_null(filter_var($settings['noindex_pt_comment_pages'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['noindex_pt_comment_pages'] : '';
		}
		
		if( isset( $settings['noindex_pt_paginations'] ) ) {
			$settings['noindex_pt_paginations'] = !is_null(filter_var($settings['noindex_pt_paginations'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['noindex_pt_paginations'] : '';
		}
		
		if( isset( $settings['_use_meta_noodp'] ) ) {
			$settings['_use_meta_noodp'] = !is_null(filter_var($settings['_use_meta_noodp'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['_use_meta_noodp'] : '';
		}
		
		if( isset( $settings['use_meta_noydir'] ) ) {
			$settings['use_meta_noydir'] = !is_null(filter_var($settings['use_meta_noydir'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_meta_noydir'] : '';
		}
		
		if( isset( $settings['autogenerate_description'] ) ) {
			$settings['autogenerate_description'] = !is_null(filter_var($settings['autogenerate_description'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['autogenerate_description'] : '';
		}
		
		if( isset( $settings['use_canonical'] ) ) {
			$settings['use_canonical'] = !is_null(filter_var($settings['use_canonical'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_canonical'] : '';
		}
		
		if( isset( $settings['use_meta_keywords'] ) ) {
			$settings['use_meta_keywords'] = !is_null(filter_var($settings['use_meta_keywords'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_meta_keywords'] : '';
		}
		
		if( isset( $settings['use_psp_template_script'] ) ) {
			$settings['use_psp_template_script'] = !is_null(filter_var($settings['use_psp_template_script'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_psp_template_script'] : '';
		}
		
		if( isset( $settings['hide_metabox_advanced'] ) ) {
			$settings['hide_metabox_advanced'] = !is_null(filter_var($settings['hide_metabox_advanced'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_metabox_advanced'] : '';
		}
		
		if( isset( $settings['nofollow_tag_links'] ) ) {
			$settings['nofollow_tag_links'] = !is_null(filter_var($settings['nofollow_tag_links'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['nofollow_tag_links'] : '';
		}
		
		if( isset( $settings['nofollow_loginregn_links'] ) ) {
			$settings['nofollow_loginregn_links'] = !is_null(filter_var($settings['nofollow_loginregn_links'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['nofollow_loginregn_links'] : '';
		}
		
		if( isset( $settings['credits'] ) ) {
			$settings['credits'] = !is_null(filter_var($settings['credits'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['credits'] : '';
		}
		
		if( isset( $settings['hide_feed_links'] ) ) {
			$settings['hide_feed_links'] = !is_null(filter_var($settings['hide_feed_links'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_feed_links'] : '';
		}
		
		if( isset( $settings['hide_rsd_link'] ) ) {
			$settings['hide_rsd_link'] = !is_null(filter_var($settings['hide_rsd_link'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_rsd_link'] : '';
		}
		
		if( isset( $settings['hide_wp_shortlink_wp_head'] ) ) {
			$settings['hide_wp_shortlink_wp_head'] = !is_null(filter_var($settings['hide_wp_shortlink_wp_head'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_wp_shortlink_wp_head'] : '';
		}
		
		if( isset( $settings['hide_wlw_manifest_link'] ) ) {
			$settings['hide_wlw_manifest_link'] = !is_null(filter_var($settings['hide_wlw_manifest_link'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_wlw_manifest_link'] : '';
		}
		
		if( isset( $settings['hide_index_rel_link'] ) ) {
			$settings['hide_index_rel_link'] = !is_null(filter_var($settings['hide_index_rel_link'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_index_rel_link'] : '';
		}
		
		if( isset( $settings['hide_adjacent_posts_rel_link_wp_head'] ) ) {
			$settings['hide_adjacent_posts_rel_link_wp_head'] = !is_null(filter_var($settings['hide_adjacent_posts_rel_link_wp_head'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_adjacent_posts_rel_link_wp_head'] : '';
		}
		
		if( isset( $settings['hide_parent_post_rel_link'] ) ) {
			$settings['hide_parent_post_rel_link'] = !is_null(filter_var($settings['hide_parent_post_rel_link'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_parent_post_rel_link'] : '';
		}
		
		if( isset( $settings['hide_start_post_rel_link'] ) ) {
			$settings['hide_start_post_rel_link'] = !is_null(filter_var($settings['hide_start_post_rel_link'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_start_post_rel_link'] : '';
		}
		
		if( isset( $settings['hide_wp_generator'] ) ) {
			$settings['hide_wp_generator'] = !is_null(filter_var($settings['hide_wp_generator'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_wp_generator'] : '';
		}
		
		if( isset( $settings['comment_text_nohtml_kses'] ) ) {
			$settings['comment_text_nohtml_kses'] = !is_null(filter_var($settings['comment_text_nohtml_kses'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['comment_text_nohtml_kses'] : '';
		}
		
		if( isset( $settings['_comment_text_rss_nohtml_kses'] ) ) {
			$settings['_comment_text_rss_nohtml_kses'] = !is_null(filter_var($settings['_comment_text_rss_nohtml_kses'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['_comment_text_rss_nohtml_kses'] : '';
		}
		
		if( isset( $settings['comment_excerpt_nohtml_kses'] ) ) {
			$settings['comment_excerpt_nohtml_kses'] = !is_null(filter_var($settings['comment_excerpt_nohtml_kses'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['comment_excerpt_nohtml_kses'] : '';
		}
		
		if( isset( $settings['comment_text_no_make_clickable'] ) ) {
			$settings['comment_text_no_make_clickable'] = !is_null(filter_var($settings['comment_text_no_make_clickable'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['comment_text_no_make_clickable'] : '';
		}
		
		return $settings;		
		
	}
	
	/*
	 * Registers the breadcrumb settings and appends the
	 * key to the plugin settings tabs array. - breadcrumbs
	 */
	private function register_breadcrumb_settings() {
	
	    $setting_name = "breadcrumb";
		$this->psp_settings_tabs[$this->psp_breadcrumb_settings_group] = 'Breadcrumbs';		
		$psp_settings_name = "psp_breadcrumb_settings";
		//$setting_name_text = str_replace( "_", " ", $psp_settings_name );
		//$setting_name_text = ucwords($setting_name_text);
		
		$psp_settings = get_option($psp_settings_name);
		//$this->psp_settings_name = $psp_settings;		
		
		register_setting( $this->psp_breadcrumb_settings_group, $psp_settings_name, array( &$this, 'sanitize_breadcrumb_settings' ) );
		
		//Section
		$section_id = 'psp_breadcrumb_section';		
		$section_title =  esc_html__( 'Breadcrumb Settings', 'platinum-seo-pack' );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_breadcrumb_desc' ), $this->psp_breadcrumb_settings_group );
		
		//yse default settings
		$psp_bc_use_defaults_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_use_defaults',
            'option_name'   => $psp_settings_name.'[use_defaults]',
			'option_value'  => isset($psp_settings['use_defaults']) ? $psp_settings['use_defaults'] : '',
			'checkbox_label' => esc_html__( 'Use default settings', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'If this is checked, all the user defined settings on this page will be ignored and default settings will be used.', 'platinum-seo-pack' ),
        );			
			
		$psp_bc_use_defaults_field_id = 'psp_'.$setting_name.'_use_defaults';		
		$psp_bc_use_defaults_field_title = esc_html__( 'Use Default Settings: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_bc_use_defaults_field_id, $psp_bc_use_defaults_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_breadcrumb_settings_group, $section_id, $psp_bc_use_defaults_field );
		
		//field				
		
		//$psp_separators = array ('&dash;', '&ndash;', '&mdash;', '&middot;', '&bull;', '*', '&#8902;', '|', '~', '&laquo;', '&raquo;', '&lt;', '&gt;'); &sstarf; &hyphen; &dash;
		//$psp_separators = array ('' => 'None', '-' => '-', '&ndash;' => '&ndash;', '&mdash;' => '&mdash;', '&middot;' => '&middot;', '&bull;' => '&bull;', '*' => '*', '|' => '|', '~' => '~', '&laquo;' => '&laquo;', '&raquo;' => '&raquo;', '&lt;' => '&lt;', '&gt;' => '&gt;', '&tilde;' => '&tilde;', '&hearts;' => '&hearts;', '&clubs;' => '&clubs;');
		$psp_separators = array ('' => 'None', '-' => '-', '&ndash;' => '&ndash;', '&mdash;' => '&mdash;', '&middot;' => '&middot;', '&bull;' => '&bull;', '*' => '*', '|' => '|', '~' => '~', '&laquo;' => '&laquo;', '&raquo;' => '&raquo;', '&lt;' => '&lt;', '&gt;' => '&gt;', '&tilde;' => '&tilde;', '&hearts;' => '&hearts;', '&clubs;' => '&clubs;', ':' => ':', '★' => '★');
		
		$psp_separator_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_separator',
            'option_name'   => $psp_settings_name.'[separator]',
			'option_value'  => isset($psp_settings['separator']) ? $psp_settings['separator'] : '',
			'radioitems' => $psp_separators,
			'option_description' => esc_html__( ' Used to specify the separator between breadcrumbs.', 'platinum-seo-pack' )
        );	        		
			
		$psp_separator_field_id = 'psp_'.$setting_name.'_separator';		
		$psp_separator_field_title = 'Breadcrumb Separator: ';	
		
		add_settings_field( $psp_separator_field_id, $psp_separator_field_title, array( &$this, 'psp_add_field_radiobuttons' ), $this->psp_breadcrumb_settings_group, $section_id, $psp_separator_field );
		
		//Parent item container tag		
		$psp_bc_container_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_container',
            'option_name'   => $psp_settings_name.'[container]',
			'option_value'  => isset($psp_settings['container']) ? esc_attr($psp_settings['container']) : '',			
			'option_description'  => esc_html__( 'The default tag used for the breadcrumb trail container is <code>div</code>. You might also use <code>span</code> or <code>li</code> as per your stying needs.', 'platinum-seo-pack' ),
        );			
			
		$psp_bc_container_field_id = 'psp_'.$setting_name.'_container';		
		$psp_bc_container_field_title = esc_html__( 'Breadcrumb Trail Container Tag: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_bc_container_field_id, $psp_bc_container_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_breadcrumb_settings_group, $section_id, $psp_bc_container_field );
		
		//show_browse 
		$psp_bc_show_browse_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_show_browse',
            'option_name'   => $psp_settings_name.'[show_browse]',
			'option_value'  => isset($psp_settings['show_browse']) ? esc_attr($psp_settings['show_browse']) : '',
			'checkbox_label' => esc_html__( 'Show <code>Browse</code> or any other user defined text in front of the breadcrumb trail', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should be checked if you want to show <code>Browse</code> or any other user defined text in front of the breadcrumb trail.', 'platinum-seo-pack' ),
        );			
			
		$psp_bc_show_browse_field_id = 'psp_'.$setting_name.'_show_browse';		
		$psp_bc_show_browse_field_title = esc_html__( 'Show Browse: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_bc_show_browse_field_id, $psp_bc_show_browse_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_breadcrumb_settings_group, $section_id, $psp_bc_show_browse_field );
		
		//show_on_front
		$show_on_front_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_show_on_front',
            'option_name'   => $psp_settings_name.'[show_on_front]',
			'option_value'  => isset($psp_settings['show_on_front']) ? $psp_settings['show_on_front'] : '',
			'checkbox_label' => esc_html__( 'Show on front', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should always remain checked if you want to show the front items like network home link, site home link or home title in the breadcrumb trail of the site front page using Platinum SEO', 'platinum-seo-pack' ),
        );			
			
		$show_on_front_field_id = 'psp_'.$setting_name.'_show_on_front';		
		$show_on_front_field_title = esc_html__( 'Show on Front: ', 'platinum-seo-pack' );	
		
		add_settings_field( $show_on_front_field_id, $show_on_front_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_breadcrumb_settings_group, $section_id, $show_on_front_field );

		//network
		$psp_bc_network_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_network',
            'option_name'   => $psp_settings_name.'[network]',
			'option_value'  => isset($psp_settings['network']) ? $psp_settings['network'] : '',
			'checkbox_label' => esc_html__( 'Do trail back to main site in the case of a multisite.', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should be checked if you want to create trail back to the main site if this site is part of a multisite.', 'platinum-seo-pack' ),
        );			
			
		$psp_bc_network_field_id = 'psp_'.$setting_name.'_network';		
		$psp_bc_network_field_title = esc_html__( 'Create trail back to Main Site: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_bc_network_field_id, $psp_bc_network_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_breadcrumb_settings_group, $section_id, $psp_bc_network_field );

		//show_title 
		$psp_bc_show_title_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_show_title',
            'option_name'   => $psp_settings_name.'[show_title]',
			'option_value'  => isset($psp_settings['show_title']) ? $psp_settings['show_title'] : '',
			'checkbox_label' => esc_html__( 'Show title as part of the breadcrumb trail', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should be checked if you want to show the title of the page in the breadcrumb trail.Title will be in plain text and not have an anchor tag', 'platinum-seo-pack' ),
        );			
			
		$psp_bc_show_title_field_id = 'psp_'.$setting_name.'_show_title';		
		$psp_bc_show_title_field_title = esc_html__( 'Show Title: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_bc_show_title_field_id, $psp_bc_show_title_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_breadcrumb_settings_group, $section_id, $psp_bc_show_title_field );	
		
		//echo 
		$psp_bc_echo_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_echo',
            'option_name'   => $psp_settings_name.'[echo]',
			'option_value'  => isset($psp_settings['echo']) ? $psp_settings['echo'] : '',
			'checkbox_label' => esc_html__( 'Enable the breadcrumbs', 'platinum-seo-pack' ),
			'option_description'  => esc_html__( 'This should be checked if you want to display the breadcrumb trail.', 'platinum-seo-pack' ),
        );			
			
		$psp_bc_echo_field_id = 'psp_'.$setting_name.'_echo';		
		//$psp_bc_echo_field_title = '<code>echo</code>'.esc_html__( ' the breadcrumb trail: ', 'platinum-seo-pack' );	
		$psp_bc_echo_field_title = esc_html__( 'Display the breadcrumb trail: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_bc_echo_field_id, $psp_bc_echo_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_breadcrumb_settings_group, $section_id, $psp_bc_echo_field );
		
		//Browse label
		$psp_bc_label_browse_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_browse',
            'option_name'   => $psp_settings_name.'[labels][browse]',
			'option_value'  => isset($psp_settings['labels']['browse']) ? esc_attr($psp_settings['labels']['browse']) : '',
			'option_description' => esc_html__( 'The default label used if <code>Show Browse</code> is checked is <code>Browse</code>. This text is used at the start of the breadcrumb trail if <code>Show Browse</code> is checked.', 'platinum-seo-pack' ),
        );
		
		$psp_bc_label_browse_field_id = 'psp_'.$setting_name.'_browse';	
		$psp_bc_label_browse_field_title = esc_html__( 'Text label to use in place of the default', 'platinum-seo-pack' ).'<code>Browse</code>:';	
		
		add_settings_field( $psp_bc_label_browse_field_id, $psp_bc_label_browse_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_breadcrumb_settings_group, $section_id,  $psp_bc_label_browse_field);
		
		
		//Home label
		$psp_bc_label_home_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_home',
            'option_name'   => $psp_settings_name.'[labels][home]',
			'option_value'  => isset($psp_settings['labels']['home']) ? esc_attr($psp_settings['labels']['home']) : '',
			'option_description' => esc_html__( 'The default label used to represent home page is <code>Home</code>.', 'platinum-seo-pack' ),
        );
		
		$psp_bc_label_home_field_id = 'psp_'.$setting_name.'_home';	
		$psp_bc_label_home_field_title = esc_html__( 'Label for Home: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_bc_label_home_field_id, $psp_bc_label_home_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_breadcrumb_settings_group, $section_id,  $psp_bc_label_home_field);
		
		//404 Error label
		$psp_bc_label_error_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_error_404',
            'option_name'   => $psp_settings_name.'[labels][error_404]',
			'option_value'  => isset($psp_settings['labels']['error_404']) ? esc_attr($psp_settings['labels']['error_404']) : '',
			'option_description' => esc_html__( 'The default label used is <code>404 Not Found</code>. This is the label to use as page title in breadcrumb trail on 404 Error page if <code>Show Title</code> is checked.', 'platinum-seo-pack' ),
        );
		
		$psp_bc_label_error_field_id = 'psp_'.$setting_name.'_error_404';	
		$psp_bc_label_error_field_title = esc_html__( 'Label to use as Title in breadcrumb trail on 404 Error page: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_bc_label_error_field_id, $psp_bc_label_error_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_breadcrumb_settings_group, $section_id,  $psp_bc_label_error_field);
		
		
	}	
	
	function sanitize_breadcrumb_settings($settings) {		
		
		if ( isset( $settings['use_defaults'] ) ) {
			$settings['use_defaults'] = !is_null(filter_var($settings['use_defaults'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_defaults'] : '';	
		}
		
		if( isset( $settings['separator'] ) ) {
			
			$settings['separator'] = sanitize_text_field( htmlentities($settings['separator']) );
			
			//$psp_separators = array ( '-', '&ndash;', '&mdash;', '&middot;', '&bull;', '*', '|', '~', '&laquo;', '&raquo;', '&lt;', '&gt;', '&tilde;', '&hearts;', '&clubs;');
			$psp_separators = array ( '-', '&ndash;', '&mdash;', '&middot;', '&bull;', '*', '|', '~', '&laquo;', '&raquo;', '&lt;', '&gt;', '&tilde;', '&hearts;', '&clubs;', ':', '★');	
			
			if (!in_array($settings['separator'], $psp_separators)) {
				$settings['separator'] = '';
			}
		}
		
		if ( isset( $settings['container'] ) ) {
			$settings['container'] = sanitize_text_field( $settings['container'] );
			$containers = array('div', 'li', 'span');
			if (!in_array($settings['container'], $containers)) {
				$settings['container'] = '';
			}
		}		
		
		if ( isset( $settings['show_browse'] ) ) {
			$settings['show_browse'] = !is_null(filter_var($settings['show_browse'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['show_browse'] : '';	
		}
		
		if ( isset( $settings['show_on_front'] ) ) {
			$settings['show_on_front'] = !is_null(filter_var($settings['show_on_front'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['show_on_front'] : '';	
		}
		
		if ( isset( $settings['network'] ) ) {
			$settings['network'] = !is_null(filter_var($settings['network'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['network'] : '';	
		}
		
		if ( isset( $settings['show_title'] ) ) {
			$settings['show_title'] = !is_null(filter_var($settings['show_title'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['show_title'] : '';	
		}
		
		if ( isset( $settings['echo'] ) ) {
			$settings['echo'] = !is_null(filter_var($settings['echo'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['echo'] : '';	
		}
		
		if( isset( $settings['labels']['browse'] ) ) $settings['labels']['browse'] = sanitize_text_field( $settings['labels']['browse'] );
		
		if( isset( $settings['labels']['home'] ) ) $settings['labels']['home'] = sanitize_text_field( $settings['labels']['home'] );
		
		if( isset( $settings['labels']['error_404'] ) ) $settings['labels']['error_404'] = sanitize_text_field( $settings['labels']['error_404'] );
		
		return $settings;
		
	}
	
	/*
	 * Registers the search and 404 title settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_other_settings($others_name) {
		$this->psp_settings_tabs[$this->psp_archives_settings_group] = 'Archives';		
		$psp_settings_name = "psp_".$others_name."_settings";
		$arc_name_text = str_replace( "_", " ", $others_name );
		$arc_name_text = ucwords($arc_name_text);
		
		$psp_settings = get_option($psp_settings_name);
		//$this->psp_settings_name = $psp_settings;
		
		wp_register_script( 'psp-atags-js', plugins_url( '/js/atags.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script('psp-atags-js');
		
		register_setting( $this->psp_archives_settings_group, $psp_settings_name, array( &$this, 'sanitize_other_archive_settings' ));
		
		//Section
		$section_id = 'psp_'.$others_name.'_section';
		//$section_title = $arc_name_text.' Settings';
		$section_title = sprintf( esc_html__( '%s Settings', 'platinum-seo-pack' ), $arc_name_text );
		
		if ($others_name == "search_result") {	
		
			add_settings_section( $section_id, $section_title, array( &$this, 'section_others_desc' ), $this->psp_archives_settings_group );
		} else if ($others_name == "404_page") {
			add_settings_section( $section_id, $section_title, array( &$this, 'section_404_desc' ), $this->psp_archives_settings_group );
		} else {
			//do nothing;
		}
		
		if ($others_name == "search_result") {		
			$option_title_formats = '<code>%search%</code> - '.esc_html__('search keyword/keyphrase', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
			
			//available tags array
		    $availableTags = ['sep', 'search', 'site_name', 'site_description'];
			
		} else if ($others_name == "404_page") {
			$option_title_formats = '<code>%title_404%</code> - '.esc_html__('"404 Not Found"', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
			
			//available tags array
		    $availableTags = ['sep', 'title_404', 'site_name'];
		} else {
			//do nothing;
		}
		
		//Fields
		$title_field     = array (
            'label_for' 	=> 'psp_'.$others_name.'_title',
            'option_name'   => $psp_settings_name.'[title]',
			'option_value'  => isset($psp_settings['title']) ? esc_attr($psp_settings['title']) : '',
			'option_description'  => $option_title_formats,
			'class_name' => 'titletags',
			'psp_tags' => $availableTags,
        );		
		
		$title_field_id = 'psp_'.$others_name.'_title';		
		$title_field_title = esc_html__('Title Format: ', 'platinum-seo-pack');	

		$robots_field     = array (
            'label_for' 	=> 'psp_'.$others_name.'_robots',
            'option_name'   => $psp_settings_name.'[robots]',
			'option_value'  => isset($psp_settings['robots']) ? $psp_settings['robots'] : '', 
			'checkbox_label' => '<code>noindex, follow</code>',
			'option_description' => esc_html__(' Do not index and show this page in SERPS (Search Engine Result Pages).', 'platinum-seo-pack')
        );
		
		$robots_field_id = 'psp_'.$others_name.'robots';
		$robots_field_title = esc_html__('Meta Robots: ', 'platinum-seo-pack');
		
		$noarchive_field     = array (
            'label_for' 	=> 'psp_'.$others_name.'_noarchive',
            'option_name'   => $psp_settings_name.'[noarchive]',
			'option_value'  => isset($psp_settings['noarchive']) ? $psp_settings['noarchive'] : '',
			'checkbox_label' => '<code>noarchive</code>',
			'option_description' => esc_html__(' Do not show Cached link in SERPS (Search Engine Result Pages) for this page. It thus tells search engines not to store a cached copy of the page.', 'platinum-seo-pack')
        );
		
		$noarchive_field_id = 'psp_'.$others_name.'noarchive';
		$noarchive_field_title = "";//esc_html__('Do not show Cached link in SERPS: ', 'platinum-seo-pack');
		
		$nosnippet_field     = array (
            'label_for' 	=> 'psp_'.$others_name.'_nosnippet',
            'option_name'   => $psp_settings_name.'[nosnippet]',
			'option_value'  => isset($psp_settings['nosnippet']) ? $psp_settings['nosnippet'] : '',
			'checkbox_label' => '<code>nosnippet</code>',
			'option_description' => esc_html__(' Do not show snippet (description) in SERPS (Search Engine Result Pages) for this page. It also tells search engines not to show a cached link in SERPS for this page.', 'platinum-seo-pack')
        );
		
		$nosnippet_field_id = 'psp_'.$others_name.'nosnippet';
		$nosnippet_field_title = "";//__('Do not show snippet in SERPS: ', 'platinum-seo-pack');
		
		add_settings_field( $title_field_id, $title_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_archives_settings_group, $section_id,  $title_field);
		add_settings_field( $robots_field_id, $robots_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_archives_settings_group, $section_id, $robots_field );
		if ($others_name == "search_result") {
			add_settings_field( $noarchive_field_id, $noarchive_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_archives_settings_group, $section_id, $noarchive_field );
			add_settings_field( $nosnippet_field_id, $nosnippet_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_archives_settings_group, $section_id, $nosnippet_field );
		}
	}
	
	/*
	 * Registers the Archive SEO settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_archive_settings($archive_name) {
		$this->psp_settings_tabs[$this->psp_archives_settings_group] = 'Archives';		
		$psp_settings_name = "psp_".$archive_name."_settings";
		$arc_name_text = str_replace( "_", " ", $archive_name );
		$arc_name_text = ucwords($arc_name_text);
		
		$psp_settings = get_option($psp_settings_name);
		//$this->psp_settings_name = $psp_settings;
		
		wp_register_script( 'psp-atags-js', plugins_url( '/js/atags.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script('psp-atags-js');
		
		//register_setting( $this->psp_archives_settings_group, $psp_settings_name );
		register_setting( $this->psp_archives_settings_group, $psp_settings_name, array( &$this, 'sanitize_other_archive_settings' ));
		//Section
		$section_id = 'psp_'.$archive_name.'section';
		//$section_title = $arc_name_text.' Settings';
		$section_title = sprintf( esc_html__( '%s Settings', 'platinum-seo-pack' ), $arc_name_text );
		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_archives_desc' ), $this->psp_archives_settings_group );
		
		if ($archive_name == "date_archive") {		
			$option_title_formats = '<code>%title_date%</code> - '.esc_html__('Date', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
			//available tags array
		    $availableTags = ['sep', 'title_date', 'site_name', 'site_description'];
		} else if ($archive_name == "author_archive") {
			$option_title_formats = '<code>%title_author%</code> - '.esc_html__('Author Name', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
			//available tags array
			$availableTags = ['sep', 'title_author', 'site_name', 'site_description'];
		} else {
			$option_title_formats = '<code>%seo_title%</code> - '.esc_html__('Post type archive Title', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
			//available tags array
			$availableTags = ['sep', 'seo_title', 'site_name', 'site_description'];
		}
		
		$title_field     = array (
            'label_for' 	=> 'psp_'.$archive_name.'_title',
            'option_name'   => $psp_settings_name.'[title]',
			'option_value'  => isset($psp_settings['title']) ? $psp_settings['title'] : '',
			'option_description' => $option_title_formats,
			'class_name' => 'titletags',
			'psp_tags' => $availableTags,
        );		
		
		$robots_field     = array (
            'label_for' 	=> 'psp_'.$archive_name.'_robots',
            'option_name'   => $psp_settings_name.'[robots]',
			'option_value'  => isset($psp_settings['robots']) ? $psp_settings['robots'] : '',
			'checkbox_label' => '<code>noindex, follow</code>',
			'option_description' => esc_html__(' Do not index and show this page in SERPS (Search Engine Result Pages).', 'platinum-seo-pack')
        );
		
		$noarchive_field     = array (
            'label_for' 	=> 'psp_'.$archive_name.'_noarchive',
            'option_name'   => $psp_settings_name.'[noarchive]',
			'option_value'  => isset($psp_settings['noarchive']) ? $psp_settings['noarchive'] : '',
			'checkbox_label' => '<code>noarchive</code>',
			'option_description' => esc_html__(' Do not show Cached link in SERPS (Search Engine Result Pages) for this page. It thus tells search engines not to store a cached copy of the page.', 'platinum-seo-pack')
        );
		
		$nosnippet_field     = array (
            'label_for' 	=> 'psp_'.$archive_name.'_nosnippet',
            'option_name'   => $psp_settings_name.'[nosnippet]',
			'option_value'  => isset($psp_settings['nosnippet']) ? $psp_settings['nosnippet'] : '',
			'checkbox_label' => '<code>nosnippet</code>',
			'option_description' => esc_html__(' Do not show snippet (description) in SERPS (Search Engine Result Pages) for this page. It also tells search engines not to show a cached link in SERPS for this page.', 'platinum-seo-pack')
        );
		
		/*$disable_field     = array (
            'label_for' 	=> 'psp_'.$archive_name.'_disable',
            'option_name'   => $psp_settings_name.'[disable]',
			'option_value'  => $psp_settings['disable'],
			'checkbox_label' => 'Disable the '.$arc_name_text
        );*/
		
		$redirect_field     = array (
            'label_for' 	=> 'psp_'.$archive_name.'_redirect_url',
            'option_name'   => $psp_settings_name.'[redirect_url]',
			'option_value'  => isset($psp_settings['redirect_url']) ? esc_url($psp_settings['redirect_url']) : '',
			'option_description' => esc_html__(' Set the URL to which users landing on this page should be redirected to. This is equivalent to disabling this archive page. You may redirect this page to the most appropriate page on your site, which might be the Front page or Home page of this site.', 'platinum-seo-pack')
        );
		
		$dditems = array('' => 'Select a redirection method', '301' => '301 Moved Permanently', '302' => '302 Found', '303' => '303 See Other', '307' => '307 Temporary Redirect');
		$redirectcode_field     = array (
            'label_for' 	=> 'psp_'.$archive_name.'_redirect_code',
            'option_name'   => $psp_settings_name.'[redirect_code]',
			'option_value'  => isset($psp_settings['redirect_code']) ? $psp_settings['redirect_code'] : '',
			'dditems' => $dditems,
			'option_description' => esc_html__(' Set the HTTP status code to use for this redirection. It is highly recommended to use 301 redirects in most cases, except where the redirection is of temporary nature.', 'platinum-seo-pack')
        );	 
		
		$title_field_id = 'psp_'.$archive_name.'_title';
		//$desc_field_id = 'psp_'.$archive_name.'_desc';
		$robots_field_id = 'psp_'.$archive_name.'_robots';
		$noarchive_field_id = 'psp_'.$archive_name.'_noarchive';
		$nosnippet_field_id = 'psp_'.$archive_name.'_nosnippet';
		//$disable_field_id = 'psp_'.$archive_name.'_disable';
		$redirect_field_id = 'psp_'.$archive_name.'_redirect';
		$redirectcode_field_id = 'psp_'.$archive_name.'_redirect_code';
		
		$title_field_title = esc_html__('Title Format: ', 'platinum-seo-pack');
		//$desc_field_title = esc_html__('Meta Description Format: ','platinum-seo-pack');
		$robots_field_title = esc_html__('Meta Robots: ', 'platinum-seo-pack');		
		$noarchive_field_title = "";//esc_html__('Do not show cached link in SERPS: ', 'platinum-seo-pack');
		$nosnippet_field_title = "";//esc_html__('Do not show snippet in SERPS: ', 'platinum-seo-pack');
		//$disable_field_title = 'Disable '.$arc_name_text. ' : ';
		//$disable_field_title = sprintf( esc_html__( 'Disable %s :', 'platinum-seo-pack' ), $arc_name_text );
		$redirect_field_title = esc_html__('Redirect To: ', 'platinum-seo-pack');	
		$redirectcode_field_title ="";// esc_html__('Use: ', 'platinum-seo-pack');
		
		//Fields
		add_settings_field( $title_field_id, $title_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_archives_settings_group, $section_id,  $title_field);
		//add_settings_field( $desc_field_id, $desc_field_title, array( &$this, 'psp_add_field_textarea' ), $this->psp_archives_settings_group, $section_id, $desc_field );
		add_settings_field( $robots_field_id, $robots_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_archives_settings_group, $section_id, $robots_field );
		add_settings_field( $noarchive_field_id, $noarchive_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_archives_settings_group, $section_id, $noarchive_field );
		add_settings_field( $nosnippet_field_id, $nosnippet_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_archives_settings_group, $section_id, $nosnippet_field );
		//add_settings_field( $disable_field_id, $disable_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_archives_settings_group, $section_id, $disable_field );
		add_settings_field( $redirect_field_id, $redirect_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_archives_settings_group, $section_id,  $redirect_field);
		add_settings_field( $redirectcode_field_id, $redirectcode_field_title, array( &$this, 'psp_add_field_dropdown' ), $this->psp_archives_settings_group, $section_id,  $redirectcode_field);
	}
	
	function sanitize_other_archive_settings($settings) {	
	
    	if ( isset( $settings['title'] ) ) {
    		$settings['title'] = sanitize_text_field( $settings['title'] );
    		
    		$psp_title_format = explode(" ", $settings['title']);
    		$titleformats = array('%title_date%', '%title_author%', '%seo_title%', '%search%', '%title_404%', '%sep%', '%site_name%', '%site_description%');
    		if(!empty($psp_title_format)) {
    			if (count($psp_title_format) != count(array_intersect($psp_title_format, $titleformats))) {
    				$settings['title'] = '';
    			}
    		}
    	}
    		
    	if ( isset( $settings['robots'] ) ) {
    		$settings['robots'] = !is_null(filter_var($settings['robots'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['robots'] : '';	
    	}
    	
    	if ( isset( $settings['noarchive'] ) ) {
    		$settings['noarchive'] = !is_null(filter_var($settings['noarchive'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['noarchive'] : '';	
    	}
    
    	if ( isset( $settings['nosnippet'] ) ) {
    		$settings['nosnippet'] = !is_null(filter_var($settings['nosnippet'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['nosnippet'] : '';	
    	}
        
        $psp_allowed_protocols = array('http','https');
    	if ( isset( $settings['redirect_url'] ) ) {
    		$settings['redirect_url'] = esc_url_raw( $settings['redirect_url'], $psp_allowed_protocols );
    	}
    	
    	if ( isset( $settings['redirect_code'] ) ) {
    		$settings['redirect_code'] = sanitize_text_field( $settings['redirect_code'] );
    		$scodes = array('301', '302', '303', '307');
    		if (!in_array($settings['redirect_code'], $scodes)) {
    			$settings['redirect_code'] = '';
    		}
    	}
    	
    	return $settings;
    }
	
	/*
	 * Registers the permalinks settings for taxonomies and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_permalink_settings() {
		$this->psp_settings_tabs[$this->psp_permalink_settings_group] = 'Permalinks';		
		$psp_settings_name = "psp_permalink_settings";		
		
		$psp_settings = get_option($psp_settings_name);
		//$this->psp_settings_name = $psp_settings;
		
		//register_setting( $this->psp_permalink_settings_group, $psp_settings_name );
		register_setting( $this->psp_permalink_settings_group, $psp_settings_name, array( &$this, 'sanitize_permalink_settings' ));
		
		//Redirection Section
		$section_id = 'psp_redirection_section';		
		$section_title = esc_html__('Redirections', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title,  array( &$this, 'section_redirections_desc' ), $this->psp_permalink_settings_group );
		
		//Fields
		
		$redirection_field     = array (
            'label_for' 	=> 'psp_redirection',
            'option_name'   => $psp_settings_name.'[redirection]',
			'option_value'  => isset($psp_settings['redirection']) ? $psp_settings['redirection'] : '',
			'checkbox_label' => esc_html__('', 'platinum-seo-pack'),
			'option_description' => esc_html__( 'Turn ON to enable redirections created using Platinum SEO (Recommended).', 'platinum-seo-pack' ),
        );
		
		$redirection_field_id = 'psp_redirection';
		//$redirection_field_title = esc_html__('Redirection: ', 'platinum-seo-pack');
		$redirection_field_title = esc_html__('Redirection: ', 'platinum-seo-pack').'<a href="https://techblissonline.com/redirection-in-wordpress/" target="_blank" rel="noopener">'.'<br>'.esc_html__('what does this do?', 'platinum-seo-pack').'</a>';
		
		add_settings_field( $redirection_field_id, $redirection_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $redirection_field );
		
		$auto_redirection_field     = array (
            'label_for' 	=> 'psp_auto_redirection',
            'option_name'   => $psp_settings_name.'[auto_redirection]',
			'option_value'  => isset($psp_settings['auto_redirection']) ? $psp_settings['auto_redirection'] : '',
			'checkbox_label' => esc_html__('', 'platinum-seo-pack'),
			'option_description' => esc_html__( 'Turn ON to enable automatics redirection of all Posts using Platinum SEO. this will automatically take care of changes in permalink format (Recommended).', 'platinum-seo-pack' ),
        );
		
		$auto_redirection_field_id = 'psp_auto_redirection';
		//$auto_redirection_field_title = esc_html__('Automatically Redirect Posts: ', 'platinum-seo-pack');
		$auto_redirection_field_title = esc_html__('Automatically Redirect Posts: ', 'platinum-seo-pack').'<a href="https://techblissonline.com/redirection-in-wordpress/#automatic-http-redirection-in-wordpress" target="_blank" rel="noopener">'.'<br>'.esc_html__('How does this help?', 'platinum-seo-pack').'</a>';
		
		add_settings_field( $auto_redirection_field_id, $auto_redirection_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $auto_redirection_field );
		
		$psp_301_limit_field     = array (
            'label_for' 	=> 'psp_301_limit',
            'option_name'   => $psp_settings_name.'[limit_301]',
			'option_value'  => isset($psp_settings['limit_301']) ? $psp_settings['limit_301'] : '',
			'option_label' => esc_html__( 'Rows', 'platinum-seo-pack' ),
			'option_description' => esc_html__( 'Set the max number of entries in Redirection log.(Highly Recommended)', 'platinum-seo-pack' ),
        );
		
		$psp_301_limit_field_id = 'psp_301_limit';
		$psp_301_limit_field_title = esc_html__('Limit Redirection Log to: ', 'platinum-seo-pack');
		
		add_settings_field( $psp_301_limit_field_id, $psp_301_limit_field_title, array( &$this, 'psp_add_field_text_number' ), $this->psp_permalink_settings_group, $section_id, $psp_301_limit_field);
		
		//V2.0.8
		$psp_disable_wp_404_guess_field     = array (
			'label_for' 	=> 'psp_disable_wp_404_guess',
			'option_name'   => $psp_settings_name.'[disable_wp_404_guess]',
			'option_value'  => isset($psp_settings['disable_wp_404_guess']) ? $psp_settings['disable_wp_404_guess'] : '',
			'checkbox_label' => esc_html__('', 'platinum-seo-pack'),
			'option_description' => esc_html__( 'Turn ON to disable WordPress 404 Canonical Redirect Guessing (Recommended).', 'platinum-seo-pack' ),
		);

		$psp_disable_wp_404_guess_id = 'psp_disable_wp_404_guess';		
		$psp_disable_wp_404_guess_title = esc_html__('Disable WP 404 Redirect Guessing: ', 'platinum-seo-pack').'<a href="https://techblissonline.com/wordpress-canonical-redirect-for-404-errors/" target="_blank" rel="noopener">'.'<br>'.esc_html__('How does this work?', 'platinum-seo-pack').'</a>';

		add_settings_field( $psp_disable_wp_404_guess_id, $psp_disable_wp_404_guess_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $psp_disable_wp_404_guess_field );
				
		//V2.0.8
		
		//404 Section
		$section_id = 'psp_404_section';		
		$section_title = esc_html__('404 Errors', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title,  array( &$this, 'section_404errors_desc' ), $this->psp_permalink_settings_group );
		
		//Fields
		
		$enable_404_field     = array (
            'label_for' 	=> 'psp_enable_404',
            'option_name'   => $psp_settings_name.'[enable_404]',
			'option_value'  => isset($psp_settings['enable_404']) ? $psp_settings['enable_404'] : '',
			'checkbox_label' => esc_html__('', 'platinum-seo-pack'),
			'option_description' => esc_html__( 'Turn ON to monitor 404 errors using Platinum SEO.', 'platinum-seo-pack' ),
        );
		
		$psp_enable_404_field_id = 'psp_enable_404';
		$psp_enable_404_field_title = esc_html__('Track 404 errors: ', 'platinum-seo-pack');
		
		add_settings_field( $psp_enable_404_field_id, $psp_enable_404_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $enable_404_field );
		
		$referrer_404_field     = array (
            'label_for' 	=> 'psp_referrer_404',
            'option_name'   => $psp_settings_name.'[referrer_404]',
			'option_value'  => isset($psp_settings['referrer_404']) ? $psp_settings['referrer_404'] : '',
			'checkbox_label' => esc_html__('', 'platinum-seo-pack'),
			'option_description' => esc_html__( 'Turn ON to track only 404 errors that occur due to a broken link reference from the site linking to your site.', 'platinum-seo-pack' ),
        );
		
		$psp_referrer_404_field_id = 'psp_referrer_404';
		$psp_referrer_404_field_title = esc_html__('Log 404s with referrers only: ', 'platinum-seo-pack');
		
		//add_settings_field( $psp_referrer_404_field_id, $psp_referrer_404_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $referrer_404_field );
		
		$bots_404_field     = array (
            'label_for' 	=> 'psp_bots_404',
            'option_name'   => $psp_settings_name.'[bots_404]',
			'option_value'  => isset($psp_settings['bots_404']) ? $psp_settings['bots_404'] : '',
			'checkbox_label' => esc_html__('', 'platinum-seo-pack'),
			'option_description' => esc_html__( 'Turn ON to log 404/410 errors encountered on your site by Search Engine Bots only - Eg. Googlebot and Bingbot (Recommended).', 'platinum-seo-pack' ),
        );
		
		$psp_bots_404_field_id = 'psp_bots_404';
		//$psp_bots_404_field_title = esc_html__('Log errors for Search Engine Bots only: ', 'platinum-seo-pack');
		$psp_bots_404_field_title = esc_html__('Log errors for Search Engine Bots only: ', 'platinum-seo-pack').'<a href="https://techblissonline.com/http-404-error/#fix-404-errors" target="_blank" rel="noopener">'.'<br>'.esc_html__('How does this work?', 'platinum-seo-pack').'</a>';
		
		add_settings_field( $psp_bots_404_field_id, $psp_bots_404_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $bots_404_field );
		
		add_settings_field( $psp_referrer_404_field_id, $psp_referrer_404_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $referrer_404_field );
		
		$psp_404_limit_field     = array (
            'label_for' 	=> 'psp_404_limit',
            'option_name'   => $psp_settings_name.'[limit_404]',
			'option_value'  => isset($psp_settings['limit_404']) ? $psp_settings['limit_404'] : '',
			'option_label' => esc_html__( 'Rows', 'platinum-seo-pack' ),
			'option_description' => esc_html__( 'Set the max number of entries in 404 log.(Highly Recommended)', 'platinum-seo-pack' ),
        );
		
		$psp_404_limit_field_id = 'psp_404_limit';
		$psp_404_limit_field_title = esc_html__('Limit 404 Log to: ', 'platinum-seo-pack');
		
		add_settings_field( $psp_404_limit_field_id, $psp_404_limit_field_title, array( &$this, 'psp_add_field_text_number' ), $this->psp_permalink_settings_group, $section_id, $psp_404_limit_field);
		
		//Section
		$section_id = 'psp_permalink_section';		
		$section_title = esc_html__('Permalink Structure for Taxonomies', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_permalinks_desc' ), $this->psp_permalink_settings_group );
		
		//Fields
		
		$category_nobase_field     = array (
            'label_for' 	=> 'psp_category_nobase',
            'option_name'   => $psp_settings_name.'[category]',
			'option_value'  => isset($psp_settings['category']) ? $psp_settings['category'] : '',
			'checkbox_label' => esc_html__('Remove Base', 'platinum-seo-pack')
        );
		
		$category_nobase_field_id = 'psp_category_nobase';
		$category_nobase_field_title = esc_html__('Category: ', 'platinum-seo-pack');
		
		add_settings_field( $category_nobase_field_id, $category_nobase_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $category_nobase_field );
		
		$cust_taxonomies = $this->custom_taxonomies;
		
		foreach($cust_taxonomies as $cust_taxonomy) {
		
			$tax_nobase_field     = array (
				'label_for' 	=> 'psp_'.$cust_taxonomy.'_nobase',
				'option_name'   => $psp_settings_name.'['.$cust_taxonomy.']',
				'option_value'  => isset($psp_settings[$cust_taxonomy]) ? $psp_settings[$cust_taxonomy] : '',
				'checkbox_label' => esc_html__('Remove Base', 'platinum-seo-pack')
			);
			
			$tax_nobase_field_id = 'psp_'.$cust_taxonomy.'_nobase';
			$tax_nobase_field_title = $cust_taxonomy.' :';
			
			add_settings_field( $tax_nobase_field_id, $tax_nobase_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_permalink_settings_group, $section_id, $tax_nobase_field );

		}
	}
	
	function sanitize_permalink_settings($settings) {
	    
	    if( ! empty( $settings['wizard'] ) ) {
			
			return $settings;
			
		}

		if( isset( $settings['redirection'] ) ) {
			$settings['redirection'] = !is_null(filter_var($settings['redirection'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['redirection'] : '';
		}
		
		if( isset( $settings['auto_redirection'] ) ) {
			$settings['auto_redirection'] = !is_null(filter_var($settings['auto_redirection'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['auto_redirection'] : '';
		}
		
		if ( isset( $settings['limit_301'] ) ) {
			$settings['limit_301'] = sanitize_text_field( $settings['limit_301'] );
			if (!filter_var($settings['limit_301'], FILTER_VALIDATE_INT) ) {
				$settings['limit_301'] = '';
			}			
		}
		
		//V2.0.8
		if( isset( $settings['disable_wp_404_guess'] ) ) {
			$settings['disable_wp_404_guess'] = !is_null(filter_var($settings['disable_wp_404_guess'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['disable_wp_404_guess'] : '';
		}
		//V2.0.8
		
		if( isset( $settings['enable_404'] ) ) {
			$settings['enable_404'] = !is_null(filter_var($settings['enable_404'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['enable_404'] : '';
		}
		
		if( isset( $settings['referrer_404'] ) ) {
			$settings['referrer_404'] = !is_null(filter_var($settings['referrer_404'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['referrer_404'] : '';
		}
		
		if( isset( $settings['bots_404'] ) ) {
			$settings['bots_404'] = !is_null(filter_var($settings['bots_404'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['bots_404'] : '';
		}
		
		if ( isset( $settings['limit_404'] ) ) {
			$settings['limit_404'] = sanitize_text_field( $settings['limit_404'] );
			if (!filter_var($settings['limit_404'], FILTER_VALIDATE_INT) ) {
				$settings['limit_404'] = '';
			}			
		}

    	if ( isset( $settings['category'] ) ) {
    		$settings['category'] = !is_null(filter_var($settings['category'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['category'] : '';	
    	} else {
    	    $settings['category'] = "";
    	}		
    	
    	$custom_tax = array();
    	if (!empty($this->custom_taxonomies)) {
    		$custom_tax = $this->custom_taxonomies;
    	}
    	
    	if (!empty($custom_tax)) {
    	
    		foreach($custom_tax as $custom_taxonomy) {
    		
    			if ( isset( $settings[$custom_taxonomy] ) ) {
    				$settings[$custom_taxonomy] = !is_null(filter_var($settings[$custom_taxonomy],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings[$custom_taxonomy] : '';	
    			}
    		
    		}
    	}		
    		
    	return $settings;
    }
	
	/*
	 * Registers the knowledge graph settings
	 */
	private function register_kg_settings() {
		$this->psp_settings_tabs[$this->psp_other_settings_group] = 'Others';		
		$psp_settings_name = "psp_other_settings";		
		
		$psp_settings = get_option($psp_settings_name);	
		 global $pagenow;
		 //$psp_pages = array('platinum-seo-social-pack-by-techblissonline', 'psp-social-by-techblissonline', 'psp-tools-by-techblissonline', 'pspp-licenses','wizard');
		 $psp_pages = array('platinum-seo-social-pack-by-techblissonline', 'psp-social-by-techblissonline', 'psp-tools-by-techblissonline', 'pspp-licenses', 'wizard');
		if ( $pagenow == 'admin.php' && in_array(sanitize_key($_GET['page']), $psp_pages))  {
            wp_enqueue_media();	
            wp_enqueue_script( 'psp-bs-toggler-js', plugins_url( '/js/pspbstoggler.js', __FILE__ ) );
             wp_enqueue_style("'psp-bs-toggler-css", plugins_url( '/css/psp-bs-toggle.css', __FILE__ ));
            //wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
            //wp_enqueue_style("psp-settings-bswide-css", plugins_url( '/css/psp-settings-bswide.css', __FILE__ ));
		    //wp_enqueue_style("psp-htmlsettings-css", plugins_url( '/css/psp-html-settings.css', __FILE__ ));
            
		}
		
		//enqueue javascript	
        //wp_enqueue_media();		
		//wp_enqueue_script( 'psp-image-uploader', plugins_url( '/js/pspmediauploader.js', __FILE__ ), array( 'jquery' ) );	
		//wp_enqueue_script( 'psp-social', plugins_url( '/js/pspsocialhandler.js', __FILE__ ), array( 'jquery' ) );
		
		register_setting( $this->psp_other_settings_group, $psp_settings_name, array( &$this, 'sanitize_kg_settings' ) );
		
		//Schema settings Section
		$section_id = 'psp_schema_section';		
		$section_title = esc_html__('Settings for generating Json-LD Schemas', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_schema_desc' ), $this->psp_other_settings_group );
		
		//Website name Section
		$section_id = 'psp_sitename_section';		
		$section_title = esc_html__('Site name for Google', 'platinum-seo-pack');
		
		//add_settings_section( $section_id, $section_title, array( &$this, 'section_empty_desc' ), $this->psp_other_settings_group );
		
		//Fields
	
		//sitelinks searchbox Section
		$section_id = 'psp_sitelinks_search_section';		
		$section_title =  esc_html__( 'Sitelinks Search Box in Google', 'platinum-seo-pack' );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_sitelinks_searchbox_desc' ), $this->psp_other_settings_group );
		
		//Enable sitelinks searchbox
		$sitelinks_search_field     = array (
			'label_for' 	=> 'psp_sitelinks_search_enabler',
			'option_name'   => $psp_settings_name.'[sitelinks_search_box]',
			'option_value'  => isset($psp_settings['sitelinks_search_box']) ? $psp_settings['sitelinks_search_box'] : '',
			'checkbox_label' => esc_html__( 'Enable', 'platinum-seo-pack' ),			
		);			
			
		$sitelinks_search_id = 'psp_sitelinks_search_enabler';		
		$sitelinks_search_title = esc_html__( 'Sitelinks Searchbox: ', 'platinum-seo-pack' );	
		
		add_settings_field( $sitelinks_search_id, $sitelinks_search_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_other_settings_group, $section_id, $sitelinks_search_field );
		
		//Sitelinks search box URL parameter		
		$psp_sitelinks_target_field     = array (
            'label_for' 	=> 'psp_sitelinks_searchbox_target',
            'option_name'   => $psp_settings_name.'[sitelinks_searchbox_target]',
			'option_value'  => isset($psp_settings['sitelinks_searchbox_target']) ? esc_url($psp_settings['sitelinks_searchbox_target']) : '',
			'option_description' => esc_html__( 'Here you can specify a search URL pattern for sending queries to your site search engine. You need to change this only if the URL is different from what is defined above. For most wordpress sites, leaving this unchanged would work.', 'platinum-seo-pack' ),
        );
		
		$psp_sitelinks_target_field_id = 'psp_sitelinks_searchbox_target';	
		$psp_sitelinks_target_field_title = esc_html__( 'Sitelinks Search box Target URL: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_sitelinks_target_field_id, $psp_sitelinks_target_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_other_settings_group, $section_id,  $psp_sitelinks_target_field);
		
		//Knowledge Graph Section
		$section_id = 'psp_kg_section';		
		$section_title = esc_html__('Knowledge Graph Settings for Google Search', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_kg_desc' ), $this->psp_other_settings_group );
		
		//Fields
		
		$kg_tags_field     = array (
            'label_for' 	=> 'psp_kg_tags_enabled',
            'option_name'   => $psp_settings_name.'[psp_kg_tags_enabled]',
			'option_value'  => isset($psp_settings['psp_kg_tags_enabled']) ? $psp_settings['psp_kg_tags_enabled'] : '',
			'checkbox_label' => esc_html__('Enable Knowledge Graph Tags for Google', 'platinum-seo-pack')
        );
		
		$kg_tags_field_id = 'psp_kg_tags_enabled';
		$kg_tags_field_title = esc_html__('Knowledge Graph Tags: ', 'platinum-seo-pack');
		
		add_settings_field( $kg_tags_field_id, $kg_tags_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_other_settings_group, $section_id, $kg_tags_field );		
		
		//Google Knowledge Graph type
		$psp_kg_profile_types = array ('' => 'Select a Profile', 'person' => 'Person', 'organization' => 'organization');
		
		$psp_kg_profile_type_field     = array (
            'label_for' 	=> 'psp_kg_profile_type',
            'option_name'   => $psp_settings_name.'[kg_profile_type]',
			'option_value'  => isset($psp_settings['kg_profile_type']) ? $psp_settings['kg_profile_type'] : '',
			'dditems'  => $psp_kg_profile_types,
			'option_description' => esc_html__( 'Select a profile type to be used for Knowledge Graph. For complete reference of knowledge graph profile types refer ', 'platinum-seo-pack' ).'<a href="https://developers.google.com/structured-data/customize/social-profiles" target="_blank">Google</a>',
        );
		
		$psp_kg_profile_type_field_id = 'psp_kg_profile_type';	
		$psp_kg_profile_type_field_title = esc_html__( 'Profile Type for Knowledge Graph: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_profile_type_field_id, $psp_kg_profile_type_field_title, array( &$this, 'psp_add_field_dropdown' ), $this->psp_other_settings_group, $section_id,  $psp_kg_profile_type_field);
		
		//KG profile name
		$psp_kg_profile_name_field     = array (
            'label_for' 	=> 'psp_social_kg_profile_name',
            'option_name'   => $psp_settings_name.'[kg_profile_name]',
			'option_value'  => isset($psp_settings['kg_profile_name']) ? esc_attr($psp_settings['kg_profile_name']) : '',
			'option_description' => esc_html__( 'Enter the name of the organization/person to be used on knowledge graph for this domain. For eg: "Tehblissonline" is the organization name used for the site http://techblissonline.com/.', 'platinum-seo-pack' ),
        );
		
		$psp_kg_profile_name_field_id = 'psp_social_kg_profile_name';	
		$psp_kg_profile_name_field_title = esc_html__( 'Name: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_profile_name_field_id, $psp_kg_profile_name_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_other_settings_group, $section_id,  $psp_kg_profile_name_field);
		
		//Logo
		$psp_kg_logo_field     = array (
            'label_for' 	=> 'psp_kg_logo',
			'class_for_row' => 'psp-kg-organization',
            'option_name'   => $psp_settings_name.'[kg_logo]',
			'option_value'  => isset($psp_settings['kg_logo']) ? esc_url($psp_settings['kg_logo']) : '',
			'option_description' => esc_html__( 'Enter the URL to the company/organization logo or upload a logo to use on Knowledge Graph', 'platinum-seo-pack' ),
			'button' 	=> 1,
        );
		
		$psp_kg_logo_field_id = 'psp_kg_logo';	
		$psp_kg_logo_field_title = esc_html__( 'Company/Organization logo to use on knowledge graph: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_logo_field_id, $psp_kg_logo_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_other_settings_group, $section_id,  $psp_kg_logo_field);
		
		//Knowledge Graph social Profiles Section
		$section_id = 'psp_kg_social_section';		
		$section_title = esc_html__('Social Profiles', 'platinum-seo-pack');
		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_kg_sp_desc' ), $this->psp_other_settings_group );
		
		//Facebook profile
		$psp_kg_fb_profile_field     = array (
            'label_for' 	=> 'psp_social_kg_facebook',
            'option_name'   => $psp_settings_name.'[kg_fb_profile]',
			'option_value'  => isset($psp_settings['kg_fb_profile']) ? esc_url($psp_settings['kg_fb_profile']) : '',
			'option_description' => esc_html__( 'Enter the facebook profile URL to be associated with this domain/site.', 'platinum-seo-pack' ),
        );
		
		$psp_kg_fb_profile_field_id = 'psp_social_kg_facebook';	
		$psp_kg_fb_profile_field_title = esc_html__( 'Facebook profile URL: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_fb_profile_field_id, $psp_kg_fb_profile_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_other_settings_group, $section_id,  $psp_kg_fb_profile_field);

		//Twitter profile
		$psp_kg_tw_profile_field     = array (
            'label_for' 	=> 'psp_social_kg_twitter',
            'option_name'   => $psp_settings_name.'[kg_tw_profile]',
			'option_value'  => isset($psp_settings['kg_tw_profile']) ? esc_url($psp_settings['kg_tw_profile']) : '',
			'option_description' => esc_html__( 'Enter the twitter profile URL to be associated with this domain/site.', 'platinum-seo-pack' ),
        );
		
		$psp_kg_tw_profile_field_id = 'psp_social_kg_twitter';	
		$psp_kg_tw_profile_field_title = esc_html__( 'Twitter profile URL: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_tw_profile_field_id, $psp_kg_tw_profile_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_other_settings_group, $section_id,  $psp_kg_tw_profile_field);
		
		//Google+ profile
		$psp_kg_go_profile_field     = array (
            'label_for' 	=> 'psp_social_kg_google',
            'option_name'   => $psp_settings_name.'[kg_go_profile]',
			'option_value'  => isset($psp_settings['kg_go_profile']) ? esc_url($psp_settings['kg_go_profile']) : '',
			'option_description' => esc_html__( 'Enter the google+ profile URL to be associated with this domain/site.', 'platinum-seo-pack' ),
        );
		
		$psp_kg_go_profile_field_id = 'psp_social_kg_google';	
		$psp_kg_go_profile_field_title = esc_html__( 'Google+ profile URL: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_go_profile_field_id, $psp_kg_go_profile_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_other_settings_group, $section_id,  $psp_kg_go_profile_field);
		
		//Instagram profile
		$psp_kg_ig_profile_field     = array (
            'label_for' 	=> 'psp_social_kg_instagram',
            'option_name'   => $psp_settings_name.'[kg_ig_profile]',
			'option_value'  => isset($psp_settings['kg_ig_profile']) ? esc_url($psp_settings['kg_ig_profile']) : '',
			'option_description' => esc_html__( 'Enter the instagram profile URL to be associated with this domain/site.', 'platinum-seo-pack' ),
        );
		
		$psp_kg_ig_profile_field_id = 'psp_social_kg_instagram';	
		$psp_kg_ig_profile_field_title = esc_html__( 'Instagram profile URL: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_ig_profile_field_id, $psp_kg_ig_profile_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_other_settings_group, $section_id,  $psp_kg_ig_profile_field);
		
		//LinkedIn profile
		$psp_kg_li_profile_field     = array (
            'label_for' 	=> 'psp_social_kg_linkedin',
            'option_name'   => $psp_settings_name.'[kg_li_profile]',
			'option_value'  => isset($psp_settings['kg_li_profile']) ? esc_url($psp_settings['kg_li_profile']) : '',
			'option_description' => esc_html__( 'Enter the linkedin profile URL to be associated with this domain/site.', 'platinum-seo-pack' ),
        );
		
		$psp_kg_li_profile_field_id = 'psp_social_kg_linkedin';	
		$psp_kg_li_profile_field_title = esc_html__( 'LinkedIn profile URL: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_li_profile_field_id, $psp_kg_li_profile_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_other_settings_group, $section_id,  $psp_kg_li_profile_field);
		
		//Youtube profile
		$psp_kg_yt_profile_field     = array (
            'label_for' 	=> 'psp_social_kg_youtube',
            'option_name'   => $psp_settings_name.'[kg_yt_profile]',
			'option_value'  => isset($psp_settings['kg_yt_profile']) ? esc_url($psp_settings['kg_yt_profile']) : '',
			'option_description' => esc_html__( 'Enter the youtube profile URL to be associated with this domain/site.', 'platinum-seo-pack' ),
        );
		
		$psp_kg_yt_profile_field_id = 'psp_social_kg_youtube';	
		$psp_kg_yt_profile_field_title = esc_html__( 'Youtube profile URL: ', 'platinum-seo-pack' );	
		
		add_settings_field( $psp_kg_yt_profile_field_id, $psp_kg_yt_profile_field_title, array( &$this, 'psp_add_field_text_url' ), $this->psp_other_settings_group, $section_id,  $psp_kg_yt_profile_field);	

		//Knowledge Graph social Profiles Section
		//$section_id = 'psp_kg_logo_section';		
		//$section_title = __('Logo (required only for KG profile type organization)', 'platinum-seo-pack');
		
		//add_settings_section( $section_id, $section_title, array( &$this, 'section_kg_logo_desc' ), $this->psp_other_settings_group );
		
	}
	
	/*
	* Sanitize KG Settings
	*/
	function sanitize_kg_settings($settings) {		
		
		if ( isset( $settings['sitelinks_search_box'] ) ) {
			$settings['sitelinks_search_box'] = !is_null(filter_var($settings['sitelinks_search_box'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['sitelinks_search_box'] : '';	
		}	
		
		if( isset( $settings['sitelinks_searchbox_target'] ) ) $settings['sitelinks_searchbox_target'] = esc_url_raw( $settings['sitelinks_searchbox_target'] );
		
		if ( isset( $settings['psp_kg_tags_enabled'] ) ) {
			$settings['psp_kg_tags_enabled'] = !is_null(filter_var($settings['psp_kg_tags_enabled'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['psp_kg_tags_enabled'] : '';	
		}
		
		if ( isset( $settings['kg_profile_type'] ) ) {
			$settings['kg_profile_type'] = sanitize_text_field( $settings['kg_profile_type'] );
			
			$kg_profiles = array("person", "organization");
			
			if (!in_array($settings['kg_profile_type'], $kg_profiles)) {
				$settings['kg_profile_type'] = '';
			}			
		}
		
		if( isset( $settings['kg_profile_name'] ) ) $settings['kg_profile_name'] = sanitize_text_field( $settings['kg_profile_name'] );
		
		$psp_allowed_protocols = array('http','https');
		
		if( isset( $settings['kg_fb_profile'] ) ) $settings['kg_fb_profile'] = esc_url_raw( $settings['kg_fb_profile'], $psp_allowed_protocols );
		if( isset( $settings['kg_tw_profile'] ) ) $settings['kg_tw_profile'] = esc_url_raw( $settings['kg_tw_profile'], $psp_allowed_protocols );
		if( isset( $settings['kg_go_profile'] ) ) $settings['kg_go_profile'] = esc_url_raw( $settings['kg_go_profile'], $psp_allowed_protocols );
		if( isset( $settings['kg_li_profile'] ) ) $settings['kg_li_profile'] = esc_url_raw( $settings['kg_li_profile'], $psp_allowed_protocols );
		if( isset( $settings['kg_yt_profile'] ) ) $settings['kg_yt_profile'] = esc_url_raw( $settings['kg_yt_profile'], $psp_allowed_protocols );
		if( isset( $settings['kg_logo'] ) ) $settings['kg_logo'] = esc_url_raw( $settings['kg_logo'], $psp_allowed_protocols );
		
		return $settings;		

	}
	
	/*
	 * Registers the Taxonomy SEO settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_taxonomy_settings($tax_name) {
		$this->psp_settings_tabs[$this->psp_taxonomy_settings_group] = 'Taxonomies';		
		$psp_settings_name = "psp_".$tax_name."_settings";
		$tax_name_text = str_replace( "_", " ", $tax_name );
		$tax_name_text = ucwords($tax_name_text);
		
		$psp_settings = get_option($psp_settings_name);
		//$this->psp_settings_name = $psp_settings;
		
		$option_title_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Term name', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');

		
		$option_desc_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Term name', 'platinum-seo-pack').', '. '<code>%description%</code> - '.esc_html__('Term description', 'platinum-seo-pack').', '. '<code>%seo_description%</code> - '.esc_html__('Platinum SEO Description', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');

		
		//available tags array
		$availableTags = ['sep', 'seo_title', 'wp_title', 'site_name', 'site_description'];
		$availableDescTags = ['sep', 'seo_title', 'wp_title', 'description', 'seo_description', 'site_name', 'site_description'];
		
		if ($tax_name == "category") {
			$option_title_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Category name', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
		
			$option_desc_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Category name', 'platinum-seo-pack').', '. '<code>%description%</code> - '.esc_html__('Category description', 'platinum-seo-pack').', '. '<code>%seo_description%</code> - '.esc_html__('Platinum SEO Description', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
			
			//available tags array
		    //$availableTags = ['sep', 'seo_title', 'title', 'category_description', 'site_name', 'seo_description', 'site_description'];
		}
		
		if ($tax_name == "tag" || $tax_name == "title_tag") {
			$option_title_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Tag name', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
		
			$option_desc_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Tag name', 'platinum-seo-pack').', '. '<code>%description%</code> - '.esc_html__('Tag description', 'platinum-seo-pack').', '. '<code>%seo_description%</code> - '.esc_html__('Platinum SEO Description', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
			
			//available tags array
		   // $availableTags = ['sep', 'seo_title', 'title', 'tag_description', 'site_name', 'seo_description', 'site_description'];
		}
		
		if ($tax_name == "post_format") {
			$option_title_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Post Format name', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
		
			$option_desc_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Post Format name', 'platinum-seo-pack').', '. '<code>%description%</code> - '.esc_html__('Post Format description', 'platinum-seo-pack').', '. '<code>%seo_description%</code> - '.esc_html__('Platinum SEO Description', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
		}
		
		$title_field     = array (
            'label_for' 	=> 'psp_'.$tax_name.'_title',
            'option_name'   => $psp_settings_name.'[title]',
			'option_value'  => isset($psp_settings['title']) ? esc_attr($psp_settings['title']) : '',
			'option_description' => $option_title_formats,
			'class_name' => 'titletags',
			'psp_tags' => $availableTags,
        );
		
		$desc_field     = array (
            'label_for' 	=> 'psp_'.$tax_name.'_description',
            'option_name'   => $psp_settings_name.'[description]',
			'option_value'  => isset($psp_settings['description']) ? esc_attr($psp_settings['description']) : '',
			'option_description' => $option_desc_formats,
			'class_name' => 'titletags',
			'psp_tags' => $availableDescTags,
        );
		
		$robots_field     = array (
            'label_for' 	=> 'psp_'.$tax_name.'_robots',
            'option_name'   => $psp_settings_name.'[robots]',
			'option_value'  => isset($psp_settings['robots']) ? $psp_settings['robots'] : '',
			'checkbox_label' => '<code>noindex, follow</code>'
        );
		
		$metabox_field     = array (
            'label_for' 	=> 'psp_'.$tax_name.'_metabox',
            'option_name'   => $psp_settings_name.'[hide_metabox]',
			'option_value'  => isset($psp_settings['hide_metabox']) ? $psp_settings['hide_metabox'] : '',
			'checkbox_label' => esc_html__('Hide for all users other than Admin', 'platinum-seo-pack')
        );
		
		$section_id = 'psp_'.$tax_name.'_section';
		//$section_title = $tax_name_text.' Settings';
		$section_title = sprintf( esc_html__( '%s Settings', 'platinum-seo-pack' ), $tax_name_text );
		
		$title_field_id = 'psp_'.$tax_name.'_title';
		$desc_field_id = 'psp_'.$tax_name.'_desc';
		$robots_field_id = 'psp_'.$tax_name.'_robots';		
		$metabox_field_id = 'psp_'.$tax_name.'_metabox';
		
		//$title_field_title = 'Title Format: ';
		//$desc_field_title = 'Meta Description Format: ';
		//$robots_field_title = 'Meta Robots: ';		
		//$metabox_field_title = 'Hide '.$tax_name_text. ' Meta box: ';
		
		$title_field_title = esc_html__('Title Format: ', 'platinum-seo-pack');
		$desc_field_title = esc_html__('Meta Description Format: ','platinum-seo-pack');
		$robots_field_title = esc_html__('Meta Robots: ', 'platinum-seo-pack');		
		$metabox_field_title = sprintf( esc_html__( 'Hide %s Metabox:', 'platinum-seo-pack' ), $tax_name_text );
		
		//wp_register_script( 'psp-taxtags-js', plugins_url( '/js/psp_post_tags.js', __FILE__ ), array( 'jquery-ui-autocomplete', 'jquery' ) );
        //wp_enqueue_script('psp-taxtags-js');
        
        wp_register_script( 'psp-atags-js', plugins_url( '/js/atags.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script('psp-atags-js');
		
		register_setting( $this->psp_taxonomy_settings_group, $psp_settings_name, array( &$this, 'sanitize_taxonomy_settings' ) );
		//Section
		add_settings_section( $section_id, $section_title, array( &$this, 'section_taxonomy_desc' ), $this->psp_taxonomy_settings_group );
		//Fields
		add_settings_field( $title_field_id, $title_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_taxonomy_settings_group, $section_id,  $title_field);
		add_settings_field( $desc_field_id, $desc_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_taxonomy_settings_group, $section_id, $desc_field );
		add_settings_field( $robots_field_id, $robots_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_taxonomy_settings_group, $section_id, $robots_field );	
		add_settings_field( $metabox_field_id, $metabox_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_taxonomy_settings_group, $section_id, $metabox_field );
	}	
	
	function sanitize_taxonomy_settings($settings) {
	
    	//if( isset( $settings['title'] ) ) $settings['title'] = sanitize_text_field( $settings['title'] );
    	//if( isset( $settings['description'] ) ) $settings['description'] = sanitize_text_field( $settings['description'] );	
    	
    	if ( isset( $settings['title'] ) ) {
    		$settings['title'] = sanitize_text_field( $settings['title'] );
    		
    		$psp_title_format = explode(" ", $settings['title']);
    		$titleformats = array('%seo_title%', '%wp_title%', '%sep%', '%site_name%', '%site_description%');
    		if(!empty($psp_title_format)) {
    			if (count($psp_title_format) != count(array_intersect($psp_title_format, $titleformats))) {
    				$settings['title'] = '';
    			}
    		}
    	}
    
    	if ( isset( $settings['description'] ) ) {
    		$settings['description'] = sanitize_text_field( $settings['description'] );
    		
    		$psp_desc_format = explode(" ", $settings['description']);
    		$descformats = array('%seo_title%', '%wp_title%', '%sep%', '%description%', '%seo_description%', '%site_name%', '%site_description%');
    		if(!empty($psp_desc_format)) {
    			if (count($psp_desc_format) != count(array_intersect($psp_desc_format, $descformats))) {
    				$settings['description'] = '';
    			}
    		}
    	}	
    	
    	if ( isset( $settings['robots'] ) ) {
    		$settings['robots'] = !is_null(filter_var($settings['robots'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['robots'] : '';	
    	}
    	
    	if ( isset( $settings['hide_metabox'] ) ) {
    		$settings['hide_metabox'] = !is_null(filter_var($settings['hide_metabox'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_metabox'] : '';	
    	}	
    	
    	return $settings;
    }
	
	/*
	 * Registers the Taxonomy SEO settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_posttype_settings($posttype_name) {
		$this->psp_settings_tabs[$this->psp_posttype_settings_group] = 'Post Types';		
		$psp_settings_name = "psp_".$posttype_name."_settings";
		$posttype_name_text = str_replace( "_", " ", $posttype_name );
		$posttype_name_text = ucwords($posttype_name_text);
		
		$psp_settings = get_option($psp_settings_name);
		//$this->psp_settings_name = $psp_settings;
		
		//Taxonomies for breadcrumb tags
		//$builtin_taxonomies = array("category", "tag", "post_format");
		$builtin_taxonomies = array("category", "post_tag");
		$custom_taxonomies = $this->custom_taxonomies;
		$psp_all_taxonomies = array_merge((array)$builtin_taxonomies, (array)$custom_taxonomies);
		$psp_taxonomies = array_combine($psp_all_taxonomies, $psp_all_taxonomies);
		$default = array( "" => "Select a Taxonomy" );
		//$psp_taxonomies = array_merge($builtin_taxonomies, $psp_taxonomies);
		$psp_bc_taxonomies = array_merge((array)$default, (array)$psp_taxonomies);
		
		$option_title_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Wordpress Title', 'platinum-seo-pack').', '.'<code>%taxonomy%</code> - '.esc_html__('Taxonomy', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
		
		$option_desc_formats = '<code>%seo_title%</code> - '.esc_html__('Platinum SEO title', 'platinum-seo-pack').', '.'<code>%wp_title%</code> - '.esc_html__('Wordpress Title', 'platinum-seo-pack').', '.'<code>%seo_description%</code> - '.esc_html__('Platinum SEO Description', 'platinum-seo-pack').', '.'<code>%site_name%</code> - '.esc_html__('site name', 'platinum-seo-pack').', '. '<code>%site_description%</code> - '.esc_html__('site description', 'platinum-seo-pack').', '.'<code>%sep%</code> - '. esc_html__('Separator chosen in General Settings', 'platinum-seo-pack');
		
		//available tags array
		//$availableTags = ['seo_title', 'wp_title', 'category', 'site_name', 'site_description'];
		$availableTags = ['sep', 'seo_title', 'wp_title', 'taxonomy', 'site_name', 'site_description', 'currentyear'];
		
		if ($posttype_name == 'page') {
		    $availableTags = ['sep', 'seo_title', 'wp_title', 'site_name', 'site_description', 'currentyear'];
		}
		
		$title_field     = array (
            'label_for' 	=> 'psp_'.$posttype_name.'_title',
            'option_name'   => $psp_settings_name.'[title]',
			'option_value'  => isset($psp_settings['title']) ? esc_attr($psp_settings['title']) : '',
			'option_description' => $option_title_formats,
			'class_name' => 'titletags',
			'psp_tags' => $availableTags,
        );
        
        //available tags array
		//$availableTagsDesc = ['seo_title', 'wp_title', 'category', 'site_name', 'seo_description', 'site_description'];
		$availableTagsDesc = ['sep', 'seo_title', 'wp_title', 'site_name', 'seo_description', 'site_description', 'currentyear'];
		if ($posttype_name == 'page') {
		    $availableTagsDesc = ['sep', 'seo_title', 'wp_title', 'site_name', 'seo_description', 'site_description', 'currentyear'];
		}
		
		$desc_field     = array (
            'label_for' 	=> 'psp_'.$posttype_name.'_description',
            'option_name'   => $psp_settings_name.'[description]',
			'option_value'  => isset($psp_settings['description']) ? esc_attr($psp_settings['description']) : '',
			'option_description' => $option_desc_formats,
			'class_name' => 'titletags',
			'psp_tags' => $availableTagsDesc,
        );
        
        $psp_header_metas = isset($psp_settings['headers']) ? html_entity_decode(stripcslashes(esc_attr($psp_settings['headers']))) : '';
        
        //validate headers
		if( !empty( $psp_header_metas ) ) {
    	
    		$allowed_html = array(
    			'meta' => array(
    				'name' => array(),
    				'property' => array(),
    				'itemprop' => array(),
    				'content' => array(),
    			),    
    		);
    
    		$psp_header_metas = wp_kses($psp_header_metas, $allowed_html);
		}
		
		$additional_headers_field     = array (
            'label_for' 	=> 'psp_'.$posttype_name.'_additional_headers',
            'option_name'   => $psp_settings_name.'[headers]',
			'option_value'  => $psp_header_metas,
			'class_name'    => 'pspcodeeditor',
        );		
        
		
		$robots_field     = array (
            'label_for' 	=> 'psp_'.$posttype_name.'_robots',
            'option_name'   => $psp_settings_name.'[robots]',
			'option_value'  => isset($psp_settings['robots']) ? $psp_settings['robots'] : '',
			'checkbox_label' => '<code>noindex, follow</code>'
        );
		
		$metabox_field     = array (
            'label_for' 	=> 'psp_'.$posttype_name.'_metabox',
            'option_name'   => $psp_settings_name.'[hide_metabox]',
			'option_value'  => isset($psp_settings['hide_metabox']) ? $psp_settings['hide_metabox'] : '',
			'checkbox_label' => esc_html__('Hide for all users other than Admin', 'platinum-seo-pack')
        );
		
		$breadcrumb_tax_field     = array (
            'label_for' 	=> 'psp_'.$posttype_name.'_taxonomy',
            'option_name'   => $psp_settings_name.'[default_tax]',
			'option_value'  => isset($psp_settings['default_tax']) ? $psp_settings['default_tax'] : '',
			'dditems'  => $psp_bc_taxonomies,
			'option_description' => esc_html__( 'Select a default taxonomy to be used for this post type in breadcrumb trail. Make sure that all posts of this post type are tagged with terms falling under this taxonomy,', 'platinum-seo-pack' ),
        );
		
		$section_id = 'psp_'.$posttype_name.'_section';
		//$section_title = $posttype_name_text.' Settings';
		$section_title = sprintf( esc_html__( '%s Settings', 'platinum-seo-pack' ), $posttype_name_text );
		if ($posttype_name == "attachment") $section_title = sprintf( esc_html__( '%s (Media) Settings', 'platinum-seo-pack' ), $posttype_name_text );
		
		$title_field_id = 'psp_'.$posttype_name.'_title';
		$desc_field_id = 'psp_'.$posttype_name.'_desc';
		$header_field_id = 'psp_'.$posttype_name.'_header';
		$robots_field_id = 'psp_'.$posttype_name.'_robots';		
		$metabox_field_id = 'psp_'.$posttype_name.'_metabox';
		$breadcrumb_tax_field_id = 'psp_'.$posttype_name.'_taxonomy';
		
		//$title_field_title = 'Title Format: ';
		//$desc_field_title = 'Meta Description Format: ';
		//$header_field_title = 'Additional '.$posttype_name_text. ' Headers: ';
		//$robots_field_title = 'Meta Robots: ';		
		//$metabox_field_title = 'Hide '.$posttype_name_text. ' Meta box: ';
		
		$title_field_title = esc_html__('Title Format: ', 'platinum-seo-pack');
		$desc_field_title = esc_html__('Meta Description Format: ','platinum-seo-pack');
		$header_field_title = 'Additional '.$posttype_name_text. ' Headers: ';
		$robots_field_title = esc_html__('Meta Robots: ', 'platinum-seo-pack');			
		$metabox_field_title = sprintf( esc_html__( 'Hide %s Metabox:', 'platinum-seo-pack' ), $posttype_name_text );
		$breadcrumb_tax_field_title = esc_html__('Taxonomy for breadcrumb trail: ', 'platinum-seo-pack');
		
		//wp_register_script( 'psp-posttags-js', plugins_url( '/js/psp_post_tags.js', __FILE__ ), array('psp-tagit-js') );
        //wp_register_script( 'psp-posttags-js', plugins_url( '/js/psp_post_tags.js', __FILE__ ), array( 'jquery-ui-autocomplete', 'jquery' ) );
        //wp_enqueue_script('psp-posttags-js');
        
        wp_register_script( 'psp-atags-js', plugins_url( '/js/atags.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_enqueue_script('psp-atags-js');
		
		register_setting( $this->psp_posttype_settings_group, $psp_settings_name, array( &$this, 'sanitize_posttype_settings' ) );
		//Section
		add_settings_section( $section_id, $section_title, array( &$this, 'section_posttype_desc' ), $this->psp_posttype_settings_group );
		//Fields
		add_settings_field( $title_field_id, $title_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_posttype_settings_group, $section_id,  $title_field);
		add_settings_field( $desc_field_id, $desc_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_posttype_settings_group, $section_id, $desc_field );
		add_settings_field( $header_field_id, $header_field_title, array( &$this, 'psp_add_field_textarea' ), $this->psp_posttype_settings_group, $section_id, $additional_headers_field );
		add_settings_field( $robots_field_id, $robots_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_posttype_settings_group, $section_id, $robots_field );	
		add_settings_field( $metabox_field_id, $metabox_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_posttype_settings_group, $section_id, $metabox_field );
		add_settings_field( $breadcrumb_tax_field_id, $breadcrumb_tax_field_title, array( &$this, 'psp_add_field_dropdown' ), $this->psp_posttype_settings_group, $section_id, $breadcrumb_tax_field );
	}
	
	function sanitize_posttype_settings($settings) {
	
    	//if( isset( $settings['title'] ) ) $settings['title'] = sanitize_text_field( $settings['title'] );
    	//if( isset( $settings['description'] ) ) $settings['description'] = sanitize_text_field( $settings['description'] );	
    	
    	if ( isset( $settings['title'] ) ) {
    		$settings['title'] = sanitize_text_field( $settings['title'] );
    		
    		$psp_title_format = explode(" ", $settings['title']);
    		$titleformats = array('%seo_title%', '%wp_title%', '%sep%', '%site_name%', '%site_description%', '%currentyear%');
    		if(!empty($psp_title_format)) {
    			if (count($psp_title_format) != count(array_intersect($psp_title_format, $titleformats))) {
    				$settings['title'] = '';
    			}
    		}
    	}
    
    	if ( isset( $settings['description'] ) ) {
    		$settings['description'] = sanitize_text_field( $settings['description'] );
    		
    		$psp_desc_format = explode(" ", $settings['description']);
    		$descformats = array('%seo_title%', '%wp_title%', '%sep%','%taxonomy%', '%seo_description%', '%site_name%', '%site_description%', '%currentyear%');
    		if(!empty($psp_desc_format)) {
    			if (count($psp_desc_format) != count(array_intersect($psp_desc_format, $descformats))) {
    				$settings['description'] = '';
    			}
    		}
    	}
    	//validate headers
    	if( isset( $settings['headers'] ) ) {
    	
    		$allowed_html = array(
    			'meta' => array(
    				'name' => array(),
    				'property' => array(),
    				'itemprop' => array(),
    				'content' => array(),
    			),    
    		);
    
    		$settings['headers'] = wp_kses($settings['headers'], $allowed_html);
    		$settings['headers'] = sanitize_textarea_field( htmlentities($settings['headers']) );
    	};
    	
    	if ( isset( $settings['robots'] ) ) {
    		$settings['robots'] = !is_null(filter_var($settings['robots'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['robots'] : '';	
    	}
    	
    	if ( isset( $settings['hide_metabox'] ) ) {
    		$settings['hide_metabox'] = !is_null(filter_var($settings['hide_metabox'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['hide_metabox'] : '';	
    	}
    	
    	if ( isset( $settings['default_tax'] ) ) {
    		$settings['default_tax'] = sanitize_text_field( $settings['default_tax'] );
    		
    		$builtin_tax = array("category", "post_tag");
    		$custom_tax = array();
    		$psp_all_tax = array();
    		$custom_tax = $this->custom_taxonomies;
    		$psp_all_tax = array_merge((array)$builtin_tax, (array)$custom_tax);
    		if (!in_array($settings['default_tax'], $psp_all_tax)) {
    			$settings['default_tax'] = '';
    		}			
    	}		
    	
    	return $settings;
    }
	
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */	
	
	function section_sitewidemeta_desc() { esc_html_e('These settings are applied throughout the site wherever appropriate.', 'platinum-seo-pack'); }
	function section_separator_desc() { echo esc_html__('The Title separator can be used in all Title formats and Description formats by specifying the tag - ', 'platinum-seo-pack').' %sep%'; }
	function section_home_desc() { echo '<a href="'.home_url().'" target=_blank">'. esc_html__('Home page SEO settings', 'platinum-seo-pack').'</a> - '.esc_html__('Set the title and meta description tags used on home page of your site here.', 'platinum-seo-pack');}
	function section_taxonomy_desc() { esc_html_e('Set the title and description formats for some default and all custom taxonomies.', 'platinum-seo-pack'); }
	function section_posttype_desc() { esc_html_e('Set the title and description formats for some default and all custom post types.', 'platinum-seo-pack'); }
	function section_others_desc() { esc_html_e('Set the title format for search result pages.', 'platinum-seo-pack'); }
	function section_404_desc() { esc_html_e('Set the title format for 404 page.', 'platinum-seo-pack'); }	
	function section_nofollow_desc() { esc_html_e('These Nofollow settings are applied throughout the site wherever appropriate.', 'platinum-seo-pack'); }
	function section_permalinks_desc() { echo esc_html__('These settings, if checked, will remove the base from taxonomies like Category and other custom taxonomies, if any. If "Remove base" is chosen for Category then the corresponding base will be removed from the permalink structure for categories.', 'platinum-seo-pack'). ' i.e. the base word <code>Category</code> will be removed from your category URLs.'; }
	function section_redirections_desc() {echo esc_html__('Manage your Redirection ', 'platinum-seo-pack'). '<a id="404errors" href="'.get_admin_url(get_current_blog_id()).'admin.php?page=redirectionmanager">'.esc_html__('here ', 'platinum-seo-pack').'</a>'; }
	function section_404errors_desc() {echo esc_html__('Manage your 404 errors ', 'platinum-seo-pack'). '<a id="404errors" href="'.get_admin_url(get_current_blog_id()).'admin.php?page=manager404">'.esc_html__('here ', 'platinum-seo-pack').'</a>'; }
	function section_cleanup_head_desc() { echo esc_html__('Remove unwanted links from HTML', 'platinum-seo-pack'). ' <code>&lt;head&gt;&lt;&#47;head&gt;</code>'. 
  esc_html__('Many of these links might not be needed in the head section for most sites and removing these might help reduce page size and also improve crawlability of more imortant links. So you may choose to remove those that are not needed for you site.', 'platinum-seo-pack'); }
	function section_cleanup_comment_desc() { esc_html_e('Strip HTML and anchor tags embedded in comments. Note that these remove the HTML and links embedded in the comments and not the comment author links.', 'platinum-seo-pack'); }
	function section_sitelinks_searchbox_desc() { echo esc_html__('Here you can enable the markup for "Google Sitelinks Search Box" on your site frontpage. For more information on this refer', 'platinum-seo-pack').' <a href="https://developers.google.com/structured-data/slsb-overview" target="_blank">Enable Sitelinks Search Box - Structured Data &mdash; Google Developers</a>. '.esc_html__('However, you must remember that though you might chose to implement this markup by enabling this, google algorithms determine whether or not to show a sitelink search box in Google SERPS for any given domain.', 'platinum-seo-pack'); }
	function section_fb_desc() {echo '';}
	function section_twitter_desc() {echo ''; }
	function section_schema_org_desc() {echo ''; }
	function section_kg_desc() {echo ''; }
	function section_kg_sp_desc() {echo ''; }
	function section_kg_logo_desc() {echo ''; }
	function section_kg_contacts_desc() {echo ''; }	
    function section_empty_desc() {echo ''; }
	function section_archives_desc() {echo ''; }
    function section_breadcrumb_desc() { echo esc_html__('These breadcrumb settings are for displaying breadcrumbs on the Post. It is built on top of Justin Tadlock\'s @BreadcrumbTrail package. Place the code', 'platinum-seo-pack').' <code>&lt;?php if ( function_exists( \'psp_breadcrumb_trail\' ) ) { psp_breadcrumb_trail(); } ?&gt;</code> '.esc_html__(' in your theme\'s single.php, at an apppropriate location, to display the generated breadcrumb trail. You may add the breadcrumb Json-LD schema (along with other schemas) in the Techblissonline Platinum SEO metabox for the Post.', 'platinum-seo-pack');  }
	function section_schema_desc() {echo esc_html__('The following settings are not necessary if you had added these schemas in the Home page and/or Contacts Page JSON Schema Editor settings of this plugin.', 'platinum-seo-pack'). ' i.e. <br /> 1. '.esc_html__('Schema for enabling Sitelink Search Box in Google and', 'platinum-seo-pack').' <br /> 2. '. esc_html__('Schema for Knowledge Graph', 'platinum-seo-pack'); }
	
	/**Callback for number textfield **/	
	function psp_add_field_text_number(array $args) {
	
		$option_name   = isset($args['option_name']) ? $args['option_name'] : '';
		$id     = isset($args['label_for']) ? $args['label_for'] : '';
		$option_value     = isset($args['option_value']) ? $args['option_value'] : '';
		$option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		$option_label     = isset($args['option_label']) ? esc_html( $args['option_label'] ) : '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
		
		echo "<input id='".esc_attr($id)."' name='".esc_attr($option_name)."' style='width:20%' type='number' min='1'  maxlength='5' value='".esc_attr($option_value)."' /> ".$option_label."<br/><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";//<br /><span class='describe'>Describe title</span>";
				
	} 
	
	/*
	 * Callback for adding a textfield.
	 */
	function psp_add_field_text(array $args) {
	
		$option_name   = isset($args['option_name']) ? $args['option_name'] : '';
		$id     = isset($args['label_for']) ? $args['label_for'] : '';
		$option_value     = isset($args['option_value']) ? $args['option_value'] : '';
		$option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		$option_button     = isset($args['button']) ?  esc_attr($args['button']) : '';
		$class_name     = isset($args['class_name']) ? $args['class_name'] : '';
		$psp_tags = isset($args['psp_tags']) ? ( $args['psp_tags'] ) : '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
		
	if (!$option_button) {
			//printf( '<input id="%1$s" name="%2$s" style="width:99%%" type="text" value="%3$s" /><br /><p class="description">%4$s</p>', $id, $option_name, $option_value,$option_description );
			if ($class_name) {
			    echo "<input id='".esc_attr($id)."' name='".esc_attr($option_name)."' class='".esc_attr($class_name)."' style='width:99%' type='text' value='".esc_attr($option_value)."' readonly/><br/><p class='description'>".wp_kses(html_entity_decode($option_description),$desc_allowed_html)."</p>";//<br /><span class='describe'>Describe title</span>";
			    if ( ! empty( $psp_tags ) ) :	?>
                	<p><?php esc_html_e( 'Available tags:' , 'platinum-seo-pack'); ?></p>
                	<ul role="list">
                		<?php 
                		foreach ( $psp_tags as $tag ) {
                			?>
                			<li class="psp">
                				<button type="button" data-added="<?php echo esc_attr( $tag );  ?>" data-id="<?php echo esc_attr( $id );  ?>"
                						class="pspbutton button button-secondary">
                					<?php echo '%' . esc_attr( $tag ) . '%'; ?>
                				</button>
                			</li>
                			<?php
                		}
                		?>
                	</ul>
                <?php endif; 
			} else {
			    echo "<input id='".esc_attr($id)."' name='".esc_attr($option_name)."' style='width:99%' type='text' value='".esc_attr($option_value)."' /><br/><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";//<br /><span class='describe'>Describe title</span>";
			}
			    
		} else {
			echo "<input style='width:87%;' type='text' name='".esc_attr($option_name)."' id='".esc_attr($id)."' value='".esc_attr($option_value)."'><input style='font-size:small' class='upload_image_button' type='button' value='Upload' /><br/><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
		}
		//echo "<input id='".$this->psp_home_settings_key['title']."' name='".$this->psp_home_settings_key['title']."' type='text' value='".esc_attr( $this->psp_home_settings['title'] )."' />";			
	}
	
	/*
	* Callback for adding a textfield for adding URLs.
	 */
	function psp_add_field_text_url(array $args) {
	
		$option_name   = isset($args['option_name']) ? $args['option_name'] : '';
		$id     = isset($args['label_for']) ? $args['label_for'] : '';
		$option_value     = isset($args['option_value']) ? $args['option_value'] : '';
		$option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		$option_button     = isset($args['button']) ?  esc_attr($args['button']) : '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
		
		if (!$option_button) {
			//printf( '<input id="%1$s" name="%2$s" style="width:99%%" type="text" value="%3$s" /><br /><p class="description">%4$s</p>', $id, $option_name, $option_value,$option_description );
			echo "<input id='".esc_attr($id)."' name='".esc_attr($option_name)."' style='width:99%' type='text' value='".esc_url($option_value)."' /><br/><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";//<br /><span class='describe'>Describe title</span>";
		} else {
			echo "<input style='width:87%;' type='text' name='".esc_attr($option_name)."' id='".esc_attr($id)."' value='".esc_url($option_value)."'><input style='font-size:small' class='upload_image_button' type='button' value='Upload' /><br/><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
		}
				
	}
	
	/*
	 * Callback for adding a textarea.
	 */
	function psp_add_field_textarea(array $args) {
	
		$option_name   = isset($args['option_name']) ? $args['option_name'] : '';
		$id     = isset($args['label_for']) ? $args['label_for'] : '';
		$option_value     = isset($args['option_value']) ? html_entity_decode(esc_textarea( $args['option_value'] )) : '';
        $option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
        $class_name     = isset($args['class_name']) ?  $args['class_name'] : '';
         $parent_class_name     = isset($args['parent_classname']) ?  $args['parent_classname'] : '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
	
		if(!empty($class_name)) {
		    if(!empty($parent_class_name)) {
		        echo "<div class='".esc_attr($parent_class_name)."'><textarea id='".esc_attr($id)."' name='".esc_attr($option_name)."' class='".esc_attr($class_name)."' rows='3' style='width:99%' type='textarea'>{$option_value}</textarea></div><br><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
		    } else {
		        echo "<textarea id='".esc_attr($id)."' name='".esc_attr($option_name)."' class='".esc_attr($class_name)."' rows='3' style='width:99%' type='textarea'>{$option_value}</textarea><br><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
		    }
		} else {
		    if(!empty($parent_class_name)) {
		        echo "<div class='".esc_attr($parent_class_name)."'><textarea id='".esc_attr($id)."' name='".esc_attr($option_name)."' rows='3' style='width:99%' type='textarea'>{$option_value}</textarea><br><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
		    } else {
		        echo "<textarea id='".esc_attr($id)."' name='".esc_attr($option_name)."' rows='3' style='width:99%' type='textarea'>{$option_value}</textarea><br><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
		    }
		    
		}
		//echo "<textarea rows='4' id='".$this->psp_home_settings_key['description']."' name='".$this->psp_home_settings_key['description']."'>".stripcslashes($this->psp_home_settings['description'])."</textarea>";			
	}
	
	/*
	 * Callback for adding a checkbox.
	 */
	function psp_add_field_checkbox(array $args) {
	
		$option_name   = isset($args['option_name']) ? esc_attr($args['option_name']) : '';
		$id     = isset($args['label_for']) ? esc_attr($args['label_for']) : '';
		$option_value     = isset($args['option_value']) ? esc_attr( $args['option_value'] ) : '';
		//$option_value     = esc_attr( $args['option_value'] );
		$checkbox_label     = isset($args['checkbox_label']) ? esc_html($args['checkbox_label']) : '';
		$option_description     = isset($args['option_description']) ?  esc_html($args['option_description'])  : '';		
		$checked = '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
		if($option_value) { $checked = ' checked="checked" '; }
		echo "<div class='psp-bs'><input ".esc_attr($checked)." id='".esc_attr($id)."' name='".esc_attr($option_name)."' type='checkbox' data-toggle='toggle'/><span>&nbsp;</span><span for='".esc_attr($id)."'>".wp_kses(html_entity_decode($checkbox_label), $desc_allowed_html)."</span><br /><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p></div>";	
			
	}
	
	/*
	 * Callback for adding a dropdown.
	 */
	function psp_add_field_dropdown(array $args) {
	
		$option_name   = isset($args['option_name']) ? esc_attr($args['option_name']) : '';
		$id     = isset($args['label_for']) ? esc_attr($args['label_for']) : '';
		$option_value     = isset($args['option_value']) ? htmlentities( $args['option_value'], ENT_COMPAT, 'UTF-8', false ) : '';
		$dditems = isset($args['dditems']) ? $args['dditems'] : array();
		$option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
		

		//if($option_value) { $checked = ' checked="checked" '; }
		//echo "<input ".$checked." id='$id' name='$option_name' type='checkbox' /><label for='$id'>$checkbox_label</label><br /><p class='description'>$option_description</p>";	
		
		echo "<select id='".esc_attr($id)."' name='".esc_attr($option_name)."'>";
		/*foreach($dditems as $item) {
			$selected = ($option_value==$item) ? 'selected="selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}*/
		//echo "<option value disabled selected>Select an option</option>";
		//echo "<option value=""></option>";
		//while (list($key, $val) = each($dditems)) {
		foreach($dditems as $key => $val) {
			$selected = ($option_value==$key) ? 'selected="selected"' : '';
			echo "<option value='".esc_attr($key)."' ".esc_attr($selected).">".esc_attr($val)."</option>";
			//$selected = ($option_value==$val) ? 'selected="selected"' : '';
			//echo "<option value='$val' $selected>$key</option>";
		} 
		echo "</select><p class='description' for='".esc_attr($id)."'> ".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
			
	}
	
	/*
	 * Callback for adding radio buttons.
	 */
	function psp_add_field_radiobuttons(array $args) {
	
		$option_name   = isset($args['option_name']) ? esc_attr($args['option_name']) : '';
		$id     = isset($args['label_for']) ? esc_attr($args['label_for']) : '';
		$option_value     = isset($args['option_value']) ? htmlentities( $args['option_value'], ENT_COMPAT, 'UTF-8', false ) : '';
		$radioitems = isset($args['radioitems']) ? $args['radioitems'] : array();//array ('-', '֧, 'ק, 'ק, 'է, '*', '?', '|', '~', '˧, 'ۧ, '<', '>');
		$option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		$desc_allowed_html = array('br' => array(), 'code' => array(), 'strong' => array(), 'em' => array(), 'i' => array(), 'bold' => array(), 'a' => array('href' => array(), 'target' => array()));
		
		$counter = 1;

		echo "<div id='$id' class='psp-separator'>";
		
		//while (list($key, $val) = each($radioitems)) {
		foreach($radioitems as $key => $val) {
		
			$radio_id = $id."-radio-item-".$counter;
			$selected = ($option_value==$key) ? 'checked="checked"' : '';
			echo "<input id='".esc_attr($radio_id)."' ".esc_attr($selected)." type='radio' name='".esc_attr($option_name)."' value='".esc_attr($key)."' /><label class='psp-radio-separator' for='".esc_attr($radio_id)."'>".esc_attr($val)."</label>";
		
			$counter = $counter + 1;
		
		}
		
		/*foreach ( $radioitems as $radioitem ) {
		
			$radio_id = $id."-radio-item-".$counter;
			$selected = ($option_value==$radioitem) ? 'checked="checked"' : '';
			echo "<input id='$radio_id' $selected type='radio' name='$option_name' value='$radioitem' /><label class='psp-radio-separator' for='$radio_id'>$radioitem</label>";
		
			$counter = $counter + 1;
		
		}*/
		
		echo "</div><br /><p class='description'>".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
		
	}
	
	/*
	 * Called during admin_menu, adds an options
	 * page under Settings called My Settings, rendered
	 * using the plugin_options_page method.
	 */
	function add_admin_menus() {
		//add_options_page( 'Platinum SEO New Settings', 'My Settings', 'manage_options', $this->psp_plugin_options_key, array( &$this, 'psp_options_page' ) );
		if ($this->psp_helper->user_has_access('psp_general')) {
		    add_menu_page(esc_html__('Techblissonline Platinum SEO and social Pack', 'platinum-seo-pack'), esc_html__('Platinum SEO and Social Pack', 'platinum-seo-pack'), 'edit_posts', $this->psp_plugin_options_key, array($this, 'psp_options_page'), plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) )), 5);
		}
		//add_menu_page(esc_html__('Techblissonline Platinum SEO and social Pack', 'platinum-seo-pack'), esc_html__('Platinum SEO and Social Pack', 'platinum-seo-pack'), 'manage_options', $this->psp_plugin_options_key, array($this, 'psp_options_page'), plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) )));
		if ($this->psp_helper->user_has_access('psp_general')) {
		    add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO and social Pack', 'platinum-seo-pack'), '<span class="dashicons dashicons-admin-settings"></span> '.esc_html__('SEO', 'platinum-seo-pack'), 'edit_posts', $this->psp_plugin_options_key);
		}
		$psp_settings = get_option('psp_pre_setting');		
		$psp_premium_valid = isset($psp_settings['premium']) ? $psp_settings['premium'] : '';
		$psp_premium_status = isset($psp_settings['psp_premium_license_key_status']) ? $psp_settings['psp_premium_license_key_status'] : '';
		
		
		//$psp_premium_valid = 1;
		//$psp_premium_status = 1;
		//if ($psp_premium_valid && $psp_premium_status)
		if ($this->psp_helper->user_has_access('psp_social')) {
		    add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO Social', 'platinum-seo-pack'), '<span class="dashicons dashicons-admin-settings"></span> '.esc_html__('Social', 'platinum-seo-pack'), 'edit_posts', 'psp-social-by-techblissonline',  array($this->psp_social_instance, 'psp_social_options_page'));
		}
		//add_submenu_page($this->psp_plugin_options_key, __('Techblissonline Platinum SEO Premium Pack', 'platinum-seo-pack'), __('SEO - Advanced', 'platinum-seo-pack'), 'manage_options', 'webmastertools',  array($this->psp_wmt_instance, 'psp_wmt_options_page'));
		//add_submenu_page($this->psp_plugin_options_key, __('Techblissonline Platinum SEO Analytics', 'platinum-seo-pack'), __('SEO - Analytics', 'platinum-seo-pack'), 'manage_options', 'psp-gatracking-by-techblissonline', array($this->psp_ga_instance, 'psp_ga_options_page'));
		if ($this->psp_helper->user_has_access('psp_tools')) {
		    add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO Tools', 'platinum-seo-pack'), '<span class="dashicons dashicons-edit"></span> '.esc_html__('Tools', 'platinum-seo-pack'), 'edit_posts', 'psp-tools-by-techblissonline', array($this->psp_tools_instance, 'psp_tools_options_page'));
		}
		//add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO Premium Pack', 'platinum-seo-pack'), '<span class="dashicons dashicons-admin-tools"></span> '.esc_html__(' SEO Tools', 'platinum-seo-pack'), 'edit_posts', 'psp-seo-tools-by-techblissonline', array( &$this, 'psp_pre_tools_display_page'));
		if ($this->psp_helper->user_has_access('redirections')) {
		    $psp_redir_page = add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO Redirections', 'platinum-seo-pack'), '<span class="dashicons dashicons-randomize"></span> '.esc_html__('Redirections', 'platinum-seo-pack'), 'edit_posts', 'redirectionmanager', array($this->psp_redirect_instance, 'redir_mgmtpage'));
		}
		if ($this->psp_helper->user_has_access('manager404')) {
		    $psp_404_page = add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO 404 Manager', 'platinum-seo-pack'), '<span class="dashicons dashicons-editor-unlink"></span> '.esc_html__('Manage 404 Errors', 'platinum-seo-pack'), 'edit_posts', 'manager404', array($this->psp_redirect_instance, 'manage_404_page'));
		}
		add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO Premium Pack', 'platinum-seo-pack'), '<span class="dashicons dashicons-admin-network"></span> '.esc_html__('Role Manager', 'platinum-seo-pack'), 'manage_options', 'pspp-caps',  array($this->psp_rcap_instance, 'psp_rolecap_options_page'));
	    //add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO Premium Pack', 'platinum-seo-pack'), __('Techblissonline - SEO Tools', 'platinum-seo-pack'), 'manage_options', 'psp-seo-tools-by-techblissonline',  array($this->psp_pre_instance, 'psp_pre_options_page'));
	    if ($psp_premium_valid) add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO Premium Pack', 'platinum-seo-pack'), '<span class="dashicons dashicons-admin-network"></span> '.esc_html__('Premium - Licenses', 'platinum-seo-pack'), 'manage_options', 'pspp-licenses',  array($this->psp_pre_instance, 'psp_premium_options_page'));
	}	
	
	function psp_pre_tools_display_page() { 
        wp_enqueue_style("psp-settings-bswide-css", plugins_url( '/css/psp-settings-bswide.css', __FILE__ ));
        include_once( 'psp_tools_renderer.php' ); 
    }
	
	//add extra fields to category edit form callback function
	function psp_extra_category_fields( $cat_object ) {	
	
		//global $wp_scripts; 
		//wp_enqueue_style("jquery-ui-css", "http://ajax.googleapis.com/ajax/libs/jqueryui/{$wp_scripts->registered['jquery-ui-core']->ver}/themes/smoothness/jquery-ui.min.css");
		
		wp_enqueue_style("jquery-ui-css", plugins_url( '/css/jquery-ui-techblissonline.css', __FILE__ ));

		wp_enqueue_media();
		wp_enqueue_script( 'psp-meta-box', plugins_url( '/js/pspmetabox.js', __FILE__ ), array( 'jquery-ui-tabs') );
		//wp_enqueue_script( 'psp-image-uploader', plugins_url( '/js/pspmediauploader.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'psp-meta-box-snippet', plugins_url( '/js/snippetpreview.js', __FILE__ ),'', '2.3.6');
		//wp_enqueue_script( 'psp-social', plugins_url( '/js/pspsocialhandler.js', __FILE__ ), array( 'jquery' ) );  
		//wp_enqueue_script( 'psp-cm', plugins_url( '/js/cm.js', __FILE__ ), array(), false, true);
		
		$psp_cm_json_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'json', 'codemirror'=> array('autoRefresh' => true)));
        wp_localize_script('psp-meta-box', 'psp_cm_json_settings', $psp_cm_json_settings);
        
        $psp_cm_html_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'html', 'codemirror'=> array('autoRefresh' => true)));
        wp_localize_script('psp-meta-box', 'psp_cm_html_settings', $psp_cm_html_settings);
		
		$psp_taxonomy_metabox_hidden = false;
		$psp_taxonomy_option_value = array();
		
		$category_id = $cat_object->term_id;
		$taxonomy_name = $cat_object->taxonomy;
		$psp_taxonomy_option_name = "psp_".$taxonomy_name."_settings";
		$psp_taxonomy_option_value = get_option($psp_taxonomy_option_name);
		//if (isset($psp_taxonomy_option_value['hide_metabox']))
		$psp_taxonomy_metabox_hidden = isset($psp_taxonomy_option_value['hide_metabox']) ? $psp_taxonomy_option_value['hide_metabox'] : '';
		$psp_taxonomy_metabox_title = isset($psp_taxonomy_option_value['title']) ? $psp_taxonomy_option_value['title'] : '';
		$psp_taxonomy_metabox_description = isset($psp_taxonomy_option_value['description']) ? $psp_taxonomy_option_value['description'] : '';
		
		$psp_settings = get_option('psp_sitewide_settings');			
		$psp_metabox_advanced_hidden = isset($psp_settings['hide_metabox_advanced']) ? $psp_settings['hide_metabox_advanced'] : '';
		
		$psp_pre_settings = get_option('psp_pre_setting');
		$psp_premium_valid = isset($psp_pre_settings['premium']) && !empty($psp_pre_settings['premium']) ? $psp_pre_settings['premium'] : '';
		$psp_premium_status = isset($psp_pre_settings['psp_premium_license_key_status']) ? $psp_pre_settings['psp_premium_license_key_status'] : '';
		//$psp_premium_valid = 1;
		//$psp_premium_status = 1;
		
		if (is_super_admin()) $psp_taxonomy_metabox_hidden = false;
		
		if (!$psp_taxonomy_metabox_hidden) {
			//wp_nonce_field( 'do_psp_extra_category_fields', 'psp_extra_category_fields_nonce' );
			//$cat_meta = get_option( "psp_category_metas_$category_id");
		?>
			</table> 
			<?php wp_nonce_field( 'do_psp_extra_category_fields', 'psp_extra_category_fields_nonce' );
			$psp_type = "taxonomy";
			//available tags array
		    $pspavailableTags = ['sep', 'seo_title', 'wp_title', 'site_name', 'site_description'];
		    $pspavailableTagsDesc = ['sep', 'seo_title', 'wp_title', 'description', 'seo_description', 'site_name', 'site_description'];
			$psp_seo_meta = get_term_meta($category_id, "psp_category_seo_metas_$category_id");
			if($psp_seo_meta) $psp_seo_meta = $psp_seo_meta[0];
			if(!$psp_seo_meta) $psp_seo_meta = get_option( "psp_category_seo_metas_$category_id");			 
			$psp_seo_meta['titleformat'] = isset($psp_seo_meta['titleformat']) ? esc_attr($psp_seo_meta['titleformat']) : esc_attr($psp_taxonomy_metabox_title);
			$psp_seo_meta['descformat'] = isset($psp_seo_meta['descformat']) ? esc_attr($psp_seo_meta['descformat']) :'';
			
			//$psp_seo_meta['schema_string'] = isset($psp_seo_meta['schema_string']) ? html_entity_decode(stripcslashes($psp_seo_meta['schema_string'])) :'';
			$json_schema_string = isset($psp_seo_meta['schema_string']) ? html_entity_decode(stripcslashes(esc_attr($psp_seo_meta['schema_string']))) : '';
			//validate it is a json object
    		$schema_obj = json_decode($json_schema_string);
    		if($schema_obj === null) {
    		    $json_schema_string = 'Invalid JSON Schema';
    		}
    		$psp_seo_meta['schema_string'] = $json_schema_string;
    		
    		$psp_sm_settings = get_option('psp_sitemap');
    		$excluded_term_ids = array();		
    		$term_excluded_from_sitemap = '';
    		
    		if( $psp_sm_settings['excluded_term_ids']  ) {
    			$excluded_term_ids = explode(",", $psp_sm_settings['excluded_term_ids']); 
    		}
    		if( !empty( $excluded_term_ids ) ) {
    			$excluded_term_ids = array_map( 'intval', $excluded_term_ids ); 
    			
    			if( in_array($category_id, $excluded_term_ids)) {
    			    $term_excluded_from_sitemap = 'on';
    			}
    			
    		}
			//$psp_seo_meta['nositemap'] = $term_excluded_from_sitemap;
			$psp_seo_meta['nositemap'] = !empty($psp_seo_meta['nositemap']) ? esc_attr($psp_seo_meta['nositemap']) : $term_excluded_from_sitemap;
			
			//$psp_seo_meta = array_map( 'esc_attr', $psp_seo_meta );
			$this->psp_taxonomy_meta_original = $psp_seo_meta;
			$psp_social_meta = get_term_meta( $category_id, "psp_category_social_metas_$category_id");
			if ($psp_social_meta) $psp_social_meta = $psp_social_meta[0];
			if (!$psp_social_meta) $psp_social_meta = get_option( "psp_category_social_metas_$category_id");
			//$psp_social_meta = array_map( 'esc_attr', $psp_social_meta );
			$this->psp_taxonomy_social_meta_original = $psp_social_meta; ?>
			
			<h3><?php echo '<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />';  ?><?php esc_html_e(' Techblissonline Platinum SEO and Social Meta Box ', 'platinum-seo-pack'); ?></h3>
			<div class="pspmbox">
			<div class="psp-bs">
        		<ul class="text-right list-inline"><li class="social"><a href="#TB_inline?height=600&amp;width=750&amp;inlineId=psp-meta-box-parent" class="tbpsp thickbox " style="text-decoration:none;" title="Platinum SEO Preview"><span class="dashicons dashicons-search"></span><?php esc_html_e( ' ', 'platinum-seo-pack' ); ?></a></li>
        		    <li class="social"><a href="#TB_inline?height=600&amp;width=750&amp;inlineId=pspanalysispar" class="tbpsp thickbox " style="text-decoration:none;" title="Platinum SEO Analysis"><?php esc_html_e( ' ', 'platinum-seo-pack' ); ?><span class="dashicons dashicons-dashboard"></span></a></li>
        		</ul>
        	</div>
        	<div id="psp-meta-box-parent">
    			<div id="psp-meta-box">
    			<ul class="psp-metabox-tabs" id="psp-metabox-tabs">
    				<li class="basic"><a href="#basic" title="Generic SEO"><span class="dashicons dashicons-admin-generic"></span><?php esc_html_e( ' SEO', 'platinum-seo-pack' ); ?></a></li>
    				<?php if (!$psp_metabox_advanced_hidden || is_super_admin()) { ?>
    				<li class="analysis"><a href="#analysis" title="SEO Analysis"><span class="dashicons dashicons-dashboard"></span><?php esc_html_e( ' Analysis', 'platinum-seo-pack' ); ?></a></li>
    				<li class="advanced"><a href="#advanced" title="Advanced SEO"><span class="dashicons dashicons-admin-tools"></span><?php esc_html_e( ' Advanced', 'platinum-seo-pack' ); ?></a></li>
    				<li class="social"><a href="#bsocial" title="Basic Social"><span class="dashicons dashicons-share"></span><?php esc_html_e( '  Basic', 'platinum-seo-pack' ); ?></a></li>
    				<li class="social"><a href="#asocial" title="Advanced Social"><span class="dashicons dashicons-networking"></span><?php esc_html_e( ' Advanced', 'platinum-seo-pack' ); ?></a></li>
    				<?php } ?>				
    			</ul>
    			<br class="clear" />
    			<div id="basic" class="psptab">
    				<?php include_once( 'psp_basic_metabox_renderer.php' ); ?>	
    			</div>
    			<div id="analysis" class="psptab">
				    <?php include_once( 'psp_analysis_metabox_renderer.php' ); ?>	
			    </div>
    			<?php if (!$psp_metabox_advanced_hidden || is_super_admin()) { ?>
    			<div id="advanced" class="hidden psptab wrap">
    				<?php include_once( 'psp_advanced_metabox_renderer.php' ); ?>
    			</div>  			
    			<div id="bsocial" class="psptab">
    				<?php include_once( 'psp_basic_social_metabox_renderer.php' ); ?>	
    			</div>
    			<div id="asocial" class="psptab">
    				<?php 	if ($psp_premium_valid && $psp_premium_status) { 
    							$metabox_template = apply_filters('psp_metabox_template', 'psp_premiumad_metabox_renderer.php');
    							if (empty($metabox_template)) $metabox_template = 'psp_premiumad_metabox_renderer.php';
    							include_once( $metabox_template ); 
    							//include_once( 'psp_advanced_social_metabox_renderer.php' );
    						} else { ?>
    							<div class="psp-bs">
    						   <div class="container">
    						<?php 
    						    wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
    						    // wp_enqueue_style("psp-htmlsettings-css", plugins_url( '/css/psp-html-settings.css', __FILE__ ));
    							include_once( 'psp_premiumad_metabox_renderer.php' ); ?>
    							</div></div>
    			    <?php	}
    				?>		
    			</div>
    			<?php } ?>
    		</div>
		    </div>
		    </div>
	<?php
		}
	}
	
	// save extra category extra fields callback function
	function psp_save_extra_category_fields( $term_id ) {
	
		// Check if our nonce is set and is valid.
		if ( ! isset( $_POST['psp_extra_category_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key($_POST['psp_extra_category_fields_nonce']), 'do_psp_extra_category_fields' )) {
			return;
		}
		
		$t_id = $term_id;
		
		// Make sure that it is set.
		if ( ! isset( $_POST['psp_seo_meta'] ) && ! isset( $_POST['psp_social_meta'] ) ) {
			return;
		} else {
		    
		    $psp_settings = get_option('psp_sitewide_settings');			
            $psp_metabox_advanced_hidden = isset($psp_settings['hide_metabox_advanced']) ? $psp_settings['hide_metabox_advanced'] : '';
            
            if (is_super_admin()) $psp_metabox_advanced_hidden = false;
		
			if (!empty($this->psp_taxonomy_meta_original)) { 
				$psp_seo_data_original = $this->psp_taxonomy_meta_original;
			} else {
				$psp_seo_data_original = get_term_meta($t_id, "psp_category_seo_metas_$t_id");
				if($psp_seo_data_original) $psp_seo_data_original = $psp_seo_data_original[0];
				if(!$psp_seo_data_original) $psp_seo_data_original = get_option( "psp_category_seo_metas_$t_id");
			}
			
			if(!empty($this->psp_taxonomy_social_meta_original)) {
				$psp_social_data_original = $this->psp_taxonomy_social_meta_original;
			} else {
				//$psp_social_data_original = get_option( "psp_category_social_metas_$t_id");
			}
			
			$psp_seo_data_current = isset($_POST['psp_seo_meta']) ? $this->psp_sanitze_seo_data($_POST['psp_seo_meta']) : array();	
			
			if (isset($psp_seo_data_original) &&  !empty($psp_seo_data_original)) { 
			    $psp_category_seo_data = array_merge((array)$psp_seo_data_original, (array)$psp_seo_data_current);
			} else {
			    $psp_category_seo_data = $psp_seo_data_current;
			}
			
			$psp_social_data_current = isset($_POST['psp_social_meta']) ? $this->psp_sanitze_social_data($_POST['psp_social_meta']) : array();
			
			if (isset($psp_social_data_original) && !empty($psp_social_data_original)) {
			    $psp_category_social_data = array_merge((array)$psp_social_data_original, (array)$psp_social_data_current);
			} else {
			    $psp_category_social_data = $psp_social_data_current;
			}

			if (isset($psp_category_seo_data) && !empty($psp_category_seo_data)) {
			
				// Sanitize SEO data.
				//$psp_category_seo_data = $this->psp_sanitze_seo_data( $psp_category_seo_data );
				//save the option array
				update_term_meta($t_id, "psp_category_seo_metas_$t_id", $psp_category_seo_data);
				//update_option( "psp_category_seo_metas_$t_id", $psp_category_seo_data );
				
				//do not proceed further if only basic seo meta data had to be saved/
				if ($psp_metabox_advanced_hidden) {
				    return;
				}
				
				//update google sitemap generator
        		 if (!empty($psp_category_seo_data['nositemap'])) {
					 $this->psp_update_wps("excluded_term_ids", $t_id, true );
        		     $this->psp_update_gsg("sm_b_exclude_cats", $t_id, true );
					 $psp_exclude = true;
        		 } else {
					  $this->psp_update_wps("excluded_term_ids", $t_id, false );
        		      $this->psp_update_gsg("sm_b_exclude_cats", $t_id, false );
					  $psp_exclude = false;
        		 }
				 $psp_id = !empty($t_id) ? $t_id : '';
				//techblissonline_psp_update_sitemap - action hook to attach your function
				//$psp_id - Post ID or Term ID 
				//$psp_exclude - Boolean indicating whether to exclude (true) or include (false) this post in the sitemap
				do_action( 'techblissonline_psp_update_sitemap', $psp_id, $psp_exclude );
			
			}
			
			if (isset($psp_category_social_data) && !empty($psp_category_social_data)) {
			
				// Sanitize Social data.
				//$psp_category_social_data = $this->psp_sanitze_social_data( $psp_category_social_data );
				//save the option array
				//update_option( "psp_category_social_metas_$t_id", $psp_category_social_data );
				update_term_meta( $t_id, "psp_category_social_metas_$t_id", $psp_category_social_data );
			
			}
			
		}
	}
	
	//add extra fields to category edit form callback function
	function psp_extra_taxonomy_fields( $term_object ) {

		//global $wp_scripts; 
		//wp_enqueue_style("jquery-ui-css", "http://ajax.googleapis.com/ajax/libs/jqueryui/{$wp_scripts->registered['jquery-ui-core']->ver}/themes/smoothness/jquery-ui.min.css");
		
		wp_enqueue_style("jquery-ui-css", plugins_url( '/css/jquery-ui-techblissonline.css', __FILE__ ));

		//wp_enqueue_media();
		wp_enqueue_script( 'psp-meta-box', plugins_url( '/js/pspmetabox.js', __FILE__ ), array( 'jquery-ui-tabs') );
		//wp_enqueue_script( 'psp-image-uploader', plugins_url( '/js/pspmediauploader.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'psp-meta-box-snippet', plugins_url( '/js/snippetpreview.js', __FILE__ ), '', '2.3.6');
		//wp_enqueue_script( 'psp-social', plugins_url( '/js/pspsocialhandler.js', __FILE__ ), array( 'jquery' ) );
		
		$psp_cm_json_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'json', 'codemirror'=> array('autoRefresh' => true)));
        wp_localize_script('psp-meta-box', 'psp_cm_json_settings', $psp_cm_json_settings);
        
        $psp_cm_html_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'html', 'codemirror'=> array('autoRefresh' => true)));
        wp_localize_script('psp-meta-box', 'psp_cm_html_settings', $psp_cm_html_settings);
		
		$psp_taxonomy_metabox_hidden = false;
		$psp_taxonomy_option_value = array();
		
		$t_id = $term_object->term_id;
		//$term_id = $term_object->term_id;
		$taxonomy_name = $term_object->taxonomy;
		
		$psp_taxonomy_option_name = "psp_".$taxonomy_name."_settings";
		$psp_taxonomy_option_value = get_option($psp_taxonomy_option_name);
		//if (isset($psp_taxonomy_option_value['hide_metabox']))
		$psp_taxonomy_metabox_hidden = isset($psp_taxonomy_option_value['hide_metabox']) ? $psp_taxonomy_option_value['hide_metabox'] : '';
		$psp_taxonomy_metabox_title = isset($psp_taxonomy_option_value['title']) ? $psp_taxonomy_option_value['title'] : '';
		$psp_taxonomy_metabox_description = isset($psp_taxonomy_option_value['description']) ? $psp_taxonomy_option_value['description'] : '';
		
		$psp_settings = get_option('psp_sitewide_settings');			
		$psp_metabox_advanced_hidden = isset($psp_settings['hide_metabox_advanced']) ? $psp_settings['hide_metabox_advanced'] : '';
		
		$psp_pre_settings = get_option('psp_pre_setting');
		$psp_premium_valid = isset($psp_pre_settings['premium']) && !empty($psp_pre_settings['premium']) ? $psp_pre_settings['premium'] : '';
		$psp_premium_status = isset($psp_pre_settings['psp_premium_license_key_status']) ? $psp_pre_settings['psp_premium_license_key_status'] : '';
		
		//$psp_premium_valid = 1;
		//$psp_premium_status = 0;
		
		if (is_super_admin()) $psp_taxonomy_metabox_hidden = false;
		
		if (!$psp_taxonomy_metabox_hidden) {
			//wp_nonce_field( 'do_psp_extra_taxonomy_fields', 'psp_extra_taxonomy_fields_nonce' );
			//$tax_meta = get_option( "psp_taxonomy_metas_$t_id");
		?>
			</table>
			<?php
			wp_nonce_field( 'do_psp_extra_taxonomy_fields', 'psp_extra_taxonomy_fields_nonce' );
			$psp_type = "taxonomy";
			//available tags array
		    $pspavailableTags = ['sep', 'seo_title', 'wp_title', 'site_name', 'site_description'];
		    $pspavailableTagsDesc = ['sep', 'seo_title', 'wp_title', 'description', 'seo_description', 'site_name', 'site_description'];
			$psp_seo_meta = get_term_meta($t_id, "psp_taxonomy_seo_metas_$t_id");
			if ($psp_seo_meta) $psp_seo_meta = $psp_seo_meta[0];
			if (!$psp_seo_meta) $psp_seo_meta = get_option( "psp_taxonomy_seo_metas_$t_id");
			$psp_seo_meta['titleformat'] = isset($psp_seo_meta['titleformat']) ? esc_attr($psp_seo_meta['titleformat']) : esc_attr($psp_taxonomy_metabox_title);
			$psp_seo_meta['descformat'] = isset($psp_seo_meta['descformat']) ? esc_attr($psp_seo_meta['descformat']) : '';
			
			//$psp_seo_meta['schema_string'] = isset($psp_seo_meta['schema_string']) ? html_entity_decode(stripcslashes(($psp_seo_meta['schema_string']))) :'';
			$json_schema_string = isset($psp_seo_meta['schema_string']) ? html_entity_decode(stripcslashes(esc_attr($psp_seo_meta['schema_string']))) : '';
			//validate it is a json object
    		$schema_obj = json_decode($json_schema_string);
    		if($schema_obj === null) {
    		    $json_schema_string = 'Invalid JSON Schema';
    		}
    		$psp_seo_meta['schema_string'] = $json_schema_string;
    		
    		$psp_sm_settings = get_option('psp_sitemap');
    		$excluded_term_ids = array();		
    		$term_excluded_from_sitemap = '';
    		
    		if( $psp_sm_settings['excluded_term_ids']  ) {
    			$excluded_term_ids = explode(",", $psp_sm_settings['excluded_term_ids']); 
    		}
    		if( !empty( $excluded_term_ids ) ) {
    			$excluded_term_ids = array_map( 'intval', $excluded_term_ids ); 
    			if( in_array($t_id, $excluded_term_ids)) {
    			    $term_excluded_from_sitemap = 'on';
    			}
    		}
			//$psp_seo_meta['nositemap'] = $term_excluded_from_sitemap;
			$psp_seo_meta['nositemap'] = !empty($psp_seo_meta['nositemap']) ? esc_attr($psp_seo_meta['nositemap']) : $term_excluded_from_sitemap;
			
			//$psp_seo_meta = array_map( 'esc_attr', $psp_seo_meta );
			$this->psp_taxonomy_meta_original = $psp_seo_meta;
			$psp_social_meta = get_term_meta($t_id, "psp_taxonomy_social_metas_$t_id");
			if ($psp_social_meta) $psp_social_meta = $psp_social_meta[0];
			if (!$psp_social_meta) $psp_social_meta = get_option( "psp_taxonomy_social_metas_$t_id");
			//$psp_social_meta = get_option( "psp_taxonomy_social_metas_$t_id");
			//$psp_social_meta = array_map( 'esc_attr', $psp_social_meta );
			$this->psp_taxonomy_social_meta_original = $psp_social_meta; ?>
			<h3><?php echo '<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />';  ?><?php esc_html_e(' Techblissonline Platinum SEO and Social Meta Box ', 'platinum-seo-pack'); ?></h3>
			<div class="pspmbox">
			<div class="psp-bs">
        		<ul class="text-right list-inline"><li class="social"><a href="#TB_inline?height=600&amp;width=750&amp;inlineId=psp-meta-box-parent" class="tbpsp thickbox " style="text-decoration:none;" title="Platinum SEO Preview"><span class="dashicons dashicons-search"></span><?php esc_html_e( ' ', 'platinum-seo-pack' ); ?></a></li>
        		    <li class="social"><a href="#TB_inline?height=600&amp;width=750&amp;inlineId=pspanalysispar" class="tbpsp thickbox " style="text-decoration:none;" title="Platinum SEO Analysis"><?php esc_html_e( ' ', 'platinum-seo-pack' ); ?><span class="dashicons dashicons-dashboard"></span></a></li>
        		</ul>
        	</div>
        	<div id="psp-meta-box-parent">
    			<div id="psp-meta-box">
    			<ul class="psp-metabox-tabs" id="psp-metabox-tabs">
    				<li class="basic"><a href="#basic" title="Generic SEO"><span class="dashicons dashicons-admin-generic"></span><?php esc_html_e( ' SEO', 'platinum-seo-pack' ); ?></a></li>
    				<li class="analysis"><a href="#analysis" title="SEO Analysis"><span class="dashicons dashicons-dashboard"></span><?php esc_html_e( ' Analysis', 'platinum-seo-pack' ); ?></a></li>
    				<?php if (!$psp_metabox_advanced_hidden || is_super_admin()) { ?>
    				<li class="advanced"><a href="#advanced" title="Advanced SEO"><span class="dashicons dashicons-admin-tools"></span><?php esc_html_e( ' Advanced', 'platinum-seo-pack' ); ?></a></li>
    				<li class="social"><a href="#bsocial" title="Basic Social"><span class="dashicons dashicons-share"></span><?php esc_html_e( '  Basic', 'platinum-seo-pack' ); ?></a></li>
    				<li class="social"><a href="#asocial" title="Advanced Social"><span class="dashicons dashicons-networking"></span><?php esc_html_e( ' Advanced', 'platinum-seo-pack' ); ?></a></li>
    				<?php } ?>				
    			</ul>
    			<br class="clear" />
    			<div id="basic" class="psptab">
    				<?php include_once( 'psp_basic_metabox_renderer.php' ); ?>	
    			</div>
    			<div id="analysis" class="psptab">
				    <?php include_once( 'psp_analysis_metabox_renderer.php' ); ?>	
			    </div>
    			<?php if (!$psp_metabox_advanced_hidden || is_super_admin()) { ?>
    			<div id="advanced" class="hidden psptab wrap">
    				<?php include_once( 'psp_advanced_metabox_renderer.php' ); ?>
    			</div>  			
    			<div id="bsocial" class="psptab">
    				<?php include_once( 'psp_basic_social_metabox_renderer.php' ); ?>	
    			</div>
    			<div id="asocial" class="psptab">
    				<?php 	if ($psp_premium_valid && $psp_premium_status) { 
    							$metabox_template = apply_filters('psp_metabox_template', 'psp_premiumad_metabox_renderer.php');
    							if (empty($metabox_template)) $metabox_template = 'psp_premiumad_metabox_renderer.php';
    							include_once( $metabox_template ); 
    							//include_once( 'psp_advanced_social_metabox_renderer.php' );							
    						} else { ?>
    							<div class="psp-bs">
    						     <div class="container">
    						<?php 
    						    wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
    						    // wp_enqueue_style("psp-htmlsettings-css", plugins_url( '/css/psp-html-settings.css', __FILE__ ));
    							include_once( 'psp_premiumad_metabox_renderer.php' ); ?>
    							</div></div>
    				<?php	}
    				?>		
    			</div>
    			<?php } ?>
    		</div>
    		</div>
    		</div>
	<?php
		}
	}
	
	// save extra category extra fields callback function
	function psp_save_extra_taxonomy_fields( $term_id ) {
	
		// Check if our nonce is set and is valid.
		if ( ! isset( $_POST['psp_extra_taxonomy_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key($_POST['psp_extra_taxonomy_fields_nonce']), 'do_psp_extra_taxonomy_fields' )) {
			return;
		}

		$t_id = $term_id;
		
		// Make sure that it is set.
		if ( ! isset( $_POST['psp_seo_meta'] ) && ! isset( $_POST['psp_social_meta'] ) ) {
			return;
		} else {
		    
		    $psp_settings = get_option('psp_sitewide_settings');			
            $psp_metabox_advanced_hidden = isset($psp_settings['hide_metabox_advanced']) ? $psp_settings['hide_metabox_advanced'] : '';
            
            if (is_super_admin()) $psp_metabox_advanced_hidden = false;
		
			if (!empty($this->psp_taxonomy_meta_original)) { 
				$psp_seo_data_original = $this->psp_taxonomy_meta_original;
			} else {
				$psp_seo_data_original = get_term_meta($t_id, "psp_taxonomy_seo_metas_$t_id");
				if($psp_seo_data_original) $psp_seo_data_original = $psp_seo_data_original[0];
				if(!$psp_seo_data_original) $psp_seo_data_original = get_option( "psp_taxonomy_seo_metas_$t_id");
			}
			
			if(!empty($this->psp_taxonomy_social_meta_original)) {
				$psp_social_data_original = $this->psp_taxonomy_social_meta_original;
			} else {
				//$psp_social_data_original = get_option( "psp_taxonomy_social_metas_$t_id");
			}
			
			$psp_seo_data_current = isset($_POST['psp_seo_meta']) ? $this->psp_sanitze_seo_data($_POST['psp_seo_meta']) : array();
			
			if(isset($psp_seo_data_original) && !empty($psp_seo_data_original)) {
			    $psp_taxonomy_seo_data = array_merge((array)$psp_seo_data_original, (array)$psp_seo_data_current);
			} else {
			    $psp_taxonomy_seo_data = $psp_seo_data_current;
			}
			
			$psp_social_data_current = isset($_POST['psp_social_meta']) ? $this->psp_sanitze_social_data($_POST['psp_social_meta']) : array();
			
			if(isset($psp_social_data_original) && !empty($psp_social_data_original)) {
			    $psp_taxonomy_social_data = array_merge((array)$psp_social_data_original, (array)$psp_social_data_current);
			} else {
			    $psp_taxonomy_social_data = $psp_social_data_current;
			}
		
			if (isset($psp_taxonomy_seo_data) && !empty($psp_taxonomy_seo_data)) {
			
				// Sanitize SEO data.
				//$psp_taxonomy_seo_data = $this->psp_sanitze_seo_data( $psp_taxonomy_seo_data );
				//save the option array
				//update_option( "psp_taxonomy_seo_metas_$t_id", $psp_taxonomy_seo_data );
				update_term_meta( $t_id, "psp_taxonomy_seo_metas_$t_id", $psp_taxonomy_seo_data );
				
				//do not proceed further if only basic seo meta data had to be saved/
				if ($psp_metabox_advanced_hidden) {
				    return;
				}
				//update google sitemap generator
        		 if (!empty($psp_taxonomy_seo_data['nositemap'])) {
					 $this->psp_update_wps("excluded_term_ids", $t_id, true );
        		     $this->psp_update_gsg("sm_b_exclude_cats", $t_id, true );
					 $psp_exclude = true;
        		 } else {
					  $this->psp_update_wps("excluded_term_ids", $t_id, false );
        		      $this->psp_update_gsg("sm_b_exclude_cats", $t_id, false );
					  $psp_exclude = false;
        		 }
				 $psp_id = !empty($t_id) ? $t_id : '';
				//techblissonline_psp_update_sitemap - action hook to attach your function
				//$psp_id - Post ID or Term ID 
				//$psp_exclude - Boolean indicating whether to exclude (true) or include (false) this post in the sitemap
				do_action( 'techblissonline_psp_update_sitemap', $psp_id, $psp_exclude );

			
			}
			
			if (isset($psp_taxonomy_social_data) && !empty($psp_taxonomy_social_data)) {
			
				// Sanitize Social data.
				//$psp_taxonomy_social_data = $this->psp_sanitze_social_data( $psp_taxonomy_social_data );
				//save the option array
				//update_option( "psp_taxonomy_social_metas_$t_id", $psp_taxonomy_social_data );
				update_term_meta( $t_id, "psp_taxonomy_social_metas_$t_id", $psp_taxonomy_social_data );
			
			}
			
		}		
		
	}
	
	function do_psp_meta_boxes() {
	
		//wp_enqueue_media();
		wp_enqueue_script( 'psp-meta-box', plugins_url( '/js/pspmetabox.js', __FILE__ ), array('jquery', 'jquery-ui-tabs' ) );
		//wp_enqueue_script( 'psp-image-uploader', plugins_url( '/js/pspmediauploader.js', __FILE__ ), array( 'jquery' ) );
		//wp_enqueue_script( 'psp-social', plugins_url( '/js/pspsocialhandler.js', __FILE__ ), array( 'jquery' ) );
		
		if ( $this->psp_helper->user_has_access('metabox') ) {
			wp_enqueue_script( 'psp-meta-box-snippet', plugins_url( '/js/snippetpreview.js', __FILE__ ), '', '2.3.6');			
		}
		$psp_cm_json_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'json', 'codemirror'=> array('autoRefresh' => true)));
        wp_localize_script('psp-meta-box', 'psp_cm_json_settings', $psp_cm_json_settings);
        
        $psp_cm_html_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'html', 'codemirror'=> array('autoRefresh' => true)));
        wp_localize_script('psp-meta-box', 'psp_cm_html_settings', $psp_cm_html_settings);
		
		//global $wp_scripts; 
		//wp_enqueue_style("jquery-ui-css", "http://ajax.googleapis.com/ajax/libs/jqueryui/{$wp_scripts->registered['jquery-ui-core']->ver}/themes/smoothness/jquery-ui.min.css");
		
		wp_enqueue_style("jquery-ui-css", plugins_url( '/css/jquery-ui-techblissonline.css', __FILE__ ));

		$psp_post_settings = get_option("psp_post_settings");
		$psp_post_metabox_hidden = isset($psp_post_settings['hide_metabox']) ? $psp_post_settings['hide_metabox'] : '';
		if (!$psp_post_metabox_hidden || is_super_admin()) {
			//add_meta_box( 'postpsp', esc_html__( 'Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ), array( &$this, 'psp_do_seo_metabox' ), 'post', 'normal', 'high' );			
			//add_meta_box( 'postpsp', '<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />'.esc_html__( ' Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ), array( &$this, 'psp_do_seo_metabox' ), 'post', 'normal', 'high' );
			if ( $this->psp_helper->user_has_access('metabox') ) {
			    add_meta_box( 'postpsp', '<span align="left">'.'<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />'.esc_html__( ' Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ).'</span>', array( &$this, 'psp_do_seo_metabox' ), 'post', 'normal', 'high' );
			}
		}
		
		$psp_page_settings = get_option("psp_page_settings");
		$psp_page_metabox_hidden = isset($psp_page_settings['hide_metabox']) ? $psp_page_settings['hide_metabox'] : '';
		if (!$psp_page_metabox_hidden || is_super_admin()) {
			//add_meta_box( 'postpsp', esc_html__( 'Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ), array( &$this, 'psp_do_seo_metabox' ), 'page', 'normal', 'high' );
			//add_meta_box( 'postpsp', '<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />'.esc_html__( ' Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ), array( &$this, 'psp_do_seo_metabox' ), 'page', 'normal', 'high' );
			if ( $this->psp_helper->user_has_access('metabox') ) {
			    add_meta_box( 'postpsp', '<span align="left">'.'<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />'.esc_html__( ' Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ).'</span>', array( &$this, 'psp_do_seo_metabox' ), 'page', 'normal', 'high' );
			}
		}
		
		$cust_post_types = array();
				
		$cust_post_types = $this->custom_post_types;
		if(empty($cust_post_types)) $cust_post_types = get_post_types( array ( '_builtin' => FALSE ) );	

		foreach($cust_post_types as $cust_post_type) {
			$psp_posttype_option_name = "psp_".$cust_post_type."_settings";
			$psp_posttype_option_value = get_option($psp_posttype_option_name);
			$psp_posttype_metabox_hidden = isset($psp_posttype_option_value['hide_metabox']) ? $psp_posttype_option_value['hide_metabox'] : '';
			if (!$psp_posttype_metabox_hidden || is_super_admin()) {
				//add_meta_box( 'postpsp', esc_html__( 'Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ), array( &$this, 'psp_do_seo_metabox' ), $cust_post_type, 'normal', 'high' );
				//add_meta_box( 'postpsp', '<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />'.esc_html__( ' Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ), array( &$this, 'psp_do_seo_metabox' ), $cust_post_type, 'normal', 'high' );
				if ( $this->psp_helper->user_has_access('metabox') ) {
				    add_meta_box( 'postpsp', '<span align="left">'.'<img src="'.esc_url(plugins_url( 'images/techblissonline-platinum-seo-pack.ico', dirname(dirname(__FILE__) ))).'" />'.esc_html__( ' Techblissonline Platinum SEO and Social Meta Box', 'platinum-seo-pack' ).'</span>', array( &$this, 'psp_do_seo_metabox' ), $cust_post_type, 'normal', 'high' );
				}
			}
		}		
		
	}
	
	//add extra slash
	public function psp_slash( $data, $postarr ) {
        wp_slash($postarr);
        return $data;
    }
	
	//add metabox to post types
	function psp_do_seo_metabox( $psp_object = NULL, $pspmeta = [], $saving = false ) {    //check for existing featured ID
	    //error_log(print_r($psp_object, true));
		//error_log(print_r($pspmeta, true));
		global $post;
		//$psp_posttype_metabox_hidden = false;		
		$psp_settings_name = "psp_".$post->post_type."_settings";
		$psp_p_settings = get_option($psp_settings_name);
		
		$psp_settings = get_option('psp_sitewide_settings');
		
		$psp_sm_settings = get_option('psp_sitemap');
		$excluded_post_ids = array();		
		$post_excluded_from_sitemap = '';
		
		if( !empty( $psp_sm_settings['excluded_post_ids'] ) ) {
			$excluded_post_ids = explode(",", $psp_sm_settings['excluded_post_ids']); 
		}
		if( !empty( $excluded_post_ids ) ) {
			$excluded_post_ids = array_map( 'intval', $excluded_post_ids ); 
			if( in_array($post->ID, $excluded_post_ids)) {
			    $post_excluded_from_sitemap = 'on';
			}
		}
		
		$psp_posttype_metabox_advanced_hidden = isset($psp_settings['hide_metabox_advanced']) ? $psp_settings['hide_metabox_advanced'] : '';
		
		$psp_pre_settings = get_option('psp_pre_setting');
		$psp_premium_valid = isset($psp_pre_settings['premium']) && !empty($psp_pre_settings['premium']) ? $psp_pre_settings['premium'] : '';	
		$psp_premium_status = isset($psp_pre_settings['psp_premium_license_key_status']) ? $psp_pre_settings['psp_premium_license_key_status'] : '';
		
		//$psp_premium_valid = 1;
		//$psp_premium_status = 0;
		if ( !$saving ) {
		    wp_nonce_field( 'do_psp_seo_meta_box', 'psp_seo_meta_box_nonce' );
		}
		//$psp_post_meta = get_post_meta($post->ID, '_psp_post_seo_meta', true);
		
		$psp_post_meta = array();
		$psp_social_meta = array(); 
		
		$wp_post_meta_data_arr = get_metadata( 'platinumseo', $post->ID, '', false );
		
		if(!$wp_post_meta_data_arr) {
			$wp_post_meta_data_arr = get_post_meta($post->ID);
		}
		/**********
		foreach ($wp_post_meta_data_arr as $key => $value) {
			
				$wp_post_meta_data[$key] = $value[0];
			
		}
		
		$psp_post_meta['title'] = $wp_post_meta_data['_techblissonline_psp_title'];
		$psp_post_meta['description'] = $wp_post_meta_data['_techblissonline_psp_description'];
		$psp_post_meta['keywords'] = $wp_post_meta_data['_techblissonline_psp_keywords'];
		$psp_post_meta['robots'] = $wp_post_meta_data['_techblissonline_psp_robots_meta'];
		$psp_post_meta['canonical_url'] = $wp_post_meta_data['_techblissonline_psp_canonical_url'];
		$psp_post_meta['noarchive'] = $wp_post_meta_data['_techblissonline_psp_noarchive'];
		$psp_post_meta['nosnippet'] = $wp_post_meta_data['_techblissonline_psp_nosnippet'];
		$psp_post_meta['noimageindex'] = $wp_post_meta_data['_techblissonline_psp_noimageidx'];
		$psp_post_meta['redirect_to_url'] = $wp_post_meta_data['_techblissonline_psp_redirect_to_url'];
		$psp_post_meta['redirect_status_code'] = $wp_post_meta_data['_techblissonline_psp_redirect_status_code'];
		
		$psp_post_disablers = unserialize($wp_post_meta_data['_techblissonline_psp_disable_flags']);
		*************/
		$psp_post_meta['title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_title'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_title'][0]) : '';		
		if (empty($psp_post_meta['title']) && !isset($wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0])) {
			$psp_old_title = !empty($wp_post_meta_data_arr['title'][0]) ? esc_attr($wp_post_meta_data_arr['title'][0]) : '';
			$psp_post_meta['title'] = $psp_old_title;
		}
		if (empty($psp_post_meta['title']) && !isset($wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0])) {
			$yoast_title = !empty($wp_post_meta_data_arr['_yoast_wpseo_title'][0]) ? esc_attr($wp_post_meta_data_arr['_yoast_wpseo_title'][0]) : '';
			if (!empty($yoast_title)) {
				$yoast_title = preg_replace('/%%[^%]+%%/', '', $yoast_title);
				$psp_post_meta['title'] = !empty($yoast_title) ? esc_attr($yoast_title) : '';
			}
		}
		
		$psp_post_meta['titleformat'] = isset($wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0]) : $psp_p_settings['title'];
		
		$psp_post_meta['description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_description'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_description'][0]) : '';		
		if (empty($psp_post_meta['description']) && !isset($wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0])) {
			$psp_old_desc = !empty($wp_post_meta_data_arr['description'][0]) ? esc_attr($wp_post_meta_data_arr['description'][0]) : '';
			$psp_post_meta['description'] = $psp_old_desc;
		}
		if (empty($psp_post_meta['description']) && !isset($wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0])) {
			$yoast_desc = !empty($wp_post_meta_data_arr['_yoast_wpseo_metadesc'][0]) ? esc_attr($wp_post_meta_data_arr['_yoast_wpseo_metadesc'][0]) : '';
			//$psp_post_meta['description'] = $yoast_desc;
			if (!empty($yoast_desc)) {
				$yoast_desc = preg_replace('/%%[^%]+%%/', '', $yoast_desc);
				$psp_post_meta['description'] = !empty($yoast_desc) ? esc_attr($yoast_desc) : '';
			}
		}
		
		$psp_post_meta['descformat'] = isset($wp_post_meta_data_arr['_techblissonline_psp_descformat'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_descformat'][0]) : '';
		$psp_post_meta['maxsnippet'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maxsnippet'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_maxsnippet'][0]) : '';
		$psp_post_meta['keywords'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_keywords'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_keywords'][0]) : '';
		if (!empty($psp_p_settings['robots'])) {
		    $psp_posttype_noindex = 'on';
		    $psp_posttype_nofollow = '';
		} else {
		    $psp_posttype_noindex = '';
		    $psp_posttype_nofollow = '';
			
			if (isset($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0])) {
				$psp_posttype_noindex = !empty($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) : '';
				$psp_posttype_nofollow = !empty($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) : '';
			} else {
				$psp_old_robots_meta = !empty($wp_post_meta_data_arr['robotsmeta'][0]) ? esc_attr($wp_post_meta_data_arr['robotsmeta'][0]) : '';

				if (!empty($psp_old_robots_meta)) {
					if ($psp_old_robots_meta == "index,follow") {
						$psp_posttype_noindex = '';
						 $psp_posttype_nofollow = '';
					}
					if ($psp_old_robots_meta == "index,nofollow") {
						$psp_posttype_noindex = '';
						 $psp_posttype_nofollow = 'on';
					}
					if ($psp_old_robots_meta == "noindex,follow") {
						 $psp_posttype_noindex = 'on';
						 $psp_posttype_nofollow = '';
					}
					if ($psp_old_robots_meta == "noindex,nofollow") {
						 $psp_posttype_noindex = 'on';
						 $psp_posttype_nofollow = 'on';
					}
				} else {
					//$psp_posttype_noindex = '';
					//$psp_posttype_nofollow = '';
					$yoast_noindex_meta = !empty($wp_post_meta_data_arr['_yoast_wpseo_meta-robots-noindex'][0]) ? esc_attr($wp_post_meta_data_arr['_yoast_wpseo_meta-robots-noindex'][0]) : '';

					if (!empty($yoast_noindex_meta)) {
						if ($yoast_noindex_meta == 1) {
							$psp_posttype_noindex = 'on';
						} else {
							$psp_posttype_noindex = '';
						}
					} else {
						$psp_posttype_noindex = '';
					}

					$yoast_nofollow_meta = !empty($wp_post_meta_data_arr['_yoast_wpseo_meta-robots-nofollow'][0]) ? esc_attr($wp_post_meta_data_arr['_yoast_wpseo_meta-robots-nofollow'][0]) : '';

					if (!empty($yoast_nofollow_meta)) {
						$psp_posttype_nofollow = 'on';
					} else {
						$psp_posttype_nofollow = '';
					}
				}
			}
		}
		$psp_post_meta['robots'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_robots_meta'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_robots_meta'][0]) : '';
		//$psp_post_meta['noindex'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) : esc_attr($psp_posttype_noindex);
		//$psp_post_meta['nofollow'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) : esc_attr($psp_posttype_nofollow);
		
		$psp_post_meta['noindex'] = isset($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) ? (!empty($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) : '') : esc_attr($psp_posttype_noindex);
		$psp_post_meta['nofollow'] = isset($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) ? (!empty($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) : '') : esc_attr($psp_posttype_nofollow);
		
		$psp_post_meta['nositemap'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nositemap'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_nositemap'][0]) : $post_excluded_from_sitemap;
		$psp_post_meta['canonical_url'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_canonical_url'][0]) ? esc_url_raw($wp_post_meta_data_arr['_techblissonline_psp_canonical_url'][0]) : '';
		//$psp_post_meta['schema_string'] = isset($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]) : '';	
		//$psp_post_meta['schema_string'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]) ? html_entity_decode(stripcslashes(esc_attr($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]))) : '';
		$json_schema_string = !empty($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]) ? html_entity_decode(stripcslashes(esc_attr($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]))) : '';
		    //validate it is a json object
    		$schema_obj = json_decode($json_schema_string);
    		if($schema_obj === null) {
    		    $json_schema_string = 'Invalid JSON Schema';
    		}
    	$psp_post_meta['schema_string'] = $json_schema_string;
    		
		$psp_post_meta['noarchive'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noarchive'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_noarchive'][0]) : '';
		$psp_post_meta['nosnippet'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nosnippet'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_nosnippet'][0]) : '';
		$psp_post_meta['noimageindex'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noimageidx'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_noimageidx'][0]) : '';
		$psp_post_meta['maxvideo'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maxvideo'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_maxvideo'][0]) : '';
		$psp_post_meta['maximage'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maximage'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_maximage'][0]) : '';
		$psp_post_meta['redirect_to_url'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_redirect_to_url'][0]) ? esc_url_raw($wp_post_meta_data_arr['_techblissonline_psp_redirect_to_url'][0]) : '';
		$psp_post_meta['redirect_status_code'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_redirect_status_code'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_redirect_status_code'][0]) : '';
		$psp_post_meta['preferred_tax'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_preferred_taxonomy'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_preferred_taxonomy'][0]) : '';
		
		$psp_post_meta['preferred_term'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_preferred_term'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_preferred_term'][0]) : '';
		
		//linkingkeywords
		$psp_post_meta['linkingkeywords'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_linkingkeywords'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_linkingkeywords'][0]) : '';
		
		$psp_post_disablers = !empty($wp_post_meta_data_arr['_techblissonline_psp_disable_flags'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_disable_flags'][0]) : array();	
		//$psp_social_meta = unserialize($wp_post_meta_data_arr['_techblissonline_psp_social_data'][0]);	
		
		//$psp_post_social_enablers = unserialize($wp_post_meta_data_arr['_techblissonline_psp_social_enabled'][0]);
		
		$psp_social_meta['fb_og_type'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_og_type'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_fb_og_type'][0]) : '';
		$psp_social_meta['fb_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_title'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_fb_title'][0]) : '';
		$psp_social_meta['fb_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_description'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_fb_description'][0]) : '';
		$psp_social_meta['fb_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_image'][0]) ? esc_url_raw($wp_post_meta_data_arr['_techblissonline_psp_fb_image'][0]) : '';
		//$psp_social_meta['fb_ogtype_properties'] = isset($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) : '';
		$psp_post_fb_ogtype_properties = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) : array();
		$psp_post_fb_ogtype_properties = array_map( 'esc_attr', $psp_post_fb_ogtype_properties );
		$psp_social_meta['fb_ogtype_properties'] = urldecode(http_build_query($psp_post_fb_ogtype_properties, '', "\r\n")); 
		
		//$psp_social_meta['fb_media_properties'] = isset($wp_post_meta_data_arr['_techblissonline_psp_fb_media_properties'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_fb_media_properties'][0]) : '';
		$psp_post_fb_media_properties = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_media_properties'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_fb_media_properties'][0]) : array();
		$psp_post_fb_media_properties = array_map( 'esc_attr', $psp_post_fb_media_properties );
		$psp_social_meta['fb_media_properties'] = urldecode(http_build_query($psp_post_fb_media_properties, '', "\r\n")); 
		
		$psp_social_meta['tw_card_type'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_card_type'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_card_type'][0]) : '';
		$psp_social_meta['tw_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_title'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_title'][0]) : '';
		$psp_social_meta['tw_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_description'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_description'][0]) : '';
		$psp_social_meta['tw_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_image'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_image'][0]) : '';
		/**********
		$psp_social_meta['tw_image'] = $wp_post_meta_data_arr['_techblissonline_psp_tw_image'][0];
		$psp_social_meta['tw_image_1'] = $wp_post_meta_data_arr['_techblissonline_psp_tw_image_1'][0];
		$psp_social_meta['tw_image_2'] = $wp_post_meta_data_arr['_techblissonline_psp_tw_image_2'][0];
		$psp_social_meta['tw_image_3'] = $wp_post_meta_data_arr['_techblissonline_psp_tw_image_3'][0];
		*********/
		$psp_post_tw_data_images = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_images'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_tw_images'][0]) : array();
		
		$psp_social_meta['tw_creator'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_creator'][0]) ? '@'.esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_creator'][0]) : '';
		$psp_social_meta['tw_imagealt'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_imagealt'][0]) ? esc_url_raw($wp_post_meta_data_arr['_techblissonline_psp_tw_imagealt'][0]) : '';
		$psp_social_meta['tw_player'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player'][0]) ? esc_url_raw($wp_post_meta_data_arr['_techblissonline_psp_tw_player'][0]) : '';
		$psp_social_meta['tw_player_stream'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_stream'][0]) ? esc_url_raw($wp_post_meta_data_arr['_techblissonline_psp_tw_player_stream'][0]) : '';
		$psp_social_meta['tw_player_width'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_width'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_player_width'][0]) : '';
		$psp_social_meta['tw_player_height'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_height'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_player_height'][0]) : '';
		
		$psp_social_meta['tw_app_country'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_country'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_country'][0]) : '';		
		$psp_social_meta['tw_app_name_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_iphone'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_iphone'][0]) : '';
		$psp_social_meta['tw_app_id_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_iphone'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_iphone'][0]) : '';
		$psp_social_meta['tw_app_url_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_iphone'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_iphone'][0]) : '';
		$psp_social_meta['tw_app_name_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_ipad'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_ipad'][0]) : '';
		$psp_social_meta['tw_app_id_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_ipad'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_ipad'][0]) : '';
		$psp_social_meta['tw_app_url_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_ipad'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_ipad'][0]) : '';
		$psp_social_meta['tw_app_name_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_googleplay'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_googleplay'][0]) : '';
		$psp_social_meta['tw_app_id_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_googleplay'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_googleplay'][0]) : '';
		$psp_social_meta['tw_app_url_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_googleplay'][0]) ? esc_url($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_googleplay'][0]) : '';
		/*************
		$psp_social_meta['tw_label_1'] = $wp_post_meta_data_arr['_techblissonline_psp_tw_label_1'][0];
		$psp_social_meta['tw_data_1'] = $wp_post_meta_data_arr['_techblissonline_psp_tw_data_1'][0];
		$psp_social_meta['tw_label_2'] = $wp_post_meta_data_arr['_techblissonline_psp_tw_label_2'][0];
		$psp_social_meta['tw_data_2'] = $wp_post_meta_data_arr['_techblissonline_psp_tw_data_2'][0];
		*********/
		$psp_post_social_tw_label_data = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_label_data'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_tw_label_data'][0]) : array();		
		
		$psp_social_meta['sc_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_title'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_sc_title'][0]) : '';
		$psp_social_meta['sc_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_description'][0]) ? esc_attr($wp_post_meta_data_arr['_techblissonline_psp_sc_description'][0]) : '';
		$psp_social_meta['sc_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_image'][0]) ? esc_url_raw($wp_post_meta_data_arr['_techblissonline_psp_sc_image'][0]) : '';
		/******
		$psp_post_meta['disable_title'] = $psp_post_disablers['disable_title'];
		$psp_post_meta['disable_description'] = $psp_post_disablers['disable_description'];
		$psp_post_meta['disable_keywords'] = $psp_post_disablers['disable_keywords'];
		$psp_post_meta['disable_canonical'] = $psp_post_disablers['disable_canonical'];
		$psp_post_meta['disable_title_format'] = $psp_post_disablers['disable_title_format'];
		$psp_post_meta['disable_desc_format'] = $psp_post_disablers['disable_desc_format'];
		$psp_post_meta['disable_psp'] = $psp_post_disablers['disable_psp'];
		********/
		//$psp_post_meta = array_merge($psp_post_meta, $psp_post_disablers, $psp_social_meta);	
        $psp_seo_meta = array_merge((array)$psp_post_meta, (array)$psp_post_disablers);	   		
		$this->psp_post_meta_original = $psp_seo_meta;
		//$psp_social_meta = array_merge((array)$psp_social_meta, (array)$psp_post_tw_data_images, (array)$psp_post_social_tw_label_data);
		//$psp_social_meta = array_merge($psp_social_meta, $psp_post_tw_data_images, $psp_post_social_tw_label_data, $psp_post_social_enablers);
		$this->psp_post_social_meta_original = $psp_social_meta;
		$psp_original_meta = array();
		$psp_original_meta['seo_meta'] = $psp_seo_meta;
		$psp_original_meta['social_meta'] = $psp_social_meta;
		
		if ($saving) {
		    return $psp_original_meta;
		}
		
		$psp_type = "posttype";
		
		//Taxonomies for breadcrumb tags
		//$builtin_taxonomies = array("category", "tag", "post_format");
		$builtin_taxonomies = array("category", "post_tag");
		$custom_taxonomies = $this->custom_taxonomies;
		$psp_all_taxonomies = array_merge((array)$builtin_taxonomies, (array)$custom_taxonomies);
		$psp_taxonomies = array_combine($psp_all_taxonomies, $psp_all_taxonomies);
		$default = array( "" => "Select a Taxonomy" );		
		$psp_bc_taxonomies = array_merge((array)$default, (array)$psp_taxonomies);
		//build tax terms
		$psp_bc_tax_terms = array();
		$psp_bc_tax_term_items = array( "" => "Select a Term" );
		$psp_bc_tax_terms_string = "";
		foreach($psp_bc_taxonomies as $key => $val) {

        	$taxonomyterms = array();	
        	
        	$termobjects = get_terms( array(
        		'taxonomy' => $key,
        		'hide_empty' => false,
        	) );
        	
        	if ( ! empty( $termobjects ) && ! is_wp_error( $termobjects ) ){
        	        $i = 0;
        		foreach ( $termobjects as $termobject ) {
        		    
        		    if (isset($psp_seo_meta['preferred_tax']) && $psp_seo_meta['preferred_tax']==$key) {
        		        $psp_bc_tax_term_items[$termobject->term_id] =  $termobject->name;	
        		    }
        		    
        		    $taxonomyterms[$i]["id"] = '"'.$termobject->term_id.'"';	
        			//$taxonomyterms[$i]["name"] = $termobject->name;	
        			$taxonomyterms[$i]["name"] = htmlentities($termobject->name, ENT_QUOTES, 'UTF-8', false);
        			$i = $i + 1;
        		}
        		
        		if ( ! empty( $taxonomyterms ) ) {
        		    //$psp_bc_tax_terms_arr[$key] = $taxonomyterms;
        			//$psp_bc_tax_terms[$key]  = '['.str_replace('""', '"', wp_unslash(json_encode($taxonomyterms))).']';
        			$psp_bc_tax_terms[$key]  = str_replace('""', '"', wp_unslash(json_encode($taxonomyterms)));
        		}
        	
        	}
        
        }
        //error_log(print_r($psp_bc_tax_term_items, true));
        $this->psp_bc_tax_term_items = $psp_bc_tax_term_items;
        //error_log(print_r($this->psp_bc_tax_term_items, true));
        $psp_search_arr = array('"[', ']"');
        $psp_replace_arr = array('[', ']');
		$psp_bc_tax_terms_json = json_encode($psp_bc_tax_terms);
		//$psp_bc_tax_terms_string = '['.str_replace($psp_search_arr, $psp_replace_arr, wp_unslash($psp_bc_tax_terms_json)) .']';
		$psp_bc_tax_terms_string = str_replace($psp_search_arr, $psp_replace_arr, wp_unslash($psp_bc_tax_terms_json)) ;
		
		
		//available tags array
		//$pspavailableTags = ['seo_title', 'wp_title', 'category', 'site_name', 'site_description'];
		$pspavailableTags = ['sep', 'seo_title', 'wp_title', 'taxonomy', 'site_name', 'site_description', 'currentyear'];
		//available tags array
		//$pspavailableTagsDesc = ['seo_title', 'wp_title', 'category', 'site_name', 'seo_description', 'site_description'];
		$pspavailableTagsDesc = ['sep', 'seo_title', 'wp_title', 'taxonomy', 'site_name', 'seo_description', 'site_description', 'currentyear'];
        
		if ($post->post_type == 'page') {
		    $pspavailableTags = ['sep', 'seo_title', 'wp_title', 'site_name', 'site_description', 'currentyear'];
		    //available tags array
		    $pspavailableTagsDesc = ['sep', 'seo_title', 'wp_title', 'site_name', 'seo_description', 'site_description', 'currentyear'];
		}
		
		?>
		<div class="psp-bs">
		<ul class="text-right list-inline"><li class="social"><a href="#TB_inline?height=600&amp;width=750&amp;inlineId=psp-meta-box-parent" class="tbpsp thickbox " style="text-decoration:none;" title="Platinum SEO Preview"><span class="dashicons dashicons-search"></span><?php esc_html_e( ' ', 'platinum-seo-pack' ); ?></a></li>
		    <li class="social"><a href="#TB_inline?height=600&amp;width=750&amp;inlineId=pspanalysispar" class="tbpsp thickbox " style="text-decoration:none;" title="Platinum SEO Analysis"><?php esc_html_e( ' ', 'platinum-seo-pack' ); ?><span class="dashicons dashicons-dashboard"></span></a></li>
		</ul></div>
		<div id="psp-meta-box-parent">
		    <div id="psp-meta-box">
			<ul class="psp-metabox-tabs" id="psp-metabox-tabs">
			    <?php if ( $this->psp_helper->user_has_access('metabox') ) { ?>
				    <li class="basic"><a href="#basic" title="Generic SEO"><span class="dashicons dashicons-admin-generic"></span><?php esc_html_e( ' SEO', 'platinum-seo-pack' ); ?></a></li>
				<?php } ?>
				<?php if ( $this->psp_helper->user_has_access('analysis') ) { ?>
				    <li class="analysis"><a href="#analysis" title="SEO Analysis"><span class="dashicons dashicons-dashboard"></span><?php esc_html_e( ' Analysis', 'platinum-seo-pack' ); ?></a></li>
				<?php } ?>
				<?php if (!$psp_posttype_metabox_advanced_hidden || is_super_admin()) { ?>
				<?php if ( $this->psp_helper->user_has_access('advancedseo') ) {  ?>
				    <li class="advanced"><a href="#advanced" title="Advanced SEO"><span class="dashicons dashicons-admin-tools"></span><?php esc_html_e( ' Advanced', 'platinum-seo-pack' ); ?></a></li>
				<?php } ?>
				<?php if ( $this->psp_helper->user_has_access('basicsocial') ) { ?>
				    <li class="social"><a href="#bsocial" title="Basic Social"><span class="dashicons dashicons-share"></span><?php esc_html_e( '  Basic', 'platinum-seo-pack' ); ?></a></li>
				<?php } ?>
				<?php if ( $this->psp_helper->user_has_access('advancedsocial') ) { ?>
				    <li class="social"><a href="#asocial" title="Advanced Social"><span class="dashicons dashicons-networking"></span><?php esc_html_e( ' Advanced', 'platinum-seo-pack' ); ?></a></li>
				<?php } ?>
				<?php if ( $this->psp_helper->user_has_access('internallinks') ) { ?>
				    <li class="social"><a href="#ilinks" title="Internal Links"><span class="dashicons dashicons-admin-links"></span><?php esc_html_e( ' Internal Links', 'platinum-seo-pack' ); ?></a></li>
				<?php } ?>
				
				<?php } ?>				
			</ul>
			<br class="clear" />
			<?php if ( $this->psp_helper->user_has_access('metabox') ) { ?>
			    <div id="basic" class="psptab">
				<?php include_once( 'psp_basic_metabox_renderer.php' ); ?>	
			</div>
			<?php } ?>
			<?php if ( $this->psp_helper->user_has_access('analysis') ) { ?>
			    <div id="analysis" class="psptab">
				    <?php include_once( 'psp_analysis_metabox_renderer.php' ); ?>	
			    </div>
			<?php } ?>
			<?php
			 if (!$psp_posttype_metabox_advanced_hidden || is_super_admin()) {
			 // if ($psp_posttype_metabox_advanced_hidden ) {
			?>
			<?php if ( $this->psp_helper->user_has_access('advancedseo') ) { ?>
			    <div id="advanced" class="hidden psptab wrap">
				    <?php include_once( 'psp_advanced_metabox_renderer.php' ); ?>
			    </div> 	
			<?php } ?>
			<?php if ( $this->psp_helper->user_has_access('basicsocial') ) { ?>
			    <div id="bsocial" class="psptab">
				    <?php include_once( 'psp_basic_social_metabox_renderer.php' ); ?>	
			    </div>
			<?php } ?>
			<?php if ( $this->psp_helper->user_has_access('advancedsocial') ) { ?>
			<div id="asocial" class="psptab">
				<?php 	if ($psp_premium_valid && $psp_premium_status) { 
							$metabox_template = apply_filters('psp_metabox_template', '');
							if (empty($metabox_template)) { ?>
							    <div class="psp-bs">
						        <div class="container">
    						     <?php    
    							    wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
    							    $metabox_template = 'psp_premiumad_metabox_renderer.php';
    							    include( $metabox_template ); 
    							    //include( 'psp_premiumad_metabox_renderer.php' ); ?>
    							 </div>
    							 </div>
						    <?php	} else {
							    include_once( $metabox_template ); 
							}
						} else { ?>
							<div class="psp-bs">
						     <div class="container">
						<?php 
						    wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
						     //wp_enqueue_style("psp-htmlsettings-css", plugins_url( '/css/psp-html-settings.css', __FILE__ ));
							include( 'psp_premiumad_metabox_renderer.php' ); ?>
							</div></div>
				<?php	}
				?>	
			</div>
			<?php }	?>	
			<?php if ( $this->psp_helper->user_has_access('internallinks') ) { ?>
			<div id="ilinks" class="psptab">
				<?php 	if ($psp_premium_valid && $psp_premium_status) { 
							$metabox_template = apply_filters('psp_ilinks_template', '');
							if (empty($metabox_template)) { ?>
							    <div class="psp-bs">
						        <div class="container">
						         <?php    
							    //error_log($metabox_template);
							    wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
							    $metabox_template = 'psp_premiumad_metabox_renderer.php';
							    include( $metabox_template ); 
							    //include( 'psp_premiumad_metabox_renderer.php' ); ?>
							    </div></div>
							<?php } else {
							    //error_log($metabox_template);
							    include_once( $metabox_template ); 
							}
						} else { ?>
							<div class="psp-bs">
						     <div class="container">
						<?php 
						    wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
						     //wp_enqueue_style("psp-htmlsettings-css", plugins_url( '/css/psp-html-settings.css', __FILE__ ));
							include( 'psp_premiumad_metabox_renderer.php' ); ?>
							</div></div>
				<?php	}
				?>	
			</div>
			<?php }	?>
			<?php
			 } 
			?>
		</div>
		</div>			
	<?php		
	}

	//Credits
	public static function add_credits()
	{ 
		if (is_home() || is_front_page()) {
	?>
		<small><a href="https://techblissonline.com/platinum-wordpress-seo-plugin/" target="_blank" rel="nofollow">Platinum WP SEO</a> by <a href="https://techblissonline.com/" target="_blank">Techblissonline.com</a></small>
	<?php	
		}
	}
	
	/**
	 * When the post is saved, saves Techblissonline psp seo data and social data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	function psp_save_seo_meta_box_data( $post_id ) {
	
		$psp_post_seo_data_original = array();
		$psp_post_social_data_original = array();
		$psp_schema = array();
		$psp_allowed_protocols = array('http','https', 'feed');

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set and is valid.
		if ( ! isset( $_POST['psp_seo_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_key($_POST['psp_seo_meta_box_nonce']), 'do_psp_seo_meta_box' )) {
			return;
		}		

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == sanitize_key($_POST['post_type'])) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */
		
		// Make sure that it is set.
		if ( ! isset( $_POST['psp_seo_meta'] ) && ! isset( $_POST['psp_social_meta'] ) ) {
			return;
		} else {
		    
		    $psp_settings = get_option('psp_sitewide_settings');			
		    $psp_metabox_advanced_hidden = isset($psp_settings['hide_metabox_advanced']) ? $psp_settings['hide_metabox_advanced'] : '';
		    
		    if (is_super_admin()) $psp_metabox_advanced_hidden = false;
		    
		    $psp_original_meta = $this->psp_do_seo_metabox( NULL, [], true );
		    /**
			if (!empty($this->psp_post_meta_original)) { 
				$psp_post_seo_data_original = $this->psp_post_meta_original;
			} 
			**/
			if (!empty($psp_original_meta['seo_meta'])) { 
				$psp_post_seo_data_original = $psp_original_meta['seo_meta'];
			} 
			/***
			if(!empty($this->psp_post_social_meta_original)) {
				$psp_post_social_data_original = $this->psp_post_social_meta_original;
			}
			***/
			if (!empty($psp_original_meta['social_meta'])) { 
				$psp_post_social_data_original = $psp_original_meta['social_meta'];
			} 
			
			$psp_post_seo_data_current = !empty($_POST['psp_seo_meta']) ? $this->psp_sanitze_seo_data($_POST['psp_seo_meta']) : array();			
			
			if(isset($psp_post_seo_data_original) && !empty($psp_post_seo_data_original)) {
			    $psp_post_seo_data = array_merge((array)$psp_post_seo_data_original, (array)$psp_post_seo_data_current);
			} else {
			    $psp_post_seo_data = $psp_post_seo_data_current;
			}
			
			$psp_post_social_data_current = !empty($_POST['psp_social_meta']) ? $this->psp_sanitze_social_data($_POST['psp_social_meta']) : array();
			
			if(isset($psp_post_social_data_original) && !empty($psp_post_social_data_original)) {
			    $psp_post_social_data = array_merge((array)$psp_post_social_data_original, (array)$psp_post_social_data_current);
			} else {
			    $psp_post_social_data = $psp_post_social_data_current;
			}
		}
		
		if (isset($psp_post_seo_data) && !empty($psp_post_seo_data)) {
			// Sanitize SEO data.
			//$psp_post_seo_data = $this->psp_sanitze_seo_data( $psp_post_seo_data );			
		}
		
		//if advance metaboxes are hidden update only the basic
			//Update the meta fields as separate records in the database.
		if ($psp_metabox_advanced_hidden) {	
    		if (!empty($psp_post_seo_data['title'])) {
    			//update_post_meta( $post_id, '_techblissonline_psp_title', $psp_post_seo_data['title'] );
				update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_title', $psp_post_seo_data['title'] );
    		} else {
    			//delete_post_meta( $post_id, '_techblissonline_psp_title');
				delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_title');
    		}
    		
    		if (!empty($psp_post_seo_data['titleformat'])) {
    			//update_post_meta( $post_id, '_techblissonline_psp_titleformat', $psp_post_seo_data['titleformat'] );
				update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_titleformat', $psp_post_seo_data['titleformat'] );
    		} else {
    			//update_post_meta( $post_id, '_techblissonline_psp_titleformat', "");
    			//delete_post_meta( $post_id, '_techblissonline_psp_titleformat');
				delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_titleformat');
    		}
    		
    		if (!empty($psp_post_seo_data['description'])) {
    			//update_post_meta( $post_id, '_techblissonline_psp_description', $psp_post_seo_data['description'] );
				update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_description', $psp_post_seo_data['description'] );
    		} else {
    			//delete_post_meta( $post_id, '_techblissonline_psp_description');
				delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_description');
    		}
    		
    		if (!empty($psp_post_seo_data['descformat'])) {
    			//update_post_meta( $post_id, '_techblissonline_psp_descformat', $psp_post_seo_data['descformat'] );
				update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_descformat', $psp_post_seo_data['descformat'] );
    		} else {
    			//update_post_meta( $post_id, '_techblissonline_psp_descformat', "");
    			//delete_post_meta( $post_id, '_techblissonline_psp_descformat');
				delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_descformat');
    		}
    		
    		if (!empty($psp_post_seo_data['maxsnippet'])) {
    			//update_post_meta( $post_id, '_techblissonline_psp_maxsnippet', $psp_post_seo_data['maxsnippet'] );
				update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_maxsnippet', $psp_post_seo_data['maxsnippet'] );
    		} else {
    			//delete_post_meta( $post_id, '_techblissonline_psp_maxsnippet');
				delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_maxsnippet');
    		}
    		
    		if (!empty($psp_post_seo_data['keywords'])) {
    			//update_post_meta( $post_id, '_techblissonline_psp_keywords', $psp_post_seo_data['keywords'] );
				update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_keywords', $psp_post_seo_data['keywords'] );
    		} else {
    			//delete_post_meta( $post_id, '_techblissonline_psp_keywords');
				delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_keywords');
    		}
    		return;
		}
		//updation of basic meta complete
		// Sanitize social data.
		//$psp_post_social_meta_data = array();
		
		/**********
		$psp_post_social_enablers = array();
		
		if ( isset( $psp_post_social_data['psp_og_tags_enabled'] ) ) {
			$psp_post_social_enablers['psp_og_tags_enabled'] =  $psp_post_social_data['psp_og_tags_enabled'] ;
		}
		
		if ( isset( $psp_post_social_data['psp_twitter_card_enabled'] ) ) {
			$psp_post_social_enablers['psp_twitter_card_enabled'] =  $psp_post_social_data['psp_twitter_card_enabled'] ;
		}
		
		if ( isset( $psp_post_social_data['psp_schemaorg_markup_enabled'] ) ) {
			$psp_post_social_enablers['psp_schemaorg_markup_enabled'] =  $psp_post_social_data['psp_schemaorg_markup_enabled'] ;
		}
		
		
		if (isset($psp_post_social_data) && !empty($psp_post_social_data)) {
			$psp_post_social_data = $this->psp_sanitze_social_data( $psp_post_social_data );
		}
		**********/
		
		$psp_post_fb_ogtype_properties_temp_arr = array();
		$psp_post_fb_ogtype_properties = array();
		if ( !empty( $psp_post_social_data['fb_ogtype_properties'] ) ) {		
			$psp_post_fb_ogtype_properties_temp_arr =  explode("\r\n", $psp_post_social_data['fb_ogtype_properties']);
			foreach ($psp_post_fb_ogtype_properties_temp_arr as $psp_post_fb_ogtype_property) {
			
				list($k, $v) = explode("=", $psp_post_fb_ogtype_property);
				
				if( !empty($k) && !empty($v)) {
				    $psp_post_fb_ogtype_properties[esc_attr($k)] = esc_attr(htmlentities($v));
				}
								
			}
		}
		
		//FB media properties - 6/8/2019
		$psp_post_fb_media_properties_temp_arr = array();
		$psp_post_fb_media_properties = array();
		if ( !empty( $psp_post_social_data['fb_media_properties'] ) ) {			
			$psp_post_fb_media_properties_temp_arr =  explode("\r\n", $psp_post_social_data['fb_media_properties']);
			foreach ($psp_post_fb_media_properties_temp_arr as $psp_post_fb_media_property) {
			
				list($a, $b) = explode("=", $psp_post_fb_media_property);
				if( !empty($a) && !empty($b)) {
				    $psp_post_fb_media_properties[esc_attr($a)] = esc_attr(htmlentities($b));
				}
				
			}
		}
		
		$psp_post_tw_data_images = array();
		
		if ( !empty( $psp_post_social_data['tw_image'] ) ) {			
			$psp_post_tw_data_images['tw_image'] =  esc_url_raw($psp_post_social_data['tw_image']);
			$psp_post_social_data['tw_image'] = esc_url_raw($psp_post_social_data['tw_image']);
		}
		if ( !empty( $psp_post_social_data['tw_image_1'] ) ) {			
			$psp_post_tw_data_images['tw_image_1'] =  esc_url_raw($psp_post_social_data['tw_image_1']);
		}
		if ( !empty( $psp_post_social_data['tw_image_2'] ) ) {			
			$psp_post_tw_data_images['tw_image_2'] =  esc_url_raw($psp_post_social_data['tw_image_2']);
		}
		if ( !empty( $psp_post_social_data['tw_image_3'] ) ) {			
			$psp_post_tw_data_images['tw_image_3'] =  esc_url_raw($psp_post_social_data['tw_image_3']);
		}
				
		$psp_post_social_tw_label_data = array();
		
		if ( !empty( $psp_post_social_data['tw_label_1'] ) &&  !empty( $psp_post_social_data['tw_data_1'] )) {			
			$psp_post_social_tw_label_data['tw_label_1'] =  esc_attr($psp_post_social_data['tw_label_1']);
			$psp_post_social_tw_label_data['tw_data_1'] =  esc_attr($psp_post_social_data['tw_data_1']);
		}
		if ( !empty( $psp_post_social_data['tw_label_2'] ) &&  !empty( $psp_post_social_data['tw_data_2'] )) {			
			$psp_post_social_tw_label_data['tw_label_2'] =  esc_attr($psp_post_social_data['tw_label_2']);
			$psp_post_social_tw_label_data['tw_data_2'] =  esc_attr($psp_post_social_data['tw_data_2']);
		}		
		
		//Sanitize social data
		if (isset($psp_post_social_data) && !empty($psp_post_social_data)) {
			//$psp_post_social_data = $this->psp_sanitze_social_data( $psp_post_social_data );
		}

		// Update the meta field in the database.
		//update_post_meta( $post_id, '_psp_post_seo_meta', $psp_post_seo_data );
		
		//Update the meta fields as separate records in the database.
		if (!empty($psp_post_seo_data['title'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_title', $psp_post_seo_data['title'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_title', $psp_post_seo_data['title'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_title');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_title');
		}
		
		if (!empty($psp_post_seo_data['titleformat'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_titleformat', $psp_post_seo_data['titleformat'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_titleformat', $psp_post_seo_data['titleformat'] );
		} else {
			//update_post_meta( $post_id, '_techblissonline_psp_titleformat', "");
			//delete_post_meta( $post_id, '_techblissonline_psp_titleformat');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_titleformat');
		}
		
		if (!empty($psp_post_seo_data['description'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_description', $psp_post_seo_data['description'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_description', $psp_post_seo_data['description'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_description');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_description');
		}
		
		if (!empty($psp_post_seo_data['descformat'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_descformat', $psp_post_seo_data['descformat'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_descformat', $psp_post_seo_data['descformat'] );
		} else {
			//update_post_meta( $post_id, '_techblissonline_psp_descformat', "");
			//delete_post_meta( $post_id, '_techblissonline_psp_descformat');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_descformat');
		}
		
		if (!empty($psp_post_seo_data['maxsnippet'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_maxsnippet', $psp_post_seo_data['maxsnippet'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_maxsnippet', $psp_post_seo_data['maxsnippet'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_maxsnippet');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_maxsnippet');
		}
		
		if (!empty($psp_post_seo_data['keywords'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_keywords', $psp_post_seo_data['keywords'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_keywords', $psp_post_seo_data['keywords'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_keywords');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_keywords');
		}
		
		if (!empty($psp_post_seo_data['maxvideo'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_maxvideo', $psp_post_seo_data['maxvideo'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_maxvideo', $psp_post_seo_data['maxvideo'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_maxvideo');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_maxvideo');
		}
		
		if (!empty($psp_post_seo_data['maximage'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_maximage', $psp_post_seo_data['maximage'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_maximage', $psp_post_seo_data['maximage'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_maximage');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_maximage');
		}
		
		if (!empty($psp_post_seo_data['canonical_url'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_canonical_url', $psp_post_seo_data['canonical_url'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_canonical_url', $psp_post_seo_data['canonical_url'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_canonical_url');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_canonical_url');
		}
		
		if (!empty($psp_post_seo_data['schema_string'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_schema_string', $psp_post_seo_data['schema_string'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_schema_string', $psp_post_seo_data['schema_string'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_schema_string');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_schema_string');
		}
		
		if (!empty($psp_post_seo_data['robots'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_robots_meta', $psp_post_seo_data['robots'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_robots_meta', $psp_post_seo_data['robots'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_robots_meta');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_robots_meta');
		}
		
		if (!empty($psp_post_seo_data['noindex'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_noindex', $psp_post_seo_data['noindex'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_noindex', $psp_post_seo_data['noindex'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_noindex');
			//update_post_meta( $post_id, '_techblissonline_psp_noindex', 0 );
			//update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_noindex', 0 );
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_noindex');
		}
        if (!empty($psp_post_seo_data['nofollow'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_nofollow', $psp_post_seo_data['nofollow'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_nofollow', $psp_post_seo_data['nofollow'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_nofollow');
			//update_post_meta( $post_id, '_techblissonline_psp_nofollow', 0 );
			//update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_nofollow', 0 );
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_nofollow');
		}
		
        if (!empty($psp_post_seo_data['nositemap'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_nositemap', $psp_post_seo_data['nositemap'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_nositemap', $psp_post_seo_data['nositemap'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_nositemap');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_nositemap');
		}
		
		if (!empty($psp_post_seo_data['noarchive'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_noarchive', $psp_post_seo_data['noarchive'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_noarchive', $psp_post_seo_data['noarchive'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_noarchive');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_noarchive');
		}
		
		if (!empty($psp_post_seo_data['nosnippet'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_nosnippet', $psp_post_seo_data['nosnippet'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_nosnippet', $psp_post_seo_data['nosnippet'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_nosnippet');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_nosnippet');
		}
		
		if (!empty($psp_post_seo_data['noimageindex'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_noimageidx', $psp_post_seo_data['noimageindex'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_noimageidx', $psp_post_seo_data['noimageindex'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_noimageidx');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_noimageidx');
		}
		
		if (!empty($psp_post_seo_data['redirect_to_url'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_redirect_to_url', $psp_post_seo_data['redirect_to_url'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_redirect_to_url', $psp_post_seo_data['redirect_to_url'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_redirect_to_url');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_redirect_to_url');			
		}
		
		if (!empty($psp_post_seo_data['redirect_status_code'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_redirect_status_code', $psp_post_seo_data['redirect_status_code'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_redirect_status_code', $psp_post_seo_data['redirect_status_code'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_redirect_status_code');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_redirect_status_code');
		}
		
		if (!empty($psp_post_seo_data['preferred_tax'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_preferred_taxonomy', $psp_post_seo_data['preferred_tax'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_preferred_taxonomy', $psp_post_seo_data['preferred_tax'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_preferred_taxonomy');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_preferred_taxonomy');
		}
		
		if (!empty($psp_post_seo_data['preferred_term'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_preferred_term', $psp_post_seo_data['preferred_term'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_preferred_term', $psp_post_seo_data['preferred_term'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_preferred_term');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_preferred_term');
		}
		
		//linkingkeywords
		if (!empty($psp_post_seo_data['linkingkeywords'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_linkingkeywords', $psp_post_seo_data['linkingkeywords'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_linkingkeywords', $psp_post_seo_data['linkingkeywords'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_linkingkeywords');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_linkingkeywords');
		}
		
		//update disable flags
		$psp_post_seo_data_disablers = array();
		
		if (!empty($psp_post_seo_data['disable_title'])) $psp_post_seo_data_disablers['disable_title'] = $psp_post_seo_data['disable_title'];
		if (!empty($psp_post_seo_data['disable_description'])) $psp_post_seo_data_disablers['disable_description'] = $psp_post_seo_data['disable_description'];
		if (!empty($psp_post_seo_data['disable_keywords'])) $psp_post_seo_data_disablers['disable_keywords'] = $psp_post_seo_data['disable_keywords'];
		if (!empty($psp_post_seo_data['disable_canonical'])) $psp_post_seo_data_disablers['disable_canonical'] = $psp_post_seo_data['disable_canonical'];
		if (!empty($psp_post_seo_data['disable_title_format'])) $psp_post_seo_data_disablers['disable_title_format'] = $psp_post_seo_data['disable_title_format'];
		if (!empty($psp_post_seo_data['disable_desc_format'])) $psp_post_seo_data_disablers['disable_desc_format'] = $psp_post_seo_data['disable_desc_format'];
		if (!empty($psp_post_seo_data['disable_psp'])) $psp_post_seo_data_disablers['disable_psp'] = $psp_post_seo_data['disable_psp'];
		
		if (isset($psp_post_seo_data_disablers) && !empty($psp_post_seo_data_disablers)) {
			//update_post_meta( $post_id, '_techblissonline_psp_disable_flags', $psp_post_seo_data_disablers);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_disable_flags', $psp_post_seo_data_disablers );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_disable_flags');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_disable_flags');
		}
		//update social meta data
		
		/*******
		$psp_post_social_data['fb_title'] = $psp_post_seo_data['fb_title'];
		$psp_post_social_data['fb_description'] = $psp_post_seo_data['fb_description'];
		$psp_post_social_data['fb_image'] = $psp_post_seo_data['fb_image'];
		$psp_post_social_data['tw_title'] = $psp_post_seo_data['tw_title'];
		$psp_post_social_data['tw_description'] = $psp_post_seo_data['tw_description'];
		$psp_post_social_data['tw_image'] = $psp_post_seo_data['tw_image'];
		$psp_post_social_data['sc_title'] = $psp_post_seo_data['sc_title'];
		$psp_post_social_data['sc_description'] = $psp_post_seo_data['sc_description'];
		$psp_post_social_data['sc_image'] = $psp_post_seo_data['sc_image'];
		************/
		/*******
		if (!empty($psp_post_social_data)) {
			update_post_meta( $post_id, '_techblissonline_psp_social_data', $psp_post_social_data);
		}
		************/
		//update_post_meta( $post_id, '_techblissonline_psp_social_enabled', $psp_post_social_enablers);
		
		if (!empty($psp_post_social_data['sc_title'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_sc_title', $psp_post_social_data['sc_title'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_sc_title', $psp_post_social_data['sc_title'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_sc_title');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_sc_title');
		}
		
		if (!empty($psp_post_social_data['sc_description'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_sc_description', $psp_post_social_data['sc_description'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_sc_description', $psp_post_social_data['sc_description'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_sc_description');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_sc_description');
		}
		
		if (!empty($psp_post_social_data['sc_image'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_sc_image', $psp_post_social_data['sc_image'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_sc_image', $psp_post_social_data['sc_image'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_sc_image');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_sc_image');
		}
		
		if (!empty($psp_post_social_data['tw_card_type'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_card_type', $psp_post_social_data['tw_card_type'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_card_type', $psp_post_social_data['tw_card_type'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_card_type');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_card_type');
		}
		
		if (!empty($psp_post_social_data['tw_title'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_title', $psp_post_social_data['tw_title'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_title', $psp_post_social_data['tw_title'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_title');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_title');
		}
		
		if (!empty($psp_post_social_data['tw_description'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_description', $psp_post_social_data['tw_description'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_description', $psp_post_social_data['tw_description'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_description');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_description');
		}
		
		/***********
		if (!empty($psp_post_social_data['tw_image'])) update_post_meta( $post_id, '_techblissonline_psp_tw_image', $psp_post_social_data['tw_image'] );
		if (!empty($psp_post_social_data['tw_image_1'])) update_post_meta( $post_id, '_techblissonline_psp_tw_image_1', $psp_post_social_data['tw_image_1'] );
		if (!empty($psp_post_social_data['tw_image_2'])) update_post_meta( $post_id, '_techblissonline_psp_tw_image_2', $psp_post_social_data['tw_image_2'] );
		if (!empty($psp_post_social_data['tw_image_3'])) update_post_meta( $post_id, '_techblissonline_psp_tw_image_3', $psp_post_social_data['tw_image_3'] );
		************/
		/**
		if (!empty($psp_post_tw_data_images)) {
			update_post_meta( $post_id, '_techblissonline_psp_tw_images', $psp_post_tw_data_images);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_images', $psp_post_tw_data_images );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_images');
			delete_metadata( $post_id, '_techblissonline_psp_tw_images');
		}
		**/
		if (!empty($psp_post_social_data['tw_image'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_image', $psp_post_social_data['tw_image'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_image', $psp_post_social_data['tw_image'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_image');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_image');
		}
		/************
		if (!empty($psp_post_social_data['tw_label_1'])) update_post_meta( $post_id, '_techblissonline_psp_tw_label_1', $psp_post_social_data['tw_label_1'] );
		if (!empty($psp_post_social_data['tw_data_1'])) update_post_meta( $post_id, '_techblissonline_psp_tw_data_1', $psp_post_social_data['tw_data_1'] );
		if (!empty($psp_post_social_data['tw_label_2'])) update_post_meta( $post_id, '_techblissonline_psp_tw_label_2', $psp_post_social_data['tw_label_2'] );
		if (!empty($psp_post_social_data['tw_data_2'])) update_post_meta( $post_id, '_techblissonline_psp_tw_data_2', $psp_post_social_data['tw_data_2'] );
		
		
		if (!empty($psp_post_social_tw_label_data)) {
			update_post_meta( $post_id, '_techblissonline_psp_tw_label_data', $psp_post_social_tw_label_data);
		} else {
			delete_post_meta( $post_id, '_techblissonline_psp_tw_label_data');
		}
		**********/
		if (!empty($psp_post_social_data['tw_creator'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_creator', $psp_post_social_data['tw_creator'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_creator', $psp_post_social_data['tw_creator'] );
			
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_creator');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_creator');
		}
		
		if (!empty($psp_post_social_data['tw_imagealt'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_imagealt', $psp_post_social_data['tw_imagealt'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_imagealt', $psp_post_social_data['tw_imagealt'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_imagealt');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_imagealt');
		}
		
		if (!empty($psp_post_social_data['tw_player'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_player', $psp_post_social_data['tw_player'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_player', $psp_post_social_data['tw_player'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_player');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_player');
		}
		
		if (!empty($psp_post_social_data['tw_player_stream'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_player_stream', $psp_post_social_data['tw_player_stream'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_player_stream', $psp_post_social_data['tw_player_stream'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_player_stream');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_player_stream');
		}
		
		if (!empty($psp_post_social_data['tw_player_width'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_player_width', $psp_post_social_data['tw_player_width']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_player_width', $psp_post_social_data['tw_player_width'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_player_width');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_player_width');
		}
		
		if (!empty($psp_post_social_data['tw_player_height'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_player_height', $psp_post_social_data['tw_player_height']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_player_height', $psp_post_social_data['tw_player_height'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_player_height');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_player_height');
		}
		
		if (!empty($psp_post_social_data['tw_app_country'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_country', $psp_post_social_data['tw_app_country']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_country', $psp_post_social_data['tw_app_country'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_country');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_country');
		}
		
		if (!empty($psp_post_social_data['tw_app_name_iphone'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_name_iphone', $psp_post_social_data['tw_app_name_iphone']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_name_iphone', $psp_post_social_data['tw_app_name_iphone'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_name_iphone');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_name_iphone');
		}
		
		if (!empty($psp_post_social_data['tw_app_id_iphone'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_id_iphone', $psp_post_social_data['tw_app_id_iphone']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_id_iphone', $psp_post_social_data['tw_app_id_iphone'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_id_iphone');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_id_iphone');
		}
		
		if (!empty($psp_post_social_data['tw_app_url_iphone'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_url_iphone', $psp_post_social_data['tw_app_url_iphone']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_url_iphone', $psp_post_social_data['tw_app_url_iphone'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_url_iphone');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_url_iphone');
		}
		
		if (!empty($psp_post_social_data['tw_app_name_ipad'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_name_ipad', $psp_post_social_data['tw_app_name_ipad']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_name_ipad', $psp_post_social_data['tw_app_name_ipad'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_name_ipad');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_name_ipad');
		}
		if (!empty($psp_post_social_data['tw_app_id_ipad'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_id_ipad', $psp_post_social_data['tw_app_id_ipad']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_id_ipad', $psp_post_social_data['tw_app_id_ipad'] );			
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_id_ipad');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_id_ipad');
		}
		if (!empty($psp_post_social_data['tw_app_url_ipad'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_url_ipad', $psp_post_social_data['tw_app_url_ipad']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_url_ipad', $psp_post_social_data['tw_app_url_ipad'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_url_ipad');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_url_ipad');			
		}
		
		if (!empty($psp_post_social_data['tw_app_name_googleplay'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_name_googleplay', $psp_post_social_data['tw_app_name_googleplay']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_name_googleplay', $psp_post_social_data['tw_app_name_googleplay'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_name_googleplay');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_name_googleplay');	
		}
		if (!empty($psp_post_social_data['tw_app_id_googleplay'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_id_googleplay', $psp_post_social_data['tw_app_id_googleplay']);	
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_id_googleplay', $psp_post_social_data['tw_app_id_googleplay'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_id_googleplay');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_id_googleplay');	
		}
		if (!empty($psp_post_social_data['tw_app_url_googleplay'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_tw_app_url_googleplay', $psp_post_social_data['tw_app_url_googleplay']);
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_url_googleplay', $psp_post_social_data['tw_app_url_googleplay'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_tw_app_url_googleplay');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_tw_app_url_googleplay');
		}
		
		if (!empty($psp_post_social_data['fb_og_type'])){
			//update_post_meta( $post_id, '_techblissonline_psp_fb_og_type', $psp_post_social_data['fb_og_type'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_og_type', $psp_post_social_data['fb_og_type'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_fb_og_type');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_og_type');
		}
		
		if (!empty($psp_post_social_data['fb_title'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_fb_title', $psp_post_social_data['fb_title'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_title', $psp_post_social_data['fb_title'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_fb_title');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_title');
		}
		
		if (!empty($psp_post_social_data['fb_description'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_fb_description', $psp_post_social_data['fb_description'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_description', $psp_post_social_data['fb_description'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_fb_description');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_description');
		}
		if (!empty($psp_post_social_data['fb_image'])) {
			//update_post_meta( $post_id, '_techblissonline_psp_fb_image', $psp_post_social_data['fb_image'] );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_image', $psp_post_social_data['fb_image'] );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_fb_image');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_image');
		}
		//if (isset($psp_post_social_data['fb_ogtype_properties'])) update_post_meta( $post_id, '_techblissonline_psp_fb_ogtype_properties', $psp_post_social_data['fb_ogtype_properties'] );
		if (!empty($psp_post_fb_ogtype_properties)) {
			//update_post_meta( $post_id, '_techblissonline_psp_fb_ogtype_properties', $psp_post_fb_ogtype_properties );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_ogtype_properties', $psp_post_fb_ogtype_properties );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_fb_ogtype_properties');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_ogtype_properties');			
		}
		//if (isset($psp_post_social_data['fb_media_properties'])) update_post_meta( $post_id, '_techblissonline_psp_fb_media_properties', $psp_post_social_data['fb_media_properties'] );
		if (!empty($psp_post_fb_media_properties)) {
			//update_post_meta( $post_id, '_techblissonline_psp_fb_media_properties', $psp_post_fb_media_properties );
			update_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_media_properties', $psp_post_fb_media_properties );
		} else {
			//delete_post_meta( $post_id, '_techblissonline_psp_fb_media_properties');
			delete_metadata( 'platinumseo', $post_id, '_techblissonline_psp_fb_media_properties');	
		}
		
		//update google sitemap generator
		 if (!empty($psp_post_seo_data['nositemap'])) {
			 $this->psp_update_wps("excluded_post_ids", $post_id, true );
		     $this->psp_update_gsg("sm_b_exclude", $post_id, true );
			 $psp_exclude = true;
		 } else {
			  $this->psp_update_wps("excluded_post_ids", $post_id, false );	
		      $this->psp_update_gsg("sm_b_exclude", $post_id, false );
			  $psp_exclude = false;
		 }
		 $psp_id = !empty($post_id) ? $post_id : '';
		//techblissonline_psp_update_sitemap - action hook to attach your function
		//$psp_id - Post ID or Term ID 
		//$psp_exclude - Boolean indicating whether to exclude (true) or include (false) this post in the sitemap
		do_action( 'techblissonline_psp_update_sitemap', $psp_id, $psp_exclude );
	}
	
	//update Platinum SEO Settings for wordPress sitemap
    private function psp_update_wps( $gsckey, $post_id, $exclude = false ) {   
    	    	    
		if (empty($post_id) || empty($gsckey) ) {
			return;
		}
		
		$psp_sm_settings = get_option('psp_sitemap');
		//$this->psp_sitemap_settings = $psp_sm_settings;	
		
		$excluded_ids = array();		
		
		if( !empty( $psp_sm_settings[$gsckey] ) ) {
		    //error_log(print_r($psp_sm_settings[$gsckey], true));
			$excluded_ids = explode(",", $psp_sm_settings[$gsckey]); 
		}
		 
		if( !empty( $excluded_ids ) ) {
			//$excluded_ids = array_map( 'intval', $excluded_ids ); 
		}	
	
		if($exclude) {
			if(empty($excluded_ids) || !in_array($post_id, $excluded_ids)) {
				//$excluded = array_push($excluded_ids, $post_id);
				$excluded_ids[] = $post_id;
				$psp_sm_settings[$gsckey] = implode(",", $excluded_ids); 
				update_option("psp_sitemap", $psp_sm_settings);
			}
		} else {		
		    
			if(!empty($excluded_ids) && in_array($post_id, $excluded_ids)) {
			    
			    
				if (($id = array_search($post_id, $excluded_ids)) !== false) {
				    //error_log(print_r($excluded_ids[$id], true));
					unset($excluded_ids[$id]);
				}	
				if(!empty($excluded_ids)) {
					$psp_sm_settings[$gsckey] = implode(",", $excluded_ids); 	
				} else {
					//unset($psp_sm_settings[$gsckey]);
					$psp_sm_settings[$gsckey] = '';
				}
				update_option("psp_sitemap", $psp_sm_settings);
			}
		}	
    	
    }
	
	//update GoogleSitemapGenerator
    private function psp_update_gsg( $gsckey, $post_id, $exclude = false ) {
    	$className = 'GoogleSitemapGenerator';
    	if( is_plugin_active( 'google-sitemap-generator/sitemap.php' ) ) {
        
    	//if(class_exists($className)) {
    	    
    	    if (empty($post_id) || empty($gsckey) ) {
    	        return;
    	    }
    		$sm_options = get_option("sm_options");
    		$sm_excludes = array();
    		$excluded_post_ids = array();
    		
    		//$gsckey = "b_exclude";
    		if(!empty($sm_options) && array_key_exists($gsckey, $sm_options)) {
    				$sm_excludes =  (array) $sm_options[$gsckey];
    			}
    			
    		if (!empty($sm_excludes)) $excluded_post_ids = array_map('intval',$sm_excludes);
    		
    		if($exclude) {
    			if(empty($excluded_post_ids) || !in_array($post_id, $excluded_post_ids)) {
    					//$excluded = array_push($excluded_post_ids, $post_id);
    					$excluded_post_ids[] = $post_id;
    					$sm_options[$gsckey] = $excluded_post_ids;
    					update_option("sm_options", $sm_options);
    				}
    		} else {		
    			if(!empty($excluded_post_ids) && in_array($post_id, $excluded_post_ids)) {
					if (($id = array_search($post_id, $excluded_post_ids)) !== false) {
						unset($excluded_post_ids[$id]);
					}	
					if(!empty($excluded_post_ids)) {
					    $sm_options[$gsckey] = $excluded_post_ids;	
					} else {
					    //unset($sm_options[$gsckey]);
					    $sm_options[$gsckey] = array();
					}
					update_option("sm_options", $sm_options);
				}
    		}	
    	}
    }
	
	//sanitize seo data
	protected function psp_sanitze_seo_data($psp_seo_data) {
	
		$psp_allowed_protocols = array('http','https', 'feed');
		$psp_sanitized_seo_data = array();
	
		// Sanitize SEO data.
		$psp_sanitized_seo_data['title'] = '';
		if ( isset( $psp_seo_data['title'] ) ) {
			$psp_sanitized_seo_data['title'] = sanitize_text_field( $psp_seo_data['title'] );
		}
		
		$psp_sanitized_seo_data['titleformat'] = '';
		if ( isset( $psp_seo_data['titleformat'] ) ) {
			$psp_sanitized_seo_data['titleformat'] = sanitize_text_field( $psp_seo_data['titleformat'] );
			
			$psp_title_format = explode(" ", $psp_sanitized_seo_data['titleformat']);
			$titleformats = array('%seo_title%', '%wp_title%', '%sep%','%taxonomy%', '%site_name%','%site_description%', '%currentyear%');
			if(!empty($psp_title_format)) {
				if (count($psp_title_format) != count(array_intersect($psp_title_format, $titleformats))) {
					$psp_sanitized_seo_data['titleformat'] = '';
				}
			}
		}
		
		$psp_sanitized_seo_data['description'] = '';
		if ( isset( $psp_seo_data['description'] ) ) {
			$psp_sanitized_seo_data['description'] = sanitize_textarea_field( $psp_seo_data['description'] );
		}
		
		$psp_sanitized_seo_data['descformat'] = '';
		if ( isset( $psp_seo_data['descformat'] ) ) {
			$psp_sanitized_seo_data['descformat'] = sanitize_text_field( $psp_seo_data['descformat'] );
			
			$psp_desc_format = explode(" ", $psp_sanitized_seo_data['descformat']);
			$descformats = array('%seo_title%', '%wp_title%', '%sep%','%taxonomy%', '%seo_description%', '%site_name%', '%site_description%', '%currentyear%');
			if(!empty($psp_desc_format)) {
				if (count($psp_desc_format) != count(array_intersect($psp_desc_format, $descformats))) {
					$psp_sanitized_seo_data['descformat'] = '';
				}
			}
		}
		
		$psp_sanitized_seo_data['maxsnippet'] = '';
		if ( isset( $psp_seo_data['maxsnippet'] ) ) {
			$psp_sanitized_seo_data['maxsnippet'] = sanitize_text_field( $psp_seo_data['maxsnippet'] );
			if (!filter_var($psp_sanitized_seo_data['maxsnippet'], FILTER_VALIDATE_INT) && $psp_sanitized_seo_data['maxsnippet'] != 0 ) {
				$psp_sanitized_seo_data['maxsnippet'] = '';
			}
			if($psp_seo_data['maxsnippet'] == '0') $psp_sanitized_seo_data['maxsnippet'] = 'zero';
		}
		
		$psp_sanitized_seo_data['keywords'] = '';
		if ( isset( $psp_seo_data['keywords'] ) ) {
			$psp_sanitized_seo_data['keywords'] = sanitize_text_field( $psp_seo_data['keywords'] );
		}
		
		//linking keywords
		$psp_sanitized_seo_data['linkingkeywords'] = '';
		if ( isset( $psp_seo_data['linkingkeywords'] ) ) {
			$psp_sanitized_seo_data['linkingkeywords'] = sanitize_text_field( $psp_seo_data['linkingkeywords'] );
		}
		
		$psp_sanitized_seo_data['noindex'] = '';
		if ( isset( $psp_seo_data['noindex'] ) ) {
			$psp_sanitized_seo_data['noindex'] = !is_null(filter_var($psp_seo_data['noindex'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['noindex'] : '';	
		}
		
		$psp_sanitized_seo_data['nofollow'] = '';
		if ( isset( $psp_seo_data['nofollow'] ) ) {
			$psp_sanitized_seo_data['nofollow'] = !is_null(filter_var($psp_seo_data['nofollow'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['nofollow'] : '';	
		}
		
		$psp_sanitized_seo_data['noarchive'] = '';
		if ( isset( $psp_seo_data['noarchive'] ) ) {
			$psp_sanitized_seo_data['noarchive'] = !is_null(filter_var($psp_seo_data['noarchive'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['noarchive'] : '';
		}
		
		$psp_sanitized_seo_data['nosnippet'] = '';
		if ( isset( $psp_seo_data['nosnippet'] ) ) {
			$psp_sanitized_seo_data['nosnippet'] = !is_null(filter_var($psp_seo_data['nosnippet'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['nosnippet'] : '';
		}
		
		$psp_sanitized_seo_data['noimageindex'] = '';
		if ( isset( $psp_seo_data['noimageindex'] ) ) {
			$psp_sanitized_seo_data['noimageindex'] = !is_null(filter_var($psp_seo_data['noimageindex'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['noimageindex'] : '';
		}
		
		$psp_sanitized_seo_data['maxvideo'] = '';
		if ( isset( $psp_seo_data['maxvideo'] ) ) {
			$psp_sanitized_seo_data['maxvideo'] = sanitize_text_field( $psp_seo_data['maxvideo'] );
			if (!filter_var($psp_sanitized_seo_data['maxvideo'], FILTER_VALIDATE_INT) && $psp_sanitized_seo_data['maxvideo'] != 0 ) {
				$psp_sanitized_seo_data['maxvideo'] = '';
			}
			if($psp_seo_data['maxvideo'] == '0') $psp_sanitized_seo_data['maxvideo'] = 'zero';
		}
		
		$psp_sanitized_seo_data['maximage'] = '';
		if ( isset( $psp_seo_data['maximage'] ) ) {
			$psp_sanitized_seo_data['maximage'] = sanitize_text_field( $psp_seo_data['maximage'] );
			$maximgtypes = array('none', 'large', 'standard');
			if (!in_array($psp_sanitized_seo_data['maximage'], $maximgtypes)) {
				$psp_sanitized_seo_data['maximage'] = '';
			}
		}
		
		$psp_sanitized_seo_data['canonical_url'] = '';
		if ( isset( $psp_seo_data['canonical_url'] ) ) {
			$psp_sanitized_seo_data['canonical_url'] = esc_url_raw( $psp_seo_data['canonical_url'], $psp_allowed_protocols );
		}
		
		$psp_sanitized_seo_data['nositemap'] = '';
		if ( isset( $psp_seo_data['nositemap'] ) ) {
			$psp_sanitized_seo_data['nositemap'] = !is_null(filter_var($psp_seo_data['nositemap'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['nositemap'] : '';
		}
		
		$psp_sanitized_seo_data['schema_string'] = ''; 
		if ( isset( $psp_seo_data['schema_string'] ) ) {
			//$psp_seo_data['schema_string'] = sanitize_textarea_field( $psp_seo_data['schema_string'] );
			$json_schema_str = ($psp_seo_data['schema_string']);
			$json_schema_str = strip_tags($json_schema_str, '<br>');
        	if ( "[" !== mb_substr($json_schema_str, 0, 1) ) {
        	    	$json_schema_str = "[".	$json_schema_str."]";
        	}
			//error_log("Invalid JSON ".$json_schema_str);
			//validate it is a json object
			$schema_obj = json_decode(stripcslashes($json_schema_str));
            if($schema_obj === null) {
                // $schema_obj is null because the json cannot be decoded
                $psp_sanitized_seo_data['schema_string'] = 'Invalid JSON Schema'; 
                //$psp_seo_data['schema_string'] = sanitize_textarea_field( $psp_seo_data['schema_string'] );
            } else {
			    $psp_sanitized_seo_data['schema_string'] = sanitize_textarea_field( htmlentities($json_schema_str) );
			   //$psp_seo_data['schema_string'] = 'acde';
			    //$psp_seo_data['schema_string'] = json_encode($psp_seo_data['schema_string']);
            }
		}
		
		$psp_sanitized_seo_data['redirect_to_url'] = ''; 
		if ( isset( $psp_seo_data['redirect_to_url'] ) ) {
			$psp_sanitized_seo_data['redirect_to_url'] = esc_url_raw( $psp_seo_data['redirect_to_url'], $psp_allowed_protocols );
		}
		
		$psp_sanitized_seo_data['redirect_status_code'] = ''; 
		if ( isset( $psp_seo_data['redirect_status_code'] ) ) {
			$psp_sanitized_seo_data['redirect_status_code'] = sanitize_text_field( $psp_seo_data['redirect_status_code'] );
			$scodes = array('301', '302', '303', '307');
			if (!in_array($psp_sanitized_seo_data['redirect_status_code'], $scodes)) {
				$psp_sanitized_seo_data['redirect_status_code'] = '';
			}
		}
		
		$psp_sanitized_seo_data['preferred_tax'] = ''; 
		if ( isset( $psp_seo_data['preferred_tax'] ) ) {
			$psp_sanitized_seo_data['preferred_tax'] = sanitize_text_field( $psp_seo_data['preferred_tax'] );
			
			$builtin_tax = array("category", "post_tag");
			$custom_tax = array();
			$psp_all_tax = array();
			$custom_tax = $this->custom_taxonomies;
			$psp_all_tax = array_merge((array)$builtin_tax, (array)$custom_tax);
			if (!in_array($psp_sanitized_seo_data['preferred_tax'], $psp_all_tax)) {
				$psp_sanitized_seo_data['preferred_tax'] = '';
			}			
		}
		
		$psp_sanitized_seo_data['preferred_term'] = ''; 
        if ( isset( $psp_seo_data['preferred_term'] ) ) {
        	if ( !empty( $psp_sanitized_seo_data['preferred_tax'] ) ) {
        	
        		$psp_sanitized_seo_data['preferred_term'] = sanitize_text_field( $psp_seo_data['preferred_term'] );
        		$psp_bc_tax_term_items = array();
        		$termobjects = get_terms( array(
            		'taxonomy' => $psp_sanitized_seo_data['preferred_tax'],
            		'hide_empty' => true,
            	) );
            	if ( ! empty( $termobjects ) ) {
                	foreach ( $termobjects as $term ) {
                		$psp_bc_tax_term_items[] = $term->term_id;
                	}
                }
        		//$psp_bc_tax_term_items = $this->psp_bc_tax_term_items;
        		//error_log(print_r($psp_bc_tax_term_items, true));
        		if (!in_array($psp_sanitized_seo_data['preferred_term'], $psp_bc_tax_term_items)) {
        			$psp_sanitized_seo_data['preferred_term'] = '';
        		}		
        	}
        }
		
		$psp_sanitized_seo_data['disable_title'] = '';
		if ( isset( $psp_seo_data['disable_title'] ) ) {
			$psp_sanitized_seo_data['disable_title'] = !is_null(filter_var($psp_seo_data['disable_title'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['disable_title'] : '';
		}
		
		$psp_sanitized_seo_data['disable_description'] = '';
		if ( isset( $psp_seo_data['disable_description'] ) ) {
			$psp_sanitized_seo_data['disable_description'] = !is_null(filter_var($psp_seo_data['disable_description'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['disable_description'] : '';
		}
		
		$psp_sanitized_seo_data['disable_keywords'] = '';
		if ( isset( $psp_seo_data['disable_keywords'] ) ) {
			$psp_sanitized_seo_data['disable_keywords'] = !is_null(filter_var($psp_seo_data['disable_keywords'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['disable_keywords'] : '';
		}
		
		$psp_sanitized_seo_data['disable_canonical'] = '';
		if ( isset( $psp_seo_data['disable_canonical'] ) ) {
			$psp_sanitized_seo_data['disable_canonical'] = !is_null(filter_var($psp_seo_data['disable_canonical'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['disable_canonical'] : '';
		}
		
		$psp_sanitized_seo_data['disable_title_format'] = '';
		if ( isset( $psp_seo_data['disable_title_format'] ) ) {
			$psp_sanitized_seo_data['disable_title_format'] = !is_null(filter_var($psp_seo_data['disable_title_format'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['disable_title_format'] : '';
		}
		
		$psp_sanitized_seo_data['disable_desc_format'] = '';
		if ( isset( $psp_seo_data['disable_desc_format'] ) ) {
			$psp_sanitized_seo_data['disable_desc_format'] = !is_null(filter_var($psp_seo_data['disable_desc_format'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['disable_desc_format'] : '';
		}
		
		$psp_sanitized_seo_data['disable_psp'] = '';
		if ( isset( $psp_seo_data['disable_psp'] ) ) {
			$psp_sanitized_seo_data['disable_psp'] = !is_null(filter_var($psp_seo_data['disable_psp'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $psp_seo_data['disable_psp'] : '';
		}
		
		return $psp_sanitized_seo_data;
	
	}
	
	//sanitize social data
	protected function psp_sanitze_social_data($psp_social_data) {
	
		$psp_allowed_protocols = array('http','https');	
		$psp_sanitized_social_data = array();	
		
		$psp_sanitized_social_data['fb_title'] = '';
		if ( isset( $psp_social_data['fb_title'] ) ) {
			$psp_sanitized_social_data['fb_title'] = sanitize_text_field( $psp_social_data['fb_title'] );
		}
		
		$psp_sanitized_social_data['fb_description'] = '';
		if ( isset( $psp_social_data['fb_description'] ) ) {
			$psp_sanitized_social_data['fb_description'] = sanitize_textarea_field( $psp_social_data['fb_description'] );
		}		
		
		$psp_sanitized_social_data['fb_image'] = '';
		if ( isset( $psp_social_data['fb_image'] ) ) {
			$psp_sanitized_social_data['fb_image'] = esc_url_raw( $psp_social_data['fb_image'], $psp_allowed_protocols );
		}		
		
		$psp_sanitized_social_data['fb_og_type'] = '';
		if ( isset( $psp_social_data['fb_og_type'] ) ) {
			$psp_sanitized_social_data['fb_og_type'] =  sanitize_text_field($psp_social_data['fb_og_type']) ;
			
			$fb_og_types = array ('website', 'article', 'books.author', 'books.book', 'books.genre', 'business.business', 'fitness.course', 'game.achievement', 'music.album', 'music.playlist', 'music.radio_station', 'music.song', 'place', 'product', 'product.group', 'product.item', 'profile', 'restaurant.menu', 'restaurant.menu_item', 'restaurant.menu_section', 'restaurant.restaurant', 'video.episode', 'video.movie', 'video.other', 'video.tv_show');
			
			if (!in_array($psp_sanitized_social_data['fb_og_type'], $fb_og_types)) {
				$psp_sanitized_social_data['fb_og_type'] = '';
			}
		}
		
		$psp_sanitized_social_data['fb_ogtype_properties'] = '';
		if ( isset( $psp_social_data['fb_ogtype_properties'] ) ) {
			$psp_sanitized_social_data['fb_ogtype_properties'] = sanitize_textarea_field( $psp_social_data['fb_ogtype_properties'] );
		}
		
		$psp_sanitized_social_data['fb_media_properties'] = '';
		if ( isset( $psp_social_data['fb_media_properties'] ) ) {
			$psp_sanitized_social_data['fb_media_properties'] = sanitize_textarea_field( $psp_social_data['fb_media_properties'] );
		}
		
		$psp_sanitized_social_data['tw_card_type'] = '';
		if ( isset( $psp_social_data['tw_card_type'] ) ) {
			$psp_sanitized_social_data['tw_card_type'] =  sanitize_text_field($psp_social_data['tw_card_type']);
			
			$tw_card_types =  array ('summary', 'summary_large_image', 'player', 'app');
			
			if (!in_array($psp_sanitized_social_data['tw_card_type'], $tw_card_types)) {
				$psp_sanitized_social_data['tw_card_type'] = '';
			}
		}		
		
		$psp_sanitized_social_data['tw_creator'] = '';
		if ( isset( $psp_social_data['tw_creator'] ) ) {
			$psp_sanitized_social_data['tw_creator'] = sanitize_text_field( $psp_social_data['tw_creator'] );
			$psp_sanitized_social_data['tw_creator'] = str_replace("@", "", $psp_sanitized_social_data['tw_creator']);
		}
		
		$psp_sanitized_social_data['tw_title'] = '';
		if ( isset( $psp_social_data['tw_title'] ) ) {
			$psp_sanitized_social_data['tw_title'] = sanitize_text_field( $psp_social_data['tw_title'] );
		}
		
		$psp_sanitized_social_data['tw_description'] = '';
		if ( isset( $psp_social_data['tw_description'] ) ) {
			$psp_sanitized_social_data['tw_description'] = sanitize_textarea_field( $psp_social_data['tw_description'] );
		}		
		
		$psp_sanitized_social_data['tw_image'] = '';
		if ( isset( $psp_social_data['tw_image'] ) ) {
			$psp_sanitized_social_data['tw_image'] = esc_url_raw( $psp_social_data['tw_image'], $psp_allowed_protocols );
		}
		
		$psp_sanitized_social_data['tw_imagealt'] = '';		
		if ( isset( $psp_social_data['tw_imagealt'] ) ) {
			$psp_sanitized_social_data['tw_imagealt'] = sanitize_textarea_field( $psp_social_data['tw_imagealt'] );
		}		
		
		$psp_sanitized_social_data['tw_image_1'] = '';	
		if ( isset( $psp_social_data['tw_image_1'] ) ) {
			$psp_sanitized_social_data['tw_image_1'] = esc_url_raw( $psp_social_data['tw_image_1'], $psp_allowed_protocols );
		}
		
		$psp_sanitized_social_data['tw_image_2'] = '';	
		if ( isset( $psp_social_data['tw_image_2'] ) ) {
			$psp_sanitized_social_data['tw_image_2'] = esc_url_raw( $psp_social_data['tw_image_2'],$psp_allowed_protocols );
		}
		
		$psp_sanitized_social_data['tw_image_3'] = '';	
		if ( isset( $psp_social_data['tw_image_3'] ) ) {
			$psp_sanitized_social_data['tw_image_3'] = esc_url_raw( $psp_social_data['tw_image_3'], $psp_allowed_protocols );
		}		
		
		if ( isset( $psp_social_data['tw_label_1'] ) ) {
			$psp_sanitized_social_data['tw_label_1'] = sanitize_text_field( $psp_social_data['tw_label_1'] );
		}
		
		if ( isset( $psp_social_data['tw_data_1'] ) ) {
			$psp_sanitized_social_data['tw_data_1'] = sanitize_text_field( $psp_social_data['tw_data_1'] );
		}
		
		if ( isset( $psp_social_data['tw_label_2'] ) ) {
			$psp_sanitized_social_data['tw_label_2'] = sanitize_text_field( $psp_social_data['tw_label_2'] );
		}
		
		if ( isset( $psp_social_data['tw_data_2'] ) ) {
			$psp_sanitized_social_data['tw_data_2'] = sanitize_text_field( $psp_social_data['tw_data_2'] );
		}
		
		$psp_sanitized_social_data['tw_player'] = '';
		if ( isset( $psp_social_data['tw_player'] ) ) {
			$psp_sanitized_social_data['tw_player'] = esc_url_raw( $psp_social_data['tw_player'], $psp_allowed_protocols );
		}		
		
		$psp_sanitized_social_data['tw_player_width'] = '';
		if ( isset( $psp_social_data['tw_player_width'] ) ) {
			$psp_sanitized_social_data['tw_player_width'] = sanitize_text_field( $psp_social_data['tw_player_width'] );
			
			if (!filter_var($psp_sanitized_social_data['tw_player_width'], FILTER_VALIDATE_INT)) {
				$psp_sanitized_social_data['tw_player_width'] = '';
			}
		}
		
		$psp_sanitized_social_data['tw_player_height'] = '';
		if ( isset( $psp_social_data['tw_player_height'] ) ) {
			$psp_sanitized_social_data['tw_player_height'] = sanitize_text_field( $psp_social_data['tw_player_height'] );
			
			if (!filter_var($psp_sanitized_social_data['tw_player_height'], FILTER_VALIDATE_INT)) {
				$psp_sanitized_social_data['tw_player_height'] = '';
			}
		}		
		
		$psp_sanitized_social_data['tw_player_stream'] = '';	
		if ( isset( $psp_social_data['tw_player_stream'] ) ) {
			$psp_sanitized_social_data['tw_player_stream'] = esc_url_raw( $psp_social_data['tw_player_stream'], $psp_allowed_protocols );
		}
		
		$psp_sanitized_social_data['tw_app_country'] = '';	
		if ( isset( $psp_social_data['tw_app_country'] ) ) {
			$psp_sanitized_social_data['tw_app_country'] = sanitize_text_field( $psp_social_data['tw_app_country'] );
		}
		
		$psp_sanitized_social_data['tw_app_name_iphone'] = '';
		if ( isset( $psp_social_data['tw_app_name_iphone'] ) ) {
			$psp_sanitized_social_data['tw_app_name_iphone'] = sanitize_text_field( $psp_social_data['tw_app_name_iphone'] );
		}
		
		$psp_sanitized_social_data['tw_app_id_iphone'] = '';
		if ( isset( $psp_social_data['tw_app_id_iphone'] ) ) {
			$psp_sanitized_social_data['tw_app_id_iphone'] = sanitize_text_field( $psp_social_data['tw_app_id_iphone'] );
		}
		
		$psp_sanitized_social_data['tw_app_url_iphone'] = '';
		if ( isset( $psp_social_data['tw_app_url_iphone'] ) ) {
			$psp_sanitized_social_data['tw_app_url_iphone'] = sanitize_text_field($psp_social_data['tw_app_url_iphone']) ;
		}
		
		$psp_sanitized_social_data['tw_app_name_ipad'] = '';
		if ( isset( $psp_social_data['tw_app_name_ipad'] ) ) {
			$psp_sanitized_social_data['tw_app_name_ipad'] = sanitize_text_field( $psp_social_data['tw_app_name_ipad'] );
		}
		
		$psp_sanitized_social_data['tw_app_id_ipad'] = '';
		if ( isset( $psp_social_data['tw_app_id_ipad'] ) ) {
			$psp_sanitized_social_data['tw_app_id_ipad'] = sanitize_text_field( $psp_social_data['tw_app_id_ipad'] );
		}
		
		$psp_sanitized_social_data['tw_app_url_ipad'] = '';
		if ( isset( $psp_social_data['tw_app_url_ipad'] ) ) {
			$psp_sanitized_social_data['tw_app_url_ipad'] =  sanitize_text_field($psp_social_data['tw_app_url_ipad']) ;
		}
		
		$psp_sanitized_social_data['tw_app_name_googleplay'] = '';
		if ( isset( $psp_social_data['tw_app_name_googleplay'] ) ) {
			$psp_sanitized_social_data['tw_app_name_googleplay'] = sanitize_text_field( $psp_social_data['tw_app_name_googleplay'] );
		}
		
		$psp_sanitized_social_data['tw_app_id_googleplay'] = '';
		if ( isset( $psp_social_data['tw_app_id_googleplay'] ) ) {
			$psp_sanitized_social_data['tw_app_id_googleplay'] = sanitize_text_field( $psp_social_data['tw_app_id_googleplay'] );
		}
		
		$psp_sanitized_social_data['tw_app_url_googleplay'] = '';
		if ( isset( $psp_social_data['tw_app_url_googleplay'] ) ) {
			$psp_sanitized_social_data['tw_app_url_googleplay'] =  esc_url_raw($psp_social_data['tw_app_url_googleplay']);
		}
		
		$psp_sanitized_social_data['sc_title'] = '';
		if ( isset( $psp_social_data['sc_title'] ) ) {
			$psp_sanitized_social_data['sc_title'] = sanitize_text_field( $psp_social_data['sc_title'] );
		}
		
		$psp_sanitized_social_data['sc_description'] = '';
		if ( isset( $psp_social_data['sc_description'] ) ) {
			$psp_sanitized_social_data['sc_description'] = sanitize_textarea_field( $psp_social_data['sc_description'] );
		}
		
		$psp_sanitized_social_data['sc_image'] = '';
		if ( isset( $psp_social_data['sc_image'] ) ) {
			$psp_sanitized_social_data['sc_image'] = esc_url_raw( $psp_social_data['sc_image'], $psp_allowed_protocols );
		}
		
		return $psp_sanitized_social_data;
	}
	
	/*
	 * renders Plugin settings page, checks
	 * for the active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function psp_options_page() {
		$tab = isset( $_GET['psptab'] ) ? sanitize_key($_GET['psptab']) : $this->psp_general_settings_group;
		$psp_button = "submit";
		if ($tab == $this->psp_permalink_settings_group) {
			$psp_button = "submit1";
		}		
		?>
		<div class="wrap">		
			<h1 style='line-height:30px;'><?php esc_html_e('Techblissonline Platinum SEO - SEO Settings', 'platinum-seo-pack') ?></h1>
			<p style="color: red"><?php esc_html_e('You need to click the "Save Settings" button to save the changes you made to each individual tab before moving on to the next tab.', 'platinum-seo-pack') ?></p>
			<?php $this->psp_options_tabs(); ?>
			<?php if ($this->psp_helper->user_has_access( $tab )) { ?>
			<form name="platinum-seo-form" method="post" action="options.php">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php settings_errors(); ?>
				<?php //do_settings_sections( $tab );
					if ($tab == $this->psp_other_settings_group) {
					     $this->psp_helper->do_psp_settings_sections( $tab );
					     //do_settings_sections( $tab );
					    //include_once( 'psp_contact_metabox_renderer-rows.php' ); 
					} else {do_settings_sections( $tab );} ?>
				<?php submit_button('Save Settings', 'primary', $psp_button); ?>
			</form>
			<?php } else { ?>
			    <p style="color: red"><?php esc_html_e('You do not have access to these Options (Settings) tab of Techblissonline Platinum SEO', 'platinum-seo-pack') ?></p>
			<?php } ?>
		
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
		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * psp_options_page method.
	 */
	function psp_options_tabs() {
		$current_tab = isset( $_GET['psptab'] ) ? sanitize_key($_GET['psptab']) : $this->psp_general_settings_group;
		//$psp_icon = '';
		//if(isset($_POST['submit1']) && ($current_tab == $this->psp_permalink_settings_group )){ 
		if((isset($_GET['settings-updated']) && sanitize_key($_GET['settings-updated']) == true) && ($current_tab == $this->psp_permalink_settings_group )){
			//refresh rewrite rules
			$this->psp_refresh_rewrite_rules();
		}
		
		if ($current_tab == $this->psp_home_settings_group) {
			$psp_cm_home_html_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/html', 'codemirror'=> array('autoRefresh' => true)));
			$psp_cm_home_json_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'json', 'codemirror'=> array('autoRefresh' => true)));
			wp_enqueue_script( 'psp-home-cm-editors', plugins_url( '/js/cm-home.js', __FILE__ ),array( 'jquery' ), false, true);
			wp_localize_script('psp-home-cm-editors', 'psp_cm_home_html_settings', $psp_cm_home_html_settings);
			wp_localize_script('psp-home-cm-editors', 'psp_cm_home_json_settings', $psp_cm_home_json_settings);
			//$psp_icon = '<span class="dashicons dashicons-admin-home"></span>';
			
		}
		if ($current_tab == $this->psp_posttype_settings_group) {
			$psp_cm_ptype_html_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/html', 'codemirror'=> array('autoRefresh' => true)));
			wp_enqueue_script( 'psp-pt-cm-editors', plugins_url( '/js/cmpt.js', __FILE__ ),array( 'jquery' ), false, true);
			wp_localize_script('psp-pt-cm-editors', 'psp_cm_ptype_html_settings', $psp_cm_ptype_html_settings);
			
			
		}
		wp_enqueue_style("psp-settings-bswide-css", plugins_url( '/css/psp-settings-bswide.css', __FILE__ ));
		wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), '2.3.5');
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
		//wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
		//wp_enqueue_style("psp-htmlsettings-css", plugins_url( '/css/psp-html-settings.css', __FILE__ ));
		
		//screen_icon();
		
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->psp_settings_tabs as $tab_key => $tab_caption ) {
			$psp_icon = '';
			if (! $this->psp_helper->user_has_access( $tab_key )) {
			   continue;
			}
			if ($tab_key == $this->psp_home_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-admin-home"></span> ';
			} 
			if ($tab_key == $this->psp_general_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-admin-generic"></span></span> ';
			} 
			if ($tab_key == $this->psp_taxonomy_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-category"></span> ';
			} 
			if ($tab_key == $this->psp_posttype_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-text-page"></span> ';
			} 
			if ($tab_key == $this->psp_archives_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-archive"></span> ';
			} 
			if ($tab_key == $this->psp_permalink_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-admin-links"></span> ';
			}
			if ($tab_key == $this->psp_sm_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-admin-site-alt3"></span> ';
			}
			if ($tab_key == $this->psp_breadcrumb_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-networking"></span> ';
			} 
			if ($tab_key == $this->psp_other_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-location"></span> ';
			}
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a style="text-decoration:none" class="nav-tab ' . esc_attr($active) . '" href="?page=' . esc_attr($this->psp_plugin_options_key) . '&psptab=' . esc_attr($tab_key) . '">' .$psp_icon.esc_attr($tab_caption) . '</a>';	
		}
		echo '</h2>';
	}
	
	//add sitelinks search schema.org JSON-LD code to frontpage
	function psp_jsonld_for_google (){
	
		$jsonld_script_tags = "";
		if ( is_front_page() ) {
			
			$psp_other_settings = get_option('psp_other_settings');	
			
			$google_site_name = isset($psp_other_settings["google_site_name"]) ? $psp_other_settings["google_site_name"] : '';
			$google_alt_site_name = isset($psp_other_settings["google_alt_site_name"]) ? $psp_other_settings["google_alt_site_name"] : '';
			if (empty($google_alt_site_name)) $google_alt_site_name = $google_site_name;
			
			$logo_url = isset($psp_other_settings["kg_logo"]) ? esc_url($psp_other_settings["kg_logo"]) : '';
			$kg_profile_type = isset($psp_other_settings["kg_profile_type"]) ? $psp_other_settings["kg_profile_type"] : '';
			$kg_profile_name = isset($psp_other_settings["kg_profile_name"]) ? $psp_other_settings["kg_profile_name"] : '';
			//$kg_contacts = $psp_other_settings["kg_contacts"];
			$kg_contactpoint = isset($psp_other_settings["kg_contactpoint"]) ? $psp_other_settings["kg_contactpoint"] : '';
			if (empty($kg_profile_name)) $kg_profile_name = $this->sitename;
			$social_profile_urls = "";
			
			
			$target_url = "";
			
			$target_url = isset($psp_other_settings["sitelinks_searchbox_target"]) ? esc_url($psp_other_settings["sitelinks_searchbox_target"]) : '';
			if (!empty($target_url)) {
				//$target_url = preg_replace( '/{.*?}/i', '{techblissonline_platinum_seo_for_wp}', $target_url );
				$target_url = preg_replace( '/\?q=.*/i', '?s={techblissonline_platinum_wordpress_seo}', $target_url );
				$target_url = preg_replace( '/\?s=.*/i', '?s={techblissonline_platinum_wordpress_seo}', $target_url );
			}
			
			if ( isset($psp_other_settings['sitelinks_search_box']) && $psp_other_settings['sitelinks_search_box'] && !empty($target_url) ) {
				$jsonld_script_tags .= '<script type="application/ld+json"> { "@context": "https://schema.org", "@type": "WebSite", "url": "'.trailingslashit(get_site_url()).'", "potentialAction": { "@type": "SearchAction", "target": "'.$target_url.'", "query-input": "required name=techblissonline_platinum_wordpress_seo" } } </script>'; 
			}
			if (!empty($google_site_name) && !empty($google_alt_site_name)) {
				if (!empty($jsonld_script_tags)) $jsonld_script_tags .= "\r\n";					
				 
				$jsonld_script_tags .= '<script type="application/ld+json"> { "@context": "https://schema.org", "@type": "WebSite", "name": "'.$google_site_name.'", "alternateName": "'.esc_attr($google_alt_site_name).'", "url": "'.trailingslashit(get_site_url()).'" } </script>';
				
			}
			if (isset($psp_other_settings["psp_kg_tags_enabled"]) && $psp_other_settings["psp_kg_tags_enabled"]) {
				if ( !empty($logo_url) && $kg_profile_type == "organization") {
					if (!empty($jsonld_script_tags)) $jsonld_script_tags .= "\r\n";					
					
					$jsonld_script_tags .= '<script type="application/ld+json"> { "@context": "https://schema.org", "@type": "Organization", "url": "'.trailingslashit(get_site_url()).'", "logo": "'.esc_url($logo_url).'" } </script>';
					
				}	
				if ( isset($psp_other_settings["kg_fb_profile"]) && !empty($psp_other_settings["kg_fb_profile"])) {
					$social_profile_urls .= '"'.esc_url($psp_other_settings["kg_fb_profile"]).'"';
				}
				if ( isset($psp_other_settings["kg_tw_profile"]) && !empty($psp_other_settings["kg_tw_profile"]) ) {
					if (!empty($social_profile_urls)) {
						$social_profile_urls .= ", ";
					}
					$social_profile_urls .= '"'.esc_url($psp_other_settings["kg_tw_profile"]).'"';
				}
				if ( isset($psp_other_settings["kg_go_profile"]) && !empty($psp_other_settings["kg_go_profile"]) ) {
					if (!empty($social_profile_urls)) {
						$social_profile_urls .= ", ";
					}
					$social_profile_urls .= '"'.esc_url($psp_other_settings["kg_go_profile"]).'"';
				}
				if ( isset($psp_other_settings["kg_ig_profile"]) && !empty($psp_other_settings["kg_ig_profile"]) ) {
					if (!empty($social_profile_urls)) {
						$social_profile_urls .= ", ";
					}
					$social_profile_urls .= '"'.esc_url($psp_other_settings["kg_ig_profile"]).'"';
				}
				if ( isset($psp_other_settings["kg_li_profile"]) && !empty($psp_other_settings["kg_li_profile"]) ) {
					if (!empty($social_profile_urls)) {
						$social_profile_urls .= ", ";
					}
					$social_profile_urls .= '"'.esc_url($psp_other_settings["kg_li_profile"]).'"';
				}
				if ( isset($psp_other_settings["kg_yt_profile"]) && !empty($psp_other_settings["kg_yt_profile"]) ) {
					if (!empty($social_profile_urls)) {
						$social_profile_urls .= ", ";
					}
					$social_profile_urls .= '"'.esc_url($psp_other_settings["kg_yt_profile"]).'"';
				}
				if ( !empty($social_profile_urls) ) {
					if (!empty($jsonld_script_tags)) $jsonld_script_tags .= "\r\n";						
					
					$jsonld_script_tags .= '<script type="application/ld+json"> { "@context": "https://schema.org", "@type": "'.esc_attr(ucwords($kg_profile_type)).'", "name": "'.esc_attr($kg_profile_name).'", "url": "'.trailingslashit(get_site_url()).'", "sameAs": [ '.$social_profile_urls.'] } </script>';
					
				}
				
				if ( !empty($kg_contactpoint) ) {
					if (!empty($jsonld_script_tags)) $jsonld_script_tags .= "\r\n";						
					
					$jsonld_script_tags .= '<script type="application/ld+json"> { "@context": "https://schema.org", "@type": "Organization", "url": "'.trailingslashit(get_site_url()).'", "contactPoint": '.esc_attr($kg_contactpoint).' } </script>';
					//$jsonld_script_tags .= $kg_contactpoint;
					
				}
			}
           		
		}
		return $jsonld_script_tags;
	} 	
	
	/**
	* Add the necessary rewrite rules for removing "category" base
	*/

	function psp_category_rewrite_rules( $rules ) {
	 
		global $wp_rewrite;
		$categories = array();
		
		//$categories = get_categories( array( 'hide_empty' => false ) );
		
		$filter_name = explode("_", current_filter());
		$tax_name = $filter_name[0];
		//error_log(print_r($tax_name));
		//$categories = get_categories(array('hide_empty' => false, 'taxonomy'  => $tax_name));
		$categories = get_terms(array('hide_empty' => false, 'taxonomy'  => $tax_name));
		
		$psp_category_rewrite_rules = $rules;
	 
		if ( ! empty( $categories ) ) {
			$psp_category_slugs = array();
	 
			foreach ( $categories as $category ) {
				//if ( is_object( $category ) && ! is_wp_error( $category ) ) {
					//if ( 0 == $category->category_parent ) {
				    if ( 0 == $category->parent ) {
						$psp_category_slugs[] = $category->slug;
					} else {
						//$psp_category_slugs[] = trim( get_category_parents( $category->cat_ID, false, '/', true ), '/' );
						$psp_category_slugs[] = trim( get_term_parents_list( $category->term_id, $tax_name, array('separator' => '/', 'link' => false, 'format' => 'slug')), '/' );
					}
				//}
			}
			
			if ( ! empty( $psp_category_slugs ) ) {
				
				$psp_category_rewrite_rules = array();

				foreach ( $psp_category_slugs as $psp_category_slug ) {
					//$psp_category_rewrite_rules[ '(' . $psp_category_slug . ')/feed/(feed|rdf|rss|rss2|atom)?/?$' ] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
					//$psp_category_rewrite_rules[ '(' . $psp_category_slug . ')/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
					//$psp_category_rewrite_rules[ '(' . $psp_category_slug . ')(/page/(\d)+/?)?$' ] = 'index.php?category_name=$matches[1]&paged=$matches[3]';
					if ($tax_name == "category") {
						$psp_category_rewrite_rules['(' . esc_attr($psp_category_slug) . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
						$psp_category_rewrite_rules[ '(' . esc_attr($psp_category_slug) . ')/'.esc_attr($wp_rewrite->pagination_base).'/(\d)+/?$' ] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
						$psp_category_rewrite_rules['(' . esc_attr($psp_category_slug) . ')/?$'] = 'index.php?category_name=$matches[1]';
					} else {
						$psp_category_rewrite_rules['(' . esc_attr($psp_category_slug) . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?'.esc_attr($tax_name).'=$matches[1]&feed=$matches[2]';
						$psp_category_rewrite_rules[ '(' . esc_attr($psp_category_slug) . ')/'.esc_attr($wp_rewrite->pagination_base).'/(\d)+/?$' ] = 'index.php?'.esc_attr($tax_name).'=$matches[1]&paged=$matches[2]';
						$psp_category_rewrite_rules['(' . esc_attr($psp_category_slug) . ')/?$'] = 'index.php?'.esc_attr($tax_name).'=$matches[1]';
					}
				}
			}
			
			// Redirect from Old Category Base	
			if ($tax_name == "category") {
				$wp_category_base = get_option('category_base') ? get_option('category_base') : 'category';
			} else {
				$wp_category_base = $tax_name;
			}
			$wp_category_base = esc_attr(trim($wp_category_base, '/'));
			$psp_category_rewrite_rules[$wp_category_base . '/(.*)$'] = 'index.php?techblissonline_psp_category_redirect=$matches[1]';
			
		}
		return $psp_category_rewrite_rules;
	}

	// Add a query variable for redirecting categories with "category" base.
	//add_filter('query_vars', 'psp_set_category_base_redir_var');
	function psp_set_category_base_redir_var($public_query_vars) {
		//$public_query_vars[] = 'techblissonline_psp_category_redirect';
		array_push($public_query_vars, 'techblissonline_psp_category_redirect');
		return $public_query_vars;
	}

	// Redirect if 'techblissonline_psp_category_redirect' is set
	//add_filter('request', 'psp_redirect_category_base_request');
	function psp_redirect_category_base_request($query_vars) {
		//print_r($query_vars); // For Debugging
		if (isset($query_vars['techblissonline_psp_category_redirect'])) {
			$catlink = esc_url_raw(trailingslashit(get_option('home')) . user_trailingslashit($query_vars['techblissonline_psp_category_redirect'], 'category'));
			wp_safe_redirect( $catlink, 301 );
			exit();
		}
		return $query_vars;
	}

	
	// Remove category base from permalink structures
	//add_action('init', 'psp_set_no_base_extra_permastruct');
	function psp_set_no_base_extra_permastruct() {
		global $wp_rewrite, $wp_version;
		
		$cust_taxonomies = array();
		
		if ( empty($wp_rewrite->permalink_structure) ) return;	

		$psp_permalink_settings = get_option('psp_permalink_settings');		                        
		
		if (isset($psp_permalink_settings['category']) && $psp_permalink_settings['category']) {
		
			if (version_compare($wp_version, '3.4', '<')) {
				// For pre-3.4 support
				$wp_rewrite -> extra_permastructs['category'][0] = '%category%';
			} else {
				$wp_rewrite -> extra_permastructs['category']['struct'] = '%category%';
			}
		
		}
		
		if ( null == $this->custom_taxonomies ) {
			$args = array(
							'public'   => true,
							'_builtin' => false		  
						); 			
			$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$cust_taxonomies = get_taxonomies( $args, $output, $operator );
			$this->custom_taxonomies = $cust_taxonomies;
		}	
		
		//$psp_taxonomy_instance = PSP_Tax_Seo_Metas::get_handle();		
		//$psp_taxonomies = $psp_taxonomy_instance->get_cust_taxonomies();
		//$cust_taxonomies = $this->custom_taxonomies;
		//print_r($cust_taxonomies);
		
		foreach($cust_taxonomies as $cust_taxonomy) {
		
			//$psp_settings_name = "psp_".$cust_taxonomy."_settings";		
			//$psp_tax_settings = get_option($psp_settings_name);
			//error_log($cust_taxonomy);
			if (isset($psp_permalink_settings[$cust_taxonomy]) && $psp_permalink_settings[$cust_taxonomy]) {
				if (version_compare($wp_version, '3.4', '<')) {
					// For pre-3.4 support
					$wp_rewrite -> extra_permastructs[$cust_taxonomy][0] = "%".esc_attr($cust_taxonomy)."%";
				} else {
					$wp_rewrite -> extra_permastructs[$cust_taxonomy]['struct'] = "%".esc_attr($cust_taxonomy)."%";
				}
			}
		}
	}
	
	public function psp_refresh_rewrite_rules() {
		global $wp_rewrite;
		$wp_rewrite -> flush_rules();
	}
};