<?php
// includes/utilities.php

/**
 * Fetch all 3D models from the database.
 * 
 * @return array An array of objects containing product_name and glb_url for each model.
 */
function db_3d_viewer_get_all_models()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "3d_viewer";
    return $wpdb->get_results("SELECT * FROM $table_name");
}

/**
 * Generate a shortcode for a given GLB URL.
 * 
 * @param string $glb_url The GLB URL for which the shortcode is to be generated.
 * @return string The shortcode for the provided GLB URL.
 */
function db_3d_viewer_generate_shortcode($glb_url)
{
    return '[db_3d_viewer glb_url="' . esc_url($glb_url) . '"]';
}

/**
 * Convert GLB URL to viewer URL.
 * 
 * @param string $glb_url The GLB URL to be converted.
 * @return string The viewer URL format with the provided GLB URL.
 */
function db_3d_viewer_convert_to_viewer_url($glb_url)
{
    return 'https://phpstack-947027-3534862.cloudwaysapps.com/?modelUrl=' . urlencode($glb_url);
}
