<?php

/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Description: Complete SEO solution for your Wordpress blog.
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspPtsSeoMetas {

	//public $plugin_settings_name = "psp-pts-seo-metas";
	
	private static $obj_handle = null;		
	
	protected static $cust_posttypes = array();
	
	protected $index_tag = "index,follow";
	protected $noindex_tag = "noindex";
	protected $noindex_nofollow_tag = "noindex,nofollow";
	protected $noodp_tag = "noodp";
	protected $noydir_tag = "noydir";	
	protected $noarchive_tag = "noarchive";
	protected $nosnippet_tag = "nosnippet";
	protected $noimageindex = "noimageindex";	
	
	protected $psp_current_ptype_format = array();
	protected $psp_sitewide_settings = array();	
	protected $sitename = "";
	protected $sitedescription = "";
	//protected $wp_post_meta_data_arr = array();
	
	protected $psp_helper;
	
	public $preferred_taxonomy_for_bc = "";
	public $default_taxonomy_for_bc = "";
	
	public $post_type_name = "";
	public $post_type_title = "";
	public $post_type_description = "";
	public $post_type_keywords = "";
	public $post_type_can_link = "";
	
	public $psp_current_ptype_meta = array();
	public $psp_current_ptype_social_meta = array();
	
	public $current_pageno = 1;
	
	private $default_post_types = array ('post', 'page', 'attachment', 'nav_menu_item', 'revision');
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;
	
	//compare function (numeric)
	public static function pspcmp($a, $b) 
    {
		// return strcmp($a->name, $b->name);
		if ($a->term_id == $b->term_id) {
			return 0;
		}
		return ($a->term_id < $b->term_id) ? -1 : 1;
    }
	
	//can be made private for singleton pattern
	public function __construct() {

		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;
		
		$this->sitename = $psp_helper_instance->get_sitename();
		$this->sitedescription = $psp_helper_instance->get_sitedescription();
		$this->psp_sitewide_settings = get_option('psp_sitewide_settings');		
	}

	public function get_cust_posttypes() {		
	
		return self::$cust_posttypes;
	
	} // end get_cust_taxonomies;		
	
	private function get_pt_robots_meta($post, $psp_post_meta, $paged, $cpage) {

        $robots_meta = "";
        $robots_meta_string = "";	

		$psp_settings = $this->psp_sitewide_settings;
		
		if(empty($cpage)) {
			$cpage = 0;
		}

		$psp_robots_meta = !empty($psp_post_meta['robots']) ? htmlspecialchars(stripcslashes($psp_post_meta['robots'])) : '';
		
		$psp_noindex_meta = !empty($psp_post_meta['noindex']) ? htmlspecialchars(stripcslashes($psp_post_meta['noindex'])) : '';
		
		$psp_nofollow_meta = !empty($psp_post_meta['nofollow']) ? htmlspecialchars(stripcslashes($psp_post_meta['nofollow'])) : '';
		
		//if (empty($psp_robots_meta)) {
		if (!empty($psp_noindex_meta) || !empty($psp_nofollow_meta)) {
    		if (isset($psp_post_meta['noindex']) && empty($psp_noindex_meta) && empty($psp_nofollow_meta)) {
    			$psp_robots_meta = 'index,follow';
    		}
    		
    		if (!empty($psp_noindex_meta)) {
    			$psp_robots_meta = 'noindex,follow';
    		}
    		
    		if (!empty($psp_nofollow_meta)) {
    			$psp_robots_meta = 'index,nofollow';
    		}
    		
    		if (!empty($psp_noindex_meta) && !empty($psp_nofollow_meta)) {
    			$psp_robots_meta = 'noindex,nofollow';
    		}
		}
		
		if (empty($psp_robots_meta)) {
			$psp_robots_meta = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'robotsmeta', true)));	
		}
		
		if (empty($psp_robots_meta)) {
		    $yoast_noindex_meta = htmlspecialchars(stripcslashes(get_post_meta($post->ID, '_yoast_wpseo_meta-robots-noindex', true)));
			$yoast_nofollow_meta = htmlspecialchars(stripcslashes(get_post_meta($post->ID, '_yoast_wpseo_meta-robots-nofollow', true)));
			
			//if (!empty($yoast_noindex_meta)) $psp_noindex_meta = $yoast_noindex_meta;
			if (!empty($yoast_noindex_meta) && $yoast_noindex_meta == 1) {
				$psp_noindex_meta = $yoast_noindex_meta;
			}
			if (!empty($yoast_nofollow_meta)) {
				$psp_nofollow_meta = $yoast_nofollow_meta;
			}
			//build psp meta robots
			//if (empty($psp_noindex_meta) && empty($psp_nofollow_meta)) {
    			//$psp_robots_meta = 'index,follow';
    		//}
    		
    		if (!empty($psp_noindex_meta)) {
    			$psp_robots_meta = 'noindex,follow';
    		}
    		
    		if (!empty($psp_nofollow_meta)) {
    			$psp_robots_meta = 'index,nofollow';
    		}
    		
    		if (!empty($psp_noindex_meta) && !empty($psp_nofollow_meta)) {
    			$psp_robots_meta = 'noindex,nofollow';
    		}
		}
		
		//if (empty($psp_post_meta) && empty($psp_robots_meta)) {
	    /**		
		if (empty($psp_robots_meta)) {
			$psp_robots_meta = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'robotsmeta', true)));	
		}
         **/
		//$post_type_format = $this->current_ptype_format;
		if (!empty($this->psp_current_ptype_format)) $post_type_format = $this->psp_current_ptype_format;
		
		//is this a paginated post?
		//$paged = $this->is_this_paged($post);

		if (isset($psp_robots_meta)  &&  !empty($psp_robots_meta)) {				
			//if ( get_option('psp_comnts_pages_noindex') && get_option('page_comments') && $cpage >= 1) {	    
			if ( (isset($psp_settings['noindex_pt_comment_pages']) && $psp_settings['noindex_pt_comment_pages'] && get_option('page_comments') && $cpage >= 1) || (isset($psp_settings['noindex_pt_paginations']) && $psp_settings['noindex_pt_paginations'] && $paged && $paged>1) ) {
					$robots_meta .= $this->noindex_tag;
			} else {
					$robots_meta = $psp_robots_meta;
			}

		} else {                
	
			//if ( get_option('psp_comnts_pages_noindex') && get_option('page_comments') && $cpage >= 1) {
			if ( (isset($psp_settings['noindex_pt_comment_pages']) && $psp_settings['noindex_pt_comment_pages'] && get_option('page_comments') && $cpage >= 1) || (isset($psp_settings['noindex_pt_paginations']) && $psp_settings['noindex_pt_paginations'] && $paged && $paged>1)) {
					$robots_meta .= $this->noindex_tag;
			} else {
			    
				if (isset($post_type_format['robots']) && $post_type_format['robots']) {
					$robots_meta .= $this->noindex_tag;
				} else {
					$robots_meta .= $this->index_tag;
				}
			
			}

		}
		
		$psp_noimageindex = !empty($psp_post_meta['noimageindex']) ? htmlspecialchars(stripcslashes($psp_post_meta['noimageindex'])) : '';
		$psp_imagepreview = !empty($psp_post_meta['maximage']) ? htmlspecialchars(stripcslashes($psp_post_meta['maximage'])) : '';
		$psp_videopreview = !empty($psp_post_meta['maxvideo']) ? htmlspecialchars(stripcslashes($psp_post_meta['maxvideo'])) : '';
	
		if ($psp_noimageindex) {
			if ($robots_meta != "") {
				$robots_meta .= ",";
			}

			$robots_meta .= $this->noimageindex;

		}
		
		$psp_noarchive = !empty($psp_post_meta['noarchive']) ? htmlspecialchars(stripcslashes($psp_post_meta['noarchive'])) : '';
		if (empty($psp_post_meta)) {
			$psp_noarchive = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'psp_noarchive', true)));
		}
		
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
		$psp_nosnippet = !empty($psp_post_meta['nosnippet']) ? htmlspecialchars(stripcslashes($psp_post_meta['nosnippet'])) : '';
		$psp_maxsnippet = !empty($psp_post_meta['maxsnippet']) ? htmlspecialchars(stripcslashes($psp_post_meta['maxsnippet'])) : '';
		
		if (empty($psp_post_meta)) {

			$psp_nosnippet = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'psp_nosnippet', true)));
		 
		 }
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
		
		if ($robots_meta != "" ) {
        
			$robots_meta_string .= '<meta name="robots" content="'.esc_attr($robots_meta).'" />';	

		}
		
		return $robots_meta_string;
	
	} // end get_pt_robots_meta;	

	private function get_unique_keywords($keywords) {
	
		$uni_keywords = array();
		foreach ($keywords as $word) {
		    $uni_keywords[] = $word;
			/*if (function_exists('mb_strtolower')) {
				if (mb_detect_encoding($word) == 'UTF8') {
					$small_keywords[] = mb_strtolower($word, 'UTF8');	
				} else {
					$small_keywords[] = strtolower($word);					
				}
			} else {
				$small_keywords[] = strtolower($word);
			}*/
		}
		$keywords_ar = array_unique($uni_keywords);
		return implode(',', $keywords_ar);
	}

	/**
	 * @return comma-separated list of unique keywords
	 */
	private function get_all_keywords($post, $psp_post_meta) {		

		if (is_404()) {
			return null;
		}		

	    $keywords = array();
	    	        
	    if ($post) {

			// custom field keywords
			$keywords_a = $keywords_i = null;
			
			$keywords_i = !empty($psp_post_meta['keywords']) ? htmlspecialchars(stripcslashes($psp_post_meta['keywords'])) : '';
		
			if (empty($psp_post_meta)) {
	        
				$keywords_i = stripcslashes($this->psp_helper->internationalize(get_post_meta($post->ID, "keywords", true)));
			}
			//get other SEO plugins keywords here
	        $keywords_i = str_replace('"', '', $keywords_i);
			
	        if (isset($keywords_i) && !empty($keywords_i)) {
	           	$traverse = explode(',', $keywords_i);
	           	foreach ($traverse as $keyword) {
	           		$keywords[] = $keyword;
				}
	        }
			/**************use tags and use categories in meta keywords commented**********************
	        if (get_option('psp_use_tags') && !is_page()) {
			
				// WP 2.3 tags
		        if (function_exists('get_the_tags')) {
		           	$tags = get_the_tags($post->ID);
		           	if ($tags && is_array($tags)) {
		               	foreach ($tags as $tag) {
							$keywords[] = $this->psp_helper->internationalize($tag->name);
						}
		            }
		        }		        
			}
			
	        if (get_option('aiosp_use_categories') && !is_page()) {
		        $categories = get_the_category($post->ID);
		        foreach ($categories as $category) {
		           	$keywords[] = $this->psp_helper->internationalize($category->cat_name);
		        }
	        }
			******************use tags and use categories in meta keywords commented******/
	    }	    
		//$keywords_ar = array_unique($keywords);
		//return implode(',', $keywords_ar);
	    return $this->get_unique_keywords($keywords);
	}	
	
	private function get_pt_description_meta($post, $psp_post_meta) {
	
		$desc_meta = "";
		$desc_meta_string = "";
		$keyword_meta_string = ""; 
		
		$psp_settings = $this->psp_sitewide_settings;	
		$this->post_type_name = $post->post_type;
		/*** commented in V1.2.6
		if (is_page()) {
			if ($this->is_static_front_page($post)){						
				//$home_desc_keyword_meta_string = get_home_description_meta();
				//return $home_desc_keyword_meta_string;
				//$psp_home_settings = get_option('psp_home_settings');
				//$title = $this->psp_helper->internationalize($psp_home_settings['title']);
				$psp_ho_instance = PspHomeOthersSeoMetas::get_instance();
				$home_desc_keyword_meta_string = $psp_ho_instance->get_home_description_meta();
				return $home_desc_keyword_meta_string;
			}
		}
		***/
		//$ptype_name = get_post_type($post);
		//$post_type_desc_format = "psp_".$ptype_name."_desc_format";	
		if (!empty($this->psp_current_ptype_format)) $post_type_format = $this->psp_current_ptype_format;
		
		$description = !empty($psp_post_meta['description']) ? trim(stripcslashes($this->psp_helper->internationalize($psp_post_meta['description']))) : '';
		$psp_nogendescription = !empty($psp_post_meta['disable_description']) ? htmlspecialchars(stripcslashes($psp_post_meta['disable_description'])) : '';
		
		//if ($psp_nogendescription) return "";
		
		
        if (empty($description) && empty($psp_post_meta['useolddata'])) {
			$description = trim(stripcslashes($this->psp_helper->internationalize(get_post_meta($post->ID, "description", true))));// post_description
			
			if(empty($description)) {
				$description = trim(stripcslashes($this->psp_helper->internationalize(get_post_meta($post->ID, "_yoast_wpseo_metadesc", true))));// yoast_description
				if(!empty($description)) {
					//trim yoast description of tags
					$description = preg_replace('/%%[^%]+%%/', '', $description);
				}
			}
			
			$psp_nogendescription  = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'psp_nogendescription', true)));
		}
		//Get another SEO plugin's description here
		if (empty ($psp_nogendescription)) $psp_nogendescription = false; 
		
		if (!$description) {
		
			//$description = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($post->post_excerpt));
			
			if (!$description && !$psp_nogendescription  && isset($psp_settings['autogenerate_description']) && $psp_settings['autogenerate_description']) {
				$description = $this->psp_helper->trim_excerpt_without_filters($this->psp_helper->internationalize($post->post_content));
			}
		}
		
		// $description = $post->title. $description;
		// "internal whitespace trim"
		$description = preg_replace("/\s\s+/", " ", $description);
		
		$description = trim(strip_tags($description));
		//$description = str_replace('"', '', $description); // not using addslashes

		// replace newlines on mac / windows?
		$description = str_replace("\r\n", ' ', $description);

		// maybe linux uses this alone
		$description = str_replace("\n", ' ', $description);			
				
		if (empty($desc_meta)) {
			if (isset($description) && $description !== "") {
				$desc_meta = $description;
			}
		} else {				
			//do nothing
		} 

		if (!empty($desc_meta)) {	
		
			// description format
			//$description_format = get_option($post_type_desc_format);
			$description_format = isset($post_type_format['description']) ? $post_type_format['description'] : '';
			$psp_desc_format = isset($psp_post_meta['descformat']) ? $psp_post_meta['descformat'] : $description_format;
			$description_format = $psp_desc_format;
			
			//description format disable check for this post/page.
			$psp_nodescformat = !empty($psp_post_meta['disable_desc_format']) ? htmlspecialchars(stripcslashes($psp_post_meta['disable_desc_format'])) : '';			
			if ($psp_nodescformat) $description_format = "";
			
            if (!isset($description_format) || empty($description_format)) {
            	$description_format = "%description%";
				$description = str_replace('%description%', $desc_meta, $description_format); 
            } else {
				$original_wp_title = $this->psp_helper->internationalize($post->post_title);
				/************commented**********
				$description = str_replace('%description%', $desc_meta, $description_format);            
				$description = str_replace('%blog_title%', get_bloginfo('name'), $description);
				$description = str_replace('%blog_description%', get_bloginfo('description'), $description);				
				//$original_wp_title = $this->psp_helper->internationalize(wp_title('', false));				
				$description = str_replace('%wp_title%', $original_wp_title, $description);
				
				
				$post_title_desc = $this->psp_helper->internationalize(get_post_meta($post->ID, "title", true));
				if (!$post_title_desc) {
					$post_title_desc = $this->psp_helper->internationalize(get_post_meta($post->ID, "title_tag", true));
					if (!$post_title_desc) {
						$post_title_desc = $original_wp_title;
						//$post_title_desc = str_replace(']]>', ']]&gt;', $post_title_desc);
					}
				}
				
				$post_title_desc = htmlspecialchars(stripcslashes($post_title_desc), ENT_QUOTES);
				
				$description = str_replace('%post_title%', trim($post_title_desc), $description);
				**********************/
				$sitename = $this->sitename;
				$sitedescription = $this->sitedescription;
				//$psp_title_separator = isset($this->psp_sitewide_settings['separator']) ? htmlentities($this->psp_sitewide_settings['separator']) : '';
				$psp_title_separator = isset($this->psp_sitewide_settings['separator']) ? $this->psp_sitewide_settings['separator'] : '';
				$psp_seo_title = "";
				if (!empty($this->post_type_title)) $psp_seo_title = $this->post_type_title;
				
				$search_format   = array('%seo_description%', '%blog_title%', '%blog_description%', '%site_name%', '%site_description%', '%wp_title%', '%seo_title%', '%sep%');
				$replaced_format = array($desc_meta, $sitename, $sitedescription, $sitename, $sitedescription, $original_wp_title, $psp_seo_title, $psp_title_separator);
				$description = str_replace($search_format, $replaced_format, $description_format);				
			}
			$this->post_type_description = $description;
			$desc_meta_string .= sprintf("<meta name=\"description\" content=\"%s\" />", stripcslashes(esc_attr($description)));
			//descripyion is disabled for this page
			if (!empty($psp_post_meta['disable_description']) && $psp_post_meta['disable_description']) $desc_meta_string = "";
		}
		
		//check for use_meta_keywords
		$use_meta_keywords = isset($psp_settings['use_meta_keywords']) ? $psp_settings['use_meta_keywords'] : '';
		$disable_keywords_for_post = !empty($psp_post_meta['disable_keywords']) ? $psp_post_meta['disable_keywords'] : '';
		if ( $use_meta_keywords && !$disable_keywords_for_post ) {
			//Fetch keywords and Form meta keyword string
			$keywords = $this->get_all_keywords($post, $psp_post_meta);
			
			//if ($keywords != "" || $keywords != null) {
			if (!empty($keywords)) {
				$keywords = $this->psp_helper->internationalize($keywords);
				$this->post_type_keywords = $keywords;
				$keyword_meta_string .= sprintf("<meta name=\"keywords\" content=\"%s\" />", stripcslashes(esc_attr($keywords)));
			}
		}
		
		if (isset($desc_meta_string) && $desc_meta_string != "" && isset($keyword_meta_string) && $keyword_meta_string != "") {
				$desc_meta_string .= "\r\n";
		}
		
		
		return $desc_meta_string.$keyword_meta_string;
	
	} // end get_pt_description_meta;	
	
	private function get_pt_canonical_meta($post, $psp_post_meta, $canonical=false, $pagednum) {
	
		$post_link = "";
		$cat_link = "";
		$set_can_link = "";
		$canonical_meta_string = "";
		//global $wp_query;
		$this_is_static_front_page = false;
		$disable_canonical_here = false;
		
		$disable_canonical_here = !empty($psp_post_meta['disable_canonical']) ? $psp_post_meta['disable_canonical'] : '';
		
		if ($disable_canonical_here) return $canonical_meta_string;
		
		if ( !$disable_canonical_here ) {	
			$can_link = !empty($psp_post_meta['canonical_url']) ? esc_url($psp_post_meta['canonical_url']) : '';
		}
		//get other seoplugins canonical here
		if ( $canonical ) {			
			
			if (empty($can_link)) {
				$post_link = get_permalink($post->ID);
				$can_link = $post_link;
			}

			/*commendted out, so no canlinks for sub pages of post types
			if ($paged && $paged > 1) { 							
				$can_link = $this->pt_paged_link($post_link, $post, $pagednum); 
			}*/
			
	
			//$can_link = trailingslashit($can_link);
			
			//$can_link will never be empty for static page at this point.
			if (is_page()) {
				if ($this->is_static_front_page($post)){					
				
					$this_is_static_front_page = true;
					/***
					$home_link = get_option('home');
					//can link for sub pages created using Next page quicktag.Is it needed?
					$can_link = $this->pt_paged_link($home_link, $post, $pagednum);	
					$can_link = trailingslashit($can_link);
					***/
				}
			}
				
            if (is_attachment()){						
				$can_link = get_permalink($post->post_parent);
			}
		}
				
		if (!empty($can_link)) {
		//if ($can_link != '' && ($canonical)) {
			//echo "".'<link rel="canonical" href="'.$can_link.'" />'."\r\n";
			$this->post_type_can_link = $can_link;
			$canonical_meta_string .= '<link rel="canonical" href="'.esc_url($can_link).'" />'."\r\n";
		}

		if (!$this_is_static_front_page && $pagednum > 1) $canonical_meta_string = "";
		
		return $canonical_meta_string;
        
	} // end get_pt_canonical_meta;	
	
	private function is_static_front_page($post) {
		//global $wp_query;
		//$post = $wp_query->get_queried_object();
		return get_option('show_on_front') == 'page' && is_page() && $post->ID == get_option('page_on_front');
	}

	private function is_static_posts_page($post) {
		//global $wp_query;
		//$post = $wp_query->get_queried_object();
		return get_option('show_on_front') == 'page' && is_home() && $post->ID == get_option('page_for_posts');
	}
	
	private function pt_paged_link($link, $post, $paged) {
	
		//$paged = get_query_var('paged');
		//$paged = $this->is_this_paged($post);
		$has_ut = function_exists('user_trailingslashit');
	    if ($paged && $paged > 1) {
	        $link = trailingslashit($link) ."page/". "$paged";
	        if ($has_ut) {
	            $link = user_trailingslashit($link, 'paged');
	        } else {
	            $link .= '/';
	        }
		}
		return $link;
	}	
	/***
	private function trim_excerpt_without_filters_full_length($text) {
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		return trim(stripcslashes($text));
	}
	**********/
	private function is_this_paged() {
	
		/*$paged = get_query_var( 'paged', 1 );
		if (is_page($post->ID)) {
			if (is_static_front_page($post)) {
				$paged = get_query_var('page', 1);
			}
		}*/

		if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
		elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
		elseif ( $this->current_pageno > 1 ) { $paged =  $this->current_pageno; }
		else { $paged = 1; }

		return $paged;
	}
	
	public function pt_paged_title($title) {
		// the page number if paged
		$paged = $this->is_this_paged();
		
		if ($paged && $paged>1) {
			//$part = $this->internationalize(get_option('aiosp_paged_format'));
			$psp_settings = $this->psp_sitewide_settings;
			$part = isset($psp_settings['psp_paged_title_format']) ? $this->psp_helper->internationalize($psp_settings['psp_paged_title_format']) : '';
			
			//if (isset($part) || !empty($part)) {
			if (isset($part) && !empty($part)) {
				$part = " " . trim($part);
				$part = str_replace('%page%', $paged, $part);				
				//$this->log("pt_paged_title() [$title] [$part]");
				$title .= $part;
			}
		}
		return $title;
	}
	
	/**
	* Check if a post is a custom post type.
	* @param  mixed $post Post object or ID
	* @return boolean
	*/
	private function is_custom_post_type( $post = NULL )
	{
		$custom_post_types = get_post_types( array ( '_builtin' => FALSE ) );

		// if there are no custom post types, return false
		if ( empty ( $custom_post_types ) )
			return FALSE;

		$custom_types      = array_keys( $custom_post_types );
		$current_post_type = get_post_type( $post );

		// if current post type cannot be detected
		if ( ! $current_post_type )
			return FALSE;

		return in_array( $current_post_type, $custom_types );
	}
	
	public function get_single_psp_title($post) {
	
		$title = "";	
		
		if (!empty($this->psp_current_ptype_format)){
			$current_ptype_format = $this->psp_current_ptype_format;
		} else {
			//$ptype_name = get_post_type($post);
			$current_ptype_format_option = "psp_post_settings";
			$current_ptype_format = get_option($current_ptype_format_option);
			$this->psp_current_ptype_format = $current_ptype_format;
		}
		
		if (!empty($this->psp_current_ptype_meta)){
			$psp_post_meta = $this->psp_current_ptype_meta;
		} else {
			$psp_post_meta = get_post_meta($post_id, '_psp_post_seo_meta', true);
			$this->psp_current_ptype_meta = $psp_post_meta;
		}	
		
		$title = $this->psp_helper->internationalize($psp_post_meta['title']);
		if (empty($title)) {
			$title = $this->psp_helper->internationalize(get_post_meta($post->ID, "title", true));
			if (!$title) {
				//$title = $this->psp_helper->internationalize(get_post_meta($post->ID, "title_tag", true));
				$title = $this->psp_helper->internationalize($post->post_title);
				//if (!$title) {
					//$title = $this->psp_helper->internationalize(wp_title('', false));
					//$title = $this->psp_helper->internationalize($post->post_title);
				//}
			}
		}
		$psp_notitleformat = $psp_post_meta['disable_title_format'];
		
		if (empty($psp_notitleformat)) {
			$psp_notitleformat = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'psp_notitleformat', true)));
		}
		
		if (!$psp_notitleformat) { 

			$authordata = get_userdata($post->post_author);
			$categories = get_the_category($post->ID);
			//$category = '';
			//if (count($categories) > 0) {
			//	$category = $categories[0]->cat_name;
			//}
		   
			//$title_format = get_option('aiosp_post_title_format');
			$title_format = $current_ptype_format['title'];
			$psp_single_title_format = isset($psp_post_meta['titleformat']) ? $psp_post_meta['titleformat'] : $title_format;
			$title_format = $psp_single_title_format;
			
			$sitename = $this->sitename;
			$sitedescription = $this->sitedescription;
			
			$categoryname = $categories[0]->cat_name; //not needed
			$userlogin = $authordata->user_login;
			$usernicename = $authordata->user_nicename;
			$userfirstname = $authordata->user_firstname;
			$userlastname = $authordata->user_lastname;
			
			$search_format   = array('%blog_title%', '%blog_description%', '%site_name%', '%site_description%', '%title%', '%category%','%taxonomy%','%author_username%','%author_nicename%','%author_firstname%','%author_lastname%');
			$replaced_format = array($sitename, $sitedescription, $sitename, $sitedescription, $title, $categoryname, $categoryname, $userlogin, $usernicename, $userfirstname, $userlastname);
			$new_title = str_replace($search_format, $replaced_format, $title_format);
			$title = $new_title;
			/************commented**************
			$new_title = str_replace('%blog_title%', $sitename, $title_format);
			$new_title = str_replace('%blog_description%', $sitedescription, $new_title);
			$new_title = str_replace('%site_name%', $sitename, $new_title);
			$new_title = str_replace('%site_description%', $sitedescription, $new_title);
			$new_title = str_replace('%post_title%', $title, $new_title);
			//$new_title = str_replace('%category%', $category, $new_title);
			//$new_title = str_replace('%category_title%', $category, $new_title);
			$new_title = str_replace('%category%', $categories[0]->cat_name, $new_title);
			$new_title = str_replace('%category_title%', $categories[0]->cat_name, $new_title);
			$new_title = str_replace('%post_author_username%', $authordata->user_login, $new_title); 
			$new_title = str_replace('%post_author_nicename%', $authordata->user_nicename, $new_title);
			$new_title = str_replace('%post_author_firstname%', ucwords($authordata->user_firstname), $new_title);
			$new_title = str_replace('%post_author_lastname%', ucwords($authordata->user_lastname), $new_title);
			$title = $new_title;
			********************/
		}
		$title = stripcslashes(trim($title));
		if (1 < $this->is_this_paged()) $title = $this->pt_paged_title($title);
		
		$title = trim($title);
        //$title = trim($title, $psp_title_separator );
		return $title;		
		
	} // end get_single_psp_title
	
	public function get_pt_psp_title($post, $psparr = false) {
	
		$title = "";
		$psp_post_meta = array();
		$psp_post_meta_data = array();
		//$current_ptype_format = array();
		//$wp_post_meta_data_arr = array();
		// we're not in the loop :(
		$ptype_name = '';
		//$post_type_title_format = "psp_".$ptype_name."_title_format";
		if(!is_object($post) || !isset( $post->ID )) return "";
		if (!empty($this->psp_current_ptype_meta)){
		    
			$psp_post_meta = $this->psp_current_ptype_meta;
		} else {
			//$psp_post_meta = get_post_meta($post_id, '_psp_post_seo_meta', true);
			$wp_post_meta_data_arr = get_metadata( 'platinumseo', $post->ID, '', false );
		
			if(!$wp_post_meta_data_arr) {
				$wp_post_meta_data_arr = get_post_meta($post->ID);
			}	
			//$wp_post_meta_data_arr = get_post_meta($post->ID);
			/************
			foreach ($wp_post_meta_data_arr as $key => $value) {
			
				$wp_post_meta_data[$key] = $value[0];
			
			}
			$psp_post_meta_data['title'] = $wp_post_meta_data['_techblissonline_psp_title'];
			$psp_post_meta_data['description'] = $wp_post_meta_data['_techblissonline_psp_description'];
			$psp_post_meta_data['keywords'] = $wp_post_meta_data['_techblissonline_psp_keywords'];
			$psp_post_meta_data['robots'] = $wp_post_meta_data['_techblissonline_psp_robots_meta'];
			$psp_post_meta_data['canonical_url'] = $wp_post_meta_data['_techblissonline_psp_canonical_url'];
			$psp_post_meta_data['noarchive'] = $wp_post_meta_data['_techblissonline_psp_noarchive'];
			$psp_post_meta_data['nosnippet'] = $wp_post_meta_data['_techblissonline_psp_nosnippet'];
			$psp_post_meta_data['noimageindex'] = $wp_post_meta_data_arr['_techblissonline_psp_noimageidx'];
			$psp_post_meta_data['redirect_to_url'] = $wp_post_meta_data['_techblissonline_psp_redirect_to_url'];
			$psp_post_meta_data['redirect_status_code'] = $wp_post_meta_data['_techblissonline_psp_redirect_status_code'];
		
			//$psp_post_disablers = $wp_post_meta_data['_techblissonline_psp_disable_flags'];
			$psp_post_disablers = unserialize($wp_post_meta_data['_techblissonline_psp_disable_flags']);
			***********/
			$psp_post_meta_data['title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_title'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_title'][0] : '';
			$psp_post_meta_data['useolddata'] = '';
			if(isset($wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0])) {
			    $psp_post_meta_data['titleformat'] = $wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0];
				$psp_post_meta_data['useolddata'] = 'none';
			}
			if(isset($wp_post_meta_data_arr['_techblissonline_psp_descformat'][0])) {
			    $psp_post_meta_data['descformat'] = $wp_post_meta_data_arr['_techblissonline_psp_descformat'][0];
			}
			
			$psp_post_meta_data['description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_description'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_description'][0] : '';
			$psp_post_meta_data['keywords'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_keywords'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_keywords'][0] : '';
			$psp_post_meta_data['robots'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_robots_meta'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_robots_meta'][0] : '';
			$psp_post_meta_data['noindex'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_noindex'][0] : '';
			$psp_post_meta_data['nofollow'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0] : '';
			$psp_post_meta_data['nositemap'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nositemap'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_nositemap'][0] : '';
			$psp_post_meta_data['maxsnippet'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maxsnippet'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_maxsnippet'][0] : '';
			$psp_post_meta_data['maxvideo'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maxvideo'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_maxvideo'][0] : '';
			$psp_post_meta_data['maximage'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maximage'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_maximage'][0] : '';
			$psp_post_meta_data['canonical_url'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_canonical_url'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_canonical_url'][0] : '';
			$psp_post_meta_data['noarchive'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noarchive'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_noarchive'][0] : '';
			$psp_post_meta_data['nosnippet'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nosnippet'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_nosnippet'][0] : '';
			$psp_post_meta_data['noimageindex'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noimageidx'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_noimageidx'][0] : '';
			$psp_post_meta_data['redirect_to_url'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_redirect_to_url'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_redirect_to_url'][0] : '';
			$psp_post_meta_data['redirect_status_code'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_redirect_status_code'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_redirect_status_code'][0] : '';
			$psp_post_meta_data['preferred_tax'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_preferred_taxonomy'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_preferred_taxonomy'][0] : '';
			$psp_post_meta_data['preferred_term'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_preferred_term'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_preferred_term'][0] : '';
		    $psp_post_meta_data['schema_string'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0] : '';
		
			$psp_post_disablers = !empty($wp_post_meta_data_arr['_techblissonline_psp_disable_flags'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_disable_flags'][0]) : array();
			//social meta
			$psp_social_meta['fb_og_type'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_og_type'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_og_type'][0] : '';
			$psp_social_meta['fb_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_title'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_title'][0] : '';
			$psp_social_meta['fb_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_description'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_description'][0] : '';
			$psp_social_meta['fb_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_image'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_image'][0] : '';				
			//$psp_social_meta['fb_ogtype_properties'] = isset($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0] : '';
			//$psp_post_fb_ogtype_properties = isset($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) : array();
			$psp_post_fb_ogtype_properties = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0] : '';
			//$psp_social_meta['fb_ogtype_properties'] = http_build_query($psp_post_fb_ogtype_properties, '', '\r\n'); 
			$psp_social_meta['fb_ogtype_properties'] =$psp_post_fb_ogtype_properties;
			$psp_social_meta['fb_media_properties'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_media_properties'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_media_properties'][0] : '';			
			
			$psp_social_meta['tw_card_type'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_card_type'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_card_type'][0] : '';
			$psp_social_meta['tw_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_title'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_title'][0] : '';
			$psp_social_meta['tw_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_description'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_description'][0] : '';
			$psp_social_meta['tw_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_image'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_image'][0] : '';
			
			//$psp_post_tw_data_images = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_images'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_tw_images'][0]) : array();
			//$psp_post_social_tw_label_data = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_label_data'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_tw_label_data'][0]) : array();	
			
			$psp_social_meta['tw_creator'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_creator'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_creator'][0] : '';
			$psp_social_meta['tw_imagealt'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_imagealt'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_imagealt'][0] : '';
			$psp_social_meta['tw_player'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_player'][0] : '';
			$psp_social_meta['tw_player_stream'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_stream'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_player_stream'][0] : '';
			$psp_social_meta['tw_player_width'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_width'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_player_width'][0] : '';
			$psp_social_meta['tw_player_height'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_height'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_player_height'][0] : '';
			
			$psp_social_meta['tw_app_country'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_country'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_country'][0] : '';		
			$psp_social_meta['tw_app_name_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_iphone'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_iphone'][0] : '';
			$psp_social_meta['tw_app_id_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_iphone'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_iphone'][0] : '';
			$psp_social_meta['tw_app_url_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_iphone'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_iphone'][0] : '';
			$psp_social_meta['tw_app_name_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_ipad'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_ipad'][0] : '';
			$psp_social_meta['tw_app_id_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_ipad'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_ipad'][0] : '';
			$psp_social_meta['tw_app_url_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_ipad'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_ipad'][0] : '';
			$psp_social_meta['tw_app_name_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_googleplay'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_googleplay'][0] : '';
			$psp_social_meta['tw_app_id_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_googleplay'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_googleplay'][0] : '';
			$psp_social_meta['tw_app_url_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_googleplay'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_googleplay'][0] : '';
			
			$psp_social_meta['sc_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_title'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_sc_title'][0] : '';
			$psp_social_meta['sc_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_description'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_sc_description'][0] : '';
			$psp_social_meta['sc_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_image'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_sc_image'][0] : '';
			
			//$psp_social_meta = array_merge((array)$psp_social_meta, (array)$psp_post_tw_data_images, (array)$psp_post_social_tw_label_data);
			$this->psp_current_ptype_social_meta = $psp_social_meta;
		
			$psp_post_meta = array_merge((array)$psp_post_meta_data, (array)$psp_post_disablers);
		
			$this->psp_current_ptype_meta = $psp_post_meta;
		}
		
		$psp_disable = !empty($psp_post_meta['disable_psp']) ? htmlspecialchars(stripcslashes($psp_post_meta['disable_psp'])) : '';
		$psp_disable_title_here = !empty($psp_post_meta['disable_title']) ? htmlspecialchars(stripcslashes($psp_post_meta['disable_title'])) : '';		
		
		if (!empty($this->psp_current_ptype_format)){
			$current_ptype_format = $this->psp_current_ptype_format;
		} else {
			$ptype_name = get_post_type($post);
			$current_ptype_format_option = "psp_".$ptype_name."_settings";
			$current_ptype_format = get_option($current_ptype_format_option);
			$this->psp_current_ptype_format = $current_ptype_format;
		}		
		
		//$title = $this->psp_helper->internationalize($psp_post_meta['title']);
		$title = !empty($psp_post_meta['title']) ? htmlspecialchars(stripcslashes($this->psp_helper->internationalize($psp_post_meta['title']))) : '';
		if ($psp_disable || $psp_disable_title_here) {
			//return "";
			$title = htmlspecialchars(stripcslashes($this->psp_helper->internationalize($post->post_title)));
		}
		//get other seo plugins title here
		if (empty($title) && empty($psp_post_meta['useolddata'])) {			
			//$title = htmlspecialchars(stripcslashes($this->psp_helper->internationalize(get_post_meta($post->ID, "title", true))));
			$title = get_post_meta($post->ID, "title", true);
			if (isset($title) && !empty($title)) {
				$title = htmlspecialchars(stripcslashes($this->psp_helper->internationalize($title)));
			//if (!$title) {
			} else {
				
				$yoast_title = trim(stripcslashes($this->psp_helper->internationalize(get_post_meta($post->ID, "_yoast_wpseo_title", true))));// yoast_title
				//trim yoast title of tags
				if (!empty($yoast_title)) {
					$yoast_title = preg_replace('/%%[^%]+%%/', '', $yoast_title);
					$title = !empty($yoast_title) ? $yoast_title : '';
				}
				
				//$title = htmlspecialchars(stripcslashes($this->psp_helper->internationalize($post->post_title)));
			}		
		}
		//assign wp title, if seo title is still empty
		//$title = empty($title) ? htmlspecialchars(stripcslashes($this->psp_helper->internationalize($post->post_title))):'';
		if (empty($title)) {
		    $title = htmlspecialchars(stripcslashes($this->psp_helper->internationalize($post->post_title)));
		}
		
		$psp_notitleformat = !empty($psp_post_meta['disable_title_format']) ? $psp_post_meta['disable_title_format'] : '';
		if (empty($psp_notitleformat)) {
			$psp_notitleformat = get_post_meta($post->ID, 'psp_notitleformat', true);
			if (!empty($psp_notitleformat)) $psp_notitleformat = htmlspecialchars(stripcslashes($psp_notitleformat));
			//$psp_notitleformat = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'psp_notitleformat', true)));
		}
		$title = stripcslashes(trim($title));
		$woo_variables_array = $this->get_woo_variables();
		
		$woo_search_format = array();
		$woo_replace_format	= array();
		
		if (!empty($woo_variables_array) ) {
			$woo_search_format = $woo_variables_array[0];
			$woo_replace_format = $woo_variables_array[1];
			$woo_search_replace_format = $woo_variables_array[2];
			$title = str_replace($search_format, $replaced_format, $title);
		}		
		
		$title = str_replace('%currentyear%', date('Y'), $title);
		$currentyear = date('Y');
		//$title = str_replace('%currentdate%', date_i18n(get_option('date_format')),, $title);
		$psp_title_separator = isset($this->psp_sitewide_settings['separator']) ? $this->psp_sitewide_settings['separator'] : '';
		$title = str_replace('%sep%', $psp_title_separator, $title);
		
		//if (!$psp_notitleformat) { 
			
			$authordata = get_userdata($post->post_author);
			//$categories = get_the_category($post->ID);
			//$categoryname = isset($categories[0]->cat_name) ? $categories[0]->cat_name : '';
			
			//$categoryname = $categories[0]->cat_name; //not needed
			//$category = '';
			//if (count($categories) > 0) {
			//	$category = $categories[0]->cat_name;
			//}
		   
			//$title_format = get_option($post_type_title_format);
			/***
			$args = array(
              'taxonomy'     => $psp_post_meta_data['preferred_tax'],
              'orderby'      => 'name',
              'hierarchical' => true,
              'title_li'     => ''
            );
			wp_list_categories($args);
			****/
			$preferred_tax = !empty($psp_post_meta['preferred_tax']) ? $psp_post_meta['preferred_tax'] : 'category';
			//$categories = get_the_terms( $post, $preferred_tax ); //commented 15/06/2020
			//sort by ID
			//if ($categories) usort($categories, array('PspPtsSeoMetas','pspcmp'));
			if ( !empty($psp_post_meta['preferred_term'] )) {
				$termobj = get_term_by('id', $psp_post_meta['preferred_term'], $preferred_tax);
				$categoryname = isset($termobj) ? $termobj->name : '';
				//$categoryname = !empty($psp_post_meta['preferred_term']) ? $psp_post_meta['preferred_term'] : 'category';	
			} else {
				$categories = get_the_terms( $post, $preferred_tax ); //introduced 15/06/2020
				$categoryname = isset($categories[0]->name) ? $categories[0]->name : '';
			}
			
			$title_format = !empty($current_ptype_format['title']) ? $current_ptype_format['title'] : '';
			$psp_post_title_format = isset($psp_post_meta['titleformat']) ? $psp_post_meta['titleformat'] : $title_format;
			$title_format = $psp_post_title_format;
			
			//if(!empty($title_format)) {
				$sitename = $this->sitename;
				$sitedescription = $this->sitedescription;	

				$original_wp_title = $this->psp_helper->internationalize($post->post_title);
				
				$userlogin = $authordata->user_login;
				$usernicename = $authordata->user_nicename;
				$userfirstname = $authordata->user_firstname;
				$userlastname = $authordata->user_lastname;
				
				//$psp_title_separator = isset($this->psp_sitewide_settings['separator']) ? htmlentities($this->psp_sitewide_settings['separator']) : '';	
				
				//$psp_title_separator = isset($this->psp_sitewide_settings['separator']) ? $this->psp_sitewide_settings['separator'] : '';
				global $pagenow;
				if (is_admin()) $screen = get_current_screen();
				if (( $pagenow == 'post.php' ) && ($screen->parent_base == 'edit')) {				
				//if (isset($_GET['post']) && (isset($_GET['action']) && $_GET['action'] == 'edit')) {
					$search_format   = array('%post_type%', '%blog_title%', '%blog_description%', '%site_name%', '%site_description%', '%wp_title%', '%category%','%taxonomy%','%author_username%','%author_nicename%','%author_firstname%','%author_lastname%', '%sep%', '%seo_title%', '%currentyear%');
					$replaced_format = array($ptype_name, $sitename, $sitedescription, $sitename, $sitedescription, $original_wp_title, $categoryname, $categoryname, $userlogin, $usernicename,$userfirstname, $userlastname, $psp_title_separator, $title, $currentyear);
				} else {
				    if($post->post_status=="publish"){
				        
					    $search_format   = array('%post_type%', '%blog_title%', '%blog_description%', '%site_name%', '%site_description%', '%seo_title%', '%wp_title%', '%category%','%taxonomy%','%author_username%','%author_nicename%','%author_firstname%','%author_lastname%', '%sep%', '%currentyear%');
					    $replaced_format = array($ptype_name, $sitename, $sitedescription, $sitename, $sitedescription, $title, $original_wp_title, $categoryname, $categoryname, $userlogin, $usernicename,$userfirstname, $userlastname, $psp_title_separator, $currentyear);
				    } else {
				        
				        //$search_format   = array('%post_type%', '%blog_title%', '%blog_description%', '%site_name%', '%site_description%', '%wp_title%', '%category%','%taxonomy%','%author_username%','%author_nicename%','%author_firstname%','%author_lastname%', '%sep%');
					    //$replaced_format = array($ptype_name, $sitename, $sitedescription, $sitename, $sitedescription, $original_wp_title, $categoryname, $categoryname, $userlogin, $usernicename,$userfirstname, $userlastname, $psp_title_separator);
						$search_format   = array('%post_type%', '%blog_title%', '%blog_description%', '%site_name%', '%site_description%', '%seo_title%', '%wp_title%', '%category%','%taxonomy%','%author_username%','%author_nicename%','%author_firstname%','%author_lastname%', '%sep%', '%currentyear%');
					    $replaced_format = array($ptype_name, $sitename, $sitedescription, $sitename, $sitedescription, $title, $original_wp_title, $categoryname, $categoryname, $userlogin, $usernicename,$userfirstname, $userlastname, $psp_title_separator, $currentyear);
				    }
				        
				}
				if(!empty($title_format)) {
					$new_title = str_replace($search_format, $replaced_format, $title_format);
					if (!$psp_disable && !$psp_notitleformat) $title = $new_title;	
				}	
			//} //titleformat empty
		//} //notitileformat
		
		if (1 < $this->is_this_paged()) $title = $this->pt_paged_title($title);
		$this->post_type_title = $title;
		
		if ($psparr) {
			
				//$title = str_replace('%currentyear%', date('Y'), $title);

	            $sitedescription = !empty($sitedescription) ? $sitedescription : '';
	            $autogendesc = !empty($this->psp_sitewide_settings['autogenerate_description']) ? $this->psp_sitewide_settings['autogenerate_description'] : '';
	            $sitename = !empty($sitename) ? $sitename : '';
	            $categoryname = !empty($categoryname) ? $categoryname :  '';
	            $title = !empty($title) ? $title : '';
	            $original_wp_title = !empty($original_wp_title) ? $original_wp_title : '';
	            $psp_title_separator = !empty($psp_title_separator) ? $psp_title_separator : '';
	            
	            $psp_post_format_arr = array('autogendesc' => $autogendesc, 'site_name' => $sitename, 'site_description' => $sitedescription, 'category' => $categoryname, 'title' => $title, 'seo_title' => $title, 'wp_title' => $original_wp_title, 'sep' => $psp_title_separator, 'currentyear' => $currentyear);
				
				$psp_post_format_arr = !empty($woo_search_replace_format) ? wp_parse_args( $woo_search_replace_format, $psp_post_format_arr) : $psp_post_format_arr;

	            return $psp_post_format_arr;

        }
        $title = trim($title);
		$psp_title_separator = html_entity_decode($psp_title_separator);
        $title = trim($title, $psp_title_separator );
		return $title;		
		
	} // end get_pt_psp_title
	
	private function get_woo_variables() {

		//WooCommerce
		$woo_single_cat_html = '';
		$woo_single_tag_html = '';
		$woo_single_price = '';
		$woo_single_price_exc_tax = '';
		$woo_single_sku = '';
		
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if (is_plugin_active('woocommerce/woocommerce.php')) {
			if (is_singular(['product'])) {
				//Woo Cat product
				$woo_single_cats = get_the_terms($post->ID, 'product_cat');

				if ($woo_single_cats && ! is_wp_error($woo_single_cats)) {
					$woo_single_cat = [];

					foreach ($woo_single_cats as $term) {
						$woo_single_cat[$term->term_id] = $term->name;
					}

					$woo_single_cat = apply_filters('seopress_titles_product_cat', $woo_single_cat);

					$woo_single_cat_html = stripslashes_deep(wp_filter_nohtml_kses(join(', ', $woo_single_cat)));
				}

				//Woo Tag product
				$woo_single_tags = get_the_terms($post->ID, 'product_tag');

				if ($woo_single_tags && ! is_wp_error($woo_single_tags)) {
					$woo_single_tag = [];

					foreach ($woo_single_tags as $term) {
						$woo_single_tag[$term->term_id] = $term->name;
					}

					$woo_single_tag = apply_filters('seopress_titles_product_tag', $woo_single_tag);

					$woo_single_tag_html = stripslashes_deep(wp_filter_nohtml_kses(join(', ', $woo_single_tag)));
				}

				//Woo Price
				$product          = wc_get_product($post->ID);
				$woo_single_price = wc_get_price_including_tax($product);

				//Woo Price tax excluded
				$product                  = wc_get_product($post->ID);
				$woo_single_price_exc_tax = wc_get_price_excluding_tax($product);

				//Woo SKU Number
				$product        = wc_get_product($post->ID);
				$woo_single_sku = $product->get_sku();
			}
			
			$woo_variables_arr = [	
				'%woo_single_cat%',
				'%woo_single_tag%',
				'%woo_single_price%',
				'%woo_single_price_exc_tax%',
				'%woo_sku%'	
			];
			
			$psp_titles_template_var_array = apply_filters('psp_titles_template_variables_array', $woo_variables_arr);
			
			$psp_titles_template_var_replace_array = [
									$woo_single_cat_html,
									$woo_single_tag_html,
									$woo_single_price,
									$woo_single_price_exc_tax,
									$woo_single_sku
									];
									
			$psp_titles_template_var_replace_array = apply_filters('psp_titles_template_var_replace_array', $psp_titles_template_var_replace_array);
			
			$psp_titles_template_var_search_replace_arr = 	[	
																'woo_single_cat' => $woo_single_cat_html,
																'woo_single_tag' => $woo_single_tag_html,
																'woo_single_price' => $woo_single_price,
																'woo_single_price_exc_tax' => $woo_single_price_exc_tax,
																'woo_sku'	=> $woo_single_sku,
															];
			
			return array($psp_titles_template_var_array, $psp_titles_template_var_replace_array, $psp_titles_template_var_search_replace_arr);
		
		} else {
			return array();
		}	

	}
	
	public function get_preferred_taxonomy_for_bc() {
		return $this->preferred_taxonomy_for_bc;
	} //get_preferred_taxonomy_for_bc
	
	public function get_default_taxonomy_for_bc() {
		return $this->default_taxonomy_for_bc;
	} //get_default_taxonomy_for_bc
	
	public function get_page_psp_title($post) {
	
		$title = "";		
		
		// we're not in the loop :(
		
		if (!empty($this->psp_current_ptype_meta)){
			$psp_post_meta = $this->psp_current_ptype_meta;
		} else {
			$psp_post_meta = get_post_meta($post_id, '_psp_post_seo_meta', true);
			$this->psp_current_ptype_meta = $psp_post_meta;
		}
		
		$psp_disable = !empty($psp_post_meta['disable_psp']) ? htmlspecialchars(stripcslashes($psp_post_meta['disable_psp'])) : '';
		$psp_disable_title_here = !empty($psp_post_meta['disable_title']) ? htmlspecialchars(stripcslashes($psp_post_meta['disable_title'])) : '';
				
		if ($psp_disable || $psp_disable_title_here) {
			return "";
		}
		
		if (!empty($this->psp_current_ptype_format)){
			$current_ptype_format = $this->psp_current_ptype_format;
		} else {
			//$ptype_name = get_post_type($post);
			$current_ptype_format_option = "psp_page_settings";
			$current_ptype_format = get_option($current_ptype_format_option);
			$this->psp_current_ptype_format = $current_ptype_format;
		}		
		
		//if ($this->is_static_front_page()) {
			//if ($this->psp_helper->internationalize(get_option('aiosp_home_title'))) {
				//	$header = $this->replace_title($header, $this->psp_helper->internationalize(get_option('aiosp_home_title')));
			//}
		//} else {
			$title = !empty($psp_post_meta['title']) ? $this->psp_helper->internationalize($psp_post_meta['title']) : '';
			//get other seo plugins title here for pages.
			if (empty($title)) {
				$title = $this->psp_helper->internationalize(get_post_meta($post->ID, "title", true));
			}
			if (!$title) {
				if ($this->is_static_front_page($post)) {
					$psp_home_settings = get_option('psp_home_settings');
					$use_front_page_settings = isset($psp_home_settings['use_front_page']) ? $psp_home_settings['use_front_page'] : '';
					if(!$use_front_page_settings) {
						$title = isset($psp_home_settings['title']) ? $this->psp_helper->internationalize($psp_home_settings['title']) : '';
					} else {
						$title = $this->psp_helper->internationalize($post->post_title);
					}
					//if (empty($title)) {
					//	$title = $this->psp_helper->internationalize(get_option('aiosp_home_title'));
					//}
				} else {		
					//$title = $this->psp_helper->internationalize(wp_title('', false));
					$title = $this->psp_helper->internationalize($post->post_title);
				}
			}
			$psp_notitleformat = !empty($psp_post_meta['disable_title_format']) ? $psp_post_meta['disable_title_format'] : '';
			/*if (empty($psp_notitleformat)) {
				$psp_notitleformat = htmlspecialchars(stripcslashes(get_post_meta($post->ID, 'psp_notitleformat', true)));
			}*/
		    if (!$psp_notitleformat) {
			
				$authordata = get_userdata($post->post_author);
				$sitename = $this->sitename;
				$sitedescription = $this->sitedescription;
			
				//$title_format = get_option('aiosp_page_title_format');
				$title_format = isset($current_ptype_format['title']) ? $current_ptype_format['title'] : '';
				$psp_page_title_format = isset($psp_post_meta['titleformat']) ? $psp_post_meta['titleformat'] : $title_format;
				$title_format = $psp_page_title_format;
				
				if(!empty($title_format)) {
					//$categoryname = $categories[0]->cat_name; //not needed
					$userlogin = $authordata->user_login;
					$usernicename = $authordata->user_nicename;
					$userfirstname = $authordata->user_firstname;
					$userlastname = $authordata->user_lastname;
				
					$search_format   = array('%post_type%', '%blog_title%', '%blog_description%', '%site_name%', '%site_description%', '%page_title%', '%page_author_username%','%page_author_nicename%','%page_author_firstname%','%page_author_lastname%');
					$replaced_format = array($ptype_name, $sitename, $sitedescription, $sitename, $sitedescription, $title, $userlogin, $usernicename,$userfirstname, $userlastname);
					$new_title = str_replace($search_format, $replaced_format, $title_format);
					/****************
					$new_title = str_replace('%blog_title%', $sitename, $title_format);
					$new_title = str_replace('%blog_description%', $sitedescription, $new_title);
					$new_title = str_replace('%page_title%', $title, $new_title);
					//author info
					$new_title = str_replace('%page_author_login%', $authordata->user_login, $new_title);
					$new_title = str_replace('%page_author_nicename%', $authordata->user_nicename, $new_title);
					$new_title = str_replace('%page_author_firstname%', ucwords($authordata->first_name), $new_title);
					$new_title = str_replace('%page_author_lastname%', ucwords($authordata->last_name), $new_title);
					***************/
					$title = stripcslashes(trim($new_title));
				}
			}
			//$header = $this->replace_title($header, $title);
			if (1 < $this->is_this_paged()) $title = $this->pt_paged_title($title);
			return $title;		
		
	} // end get_page_psp_title
	
	public function get_pt_seo_metas($post, $canonical) {
	
		$can_link = "";
		$term_link = "";
		$robots_meta_string = "";
		$desc_keyword_meta_string = "";
		$canonical_meta_string = "";
		$json_schema_string = "";
		$jsonld_script_tag = "";
		$seo_meta_string = "";
		$post_meta = "";
		
		$psp_post_meta = array();
		$psp_post_meta_data = array();
		$current_ptype_format = array();
		
		$cpage = get_query_var('cpage');
		
		//To handle exta metas,if any, defined in settings.
		$meta_string = "";
		$meta_psp_string = "";

		if(!is_object($post) || !isset( $post->ID )) return "";
		
		$post_id = $post->ID;
		
		//get post meta
		//$psp_post_meta = get_post_meta($post_id, '_psp_post_seo_meta', true);		
		if (!empty($this->psp_current_ptype_meta)){
			$psp_post_meta = $this->psp_current_ptype_meta;
		} else {
			//$psp_post_meta = get_post_meta($post_id, '_psp_post_seo_meta', true);
			
			$wp_post_meta_data_arr = get_metadata( 'platinumseo', $post->ID, '', false );
		
			if(!$wp_post_meta_data_arr) {
				$wp_post_meta_data_arr = get_post_meta($post->ID);
			}		
			
			/**********
			foreach ($wp_post_meta_data_arr as $key => $value) {
			
				$wp_post_meta_data[$key] = $value[0];
			
			}
			
			$psp_post_meta_data['title'] = $wp_post_meta_data['_techblissonline_psp_title'];
			$psp_post_meta_data['description'] = $wp_post_meta_data['_techblissonline_psp_description'];
			$psp_post_meta_data['keywords'] = $wp_post_meta_data['_techblissonline_psp_keywords'];
			$psp_post_meta_data['robots'] = $wp_post_meta_data['_techblissonline_psp_robots_meta'];
			$psp_post_meta_data['canonical_url'] = $wp_post_meta_data['_techblissonline_psp_canonical_url'];
			$psp_post_meta_data['noarchive'] = $wp_post_meta_data['_techblissonline_psp_noarchive'];
			$psp_post_meta_data['nosnippet'] = $wp_post_meta_data['_techblissonline_psp_nosnippet'];
			$psp_post_meta_data['noimageindex'] = $wp_post_meta_data_arr['_techblissonline_psp_noimageidx'];
			$psp_post_meta_data['redirect_to_url'] = $wp_post_meta_data['_techblissonline_psp_redirect_to_url'];
			$psp_post_meta_data['redirect_status_code'] = $wp_post_meta_data['_techblissonline_psp_redirect_status_code'];
		
			//$psp_post_disablers = $wp_post_meta_data['_techblissonline_psp_disable_flags'];
			$psp_post_disablers = unserialize($wp_post_meta_data['_techblissonline_psp_disable_flags']);
			***********/
			$psp_post_meta_data['title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_title'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_title'][0] : '';
			$psp_post_meta_data['useolddata'] = '';
			if(isset($wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0])) {
			    $psp_post_meta_data['titleformat'] = $wp_post_meta_data_arr['_techblissonline_psp_titleformat'][0];
				$psp_post_meta_data['useolddata'] = 'none';
			}
			if(isset($wp_post_meta_data_arr['_techblissonline_psp_descformat'][0])) {
			    $psp_post_meta_data['descformat'] = $wp_post_meta_data_arr['_techblissonline_psp_descformat'][0];
			}
			$psp_post_meta_data['description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_description'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_description'][0] : '';
			$psp_post_meta_data['keywords'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_keywords'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_keywords'][0] : '';
			$psp_post_meta_data['robots'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_robots_meta'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_robots_meta'][0] : '';
			$psp_post_meta_data['noindex'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noindex'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_noindex'][0] : '';
			$psp_post_meta_data['nofollow'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_nofollow'][0] : '';
			$psp_post_meta_data['nositemap'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nositemap'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_nositemap'][0] : '';
			$psp_post_meta_data['maxsnippet'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maxsnippet'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_maxsnippet'][0] : '';
			$psp_post_meta_data['maxvideo'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maxvideo'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_maxvideo'][0] : '';
			$psp_post_meta_data['maximage'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_maximage'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_maximage'][0] : '';
			$psp_post_meta_data['canonical_url'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_canonical_url'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_canonical_url'][0] : '';
			$psp_post_meta_data['noarchive'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noarchive'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_noarchive'][0] : '';
			$psp_post_meta_data['nosnippet'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_nosnippet'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_nosnippet'][0] : '';
			$psp_post_meta_data['noimageindex'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_noimageidx'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_noimageidx'][0] : '';
			$psp_post_meta_data['redirect_to_url'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_redirect_to_url'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_redirect_to_url'][0] : '';
			$psp_post_meta_data['redirect_status_code'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_redirect_status_code'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_redirect_status_code'][0] : '';
			$psp_post_meta_data['preferred_tax'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_preferred_taxonomy'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_preferred_taxonomy'][0] : '';
			$psp_post_meta_data['preferred_term'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_preferred_term'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_preferred_term'][0] : '';
			$psp_post_meta_data['schema_string'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0] : '';
			
			$psp_post_disablers = !empty($wp_post_meta_data_arr['_techblissonline_psp_disable_flags'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_disable_flags'][0]) : array();	
			//social meta
			$psp_social_meta['fb_og_type'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_og_type'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_og_type'][0] : '';
			$psp_social_meta['fb_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_title'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_title'][0] : '';
			$psp_social_meta['fb_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_description'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_description'][0] : '';
			$psp_social_meta['fb_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_image'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_image'][0] : '';
			//$psp_social_meta['fb_ogtype_properties'] = isset($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0] : '';
			//$psp_post_fb_ogtype_properties = isset($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) : array();
			$psp_post_fb_ogtype_properties = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_ogtype_properties'][0] : '';
			//$psp_social_meta['fb_ogtype_properties'] = http_build_query($psp_post_fb_ogtype_properties, '', '\r\n'); 
			$psp_social_meta['fb_ogtype_properties'] =$psp_post_fb_ogtype_properties;
			$psp_social_meta['fb_media_properties'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_fb_media_properties'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_fb_media_properties'][0] : '';
			
			$psp_social_meta['tw_card_type'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_card_type'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_card_type'][0] : '';
			$psp_social_meta['tw_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_title'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_title'][0] : '';
			$psp_social_meta['tw_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_description'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_description'][0] : '';
			$psp_social_meta['tw_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_image'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_image'][0] : '';
			
			//$psp_post_tw_data_images = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_images'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_tw_images'][0]) : array();
			//$psp_post_social_tw_label_data = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_label_data'][0]) ? unserialize($wp_post_meta_data_arr['_techblissonline_psp_tw_label_data'][0]) : array();	
			
			$psp_social_meta['tw_creator'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_creator'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_creator'][0] : '';
			$psp_social_meta['tw_imagealt'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_imagealt'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_imagealt'][0] : '';
			$psp_social_meta['tw_player'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_player'][0] : '';
			$psp_social_meta['tw_player_stream'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_stream'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_player_stream'][0] : '';
			$psp_social_meta['tw_player_width'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_width'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_player_width'][0] : '';
			$psp_social_meta['tw_player_height'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_player_height'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_player_height'][0] : '';
			
			$psp_social_meta['tw_app_country'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_country'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_country'][0] : '';		
			$psp_social_meta['tw_app_name_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_iphone'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_iphone'][0] : '';
			$psp_social_meta['tw_app_id_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_iphone'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_iphone'][0] : '';
			$psp_social_meta['tw_app_url_iphone'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_iphone'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_iphone'][0] : '';
			$psp_social_meta['tw_app_name_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_ipad'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_ipad'][0] : '';
			$psp_social_meta['tw_app_id_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_ipad'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_ipad'][0] : '';
			$psp_social_meta['tw_app_url_ipad'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_ipad'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_ipad'][0] : '';
			$psp_social_meta['tw_app_name_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_googleplay'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_name_googleplay'][0] : '';
			$psp_social_meta['tw_app_id_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_googleplay'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_id_googleplay'][0] : '';
			$psp_social_meta['tw_app_url_googleplay'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_googleplay'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_tw_app_url_googleplay'][0] : '';
			
			$psp_social_meta['sc_title'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_title'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_sc_title'][0] : '';
			$psp_social_meta['sc_description'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_description'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_sc_description'][0] : '';
			$psp_social_meta['sc_image'] = !empty($wp_post_meta_data_arr['_techblissonline_psp_sc_image'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_sc_image'][0] : '';
					
			//$psp_social_meta = array_merge((array)$psp_social_meta, (array)$psp_post_tw_data_images, (array)$psp_post_social_tw_label_data);
			$this->psp_current_ptype_social_meta = $psp_social_meta;
		
			$psp_post_meta = array_merge((array)$psp_post_meta_data, (array)$psp_post_disablers);
		
			$this->psp_current_ptype_meta = $psp_post_meta;
		}
		
		if (!empty($psp_post_meta)) {
			$this->preferred_taxonomy_for_bc = !empty($psp_post_meta['preferred_tax']) ? $psp_post_meta['preferred_tax'] : '';
		}
		
		$psp_disable = !empty($psp_post_meta['disable_psp']) ? htmlspecialchars(stripcslashes($psp_post_meta['disable_psp'])) : '';
				
		if ($psp_disable) {
			return "";
		}
		
		
		if (!empty($this->psp_current_ptype_format)){
			$current_ptype_format = $this->psp_current_ptype_format;
		} else {
			$ptype_name = get_post_type($post);
			$current_ptype_format_option = "psp_".$ptype_name."_settings";
			$current_ptype_format = get_option($current_ptype_format_option);
			$this->psp_current_ptype_format = $current_ptype_format;
		}
		
		if (!empty($current_ptype_format)) {
			$this->default_taxonomy_for_bc = isset($current_ptype_format['default_tax']) ? $current_ptype_format['default_tax'] : '';
		}
		
		//$paged = $this->is_this_paged($post);
		$paged = $this->is_this_paged();		
		
		$robots_meta_string = $this->get_pt_robots_meta($post, $psp_post_meta, $paged, $cpage);
		
		//$paged = get_query_var('paged');
		//if (is_page()) {
		//	if (is_static_front_page()) {
		//		$paged = get_query_var('page');
		//	}
		//}
				
		
	    if ($paged < 2 || (!$paged)) {
		//if ($paged < 2) {
			$desc_keyword_meta_string = $this->get_pt_description_meta($post, $psp_post_meta);
		}
		if ($canonical)
		{
			$canonical_meta_string = $this->get_pt_canonical_meta($post, $psp_post_meta, $canonical, $paged);
		}
		
		$psp_settings = $this->psp_sitewide_settings;
		
		$json_schema_string = !empty($this->psp_current_ptype_meta['schema_string']) ? $this->psp_current_ptype_meta['schema_string'] : '';
		//error_log("Schema JSON ".$json_schema_string);
		
		$json_schema_string = html_entity_decode(stripcslashes(esc_html($json_schema_string)));
		//validate it is a json object
		$schema_obj = json_decode($json_schema_string);
			if($schema_obj === null) {
			   $json_schema_string = "";
			}
		
		if (!empty($json_schema_string) && !empty($psp_settings['enable_schema']) ) {
			//$jsonld_script_tag = '<scri' + 'pt type="application/ld+json">'.' \r\n'. $json_schema_string . '\r\n </scri' . 'pt>';
			$jsonld_script_tag = '<scri'.'pt type="application/ld+json">'. "\r\n" . $json_schema_string . "\r\n" . '</scri'.'pt>' ."\r\n";
		}
		//$current_ptype_format['headers'];
		
		//$page_meta = stripcslashes(get_option('aiosp_page_meta_tags'));
		//$post_meta = stripcslashes(get_option('aiosp_post_meta_tags'));
		//$post_meta = isset($current_ptype_format['headers']) ? stripcslashes($current_ptype_format['headers']) : '';
		$post_meta = isset($current_ptype_format['headers']) ? html_entity_decode(stripcslashes(esc_html($current_ptype_format['headers']))) : '';
		//validate headers
		if( !empty( $post_meta ) ) {
    	
    		$allowed_html = array(
    			'meta' => array(
    				'name' => array(),
    				'property' => array(),
    				'itemprop' => array(),
    				'content' => array(),
    			),    
    		);
    
    		$post_meta = wp_kses($post_meta, $allowed_html);
		}
		
		$page_meta = $post_meta;
		//$home_meta = stripcslashes(get_option('aiosp_home_meta_tags'));
		
		if (is_page() && isset($page_meta) && !empty($page_meta)) {
			if (isset($meta_string) && !empty($meta_string)) {
				$meta_string .= "\r\n";
			}
			//echo "\r\n$page_meta";
			$meta_string .= "$page_meta";
		}

		if (is_single() && isset($post_meta) && !empty($post_meta)) {
			if (isset($meta_string) && !empty($meta_string)) {
				$meta_string .= "\r\n";
			}
			$meta_string .= "$post_meta";
		}
		/**
		if (is_home() && !empty($home_meta)) {
			if (isset($meta_string)) {
				$meta_string .= "\r\n";
			}
			$meta_string .= "$home_meta";
		}
		**/
		if ($meta_string != null) {
			//echo "$meta_string\r\n";
			//$meta_psp_string .= "$meta_string\r\n";
			$meta_psp_string .= "$meta_string";
		}
		
		//$seo_meta_string =  $desc_keyword_meta_string."\r\n".$robots_meta_string."\r\n".$meta_psp_string.$canonical_meta_string;
		
		if (!empty($desc_keyword_meta_string)) $seo_meta_string = $desc_keyword_meta_string;
		if (!empty($robots_meta_string)) $seo_meta_string = $seo_meta_string."\r\n".$robots_meta_string;
		if (!empty($canonical_meta_string)) $seo_meta_string = $seo_meta_string."\r\n".$canonical_meta_string;
		if (!empty($jsonld_script_tag)) $seo_meta_string = $seo_meta_string.$jsonld_script_tag;
		if (!empty($meta_psp_string)) $seo_meta_string = $seo_meta_string.$meta_psp_string;
		
		return $seo_meta_string;
	
	} // end get_pt_seo_metas;	
	
	public function psp_get_pt_schema($post) {	

		$jsonld_script_tag = "";
		$json_schema_string  = "";	
		if($post) {
			$wp_post_meta_data_arr = get_metadata( 'platinumseo', $post->ID, '', false );
			$json_schema_string = !empty($wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0]) ? $wp_post_meta_data_arr['_techblissonline_psp_schema_string'][0] : '';
			
			$json_schema_string = html_entity_decode(stripcslashes(esc_html($json_schema_string)));
			//validate it is a json object
			$schema_obj = json_decode($json_schema_string);
				if($schema_obj === null) {
				   $json_schema_string = "";
				}
			
			if (!empty($json_schema_string)) {
				//$jsonld_script_tag = '<scri' + 'pt type="application/ld+json">'.' \r\n'. $json_schema_string . '\r\n </scri' . 'pt>';
				$jsonld_script_tag = '<scri'.'pt type="application/ld+json">'. "\r\n" . $json_schema_string . "\r\n" . '</scri'.'pt>' ."\r\n";
			}
			
			if (!empty($jsonld_script_tag) && !is_paged() ) {

				return $jsonld_script_tag;
				
			} else {
				return "";
			}
		}		
		
	} // end psp_get_pt_schema;
	
}
?>