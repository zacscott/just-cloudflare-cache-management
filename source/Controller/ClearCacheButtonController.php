<?php

namespace JustCloudflareCacheManagement\Controller;

use JustCloudflareCacheManagement\Library\CacheManager;

/**
 * Responsible for managing the admin bar button which clears the entire Cloudflare cache.
 * 
 * @package JustCloudflareCacheManagement\Controller
 */
class ClearCacheButtonController {

    const NONCE = 'just_cloudflare_cache_management_clear_cache';

    public function __construct() {

        add_action( 'admin_bar_menu', [ $this, 'add_clear_cache_button_to_admin_bar' ], 999999999 );
        add_action( 'admin_init', [ $this, 'handle_clear_cache_request' ] );

    }

    public function add_clear_cache_button_to_admin_bar( $wp_admin_bar ) {

        if ( ! $this->is_authorised_clear_entire_cache() ) {
            return;
        }

        if ( ! empty( $wp_admin_bar ) ) {

            $wp_admin_bar->add_node(
                [
                    'id'    => 'just_cloudflare_cache_management_clear_cache',
                    'title' => 'Clear Cache',
                    'href'  => $this->get_clear_cache_url(),
                ]
            );

        }

    }

    public function handle_clear_cache_request() {

        if ( ! $this->is_authorised_clear_entire_cache() ) {
            return;
        }

        // Check the request is for us.

        $action = $_GET['just_cloudflare_cache_management'] ?? '';

        if ( 'clear_cache' !== $action ) {
            return;
        }

        // Check the nonce.

        $nonce = $_GET['_wpnonce'] ?? '';
        if ( ! wp_verify_nonce( $nonce, self::NONCE ) ) {
            return;
        }

        // Clear the cache.

        $cache_manager = new CacheManager();
        $cache_manager->clear_cache();
        
        // Display to the user that the cache was cleared successfully.
        add_action(
            'admin_notices',
            function() {
                ?>
                <div class="success notice notice-success is-dismissible">
                    <p>
                        Cloudflare cache cleared successfully.
                    </p>
                </div>
                <?php
            }
        );

    }

    protected function is_authorised_clear_entire_cache() {

        $user_is_authorised = current_user_can( 'manage_options' );

        /**
         * Filter whether the current user is authorised to clear the Cloudflare cache.
         * 
         * @param bool $user_is_authorised
         */
        $user_is_authorised = apply_filters( 'just_cloudflare_cache_management_user_is_authorised_clear_entire_cache', $user_is_authorised );

        return $user_is_authorised;

    }

    protected function get_clear_cache_url() {

        $clear_cache_url = wp_nonce_url(
            admin_url( 'index.php?just_cloudflare_cache_management=clear_cache' ),
            self::NONCE
        );

        return $clear_cache_url;
    }

}
