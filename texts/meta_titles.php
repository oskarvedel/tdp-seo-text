<?php

function get_first_paragraph_seo_text($location_slug)
{
    global $first_paragraph;
    $first_paragraph = str_replace('[location]', $location_slug, $first_paragraph);
    return $first_paragraph;
}
$no_gd_places = array(
    'Hvad koster et depotrum i [location]?',
);

$candidates = array(
    '<h2>Hvordan ligger priserne for et depotrum i [location]?</h2>
<p class="three-columns"></p>',

    '<h2>Priser på depotrum i [location]-området</h2>
<p class="three-columns"></p>'
);
