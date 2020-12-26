<?php

add_action( 'plugins_loaded', function() {
    do_action('register_andyp_plugin', [
        'title'     => 'Auto Menu Items',
        'icon'      => 'microsoft-xbox-controller-menu',
        'color'     => '#66BB6A',
        'path'      => __FILE__,
    ]);
} );