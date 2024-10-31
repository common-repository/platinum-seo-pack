<?php

/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: SiteMap Management class
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/ 
*/

class PspSitemap {

	private static $obj_handle = null;
	private $psp_helper;
	private $psp_sitemap_settings;
	private $psp_excluded_post_ids = array();
	private $psp_excluded_term_ids = array();
	private $stylesheet;

	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;	
	
	function __construct() {

		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;
		
		// The limit for how many sitemaps to include in an index.
		if ( ! defined( 'WP_SITEMAPS_MAX_SITEMAPS' ) ) {
			define( 'WP_SITEMAPS_MAX_SITEMAPS', 50000 );
		}
		
		// WP Sitemaps rewrite version
		if ( ! defined( 'WP_SITEMAPS_REWRITE_VERSION' ) ) {
			define( 'WP_SITEMAPS_REWRITE_VERSION', '2020-07-13' );
		}
		
		// Limit the number of URLs included in a sitemap.
		if ( ! defined( 'WP_SITEMAPS_MAX_URLS' ) ) {
			define( 'WP_SITEMAPS_MAX_URLS', 2000 );
		}
		
		$psp_sm_settings = get_option('psp_sitemap');
		$this->psp_sitemap_settings = $psp_sm_settings;		
		
		$psp_wp_sitemaps_enabled = isset($psp_sm_settings['enable']) ? $psp_sm_settings['enable'] : '';		

		require_once __DIR__ . '/inc/class-wp-sitemaps.php';
		require_once __DIR__ . '/inc/class-wp-sitemaps-provider.php';
		require_once __DIR__ . '/inc/class-wp-sitemaps-index.php';
		require_once __DIR__ . '/inc/class-wp-sitemaps-registry.php';
		require_once __DIR__ . '/inc/class-wp-sitemaps-renderer.php';
		require_once __DIR__ . '/inc/class-wp-sitemaps-stylesheet.php';
		require_once __DIR__ . '/inc/providers/class-wp-sitemaps-posts.php';
		require_once __DIR__ . '/inc/providers/class-wp-sitemaps-taxonomies.php';
		require_once __DIR__ . '/inc/providers/class-wp-sitemaps-users.php';
		require_once __DIR__ . '/inc/functions.php';
		
		if (!$psp_wp_sitemaps_enabled) return;
				
		
		add_filter('psp_sitemaps_stylesheet_index_url', array($this, 'psp_get_sitemaps_stylesheet_index_url'), 10, 1);
		add_filter('psp_sitemaps_stylesheet_url', array($this, 'psp_get_sitemaps_stylesheet_url'), 10, 1);
		
		
		$excluded_post_ids = array();
		$excluded_term_ids = array();
		
		$psp_excluded_post_ids = isset($psp_sm_settings['excluded_post_ids']) ? $psp_sm_settings['excluded_post_ids'] : '';
		$excluded_post_ids = explode(",", $psp_excluded_post_ids);
		if( !empty( $excluded_post_ids ) ) $excluded_post_ids = array_map( 'absint', $excluded_post_ids );
		$this->psp_excluded_post_ids = $excluded_post_ids;
		
		$excluded_term_ids = isset($psp_sm_settings['excluded_term_ids']) ? $psp_sm_settings['excluded_term_ids'] : '';
		$excluded_term_ids = explode(",", $excluded_term_ids);
		if( !empty( $excluded_term_ids ) ) $excluded_term_ids = array_map( 'absint', $excluded_term_ids );
		$this->psp_excluded_term_ids = $excluded_term_ids;
		
		//psp sitemap disabled
		//add_filter( 'pre_handle_404', array( $this, 'psp_redirect_sitemapxml' ), 9, 2 );		
		add_filter( 'psp_sitemaps_register_providers', array( $this, 'psp_sitemap_providers' ), 10, 1 );
		add_filter( 'psp_sitemaps_max_urls', array( $this, 'psp_sitemap_max_urls' ), 10, 2 );
		
		add_filter('psp_sitemaps_is_enabled', array($this, 'psp_enable_wp_sitemaps'), 10, 1);
		add_filter('psp_sitemaps_taxonomies', array($this, 'psp_taxonomies_for_sitemap'), 10, 1);
		add_filter('psp_sitemaps_post_types', array($this, 'psp_post_types_for_sitemap'), 10, 1);
		
		//add_filter('core_sitemaps_posts_list', array($this, 'psp_posts_for_sitemap'), 10, 3);
		//add_filter('core_sitemaps_terms_list', array($this, 'psp_terms_for_sitemap'), 10, 3);
		
		add_filter('psp_sitemaps_posts_query_args', array($this, 'psp_posts_args_for_sitemap'), 10, 2);
		add_filter('psp_sitemaps_taxonomies_query_args', array($this, 'psp_terms_args_for_sitemap'), 10, 2);
		
		add_filter('psp_sitemaps_posts_entry', array($this, 'psp_post_entry_for_sitemap'), 10, 3);
		
		add_filter('psp_sitemaps_taxonomies_entry', array($this, 'psp_term_entry_for_sitemap'), 10, 3);
		
		add_filter('psp_sitemaps_get_sitemap_xml', array($this, 'psp_get_sitemap_xml'), 10, 4);

		// Boot the sitemaps system.
		remove_action( 'init', 'wp_sitemaps_get_server' );	
		add_action( 'init', 'psp_sitemaps_get_server' );	
		
	}
	
	public function psp_enable_wp_sitemaps($enabled) {
		
		$psp_sm_settings = $this->psp_sitemap_settings;
		$psp_wp_sitemaps_enabled = isset($psp_sm_settings['enable']) ? $psp_sm_settings['enable'] : '';
		
		if ( $psp_wp_sitemaps_enabled ) {
			return 'enabled';
		} else {
			return false;
		}	
			
	}
	
	public function psp_sitemap_providers($providers) {
		
		$psp_sm_settings = $this->psp_sitemap_settings;
		$psp_author_sitemaps_enabled = isset($psp_sm_settings['enable_authors']) ? $psp_sm_settings['enable_authors'] : '';
		
		if ( $psp_author_sitemaps_enabled ) {
			return $providers;
		} else {
			return array(
				'posts'      => new PSP_Sitemaps_Posts(),
				'taxonomies' => new PSP_Sitemaps_Taxonomies(),				
			);
		}	
			
	}
	
	public function psp_sitemap_max_urls($wp_sitemap_max_urls, $object_type) {
		
		$psp_sm_settings = $this->psp_sitemap_settings;
		$psp_sitemaps_max_urls = isset($psp_sm_settings['max_urls']) ? $psp_sm_settings['max_urls'] : '';
		
		if ( $psp_sitemaps_max_urls ) {
			return (int) $psp_sitemaps_max_urls;
		} else {
			return (int) $wp_sitemap_max_urls;
		}	
			
	}
	
	public function psp_taxonomies_for_sitemap($taxonomies) {
		
		$psp_sm_settings = $this->psp_sitemap_settings;
		$psp_taxonomies_for_sm = isset($psp_sm_settings['taxonomies_list']) ? $psp_sm_settings['taxonomies_list'] : '';
		
		$psp_taxonomies = array();
		
		if (!empty( $psp_taxonomies_for_sm )) {
			
			foreach($taxonomies as $key => $val) {	
			    if (in_array($key, $psp_taxonomies_for_sm)) {
					$psp_taxonomies[$key] = $val;
				}	
			}	
			
			return $psp_taxonomies;
		}
		
		return $psp_taxonomies;
			
	}
	
	public function psp_post_types_for_sitemap($post_types) {
		
		$psp_sm_settings = $this->psp_sitemap_settings;
		$psp_post_types_for_sm = isset($psp_sm_settings['posttypes_list']) ? $psp_sm_settings['posttypes_list'] : '';
		
		$psp_post_types = array();
		
		if (!empty( $psp_post_types_for_sm )) {
			foreach($post_types as $key => $val) {	
			    if (in_array($key, $psp_post_types_for_sm)) {
					$psp_post_types[$key] = $val;
				}	
			}
			return $psp_post_types;
		}
		
		return $post_types;
		
	}
	
	public function psp_posts_args_for_sitemap($posts_args, $post_type) {
		
		$psp_excluded_post_ids = array();
		$psp_excluded_post_ids = $this->psp_excluded_post_ids;
		$psp_noindex_post_ids  = array();
		
		$psp_sm_settings = $this->psp_sitemap_settings;
		
		$psp_posts_args = array(
			    'fields'                 => 'ids',
				'orderby'                => 'ID',
				//'order'                  => 'DESC',	
				'order'                  => empty($psp_sm_settings['sort_order']) ? 'DESC' : 'ASC' ,		
				'post_type'              => $post_type,				
				'post_status'            => array( 'publish' ),				
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			);
			
		$psp_posts_args['posts_per_page'] = !empty($posts_args['posts_per_page']) ? $posts_args['posts_per_page'] : psp_sitemaps_get_max_urls( 'post' );

		
		//Exclude NoIndex Posts
		$psp_noindex_post_ids = $this->psp_get_noindex_posts();
		if ( !empty($psp_excluded_post_ids) ) {
			
			$psp_excluded_post_ids = array_unique (array_merge ($psp_excluded_post_ids, $psp_noindex_post_ids));			
		
		}
		
		if ($psp_excluded_post_ids) {
		
			$psp_posts_args['post__not_in'] = $psp_excluded_post_ids;
		
		}
		
		/***
		$psp_posts_args['meta_query'] => array(
												'relation' => 'OR',
												array(
													'key' => '_techblissonline_psp_noindex',
													'compare' => 'NOT EXISTS'
												),
												array(
													'key' => '_techblissonline_psp_noindex',
													'compare' => '!=',
													'value' => 'on'
												),
											),
		 ***/
		return $psp_posts_args;			
		
	}
	
	public function psp_terms_args_for_sitemap($terms_args, $taxonomy) {			
		
		$psp_excluded_term_ids = $this->psp_excluded_term_ids;
		
		$psp_terms_args = 	array(
							'fields'                 => 'ids',
							'taxonomy'               => $taxonomy,
							'orderby'                => 'term_order',
							//'number'                 => wp_sitemaps_get_max_urls( $this->object_type ),
							'hide_empty'             => true,
							'hierarchical'           => false,
							'update_term_meta_cache' => false,
						);
						
		$psp_terms_args['number'] = !empty($terms_args['number']) ? $terms_args['number'] : psp_sitemaps_get_max_urls( 'taxonomy' );
		
		//Exclude noindex terms
		
		$terms = get_terms( array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
		) );
		
		//$noindex_term_ids = array();

		foreach ( $terms as $term ) {

			$term_id = $term->term_id;
			
			if ( "category" === $term->taxonomy )  {
			
				$taxname = "category";				
				
			} else {
			
				$taxname = "taxonomy";
				
			}
			
			$psp_tax_seo_metas = "psp_".$taxname."_seo_metas_".$term_id;
			$term_meta = get_term_meta($term_id, $psp_tax_seo_metas);
			
			if ($term_meta) $term_meta = $term_meta[0]; 
			if (!$term_meta) $term_meta = get_option( $psp_tax_seo_metas );
			if ( !empty($term_meta['noindex']) ) {
				if ( empty($psp_excluded_term_ids) || !in_array( $term_id, $psp_excluded_term_ids ) ) {
					//$noindex_term_ids[] = $term_id;
					$psp_excluded_term_ids[] = $term_id;
				}
			}
			
		}
		
		if ($psp_excluded_term_ids) {
		
			$psp_terms_args['exclude'] = $psp_excluded_term_ids;
		
		}

		return $psp_terms_args;	
		
	}
	
	public function psp_post_entry_for_sitemap($post_sm_attributes = array(), $post, $post_type) {		
		
		 $psp_sm_settings = $this->psp_sitemap_settings;
		 
		$psp_lastmod_sitemaps_enabled = isset($psp_sm_settings['include_lastmod']) ? $psp_sm_settings['include_lastmod'] : ''; 
		
		if ($psp_lastmod_sitemaps_enabled) {
	        $post_sm_attributes['lastmod'] = $this->psp_get_last_modified_time( $post )->format('Y-m-d\TH:i:sP');
		}
	    
		$psp_image_sitemaps_enabled = isset($psp_sm_settings['include_images']) ? $psp_sm_settings['include_images'] : '';
		
		if ($psp_image_sitemaps_enabled) {
    	     $images = $this->psp_get_images( $post );
    	     
    	     if ($images) {
    	         
    	         $post_sm_attributes["image"] = $images;
    	         
        	 }
		}
		return $post_sm_attributes;
		
	}
	
	public function psp_term_entry_for_sitemap($term_sm_attributes = array(), $term, $taxonomy) {		
		
		//$post_sm_attributes['lastmod'] = $this->psp_get_last_modified_time( $post )->format('Y-m-d h:m:s');	
		return $term_sm_attributes;			
		
	}
	
	public function psp_get_last_modified_time( $post ) {
        //get the local time of the current post in seconds
        $postTime = get_post_time( 'U', true, $post ); 
    
        //get the time when the post was last modified
        $postModifiedTime = get_post_modified_time( 'U', true, $post );  
    
        if ($postModifiedTime >= $postTime + 86400) { 
            return get_post_datetime( $post, 'modified', 'gmt'  );  
        } else {
    		return get_post_datetime( $post, 'date', 'gmt'  );
    	} 
    }
    
    public function psp_get_images( $post ) {
         //get the local time of the current post in seconds
        /***
    	$attachments = get_posts( array(
    		'post_type' => 'attachment',
    		'posts_per_page' => -1,
    		'post_parent' => $post,             
    	) );
    	***/
    	//get images
    	$attachments = get_children(array('post_parent' => $post,
                            'post_status' => 'inherit',
                            'post_type' => 'attachment',
                            'post_mime_type' => 'image',
                            'order' => 'ASC',
                            'orderby' => 'menu_order ID'));
                            
    	
    	$psp_images = array();	
    	if ( $attachments ) {
    		
    		foreach($attachments as $att_id => $attachment) {
    		    $full_img_url = "";
    		    $imagedet = array();
    			$full_img_url = wp_get_attachment_url($attachment->ID);
    			$full_image_title = get_the_title($attachment->ID);
    			$full_image_caption = wp_get_attachment_caption($attachment->ID);
    			if ($full_img_url) {
    			    $imagedet['loc'] = $full_img_url;
				}
				if ($full_image_title) {
    			    $imagedet['title'] = $full_image_title;
				}
				if ($full_image_caption) {
    			    $imagedet['caption'] = $full_image_caption;
				}
                $psp_images[] = $imagedet;
			}
		
		}
		
		return $psp_images;
    
    }
	
	private function psp_get_noindex_posts () {
		
		global $wpdb;
					
		$psp_meta_tbl = $wpdb->prefix . "platinumseometa";	
		$psp_meta_key = "_techblissonline_psp_noindex";
		$psp_meta_value = "on";
		$posts_list = array();
		
		$sql_posts_ids = $wpdb->prepare("SELECT platinumseo_id FROM $psp_meta_tbl WHERE meta_key = %s and meta_value = %s", $psp_meta_key, $psp_meta_value );
		$posts_list = $wpdb->get_col($sql_posts_ids);
		
		return $posts_list;
	}
	
	public function psp_get_sitemaps_stylesheet_url( $sitemap_url = '' ) {
        //error_log (plugins_url( '/sitemap.xsl', __FILE__ ));
        //return $sitemaps_stylesheet_url;
        $psp_stylesheet_url = plugins_url( '/sitemap.xsl', __FILE__ );
        
        if($psp_stylesheet_url) {
            //$this->stylesheet = $psp_stylesheet_url;
    	    return plugins_url( '/sitemap.xsl', __FILE__ );
        } else {
            return $sitemaps_stylesheet_url;
        }
    	
    }
	
	public function psp_get_sitemaps_stylesheet_index_url( $sitemaps_stylesheet_url = '' ) {
        //error_log (plugins_url( '/sitemap-index.xsl', __FILE__ ));
        //return $sitemaps_stylesheet_url;
        $psp_stylesheet_url = plugins_url( '/sitemap-index.xsl', __FILE__ );
        if($psp_stylesheet_url) {
    	    return plugins_url( '/sitemap-index.xsl', __FILE__ );
        } else {
            return $sitemaps_stylesheet_url;
        }
    	
    }
    
    /**
	 * Gets XML for a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param array $url_list A list of URLs for a sitemap.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function psp_get_sitemap_xml( $sitemap_xml = '', $url_list, $object_type, $object_subtype ) {		
		
		if (!$url_list) {
			
			return $sitemap_xml;
			
		}
	    
	    $psp_sm_settings = $this->psp_sitemap_settings;
	    
	    $psp_lastmod_sitemaps_enabled = isset($psp_sm_settings['include_lastmod']) ? $psp_sm_settings['include_lastmod'] : '';
	    
		$psp_image_sitemaps_enabled = isset($psp_sm_settings['include_images']) ? $psp_sm_settings['include_images'] : '';
	    
	    if ( 'post' === $object_type ) {
	        
	         $psp_stylesheet_url = plugins_url( '/sitemap.xsl', __FILE__ );
                $this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $psp_stylesheet_url ) . '" ?>'; 
	        
	        if ($psp_lastmod_sitemaps_enabled) {
	        
    	        $psp_stylesheet_url = plugins_url( '/sitemap-post.xsl', __FILE__ );
                $this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $psp_stylesheet_url ) . '" ?>'; 
	        }
	        
            if ($psp_image_sitemaps_enabled) {
                
                $psp_stylesheet_url = plugins_url( '/sitemap-image.xsl', __FILE__ );
                $this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $psp_stylesheet_url ) . '" ?>';
                
            }
	    } else {
	        $psp_stylesheet_url = plugins_url( '/sitemap.xsl', __FILE__ );
            $this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $psp_stylesheet_url ) . '" ?>'; 
	    }
		$urlset = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$this->stylesheet,
				'<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

		foreach ( $url_list as $url_item ) {
			$url = $urlset->addChild( 'url' );

			// Add each attribute as a child node to the URL entry.
			foreach ( $url_item as $attr => $value ) {
				if ( 'url' === $attr ) {
					
					$url->addChild( $attr, esc_url( $value ) );
					
				} else if ('image' === $attr) {
				    
					foreach ($value as $imageattr) {
					    $image = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
					    if(array_key_exists("loc", $imageattr)) $image->addChild('image:loc',esc_url( $imageattr['loc'] ), 'http://www.google.com/schemas/sitemap-image/1.1');
					    if(array_key_exists("title", $imageattr)) $image->addChild('image:title',esc_attr( $imageattr['title'] ), 'http://www.google.com/schemas/sitemap-image/1.1');
					    if(array_key_exists("caption", $imageattr)) $image->addChild('image:caption',esc_attr( $imageattr['caption'] ), 'http://www.google.com/schemas/sitemap-image/1.1');
					}
					
				} else {
				    
					$url->addChild( $attr, esc_attr( $value ) );
				}
			}
			
		}		
		
		return $urlset->asXML();
		
	}

}