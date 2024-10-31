<?php

/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: App ID and Secret Management class
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/ 
*/

class PspPreSettings extends PspSettings {	
	 
	private static $obj_handle = null;	
	
	// this is the URL our updater / license checker pings. 
	private static $PSPP_SITE_URL = 'https://techblissonline.com/tools/platinum-seo-wordpress-premium/'; 

	// Product Name
	private static $PSPP_ITEM_NAME = 'techblissonline_platinum_seo_premium'; 

	// the name of the settings page for the license input field to be displayed
	private static $PSPP_LICENSE_PAGE = 'pspp-license';
	
	private $psp_helper;
	private $psp_settings_instance;
	private $sitename;
	private $sitedescription;	
	
	private $plugin_settings_tabs = array();
	 
	private $psp_pre_security_settings_group = 'psp_pre_credentials';	
	
	protected $psp_plugin_options_key = 'psp-pre-by-techblissonline';
	//private $psp_plugin_lic_key = 'psp-pre-by-techblissonline';
	private $psp_settings_tabs = array();

	private $psp_pre_settings = array();
	private $psp_settings = array();
	
	public static function get_instance() {
	
		if ( null == self::$obj_handle ) {
			self::$obj_handle = new self;
		}
	
		return self::$obj_handle;
	
	} // end get_instance;	
	
	function __construct() {

		$psp_helper_instance = PspHelper::get_instance();		
		$this->psp_helper = $psp_helper_instance;
		
		//$psp_settings_instance = PspSettings::get_instance();		
		//$this->psp_settings_instance = $psp_settings_instance;
		
		$this->sitename = $psp_helper_instance->get_sitename();
		
		if (get_option("psp_tools_plugin_url")) {
		    self::$PSPP_SITE_URL = get_option("psp_tools_plugin_url");
		}
		
		$this->psp_settings_tabs[$this->psp_pre_security_settings_group] = 'Licenses';
		//$this->psp_settings_tabs[$this->psp_home_settings_group] = 'Home';
		$this->psp_settings = get_option("psp_sitewide_settings");
		
		add_action( 'admin_init', array( &$this, 'psp_pre_settings_init' ) );
		//add_action( 'admin_init', array( &$this, 'pspp_init_plugin_updater' ), 0 );
		
		add_action('admin_init', array( &$this, 'pspp_activate_license') );
		add_action('admin_init', array( &$this, 'pspp_deactivate_license') );
		//add_action('psp_vlk', array( &$this, 'pspp_check_license') );
		//add_action( 'admin_notices', array( &$this, 'pspp_admin_notices') );
		//add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
		/***
		if( !class_exists( 'PSPP_SL_Plugin_Updater' ) ) {
			// load our custom updater
			include( dirname( __FILE__ ) . '/PSPP_SL_Plugin_Updater.php' );
		}
        ***/
		
		//add_filter('puc_request_info_query_args-'.$this->slug, 'psp_premium_auto_updater_query_args');

		//Use version 2.0 of the automatic update checker.
		/*require 'plugin-updates/plugin-update-checker.php';
		$PspPremiumUpdateChecker = new PluginUpdateChecker_2_1 (
			'http://techblissonline.com/path/to/metadata.json',
			__FILE__,
			'platinum-seo-pack-premium'
		);
		$PspPremiumUpdateChecker->addQueryArgFilter('psp_premium_auto_updater_query_args');*/
	}
	/***
	function pspp_init_plugin_updater() {

		// retrieve our license key from the DB
		//$license_key = trim( get_option( 'pspp_license_key' ) );
		$psp_pre_settings = $this->psp_pre_settings;
		$psp_pre_setting = get_option('psp_pre_setting');
		if ($psp_pre_settings) {
    		$license_key = isset($psp_pre_settings['psp_premium_license_key']) ? trim($psp_pre_settings['psp_premium_license_key']) : '';
    		$status = isset($psp_pre_setting['psp_premium_license_key_status']) ? trim ($psp_pre_setting['psp_premium_license_key_status']) : '';
    
    		// setup the updater
    		$pspp_updater = new PSPP_SL_Plugin_Updater( self::$PSPP_SITE_URL, __FILE__, array(
    				'version' 	=> '1.0', 				// current version number
    				'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
    				'item_name' => self::$PSPP_ITEM_NAME, 	// name of this plugin
    				'author' 	=> 'Rajesh - Techblissonline.com',  // author of this plugin
    				'url'       => esc_url(home_url())
    			)
    		);
		
		}

	}
	***/
	function psp_pre_settings_init() {		
		
		$tab = isset( $_GET['psppretab'] ) ? Sanitize_key($_GET['psppretab']) : $this->psp_pre_security_settings_group;
		
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
	
		$this->register_pre_security_settings();			
		
	}
	
	//filter query args for automatic upgrades
	/***
	function psp_premium_auto_updater_query_args($queryArgs = array()) {
				
		$psp_pre_settings = get_option("psp_pre_settings");
		
		if ($psp_pre_settings) {
		
    		$psp_client_id = isset($psp_pre_settings['psp_premium_client_id']) ? esc_attr($psp_pre_settings['psp_premium_client_id']) : '';
    		$psp_license_key = isset($psp_pre_settings['psp_premium_license_key']) ? esc_attr($psp_pre_settings['psp_premium_license_key']) : '';
    		$psp_secret_key = isset($psp_pre_settings['psp_premium_client_secret']) ? esc_attr($psp_pre_settings['psp_premium_client_secret']) : '';		
    		
    		$queryArgs['psp_client_id'] = $psp_client_id;
    		$queryArgs['psp_license_key'] = $psp_license_key;
    		$queryArgs['psp_secret_key'] = $psp_secret_key;
		
		}

		return $queryArgs;
		
	}
    ***/ 
	
	/*
	 * Registers the Home SEO settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_pre_security_settings() {
		$this->psp_settings_tabs[$this->psp_pre_security_settings_group] = 'Licenses';		
		$psp_pre_settings_name = "psp_pre_settings";
		
		$psp_pre_settings = get_option($psp_pre_settings_name);
		if (!empty($psp_pre_settings)) $this->psp_pre_settings = $psp_pre_settings;
		
		//wp_enqueue_script( 'psp-input-toggler', plugins_url( '/js/pspinputtypetoggler.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'psp-input-toggler', plugins_url( '/js/pspinputtypetoggler.js', __FILE__ ), array( 'jquery' ), '2.1.7');
		//register
		register_setting( $this->psp_pre_security_settings_group, $psp_pre_settings_name, array( &$this, 'psp_sanitize_license' ) );
		//add Section
		add_settings_section( 'psp_section_pre_license', esc_html__('Platinum SEO and Social Premium Pack License', 'platinum-seo-pack' ), array( &$this, 'section_pre_license_desc' ), $this->psp_pre_security_settings_group );
		//add fields
		$psp_license_key_field     = array (
            'label_for' 	=> 'psp_license_key_id',
            'option_name'   => $psp_pre_settings_name.'[psp_premium_license_key]',
			'option_value'  => isset($psp_pre_settings['psp_premium_license_key']) ? esc_attr($psp_pre_settings['psp_premium_license_key']) : '',
			'option_description' => esc_html__( 'Enter your Platinum SEO and Social Premium Pack License Key. The license key is used for access to premium features and their upgrades.', 'platinum-seo-pack' ),
        );
		add_settings_field( 'psp_license_key_id', '<a href="'.self::$PSPP_SITE_URL.'" target="_blank">'.esc_html__('Techblissonline Platinum SEO Premium License Key ', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_password' ), $this->psp_pre_security_settings_group, 'psp_section_pre_license',  $psp_license_key_field);		
		
		$psp_license_client_field     = array (
            'label_for' 	=> 'psp_license_client_id',
            'option_name'   => $psp_pre_settings_name.'[psp_premium_client_id]',
			'option_value'  => isset($psp_pre_settings['psp_premium_client_id']) ? esc_attr($psp_pre_settings['psp_premium_client_id']) : '',
			'option_description' => esc_html__( 'Enter your Platinum SEO and Social Premium Pack Client ID.', 'platinum-seo-pack' ),
        );
		add_settings_field( 'psp_license_client_id', '<a href="'.self::$PSPP_SITE_URL.'" target="_blank">'.esc_html__('Techblissonline Platinum SEO Premium Client ID ', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_password' ), $this->psp_pre_security_settings_group, 'psp_section_pre_license',  $psp_license_client_field);	
	}
	
	function section_pre_license_desc() {echo ''; }
	function section_pre_credentials_desc() {echo ''; }
	
	function psp_sanitize_license( $new_settings ) {
	
		//$psp_pre_settings = $this->psp_pre_settings;
		//$old = isset($psp_pre_settings['psp_premium_license_key']) ? $psp_pre_settings['psp_premium_license_key'] : '';
		/******
		if( $old && $old != $new_settings['psp_premium_license_key'] ) {
			$psp_pre_settings['psp_premium_license_key'] = ""; // new license has been entered, so must reactivate
		}
		******/
		if( empty( $new_settings['psp_premium_license_key'] ) ) {
		    add_settings_error('psp_pre_settings', esc_attr( 'empty_license_key' ), 'License Key cannot be empty', 'error');
		    return false;
		}
		
		if( empty( $new_settings['psp_premium_client_id'] ) ) {
		    add_settings_error('psp_pre_settings', esc_attr( 'empty_client_id' ), 'Client ID cannot be empty', 'error');
		    return false;
		}
		
		if( !empty( $new_settings['psp_premium_license_key'] ) ) $new_settings['psp_premium_license_key'] = sanitize_text_field( $new_settings['psp_premium_license_key'] );
		if( !empty( $new_settings['psp_premium_client_id'] ) ) $new_settings['psp_premium_client_id'] = sanitize_text_field( $new_settings['psp_premium_client_id'] );
		return $new_settings;
	}
	
	/*
	 * Callback for adding a textfield.
	 */
	function psp_add_field_password(array $args) {
	
		$option_name   = isset($args['option_name']) ? esc_attr($args['option_name']) : '';
		$id     = isset($args['label_for']) ? esc_attr($args['label_for']) : '';
		$option_value     = isset($args['option_value']) ? esc_attr( $args['option_value'] ) : '';
		$option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		
		echo "<input id='".esc_attr($id)."' name='".esc_attr($option_name)."' class='regular-text' style='width:95%' type='password' autofill='off' value='".esc_attr($option_value)."' /><span class='dashicons dashicons-hidden view-obfuscated-text'></span><br/><p class='description'>".html_entity_decode($option_description)."</p>";			
				
	}
	
	function pspp_activate_license() {

		// listen for our activate button to be clicked
		if( isset( $_POST['pspp_license_activate'] ) ) {

			// run a quick security check
			if( ! check_admin_referer( 'pspp_nonce', 'pspp_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			//$license = trim( get_option( 'pspp_license_key' ) );
			$psp_pre_settings = $this->psp_pre_settings;
			$psp_settings = $this->psp_settings;
			$psp_pre_setting = get_option('psp_pre_setting');
			$license = isset($psp_pre_settings['psp_premium_license_key']) ? trim(esc_attr($psp_pre_settings['psp_premium_license_key'])) : '';
			$clientid = isset($psp_pre_settings['psp_premium_client_id']) ? trim (esc_attr($psp_pre_settings['psp_premium_client_id'])) : '';
			$status = isset($psp_pre_setting['psp_premium_license_key_status']) ? trim (esc_attr($psp_pre_setting['psp_premium_license_key_status'])) : '';
			$psp_license_status = "";
			
			if( $status !== "" && $status == 'valid' ) {
				return;
			}
				
			if(!$license ||  !$clientid) {
				return;
			}
			
			$home_url = esc_url(home_url());
			$urlparts = parse_url(esc_url(home_url()));
            $domain = $urlparts['host'];
            //$installed_version = '2.0';
			if (class_exists('PsppMain')) {
				global $pspp_db_version;
				$installed_version = $pspp_db_version;
			} else {
				$installed_version = '2.0'; 
			}
			$installed_date = get_option( 'pspp_installed_dt' );
			if (!$installed_date) $installed_date = "";

			// data to send in our API request
			$api_params = array(
				'pspp-action'=> 'activate_license',
				'license_key' 	=> $license,
				'clientid' 	=> $clientid,
				'domain' 	=> $domain,
				'installed_version' => $installed_version,
				'installed_date' => $installed_date,
				'item_name' => urlencode( self::$PSPP_ITEM_NAME ), 
				'url'       => $home_url
			);

			// Call the custom API.
			$response = wp_remote_post( self::$PSPP_SITE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
			
			//error_log("Response ".print_r($response, true));

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = sanitize_text_field($response->get_error_message());
				} else {
					$message = esc_html__( 'An error occurred, please try again.', 'platinum-seo-pack' );
				}

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				//error_log('license data '.print_r($license_data, true));
				if ( $license_data && 'FAIL' === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :
                            /**
							$message = sprintf(
								esc_html__( 'Your license key expired on %s.',  'platinum-seo-pack' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
								
							
							);
							**/
							$message = esc_html__( 'Your license key has  expired. Kindly buy a fresh license key' ,  'platinum-seo-pack');
							break;

						case 'revoked' :

							$message = esc_html__( 'Your license key has been disabled.' ,  'platinum-seo-pack');
							break;
							
						case 'license_already_active' :

							//$message = __( 'Your domain is already active using this license key.' );
							$psp_license_status = "activated";
							break;
							
						case 'empty_license' :

							$message = esc_html__( 'lience key is empty.' ,  'platinum-seo-pack' );
							break;
							
						case 'invalid_client_id' :

							$message = esc_html__( 'Client ID is Imvalid.' ,  'platinum-seo-pack' );
							break;

						case 'missing' :

							$message = esc_html__( 'Invalid license.' ,  'platinum-seo-pack');
							break;
							
						case 'empty_request' :

							$message = esc_html__( 'Request was empty. Try again.' ,  'platinum-seo-pack');
							break;
							
						case 'invalid_request' :

							$message = esc_html__( 'Request was invalid.',  'platinum-seo-pack' );
							break;
							
						case 'empty_clientid' :

							$message = esc_html__( 'Client ID was empty. Try again.',  'platinum-seo-pack' );
							break;
							
						case 'empty_domain' :

							$message = esc_html__( 'Domain name was empty.',  'platinum-seo-pack' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = esc_html__( 'Your license is not active for this URL.',  'platinum-seo-pack' );
							break;

						case 'item_name_mismatch' :

							$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.' ,  'platinum-seo-pack'), self::$PSPP_ITEM_NAME );
							break;

						case 'no_activations_left':

							$message = esc_html__( 'Your license key has reached its activation limit.',  'platinum-seo-pack' );
							break;

						default :

							$message = esc_html__( 'An error occurred, please try again.' ,  'platinum-seo-pack');
							break;
					}

				}

			}

			// decode the license data
			//$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "valid" or "invalid"
			
			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				//$base_url = admin_url( 'plugins.php?page=' . self::$PSPP_LICENSE_PAGE );
				//$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
				
				add_settings_error('psp_pre_settings', 'activation_error', esc_html($message), 'error');
		        return false;

				//wp_redirect( $redirect );
				//exit();
			}
			//if (!$license_data->license) return;
			if( isset($license_data->license) && ($license_data->license == 'valid') || $psp_license_status == "activated" ) {
    			$psp_settings['psp_premium_license_key_status'] = "valid";
    			$psp_settings['premium'] = 1;
    			$psp_pre_setting['psp_premium_license_key_status'] = 1;
    			
    			if ( isset( $license_data->created ) && !empty($license_data->created) ) {
					$created = $license_data->created;
					error_log($created);
					update_option( 'pspp_buy_dt', $created );
				}
    			
    			
    			update_option( 'psp_sitewide_settings', $psp_settings );
    			update_option( 'psp_pre_setting', $psp_pre_setting );
    			update_option( 'pspp_license_status', 'valid' );
				
				
			    
    			//$base_url = admin_url( 'plugins.php?page=' . self::$PSPP_LICENSE_PAGE );
                $message = esc_html__( 'License key successfully activated for this domain!', 'platinum-seo-pack' );
    			//$redirect = add_query_arg( array( 'sl_activation' => 'success', 'message' => urlencode( $message ) ), $base_url );
    			//Rajesh - captures successful activation
    			add_settings_error('psp_pre_settings', esc_html( 'activation_success' ), $message, 'updated');
    			return false;
    			//wp_redirect( $redirect );
    			//Rajesh - old redirect without capturing success message
    			//wp_redirect( admin_url( 'admin.php?page=' . self::$PSPP_LICENSE_PAGE ) );
    			//exit();
			}
			
			//wp_redirect( admin_url( 'plugins.php?page=' . self::$PSPP_LICENSE_PAGE ) );
    		//exit();
    		$message = esc_html__( 'Some unknown error occured during activation, Pls. try Again!', 'platinum-seo-pack'  );
    		add_settings_error('psp_pre_settings', esc_html( 'activation_error' ), $message, 'error');
            //return false;
		}
	}
	
	//deactivate license
	function pspp_deactivate_license() {

		// listen for our de-activate button to be clicked
		if( isset( $_POST['pspp_license_deactivate'] ) ) {

			// run a quick security check
			if( ! check_admin_referer( 'pspp_nonce', 'pspp_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			//$license = trim( get_option( 'pspp_license_key' ) );
			$psp_settings = $this->psp_settings;
			$psp_pre_settings = $this->psp_pre_settings;
			$psp_pre_setting = get_option('psp_pre_setting');
			$psp_license_status = "";
			
		    $license = isset($psp_pre_settings['psp_premium_license_key']) ? trim(esc_attr($psp_pre_settings['psp_premium_license_key'])) : '';
			$clientid = isset($psp_pre_settings['psp_premium_client_id']) ? trim (esc_attr($psp_pre_settings['psp_premium_client_id'])) : '';
			$status = isset($psp_pre_setting['psp_premium_license_key_status']) ? trim (esc_attr($psp_pre_setting['psp_premium_license_key_status'])) : '';
			
			if(! $status ) {
				return;
			}
				
			if(!$license ||  !$clientid) {
				return;
			}
			
			$home_url = esc_url(home_url());
			$urlparts = parse_url(esc_url(home_url()));
            $domain = $urlparts['host'];
            //$installed_version = '2.0';
			if (class_exists('PsppMain')) {
				global $pspp_db_version;
				$installed_version = $pspp_db_version;
			} else {
				$installed_version = '2.0'; 
			}
			$installed_date = get_option( 'pspp_installed_dt' );
			if (!$installed_date) $installed_date = "";

			// data to send in our API request
			$api_params = array(
				'pspp-action'=> 'deactivate_license',
				'license_key' 	=> $license,
				'clientid' 	=> $clientid,
				'domain' 	=> $domain,
				'installed_version' => $installed_version,
				'installed_date' => $installed_date,
				'item_name' => urlencode( self::$PSPP_ITEM_NAME ), // the name of our product in PSPP
				'url'       => $home_url
			);

			// Call the custom API.
			//$response = wp_remote_post( self::$PSPP_SITE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
			
			// Call the custom API.
			$response = wp_remote_post( self::$PSPP_SITE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
			
			//error_log(print_r($response,true));

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = sanitize_text_field($response->get_error_message());
				} else {
					$message = esc_html__( 'An error occurred while deactivating, please try again.' , 'platinum-seo-pack' );
				}

				//$base_url = admin_url( 'admin.php?page=' . self::$PSPP_LICENSE_PAGE );
				//$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				//wp_redirect( $redirect );
				//exit();
			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
                //error_log('license data '.print_r($license_data, true));
				if ( $license_data && 'FAIL' === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :

							$psp_license_status = "deactivated";
							break;

						case 'revoked' :

							$psp_license_status = "deactivated";
							break;
							
						case 'license_already_active' :

							//$message = __( 'Your domain already uses this license key.' );
							$psp_license_status = "deactivated";
							break;

						case 'empty_license' :

							//$message = esc_html__( 'lience key was empty.' , 'platinum-seo-pack');
							$psp_license_status = "deactivated";
							break;
							
						case 'invalid_client_id' :

							//$message = esc_html__( 'Client ID is Imvalid.', 'platinum-seo-pack' );
							$psp_license_status = "deactivated";
							break;
							
						case 'missing' :

							$psp_license_status = "deactivated";
							break;
							
						case 'empty_request' :

							$message = esc_html__( 'Request was empty. Try again.', 'platinum-seo-pack' );
							break;
							
						case 'invalid_request' :

							$message = esc_html__( 'Request was invalid.' , 'platinum-seo-pack');
							break;
							
						case 'empty_clientid' :

							//$message = esc_html__( 'Client ID was empty. Try again.' , 'platinum-seo-pack');
							$psp_license_status = "deactivated";
							break;
							
						case 'empty_domain' :

							$message = esc_html__( 'Domain name was empty.' , 'platinum-seo-pack');
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = esc_html__( 'Your license is not active for this URL.' , 'platinum-seo-pack');
							break;

						case 'item_name_mismatch' :

							$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.' , 'platinum-seo-pack'), self::$PSPP_ITEM_NAME );
							break;

						case 'no_activations_left':

							//$message = __( 'Your license key has reached its activation limit.' );
							$psp_license_status = "deactivated";
							break;

						default :

							$message = esc_html__( 'An error occurred, please try again.' , 'platinum-seo-pack');
							break;
					}

				}
				
			}
			
			//Further processing for deactivation
			
			
			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				//$base_url = admin_url( 'admin.php?page=' . self::$PSPP_LICENSE_PAGE );
				//$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
                
                //error_log('message '.print_r($message, true));
				//wp_redirect( $redirect );
				//exit();
				add_settings_error('psp_pre_settings', 'deactivation_error', esc_html($message), 'error');
				return false;
			}

			// decode the license data
			//$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			// $license_data->license will be either "deactivated" or "failed"

		    //if deactivated successfully
			if( (isset($license_data) && ($license_data->license == 'deactivated')) || $psp_license_status == "deactivated" ) {
			    
			    $psp_pre_setting['psp_premium_license_key_status'] = "";
				$psp_settings['psp_premium_license_key_status'] = "";
				//unset($psp_settings['psp_premium_license_key_status']);
				
				update_option( 'psp_sitewide_settings', $psp_settings );
				update_option( 'psp_pre_setting', $psp_pre_setting );
				delete_option( 'pspp_license_status' );
				
				$links_hook = wp_next_scheduled( 'pspp_links_cron_hook' );
				if ($links_hook) {
					wp_unschedule_event( $links_hook, 'pspp_links_cron_hook' );
				}
				
				$message = esc_html__( 'License key successfully deactivated for this domain!','platinum-seo-pack' );
				
	            add_settings_error('psp_pre_settings', 'deactivation_success', esc_html($message), 'updated');
                return false;
                //$base_url = admin_url( 'admin.php?page=' . self::$PSPP_LICENSE_PAGE );
                //$message = __( 'License key successfully deactivated!' );
    			//$redirect = add_query_arg( array( 'sl_activation' => 'success', 'message' => urlencode( $message ) ), $base_url );
    			//Rajesh - captures successful deactivation
    			//wp_redirect( $redirect );
    			//Rajesh - old redirect without capturing success message
    			//wp_redirect( admin_url( 'plugins.php?page=' . self::$PSPP_LICENSE_PAGE ) );
    			//exit();
			
			}
			
			$message = esc_html__( 'Some unknown error occured during deactivation, Pls. try Again!', 'platinum-seo-pack'  );
    		add_settings_error('psp_pre_settings', 'deactivation_error', esc_html($message), 'error');
    		//return false;
			
			//wp_redirect( admin_url( 'admin.php?page=' . self::$PSPP_LICENSE_PAGE ) );
    		//exit();

		}
	}
	
	//check license status
	function pspp_check_license() {	

		// retrieve the license from the database
		//$license = trim( get_option( 'pspp_license_key' ) );
		$psp_pre_settings = $this->psp_pre_settings;
		$psp_settings = $this->psp_settings;
		$psp_pre_setting = get_option('psp_pre_setting');
		$license = isset($psp_pre_settings['psp_premium_license_key']) ? trim(esc_attr($psp_pre_settings['psp_premium_license_key'])) : '';
		$clientid = isset($psp_pre_settings['psp_premium_client_id']) ? trim (esc_attr($psp_pre_settings['psp_premium_client_id'])) : '';
		$status = isset($psp_pre_setting['psp_premium_license_key_status']) ? trim (esc_attr($psp_pre_setting['psp_premium_license_key_status'])) : '';
		$psp_license_status = "";					
			
		if(!$license ||  !$clientid) {
			return;
		}	
		
		$home_url = esc_url(home_url());
		$urlparts = parse_url(esc_url(home_url()));
		$domain = $urlparts['host'];
		if (class_exists('PsppMain')) {
		    global $pspp_db_version;
			$installed_version = $pspp_db_version;
		} else {
			$installed_version = '2.0'; 
		}
		$installed_date = get_option( 'pspp_installed_dt' );
		if (!$installed_date) $installed_date = "";
		// data to send in our API request
		$api_params = array(
			'pspp-action'=> 'check_license',
			'license_key' 	=> $license,
			'clientid' 	=> $clientid,
			'domain' 	=> $domain,
			'installed_version' => $installed_version,
			'installed_date' => $installed_date,
			'item_name' => urlencode( self::$PSPP_ITEM_NAME ), 
			'url'       => $home_url
		);

		// Call the custom API.
		$response = wp_remote_post( self::$PSPP_SITE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		
		//error_log("Response ".print_r($response, true));

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = sanitize_text_field($response->get_error_message());
			} else {
				$message = esc_html__( 'An error occurred, please try again.', 'platinum-seo-pack' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			
			if ( isset( $license_data->created ) && !empty($license_data->created) ) {
				update_option( 'pspp_buy_date', $license_data->created );
			}
			//error_log('license data '.print_r($license_data, true));
			if ( $license_data && 'FAIL' === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						/**$message = sprintf(
							esc_html__( 'Your license key expired on %s.',  'platinum-seo-pack' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);**/
						$psp_license_status == "expired";
						break;

					case 'revoked' :
						
						$psp_license_status == "invalid";
						//$message = esc_html__( 'Your license key has been disabled.' ,  'platinum-seo-pack');
						break;
						
					case 'license_already_active' :

						//$message = __( 'Your domain is already active using this license key.' );
						$psp_license_status == "invalid";
						break;
						
					case 'empty_license' :

						//$message = esc_html__( 'lience key is empty.' ,  'platinum-seo-pack' );
						$psp_license_status == "invalid";
						break;
						
					case 'invalid_client_id' :

						//$message = esc_html__( 'Client ID is Imvalid.' ,  'platinum-seo-pack' );
						$psp_license_status == "invalid";
						break;

					case 'missing' :

						//$message = esc_html__( 'Invalid license.' ,  'platinum-seo-pack');
						$psp_license_status == "expired";
						break;
						
					case 'empty_request' :

						//$message = esc_html__( 'Request was empty. Try again.' ,  'platinum-seo-pack');
						$psp_license_status == "invalid";
						break;
						
					case 'invalid_request' :

						//$message = esc_html__( 'Request was invalid.',  'platinum-seo-pack' );
						$psp_license_status == "invalid";
						break;
						
					case 'empty_clientid' :

						//$message = esc_html__( 'Client ID was empty. Try again.',  'platinum-seo-pack' );
						$psp_license_status == "invalid";
						break;
						
					case 'empty_domain' :

						//$message = esc_html__( 'Domain name was empty.',  'platinum-seo-pack' );
						$psp_license_status == "invalid";
						break;

					case 'invalid' :
					case 'site_inactive' :

						//$message = esc_html__( 'Your license is not active for this URL.',  'platinum-seo-pack' );
						$psp_license_status == "invalid";
						break;

					case 'item_name_mismatch' :

						//$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.' ,  'platinum-seo-pack'), self::$PSPP_ITEM_NAME );
						$psp_license_status == "invalid";
						break;

					case 'no_activations_left':

						//$message = esc_html__( 'Your license key has reached its activation limit.',  'platinum-seo-pack' );
						$psp_license_status == "invalid";
						break;

					default :

						//$message = esc_html__( 'An error occurred, please try again.' ,  'platinum-seo-pack');
						$psp_license_status == "invalid";
						break;
				}

			}

		}		
		
		if( $psp_license_status == "expired" ) {
			
			$psp_pre_settings['psp_premium_license_key_status'] = "";
			$psp_pre_settings['premium'] = 1;
		
			update_option( 'psp_pre_setting', $psp_pre_settings );
			
			$psp_settings = get_option('psp_sitewide_settings');
			$separator = html_entity_decode($psp_settings['separator']);
			$psp_settings['separator'] = $separator;		
			/**
			if (array_key_exists("psp_premium_license_key_status", $psp_settings)) {
				unset($psp_settings['psp_premium_license_key_status']);
			}
			**/
			$psp_settings['premium'] = 1;
			$psp_settings['psp_premium_license_key_status'] = "";
			delete_option("psp_sitewide_settings");
			add_option("psp_sitewide_settings", $psp_settings);
			
			delete_option( 'pspp_license_status' );
			update_option( 'pspp_installed_dt', '' );
			//update_option( 'pspp_buy_dt', '' );
			update_option( 'pspp_db_version', '' );	
			
			//delete_option("psp_pre_setting");			
				
			$links_hook = wp_next_scheduled( 'pspp_links_cron_hook' );
			if ($links_hook) {
				wp_unschedule_event( $links_hook, 'pspp_links_cron_hook' );
			}
			
			$vlk_hook = wp_next_scheduled( 'psp_vlk' );
			if ($vlk_hook) {
				wp_unschedule_event( $vlk_hook, 'psp_vlk' );
			}
		
		}					
		
		//$message = esc_html__( 'Some unknown error occured during validation, Pls. try Again!', 'platinum-seo-pack'  );	
		return;
			
	}
	
	/**
	 * This is a means of catching errors from the activation method above and displaying it to the customer
	 */
	 /***
	function pspp_admin_notices() {
		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch( Sanitize_key($_GET['sl_activation']) ) {

				case 'false':
					$message = urldecode( Sanitize_key($_GET['message']) );
					?>
					<div class="notice notice-error">
						<p><?php echo esc_html($message); ?></p>
					</div>
					<?php
					break;
				case 'success':
					$message = urldecode( Sanitize_key($_GET['message']) );
					?>
					<div class="notice notice-success is-dismissible">
						<p><?php echo esc_html($message); ?></p>
					</div>
					<?php
					break;

				case 'true':
				default:
					break;

			}
		}
	}
	***/
	function psp_pre_options_page() { ?>
	    <?php wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
	    include_once( 'psp_tools_renderer.php' ); ?>
	<?php }
	
	/*
	 * renders Plugin settings page, checks
	 * for the active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function psp_premium_options_page() {
		$tab = isset( $_GET['psppretab'] ) ? Sanitize_key($_GET['psppretab']) : $this->psp_pre_security_settings_group;
		$psp_button = "submit";		
		?>
		<div class="wrap">		
			<h1 style='line-height:30px;'><?php esc_html_e('Techblissonline Platinum SEO Premium - License Manager', 'platinum-seo-pack') ?></h1>
			<p style="color: red"><?php esc_html_e('You need to click the "Save Settings" button to save the changes you made to each individual tab before moving on to the next tab.', 'platinum-seo-pack') ?></p>
			<?php $this->psp_pre_options_tabs(); ?>
			<form name="platinum-seo-form" method="post" action="options.php">
				<?php wp_nonce_field( 'update-pre-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php settings_errors(); ?>
				<?php do_settings_sections( $tab ); ?>
				<?php $psp_pre_settings = get_option('psp_pre_settings');
				      $psp_pre_setting = get_option('psp_pre_setting');     
						$license = isset($psp_pre_settings['psp_premium_license_key']) ? esc_attr($psp_pre_settings['psp_premium_license_key']) : false;
						$status = isset($psp_pre_setting['psp_premium_license_key_status']) ? esc_attr($psp_pre_setting['psp_premium_license_key_status']) : false;
				?>
				<?php if( false !== $license && $status) { ?>
						<tr valign="top">	
							<th scope="row" valign="top">
								<?php esc_html_e(''); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 1 ) { ?>
									<span style="color:green;"><?php esc_html_e('active'); ?></span>
									<?php wp_nonce_field( 'pspp_nonce', 'pspp_nonce' ); ?>
									<input type="submit" class="button-secondary" name="pspp_license_deactivate" value="<?php esc_html_e('Deactivate License'); ?>"/>
								<?php } else {
									$psp_button = "pspp_license_activate";
									wp_nonce_field( 'pspp_nonce', 'pspp_nonce' ); ?>
									<input type="submit" class="button-secondary" name="pspp_license_activate" value="<?php esc_html_e('Activate License'); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } else if ( false !== $license && !$status) { ?>
					    <tr valign="top">	
							<th scope="row" valign="top">
								<?php esc_html_e(''); ?>
							</th>
							<td>
					<?php    $psp_button = "pspp_license_activate"; ?>
					        <span style="color:red;"><?php esc_html_e('inactive'); ?></span>
					<?php    wp_nonce_field( 'pspp_nonce', 'pspp_nonce' ); ?>
						    <input type="submit" class="button-secondary" name="pspp_license_activate" value="<?php esc_html_e('Activate License'); ?>"/>
						    </td>
						</tr>
					<?php } ?>
				<?php submit_button('Save Credentials', 'primary', $psp_button, true, 'id="submit"'); ?>
			</form>
		
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
	function psp_pre_options_tabs() {
		$current_tab = isset( $_GET['psppretab'] ) ? Sanitize_key($_GET['psppretab']) : $this->psp_pre_security_settings_group;	
		//wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
		wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), '2.3.5');
		wp_enqueue_style("psp-settings-bswide-css", plugins_url( '/css/psp-settings-bswide.css', __FILE__ ));
		//screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->psp_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr($active) . '" href="?page=' . esc_attr($this->psp_plugin_options_key) . '&psppretab=' . esc_attr($tab_key) . '">' . esc_attr($tab_caption) . '</a>';	
		}
		echo '</h2>';
	}
}