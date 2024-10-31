<?php

/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Description: Complete SEO solution for your Wordpress blog.
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspTaxSeoMetas {

	//public static $plugin_settings_name = "psp-tax-seo-metas";
	
	private static $obj_handle = null;
	
	//private static $psp_seo_meta_tags = null;
	
	protected static $cust_taxonomies = array();
	
	protected $psp_cat_meta = array();
	protected $psp_tag_meta = array();
	protected $psp_term_meta = array();
	
	protected $psp_current_tax_format = array();
	protected $psp_sitewide_settings = array();
	
	protected $psp_current_cat_obj;
	protected $psp_current_tag_obj;
	protected $psp_current_term_obj;	
	
	protected $index_tag = "index,follow";
	protected $noindex_tag = "noindex,follow";
	protected $noindex_nofollow_tag = "noindex,nofollow";
	protected $noodp_tag = "noodp";
	protected $noydir_tag = "noydir";
    protected $noarchive_tag = "noarchive";
	protected $nosnippet_tag = "nosnippet";
	protected $noimageindex = "noimageindex";
	
	protected $sitename = "";
	protected $sitedescription = "";
	
	protected $psp_helper;
	
	public $taxonomy_name = "";
	public $taxonomy_description = "";
	public $taxonomy_title = "";
	public $taxonomy_keywords = "";
	public $taxonomy_can_link = "";	
	
	public $current_pageno = 1;
	
	public $term_social_meta = array();
	
	//public $options = array();
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;
	
	function __construct() {
		/*************
		if ( null == self::$cust_taxonomies ) {
			$args = array(
							'public'   => true,
							'_builtin' => false		  
						); 
			$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$cust_taxonomies = get_taxonomies( $args, $output, $operator );
			self::$cust_taxonomies = $cust_taxonomies;
		}
		**********/
		//not needed here
		//$this->sitename = $this->psp_helper->internationalize(get_bloginfo('name'));
		//$this->sitedescription = $this->psp_helper->internationalize(get_bloginfo('description'));
		
		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;
		
		$this->sitename = $psp_helper_instance->get_sitename();
		$this->sitedescription = $psp_helper_instance->get_sitedescription();
		$this->psp_sitewide_settings = get_option('psp_sitewide_settings');	
		
	}	
	
	public function get_cust_taxonomies() {		
	
		return self::$cust_taxonomies;
	
	} // end get_cust_taxonomies;	
	
	private function get_tax_robots_meta($term_meta) {

        $robots_meta = "";
		$robots_meta_string = "";
		
		$psp_settings = $this->psp_sitewide_settings;	
		$current_tax_format = $this->psp_current_tax_format;
		
		$noindex_tax = !empty($current_tax_format['robots']) ? $current_tax_format['robots'] : "";
        //$tax_meta = "psp_".$tax_name."_noindex";
		$term_meta_robots = !empty($term_meta['robots']) ? $term_meta['robots'] : "";
		$term_meta_noidx = !empty($term_meta['noindex']) ? $term_meta['noindex'] : "";
		$term_meta_nofollow = !empty($term_meta['nofollow']) ? $term_meta['nofollow'] : "";
		/**
		if (empty($term_meta_noidx) && empty($term_meta_nofollow)) {
			$term_meta_robots = 'index,follow';
		}
		**/
		if (!empty($term_meta_noidx)) {
			$term_meta_robots = 'noindex,follow';
		}
		
		if (!empty($term_meta_nofollow)) {
			$term_meta_robots = 'index,nofollow';
		}
		
		if (!empty($term_meta_noidx) && !empty($term_meta_nofollow)) {
			$term_meta_robots = 'noindex,nofollow';
		}
		
		
		if ($term_meta_robots) {
			//$robots_meta .= $this->noindex_tag;
			$robots_meta .= $term_meta_robots;
		}
		
		if (empty($robots_meta)) {	
			//if (get_option($tax_meta)) {
			if ($noindex_tax) {
			   $robots_meta .= $this->noindex_tag;
			} else {
				if (isset($psp_settings['noindex_subpages']) && $psp_settings['noindex_subpages'] && (get_query_var('paged') > 1 || $this->current_pageno > 1) ) {
					$robots_meta .= $this->noindex_tag;   			
				} else { 
					$robots_meta .= $this->index_tag;
				}			
			}
		}
		
		$psp_noimageindex = !empty($term_meta['noimageindex']) ? htmlspecialchars(stripcslashes($term_meta['noimageindex'])) : '';
		$psp_imagepreview = !empty($term_meta['maximage']) ? htmlspecialchars(stripcslashes($term_meta['maximage'])) : '';
		$psp_videopreview = !empty($term_meta['maxvideo']) ? htmlspecialchars(stripcslashes($term_meta['maxvideo'])) : '';
	
		if ($psp_noimageindex) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noimageindex;

		}
		
		$psp_noarchive = isset($term_meta['noarchive']) ? htmlspecialchars(stripcslashes($term_meta['noarchive'])) : "";
				
		if ($psp_noarchive) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noarchive_tag;

		}
		/**
		if (isset($psp_settings['use_meta_noodp']) && $psp_settings['use_meta_noodp']) {

			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noodp_tag;
		}

		if (isset($psp_settings['use_meta_noydir']) && $psp_settings['use_meta_noydir']) {

			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noydir_tag;

		}
		**/
		$psp_nosnippet = isset($term_meta['nosnippet']) ? htmlspecialchars(stripcslashes($term_meta['nosnippet'])) : "";
		$psp_maxsnippet = !empty($term_meta['maxsnippet']) ? htmlspecialchars(stripcslashes($term_meta['maxsnippet'])) : '';
		
		if ($psp_nosnippet) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->nosnippet_tag;

		}	
		
		if (!$psp_nosnippet && $psp_maxsnippet) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}
			if($psp_maxsnippet == "zero") $psp_maxsnippet = 0;
			$robots_meta .= "max-snippet:".esc_attr($psp_maxsnippet);

		}
		
		if (!$psp_noimageindex && $psp_imagepreview) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= "max-image-preview:".esc_attr($psp_imagepreview);

		}
		
		if ($psp_videopreview) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}
			if($psp_videopreview == "zero") $psp_videopreview = 0;
			$robots_meta .= "max-video-preview:".esc_attr($psp_videopreview);

		}
		
		if ($robots_meta != "") {
        
			$robots_meta_string .= '<meta name="robots" content="'.esc_attr($robots_meta).'" />';	
			$robots_meta_string .= "\r\n";

		}
		
		return $robots_meta_string;
	
	} // end get_tax_robots_meta;
	
	
	private function get_cat_description_meta( $current_cat_obj, $term_meta ) {
	
		$desc_meta = "";
		$desc_meta_string = "";
		$keyword_meta_string = "";
		
		$current_tax_format = $this->psp_current_tax_format;
		$psp_settings = $this->psp_sitewide_settings;
		
		$this->taxonomy_name = "category";
		
		/************moved to parent*****************
		$term_meta = array();
        //$tax_format = "psp_".$tax_name."_desc_format";
		
		
		//$current_cat_obj = get_category(get_query_var('cat'));
        $cat_id = $current_cat_obj->cat_ID;
		$cat_name = $current_cat_obj->name;
		
		//$cat_ID = get_query_var('cat'); //move it to parent
		
		$term_meta_option_name = $cat_name. "_". $cat_id. "_metas";

		$term_meta = unserialize(get_option( "$cat_name_$cat_id_metas"));

		************moved to parent*****************/
		
		$description = isset($term_meta['description']) ? $term_meta['description'] : "";
		
		$term_description = category_description();
		$term_description = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($term_description));
		
		if (empty($description)) {

			$description = category_description();// term_description
			$desc_meta = $this->get_string_between($description, "[description]", "[/description]");

		}
		
		//$keywords = $this->get_string_between($description, "[keywords]", "[/keywords]");
		
				
		//if ($desc_meta == "" || $desc_meta == null) {
		if (empty($desc_meta)) {
			if (isset($description) && $description !== "") {
				$desc_meta = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($description));
			}
		} else {
			//equivalent to doing nothing
			$desc_meta = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($desc_meta));
		} 

		//if (isset($desc_meta) && $desc_meta !== "") {
		if (isset($desc_meta) && !empty($desc_meta)) {
		     
			/*********might not be needed********
			// "internal whitespace trim"
			$desc_meta = preg_replace("/\s\s+/", " ", $desc_meta);
		
			$desc_meta = trim(strip_tags($desc_meta));
			$desc_meta = str_replace('"', '', $desc_meta);

			// replace newlines on mac / windows?
			$desc_meta = str_replace("\r\n", ' ', $desc_meta);

			// maybe linux uses this alone
			$desc_meta = str_replace("\n", ' ', $desc_meta);
			**************************************/	
		
			// description format
			//$description_format = get_option('psp_cat_description_format');
			$description_format = isset($current_tax_format['description']) ? $current_tax_format['description'] : "";
			$psp_term_desc_format = isset($term_meta['descformat']) ? $term_meta['descformat'] : $description_format;
			$description_format = $psp_term_desc_format;
			
            if (!isset($description_format) || empty($description_format)) {
            	$description_format = "%description%";
				$description = str_replace('%description%', $desc_meta, $description_format); 
            } else {
			
				$psp_seo_title = "";
				if (!empty($this->taxonomy_title)) $psp_seo_title = $this->taxonomy_title;
			
				$description = str_replace('%seo_description%', $desc_meta, $description_format);
				$description = str_replace('%description%', $term_description, $description);
                $description = str_replace('%term_description%', $term_description, $description);		
				//$description = str_replace('%blog_title%', $this->sitename, $description);
				//$description = str_replace('%blog_description%', $this->sitedescription, $description);
				$description = str_replace('%site_name%', $this->sitename, $description);
				$description = str_replace('%site_description%', $this->sitedescription, $description);
				$description = str_replace('%category_name%', single_cat_title( '', false ), $description);
				$description = str_replace('%term_name%', single_cat_title( '', false ), $description);	
				$description = str_replace('%title%', single_cat_title( '', false ), $description);	
				$description = str_replace('%wp_title%', single_cat_title( '', false ), $description);	
				$description = str_replace('%seo_title%', $psp_seo_title, $description);	
				$description = str_replace('%sep%', $psp_settings['separator'], $description);
			}
			$this->taxonomy_description = $description;
			$desc_meta_string .= sprintf("<meta name=\"description\" content=\"%s\" />", esc_attr($description));
		}		
		
		//check for use_meta_keywords
		if ( isset($psp_settings['use_meta_keywords']) && $psp_settings['use_meta_keywords'] ) {
		
			$keywords = isset($term_meta['keywords']) ? $term_meta['keywords'] : "";
			
			if (empty($keywords)) {
			
				$keywords = $this->get_string_between($description, "[keywords]", "[/keywords]");
			
			}
			
			//if ($keywords != "" || $keywords != null) {
			if (isset($keywords) && !empty($keywords)) {
				$keywords = $this->psp_helper->internationalize($keywords);
				$this->taxonomy_keywords = $keywords;
				$keyword_meta_string .= sprintf("<meta name=\"keywords\" content=\"%s\" />", esc_attr($keywords));
			}
		}	
		
		if (isset($desc_meta_string) && $desc_meta_string != "" && isset($keyword_meta_string) && $keyword_meta_string != "") {
				$desc_meta_string .= "\r\n";
		}
		
		
		return $desc_meta_string.$keyword_meta_string;
	
	} // end get_cat_description_meta;
	
	private function get_tag_description_meta($current_tag_obj, $term_meta) {
	
		$desc_meta = "";
		$desc_meta_string = "";
		$keyword_meta_string = "";
		
		$current_tax_format = $this->psp_current_tax_format;
		$psp_settings = $this->psp_sitewide_settings;
		
		$this->taxonomy_name = "tag";
		
		/**********moved to parent**********
		$term_meta = array();
        //$tax_format = "psp_".$tax_name."_desc_format";	
		
		$term_id = $current_tag_obj->term_id;
		$term_name = $current_tag_obj->name;
		
		$term_meta_option_name = $term_name. "_". $term_id. "_metas";
		
		$term_meta = unserialize(get_option("$term_name_$term_id_metas"));	
		****************/
		
		$description = isset($term_meta['description']) ? $term_meta['description'] : "";
		
		$term_description = tag_description();
		$term_description = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($term_description));
		
		if (empty($description)) {

			$description = tag_description();// term_description
			$desc_meta = $this->get_string_between($description, "[description]", "[/description]");
		}
		
		//$keywords = $this->get_string_between($description, "[keywords]", "[/keywords]");
		
				
		//if ($desc_meta == "" || $desc_meta == null) {
		if (empty($desc_meta)) {
			if (isset($description) && $description !== "") {
				$desc_meta = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($description));
			}
		} else {		
			//equivalent to do nothing
			$desc_meta = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($desc_meta));
		} 

		//if (isset($desc_meta) && $desc_meta !== "") {	
		if (isset($desc_meta) && !empty($desc_meta)) {
		
			/*********might not be needed********
			// "internal whitespace trim"
			$desc_meta = preg_replace("/\s\s+/", " ", $desc_meta);
			
			$desc_meta = trim(strip_tags($desc_meta));
			$desc_meta = str_replace('"', '', $desc_meta);

			// replace newlines on mac / windows?
			$desc_meta = str_replace("\r\n", ' ', $desc_meta);

			// maybe linux uses this alone
			$desc_meta = str_replace("\n", ' ', $desc_meta);
			**************************************/
			
			// description format
			//$description_format = get_option('psp_tag_description_format');			
			$description_format = isset($current_tax_format['description']) ? $current_tax_format['description'] : "";
			$psp_term_desc_format = isset($term_meta['descformat']) ? $term_meta['descformat'] : $description_format;
			$description_format = $psp_term_desc_format;
			
            if (!isset($description_format) || empty($description_format)) {
            	$description_format = "%description%";
				$description = str_replace('%description%', $desc_meta, $description_format); 
				
            } else {
			
				$psp_seo_title = "";
				if (!empty($this->taxonomy_title)) $psp_seo_title = $this->taxonomy_title;
			
				$description = str_replace('%seo_description%', $desc_meta, $description_format);
				$description = str_replace('%description%', $term_description, $description);
                $description = str_replace('%term_description%', $term_description, $description);		
				//$description = str_replace('%blog_title%', $this->sitename, $description);
				//$description = str_replace('%blog_description%', $this->sitedescription, $description);
				$description = str_replace('%site_name%', $this->sitename, $description);
				$description = str_replace('%site_description%', $this->sitedescription, $description);
				$description = str_replace('%tag_name%', single_tag_title( '', false ), $description);
				$description = str_replace('%term_name%', single_tag_title( '', false ), $description);
				$description = str_replace('%title%', single_tag_title( '', false ), $description);	
				$description = str_replace('%wp_title%', single_tag_title( '', false ), $description);	
				$description = str_replace('%seo_title%', $psp_seo_title, $description);	
				$description = str_replace('%sep%', $psp_settings['separator'], $description);
			
			}
			$this->taxonomy_description = $description;
			$desc_meta_string .= sprintf("<meta name=\"description\" content=\"%s\" />", esc_attr($description));
		}		
		
		//check for use_meta_keywords
		if ( isset($psp_settings['use_meta_keywords']) && $psp_settings['use_meta_keywords'] ) {
		
			$keywords = isset($term_meta['keywords']) ? $term_meta['keywords'] : "";
			
			if (empty($keywords)) {
			
				$keywords = $this->get_string_between($description, "[keywords]", "[/keywords]");
			
			}
			
			//if ($keywords != "" || $keywords != null) {
			if (isset($keywords) && !empty($keywords)) {
				$keywords = $this->psp_helper->internationalize($keywords);
				$this->taxonomy_keywords = $keywords;
				$keyword_meta_string .= sprintf("<meta name=\"keywords\" content=\"%s\" />", esc_attr($keywords));
			}
		}
		if (isset($desc_meta_string) && $desc_meta_string != "" && isset($keyword_meta_string) && $keyword_meta_string != "") {
				$desc_meta_string .= "\r\n";
		}
		
		
		return $desc_meta_string.$keyword_meta_string;
	
	} // end get_tag_description_meta;
	
	private function get_tax_description_meta($current_term_obj, $term_meta, $tax_name) {
	
		$desc_meta = "";
		$desc_meta_string = "";
		$keyword_meta_string = "";
		
		$current_tax_format = $this->psp_current_tax_format;
		$psp_settings = $this->psp_sitewide_settings;
		
		$this->taxonomy_name = $tax_name;
		
		/********moved to parent************
		$term_meta = array();
		
        $tax_format = "psp_".$tax_name."_desc_format";	
		
		$term_id = $current_term_obj->term_id;
		$term_name = $current_term_obj->name;
		
		$term_meta_option_name = $term_name. "_". $term_id. "_metas";
		
		$term_meta = unserialize(get_option("$term_name_$term_id_metas"));
		***************************************/
		
		$description = isset($term_meta['description']) ? $term_meta['description'] : "";
		
		$term_description = term_description();
		$term_description = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($term_description));
		
		if (empty($description)) {

			$description = term_description();// term_description
			$desc_meta = $this->get_string_between($description, "[description]", "[/description]");
		
		}
		
		//$keywords = $this->get_string_between($description, "[keywords]", "[/keywords]");
		
				
		//if ($desc_meta == "" || $desc_meta == null) {
		if (empty($desc_meta)) {
			if (isset($description) && !empty($description)) {
				$desc_meta = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($description));
			}
		} else {
            //equivalent to do nothing		
			$desc_meta = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($desc_meta));
		} 

		//if (isset($desc_meta) && $desc_meta !== "") {
        if (isset($desc_meta) && !empty($desc_meta)) {
		
			/*********might not be needed********
			// "internal whitespace trim"
			$desc_meta = preg_replace("/\s\s+/", " ", $desc_meta);
			
			$desc_meta = trim(strip_tags($desc_meta));
			$desc_meta = str_replace('"', '', $desc_meta);

			// replace newlines on mac / windows?
			$desc_meta = str_replace("\r\n", ' ', $desc_meta);

			// maybe linux uses this alone
			$desc_meta = str_replace("\n", ' ', $desc_meta);
			**************************************/
		
			// description format
			$description_format = isset($current_tax_format['description']) ? $current_tax_format['description'] : "";//get_option($tax_format);
			$psp_term_desc_format = isset($term_meta['descformat']) ? $term_meta['descformat'] : $description_format;
			$description_format = $psp_term_desc_format;
			
            if (!isset($description_format) || empty($description_format)) {
            	$description_format = "%description%";
				$description = str_replace('%description%', $desc_meta, $description_format); 
            } else {
			
				//Term object
				//$term = get_term_by( 'slug', get_query_var('term'), get_query_var( 'taxonomy' ) );
				$term = $current_term_obj;
				
				$psp_seo_title = "";
				if (!empty($this->taxonomy_title)) $psp_seo_title = $this->taxonomy_title;
				
				$description = str_replace('%seo_description%', $desc_meta, $description_format);
                $description = str_replace('%description%', $term_description, $description);		
				//$description = str_replace('%blog_title%', $this->sitename, $description);
				//$description = str_replace('%blog_description%', $this->sitedescription, $description);
				$description = str_replace('%site_name%', $this->sitename, $description);
				$description = str_replace('%site_description%', $this->sitedescription, $description);
				$description = str_replace('%tax_name%', $tax_name, $description);
				$description = str_replace('%term_name%', $term->name, $description);
				$description = str_replace('%seo_title%', $psp_seo_title, $description);
				$description = str_replace('%title%', $term->name, $description);
				$description = str_replace('%wp_title%', $term->name, $description);
				$description = str_replace('%sep%', $psp_settings['separator'], $description);
				
			}
			$this->taxonomy_description = $description;
			$desc_meta_string .= sprintf("<meta name=\"description\" content=\"%s\" />", esc_attr($description));
		}
		
		//check for use_meta_keywords
		if ( isset($psp_settings['use_meta_keywords']) && $psp_settings['use_meta_keywords'] ) {
		
			$keywords = isset($term_meta['keywords']) ? $term_meta['keywords'] : "";
			
			if (empty($keywords)) {
			
				$keywords = $this->get_string_between($description, "[keywords]", "[/keywords]");
			
			}
			
			//if ($keywords != "" || $keywords != null) {
			if (isset($keywords) && !empty($keywords)) {
				$keywords = $this->psp_helper->internationalize($keywords);
				$this->taxonomy_keywords = $keywords;
				$keyword_meta_string .= sprintf("<meta name=\"keywords\" content=\"%s\" />", esc_attr($keywords));
			}
		}
				
		if (isset($desc_meta_string) && $desc_meta_string != "" && isset($keyword_meta_string) && $keyword_meta_string != "") {
				$desc_meta_string .= "\r\n";
		}
		
		return $desc_meta_string.$keyword_meta_string;
	
	} // end get_tax_description_meta;
	
	private function get_string_between($string, $start, $end){
        $string = " ".$string;
        $ini = strpos($string,$start);
        //if ($ini == 0) return "";
		if ($ini === false) {
			if ($start == "[description]") {
				return $string;
			} else {
				return "";
			}
		}
        $ini += strlen($start);   
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
	} //get_string_between
	
	private function get_cat_canonical_meta($current_cat_obj, $term_meta) {
	
		$can_link = "";
		$cat_link = "";
		$canonical_meta_string = "";

		if (isset($term_meta['canonical_url']) && !empty($term_meta['canonical_url'])) {
			$can_link = esc_url($term_meta['canonical_url']);
			$can_link = $this->psp_helper->paged_link($can_link);
		}
	
		//$cat_link = get_category_link(get_query_var('cat'));
		if(isset($current_cat_obj->cat_ID) && !empty($current_cat_obj->cat_ID)) $cat_link = get_category_link($current_cat_obj->cat_ID);
		                              
		//$can_link = $this->psp_helper->paged_link($cat_link);	
		if (empty($can_link)) $can_link = $this->psp_helper->paged_link($cat_link);

		if ($can_link != '') {
			//echo "".'<link rel="canonical" href="'.$can_link.'" />'."\r\n";
			$this->taxonomy_can_link = $can_link;
			$canonical_meta_string .= '<link rel="canonical" href="'.esc_url($can_link).'" />'."\r\n";
		}	
		
		return $canonical_meta_string;
        
	} // end get_cat_canonical_meta;
	
	private function get_tag_canonical_meta( $tag, $term_meta) {
	
		$can_link = "";
		$tag_link = "";
		$canonical_meta_string = "";
		
		if (isset($term_meta['canonical_url']) && !empty($term_meta['canonical_url'])) {
			$can_link = esc_url($term_meta['canonical_url']);
			$can_link = $this->psp_helper->paged_link($can_link);
		}
	
		//$tag = get_term_by('slug',get_query_var('tag'),'post_tag');
	    if (isset($tag->term_id) && !empty($tag->term_id)) {
	        $tag_link = get_tag_link($tag->term_id);
	    } 
		//$can_link = $this->psp_helper->paged_link($tag_link);
		if (empty($can_link)) $can_link = $this->psp_helper->paged_link($tag_link);		
		
		if ($can_link != '') {
			//echo "".'<link rel="canonical" href="'.$can_link.'" />'."\r\n";
			$this->taxonomy_can_link = $can_link;
			$canonical_meta_string .= '<link rel="canonical" href="'.esc_url($can_link).'" />'."\r\n";
		}
        
		return $canonical_meta_string;
		
	} // end get_tag_canonical_meta;
	
	private function get_tax_canonical_meta($term_object, $term_meta) {
	
		$can_link = "";
		$term_link = "";
		$canonical_meta_string = "";
	
		//$curr_term = get_query_var('term');
        //$term_object = get_term_by( 'slug', $curr_term, get_query_var( 'taxonomy' ) );
		
		if (isset($term_meta['canonical_url']) && !empty($term_meta['canonical_url'])) {
			$can_link = esc_url($term_meta['canonical_url']);
			$can_link = $this->psp_helper->paged_link($can_link);
		}
		
		if (isset($term_object->term_id) && !empty($term_object->term_id)) {
			$term_link = get_term_link( $term_object );
		}
		
		if (empty($can_link)) $can_link = $this->psp_helper->paged_link($term_link);
		
		if ($can_link != '') {
			//echo "".'<link rel="canonical" href="'.$can_link.'" />'."\r\n";
			$this->taxonomy_can_link = $can_link;
			$canonical_meta_string .= '<link rel="canonical" href="'.esc_url($can_link).'" />'."\r\n";
		}		
		
		return $canonical_meta_string;
	} // end get_tax_canonical_meta;	
	
	public function get_cat_psp_title() {
	
		$title = "";
		$term_id = "";
		$term_name = "";
		$cat_id = "";
		$cat_name = "";
		$term_meta = array();
		
		$psp_settings = $this->psp_sitewide_settings;
		
		if (isset($this->psp_current_tax_format) && !empty($this->psp_current_tax_format)){
			$current_tax_format = $this->psp_current_tax_format;
		} else {
			$current_tax_format_option = "psp_category_settings";
			$current_tax_format = get_option($current_tax_format_option);
			$this->psp_current_tax_format = $current_tax_format;
		}
		
		if (isset($this->psp_current_cat_obj) && !empty($this->psp_current_cat_obj)){		
			$current_cat_obj = $this->psp_current_cat_obj;
		} else {
			$current_cat_obj = get_category(get_query_var('cat'));
			$this->psp_current_cat_obj = $current_cat_obj;
		}
		if (is_object($current_cat_obj)) {
			$cat_id = isset($current_cat_obj->cat_ID) ? $current_cat_obj->cat_ID : '';
			$cat_name = isset($current_cat_obj->name) ? $current_cat_obj->name : '';
		}
		//$cat_ID = get_query_var('cat'); //move it to parent
		
		if (empty($this->psp_cat_meta)) {
		
			$term_meta_option_name = "psp_category_seo_metas_".$cat_id;
			//$term_meta = get_option("$cat_name_$cat_id_metas");			
			if (!empty($cat_id)) {
				$term_meta = get_term_meta( $cat_id, "psp_category_seo_metas_$cat_id");
				if ($term_meta) $term_meta = $term_meta[0]; 
				if (!$term_meta) $term_meta = get_option( "psp_category_seo_metas_$cat_id");
			}
			$this->psp_cat_meta = $term_meta;
		} else {
			$term_meta = $this->psp_cat_meta;
		}
		
		if (empty($this->term_social_meta)) {
			if (!empty($cat_id)) {
				$term_social_meta = get_term_meta( $cat_id, "psp_category_social_metas_$cat_id");
				if ($term_social_meta) $this->term_social_meta = $term_social_meta[0]; 
				if (!$this->term_social_meta) $this->term_social_meta = get_option( "psp_category_social_metas_$cat_id");
			}
			//if (!empty($cat_id)) $this->term_social_meta = get_option( "psp_category_social_metas_$cat_id");
		}
		
		$title = isset($term_meta['title']) ? $this->psp_helper->internationalize($term_meta['title']) : "";
		$category_name = ucwords($this->psp_helper->internationalize($cat_name));
		if (empty($title)) {		
			
			$title = $category_name;
		}
		
			$title = trim(stripcslashes($title));
			//is this needed?
			$category_description = $this->psp_helper->internationalize(category_description());
			//$category_name = ucwords($this->psp_helper->internationalize(single_cat_title('', false)));	
						
			//$title_format = get_option('aiosp_category_title_format');
			$title_format = isset($current_tax_format['title']) ? $current_tax_format['title'] : "";
			$psp_term_title_format = isset($term_meta['titleformat']) ? $term_meta['titleformat'] : $title_format;
			$title_format = $psp_term_title_format;
			
			if (!isset($title_format) || empty($title_format)) {
            	$title_format = "%seo_title%";
				$title = str_replace('%seo_title%', $title, $title_format); 
            } else {
				$title = str_replace('%seo_title%', $title, $title_format);
				$title = str_replace('%title%', $category_name, $title);
				$title = str_replace('%wp_title%', $category_name, $title);
				$title = str_replace('%category_name%', $category_name, $title);
				$title = str_replace('%term_name%', $category_name, $title);				
				//$title = str_replace('%sep%', htmlentities($psp_settings['separator']), $title);
				$title = str_replace('%sep%', ($psp_settings['separator']), $title);
				$title = str_replace('%term_description%', $category_description, $title);
				$title = str_replace('%description%', $category_description, $title);
				//$title = str_replace('%blog_title%', $this->psp_helper->internationalize(get_bloginfo('name')), $title);
				//$title = str_replace('%blog_description%', $this->psp_helper->internationalize(get_bloginfo('description')), $title);
				//$title = str_replace('%blog_title%', $this->sitename, $title);
				//$title = str_replace('%blog_description%', $this->sitedescription, $title);
				$title = str_replace('%site_name%', $this->sitename, $title);
				$title = str_replace('%site_description%', $this->sitedescription, $title);				
				//$title = str_replace ("+"," ", $title);
			}
 
		//$title = $this->paged_title($title);
		$title = $this->psp_helper->paged_title($title);
		$this->taxonomy_title = $title;
		return $title;
		
	} // end get_cat_psp_title
	
	public function get_tag_psp_title() {
	
		//global $STagging;
		
		$title = "";
		$term_meta = array();
		$term_id = "";
		$term_name = "";
		
		$psp_settings = $this->psp_sitewide_settings;
		
		if (isset($this->psp_current_tax_format) && !empty($this->psp_current_tax_format)){
			$current_tax_format = $this->psp_current_tax_format;
		} else {
			$current_tax_format_option = "psp_tag_settings";
			$current_tax_format = get_option($current_tax_format_option);
			$this->psp_current_tax_format = $current_tax_format;
		}
		
		if (isset($this->psp_current_tag_obj) && !empty($this->psp_current_tag_obj)){
			$current_tag_obj = $this->psp_current_tag_obj;
		} else {
			$current_tag_obj = get_term_by('slug',get_query_var('tag'),'post_tag');
			$this->psp_current_tag_obj = $current_tag_obj;
		}
		if ( is_object($current_tag_obj)) {
			$term_id = isset($current_tag_obj->term_id) ? $current_tag_obj->term_id : '';
			$term_name = isset($current_tag_obj->name) ? $current_tag_obj->name : '';
		}
		//$psp_tag_meta = isset($this->psp_tag_meta) : $this->psp_tag_meta : "";
		if (empty($this->psp_tag_meta)) {
		
			$term_meta_option_name = "psp_taxonomy_seo_metas_".$term_id;
			//$term_meta = get_option("$cat_name_$cat_id_metas");			
			if (!empty($term_id)) {
				$term_meta = get_term_meta( $term_id, "psp_taxonomy_seo_metas_$term_id");
				if($term_meta) $term_meta = $term_meta[0];
				if(!$term_meta) $term_meta = get_option( "psp_taxonomy_seo_metas_$term_id");
			}
			$this->psp_tag_meta = $term_meta;
		} else {
			$term_meta = $this->psp_tag_meta;
		}
		//$psp_term_social_meta = isset($this->term_social_meta) : $this->term_social_meta : "";
		if (empty($this->term_social_meta)) {
			if (!empty($term_id)) {
				$term_social_meta = get_term_meta( $term_id, "psp_taxonomy_social_metas_$term_id");
				if($term_social_meta) $this->term_social_meta = $term_social_meta[0];
				if (!$this->term_social_meta) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
			}
		}
			
		$title = isset($term_meta['title']) ? $this->psp_helper->internationalize($term_meta['title']) : "";
		$term_name = ucwords($this->psp_helper->internationalize($term_name));
		if (empty($title)) {
			//$tag = $this->psp_helper->internationalize($term_name);
			//$tag = $this->capitalize($tag);
			//$tag = ucwords($this->psp_helper->internationalize($term_name));
			//$title = $tag;
			$title = $term_name;
		}
		$title = trim(stripcslashes($title));
		//is this needed?
		$tag_description = $this->psp_helper->internationalize(tag_description());
		
		//$title_format = get_option('aiosp_tag_title_format');
		$title_format = isset($current_tax_format['title']) ? $current_tax_format['title'] : "";
		$psp_term_title_format = isset($term_meta['titleformat']) ? $term_meta['titleformat'] : $title_format;
		$title_format = $psp_term_title_format;
		
		if (!isset($title_format) || empty($title_format)) {
            	$title_format = "%seo_title%";
				$title = str_replace('%seo_title%', $title, $title_format); 
        } else {
		
			$title = str_replace('%seo_title%', $title, $title_format);
			$title = str_replace('%title%', $term_name, $title);
			$title = str_replace('%wp_title%', $term_name, $title);
			$title = str_replace('%tag_name%', $term_name, $title);
			$title = str_replace('%term_name%', $term_name, $title);			
			//$title = str_replace('%sep%', htmlentities($psp_settings['separator']), $title);
			$title = str_replace('%sep%', $psp_settings['separator'], $title);
			$title = str_replace('%term_description%', $tag_description, $title);
			$title = str_replace('%description%', $tag_description, $title);
			//$title = str_replace('%blog_title%', $this->psp_helper->internationalize(get_bloginfo('name')), $title);
			//$title = str_replace('%blog_description%', $this->psp_helper->internationalize(get_bloginfo('description')), $title);		
			//$title = str_replace('%blog_title%', $this->sitename, $title);
			//$title = str_replace('%blog_description%', $this->sitedescription, $title);
			$title = str_replace('%site_name%', $this->sitename, $title);
			$title = str_replace('%site_description%', $this->sitedescription, $title);
		}		
		
		//$title = $this->paged_title($title);
		$title = $this->psp_helper->paged_title($title);
		$this->taxonomy_title = $title;
		return $title;
		
	} // end get_tag_psp_title
	
	public function get_tax_psp_title($psparr = false) {
	
		$title = "";
		$term_meta = array();
		//$term_id = isset($_GET['tag_ID']) ? $_GET['tag_ID'] : '';
		$term_id = '';
	
		$term_name = "";
		$tax_slug = '';
		
		$psp_settings = $this->psp_sitewide_settings;
		
	//	if (!empty($this->psp_current_tax_format)){
	//		$current_tax_format = $this->psp_current_tax_format;
			
	//	} else {
			//$current_tax_object = get_taxonomy( get_query_var( 'taxonomy' ));
			//$tax_name = $current_tax_object->labels->name;
			if (!$term_id) $term_id = get_queried_object_id(); //get_queried_object()->term_id;
			if (!$tax_slug) $tax_slug = get_query_var( 'taxonomy' );
			$current_tax_format_option = "psp_". $tax_slug. "_settings";
			//error_log($current_tax_format_option);
			$current_tax_format = get_option($current_tax_format_option);
			$this->psp_current_tax_format = $current_tax_format;
	//	}
		
		//$curr_term = get_query_var('term');
		
		if (isset($this->psp_current_term_obj) && !empty($this->psp_current_term_obj)){
			$term_object = $this->psp_current_term_obj;
		} else {
			if ($tax_slug && $term_id) {
				$term_object = get_term_by( 'id', $term_id, $tax_slug );
			} else {
				$term_object = get_term_by( 'slug', get_query_var('term'), get_query_var( 'taxonomy' ) );
			}
			$this->psp_current_term_obj = $term_object;
		}
		if ( is_object($term_object)) {
			$term_id = isset($term_object->term_id) ? $term_object->term_id : '';
			$term_name = isset($term_object->name) ? $term_object->name : '';
		}
		//$psp_term_meta = isset($this->psp_term_meta) ? $this->psp_term_meta : "";
		if (empty($this->psp_term_meta)) {
		
			$term_meta_option_name = "psp_taxonomy_seo_metas_".$term_id;
			//$term_meta = get_option("$cat_name_$cat_id_metas");			
			if (!empty($term_id)) {
				$term_meta = get_term_meta( $term_id, "psp_taxonomy_seo_metas_$term_id");
				if ($term_meta) $term_meta = $term_meta[0];
				if (!$term_meta) $term_meta = get_option( "psp_taxonomy_seo_metas_$term_id");
			}	
			$this->psp_term_meta = $term_meta;
		
		} else {
			$term_meta = $this->psp_term_meta;
		}
		$psp_term_social_meta = isset($this->term_social_meta) ? $this->term_social_meta : "";
		if (empty($psp_term_social_meta)) {
			if (!empty($term_id)) {
				$term_social_meta = get_term_meta( $term_id, "psp_taxonomy_social_metas_$term_id");
				if ($term_social_meta) $this->term_social_meta = $term_social_meta[0];
				if (!$this->term_social_meta) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
			}
			//if (!empty($term_id)) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
		}
		//continue
		//$title = $this->psp_helper->internationalize($term_meta->title);
		$title = isset($term_meta['title']) ? $this->psp_helper->internationalize($term_meta['title']) : "";
		$term_name = ucwords($this->psp_helper->internationalize($term_name));
		if (empty($title)) {			
			$title = $term_name;
		}
		$title = trim(stripcslashes($title));
		$term_description = $this->psp_helper->internationalize(term_description());
		
		$title_format = isset($current_tax_format['title']) ? $current_tax_format['title'] : "";
		$psp_term_title_format = isset($term_meta['titleformat']) ? $term_meta['titleformat'] : $title_format;
		$title_format = $psp_term_title_format;
		//$title_format = get_option($tax_title_format);		
		//$title_format = get_option('psp_taxonomy_title_format');	
		//error_log($title_format);

		if (!isset($title_format) || empty($title_format)) {
            $title_format = "%seo_title%";
			//$new_title = str_replace('%seo_title%', $title, $title_format); 
			//$new_title = isset($_GET['tag_ID'], $_GET['taxonomy']) ? $title_format : str_replace('%seo_title%', $title, $title_format);
			$new_title = is_tax()  ? str_replace('%seo_title%', $title, $title_format) : $title_format ;
        } else {
			//$new_title = isset($_GET['tag_ID'], $_GET['taxonomy']) ? $title_format : str_replace('%seo_title%', $title, $title_format);
			$new_title = is_tax()  ? str_replace('%seo_title%', $title, $title_format) : $title_format ;
			//$new_title = str_replace('%seo_title%', $title, $title_format); 
			$new_title = str_replace('%title%', $term_name, $new_title);
			$new_title = str_replace('%wp_title%', $term_name, $new_title);
			$new_title = str_replace('%term_name%', $term_name, $new_title);			
		//	$new_title = str_replace('%sep%', htmlentities($psp_settings['separator']), $new_title);
			$new_title = str_replace('%sep%', $psp_settings['separator'], $new_title);
			//$new_title = str_replace('%term_description%', $term_description, $new_title);
			$new_title = str_replace('%description%', $term_description, $new_title);
			//$new_title = str_replace('%blog_title%', $this->sitename, $new_title);
			//$new_title = str_replace('%blog_description%', $this->sitedescription, $new_title);
			$new_title = str_replace('%site_name%', $this->sitename, $new_title);
			$new_title = str_replace('%site_description%', $this->sitedescription, $new_title);
        }
		//$title = $this->paged_title($new_title);	
		$title = $this->psp_helper->paged_title($new_title);
		$this->taxonomy_title = $title;
		
		
		$sitename = $this->sitename;
		$sitedescription = $this->sitedescription;
		//$psp_title_separator = isset($psp_settings['separator']) ? htmlentities($psp_settings['separator']) : '';
		$psp_title_separator = isset($psp_settings['separator']) ? ($psp_settings['separator']) : '';
		
		if ($psparr) {

        	$sitedescription = !empty($sitedescription) ? $sitedescription : '';
            $sitename = !empty($sitename) ? $sitename : '';
            $term_name = !empty($term_name) ? $term_name :  '';
            $title = !empty($title) ? $title : '';
            $term_description = !empty($term_description) ? $term_description : '';
            $psp_title_separator = !empty($psp_title_separator) ? $psp_title_separator : '';
        	
        	$psp_term_format_arr = array('site_name' => $sitename, 'site_description' => $sitedescription, 'description' => $term_description, 'wp_title' => $term_name, 'seo_title' => $title, 'sep' => $psp_title_separator);
        
        	return $psp_term_format_arr;
        
        }
        $title = trim($title);
        $title = trim($title, $psp_title_separator);
		return $title;
		
	} // end get_tax_psp_title
	
	public function get_cat_seo_metas($canonical) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";
		$term_meta = array();
		$prevnext_meta_string = "";
		$cat_id = "";
		$cat_name = "";
		$json_schema_string =  "";
		$jsonld_script_tag = "";
		
		if (!empty($this->psp_current_tax_format)){
			$current_tax_format = $this->psp_current_tax_format;
		} else {
			$current_tax_format_option = "psp_category_settings";
			$current_tax_format = get_option($current_tax_format_option);
			$this->psp_current_tax_format = $current_tax_format;
		}
		
		if (isset($this->psp_current_cat_obj) && !empty($this->psp_current_cat_obj)){		
			$current_cat_obj = $this->psp_current_cat_obj;
		} else {
			$current_cat_obj = get_category(get_query_var('cat'));
			$this->psp_current_cat_obj = $current_cat_obj;
		}
		if ( is_object($current_cat_obj)) {
			$cat_id = isset($current_cat_obj->cat_ID) ? $current_cat_obj->cat_ID : '';
			$cat_name = isset($current_cat_obj->name) ? $current_cat_obj->name : '';
		}
		//$cat_ID = get_query_var('cat'); //move it to parent
		
		if (empty($this->psp_cat_meta)) {
		
			$term_meta_option_name = "psp_category_seo_metas_".$cat_id;
			//$term_meta = get_option("$cat_name_$cat_id_metas");			
			if (!empty($cat_id)) {
				$term_meta = get_term_meta( $cat_id, "psp_category_seo_metas_$cat_id");
				if ($term_meta) $term_meta = $term_meta[0];
				if (!$term_meta) $term_meta = get_option( "psp_category_seo_metas_$cat_id");
			}
			$this->psp_cat_meta = $term_meta;
		} else {
			$term_meta = $this->psp_cat_meta;
		}
		
		if (empty($this->term_social_meta)) {
			if (!empty($cat_id)) {
				$term_social_meta = get_term_meta( $cat_id, "psp_category_social_metas_$cat_id");
				if ($term_social_meta) $this->term_social_meta = $term_social_meta[0];
				if (!$this->term_social_meta) $this->term_social_meta = get_option( "psp_category_social_metas_$cat_id");
			}
			//if (!empty($cat_id)) $this->term_social_meta = get_option( "psp_category_social_metas_$cat_id");
		}
		
		$robots_meta_string = $this->get_tax_robots_meta($term_meta);
		if (!is_paged()) {
			$desc_keyword_meta_string = $this->get_cat_description_meta($current_cat_obj, $term_meta);
		}
		if (!empty($desc_keyword_meta_string)) $desc_keyword_meta_string .= "\r\n";
		
		if ( $canonical ) {
			$canonical_meta_string = $this->get_cat_canonical_meta($current_cat_obj, $term_meta);
		}
		
		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		$psp_settings = $this->psp_sitewide_settings;
		
		if (isset($term_meta['schema_string']) && !empty($term_meta['schema_string'])) {
			$json_schema_string = $term_meta['schema_string'];
			$json_schema_string = html_entity_decode(esc_html(stripcslashes($json_schema_string)));
			$schema_obj = json_decode($json_schema_string);
			if($schema_obj === null) {
			   $json_schema_string = "";
			}
		}
		if (!empty($json_schema_string) && !empty($psp_settings['enable_schema'])) {
			$jsonld_script_tag = '<scri'. 'pt type="application/ld+json">'. "\r\n". $json_schema_string . "\r\n". '</scri' . 'pt>';
		}
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$prevnext_meta_string;
		if (!empty($jsonld_script_tag) && !is_paged() ) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		
		return $seo_meta_string;
	
	} // end get_cat_seo_metas;	
	
	public function get_tag_seo_metas($canonical) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";
		$term_meta = array();
		$prevnext_meta_string = "";
		$term_id = "";
		$term_name = "";
		$json_schema_string =  "";
		$jsonld_script_tag = "";
		
		if (!empty($this->psp_current_tax_format)){
			$current_tax_format = $this->psp_current_tax_format;
		} else {
			$current_tax_format_option = "psp_tag_settings";
			$current_tax_format = get_option($current_tax_format_option);
			$this->psp_current_tax_format = $current_tax_format;
		}
		
		if (isset($this->psp_current_tag_obj) && !empty($this->psp_current_tag_obj)){
			$current_tag_obj = $this->psp_current_tag_obj;
		} else {
			$current_tag_obj = get_term_by('slug',get_query_var('tag'),'post_tag');
			$this->psp_current_tag_obj = $current_tag_obj;
		}
		if ( is_object($current_tag_obj)) {
			$term_id = isset($current_tag_obj->term_id) ? $current_tag_obj->term_id : '';
			$term_name = isset($current_tag_obj->name) ? $current_tag_obj->name : '';
		}
		if (empty($this->psp_tag_meta)) {
		
			$term_meta_option_name = "psp_taxonomy_seo_metas_".$term_id;
			//$term_meta = get_option("$cat_name_$cat_id_metas");			
			if (!empty($term_id)) {
				$term_meta = get_term_meta( $term_id, "psp_taxonomy_seo_metas_$term_id");
				if ($term_meta) $term_meta = $term_meta[0];
				if (!$term_meta) $term_meta = get_option( "psp_taxonomy_seo_metas_$term_id");				
			}
			$this->psp_tag_meta = $term_meta;
		} else {
			$term_meta = $this->psp_tag_meta;
		}
		
		if (empty($this->term_social_meta)) {
			if (!empty($term_id)) {
				$term_social_meta = get_term_meta( $term_id, "psp_taxonomy_social_metas_$term_id");
				if ($term_social_meta) $this->term_social_meta = $term_social_meta[0]; 
				if (!$this->term_social_meta) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
			}
			//if (!empty($term_id)) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
		}
		
		$robots_meta_string = $this->get_tax_robots_meta($term_meta);
		if (!is_paged()) {
			$desc_keyword_meta_string = $this->get_tag_description_meta($current_tag_obj, $term_meta);
		}
		
		if (!empty($desc_keyword_meta_string)) $desc_keyword_meta_string .= "\r\n";
		
		if ( $canonical ) {
			$canonical_meta_string = $this->get_tag_canonical_meta($current_tag_obj, $term_meta);
		}
		
		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		$psp_settings = $this->psp_sitewide_settings;
		
		if (isset($term_meta['schema_string']) && !empty($term_meta['schema_string'])) {
			$json_schema_string = $term_meta['schema_string'];
		    $json_schema_string = html_entity_decode(esc_html(stripcslashes($json_schema_string)));
			$schema_obj = json_decode($json_schema_string);
			if($schema_obj === null) {
			    $json_schema_string = "";
			}
		}
		if (!empty($json_schema_string) && !empty($psp_settings['enable_schema'])) {
			$jsonld_script_tag = '<scri' . 'pt type="application/ld+json">'. "\r\n". $json_schema_string . "\r\n". '</scri' . 'pt>';
		}
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$prevnext_meta_string;
		//if (!empty($jsonld_script_tag)) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		if (!empty($jsonld_script_tag) && !is_paged() ) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		
		return $seo_meta_string;
	
	
	} // end get_tag_seo_metas;
	
	public function get_tax_seo_metas($canonical) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";
		$term_meta = array();
		$prevnext_meta_string = "";
		$json_schema_string =  "";
		$jsonld_script_tag = "";
		
		$current_tax_object = get_taxonomy( get_query_var( 'taxonomy' ));
		$tax_name = $current_tax_object->labels->name;
		
		if (!empty($this->psp_current_tax_format)){
			$current_tax_format = $this->psp_current_tax_format;
		} else {
			
			$tax_slug = get_query_var( 'taxonomy' );
			$current_tax_format_option = "psp_". $tax_slug. "_settings";
			$current_tax_format = get_option($current_tax_format_option);
			$this->psp_current_tax_format = $current_tax_format;
		}
		
		//$curr_term = get_query_var('term');
		if (isset($this->psp_current_term_obj) && !empty($this->psp_current_term_obj)){
			$term_object = $this->psp_current_term_obj;
		} else {
			$term_object = get_term_by( 'slug', get_query_var('term'), get_query_var( 'taxonomy' ) );
			$this->psp_current_term_obj = $term_object;
		}
		if ( is_object($term_object)) { 
			$term_id = isset($term_object->term_id) ? $term_object->term_id : '';
			$term_name = isset($term_object->name) ? $term_object->name : '';
		}
		
		if (empty($this->psp_term_meta)) {
		
			$term_meta_option_name = "psp_taxonomy_seo_metas_".$term_id;
			//$term_meta = get_option("$cat_name_$cat_id_metas");			
			if (!empty($term_id)) {
				$term_meta = get_term_meta( $term_id, "psp_taxonomy_seo_metas_$term_id");
				if ($term_meta) $term_meta = $term_meta[0]; 
				if (!$term_meta) $term_meta = get_option( "psp_taxonomy_seo_metas_$term_id");				
			}	
			$this->psp_term_meta = $term_meta;
		
		} else {
			$term_meta = $this->psp_term_meta;
		}
		
		if (empty($this->term_social_meta)) {
			if (!empty($term_id)) {
				$term_social_meta = get_term_meta( $term_id, "psp_taxonomy_social_metas_$term_id");
				if ($term_social_meta) $this->term_social_meta = $term_social_meta[0];
				if (!$this->term_social_meta) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
			}
			//if (!empty($term_id)) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
		}
		
		$robots_meta_string = $this->get_tax_robots_meta($term_meta);
		if (!is_paged()) {
			$desc_keyword_meta_string = $this->get_tax_description_meta($term_object, $term_meta, $tax_name);
		}
		
		if (!empty($desc_keyword_meta_string)) $desc_keyword_meta_string .= "\r\n";
		
		if ( $canonical ) {
			$canonical_meta_string = $this->get_tax_canonical_meta($term_object, $term_meta);
		}
		
		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		$psp_settings = $this->psp_sitewide_settings;
		
		if (isset($term_meta['schema_string']) && !empty($term_meta['schema_string'])) {
			$json_schema_string = $term_meta['schema_string'];
		    $json_schema_string = html_entity_decode(esc_html(stripcslashes($json_schema_string)));
			$schema_obj = json_decode($json_schema_string);
			if($schema_obj === null) {
			    $json_schema_string = "";
			}
		}
		if (!empty($json_schema_string)  && !empty($psp_settings['enable_schema']) ) {
			$jsonld_script_tag = '<scri' . 'pt type="application/ld+json">'. "\r\n". $json_schema_string . "\r\n".'</scri' . 'pt>';
		}
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$prevnext_meta_string;
		//if (!empty($jsonld_script_tag)) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		if (!empty($jsonld_script_tag) && !is_paged() ) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		
		return $seo_meta_string;
	
	} // end get_tax_seo_metas;
	
	public function psp_get_tax_schema($term_meta) {

			$json_schema_string = "";
			$jsonld_script_tag = "";

		if (isset($term_meta['schema_string']) && !empty($term_meta['schema_string'])) {
			$json_schema_string = $term_meta['schema_string'];
			$json_schema_string = html_entity_decode(esc_html(stripcslashes($json_schema_string)));
			$schema_obj = json_decode($json_schema_string);
			if($schema_obj === null) {
			   $json_schema_string = "";
			}
		}
		if (!empty($json_schema_string)) {
			$jsonld_script_tag = '<scri'. 'pt type="application/ld+json">'. "\r\n". $json_schema_string . "\r\n". '</scri' . 'pt>';
		}
		
		if (!empty($jsonld_script_tag) && !is_paged() ) {

			return $jsonld_script_tag;
			
		} else {
			return "";
		}

	}
	
	public function get_tax_seo_metas_for_rest($term_id, $canonical, $tax_name){
	    
	    //error_log("termid ".($term_id));

		if (empty($term_id )) return "";
		if (empty($tax_name )) return "";
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";
		$term_meta = array();
		$prevnext_meta_string = "";
		$json_schema_string =  "";
		$jsonld_script_tag = "";
		
		if ( 'category' == $tax_name ) {
		
			$psp_seo_label = "psp_category_seo_metas_$term_id";
			$psp_social_label = "psp_category_social_metas_$term_id";
		
		} else {
			$psp_seo_label = "psp_taxonomy_seo_metas_$term_id";
			$psp_social_label = "psp_taxonomy_social_metas_$term_id";
		}
		
		//$current_tax_object = get_taxonomy( get_query_var( 'taxonomy' ));
		//$tax_name = $current_tax_object->labels->name;
		
		if (!empty($this->psp_current_tax_format)){
			$current_tax_format = $this->psp_current_tax_format;
		} else {
			
			//$tax_slug = get_query_var( 'taxonomy' );
			$tax_slug = $tax_name;
			$current_tax_format_option = "psp_". $tax_slug. "_settings";
			$current_tax_format = get_option($current_tax_format_option);
			$this->psp_current_tax_format = $current_tax_format;
		}
		
		//$curr_term = get_query_var('term');
		if (isset($this->psp_current_term_obj) && !empty($this->psp_current_term_obj)){
			$term_object = $this->psp_current_term_obj;
		} else {
			//$term_object = get_term_by( 'slug', get_query_var('term'), get_query_var( 'taxonomy' ) );
			$term_object = get_term( $term_id );
			$this->psp_current_term_obj = $term_object;
		}
		if ( is_object($term_object)) { 
			//$term_id = isset($term_object->term_id) ? $term_object->term_id : '';
			$term_name = isset($term_object->name) ? $term_object->name : '';
		}
		
		if (empty($this->psp_term_meta)) {
		
			//$term_meta_option_name = "psp_taxonomy_seo_metas_".$term_id;
			//$term_meta = get_option("$cat_name_$cat_id_metas");			
			if (!empty($term_id)) {
				//$term_meta = get_term_meta( $term_id, "psp_taxonomy_seo_metas_$term_id");
				$term_meta = get_term_meta( $term_id, $psp_seo_label);
				if ($term_meta) $term_meta = $term_meta[0]; 
				//if (!$term_meta) $term_meta = get_option( "psp_taxonomy_seo_metas_$term_id");	
				if (!$term_meta) $term_meta = get_option( $psp_seo_label);	
			}	
			$this->psp_term_meta = $term_meta;
		
		} else {
			$term_meta = $this->psp_term_meta;
		}
		
		
		
		if (empty($this->term_social_meta)) {
			if (!empty($term_id)) {
				//$term_social_meta = get_term_meta( $term_id, "psp_taxonomy_social_metas_$term_id");
				$term_social_meta = get_term_meta( $term_id, $psp_social_label);
				if ($term_social_meta) $this->term_social_meta = $term_social_meta[0];
				//if (!$this->term_social_meta) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
				if (!$this->term_social_meta) $this->term_social_meta = get_option( "psp_taxonomy_social_metas_$term_id");
			}
			//if (!empty($term_id)) $this->term_social_meta = get_option( $psp_social_label);
		}
		
		$robots_meta_string = $this->get_tax_robots_meta($term_meta);
		
		if ($this->current_pageno < 2) {
			$desc_keyword_meta_string = $this->get_tax_description_meta($term_object, $term_meta, $tax_name);
		}
		
		if (!empty($desc_keyword_meta_string)) $desc_keyword_meta_string .= "\r\n";
		
		if ( $canonical ) {
			$canonical_meta_string = $this->get_tax_canonical_meta($term_object, $term_meta);
		}
		
		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		if (isset($term_meta['schema_string']) && !empty($term_meta['schema_string'])) {
			$json_schema_string = $term_meta['schema_string'];
		    $json_schema_string = html_entity_decode(esc_html(stripcslashes($json_schema_string)));
			$schema_obj = json_decode($json_schema_string);
			if($schema_obj === null) {
			    $json_schema_string = "";
			}
		}
		if (!empty($json_schema_string)) {
			$jsonld_script_tag = '<scri' . 'pt type="application/ld+json">'. "\r\n". $json_schema_string . "\r\n".'</scri' . 'pt>';
		}
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$prevnext_meta_string;
		//if (!empty($jsonld_script_tag)) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		if (!empty($jsonld_script_tag) ) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		
		return $seo_meta_string;
	
	} // end get_tax_seo_metas_for_rest;
	
	public function get_tax_psp_title_for_rest($term_id, $tax_name) {
	
		$title = "";
		$term_meta = array();
		$psp_seo_label =  "";	
		$psp_social_label =  "";
	
		$term_name = "";
		$tax_slug = $tax_name;
		
		$psp_settings = $this->psp_sitewide_settings;
		
		$current_tax_format_option = "psp_". $tax_slug. "_settings";
		//error_log($current_tax_format_option);
		$current_tax_format = get_option($current_tax_format_option);
		$this->psp_current_tax_format = $current_tax_format;	
		
		if ( 'category' == $tax_name ) {
		
			$psp_seo_label = "psp_category_seo_metas_$term_id";
			$psp_social_label = "psp_category_social_metas_$term_id";
		
		} else {
			$psp_seo_label = "psp_taxonomy_seo_metas_$term_id";
			$psp_social_label = "psp_taxonomy_social_metas_$term_id";
		}
		
		//$curr_term = get_query_var('term');
		
		if (isset($this->psp_current_term_obj) && !empty($this->psp_current_term_obj)){
			$term_object = $this->psp_current_term_obj;
		} else {
			$term_object = get_term( $term_id );
			$this->psp_current_term_obj = $term_object;			
		}
		if ( is_object($term_object)) {
			//$term_id = isset($term_object->term_id) ? $term_object->term_id : '';
			$term_name = isset($term_object->name) ? $term_object->name : '';
		}
		//$psp_term_meta = isset($this->psp_term_meta) ? $this->psp_term_meta : "";
		if (empty($this->psp_term_meta)) {		
					
			if (!empty($term_id)) {
				$term_meta = get_term_meta( $term_id, $psp_seo_label);
				if ($term_meta) $term_meta = $term_meta[0];
				if (!$term_meta) $term_meta = get_option( $psp_seo_label);
			}	
			$this->psp_term_meta = $term_meta;
		
		} else {
			$term_meta = $this->psp_term_meta;
		}
		$psp_term_social_meta = isset($this->term_social_meta) ? $this->term_social_meta : "";
		if (empty($psp_term_social_meta)) {
			if (!empty($term_id)) {
				$term_social_meta = get_term_meta( $term_id, $psp_social_label);
				if ($term_social_meta) $this->term_social_meta = $term_social_meta[0];
				if (!$this->term_social_meta) $this->term_social_meta = get_option( $psp_social_label);
			}			
		}
		//continue
		//$title = $this->psp_helper->internationalize($term_meta->title);
		$title = isset($term_meta['title']) ? $this->psp_helper->internationalize($term_meta['title']) : "";
		$term_name = ucwords($this->psp_helper->internationalize($term_name));
		if (empty($title)) {			
			$title = $term_name;
		}
		$title = trim(stripcslashes($title));
		$term_description = $this->psp_helper->internationalize(term_description());
		
		$title_format = isset($current_tax_format['title']) ? $current_tax_format['title'] : "";
		$psp_term_title_format = isset($term_meta['titleformat']) ? $term_meta['titleformat'] : $title_format;
		$title_format = $psp_term_title_format;
		//$title_format = get_option($tax_title_format);		
		//$title_format = get_option('psp_taxonomy_title_format');	
		//error_log($title_format);

		if (!isset($title_format) || empty($title_format)) {
            $title_format = "%seo_title%";
			//$new_title = str_replace('%seo_title%', $title, $title_format); 
			//$new_title = isset($_GET['tag_ID'], $_GET['taxonomy']) ? $title_format : str_replace('%seo_title%', $title, $title_format);
			$new_title = !empty($term_id)  ? str_replace('%seo_title%', $title, $title_format) : $title_format ;
        } else {
			//$new_title = isset($_GET['tag_ID'], $_GET['taxonomy']) ? $title_format : str_replace('%seo_title%', $title, $title_format);
			$new_title = !empty($term_id)  ? str_replace('%seo_title%', $title, $title_format) : $title_format ;
			//$new_title = str_replace('%seo_title%', $title, $title_format); 
			$new_title = str_replace('%title%', $term_name, $new_title);
			$new_title = str_replace('%wp_title%', $term_name, $new_title);
			$new_title = str_replace('%term_name%', $term_name, $new_title);			
		//	$new_title = str_replace('%sep%', htmlentities($psp_settings['separator']), $new_title);
			$new_title = str_replace('%sep%', $psp_settings['separator'], $new_title);
			//$new_title = str_replace('%term_description%', $term_description, $new_title);
			$new_title = str_replace('%description%', $term_description, $new_title);
			//$new_title = str_replace('%blog_title%', $this->sitename, $new_title);
			//$new_title = str_replace('%blog_description%', $this->sitedescription, $new_title);
			$new_title = str_replace('%site_name%', $this->sitename, $new_title);
			$new_title = str_replace('%site_description%', $this->sitedescription, $new_title);
        }
		$title = $this->psp_helper->paged_title($new_title);
		//$title = $new_title;
		$this->taxonomy_title = $title;
		
		
		$sitename = $this->sitename;
		$sitedescription = $this->sitedescription;
		//$psp_title_separator = isset($psp_settings['separator']) ? htmlentities($psp_settings['separator']) : '';
		$psp_title_separator = isset($psp_settings['separator']) ? ($psp_settings['separator']) : '';		
		
        $title = trim($title);
        $title = trim($title, $psp_title_separator);
		return $title;
		
	} // end get_tax_psp_title_for_rest
	
	
}
?>