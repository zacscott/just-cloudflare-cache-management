<?php

namespace JustCloudflareCacheManagement\Library;

class CloudflareAPI {

    /**
     * Clear the cache for a specific URL.
     * 
     * @param string $url The URL to clear the cache for.
     */
    public function clear_for_url( string $url ) {

        $zone_id = $this->get_zone_id();
        if ( $zone_id ) {

            // TODO clear cache API call.

        }

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

        //Purge the entire cache via API
        // $ch_purge = curl_init();
        // curl_setopt($ch_purge, CURLOPT_URL, "");
        // curl_setopt($ch_purge, CURLOPT_CUSTOMREQUEST, "DELETE");
        // curl_setopt($ch_purge, CURLOPT_RETURNTRANSFER, 1);
        // $headers = [
        //     'X-Auth-Email: '.$cust_email,
        //     'X-Auth-Key: '.$cust_xauth,
        //     'Content-Type: application/json'
        // ];
        // $data = json_encode(array());
        // curl_setopt($ch_purge, CURLOPT_POST, true);
        // curl_setopt($ch_purge, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch_purge, CURLOPT_HTTPHEADER, $headers);

        // $result = json_decode(curl_exec($ch_purge),true);
        // curl_close($ch_purge);

        return false;

    }

    /**
     * Get the zone ID for the domain.
     * 
     * @return string
     */
    protected function get_zone_id() {
        // TODO cache zone id

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
