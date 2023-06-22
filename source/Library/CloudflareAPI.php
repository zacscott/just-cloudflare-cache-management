<?php

namespace JustCloudflareCacheManagement\Library;

class CloudflareAPI {

    /**
     * Clear the cache for specific URLs.
     * 
     * @param array $url_prefixes The URL prefixes to clear the cache for.
     */
    public function clear_cache_for_urls( array $url_prefixes ) {

        $success = false;

        $zone_id = $this->get_zone_id();
        if ( $zone_id ) {

            // Build Cloudflare API files array of each URL to be cleared.

            $files = [];
            foreach ( $url_prefixes as $url_prefix ) {

                $files[] = [
                    'url' => $url_prefix,
                ];

            }

            // Make the API call.

            $api_url = sprintf(
                'https://api.cloudflare.com/client/v4/zones/%s/purge_cache',
                $zone_id
            );

            $response = $this->api_call( 
                'DELETE', 
                $api_url,
                [],
                [
                    'files' => $files,
                ]
            );
    
            $success = null !== $response;

        }

        return $success;

    }

    /**
     * Clear the entire Cloudflare cache.
     */
    public function clear_cache() {

        $success = false;

        $zone_id = $this->get_zone_id();
        if ( $zone_id ) {

            $api_url = sprintf(
                'https://api.cloudflare.com/client/v4/zones/%s/purge_cache',
                $zone_id
            );

            $response = $this->api_call( 
                'DELETE', 
                $api_url,
                [],
                [
                    'purge_everything' => true,
                ]
            );
    
            $success = null !== $response;

        }

        return $success;

    }

    /**
     * Get the zone ID for the domain.
     * 
     * @return string
     */
    protected function get_zone_id() {

        $zone_id = null;

        $api_credentials = $this->get_api_credentials();

        $response = $this->api_call( 
            'GET', 
            'https://api.cloudflare.com/client/v4/zones',
            [
                'name'     => $api_credentials['domain'],
                'status'   => 'active',
                'page'     => 1,
                'per_page' => 1,
                'order'    => 'status',
                'direction'=> 'desc',
                'match'    => 'all',
            ]
        );

        if ( $response ) {
            $zone_id = $response['result'][0]['id'];
        }

        return $zone_id;

    }

    /**
     * Make an API call to Cloudflare.
     * 
     * @param string $method The HTTP method to use.
     * @param string $url The URL to make the request to.
     * @param array $query The query parameters to add to the URL.
     * @param array $data The data to send in the request body.
     * @return array|null
     */
    protected function api_call( string $method, string $url, array $query, array $data = [] ) {

        $api_credentials = $this->get_api_credentials();

        $url = add_query_arg( $query, $url );

        $request = [
            'method'      => $method,
            'headers'     => [
                'X-Auth-Email' => $api_credentials['email'],
                'X-Auth-Key'   => $api_credentials['key'],
                'Content-Type' => 'application/json',
            ],
        ];

        if ( ! empty( $data ) ) {
            $request['body'] = json_encode( $data );
            $request['data_format'] = 'body';
        }

        $response_raw = wp_remote_request( $url, $request );

        $response = null;
        if ( ! is_wp_error( $response_raw ) && 200 === $response_raw['response']['code'] ) {
            $response = json_decode( $response_raw['body'], true );
        }

        return $response;

    }

    /**
     * Get the API credentials from the plugin settings.
     * 
     * @return array
     */
    protected function get_api_credentials() {

        $api_credentials = [
            'email'  => get_option( 'just_cloudflare_cache_managment_email' ),
            'key'    => get_option( 'just_cloudflare_cache_managment_api_key' ),
            'domain' => get_option( 'just_cloudflare_cache_managment_domain' ),
        ];

        return $api_credentials;

    }

}
