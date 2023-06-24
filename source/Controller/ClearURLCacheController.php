<?php

namespace JustCloudflareCacheManagement\Controller;

use JustCloudflareCacheManagement\Library\CacheManager;

/**
 * Responsible for clearing the Cloudflare cache on update of posts & pages managed by WordPress.
 * This will attempt to clear the required pages only. Including the post permalink,
 * homepage, category page.
 * 
 * @package JustCloudflareCacheManagement\Controller
 */
class ClearURLCacheController {

    public function __construct() {

        add_action( 'post_updated', [ $this, 'clear_cloudflare_cache_on_post_update' ], 999999999, 3 );
        add_action( 'saved_term', [ $this, 'clear_cloudflare_cache_on_term_update' ], 999999999, 3 );

    }

    public function clear_cloudflare_cache_on_post_update( $post_id, $post_after, $post_before ) {

        $should_clear_cache = false;

        // Clear the cache if the post status has changed.
        $should_clear_cache = $should_clear_cache || $post_after->post_status !== $post_before->post_status;

        // Clear the cache if the post is published.
        $should_clear_cache = $should_clear_cache || 'published' === $post_after->post_status;

        if ( $should_clear_cache ) {

            $urls = $this->get_urls_for_post( $post_after );

            /**
             * Filter the URLs which should be cleared from the cache when the given post is updated.
             * 
             * @param array $urls The URLs to clear the cache for.
             * @param int $post_id ID of the post that was updated.
             */
            $urls = apply_filters( 'just_cloudflare_cache_management_post_urls', $urls, $post_id );

            // Clear the cache

            $cache_manager = new CacheManager();
            $cache_manager->clear_cache_for_urls( $urls );

        }

    }

    public function clear_cloudflare_cache_on_term_update( $term_id, $tt_id, $taxonomy ) {

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
            $cache_manager->clear_cache_for_urls( $urls );

        }

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
