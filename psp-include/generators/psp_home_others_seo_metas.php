<?php

/*
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Description: Complete SEO solution for your Wordpress blog.
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspHomeOthersSeoMetas {

	//public $plugin_settings_name = "psp-pts-seo-metas";
	
	private static $obj_handle = null;		
	
	protected $index_tag = "index,follow";
	protected $noindex_tag = "noindex,follow";
	protected $noindex_nofollow_tag = "noindex,nofollow";
	protected $noodp_tag = "noodp";
	protected $noydir_tag = "noydir";	
	protected $noarchive_tag = "noarchive";
	protected $nosnippet_tag = "nosnippet";
	
	protected $sitename = "";
	protected $sitedescription = "";
	
	protected $psp_helper;
	protected $psp_home_settings = array();
	protected $psp_author_archive_settings = array();
	protected $psp_date_archive_settings = array();
	protected $psp_posttype_archive_settings = array();
	protected $psp_search_result_settings = array();
	protected $psp_sitewide_settungs = array();
	protected $psp_404_settings = array();
	
	public $home_description = "";
	public $home_keywords = "";
	public $home_title = "";
	public $home_can_link = "";
	public $archive_can_link = "";
	
	public $search_results_title = "";
	public $author_archive_title = "";
	public $date_archive_title = "";
	public $pt_archive_title = "";
	
	public $current_pageno = 1;	
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;
	//can be made private for singleton pattern
	public function __construct() {	

		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;
	
		//$this->psp_home_settings = get_option("psp_home_settings");
		//$this->psp_search_result_settings = get_option("psp_search_result_settings");
		//$this->psp_author_archive_settings = get_option("psp_author_archive_settings");
		//$this->psp_date_archive_settings = get_option("psp_date_archive_settings");
		//$this->psp_posttype_archive_settings = get_option("psp_posttype_archive_settings");
		$this->psp_sitewide_settungs = get_option("psp_sitewide_settings");
		$this->psp_404_settings = get_option("psp_404_page_settings");
	
		$this->sitename = $psp_helper_instance->get_sitename();
		$this->sitedescription = $psp_helper_instance->get_sitedescription();
		
	}	
	
	private function get_home_robots_meta() {

        $robots_meta = "";
        $robots_meta_string = "";
		$psp_home_settings = array();
		$psp_settings = array();
		
		if (!empty($this->psp_home_settings))	$psp_home_settings = $this->psp_home_settings;
		if (!empty($this->psp_sitewide_settungs))	$psp_settings = $this->psp_sitewide_settungs;

		if (isset($psp_settings['noindex_subpages']) && $psp_settings['noindex_subpages'] && ( get_query_var('paged') > 1 || $this->current_pageno > 1 ) ) {
				$robots_meta .= $this->noindex_tag;
		} else {
				$robots_meta .= $this->index_tag;
		}
		
		//$psp_noarchive = htmlspecialchars(stripcslashes($psp_home_settings['noarchive']));
		$psp_noarchive = isset($psp_home_settings['noarchive']) ? htmlspecialchars(stripcslashes($psp_home_settings['noarchive'])) : "";
				
		if ($psp_noarchive) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noarchive_tag;

		}
		
		//$psp_nosnippet = htmlspecialchars(stripcslashes($psp_home_settings['nosnippet']));
		$psp_nosnippet = isset($psp_home_settings['nosnippet']) ? htmlspecialchars(stripcslashes($psp_home_settings['nosnippet'])) : "";
		
		if ($psp_nosnippet) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->nosnippet_tag;

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
		if ($robots_meta != "" ) {
        
			$robots_meta_string .= '<meta name="robots" content="'.esc_attr($robots_meta).'" />';	
			$robots_meta_string .= "\r\n";

		}
		
		return $robots_meta_string;
	
	} // end get_home_robots_meta;			
	
	public function get_home_description_meta() {
	
		$desc_meta_string = "";
		$keyword_meta_string = "";
		$psp_home_settings = array();
		$psp_settings = array();
		
		if (!empty($this->psp_home_settings))	$psp_home_settings = $this->psp_home_settings;
		if (!empty($this->psp_sitewide_settungs))	$psp_settings = $this->psp_sitewide_settungs;
 		
		
		$description = isset($psp_home_settings['description']) ? trim(stripcslashes($this->psp_helper->internationalize($psp_home_settings['description']))) : ""; // home_description	
		
		if (empty ($description)) $description = trim(stripcslashes($this->psp_helper->internationalize(get_option('aiosp_description_format')))); // home_description

		if (!empty($description)) {			
			$description = str_replace('%site_name%', $this->sitename, $description);
			$description = str_replace('%site_description%', $this->sitedescription, $description);	
			$description = str_replace('%sep%', $psp_settings['separator'], $description);	
		}
		
		if (empty ($description)) $description = $this->sitedescription;

		if (!empty($description)) {		
		
			$this->home_description = stripcslashes($description);
			$desc_meta_string .= sprintf("<meta name=\"description\" content=\"%s\" />", esc_attr($description));
		
		}
		
		//check for use_meta_keywords
		if ( isset($psp_settings['use_meta_keywords']) && $psp_settings['use_meta_keywords'] ) {
		
			//if ( isset($psp_home_settings['keywords']) && $psp_home_settings['keywords'] ) {
			
				$keywords = isset($psp_home_settings['keywords']) ? trim($this->psp_helper->internationalize($psp_home_settings['keywords'])) : "";
				
			//} else {
				//$keywords = $this->get_all_keywords();
			//}		
			
			if (!empty($keywords)) {
				//$keywords = $this->psp_helper->internationalize($keywords);
				$this->home_keywords = $keywords;
				$keyword_meta_string .= sprintf("<meta name=\"keywords\" content=\"%s\" />", esc_attr($keywords));
			}
		}
		
		if (isset($desc_meta_string) && $desc_meta_string != "" && isset($keyword_meta_string) && $keyword_meta_string != "") {
				$desc_meta_string .= "\r\n";
				//$keyword_meta_string .= "\r\n";
		}
		
		return $desc_meta_string.$keyword_meta_string;
	
	} // end get_home_description_meta;	
	
	private function get_home_canonical_meta($canonical) {
	
		$canonical_meta_string = "";
		
		$home_link = get_option('home');
		$can_link = $this->psp_helper->paged_link($home_link);
		$can_link = trailingslashit($can_link);	

		$this->home_can_link = $can_link;
				
		if ($can_link != '' && ($canonical)) {
			//echo "".'<link rel="canonical" href="'.$can_link.'" />'."\r\n";
			$canonical_meta_string .= '<link rel="canonical" href="'.esc_url($can_link).'" />'."\r\n";
		}		
		
		return $canonical_meta_string;
        
	} // end get_home_canonical_meta;	
	
	private function get_search_robots_meta() {

        $robots_meta = "";
        $robots_meta_string = "";
		$psp_search_result_settings = array();
		$psp_settings = array();
		
		if (!empty($this->psp_search_result_settings))	$psp_search_result_settings = $this->psp_search_result_settings;
		//$psp_search_result_settings = get_option("psp_search_result_settings");
		if (!empty($this->psp_sitewide_settungs))	$psp_settings = $this->psp_sitewide_settungs;
		
		if (isset($psp_search_result_settings['robots']) && $psp_search_result_settings['robots']) {
			$robots_meta .= $this->noindex_tag;
		} else {
			if (isset($psp_settings['noindex_subpages']) && $psp_settings['noindex_subpages'] && ( get_query_var('paged') > 1 || $this->current_pageno > 1 )) {
			//if ($psp_settings['noindex_subpages'] && get_query_var('paged') > 1) {
				$robots_meta .= $this->noindex_tag;
			} else {
				$robots_meta .= $this->index_tag;
			}
		}

		$psp_noarchive = isset($psp_search_result_settings['noarchive']) ? htmlspecialchars(stripcslashes($psp_search_result_settings['noarchive'])) : "";
				
		if ($psp_noarchive) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noarchive_tag;

		}
		
		$psp_nosnippet = isset($psp_search_result_settings['nosnippet']) ? htmlspecialchars(stripcslashes($psp_search_result_settings['nosnippet'])) : "";
		
		if ($psp_nosnippet) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->nosnippet_tag;

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
		if ($robots_meta != "" ) {
        
			$robots_meta_string .= '<meta name="robots" content="'.esc_attr($robots_meta).'" />';	
			//$robots_meta_string .= "\n";

		}
		
		return $robots_meta_string;
	
	} // end get_search_robots_meta;
	
	private function get_search_canonical_meta($canonical) {
	
		$canonical_meta_string = "";		
		return $canonical_meta_string;
        
	} // end get_search_canonical_meta;
	
	private function get_author_archives_robots_meta() {

        $robots_meta = "";
        $robots_meta_string = "";
		$psp_author_archive_settings = array();
		$psp_settings = array();
		
		if (!empty($this->psp_author_archive_settings))	$psp_author_archive_settings = $this->psp_author_archive_settings;
		if (!empty($this->psp_sitewide_settungs))	$psp_settings = $this->psp_sitewide_settungs;
		
		if (isset($psp_author_archive_settings['robots']) && $psp_author_archive_settings['robots']) {
			$robots_meta .= $this->noindex_tag;
		} else {
			if (isset($psp_settings['noindex_subpages']) && $psp_settings['noindex_subpages'] && get_query_var('paged') > 1) {
			//if ($psp_settings['noindex_subpages'] && get_query_var('paged') > 1) {
					$robots_meta .= $this->noindex_tag;
			} else {
					$robots_meta .= $this->index_tag;
			}
		}	

		$psp_noarchive = isset($psp_author_archive_settings['noarchive']) ? htmlspecialchars(stripcslashes($psp_author_archive_settings['noarchive'])) : "";
				
		if ($psp_noarchive) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noarchive_tag;

		}
		
		$psp_nosnippet = isset($psp_author_archive_settings['nosnippet']) ? htmlspecialchars(stripcslashes($psp_author_archive_settings['nosnippet'])) : "";		
		
		if ($psp_nosnippet) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->nosnippet_tag;

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
		if ($robots_meta != "" ) {
        
			$robots_meta_string .= '<meta name="robots" content="'.esc_attr($robots_meta).'" />';	

		}
		
		return $robots_meta_string;
	
	} // end get_author_archives_robots_meta;	
	
	private function get_author_arch_canonical_meta($canonical) {
	
		$canonical_meta_string = "";
		$can_link = "";
		
		$author = get_userdata(get_query_var('author'));
	    if ($author === false) 
		{
			$can_link = "";
		} else {
			//$auth_link = get_author_link(false, $author->ID, $author->user_nicename);
			$auth_link = get_author_posts_url($author->ID, $author->user_nicename);
			$can_link = $this->psp_helper->paged_link($auth_link);
			//if (!is_paged()) {
			//	$can_link = $auth_link;
			//}
		}
		
		//$can_link = trailingslashit($can_link);			
		$this->archive_can_link = $can_link;		
		if ($can_link != '' && ($canonical)) {
			//echo "".'<link rel="canonical" href="'.$can_link.'" />'."\r\n";
			//$this->archive_can_link = $can_link;
			$canonical_meta_string .= '<link rel="canonical" href="'.esc_url($can_link).'" />'."\r\n";
		}		
		
		return $canonical_meta_string;
        
	} // end get_author_arch_canonical_meta;
	
	private function get_date_archives_robots_meta() {

        $robots_meta = "";
        $robots_meta_string = "";
		$psp_date_archive_settings = array();
		$psp_settings = array();
		
		if (!empty($this->psp_date_archive_settings))	$psp_date_archive_settings = $this->psp_date_archive_settings;
		if (!empty($this->psp_sitewide_settungs))	$psp_settings = $this->psp_sitewide_settungs;
		
		if (isset($psp_date_archive_settings['robots']) && $psp_date_archive_settings['robots']) {
			$robots_meta .= $this->noindex_tag;
		} else {
			
			if (isset($psp_settings['noindex_subpages']) && $psp_settings['noindex_subpages'] && ( get_query_var('paged') > 1 || $this->current_pageno > 1 )) {
			//if ($psp_settings['noindex_subpages'] && get_query_var('paged') > 1) {
					$robots_meta .= $this->noindex_tag;
			} else {
					$robots_meta .= $this->index_tag;
			}
		}		

		$psp_noarchive = isset($psp_date_archive_settings['noarchive']) ? htmlspecialchars(stripcslashes($psp_date_archive_settings['noarchive'])) : "";
				
		if ($psp_noarchive) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noarchive_tag;

		}
		
		$psp_nosnippet = isset($psp_date_archive_settings['nosnippet']) ? htmlspecialchars(stripcslashes($psp_date_archive_settings['nosnippet'])) : "";
		
		if ($psp_nosnippet) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->nosnippet_tag;

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
		if ($robots_meta != "" ) {
        
			$robots_meta_string .= '<meta name="robots" content="'.esc_attr($robots_meta).'" />';	

		}
		
		return $robots_meta_string;
	
	} // end get_date_archives_robots_meta;
	
	private function get_date_arch_canonical_meta($canonical) {
	
		$canonical_meta_string = "";
		$can_link = "";
		
		global $wp_query;
		
		if ($canonical) {
			if (get_query_var('m')) {
		        $m = preg_replace('/[^0-9]/', '', get_query_var('m'));
		        switch (strlen($m)) {
		            case 4: 
		                $can_link = get_year_link($m);
						$can_link = $this->psp_helper->paged_link($can_link);
		                break;
		            case 6: 
		                $can_link = get_month_link(substr($m, 0, 4), substr($m, 4, 2));
						$can_link = $this->psp_helper->paged_link($can_link);
		                break;
		            case 8: 
		                $can_link = get_day_link(substr($m, 0, 4), substr($m, 4, 2),
		                                     substr($m, 6, 2));
						$can_link = $this->psp_helper->paged_link($can_link);					 
		                break;
		            default:
		                $can_link = '';
		        }
		    }
			if ($wp_query->is_day) {
		        $can_link = get_day_link(get_query_var('year'),
		                             get_query_var('monthnum'),
		                             get_query_var('day'));
				$can_link = $this->psp_helper->paged_link($can_link);					 
		    } else if ($wp_query->is_month) {
		        $can_link = get_month_link(get_query_var('year'),
		                               get_query_var('monthnum'));
				$can_link = $this->psp_helper->paged_link($can_link);					   
		    } else if ($wp_query->is_year) {
		        $can_link = get_year_link(get_query_var('year'));
				$can_link = $this->psp_helper->paged_link($can_link);
		    }
		}
		
		//$can_link = trailingslashit($can_link);			
		$this->archive_can_link = $can_link;		
		if ($can_link != '' && ($canonical)) {
			//echo "".'<link rel="canonical" href="'.$can_link.'" />'."\r\n";
			$canonical_meta_string .= '<link rel="canonical" href="'.esc_url($can_link).'" />'."\r\n";
		}		
		
		return $canonical_meta_string;
        
	} // end get_date_arch_canonical_meta;
	
	private function get_pt_archives_robots_meta($post_type) {

        $robots_meta = "";
        $robots_meta_string = "";
		
		//$post_type_noindex_option = "psp_". $post_type. "_archives_noindex";
		if (!empty($this->psp_posttype_archive_settings))	$psp_pt_archive_settings = $this->psp_posttype_archive_settings;
		//$psp_pt_archive_settings = get_option("psp_posttype_archive_settings");
		if (!empty($this->psp_sitewide_settungs))	$psp_settings = $this->psp_sitewide_settungs;
		
		//need to do for individual custom post type		
		//if (get_option($psp_archive_noindex)) {
		//if (get_option($post_type_noindex_option)) {
		if (isset($psp_pt_archive_settings['robots']) && $psp_pt_archive_settings['robots']) {
			$robots_meta .= $this->noindex_tag;
		} else {

			if (isset($psp_settings['noindex_subpages']) && $psp_settings['noindex_subpages'] && ( get_query_var('paged') > 1 || $this->current_pageno > 1 )) {
					$robots_meta .= $this->noindex_tag;
			} else {
					$robots_meta .= $this->index_tag;
			}
		}
		
		$psp_noarchive = isset($psp_pt_archive_settings['noarchive']) ? htmlspecialchars(stripcslashes($psp_pt_archive_settings['noarchive'])) : "";
				
		if ($psp_noarchive) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noarchive_tag;

		}
		
		$psp_nosnippet = isset($psp_pt_archive_settings['nosnippet']) ? htmlspecialchars(stripcslashes($psp_pt_archive_settings['nosnippet'])) : "";
		
		if ($psp_nosnippet) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->nosnippet_tag;

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
		if ($robots_meta != "" ) {
        
			$robots_meta_string .= '<meta name="robots" content="'.esc_attr($robots_meta).'" />';	

		}
				
		return $robots_meta_string;
	
	} // end get_pt_archives_robots_meta;
	
	private function get_pt_arch_canonical_meta($post_type, $canonical) {
	
		$canonical_meta_string = "";
		$can_link = "";

		//$post_type = get_query_var( 'post_type' );
		//if ( is_array( $post_type ) ) $post_type = reset( $post_type );		
		
		$post_type_archive_link = get_post_type_archive_link( $post_type );
		
		if ( !$post_type_archive_link ) {
			return "";
		}
		$can_link = $this->psp_helper->paged_link($post_type_archive_link);
		$this->archive_can_link = $can_link;
		if ($can_link != '' && ($canonical)) {
			//echo "".'<link rel="canonical" href="'.$can_link.'" />'."\r\n";
			$canonical_meta_string .= '<link rel="canonical" href="'.esc_url($can_link).'" />'."\r\n";
		}
		
		return $canonical_meta_string;
        
	} // end get_pt_arch_canonical_meta;
	
	/***********
	private function paged_link($link) {
		$page = get_query_var('paged');
		$has_ut = function_exists('user_trailingslashit');
	    if ($page && $page > 1) {
	        $link = trailingslashit($link) ."page/". "$page";
	        if ($has_ut) {
	            $link = user_trailingslashit($link, 'paged');
	        } else {
	            $link .= '/';
	        }
		}
			return $link;
	} // end paged_link;
	*********/
	
	public function get_home_psp_title() {
	
		$title = "";
		
		if (empty($this->psp_home_settings)) $this->psp_home_settings = get_option("psp_home_settings");
		if (!empty($this->psp_home_settings))	$psp_home_settings = $this->psp_home_settings;
		if (!empty($this->psp_sitewide_settungs))	$psp_settings = $this->psp_sitewide_settungs;
		
		$title = isset($psp_home_settings['title']) ? $this->psp_helper->internationalize($psp_home_settings['title']) : "";
				
		if (empty($title)) {
			$title = $this->psp_helper->internationalize(get_option('aiosp_home_title'));
		}		
		
		if (!empty($title)) {
			$title = str_replace('%site_name%', $this->sitename, $title);
			$title = str_replace('%site_description%', $this->sitedescription, $title);	
			//$title = str_replace('%sep%', htmlentities($psp_settings['separator']), $title);
			$title = str_replace('%sep%', $psp_settings['separator'], $title);
			$title = trim(stripcslashes($title));
		}		
							
		if (empty($title)) {
			$title = $this->sitename;
		}
		//$title = trim(stripcslashes($title));
		$title = stripcslashes($this->psp_helper->paged_title($title));
		$this->home_title = $title;
		return $title;
		//$header = $this->replace_title($header, $title);
	} // end get_home_psp_title
	
	public function get_search_psp_title() {
	
		$title = "";
		$search = "";
		global $s;
		
		if (empty($this->psp_search_result_settings)) $this->psp_search_result_settings = get_option("psp_search_result_settings");
		
		if (is_search() && isset($s) && !empty($s)) {
			/***
			if (function_exists('attribute_escape')) {
				$search = attribute_escape($s);
			} else {
				$search = esc_attr($s);
			}
			***/
			$search = esc_attr($s);
			$search = str_replace('+', ' ', $search); 
			//$search = $this->capitalize($search);
			$search = ucwords($this->psp_helper->internationalize($search));
            $title_format = isset($this->psp_search_result_settings['title']) ? $this->psp_search_result_settings['title'] : "";
			if (empty($title_format)) {
				$title_format = "%search%";
				$title = str_replace('%search%', $search, $title_format);
			} else {
				//get_option('aiosp_search_title_format');
				//$title = str_replace('%blog_title%', $this->psp_helper->internationalize(get_bloginfo('name')), $title_format);
				//$title = str_replace('%blog_description%', $this->psp_helper->internationalize(get_bloginfo('description')), $title);
				//$title = str_replace('%blog_title%', $this->sitename, $title_format);
				//$title = str_replace('%blog_description%', $this->sitedescription, $title);
				$title = str_replace('%site_name%', $this->sitename, $title_format);
				$title = str_replace('%site_description%', $this->sitedescription, $title);
				$title = str_replace('%search%', $search, $title);
				$title = str_replace('%title%', $search, $title);				
				//$title = str_replace('%sep%', htmlentities($this->psp_sitewide_settungs['separator']), $title);
				$title = str_replace('%sep%', $this->psp_sitewide_settungs['separator'], $title);
				//$title = $this->paged_title($title);
			}
		}	
		$title = trim($title);
		$title = $this->psp_helper->paged_title($title);
		$this->search_results_title = stripcslashes($title);
		return $title;
		
	} // end get_search_psp_title
	
	public function get_404_psp_title() {
	
    	$title = "";	
    	
    	global $wpdb;
    	$psp_404_log = $wpdb->prefix . "psp_404_log";  
    	global $wp;
    	$id_404 = '';
    	$error_title = "404 Not Found";	
    	
    	$path_to_page = trim($wp->request); // /path/to/page
    	
    	$sql_404 = $wpdb->prepare("SELECT id, source_uri, source_url, total_hits, referrer, status  FROM $psp_404_log a WHERE a.source_uri = %s", $path_to_page );
    	
    	if (!empty($path_to_page) && ($wpdb->get_var("show tables like '$psp_404_log'") == $psp_404_log)) {
    		$id_404 = $wpdb->get_row($sql_404, OBJECT);
    	}
    	
    	if (!empty($id_404) && trim($id_404->status) == "410") {
    	
    		$error_title = '410 Gone';
    	}
    	
    	
    	if ( is_404() ) {
    		
    		$title_format = isset($this->psp_404_settings['title']) ? $this->psp_404_settings['title'] : "";
    		if (empty($title_format)) {
    			$title_format = "%title_404%";
    			$title = str_replace('%title_404%', $error_title, $title_format);
    		} else {
    			//get_option('aiosp_search_title_format');
    			//$title = str_replace('%blog_title%', $this->psp_helper->internationalize(get_bloginfo('name')), $title_format);
    			//$title = str_replace('%blog_description%', $this->psp_helper->internationalize(get_bloginfo('description')), $title);
    			//$title = str_replace('%blog_title%', $this->sitename, $title_format);
    			//$title = str_replace('%blog_description%', $this->sitedescription, $title);
    			$title = str_replace('%title_404%', $error_title, $title_format);
    			$title = str_replace('%title%', $error_title, $title);
    			$title = str_replace('%seo_title%', $error_title, $title);
    			$title = str_replace('%site_name%', $this->sitename, $title);	
    			//$title = str_replace('%sep%', htmlentities($this->psp_sitewide_settungs['separator']), $title);
    			$title = str_replace('%sep%', $this->psp_sitewide_settungs['separator'], $title);
    			//$title = $this->paged_title($title);
    		}
    	}	
    	$title = trim($title);		
    	return $title;
    	
    } // end get_404_psp_title
	
	public function get_date_archive_psp_title() {
	
	    global $wp_locale;
	
		$title = "";
		//$sep = isset($this->psp_sitewide_settungs['separator']) ? htmlentities($this->psp_sitewide_settungs['separator']) : "";
		$sep = isset($this->psp_sitewide_settungs['separator']) ? $this->psp_sitewide_settungs['separator'] : "";
		
		if (empty($this->psp_date_archive_settings)) $this->psp_date_archive_settings = get_option("psp_date_archive_settings");
		
		//$date = $this->psp_helper->internationalize(wp_title('', false));
		
		/**************get title************/
		$m = get_query_var('m');
		$year = get_query_var('year');
		$monthnum = get_query_var('monthnum');
		$day = get_query_var('day');		

		$t_sep = '%WP_TITILE_SEP%'; // Temporary separator, for accurate flipping, if necessary

		// If there's a month
		if ( is_archive() && !empty($m) ) {
			$psp_year = substr($m, 0, 4);
			$psp_month = $wp_locale->get_month(substr($m, 4, 2));
			$psp_day = intval(substr($m, 6, 2));
			$datetitle = $psp_year . ( $psp_month ? $t_sep . $psp_month : '' ) . ( $psp_day ? $t_sep . $psp_day : '' );
		}

		// If there's a year
		if ( is_archive() && !empty($year) ) {
			$datetitle = $year;
			if ( !empty($monthnum) )	$datetitle .= $t_sep . $wp_locale->get_month($monthnum);
			if ( !empty($day) )			$datetitle .= $t_sep . zeroise($day, 2);
		}

		$title_array = explode( $t_sep, $datetitle );
		$title_array = array_reverse( $title_array );
		$datetitle = implode( " $sep ", $title_array );
		
		$date = $this->psp_helper->internationalize($datetitle);
		/***************/
		$title_format = $this->psp_date_archive_settings['title'];
        //$title_format = get_option('aiosp_archive_title_format');
		
		if (empty($title_format)) {
			$title_format = "%title_date%";
			$title = str_replace('%title_date%', $date, $title_format);
		} else {		
			$new_title = str_replace('%title_date%', $date, $title_format);
			$new_title = str_replace('%title%', $date, $new_title);
			$new_title = str_replace('%seo_title%', $date, $new_title);
			//$new_title = str_replace('%blog_title%', $this->sitename, $new_title);
			//$new_title = str_replace('%blog_description%', $this->sitedescription, $new_title);
			$new_title = str_replace('%site_name%', $this->sitename, $new_title);
			$new_title = str_replace('%site_description%', $this->sitedescription, $new_title);
			$new_title = str_replace('%sep%', $sep, $new_title);
        }
		$title = trim($new_title);
        $title = $this->psp_helper->paged_title($title);	
		$this->date_archive_title = $title;		
		return $title;
		
	} // end get_date_archive_psp_title
	
	public function get_pt_archive_psp_title($post_type = '') {
	
		$title = "";
		
		if(! $post_type) {
		
			$title = post_type_archive_title('', false);	
		} else {
			$post_type_obj = get_post_type_object( $post_type );
			$title = apply_filters( 'post_type_archive_title', $post_type_obj->labels->name, $post_type );
		}

		if (empty($this->psp_posttype_archive_settings)) $this->psp_posttype_archive_settings = get_option("psp_posttype_archive_settings");
		
		$title_format = isset($this->psp_posttype_archive_settings['title']) ? $this->psp_posttype_archive_settings['title'] : "";
		
        //$title_format = get_option('psp_pt_archive_title_format');
		//$title_format = get_option($post_type_archive_title_format);
		
        //$new_title = str_replace('%blog_title%', $this->psp_helper->internationalize(get_bloginfo('name')), $title_format);
        //$new_title = str_replace('%blog_description%', $this->psp_helper->internationalize(get_bloginfo('description')), $new_title);
		
		if (empty($title_format)) {
			$title_format = "%title%";
			$new_title = str_replace('%title%', $title, $title_format);
		} else {		
			$new_title = str_replace('%title%', $title, $title_format);
			$new_title = str_replace('%seo_title%', $title, $new_title);
			//$new_title = str_replace('%blog_title%', $this->sitename, $new_title);
			//$new_title = str_replace('%blog_description%', $this->sitedescription, $new_title);
			$new_title = str_replace('%site_name%', $this->sitename, $new_title);
			$new_title = str_replace('%site_description%', $this->sitedescription, $new_title);
			//$new_title = str_replace('%sep%', htmlentities($this->psp_sitewide_settungs['separator']), $new_title);
			$new_title = str_replace('%sep%', $this->psp_sitewide_settungs['separator'], $new_title);
        }
		$title = trim($new_title);
        $title = $this->psp_helper->paged_title($title);
		$this->pt_archive_title = $title;	
		
		return $title;
		
	} // end get_pt_archive_psp_title
	
	public function get_author_archive_psp_title( $user_id = 0 ) {
	
		$title = "";
		$author = "";
		
		if(empty($this->psp_author_archive_settings)) $this->psp_author_archive_settings = get_option("psp_author_archive_settings");
		
		if (!$user_id) {
			
			$author_obj = get_userdata( get_query_var('author') );
			
		} else {
			$author_obj = get_userdata($user_id);
		}
		
		if ($author_obj) $author = $author_obj->display_name;		
		//$author = $this->psp_helper->internationalize( $author_obj->display_name );
		
	    //$title_format = get_option('psp_archive_title_format');
		$title_format = isset($this->psp_author_archive_settings['title']) ? $this->psp_author_archive_settings['title'] : "";
		
		
		if (empty($title_format)) {
			$title_format = "%title_author%";
			$new_title = str_replace('%title_author%', $author, $title_format);
		} else {
			$new_title = str_replace( '%title_author%', $author, $title_format );
			$new_title = str_replace( '%author%', $author, $new_title );	
            $new_title = str_replace( '%title%', $author, $new_title );	
			$new_title = str_replace( '%seo_title%', $author, $new_title );	
			//$new_title = str_replace('%blog_title%', $this->psp_helper->internationalize(get_bloginfo('name')), $new_title);
			//$new_title = str_replace('%blog_description%', $this->psp_helper->internationalize(get_bloginfo('description')), $new_title); 
			//$new_title = str_replace('%blog_title%', $this->sitename, $new_title);
			//$new_title = str_replace('%blog_description%', $this->sitedescription, $new_title);
			$new_title = str_replace('%site_name%', $this->sitename, $new_title);
			$new_title = str_replace('%site_description%', $this->sitedescription, $new_title);
			//$new_title = str_replace('%sep%', htmlentities($this->psp_sitewide_settungs['separator']), $new_title);
			$new_title = str_replace('%sep%', $this->psp_sitewide_settungs['separator'], $new_title);
		}
		$title = trim($new_title);
        $title = $this->psp_helper->paged_title($title);
		$this->author_archive_title = $title;	
		
		return $title;
		
	} // end get_author_archive_psp_title
	
	public function get_home_seo_metas($canonical) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";	
		$prevnext_meta_string = "";
		$json_schema_string = "";
		$jsonld_script_tag = "";
		
		//To handle exta metas,if any, defined in settings.
		$home_meta = "";	

		$this->psp_home_settings = get_option("psp_home_settings");
		$psp_settings = get_option("psp_sitewide_settings");
		
		$robots_meta_string = $this->get_home_robots_meta();
		if (!is_paged()) {
			$desc_keyword_meta_string = $this->get_home_description_meta();
		}
		if (!empty($desc_keyword_meta_string)) $desc_keyword_meta_string .= "\r\n";
		
		if ($canonical) {
			$canonical_meta_string = $this->get_home_canonical_meta($canonical);
		}
		//$home_meta = stripcslashes(get_option('aiosp_home_meta_tags', $home_meta));	
		$home_meta = (isset($this->psp_home_settings['headers']) && !empty($this->psp_home_settings['headers'])) ? html_entity_decode(stripcslashes(esc_html($this->psp_home_settings['headers']))) : '';
		//validate headers
		if( !empty( $home_meta ) ) {
    	
    		$allowed_html = array(
    			'meta' => array(
    				'name' => array(),
    				'property' => array(),
    				'itemprop' => array(),
    				'content' => array(),
    			),    
    		);
    
    		$home_meta = wp_kses($home_meta, $allowed_html);
		}
		if (!empty($home_meta)) $home_meta .= "\r\n";
		
		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		if (isset($this->psp_home_settings['schema']) && !empty($this->psp_home_settings['schema'])) {
			$json_schema_string = $this->psp_home_settings['schema'];
		    $json_schema_string = html_entity_decode(esc_html(stripcslashes($json_schema_string)));
		    //validate it is a json object
			$schema_obj = json_decode($json_schema_string);
			if($schema_obj === null) {
			    $json_schema_string = "";
			}
		}
		if (!empty($json_schema_string) && !empty($psp_settings['enable_schema']) ) {
			$jsonld_script_tag = '<scri' . 'pt type="application/ld+json">'. "\r\n". $json_schema_string . "\r\n". '</scri' . 'pt>';
		}
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$home_meta.$prevnext_meta_string;
		if (!empty($jsonld_script_tag) && !is_paged() ) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		
		return $seo_meta_string;
	
	} // end get_home_seo_metas;

	public function psp_get_home_schema() {

		$jsonld_script_tag = "";	

		if (isset($this->psp_home_settings['schema']) && !empty($this->psp_home_settings['schema'])) {
			$json_schema_string = $this->psp_home_settings['schema'];
			$json_schema_string = html_entity_decode(esc_html(stripcslashes($json_schema_string)));
			//validate it is a json object
			$schema_obj = json_decode($json_schema_string);
			if($schema_obj === null) {
				$json_schema_string = "";
			}
		}
		if (!empty($json_schema_string)) {
			$jsonld_script_tag = '<scri' . 'pt type="application/ld+json">'. "\r\n". $json_schema_string . "\r\n". '</scri' . 'pt>';
		}

		if (!empty($jsonld_script_tag) && !is_paged() ) {

			return $jsonld_script_tag;
			
		} else {
			return "";
		}
		
	} // end psp_get_home_schema;
	
	public function get_search_seo_metas($canonical) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";		
		$prevnext_meta_string = "";	
		
		$this->psp_search_result_settings = get_option("psp_search_result_settings");
		
		if ($desc_keyword_meta_string) $desc_keyword_meta_string .= "\r\n";
		
		$robots_meta_string = $this->get_search_robots_meta();
		
		if ($robots_meta_string) $robots_meta_string .= "\r\n";
		
		if ($canonical) {
			$canonical_meta_string = $this->get_search_canonical_meta($canonical);
		}		

		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$prevnext_meta_string;
		
		return $seo_meta_string;
	
	} // end get_search_seo_metas;
	
	public function get_404_seo_metas() {
	
		$robots_meta = "";
		$robots_meta_string = "";		
		$seo_meta_string = "";		
		
		if (isset($this->psp_404_settings['robots']) && $this->psp_404_settings['robots']) {
			$robots_meta .= $this->noindex_tag;
		} else {
			$robots_meta .= $this->index_tag;
		}	
		$robots_meta_string .= '<meta name="robots" content="'.esc_attr($robots_meta).'" />';	
		$seo_meta_string =  $robots_meta_string."\r\n";
		
		return $seo_meta_string;
	
	} // end get_404_seo_metas;
	
	public function get_author_seo_metas($canonical) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";		
		$prevnext_meta_string = "";	
		
		$this->psp_author_archive_settings = get_option("psp_author_archive_settings");
		
		if ($desc_keyword_meta_string) $desc_keyword_meta_string .= "\r\n";
		
		$robots_meta_string = $this->get_author_archives_robots_meta();
		
		if ($robots_meta_string) $robots_meta_string .= "\r\n";
		
		if ($canonical) {
			$canonical_meta_string = $this->get_author_arch_canonical_meta($canonical);
		}	
		
		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$prevnext_meta_string;
		
		return $seo_meta_string;
	
	} // end get_author_seo_metas;
	
	public function get_date_archives_seo_metas($canonical) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";		
		$prevnext_meta_string = "";	

		$this->psp_date_archive_settings = get_option("psp_date_archive_settings");
		
		if ($desc_keyword_meta_string) $desc_keyword_meta_string .= "\r\n";
		
		$robots_meta_string = $this->get_date_archives_robots_meta();
		
		if ($robots_meta_string) $robots_meta_string .= "\r\n";
		
		if ($canonical) {
			$canonical_meta_string = $this->get_date_arch_canonical_meta($canonical);
		}	
		
		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$prevnext_meta_string;
		
		return $seo_meta_string;
	
	} // end get_date_archives_seo_metas;
	
	public function get_pt_archives_seo_metas($canonical, $post_type = null) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$seo_meta_string = "";		
		$prevnext_meta_string = "";		
		
		if ( $post_type === null ) {
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) $post_type = reset( $post_type );	
		}
		//$curr_post_type_obj = get_post_type_object( $post_type );		
		//$curr_post_type_obj_title = $post_type_obj->labels->name;
		//if ( ! $curr_post_type_obj->has_archive ) $curr_post_type_obj_title = post_type_archive_title( '', false );	

		$this->psp_posttype_archive_settings = get_option("psp_posttype_archive_settings");
		
		if ($desc_keyword_meta_string) $desc_keyword_meta_string .= "\r\n";
		
		$robots_meta_string = $this->get_pt_archives_robots_meta($post_type);
		
		if ($robots_meta_string) $robots_meta_string .= "\r\n";
		
		if ($canonical) {
			$canonical_meta_string = $this->get_pt_arch_canonical_meta($post_type, $canonical);
		}	
		
		$prevnext_meta_string = $this->psp_helper->get_prev_next_links();
		
		$seo_meta_string =  $desc_keyword_meta_string.$robots_meta_string.$canonical_meta_string.$prevnext_meta_string;
		
		return $seo_meta_string;
	
	} // end get_pt_archives_seo_metas;
	
}
?>