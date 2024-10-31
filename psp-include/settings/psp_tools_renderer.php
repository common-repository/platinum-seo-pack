<?php
/*
Plugin Name: Techblissonline Platinum SEO and Social Pack
Description: Complete SEO and Social optimization solution for your Wordpress blog/site.
Text Domain: platinum-seo-pack 
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: https://techblissonline.com/
*/
?>
<style>.vertical-align {
    display: flex;
    align-items: center;
}</style>
<?php
do {
    if ($_FILES) {
    
    	$psp_meta_file_uploaded = false;
    	$psp_options_file_uploaded = false;
		$psp_filename = "";
		//sanitize_file_name($_FILES['pspmetafile']['name']);
    	//if( isset($_FILES['pspmetafile']) && strpos( sanitize_file_name($_FILES['pspmetafile']['name']), 'platinumseometa' ) !== false) {
		if( isset($_FILES['pspmetafile']) ) {
				
			$psp_filename = sanitize_file_name($_FILES['pspmetafile']['name']);
				
			if ( strpos( $psp_filename, 'platinumseometa' ) !== false ) {			
    	    
				if (!isset( $_POST['psp_import_meta_nonce'] ) ||  !wp_verify_nonce( sanitize_key($_POST['psp_import_meta_nonce']), 'do_psp_importmeta_action' )) {
					
					//raise error;
					$message = esc_html__( 'The nonce key does not exist or validate. Import of Meta Data Failed!' ,  'platinum-seo-pack');
					add_settings_error('psp_tools_renderer', 'nonce_error', esc_html($message), 'error');
					break;
				}
				$psp_meta_file_uploaded = 'pspmetafile';
				
			}
    	    
    	} 
    	//if( isset($_FILES['optionsfile']) && strpos( sanitize_file_name( $_FILES['optionsfile']['name'] ), 'platinumseooptions' ) !== false) {
		if( isset($_FILES['optionsfile']) ) {
			
			$psp_filename = sanitize_file_name($_FILES['optionsfile']['name']);
			
			if ( strpos( $psp_filename, 'platinumseooptions' ) !== false ) {
    	    
				if (!isset( $_POST['psp_import_options_nonce'] ) ||  !wp_verify_nonce( sanitize_key($_POST['psp_import_options_nonce']), 'do_psp_importoptions_action' )) {
					
					//raise error;
					$message = esc_html__( 'The nonce key does not exist or validate. Import of Options Data Failed!' ,  'platinum-seo-pack');
					add_settings_error('psp_tools_renderer', 'nonce_error', esc_html($message), 'error');
					break;
				}
				$psp_options_file_uploaded = 'optionsfile';
				
			}
    	    
    	} 
    	
    	$options_file_type = [];
    	if ($psp_options_file_uploaded) {
    	    //$options_file_type      = wp_check_filetype($_FILES['optionsfile']['name']);
			 $options_file_type     = wp_check_filetype( $psp_filename );
			
    	} else if ($psp_meta_file_uploaded) {
    	    //$options_file_type      = wp_check_filetype($_FILES['pspmetafile']['name']);
			$options_file_type     = wp_check_filetype( $psp_filename );
    	} else {
    	    //raise error;
    		$message = esc_html__( 'The file you are trying to upload should have the same name and be of the same type as the exported file. Import Failed!' ,  'platinum-seo-pack');
    		add_settings_error('psp_tools_renderer', 'file_error', esc_html($message), 'error');
    		break;
    	}
    	if ( empty($options_file_type ) ) {
    		//raise error;
    		$message = esc_html__( 'The file extension is not csv. Import Failed!' ,  'platinum-seo-pack');
    		add_settings_error('psp_tools_renderer', 'file_error', esc_html($message), 'error');
    		break;
    	}
    	if (!empty($options_file_type ) && $options_file_type['ext'] !== 'csv' ) {
    		//raise error;
    		$message = esc_html__( 'The file extension is not csv. Import Failed!' ,  'platinum-seo-pack');
    		add_settings_error('psp_tools_renderer', 'file_error', esc_html($message), 'error');
    		break;
    	}
        
        // Accepted file types
        $csv_file_types = array('text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel');
    	
    	if ( isset($options_file_type['type'] ) && !in_array($options_file_type['type'],  $csv_file_types )){
    	
    		//raise error;
    		$message = esc_html__( 'The file type is not csv. Import Failed!',  'platinum-seo-pack');
    		add_settings_error('psp_tools_renderer', 'file_error', esc_html($message), 'error');
    		break;
    	
    	}
    	global $wp_filesystem;
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');	
    		if ( empty( $wp_filesystem ) ) {
    			WP_Filesystem();
    		}
        }
        
        $file = [];
    
       if ($psp_options_file_uploaded) {
    	
    	    $file = wp_handle_upload( $_FILES['optionsfile'] );
       } else if ($psp_meta_file_uploaded) {
           
           $file = wp_handle_upload( $_FILES['pspmetafile'] );
           
       }
    	
    	if ( !empty($file) && ( !file_exists($file['file']) || !is_readable($file['file']))) {
    	    $message = esc_html__( 'There no csv file to upload or the file is not readable.Import Failed!' ,  'platinum-seo-pack');
    		add_settings_error('psp_tools_renderer', 'file_error', esc_html( $message), 'error');
            break;
        }
    	if ( is_wp_error( $file ) ) {
    		$message = esc_html__( 'There was a WP Error. Import Failed!' ,  'platinum-seo-pack');
    		add_settings_error('psp_tools_renderer', 'file_error', esc_html( $message. ' '. $file['error']), 'error');
    		break;
    	}
    
    	if ( isset( $file['error'] ) ) {
    		$message = esc_html__( 'Import Failed!' ,  'platinum-seo-pack');
    		add_settings_error('psp_tools_renderer', 'file_error', esc_html( $message. ' '. $file['error']), 'error');
    		break;
    	}
    
    	if ( ! isset( $file['file'] ) ) {
    		$message = esc_html__( 'Import Failed as CSV file cannot be uploaded or is not the correct csv file!' ,  'platinum-seo-pack');
    		add_settings_error('psp_tools_renderer', 'file_error', esc_html( $message ), 'error');
    		break;
    	}
    	
    	if ( false === $file ) {
    		return false;
    	}
    	
        $pspfh = @fopen($file['file'], "r");
            
		$psp_values = array();
		$header_row = true;
		
		global $wpdb;	
		
		if ($psp_meta_file_uploaded) {
	
    		$psp_post_seo_tbl = $wpdb->prefix . "platinumseometa";
    		$psp_post_tmp_seo_tbl = $wpdb->prefix . "tempmeta";
    		
    		$wpdb->query("TRUNCATE TABLE {$psp_post_tmp_seo_tbl}");
    		$wpdb->query("Insert into {$psp_post_tmp_seo_tbl} (meta_id, post_id, meta_key, meta_value) Select meta_id, platinumseo_id, meta_key, meta_value from {$psp_post_seo_tbl}");
		} else if ( $psp_options_file_uploaded ) {
		    $psp_options_tbl = $wpdb->prefix . "options";
		}

		// Assign .csv rows to array
		while ( ( $row = fgetcsv( $pspfh )) !== false ) {  // Get file contents 
			if ($header_row ) {
				$header_row = false;
				continue;
			}
			
			array_push($psp_values,$row[0], $row[1], $row[2], $row[3]);
		
			if ($psp_meta_file_uploaded) {
			    $place_holders[] = "(%d, %d, %s, %s)";
			} else if ($psp_options_file_uploaded) {
			    $place_holders[] = "(%d, %s, %s, %s)";
			}
			$row= [];
		}
		
		if ($psp_values) {
			
			if ($psp_meta_file_uploaded) {
    			$psp_query = "INSERT INTO {$psp_post_seo_tbl} (meta_id, platinumseo_id, meta_key, meta_value) VALUES ";
    			
    			$wpdb->query("TRUNCATE TABLE {$psp_post_seo_tbl}");
    			$psp_query .= implode(', ', $place_holders);
    			//$wpdb->query( $wpdb->prepare("$psp_query", $psp_values));
				
				if ( false === $wpdb->query( $wpdb->prepare("$psp_query", $psp_values)) ) {
					if ( $wpdb->last_error ) {
						//return new WP_Error( 'db_query_error', __( 'Could not execute query' ), $wpdb->last_error );
						$message = esc_html__( 'Could not execute Platinum SEO Meta data mport query - '. $wpdb->last_error,  'platinum-seo-pack');	
					} else {
						$message = esc_html__( 'Could not execute Platinum SEO Meta data import query!',  'platinum-seo-pack');	
					}
				} else {
					$message = esc_html__( 'Platinum SEO Meta Data of '. count($place_holders) . ' Rows successfully imported!',  'platinum-seo-pack');	
				}
    			
    			//$message = esc_html__( 'Platinum SEO Meta Data of '. count($place_holders) . ' Rows successfully imported!',  'platinum-seo-pack');
		        add_settings_error('psp_tools_renderer', 'success', esc_html( $message), 'success');
		        break;
			} else if ($psp_options_file_uploaded) {
			    
			    $wpdb->query("DELETE FROM {$psp_options_tbl} WHERE option_name like 'psp_%'");
			    $psp_query = "INSERT INTO {$psp_options_tbl} (option_id, option_name, option_value, autoload) VALUES ";
    			$psp_query .= implode(', ', $place_holders);
    			//$wpdb->query( $wpdb->prepare("$psp_query", $psp_values));
				
				if ( false === $wpdb->query( $wpdb->prepare("$psp_query", $psp_values)) ) {
					if ( $wpdb->last_error ) {
						//return new WP_Error( 'db_query_error', __( 'Could not execute query' ), $wpdb->last_error );
						$message = esc_html__( 'Could not execute Platinum SEO Options data import query - '. $wpdb->last_error,  'platinum-seo-pack');	
					} else {
						$message = esc_html__( 'Could not execute Platinum SEO Options data import query!',  'platinum-seo-pack');
					}
				} else {
					$message = esc_html__( 'Platinum SEO Options Data of '. count($place_holders) . ' Rows successfully imported!',  'platinum-seo-pack');	
				}
    			
    			//$message = esc_html__( 'Platinum SEO Options Data of '. count($place_holders) . ' Rows successfully imported!',  'platinum-seo-pack');
		        add_settings_error('psp_tools_renderer', 'success', esc_html( $message), 'success');
		        break;
			    
			}
		}
		
		unlink( $file['file'] );
	
    } else {
		
		//raise error;
		//$message = esc_html__( 'No file had been selected to upload!' ,  'platinum-seo-pack');
		//add_settings_error('psp_tools_renderer', 'nonce_error', esc_html($message), 'error');
		//break;
	}
} while (0);
settings_errors( 'psp_tools_renderer' );
 ?>
<div id="logo" class="container-fluid" style="width:90%">
	<div class="row m-2 p-1"><div class="h3 col-sm-12">
	<a class="bookmarkme" href="<?php echo 'https://techblissonline.com/tools/'; ?>" target="_blank"><img src="<?php echo esc_url(PSP_PLUGIN_URL).'images/techblissonline-logo.png'; ?>" class="img-responsive" alt="Techblissonline Platinum SEO Wordpress Tools"/></a></div></div>
</div><!-- end of #logo -->
<?php if ($this->psp_helper->user_has_access('psp_pluginsettings')) { ?>
	<div class="clearfix"></div>
	<div class="container bg-info" id="header" style="width:90%">
		<h2 class="btn-success"> <?php esc_html_e('Import Platinum SEO Data', 'platinum-seo-pack'); ?> </h2>
		<h4 class="col-sm-12"> <?php esc_html_e('Import Platinum SEO Options Data', 'platinum-seo-pack'); ?> </h4>
		<form action="" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<div class="col-sm-2"><h5><?php esc_html_e('Select csv File to Upload:', 'platinum-seo-pack'); ?></h5> </div>  
			<div class="col-sm-10">
				<input type="file" class="form-control" name="optionsfile" id="optionsfile"/>			
			</div>	
		</div> 
		<div class="form-group"> 
			<div class="col-sm-offset-2 col-sm-10">	
				<input type="hidden" value="wp_handle_upload" name="action" />
				<input type="submit" value="<?php _e('Submit'); ?>" name="submitit" class="btn btn-success" />
			</div>
		</div>
		<?php 			
			wp_nonce_field( 'do_psp_importoptions_action', 'psp_import_options_nonce' );		
		?>
	</form>
		<h4 class="col-sm-12"> <?php esc_html_e('Import Platinum SEO Meta Data', 'platinum-seo-pack'); ?> </h4>
		<form action="" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<div class="col-sm-2"><h5><?php esc_html_e('Select csv File to Upload:', 'platinum-seo-pack'); ?></h5> </div>  
			<div class="col-sm-10">
				<input type="file" class="form-control" name="pspmetafile" id="pspmetafile"/>			
			</div>	
		</div> 
		<div class="form-group"> 
			<div class="col-sm-offset-2 col-sm-10">	
				<input type="hidden" value="wp_handle_upload" name="action" />
				<input type="submit" value="<?php esc_html_e('Submit', 'platinum-seo-pack'); ?>" name="submitit" class="btn btn-success" />
			</div>
		</div>
		<?php 			
			wp_nonce_field( 'do_psp_importmeta_action', 'psp_import_meta_nonce' );		
		?>
	</form>
	</div>
	<div class="clearfix"></div>
	<div class="container bg-info" id="header" style="width:90%">
		<h2 class="btn-success"> <?php esc_html_e('Export Platinum SEO Data', 'platinum-seo-pack'); ?> </h2>
			<div class="clearfix"></div>
		<br />
		<div class="row vertical-align">
			<div class="col-sm-6"><h4><?php esc_html_e('Export Platinum SEO Meta Data:', 'platinum-seo-pack'); ?></h4> </div>   
			<div class="col-sm-6">
		<a href="<?php echo admin_url( 'admin.php?page=psp-seo-tools-by-techblissonline' ) ?>&action=psp_meta_download_csv&_wpnonce=<?php echo wp_create_nonce( 'psp_meta_download_csv' )?>" class="btn btn-default btn-success"><?php _e('Export to CSV','platinum-seo-pack');?></a>
	</div>
		</div>
		<div class="clearfix"></div>
	<br />
		<div class="row vertical-align">
			<div class="col-sm-6"><h4><?php esc_html_e('Export Platinum SEO Option Data:', 'platinum-seo-pack'); ?></h4> </div>  
			<div class="col-sm-6">
		<a href="<?php echo admin_url( 'admin.php?page=psp-seo-tools-by-techblissonline' ) ?>&action=psp_options_download_csv&_wpnonce=<?php echo wp_create_nonce( 'psp_options_download_csv' )?>" class="btn btn-default btn-success"><?php _e('Export to CSV','platinum-seo-pack');?></a>
	</div>
		</div>
	</div>
<?php } ?>
<div class="clearfix"></div>



<div class="container bg-success" id="header" style="width:90%">
    <h1 class="btn-success"> <?php esc_html_e('Tools', 'platinum-seo-pack'); ?> </h1>
<p> <?php echo esc_html__('These are premium tools available as SaaS on Techblissonline.com. You can subscribe to them for just 9$ Google NLP Entity Analyser is available for subscribers who pay 14$. If you buy Platinum SEO Premium, you get a free subscription to use these Tools for one month. Subscribe ', 'platinum-seo-pack').' <a href="https://techblissonline.com/tools/" target="_blank" rel="noopener">'.esc_html__('here', 'platinum-seo-pack').'</a>'; ?></p>
</div>
<div class="clearfix"></div>
<div class="container-fluid bg-info form-group" id="tools" style="width:90%">
     <div class="row m-auto p-1"><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/tf-idf-analysis/" target="_blank">TF-IDF Analysis of Competition</a></div><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/seo-analysis/" target="_blank">On Page SEO Analysis Tool</a></div></div>
      <div class="row m-auto p-1"><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/competition-analysis-report/" target="_blank">Competition Analysis Reporter Tool</a></div><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/keyword-density-analysis-tool/" target="_blank">Keyword Density Analysis Tool</a></div></div>
    <div class="row m-auto p-1"><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/readability/" target="_blank">Readability Analyzer (From Text)</a></div><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/readability-from-url/" target="_blank">Readability Analyzer (From URL)</a></div></div> 
    <div class="row m-auto p-1"><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/ga-insights/" target="_blank">Google Analytics Insights Dashboard</a></div><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/query-explorer/" target="_blank">Google Analytics Chart Explorer</a></div></div> 	
     <div class="row m-auto p-1"><div class="h3 col-sm-6"><a class="text-white bs-link" href="https://techblissonline.com/tools/check-grammar/" target="_blank">Check Grammar (From Text or URL)</a></div><div class="h3 col-sm-6"></div></div> 
</div>

<div class="clearfix"></div>
<div class="container-fluid bg-info form-group" id="main-tool" style="width:90%">
    <div class="row m-2 p-1"><div class="h3 col-sm-12"><a class="text-white bs-link" href="https://techblissonline.com/tools/json-schema-generator/" target="_blank">JSON Schema Generator</a><span class="badge btn-success">Free</span><span class="badge btn-success">Sign in required</span></div></div>
     <div class="row m-2 p-1"><div class="h3 col-sm-12"><a class="text-white bs-link" href="https://techblissonline.com/tools/rewrite-rules-generator/" target="_blank">Rewrite Rules Generator</a></div></div>
    
</div>

<div class="clearfix"></div>
<div class="container-fluid bg-info form-group" id="entity-tool" style="width:90%">
    <div class="row m-2 p-1"><div class="h3 col-sm-12"><a class="text-white bs-link" href="https://techblissonline.com/tools/entity-analysis/" target="_blank">Google NLP Entity and Sentiment Analysis</a></div></div>
     
</div>