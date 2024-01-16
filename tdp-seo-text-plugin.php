<?php

/**
 * Plugin Name: tdp-seo-text
 * Version: 1.1
 */

require_once dirname(__FILE__) . '/tdp-common-seo-text.php';

require_once dirname(__FILE__) . '/generate/generate-seo-texts.php';
require_once dirname(__FILE__) . '/generate/generate-top-seo-texts.php';
require_once dirname(__FILE__) . '/generate/generate-missing-static-map-images.php';
require_once dirname(__FILE__) . '/generate/generate-nearby-locations-lists.php';
require_once dirname(__FILE__) . '/generate/generate-meta-titles.php';
require_once dirname(__FILE__) . '/generate/generate-meta-descriptions.php';

require_once dirname(__FILE__) . '/gd_location-metadata-shortcode.php';

require_once dirname(__FILE__) . '/texts/basic-text.php';
require_once dirname(__FILE__) . '/texts/first-paragraph.php';
require_once dirname(__FILE__) . '/texts/second-paragraph.php';
require_once dirname(__FILE__) . '/texts/meta-title.php';
require_once dirname(__FILE__) . '/texts/meta-description.php';
require_once dirname(__FILE__) . '/texts/top-seo-text.php';

function set_meta_title()
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
add_filter('pre_get_document_title', 'set_meta_title', 21);

function set_meta_description()
{
    if (geodir_is_page('post_type')) {
        $geolocation_id = extract_geolocation_id_via_url_seo_text();
        $meta_description = get_post_meta($geolocation_id, 'meta_description', true);
        if (!empty($meta_description)) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">';
        } else {
            echo ''; //let rankmath handle it
        }
    }
}
add_action('wp_head', 'set_meta_description');

function top_seo_text_func()
{
    $id = extract_geolocation_id_via_url_seo_text();
    $top_seo_text = get_post_meta($id, 'top_seo_text', true);
    echo $top_seo_text;
}

add_shortcode('gd_location_top_seo_text_shortcode', 'top_seo_text_func');

function seo_text_func()
{
    $id = extract_geolocation_id_via_url_seo_text();
    $seo_text = get_post_meta($id, 'seo_text', true);
    echo $seo_text;
}

add_shortcode('gd_location_seo_text_shortcode', 'seo_text_func');

function nearby_locations_list_func()
{
    $id = extract_geolocation_id_via_url_seo_text();
    $nearby_locations_list = get_post_meta($id, 'nearby_locations_list', true);
    echo $nearby_locations_list;
}

add_shortcode('gd_location_nearby_locations_list_shortcode', 'nearby_locations_list_func');

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

function add_generate_top_seo_texts_button($links)
{
    $consolidate_link = '<a href="' . esc_url(admin_url('admin-post.php?action=generate_top_seo_texts')) . '">Generate top SEO texts</a>';
    array_unshift($links, $consolidate_link);
    return $links;
}
add_filter('plugin_action_links_tdp-seo-text/tdp-seo-text-plugin.php', 'add_generate_top_seo_texts_button');

function handle_generate_top_seo_texts()
{
    generate_top_seo_texts();
    wp_redirect(admin_url('plugins.php?s=tdp&plugin_status=all'));
    exit;
}
add_action('admin_post_generate_top_seo_texts', 'handle_generate_top_seo_texts');

add_action('admin_post_generate_meta_titles', 'handle_generate_meta_titles');

function add_generate_nearby_locations_lists_button($links)
{
    $consolidate_link = '<a href="' . esc_url(admin_url('admin-post.php?action=generate_nearby_locations_lists')) . '">Generate nearby locations lists</a>';
    array_unshift($links, $consolidate_link);
    return $links;
}

add_filter('plugin_action_links_tdp-seo-text/tdp-seo-text-plugin.php', 'add_generate_nearby_locations_lists_button');

function handle_generate_nearby_locations_lists()
{
    generate_nearby_locations_lists();
    wp_redirect(admin_url('plugins.php?s=tdp&plugin_status=all'));
    exit;
}
add_action('admin_post_generate_nearby_locations_lists', 'handle_generate_nearby_locations_lists');

function add_generate_meta_titles_button($links)
{
    $consolidate_link = '<a href="' . esc_url(admin_url('admin-post.php?action=generate_meta_titles')) . '">Generate meta titles</a>';
    array_unshift($links, $consolidate_link);
    return $links;
}

add_filter('plugin_action_links_tdp-seo-text/tdp-seo-text-plugin.php', 'add_generate_meta_titles_button');

function handle_generate_meta_titles()
{
    generate_meta_titles();
    wp_redirect(admin_url('plugins.php?s=tdp&plugin_status=all'));
    exit;
}

function add_generate_meta_descriptions_button($links)
{
    $consolidate_link = '<a href="' . esc_url(admin_url('admin-post.php?action=generate_meta_descriptions')) . '">Generate meta descriptions</a>';
    array_unshift($links, $consolidate_link);
    return $links;
}

add_filter('plugin_action_links_tdp-seo-text/tdp-seo-text-plugin.php', 'add_generate_meta_descriptions_button');

function handle_generate_meta_descriptions()
{
    generate_meta_descriptions();
    wp_redirect(admin_url('plugins.php?s=tdp&plugin_status=all'));
    exit;
}
add_action('admin_post_generate_meta_descriptions', 'handle_generate_meta_descriptions');



function modify_archive_query($query)
{
    // Check if we are on the front end and if the main query is being modified
    if (!is_admin() && $query->is_main_query()) {
        // Target a specific archive page, e.g., a custom post type archive
        if ($query->is_post_type_archive('gd_place')) {
            // Extract the geolocation ID from the URL
            $geolocation_id = extract_geolocation_id_via_url_seo_text();
            // Assume this function returns an array of post IDs
            $gd_place_list_combined = get_post_meta($geolocation_id, 'archive_gd_place_list', false);
            if (!empty($gd_place_list_combined)) {
                //get the ids from the gd_place_list_combined array
                $gd_place_list_combined = array_map(function ($post) {
                    return $post['ID'];
                }, $gd_place_list_combined);
            }

            // Set the post__in parameter for the main query
            if (!empty($gd_place_list_combined)) {
                $query->set('post__in', array_values($gd_place_list_combined));
                $query->set('orderby', 'post__in');
            } else {
                $query->set('post__in', array(0));
            }
        }
    }
}

add_action('pre_get_posts', 'modify_archive_query', 1);


// Remove dashicons in frontend for unauthenticated users
add_action('wp_enqueue_scripts', 'bs_dequeue_dashicons');
function bs_dequeue_dashicons()
{
    if (!is_user_logged_in()) {
        wp_deregister_style('dashicons');
    }
}
