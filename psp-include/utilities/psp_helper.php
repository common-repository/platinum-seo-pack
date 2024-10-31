<?php

/*
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Description: Complete SEO solution for your Wordpress blog.
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspHelper {
	private static $obj_handle = null;	
	
	protected $psp_sitewide_settings = array();
	
	/** Max numbers of chars in auto-generated description */
 	private $max_description_length = 200;

 	/** Minimum number of chars an excerpt should be so that it can be used
 	 * as description. Touch only if you know what you're doing
 	 */
 	private $min_description_length = 1;
	private $version = "2.4.0";
	
	public $sitename = "";
	public $sitedescription = "";
	
	public $pagenumber = 1;
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;
	
	public function __construct() {

		$this->sitename = trim(stripcslashes($this->internationalize(get_bloginfo('name'))));
		$this->sitedescription = trim(stripcslashes($this->internationalize(get_bloginfo('description'))));	
		
	}
	
	public function get_sitename() {
	
		return $this->sitename;
	
	} // end get_sitename;
	
	public function get_sitedescription() {
	
		return $this->sitedescription;
	
	} // end get_sitedescription;
	
	public function get_prev_next_links() {
	    global $paged;
		$prevnext_meta_string = "";
		if ( get_previous_posts_link() ) {			
			$prev_rel_link = get_pagenum_link( $paged - 1 );
                        //if (is_search()) $prev_rel_link = get_pagenum_link( $page - 1 );	
			//if (!is_search()) $meta_psp_string .= '<link rel="prev" href="'.$prev_rel_link.'" />'."\n";
			$prevnext_meta_string .= '<link rel="prev" href="'.esc_url($prev_rel_link).'" />'."\n";
		}
		if ( get_next_posts_link() ) {
			$next_rel_link = get_pagenum_link( $paged + 1 );
			//if (!is_search() && !is_home()) $meta_psp_string .= '<link rel="next" href="'.$next_rel_link.'" />'."\n";
			$prevnext_meta_string .= '<link rel="next" href="'.esc_url($next_rel_link).'" />'."\n";
		}
		return $prevnext_meta_string;
	} // end get_prev_next_links
	
	public function internationalize($in) {
		if (function_exists('langswitch_filter_langs_with_message')) {
			$in = langswitch_filter_langs_with_message($in);
		}
		if (function_exists('polyglot_filter')) {
			$in = polyglot_filter($in);
		}
		if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
			$in = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($in);
		}
		$in = apply_filters('localization', $in);
		return $in;
	}
	
	/*
	* Check user access capability
	*/
	public function user_has_access ( $tab ) {
	    
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

	public function trim_excerpt_without_filters($text) {
		$text = str_replace(']]>', ']]&gt;', $text);                
		$text = strip_tags($text);
		$max = $this->max_description_length;

		if ($max < strlen($text)) {
			while($text[$max] != ' ' && $max > $this->min_description_length) {
				$max--;
			}
		}
        if ($max < strlen($text)) {
			$findchar = array (".", "!", "?");
			$desc_end = $this->strpos_array($text, $findchar, $max);
			$text = substr($text, 0, $desc_end+1);
		} else {
			$text = substr($text, 0, $max);
		}
		
		//$text = substr($text, 0, $max);		
		return trim(stripcslashes($text));
	}
	
	public function do_psp_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[$page] ) )
				return;

		foreach ( (array) $wp_settings_sections[$page] as $section ) {
				if ( $section['title'] )
						echo "<h3>".esc_html($section['title'])."</h3>\n";

				if ( $section['callback'] )
						call_user_func( $section['callback'], $section );

				if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
						continue;
				echo '<table class="form-table">';
				$this->do_psp_settings_fields( $page, $section['id'] );
				echo '</table>';
		}
	}
	
	public function do_psp_settings_fields($page, $section) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[$page][$section] ) )
				return;

		foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
				if ( !empty($field['args']['class_for_row']) )
					echo '<tr class="'. esc_attr($field['args']['class_for_row']) . '">';
				else
					echo '<tr>';
				if ( !empty($field['args']['label_for']) )
						echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . esc_html($field['title']) . '</label></th>';
				else
						echo '<th scope="row">' . esc_html($field['title']) . '</th>';
				echo '<td>';
				call_user_func($field['callback'], $field['args']);
				echo '</td>';
				echo '</tr>';
		}
	}

	public function strpos_array($haystack, $needles, $offset) {
	
		$poses = array();
		if ( is_array($needles) ) {
			foreach ($needles as $str) {
				if ( is_array($str) ) {
					$pos = strpos_array($haystack, $str, $offset);
				} else {
					$pos = strpos($haystack, $str, $offset);
					if ($pos != false) {
						$poses[] = $pos;
					}
				}
				//return $pos;            
			}
			
			if (!empty($poses)) {
				return min($poses);
			} else {
				return $offset;
			}
		} else {
			return strpos($haystack, $needles);
		}
	}

	public function trim_excerpt_without_filters_full_length($text) {
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		return trim(stripcslashes($text));
	}
	public function get_psp_sitewide_settings() {
		if (empty($this->psp_sitewide_settings)) {
			$this->psp_sitewide_settings = get_option('psp_sitewide_settings');
		}
		return $this->psp_sitewide_settings;
	}
	public function paged_title($title) {
		// the page number if paged
		global $paged;
		$pagenumber = $paged;
		
		if($this->pagenumber > 1) $pagenumber = $this->pagenumber;

		// simple tagging support
		global $STagging;

		if (is_paged() || $pagenumber > 1 || (isset($STagging) && $STagging->is_tag_view() && $pagenumber)) { //$pagenumber instead of $paged
			//$part = $this->internationalize(get_option('aiosp_paged_format'));
			//$psp_settings = $this->psp_sitewide_settings;
			$psp_settings = get_option('psp_sitewide_settings');
			$part = $this->internationalize($psp_settings['paged_title_format']);
			
			if (isset($part) || !empty($part)) {
				$part = " " . trim($part);
				//$part = str_replace('%page%', $paged, $part);
				$part = str_replace('%page%', $pagenumber, $part);	
                $part = str_replace('%sep%', $psp_settings['separator'], $part);				
				//$this->log("paged_title() [$title] [$part]");
				$title .= $part;
			}
		}
		return $title;
	}
	
	public function psp_tracer( $which_tracer, $echo = false ) {		
		$start_tracer =  "<!-- Platinum Seo Pack, version ".$this->version." by https://techblissonline.com/ -->\r\n";          
		$end_tracer =  "\r\n<!--Techblissonline Platinum SEO Pack -->\r\n";
		if ($which_tracer == "START") {
			$return_tracer = $start_tracer;                       
		} else {
			$return_tracer = $end_tracer;                       
		}                
		if ( $echo == false ) {
			return $return_tracer;
		} else {
			echo "\n$return_tracer\n";
		}
	}
	
	public function paged_link($link) {
		//$page = get_query_var('paged');
		$page = get_query_var( 'paged', 1 );
		if($this->pagenumber > 1) $page = $this->pagenumber;
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
	}
	
	public function psp_auto_updater( $update, $item ) {
		return in_array( $item->slug, array( 'platinum-seo-pack') ) ? true : $update ;
	}
}