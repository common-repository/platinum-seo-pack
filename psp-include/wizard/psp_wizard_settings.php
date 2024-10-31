<?php

/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: Complete SEO and Social optimization solution for your Wordpress blog/site.
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspWizardSettings {	
	 
	private static $obj_handle = null;	
	
	private $psp_helper;		
	
	public $custom_taxonomies = array();
	public $custom_post_types = array();
	public $psp_sm_settings = array();
	
	public $psp_settings_instance;
	
	private $psp_start_settings_group = 'psp_start_wizard'; 
	private $psp_import_settings_group = 'psp_importer_wizard'; 
	private $psp_general_settings_group = 'psp_general_wizard';
	private $psp_home_settings_group = 'psp_home_wizard';	
	private $psp_permalink_settings_group = 'psp_permalink_wizard';
	private $psp_social_settings_group = 'psp_social_wizard';	
	private $psp_sm_settings_group = 'psp_sitemap_wizard';
	private $psp_finish_settings_group = 'psp_finish_wizard';
	
	//protected $psp_plugin_options_key = 'platinum-seo-wizard';
	protected $psp_plugin_options_key = 'platinum-seo-social-pack-by-techblissonline';
	private $psp_settings_tabs = array();	
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;
	
	
	function __construct() {
		
		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;				

		add_action('admin_menu', array(&$this, 'psp_wizard_admin_menu'));		
		
		add_action( 'admin_init', array( &$this, 'psp_admin_settings_init' ) );
		//add_action( 'admin_menu', array( &$this, 'add_admin_menus' ), 9 );
		
		foreach ($this->psp_settings_tabs as $psp_group => $psp_group_name) {
		    //Allow psp_capability
		    add_filter( 'option_page_capability_'.$psp_group, function( $capability ){
		        //error_log("psp const cap ".'psp_capability');
                return 'edit_posts';
            } );
		}		
	}	
	
	/*
	 * Registers settings 	
	 */
	function psp_admin_settings_init() {	
		//$screen = get_current_screen();
		//error_log(print_r($screen, true));
		$this->register_general_settings('sitewide');
		$this->register_home_settings();
		$this->register_sm_settings();
		$this->register_social_settings();
		$this->register_permalink_settings();	
		//wp_enqueue_script( 'psp-ajax-wizard-script', plugins_url( 'settings/js/psp_wizard.js', PSP_PLUGIN_SETTINGS_URL ), array('jquery'), '2.2.1', false );
		
	}
	
	public function psp_wizard_admin_menu() {
	    
	    //remove_menu_page( 'index.php' );
		
		$psp_wizard_page = add_submenu_page($this->psp_plugin_options_key, esc_html__('Platinum SEO Setup Wizard', 'platinum-seo-pack'), '<span class="dashicons dashicons-admin-tools"></span> '.esc_html__('Setup wizard', 'platinum-seo-pack'), 'manage_options', 'wizard', array($this, 'psp_wizard_page'));
		//$psp_importer_page_2 = 'platinum-seo-and-social-pack_page_pspimporter';
		//error_log('redir '. $psp_importer_page);
		
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
		
		$cust_post_types = get_post_types( array ( '_builtin' => FALSE ) );	
		$this->custom_post_types = $cust_post_types;
			
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
			'option_description'  => esc_html__( 'Enter a title in plain text. Do not use any tags.', 'platinum-seo-pack' ),
        );
		
		$desc_field     = array (
            'label_for' 	=> 'psp_home_description',
            'option_name'   => $psp_home_settings_name.'[description]',
			'option_value'  => isset($psp_home_settings['description']) ? stripcslashes(esc_attr($psp_home_settings['description'])) : '',
			'option_description'  => esc_html__( 'Enter a meta description in plain text. Do not use any tags.', 'platinum-seo-pack' ),
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
		
		add_settings_field( $use_front_page_field_id, $use_front_page_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_home_settings_group, 'psp_section_home', $use_front_page_field );
		add_settings_field( 'psp_home_title', esc_html__('Home Page Title: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_text' ), $this->psp_home_settings_group, 'psp_section_home',  $title_field);
		add_settings_field( 'psp_home_description', esc_html__('Home Page Meta Description: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_textarea' ), $this->psp_home_settings_group, 'psp_section_home', $desc_field );
		//add_settings_field( 'psp_home_keywords', esc_html__('Home Page Meta Keywords: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_text' ), $this->psp_home_settings_group, 'psp_section_home', $keywords_field );
		//add_settings_field( 'psp_home_additional_headers', esc_html__('Additional Home Page Headers: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_textarea' ), $this->psp_home_settings_group, 'psp_section_home', $additional_headers_field );
		//add_settings_field( 'psp_home_schemas', esc_html__('Schemas >> ', 'platinum-seo-pack').'<a href="https://techblissonline.com/tools/schema-markup-generator/" target="_blank">'.esc_html__('Generate here', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_textarea' ), $this->psp_home_settings_group, 'psp_section_home', $schema_field );
		
		//wizard
		$setting_name = "home";
		
		$psp_wizard_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_wizard',
            'option_name'   => $psp_home_settings_name.'[wizard]',
			'option_value'  => 'wizard',			
        );
		
		$psp_wizard_field_id = 'psp_'.$setting_name.'_wizard';	
		$psp_wizard_field_title = '';
		
		add_settings_field( $psp_wizard_field_id, $psp_wizard_field_title, array( &$this, 'psp_add_field_hidden' ), $this->psp_home_settings_group, 'psp_section_home',  $psp_wizard_field);	
	}
	
	function sanitize_home_settings($settings) {
		
		if( ! isset( $settings['wizard'] ) ) {
			
			return $settings;
			
		}
		
		$psp_current_settings = get_option('psp_home_settings') ? get_option('psp_home_settings') : array();
		
		if( isset( $settings['use_front_page'] ) ) {
			$settings['use_front_page'] = !is_null(filter_var($settings['use_front_page'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_front_page'] : '';
		} else {
			$settings['use_front_page'] = '';
		}
		
		if( isset( $settings['title'] ) ) {
			$settings['title'] = sanitize_text_field( $settings['title'] );
		} else {
			$settings['title'] = '';
		}
		
		
		if( isset( $settings['description'] ) ) {
			$settings['description'] = sanitize_textarea_field( $settings['description'] );
		} else {
			$settings['description'] = '';
		}
		/***
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
        	$schema_obj = json_decode(stripcslashes($json_schema_str));
        	//validate it is a json object
        	if($schema_obj === null) {
        		// $schema_obj is null because the json cannot be decoded
        		$settings['schema'] = '';                 
        	} else {
        		$settings['schema'] = sanitize_textarea_field( htmlentities($settings['schema']) );
        	   
        	}
        }
		***/
		$psp_new_settings = array_merge( $psp_current_settings, $settings );
		
		error_log(print_r(	$psp_current_settings, true ));
		error_log(print_r(	$settings, true ));
		
		//Remove sanitizing for adding
		unset( $psp_new_settings[ 'wizard' ] );	
		remove_filter( "sanitize_option_psp_home_settings", array( $this->psp_settings_instance, 'sanitize_home_settings' ));
		remove_filter( "sanitize_option_psp_home_settings", array( &$this, 'sanitize_home_settings' ));
		
		$psp_new_settings = array_filter( $psp_new_settings, 'strlen' );
		update_option( "psp_home_settings", $psp_new_settings);
		$psp_redirect_to_url = get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=psp_sitemap_wizard";
		wp_safe_redirect($psp_redirect_to_url,302);
		exit();		
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
		
		register_setting( $this->psp_general_settings_group, $psp_settings_name, array( &$this, 'sanitize_general_settings' ) );
		
		//Section
		$section_id = 'psp_separator_section';		
		$section_title =  esc_html__( 'Sitewide Title Settings', 'platinum-seo-pack' );		
		add_settings_section( $section_id, $section_title, array( &$this, 'section_separator_desc' ), $this->psp_general_settings_group );
		
		//field			
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
		
		//wizard
		$psp_wizard_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_wizard',
            'option_name'   => $psp_settings_name.'[wizard]',
			'option_value'  => 'wizard',			
        );
		
		$psp_wizard_field_id = 'psp_'.$setting_name.'_wizard';	
		$psp_wizard_field_title = '';
		
		add_settings_field( $psp_wizard_field_id, $psp_wizard_field_title, array( &$this, 'psp_add_field_hidden' ), $this->psp_general_settings_group, $section_id,  $psp_wizard_field);	
		
	}	
	
	function sanitize_general_settings($settings) {
		
		if( ! isset( $settings['wizard'] ) ) {
			
			return $settings;
			
		}
		
		$psp_settings_name = 'psp_sitewide_settings';
		
		$psp_current_settings = get_option('psp_sitewide_settings') ? get_option('psp_sitewide_settings') : array();
	
	    if( isset( $settings['separator'] ) ) {
			
			$settings['separator'] = sanitize_text_field( htmlentities($settings['separator']) );
			
			//$psp_separators = array ( '-', '&ndash;', '&mdash;', '&middot;', '&bull;', '*', '|', '~', '&laquo;', '&raquo;', '&lt;', '&gt;', '&tilde;', '&hearts;', '&clubs;');	
			$psp_separators = array ( '-', '&ndash;', '&mdash;', '&middot;', '&bull;', '*', '|', '~', '&laquo;', '&raquo;', '&lt;', '&gt;', '&tilde;', '&hearts;', '&clubs;', ':', '★');
			
			if (!in_array($settings['separator'], $psp_separators)) {
				$settings['separator'] = '';
			}
		} else {
			$settings['separator'] = '';
		}
		
		if( isset( $settings['paged_title_format'] ) ) {
			$settings['paged_title_format'] = sanitize_text_field( $settings['paged_title_format'] );			
		} else {
			$settings['paged_title_format'] = '';
		}
				
		if( isset( $settings['autogenerate_description'] ) ) {
			$settings['autogenerate_description'] = !is_null(filter_var($settings['autogenerate_description'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['autogenerate_description'] : '';
		} else {
			$settings['autogenerate_description'] = '';
		}
		
		if( isset( $settings['use_canonical'] ) ) {
			$settings['use_canonical'] = !is_null(filter_var($settings['use_canonical'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_canonical'] : '';
		} else {
			$settings['use_canonical'] = '';
		}		
		
		$psp_new_settings = array_merge( $psp_current_settings, $settings );
		
		//Remove sanitizing for adding
		unset( $psp_new_settings[ 'wizard' ] );
		remove_filter( "sanitize_option_psp_sitewide_settings", array( $this->psp_settings_instance, 'sanitize_general_settings' ));
		remove_filter( "sanitize_option_psp_sitewide_settings", array( &$this, 'sanitize_general_settings' ));
		
		$psp_new_settings = array_filter( $psp_new_settings, 'strlen' );
		update_option( "psp_sitewide_settings", $psp_new_settings);
		$psp_redirect_to_url = get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=psp_home_wizard";
		wp_safe_redirect($psp_redirect_to_url,302);
		exit();
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
		
		//wizard
		$setting_name = "permalink";
		
		$psp_wizard_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_wizard',
            'option_name'   => $psp_settings_name.'[wizard]',
			'option_value'  => 'wizard',			
        );
		
		$psp_wizard_field_id = 'psp_'.$setting_name.'_wizard';	
		$psp_wizard_field_title = '';
		
		add_settings_field( $psp_wizard_field_id, $psp_wizard_field_title, array( &$this, 'psp_add_field_hidden' ), $this->psp_permalink_settings_group, $section_id,  $psp_wizard_field);
		
	}
	
	function sanitize_permalink_settings($settings) {
		
		if( ! isset( $settings['wizard'] ) ) {
			
			return $settings;
			
		}
		
		$psp_current_settings = get_option('psp_permalink_settings') ? get_option('psp_permalink_settings') : array();

		if( isset( $settings['redirection'] ) ) {
			$settings['redirection'] = !is_null(filter_var($settings['redirection'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['redirection'] : '';
		} else {
			$settings['redirection'] = '';
		}
		
		if( isset( $settings['auto_redirection'] ) ) {
			$settings['auto_redirection'] = !is_null(filter_var($settings['auto_redirection'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['auto_redirection'] : '';
		} else {
			$settings['auto_redirection'] = '';
		}
		
		if ( isset( $settings['limit_301'] ) ) {
			$settings['limit_301'] = sanitize_text_field( $settings['limit_301'] );
			if (!filter_var($settings['limit_301'], FILTER_VALIDATE_INT) ) {
				$settings['limit_301'] = '';
			}			
		} else {
			$settings['limit_301'] = '';
		}
		
		//V2.0.8
		if( isset( $settings['disable_wp_404_guess'] ) ) {
			$settings['disable_wp_404_guess'] = !is_null(filter_var($settings['disable_wp_404_guess'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['disable_wp_404_guess'] : '';
		} else {
			$settings['disable_wp_404_guess'] = '';
		}
		//V2.0.8
		
		if( isset( $settings['enable_404'] ) ) {
			$settings['enable_404'] = !is_null(filter_var($settings['enable_404'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['enable_404'] : '';
		} else {
			$settings['enable_404'] = '';
		}
		
		if( isset( $settings['bots_404'] ) ) {
			$settings['bots_404'] = !is_null(filter_var($settings['bots_404'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['bots_404'] : '';
		} else {
			$settings['bots_404'] = '';
		}
		
		if ( isset( $settings['limit_404'] ) ) {
			$settings['limit_404'] = sanitize_text_field( $settings['limit_404'] );
			if (!filter_var($settings['limit_404'], FILTER_VALIDATE_INT) ) {
				$settings['limit_404'] = '';
			}			
		} else {
			$settings['limit_404'] = '';
		}  			
    		
    	//return $settings;
		$psp_new_settings = array_merge( $psp_current_settings, $settings );		
		
		//Remove sanitizing for adding
		unset( $psp_new_settings[ 'wizard' ] );
		remove_filter( "sanitize_option_psp_permalink_settings", array( $this->psp_settings_instance, 'sanitize_permalink_settings' ));
		remove_filter( "sanitize_option_psp_permalink_settings", array( &$this, 'sanitize_permalink_settings' ));
		
		$psp_new_settings = array_filter( $psp_new_settings, 'strlen' );
		update_option( "psp_permalink_settings", $psp_new_settings);
		$psp_redirect_to_url = get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=".$this->psp_finish_settings_group;
		wp_safe_redirect($psp_redirect_to_url,302);
		exit();
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
		register_setting( $this->psp_sm_settings_group, $psp_sm_settings_name, array( &$this, 'sanitize_sm_settings' ) );
		//add Section
		add_settings_section( 'psp_sitemap_section', esc_html__('SiteMap Settings', 'platinum-seo-pack' ), array( &$this, 'section_sm_desc' ), $this->psp_sm_settings_group );
		
		//add fields
		
		//Enable siteMap
		$sitemap_field     = array (
            'label_for' 	=> 'psp_'.$psp_sm_settings_name.'_enable',
            'option_name'   => $psp_sm_settings_name.'[enable]',
			'option_value'  => isset($psp_sm_settings['enable']) ? $psp_sm_settings['enable'] : '',
			//'checkbox_label' => esc_html__( 'Enable ', 'platinum-seo-pack' ),
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
		$psp_post_rypes = array();		
		
		$builtin_post_types = array("post", "page");
		$custom_post_types = $this->custom_post_types;
		$psp_all_posttypes = array_merge((array)$builtin_post_types, (array)$custom_post_types);
		$psp_post_rypes = array_combine($psp_all_posttypes, $psp_all_posttypes);
		
		$psp_posttypes_list_field     = array (
            'label_for' 	=> 'psp_posttypes_list',
            'option_name'   => $psp_sm_settings_name.'[posttypes_list][]',
			'option_value'  => isset($psp_sm_settings['posttypes_list']) ? $psp_sm_settings['posttypes_list'] : '',
			'checkboxitems' => $psp_post_rypes,
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
			//'checkbox_label' => esc_html__( 'Enable ', 'platinum-seo-pack' ),
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
			//'checkbox_label' => esc_html__( 'Enable ', 'platinum-seo-pack' ),
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
		
		//add_settings_field( $sitemap_sort_field_id, $sitemap_sort_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $sitemap_sort_field );

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
		
		//add_settings_field( $sitemap_author_field_id, $sitemap_author_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $sitemap_author_field );	
		
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
		
		//add_settings_field( $psp_url_limit_field_id, $psp_url_limit_field_title, array( &$this, 'psp_add_field_text_number' ), $this->psp_sm_settings_group, 'psp_sitemap_section', $psp_url_limit_field);
		
		//Excluded Post IDs
		$psp_sm_posts_field_title = esc_html__( 'Excluded Posts: ', 'platinum-seo-pack' );
		
		$psp_sm_posts_field     = array (
            'label_for' 	=> 'psp_sm_posts_id',
            'option_name'   => $psp_sm_settings_name.'[excluded_post_ids]',
			'option_value'  => isset($psp_sm_settings['excluded_post_ids']) ? esc_attr($psp_sm_settings['excluded_post_ids']) : '',
			'option_description' => esc_html__( 'Enter a comma separated list of Post IDs to be excluded from SiteMap. This can be automatically updated via Platinum SEO Meta Box for individual Posts or Pages.NoIndex Pages are excluded from the sitemap by default and you need not have to enter them here again.', 'platinum-seo-pack' ),
        );
		//add_settings_field( 'psp_sm_posts_id', $psp_sm_posts_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_sm_settings_group, 'psp_sitemap_section',  $psp_sm_posts_field);	

			
		//Excluded Term IDs
		$psp_sm_terms_field_title = esc_html__( 'Excluded Terms (Category IDs/Term IDs): ', 'platinum-seo-pack' );
		
		$psp_sm_terms_field     = array (
            'label_for' 	=> 'psp_sm_terms_ids',
            'option_name'   => $psp_sm_settings_name.'[excluded_term_ids]',
			'option_value'  => isset($psp_sm_settings['excluded_term_ids']) ? esc_attr($psp_sm_settings['excluded_term_ids']) : '',
			'option_description' => esc_html__( 'Enter a comma separated list of Term IDs to be excluded from SiteMap. This can be automatically updated via Platinum SEO Meta Box for individual Category or Taxonomy Terms. NoIndex Terms are excluded from the sitemap by default and you need not have to enter them here again.', 'platinum-seo-pack' ),
        );
		//add_settings_field( 'psp_sm_terms_ids', $psp_sm_terms_field_title, array( &$this, 'psp_add_field_text' ), $this->psp_sm_settings_group, 'psp_sitemap_section',  $psp_sm_terms_field);

		//wizard
		$setting_name = "sitemap";
		
		$psp_wizard_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_wizard',
            'option_name'   => $psp_sm_settings_name.'[wizard]',
			'option_value'  => 'wizard',			
        );
		
		$psp_wizard_field_id = 'psp_'.$setting_name.'_wizard';	
		$psp_wizard_field_title = '';
		
		add_settings_field( $psp_wizard_field_id, $psp_wizard_field_title, array( &$this, 'psp_add_field_hidden' ), $this->psp_sm_settings_group, 'psp_sitemap_section',  $psp_wizard_field);	
		
	}	
	
	function sanitize_sm_settings( $settings ) {
		
		if( ! isset( $settings['wizard'] ) ) {
			
			return $settings;
			
		}
		
		$psp_current_settings = get_option('psp_sitemap') ? get_option('psp_sitemap') : array();
		
		if ( isset( $settings['enable'] ) ) {
			$settings['enable'] = !is_null(filter_var($settings['enable'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['enable'] : '';						
		} else {
			$settings['enable'] = '';
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
		/**
		if ( isset( $settings['sort_order'] ) ) {
			$settings['sort_order'] = !is_null(filter_var($settings['sort_order'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['sort_order'] : '';
			
		}
		**/
		if ( isset( $settings['include_images'] ) ) {
			$settings['include_images'] = !is_null(filter_var($settings['include_images'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['include_images'] : '';
			
		} else {
			$settings['include_images'] = '';
		}
		
		if ( isset( $settings['include_lastmod'] ) ) {
			$settings['include_lastmod'] = !is_null(filter_var($settings['include_lastmod'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['include_lastmod'] : '';
			
		} else {
			$settings['include_lastmod'] = '';
		}
		/**
		if ( isset( $settings['enable_authors'] ) ) {			
			$settings['enable_authors'] = !is_null(filter_var($settings['enable_authors'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['enable_authors'] : '';			
		}	
		
		if ( isset( $settings['max_urls'] ) ) {
			$settings['max_urls'] = sanitize_text_field( $settings['max_urls'] );
			if (!filter_var($settings['max_urls'], FILTER_VALIDATE_INT) ) {
				$settings['max_urls'] = 2000;
			}			
		}
		**/
		if ( isset( $settings['taxonomies_list'] ) ) {
    		
			$settings['taxonomies_list'] = array_map( 'sanitize_text_field', $settings['taxonomies_list'] );
    		
    		$builtin_tax = array("category", "post_tag");
    		$custom_tax = array();
    		$psp_all_tax = array();
    		$custom_tax = $this->custom_taxonomies;
    		$psp_all_tax = array_merge((array)$builtin_tax, (array)$custom_tax);
			
			if(!empty($settings['taxonomies_list'])) {
				if (count($settings['taxonomies_list']) != count(array_intersect($settings['taxonomies_list'], $psp_all_tax))) {
					$$settings['taxonomies_list'] = array();
				}
			}
			
    	} else {
			$settings['taxonomies_list'] = array();
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
					$$settings['posttypes_list'] = array();
				}
			}
			
    	} else {
			$settings['posttypes_list'] = array();
		}		
		
		$psp_new_settings = array_merge( $psp_current_settings, $settings );		
		
		//Remove sanitizing for adding
		unset( $psp_new_settings[ 'wizard' ] );
		remove_filter( "sanitize_option_psp_sitemap", array( $this->psp_settings_instance->psp_sitemap_instance, 'sanitize_sm_settings' ));
		remove_filter( "sanitize_option_psp_sitemap", array( &$this, 'sanitize_sm_settings' ));
		
		//$psp_new_settings = array_filter( $psp_new_settings, 'strlen' );
		$psp_new_settings = $this->discard_empty( $psp_new_settings );
		update_option( "psp_sitemap", $psp_new_settings);
		$psp_redirect_to_url = get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=psp_social_wizard";
		wp_safe_redirect($psp_redirect_to_url,302);
		exit();
	}
	
	public function discard_empty ( $setting ) {

    	foreach ( $setting as $key => $value ) {
    	    
    	    if ( empty( $value ) ) unset ($setting[$key]);
    	}
    	
    	return $setting;
    
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
		
		//wp_enqueue_media();	
		//wp_enqueue_script( 'psp-image-uploader', plugins_url( '/js/pspmediauploader.js', __FILE__ ), array( 'jquery' ) );
		//wp_enqueue_script( 'psp-image-uploader', plugins_url( '/js/pspmediauploader.js', PSP_PLUGIN_SETTINGS_URL ), array( 'jquery' ), '2.2.1' );
		//wp_enqueue_script( 'psp-social', plugins_url( '/js/pspsocialhandler.js', PSP_PLUGIN_SETTINGS_URL ), array( 'jquery' ) );
		
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
		//$section_id = 'psp_schema_org_section';		
		//$section_title = esc_html__('Pinterest and Linkedin Settings', 'platinum-seo-pack');
		
		//add_settings_section( $section_id, $section_title, array( &$this, 'section_schema_org_desc' ), $this->psp_social_settings_group );	

		//wizard
		$setting_name = "social";
		
		$psp_wizard_field     = array (
            'label_for' 	=> 'psp_'.$setting_name.'_wizard',
            'option_name'   => $psp_settings_name.'[wizard]',
			'option_value'  => 'wizard',			
        );
		
		$psp_wizard_field_id = 'psp_'.$setting_name.'_wizard';	
		$psp_wizard_field_title = '';
		
		add_settings_field( $psp_wizard_field_id, $psp_wizard_field_title, array( &$this, 'psp_add_field_hidden' ), $this->psp_social_settings_group, $section_id,  $psp_wizard_field);	
		
	}
	
	function sanitize_social_settings($settings) {
		
		if( ! isset( $settings['wizard'] ) ) {
			
			return $settings;
			
		}

		$psp_allowed_protocols = array('http','https');	
		
		$psp_current_settings = get_option('psp_social_settings') ? get_option('psp_social_settings') : array();

		if ( isset( $settings['psp_og_tags_enabled'] ) ) {
			$settings['psp_og_tags_enabled'] = !is_null(filter_var($settings['psp_og_tags_enabled'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['psp_og_tags_enabled'] : '';
		} else {
			$settings['psp_og_tags_enabled'] = '';
		}
	
		if( isset( $settings['fb_site_name'] ) ) {
			$settings['fb_site_name'] = sanitize_text_field( $settings['fb_site_name'] );
		} else {
			$settings['fb_site_name'] = '';
		}
			
		
		if ( isset( $settings['fb_default_image'] ) ) {
			$settings['fb_default_image'] = esc_url_raw( $settings['fb_default_image'], $psp_allowed_protocols );
		} else {
			$settings['fb_default_image'] = '';
		}

		if ( isset( $settings['psp_twitter_card_enabled'] ) ) {
			$settings['psp_twitter_card_enabled'] = !is_null(filter_var($settings['psp_twitter_card_enabled'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['psp_twitter_card_enabled'] : '';
		} else {
			$settings['psp_twitter_card_enabled'] = '';
		}
		
		if ( isset( $settings['tw_ct_type'] ) ) {
			$settings['tw_ct_type'] =  sanitize_text_field($settings['tw_ct_type']);
			
			$tw_card_types =  array ('summary', 'summary_large_image', 'player', 'app');
			
			if (!in_array($settings['tw_ct_type'], $tw_card_types)) {
				$settings['tw_ct_type'] = '';
			}
		} else {
			$settings['tw_ct_type'] = '';
		}	
		
		if( isset( $settings['tw_user'] ) ) {
			$settings['tw_user'] = sanitize_text_field( $settings['tw_user'] );
		} else {
			$settings['tw_user'] = '';
		}
		
		if ( isset( $settings['tw_default_image'] ) ) {
			$settings['tw_default_image'] = esc_url_raw( $settings['tw_default_image'], $psp_allowed_protocols );
		} else {
			$settings['tw_default_image'] = '';
		}		
		
		$psp_new_settings = array_merge( $psp_current_settings, $settings );		
		
		//Remove sanitizing for adding
		unset( $psp_new_settings[ 'wizard' ] );
		remove_filter( "sanitize_option_psp_social_settings", array( $this->psp_settings_instance->psp_social_instance, 'sanitize_social_settings' ));
		remove_filter( "sanitize_option_psp_social_settings", array( &$this, 'sanitize_social_settings' ));
		
		//$psp_new_settings = array_filter( $psp_new_settings, 'strlen' );
		$psp_new_settings = $this->discard_empty( $psp_new_settings );
		update_option( "psp_social_settings", $psp_new_settings);
		$psp_redirect_to_url = get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=psp_permalink_wizard";
		wp_safe_redirect($psp_redirect_to_url,302);
		exit();

	}	
		
	/*
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 */			
	function section_separator_desc() { echo esc_html__('The Title separator can be used in all Title formats and Description formats by specifying the tag - ', 'platinum-seo-pack').' %sep%'; }
	function section_home_desc() { echo '<a href="'.home_url().'" target=_blank">'. esc_html__('Home page SEO settings', 'platinum-seo-pack').'</a> - '.esc_html__('Set the title and meta description tags used on home page of your site here.', 'platinum-seo-pack');}	
	function section_permalinks_desc() { echo esc_html__('These settings, if checked, will remove the base from taxonomies like Category and other custom taxonomies, if any. If "Remove base" is chosen for Category then the corresponding base will be removed from the permalink structure for categories.', 'platinum-seo-pack'). ' i.e. <code>Category</code>'; }
	function section_redirections_desc() {echo ''; }
	function section_404errors_desc() {echo ''; }  
	function section_schema_desc() {echo esc_html__('The following settings are not necessary if you had added these schemas in the Home page and/or Contacts Page JSON Schema Editor settings of this plugin.', 'platinum-seo-pack'). ' i.e. <br /> 1. '.esc_html__('Schema for enabling Sitelink Search Box in Google and', 'platinum-seo-pack').' <br /> 2. '. esc_html__('Schema for Knowledge Graph', 'platinum-seo-pack'); }
	function section_sm_desc() {echo ''; }
	function section_fb_desc() {echo '';}
	function section_twitter_desc() {echo ''; }
	
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
	
	//callback for hidden field
	function psp_add_field_hidden(array $args) {
	
		$option_name   = isset($args['option_name']) ? $args['option_name'] : '';
		$id     = isset($args['label_for']) ? $args['label_for'] : '';
		$option_value     = isset($args['option_value']) ? $args['option_value'] : '';
		
		echo "<input id='".esc_attr($id)."' name='".esc_attr($option_name)."' type='hidden' value='".esc_attr($option_value)."' /> ";
				
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
			echo "<div class='psp-bs'><input ".$checked." id='$checkbox_id' name='$option_name' value='".esc_attr($checkboxitemkey)."' type='checkbox' data-toggle='toggle' /><span>&nbsp;</span><span for='$id'>".esc_attr($checkboxitemvalue)."</span></div><br />";
		
			$counter = $counter + 1;
		
		}
		
		echo "</div><br /><p class='description'>".esc_html($option_description)."</p>";
		
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
		echo "</select><p for='".esc_attr($id)."'> ".wp_kses(html_entity_decode($option_description), $desc_allowed_html)."</p>";
			
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
	 * renders Plugin settings page, checks
	 * for the active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function psp_wizard_page() {
		$tab = isset( $_GET['psptab'] ) ? sanitize_key($_GET['psptab']) : $this->psp_start_settings_group;
		
		if( $tab == $this->psp_start_settings_group ) {
		    
		    include_once( PSP_PLUGIN_HOME . PSPINC . '/wizard/psp_wizard_startbtn_renderer.php' );
		    return;
		    
		} else if ( $tab == $this->psp_finish_settings_group ) {
		    
		    include_once( PSP_PLUGIN_HOME . PSPINC . '/wizard/psp_wizard_finishbtn_renderer.php' );
		    return;
		    
		}
		$psp_default = "btn btn-default btn-circle";
		$psp_green = "btn btn-success btn-circle";
		
		$btnclass_2 = $psp_default;
		$btnclass_3 = $psp_default;
		$btnclass_4 = $psp_default;
		$btnclass_5 = $psp_default;
		$btnclass_6 = $psp_default;
		
		if ($tab == $this->psp_general_settings_group) {			
			//$this->register_general_settings('sitewide');
			$skip = $this->psp_home_settings_group;
			$btnclass_2 = $psp_green;
		} else if ($tab == $this->psp_home_settings_group) {			
			//$this->register_home_settings();
			$skip = $this->psp_sm_settings_group;
			$btnclass_2 = $psp_green;
			$btnclass_3 = $psp_green;
		} else if ($tab == $this->psp_sm_settings_group) {
			//$this->register_sm_settings();
			$skip = $this->psp_social_settings_group;
			$btnclass_2 = $psp_green;
			$btnclass_3 = $psp_green;
			$btnclass_4 = $psp_green;
		} else if ($tab == $this->psp_social_settings_group) {
			//$this->register_social_settings();
			$skip = $this->psp_permalink_settings_group;
			$btnclass_2 = $psp_green;
			$btnclass_3 = $psp_green;
			$btnclass_4 = $psp_green;
			$btnclass_5 = $psp_green;
		} else if ($tab == $this->psp_permalink_settings_group) {
			//$this->register_permalink_settings();
			$skip = $this->psp_finish_settings_group;
			$btnclass_2 = $psp_green;
			$btnclass_3 = $psp_green;
			$btnclass_4 = $psp_green;
			$btnclass_5 = $psp_green;
			$btnclass_6 = $psp_green;
		}
		$psp_button = "submit";				
		?>
		<style>
            #wpcontent, #footer { margin-left: 0px !important; }
            #adminmenuback, #adminmenuwrap { display: none !important; }
            
		 body {
			/*margin-top:30px;*/
			font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
		}
		.stepwizard-step p {
			margin-top: 0px;
			color:#666;
		}
		.stepwizard-row {
			display: table-row;
		}
		.stepwizard {
			display: table;
			width: 90%;
			position: relative;
		}
		.stepwizard-step button[disabled] {
			/*opacity: 1 !important;
			filter: alpha(opacity=100) !important;*/
		}
		.stepwizard .btn.disabled, .stepwizard .btn[disabled], .stepwizard fieldset[disabled] .btn {
			opacity:1 !important;
			color:#bbb;
		}
		.stepwizard-row:before {
			top: 14px;
			bottom: 0;
			position: absolute;
			content:" ";
			width: 100%;
			height: 1px;
			background-color: #ccc;
			z-index: 0;
		}
		.stepwizard-step {
			display: table-cell;
			text-align: center;
			position: relative;
		}
		.btn-circle {
			width: 30px;
			height: 30px;
			text-align: center;
			padding: 6px 0;
			font-size: 12px;
			line-height: 1.428571429;
			border-radius: 15px;
		}
		
		.wizardbody {
			margin-left:10%;
		}
		.psp-bs {
			width: 92%;
		}
		.panel {			
			/*width: 86%;*/
			margin-left: 5%;
			overflow: auto;
		}
		.psp-left{
			float:left;
			padding-left:10px;
		}
		.psp-right{
			float:right;
			padding-right:10px;
		}
		.wrap, .wrap psp-bs {
			width: 92%;
			background-color: #fff;	
		}
		.psp-margin {
		    margin-top: 10px;
		}
		.btn-orange {
		    background-color: #EB931A !important;
		    color: #FFF !important;
		    border: none !important;
		    font-weight: bold;
		}
		/***
		form[name="platinum-seo-form1"] {		
			margin-left: 5%;
		}
		***/		
		</style>
		<div align="center">
		<h1><a class="bookmarkme" href="<?php echo 'https://techblissonline.com/tools/'; ?>" target="_blank"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-logo.png'; ?>" class="img-responsive" alt="Techblissonline Platinum SEO Wordpress Tools"/></a><br /><br /> PLATINUM SEO</h1>
		<small>This is a wizard that sets up the essential options. For full set of options under each head, go to WP Admin >> Platinum SEO &amp; Social Pack >> SEO</small>
		</div>
		<div class="wizardbody">
		<div class="psp-bs">
			<div class="container">				
				<div class="stepwizard">
					<div class="stepwizard-row setup-panel">
						<div class="stepwizard-step col-xs-2"> 
							<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=".$this->psp_import_settings_group ?>" type="button" class="btn btn-success btn-circle">1</a>
							<p><small>Importer</small></p>
						</div>
						<div class="stepwizard-step col-xs-2"> 
							<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=".$this->psp_general_settings_group ?>" type="button" class="<?php echo $btnclass_2 ?>">2</a>
							<p><small>General</small></p>
						</div>
						<div class="stepwizard-step col-xs-2"> 
							<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=".$this->psp_home_settings_group ?>" type="button" class="<?php echo $btnclass_3 ?>">3</a>
							<p><small>Home</small></p>
						</div>
						<div class="stepwizard-step col-xs-2"> 
							<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=".$this->psp_sm_settings_group ?>" type="button" class="<?php echo $btnclass_4 ?>">4</a>
							<p><small>Sitemaps</small></p>
						</div>
						<div class="stepwizard-step col-xs-2"> 
							<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=".$this->psp_social_settings_group ?>" type="button" class="<?php echo $btnclass_5 ?>">5</a>
							<p><small>Social</small></p>
						</div>
						<div class="stepwizard-step col-xs-2"> 
							<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=".$this->psp_permalink_settings_group ?>" type="button" class="<?php echo $btnclass_6 ?>">6</a>
							<p><small>Permalinks</small></p>
						</div>
					</div>
				</div>
			</div> <?php //container ?>
		</div> <?php //psp-bs ?>
		<div class="wrap">						
			<?php $this->psp_enqueue_scripts(); ?>
			<?php if ($this->psp_helper->user_has_access( $tab )) { ?>
			
				<?php if ( $tab == $this->psp_import_settings_group) { ?>
					<div class="panel">
					<?php include_once( PSP_PLUGIN_HOME . PSPINC . '/wizard/psp_wizard_importer.php' ); ?>
					</div>
					<div class="psp-bs">
						<div class="container">	
							<div class="stepwizard">							
							<div class="pull-right">
								<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=psp_general_wizard" ?>" class="button btn-orange" role="button"> <?php esc_html_e('Next', 'platinum-seo-pack') ?></a>
							</div>		
							</div>	
						</div>
					</div>
				<?php } else { ?>
					<form name="platinum-seo-form1" method="post" action="options.php">	
					<div class="panel">
						<?php settings_fields( $tab ); ?>
						<?php settings_errors(); ?>
						<?php do_settings_sections( $tab ); ?>
					</div>
					<div class="psp-bs">
						<div class="container">
							<div class="stepwizard">
							<div class="pull-left psp-margin">
								<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=wizard&psptab=".$skip ?>" class="button btn-orange" role="button"> <?php esc_html_e('Skip &amp; Continue', 'platinum-seo-pack') ?></a>
							</div>
							<div class="pull-right">
								<?php submit_button('Save &amp; Continue', 'primary', $psp_button); ?>
							</div>	
							</div>	
						</div>
					</div>
					
				<?php } ?>
			</form>
			<?php } else { ?>
			    <p style="color: red"><?php esc_html_e('You do not have access to these Options (Settings) tab of Techblissonline Platinum SEO', 'platinum-seo-pack') ?></p>
			<?php } ?>
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
	function psp_enqueue_scripts() {
		$current_tab = isset( $_GET['psptab'] ) ? sanitize_key($_GET['psptab']) : $this->psp_import_settings_group;		
		
		if ($current_tab == $this->psp_home_settings_group) {
			$psp_cm_home_html_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/html', 'codemirror'=> array('autoRefresh' => true)));
			$psp_cm_home_json_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'json', 'codemirror'=> array('autoRefresh' => true)));
			wp_enqueue_script( 'psp-home-cm-editors', plugins_url( 'settings/js/cm-home.js', PSP_PLUGIN_SETTINGS_URL ),array( 'jquery' ), false, true);
			wp_localize_script('psp-home-cm-editors', 'psp_cm_home_html_settings', $psp_cm_home_html_settings);
			wp_localize_script('psp-home-cm-editors', 'psp_cm_home_json_settings', $psp_cm_home_json_settings);
			//$psp_icon = '<span class="dashicons dashicons-admin-home"></span>';
			
		}
		
		//wp_enqueue_style("psp-settings-bswide-css", plugins_url( 'settings/css/psp-settings-bswide.css', PSP_PLUGIN_SETTINGS_URL ));
		//wp_enqueue_style("psp-settings-css", plugins_url( 'settings/css/psp-settings.css', PSP_PLUGIN_SETTINGS_URL ));
		wp_enqueue_style("psp-settings-css", plugins_url( 'settings/css/psp-settings.css', PSP_PLUGIN_SETTINGS_URL ), array(), '2.3.5');
		
		//$psp_wizard_data_nonce = wp_create_nonce( 'psp_wizard_data_nonce' ); 
		
		wp_enqueue_script( 'psp-ajax-wizard-script', plugins_url( 'settings/js/psp_wizard.js', PSP_PLUGIN_SETTINGS_URL ), array('jquery'), '2.2.1', false );
		//wp_localize_script( 'psp-ajax-wizard-script', 'psp_ajax_wizard_object', array( 'ajax_url' => admin_url( 'admin-ajax.php'), 'pspnonce' => $psp_wizard_data_nonce) );
		wp_enqueue_script( 'psp-bs-js',  plugins_url( 'settings/js/pspbsjs.js', PSP_PLUGIN_SETTINGS_URL ) );		
		wp_enqueue_style("psp-settings-bs-css", plugins_url( 'settings/css/psp-settings-bs.css', PSP_PLUGIN_SETTINGS_URL ));
		//wp_enqueue_style("psp-settings-css", plugins_url( 'settings/css/psp-settings.css', PSP_PLUGIN_SETTINGS_URL ));
		wp_enqueue_script( 'psp-bs-toggler-js', plugins_url( 'settings/js/pspbstoggler.js', PSP_PLUGIN_SETTINGS_URL ) );
        wp_enqueue_style("'psp-bs-toggler-css", plugins_url( 'settings/css/psp-bs-toggle.css', PSP_PLUGIN_SETTINGS_URL ));	
		
	}	
};