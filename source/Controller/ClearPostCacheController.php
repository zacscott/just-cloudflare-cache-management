<?php

namespace JustCloudflareCacheManagement\Controller;

use JustCloudflareCacheManagement\Library\CacheManager;

/**
 * Responsible for clearing the Cloudflare cache on update of posts.
 * This will attempt to clear the required pages only. Including the post permalink,
 * homepage, category page.
 * 
 * @package JustCloudflareCacheManagement\Controller
 */
class ClearPostCacheController {

    public function __construct() {
        
        add_action( 'init', [ $this, 'test' ] );
        /**
         * TODO on post_save
         * clear the permalink, category page, homepage, term pages, sitemap*
         * add hook/filter to allow custom pages to be cleared
         */

    }

    function test() {

        $cache_manager = new CacheManager();

        var_dump( $cache_manager->clear_cache_for_urls( [ 'https://zacscott.net/test' ] ) );
        die();

    }

}
