<?php

require_once plugin_dir_path(__FILE__) . 'includes/utilities.php';


function db_3d_viewer_admin_page()
{

    die("Sanu Khan");
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

// The JavaScript for the WordPress media uploader will require the WordPress media scripts to be enqueued.
function db_3d_viewer_admin_enqueue_scripts()
{
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'db_3d_viewer_admin_enqueue_scripts');
