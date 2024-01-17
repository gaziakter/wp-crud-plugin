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
function wp_crud_activation(){
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
register_activation_hook( __FILE__, "wp_crud_activation");


/**
 * Creating custom admin menu item
 */
function custom_data_menu() {
    $page_title = 'Custom Data';
    $menu_title = 'Custom Data';
    $capability = 'manage_options';
    $menu_slug = 'custom-data';
    $function = 'custom_data_page';
    $icon_url = 'dashicons-admin-generic';
    $position = 25;

    add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);

    // Submenu pages
    add_submenu_page($menu_slug, 'Add New', 'Add New', $capability, 'custom-data-add', 'custom_data_add_page');
    add_submenu_page($menu_slug, 'Edit', 'Edit', $capability, 'custom-data-edit', 'custom_data_edit_page');
}
add_action('admin_menu', 'custom_data_menu');


function custom_data_page() {
    // Main custom data management page content
}

function custom_data_add_page() {
    // Add new custom data page content
}

function custom_data_edit_page() {
    // Edit custom data page content
}