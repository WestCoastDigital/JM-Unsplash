<?php
add_action( 'wp_ajax_jm_add_media', 'jm_add_media' );
add_action( 'wp_ajax_nopriv_jm_add_media', 'jm_add_media' );

function jm_add_media() {
    $imageDataString = $_POST['imageData'] ?? '';
    set_time_limit(60); // Set the time limit to 60 seconds

    // Remove escaped slashes
    $unescapedDataString = stripslashes($imageDataString);

    // Decode the unescaped JSON string to an array
    $imageData = json_decode($unescapedDataString, true);

    if (empty($imageData) || !is_array($imageData)) {
        wp_send_json_error('Invalid or empty image data received');
    } else {
        $uploadedImages = [];
        $newImageIds = [];

        foreach ($imageData as $image) {
            if (isset($image['url'], $image['alt'])) {
                $imageId = media_sideload_image($image['url'], 0, $image['alt'], 'id');

                if (!is_wp_error($imageId)) {
                    $uploadedImages[] = $imageId;
                } else {
                    // Log the error returned by media_sideload_image()
                    $error_message = $imageId->get_error_message();
                    error_log('Error uploading image: ' . $error_message);

                    // Include the error message in the response
                    $uploadedImages[] = [
                        'error' => 'Failed to upload image: ' . $image['alt'] . '. Error: ' . $error_message
                    ];
                }
            } else {
                // Handle missing image data
                $uploadedImages[] = [
                    'error' => 'Missing image data for upload'
                ];
            }
        }

        jm_update_options($uploadedImages);

        // Return the uploaded images or errors
        wp_send_json_success($uploadedImages);
    }

    // Return a response if needed
    wp_die();
}

function jm_update_options($ids) {

        // Get the existing image IDs from the option
        $existingImageIds = get_option('jm_unsplash_images', []);

        // Merge the existing IDs with the newly uploaded IDs (avoiding duplicates)
        $updatedImageIds = array_unique(array_merge($existingImageIds, $ids));

        // Update the option with the combined set of image IDs
        update_option('jm_unsplash_images', $updatedImageIds);
}