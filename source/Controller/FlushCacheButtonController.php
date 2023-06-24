<?php

namespace JustCloudflareCacheManagement\Controller;

use JustCloudflareCacheManagement\Library\CacheManager;
use JustCloudflareCacheManagement\Library\CloudflareAPI;

/**
 * Responsible for managing the admin bar button which flush the entire Cloudflare cache.
 * 
 * @package JustCloudflareCacheManagement\Controller
 */
class FlushCacheButtonController {

    const NONCE = 'just_cloudflare_cache_management_flush_cache';

    public function __construct() {

        $cloudflare_api = new CloudflareAPI();
        if ( $cloudflare_api->is_configured() ) {

            add_action( 'admin_bar_menu', [ $this, 'add_flush_cache_button_to_admin_bar' ], 999999999 );
            add_action( 'admin_init', [ $this, 'handle_flush_cache_request' ] );

        }

    }

    public function add_flush_cache_button_to_admin_bar( $wp_admin_bar ) {

        if ( ! $this->is_authorised_flush_entire_cache() ) {
            return;
        }

        if ( ! empty( $wp_admin_bar ) ) {

            $wp_admin_bar->add_node(
                [
                    'id'    => 'just_cloudflare_cache_management_flush_cache',
                    'title' => __( 'Flush Cache', 'just-cloudflare-cache-management' ),
                    'href'  => $this->get_flush_cache_url(),
                ]
            );

        }

    }

    public function handle_flush_cache_request() {

        if ( ! $this->is_authorised_flush_entire_cache() ) {
            return;
        }

        // Check the request is for us.

        $action = $_GET['just_cloudflare_cache_management'] ?? '';

        if ( 'flush_cache' !== $action ) {
            return;
        }

        // Check the nonce.

        $nonce = $_GET['_wpnonce'] ?? '';
        if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
            return;
        }

        // Clear the cache.

        $cache_manager = new CacheManager();
        $cache_manager->flush_cache();
        
        // Display to the user that the cache was cleared successfully.
        add_action(
            'admin_notices',
            function() {
                ?>
                <div class="success notice notice-success is-dismissible">
                    <p><?php echo esc_html( __( 'Cloudflare cache flushed successfully.', 'just-cloudflare-cache-management' ) ); ?></p>
                </div>
                <?php
            }
        );

    }

    protected function is_authorised_flush_entire_cache() {

        $user_is_authorised = current_user_can( 'manage_options' );

        /**
         * Filter whether the current user is authorised to clear the Cloudflare cache.
         * 
         * @param bool $user_is_authorised
         */
        $user_is_authorised = apply_filters( 'just_cloudflare_cache_management_user_is_authorised_flush_entire_cache', $user_is_authorised );

        return $user_is_authorised;

    }

    protected function get_flush_cache_url() {

        $flush_cache_url = wp_nonce_url(
            admin_url( 'index.php?just_cloudflare_cache_management=flush_cache' ),
            self::NONCE
        );

        return $flush_cache_url;
    }

}
