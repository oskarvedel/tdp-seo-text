<?php

function generate_nearby_locations_lists()
{
  $geolocations = get_posts(array('post_type' => 'geolocations', 'posts_per_page' => -1));
  foreach ($geolocations as $geolocation) {
    $geolocation_id = $geolocation->ID;

    $neighbourhoods = get_post_meta($geolocation_id, 'geodir_neighbourhoods', false);

    if (empty($neighbourhoods[0])) {
      $neighbourhoods = array();
    }
    $first_10_geolocations_within_8_km_with_seo_gd_place_list_sorted_by_distance = get_post_meta($geolocation_id, 'first_10_geolocations_within_8_km_with_seo_gd_place_list_sorted_by_distance', false);

    $all_nearby_locations = array_merge($neighbourhoods, $first_10_geolocations_within_8_km_with_seo_gd_place_list_sorted_by_distance);

    $all_nearby_locations_lightened = array();
    foreach ($all_nearby_locations as $heavy_nearby_location) {
      $light_nearby_location = array($heavy_nearby_location['ID'], $heavy_nearby_location['post_title'], $heavy_nearby_location['post_name']); // replace 0, 1, 2 with the indexes of the values you want
      $all_nearby_locations_lightened[] = $light_nearby_location;
    }

    //remove duplicates
    $all_nearby_locations_lightened = array_map('unserialize', array_unique(array_map('serialize', $all_nearby_locations_lightened)));

    $parent_location = get_post_meta($geolocation_id, 'parent_location', true);
    if (!empty($parent_location)) {
      $parent_location_id = $parent_location['ID'];
      foreach ($all_nearby_locations_lightened as $key => $nearby_location) {
        if ($nearby_location[0] == $parent_location_id) {
          unset($all_nearby_locations_lightened[$key]);
        }
      }
      // $all_nearby_locations_lightened = array_diff($all_nearby_locations_lightened, $parent_location);

    }
    $all_nearby_locations_lightened = array_slice($all_nearby_locations_lightened, 0, 10);

    if (empty($all_nearby_locations_lightened)) {
      update_post_meta($geolocation_id, 'nearby_locations_list', "");
      continue;
    }
    $output = '<nav><div class="horizontal-list">';


    foreach ($all_nearby_locations_lightened as $geolocation) {
      $post_title = $geolocation[1];
      $slug = $geolocation[2];
      $gd_location_slug = get_post_meta($geolocation[0], 'gd_location_slug', true); //use when migrating from geodir
      if (empty($gd_location_slug)) {
        $gd_location_slug = $slug;
      }
      $link = "https://www.tjekdepot.dk/lokation/$gd_location_slug/";
      $output .= '<a href="' . $link . '">' . $post_title . '</a>';
    }

    $output .= '</div></nav>';

    $current_nearby_locations_list = get_post_meta($geolocation_id, 'nearby_locations_list', true);

    if ($current_nearby_locations_list == $output) {
      continue;
    } else {
      trigger_error("updated nearby location lists for " . $geolocation->post_title, E_USER_NOTICE);
      update_post_meta($geolocation_id, 'nearby_locations_list', $output);
    }
  }
  trigger_error("nearby location lists updated", E_USER_NOTICE);
}
