<?php
/*
Plugin Name: DB 3D Product Model for WooCommerce
Plugin URI: https://www.digitalbuddha.in
Description:  Digital Buddha 3D WooCommerce Product 3D Model
Version: 1.0
Author: Sanu Khan
Author URI: sanulgbello@gmail.com 
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Add custom fields to product
function add_3d_model_product_field() {
    echo '<div class="options_group">';

    woocommerce_wp_text_input( array(
        'id'                => '_3d_model_url',
        'label'             => __( '3D Model URL', 'woocommerce' ),
        'desc_tip'          => true,
        'description'       => __( 'Enter the URL of your 3D model here.', 'woocommerce' ),
    ) );

    echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'add_3d_model_product_field' );

// Save custom fields
function save_3d_model_product_field( $post_id ) {
    if ( ! empty( $_POST['_3d_model_url'] ) ) {
        update_post_meta( $post_id, '_3d_model_url', $_POST['_3d_model_url']);
    }
}
add_action( 'woocommerce_process_product_meta', 'save_3d_model_product_field' );

// Display "View 3D Product" button on product page
function display_3d_model_on_product_page() {
    global $product;

    $product_id = $product->get_id();
    $model_url = get_post_meta( $product_id, '_3d_model_url', true );

    if ( ! empty( $model_url ) ) {
        $external_viewer_url = "https://phpstack-947027-3534862.cloudwaysapps.com/?modelUrl=";
        $full_url = $external_viewer_url . urlencode($model_url);
        echo '<div id="product-3d-button">';
        echo '<button class="button-3d" onclick="window.open(\'' . esc_url( $full_url ) . '\', \'_blank\', \'toolbar=no,scrollbars=yes,resizable=yes,width=900,height=400\')"><i class="icon-3d"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-badge-3d-fill" viewBox="0 0 16 16"> <path d="M10.157 5.968h-.844v4.06h.844c1.116 0 1.621-.667 1.621-2.02 0-1.354-.51-2.04-1.621-2.04z"/> <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm5.184 4.368c.646 0 1.055.378 1.06.9.008.537-.427.919-1.086.919-.598-.004-1.037-.325-1.068-.756H3c.03.914.791 1.688 2.153 1.688 1.24 0 2.285-.66 2.272-1.798-.013-.953-.747-1.38-1.292-1.432v-.062c.44-.07 1.125-.527 1.108-1.375-.013-.906-.8-1.57-2.053-1.565-1.31.005-2.043.734-2.074 1.67h1.103c.022-.391.383-.751.936-.751.532 0 .928.33.928.813.004.479-.383.835-.928.835h-.632v.914h.663zM8.126 11h2.189C12.125 11 13 9.893 13 7.985c0-1.894-.861-2.984-2.685-2.984H8.126V11z"/> </svg></i> </button>';
        echo '</div>';
    }
}
add_action( 'woocommerce_single_product_summary', 'display_3d_model_on_product_page' );

// Enqueue styles
function enqueue_3d_model_styles() {
    wp_enqueue_style( 'product-3d-model', plugins_url( '/css/product-3d-model.css', __FILE__ ) );
    wp_enqueue_script( 'product-3d-model', plugins_url( '/js/product-3d-model.js', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'enqueue_3d_model_styles' );


function add_custom_data() {
    if ( ! isset( $_POST['custom_option'] ) || ! isset( $_POST['product_id'] ) || ! isset( $_POST['quantity'] ) ) {
        wp_send_json( array( 'error' => true, 'product_url' => get_permalink( $_POST['product_id'] ) ) );
    }

    $product_id = intval( $_POST['product_id'] );
    $quantity = intval( $_POST['quantity'] );
    $custom_option = sanitize_text_field( $_POST['custom_option'] );

    // Add the product to the cart with the custom field
    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

    if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), array( 'custom_option' => $custom_option ) ) ) {
        do_action( 'woocommerce_ajax_added_to_cart', $product_id );
        wp_send_json_success();
    } else {
        wp_send_json( array( 'error' => true, 'product_url' => get_permalink( $product_id ) ) );
    }

    wp_die();
}
add_action( 'wp_ajax_add_custom_data', 'add_custom_data' );
add_action( 'wp_ajax_nopriv_add_custom_data', 'add_custom_data' );

// Store custom field in order item meta
function add_custom_option_order_item_meta( $item, $cart_item_key, $values, $order ) {
    if ( array_key_exists( 'custom_option', $values ) ) {
        $item->add_meta_data( __( 'Customer configration Preference: ', 'woocommerce' ), $values['custom_option'] );
    }
}
add_action( 'woocommerce_checkout_create_order_line_item', 'add_custom_option_order_item_meta', 10, 4 );
