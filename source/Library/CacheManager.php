<?php

namespace JustCloudflareCacheManagement\Library;

class CacheManager {

    /**
     * Clear the Cloudflare cache for a specific post.
     * 
     * @param int $post_id The ID of the post to clear the cache for.
     */
    public function clear_for_post( int $post_id ) {

        return $this->clear_for_url( get_permalink( $post_id ) );

    }

    /**
     * Clear the cache for a specific URL.
     * 
     * @param string $url The URL to clear the cache for.
     */
    public function clear_for_url( string $url ) {

        $cloudflare_api = new CloudflareAPI();

        $cloudflare_api->clear_for_url( $url );
        $this->clear_object_cache();

    }

    /**
     * Clear the entire cache.
     */
    public function clear_cache() {

        $cloudflare_api = new CloudflareAPI();

        $cloudflare_api->clear_cache();
        $this->clear_object_cache();

    }

    /**
     * Clear the entire WordPress object cache.
     */
    public function clear_object_cache() {

        // Flush the object cache.
        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }

    }

}
