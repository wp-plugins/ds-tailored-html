<?php

/*FUNCTION RESPONSIBLE FOR CONSTRUCTING FRONT-END WIDGET*/
function ds_tt_widget_instance($configuration_id) {
	
	global $wpdb;
		
	$sql = "SELECT id, authorized_template, unauthorized_template, authorized_editor, unauthorized_editor, token_sql FROM `wp_ds_tailored_html` where id = '".$configuration_id."';";
	
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
	
	
	$sql_get_css = $wpdb->get_var("SELECT custom_css FROM `wp_ds_tailored_html_stylesheet` where plugin_id = '".$instance_id."';");
	
	$instance_HTML .= '<style type="text/css">';
	$instance_HTML .= str_replace("\'","'",str_replace('\"','"',$sql_get_css));
	$instance_HTML .= '</style>';

    return $instance_HTML;	
}
	
?>