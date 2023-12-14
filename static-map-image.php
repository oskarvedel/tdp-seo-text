<?php

function generate_missing_static_map_images()
{
    $geolocations = get_posts(array('post_type' => 'geolocations', 'posts_per_page' => -1));
    wp_redirect(admin_url('plugins.php?s=tdp&plugin_status=all'));
    foreach ($geolocations as $geolocation) {
        $geolocation_id = $geolocation->ID;
        $has_map = get_post_meta($geolocation_id, 'static_map', true);
        if ($has_map != "") {
            continue;
        }
        $geolocation_meta = get_post_meta($geolocation_id);
        $geolocation_meta['geolocation'] = unserialize($geolocation_meta['geolocation'][0]);
        $lat = $geolocation_meta['latitude'][0];
        $lng = $geolocation_meta['longitude'][0];
        $zoom = 10;
        xdebug_break();
        $width = 400;
        $height = 300;
        $apiKey = "AIzaSyAXa6QObKCBreGTsTTQdLRvT2UagPsGhpU";
        $url = generate_static_map_image($lat, $lng, $zoom, $width, $height, $apiKey);
        $image = file_get_contents($url);
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'];
        $upload_url = $upload_dir['url'];
        $filename = $geolocation_id . ".png";
        $file = $upload_path . "/" . $filename;
        $file = $upload_path . "/" . $filename;
        if (file_exists($file)) {
            unlink($file);
            trigger_error("deleted old static map image for geolocation " . $geolocation_id . " at " . $file_url, E_USER_NOTICE);
        }
        file_put_contents($file, $image);
        $file_url = $upload_url . "/" . $filename;
        update_post_meta($geolocation_id, 'static_map', $file_url);
        trigger_error("Generated static map for geolocation " . $geolocation_id . " at " . $file_url, E_USER_NOTICE);
    }
}

function generate_static_map_image($lat, $lng, $zoom, $width = 400, $height = 300, $apiKey)
{
    $url = "https://maps.googleapis.com/maps/api/staticmap?";
    $url .= "center=$lat,$lng";
    $url .= "&zoom=$zoom";
    $url .= "&size=" . $width . "x" . $height;
    $url .= "&key=$apiKey";

    return $url;
}
