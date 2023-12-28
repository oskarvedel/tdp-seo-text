<?php

function generate_nearby_locations_lists()
{
  $geolocations = get_posts(array('post_type' => 'geolocations', 'posts_per_page' => -1));
  foreach ($geolocations as $geolocation) {
    $geolocation_id = $geolocation->ID;
    // if ($geolocation_id == 6284) {
    //   xdebug_break();
    // }
    $neighbourhoods = get_post_meta($geolocation_id, 'geodir_neighbourhoods', false);

    if ($geolocation_id == 6297) {
      error_log("geolocation_id: $geolocation_id");
      error_log("neighbourhoods: " . var_export($neighbourhoods, true));
    }

    $first_10_geolocations_within_8_km_with_seo_gd_place_list_sorted_by_distance = get_post_meta($geolocation_id, 'first_10_geolocations_within_8_km_with_seo_gd_place_list_sorted_by_distance', false);


    if ($geolocation_id == 6297) {
      error_log("geolocation_id: $geolocation_id");
      error_log("first_10_geolocations_within_8_km_with_seo_gd_place_list_sorted_by_distance: " . var_export($first_10_geolocations_within_8_km_with_seo_gd_place_list_sorted_by_distance, true));
    }

    $combined = array_merge($neighbourhoods, $first_10_geolocations_within_8_km_with_seo_gd_place_list_sorted_by_distance);

    if ($geolocation_id == 6297) {
      error_log("geolocation_id: $geolocation_id");
      error_log("combined: " . var_export($combined, true));
    }

    $combined = array_map('intval', $combined);
    $combined = array_unique($combined);
    $parent_location = get_post_meta($geolocation_id, 'parent_location', false);

    if ($geolocation_id == 6297) {
      error_log("geolocation_id: $geolocation_id");
      error_log("parent_location: " . var_export($parent_location, true));
    }

    if (!empty($parent_location)) {
      $parent_location = array_map('intval', $parent_location);
      $combined = array_diff($combined, $parent_location);
    }
    $combined = array_slice($combined, 0, 10);


    if ($geolocation_id == 6297) {
      error_log("geolocation_id: $geolocation_id");
      error_log("combined: " . var_export($combined, true));
    }
    if (empty($combined)) {
      error_log("geolocation_id: $geolocation_id");
      error_log("combined is empty");
      update_post_meta($geolocation_id, 'nearby_locations_list', "");
      continue;
    }
    $output = '<nav><div class="horizontal-list">';

    foreach ($combined as $geolocation => $nearby_geolocation_id) {
      $title = get_the_title($nearby_geolocation_id);
      $gd_location_slug = get_post_meta($nearby_geolocation_id, 'gd_location_slug', true);
      $slug = get_post_field('post_name', $nearby_geolocation_id); //use when migrating from geodir
      if (empty($gd_location_slug)) {
        $gd_location_slug = $slug;
      }
      $link = "https://www.tjekdepot.dk/lokation/$gd_location_slug/";
      $output .= '<a href="' . $link . '">' . $title . '</a>';
    }

    $output .= '</div></nav>';

    update_post_meta($geolocation_id, 'nearby_locations_list', $output);

    $test = get_post_meta($geolocation_id, 'nearby_locations_list', true);
    if ($geolocation_id == 6297) {
      error_log("nearby_locations_list for geolocation $geolocation_id: $test");
    }
  }
  trigger_error("nearby location lists updated", E_USER_NOTICE);
}
