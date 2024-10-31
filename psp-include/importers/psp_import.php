<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
class PspImporter {	
	
	private static $obj_handle = null;	
	
	protected $psp_helper;
	
	protected $psp_plugin_options_key = 'platinum-seo-social-pack-by-techblissonline';
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;
	//can be made private for singleton pattern
	public function __construct() {	

		//$psp_helper_instance = PspHelper::get_instance();		
		//$this->psp_helper = $psp_helper_instance;
		
		//add_action('admin_menu', array(&$this, 'psp_importer_admin_menu'));		
		add_action( 'wp_ajax_import_yoast_data', array($this, 'import_yoast_data' ), 1);
		
		//add_action( 'wp_ajax_psp_rm_importer', array(&$this, 'psp_rm_importer') );

		//add_action( 'admin_enqueue_scripts', array(&$this, 'psp_importer_enqueue') );
		
	}
	
	public function psp_importer_enqueue($hook) {
		
		$import_yoast_data_nonce = wp_create_nonce( 'import_yoast_data_nonce' ); 
        // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        wp_enqueue_script( 'psp-ajax-import-script', plugins_url( '/js/psp-importer.js', __FILE__ ), array('jquery') );
        
        wp_localize_script( 'psp-ajax-import-script', 'psp_ajax_importer_object', array( 'ajax_url' => admin_url( 'admin-ajax.php'), 'nonce' => $import_yoast_data_nonce) );
		
	}
	
	public function psp_importer_admin_menu() {				
		
		//$psp_importer_page = add_management_page('Data Import Manager', 'Platinum SEO Importer', 'manage_options', 'pspimporter', array($this, 'pspimport_mgmtpage'));
		//$psp_importer_page_2 = add_submenu_page($this->psp_plugin_options_key, esc_html__('Techblissonline Platinum SEO Importer', 'platinum-seo-pack'), '<span class="dashicons dashicons-admin-tools"></span> '.esc_html__('Importer', 'platinum-seo-pack'), 'manage_options', 'importer', array($this, 'pspimport_mgmtpage'));
		//$psp_importer_page_2 = 'platinum-seo-and-social-pack_page_pspimporter';
		//error_log('redir '. $psp_importer_page);
			
	}
	
	public function pspimport_mgmtpage() {
	
		include_once( 'psp_data_importer.php' );
	
	}
	
	public function import_yoast_data() {
		
		//error_log("psp_yoast_importer");
		check_ajax_referer( 'import_yoast_data_nonce', 'ajax_nonce');
		//$this->import_yoast_post_meta();
		$whattoimport = isset($_POST['title']) ? sanitize_key( $_POST['title'] ) : '';
		$meta = substr($whattoimport, strpos($whattoimport, "_") + 1); //yoast_titles, rankmath_titles
		$plugin = substr($whattoimport, 0, strpos($whattoimport, "_"));
		
		//error_log($meta);
		//error_log($plugin);
		
		if($meta == "title") {
			//$this->import_yoast_rm_post_meta($meta, $plugin);
			//$this->import_yoast_rm_post_meta("description", $plugin);
			if($plugin == "yoastnew") {
				$import_error = $this->import_yoast_new_post_meta($meta, $plugin);
				if($import_error) {
				   esc_html_e($import_error);
				   wp_die();
				}
				$this->import_yoast_new_post_meta("description", $plugin);
			} else {				
				$this->import_yoast_rm_post_meta($meta, $plugin);
				$this->import_yoast_rm_post_meta("description", $plugin);
			}
			echo ucwords($plugin). esc_html(' Meta Titles and Descriptions successfully Imported', 'platinum-seo-pack');  
			//echo ucwords($plugin)." Meta Titles and Descriptions successfully Imported";
			wp_die();
		}
		
		if($meta == "basicrobots") {
			$this->import_yoast_rm_post_meta('noindex', $plugin);
			$this->import_yoast_rm_post_meta('nofollow', $plugin);
			echo ucwords($plugin). esc_html(' Basic Robots Data successfully Imported', 'platinum-seo-pack');  
			//echo ucwords($plugin)." Basic Robots Data successfully Imported";	
			wp_die();
		}
		
		if($meta == "robots") {
			
			if($plugin == "yoastnew") {
				$import_error = $this->import_yoast_new_post_meta($meta, $plugin);
				if($import_error) {
				   esc_html_e($import_error);				   
				} else {
					echo ucwords($plugin). esc_html(' All Robots Meta Data successfully Imported', 'platinum-seo-pack');
				}
				wp_die();
			} else {
				$this->import_yoast_rm_post_meta($meta, $plugin);				
			}
			if ($plugin == "rankmath") {
			    $this->import_yoast_rm_post_meta('advrobots', $plugin);				
			}
			echo ucwords($plugin). esc_html(' Advanced Robots Meta Data successfully Imported', 'platinum-seo-pack');  
			//Advanced Robots Meta Data successfully Imported";	
			wp_die();
		}
		
		if($meta == "advrobots") {
			$this->import_yoast_rm_post_meta($meta, $plugin);
			
			echo ucwords($plugin). esc_html(' Advanced Robots Meta Data successfully Imported', 'platinum-seo-pack');  
			//Advanced Robots Meta Data successfully Imported";	
			wp_die();
			
		}
		
		if($meta == "premium") {
			$this->import_yoast_rm_post_meta('focuskeywords', $plugin);
			
			echo ucwords($plugin). esc_html(' Premium Focus Keywords Meta Data successfully Imported', 'platinum-seo-pack'); 
			//echo ucwords($plugin)." Premium Focus Keywords Meta Data successfully Imported";	
			wp_die();
			
		}
		
		if($meta == "others") {
			$import_error = $this->import_yoast_post_meta_others($plugin);	
			
			if($import_error) {
			   esc_html_e($import_error);
			   wp_die();
			} else {
				echo ucwords($plugin). esc_html(' Other SEO and social Data successfully Imported', 'platinum-seo-pack');
				//echo ucwords($plugin)." Other SEO and social Data successfully Imported";
				wp_die();
			}	
		}
		
		if($meta == "terms" && $plugin == "yoast") {
			$import_error = $this->import_yoast_term_meta();
			if($import_error) {
			   esc_html_e($import_error);
			   wp_die();
			} else {
			    esc_html_e('Yoast SEO and social Data for Terms successfully Imported', 'platinum-seo-pack');
			    //echo "Yoast SEO and social Data for Terms successfully Imported";
			    wp_die();
			}
		}
		
		if($meta == "terms" && $plugin == "yoastnew") {
			$import_error = $this->import_yoastnew_term_meta($plugin);
			if($import_error) {
			   esc_html_e($import_error);
			   wp_die();
			} else {
			    esc_html_e('Yoast Indexable SEO and social Data for Terms successfully Imported', 'platinum-seo-pack');
			    //echo "Yoast SEO and social Data for Terms successfully Imported";
			    wp_die();
			}
		}
		
		if($meta == "terms" && $plugin == "rankmath") {
			$import_error = $this->import_rm_term_meta();
			
			if($import_error) {
				esc_html_e($import_error);
				wp_die();
			} else {
			    esc_html_e('RankMath SEO and social Data for Terms successfully Imported', 'platinum-seo-pack');
			    //echo "Yoast SEO and social Data for Terms successfully Imported";
			    wp_die();
			}
		}
		
		if($meta == "terms" && $plugin == "psp") {
			$import_error = $this->import_psp_term_meta();
			
			if($import_error) {
				esc_html_e($import_error);
				wp_die();
			} else {
			    esc_html_e('Platinum SEO SEO and social Data for Terms successfully Imported to new Tables', 'platinum-seo-pack');
			    //echo "Yoast SEO and social Data for Terms successfully Imported";
			    wp_die();
			}
		}
			
		
		//echo "Yoast Data successfully Imported";

		wp_die(); // terminate
	}
	
	private function import_aioseop_post_social_meta($meta = "", $plugin = "") {
	    
	    //error_log("import_yoast_rm_post_meta_1");		
		
		global $wpdb;
					
		$psp_post_meta_tbl = $wpdb->prefix . "postmeta";
		$psp_post_seo_tbl = $wpdb->prefix . "platinumseometa";
		
		$max_metas_per_page = 100;
		$meta_to_import = "";
		
		$counter = 0;
		//While ($counter < 20) {
		$last_post_id = 0;
		$rows_exist = true;	
		
		While ($rows_exist) {	
			
			$meta_to_import = "_aioseop_opengraph_settings";
			
			$yoast_meta_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from {$psp_post_meta_tbl} WHERE post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key in (%s, %s, %s, %s)) ORDER by post_id LIMIT %d", $last_post_id, $meta_to_import, $last_post_id, '_techblissonline_psp_fb_title', '_techblissonline_psp_fb_description', '_techblissonline_psp_fb_image', '_techblissonline_psp_tw_card_type', $max_metas_per_page);
			
			$yoast_metas = $wpdb->get_results($yoast_meta_sql, OBJECT);
			
			if (!$yoast_metas) {				
				
				$rows_exist = false;
				
			} else {
		
    			$values = "";			
    			
    			$psp_values = array();
                $place_holders = array();
                $psp_query = "INSERT INTO {$psp_post_seo_tbl} (platinumseo_id, meta_key, meta_value) VALUES ";
    
    			foreach ($yoast_metas as $yoast_meta) {	
    
    				if($meta == "socialmeta") {
						//rankmath adv robots
						$aioseop_sm_arr = unserialize($yoast_meta->meta_value);
						
						$aioseop_fb_title = ! empty( $aioseop_sm_arr['aioseop_opengraph_settings_title'] ) ? $aioseop_sm_arr['aioseop_opengraph_settings_title'] : "";
						if ( ! empty( $aioseop_fb_title ) ) {
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_fb_title', $aioseop_fb_title);
						}
						
						$aioseop_fb_desc = ! empty( $aioseop_sm_arr['aioseop_opengraph_settings_desc'] ) ? $aioseop_sm_arr['aioseop_opengraph_settings_desc'] : "";
						if ( ! empty( $aioseop_fb_desc ) ) {
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_fb_description', $aioseop_fb_desc);
						}						
						
						$aioseop_fb_image = ! empty( $aioseop_sm_arr['aioseop_opengraph_settings_customimg'] ) ? $aioseop_sm_arr['aioseop_opengraph_settings_customimg'] : ( ! empty( $aioseop_sm_arr['aioseop_opengraph_settings_image'] ) ? $aioseop_sm_arr['aioseop_opengraph_settings_image'] : '' );
						if ( ! empty( $aioseop_fb_image ) ) {
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_fb_image', $aioseop_fb_image);
						}
						
						//$aioseop_tw_cardtype = ! empty( $aioseop_sm_arr['aioseop_opengraph_settings_desc'] ) ? $aioseop_sm_arr['aioseop_opengraph_settings_desc'] : "";
						$aioseop_tw_cardtype = 'summary' === $opengraph_meta['aioseop_opengraph_settings_setcard'] ? 'summary' : 'summary_large_image';
						if ( ! empty( $aioseop_tw_cardtype ) ) {
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_tw_card_type', $aioseop_tw_cardtype);
						}
						
						$aioseop_tw_image = ! empty( $aioseop_sm_arr['aioseop_opengraph_settings_customimg_twitter'] ) ? $aioseop_sm_arr['aioseop_opengraph_settings_customimg_twitter'] : "";
						if ( ! empty( $aioseop_tw_image ) ) {
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_tw_image', $aioseop_tw_image);
						}

						
					}    				
    				
    			}
    			//$values = rtrim($values, ",");
    			//error_log($values);
    			$last_post_id = $yoast_meta->post_id;
    			
    			if ($psp_values) {
    			    $psp_query .= implode(', ', $place_holders);
                    $wpdb->query( $wpdb->prepare("$psp_query", $psp_values));
    			}
    		
    			if ($values) {
    				
    				//$wpdb->query("INSERT INTO {$psp_post_seo_tbl} (post_id, meta_key, meta_value) VALUES {$values}");
    				
    			}
			}
			
		}
		
	}
	
	private function import_yoast_rm_post_meta($meta = "", $plugin = "") {
	    
	    //error_log("import_yoast_rm_post_meta_1");		
		
		global $wpdb;
					
		$psp_post_meta_tbl = $wpdb->prefix . "postmeta";
		$psp_post_seo_tbl = $wpdb->prefix . "platinumseometa";
		
		$max_metas_per_page = 100;
		$meta_to_import = "";
		
		if($plugin == "yoast") {
		
			if($meta == "title") {
			    
				//$meta_to_import = "title";
				$meta_to_import = "_yoast_wpseo_title";
				$psp_meta = "_techblissonline_psp_title";
				
			} else if ($meta == "description") {
				
				//$meta_to_import = "description";
				$meta_to_import = "_yoast_wpseo_metadesc";
				$psp_meta = "_techblissonline_psp_description";
				
			} else if ($meta == "noindex") {
			    
				$meta_to_import = "_yoast_wpseo_meta-robots-noindex";
				$psp_meta = "_techblissonline_psp_noindex";
				
			} else if ($meta == "nofollow") {
			    
				$meta_to_import = "_yoast_wpseo_meta-robots-nofollow";
				$psp_meta = "_techblissonline_psp_nofollow";
				
			} else if ($meta == "focuskeywords") {
			    
				$meta_to_import = "_yoast_wpseo_focuskeywords";
				$psp_meta = "_techblissonline_psp_focuswords";
				
			} else if ($meta == "robots") {
			    
				$meta_to_import = "_yoast_wpseo_meta-robots-adv";
				//$meta_to_import = "robotsmeta";
				$psp_meta = "_techblissonline_psp_advmeta";
				
			}
		}
		if($plugin == "rankmath") {	
		
			if($meta == "title") {				
				$meta_to_import = "rank_math_title";
				//$meta_to_import = "title";
				$psp_meta = "_techblissonline_psp_title";				
			} else if ($meta == "description") {				
				$meta_to_import = "rank_math_description";
				//$meta_to_import = "description";
				$psp_meta = "_techblissonline_psp_description";
			} else if ($meta == "robots") {
				$meta_to_import = "rank_math_robots";
				//$meta_to_import = "robotsmeta";
				$psp_meta = "_techblissonline_psp_advmeta";
			} else if ($meta == "advrobots") {
				$meta_to_import = "rank_math_advanced_robots";				
				$psp_meta = "_techblissonline_psp_advmetarobots";
			}		
		}
		if($plugin == "aioseop") {
		
			if($meta == "title") {
			    
				//$meta_to_import = "title";
				$meta_to_import = "_aioseop_title";
				$psp_meta = "_techblissonline_psp_title";
				
			} else if ($meta == "description") {
				
				//$meta_to_import = "description";
				$meta_to_import = "_aioseop_description";
				$psp_meta = "_techblissonline_psp_description";
				
			} else if ($meta == "noindex") {
			    
				$meta_to_import = "_aioseop_noindex";
				$psp_meta = "_techblissonline_psp_noindex";
				
			} else if ($meta == "nofollow") {
			    
				$meta_to_import = "_aioseop_nofollow";
				$psp_meta = "_techblissonline_psp_nofollow";
				
			} else if ($meta == "focuskeywords") {
			    
				$meta_to_import = "_aioseop_keywords";
				$psp_meta = "_techblissonline_psp_focuswords";
				
			} 
		}
		
		if($plugin == "psp") {
			
			if ($meta == "robots") {
				//$meta_to_import = "rank_math_robots";
				$meta_to_import = "robotsmeta";
				$psp_meta = "_techblissonline_psp_advmeta";
			}
			
		}
		
		$counter = 0;
		//While ($counter < 20) {
		$last_post_id = 0;
		$rows_exist = true;	
			
		// $counter = $counter + 1;   
		While ($rows_exist) {
			
			//$yoast_meta_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from {$psp_post_meta_tbl} WHERE post_id > %d and meta_key = %s ORDER by post_id LIMIT %d", $last_post_id, $meta_to_import, $max_metas_per_page);
			
			if ($psp_meta == "_techblissonline_psp_advmeta") {
			    
			    if($plugin == "yoast") {
			    
			        $yoast_meta_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from {$psp_post_meta_tbl} WHERE post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key in (%s, %s, %s)) ORDER by post_id LIMIT %d", $last_post_id, $meta_to_import, $last_post_id, '_techblissonline_psp_noarchive', '_techblissonline_psp_nosnippet', '_techblissonline_psp_noimageidx', $max_metas_per_page);
			        
			    } else if($plugin == "rankmath") {
			        
			        $yoast_meta_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from {$psp_post_meta_tbl} WHERE post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key in (%s, %s, %s, %s, %s)) ORDER by post_id LIMIT %d", $last_post_id, $meta_to_import, $last_post_id, '_techblissonline_psp_noindex', '_techblissonline_psp_nofollow','_techblissonline_psp_noarchive', '_techblissonline_psp_nosnippet', '_techblissonline_psp_noimageidx', $max_metas_per_page);
			        
			    } else if($plugin == "psp") {
			        
			        $yoast_meta_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from {$psp_post_meta_tbl} WHERE post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key in (%s, %s)) ORDER by post_id LIMIT %d", $last_post_id, $meta_to_import, $last_post_id, '_techblissonline_psp_noindex', '_techblissonline_psp_nofollow',$max_metas_per_page);
			        
			    }
			    
			} else if ($plugin == "rankmath" && $psp_meta == "_techblissonline_psp_advmetarobots") {
			
				$yoast_meta_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from {$psp_post_meta_tbl} WHERE post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key in (%s, %s, %s)) ORDER by post_id LIMIT %d", $last_post_id, $meta_to_import, $last_post_id, '_techblissonline_psp_maxsnippet', '_techblissonline_psp_maxvideo','_techblissonline_psp_maximage', $max_metas_per_page);
			
			} else {
			
				$yoast_meta_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from {$psp_post_meta_tbl} WHERE post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by post_id LIMIT %d", $last_post_id, $meta_to_import, $last_post_id, $psp_meta, $max_metas_per_page);
			
			}
			
			//error_log($yoast_meta_sql);
			
			//$yoast_title_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from $psp_post_meta_tbl WHERE post_id > %d and meta_key in(%s, %s, %s) ORDER by post_id LIMIT %d", $last_post_id, 'title', '_techblissonline_psp_title', '_yoast_wpseo_title', $max_titles_per_page);
			
			//$yoast_meta_sql = $wpdb->prepare("Select post_id, meta_key, meta_value from $psp_post_meta_tbl WHERE post_id > %d and meta_key in (%s, %s, %s) ORDER by post_id LIMIT %d", $last_post_id, $meta_to_import, $max_metas_per_page);
			
			//error_log($yoast_meta_sql);
			
			$yoast_metas = $wpdb->get_results($yoast_meta_sql, OBJECT);
			
			if (!$yoast_metas) {				
				
				$rows_exist = false;
				
			} else {
		
    			$values = "";			
    			
    			$psp_values = array();
                $place_holders = array();
                $psp_query = "INSERT INTO {$psp_post_seo_tbl} (platinumseo_id, meta_key, meta_value) VALUES ";
    
    			foreach ($yoast_metas as $yoast_meta) {	
    
    				if($meta == "description" || $meta == "title") {
    				    
    				    if($plugin == "yoast") {
    				
        					$yoast_meta_value = "";				
        					$yoast_meta_value = preg_replace('/%%[^%]+%%/',"", $yoast_meta->meta_value);
        					$yoast_meta_value = preg_replace('/\s+/', ' ', $yoast_meta_value);
    				    } else if ($plugin == "rankmath") {
    				        
        				    $yoast_meta_value = "";				
        					$yoast_meta_value = preg_replace('/%[^%]+%/',"", $yoast_meta->meta_value);
        					$yoast_meta_value = preg_replace('/\s+/', ' ', $yoast_meta_value);
    				        
    				    } else if ($plugin == "aioseop") {
    				        
        				    $yoast_meta_value = "";				
        					//$yoast_meta_value = preg_replace('/%[^%]+%/',"", $yoast_meta->meta_value);
        					//$yoast_meta_value = preg_replace('/\s+/', ' ', $yoast_meta_value);
							$yoast_meta_value = $yoast_meta->meta_value;
    				        
    				    }
    					//$yoast_meta_value = preg_replace('/\'/', '&#039;', $yoast_meta_value);	
    					//$yoast_meta_value = preg_replace('/\"/', '', $yoast_meta_value);
    				
    				} else if($meta == "noindex") {
						if ( $plugin == "aioseop" && $yoast_meta->meta_value === "on" ) {
							$yoast_meta_value = 'on';
						} else {
							if( $yoast_meta->meta_value == 1) {
								$yoast_meta_value = 'on';
							} 
						}
    				
    				} else if($meta == "nofollow") {
						if ( $plugin == "aioseop" && $yoast_meta->meta_value === "on" ) {
							$yoast_meta_value = 'on';
						} else {
							if( $yoast_meta->meta_value == 1) {
								$yoast_meta_value = 'on';
							} 
						}
    				} else if($meta == "focuskeywords") {
						//if ($plugin == "aioseop") {
						//	$yoast_meta_value = $yoast_meta->meta_value;
						//} else {
							$yoast_meta_value = $yoast_meta->meta_value;
							$yoast_meta_value = json_decode( $yoast_meta_value, true );
							if(!$yoast_meta_value) {
								$yoast_meta_value = implode( ', ', array_map( [ &$this, 'psp_get_focus_keyword' ], $yoast_meta_value ) );
								//$yoast_meta_value = implode( ', ', array_map( array( &$this, 'psp_get_focus_keyword' ), $yoast_meta_value ) );
							}
						//}
					} else if($meta == "robots") {
						
						//$yoast_meta_value = $yoast_meta->meta_value;
						
						if ($plugin == "rankmath" || $plugin == "psp") {
						    
						    if (strpos($yoast_meta->meta_value, 'noindex') !== FALSE) {
							    $place_holders[] = "(%d, %s, %s)";
							    array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_noindex', 'on');
							} /***else {
							    $place_holders[] = "(%d, %s, %s)";
							    array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_noindex', 0);
							}***/
							if (strpos($yoast_meta->meta_value, 'nofollow') !== FALSE) {
							    $place_holders[] = "(%d, %s, %s)";
							    array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_nofollow', 'on');
							} /***else {
							    $place_holders[] = "(%d, %s, %s)";
							    array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_nofollow', 0);
							}***/
						
						}
						
						if (strpos($yoast_meta->meta_value, 'noarchive') !== FALSE) {
						    $place_holders[] = "(%d, %s, %s)";
						    array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_noarchive', 'on');
						}
						if (strpos($yoast_meta->meta_value, 'nosnippet') !== FALSE) {
						    $place_holders[] = "(%d, %s, %s)";
						    array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_nosnippet', 'on');
						}
						if (strpos($yoast_meta->meta_value, 'noimageindex') !== FALSE) {
						    $place_holders[] = "(%d, %s, %s)";
						    array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_noimageidx', 'on');
						}
					} else if($meta == "advrobots") {
						//rankmath adv robots
						$rm_robots_arr = unserialize($yoast_meta->meta_value);
						$place_holders[] = "(%d, %s, %s)";
						array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_maxsnippet', $rm_robots_arr["max-snippet"]);
						$place_holders[] = "(%d, %s, %s)";
						array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_maxvideo', $rm_robots_arr["max-video-preview"]);
						$place_holders[] = "(%d, %s, %s)";
						array_push($psp_values, $yoast_meta->post_id, '_techblissonline_psp_maximage', $rm_robots_arr["max-image-preview"]);						
					}
    				
    				if(!empty($yoast_meta_value)) {
    				
    					//$values =$values. "($yoast_meta->post_id, '$psp_meta', '$yoast_meta_value'),";
    					
    					$place_holders[] = "(%d, %s, %s)";
    					array_push($psp_values, $yoast_meta->post_id, $psp_meta, $yoast_meta_value);
    				
    				}
    				
    			}
    			//$values = rtrim($values, ",");
    			//error_log($values);
    			$last_post_id = $yoast_meta->post_id;
    			
    			if ($psp_values) {
    			    $psp_query .= implode(', ', $place_holders);
                    $wpdb->query( $wpdb->prepare("$psp_query", $psp_values));
    			}
    		
    			if ($values) {
    				
    				//$wpdb->query("INSERT INTO {$psp_post_seo_tbl} (post_id, meta_key, meta_value) VALUES {$values}");
    				
    			}
			}
		}
		
		//}
		
		return '';
	}
	
	private function psp_get_focus_keyword( $focus ) {
		return $focus['keyword'];
	}
	
	private function import_yoast_new_post_meta($meta = "", $plugin = "") {
	    
	    //error_log("import_yoast_new_post_meta");		
		
		global $wpdb;
					
		$psp_post_meta_tbl = $wpdb->prefix . "yoast_indexable";
		$psp_post_seo_tbl = $wpdb->prefix . "platinumseometa";
		
		$wpdb->yoast_indexable = $psp_post_meta_tbl;
		
		if($wpdb->get_var("show tables like '$psp_post_meta_tbl'") != $psp_post_meta_tbl) {
			$import_error = esc_html('Yoast Indexable does not exist.', 'platinum-seo-pack');
			return $import_error;
		}	
		
		$max_metas_per_page = 100;
		$meta_to_import = "";
			
		if($plugin == "yoastnew") {
			
			//$psp_post_meta_tbl = $wpdb->prefix . "yoast_indexable";
		
			if($meta == "title") {
			    
				$meta_to_import = "title";				
				$psp_meta = "_techblissonline_psp_title";
				
			} else if ($meta == "description") {
				
				$meta_to_import = "description";				
				$psp_meta = "_techblissonline_psp_description";
				
			} else if ($meta == "robots") {
			    
				//$meta_to_import = "is_robots_noindex";				
				//$psp_meta = "_techblissonline_psp_advmeta";
				
			}
		}

		$counter = 0;
		
		$last_post_id = 0;
		$rows_exist = true;	
		
		While ($rows_exist) {			
		
			//$yoast_meta_sql = $wpdb->prepare("Select object_id, {$meta_to_import} as meta_value, object_type from {$psp_post_meta_tbl} WHERE object_id > %d and object_type = %s and object_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by object_id LIMIT %d", $last_post_id, 'post', $last_post_id,  $psp_meta, $max_metas_per_page);
			
			if ($meta == "robots") {
			
				$yoast_meta_sql = $wpdb->prepare("Select object_id, is_robots_noindex as noindex, is_robots_nofollow as nofollow, is_robots_noarchive as noarchive, is_robots_nosnippet as nosnippet, is_robots_noimageindex as noimageidx from {$psp_post_meta_tbl} WHERE object_id > %d and object_type = %s and object_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key in (%s, %s, %s, %s, %s)) ORDER by object_id LIMIT %d", $last_post_id, 'post', $last_post_id, '_techblissonline_psp_noindex', '_techblissonline_psp_nofollow','_techblissonline_psp_noarchive', '_techblissonline_psp_nosnippet', '_techblissonline_psp_noimageidx', $max_metas_per_page);
			
			} else {
				$yoast_meta_sql = $wpdb->prepare("Select object_id, {$meta_to_import} as meta_value, object_type from {$psp_post_meta_tbl} WHERE object_id > %d and object_type = %s and object_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by object_id LIMIT %d", $last_post_id, 'post', $last_post_id,  $psp_meta, $max_metas_per_page);
			}
			
			$yoast_metas = $wpdb->get_results($yoast_meta_sql, OBJECT);
			
			if (!$yoast_metas) {				
				
				$rows_exist = false;
				
			} else {
			
				$values = "";			
    			
    			$psp_values = array();
                $place_holders = array();
                $psp_query = "INSERT INTO {$psp_post_seo_tbl} (platinumseo_id, meta_key, meta_value) VALUES ";
				
				foreach ($yoast_metas as $yoast_meta) {	
			
					if($meta == "description" || $meta == "title") {
					
						$yoast_meta_value = "";				
						$yoast_meta_value = preg_replace('/%%[^%]+%%/',"", $yoast_meta->meta_value);
						$yoast_meta_value = preg_replace('/\s+/', ' ', $yoast_meta_value);
					
					} else if ($meta == "robots") {
					
						if ($yoast_meta->noindex) {
							//error_log("noindex ".$yoast_meta->noindex);
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->object_id, '_techblissonline_psp_noindex', 'on');
						
						}
						
						if ($yoast_meta->nofollow) {
						
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->object_id, '_techblissonline_psp_nofollow', 'on');
						
						}
						
						if ($yoast_meta->noarchive) {
						
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->object_id, '_techblissonline_psp_noarchive', 'on');
						
						}
						
						if ($yoast_meta->nosnippet) {
						
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->object_id, '_techblissonline_psp_nosnippet', 'on');
						
						}
						
						if ($yoast_meta->noimageidx) {
						
							$place_holders[] = "(%d, %s, %s)";
							array_push($psp_values, $yoast_meta->object_id, '_techblissonline_psp_noimageidx', 'on');
						
						}
					
					}
					
					if(!empty($yoast_meta_value)) {
    				
    					//$values =$values. "($yoast_meta->post_id, '$psp_meta', '$yoast_meta_value'),";
    					
    					$place_holders[] = "(%d, %s, %s)";
    					array_push($psp_values, $yoast_meta->object_id, $psp_meta, $yoast_meta_value);
    				
    				}
				}
				
				$last_post_id = $yoast_meta->object_id;
    			
    			if ($psp_values) {
    			    $psp_query .= implode(', ', $place_holders);
                    $wpdb->query( $wpdb->prepare("$psp_query", $psp_values));
    			}
			
			}
		
		}
		
		return '';
			
	}
	
	private function import_yoast_post_meta_others($plugin = "") {
		
		global $wpdb;
					
		$psp_post_meta_tbl 	= $wpdb->prefix . "postmeta";
		$psp_post_seo_tbl 	= $wpdb->prefix . "platinumseometa";
		$yoast_post_seo_tbl = $wpdb->prefix . "tempmeta";		
		
		if($plugin == "yoast") {
		    
		    //error_log("yoast");
			
			$yoast_post_meta = array(			
				//'_techblissonline_psp_title' 			=>		'title',
				//'_techblissonline_psp_description' 	=>		'description',
				'_techblissonline_psp_keywords' 		=>		'_yoast_wpseo_focuskw',			
				'_techblissonline_psp_canonical_url' 	=>		'_yoast_wpseo_canonical',			
				'_techblissonline_psp_fb_title' 		=>		'_yoast_wpseo_opengraph-title',
				'_techblissonline_psp_fb_description' 	=>		'_yoast_wpseo_opengraph-description',
				'_techblissonline_psp_fb_image' 		=>		'_yoast_wpseo_opengraph-image',
				'_techblissonline_psp_tw_title' 		=>		'_yoast_wpseo_twitter-title',
				'_techblissonline_psp_tw_description' 	=>		'_yoast_wpseo_twitter-description',
				'_techblissonline_psp_tw_image' 		=>		'_yoast_wpseo_twitter-image',
				'_techblissonline_psp_preferred_term' 	=>		'_yoast_wpseo_primary_category',
				'_techblissonline_psp_bc_title' 		=>		'_yoast_wpseo_bctitle',	
				
			);
		
		};
		
		if($plugin == "yoastnew") {
		    
		    $psp_post_meta_tbl 	= $wpdb->prefix . "yoast_indexable"; //for Yoast 14.0+
			$yoast_primary_term_rbl = $wpdb->prefix . "yoast_primary_term";
			$wpdb->yoast_indexable = $psp_post_meta_tbl;
			$wpdb->yoast_primary_term = $yoast_primary_term_rbl;
			
			if($wpdb->get_var("show tables like '$psp_post_meta_tbl'") != $psp_post_meta_tbl) {
				$import_error = esc_html('Yoast Indexable does not exist.', 'platinum-seo-pack');
				return $import_error;
			}	
		
			$yoast_post_meta = array(			
				//'_techblissonline_psp_title' 			=>		'title',
				//'_techblissonline_psp_description' 	=>		'description',
				'_techblissonline_psp_preferred_term' 	=>		'term_id',
				'_techblissonline_psp_bc_title' 		=>		'breadcrumb_title',	
				'_techblissonline_psp_keywords' 		=>		'primary_focus_keyword',			
				'_techblissonline_psp_canonical_url' 	=>		'canonical',			
				'_techblissonline_psp_fb_title' 		=>		'open_graph_title',
				'_techblissonline_psp_fb_description' 	=>		'open_graph_description',
				'_techblissonline_psp_fb_image' 		=>		'open_graph_image',
				'_techblissonline_psp_tw_title' 		=>		'twitter_title',
				'_techblissonline_psp_tw_description' 	=>		'twitter_description',
				'_techblissonline_psp_tw_image' 		=>		'twitter_image',				
				
			);
		
		};
		
		if($plugin == "rankmath") {
		
			$yoast_post_meta = array(
			
				'_techblissonline_psp_keywords' 		=>		'rank_math_focus_keyword',			
				'_techblissonline_psp_canonical_url' 	=>		'rank_math_canonical_url',			
				'_techblissonline_psp_fb_title' 		=>		'rank_math_facebook_title',
				'_techblissonline_psp_fb_description' 	=>		'rank_math_facebook_description',
				'_techblissonline_psp_fb_image' 		=>		'rank_math_facebook_image',
				'_techblissonline_psp_tw_title' 		=>		'rank_math_twitter_title',
				'_techblissonline_psp_tw_description' 	=>		'rank_math_twitter_description',
				'_techblissonline_psp_tw_image' 		=>		'rank_math_twitter_image',
				'_techblissonline_psp_preferred_term' 	=>		'rank_math_primary_category',
				'_techblissonline_psp_bc_title' 		=>		'rank_math_breadcrumb_title',	
			);
		
		};
		
		if($plugin == "aioseop") {
		    
		    //error_log("yoast");
		
			$yoast_post_meta = array(			
				//'_techblissonline_psp_title' 			=>		'title',
				//'_techblissonline_psp_description' 	=>		'description',
				'_techblissonline_psp_keywords' 		=>		'_aioseop_keywords',			
				'_techblissonline_psp_canonical_url' 	=>		'_aioseop_custom_link',	
				'_techblissonline_psp_nositemap'		=>		'_aioseop_sitemap_exclude',	
				//'_techblissonline_psp_fb_title' 		=>		'_yoast_wpseo_opengraph-title',
				//'_techblissonline_psp_fb_description' 	=>		'_yoast_wpseo_opengraph-description',
				//'_techblissonline_psp_fb_image' 		=>		'_yoast_wpseo_opengraph-image',
				//'_techblissonline_psp_tw_title' 		=>		'_yoast_wpseo_twitter-title',
				//'_techblissonline_psp_tw_description' 	=>		'_yoast_wpseo_twitter-description',
				//'_techblissonline_psp_tw_image' 		=>		'_yoast_wpseo_twitter-image',
				//'_techblissonline_psp_preferred_term' 	=>		'_yoast_wpseo_primary_category',
				//'_techblissonline_psp_bc_title' 		=>		'_yoast_wpseo_bctitle',	
				
			);
		
		};
		
		if($plugin == "psp") {
		
			$yoast_post_meta = array(			
				'_techblissonline_psp_title' 					=>		'_techblissonline_psp_title',				
				'_techblissonline_psp_description' 				=>		'_techblissonline_psp_description',
				'_techblissonline_psp_titleformat' 				=>		'_techblissonline_psp_titleformat',
				'_techblissonline_psp_noindex' 					=>		'_techblissonline_psp_noindex',
				'_techblissonline_psp_nofollow' 				=>		'_techblissonline_psp_nofollow',			
				'_techblissonline_psp_noarchive' 				=>		'_techblissonline_psp_noarchive',			
				'_techblissonline_psp_nosnippet' 				=>		'_techblissonline_psp_nosnippet',
				'_techblissonline_psp_noimageidx' 				=>		'_techblissonline_psp_noimageidx',
				'_techblissonline_psp_maxvideo' 				=>		'_techblissonline_psp_maxvideo',
				'_techblissonline_psp_maximage' 				=>		'_techblissonline_psp_maximage',
				'_techblissonline_psp_schema_string'			=>		'_techblissonline_psp_schema_string',
				'_techblissonline_psp_redirect_to_url' 			=>		'_techblissonline_psp_redirect_to_url',
				'_techblissonline_psp_redirect_status_code' 	=>		'_techblissonline_psp_redirect_status_code',		
				'_techblissonline_psp_preferred_taxonomy' 		=>		'_techblissonline_psp_preferred_taxonomy',			
				'_techblissonline_psp_disable_flags' 			=>		'_techblissonline_psp_disable_flags',
				'_techblissonline_psp_fb_og_type' 				=>		'_techblissonline_psp_fb_og_type',
				'_techblissonline_psp_tw_card_type'				=>		'_techblissonline_psp_tw_card_type',				
				'_techblissonline_psp_keywords' 				=>		'_techblissonline_psp_keywords',			
				'_techblissonline_psp_canonical_url' 			=>		'_techblissonline_psp_canonical_url',			
				'_techblissonline_psp_fb_title' 				=>		'_techblissonline_psp_fb_title',
				'_techblissonline_psp_fb_description' 			=>		'_techblissonline_psp_fb_description',
				'_techblissonline_psp_fb_image' 				=>		'_techblissonline_psp_fb_image',
				'_techblissonline_psp_tw_title' 				=>		'_techblissonline_psp_tw_title',
				'_techblissonline_psp_tw_description' 			=>		'_techblissonline_psp_tw_description',
				'_techblissonline_psp_tw_image' 				=>		'_techblissonline_psp_tw_image',
				'_techblissonline_psp_noarchive' 				=>		'psp_noarchive',
				'_techblissonline_psp_nosnippet' 				=>		'psp_nosnippet',
				'_techblissonline_psp_title' 					=>		'title',
				'_techblissonline_psp_description' 				=>		'description',
				'_techblissonline_psp_keywords' 				=>		'keywords',	
			);
		
		};

		$counter = 1;
		
		foreach($yoast_post_meta as $key => $value) {
		    
		    //$loops = 0;
		    //While ($loops < 100) {
			//
			$counter = $counter + 1;
			
			$last_id = 0;
			$max_rows_to_insert = 1000;
			$rows_exist = true;
			
			//$yoast_sql_$counter = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select post_id, %s, meta_value from {$psp_post_meta_tbl} where post_id > %d and meta_key = %s order by post_id  Limit 1000", $key, $last_id, $value);
			
			While ($rows_exist) {				
			
				//$yoast_sql_{$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select post_id, %s, meta_value from {$psp_post_meta_tbl} where meta_key = %s", $key, $value);
				
				$rows_inserted = 0;
				//error_log("last post id ".$last_id);
				
				//$yoast_sql_{$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select post_id, %s, meta_value from {$psp_post_meta_tbl} where post_id > %d and meta_key = %s ORDER by post_id LIMIT %d", $key, $last_id, $value, $max_rows_to_insert);				
				
				if($plugin == "yoastnew") {
					
					if ($key == '_techblissonline_psp_preferred_term') {
						
						${"yoast_sql_".$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select post_id, %s, term_id from {$yoast_primary_term_rbl} WHERE post_id > %d and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by post_id LIMIT %d", $key, $last_id, $last_id, $key, $max_rows_to_insert);
						
					} else if($key == '_techblissonline_psp_fb_image') {
						
						${"yoast_sql_".$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select object_id, %s, {$value} from {$psp_post_meta_tbl} WHERE {$value} IS NOT NULL and open_graph_image_source = 'set-by-user' and object_id > %d and object_type = %s and object_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by object_id LIMIT %d", $key, $last_id, 'post', $last_id, $key, $max_rows_to_insert);					
						
					}  else if($key == '_techblissonline_psp_tw_image') {
						
						${"yoast_sql_".$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select object_id, %s, {$value} from {$psp_post_meta_tbl} WHERE {$value} IS NOT NULL and twitter_image_source = 'set-by-user' and object_id > %d and object_type = %s and object_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by object_id LIMIT %d", $key, $last_id, 'post', $last_id, $key, $max_rows_to_insert);				
						
					} else {
						${"yoast_sql_".$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select object_id, %s, {$value} from {$psp_post_meta_tbl} WHERE {$value} IS NOT NULL and object_id > %d and object_type = %s and object_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by object_id LIMIT %d", $key, $last_id, 'post', $last_id, $key, $max_rows_to_insert);
					}
					
				} else {
					
					if($plugin == "psp") {
						${"yoast_sql_".$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select post_id, %s, meta_value from {$psp_post_meta_tbl} WHERE (meta_value IS NOT NULL AND meta_value != '' AND meta_value != '0') and post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by post_id LIMIT %d", $key, $last_id, $value, $last_id, $key, $max_rows_to_insert);
					} else {
						${"yoast_sql_".$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select post_id, %s, meta_value from {$psp_post_meta_tbl} WHERE (meta_value IS NOT NULL AND meta_value != '') and post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE platinumseo_id > %d and meta_key = %s) ORDER by post_id LIMIT %d", $key, $last_id, $value, $last_id, $key, $max_rows_to_insert);
					}
					//$yoast_sql_{$counter} = $wpdb->prepare("Insert into {$yoast_post_seo_tbl} (post_id, meta_key, meta_value) Select post_id, %s, meta_value from {$psp_post_meta_tbl} WHERE post_id > %d and meta_key = %s and post_id not in (Select platinumseo_id from {$psp_post_seo_tbl} WHERE  meta_key = %s) ORDER by post_id LIMIT %d", $key, $last_id, $value, $key, $max_rows_to_insert);
				}
				
				//error_log($yoast_sql_{$counter});
			
				$rows_inserted = $wpdb->query(${"yoast_sql_".$counter});
				
				//error_log("rows_inserted ".$rows_inserted);
				
				if (!$rows_inserted) {
					$rows_exist = false;
				} else {
					
					//$max_post_id_query = $wpdb->prepare("SELECT max(post_id) from {$yoast_post_seo_tbl}");
					
					$max_post_id_query = "SELECT max(post_id) from {$yoast_post_seo_tbl}";
				
					$last_id = $wpdb->get_var($max_post_id_query);
					//error_log("last post id ".$last_id);
					
					//$yoast_to_psp_sql = $wpdb->prepare("Insert into {$psp_post_seo_tbl} (post_id, meta_key, meta_value) Select post_id, meta_key, meta_value from {$yoast_post_seo_tbl}");
					
					$yoast_to_psp_sql = "Insert into {$psp_post_seo_tbl} (platinumseo_id, meta_key, meta_value) Select post_id, meta_key, meta_value from {$yoast_post_seo_tbl}";
				
					$wpdb->query($yoast_to_psp_sql);
				
					$wpdb->query("TRUNCATE TABLE {$yoast_post_seo_tbl}");
				
				}				
				
			}
			
			//$loops = $loops + 1;
			
		   // } //loops

			//$wpdb->query("TRUNCATE TABLE {$yoast_post_seo_tbl}");

		}
		
		if($plugin == "aioseop") {
			
			$this->import_aioseop_post_social_meta( "socialmeta", "aioseop");
			
		}
		
		return "";
        
	}

	private function import_yoast_term_meta() {
		
		$yoast_taxonomy_meta = get_option( 'wpseo_taxonomy_meta' );	
		
		$import_error = "";
		
		if ( empty( $yoast_taxonomy_meta ) ) {
		    //$import_error = "No Category or taxonomy terms to import";
			$import_error = esc_html('No Category or taxonomy terms to import', 'platinum-seo-pack');
			return $import_error;
		}
			
		
		//$psp_term_meta_seo_keys = array('_techblissonline_psp_title', '_techblissonline_psp_description', '_techblissonline_psp_keywords', '_techblissonline_psp_noindex', '_techblissonline_psp_canonical_url', '_techblissonline_psp_bctitle' );
		
		$psp_term_meta_seo_keys = array('wpseo_title', 'wpseo_desc', 'wpseo_metadesc', 'wpseo_focuskw', 'wpseo_noindex', 'wpseo_canonical', 'wpseo_bctitle' );
		
		//$psp_term_meta_social_keys = array('_techblissonline_psp_fb_title', '_techblissonline_psp_fb_description', '_techblissonline_psp_fb_image', '_techblissonline_psp_tw_title', '_techblissonline_psp_tw_description', '_techblissonline_psp_tw_image' );
		
		$psp_term_meta_social_keys = array('wpseo_opengraph-title', 'wpseo_opengraph-description', 'wpseo_opengraph-image', 'wpseo_twitter-title', 'wpseo_twitter-description', 'wpseo_twitter-image' );

		$yoast_term_meta = array(
			'wpseo_title'			    	=>	'title', 
			'wpseo_desc'  	            	=>	'description',
			'wpseo_metadesc'  	        	=>	'description',
			'wpseo_focuskw' 		    	=>	'keywords',
			'wpseo_noindex' 		    	=>	'noindex',
			'wpseo_canonical' 	        	=>	'canonical_url',			
			'wpseo_opengraph-title'	    	=>	'fb_title',
			'wpseo_opengraph-description' 	=>	'fb_description',
			'wpseo_opengraph-image'     	=> 	'fb_image',
			'wpseo_twitter-title'       	=> 	'tw_title',
			'wpseo_twitter-description' 	=> 	'tw_description',
			'wpseo_twitter-image'       	=> 	'tw_image',
			'wpseo_bctitle'             	=> 	'bc_title',
		);	
		
		foreach ( $yoast_taxonomy_meta as $taxonomy => $terms) {
		    
		    //error_log($taxonomy);
			
			foreach ( $terms as $term_id => $yoast_term_data ) {				
				
				if ($taxonomy == "category") {
					$taxname = "category";
				} else {
					$taxname = "taxonomy";
				}
				//error_log($taxname);
				//error_log($term_id);
				/***
				if (get_option( "psp_{$taxname}_seo_metas_{$term_id}")) {
				    $import_error = "A few category or taxonomy terms have already been imported and only the remaining, if any, have been imported.";
					//continue;
				}
				***/
				if (get_term_meta( $term_id, "psp_{$taxname}_seo_metas_{$term_id}")) {
					//$import_error = "A few category or taxonomy terms have already been imported and only the remaining, if any, have been imported.";
					$import_error = esc_html('A few category terms have already been imported and only the remaining, if any, have been imported.', 'platinum-seo-pack');
					continue;
				}
			
				
				foreach($yoast_term_data as $key => $term_value) {				
					
					if (in_array($key, $psp_term_meta_seo_keys)) {
				
						//$psp_term_seo_meta[$key] = $yoast_term_data[$value];
						
						if($key == 'wpseo_noindex') {
							if ($term_value == "noindex") {								
								$psp_term_seo_meta[$yoast_term_meta[$key]] = 'on';
							} 					
						} else if ($key == 'wpseo_title') {
							$yoast_title = preg_replace('/%%[^%]+%%/', '', $term_value);
							$yoast_title = preg_replace('/Archives/',"", $yoast_title);
							$yoast_title = preg_replace('/\s+/', ' ', $yoast_title);				
							$psp_term_seo_meta[$yoast_term_meta[$key]] = $yoast_title;
						} else if ($key == 'wpseo_desc') {
							$yoast_description = preg_replace('/%%[^%]+%%/', '', $term_value);
							$yoast_description = preg_replace('/\s+/', ' ', $yoast_description);
							$psp_term_seo_meta[$yoast_term_meta[$key]] = $yoast_description;
						} else {
							$psp_term_seo_meta[$yoast_term_meta[$key]] = $term_value;
						}
							
						
					} else if (in_array($key, $psp_term_meta_social_keys)) {					    
						
						$psp_term_social_meta[$yoast_term_meta[$key]] = $term_value;
						$psp_term_social_meta[$yoast_term_meta[$key]] = $term_value;
						/***
						if ($key == "_techblissonline_psp_tw_images") {
					
							$psp_tw_image = "";
							$psp_tw_image = $yoast_term_data[$value];
							if($psp_tw_image) {
								$psp_term_social_meta[$key] = array("tw_image" => $psp_tw_image);
							}
						
						} else {
							$psp_term_social_meta[$key] = $yoast_term_data[$value];
						}
						***/
					}					
				
				}
								
				$psp_tax_seo_metas = "psp_".$taxname."_seo_metas_".$term_id;
				$psp_tax_social_metas = "psp_".$taxname."_social_metas_".$term_id;
				
				
			    //update_option( $psp_tax_seo_metas, $psp_term_seo_meta, false );				
			    //update_option( $psp_tax_social_metas, $psp_term_social_meta, false );
			    update_term_meta($term_id, $psp_tax_seo_metas, $psp_term_seo_meta);
			    update_term_meta($term_id, $psp_tax_social_metas, $psp_term_social_meta);
				
				
				
				//error_log("seo meta key ".$psp_tax_seo_metas);
				//error_log("seo meta value ".print_r($psp_term_seo_meta, true));
				
				//error_log("social meta key ".$psp_tax_social_metas);
				//error_log("social meta value ".print_r($psp_term_social_meta, true));
				
				
								
			}
		}

		return $import_error;
	}
	
	private function import_rm_term_meta() {
		
		$rm_term_data_arr = array();

		$rm_robots_arr = array();
		$rm_robots = "";
		
		$import_error = "";
		
		//$term_query = new WP_Term_Query();
		
		//error_log("import_rm_term_meta");
		
		$terms = get_terms();
		
		
		
		$rm_term_meta = array(
			'rank_math_title'		    		=>	'title', 
			'rank_math_description'        		=>	'description',			
			'rank_math_focus_keyword' 		   	=>	'keywords',
			'rank_math_robots' 		    		=>	'robots',
			'rank_math_canonical_url'      		=>	'canonical_url',			
			'rank_math_facebook_title'	    	=>	'fb_title',
			'rank_math_facebook_description' 	=>	'fb_description',
			'rank_math_facebook_image'     		=> 	'fb_image',
			'rank_math_twitter_title'      		=> 	'tw_title',
			'rank_math_twitter_description' 	=> 	'tw_description',
			'rank_math_twitter_image'       	=> 	'tw_image',
			'rank_math_breadcrumb_title'        => 	'bc_title',
		);
		
		$psp_term_meta_seo_keys = array('rank_math_title', 'rank_math_description', 'rank_math_focus_keyword', 'rank_math_robots', 'rank_math_canonical_url', 'rank_math_breadcrumb_title' );
		
		$psp_term_meta_social_keys = array('rank_math_facebook_title', 'rank_math_facebook_description', 'rank_math_facebook_image', 'rank_math_twitter_title', 'rank_math_twitter_description', 'rank_math_twitter_image' );
		
		foreach ( $terms as $term ) {
		    
		    //error_log("term id ".$term->term_id);
		    $term_id = $term->term_id;
		    
		    if ($term->taxonomy = "category") {
		        $taxname = "category";
		    } else {
		        $taxname = "taxonomy";
		    }
		
			$rm_term_data_arr = get_term_meta($term_id);
			
			//error_log(print_r($rm_term_data_arr, true));
			
			if(!$rm_term_data_arr) continue;
			/***
			if (get_option( "psp_{$taxname}_seo_metas_{$term_id}")) {
				$import_error = "A few category or taxonomy terms have already been imported and only the remaining, if any, have been imported now.";
				continue;
			}
			***/
			if (get_term_meta( $term_id, "psp_{$taxname}_seo_metas_{$term_id}")) {
				//$import_error = "A few category or taxonomy terms have already been imported and only the remaining, if any, have been imported.";
				$import_error = esc_html('A few category terms have already been imported and only the remaining, if any, have been imported.', 'platinum-seo-pack');
				continue;
			}
			
			foreach($rm_term_data_arr as $key => $value) {
			    
			    $term_value = $value[0];
			    
			    //error_log($key);
					
				if (in_array($key, $psp_term_meta_seo_keys)) {					
					
					if($key == 'rank_math_robots') {
					
						$rm_robots_arr = unserialize($term_value);
						$rm_robots = implode(", ", $rm_robots_arr);
						
						if (strpos($rm_robots, 'noindex') !== FALSE) {
							$psp_term_seo_meta['noindex'] = 'on';
						}
						if (strpos($rm_robots, 'nofollow') !== FALSE) {
							$psp_term_seo_meta['nofollow'] = 'on';
						}						
						if (strpos($rm_robots, 'noarchive') !== FALSE) {
						    $psp_term_seo_meta['noarchive'] = 'on';
						}
						if (strpos($rm_robots, 'nosnippet') !== FALSE) {
						    $psp_term_seo_meta['nosnippet'] = 'on';
						}
						if (strpos($rm_robots, 'noimageindex') !== FALSE) {
						    $psp_term_seo_meta['noimageindex'] = 'on';
						}
					} else if ($key == 'rank_math_advanced_robots') {
					    
					    $rm_adv_robots_arr = unserialize($term_value);
					    if($rm_adv_robots_ar["max-snippet"]) {
					        $psp_term_seo_meta['maxsnippet'] = $rm_adv_robots_ar["max-snippet"];
					    }
					    if($rm_adv_robots_ar["max-video-preview"]) {
					        $psp_term_seo_meta['maxvideo'] = $rm_adv_robots_ar["max-video-preview"];
					    }
					    if($rm_adv_robots_ar["max-image-preview"]) {
					        $psp_term_seo_meta['maximage'] = $rm_adv_robots_ar["max-image-preview"];
					    }
					    
					} else if ($key == 'rank_math_title') {
						$rm_title = preg_replace('/%[^%]+%/', '', $term_value);
						$rm_title = preg_replace('/Archives/',"", $rm_title);
						$rm_title = preg_replace('/\s+/', ' ', $rm_title);				
						$psp_term_seo_meta[$rm_term_meta[$key]] = $rm_title;
					} else if ($key == 'rank_math_description') {
						$rank_math_description = preg_replace('/%[^%]+%/', '', $term_value);
						$rank_math_description = preg_replace('/\s+/', ' ', $rank_math_description);
						$psp_term_seo_meta[$rm_term_meta[$key]] = $rank_math_description;
					} else {
						$psp_term_seo_meta[$rm_term_meta[$key]] = $term_value;
					}
						
					
				} else if (in_array($key, $psp_term_meta_social_keys)) {					    
					
					$psp_term_social_meta[$rm_term_meta[$key]] = $term_value;
					$psp_term_social_meta[$rm_term_meta[$key]] = $term_value;
					
				}					
			
			}

			$psp_tax_seo_metas = "psp_".$taxname."_seo_metas_".$term_id;
			$psp_tax_social_metas = "psp_".$taxname."_social_metas_".$term_id;
			
			//update_option( $psp_tax_seo_metas, $psp_term_seo_meta, false );				
			//update_option( $psp_tax_social_metas, $psp_term_social_meta, false );
			//migrating to term_meta table
			update_term_meta($term_id, $psp_tax_seo_metas, $psp_term_seo_meta);
			update_term_meta($term_id, $psp_tax_social_metas, $psp_term_social_meta);
			
			//error_log("seo meta key ".$psp_tax_seo_metas);
			//error_log("seo meta value ".print_r($psp_term_seo_meta, true));
			
			//error_log("social meta key ".$psp_tax_social_metas);
			//error_log("social meta value ".print_r($psp_term_social_meta, true));
		
		}
		
		return $import_error;
		
	}
	
	private function import_psp_term_meta() {		
		
		$terms = get_terms();
		$import_error = "";
		
		foreach ( $terms as $term ) {
			
			//error_log("term id ".$term->term_id);
			$term_id = $term->term_id;
			$psp_category_seo_data = array();
			$psp_category_social_data = array();
			
			if ($term->taxonomy = "category") {
				$psp_category_seo_data = get_option( "psp_category_seo_metas_$term_id");
				$psp_category_social_data = get_option( "psp_category_social_metas_$term_id");
				
				if(!$psp_category_seo_data && !$psp_category_social_data ) continue;
				
				if (get_term_meta( $term_id, "psp_category_seo_metas_$term_id") && get_term_meta( $term_id, "psp_category_social_metas_$term_id")) {
					//$import_error = "A few category terms have already been imported and only the remaining, if any, have been imported.";
					$import_error = esc_html('A few taxonomy terms have already been imported and only the remaining, if any, have been imported.', 'platinum-seo-pack');
					continue;
				}
				
				if ($psp_category_seo_data) {
					update_term_meta($term_id, "psp_category_seo_metas_$term_id", $psp_category_seo_data);
				}
				
				if ($psp_category_social_data) {
					update_term_meta($term_id, "psp_category_social_metas_$term_id", $psp_category_social_data);
				}
			} else {
				$psp_category_seo_data = get_option( "psp_taxonomy_seo_metas_$term_id");
				$psp_category_social_data = get_option( "psp_taxonomy_social_metas_$term_id");
				
				if(!$psp_category_seo_data && !$psp_category_social_data ) continue;
				
				if (get_term_meta( $term_id, "psp_taxonomy_seo_metas_$term_id") && get_term_meta( $term_id, "psp_taxonomy_social_metas_$term_id")) {
					//$import_error = "A few taxonomy terms have already been imported and only the remaining, if any, have been imported.";
					$import_error = esc_html('A few taxonomy terms have already been imported and only the remaining, if any, have been imported.', 'platinum-seo-pack');
					continue;
				}
				
				if ($psp_category_seo_data) {
					update_term_meta($term_id, "psp_taxonomy_seo_metas_$term_id", $psp_category_seo_data);
				}
				
				if ($psp_category_social_data) {
					update_term_meta($term_id, "psp_taxonomy_social_metas_$term_id", $psp_category_social_data);
				}
			}
			
		
		}
		
		return $import_error;
	}
	
	private function import_yoastnew_term_meta($plugin = "") {
		
		global $wpdb;
		
		$psp_post_meta_tbl 	= $wpdb->prefix . "yoast_indexable"; //for Yoast 14.0+
		$wpdb->yoast_indexable = $psp_post_meta_tbl;	
		
		if($wpdb->get_var("show tables like '$psp_post_meta_tbl'") != $psp_post_meta_tbl) {
			$import_error = esc_html('Yoast Indexable does not exist.', 'platinum-seo-pack');
			return $import_error;
		}	

		$max_metas_per_page = 100;
		$last_id = 0;
		$term_id = 0;
		$rows_exist = true;	

		$import_error = "";

		While ($rows_exist) {

			$yoast_meta_sql = $wpdb->prepare("Select object_id, object_type, object_sub_type, title, description, breadcrumb_title, canonical, primary_focus_keyword, is_robots_noindex, is_robots_nofollow, is_robots_noarchive, is_robots_noimageindex, is_robots_nosnippet, twitter_title, twitter_description, twitter_image, twitter_image_source, open_graph_title, open_graph_description, open_graph_image, open_graph_image_source from {$psp_post_meta_tbl} WHERE object_id > %d and object_type = %s ORDER by object_id LIMIT %d", $last_id, 'term', $max_metas_per_page);
			
			//error_log($yoast_meta_sql);

			$yoast_metas = $wpdb->get_results($yoast_meta_sql, OBJECT);

			if (!$yoast_metas) {				
							
				$rows_exist = false;
				
			} else {		
			
				foreach($yoast_metas as $yoast_meta) {
				
					$term_id = $yoast_meta->object_id;
					
					if ($yoast_meta->object_sub_type == "category") {
					
						if (get_term_meta( $term_id, "psp_category_seo_metas_$term_id") && get_term_meta( $term_id, "psp_category_seo_metas_$term_id")) {
							//$import_error = "A few taxonomy terms have already been imported and only the remaining, if any, have been imported.";
							$import_error = esc_html('A few taxonomy terms have already been imported and only the remaining, if any, have been imported.', 'platinum-seo-pack');
							continue;
						}
					
					} else {

						if (get_term_meta( $term_id, "psp_taxonomy_seo_metas_$term_id") && get_term_meta( $term_id, "psp_taxonomy_social_metas_$term_id")) {
							//$import_error = "A few taxonomy terms have already been imported and only the remaining, if any, have been imported.";
							$import_error = esc_html('A few taxonomy terms have already been imported and only the remaining, if any, have been imported.', 'platinum-seo-pack');
							continue;
						}
					
					}					
					$yoast_title = preg_replace('/%%[^%]+%%/', '', $yoast_meta->title);
					$yoast_title = preg_replace('/Archives/',"", $yoast_title);
					$yoast_title = preg_replace('/\s+/', ' ', $yoast_title);				
					$psp_term_seo_meta['title'] = !empty($yoast_title) ? $yoast_title : '';
			
					$yoast_description = preg_replace('/%%[^%]+%%/', '', $yoast_meta->description);
					$yoast_description = preg_replace('/\s+/', ' ', $yoast_description);
					$psp_term_seo_meta['description'] = !empty($yoast_description) ? $yoast_description : '';
					
					if ($yoast_meta->is_robots_noindex) {
						$psp_term_seo_meta['noindex'] = 'on';			
					}
					if ($yoast_meta->is_robots_nofollow) {
						$psp_term_seo_meta['nofollow'] = 'on';			
					}	
					
					if ($yoast_meta->is_robots_noarchive) {
						$psp_term_seo_meta['noarchive'] = 'on';			
					}
					
					if ($yoast_meta->is_robots_nosnippet) {
						$psp_term_seo_meta['nosnippet'] = 'on';				
					}
					
					if ($yoast_meta->is_robots_noimageindex) {
						$psp_term_seo_meta['noimageindex'] = 'on';			
					}
					
					if ($yoast_meta->twitter_image_source == 'set-by-user') {
						
						$psp_term_social_meta['tw_image'] = $yoast_meta->twitter_image;			
					}
					
					if ($yoast_meta->open_graph_image_source == 'set-by-user') {
						
						$psp_term_social_meta['fb_image'] = $yoast_meta->open_graph_image;			
					}
					
					$psp_term_seo_meta['keywords'] = !empty($yoast_meta->primary_focus_keyword) ? $yoast_meta->primary_focus_keyword : '';			
					$psp_term_seo_meta['canonical_url'] = !empty($yoast_meta->canonical) ? $yoast_meta->canonical : '';
					$psp_term_seo_meta['bc_title'] = !empty($yoast_meta->breadcrumb_title) ? $yoast_meta->breadcrumb_title : '';
					
					$psp_term_social_meta['fb_title'] = !empty($yoast_meta->open_graph_title) ? $yoast_meta->open_graph_title : '';
					$psp_term_social_meta['fb_description'] = !empty($yoast_meta->open_graph_description) ? $yoast_meta->open_graph_description : '';			
					
					$psp_term_social_meta['tw_title'] = !empty($yoast_meta->twitter_title) ? $yoast_meta->twitter_title : '';
					$psp_term_social_meta['tw_description'] = !empty($yoast_meta->twitter_description) ? $yoast_meta->twitter_description : '';

					if ($yoast_meta->object_sub_type == "category") {
					
						update_term_meta($term_id, "psp_category_seo_metas_$term_id", $psp_term_seo_meta);
						update_term_meta($term_id, "psp_category_social_metas_$term_id", $psp_term_social_meta);
					
					} else {			
						
						update_term_meta($term_id, "psp_taxonomy_seo_metas_$term_id", $psp_term_seo_meta);
						update_term_meta($term_id, "psp_taxonomy_social_metas_$term_id", $psp_term_social_meta);
					
					}
				}
				
				$last_id = $term_id;
			
			}
			
		}

		return $import_error;
		
	}
	
}
?>