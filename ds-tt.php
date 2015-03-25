<?php
/*
 * Plugin Name: Data Springs Tailored HTML
 * Plugin URI: https://www.datasprings.com/wordpress/tailored-html
 * Description: Provide different templates based on if the user is authenticated or unauthenticated.
 * Version: 1.0
 * Author: Ryan Bakerink
 * Author URI: https://www.datasprings.com/wordpress/tailored-html
 * License: GPL2
*/

include("/resources/ds-tt-configuration.php");
include("/resources/ds-tt-get-configuration.php");
include("/resources/ds-tt-widget.php");

register_activation_hook( __FILE__, 'ds_tt_activation_script' );
register_deactivation_hook( __FILE__, 'ds_tt_deactiviation_script' );

add_action('admin_menu', 'ds_tt_create_admin_menu');

add_action( 'wp_enqueue_scripts', 'ds_tt_load_scripts' );
add_action( 'admin_enqueue_scripts', 'ds_tt_load_scripts' );

add_action('init', 'ds_tt_generate_short_codes');
add_action('widgets_init', 'ds_tt_instance_init');


add_action("wp_ajax_tt_get_template_configuration", "ds_tt_get_template_configuration");
add_action("wp_ajax_tt_get_stylesheet", "ds_tt_get_stylesheet");
add_action("wp_ajax_tt_get_preview", "ds_tt_get_preview");
add_action("wp_ajax_tt_update_stylesheet", "ds_tt_update_stylesheet");

/*REGISTER JAVASCRIPT & CSS*/
function ds_tt_load_scripts() {
	
	$plugin_url = plugin_dir_url(__FILE__);
	
	
	//Registering JavaScript / jQuery
	wp_register_script('ds-tt-jquery-block-ui', $plugin_url . '/js/ds-tt-jquery-block-ui.js', array('jquery'), '1.0.0');
	wp_register_script('ds-tt-configuration', $plugin_url . '/js/ds-tt-configuration.js', array('jquery'), '1.0.0', true);
	wp_register_script('ds-tt-tinymce', $plugin_url . '/js/tinyMCE/tinymce.min.js', array('jquery'), '1.0.0');
	
	//Registering CSS
	wp_register_style( 'ds-tt-configuration-css',$plugin_url . '/css/ds-tt-configuration.css', false, '1.0.0');
	wp_register_style( 'ds-tt-jquery-theme-css', $plugin_url . '/css/ds-tt-jquery-ui.theme.min.css', '1.0.0');
	wp_register_style( 'ds-tt-jquery-ui-css', $plugin_url . '/css/ds-tt-jquery-ui.min.css', '1.0.0');

	//Enqueue CSS
	wp_enqueue_style( 'ds-tt-configuration-css', $plugin_url . '/css/ds-tt-configuration.css', '1.0.0');
	wp_enqueue_style( 'ds-tt-jquery-theme-css', $plugin_url . '/css/ds-tt-jquery-ui.theme.min.css', '1.0.0');
	wp_enqueue_style( 'ds-tt-jquery-ui-css', $plugin_url . '/css/ds-tt-jquery-ui.min.css', '1.0.0');
	
	//Enqueue JavaScript
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-accordion');
	wp_enqueue_script('jquery-ui-tabs');

	wp_enqueue_script('ds-tt-jquery-block-ui', $plugin_url . '/js/ds-tt-jquery-block-ui.js', array(), '1.0.0');	
	wp_enqueue_script('ds-tt-configuration', $plugin_url . '/js/ds-tt-configuration.js', array(), '1.0.0', true);
	wp_enqueue_script('ds-tt-tinymce', $plugin_url . '/js/tinyMCE/tinymce.min.js' ,array(), '1.0.0');
	
	wp_localize_script( 'ds-tt-configuration', 'tt_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
		
}


/*PLUGIN ACTIVATION SCRIPT*/
function ds_tt_activation_script() {
    global $wpdb;
    $ds_tt_tablename = $wpdb->prefix . "ds_tailored_html";
	$ds_tt_tablename2 = $wpdb->prefix . "ds_tailored_html_stylesheet";

    $charset_collate = $wpdb->get_charset_collate();
	
	if( $wpdb->get_var("SHOW TABLES LIKE '$ds_tt_tablename'") != $ds_tt_tablename ) {	
	
		$sql .="CREATE TABLE `$ds_tt_tablename`(
				`id` INT NOT NULL AUTO_INCREMENT,
				`instance_name` VARCHAR(500) NOT NULL,
				`authorized_template` text NOT NULL,
				`unauthorized_template` text NOT NULL,
				`authorized_editor` VARCHAR(64) NOT NULL,
				`unauthorized_editor` VARCHAR(64) NOT NULL,
				`token_sql` text NOT NULL,
				PRIMARY KEY ( id )
			) $charset_collate;";
			
	}
	
	
	if( $wpdb->get_var("SHOW TABLES LIKE '$ds_tt_tablename2'") != $ds_tt_tablename2 ) {	
	
		$sql .="CREATE TABLE `$ds_tt_tablename2`(
				`id` INT NOT NULL AUTO_INCREMENT,
				`plugin_id` VARCHAR(10) NOT NULL,
				`custom_css` text NOT NULL,
				PRIMARY KEY ( id )
			) $charset_collate;";
			
	}
	
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
}

/*PLUGIN DEACTIVATION SCRIPT*/
function ds_tt_deactiviation_script() {
	error_log('plugin deactivated');
}

/*CREATE ADMIN MENU UNDER SETTINGS TAB*/
function ds_tt_create_admin_menu() {
	add_options_page('Tailored HTML', 'Tailored HTML', 'manage_options', 'ds-tt-configuration.php', 'ds_tt_admin_menu');
	
}

/*CREATE SHORTCODES FOR CONFIGURATIONS*/
function ds_tt_generate_short_codes() {
    add_shortcode( 'Data_Springs_Tailored_HTML', 'ds_tt_get_instance' );
}

/*REGISTER STOCK QUOTE WIDGET CLASS*/
function ds_tt_instance_init() {
	register_widget(ds_tt_instance);
}

/*STOCK QUOTE WIDGET CLASS*/
class ds_tt_instance extends WP_Widget {
    
	function ds_tt_instance() {
		$widget_options = array(
		'classname' => 'ds_tt_class',
		'description' => 'Display a Tailored HTML instance.'
		);
		
		$this->WP_Widget('ds_tt_ID', 'Data Springs Tailored HTML');
	}
	
    function form($instance) {
		$defaults = array('title' => 'Value', 'configuration' => '-1');
		
		$instance = wp_parse_args( (array) $instance, $defaults);
		
		$title = esc_attr($instance['title']);
		$configuration = esc_attr($instance['configuration']);
		
		echo '<p>Title <input type="text" class="widefat" name="'.$this->get_field_name('title').'" value="'.$title.'"/></p>';

		echo '<p>Configuration <select id="'.$this->get_field_name('configuration').'" name="'.$this->get_field_name('configuration').'" class="widefat"><option value="-1">-- Select --</option>';

		global $wpdb;
		
		$sql = "SELECT id , instance_name FROM `wp_ds_tailored_html` ORDER BY instance_name ASC";
		
		$results = $wpdb->get_results( $sql , ARRAY_A);
		
		foreach($results as $result) {
			$id = $result['id'];
			$instance_name = $result['instance_name'];
		
		echo '<option value="' .$id.'">'.$instance_name.'</option>';
		
		}
		
		if($configuration != '-1') {
			echo '</select></p><script type="text/javascript">document.getElementById(\''.$this->get_field_name('configuration').'\').value = '.$configuration.'</script>';					
		}
		else {
			echo '</select></p>';
		}
		
    }
    
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['configuration'] = strip_tags($new_instance['configuration']);
		return $instance;
    }

    function widget($args, $instance) {
		extract($args);
		
		$title = apply_filters('widget_title', $instance['title']);
		$configuration = apply_filters('widget_configuration', $instance['configuration']);
        
		if(is_single()) {
			echo $before_widget;
			echo $before_title.$title.$after_title;
			
			//print widget content;
			
			//echo 'Test';
			echo ds_tt_widget_instance($configuration);
			
			echo $after_widget;
		}
    }
}
?>