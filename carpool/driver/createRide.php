<!DOCTYPE html>
<html>
  <head>
    <title>Create Ride</title>
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta charset="utf-8">

      <!-- Bootstrap -->
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../classes/normalizer.css" rel="stylesheet">
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->




    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 650px;
        width: 650px;

      }
      /* Optional: Makes the sample page fill the window. */
    

      .controls {
        margin-top: 10px;
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);

      }

      #origin-input,
      #destination-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 200px;
]

      }

      #origin-input:focus,
      #destination-input:focus {
        border-color: #4d90fe;
        margin: 0 auto;
        position: relative;


      }

      #mode-selector {
        color: #fff;
        background-color: #4d90fe;
        margin-left: 12px;
        padding: 5px 11px 0px 11px;
      }

      #mode-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }


      .col-sm-3 {
        padding-top: 70px;

      }
    </style>







</head>


<?php

session_start();

$email = $_SESSION['email'];
// Check if passenger is logged in
if (!isset($_SESSION['email'])) { 
  header("Location: ../index.php");
  exit;
}

?>

<?php
include('createRideNav.php');
?>

<?php

include ('../classes/password.php');
$dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=".$password) or die ( 'Could not connect: ' . pg_last_error () );
?>


<body>

<div class="col-sm-1">
</div>

<form>

<div class="col-sm-6">


<input type="radio" name="type" id="changemode-walking">
<label for="changemode-walking">Walking</label>

<input type="radio" name="type" id="changemode-transit">
<label for="changemode-transit">Transit</label>

<input type="radio" name="type" id="changemode-driving" checked="checked">
<label for="changemode-driving">Driving</label>

<div id="map"></div>

<input id="origin-input" name="startloc" class="controls" type="text"
            placeholder="Enter an origin location">


<input id="destination-input" name="endloc" class="controls" type="text"
            placeholder="Enter a destination location">


</div>


<div class="col-sm-3">


<div class="form-group">
<label for="startdate">Ride Start Date:</label>
<input type="date" class="form-control" name="startdate" placeholder="YYYY-MM-DD">
</div>

<div class="form-group">
<label for="startTime">Ride Start Time:</label>
<input type="time" class="form-control" name="starttime" placeholder="HH:MM">
</div>

<div class="form-group">
<label for="minPrice">Minimum Price:</label>
<input type="number" class="form-control" name="minprice">
</div>

<div class="form-group">
<label for="endbiddate">End Bid Date:</label>
<input type="date" class="form-control" name="endbiddate" placeholder="YYYY-MM-DD">
</div>

<div class="form-group">
<label for="endbidtime">End Bid Time:</label>
<input type="time" class="form-control" name="endbidtime" placeholder="HH:MM">
</div>

<div class="form-group">
<label for="carcapacity">Car Capacity:</label>
<input type="number" class="form-control" name="carcapacity">
</div>



<input type="submit" class="btn btn-info" value="Create" name="Create">




</form>

</div>


<?php
if(isset($_GET['Create'])) {

  $startloc = $_GET['startloc'];
  $endloc = $_GET['endloc'];
  $startdate = $_GET['startdate'];
  $starttime = $_GET['starttime'];
  $minprice = $_GET['minprice'];
  $endbiddate = $_GET['endbiddate'];
  $endbidtime = $_GET['endbidtime'];
  $carcapacity = $_GET['carcapacity'];

  $carplatenoQuery = "SELECT d.carplateno FROM cs2102.Driver d WHERE d.email=$1";
  $getCarplatenoQuery = pg_query_params($dbconn, $carplatenoQuery, array($email));
  $carplateno = pg_fetch_row($getCarplatenoQuery)[0];


  $createRideQuery = "INSERT INTO cs2102.RideOffer (timecreated, startloc, endloc, startdate, starttime, minprice, endbiddate, endbidtime, carcapacity, carplateno) VALUES (now(), '$startloc', '$endloc', '$startdate', '$starttime', '$minprice', '$endbiddate', '$endbidtime', '$carcapacity', '$carplateno')";

  $createRide = pg_query($dbconn, $createRideQuery);

  echo pg_last_error($dbconn);

  $insert_query = pg_query("SELECT lastval();");
  $insert_row = pg_fetch_row($insert_query);
  $insert_id = $insert_row[0];


  $createPickUpLocQuery = "INSERT INTO cs2102.Has_PickupPoints (pickuploc, offerid, starttime) VALUES ('$startloc', '$insert_id', '$starttime')";
  $createDropOffLocQuery = "INSERT INTO cs2102.Has_DropoffPoints (dropoffloc, offerid) VALUES ('$endloc', '$insert_id')";

  $createPickUpLoc = pg_query($dbconn, $createPickUpLocQuery);
  $createDropOffLoc = pg_query($dbconn, $createDropOffLocQuery);

  header("Location: modifyRide.php");

  pg_close($dbconn);


  }
?>







  
    <script>
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          mapTypeControl: false,
          center: {lat: 1.3521, lng: 103.8198},
          zoom: 13
        });

        new AutocompleteDirectionsHandler(map);
      }

       /**
        * @constructor
       */
      function AutocompleteDirectionsHandler(map) {
        this.map = map;
        this.originPlaceId = null;
        this.destinationPlaceId = null;
        this.travelMode = 'WALKING';
        var originInput = document.getElementById('origin-input');
        var destinationInput = document.getElementById('destination-input');
        var modeSelector = document.getElementById('mode-selector');
        this.directionsService = new google.maps.DirectionsService;
        this.directionsDisplay = new google.maps.DirectionsRenderer;
        this.directionsDisplay.setMap(map);

        var originAutocomplete = new google.maps.places.Autocomplete(
            originInput, {placeIdOnly: true});
        var destinationAutocomplete = new google.maps.places.Autocomplete(
            destinationInput, {placeIdOnly: true});

        this.setupClickListener('changemode-walking', 'WALKING');
        this.setupClickListener('changemode-transit', 'TRANSIT');
        this.setupClickListener('changemode-driving', 'DRIVING');

        this.setupPlaceChangedListener(originAutocomplete, 'ORIG');
        this.setupPlaceChangedListener(destinationAutocomplete, 'DEST');

        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(originInput);
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(destinationInput);
        this.map.controls[google.maps.ControlPosition.TOP_LEFT].push(modeSelector);
      }

      // Sets a listener on a radio button to change the filter type on Places
      // Autocomplete.
      AutocompleteDirectionsHandler.prototype.setupClickListener = function(id, mode) {
        var radioButton = document.getElementById(id);
        var me = this;
        radioButton.addEventListener('click', function() {
          me.travelMode = mode;
          me.route();
        });
      };

      AutocompleteDirectionsHandler.prototype.setupPlaceChangedListener = function(autocomplete, mode) {
        var me = this;
        autocomplete.bindTo('bounds', this.map);
        autocomplete.addListener('place_changed', function() {
          var place = autocomplete.getPlace();
          if (!place.place_id) {
            window.alert("Please select an option from the dropdown list.");
            return;
          }
          if (mode === 'ORIG') {
            me.originPlaceId = place.place_id;
          } else {
            me.destinationPlaceId = place.place_id;
          }
          me.route();
        });

      };

      AutocompleteDirectionsHandler.prototype.route = function() {
        if (!this.originPlaceId || !this.destinationPlaceId) {
          return;
        }
        var me = this;

        this.directionsService.route({
          origin: {'placeId': this.originPlaceId},
          destination: {'placeId': this.destinationPlaceId},
          travelMode: this.travelMode
        }, function(response, status) {
          if (status === 'OK') {
            me.directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      };

    </script>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyALuBeRQT1dwW_3LEUfqL6f5rzHioDldZQ&libraries=places&callback=initMap"
async defer></script>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>