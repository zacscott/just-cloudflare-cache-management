<?php

namespace JustCloudflareCacheManagement\Library;

class CacheManager {

    /**
     * Clear the cache for specific URLs.
     * 
     * @param array $url_prefixes The URL prefixes to clear the cache for.
     */
    public function clear_cache_for_urls( array $url_prefixes ) {

        $cloudflare_api = new CloudflareAPI();

        $cloudflare_api->clear_cache_for_urls( $url_prefixes );
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
