<?php

if ( ! defined('WP_UNINSTALL_PLUGIN' ) ) {
    die;
}
 
unregister_setting( 'speed-matters', 'speed_matters_update_jquery' );
unregister_setting( 'speed-matters', 'speed_matters_remove_jquery_migrate' );
unregister_setting( 'speed-matters', 'speed_matters_remove_wpembed' );
unregister_setting( 'speed-matters', 'speed_matters_disable_emojis' );
unregister_setting( 'speed-matters', 'speed_matters_disable_heartbeat' );