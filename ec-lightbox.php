<?php
/**
 * Plugin Name: EC Lightbox
 * Plugin URI: https://ercoliconsulting.eu
 * Description: Optional lightbox for images and galleries using GLightbox, activated via a custom CSS class on blocks.
 * Version: 1.0.0
 * Author: Danilo Ercoli
 * Author URI: https://ercoliconsulting.eu
 * License: GPLv3
 * Text Domain: ec-lightbox
 * Update URI: false
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue GLightbox and our init script only on singular pages.
 */
function ec_lightbox_enqueue_assets() {
    if ( is_admin() || ! is_singular() ) {
        return;
    }

    $plugin_url = plugin_dir_url( __FILE__ );

    /**
     * Allow switching to a CDN instead of local vendor files.
     *
     * Example:
     *   add_filter( 'ec_lightbox_use_cdn', '__return_true' );
     */
    $use_cdn = apply_filters( 'ec_lightbox_use_cdn', false );

    if ( $use_cdn ) {
        // GLightbox from CDN (example with jsDelivr).
        wp_enqueue_style(
            'glightbox',
            'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css',
            array(),
            '3.2.0'
        );

        wp_enqueue_script(
            'glightbox',
            'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js',
            array(),
            '3.2.0',
            true
        );
    } else {
        // GLightbox local vendor files.
        wp_enqueue_style(
            'glightbox',
            $plugin_url . 'assets/vendors/glightbox/glightbox.min.css',
            array(),
            '3.2.0'
        );

        wp_enqueue_script(
            'glightbox',
            $plugin_url . 'assets/vendors/glightbox/glightbox.min.js',
            array(),
            '3.2.0',
            true
        );
    }

    // Our minimal CSS.
    wp_enqueue_style(
        'ec-lightbox',
        $plugin_url . 'assets/ec-lightbox.css',
        array( 'glightbox' ),
        '1.0.0'
    );

    // Our init script.
    wp_enqueue_script(
        'ec-lightbox',
        $plugin_url . 'assets/ec-lightbox.js',
        array( 'glightbox' ),
        '1.0.0',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'ec_lightbox_enqueue_assets', 20 );
