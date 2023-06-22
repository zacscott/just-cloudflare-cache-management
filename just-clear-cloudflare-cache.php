<?php
/**
 * Plugin Name: Just Cloudflare Cache Management
 * Version:     1.0
 * Author:      Zac Scott
 * Author URI:  https://zacscott.net
 * Description: Clear the Cloudflare edge cache for posts/pages when they are updated and more.
 * Text Domain: just-cloudflare-cache-management
 */

require dirname( __FILE__ ) . '/vendor/autoload.php';

define( 'JUST_CLOUDFLARE_CACHE_MANAGEMENT_PLUGIN_ABSPATH', dirname( __FILE__ ) );
define( 'JUST_CLOUDFLARE_CACHE_MANAGEMENT_PLUGIN_ABSURL', plugin_dir_url( __FILE__ )  );

// Boot each of the plugin logic controllers.
new \JustCloudflareCacheManagement\Controller\ClearPostCacheController();
