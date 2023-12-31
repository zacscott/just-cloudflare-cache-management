<?php

namespace JustCloudflareCacheManagement\Library;

class CloudflareAPI {

    /**
     * Clear the cache for specific URLs.
     * 
     * @param array $url_prefixes The URL prefixes to clear the cache for.
     */
    public function flush_cache_for_urls( array $url_prefixes ) {

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
    public function flush_cache() {

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
        if ( $api_credentials ) {

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

                if ( isset( $response['result'] ) ) {
                    $result = $response['result'];

                    if ( ! empty( $result ) ) {
                        $first_result = $result[0];

                        if ( isset( $first_result['id'] ) ) {
                            $zone_id = $first_result['id'];

                        }

                    }

                }
                
            }

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

        $response = null;

        $api_credentials = $this->get_api_credentials();
        if ( $api_credentials ) {

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

            if ( ! is_wp_error( $response_raw ) && 200 === $response_raw['response']['code'] ) {
                $response = json_decode( $response_raw['body'], true );
            }

        }

        return $response;

    }

    /**
     * Whether the plugin API credentials are configured or not.
     * 
     * @return bool
     */
    public function is_configured() {

        $api_credentials = $this->get_api_credentials();

        $is_configured = ! empty( $api_credentials );

        return $is_configured;

    }

    /**
     * Get the API credentials from the plugin settings.
     * 
     * @return array
     */
    protected function get_api_credentials() {

        $api_credentials = null;

        $model = new \JustCloudflareCacheManagement\Model\SettingsModel();

        $url_parts = wp_parse_url( home_url() );
        $domain    = $url_parts['host'];

        $email = $model->get_value( 'email' );

        $api_key = $model->get_value( 'api_key' );

        if ( $email && $api_key && $domain ) {

            $api_credentials = [
                'email'  => $email,
                'key'    => $api_key,
                'domain' => $domain,
            ];

        }

        return $api_credentials;

    }

}
