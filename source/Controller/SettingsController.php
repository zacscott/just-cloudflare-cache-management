<?php

namespace JustCloudflareCacheManagement\Controller;

/**
 * Responsible for handling the settings page for the plugin.
 * 
 * @package JustCloudflareCacheManagement\Controller
 */
class SettingsController {

    /**
     * The plugin settings options, set by set_settings_config().  
     * @var array $settings
     */
    protected $settings = [];

    public function __construct() {
        
        add_action( 'admin_init', [ $this, 'set_settings_config' ], -1 );
        add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings_section' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );

    }

    /**
     * Set the configuration used to generate the options presented on the settings page.
     * 
     * @return void
     */
    public function set_settings_config() {

        $this->settings = [
            [
                'setting' => 'email',
                'type'    => 'textbox',
                'label'   => __( 'Email', 'just-cloudflare-cache-management' ),
                'desc'    => __( 'Cloudflare login email address.', 'just-cloudflare-cache-management' ),
            ],
            [
                'setting' => 'api_key',
                'type'    => 'textbox',
                'label'   => __( 'Global API Key', 'just-cloudflare-cache-management' ),
                'desc'    => __( 'Get this from your <a href="https://dash.cloudflare.com/profile/api-tokens">Cloudflare profile</a>.', 'just-cloudflare-cache-management' ),
            ],
        ];

    }

    /**
     * Register the settings page in wp-admin.
     * 
     * @return void
     */
    public function register_settings_page() {

        add_submenu_page(
            'options-general.php',
            __( 'Just Cloudflare Cache', 'just-cloudflare-cache-management' ),
            __( 'Just Cloudflare Cache', 'just-cloudflare-cache-management' ),
            'manage_options',
            'justjust_cloudflare_cache_management',
            [ $this, 'render_settings_page' ]
        );

    }
    
    /**
     * Render the settings page.
     * 
     * @return void
     */
    public function render_settings_page() {

        // Ensure current user has access.
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Show a notice when settings are saved.
        if ( isset( $_POST['update'] ) ) {

            add_settings_error(
                'just_cloudflare_cache_management_messages',
                'just_cloudflare_cache_management_message',
                __( 'Settings Saved', 'just-cloudflare-cache-management' ),
                'updated'
            );

        }

        // Render the settings page.

        $template_path = sprintf(
            '%s/templates/settings/page.php',
            JUST_CLOUDFLARE_CACHE_MANAGEMENT_PLUGIN_ABSPATH
        );

        include $template_path;

    }

    /**
     * Register the settings section within the setting page.
     * 
     * @return void
     */
    public function register_settings_section() {
        
        add_settings_section(
            'just_cloudflare_cache_management_section',
            '',
            [ $this, 'render_settings_section' ],
            'just_cloudflare_cache_management'
        );

    }

    /**
     * Render the settings section within the setting page.
     * 
     * @return void
     */
    public function render_settings_section( $args ) {

        $template_path = sprintf(
            '%s/templates/settings/section.php',
            JUST_CLOUDFLARE_CACHE_MANAGEMENT_PLUGIN_ABSPATH
        );

        include $template_path;

    }

    /**
     * Register each of the settings/options within the settings section.
     * 
     * @return void
     */
    public function register_settings() {

        $model = new \JustCloudflareCacheManagement\Model\SettingsModel();

        foreach ( $this->settings as $setting ) {

            $option_name = $model->get_option_name( $setting['setting'] );

            register_setting( 'just_cloudflare_cache_management', $option_name );

            add_settings_field(
                $option_name, 
                $setting['label'],
                [ $this, 'render_settings_field' ],
                'just_cloudflare_cache_management',
                'just_cloudflare_cache_management_section',
                $setting
            );

        }
        
    }

    /**
     * Render the given setting/option within the settings section.
     * 
     * @return void
     */
    public function render_settings_field( $args ) {
        
        $template_path = sprintf(
            '%s/templates/settings/field/%s.php',
            JUST_CLOUDFLARE_CACHE_MANAGEMENT_PLUGIN_ABSPATH,
            $args['type']
        );

        include $template_path;

    }

}
