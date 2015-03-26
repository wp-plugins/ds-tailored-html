<?php

/*FUNCTION RESPONSIBLE FOR CONSTRUCTING FRONT-END*/
function ds_tt_get_instance($args, $content) {
    
	$instance_name = $args['name'];
	
	global $wpdb;
		
	$sql = "SELECT authorized_template, unauthorized_template, authorized_editor, unauthorized_editor, token_sql, id FROM `wp_ds_tailored_html` where instance_name = '".$instance_name."';";
	
	$results = $wpdb->get_results( $sql , ARRAY_A);
		
	/*GET THE CONFIGURATION*/
	foreach($results as $result) {
		$instance_id = $result['id'];
		$authorized_template = $result['authorized_template'];
		$unauthorized_template = $result['unauthorized_template'];
		$authorized_editor = $result['authorized_editor'];
		$unauthorized_editor = $result['unauthorized_editor'];
		$token_sql = $result['token_sql'];
		
		
		
		if( $token_sql == '') {
			if ( is_user_logged_in() ) {
				$instance_HTML = str_replace("\'","'",str_replace('\"','"',$authorized_template));
			}
			else {
				$instance_HTML = str_replace("\'","'",str_replace('\"','"',$unauthorized_template));
			}
		}
		else {
			$sql_for_tokens = $token_sql;
			
			$token_results = $wpdb->get_results( $sql_for_tokens , ARRAY_A);
			 
			 if (!$token_results) {
			
				if ( is_user_logged_in() ) {
					$instance_HTML = str_replace("\'","'",str_replace('\"','"',$authorized_template));
				}
				else {
					$instance_HTML = str_replace("\'","'",str_replace('\"','"',$unauthorized_template));
				}
			}
			else {
					
				foreach($token_results as $token) {
					
					if ( is_user_logged_in() ) {
						$instance_HTML .= str_replace("\'","'",str_replace('\"','"',ds_tt_replace_tokens_with_values($authorized_template, $token)));
					}
					else {
						$instance_HTML .= str_replace("\'","'",str_replace('\"','"',ds_tt_replace_tokens_with_values($unauthorized_template, $token)));
					}
				}
			}
		
		}
	
	
	}
	
	
	$get_instance_id = $wpdb->get_var("SELECT id FROM `wp_ds_tailored_html` where instance_name = '".$instance_name."';");
	
	$sql_get_css = $wpdb->get_var("SELECT custom_css FROM `wp_ds_tailored_html_stylesheet` where plugin_id = '".$get_instance_id."';");
	
	$instance_HTML .= '<style type="text/css">';
	$instance_HTML .= str_replace("\'","'",str_replace('\"','"',$sql_get_css));
	$instance_HTML .= '</style>';
	
    return $instance_HTML;	
}

function ds_tt_replace_tokens_with_values($template, $array) {
	$template_value = $template;
	$tokens = '';
	foreach($array as $key => $value) {
		$template_value = str_replace('['.strtolower($key).']',$value, $template_value);	
		//$tokens .= '['.strtolower($key).']';
	}

	//need to accumulate list of tokens in the template.
	

	
	return $template_value;
}


/*GET THE CONFIGURATION DETAILS*/
function ds_tt_get_template() {

   $main_id = sanitize_text_field($_REQUEST['cid']);
	$e = '';
	$token_list = '';
	
   if( $main_id != '-1' AND $main_id != '0') {
		global $wpdb;
		
		$sql = "SELECT id , instance_name, authorized_template, unauthorized_template, authorized_editor, unauthorized_editor, token_sql, '' As token_sql_columns, 'success' As status_type FROM `wp_ds_tailored_html` where id= ".$main_id." ORDER BY instance_name ASC";
		
		$results = $wpdb->get_results( $sql , ARRAY_A);
		
		foreach($results as $result) {
			$id = $result['id'];
			$instance_name = $result['instance_name'];
			/*$authorized_template = $result['authorized_template'];
			$unauthorized_template = $result['unauthorized_template'];*/
			$authorized_editor = $result['authorized_editor'];	
			$unauthorized_editor = $result['unauthorized_editor'];	
			$token_sql = $result['token_sql'];	
			$status_type = $result['status_type'];
			
			$sql_for_tokens = $token_sql;
			
			
			try {
					if( $sql_for_tokens == ''){
						$token_list = '';
					}
					else {
						$token_results = $wpdb->get_results( $sql_for_tokens , ARRAY_A);
						
						$token_list = '';
						foreach($token_results as $token) {
							foreach($token as $name => $value) {
								
								if ( !preg_match('['.strtolower($name).']',$token_list)) {
									$token_list .= '['.strtolower($name).']&nbsp;&nbsp;&nbsp;';	
								}

								
							}
							
						}
					}
			} 
			catch (Exception $e) {
				$token_list = '';
			}

			
			
			

			
			$result['token_sql_columns'] = $token_list;


		}
		
		echo json_encode($result);
		
		die();
	}
	else {
		$result_failed['status_type'] = 'failed';
		echo json_encode($result_failed);
	}
}

function ds_tt_get_stylesheet() {

   $instance_id = sanitize_text_field($_REQUEST['cid']);

   if( $instance_id != '-1') {
		global $wpdb;
		
		$sql = "SELECT plugin_id, custom_css, 'success' As status_type FROM `wp_ds_tailored_html_stylesheet` where plugin_id= ".$instance_id.";";
		
		$results = $wpdb->get_results( $sql , ARRAY_A);
				
		foreach($results as $result) {
			$plugin_id = $result['plugin_id'];
			$custom_css = $result['custom_css'];
			$status_type = $result['status_type'];
		}
			
		echo json_encode($result);
			
		die();
	}
	else {
		$result_failed['status_type'] = 'failed';
		echo json_encode($result_failed);
	}
   die();
}

/*UPDATE STYLESHEET AJAX HANDLER*/
function ds_tt_update_stylesheet() {

   $plugin_id = sanitize_text_field($_REQUEST['cid']);
   $css_content = $_REQUEST['css'];

	global $wpdb;
		
	$tablename = $wpdb->prefix . "ds_tailored_html_stylesheet";
	
	$values = array (
	'plugin_id'=>  $plugin_id,
	'custom_css'=> $css_content
	);
	
	$where = array('plugin_id' => $plugin_id);
	
	$update_result = $wpdb->update( $tablename, $values, $where);
   
	echo json_encode('success');
   die();
}


/*GET PREVIEW AJAX HANDLER*/
function ds_tt_get_preview() {

   $request_instance_id = sanitize_text_field($_REQUEST['cid']);

	global $wpdb;
		
	$sql = "SELECT A.id, A.authorized_template, A.unauthorized_template, A.authorized_editor, A.unauthorized_editor, B.custom_css FROM `wp_ds_tailored_html` A inner join `wp_ds_tailored_html_stylesheet` B on A.id = B.plugin_id where A.id = '".$request_instance_id."';";
	
	$results = $wpdb->get_results( $sql , ARRAY_A);
	
	$instance_HTML = '';
	
	foreach($results as $result) {
		$instance_id = $result['id'];
		$authorized_template = $result['authorized_template'];
		$unauthorized_template = $result['unauthorized_template'];
		$authorized_editor = $result['authorized_editor'];
		$unauthorized_editor = $result['unauthorized_editor'];
		$custom_css = $result['custom_css'];
	}
   
	echo json_encode($result);

   die();
}

?>