<?php
/*
Plugin Name: Wp CRUD Plugin
Plugin URI: https://classysystem.com/
Description: Using dynamic database 
Version: 1.0
Author: Gazi Akter
Author URI: https://gaziakter.com/
License: GPLv2 or later
Text Domain: wp-crud
Domain Path: /languages/
*/

/** 
 * Create Table 
 */
function wp_cruud_activation(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'crud_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        content text NOT NULL,
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

}
register_activation_hook( __FILE__, "wp_cruud_activation");