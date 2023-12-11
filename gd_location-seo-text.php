<?php

function gd_location_seo_text_func($atts)
{
     //get data
     $geolocation_id = extract_geolocation_id_via_url_seo_text();

     $num_of_gd_places = intval(get_post_meta($geolocation_id, 'num of gd_places', true));

     $archive_title_trimmed = substr(get_the_archive_title(), 2);

     $gd_place_names = get_post_meta($geolocation_id, 'gd_place_names', true);
     $schools = get_post_meta($geolocation_id, 'schools', true);
     $sublocations = get_post_meta($geolocation_id, 'sublocations', false);

     //set variables
     global $statistics_data_fields;
     global $meta_title_candidates;
     global $first_paragraph_candidates;
     global $second_paragraph;
     global $price_table;
     global $third_paragraph;
     $output = "";
     global $statistics_data_fields_texts;

     set_meta_title($geolocation_id, $num_of_gd_places, $archive_title_trimmed, $statistics_data_fields, $meta_title_candidates);

     if ($num_of_gd_places <= 2) {
          global $basic_text;
          $output = $basic_text;
          $output = str_replace("[location]", $archive_title_trimmed, $output);
          echo $output;
          return;
     }

     //add content to output
     $output .= get_seo_paragraph($archive_title_trimmed, $first_paragraph_candidates);
     $output .= '<hr class="line">';
     $output .= generate_price_table();
     $output .= '<hr class="line">';
     $output .= $second_paragraph;
     $output .= '<hr class="line">';
     $output .= $third_paragraph;
     $output .= '<hr class="line">';
     $output .= generate_schools_paragraph($schools);
     $output .= generate_neighbourhoods_paragraph($sublocations);
     $output .= generate_selfstorage_provider_list($gd_place_names);

     //relace variable placeholders with data
     $output = replace_variable_placeholders($output, $statistics_data_fields, $geolocation_id, $num_of_gd_places, $archive_title_trimmed);

     echo $output;
}

add_shortcode('gd_location_seo_text_shortcode', 'gd_location_seo_text_func');

function set_meta_title($geolocation_id, $num_of_gd_places, $archive_title_trimmed, $statistics_data_fields, $meta_title_candidates)
{
     $lowest_price = get_post_meta($geolocation_id, 'lowest price', true);
     $lowest_price_floatval = floatval($lowest_price);
     if ($num_of_gd_places == 0) {
          $meta_title = "Opbevaring " . $archive_title_trimmed . " – Find depotrum nær " . $archive_title_trimmed;
     } else if ($lowest_price_floatval == 0) {
          $meta_title = $meta_title_candidates[1];
     } else {
          $meta_title = $meta_title_candidates[0];
     }

     $meta_title = replace_variable_placeholders($meta_title, $statistics_data_fields, $geolocation_id, $num_of_gd_places, $archive_title_trimmed);
     update_post_meta($geolocation_id, 'meta_title', $meta_title);
     trigger_error("meta title set: " . $meta_title, E_USER_NOTICE);
}

function replace_variable_placeholders($input_text, $statistics_data_fields, $geolocation_id, $num_of_gd_places, $archive_title_trimmed)
{
     $input_text = str_replace("[num of gd_places]", $num_of_gd_places, $input_text);

     $input_text = str_replace("[location]", $archive_title_trimmed, $input_text);

     $input_text = replace_statistics_data_fields_with_values($input_text, $statistics_data_fields, $geolocation_id);

     return $input_text;
}

function replace_statistics_data_fields_with_values($input_text, $statistics_data_fields, $geolocation_id)
{
     foreach ($statistics_data_fields as $field) {
          $value = get_post_meta($geolocation_id, $field, true);
          if (!empty($value)) {
               $rounded = floatval(round($value, 2));
               $numberformat = number_format($value, 0, ',', '.');
               $input_text = str_replace("[$field]", $numberformat, $input_text);
          } else {
               $input_text = str_replace("[$field]", "Ukendt", $input_text);
          }
     }
     return $input_text;
}

function generate_selfstorage_provider_list($gd_place_names)
{
     if (!empty($gd_place_names)) {
          $return_text = '<h4>Der er i alt [num of gd_places] udbydere af depotrum i [location]:</h4>';
          $return_text .= '<p class="three-columns"><small>';
          foreach ($gd_place_names as $place_name) {
               $return_text .=  $place_name  . '<br>';
          }
          $return_text .= '</small></ul>';
          return $return_text;
     }
}

$second_paragraph = '<h2>Find opbevaring i [location]</h2>
     <p class="three-columns">Hvis du leder efter opbevaring i [location], er du kommet til det rette sted. Her på tjekdepot har vi registreret [num of gd_places] udbydere af opbevaring placeret i [location], og de tilbyder alle sikre og tilgængelige depotrum. Du kan sortere alle depotrum i dit område efter pris, størrelse og egenskaber. Her kan du også finde mere information om de forskellige egenskaber ved et depotrum såsom klimakontrol, adgangsforhold og sikkerhedsforanstaltninger. Se vores opslag i [location], vælg et depotrum, der passer til dine behov, og lej den med det samme. Når du reserverer opbevaring, har du et rigtig godt sted at placere dine ting, både på lang og kort sigt. Langt de fleste opbevaringsrum udlejes på månedsbasis, hvilket betyder, at du har mulighed for at flytte ind og ud når som helst. Du kan leje opbevaring i [location] i en måned eller et helt år - uanset hvad, kan du forlænge din lejeperiode og skifte til et depotrum på en anden størrelse, når du ønsker det. På denne måde er det nemt at vælge den bedste opbevaring til dine ejendele!</p>';

function generate_price_table()
{
     $price_table = '
               <table>
               <thead>
               <tr>
               <th class="left-align"><strong>Størrelse</strong></th>
               <th class="right-align"><strong>Laveste pris</strong></th>
               <th class="right-align"><strong>Gennemsnitpris</strong></th>
               <th class="right-align"><strong>Højeste pris</strong></th>
               </tr>
               </thead>
               <tbody>
               <tr>
               <td>Mini (fra 0 til 2 m²)</td>
               <td class="right-align">[mini size lowest price]</td>
               <td class="right-align">[mini size average price]</td>
               <td class="right-align">[mini size highest price]</td>
               </tr>
               <tr>
               <td>Lille (2 til 7 m²)</td>
               <td class="right-align">[small size lowest price]</td>
               <td class="right-align">[small size average price]</td>
               <td class="right-align">[small size highest price]</td>
               </tr>
               <tr>
               <td>Mellem (7 til 18 m²)</td>
               <td class="right-align">[medium size lowest price]</td>
               <td class="right-align">[medium size average price]</td>
               <td class="right-align">[medium size highest price]</td>
               </tr>
               <tr>
               <td>Stort (18 til 25 m²)</td>
               <td class="right-align">[large size lowest price]</td>
               <td class="right-align">[large size average price]</td>
               <td class="right-align">[large size highest price]</td>
               </tr>
               <tr>
               <td>Meget stort (over 25 m²)</td>
               <td class="right-align">[very large size lowest price]</td>
               <td class="right-align">[very large size average price]</td>
               <td class="right-align">[very large size highest price]</td>
               </tr>
               </tbody>
               </table>';

     return $price_table;
}

$third_paragraph = '<h2>Hvordan finder jeg et sted at opmagasinere mine ting?</h2>
     <p class="three-columns">Hvis du leder efter et sikkert sted at opbevare mindre brugte genstande og personlige ejendele af alle typer og størrelser, er det oplagt at bruge opmagasinering, depotrum eller et opbevaringsrum tæt på dit hjem eller din virksomhed, så du har adgang til dine ting når som helst uden besvær. Her på tjekdepot får du et overblik over priser på opmagasinering og opbevaring og over rumstørrelser, egenskaber og fordele afhængigt af dine behov og dit budget. Du kan nemt finde opmagasinering i dit nabolag eller din by, uanset hvor du befinder dig i Danmark. Du er kun én søgning væk fra at finde det rigtige sted til dine ejendele i [location].</p>';

function generate_schools_paragraph($schools)
{
     $schools_first_paragraph = '<h2>Udvid din studiebolig i [location] med et depotrum</h2>
     <p class="three-columns">Hvis du skal flytte til [location] for at studere studere på ';
     $schools_second_paragraph = 'kan du få mere plads i din studiebolig med et depotrum. Studieboliger har ofte meget begrænset plads, og du kan undgå at rod af tøj og bøger, du ikke bruger hele året, ligger og roder. Et depotrum kan også være en idé for internationale studerende, der skal have opbevaret deres ejendele imens de er hjemme på sommerferie.</p>';
     if (!empty($schools)) {
          $schools_array = explode(",", $schools);
          $return_text = '';
          $return_text .=  $schools_first_paragraph;
          foreach ($schools_array as $key => $school) {
               if ($key === count($schools_array) - 2) {
                    $return_text .=  $school  . ' eller ';
               } elseif ($key === count($schools_array) - 1) {
                    $return_text .=  $school . ' ';
               } else {
                    $return_text .=  $school  . ', ';
               }
          }
          $return_text .=  $schools_second_paragraph;
          $return_text .= '<hr class="line">';
          return $return_text;
     }
}

function generate_neighbourhoods_paragraph($sublocations)
{
     $neighbourhoods_first_paragraph = '<h2>Bydele i Åarhus</h2>';
     if (!empty($sublocations)) {
          $return_text = '<p>';
          $return_text .=  $neighbourhoods_first_paragraph;
          foreach ($sublocations as $key => $sublocation) {
               $title = '<strong>' . get_the_title($sublocation) . '</strong>';
               $description = str_replace('<p>', '', get_the_content(null, false, $sublocation));
               $return_text .= $title . $description;
          }
          $return_text .= '</p>';
          $return_text .= '<hr class="line">';
          return $return_text;
     }
}

$text_template = '
          <p>[average price]
          [smallest m2 size]
          [lowest price]
          [average m2 price]
          [average m3 price]</p>
          <h3>Priser på depotrum i [location] fordelt efter størrelse</h3>
          <p>[mini size average price]
          [small size average price]
          [medium size average price]
          [large size average price]
          [very large size average price]</p>
           ';


$statistics_data_fields_texts = array(
     'average price' => 'Den gennemsnitlige pris for et ledigt depotrum i [location] er <strong>[average price] kr.</strong>',
     'smallest m2 size' => 'Der er er lige nu ledige depotrum fra <strong>[smallest m2 size] m² op til [largest m2 size] m², </strong>',
     'lowest price' => 'og prisen er mellem <strong>[lowest price] kr og [highest price] kr.</strong>',
     'average m2 price' => 'Kvadratmeterprisen er i gennemsnit <strong>[average m2 price] kr/m²,</strong>  og ',
     'average m3 price' => 'kubikmeterprisen er i gennemsnit <strong>[average m3 price] kr/m³.</strong>',
     'mini size average price' =>  'Et mini depotrum (op til 2 m²) koster i gennemsnit: <strong>[mini size average price] kr. </strong>',
     'small size average price' => 'Et lille depotrum (mellem 2 og 7 m²) koster i gennemsnit: <strong>[small size average price] kr. </strong>',
     'medium size average price' =>  'Et mellem depotrum (mellem 7 og 18 m²) koster i gennemsnit: <strong>[medium size average price] kr. </strong>',
     'large size average price' => 'Et stort depotrum (mellem 18 og 25 m²) koster i gennemsnit: <strong>[large size average price] kr. </strong>',
     'very large size average price' => 'Et meget stort depotrum (over 25 m²) koster i gennemsnit: <strong>[very large size average price] kr. </strong>',
);

function get_seo_paragraph($location_title, $paragraph_array)
{
     // print_r($first_paragraph);
     $num_of_options = count($paragraph_array);
     //echo "num of options: ", $num_of_options;
     $chosen_option = seeded_rand(0, $num_of_options, $location_title);
     //echo $chosen_option;
     //return $paragraph_array[2];
     return $paragraph_array[$chosen_option - 1];
}

function seeded_rand($min, $max, $seed)
{
     // Convert the seed to an integer using the crc32 function
     $seed = crc32($seed);

     // Seed the random number generator
     mt_srand($seed);

     // Generate and return a random number
     return mt_rand($min, $max);
}
