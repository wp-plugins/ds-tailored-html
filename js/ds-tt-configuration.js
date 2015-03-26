//WHEN DOCUMENT READY
jQuery( document ).ready(function() {
	
	var err;
	
	try{jQuery.unblockUI()} catch(err){};
	try{jQuery( "#ds_tt_panels" ).accordion({heightStyle: "content"})} catch(err){};
	try{jQuery( "#ds_tt_template_tabs" ).tabs()} catch(err){};

	try{jQuery( "#tabs" ).tabs()} catch(err){};
	
	var err_catch;
	
	try{jQuery('#ds_tt_configuration_delete').hide()} catch(err){};
	try{jQuery('.hide-template-default').hide()} catch(err){};
	try{jQuery('.show-template-default').show()} catch(err){};
	try{
		if(jQuery('#ds_tt_instance').val() == '-1') {
			jQuery('.hide_show_template_row').hide();
		}
		
		if(jQuery('#ds_tt_stylesheet_instance').val() == '-1') {
			jQuery('.hide_show_stylesheet_row').hide();
		}
		
		if(jQuery('#ds_tt_preview_instance').val() == '-1') {
			jQuery('.hide_show_preview_row').hide();
		}
	} catch(err){}
	
});

//OnChange event for Template Configuration Combo Box
jQuery('#ds_tt_instance').change(function(){
	jQuery.blockUI();
	ds_tt_hide_error_success_messages();


	if(jQuery('#ds_tt_instance').val() == '-1') {
		ds_tt_clear_template_form();
		jQuery('.hide_show_template_row').hide();
		ds_tt_get_template_details(jQuery('#ds_tt_instance').val());
	}
	else { 
		if(jQuery('#ds_tt_instance').val() == '0') {
			ds_tt_clear_template_form();
			jQuery.unblockUI();
		}
		else{
			ds_tt_get_template_details(jQuery('#ds_tt_instance').val());	
		}
		
		jQuery('.hide_show_template_row').show();
	}

});



//OnChange event for Stylesheet Configuration Combo Box
jQuery('#ds_tt_stylesheet_instance').change(function(){

	jQuery.blockUI();
	ds_tt_hide_error_success_messages();
	ds_tt_get_stylesheet_configuration_details(jQuery('#ds_tt_stylesheet_instance').val());

	if(jQuery('#ds_tt_stylesheet_instance').val() == '-1') {
		jQuery('.hide_show_stylesheet_row').hide();
		ds_tt_get_stylesheet_configuration_details(jQuery('#ds_tt_stylesheet_instance').val());
	}
		
	jQuery('.hide_show_stylesheet_row').show();
});


//OnChange event for Preview Configuration Combo Box
jQuery('#ds_tt_preview_instance').change(function(){

	jQuery.blockUI();
	ds_tt_hide_error_success_messages();
	ds_tt_get_preview_configuration_details(jQuery('#ds_tt_preview_instance').val());

	if(jQuery('#ds_tt_preview_instance').val() == '-1') {
		jQuery('.hide_show_preview_row').hide();
		ds_tt_get_preview_configuration_details(jQuery('#ds_tt_preview_instance').val());
	}
		
	jQuery('.hide_show_preview_row').show();
});


//OnChange event unauthenticated Editor
jQuery('#ds_tt_unauthenticated').change(function(){

	if( jQuery('#ds_tt_unauthenticated').val() == 'advanced') {
		
		tinymce.init({
			selector: "#ds_tt_unauthenticated",
			plugins: [
				"autolink lists link image charmap preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
			],
			toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
		});
	}
});


//OnClick event for Submit button
jQuery('#ds_tt_configuration_submit').click(function(){
	
	//if all required fields pass
	if( ds_tt_validate_template_settings() == 'true') {
	
	jQuery.blockUI();
	}
	//required fields did not pass prevent form submission
	else {
	return false;
	}
});

//OnClick event for Delete button
jQuery('#ds_tt_configuration_delete').click(function(){
	
	var conf = confirm('Are you sure you would like to delete this configuration?');
	
	if(conf) {
		jQuery.blockUI();
	}
	else {
		return false;
	}
});

//OnClick event for Cancel button
jQuery('#ds_tt_configuration_cancel').click(function(){
	jQuery.blockUI();
});


//OnClick event for Stylesheet button
jQuery('#ds_tt_stylesheet_submit').click(function(){
	
		jQuery.blockUI();
		ds_tt_update_stylesheet_configuration_details(document.getElementById('ds_tt_stylesheet_instance').value, document.getElementById('ds_tt_stylesheet_content').value);

});

//template validation
function ds_tt_validate_template_settings() {

	var status = 'true';

	if( jQuery('#ds_tt_instance_name').val() == '') {
		jQuery('#ds-tt-configuration-name-error-message').html('Required');
		jQuery('#ds_tt_instance').focus();
		status = 'false';
	}
	else {
	jQuery('#ds-tt-configuration-name-error-message').html('');
	}

	return status;
}


//Manage Template clear form
function ds_tt_clear_template_form() {
	document.getElementById('ds_tt_instance_id').value = '';
	document.getElementById('ds_tt_instance_name').value = '';
	document.getElementById('ds_tt_authenticated').value = '';
	document.getElementById('ds_tt_unauthenticated').value = '';
	
	document.getElementById('ds_tt_authenticated_basic').checked = true;
	document.getElementById('ds_tt_unauthenticated_basic').value = true;
	
	document.getElementById('ds_tt_configuration_submit').value = 'Save Configuration';
	jQuery('#ds_tt_configuration_delete').hide();
	
}


//Show success Message
function ds_tt_success_message() {
	jQuery('#ds_success_message').removeClass("hide-template-default").addClass("show-template-default");
	return false;
}

//Show failed Message
function ds_tt_failure_message() {
	jQuery('#ds_error_message').removeClass("hide-template-default").addClass("show-template-default");
	return false;
}

//Hide both Success & Failed Message
function ds_tt_hide_error_success_messages() {
	
	jQuery('#ds_success_message').removeClass("show-template-default").addClass("hide-template-default");
	jQuery('#ds_error_message').removeClass("show-template-default").addClass("hide-template-default");
	jQuery('.hide-template-default').hide();
	
}


//FUNCTION TO PREVENT INPUT OF " OR '
function ds_tt_remove_quotes_validation( input_value, input_id ) {
	var str = '';
	var template_contents = input_value;

	for(i=0; i<template_contents.length; i++) 
	{
		if( template_contents.charAt(i) == "'") {
			str += '';
		}
		else if( template_contents.charAt(i) == '"') {
			str += '';
		}
		else {
			str += template_contents.charAt(i);
		}
		
		document.getElementById(input_id).value = str;
	}
}


//AJAX to get configuration settings by instance_id selected
function ds_tt_get_template_details(main_id) {

	 jQuery.ajax({
			 type : "post",
			 dataType : "json",
			 url : tt_ajax.ajaxurl, 
			 data : {action: "tt_get_template",  cid : main_id},
			 success: function(response) {

					if (response['status_type'] == 'success') {
						document.getElementById('ds_tt_instance').value = response['id'];
						document.getElementById('ds_tt_instance_id').value = response['id'];
						document.getElementById('ds_tt_instance_name').value = response['instance_name'];
						document.getElementById('ds_tt_authenticated').value = ds_tt_remove_quotes(response['authorized_template']);
						document.getElementById('ds_tt_unauthenticated').value = ds_tt_remove_quotes(response['unauthorized_template']);
						document.getElementById('ds_tt_sql').value = ds_tt_remove_quotes(response['token_sql']);
						document.getElementById('ds_tt_sql_tokens').innerHTML = ds_tt_remove_quotes(response['token_sql_columns']);
						
						 
						
						
						if( response['authorized_editor'] == 'basic') {
							document.getElementById('ds_tt_authenticated_basic').checked = true;
							tinymce.EditorManager.execCommand('mceRemoveEditor',true,  'ds_tt_authenticated');
						}
						if( response['authorized_editor'] == 'advanced') {
							document.getElementById('ds_tt_authenticated_advanced').checked = true;
							
								tinymce.init({
									selector: "#ds_tt_authenticated",
									plugins: "code"
								});
						}
						
						if( response['unauthorized_editor'] == 'basic') {
							document.getElementById('ds_tt_unauthenticated_basic').checked = true;
							tinymce.EditorManager.execCommand('mceRemoveEditor',true,  'ds_tt_unauthenticated');
						}
						if( response['unauthorized_editor'] == 'advanced') {
							document.getElementById('ds_tt_unauthenticated_advanced').checked = true;
								tinymce.init({
									selector: "#ds_tt_unauthenticated",
									plugins: "code"
								});
						}
				
						document.getElementById('ds_tt_configuration_submit').value = 'Update Configuration';
						jQuery('#ds_tt_configuration_delete').show();
						jQuery.unblockUI();
					}
					else {
						jQuery.unblockUI();
					}
		
			 }
		  });	   
}

/*AJAX TO GET THE STYLESHEET RELATED TO CONFIGURATION*/
function ds_tt_get_stylesheet_configuration_details(instance_id) {

	 jQuery.ajax({
			 type : "post",
			 dataType : "json",
			 url : tt_ajax.ajaxurl, 
			 data : {action: "tt_get_stylesheet",  cid : instance_id},
			 success: function(response) {
				document.getElementById('ds_tt_stylesheet_instance_id').value = response['plugin_id'];
				document.getElementById('ds_tt_stylesheet_instance').value = instance_id;
				document.getElementById('ds_tt_stylesheet_content').value = response['custom_css'];
					
				jQuery.unblockUI();
			 }
		  });	  
}

/*AJAX TO UPDATE THE STYLESHEET RELATED TO CONFIGURATION*/
function ds_tt_update_stylesheet_configuration_details(instance_id, css_content) {

	 jQuery.ajax({
			 type : "post",
			 dataType : "json",
			 url : tt_ajax.ajaxurl, 
			 data : {action: "tt_update_stylesheet",  cid : instance_id, css : css_content},
			 success: function(response) {			
				ds_tt_get_stylesheet_configuration_details(instance_id);
			 }
		  });	  
}

/*AJAX TO PREVIEW THE CONFIGURATION*/
function ds_tt_get_preview_configuration_details(instance_id) {

	 jQuery.ajax({
			 type : "post",
			 dataType : "json",
			 url : tt_ajax.ajaxurl, 
			 data : {action: "tt_get_preview",  cid : instance_id},
			 success: function(response) {
				var err;
				try{document.getElementById('ds_tt_preview_instance').value = instance_id} catch(err){document.getElementById('ds_tt_preview_instance').value = '-1'; jQuery('.hide_show_preview_row').hide();};
				try{document.getElementById('ds_tt_preview_html_auth').innerHTML = ds_tt_remove_quotes(response['authorized_template'])} catch(err){document.getElementById('ds_tt_preview_html_auth').innerHTML = ''; jQuery('.hide_show_preview_row').hide();};
				try{document.getElementById('ds_tt_preview_html_unauth').innerHTML = ds_tt_remove_quotes(response['unauthorized_template'])} catch(err){document.getElementById('ds_tt_preview_html_unauth').innerHTML = ''; jQuery('.hide_show_preview_row').hide();};
				try{jQuery("<style type='text/css'>" + ds_tt_remove_quotes(response['custom_css']) +  "</style>").appendTo("head")} catch(err){};				
				jQuery.unblockUI();
			 }
		  });	  
}


//OnChange event for basic text editor
jQuery('#ds_tt_authenticated_basic').change(function(){
	tinymce.EditorManager.execCommand('mceRemoveEditor',true,  'ds_tt_authenticated');
});

//OnChange event for advanced text editor
jQuery('#ds_tt_authenticated_advanced').change(function(){
	
	tinymce.init({
		selector: "#ds_tt_authenticated",
		plugins: "code"
	});
});




//OnChange event for basic text editor
jQuery('#ds_tt_unauthenticated_basic').change(function(){
	tinymce.EditorManager.execCommand('mceRemoveEditor',true,  'ds_tt_unauthenticated');
	
});

//OnChange event for advanced text editor
jQuery('#ds_tt_unauthenticated_advanced').change(function(){
	
	tinymce.init({
		selector: "#ds_tt_unauthenticated",
		plugins: "code"
	});
});


//Remove escape sequences for usability of templates.
function ds_tt_remove_quotes( template ) {
	var str = '';
	var template_contents = template;

	for(i=0; i<template_contents.length; i++) 
	{
		if( template_contents.charAt(i) == "\\") {
			str += '';
		}
		else {
			str += template_contents.charAt(i);
		}
	}
	return str;
}


	


