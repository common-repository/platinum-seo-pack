<?php

/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: Role Capability Manager Class
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/ 
*/

class PspRcapSettings extends PspSettings {	
	 
	private static $obj_handle = null;	
	
	// this is the URL our updater / license checker pings. 
	private static $PSPP_SITE_URL = 'https://techblissonline.com/tools/platinum-seo-wordpress-premium/'; 

	// Product Name
	//private static $PSPP_ITEM_NAME = 'techblissonline_platinum_seo_premium'; 

	// the name of the settings page for the license input field to be displayed
	//private static $PSPP_LICENSE_PAGE = 'psp-rolecap';//'pspp-license';
	
	private $psp_helper;
	private $psp_settings_instance;
	private $sitename;
	private $sitedescription;	
	
	private $plugin_settings_tabs = array();
	 
	private $psp_role_cap_settings_group = 'psp_role_capabilities';//'psp_pre_credentials';	
	
	protected $psp_plugin_options_key = 'psp-rcap-by-techblissonline';//'psp-pre-by-techblissonline';
	//private $psp_plugin_lic_key = 'psp-pre-by-techblissonline';
	private $psp_settings_tabs = array();

	//private $psp_pre_settings = array();
	private $psp_rcap_settings = array();
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
		
		$this->psp_settings_tabs[$this->psp_role_cap_settings_group] = 'Role Capabilities';
		//$this->psp_settings_tabs[$this->psp_home_settings_group] = 'Home';
		$this->psp_settings = get_option("psp_sitewide_settings");
		
		add_action( 'admin_init', array( &$this, 'psp_rcap_settings_init' ) );
		//add_action( 'admin_init', array( &$this, 'pspp_init_plugin_updater' ), 		
		
	}
	
	function psp_rcap_settings_init() {		
		
		$tab = isset( $_GET['psprolecaptab'] ) ? Sanitize_key($_GET['psprolecaptab']) : $this->psp_role_cap_settings_group;
		
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
	
		$this->register_role_cap_settings();			
		
	}	
	
	/*
	 * Registers the Home SEO settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_role_cap_settings() {
		$this->psp_settings_tabs[$this->psp_role_cap_settings_group] = 'Role Capabilities';		
		$psp_rcap_settings_name = "psp_rolecap"; //"psp_rcap_settings";
		
		$psp_rcap_settings = get_option($psp_rcap_settings_name);
		if (!empty($psp_rcap_settings)) $this->psp_rcap_settings = $psp_rcap_settings;
		
		$psp_pre_settings = get_option('psp_pre_setting');		
		$psp_premium_valid = isset($psp_pre_settings['psp_premium_license_key_status']) ? $psp_pre_settings['psp_premium_license_key_status'] : '';
		
		//wp_enqueue_script( 'psp-input-toggler', plugins_url( '/js/pspinputtypetoggler.js', __FILE__ ), array( 'jquery' ) );
		//wp_enqueue_script( 'psp-input-toggler', plugins_url( '/js/pspinputtypetoggler.js', __FILE__ ), array( 'jquery' ), '2.1.7');
		//register
		register_setting( $this->psp_role_cap_settings_group, $psp_rcap_settings_name, array( &$this, 'psp_sanitize_capabilities' ) );
		//add Section
		add_settings_section( 'psp_section_roles_capabilities', esc_html__('', 'platinum-seo-pack' ), array( &$this, 'section_roles_capabilities_desc' ), $this->psp_role_cap_settings_group );
		
		$user_roles = get_editable_roles();
		
		//$psp_capabilities = array( 'metabox' => 'Platinum SEO MetaBox', 'basicseo' => 'Generic SEO', 'analysis' => 'SEO Analysis', 'advancedseo' => 'Advanced SEO', 'basicsocial' => 'Basic Social', 'advancedsocial' => 'Advanced social', 'internallinks' => 'Internal Links', 'seosettings' => 'SEO Settings', 'general' => 'SEO - General', 'home' => 'SEO - Home', 'posttype' => 'SEO - PostType', 'taxonomy' => 'SEO - Taxonomy', 'archives' => 'SEO - Archives', 'permalinks' => 'SEO - Permalinks', 'sitemaps' => 'SEO - Sitemaps', 'others' => 'SEO - Others', 'breadcrumbs' => 'SEO - Breadcrumbs', 'socialsettings' => 'Social', 'editors' => 'Editors', 'importer' => 'Importers', 'redirections' => 'Redirections', 'manager404' => '404 manager', 'license' => 'Premium License', 'auditsettings' => 'Audit Settings', 'auditreports' => 'Audit Reports');
		
		if ($psp_premium_valid) {
		
		    $psp_capabilities = array( 'metabox' => 'Platinum SEO MetaBox',  'analysis' => 'SEO Analysis', 'advancedseo' => 'Advanced SEO', 'basicsocial' => 'Basic Social', 'advancedsocial' => 'Advanced social', 'internallinks' => 'Internal Links', 'psp_general' => 'Platinum SEO & Social Pack', 'psp_home' => 'SEO - Home', 'psp_pt' => 'SEO - PostType', 'psp_taxonomy' => 'SEO - Taxonomy', 'psp_archive' => 'SEO - Archives', 'psp_permalink' => 'SEO - Permalinks', 'psp_sitemap' => 'SEO - Sitemaps', 'psp_others' => 'SEO - Others', 'psp_breadcrumb' => 'SEO - Breadcrumbs', 'psp_social' => 'Social', 'psp_tools' => 'Tools', 'psp_bulkeditor' => 'Bulk Editor', 'psp_robotstxt' => 'Robots.txt Editor', 'psp_analytics' => 'GA Tracking Code Editor', 'psp_htaccess' => '.htaccess Editor', 'psp_importer' => 'Import from other Plugins', 'psp_pluginsettings' => 'Export/Import Settings & Meta Data', 'redirections' => 'Redirections', 'manager404' => '404 manager', 'adminbarmenu' => 'AdminBar Menu', 'auditsettings' => 'Audit Settings', 'auditreports' => 'Audit Reports');
		} else {
		    
		    $psp_capabilities = array( 'metabox' => 'Platinum SEO MetaBox',  'analysis' => 'SEO Analysis', 'advancedseo' => 'Advanced SEO', 'basicsocial' => 'Basic Social', 'advancedsocial' => 'Advanced social', 'internallinks' => 'Internal Links', 'psp_general' => 'Platinum SEO & Social Pack', 'psp_home' => 'SEO - Home', 'psp_pt' => 'SEO - PostType', 'psp_taxonomy' => 'SEO - Taxonomy', 'psp_archive' => 'SEO - Archives', 'psp_permalink' => 'SEO - Permalinks', 'psp_sitemap' => 'SEO - Sitemaps', 'psp_others' => 'SEO - Others', 'psp_breadcrumb' => 'SEO - Breadcrumbs', 'psp_social' => 'Social', 'psp_tools' => 'Tools', 'psp_bulkeditor' => 'Bulk Editor', 'psp_robotstxt' => 'Robots.txt Editor', 'psp_analytics' => 'GA Tracking Code Editor', 'psp_htaccess' => '.htaccess Editor', 'psp_importer' => 'Import from other Plugins', 'psp_pluginsettings' => 'Export/Import Settings & Meta Data', 'redirections' => 'Redirections', 'manager404' => '404 manager', 'adminbarmenu' => 'AdminBar Menu');
		    
		}
		
		$psp_excluded_roles = array('administrator', 'subscriber');
		
		foreach ( $user_roles as $role => $details ) {
		    /***
		    if ($role === "administrator") {
		        continue;
		    }
			***/ 
			//$user_permissions = get_role( $role );
		    // Add a new capability to all roles
            //$user_permissions->add_cap( 'psp_capability', true );
            
		    if ( in_array( $role, $psp_excluded_roles )) {
		        continue;
		    }
			
			$rolename = translate_user_role( $details['name'] );
			//add fields
			$psp_role_cap_field     = array (
				'label_for' 	=> 'psp_'.$role.'_id',
				'option_name'   => $psp_rcap_settings_name."[".$role."][]",
				'option_value'  => isset($psp_rcap_settings[$role]) ? ($psp_rcap_settings[$role]) : '',
				'checkboxitems' => $psp_capabilities,
				'option_description' => esc_html__( 'Enter your Platinum SEO and Social Premium Pack License Key. The license key is used for access to premium features and their upgrades.', 'platinum-seo-pack' ),
			);
			add_settings_field( 'psp_'.$role.'_id', $rolename, array( &$this, 'psp_add_cbx_array' ), $this->psp_role_cap_settings_group, 'psp_section_roles_capabilities',  $psp_role_cap_field);	

		}			
	}
	
	function psp_add_cbx_array(array $args) {
		
		$option_name   = isset($args['option_name']) ? esc_attr($args['option_name']) : '';
		$id     = isset($args['label_for']) ? esc_attr($args['label_for']) : '';
		$option_array_value     = isset($args['option_value']) ?  (array) $args['option_value']  : array();
		$option_array_value = array_map( 'esc_attr', $option_array_value );		
		$checkboxitems = isset($args['checkboxitems']) ? $args['checkboxitems'] : array();//array
		//$option_description     = isset($args['option_description']) ? esc_attr( $args['option_description'] ) : '';
		
		$counter = 1;
		$colcounter = 1;
		
		//include renderer
		echo '<div class="psp-bs cbxarr">';
		foreach ( $checkboxitems as $checkboxitemkey => $checkboxitemvalue ) {
		
			$checkbox_id = esc_attr($id."-cbx-item-".$counter);
			$checked = in_array($checkboxitemkey, $option_array_value) ? 'checked="checked"' : '';
			//echo "<input id='$checkbox_id' $checked type='checkbox' name='$option_name' value='$checkboxitem' /><label class='psp-radio-separator' for='$radio_id'>$checkboxitemvalue</label>";
			
			
			if ( $colcounter === 1 ) echo '<div class="row">';
						
			echo '<div class="cbx col-sm-4">';  
			echo "<li class='rcap'><input ".$checked." id='$checkbox_id' data-toggle='toggle' data-size='mini' data-onstyle='success' name='$option_name' value='".esc_attr($checkboxitemkey)."' type='checkbox' /><span>&nbsp;</span><span for='$id'>".esc_attr($checkboxitemvalue)."</span></li><br />";
			echo '</div>';
						
			if ( $colcounter === 3 ) {
				echo '</div>';	
				$colcounter = 1;	
			} else {
				$colcounter = $colcounter + 1;
			}
					
			$counter = $counter + 1;
		
		}
		echo '</div>';
		
	}
	
	function section_roles_capabilities_desc() {echo ''; }	
	
	function psp_sanitize_capabilities( $settings ) {
	    
	    //error_log(print_r($settings, true));
		
		//$psp_capabilities = array( 'metabox', 'basicseo', 'analysis', 'advancedseo', 'basicsocial', 'advancedsocial', 'internallinks', 'seosettings', 'general', 'home', 'posttype', 'taxonomy', 'archives', 'permalinks', 'sitemaps', 'others', 'breadcrumbs', 'socialsettings', 'editors', 'importer', 'redirections', 'manager404', 'license', 'auditsettings', 'auditreports' );
		
		$psp_capabilities = array( 'metabox', 'basicseo', 'analysis', 'advancedseo', 'basicsocial', 'advancedsocial', 'internallinks', 'psp_general', 'psp_home', 'psp_pt', 'psp_taxonomy', 'psp_archive', 'psp_permalink', 'psp_sitemap', 'psp_others', 'psp_breadcrumb', 'psp_social', 'psp_tools', 'psp_bulkeditor', 'psp_htaccess', 'psp_analytics', 'psp_robotstxt', 'psp_pluginsettings', 'psp_importer', 'redirections', 'manager404', 'adminbarmenu', 'psp_pre_credentials', 'auditsettings', 'auditreports' );
	
		$capabilities = array();
		$role_capability_arr = array();
		
		$user_roles = get_editable_roles();
		$user_roles_arr = array();
		
		foreach ( $user_roles as $role => $details ) {
			//$user_roles_arr[] = $role;	
			if ( !empty ($settings[$role]) ) {				
				$capabilities = $settings[$role];
				$role_capability_arr = array();
				foreach ( $capabilities as $capability ) {					
					if ( in_array($capability, $psp_capabilities) ) {
						$role_capability_arr[] = $capability;
					}						
				}
				$settings[$role] = $role_capability_arr;
			}
		}		
		return $settings;
	}
	
	/*
	 * renders Plugin settings page, checks
	 * for the active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function psp_rolecap_options_page() {
		$tab = isset( $_GET['psprolecaptab'] ) ? Sanitize_key($_GET['psprolecaptab']) : $this->psp_role_cap_settings_group;
		$psp_button = "submit";			
		?>
		<style>
		li.rcap {
		    list-style-type: none;
		    background: #F0F2F4;
		    border: #74868d;
		    padding: 5px;
		    color: purple;/*#069de3*/
		    /*font-weight: bold;*/
		}
		.cbxarr {
		    padding-left: 20px !important;
		    margin-left: 10px !important;
		}
		.psp-bs .cbx {
	padding: 5px !important;
}</style>
		<div class="wrap">		
			<h1 style='line-height:30px;'><?php esc_html_e(' Techblissonline Platinum SEO - Role Manager', 'platinum-seo-pack') ?></h1>
			<p style="color: red"><?php //esc_html_e('You need to click the "Save Settings" button to save the changes you made to each individual tab before moving on to the next tab.', 'platinum-seo-pack') ?></p>
			<?php $this->psp_rolecap_options_tabs(); ?>
			<form name="platinum-seo-form1" method="post" action="options.php">
				<?php wp_nonce_field( 'update-rcap-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php settings_errors(); ?>
				<?php do_settings_sections( $tab ); ?>							
				<?php submit_button('Save Capabilities', 'primary', $psp_button, true, 'id="submit"'); ?>
			</form>	
		</div>
		<?php
	}
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * psp_options_page method.
	 */
	function psp_rolecap_options_tabs() {
		$current_tab = isset( $_GET['psprolecaptab'] ) ? Sanitize_key($_GET['psprolecaptab']) : $this->psp_role_cap_settings_group;	
		wp_enqueue_script( 'psp-bs-toggler-js', plugins_url( '/js/pspbstoggler.js', __FILE__ ) );
		wp_enqueue_style("'psp-bs-toggler-css", plugins_url( '/css/psp-bs-toggle.css', __FILE__ ));
		//wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));		
		//wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ));
		wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), '2.3.5');
		wp_enqueue_style("psp-settings-bswide-css", plugins_url( '/css/psp-settings-bswide.css', __FILE__ ));
		//screen_icon();
		echo '<h2 class="nav-tab-wrapper" align="center">';
		/***
		foreach ( $this->psp_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr($active) . '" href="?page=' . esc_attr($this->psp_plugin_options_key) . '&psprolecaptab=' . esc_attr($tab_key) . '">' . esc_attr($tab_caption) . '</a>';	
		}***/
		echo 'Roles and Capabilities</h2>';
	}
}