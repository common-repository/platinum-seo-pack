<?php

/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: Tools management class
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/

class PspToolSettings extends PspSettings {	
	 
	private static $obj_handle = null;	
	
	private $psp_helper;
	private $psp_settings_instance;
	private $sitename;
	private $sitedescription;
	
	private $plugin_settings_tabs = array();
	 
	private $psp_robots_settings_group = 'psp_robotstxt';
    private $psp_htaccess_settings_group = 'psp_htaccess';	
	private $psp_ga_settings_group = 'psp_analytics';
	private $psp_bulk_editor_group = 'psp_bulkeditor';
	private $psp_importer_group = 'psp_importer';
	private $psp_plugin_settings_group = 'psp_pluginsettings';
	private $psp_wizard_group = 'psp_wizard';
	
	protected $psp_plugin_options_key = 'psp-tools-by-techblissonline';
	private $psp_settings_tabs = array();

	public $robotstxt_file = '';
	public $htaccess_file  = '';
	public $sitemap_file  = '';
	
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
		
		$this->psp_settings_tabs[$this->psp_bulk_editor_group] = 'Bulk Editor';
		$this->psp_settings_tabs[$this->psp_importer_group] = 'Import from Other Plugins';
		$this->psp_settings_tabs[$this->psp_plugin_settings_group] = 'Export/import Settings & Meta Data';
		$this->psp_settings_tabs[$this->psp_ga_settings_group] = 'GA Tracking Code Editor';
		$this->psp_settings_tabs[$this->psp_robots_settings_group] = 'Robots.txt Editor';
		$this->psp_settings_tabs[$this->psp_htaccess_settings_group] = '.htaccess Editor';	
		$this->psp_settings_tabs[$this->psp_wizard_group] = 'SetUp wizard';
		
		$this->psp_import_instance = PspImporter::get_instance();
		
		$this->psp_wizard_instance = PspWizardSettings::get_instance();
		$this->psp_wizard_instance->psp_settings_instance = $this;
		
		add_action( 'admin_init', array( &$this, 'psp_tools_settings_init' ) );
		add_action( 'admin_menu', array( &$this, 'psp_tools_admin_menu' ) );
		
		add_action( 'wp_ajax_psp_update_meta_data', array($this, 'psp_update_meta_data' ), 1);
		
		//add_filter('set-screen-option', array(&$this, 'psp_set_screen_option'), 10, 3);	
		add_filter('set_screen_option_psp_bulkedit_rows_per_page', array(&$this, 'psp_set_screen_option'), 10, 3);	
		
		//$psp_bulkedit_page = "platinum-seo-and-social-pack_page_psp-tools-by-techblissonline";
		//add_action("load-$psp_bulkedit_page", array($this, 'psp_screen_options'));
	}
	
	function psp_tools_admin_menu() {
		
		$tab = isset( $_GET['psptools'] ) ? sanitize_key($_GET['psptools']) : $this->psp_bulk_editor_group;
		
		if ( $tab == $this->psp_bulk_editor_group ) {
			$psp_bulkedit_page = "platinum-seo-and-social-pack_page_psp-tools-by-techblissonline";
			add_action("load-$psp_bulkedit_page", array($this, 'psp_screen_options'));	
		}
		
	}
	
	function psp_tools_settings_init() {		
		
		$tab = isset( $_GET['psptools'] ) ? sanitize_key($_GET['psptools']) : $this->psp_bulk_editor_group;
		
		//if ( $tab == $this->psp_bulk_editor_group ) {
		//	$psp_bulkedit_page = "platinum-seo-and-social-pack_page_psp-tools-by-techblissonline";
		//	add_action("load-$psp_bulkedit_page", array($this, 'psp_screen_options'));	
		//}
		
		wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), "2.3.5");
		
		$this->robotstxt_file = get_home_path() . 'robots.txt';
		$this->htaccess_file  = get_home_path() . '.htaccess';
		$this->sitemap_file  = get_home_path() . 'sitemap.xml';
		
		$this->register_ga_settings();
		$this->register_robotstxt_settings();		
		$this->register_htaccess_settings();
		
	}
	
	/*
	 * Registers the google analytics tracking code and appends the
	 * key to the settings tabs array.
	 */
	private function register_ga_settings() {
		$this->psp_settings_tabs[$this->psp_ga_settings_group] = 'GA Tracking Code Editor';		
		$psp_ga_settings_name = "psp_ga_settings";		
		
		$psp_ga_settings = get_option($psp_ga_settings_name);					
		
		//register
		register_setting( $this->psp_ga_settings_group, $psp_ga_settings_name, array( &$this, 'sanitize_ga_settings' ) );
		//add Section
		add_settings_section( 'psp_section_ga', esc_html__('Google analytics Tracking', 'platinum-seo-pack' ), array( &$this, 'section_ga_desc' ), $this->psp_ga_settings_group );		

		//add fields	
		//Enable Google analytics tracking code addition by this plugin.
		$psp_ga_enable_field     = array (
            'label_for' 	=> 'psp_ga_enable',
            'option_name'   => $psp_ga_settings_name.'[ga_tc_enabled]',
			'option_value'  => isset($psp_ga_settings['ga_tc_enabled']) ? esc_attr($psp_ga_settings['ga_tc_enabled']) : '',
			'checkbox_label' => esc_html__( 'Yes', 'platinum-seo-pack' ),
			'option_description' => esc_html__( 'Check to add Google analytics Tracking Code with this plugin. If this is not checked, trscking code will not be added by this plugin.', 'platinum-seo-pack' ),
        );
		
		$psp_ga_enable_field_id = 'psp_ga_enable';		
		$psp_ga_enable_field_title = esc_html__('Add Tracking Code: ', 'platinum-seo-pack');	
		
		add_settings_field( $psp_ga_enable_field_id, $psp_ga_enable_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_ga_settings_group, 'psp_section_ga',  $psp_ga_enable_field);
		
		//tracking code entry textarea
		$psp_ga_tracking_code_field     = array (
            'label_for' 	=> 'psp_ga_tracking_code_id',
            'option_name'   => $psp_ga_settings_name.'[tracking_code]',
			'option_value'  => isset($psp_ga_settings['tracking_code']) ? html_entity_decode( $psp_ga_settings['tracking_code'], ENT_QUOTES) : '',
			'option_description'  => esc_html__( 'Here you may enter the google analytics tracking code for adding it across all pages of the site.', 'platinum-seo-pack' ),
        );		
		
		add_settings_field( 'psp_ga_tracking_code_id', esc_html__( 'Tracking Code:', 'platinum-seo-pack' ), array( &$this, 'psp_add_field_textarea_js' ), $this->psp_ga_settings_group, 'psp_section_ga', $psp_ga_tracking_code_field );
		
	}	
	
	function section_ga_desc() {echo '';}	
		
	function sanitize_ga_settings($settings) {
	     if ( isset( $settings['ga_tc_enabled'] ) ) {
			$settings['ga_tc_enabled'] = !is_null(filter_var($settings['ga_tc_enabled'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['ga_tc_enabled'] : '';	
		} else {
		    $settings['ga_tc_enabled'] = '';
		}
		
		if ( isset( $settings['tracking_code'] ) ) {
    		$allowed_html = array(
    			'script' => array(
    				'src' => array(),
    				'async' => array(),
    			),    
    		);

		    $settings['tracking_code'] = wp_kses($settings['tracking_code'], $allowed_html);
		    $settings['tracking_code'] = sanitize_textarea_field(htmlentities ($settings['tracking_code']) );
			//$settings['tracking_code'] = base64_encode( $settings['tracking_code'] );
		}
		
		return $settings;
	}
	
	/*
	 * Registers the Robots.txt editor settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_robotstxt_settings() {
		$this->psp_settings_tabs[$this->psp_robots_settings_group] = 'Robots.txt Editor';		
		$psp_robotstxt_settings_name = "psp_robotstxt_settings";		
		$disabled = 0;
		$option_description = "";
		$psp_tab = isset( $_GET['psptools'] ) ? sanitize_key($_GET['psptools']) : '';
		$psp_admin_page = isset( $_GET['page'] ) ? sanitize_key($_GET['page']) : '';
		//$psp_settings_updated = isset( $_GET['settings-updated'] ) ? sanitize_key($_GET['settings-updated']) : '';
		
		$psp_robotstxt_settings = get_option($psp_robotstxt_settings_name);
		
		$robotstxt_content = "";
		
		$robotstxt_content = $this->getDefaultRobots();
		
		//register
		register_setting( $this->psp_robots_settings_group, $psp_robotstxt_settings_name, array( &$this, 'sanitize_robotstxt_settings' ) );
		//add Section
		add_settings_section( 'psp_section_robotstxt', esc_html__('Robots.txt Editor', 'platinum-seo-pack' ), array( &$this, 'section_robotstxt_desc' ), $this->psp_robots_settings_group );

		//add fields		
		
		$robotstxt_file    = $this->robotstxt_file; 
		$use_virtual_robots_file = isset($psp_robotstxt_settings['use_virtual_robots_file']) ? esc_attr($psp_robotstxt_settings['use_virtual_robots_file']) : '';
		
		if ( ! file_exists( $robotstxt_file )) { 			
			if(( isset($_GET['settings-updated']) && true == sanitize_key($_GET['settings-updated'])) && ($psp_admin_page == 'psp-tools-by-techblissonline' ) && ($psp_tab == $this->psp_robots_settings_group ) && !$use_virtual_robots_file){
				//echo "do nothing";
				add_settings_error('psp_robotstxt_settings',  'use_virtual_robots_file', 'A physical robots.txt file has been created,', 'updated');
			} else {
				//virtual robots.txt file
				$virtual_robots_field     = array (
					'label_for' 	=> 'psp_virtual_robots_id',
					'option_name'   => $psp_robotstxt_settings_name.'[use_virtual_robots_file]',
					'option_value'  => $use_virtual_robots_file,
					'checkbox_label' => esc_html__( 'Yes, use a virtual robots.txt file', 'platinum-seo-pack' ),
					'option_description'  => esc_html__( 'Checking this will not create a physical robots.txt file but your robots.txt content will be visible to all visitors including search engine bots when they try to access the robots.txt file in the root. Even if you keep this unchecked, a physical robots.txt file will be created by Techblissonline platinum seo when you hit the "Save Settings" button.However, This will happen only if the file is writeable to root. If the file is not writeable to root, the content that you see here will be presented as a virtual rotots.txt file & this is done by wordpress by default.', 'platinum-seo-pack' ),	
				);			
					
				//$virtual_robots_field_id = 'psp_'.$setting_name.'_use_virtual_robots_file';		
				$virtual_robots_field_title = esc_html__( 'Do you want to use the virtual robots.txt file created by wordpress? ', 'platinum-seo-pack' );	
				
				add_settings_field( 'psp_virtual_robots_id', $virtual_robots_field_title, array( &$this, 'psp_add_field_checkbox' ), $this->psp_robots_settings_group, 'psp_section_robotstxt', $virtual_robots_field );
			}
		} else {
			
			//if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == true){
				//echo "do nothing";
			//} else {
		
				$robotstxt_file_handle = fopen( $robotstxt_file, 'r' );

				$content = '';
				if ( filesize( $robotstxt_file ) > 0 ) {
					$content = fread( $robotstxt_file_handle, filesize( $robotstxt_file ) );
				}
				$robotstxt_content = esc_textarea( $content );
				//$psp_robotstxt_settings['content'] = esc_textarea( $content );
				fclose( $robotstxt_file_handle );
			//}
			
			$option_description = "";
			if ( ! is_writable( $robotstxt_file ) ) {
				$disabled = 1;
				$option_description = esc_html__( 'Robots.txt file exists in the root but it is not writeable. Make sure that it is writeable for you to write into it here.', 'platinum-seo-pack' );
			}
			
		}		
		
		$content_field     = array (
            'label_for' 	=> 'psp_robotstxt_content',
            'option_name'   => $psp_robotstxt_settings_name.'[content]',
			'option_value'  => $robotstxt_content,
			'disabled'  => $disabled,
			'option_description'  => $option_description,
        );
		
		add_settings_field( 'psp_robotstxt_content', '<a href="'.home_url().'/robots.txt">'.esc_html__('Robots.txt Content: ', 'platinum-seo-pack').'</a>', array( &$this, 'psp_add_field_textarea' ), $this->psp_robots_settings_group, 'psp_section_robotstxt', $content_field );		
		
	}
	
	public function sanitize_robotstxt_settings($settings) {
	
		$use_virtual_robots_file = '';
		if ( isset( $settings['use_virtual_robots_file'] ) ) {
			$settings['use_virtual_robots_file'] = !is_null(filter_var($settings['use_virtual_robots_file'],FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE)) ? $settings['use_virtual_robots_file'] : '';
			$use_virtual_robots_file = 	$settings['use_virtual_robots_file'];
		}
		
		if( isset( $settings['content'] ) ) $settings['content'] = sanitize_textarea_field( $settings['content'] );	
		
		if( !empty( $settings['content'] ) ) {
			$this->psp_update_robots_txt($use_virtual_robots_file, $settings['content']);
		}
		return $settings;
	}
	
	private function psp_update_robots_txt($use_virtual_robots_file = '', $robotscontent) {		
		
		$robotstxt_file = $this->robotstxt_file; //;

		if (!$use_virtual_robots_file) {			
		
			if ( file_exists( $robotstxt_file )) {
				
				if ( is_writable( $robotstxt_file ) ) {
					$robotstxt_filehandle = fopen( $robotstxt_file, 'w+' );
					fwrite( $robotstxt_filehandle, $robotscontent );
					fclose( $robotstxt_filehandle );
					$msg = esc_html__( 'Updated Robots.txt', 'platinum-seo-pack' );
				}
			} else {
				if ( is_writable( get_home_path() ) ) {
					$robotstxt_filehandle = fopen( $robotstxt_file, 'x' );
					fwrite( $robotstxt_filehandle, $robotscontent );
					fclose( $robotstxt_filehandle );
					$msg = esc_html__( 'Created Robots.txt', 'platinum-seo-pack' );
				}					
			}
		}
		
	}
	
	public function section_robotstxt_desc() {
		$robotstxt_file    = $this->robotstxt_file; //;		
		if ( ! file_exists( $robotstxt_file ) ) {			
			echo '<p style="color: orange">'.esc_html__('A physical robots.txt file does not exist in the root.', 'platinum-seo-pack') . '</p>';			
		} else {
			if ( ! is_writable( $robotstxt_file ) ) {
				echo '<p style="color: orange">'.esc_html__('A physical robots.txt file exists in the root but it is not writeable. Ensure that it is writeable for you to edit it here.', 'platinum-seo-pack') . '</p>';
			}
		}
	}
	
	/**
     * Get the default robots.txt content.  This is copied as is from
     * WP's `do_robots` function
     * 
     * @since   1.0
     * @access  protected
     * @uses    get_option
     * @return  string The default robots.txt content
     */
    protected function getDefaultRobots() {
    
        $public = get_option('blog_public');

        $output = "User-agent: *\n";
        if ('0' == $public) {
            $output .= "Disallow: /\n";
        } else {
            $home_url =  site_url();
            $site_url = parse_url( site_url() );
			$path     = ( ! empty( $site_url['path'] ) ) ? $site_url['path'] : '';
            $output  .= "Disallow: $path/wp-admin/\n";
            $output  .= "Allow: $path/wp-admin/admin-ajax.php\n";
            //$output  .= "\nSitemap: $home_url/sitemap.xml\n"; 
            $sitemap_file    = $this->sitemap_file; 
		    if ( file_exists( $sitemap_file )) { 
                $output  .= "\nSitemap: $home_url/sitemap.xml\n"; 
		    }
        }
        
        $psp_robotstxt_settings = get_option('psp_robotstxt_settings');
		$psp_virtual_robots_content = isset($psp_robotstxt_settings['content']) ? stripcslashes(esc_textarea($psp_robotstxt_settings['content'])) : '';
		$use_virtual_robots_file = isset($psp_robotstxt_settings['use_virtual_robots_file']) ? esc_attr($psp_robotstxt_settings['use_virtual_robots_file']) : '';
		
		$robotstxt_file    = $this->robotstxt_file; 
		
		if (file_exists($robotstxt_file)) $use_virtual_robots_file = '';
		
		if (!empty($psp_virtual_robots_content) && $use_virtual_robots_file) {
			$output = stripcslashes(esc_textarea(strip_tags($psp_virtual_robots_content)));
		}
			
        return $output;
    }
	
	public function psp_filter_virtual_robots($robots_content, $public)
	{
		if ('0' == $public) {
			//$robots_content = getDefaultRobots();
        } else {
			$psp_robotstxt_settings = get_option('psp_robotstxt_settings');
			$psp_virtual_robots_content = isset($psp_robotstxt_settings['content']) ? stripcslashes(esc_textarea($psp_robotstxt_settings['content'])) : '';
			if ($psp_virtual_robots_content) {
				$robots_content = stripcslashes(esc_textarea(strip_tags($psp_virtual_robots_content)));
			}
		}
		
		return $robots_content;
	}
	
	/*
	 * Registers the .htaccess editor settings and appends the
	 * key to the plugin settings tabs array.
	 */
	private function register_htaccess_settings() {
		$this->psp_settings_tabs[$this->psp_htaccess_settings_group] = '.htaccess Editor';		
		$psp_htaccess_settings_name = "psp_htaccess_settings";

		$disabled = 0;
		
		$psp_htaccess_settings = get_option($psp_htaccess_settings_name);
		$htaccess_content = isset($psp_htaccess_settings['content']) ? stripcslashes(esc_textarea(html_entity_decode($psp_htaccess_settings['content']))) : '';
		
		//wp_enqueue_script( 'psp-hta', plugins_url( '/js/cm-hta.js', __FILE__ ),array( 'jquery' ), false, true);
		//$cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/nginx', 'codemirror'=> array('autoRefresh' => true)));
        //wp_localize_script('psphta', 'cm_settings', $cm_settings);
			
		//wp_enqueue_script( 'psp-input-toggler', plugins_url( '/js/pspinputtypetoggler.js', __FILE__ ), array( 'jquery' ) );
		//register
		register_setting( $this->psp_htaccess_settings_group, $psp_htaccess_settings_name, array( &$this, 'sanitize_htaccess_settings' ) );
		//add Section
		add_settings_section( 'psp_section_htaccess', esc_html__('.htaccess Editor', 'platinum-seo-pack' ), array( &$this, 'section_htaccess_desc' ), $this->psp_htaccess_settings_group );

		//add fields		
		
		$htaccess_file    = $this->htaccess_file; 
		
		if ( ! file_exists( $htaccess_file ) ) {		
			//echo "do nothing";
			add_settings_error('psp_htaccess_settings', esc_html( 'psp_htaccess_content' ), '.htaccess file does not seem to exist in the root! Ensure that you have one to write into it here.', 'error');
		} else {
		
			if( isset($_GET['settings-updated']) && true == sanitize_key($_GET['settings-updated'])){
				//echo "do nothing";
			} else {
		
				$htaccess_file_handle = fopen( $htaccess_file, 'r' );

				$content = '';
				if ( filesize( $htaccess_file ) > 0 ) {
					$content = fread( $htaccess_file_handle, filesize( $htaccess_file ) );
				}
				$htaccess_content = esc_textarea( $content );
				//$psp_robotstxt_settings['content'] = esc_textarea( $content );
				fclose( $htaccess_file_handle );
				
			}
			$option_description = "";
			if ( ! is_writable( $htaccess_file ) ) {
				$disabled = 1;
				$option_description = esc_html__( '.htaccess file exists in the root but it is not writeable. Make sure that it is writeable for you to write into it here.', 'platinum-seo-pack' );
			}			
		
			$content_field     = array (
				'label_for' 	=> 'psp_htaccess_content',
				'option_name'   => $psp_htaccess_settings_name.'[content]',
				'option_value'  => $htaccess_content,
				'disabled'  => $disabled,
				'option_description'  => $option_description,
			);
			
			add_settings_field( 'psp_htaccess_content', esc_html__('.htaccess Content: ', 'platinum-seo-pack'), array( &$this, 'psp_add_field_textarea' ), $this->psp_htaccess_settings_group, 'psp_section_htaccess', $content_field );
		}
		
	}
	
	public function sanitize_htaccess_settings($settings) {
	
		if( isset( $settings['content'] ) ) $settings['content'] = sanitize_textarea_field( wp_slash(htmlentities($settings['content'])) );	
		
		if( !empty( $settings['content'] ) ) {
			$this->psp_update_htaccess( $settings['content'] );
		}
		
		return $settings;
	}
	
	private function psp_update_htaccess( $htaccesscontent ) {	
	
		$htaccess_file = $this->htaccess_file; //;	

		$htaccessfilecontent = !empty($htaccesscontent) ? stripcslashes( html_entity_decode($htaccesscontent) ) : '';
		
		if ( file_exists( $htaccess_file ) && !empty($htaccessfilecontent) ) {
			
			if ( is_writable( $htaccess_file ) ) {
				$htaccess_filehandle = fopen( $htaccess_file, 'w+' );
				fwrite( $htaccess_filehandle, $htaccessfilecontent );
				fclose( $htaccess_filehandle );
				$msg = esc_html__( 'Updated .htaccess file', 'platinum-seo-pack' );
			}
		} else {
			//do nothing.				
		}	
	}
	
	//function section_htaccess_desc() {echo ''; }
	public function section_htaccess_desc() {
		$htaccess_file    = $this->htaccess_file; //;		
		if ( ! file_exists( $htaccess_file ) ) {			
			echo '<p style="color: orange">'.esc_html__('A .htaccess file does not exist in the root! Ensure that you have not accidentally deleted it!', 'platinum-seo-pack') . '</p>';			
		} else {
			if ( ! is_writable( $htaccess_file ) ) {
				echo '<p style="color: orange">'.esc_html__('A .htaccess file exists in the root but it is not writeable. Ensure that it is writeable for you to edit it here.', 'platinum-seo-pack') . '</p>';
			}
		}
	}
	
	/*
	 * Callback for adding a textarea.
	 */
	function psp_add_field_textarea(array $args) {
	
		$option_name   = isset($args['option_name']) ? $args['option_name'] : '';
		$id     = isset($args['label_for']) ? $args['label_for'] : '';
		$option_value     = isset($args['option_value']) ? esc_textarea( html_entity_decode(($args['option_value']) )) : '';
        $option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		$option_disabled = "";
		if (isset($args['disabled']) && $args['disabled']) $option_disabled = "disabled='disabled'";
	
		echo "<textarea id='".esc_attr($id)."' name='".esc_attr($option_name)."' ".esc_html($option_disabled)." rows='50' style='width:99%' type='textarea'>{$option_value}</textarea><br /><p class='description'>".esc_html($option_description)."</p>";
	
		//echo "<textarea rows='4' id='".$this->psp_home_settings_key['description']."' name='".$this->psp_home_settings_key['description']."'>".stripcslashes($this->psp_home_settings['description'])."</textarea>";			
	}
	
	/*
	 * Callback for adding a textarea.
	 */
	function psp_add_field_textarea_js(array $args) {
	
		$option_name   = isset($args['option_name']) ? $args['option_name'] : '';
		$id     = isset($args['label_for']) ? $args['label_for'] : '';
		
		//$option_value     = isset($args['option_value']) ? esc_textarea(html_entity_decode(base64_decode( $args['option_value'] ), ENT_QUOTES)) : '';
		//$option_value     = isset($args['option_value']) ? esc_textarea(html_entity_decode( $args['option_value'], ENT_QUOTES)) : '';
		$option_value     = isset($args['option_value']) ? esc_textarea($args['option_value']) : '';
		
        $option_description     = isset($args['option_description']) ? esc_html( $args['option_description'] ) : '';
		$option_disabled = "";
		if (isset($args['disabled']) && $args['disabled']) $option_disabled = "disabled='disabled'";
	
		echo "<textarea id='".esc_attr($id)."' name='".esc_attr($option_name)."' ".esc_html($option_disabled)." rows='50' style='width:99%' type='textarea'>{$option_value}</textarea><br /><p class='description'>".esc_html($option_description)."</p>";
	
		//echo "<textarea rows='4' id='".$this->psp_home_settings_key['description']."' name='".$this->psp_home_settings_key['description']."'>".stripcslashes($this->psp_home_settings['description'])."</textarea>";			
	}
	
	/*
	 * renders Plugin settings page, checks
	 * for the active tab and replaces key with the related
	 * settings key. Uses the plugin_options_tabs method
	 * to render the tabs.
	 */
	function psp_tools_options_page() {
		$tab = isset( $_GET['psptools'] ) ? sanitize_key($_GET['psptools']) : $this->psp_bulk_editor_group;
		//if( $this->psp_helper->user_has_access( $tab ) ) $tab = '';
		if ($tab == $this->psp_robots_settings_group) {
			$psp_button = "submitrobotstxt";
			$psp_nonce_field = "psp-robotstxt-nonce";
			$psp_nonce_name = "psp-robotstxt";
		    $psp_cm_text_settings['codeEditor'] = wp_enqueue_code_editor(array('codemirror'=> array('autoRefresh' => true)));
		    wp_localize_script('psp-meta-box', 'psp_cm_text_settings', $psp_cm_text_settings);
		   wp_enqueue_script( 'psp-cm', plugins_url( '/js/cm.js', __FILE__ ),array( 'jquery' ), false, true);
		} elseif ($tab == $this->psp_htaccess_settings_group) {
			$psp_button = "submithtaccess";
			$psp_nonce_field = "psp-htaccess-nonce";
			$psp_nonce_name = "psp-htaccess";
			$psp_cm_hta_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/nginx', 'codemirror'=> array('autoRefresh' => true)));
			wp_localize_script('psp-meta-box', 'psp_cm_hta_settings', $psp_cm_hta_settings);
			wp_enqueue_script( 'psp-hta', plugins_url( '/js/cm-hta.js', __FILE__ ),array( 'jquery' ), false, true);
		} elseif ($tab == $this->psp_ga_settings_group) {
			$psp_button = "submitanalyticscode";
			$psp_nonce_field = "psp-ga-nonce";
			$psp_nonce_name = "psp-ga";
			$psp_cm_ga_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/javascript', 'codemirror'=> array('autoRefresh' => true)));
			wp_localize_script('psp-meta-box', 'psp_cm_ga_settings', $psp_cm_ga_settings);
			wp_enqueue_script( 'psp-cmjs', plugins_url( '/js/cmjs.js', __FILE__ ),array( 'jquery' ), false, true);
		} elseif ($tab == $this->psp_bulk_editor_group) {
			$psp_nonce_field = "psp-bulkedit-nonce";
			$psp_nonce_name = "psp-be";			
		} elseif ($tab == $this->psp_importer_group) {
			$psp_nonce_field = "psp-bimportert-nonce";
			$psp_nonce_name = "psp-importer";			
		} elseif ($tab == $this->psp_plugin_settings_group) {
			$psp_nonce_field = "psp-plugin-settings-nonce";
			$psp_nonce_name = "psp-settings-importer";			
		} elseif ($tab == $this->psp_wizard_group) {
			$psp_nonce_field = "psp-wizard-nonce";
			$psp_nonce_name = "psp-wizard";			
		}
		/**
		//if ( isset( $_POST['submitrobotstxt'] ) ) {
		if((isset($_GET['settings-updated']) && true == sanitize_key($_GET['settings-updated'])) && ($tab == $this->psp_robots_settings_group )){
			if ( ! current_user_can( 'manage_options' ) ) {
				die( esc_html__( 'You cannot edit the robots.txt file.', 'platinum-seo-pack' ) );
			}
			
			//check_admin_referer( 'psp-robotstxt' );
			$psp_settings = get_option("psp_robotstxt_settings");
			$use_virtual_robots_file = isset($psp_settings['use_virtual_robots_file']) ? esc_attr($psp_settings['use_virtual_robots_file']) : '';
			$robotscontent = isset($psp_settings['content']) ? stripcslashes( esc_textarea($psp_settings['content'] )) : '';
			
			$robotstxt_file = $this->robotstxt_file; //;

			if (!$use_virtual_robots_file) {
			    
			    if (empty($robotscontent)) {
			        $robotscontent = $this->getDefaultRobots();
			    }
			
				if ( file_exists( $robotstxt_file )) {
					
					if ( is_writable( $robotstxt_file ) ) {
						$robotstxt_filehandle = fopen( $robotstxt_file, 'w+' );
						fwrite( $robotstxt_filehandle, $robotscontent );
						fclose( $robotstxt_filehandle );
						$msg = esc_html__( 'Updated Robots.txt', 'platinum-seo-pack' );
					}
				} else {
					if ( is_writable( get_home_path() ) ) {
						$robotstxt_filehandle = fopen( $robotstxt_file, 'x' );
						fwrite( $robotstxt_filehandle, $robotscontent );
						fclose( $robotstxt_filehandle );
						$msg = esc_html__( 'Created Robots.txt', 'platinum-seo-pack' );
					}					
				}
			}
		}
		**/
		//if ( isset( $_POST['submithtaccess'] ) ) {
		/**
		if((isset($_GET['settings-updated']) && true == sanitize_key($_GET['settings-updated'])) && ($tab == $this->psp_htaccess_settings_group )){
			if ( ! current_user_can( 'manage_options' ) ) {
				die( esc_html__( 'You cannot edit the .htaccess file.', 'platinum-seo-pack' ) );
			}
			
			$psp_htaccess_settings = get_option("psp_htaccess_settings");			
			$htaccesscontent = isset($psp_htaccess_settings['content']) ? stripcslashes( html_entity_decode($psp_htaccess_settings['content']) ) : '';
			
			$htaccess_file = $this->htaccess_file; //;			
			
			if ( file_exists( $htaccess_file )) {
				
				if ( is_writable( $htaccess_file ) ) {
					$htaccess_filehandle = fopen( $htaccess_file, 'w+' );
					fwrite( $htaccess_filehandle, $htaccesscontent );
					fclose( $htaccess_filehandle );
					$msg = esc_html__( 'Updated .htaccess file', 'platinum-seo-pack' );
				}
			} else {
				//do nothing.				
			}
			
		}
		**/
		?>
		<div class="wrap">		
			<h1 style='line-height:30px;'><?php esc_html_e('Techblissonline Platinum SEO Pack Tools', 'platinum-seo-pack') ?></h1>
			<p style="color: red"><?php esc_html_e('You need to click the "Save Settings" button to save the changes you made to each individual tab i.e. GA tracking code Editor, Robots.txt Editor and .htaccess Editor, before moving on to the next tab.', 'platinum-seo-pack') ?></p>		
			<?php $this->psp_tools_tabs(); 	?>
			<?php if ($this->psp_helper->user_has_access( $tab )) { 
				//if ($tab !== $this->psp_bulk_editor_group && $tab !== $this->psp_importer_group && $tab !== $this->psp_plugin_settings_group && $tab !== $this->psp_wizard_group ) {
			?>
			
			<form name="platinum-seo-form" method="post" action="options.php">
				<?php wp_nonce_field( $psp_nonce_field, $psp_nonce_name );?>
				<?php settings_fields( $tab ); ?>
				<?php settings_errors(); ?>
				<?php do_settings_sections( $tab ); ?>
				
				    <?php  if ($tab == $this->psp_robots_settings_group || $tab == $this->psp_htaccess_settings_group || $tab == $this->psp_ga_settings_group) {
				            submit_button('Save Settings', 'primary', $psp_button); } ?>
			
			</form>
		    <?php } else { ?>
			    <p style="color: red"><?php esc_html_e('You do not have access to this Options (Settings) tab of Techblissonline Platinum SEO Tools', 'platinum-seo-pack') ?></p>
			<?php } ?>
			<?php if ($tab == $this->psp_robots_settings_group || $tab == $this->psp_htaccess_settings_group || $tab == $this->psp_ga_settings_group) { ?>
			<div class="sidebar-cta">
			<h2>   
				<a class="bookmarkme" href="<?php echo 'https://techblissonline.com/tools/'; ?>" target="_blank"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-logo.png'; ?>" class="img-responsive" alt="Techblissonline Platinum SEO Wordpress Tools"/></a>
			</h2>
			    <div class="container bg-info" id="tools" style="width:100%">
                    <div class="row"><div class="h3 col-sm-12"><a class="btn-primary col-sm-12" href="https://techblissonline.com/tools/platinum-seo-wordpress-premium/" target="_blank">Platinum SEO Premium for wordpress</a></div><div class="h3 col-sm-12"><a class="btn-success col-sm-12" href="https://techblissonline.com/tools/" target="_blank">Techblissonline Platinum SEO Audit and Analysis Tools</a></div></div>     
                </div>
				<a href="https://techblissonline.com/tools/" target="_blank">Be our Patreon and enjoy these premium Wordpress SEO tools for just $9</a>
				<div class="container" style="width:100%"><a href="https://techblissonline.com/tools/" target="_blank"><span class="col-sm-12 dashicons dashicons-thumbs-up dashicons-psp"></span></a></div>
			</div>
				<?php  } ?>
		</div>
		<?php
	}
	
	public function psp_screen_options() {
        
		$psp_bulkedit_page = "platinum-seo-and-social-pack_page_psp-tools-by-techblissonline";
	 
		$screen = get_current_screen();
		
		//error_log($screen->id);
	 
		// get out of here if we are not on our settings page
		if(!is_object($screen) || ( $screen->id != $psp_bulkedit_page ))
		{
			return;
		}
		if ($screen->id == $psp_bulkedit_page) {
			$bulkedit_args = array(
				'label' => __('Rows per page'),
				'default' => 10,
				'option' => 'psp_bulkedit_rows_per_page'
			);
			add_screen_option( 'per_page', $bulkedit_args );
		}
		/***
		if ($screen->id == $psp_404_page || $screen->id == $psp_404_page_2) {
			$filter_args = array(
				'label' => __('Rows per page'),
				'default' => 10,
				'option' => 'psp_filter_rows_per_page'
			);
			add_screen_option( 'per_page', $filter_args );
		}**/
    }
	
	public function psp_set_screen_option($status, $option, $value) {
		
		//error_log('option '.$option.' '.$status.' '.$value);
            
		if ( 'psp_bulkedit_rows_per_page' == $option) {
			//error_log("psp rows status ".$status);
			//error_log("psp rows option ".$option);
			//error_log("psp rows ".$value);
			return $value;
			//return sanitize_key($value);
		}
	}

	public function psp_update_meta_data() {
		
		check_ajax_referer( 'psp_bulkedit_meta_nonce', 'pspbedit_ajax_nonce');
		$psp_post_id = isset($_POST['pspid']) ? sanitize_key( $_POST['pspid'] ) : '';
		$whattoupdate = isset($_POST['title']) ? sanitize_key( $_POST['title'] ) : '';
		//$update_status = false;
		//error_log("psp_post_id ".$psp_post_id);
		//error_log("whattoupdate ".$whattoupdate);
		if($whattoupdate == "psp_title") {
			$valuetoupdate = isset($_POST['pspvalue']) ? sanitize_text_field( $_POST['pspvalue'] ) : '';
			//error_log("title ".$valuetoupdate);
			if ( !empty($valuetoupdate) ) {
				$update_status = update_metadata( 'platinumseo', $psp_post_id, '_techblissonline_psp_title', $valuetoupdate );
			} else {
				$update_status = delete_metadata( 'platinumseo', $psp_post_id, '_techblissonline_psp_title');
			}	
		} else if ( $whattoupdate == "psp_description" ) {
			$valuetoupdate = isset($_POST['pspvalue']) ? sanitize_textarea_field( $_POST['pspvalue'] ) : '';
			//error_log("description ".$valuetoupdate);
			//update_metadata( 'platinumseo', $psp_post_id, '_techblissonline_psp_description', $valuetoupdate );
			if ( !empty($valuetoupdate) ) {
				$update_status = update_metadata( 'platinumseo', $psp_post_id, '_techblissonline_psp_description', $valuetoupdate );
			} else {
				$update_status = delete_metadata( 'platinumseo', $psp_post_id, '_techblissonline_psp_description');
			}
		} else if ( $whattoupdate == "psp_schema" ) {
			$valuetoupdate = isset($_POST['pspvalue']) ? sanitize_textarea_field( $_POST['pspvalue'] ) : '';
			//error_log("schema ".$valuetoupdate);
			//update_metadata( 'platinumseo', $psp_post_id, '_techblissonline_psp_description', $valuetoupdate );			
			if ( !empty($valuetoupdate) && $valuetoupdate !== 'Invalid JSON Schema') {
				$json_schema_str = ($valuetoupdate);
				//validate it is a json object
				$schema_obj = json_decode(stripcslashes($json_schema_str));
				if($schema_obj === null) {
                // $schema_obj is null because the json cannot be decoded
                $valuetoupdate = 'Invalid JSON Schema';                 
				} else {
					$valuetoupdate = sanitize_textarea_field( htmlentities($valuetoupdate) );	
					error_log("schema ".$valuetoupdate);
				}
				$update_status = update_metadata( 'platinumseo', $psp_post_id, '_techblissonline_psp_schema_string', $valuetoupdate );
			} else {
				$update_status = delete_metadata( 'platinumseo', $psp_post_id, '_techblissonline_psp_schema_string');
			}
		}
		
		if($update_status === false) {
          $result['type'] = "error";
          $result['message'] = "Not updated or Update Error!";
          //error_log("failed");
        } else {
            $result['type'] = "success";
            $result['message'] = "Updated!";
            //error_log("success");
        }

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
              $result = json_encode($result);
              //error_log(print_r($result, true));
              echo $result;
        } else {
             //header("Location: ".$_SERVER["HTTP_REFERER"]);
        }
        //$result = json_encode($result);
        // echo $result;
		//echo ''. esc_html(' Updated!', 'platinum-seo-pack');  
			//echo ucwords($plugin)." Meta Titles and Descriptions successfully Imported";
			wp_die();
		
	}
	    
	public function bulkedit_mgmtpage() {

		global $wpdb;
				
		$tbl_posts = $wpdb->prefix . "posts";
		//$tbl_postmeta = $wpdb->prefix . "postmeta";
		$tbl_postmeta = $wpdb->prefix . "platinumseometa";
		//$psp_redirections_tbl = $wpdb->prefix . "psp_redirections";
		$psp_meta_log = $wpdb->prefix . "psp_meta_log";
		
		$psp_post_types = array();		
		
		$builtin_post_types = array("post", "page");
		$custom_post_types = $this->custom_post_types;		
		$psp_all_posttypes = array_merge((array)$builtin_post_types, (array)$custom_post_types);
		$psp_post_types = array_combine($psp_all_posttypes, $psp_all_posttypes);
		
		$builtin_all_post_types = array("" => "Posts", "page" => "Pages");
		$psp_all_custom_post_types = array_combine($custom_post_types, $custom_post_types);
		$all_posttypes = array("all" => "All Post types");
		$psp_posttypes_for_ddl = array_merge( $builtin_all_post_types, $psp_all_custom_post_types, $all_posttypes );
		//error_log("post types all ".print_r($psp_posttypes_for_ddl, "n"));
		
		$post_types_count = count($psp_post_types);
		$placeholders = array_fill(0, $post_types_count, '%s');
		$psp_format = implode(', ', $placeholders);
		
		$psp_post_types_str = "";
		foreach( $psp_post_types as $psp_post_typee) {
		    $psp_post_types_str = $psp_post_types_str . "'".$psp_post_typee."', ";
		}
		$psp_post_types_str = substr($psp_post_types_str, 0, -2);
		$psp_post_types_str = "( ". $psp_post_types_str . " )";
		//error_log("post types ".$psp_post_types_str);
		
		$posts_list = array();
		$sql_posts = '';
		$bad_links = array();
		$sql_posts_1 = '';
		$sql_posts_2 = '';
		$psp_meta_type = isset($_GET['psp_meta_type']) ? sanitize_key($_GET['psp_meta_type']) : '';	
		$psp_order_type = isset($_GET['psp_order_type']) ? sanitize_key($_GET['psp_order_type']) : '';	
		$psp_sort_type = isset($_GET['psp_order_type']) ? sanitize_key($_GET['psp_sort_type']) : '';
		$psp_post_type = isset($_GET['psp_post_type']) ? sanitize_key($_GET['psp_post_type']) : '';
		
		if ($psp_post_type !== "all") {
			
			if (empty( $psp_post_type )) {
				$psp_post_types = array("post" => "post");
				$psp_post_type = "post";
				$psp_post_types_str = "( '". $psp_post_type . "' )";
			} else {
				$psp_post_types = array($psp_post_type => $psp_post_type);
				$psp_post_types_str = "( '". $psp_post_type . "' )";
			}
			$psp_format = "%s";
			//error_log("post type ".print_r($psp_post_types, "n"));
			//error_log("post type string ".print_r($psp_post_types_str, "n"));
		}
		
		// Handle bulk deletes
		if ( isset($_POST['deleteit']) && isset($_POST['update']) ) {

			foreach( (array) $_POST['update'] as $psp_id ) {
				
				$psp_id = sanitize_key($psp_id);
				if ( !current_user_can('edit_posts', $psp_id) )
					wp_die( __('You are not allowed to Delete.') );
					
				$psp_action = isset($_POST['psp_action']) ? sanitize_key($_POST['psp_action']) : '';				
				
				//Delete SQL here
				
				if ( empty($psp_meta_type) && isset( $_POST['psp_posts_bulkedit_title_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_posts_bulkedit_title_nonce']), 'do_psp_posts_bulkedit_title' )) {
					delete_metadata( 'platinumseo', $psp_id, '_techblissonline_psp_title');
				} else if ( ($psp_meta_type == "description") && isset( $_POST['psp_posts_bulkedit_desc_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_posts_bulkedit_desc_nonce']), 'do_psp_posts_bulkedit_desc' )) {
					delete_metadata( 'platinumseo', $psp_id, '_techblissonline_psp_description');
				} else if ( ($psp_meta_type == "schema") && isset( $_POST['psp_posts_bulkedit_schema_nonce'] ) && wp_verify_nonce( sanitize_key($_POST['psp_posts_bulkedit_schema_nonce']), 'do_psp_posts_bulkedit_schema' )) {
					delete_metadata( 'platinumseo', $psp_id, '_techblissonline_psp_schema_string');
				}
				
				
			}
		}
		
	   //Handle search
		if ( !empty($_GET['psp_filter']) && !empty($_GET['post-search-input']) ) {			
				
			if (!empty($_GET['post-search-input'])) {
			
				$psp_search = sanitize_text_field($_GET['post-search-input']);
			
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
				
				if (empty($psp_meta_type)) {
					$sql_posts_1 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_title FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_title') b ON a.ID = b.platinumseo_id WHERE a.post_type in $psp_post_types_str AND a.post_status = 'publish' AND a.post_title LIKE %s", $psp_like );
					
					if ($psp_like == "equals") {
						$sql_posts_1 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_title FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_title') b ON a.ID = b.platinumseo_id WHERE a.post_type in $psp_post_types_str AND a.post_status = 'publish' AND a.post_title = %s", $psp_search );						
					}					
				} else if($psp_meta_type == "description") {
					
					$sql_posts_1 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_description FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_description') b ON a.ID = b.platinumseo_id WHERE a.post_type in $psp_post_types_str AND a.post_status = 'publish' AND a.post_title LIKE %s", $psp_like );
					
					if ($psp_like == "equals") {
						$sql_posts_1 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_description FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_description') b ON a.ID = b.platinumseo_id WHERE a.post_type in $psp_post_types_str AND a.post_status = 'publish' AND a.post_title = %s", $psp_search );
					}	
					
				}  else if($psp_meta_type == "schema") {
					
					$sql_posts_1 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_schema FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_schema_string') b ON a.ID = b.platinumseo_id WHERE a.post_type in $psp_post_types_str AND a.post_status = 'publish' AND a.post_title LIKE %s", $psp_like );
					
					if ($psp_like == "equals") {
						$sql_posts_1 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_schema FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_schema_string') b ON a.ID = b.platinumseo_id WHERE a.post_type in $psp_post_types_str AND a.post_status = 'publish' AND a.post_title = %s", $psp_search );
					}	
					
				}  
				if (empty($psp_order_type)) {
					if (empty($psp_sort_type)) {
						$sql_posts_1 = $sql_posts_1 . "ORDER BY psp_id DESC";
					} else {
						$sql_posts_1 = $sql_posts_1 . "ORDER BY psp_post_name DESC";
					}
				} else {
					if (empty($psp_sort_type)) {
						$sql_posts_1 = $sql_posts_1 . "ORDER BY psp_id ASC";
					} else {
						$sql_posts_1 = $sql_posts_1 . "ORDER BY psp_post_name ASC";
					}
				}
				//error_log("sql 1 ".$sql_posts_1);
				$posts_list = $wpdb->get_results($sql_posts_1, OBJECT);
				
			}
		
		
		} else if ( empty($_GET['psp_filter']) ) {	
					
			if (empty($psp_meta_type)) {
				$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_title FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_title') b ON a.ID = b.platinumseo_id WHERE a.post_type in ($psp_format) AND a.post_status = 'publish'", $psp_post_types);				
			} else if($psp_meta_type == "description") {
					$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_description FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_description') b ON a.ID = b.platinumseo_id WHERE a.post_type in ($psp_format) AND a.post_status = 'publish'", $psp_post_types);
			} else if($psp_meta_type == "schema") {
					$sql_posts_2 = $wpdb->prepare("SELECT a.ID AS psp_id, a.post_title AS psp_post_name, b.meta_value as psp_schema FROM $tbl_posts a LEFT JOIN (Select platinumseo_id, meta_key, meta_value from $tbl_postmeta WHERE meta_key = '_techblissonline_psp_schema_string') b ON a.ID = b.platinumseo_id WHERE a.post_type in ($psp_format) AND a.post_status = 'publish'", $psp_post_types);
			}
			if (empty($psp_order_type)) {
				if (empty($psp_sort_type)) {
					$sql_posts_2 = $sql_posts_2 . "ORDER BY psp_id DESC";
				} else {
					$sql_posts_2 = $sql_posts_2 . "ORDER BY psp_post_name DESC";
				}
			} else {
				if (empty($psp_sort_type)) {
					$sql_posts_2 = $sql_posts_2 . "ORDER BY psp_id ASC";
				} else {
					$sql_posts_2 = $sql_posts_2 . "ORDER BY psp_post_name ASC";
				}
			}
			//error_log("sql 2 ".$sql_posts_2);
			$posts_list = $wpdb->get_results($sql_posts_2, OBJECT);
		}
		
		$total_no_posts = count($posts_list);
		
		//error_log("total number of posts ".$total_no_posts);
		
		$max_posts_per_page = 10;
        $user = get_current_user_id();
        $screen = get_current_screen();
		//error_log("screen ".print_r($screen, "n"));
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
		//error_log("max posts pers page ".$max_posts_per_page);
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

		include_once( 'psp_bulk_edit_renderer.php' ); 
	}   
	
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * psp_options_page method.
	 */
	function psp_tools_tabs() {
		$current_tab = isset( $_GET['psptools'] ) ? sanitize_key($_GET['psptools']) : $this->psp_bulk_editor_group;
		//$current_tab = isset( $_GET['psptools'] ) ? sanitize_key($_GET['psptools']) : '';
		//if( !$this->psp_helper->user_has_access( $current_tab ) ) $current_tab = '';
		//$current_tab = $active_tab;
		if ($current_tab !== $this->psp_bulk_editor_group) {
			//wp_enqueue_style("psp-settings-bs-css", plugins_url( '/css/psp-settings-bs.css', __FILE__ ));
			//wp_enqueue_style("psp-htmlsettings-css", plugins_url( '/css/psp-html-settings.css', __FILE__ ));
			wp_enqueue_style("psp-settings-css", plugins_url( '/css/psp-settings.css', __FILE__ ), array(), '2.3.5');
			wp_enqueue_style("psp-settings-bswide-css", plugins_url( '/css/psp-settings-bswide.css', __FILE__ ));
		} else {
			
		}
		//screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->psp_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			$psp_icon = '';
			if (($tab_key !== $this->psp_wizard_group && $tab_key !== $this->psp_bulk_editor_group && $tab_key !== $this->psp_plugin_settings_group) && ! $this->psp_helper->user_has_access( $tab_key ) ) {
			   continue;
			}
			if ( ($tab_key == $this->psp_wizard_group) && ! current_user_can( 'manage_options' ) ) {
			    continue;
			}
			
			if ($tab_key == $this->psp_bulk_editor_group) {
				$psp_icon = '<span class="dashicons dashicons-edit-large"></span> ';				
			} 
			if ($tab_key == $this->psp_importer_group) {
				$psp_icon = '<span class="dashicons dashicons-database-import"></span> ';
			} 
			if ($tab_key == $this->psp_wizard_group) {
				$psp_icon = '<span class="dashicons dashicons-admin-settings"></span> ';
			} 
			if ($tab_key == $this->psp_plugin_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-database-export"></span> ';
			} 
			if ($tab_key == $this->psp_ga_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-edit-page"></span> ';
			} 
			if ($tab_key == $this->psp_robots_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-edit-page"></span> ';
			}
			if ($tab_key == $this->psp_htaccess_settings_group) {
				$psp_icon = '<span class="dashicons dashicons-edit-page"></span> ';
			}
			if ($tab_key == $this->psp_wizard_group) {
				echo '<a style="text-decoration:none" class="nav-tab ' . esc_attr($active) . '" href="?page=' . esc_attr('wizard') . '">' . $psp_icon. esc_attr($tab_caption) .'</a>';
				
			} else {
				echo '<a style="text-decoration:none" class="nav-tab ' . esc_attr($active) . '" href="?page=' . esc_attr($this->psp_plugin_options_key) . '&psptools=' . esc_attr($tab_key) . '">' . $psp_icon. esc_attr($tab_caption) . '</a>';	
			}
			
		}
		echo '</h2>';
		
		if ($current_tab == $this->psp_bulk_editor_group && $this->psp_helper->user_has_access( $current_tab )) {
				$this->bulkedit_mgmtpage();
			}
		if ($current_tab == $this->psp_importer_group && $this->psp_helper->user_has_access( $current_tab )) {
				$this->psp_import_instance->pspimport_mgmtpage();
			}
		if ($current_tab == $this->psp_plugin_settings_group) {
				wp_enqueue_style("psp-settings-bswide-css", plugins_url( '/css/psp-settings-bswide.css', __FILE__ ));
				include( 'psp_tools_renderer.php' ); 
			} 
		if ($current_tab == $this->psp_wizard_group && current_user_can( 'manage_options' )) {
				$this->psp_wizard_instance->psp_wizard_page();
				
			} 	
	}
}