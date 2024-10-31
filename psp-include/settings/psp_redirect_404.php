<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
class PspRedirections {	
	
	private static $obj_handle = null;	
	
	protected $psp_helper;
	
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
		
		add_action('admin_menu', array(&$this, 'psp_redirect_admin_menu'));
		
		add_filter('set-screen-option', array(&$this, 'psp_set_screen_option'), 10, 3);		
		
	}
	
	public function psp_redirect_admin_menu() {				
		
		$psp_redir_page = add_management_page('Redirection Manager', 'Platinum SEO Redirections Manager', 'manage_options', 'redirectionmanager', array($this, 'redir_mgmtpage'));
		$psp_redir_page_2 = 'platinum-seo-and-social-pack_page_redirectionmanager';
		//error_log('redir '. $psp_redir_page);
		
		add_action("load-$psp_redir_page", array($this, 'psp_screen_options'));
		add_action("load-$psp_redir_page_2", array($this, 'psp_screen_options'));
		
		$psp_404_page = add_management_page('404 Manager', 'Platinum SEO 404 Manager', 'manage_options', 'manager404', array($this, 'manage_404_page'));
		$psp_404_page_2 = 'platinum-seo-and-social-pack_page_manager404';
		
		add_action("load-$psp_404_page", array($this, 'psp_screen_options'));
		add_action("load-$psp_404_page_2", array($this, 'psp_screen_options'));
		add_filter("manage_".$psp_redir_page."_columns", array($this, 'psp_screen_column_options'));
		add_filter("manage_".$psp_redir_page_2."_columns", array($this, 'psp_screen_column_options'));
			
	}
	
	public function psp_screen_options() {
        
		$psp_404_page = "tools_page_manager404";
		$psp_404_page_2 = 'platinum-seo-and-social-pack_page_manager404';
		$psp_redir_page = "tools_page_redirectionmanager";
		$psp_redir_page_2 = "platinum-seo-and-social-pack_page_redirectionmanager";
	 
		$screen = get_current_screen();
		
		//error_log($screen->id);
	 
		// get out of here if we are not on our settings page
		if(!is_object($screen) || ($screen->id != $psp_redir_page_2 && $screen->id != $psp_redir_page && $screen->id != $psp_404_page && $screen->id != $psp_404_page_2))
		{
			return;
		}
		if ($screen->id == $psp_redir_page_2 || $screen->id == $psp_redir_page) {
			$redir_args = array(
				'label' => __('Rows per page'),
				'default' => 10,
				'option' => 'psp_redir_rows_per_page'
			);
			add_screen_option( 'per_page', $redir_args );
		}
		
		if ($screen->id == $psp_404_page || $screen->id == $psp_404_page_2) {
			$filter_args = array(
				'label' => __('Rows per page'),
				'default' => 10,
				'option' => 'psp_filter_rows_per_page'
			);
			add_screen_option( 'per_page', $filter_args );
		}
    }
	
	public function psp_screen_column_options() {
        
		$psp_404_page = "tools_page_manager404";
		$psp_404_page_2 = 'platinum-seo-and-social-pack_page_manager404';
		$psp_redir_page = "tools_page_redirectionmanager";
		$psp_redir_page_2 = "platinum-seo-and-social-pack_page_redirectionmanager";
	 
		$screen = get_current_screen();
		
		//error_log($screen->id);
	 
		// get out of here if we are not on our settings page
		if(!is_object($screen) || ($screen->id != $psp_redir_page_2 && $screen->id != $psp_redir_page && $screen->id != $psp_404_page && $screen->id != $psp_404_page_2))
		{
			return;
		}     
		
		$psp_redir_type = isset($_GET['psp_redir_type']) ? sanitize_key($_GET['psp_redir_type']) : '';
		
		if ($psp_redir_type != "psplogs") {
			return;
		}
		
		if ($screen->id == $psp_redir_page_2 || $screen->id == $psp_redir_page) {
			$columns = array('ipaddress' => 'IP Address', 'useragent' => 'User Agent','referrer' => 'Referrer');
			return $columns;
			
		}
	}

	public function psp_set_screen_option($status, $option, $value) {
		
		//error_log('option '.$option.' '.$status.' '.$value);
            
		if ( 'psp_redir_rows_per_page' == $option || 'psp_filter_rows_per_page' == $option) {
			//error_log("psp rows ".$value);
			return sanitize_key($value);
		}
	}
        
	public function manage_404_page() {

		global $wpdb;
					
		$psp_redirections_tbl = $wpdb->prefix . "psp_redirections";
		$psp_404_log = $wpdb->prefix . "psp_404_log";			
		
		$posts_list = array();
		$sql_posts = '';
		$bad_links = array();
		$sql_posts_1 = '';
		$sql_posts_2 = '';
		$psp_404_type = isset($_GET['psp_404_type']) ? sanitize_key($_GET['psp_404_type']) : '';
		
		//handle single addnew
		if ( isset($_POST['insertit']) && !empty($_POST['source-url-input']) && !empty($_POST['id-input']) ) 
		{
			if (isset( $_POST['psp_404_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_404_actions_nonce']), 'do_psp_404_actions' )) {
				
				//$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : 'addredirect';
				$psp_action = 'addredirect';
			
				$psp_id = isset($_POST['id-input']) ? sanitize_key($_POST['id-input']) : '';
				$psp_source_url = isset($_POST['source-url-input']) ? trim(sanitize_text_field(html_entity_decode($_POST['source-url-input']))) : '';				
				$psp_redirect_to = isset($_POST['redirect-url-input']) ? trim(esc_url_raw(html_entity_decode($_POST['redirect-url-input']))) : '';	
				$psp_redirect_code = isset($_POST['psp-redirect-code']) ? trim(sanitize_key($_POST['psp-redirect-code'])) : '';
				
				if ($psp_action == 'addredirect' && !empty($psp_source_url) && !empty($psp_redirect_to) && !empty($psp_redirect_code)) {
					
					//Add new redirect
					if (!empty($psp_source_url)) $pspinsert['source_url'] = $psp_source_url;
					if (!empty($psp_redirect_to)) $pspinsert['dest_url'] = $psp_redirect_to;
					if (!empty($psp_redirect_code)) $pspinsert['redir_code'] = $psp_redirect_code;		
					
					//insert into $psp_redirections_tbl
					$wpdb->insert( $psp_redirections_tbl, $pspinsert  );
					//delete from $psp_404_log
					$wpdb->delete( $psp_404_log, array( 'id' => $psp_id ) );
				}

			}
							
		}
		
		//handle bulk addnew
		if ( isset($_POST['insertit']) && isset($_POST['update']) ) 
		{
			
			if (isset( $_POST['psp_404_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_404_actions_nonce']), 'do_psp_404_actions' )) {
			
				foreach( (array) $_POST['update'] as $psp_id ) {
					$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';
					$psp_id = sanitize_key($psp_id);
						
					//$psp_source_url = isset($_POST['source-url-input']) ? esc_url_raw(html_entity_decode($_POST['source-url-input'])) : '';
					$psp_source_url = isset($_POST["psp-".$psp_id]) ? trim(sanitize_text_field(html_entity_decode($_POST["psp-".$psp_id]))) : '';
					$psp_redirect_to = isset($_POST['redirect-url-input']) ? trim(esc_url_raw(html_entity_decode($_POST['redirect-url-input']))) : '';	
					$psp_redirect_code = isset($_POST['psp-redirect-code']) ? trim(sanitize_key($_POST['psp-redirect-code'])) : '';
					
					if ($psp_action == 'addredirect' && !empty($psp_source_url) && !empty($psp_redirect_to) && !empty($psp_redirect_code)) {			
						//Add new redirect
						if (!empty($psp_source_url)) $pspinsert['source_url'] = $psp_source_url;
						if (!empty($psp_redirect_to)) $pspinsert['dest_url'] = $psp_redirect_to;
						if (!empty($psp_redirect_code)) $pspinsert['redir_code'] = $psp_redirect_code;		
						
						//insert into $psp_redirections_tbl
						$wpdb->insert( $psp_redirections_tbl, $pspinsert  );
						//delete from $psp_404_log
						$wpdb->delete( $psp_404_log, array( 'id' => $psp_id ) );
					}
				}

			}	
		}
		
		// Handle bulk deletes
		if ( isset($_POST['deleteit']) && isset($_POST['update']) ) {
			
			if (isset( $_POST['psp_404_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_404_actions_nonce']), 'do_psp_404_actions' )) {

				foreach( (array) $_POST['update'] as $psp_id ) {
					
					$psp_id = sanitize_key($psp_id);

					if ( !current_user_can('edit_posts', $psp_id) )
						wp_die( __('You are not allowed to Delete.') );
						
					$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';                	
					
					//Delete SQL here
					
					if($wpdb->get_var("show tables like '$psp_404_log'") == $psp_404_log && $psp_action == 'delete') 
					{
						//delete from $psp_404_log
						$wpdb->delete( $psp_404_log, array( 'id' => $psp_id ) );
					}
					
				}
			
			}
		}
		
		// Handle delete all
		if ( isset($_POST['deleteit']) && isset( $_POST['psp_404_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_404_actions_nonce']), 'do_psp_404_actions' ) ) {
			
			$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';

			if ($psp_action == 'deleteall') {

				if ( !current_user_can('edit_posts') )
						wp_die( __('You are not allowed to Truncate.') );
						
				if($wpdb->get_var("show tables like '$psp_404_log'") == $psp_404_log) 
				{
					//delete all from $psp_redirections_log
					$deleteall = $wpdb->query("TRUNCATE TABLE $psp_404_log");				
				}
			
			}

		}
		
		//Handle bulk update
		if ( isset($_POST['updateit']) && isset($_POST['update']) ) {

			if (isset( $_POST['psp_404_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_404_actions_nonce']), 'do_psp_404_actions' )) {

				foreach( (array) $_POST['update'] as $psp_id ) {
					
					$psp_id = sanitize_key($psp_id);

					if ( !current_user_can('edit_posts', $psp_id) )
						wp_die( __('You are not allowed to update.') );
						
					$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';
					
					//$psp_redirect_to = isset($_POST['redirect-url-input']) ? esc_url_raw(html_entity_decode($_POST['redirect-url-input'])) : '';
					//$psp_source_url = isset($_POST['source-url-input']) ? esc_url_raw(html_entity_decode($_POST['source-url-input'])) : '';
					//$psp_redirect_code = isset($_POST['psp-redirect-code']) ? sanitize_key($_POST['psp-redirect-code']) : '';	
					$psp_status = isset($_POST['psp_status']) ? sanitize_key($_POST['psp_status']) : '';		
					
					//update status to 410 instead of 404
					if($wpdb->get_var("show tables like '$psp_404_log'") == $psp_404_log && $psp_action == 'edit') {
						//update $psp_404_log
						$pspupdate = array();						
						$pspupdate['status'] = $psp_status;		
							$pspupdate['total_hits'] = 1; //reinitialize for 410
							$wpdb->update( $psp_404_log, $pspupdate, array( 'id' => $psp_id ) );
					}                	
				}
			
			}
		}
		
	   //Handle search
		if ( !empty($_GET['psp_filter']) && !empty($_GET['post-search-input']) ) {			
				
			if (!empty($_GET['post-search-input'])) {
			
				$psp_search = sanitize_title($_GET['post-search-input']);
			
				if ($_GET['psp_filter'] == "contains") {
				
					$psp_like = '%'.$wpdb->esc_like($psp_search).'%';
					//$psp_like = '%'. $psp_search.'%';					
				
				}
				
				if ($_GET['psp_filter'] == "starts-with") {					
					
					$psp_like = $wpdb->esc_like($psp_search).'%';
				}
				
				if ($_GET['psp_filter'] == "ends-with") {					
					
					$psp_like = '%'.$wpdb->esc_like($psp_search);
				
				}

				if ($_GET['psp_filter'] == "equals") {					
					
					$psp_like = "equals";
				}
				
			}
			
			if (!empty($psp_like)) {
				//$sql_posts = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_name AS psp_post_name, b.meta_value AS psp_redirect, c. meta_value AS psp_redirect_code FROM $tbl_posts a, $tbl_postmeta b, $tbl_postmeta c WHERE a.post_name LIKE %s AND a.ID = b.post_id AND a.ID = c.post_id AND (b.meta_key='_techblissonline_psp_redirect_to_url' or 1=1) AND (c.meta_key='_techblissonline_psp_redirect_status_code' or 1=1)", $psp_like );
				if (empty($psp_404_type)) {
					$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.source_uri AS psp_post_name, a.source_url AS psp_rel_url, a.referrer as referrer, a.status as status, a.user_agent as user_agent, a.ipaddress as ipaddress, a.total_hits as total_hits, a.last_logged as last_logged, a.created as created FROM $psp_404_log a WHERE a.source_uri LIKE %s", $psp_like );
					if ($psp_like == "equals") {
						$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.source_uri AS psp_post_name, a.source_url AS psp_rel_url, a.referrer as referrer, a.status as status, a.user_agent as user_agent, a.ipaddress as ipaddress, a.total_hits as total_hits, a.last_logged as last_logged, a.created as created FROM $psp_404_log a WHERE a.source_uri = %s", $psp_search );
					}
				} else {
					if ($psp_404_type == "all_404") $psp_where = "status = '' and ";
					if ($psp_404_type == "all_410") $psp_where = "status = '410' and ";
					if ($psp_404_type == "all_404_with_referrers") $psp_where = "status = '' and referrer != '' and ";
					if ($psp_404_type == "all_410_with_referrers") $psp_where = "status = '410' and referrer != '' and ";
					if ($psp_404_type == "all_with_referrers") $psp_where = "referrer != '' and ";
					
					$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.source_uri AS psp_post_name, a.source_url AS psp_rel_url, a.referrer as referrer, a.status as status, a.user_agent as user_agent, a.ipaddress as ipaddress, a.total_hits as total_hits, a.last_logged as last_logged, a.created as created  FROM $psp_404_log a WHERE $psp_where a.source_url LIKE %s", $psp_like );
					if ($psp_like == "equals") {
					
						$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.source_uri AS psp_post_name, a.source_url AS psp_rel_url, a.referrer as referrer, a.status as status, a.user_agent as user_agent, a.ipaddress as ipaddress, a.total_hits as total_hits, a.last_logged as last_logged,a.created as created FROM $psp_404_log a WHERE $psp_where a.source_url = %s", $psp_search );
					
					}
					
				}
					//error_log($sql_posts_2);
				$posts_list = $wpdb->get_results($sql_posts_2, OBJECT);
				
			}
		
		} else if ( empty($_GET['psp_filter']) ) {
			
			if ($psp_404_type == "") $psp_where = " ";			    
			if ($psp_404_type == "all_404") $psp_where = "where status = '' ";
			if ($psp_404_type == "all_410") $psp_where = "where status = '410' ";
			if ($psp_404_type == "all_404_with_referrers") $psp_where = "where status = '' and referrer != '' ";
			if ($psp_404_type == "all_410_with_referrers") $psp_where = "where status = '410' and referrer != '' ";
			if ($psp_404_type == "all_with_referrers") $psp_where = "where referrer != '' ";
			
			$sql_posts_1 = "SELECT a.ID AS psp_id, a.source_uri AS psp_post_name, a.source_url AS psp_rel_url, a.referrer as referrer, a.status as status, a.user_agent as user_agent, a.ipaddress as ipaddress, a.total_hits as total_hits, a.last_logged as last_logged, a.created as created FROM $psp_404_log a $psp_where";
			
			$posts_list = $wpdb->get_results($sql_posts_1, OBJECT);
		}
		
		$total_no_posts = count($posts_list);
		$max_posts_per_page = 10;
		$user = get_current_user_id();
		$screen = get_current_screen();
		// retrieve the "per_page" option
		$screen_option = $screen->get_option('per_page', 'option');
		// retrieve the value of the option stored for the current user
		//error_log('screen option '.$screen_option);
		$max_posts_per_page = get_user_meta($user, $screen_option, true);

		if ( empty ( $max_posts_per_page) || $max_posts_per_page < 1 ) {
		// get the default value if none is set
		$max_posts_per_page = $screen->get_option( 'per_page', 'default' );
		}



		$link_count = ceil($total_no_posts/$max_posts_per_page);		
		$page_no = isset( $_GET['paged'] ) ? sanitize_key( $_GET['paged'] ) : 1;

		$limit_sql = ' LIMIT '.(($page_no - 1) * $max_posts_per_page).', '.$max_posts_per_page;
		//if($sql_posts != '') $sql_posts .= $limit_sql;
		if($sql_posts_1 != '') $sql_posts = $sql_posts_1 . $limit_sql;
		if($sql_posts_2 != '') $sql_posts = $sql_posts_2 . $limit_sql;

		if($sql_posts != '') $bad_links = $wpdb->get_results( $sql_posts );
		//if($sql_posts_2 != '') $bad_links_2 = $wpdb->get_results( $sql_posts_2 );

		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'total' => ceil($total_no_posts/$max_posts_per_page),
			'current' => $page_no
		));

		 include_once( 'psp_404_list_renderer.php' ); 
	} 
        
	public function redir_mgmtpage() {

		global $wpdb;
		//$psp_bad_links_table = $this->psp_bad_links_table;
		
		$tbl_posts = $wpdb->prefix . "posts";
		//$tbl_postmeta = $wpdb->prefix . "postmeta";
		$tbl_postmeta = $wpdb->prefix . "platinumseometa";
		$psp_redirections_tbl = $wpdb->prefix . "psp_redirections";
		$psp_redirections_log = $wpdb->prefix . "psp_redirections_log";
		
		$posts_list = array();
		$sql_posts = '';
		$bad_links = array();
		$sql_posts_1 = '';
		$sql_posts_2 = '';
		$psp_redir_type = isset($_GET['psp_redir_type']) ? sanitize_key($_GET['psp_redir_type']) : '';
		
		$builtin_post_types = array("post", "page");
		//$custom_post_types = $this->custom_post_types;
		$custom_post_types = get_post_types( array ( '_builtin' => FALSE ) );	
		$psp_all_posttypes = array_merge((array)$builtin_post_types, (array)$custom_post_types);
		$psp_post_types = array_combine($psp_all_posttypes, $psp_all_posttypes);
		
		$psp_post_types_str = "";
		foreach( $psp_post_types as $psp_post_typee) {
			$psp_post_types_str = $psp_post_types_str . "'".$psp_post_typee."', ";
		}
		$psp_post_types_str = substr($psp_post_types_str, 0, -2);
		//$psp_post_types_str = "( ". $psp_post_types_str . " )";
		
		//handle addnew
		if ( isset($_POST['insertit']) && !empty($psp_redir_type) ) {

			$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';
				
			$psp_source_url = isset($_POST['source-url-input']) ? trim(sanitize_text_field(html_entity_decode($_POST['source-url-input']))) : '';
			$psp_redirect_to = isset($_POST['redirect-url-input']) ? trim(esc_url_raw(html_entity_decode($_POST['redirect-url-input']))) : '';	
			$psp_redirect_code = isset($_POST['psp-redirect-code']) ? trim(sanitize_key($_POST['psp-redirect-code'])) : '';
			$psp_log = isset($_POST['psplog']) ? sanitize_key($_POST['psplog']) : '';
			
			if (!empty($psp_redir_type) && isset( $_POST['psp_urls_redirect_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_urls_redirect_actions_nonce']), 'do_psp_urls_redirect_actions' )) {
			
				if ($psp_action == 'addnew' && !empty($psp_source_url) && !empty($psp_redirect_to) && !empty($psp_redirect_code)) {			
					//Add new
						if (!empty($psp_source_url)) $pspinsert['source_url'] = $psp_source_url;
						if (!empty($psp_redirect_to)) $pspinsert['dest_url'] = $psp_redirect_to;
						if (!empty($psp_redirect_code)) $pspinsert['redir_code'] = $psp_redirect_code;
						if (!empty($psp_log)) $pspinsert['log_redirect'] = $psp_log;
						
						$wpdb->insert( $psp_redirections_tbl, $pspinsert  );		
				}	
			}
		}		
		
		// Handle bulk deletes
		if ( isset($_POST['deleteit']) && isset($_POST['update']) ) {

			foreach( (array) $_POST['update'] as $psp_id ) {
				
				$psp_id = sanitize_key($psp_id);
				if ( !current_user_can('edit_posts', $psp_id) )
					wp_die( __('You are not allowed to Delete.') );
					
				$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';
				
				//$psp_redirect_to = isset($_POST['redirect-url-input']) ? esc_url_raw($_POST['redirect-url-input']) : '';
				//$psp_redirect_code = isset($_POST['psp-redirect-code']) ? sanitize_key($_POST['psp-redirect-code']) : '';
				
				//Delete SQL here
				
				if (empty($psp_redir_type) && isset( $_POST['psp_posts_redirect_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_posts_redirect_actions_nonce']), 'do_psp_posts_redirect_actions' )) {
					if ($psp_action == 'delete' && !empty($psp_id) ) {
						delete_post_meta( $psp_id, '_techblissonline_psp_redirect_to_url');
						delete_metadata( 'platinumseo', $psp_id, '_techblissonline_psp_redirect_to_url');
						delete_post_meta( $psp_id, '_techblissonline_psp_redirect_status_code');
						delete_metadata( 'platinumseo', $psp_id, '_techblissonline_psp_redirect_status_code');
					}
				} else {
					
					if ($psp_redir_type == "pspurls" && isset( $_POST['psp_urls_redirect_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_urls_redirect_actions_nonce']), 'do_psp_urls_redirect_actions' )) {
						
						if($wpdb->get_var("show tables like '$psp_redirections_tbl'") == $psp_redirections_tbl && $psp_action == 'delete') 
						{
							//delete from $psp_redirections_tbl
							$wpdb->delete( $psp_redirections_tbl, array( 'id' => $psp_id ) );

						}
						
					} else if ($psp_redir_type == "psplogs" && isset( $_POST['psp_logs_redirect_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_logs_redirect_actions_nonce']), 'do_psp_logs_redirect_actions' )) {
					
						if($wpdb->get_var("show tables like '$psp_redirections_log'") == $psp_redirections_log && $psp_action == 'delete') 
						{
							//delete from $psp_redirections_log
							$wpdb->delete( $psp_redirections_log, array( 'id' => $psp_id ) );
						}
						
					}
				}
				
			}
		}
		
		// Handle delete all
		if ( isset($_POST['deleteit']) ) {
			
			$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';

			if ($psp_action == 'deleteall' && $psp_redir_type == "psplogs" && isset( $_POST['psp_logs_redirect_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_logs_redirect_actions_nonce']), 'do_psp_logs_redirect_actions' )) {

				if ( !current_user_can('edit_posts') )
						wp_die( __('You are not allowed to Truncate.') );
						
				if($wpdb->get_var("show tables like '$psp_redirections_log'") == $psp_redirections_log) 
				{
					//delete all from $psp_redirections_log
					$deleteall = $wpdb->query("TRUNCATE TABLE $psp_redirections_log");				
				}
			
			}

		}
		
		//Handle bulk update
		if ( isset($_POST['updateit']) && isset($_POST['update']) ) {			    

			foreach( (array) $_POST['update'] as $psp_id ) {
				
				$psp_id = sanitize_key($psp_id);

				if ( !current_user_can('edit_posts', $psp_id) )
					wp_die( __('You are not allowed to update.') );
					
				$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';
				
				$psp_redirect_to = isset($_POST['redirect-url-input']) ? trim(esc_url_raw(html_entity_decode($_POST['redirect-url-input']))) : '';
				$psp_source_url = isset($_POST['source-url-input']) ? trim(sanitize_text_field(html_entity_decode($_POST['source-url-input']))) : '';
				$psp_redirect_code = isset($_POST['psp-redirect-code']) ? trim(sanitize_key($_POST['psp-redirect-code'])) : '';	
				$psp_log = isset($_POST['psplog']) ? sanitize_key($_POST['psplog']) : '';	
				
				//Update or Delete SQL here
				if (empty($psp_redir_type) && isset( $_POST['psp_posts_redirect_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_posts_redirect_actions_nonce']), 'do_psp_posts_redirect_actions' )) {
					if ($psp_action == 'edit' && !empty($psp_redirect_to) && !empty($psp_redirect_code)) {
						//update_post_meta( $psp_id, '_techblissonline_psp_redirect_to_url', $psp_redirect_to);
						//update_post_meta( $psp_id, '_techblissonline_psp_redirect_status_code', $psp_redirect_code);
						update_metadata( 'platinumseo', $psp_id, '_techblissonline_psp_redirect_to_url', $psp_redirect_to);
						update_metadata( 'platinumseo', $psp_id, '_techblissonline_psp_redirect_status_code', $psp_redirect_code);
					}
				} else if ($psp_redir_type == "pspurls" && isset( $_POST['psp_urls_redirect_actions_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_urls_redirect_actions_nonce']), 'do_psp_urls_redirect_actions' )) {
					if($wpdb->get_var("show tables like '$psp_redirections_tbl'") == $psp_redirections_tbl && $psp_action == 'edit') {
						//update $psp_redirections_tbl
						$pspupdate = array();
						if (!empty($psp_redirect_to)) $pspupdate['dest_url'] = $psp_redirect_to;
						if (!empty($psp_redirect_code)) $pspupdate['redir_code'] = $psp_redirect_code;
						//if (!empty($psp_log)) $pspupdate['log_redirect'] = $psp_log;
						if (!empty($psp_source_url)) $pspupdate['source_url'] = $psp_source_url;
						$pspupdate['log_redirect'] = !empty($psp_log) ? $psp_log : '';
						
						$wpdb->update( $psp_redirections_tbl, $pspupdate, array( 'id' => $psp_id ) );
					}
				}

				//$delete_bad_links = "Delete from $psp_bad_links_table where psp_id = '".$psp_id."'";
				//$wpdb->query( $delete_bad_links );
			}
		}
		
	   //Handle search
		if ( !empty($_GET['psp_filter']) && !empty($_GET['post-search-input']) ) {			
				
			if (!empty($_GET['post-search-input'])) {
			
				//$psp_search = sanitize_title($_GET['post-search-input']);
				if (empty($psp_redir_type)) {
					$psp_search = sanitize_text_field($_GET['post-search-input']);
				} else {
					$psp_search = sanitize_title($_GET['post-search-input']);
				}
			
				if ($_GET['psp_filter'] == "contains") {
				
					$psp_like = '%'.$wpdb->esc_like($psp_search).'%';
					//$psp_like = '%'. $psp_search.'%';					
				
				}
				
				if ($_GET['psp_filter'] == "starts-with") {					
					
					$psp_like = $wpdb->esc_like($psp_search).'%';
				}
				
				if ($_GET['psp_filter'] == "ends-with") {					
					
					$psp_like = '%'.$wpdb->esc_like($psp_search);
				
				}

				if ($_GET['psp_filter'] == "equals") {					
					
					$psp_like = "equals";
				}				
				
			}
			
			if (!empty($psp_like)) {
				//$sql_posts = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_name AS psp_post_name, b.meta_value AS psp_redirect, c. meta_value AS psp_redirect_code FROM $tbl_posts a, $tbl_postmeta b, $tbl_postmeta c WHERE a.post_name LIKE %s AND a.ID = b.post_id AND a.ID = c.post_id AND (b.meta_key='_techblissonline_psp_redirect_to_url' or 1=1) AND (c.meta_key='_techblissonline_psp_redirect_status_code' or 1=1)", $psp_like );
				if (empty($psp_redir_type)) {
					$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name FROM $tbl_posts a WHERE a.post_type in ($psp_post_types_str) AND  a.post_status='publish' AND (a.post_title LIKE %s OR a.post_name LIKE %s)", $psp_like, $psp_like );
					if ($psp_like == "equals") {
						$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name FROM $tbl_posts a WHERE a.post_status='publish'AND  a.post_type in ($psp_post_types_str) AND  (a.post_title = %s OR a.post_name = %s)", $psp_search, $psp_search );
					}					
				} else if($psp_redir_type == "pspurls") {
					//$sql_posts_2 = $wpdb-prepare("SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code  FROM %s a WHERE a.source_url LIKE %s", array( $psp_redirections_tbl, $psp_like ));
					$sql_posts_2 = $wpdb->prepare("SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.log_redirect as psp_log FROM $psp_redirections_tbl a WHERE a.source_url LIKE %s", $psp_like );
					if ($psp_like == "equals") {
					
						$sql_posts_2 = $wpdb->prepare("SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.log_redirect as psp_log FROM $psp_redirections_tbl a WHERE a.source_url = %s", $psp_search );
					
					}
					
				} else if($psp_redir_type == "psplogs") {
					
					$sql_posts_2 = $wpdb->prepare("SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.referrer as psp_referrer, a.ipaddress as psp_ipaddress, a.user_agent as psp_useragent FROM $psp_redirections_log a WHERE a.source_url LIKE %s", $psp_like );
					
					if ($psp_like == "equals") {
					
						$sql_posts_2 = $wpdb->prepare("SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.referrer as psp_referrer, a.ipaddress as psp_ipaddress, a.user_agent as psp_useragent FROM $psp_redirections_log a WHERE a.source_url = %s", $psp_search );
					
					}
					
				}
					//error_log($sql_posts_2);
				if (empty($psp_redir_type)) {
					$posts_list = $wpdb->get_results($sql_posts_2, OBJECT);
				} else {
					if($psp_redir_type == "pspurls") {
						if($wpdb->get_var("show tables like '$psp_redirections_tbl'") == $psp_redirections_tbl)
						{
							 $posts_list = $wpdb->get_results($sql_posts_2, OBJECT);
						}
					} else if($psp_redir_type == "psplogs") {
						if($wpdb->get_var("show tables like '$psp_redirections_log'") == $psp_redirections_log)
						{
							 $posts_list = $wpdb->get_results($sql_posts_2, OBJECT);
						}
					}
				}
				
			}
		
		//} else if ( empty($_GET['psp_filter']) ) {
		} else if ( !empty($_GET['psp_filter']) && $_GET['psp_filter'] == "redirected" ) {
			
			if (empty($psp_redir_type)) {
			
				$sql_posts_1 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value AS psp_redirect, c. meta_value AS psp_redirect_code   FROM $tbl_posts a, $tbl_postmeta b, $tbl_postmeta c WHERE a.post_status = 'publish' AND a.post_type in ($psp_post_types_str) AND a.ID = b.platinumseo_id AND a.ID = c.platinumseo_id AND (b.meta_key=%s) AND (c.meta_key=%s)", array('_techblissonline_psp_redirect_to_url', '_techblissonline_psp_redirect_status_code') );
			} else {
				
				if($psp_redir_type == "pspurls") {
					$sql_posts_1 = "SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.log_redirect as psp_log FROM $psp_redirections_tbl a";
				} else if($psp_redir_type == "psplogs") {
					$sql_posts_1 = "SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.referrer as psp_referrer, a.ipaddress as psp_ipaddress, a.user_agent as psp_useragent FROM $psp_redirections_log a";				
				}
				
			}
			//error_log($sql_posts_1);
			//$posts_list = $wpdb->get_results($sql_posts_1, OBJECT);
			//error_log(print_r($posts_list));
			if (empty($psp_redir_type)) {
					$posts_list = $wpdb->get_results($sql_posts_1, OBJECT);
			} else {
				//if( $wpdb->get_var("show tables like '$psp_redirections_tbl'") == $psp_redirections_tbl)
				//{
					 $posts_list = $wpdb->get_results($sql_posts_1, OBJECT);
				//}
			}
		//} else if ( $_GET['psp_filter'] == "all" ) {
		} else if ( empty($_GET['psp_filter']) ) {	
					
			if (empty($psp_redir_type)) {				
				
				$sql_posts_2 = "SELECT a.ID AS psp_id, a.post_title AS psp_post_name FROM $tbl_posts a WHERE a.post_type in ($psp_post_types_str) AND a.post_status='publish'";
				//error_log("posts array ".print_r($sql_posts_2, 'n'));
				$posts_list = $wpdb->get_results($sql_posts_2, OBJECT);
				//error_log("posts array ".print_r($posts_list, 'n'));
			} else {
					if($psp_redir_type == "pspurls") {
						
						$sql_posts_1 = "SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.log_redirect as psp_log FROM $psp_redirections_tbl a";
						if($wpdb->get_var("show tables like '$psp_redirections_tbl'") == $psp_redirections_tbl)
						{
							 $posts_list = $wpdb->get_results($sql_posts_1, OBJECT);
						}
					} else if($psp_redir_type == "psplogs") {
						
						$sql_posts_1 = "SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.referrer as psp_referrer, a.ipaddress as psp_ipaddress, a.user_agent as psp_useragent FROM $psp_redirections_log a";	
						if($wpdb->get_var("show tables like '$psp_redirections_log'") == $psp_redirections_log)
						{
							 $posts_list = $wpdb->get_results($sql_posts_1, OBJECT);
						}
					}
				}
		}
		
		$total_no_posts = count($posts_list);
		
		$max_posts_per_page = 10;
        $user = get_current_user_id();
        $screen = get_current_screen();
        // retrieve the "per_page" option
        $screen_option = $screen->get_option('per_page', 'option');
        // retrieve the value of the option stored for the current user
        //error_log('screen option '.$screen_option);
        $max_posts_per_page = get_user_meta($user, $screen_option, true);
        
        if ( empty ( $max_posts_per_page) || $max_posts_per_page < 1 ) {
        	// get the default value if none is set
        	$max_posts_per_page = $screen->get_option( 'per_page', 'default' );
        }
        
        // now use $per_page to set the number of items displayed
		//$max_posts_per_page = 10;
		$link_count = ceil($total_no_posts/$max_posts_per_page);
		$page_no = isset( $_GET['paged'] ) ? sanitize_key( $_GET['paged'] ) : 1;

		$limit_sql = ' LIMIT '.(($page_no - 1) * $max_posts_per_page).', '.$max_posts_per_page;
		//if($sql_posts != '') $sql_posts .= $limit_sql;
		if($sql_posts_1 != '') $sql_posts = $sql_posts_1 . $limit_sql;
		if($sql_posts_2 != '') $sql_posts = $sql_posts_2 . $limit_sql;

		if($sql_posts != '') $bad_links = $wpdb->get_results( $sql_posts );
		//if($sql_posts_2 != '') $bad_links_2 = $wpdb->get_results( $sql_posts_2 );

		$page_links = paginate_links( array(
				'base' => add_query_arg( 'paged', '%#%' ),
				'format' => '',
				'total' => ceil($total_no_posts/$max_posts_per_page),
				'current' => $page_no
		));

		include_once( 'psp_redirect_list_renderer.php' ); 
	}
     
    public function psp_handle_404() {		

    	global $wp;
    	global $wpdb;
		
		$psp_allowed_protocols = array('http','https');	
    	
    	$req_uri = '';
    	$req_url = '';
    	$requested_url = '';
    	
    	if (empty($wp) || empty($wp->request)) return;
    	
    	$path_to_page = trim($wp->request); // /path/to/page 
    	/***
    	if (filter_var($path_to_page, FILTER_VALIDATE_URL)) { 
    	   $requested_url =  trim(add_query_arg( $wp->query_vars,  $wp->request  ));
    	} else {
    	    $requested_url =  trim(add_query_arg( $wp->query_vars, home_url( $wp->request ) ));
    	}
    	***/
    	list( $req_uri ) = explode( '?', $_SERVER['REQUEST_URI'] );
		$req_uri = trim($req_uri);
		if($req_uri) $req_url = home_url($req_uri);
		$requested_url = filter_var( $req_url, FILTER_VALIDATE_URL, '' );
    	//error_log("path to page ".$path_to_page);
    	//error_log("requested url ".$requested_url);
		
		if ( $requested_url ) {
			$requested_url = trim(esc_url_raw( $requested_url, $psp_allowed_protocols ));
		}
		
		if ( !$requested_url ) {
			return;
		}
    	
    	$redirections = array();
    	$psp_redirections_tbl = $wpdb->prefix . "psp_redirections";
    	$psp_redirection_settings = get_option('psp_permalink_settings');
    	$do_redirect = isset($psp_redirection_settings['redirection']) ? ($psp_redirection_settings['redirection']) : '';
		$do_auto_redirect = isset($psp_redirection_settings['auto_redirection']) ? ($psp_redirection_settings['auto_redirection']) : '';
    	
    	if ( is_404() ) {
			/***commented in V2.0.9
			if ($do_auto_redirect) {
    		    $this->psp_auto_redirect();
    		}
    	   ***/
    		if ( $do_redirect ) {
    	
    			$sql_redirections_1 = $wpdb->prepare("SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.log_redirect AS psp_log_redirect  FROM $psp_redirections_tbl a WHERE a.source_url IN ( %s, %s)", $path_to_page, $req_uri );
				
				//$sql_redirections_1 = $wpdb->prepare("SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.log_redirect AS psp_log_redirect  FROM $psp_redirections_tbl a WHERE a.source_url = %s", $path_to_page );
    			
    			$sql_redirections_2 = $wpdb->prepare("SELECT a.id AS psp_id, a.source_url AS psp_post_name, a.dest_url AS psp_redirect, a.redir_code AS psp_redirect_code, a.log_redirect AS psp_log_redirect  FROM $psp_redirections_tbl a WHERE a.source_url = %s", $requested_url );
    			
    			$redirections = $wpdb->get_results($sql_redirections_1, OBJECT);
    			if (!$redirections) {
    			    //error_log("nothings exists for path  ".$path_to_page);
    				$redirections = $wpdb->get_results($sql_redirections_2, OBJECT);
    				//error_log(print_r($redirections, true));
    			}				
    			
    			if (!$redirections) {
    			    //error_log("nothings exists for requested url  ".$requested_url);
    			}
    			
    			if ($redirections) {
    			    
    				$redirect_to = $redirections[0]->psp_redirect;
    				$redirect_method = $redirections[0]->psp_redirect_code;
    				
    				//error_log("redirect to ".$redirect_to);
    				
    				if (!empty($redirect_method) && !empty($redirect_to)) {
    					wp_safe_redirect($redirect_to, $redirect_method);
						if($redirections[0]->psp_log_redirect) {
							
							$limit_301 = isset($psp_redirection_settings['limit_301']) ? ($psp_redirection_settings['limit_301']) : '';
							$psp_redirections_log = $wpdb->prefix . 'psp_redirections_log';
							$sql_301_count = $wpdb->get_var("SELECT count(*) FROM $psp_redirections_log" );
							//error_log("rowcount ".$sql_301_count);
							if ($limit_301 && ($limit_301 <= $sql_301_count)) {
								return;
							}
							
							
							$this->psp_log_redirect($redirections);
						}	
    					exit();
    				}
    			}
				
    		}
    		
    		
    		if ($do_auto_redirect) {
    		    $this->psp_auto_redirect();
    		}
    		//$this->psp_log_404();		
    		
    	}	
    	
    	return;
    }
	
	public function psp_do_log_404() {
		if (is_404()) {
			$this->psp_log_404();
		}
	}
	
	public function psp_log_redirect($redirections) {
		
		global $wp;
    	global $wpdb;

		$pspinsert  = array();
		$req_uri = '';
		$req_url = '';
		$requested_url = '';
		$psp_redirections_log = $wpdb->prefix . 'psp_redirections_log';
		
		$referrer = wp_get_original_referer();
		//$referrer = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? filter_var( $_SERVER[ 'HTTP_REFERER' ], FILTER_VALIDATE_URL, '' ) : '';
		//$user_agent = isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? filter_var( $_SERVER[ 'HTTP_USER_AGENT' ], FILTER_DEFAULT, '' ) : '';
		$user_agent = isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? sanitize_text_field( $_SERVER[ 'HTTP_USER_AGENT'] ) : '';
    	$ipaddress = $this->psp_get_user_ip();
		
		$pspinsert['referrer'] = !empty($referrer) ? esc_url_raw($referrer) : '';    	
    	$pspinsert['ipaddress'] = !empty($ipaddress) ? $ipaddress : '';
    	$pspinsert['user_agent'] = !empty($user_agent) ? $user_agent : '';
		
		list( $req_uri ) = explode( '?', $_SERVER['REQUEST_URI'] );
		if($req_uri) $req_url = home_url($req_uri);
		$requested_url = filter_var( $req_url, FILTER_VALIDATE_URL, '' );
		
		if(!$requested_url) {
			return;
		}
		
		$pspinsert['source_uri'] = !empty($req_uri) ? $req_uri : '';
		$pspinsert['source_url'] = !empty($requested_url) ? esc_url_raw($requested_url) : '';
		
		if (!empty($redirections)) {			
			$pspinsert['dest_url'] = !empty($redirections[0]->psp_redirect) ? esc_url_raw($redirections[0]->psp_redirect) : '';
			$pspinsert['redir_code'] = !empty($redirections[0]->psp_redirect_code) ? sanitize_key($redirections[0]->psp_redirect_code) : '';
			
		}
		
		if(!empty($pspinsert)) {
			$wpdb->insert( $psp_redirections_log, $pspinsert  );	
		}
		return;
	}
    
    public function psp_loaded_filter() {
        add_filter('status_header', array($this, 'psp_log_404'), 10, 4);
    }
	
	public function psp_log_404($sh = '', $code = '', $descrption = '', $protocol = '') {
	     
	     $error_codes = array('404', '410', '');
	     
	     //error_log('startcode '.$code);
	     
    	if ( !in_array($code, $error_codes)) {
    	   
    		return $sh;
    	}
    
    	global $wp;
    	global $wpdb;
    	
    	$pspinsert  = array();
    	$psp_404_log = $wpdb->prefix . "psp_404_log";    	
    	$ipaddress = "";
    	$referrer = "";
    	$req_uri = '';
    	$req_url = '';
    	$requested_url = '';
    	
    	$psp_404_settings = get_option('psp_permalink_settings');
    	$bots_only = isset($psp_404_settings['bots_404']) ? ($psp_404_settings['bots_404']) : '';
    	$referrer_only = isset($psp_404_settings['referrer_404']) ? ($psp_404_settings['referrer_404']) :'';
    	$track_404 = isset($psp_404_settings['enable_404']) ? ($psp_404_settings['enable_404']) : '';
		$limit_404 = isset($psp_redirection_settings['limit_404']) ? ($psp_redirection_settings['limit_404']) : '';    	
    	
    	$path_to_page = trim($wp->request); // /path/to/page 
    
    	// Check if excluded.
    	/***
    	if ( $this->is_url_excluded( $path_to_page ) ) {
    		return;
    	}
    	***/
    	//$requested_url =  trim(add_query_arg( $wp->query_vars, home_url( $wp->request ) ));
    	
    	list( $req_uri ) = explode( '?', $_SERVER['REQUEST_URI'] );		
		if($req_uri) $req_url = home_url($req_uri);
		//$req_url = home_url($_SERVER['REQUEST_URI']);
		$requested_url = filter_var( $req_url, FILTER_VALIDATE_URL, '' );
    	
    	if ( $bots_only ) {
    		//$requested_url =  trim(home_url( $wp->request ));
    	}   	
    		
    	$pspinsert['source_uri'] = !empty($path_to_page) ? trim($path_to_page) : '';
    	$pspinsert['source_url'] = !empty($requested_url) ? trim(esc_url_raw($requested_url)) : '';
    	
    	if (empty($path_to_page) || empty($requested_url)) {
    	
    		return $sh;	
    	}    	
    	
    	$sql_404 = $wpdb->prepare("SELECT id, source_uri, source_url, total_hits, referrer, status  FROM $psp_404_log a WHERE a.source_uri = %s", $path_to_page );
    	//error_log('path to page '.$path_to_page);
    	$id_404 = $wpdb->get_row($sql_404, OBJECT);
		//error_log('middlecode '.$code);
		if($code == '404') {
		    //error_log('status '.$id_404->status);
		    if (!empty($id_404) && trim($id_404->status) == "410") {
    			//set status tp 410 via filter
    			$code = '410';
    			$description = get_status_header_desc( $code );
    			//error_log('410 return '.$code);
    			return "$protocol $code $description";
		    } else {
		        //error_log('404 return '.$code);
		        return $sh;
		    }
		}
    	
		if(!$track_404) {
			return;
		}
		 //error_log('404 logging enabled '.$code);
		$sql_404_count = $wpdb->get_var("SELECT count(*) FROM $psp_404_log" );
		//error_log("rowcount ".$sql_404_count);
		if ($limit_404 && ($limit_404 <= $sql_404_count) && empty($id_404)) {
			return;
		}
		$referrer = wp_get_original_referer();
		//$referrer = isset( $_SERVER[ 'HTTP_REFERER' ] ) ? filter_var( $_SERVER[ 'HTTP_REFERER' ], FILTER_VALIDATE_URL, '' ) : '';
    	
    	if ( $referrer_only && empty($referrer) ) {
    		return;
    	}
    	
    	$pspinsert['referrer'] = !empty($referrer) ? esc_url_raw($referrer) : '';
    	//$user_agent = isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? filter_var( $_SERVER[ 'HTTP_USER_AGENT' ], FILTER_DEFAULT, '' ) : '';
		$user_agent = isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? sanitize_text_field( $_SERVER[ 'HTTP_USER_AGENT'] ) : '';
    	$ipaddress = $this->psp_get_user_ip();
    	
    	$pspinsert['ipaddress'] = !empty($ipaddress) ? $ipaddress : '';
    	$pspinsert['user_agent'] = !empty($user_agent) ? $user_agent : '';
    	
    	if ($bots_only && !$this->detectSearchBot($pspinsert['ipaddress'], $pspinsert['user_agent'])) {
    	    return;
    	}
    	//error_log('404 logged as it is googlebot and error code is '.$code);
    	if (!empty($path_to_page) && !empty($requested_url)) {
    	
    		if (!empty($id_404)) {
    			$pspinsert['total_hits'] = (int) $id_404->total_hits+1;			
    			$wpdb->update( $psp_404_log, $pspinsert, array('id' => $id_404->id)  );
    		} else {
    			$wpdb->insert( $psp_404_log, $pspinsert  );	
    		}			
    	}
    	
    	return;
    }  
   
    private function psp_get_user_ip() {

    	$ip = "";
    	
    	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
    	
    		//check ip from share internet //ip = $_SERVER['HTTP_CLIENT_IP'];	
    		$ip = isset( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ? filter_var( $_SERVER[ 'HTTP_CLIENT_IP' ], FILTER_VALIDATE_IP, '' ) : ''; 
    		
    	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    	
    		//to check ip is pass from proxy //$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];	
    		$ip = isset( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ? filter_var( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ], FILTER_VALIDATE_IP, '' ) : ''; 
    		
    	} else {
    	
    		//to get remote address // $ip = $_SERVER['REMOTE_ADDR'];
    		$ip = isset( $_SERVER[ 'REMOTE_ADDR' ] ) ? filter_var( $_SERVER[ 'REMOTE_ADDR' ], FILTER_VALIDATE_IP, '' ) : ''; 
    	}
    	return apply_filters( 'psp_get_user_ip', $ip );
    }
    
    private function psp_auto_redirect() {

		global $wpdb;
		$ID = '';
		$slug = '';
		$pspurl = '';
		$exts=array("/",".php",".html",".htm");
		$psp_posts = $wpdb->prefix . "posts"; 
		
		list( $req_uri ) = explode( '?', $_SERVER['REQUEST_URI'] );
		$req_url = home_url($req_uri);
      //error_log("auto redirect uri ".$req_url);
		$pspurl = filter_var( $req_url, FILTER_VALIDATE_URL, '' );
		//error_log("auto redirect url ".$pspurl);
		if (!$pspurl) {
			return;
		}
		
		if ($pspurl) $slug = sanitize_title(basename( $pspurl ));
		
		//	error_log("auto redirect slug ".$slug);
		
		// This will also work with PHP version <= 5.x.x 
		foreach( $exts as $ext ) { 
			$slug = str_replace( $ext, "", $slug ); 
			$slug = trim($slug);
		}
		
		if (!$slug) return;
		
		if ($slug) {
			$sql  = $wpdb->prepare("SELECT ID FROM $psp_posts WHERE post_name = %s AND post_status = 'publish'", $slug);
			$ID = $wpdb->get_var( $sql );
		}
		//error_log("post id ".$ID);
		if (!$ID) return;

		if( $ID ) {
			//$this->redirect_to_new_location( get_permalink( $ID ));
			$psp_redirect_to_url = get_permalink( $ID );
			$psp_redirect_status_code = '301';
			wp_safe_redirect($psp_redirect_to_url,$psp_redirect_status_code);
				exit();
		}
	}
	
	private function validate_url($url) {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);
    
        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }
	
	private function detectSearchBot($ip, $agent) {
	
		$hostname = $ip;
        //error_log("ip address ".$ip);
        // error_log("user agent ".$agent);
		// check HTTP_USER_AGENT what not to touch gethostbyaddr in vain
		if (preg_match('/(?:google?|bing)bot/iu', $agent)) {
			// success - return host, fail - return ip or false
			$hostname = gethostbyaddr($ip);
            //error_log("host name ".$hostname);
			// https://support.google.com/webmasters/answer/80553
			if ($hostname !== false && $hostname != $ip) {
				// detect google and yandex search bots
				//if (preg_match('/\.((?:google(?:bot)?|yandex)\.(?:com|ru))$/iu', $hostname)) {
				if (preg_match('/\.((?:google(?:bot)?|bing|googleusercontent)\.(?:com))$/iu', $hostname)) {
					// success - return ip, fail - return hostname
					$ip = gethostbyname($hostname);

					if ($ip != $hostname) {
						return true;
					}
				}
			}
		}

		return false;
	}
}
?>