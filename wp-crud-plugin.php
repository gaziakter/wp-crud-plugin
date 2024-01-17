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
    $table_name = $wpdb->prefix . 'custom_data';
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



/**
 * Display custon data in the admin area
 */
function custom_data_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Custom Data</h1>';
    echo '<a href="' . admin_url('admin.php?page=custom-data-add') . '" class="page-title-action">Add New</a>';
    echo '<hr class="wp-header-end">';

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Title</th><th>Content</th><th>Created At</th><th>Updated At</th><th>Actions</th></tr></thead>';
    echo '<tbody>';

    foreach ($results as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row->title) . '</td>';
        echo '<td>' . esc_html($row->content) . '</td>';
        echo '<td>' . esc_html($row->created_at) . '</td>';
        echo '<td>' . esc_html($row->updated_at) . '</td>';
        echo '<td><a href="' . admin_url('admin.php?page=custom-data-edit&id=' . $row->id) . '">Edit</a> | <a href="#" class="delete-link" data-id="' . $row->id . '">Delete</a></td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

function custom_data_add_page() {
    // Add new custom data page content
}

function custom_data_edit_page() {
    // Edit custom data page content
}
