<?php

if (isset($_POST['submit'])) {

  $addressInput = !empty($_POST['addressInput']) ? $_POST['addressInput'] : NULL;

  if (isset($addressInput)) {
    require 'vendor/autoload.php';

    $geocoder = new \Geocoder\Geocoder();
    $adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

    $geocoder->registerProviders(array(
      new \Geocoder\Provider\GoogleMapsProvider($adapter),
      ));

    $provider = new \Geocoder\Provider\FreeGeoIpProvider($adapter);

    try {
      $geocode = $geocoder->geocode($addressInput);
    } catch (Exception $e) {
      echo $e->getMessage();
    }

  }

  if (isset($geocode)) {

    $param_baseurl   = 'http://api.nytimes.com/svc/events/v2/listings.sphp?';
    $param_latitude  = $geocode->getLatitude();
    $param_longitude = $geocode->getLongitude();
    $param_radius    = '1000';
    $param_limit     = '20';
    $param_apikey    = 'd3ce9f86fec65789fe095489f2cd1a7f:15:61046211';
    $params =
      $param_baseurl .
      'll=' . $param_latitude . ',' . $param_longitude .
      '&radius=' . $param_radius .
      '&limit=' . $param_limit .
      '&api-key=' . $param_apikey;

    $ch = curl_init( $params );
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    $response = curl_exec( $ch );
  }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <title>Eventr</title>
  <meta name="description" content="A web service for listing nearby events using your location provided by the NYTimes Events API">
  <meta name="author" content="Alex Ho">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href='//fonts.googleapis.com/css?family=Raleway:400,300,600' rel='stylesheet' type='text/css'>
  <link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css' rel='stylesheet'>

  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/skeleton.css">
  <link rel="stylesheet" href="css/main.css">
  <link rel="icon" type="image/png" href="images/favicon.png" />

  <script type="text/javascript" src="js/custom.modernizr.min.js"></script>


  <style type="text/css">
    #map { position:absolute; top:0; bottom:0; width:100%; }
  </style>
</head>
<body>

  <!-- Primary Page Layout
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
  <div class="container">
  <nav class="navbar">
    <div class="container">
      <ul class="navbar-list">
        <li class="navbar-item"><a class="navbar-link" href="#home">Home</a></li>
        <li class="navbar-item"><a class="navbar-link" href="#about">About</a></li>
      </ul>
    </div>
  </nav>
    <div class="row">
      <div style="margin-top: 10%">
        <h4>Enter an address below or share your browser location</h4>
        <form name="submitForm" method="post" action="index.php">
          <div class="row">
            <div class="six columns">
              <label for="addressInput">Address</label>
              <input class="u-full-width" placeholder="620 Eighth Ave, New York, NY" name="addressInput" type="text">
            </div>
          </div>
          <input class="button-primary" name="submit" value="Submit" type="submit">
        </form>
      </div>
    </div>
    <div class="row">
      <div id="map"></div>
    </div>
  </div>
  <script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.js'></script>
  <script type="text/javascript">

  function show_map(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    L.mapbox.accessToken = 'pk.eyJ1IjoibXJhbGV4aG8iLCJhIjoiZkVVWXpMdyJ9.MfTGF2viSjHQ7hrBmbFPYw';
    var map = L.mapbox.map('map', 'mralexho.kincjfb5')
        .setView([latitude, longitude], 15);

    L.mapbox.featureLayer({
      type: 'Feature',
      geometry: {
        type: 'Point',
        coordinates: [
          longitude,
          latitude
        ]
      },
      properties: {
        title: 'You are here',
        'marker-color': '#1EAEDB',
      }
    }).addTo(map);
  }

  function handle_error(err) {
    if (err.code == 1) {
      // PERMISSION_DENIED
    } else if (err.code == 2) {
      // POSITION_UNAVAILABLE
    } else if (err.code == 3) {
      // TIMEOUT
    }
  }

  if (Modernizr.geolocation) {
    navigator.geolocation.getCurrentPosition(
      show_map, // success callback
      handle_error, // error callback
      { maximumAge: 60000 });
  }
  </script>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
</body>
</html>
