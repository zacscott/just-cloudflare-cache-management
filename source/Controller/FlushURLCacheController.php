<?php

namespace JustCloudflareCacheManagement\Controller;

use JustCloudflareCacheManagement\Library\CacheManager;
use JustCloudflareCacheManagement\Library\CloudflareAPI;

/**
 * Responsible for flushing the Cloudflare cache on update of posts & pages managed by WordPress.
 * This will attempt to clear the required pages only. Including the post permalink,
 * homepage, category page.
 * 
 * @package JustCloudflareCacheManagement\Controller
 */
class FlushURLCacheController {

    public function __construct() {

        $cloudflare_api = new CloudflareAPI();
        if ( $cloudflare_api->is_configured() ) {

            add_action( 'post_updated', [ $this, 'flush_cloudflare_cache_on_post_update' ], 999999999, 3 );
            add_action( 'saved_term', [ $this, 'flush_cloudflare_cache_on_term_update' ], 999999999, 3 );

        }

    }

    /**
     * Clear the cache when a post is updated.
     * 
     * @param int $post_id
     * @param WP_Post $post_after
     * @param WP_Post $post_before
     */
    public function flush_cloudflare_cache_on_post_update( $post_id, $post_after, $post_before ) {

        $cache_manager = new CacheManager();

        $should_flush_cache = false;

        // Clear the cache if the post status has changed.
        $should_flush_cache = $should_flush_cache || $post_after->post_status !== $post_before->post_status;

        // Clear the cache if the post is published.
        $should_flush_cache = $should_flush_cache || 'publish' === $post_after->post_status;

        if ( $should_flush_cache ) {

            if ( 'wp_block' === $post_after->post_type ) {
                // Clear entire site cache when a reusable block is updated, as it could be anywhere on the site.
                
                $cache_manager->flush_cache();

            } else {

                $this->flush_cache_for_post( $post_after );

            }

        }

    }

    /**
     * Clear the cache when a term is updated.
     * 
     * @param int $term_id
     * @param int $tt_id
     * @param string $taxonomy
     */
    public function flush_cloudflare_cache_on_term_update( $term_id, $tt_id, $taxonomy ) {

        $term = get_term_by( 'ID', $term_id, $taxonomy );
        if ( $term && ! is_wp_error( $term ) ) {

            $urls = $this->get_urls_for_term( $term );

            /**
             * Filter the URLs which should be cleared from the cache when the given post is updated.
             * 
             * @param array $urls The URLs to clear the cache for.
             * @param WP_Term $term ID of the post that was updated.
             */
            $urls = apply_filters( 'just_cloudflare_cache_management_term_urls', $urls, $term );

            // Clear the cache

            $cache_manager = new CacheManager();
            $cache_manager->flush_cache_for_urls( $urls );

        }

    }

    protected function flush_cache_for_post( $post ) {

        $urls = $this->get_urls_for_post( $post );

        /**
         * Filter the URLs which should be cleared from the cache when the given post is updated.
         * 
         * @param array $urls The URLs to clear the cache for.
         * @param int $post_id ID of the post that was updated.
         */
        $urls = apply_filters( 'just_cloudflare_cache_management_post_urls', $urls, $post->ID );

        // Clear the cache

        $cache_manager = new CacheManager();
        $cache_manager->flush_cache_for_urls( $urls );

    }

    protected function get_urls_for_post( $post ) {

        $urls = [];

        $urls[] = get_permalink( $post );

        $urls[] = home_url();
        $urls[] = home_url( 'sitemap' );
        $urls[] = home_url( 'feed' );

        // Get URLs for all of the taxonomy terms associated with the post.

        $taxonomies = get_object_taxonomies(
            (object)
            [
                'post_type' => $post->post_type,
                'hide_empty' => true
            ]
        );

        foreach( $taxonomies as $taxonomy ) {
            $urls = array_merge( $urls, $this->get_urls_for_post_terms( $post, $taxonomy ) );
        }

        // Normalise all URLs to not have a trailing slash.

        foreach ( $urls as $key => $url ) {
            $urls[ $key ] = untrailingslashit( $url );
        }

        return $urls;

    }

    protected function get_urls_for_post_terms( $post, $taxonomy ) {

        $urls = [];

        $terms = get_the_terms( $post->ID, $taxonomy );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

            foreach ( $terms as $term ) {

                $term_url = get_term_link( $term, $taxonomy );
                
                if ( $term_url ) {
                    $urls[] = $term_url;
                }

            }

        }

        return $urls;

    }

    protected function get_urls_for_term( $term ) {

        $urls = [];

        $urls[] = home_url( 'sitemap' );
        $urls[] = home_url( 'feed' );

        $urls[] = get_term_link( $term );

        return $urls;

    }

}
