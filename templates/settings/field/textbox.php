<?php

$model = new \JustCloudflareCacheManagement\Model\SettingsModel();

$option_name = $model->get_option_name( $args['setting'] );

$option_value = $model->get_value( $args['setting'] );

?>

<p>
    <div>
        <input 
            id="<?php echo esc_attr( $option_name ); ?>"
            name="<?php echo esc_attr( $option_name ); ?>"
            text="textbox"
            value="<?php echo esc_attr( $option_value ); ?>">
    </div>
    <div>
        <label for="<?php echo esc_attr( $option_name ); ?>">
            <?php echo $args['desc']; ?>
        </label>
    </div>
</p>
