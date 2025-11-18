<?php
/**
 * Plugin Name: EC Lightbox
 * Plugin URI: https://ercoliconsulting.eu
 * Description: Optional lightbox for images and galleries using GLightbox, activated via a custom CSS class on blocks.
 * Version: 1.1.0
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
 * Get plugin options with defaults.
 */
function ec_lightbox_get_options() {
    $defaults = array(
        'use_cdn'         => 0,
        'loop'            => 0,
        'touch_navigation'=> 1,
        'zoomable'        => 1,
        'autoplay_videos' => 0,
        'add_referrer_policy' => 0,
    );

    $saved = get_option( 'ec_lightbox_options', array() );
    if ( ! is_array( $saved ) ) {
        $saved = array();
    }

    return array_merge( $defaults, $saved );
}

/**
 * Enqueue GLightbox and our init script only on singular pages.
 */
function ec_lightbox_enqueue_assets() {
    if ( is_admin() || ! is_singular() ) {
        return;
    }

    $plugin_url = plugin_dir_url( __FILE__ );
    $options    = ec_lightbox_get_options();

    // Allow filter override, but default to option "use_cdn".
    $use_cdn = apply_filters( 'ec_lightbox_use_cdn', ! empty( $options['use_cdn'] ) );
    // add_filter( 'ec_lightbox_use_cdn', '__return_false' ); to force-disable
    // (or __return_true to force-enable it globally).

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
        '1.1.0'
    );

    // Our init script.
    wp_enqueue_script(
        'ec-lightbox',
        $plugin_url . 'assets/ec-lightbox.js',
        array( 'glightbox' ),
        '1.1.0',
        true
    );

    // Default JS options for GLightbox, based on plugin settings.
    $js_options = array(
        'touchNavigation' => ! empty( $options['touch_navigation'] ),
        'loop'            => ! empty( $options['loop'] ),
        'zoomable'        => ! empty( $options['zoomable'] ),
        'autoplayVideos'  => ! empty( $options['autoplay_videos'] ),
        'addReferrerPolicy'=> ! empty( $options['add_referrer_policy'] )
    );

    /**
     * Filter GLightbox options passed to the frontend.
     *
     * Example:
     * add_filter( 'ec_lightbox_js_options', function ( $options ) {
     *     $options['loop'] = true;
     *     return $options;
     * } );
     */
    $js_options = apply_filters( 'ec_lightbox_js_options', $js_options );

    wp_localize_script(
        'ec-lightbox',
        'ecLightboxOptions',
        $js_options
    );
}
add_action( 'wp_enqueue_scripts', 'ec_lightbox_enqueue_assets', 20 );

/**
 * Register settings page.
 */
function ec_lightbox_add_settings_page() {
    add_options_page(
        __( 'EC Lightbox', 'ec-lightbox' ),
        __( 'EC Lightbox', 'ec-lightbox' ),
        'manage_options',
        'ec-lightbox',
        'ec_lightbox_render_settings_page'
    );
}
add_action( 'admin_menu', 'ec_lightbox_add_settings_page' );

/**
 * Register setting for EC Lightbox options.
 */
function ec_lightbox_register_settings() {
    register_setting(
        'ec_lightbox_options_group',
        'ec_lightbox_options',
        'ec_lightbox_sanitize_options'
    );
}
add_action( 'admin_init', 'ec_lightbox_register_settings' );

/**
 * Sanitize options before saving.
 */
function ec_lightbox_sanitize_options( $input ) {
    $output = array();

    $output['use_cdn']          = ! empty( $input['use_cdn'] ) ? 1 : 0;
    $output['loop']             = ! empty( $input['loop'] ) ? 1 : 0;
    $output['touch_navigation'] = ! empty( $input['touch_navigation'] ) ? 1 : 0;
    $output['zoomable']         = ! empty( $input['zoomable'] ) ? 1 : 0;
    $output['autoplay_videos']  = ! empty( $input['autoplay_videos'] ) ? 1 : 0;
    $output['add_referrer_policy'] = ! empty( $input['add_referrer_policy'] ) ? 1 : 0;

    return $output;
}

/**
 * Render settings page HTML.
 */
function ec_lightbox_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $options = ec_lightbox_get_options();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'EC Lightbox Settings', 'ec-lightbox' ); ?></h1>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'ec_lightbox_options_group' );
            // No sections/fields via Settings API, we output the form manually.
            ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="ec_lightbox_use_cdn">
                                <?php esc_html_e( 'Use CDN for GLightbox', 'ec-lightbox' ); ?>
                            </label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox"
                                    id="ec_lightbox_use_cdn"
                                    name="ec_lightbox_options[use_cdn]"
                                    value="1" <?php checked( $options['use_cdn'], 1 ); ?> />
                                <?php esc_html_e( 'Load GLightbox from jsDelivr (cdn.jsdelivr.net) instead of the local vendor JS/CSS files.', 'ec-lightbox' ); ?>
                            </label>
                            <p class="description">
                                <?php esc_html_e( 'By default, EC Lightbox uses the local GLightbox assets bundled with the plugin. Enable this to use the jsDelivr CDN URLs instead. You can also override or disable this behavior programmatically using the ec_lightbox_use_cdn filter.', 'ec-lightbox' ); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Lightbox behavior', 'ec-lightbox' ); ?>
                        </th>
                        <td>
                            <p class="description">
                                <?php esc_html_e( 'These options are passed to GLightbox on initialization for each gallery.', 'ec-lightbox' ); ?>
                            </p>
                            <label>
                                <input type="checkbox"
                                    name="ec_lightbox_options[loop]"
                                    value="1" <?php checked( $options['loop'], 1 ); ?> />
                                <?php esc_html_e( 'Loop images inside each gallery', 'ec-lightbox' ); ?>
                            </label>
                            <br/>
                            <label>
                                <input type="checkbox"
                                    name="ec_lightbox_options[touch_navigation]"
                                    value="1" <?php checked( $options['touch_navigation'], 1 ); ?> />
                                <?php esc_html_e( 'Enable touch navigation (swipe on mobile)', 'ec-lightbox' ); ?>
                            </label>
                            <br/>
                            <label>
                                <input type="checkbox"
                                    name="ec_lightbox_options[zoomable]"
                                    value="1" <?php checked( $options['zoomable'], 1 ); ?> />
                                <?php esc_html_e( 'Allow zoom on images', 'ec-lightbox' ); ?>
                            </label>
                            <br/>
                            <label>
                                <input type="checkbox"
                                    name="ec_lightbox_options[autoplay_videos]"
                                    value="1" <?php checked( $options['autoplay_videos'], 1 ); ?> />
                                <?php esc_html_e( 'Autoplay videos (if used in future)', 'ec-lightbox' ); ?>
                            </label>
                            <br/>
                            <label>
                                <input type="checkbox"
                                    name="ec_lightbox_options[add_referrer_policy]"
                                    value="1" <?php checked( $options['add_referrer_policy'], 1 ); ?> />
                                <?php esc_html_e( 'Add referrerpolicy="some-origin" to gallery images', 'ec-lightbox' ); ?>
                            </label>

                            <p class="description">
                                <?php esc_html_e( 'When enabled, all <img> elements inside ec-lightbox containers will include referrerpolicy="some-origin".', 'ec-lightbox' ); ?>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>

        <hr/>

        <h2><?php esc_html_e( 'How to use EC Lightbox', 'ec-lightbox' ); ?></h2>
        <p>
            <?php esc_html_e( 'Add the CSS class "ec-lightbox" to a Gallery block (or any container with images) and make sure images have no click/lightbox action configured in the editor. EC Lightbox will automatically wrap images in links and enable the lightbox for that container.', 'ec-lightbox' ); ?>
        </p>
    </div>
    <?php
}
