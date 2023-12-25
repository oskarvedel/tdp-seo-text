<?php

function generate_nearby_locations_lists()
{
  $geolocations = get_posts(array('post_type' => 'geolocations', 'posts_per_page' => -1));

  foreach ($geolocations as $geolocation) {
    $geolocation_id = $geolocation->ID;
    // if ($geolocation_id == 6345) {
    //   xdebug_break();
    // }
    $geolocations_within_8_km_with_gd_places_within_8_km_sorted_by_distance = get_post_meta($geolocation_id, 'geolocations_within_8_km_with_gd_places_within_8_km_sorted_by_distance', true);
    $output = '<nav><div class="horizontal-list">';
    $counter  = 0;
    foreach ($geolocations_within_8_km_with_gd_places_within_8_km_sorted_by_distance as $geolocation => $distance) {
      $counter++;
      $title = get_the_title($geolocation);
      $gd_location_slug = get_post_meta($geolocation, 'gd_location_slug', true);
      $parent_location = get_post_meta($geolocation, 'parent_location', true);
      if ($parent_location != "") {
        $parent_location_slug = get_post_meta($parent_location, 'gd_location_slug', true);
        $link = "https://www.tjekdepot.dk/lokation/$parent_location_slug/$gd_location_slug/";
      } else {
        $link = "https://www.tjekdepot.dk/lokation/$gd_location_slug/";
      }
      $output .= '<a href="' . $link . '">' . $title . '</a>';
      if ($counter == 8) {
        break;
      }
    }

    $output .= '</div></nav>';

    update_post_meta($geolocation_id, 'nearby_locations_list', $output);

    $test = get_post_meta($geolocation_id, 'nearby_locations_list', true);
  }
  trigger_error("nearby location lists updated", E_USER_NOTICE);
}
