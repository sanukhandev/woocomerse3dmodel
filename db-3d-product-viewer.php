<?php

/**
 * Plugin Name: DB 3D Product Viewer
 * Description: A plugin to view 3D products via GLB files.
 * Version: 1.0
 * Author: Digital Buddha
 * Author URI: https://www.digitalbuddha.in/
 */

// Ensure that the file is not accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/utilities.php';


// Create table on plugin activation.
register_activation_hook(__FILE__, 'db_3d_viewer_create_table');
function db_3d_viewer_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "3d_viewer";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product_name varchar(255) NOT NULL,
        glb_url varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Add admin menu.
add_action('admin_menu', 'db_3d_viewer_menu');
function db_3d_viewer_menu()
{
    add_submenu_page('tools.php', 'DB 3D Product Viewer', '3D Viewer', 'manage_options', 'db-3d-viewer', 'db_3d_viewer_admin_page');
}

// Admin page content.
function db_3d_viewer_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "3d_viewer";

    // Check if GLB file is uploaded and save to database.
    if (isset($_POST['glb_url']) && isset($_POST['product_name'])) {
        $wpdb->insert($table_name, array(
            'glb_url' => sanitize_text_field($_POST['glb_url']),
            'product_name' => sanitize_text_field($_POST['product_name'])
        ));
    }

    echo '<h2>Upload GLB File</h2>';
    echo '<input type="button" value="Upload GLB" onclick="uploadGLB();" class="button-primary">';

    // Displaying uploaded models.
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<h2>Uploaded Models</h2>';
    echo '<table>';
    foreach ($results as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row->product_name) . '</td>';
        echo '<td>' . esc_url($row->glb_url) . '</td>';
        echo '<td>[db_3d_viewer glb_url="' . esc_url($row->glb_url) . '"]</td>';
        echo '</tr>';
    }
    echo '</table>';

    // JavaScript for the WordPress media uploader.
?>
    <script>
        function uploadGLB() {
            var uploader = wp.media({
                title: 'Select GLB',
                button: {
                    text: 'Select'
                },
                multiple: false
            }).on('select', function() {
                var selection = uploader.state().get('selection');
                var attachment = selection.first().toJSON();
                // Save or process the attachment URL as needed...
            }).open();
        }
    </script>
<?php
}

// Shortcode implementation.
function db_3d_viewer_shortcode($atts)
{
    $atts = shortcode_atts(array('glb_url' => ''), $atts, 'db_3d_viewer');

    if (is_singular('product')) {
        // If inside a WooCommerce product.
        return '<button onclick="window.open(\'' . esc_url('https://phpstack-947027-3534862.cloudwaysapps.com/?modelUrl=' . $atts['glb_url']) . '\')">View 3D Model</button>';
    } else {
        // Anywhere else in WordPress.
        return '<iframe src="' . esc_url('https://phpstack-947027-3534862.cloudwaysapps.com/?modelUrl=' . $atts['glb_url']) . '" style="width:100%; height:500px;"></iframe>';
    }
}
add_shortcode('db_3d_viewer', 'db_3d_viewer_shortcode');

function db_3d_viewer_admin_assets($hook)
{
    // Ensure that scripts/styles are only loaded on our plugin's admin page.
    if ($hook != 'tools_page_db-3d-viewer') {
        return;
    }

    // Enqueue the admin styles.
    wp_enqueue_style('db-3d-viewer-admin-style', plugin_dir_url(__FILE__) . 'admin/admin-style.css');

    // Enqueue the admin scripts.
    wp_enqueue_script('db-3d-viewer-admin-script', plugin_dir_url(__FILE__) . 'admin/admin-script.js', array('jquery'), false, true);
}

add_action('admin_enqueue_scripts', 'db_3d_viewer_admin_assets');

function db_3d_viewer_public_assets()
{
    // Enqueue the public styles.
    wp_enqueue_style('db-3d-viewer-public-style', plugin_dir_url(__FILE__) . 'public/public-style.css');

    // Enqueue the public scripts.
    wp_enqueue_script('db-3d-viewer-public-script', plugin_dir_url(__FILE__) . 'public/public-script.js', array('jquery'), false, true);
}

add_action('wp_enqueue_scripts', 'db_3d_viewer_public_assets');



?>