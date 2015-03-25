<?php

//only execute the contents of this file if the plugin is really being uninstalled
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit ();
}

global $wpdb;
$ds_tt_tablename = $wpdb->prefix . "ds_tailored_html";
$ds_tt_tablename2 = $wpdb->prefix . "ds_tailored_html_stylesheet";

if( $wpdb->get_var("SHOW TABLES LIKE '$ds_tt_tablename'") == $ds_tt_tablename ) {

    $sql = "DROP TABLE `$ds_tt_tablename`;";
    $wpdb->query($sql);
}

if( $wpdb->get_var("SHOW TABLES LIKE '$ds_tt_tablename2'") == $ds_tt_tablename2 ) {

    $sql2 = "DROP TABLE `$ds_tt_tablename2`;";
    $wpdb->query($sql2);
}
?>