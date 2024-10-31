<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
class PspRestApi {
	
		private static $obj_handle = null;	
	
		protected $psp_settings_handle;
		protected $psp_sitewide_settings = array();		
		protected $psp_helper;
		protected $psp_social_handle;
		protected $psp_tax_handle;
		protected $psp_pt_handle;
		protected $psp_home_handle;
		
		public $pagenumber = 1;
		
		public $public_taxonomies = array();
		public $public_post_types = array();
		
		const PSP_HEAD_FIELD_NAME = 'psp_head';
		const PSP_META_FOR_URL_ROUTE = 'get_psp_meta';
		const API_V1_NAMESPACE = 'platinumseo/v1';
		const FULL_HEAD_FOR_URL_ROUTE = self::API_V1_NAMESPACE . '/' . self::PSP_META_FOR_URL_ROUTE;
	
		public function __construct() {			
			
			//create settings instance
			$psp_settings_instance = PspSettings::get_instance();
			$this->psp_settings_handle = $psp_settings_instance;
			
			$psp_helper_instance = PspHelper::get_instance();		
			$this->psp_helper = $psp_helper_instance;	

			$psp_social_instance = PspSocialMetas::get_instance();
			$this->psp_social_handle = $psp_social_instance;
			
			$psp_tax_instance = PspTaxSeoMetas::get_instance();
			$this->psp_tax_handle = $psp_tax_instance;
			
			$psp_pt_instance = PspPtsSeoMetas::get_instance();
			$this->psp_pt_handle = $psp_pt_instance;
			
			$psp_ho_instance = PspHomeOthersSeoMetas::get_instance();
			$this->psp_home_handle = $psp_ho_instance;
			
			$psp_settings = array();
			$psp_settings = get_option("psp_sitewide_settings");			
			$this->psp_sitewide_settings = $psp_settings;	
			
			add_action('rest_api_init', array($this, 'register_rest_routes_fields'));			

		}
		
		public static function get_instance() {
	
			if ( null == self::$obj_handle ) {
				self::$obj_handle = new self;
			}
		
			return self::$obj_handle;
		
		} // end get_instance;
		
		public function register_rest_routes_fields() {
			
			$this->psp_register_rest_routes();
			$this->psp_register_rest_fields();
			
		}
		
		public function psp_register_rest_routes() {
			$rest_route_args = [
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_psp_meta' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'url' => [
						'validate_callback' => [ $this, 'is_valid_url' ],
						'required'          => true,
					],
				],
			];
			\register_rest_route( self::API_V1_NAMESPACE, self::PSP_META_FOR_URL_ROUTE, $rest_route_args );
		}
		
		public function get_psp_meta( WP_REST_Request $request ) {
			$url  = \esc_url_raw( $request['url'] );
			$pagenum = $this->psp_get_pagenum('page', $url);
			$psp_tax_instance = $this->psp_tax_handle;
			$psp_pt_instance = $this->psp_pt_handle;
			$psp_home_instance = $this->psp_home_handle;
			
			if ($pagenum > 1) {
				
				$url = $this->psp_get_url_wo_pagination( $url );
				$this->pagenumber = $pagenum;
				//error_log("pagenumber ".$pagenum);
				$this->psp_helper->pagenumber = $pagenum;
				$psp_tax_instance->current_pageno = $pagenum;
				$psp_pt_instance->current_pageno = $pagenum;
				$psp_home_instance->current_pageno = $pagenum;
				  
			}
			$data = $this->get_psp_mets_from_url( $url );

			return new WP_REST_Response( $data, $data->status );
		}
		
		public function psp_get_pagenum($name, $url)
        {
			//$strURL = $_SERVER['REQUEST_URI'];
			$arrVals = explode("/",$url);
			$found = 0;
			foreach ($arrVals as $index => $value) 
			{
				if($value == $name) $found = $index;
			}
			$position = $found + 1;    
			return ($found == 0) ? 1 : $arrVals[$position];
		}
		
		function psp_get_url_wo_pagination( $url ) {
    
			$position = strpos( $url , '/page' );
			$url_wo_pagination = ( $position ) ? substr( $url, 0, $position ) : $url;

			return trailingslashit( $url_wo_pagination );

		}

		
		public function get_psp_mets_from_url( $url ) {
			
			$psp_head_meta = '';
			
			if ( trim( $url, '/' ) === home_url() ) {

				$psp_head_meta = $this->get_psp_meta_for_frontpage();
				if ($psp_head_meta ) {
					return (object) [
						'psp_head'   => $psp_head_meta,
						'status' => 200,
					];
				} else {
					return (object) [
						'psp_head'   => 'No data',
						'status' => 404,
					];
				}
			}
			
			$post_id = $this->psp_url_to_postid( $url );
			
			if ( $post_id ) {		

				$psp_head_meta = $this->get_psp_meta_for_post( $post_id );
				
				if ($psp_head_meta ) {
					return (object) [
						'psp_head'   => $psp_head_meta,
						'status' => 200,
					];
				} else {
					
					return (object) [
						'psp_head'   => 'No data',
						'status' => 404,
					];
					
				}
				
			}
			
			if (!$post_id) {
				//error_log("url ".($url));
				$term_id = $this->psp_url_to_term_id( $url );
				//error_log("termid ".($term_id));
				
				if ( $term_id ) {

					$psp_head_meta = $this->get_psp_meta_for_term( $term_id );
				
					if ( $psp_head_meta ) {
						return (object) [
							'psp_head'   => $psp_head_meta,
							'status' => 200,
						];
					} else {
						return (object) [
							'psp_head'   => 'No data',
							'status' => 404,
						];
					}
					
				}
				
			}
			
			return (object) [
								'psp_head'   => $psp_head_meta,
								'status' => 404,
							];
			
		}
		
		private function psp_url_to_postid( $url ) {
			global $wp_rewrite;
		 
			/**
			 * Filters the URL to derive the post ID from.
			 *
			 * @since 2.2.0
			 *
			 * @param string $url The URL to derive the post ID from.
			 */
			$url = apply_filters( 'psp_url_to_postid', $url );
		 
			$url_host      = str_replace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
			$home_url_host = str_replace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		 
			// Bail early if the URL does not belong to this site.
			if ( $url_host && $url_host !== $home_url_host ) {
				return 0;
			}
		 
			// First, check to see if there is a 'p=N' or 'page_id=N' to match against.
			if ( preg_match( '#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values ) ) {
				$id = absint( $values[2] );
				if ( $id ) {
					return $id;
				}
			}
		 
			// Get rid of the #anchor.
			$url_split = explode( '#', $url );
			$url       = $url_split[0];
		 
			// Get rid of URL ?query=string.
			$url_split = explode( '?', $url );
			$url       = $url_split[0];
		 
			// Set the correct URL scheme.
			$scheme = parse_url( home_url(), PHP_URL_SCHEME );
			$url    = set_url_scheme( $url, $scheme );
		 
			// Add 'www.' if it is absent and should be there.
			if ( false !== strpos( home_url(), '://www.' ) && false === strpos( $url, '://www.' ) ) {
				$url = str_replace( '://', '://www.', $url );
			}
		 
			// Strip 'www.' if it is present and shouldn't be.
			if ( false === strpos( home_url(), '://www.' ) ) {
				$url = str_replace( '://www.', '://', $url );
			}
		 
			if ( trim( $url, '/' ) === home_url() && 'page' === get_option( 'show_on_front' ) ) {
				$page_on_front = get_option( 'page_on_front' );
		 
				if ( $page_on_front && get_post( $page_on_front ) instanceof WP_Post ) {
					return (int) $page_on_front;
				}
			}
		 
			// Check to see if we are using rewrite rules.
			$rewrite = $wp_rewrite->wp_rewrite_rules();
		 
			// Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options.
			if ( empty( $rewrite ) ) {
				return 0;
			}
		 
			// Strip 'index.php/' if we're not using path info permalinks.
			if ( ! $wp_rewrite->using_index_permalinks() ) {
				$url = str_replace( $wp_rewrite->index . '/', '', $url );
			}
		 
			if ( false !== strpos( trailingslashit( $url ), home_url( '/' ) ) ) {
				// Chop off http://domain.com/[path].
				$url = str_replace( home_url(), '', $url );
			} else {
				// Chop off /path/to/blog.
				$home_path = parse_url( home_url( '/' ) );
				$home_path = isset( $home_path['path'] ) ? $home_path['path'] : '';
				$url       = preg_replace( sprintf( '#^%s#', preg_quote( $home_path ) ), '', trailingslashit( $url ) );
			}
		 
			// Trim leading and lagging slashes.
			$url = trim( $url, '/' );
		 
			$request              = $url;
			$post_type_query_vars = array();
		 
			foreach ( get_post_types( array(), 'objects' ) as $post_type => $t ) {
				if ( ! empty( $t->query_var ) ) {
					$post_type_query_vars[ $t->query_var ] = $post_type;
				}
			}
		 
			// Look for matches.
			$request_match = $request;
			foreach ( (array) $rewrite as $match => $query ) {
		 
				// If the requesting file is the anchor of the match,
				// prepend it to the path info.
				if ( ! empty( $url ) && ( $url != $request ) && ( strpos( $match, $url ) === 0 ) ) {
					$request_match = $url . '/' . $request;
				}
		 
				if ( preg_match( "#^$match#", $request_match, $matches ) ) {
		 
					if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
						// This is a verbose page match, let's check to be sure about it.
						$page = get_page_by_path( $matches[ $varmatch[1] ] );
						if ( ! $page ) {
							continue;
						}
		 
						$post_status_obj = get_post_status_object( $page->post_status );
						if ( ! $post_status_obj->public && ! $post_status_obj->protected
							&& ! $post_status_obj->private && $post_status_obj->exclude_from_search ) {
							continue;
						}
					}
		 
					// Got a match.
					// Trim the query of everything up to the '?'.
					$query = preg_replace( '!^.+\?!', '', $query );
		 
					// Substitute the substring matches into the query.
					$query = addslashes( WP_MatchesMapRegex::apply( $query, $matches ) );
		 
					// Filter out non-public query vars.
					global $wp;
					parse_str( $query, $query_vars );
					$query = array();
					foreach ( (array) $query_vars as $key => $value ) {
						if ( in_array( (string) $key, $wp->public_query_vars, true ) ) {
							$query[ $key ] = $value;
							if ( isset( $post_type_query_vars[ $key ] ) ) {
								$query['post_type'] = $post_type_query_vars[ $key ];
								$query['name']      = $value;
							}
						}
					}
		 
					// Resolve conflicts between posts with numeric slugs and date archive queries.
					$query = wp_resolve_numeric_slug_conflicts( $query );
		 
					// Do the query.
					$query = new WP_Query( $query );
					if ( ! empty( $query->posts ) && $query->is_singular ) {
						return $query->post->ID;
					} else {
						return 0;
					}
				}
			}
			return 0;
		}
		
		private function psp_url_to_postid_2( $request_url ) {
			
			global $wpdb;
			$ID = '';
			$slug = '';
			$pspurl = '';
			$exts=array("/",".php",".html",".htm");
			$psp_posts = $wpdb->prefix . "posts"; 
			
			$request_url = apply_filters( 'psp_url_to_postid', $request_url );
			
			$pspurl = filter_var( $request_url, FILTER_VALIDATE_URL, '' );

			if (!$pspurl) {
				return 0;
			}
		 
			$url_host      = str_replace( 'www.', '', parse_url( $pspurl, PHP_URL_HOST ) );
			$home_url_host = str_replace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		 
			// Bail early if the URL does not belong to this site.
			if ( $url_host && $url_host !== $home_url_host ) {
				return 0;
			}			
			
			if ($pspurl) $slug = sanitize_title(basename( $pspurl ));		
			
			// This will also work with PHP version <= 5.x.x 
			foreach( $exts as $ext ) { 
				$slug = str_replace( $ext, "", $slug ); 
				$slug = trim($slug);
			}
			
			if (!$slug) return 0;
			
			if ($slug) {
				$sql  = $wpdb->prepare("SELECT ID FROM $psp_posts WHERE post_name = %s AND post_status = 'publish'", $slug);
				$ID = $wpdb->get_var( $sql );
			}
			//error_log("post id ".$ID);
			if (!$ID) return 0;
			return $ID;
		
		}
		
		private function psp_url_to_term_id ( $url = "" ) {

			if ( empty($url) ) return "";
			
			$url = apply_filters( 'psp_url_to_term_id', $url );
		 
			$url_host      = str_replace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
			$home_url_host = str_replace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		 
			// Bail early if the URL does not belong to this site.
			if ( $url_host && $url_host !== $home_url_host ) {
				return 0;
			}
		
			$slug = basename( $url );
			
			//error_log("slug ".$slug);
			
			//$psp_wp_term = !empty($slug) ? get_term_by('slug', $slug, '', OBJECT) : false;
			$psp_wp_term = !empty($slug) ? $this->get_taxonomy_by_slug($slug) : false;
			
			//error_log("termobject ".print_r($psp_wp_term, "n"));
			
			if (!$psp_wp_term) return false;
			
			return $psp_wp_term->term_id;

		}
		
		public function psp_register_rest_fields() {
			$public_post_types = $this->psp_get_public_post_types();

			foreach ( $public_post_types as $post_type ) {
				\register_rest_field( $post_type, self::PSP_HEAD_FIELD_NAME, [ 'get_callback' => [ $this, 'get_psp_meta_for_post' ] ] );
			}

			$public_taxonomies = $this->psp_get_public_taxonomies();

			foreach ( $public_taxonomies as $taxonomy ) {
				if ( $taxonomy === 'post_tag' ) {
					$taxonomy = 'tag';
				}
				\register_rest_field( $taxonomy, self::PSP_HEAD_FIELD_NAME, [ 'get_callback' => [ $this, 'get_psp_meta_for_term' ] ] );
			}

			\register_rest_field( 'user', self::PSP_HEAD_FIELD_NAME, [ 'get_callback' => [ $this, 'get_psp_meta_for_author' ] ] );

			\register_rest_field( 'type', self::PSP_HEAD_FIELD_NAME, [ 'get_callback' => [ $this, 'get_psp_meta_for_pt_archive' ] ] );
		}		
		
		private function psp_get_public_post_types( $posttypes = 'names' ) {
			return \get_post_types( [ 'public' => true ], $posttypes );
		}
		
		private function psp_get_public_taxonomies( $taxonomies = 'names' ) {
			return \get_taxonomies( [ 'public' => true ], $taxonomies );
		}
		
		public function get_psp_meta_for_frontpage() {
			
			$psp_meta = false;
			$front_page_id = 0;
			
			$psp_home_settings = get_option("psp_home_settings");
			$use_front_page_settings = isset($psp_home_settings['use_front_page']) ? $psp_home_settings['use_front_page'] : '';
			if(!$use_front_page_settings) {
				
				$psp_meta = $this->get_psp_meta_for_home();
				
			} else {
				
				//$front_page_id = get_queried_object_id();
				if (!$front_page_id) $front_page_id = get_option('page_on_front');
				if ($front_page_id) {
					$psp_meta = $this->get_psp_meta_for_post( $front_page_id );
					if ( $psp_meta === false ) {
						return null;
					}	
				}
				
			}
			
			return $psp_meta;;
			
		}
		
		public function get_psp_meta_for_home() {
			
			$seo_title = "";
			$seo_meta_string = "";
			$social_meta_string = "";
			$psp_ho_instance = PspHomeOthersSeoMetas::get_instance();

			$psp_settings = $this->psp_sitewide_settings;
			$canonical = $psp_settings['use_canonical'];						
						
			$seo_meta_string = $psp_ho_instance->get_home_seo_metas($canonical);
			$this->psp_rest_set_home_social_metas($psp_ho_instance);	
			$social_meta_string = $this->psp_social_handle->psp_get_social_metas();
			if (!empty($social_meta_string)) $seo_meta_string .= "\r\n".$social_meta_string;
			
			if (empty($seo_title)) $seo_title = $psp_ho_instance->get_home_psp_title();	
			return '<title>'. $seo_title. '</title>'. "\r\n" .$seo_meta_string;			
		}
		
		public function get_psp_meta_for_post( $request = null ) {

			 $psp_meta = false;
			 
			 if ( is_array( $request ) ) {
			
				$psp_meta = $this->get_rest_psp_pt_data( $request['id'] );
				
			 } else {
				 
				$psp_meta = $this->get_rest_psp_pt_data( $request );
				 
			 }

			if ( $psp_meta === false ) {
				return null;
			}			

			return $psp_meta;			
		}
		
		public function get_psp_meta_for_term( $request = null ) {	

			$psp_meta = false;
			
			if ( is_array( $request ) ) {
				$psp_meta = $this->get_rest_psp_term_data( $request['id'] );
			} else {
				$psp_meta = $this->get_rest_psp_term_data( $request );
			}

			if ( $psp_meta === false ) {
				return null;
			}			

			return $psp_meta;			
		}
		
		public function get_psp_meta_for_pt_archive( $request ) {

			$psp_post_type_obj = get_post_type_object( $request['slug'] );
			
			//if(! $psp_post_type_obj['has_archive']) {
			//	return null;
			//}
			
			if($request['slug'] === 'post') {
				$id_of_posts_page = (int) \get_option( 'page_for_posts' );	
				if ($id_of_posts_page) {
					$psp_meta = $this->get_psp_meta_for_post( $id_of_posts_page );
					if ( ! $psp_meta ) {
						return null;
					}	
					return $psp_meta;
				}	
			}
			
			$psp_meta = $this->get_rest_psp_ptarchives_data( $request['slug'] );

			if ( $psp_meta === false ) {
				return null;
			}			

			return $psp_meta;			
		}
		
		public function get_psp_meta_for_author( $request ) {			
			
			$psp_meta = $this->get_rest_psp_author_data( $request['id'] );

			if ( $psp_meta === false ) {
				return null;
			}			

			return $psp_meta;			
		}

		public function get_rest_psp_pt_data($post_id = 0) {

			if (empty($post_id)) return "";

			$psp_settings = $this->psp_sitewide_settings;
			$canonical = $psp_settings['use_canonical'];
			
			$post = get_post($post_id);
			
			//$psp_pt_instance = PspPtsSeoMetas::get_instance();
			$psp_pt_instance = $this->psp_pt_handle;
			
			$seo_meta_string = $psp_pt_instance->get_pt_seo_metas($post, $canonical);
			
			$this->psp_rest_set_post_social_metas($psp_pt_instance, $post);
			$this->psp_social_handle->psp_set_post_image($post);
			$social_meta_string = $this->psp_social_handle->psp_get_social_metas();
			if (!empty($social_meta_string)) $seo_meta_string .= "\r\n".$social_meta_string;
			
			$seo_title = $psp_pt_instance->get_pt_psp_title($post);	
			
			return '<title>'.$seo_title. "</title>"."\r\n" .$seo_meta_string;

		} // end get_rest_psp_pt_data;	


		public function get_rest_psp_term_data($term_id = 0) {
		    
		    //error_log("termid ".($term_id));

			if (empty($term_id)) return "";
			
			$seo_title = '';
			$seo_meta_string = '';
			$taxonomy = '';	
			
			//$psp_tax_instance = PspTaxSeoMetas::get_instance();
			$psp_tax_instance = $this->psp_tax_handle;
			
			$term = get_term( $term_id );	
			
			if ( $term ) {
				
				$taxonomy = trim($term->taxonomy);

			}	

			$psp_settings = $this->psp_sitewide_settings;
			$canonical = $psp_settings['use_canonical'];
			
			$seo_meta_string = $psp_tax_instance->get_tax_seo_metas_for_rest( $term_id, $canonical, $taxonomy);
			
			$this->psp_rest_set_tax_social_metas($psp_tax_instance, $term_id, $taxonomy);	
			$social_meta_string = $this->psp_social_handle->psp_get_social_metas();		
			if (!empty($social_meta_string)) $seo_meta_string .= $social_meta_string;
			$seo_title = $psp_tax_instance->get_tax_psp_title_for_rest($term_id, $taxonomy);	
			
			return '<title>'.$seo_title. "</title>"."\r\n" .$seo_meta_string;
		}	

		public function get_rest_psp_ptarchives_data($type = '') {

			if (!$type) return "";
			
			$seo_title = "";
			$seo_meta_string = "";
			$social_meta_string = "";
			$psp_ho_instance = PspHomeOthersSeoMetas::get_instance();
			$psp_pt_instance = PspPtsSeoMetas::get_instance();
			
			$post_type_archive_exists = get_post_type_archive_link($type);
			
			if (!$post_type_archive_exists) return false;
			
			$seo_title = $psp_ho_instance->get_pt_archive_psp_title($type);
			
			$psp_settings = $this->psp_sitewide_settings;
			$canonical = $psp_settings['use_canonical'];
			
			if ( class_exists( 'WooCommerce' ) ) {
				$shop_page = get_page_by_path( 'shop' );
				if (isset($shop_page) && !empty($shop_page)) {					
					$seo_meta_string = $psp_pt_instance->get_pt_seo_metas($shop_page, $canonical);
					$this->psp_rest_set_post_social_metas($psp_pt_instance, $shop_page);
					$this->psp_social_handle->psp_set_post_image($shop_page);
					$social_meta_string = $this->psp_social_handle->psp_get_social_metas();
					if(!empty($social_meta_string)) $seo_meta_string .= "\r\n".$social_meta_string;
				}
			} else {
				$seo_meta_string = $psp_ho_instance->get_pt_archives_seo_metas($canonical, $type);
				$this->psp_rest_set_ptarchives_social_metas($psp_ho_instance, $type);
			}
			
			//return $seo_title. "\r\n" .$seo_meta_string;
			return '<title>'.$seo_title. "</title>"."\r\n" .$seo_meta_string;

		}
		
		public function get_rest_psp_author_data($user_id) {
			
			$seo_title = '';
			$seo_meta_string = '';
			
			$psp_ho_instance = PspHomeOthersSeoMetas::get_instance();

			$psp_settings = $this->psp_sitewide_settings;
			$canonical = $psp_settings['use_canonical'];
			
			$seo_title = $psp_ho_instance->get_author_archive_psp_title($user_id);

			$seo_meta_string = $psp_ho_instance->get_pt_archives_seo_metas($canonical);
			//return $seo_title. "\r\n" .$seo_meta_string;
			return '<title>'.$seo_title. "</title>"."\r\n" .$seo_meta_string;
			
		}
		
		//set social metas for home for rest api calls
		public function psp_rest_set_home_social_metas($psp_type_instance) {		    
			
			$seo_title = $psp_type_instance->get_home_psp_title();
			$this->seo_title = $seo_title;
			$this->psp_social_handle->psp_seo_title = $psp_type_instance->home_title;
			$this->psp_social_handle->psp_seo_description = $psp_type_instance->home_description;
			$this->psp_social_handle->psp_can_link = $psp_type_instance->home_can_link;
			$this->psp_social_handle->psp_type = "Website";
			//$this->psp_can_link = $psp_type_instance->home_can_link;
		}
		
		//set social metas for taxonomies for rest api calls
		public function psp_rest_set_tax_social_metas($psp_type_instance, $term_id, $tax_name) {
		    
		    $psp_tax_instance = $this->psp_tax_handle;
			
			$seo_title = $psp_tax_instance->get_tax_psp_title_for_rest($term_id, $tax_name);
			
			//$this->seo_title = $seo_title;
			$this->psp_social_handle->psp_social_metas = $psp_type_instance->term_social_meta;
			$this->psp_social_handle->psp_seo_title = $psp_type_instance->taxonomy_title;
			$this->psp_social_handle->psp_seo_description = $psp_type_instance->taxonomy_description;
			$this->psp_social_handle->psp_can_link = $psp_type_instance->taxonomy_can_link;
			$this->psp_social_handle->psp_type = $psp_type_instance->taxonomy_name;
			//$this->psp_can_link = $psp_type_instance->taxonomy_can_link;
		}

		public function psp_rest_set_post_social_metas($psp_type_instance, $post) {

			$seo_title = $psp_type_instance->get_pt_psp_title($post);
			
			//$this->seo_title = $seo_title;
			$this->psp_social_handle->psp_social_metas = $psp_type_instance->psp_current_ptype_social_meta;
			$this->psp_social_handle->psp_seo_title = $psp_type_instance->post_type_title;
			$this->psp_social_handle->psp_seo_description = $psp_type_instance->post_type_description;
			$this->psp_social_handle->psp_can_link = $psp_type_instance->post_type_can_link;
			$this->psp_social_handle->psp_type = $psp_type_instance->post_type_name;
			//$this->psp_can_link = $psp_type_instance->post_type_can_link;

		}
		
		public function psp_rest_set_ptarchives_social_metas ($psp_type_instance, $type) {
			
			$seo_title = $psp_type_instance->get_pt_archive_psp_title($type);
			//$this->seo_title = $seo_title;
			$this->psp_social_handle->psp_seo_title = $seo_title;
			$this->psp_social_handle->psp_can_link = $psp_type_instance->archive_can_link;
			//$this->psp_can_link = $psp_type_instance->archive_can_link;
			
		}		
		
		public function is_valid_url( $url ) {
			if ( \filter_var( $url, \FILTER_VALIDATE_URL ) === false ) {
				return false;
			}
			return true;
		}
		
		public function get_taxonomy_by_slug($term_slug){
			$term_object = false;
			$taxonomies = get_taxonomies();
			foreach ($taxonomies as $tax_type_key => $taxonomy) {
				// If term object is returned, break out of loop. (Returns false if there's no object);
				if ($term_object = get_term_by('slug', $term_slug, $taxonomy)) {
					break;
				}else{
					$term_object = false;
				}
			}
			return $term_object;
		}

}