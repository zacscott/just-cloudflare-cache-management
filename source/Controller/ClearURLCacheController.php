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
        // TODO for taxonomy terms.

    }

    public function clear_cloudflare_cache_on_post_update( $post_id, $post_after, $post_before ) {

        // TODO only when post status cahnged OR post published.

        $cache_manager = new CacheManager();

        $cache_manager->clear_cache_for_urls(
            $this->get_urls_for_post( $post_after )
        );

    }

    protected function get_urls_for_post( $post ) {

        $urls = [];

        $urls[] = get_permalink( $post );

        $urls[] = home_url();
        $urls[] = home_url( 'sitemap' );
        $urls[] = home_url( 'feed' );

        // Get URLs for all of the taxonomy terms associated with the post.

        $taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type, 'hide_empty' => true ) );
        foreach( $taxonomies as $taxonomy ) {
            $urls = array_merge( $urls, $this->get_urls_for_post_terms( $post, $taxonomy ) );
        }

        // Normalise all URLs to not have a trailing slash.

        foreach ( $urls as $key => $url ) {
            $urls[ $key ] = untrailingslashit( $url );
        }

        // TODO filter here pls.

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

}
