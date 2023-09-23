<?php

/**
 * Plugin Name: 3D Model Showcase
 * Plugin URI: https://www.innovz.it/
 * Description: A plugin to upload and display 3D GLB files.
 * Version: 1.0
 * Author: Sanu Khan
 * Author URI: https://www.innovz.it/
 * License: GPL2
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
    add_menu_page(
        '3D Model Showcase Admin',  // Page title
        '3D Model Showcase',        // Menu title
        'manage_options',         // Capability required to see the menu
        'db-3d-viewer',           // Menu slug
        'db_3d_viewer_admin_page', // The function to be called to output the content for this page
        'dashicons-format-image', // Icon for the menu (I'm using a generic image icon, you can choose other dashicons or omit this parameter for default icon)
        6                         // Position in the menu order where it should appear. The lower the number, the higher its position. 
    );
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

    echo '<div class="wrap">';
    echo '<h1>DB 3D Product Viewer - Upload GLB File</h1>';

    // Display the file upload form.
    echo '<form method="post" action="">';
    echo '<label for="product_name">Product Name:</label>';
    echo '<input type="text" name="product_name" required>';
    echo '<label for="glb_url">GLB URL:</label>';
    echo '<input type="text" name="glb_url" id="glb_url" required>';
    echo '<button type="button" onclick="uploadGLB()" class="button">Select GLB</button>';
    echo '<input type="submit" value="Upload" class="button-primary">';
    echo '</form>';

    // Displaying uploaded models.
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<h2>Uploaded Models</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Product Name</th><th>GLB URL</th><th>Shortcode</th></tr></thead>';
    echo '<tbody>';
    foreach ($results as $row) {
        echo '<tr>';
        echo '<td>' . esc_html($row->product_name) . '</td>';
        echo '<td>' . esc_url($row->glb_url) . '</td>';
        echo '<td>[db_3d_viewer glb_url="' . esc_url($row->glb_url) . '"]</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';

    echo '</div>';

    // JavaScript for the WordPress media uploader.
?>
    <script type="text/javascript">
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

                // Assuming you'd want to populate a text input with the attachment URL.
                document.querySelector('input[name="glb_url"]').value = attachment.url;
            }).open();
        }
    </script>
<?php
}

// Shortcode implementation.
function db_3d_viewer_shortcode($atts)
{
    $atts = shortcode_atts(array('glb_url' => ''), $atts, 'db_3d_viewer');

    if (is_singular('product') && false) {
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

    if (isset($_GET['page']) && $_GET['page'] == 'db-3d-viewer') {
        wp_enqueue_media(); // This will enqueue the necessary Media Library scripts and styles
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

function custom_glb_upload_validation($data, $file, $filename, $mimes)
{
    // Check if the file is a GLB
    if (strpos($filename, '.glb') !== false) {
        // Bypass WordPress's check and manually specify the MIME type
        $data['type'] = 'model/gltf-binary';
        $data['ext'] = 'glb';
        $data['proper_filename'] = $filename;
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'custom_glb_upload_validation', 10, 4);

function db_3d_viewer_custom_upload_mimes($mimes = array())
{
    $mimes['glb'] = 'model/gltf-binary';
    error_log("The custom MIME type filter ran!");
    return $mimes;
}
add_filter('upload_mimes', 'db_3d_viewer_custom_upload_mimes', 9999);

function db_3d_viewer_enqueue_scripts()
{
    if (isset($_GET['page']) && $_GET['page'] == 'db-3d-viewer') {
        wp_enqueue_media(); // This will enqueue the necessary Media Library scripts and styles
    }
}
add_action('admin_enqueue_scripts', 'db_3d_viewer_enqueue_scripts');



?>