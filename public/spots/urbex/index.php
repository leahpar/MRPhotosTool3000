<?php

// https://www.pinterest.ch/patrimoine43/abandoned-urbex-locations/

$f = file_get_contents("totoall1.html");
$f .= file_get_contents("totoall2.html");
$f .= file_get_contents("totoall3.html");

//$regex = '/<img alt="([^"]*)" class=/';
//$regex = '#href="/pin/([0-9]+)/".*<img alt="([^"]*)" class=#';
$regex = '/<img alt="([^"]*)" class="[^"]*" importance="[^"]*" loading="[^"]*" src="[^"]*" srcset="([^"]*)"/';
$matches = [];
preg_match_all($regex, $f, $matches, PREG_SET_ORDER);

$spots = [];
foreach ($matches as $k => $data) {
    $match = html_entity_decode($data[1]);
    $pos = explode(" ", $match);

    $srcset = $data[2];
    $img = explode(" ", $srcset)[6];

    $spots[$img] = [
        "img" => $img,
        "match" => $match,
        "lat" => $pos[0],
        "lng" => $pos[1],
    ];

}

function gps($str) {
    $str = str_replace('°', '&', $str);
    $p1 = strpos($str, "&");
    $p2 = strpos($str, "'");
    $p3 = strpos($str, "\"");
    $l = strlen($str);
    $deg = substr($str, 0, $p1);
    $min = substr($str, $p1+1, $p2-$p1-1);
    $sec = substr($str, $p2+1, $p3-$p2-1);
    $azi = substr($str, $p3+1);
    $pos = $deg+((($min*60)+($sec))/3600);
    if ($azi == "W") $pos = -$pos;
    if ($azi == "S") $pos = -$pos;
    //echo "\n$str\n$p1 $p2 $p3 $l - $deg $min $sec $azi\n$pos";
    return $pos;
}


if (php_sapi_name() === 'cli') {
    foreach ($spots as $k => $spot) {

        echo gps($spot["lat"])."\t";
        echo gps($spot["lng"])."\t";
        //echo $spot["img"]."\t";
        echo $spot["match"]."\t";
        //if ($k >= 10) die();
    }
    die;
}


set_error_handler('warning_handler', E_WARNING);
function warning_handler($errno, $errstr, $errfile, $errline, array $errcontext)
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}


?>

<style>
    .body {
        height: 100vh;
    }
    .content-int {
        padding: 0 !important;
        height: calc(100% - 60px);
    }

</style>
<div id="map" style="height: 100%; width: 100%; margin: auto"></div>

<?php

?>

<script>
    let map = false;

    function initMap() {
        /*
        // https://developers.google.com/maps/documentation/javascript/markers
        let markerIcon = {
            //path: svgPath,
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: "blue",
            fillOpacity: 0.6,
            strokeWeight: 0,
            rotation: 0,
            scale: 10,
            //scale: 0.08,
            //anchor: new google.maps.Point(15, 30),
        };
        */

        // Rouen
        let initialLocation = { lat: 49.44, lng: 1.10 };

        map = new google.maps.Map(document.getElementById("map"), {

            center: initialLocation,
            zoom: 5,

            // https://developers.google.com/maps/documentation/javascript/style-reference
            styles: [
                {
                    featureType: "poi",
                    stylers: [{ visibility: "off" }],
                },
                {
                    featureType: "transit",
                    elementType: "labels.icon",
                    stylers: [{ visibility: "off" }],
                },
            ],
        });

        const colors = {
            0: 'blue',   // précontact
            1: 'green',  // prospect
            2: 'yellow', // client
            3: 'red'     // en pose / posé
        };

        let marker;
        let markers = [];
        let infowindow = new google.maps.InfoWindow();

        <?php foreach ($spots as $k => $spot): ?>

            <?php
            //if ($k >= 10) break;


            try {
                $aaa = json_encode(["lat" => gps($spot["lat"]), "lng" => gps($spot["lng"])]);
            } catch (\Exception $e) {
                echo "console.log('".$spot["match"]."');";
                break;
            }


            $content = "<h3>".$spot["match"]."</h3>";
            $content .= "<img src='".$spot["img"]."'>";
            ?>

            marker = new google.maps.Marker({
                    position: <?= $aaa ?>,
                    map,
                    title: "",
                    icon: { url: "https://maps.google.com/mapfiles/ms/icons/green-dot.png" },
                }
            );
            markers.push(marker);

            // Some help here : https://gist.github.com/grandmanitou/8863248
            google.maps.event.addListener(marker, 'click', (function(marker) {
                // Funcionception !
                return function() {
                    infowindow.setContent("<?= str_replace('"', '\\"', $content) ?>");
                    //infowindow.setOptions({maxWidth: 200});
                    infowindow.open(map, marker);
                }
            }) (marker));

        <?php endforeach; ?>

    }

</script>

<!-- Async script executes immediately and must be after any DOM elements used in callback. -->
<?php define("GOOGLE_API_KEY", "AIzaSyB5Eunxt7ro-1hHZ0KH-eLde7iSDhutRuA"); ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_API_KEY ?>&callback=initMap&libraries=&v=weekly" async></script>

