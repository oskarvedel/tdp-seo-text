<?php

/**
 * Plugin Name: tdp-seo-text-plugin
 * Version: 1.0
 */

require_once dirname(__FILE__) . '/tdp-common-seo-text.php';

require_once dirname(__FILE__) . '/gd_location-seo-text.php';
require_once dirname(__FILE__) . '/gd_location-metadata-shortcode.php';

require_once dirname(__FILE__) . '/texts/basic-text.php';
require_once dirname(__FILE__) . '/texts/first-paragraph.php';
require_once dirname(__FILE__) . '/texts/meta-title.php';

function wp_meta_title()
{
    if (geodir_is_page('post_type')) {
        $geolocation_id = extract_geolocation_id_via_url_seo_text();
        $meta_title = get_post_meta($geolocation_id, 'meta_title', true);
        if (!empty($meta_title)) {
            return $meta_title;
        }
    }
}
add_filter('pre_get_document_title', 'wp_meta_title', 21);
add_filter('wp_title', 'wp_meta_title', 21);
