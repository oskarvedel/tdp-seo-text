<?php

/**
 * Plugin Name: tdp-seo-text
 * Version: 1.1
 */

require_once dirname(__FILE__) . '/tdp-common-seo-text.php';

require_once dirname(__FILE__) . '/gd_location-seo-text.php';
require_once dirname(__FILE__) . '/gd_location-metadata-shortcode.php';

require_once dirname(__FILE__) . '/texts/basic-text.php';
require_once dirname(__FILE__) . '/texts/first-paragraph.php';
require_once dirname(__FILE__) . '/texts/meta-title.php';
require_once dirname(__FILE__) . '/static-map-image.php';

function wp_meta_title()
{
    if (geodir_is_page('post_type')) {
        $geolocation_id = extract_geolocation_id_via_url_seo_text();
        $meta_title = get_post_meta($geolocation_id, 'meta_title', true);
        if (!empty($meta_title)) {
            return $meta_title;
        } else {
            return "Find opbevaring og depotrum";
        }
    }
}
add_filter('pre_get_document_title', 'wp_meta_title', 21);
add_filter('wp_title', 'wp_meta_title', 21);

//add a button that runs the generate static map image function on the plugin settings page
function add_generate_maps_button($links)
{
    $consolidate_link = '<a href="' . esc_url(admin_url('admin-post.php?action=generate_maps')) . '">Generate missing static maps</a>';
    array_unshift($links, $consolidate_link);
    return $links;
}
add_filter('plugin_action_links_tdp-seo-text/tdp-seo-text-plugin.php', 'add_generate_maps_button');

function handle_generate_static_maps()
{
    generate_missing_static_map_images();
    wp_redirect(admin_url('plugins.php?s=tdp&plugin_status=all'));
    exit;
}
add_action('admin_post_generate_maps', 'handle_generate_static_maps');

//add a button that runs the generate static map image function on the plugin settings page
function add_generate_seo_texts_button($links)
{
    $consolidate_link = '<a href="' . esc_url(admin_url('admin-post.php?action=generate_seo_texts')) . '">Generate SEO texts</a>';
    array_unshift($links, $consolidate_link);
    return $links;
}
add_filter('plugin_action_links_tdp-seo-text/tdp-seo-text-plugin.php', 'add_generate_seo_texts_button');

function handle_generate_seo_texts()
{
    generate_seo_texts();
    wp_redirect(admin_url('plugins.php?s=tdp&plugin_status=all'));
    exit;
}
add_action('admin_post_generate_seo_texts', 'handle_generate_seo_texts');


function gd_location_seo_text_func()
{
    $id = extract_geolocation_id_via_url_seo_text();
    $seo_text = get_post_meta($id, 'seo_text', true);
    echo $seo_text;
}

add_shortcode('gd_location_seo_text_shortcode', 'gd_location_seo_text_func');


add_action('elementor/query/gd_places_for_geolocation', function ($query) {
    $geolocation_id = extract_geolocation_id_via_url_seo_text();
    $gd_place_list_combined = get_post_meta($geolocation_id, 'gd_place_list_combined', true);

    //remove all posts with "hide" set to 1 from gd_place_list_combined
    $gd_place_list_combined = array_filter($gd_place_list_combined, function ($post_id) {
        return get_post_meta($post_id, 'hide', true) != 1;
    });

    // Set the post type to 'gd_place'
    $query->set('post_type', 'gd_place');

    // Only include posts that are in $gd_place_list_combined
    $query->set('post__in', $gd_place_list_combined);

    // Order by the 'partner' meta key in descending order
    $query->set('meta_key', 'partner');
    $query->set('orderby', 'meta_value_num');
    $query->set('order', 'DESC');
});
