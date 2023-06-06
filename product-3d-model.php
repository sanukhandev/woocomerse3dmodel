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
        update_post_meta( $post_id, '_3d_model_url', esc_url( $_POST['_3d_model_url'] ) );
    }
}
add_action( 'woocommerce_process_product_meta', 'save_3d_model_product_field' );

// Display 3D model on product page
function display_3d_model_on_product_page() {
    global $product;

    $product_id = $product->get_id();
    $model_url = get_post_meta( $product_id, '_3d_model_url', true );

    if ( ! empty( $model_url ) ) {
        echo '<div id="product-3d-model"></div>';
        echo '<script>
        var scene = new THREE.Scene();
        var camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 0.1, 1000 );
        var renderer = new THREE.WebGLRenderer();

        renderer.setSize( window.innerWidth, window.innerHeight );
        document.getElementById("product-3d-model").appendChild( renderer.domElement );

        var loader = new THREE.GLTFLoader();

        loader.load( "' . esc_url( $model_url ) . '", function ( gltf ) {
            scene.add( gltf.scene );
        }, undefined, function ( error ) {
            console.error( error );
        } );

        camera.position.z = 5;

        var animate = function () {
            requestAnimationFrame( animate );
            renderer.render( scene, camera );
        };

        animate();
        </script>';
    }
}
add_action( 'woocommerce_before_single_product_summary', 'display_3d_model_on_product_page' );

// Enqueue scripts and styles
function enqueue_3d_model_scripts_and_styles() {
    wp_enqueue_script( 'three', 'https://unpkg.com/three@0.139.2/build/three.min.js', array(), null, true );
    wp_enqueue_style( 'product-3d-model', plugins_url( '/css/product-3d-model.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'enqueue_3d_model_scripts_and_styles' );
