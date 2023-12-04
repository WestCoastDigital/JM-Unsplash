<?php

add_action( 'wp_ajax_jm_delete_media', 'jm_delete_media' );
add_action( 'wp_ajax_nopriv_jm_delete_media', 'jm_delete_media' );

function jm_delete_media() {
    $imgOptions = get_option('jm_unsplash_images', []);
    if(!empty($imgOptions)) {
        foreach($imgOptions as $img) {
            wp_delete_attachment($img, true);
        }
    }
    delete_option('jm_unsplash_images');
}