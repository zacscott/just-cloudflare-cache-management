<?php

// Render the settings template.
settings_errors( 'just_cloudflare_cache_management_messages' );
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields( 'just_cloudflare_cache_management' );
        do_settings_sections( 'just_cloudflare_cache_management' );
        submit_button( 'Save' );
        ?>
    </form>
</div>
