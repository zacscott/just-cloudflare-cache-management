<?php

namespace JustCloudflareCacheManagement\Model;

/** 
 * Handles all interactions with the plugin settings in the database.
 * 
 * @package JustCloudflareCacheManagement\Library
 */
class SettingsModel {

    /**
     * Get the value of a setting.
     * 
     * @param string $setting The name of the setting.
     * @param string $default The default value to return if the setting/option
     *                        is not set.
     * 
     * @return mixed
     */
    public function get_value( $setting, $default = '' ) {

        $option_name =$this->get_option_name( $setting );

        return get_option( $option_name, $default );

    }

    /**
     * Update the value of a setting.
     * 
     * @param string $setting The name of the setting.
     * @param mixed  $value   The value to set.
     * 
     * @return bool
     */
    public function set_value( $setting, $value ) {

        $option_name = $this->get_option_name( $setting );

        return update_option( $option_name, $value );

    }

    /**
     * Get the name of the option in the database for the given setting.
     * 
     * @param string $option The name of the option.
     * 
     * @return string
     */
    public function get_option_name( $option ) {

        $option_name = 'just_cloudflare_cache_managment_' . $option;

        return $option_name;

    }

}
