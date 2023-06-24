<?php

namespace JustCloudflareCacheManagement\Library;

class CacheManager {

    /**
     * Flush the cache for specific URLs.
     * 
     * @param array $url_prefixes The URL prefixes to clear the cache for.
     */
    public function flush_cache_for_urls( array $url_prefixes ) {

        $cloudflare_api = new CloudflareAPI();

        $cloudflare_api->flush_cache_for_urls( $url_prefixes );
        $this->flush_object_cache();

    }

    /**
     * Flush the entire cache.
     */
    public function flush_cache() {

        $cloudflare_api = new CloudflareAPI();

        $cloudflare_api->flush_cache();
        $this->flush_object_cache();

    }

    /**
     * Flush the entire WordPress object cache.
     */
    public function flush_object_cache() {

        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }

    }

}
