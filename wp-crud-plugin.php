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

/**
 * Adding New Custom Data
 */
function custom_data_add_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['custom_data_nonce']) && wp_verify_nonce($_POST['custom_data_nonce'], 'custom_data_add')) {
        $title = sanitize_text_field($_POST['title']);
        $content = sanitize_textarea_field($_POST['content']);
        $created_at = current_time('mysql');
        $updated_at = current_time('mysql');

        $wpdb->insert($table_name, compact('title', 'content', 'created_at', 'updated_at'));

        echo '<div class="notice notice-success is-dismissible"><p>Custom data added successfully!</p></div>';
        echo '<script>window.location.href="' . admin_url('admin.php?page=custom-data') . '";</script>';
    }

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Add New Custom Data</h1>';
    echo '<hr class="wp-header-end">';

    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th scope="row"><label for="title">Title</label></th>';
    echo '<td><input name="title" type="text" id="title" class="regular-text" required></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row"><label for="content">Content</label></th>';
    echo '<td><textarea name="content" id="content" class="large-text" rows="10" required></textarea></td>';
    echo '</tr>';
    echo '</table>';

    echo '<input type="hidden" name="custom_data_nonce" value="' . wp_create_nonce('custom_data_add') . '">';
    echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Add New"></p>';
    echo '</form>';
    echo '</div>';
}

/**
 * Editing and Updating custom data
 */
function custom_data_edit_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

    if (!$row) {
        echo '<div class="notice notice-error is-dismissible"><p>Invalid custom data ID!</p></div>';
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['custom_data_nonce']) && wp_verify_nonce($_POST['custom_data_nonce'], 'custom_data_edit')) {
        $title = sanitize_text_field($_POST['title']);
        $content = sanitize_textarea_field($_POST['content']);
        $updated_at = current_time('mysql');

        $wpdb->update($table_name, compact('title', 'content', 'updated_at'), array('id' => $id));

        echo '<div class="notice notice-success is-dismissible"><p>Custom data updated successfully!</p></div>';
        echo '<script>window.location.href="' . admin_url('admin.php?page=custom-data') . '";</script>';
    }

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Edit Custom Data</h1>';
    echo '<hr class="wp-header-end">';

    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr>';
    echo '<th scope="row"><label for="title">Title</label></th>';
    echo '<td><input name="title" type="text" id="title" value="' . esc_attr($row->title) . '" class="regular-text" required></td>';
    echo '</tr>';
    echo '<tr>';
    echo '<th scope="row"><label for="content">Content</label></th>';
    echo '<td><textarea name="content" id="content" class="large-text" rows="10" required>' . esc_textarea($row->content) . '</textarea></td>';
    echo '</tr>';
    echo '</table>';

    echo '<input type="hidden" name="custom_data_nonce" value="' . wp_create_nonce('custom_data_edit') . '">';
    echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Update"></p>';
    echo '</form>';
    echo '</div>';
}

/**
 * Deleting custon data
 */
function delete_custom_data() {
    check_ajax_referer('delete_custom_data_nonce', 'nonce');
    global $wpdb;
    $table_name = $wpdb->prefix . 'custom_data';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    $result = $wpdb->delete($table_name, array('id' => $id));
    wp_send_json_success($result);
}
add_action('wp_ajax_delete_custom_data', 'delete_custom_data');

function custom_data_admin_scripts() {
    wp_enqueue_script('custom-data', plugin_dir_url( __FILE__ ) . '/assets/js/custom-data.js', array('jquery'), false, true);
    wp_localize_script('custom-data', 'customData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'delete_nonce' => wp_create_nonce('delete_custom_data_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'custom_data_admin_scripts');