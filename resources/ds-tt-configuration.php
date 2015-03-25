<?php
function ds_tt_admin_menu() {
?>

<div class="wrap">

<div id="ds_success_message" class="updated hide-template-default">Settings saved successfully</div>
<div id="ds_error_message" class="error hide-template-default">Error saving settings</div>

	<h2>Tailored HTML Configuration</h2>
	<p>Welcome to Data Springs Tailored HTML plugin. Use this page to configure all your Tailored HTML configurations.</p>
<p><a href="https://www.datasprings.com/wordpress/plugins/tailored-html#86671-support" target="_blank">Support</a>&nbsp;|&nbsp;<a href="https://www.datasprings.com/wordpress/plugins/tailored-html" target="_blank">User Guide</a>&nbsp;|&nbsp;<a href="https://www.datasprings.com/wordpress/plugins/tailored-html#86672-feedback" target="_blank">Feedback</a>&nbsp;|&nbsp;<a href="https://www.datasprings.com/wordpress/plugins/tailored-html#86673-donate" target="_blank">Donate</a></p>
	
<div id="ds_tt_panels">
<h3>1. Manage Configuration</h3>
<div>
	<div class="ds-tt-help-information">Use this section to configure your Tailored HTML instance and/or manage your instance templates.</div>

	<?php 
		
	/*DELETE FORM POST*/
	if(isset($_POST['ds_tt_configuration_delete']))
	{			
		$post_ds_tt_instance = sanitize_text_field($_POST['ds_tt_instance_id']);
		
		global $wpdb;
			
		$tablename = $wpdb->prefix . "ds_tailored_html";
		
		$sql3 = "Delete from `$tablename` where id = $post_ds_tt_instance;";
		$wpdb->query($sql3);
	}

	/*SAVE SETTINGS FORM POST*/
	if(isset($_POST['ds_tt_configuration_submit']))
	{
		if( sanitize_text_field($_POST['ds_tt_instance_id'])  == '') {
			
			
			/*DECLARE FORM VALUES*/
			$post_ds_tt_instance = sanitize_text_field($_POST['ds_tt_instance']);
			$post_ds_tt_instance_name = sanitize_text_field($_POST['ds_tt_instance_name']);
			$post_ds_tt_authorized_template = $_POST['ds_tt_authorized_template'];
			$post_ds_tt_unauthorized_template = $_POST['ds_tt_unauthorized_template'];
			$post_ds_tt_authenticated_editor = sanitize_text_field($_POST['ds_tt_authenticated_editor']);
			$post_ds_tt_unauthenticated_editor = sanitize_text_field($_POST['ds_tt_unauthenticated_editor']);
			$post_ds_tt_sql = sanitize_text_field(strtolower($_POST['ds_tt_sql']));
			
			$post_ds_tt_sql = str_replace('update','',$post_ds_tt_sql);
			$post_ds_tt_sql = str_replace('delete','',$post_ds_tt_sql);
			$post_ds_tt_sql = str_replace('truncate','',$post_ds_tt_sql);
			$post_ds_tt_sql = str_replace('drop','',$post_ds_tt_sql);
			$post_ds_tt_sql = str_replace('into','',$post_ds_tt_sql);
			
			global $wpdb;
				
			$tablename = $wpdb->prefix . "ds_tailored_html";
			$tablename2 = $wpdb->prefix . "ds_tailored_html_stylesheet";
			
			$sql_check = $wpdb->get_var("SELECT Count(*) As TheCount FROM `wp_ds_tailored_html` where instance_name = '".$post_ds_tt_instance_name."';");
			
			if( $sql_check == 0)
			{
				
				$result = $wpdb->insert(
				$tablename,
				array (
				'instance_name'=>  $post_ds_tt_instance_name,
				'authorized_template'=>  $post_ds_tt_authorized_template,
				'unauthorized_template'=>  $post_ds_tt_unauthorized_template,
				'authorized_editor'=>  $post_ds_tt_authenticated_editor,
				'unauthorized_editor'=>  $post_ds_tt_unauthenticated_editor,
				'token_sql'=>  $post_ds_tt_sql
				)
				);
				
				$lastid = $wpdb->insert_id;
				
				$result_css = $wpdb->insert(
				$tablename2,
				array (
				'plugin_id'=>  $lastid ,
				'custom_css'=>  ''
				)
				);
			
			
			}
		}
		/*UPDATE SETTINGS FORM POST*/
		else {
			$get_ds_tt_instance_id = sanitize_text_field($_POST['ds_tt_instance_id']);
			$get_ds_tt_instance = sanitize_text_field($_POST['ds_tt_instance']);
			$get_ds_tt_instance_name = sanitize_text_field($_POST['ds_tt_instance_name']);
			$get_ds_tt_authorized_template = $_POST['ds_tt_authorized_template'];
			$get_ds_tt_unauthorized_template = $_POST['ds_tt_unauthorized_template'];
			$get_ds_tt_authenticated_editor = sanitize_text_field($_POST['ds_tt_authenticated_editor']);
			$get_ds_tt_unauthenticated_editor = sanitize_text_field($_POST['ds_tt_unauthenticated_editor']);
			$get_ds_tt_sql = sanitize_text_field(strtolower($_POST['ds_tt_sql']));
			
						
			$get_ds_tt_sql = str_replace('update','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('delete','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('truncate','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('drop','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('into','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('table','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('database','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('alter','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('create','',$get_ds_tt_sql);
			$get_ds_tt_sql = str_replace('exec','',$get_ds_tt_sql);
			
			global $wpdb;
				
			$tablename = $wpdb->prefix . "ds_tailored_html";
			
			$sql_check2 = $wpdb->get_var("SELECT Count(*) As TheCount FROM `wp_ds_tailored_html` where instance_name = '".$get_ds_tt_instance_name."';");
						
			if( $sql_check2 == 0)
			{
				$values = array (
				'instance_name'=>  $get_ds_tt_instance_name,
				'authorized_template'=>  $get_ds_tt_authorized_template,
				'unauthorized_template'=>  $get_ds_tt_unauthorized_template,
				'authorized_editor'=>  $get_ds_tt_authenticated_editor,
				'unauthorized_editor'=>  $get_ds_tt_unauthenticated_editor,
				'token_sql'=>  $get_ds_tt_sql
				);
				
				$where = array('ID' => $get_ds_tt_instance_id);
				
				$update_result = $wpdb->update( $tablename, $values, $where);
			}
			else {

				$values = array (
				'authorized_template'=> $get_ds_tt_authorized_template,
				'unauthorized_template'=> $get_ds_tt_unauthorized_template,
				'authorized_editor'=> $get_ds_tt_authenticated_editor,
				'unauthorized_editor'=> $get_ds_tt_unauthenticated_editor,
				'token_sql'=>  $get_ds_tt_sql
				);
				
				$where = array('ID' => $get_ds_tt_instance_id);
				
				$update_result = $wpdb->update( $tablename, $values, $where);
			}
		}
	}
?>

	<h3>General Configuration</h3>

<form method="post" id="configuration_form" action="<?php echo $_SERVER['PHP_SELF'] . '?page=ds-tt-configuration.php'; ?>" >
	<table class="form-table"> 
	<tr valign="top"> 
		<th scope="row">
		<input type="hidden" name="ds_tt_instance_id" id="ds_tt_instance_id" value="" />
		<input type="hidden" name="ds_tt_stylesheet_updated" id="ds_tt_stylesheet_updated" value="" />
		<label for="ds-tt-label">Tailored HTML:</label></th> 
		<td>
			<select name="ds_tt_instance" id="ds_tt_instance" class="widefat">
				<option value="-1">-- Select --</option>
				<option value="0">New Configuration</option>

				<?php 
				
				global $wpdb;
				
				$sql = "SELECT id , instance_name FROM `wp_ds_tailored_html` ORDER BY instance_name ASC";
				
				$results = $wpdb->get_results( $sql , ARRAY_A);
				
				foreach($results as $result) {
					$id = $result['id'];
					$instance_name = $result['instance_name'];
				
				echo '<option value="' .$id.'">'.$instance_name.'</option>';
				
				}
				?>
			</select>
			<br/>
			<small>Create new or select existing configuration</small>
		</td>                
	</tr> 
	<tr valign="top" class="hide_show_template_row"> 
		<th scope="row"><label for="ds_tt_instance_name">Configuration Name: <span class="ds-tt-required-field">*</span></label></th> 
		<td>
			<input type="textbox" name="ds_tt_instance_name" id="ds_tt_instance_name" class="widefat" onChange="ds_tt_remove_quotes_validation(document.getElementById('ds_tt_instance_name').value, 'ds_tt_instance_name')">
			<br/><small>Provide a unique configuration name</small>&nbsp;&nbsp;<small id="ds-tt-configuration-name-error-message"></small>
		</td>                
	</tr> 
</table>

<div id="ds_tt_template_tabs" class="hide_show_template_row">
	<ul>
		<li><a href="#ds_tt_template_tabs_1">Dynamic Tokens</a></li>
		<li><a href="#ds_tt_template_tabs_2">Authenticated Template</a></li>
		<li><a href="#ds_tt_template_tabs_3">Unauthenticated Template</a></li>
	</ul>

	<div id="ds_tt_template_tabs_1">
		<div class="ds-tt-help-information">Dynamic Tokens will make your Tailored HTML templates much more powerful.</div>
			<table class="form-table">
					<tr valign="top" class="hide_show_template_row"> 
					<th scope="row"><label for="ds_tt_sql">SQL:</label></th> 
					<td>
						<textarea name="ds_tt_sql" rows="11" id="ds_tt_sql" class="widefat" placeHolder="Provide a valid SQL Select Statement..."></textarea><br />
						<br/><small>Use a valid SQL Select Statement</small>
					</td>                
				</tr> 
				<tr valign="top" class="hide_show_template_row"> 
					<th scope="row"><label for="ds_tt_sql">Available Tokens:</label></th> 
					<td>
						<span name="ds_tt_sql_tokens" rows="11" id="ds_tt_sql_tokens" class="widefat"></span>
					</td>                
				</tr> 
			</table>
	</div>
	<div id="ds_tt_template_tabs_2">
		<div class="ds-tt-help-information">The Authenticated Template is the template that will be displayed to users that are authenticated(logged in).</div>
		<textarea name="ds_tt_authorized_template" rows="11" id="ds_tt_authenticated" class="widefat" placeHolder="Add your content..."></textarea><br />
		<input type="radio" name="ds_tt_authenticated_editor" id="ds_tt_authenticated_basic" checked value="basic"/>&nbsp;Basic Editor&nbsp;&nbsp;
		<input type="radio" name="ds_tt_authenticated_editor" id="ds_tt_authenticated_advanced" value="advanced"/>&nbsp;Advanced Editor
	</div>
	<div id="ds_tt_template_tabs_3">
		<div class="ds-tt-help-information">The Unauthenticated Template is the template that will be displayed to users that are unauthenticated(not logged in).</div>
		<textarea name="ds_tt_unauthorized_template" rows="11" id="ds_tt_unauthenticated" class="widefat" placeHolder="Add your content..."></textarea><br />
		<input type="radio" name="ds_tt_unauthenticated_editor" id="ds_tt_unauthenticated_basic" checked value="basic"/>&nbsp;Basic Editor&nbsp;&nbsp;
		<input type="radio" name="ds_tt_unauthenticated_editor" id="ds_tt_unauthenticated_advanced" value="advanced"/>&nbsp;Advanced Editor
	</div>
</div>

<table class="form-table"> 
	<tr valign="top" class="hide_show_template_row"> 
		<td colspan="2">
			<input type="submit" id="ds_tt_configuration_submit" name="ds_tt_configuration_submit" value="Save Configuration" class="button-primary"/>
			<input type="submit" id="ds_tt_configuration_delete" name="ds_tt_configuration_delete" value="Delete Configuration" class="button-secondary"/>
			<input type="button" id="ds_tt_configuration_cancel" name="ds_tt_configuration_cancel" value="Cancel" class="button-secondary" onClick="window.location.href= window.location.protocol"/>
		</td>
	</tr>	
</table> 
</form>			
</div>

<h3>2. Custom CSS</h3>
<div>
		<form method="post" id="stylesheet_form" action="<?php echo $_SERVER['PHP_SELF'] . '?page=ds-tt-configuration.php'; ?>" >
		
			<h3>Select Configuration</h3>
			<input type="hidden" id="ds_tt_stylesheet_instance_id" name="ds_tt_stylesheet_instance_id" />
			<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row">
					<label for="ds-tt-label">Tailored HTML:</label></th> 
					<td>
						<select name="ds_tt_stylesheet_instance" id="ds_tt_stylesheet_instance" class="widefat">
							<option value="-1">-- Select --</option>

							<?php 							
								global $wpdb;
								
								$sql = "SELECT id , instance_name FROM `wp_ds_tailored_html` ORDER BY instance_name ASC";
								
								$results = $wpdb->get_results( $sql , ARRAY_A);
								
								foreach($results as $result) {
									$id = $result['id'];
									$instance_name = $result['instance_name'];
								
								echo '<option value="' .$id.'">'.$instance_name.'</option>';
								}
							?>
						</select>
						<br/>
						<small>Select configuration to manage your custom CSS</small>
					</td>                
				</tr> 
			</table>
			<div class="ds-tt-help-information hide_show_stylesheet_row">Any CSS provided below will be applied in your Tailored HTML configuration.</div>
			<table class="form-table hide_show_stylesheet_row"> 
				<tr valign="top"> 
					<th scope="row">
					<label for="ds_tt_stylesheet_content">Custom CSS</label></th>
					<td>					
						<textarea name="ds_tt_stylesheet_content" id="ds_tt_stylesheet_content" placeholder="/*DATA SPRINGS CUSTOM CSS*/" rows="15" class="widefat"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">
								<input type="button" id="ds_tt_stylesheet_submit" name="ds_tt_stylesheet_submit" value="Save Stylesheet" class="button-primary"/>
								<input type="button" id="ds_tt_stylesheet_cancel" name="ds_tt_stylesheet_cancel" value="Cancel" class="button-secondary" onClick="window.location.href= window.location.protocol"/>
					</td>
			</table>
	</form>
</div>


<h3>3. Short Codes</h3>
<div>
	<div class="ds-tt-help-information"><p>Short Codes are very powerful. Simply copy/paste any of the Short Codes below into any post on your WordPress site. </p></div>
	
	<table class="form-table"> 
		<?php 
			global $wpdb;
			
			$sql = "SELECT instance_name, id FROM `wp_ds_tailored_html` ORDER BY instance_name ASC";
			
			$results = $wpdb->get_results( $sql , ARRAY_A);
			
			foreach($results as $result) {
				$instance_name = $result['instance_name'];
				$instance_id = $result['id'];
			
			echo '<tr valign="top"><th scope="row"><label for="ds_tt_short_code_'.$instance_id.'">'.$instance_name.'</label></th><td><input type="text" value=\'[Data_Springs_Tailored_HTML name="'.$instance_name.'"][/Data_Springs_Tailored_HTML]\' name="ds_tt_short_code_'.$instance_id.'"  id="ds_tt_short_code_'.$instance_id.'" class="widefat" /><br /><small>Copy/Paste this Code</small></td></tr>';
			}
		?>
	</table>
</div>

<h3 id="ds_tt_preview_accordion">4. Preview</h3>
<div>
	
		<form method="post" id="preview_form" action="<?php echo $_SERVER['PHP_SELF'] . '?page=ds-tt-configuration.php'; ?>" >
		
			<h3>Select Configuration</h3>
			<table class="form-table"> 
				<tr valign="top"> 
					<th scope="row">
					<label for="ds-tt-label">Tailored HTML:</label></th> 
					<td>
						<select name="ds_tt_preview_instance" id="ds_tt_preview_instance" class="widefat">
							<option value="-1">-- Select --</option>

							<?php 							
								global $wpdb;
								
								$sql = "SELECT id , instance_name FROM `wp_ds_tailored_html` ORDER BY instance_name ASC";
								
								$results = $wpdb->get_results( $sql , ARRAY_A);
								
								foreach($results as $result) {
									$id = $result['id'];
									$instance_name = $result['instance_name'];
								
								echo '<option value="' .$id.'">'.$instance_name.'</option>';
								}
							?>
						</select>
						<br/>
						<small>Select configuration to preview layout</small>
					</td>                
				</tr> 
			</table>
			<div class="ds-tt-help-information hide_show_preview_row">Below is a representation of what users will see wherever this configuration is applied(short code or Widget).</div>
			<table class="form-table hide_show_preview_row"> 
				<tr valign="top"> 
					<th scope="row">
					<label for="ds_tt_preview_html_auth">Authenticated:</label></th>
					<td>					
						<div id="ds_tt_preview_html_auth" name="ds_tt_preview_html_auth"></div>
					</td>
				</tr>
				<tr valign="top"> 
					<th scope="row">
					<label for="ds_tt_preview_html_unauth">Unauthenticated:</label></th>
					<td>					
						<div id="ds_tt_preview_html_unauth" name="ds_tt_preview_html_unauth"></div>
					</td>
				</tr>
			</table>
	</form>
	
</div>

    </div>
<?php
}
?>