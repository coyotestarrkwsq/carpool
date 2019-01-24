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
        height: 600px;
        width: 600px;
        margin-left:auto; 
        margin-right:auto;

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

      .container-table {
        padding-bottom: 50px;
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
include('modifyRideNav.php');
?>

<?php

include ('../classes/password.php');
$dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=".$password) or die ( 'Could not connect: ' . pg_last_error () );
?>


<body>



<form>

<div class="container-fluid bg-1 text-center">


<input type="radio" name="type" id="changemode-walking">
<label for="changemode-walking">Walking</label>

<input type="radio" name="type" id="changemode-transit">
<label for="changemode-transit">Transit</label>

<input type="radio" name="type" id="changemode-driving" checked="checked">
<label for="changemode-driving">Driving</label>

<div id="map" ></div>



<input id="origin-input" name="pickuploc" class="controls" type="text"
            placeholder="Enter an pick up location">


<input id="destination-input" name="dropoffloc" class="controls" type="text"
            placeholder="Enter a drop off location">

<h2> Add Pick Up or Drop Off Location </h2>


</div>

<div class="container-table">


<?php

  $numOfPickupQuery = "SELECT o.offerid, o.startdate, o.starttime, o.startloc, o.endloc, COUNT(p.pickuploc) FROM cs2102.RideOffer o INNER JOIN cs2102.Has_PickupPoints p ON p.offerid = o.offerid WHERE o.carplateno = (SELECT d1.carplateno FROM cs2102.Driver d1 WHERE d1.email = $1) AND o.endbiddate >= current_date AND o.offerid NOT IN (SELECT DISTINCT r.offerid from cs2102.Ride r WHERE r.carplateno = (SELECT d.carplateno FROM cs2102.Driver d WHERE d.email=$1)) GROUP BY o.offerid ORDER BY o.startdate, o.offerid";

  $numOfDropoffQuery = "SELECT o.offerid, COUNT(l.dropoffloc) FROM cs2102.RideOffer o NATURAL JOIN cs2102.Has_DropoffPoints l WHERE o.carplateno = (SELECT d1.carplateno FROM cs2102.Driver d1 WHERE d1.email = $1) AND o.endbiddate >= current_date AND o.offerid NOT IN (SELECT DISTINCT r.offerid from cs2102.Ride r WHERE r.carplateno = (SELECT d.carplateno FROM cs2102.Driver d WHERE d.email=$1)) GROUP BY o.offerid ORDER BY o.startdate, o.offerid";


  //$getQuery = pg_query($dbconn, $query);

  $numOfPickup = pg_query_params($dbconn, $numOfPickupQuery, array($email));
  $numOfDropoff = pg_query_params($dbconn, $numOfDropoffQuery, array($email));

  
  $numRows = pg_num_rows($numOfPickup);



  echo "<table class=\"table table-striped\">
    <thead>
      <tr>
        <th></th>
        <th>Ride Offer ID</th>
        <th>Start Date</th>
        <th>Start Time</th>
        <th>Start Location</th>
        <th>End Location</th>
        <th>No of Pick Up Locations</th>
        <th>No of Drop Off Locations</th>
      </tr>
    </thead>";

  $count = 0;  

  while (($row1 = pg_fetch_row($numOfPickup)) && ($row2 = pg_fetch_row($numOfDropoff))) {
    echo "<tr>";
      //echo "<td><label><input type='checkbox' value=" . $row1[0] . " name=\"offerid\"></label></td>"; 

      echo "<td><label><input type='checkbox' name='select" .$count. "' value ='" . $row1[0] . "'/></label></td>";
      echo "<td>" . $row1[0] . "</td>";
      echo "<td>" . $row1[1] . "</td>";
      echo "<td>" . $row1[2] . "</td>";
      echo "<td>" . $row1[3] . "</td>";
      echo "<td>" . $row1[4] . "</td>";
      echo "<td>" . $row1[5] . "</td>";
      echo "<td>" . $row2[1] . "</td>";
      echo "</tr>";

      $count++;
  }


  echo "</tbody>
  </table>";


      
  echo "<div class='col-xs-3'> <label for='starttime'>Enter Start Time For New Pick Up Location</label>
<input class='form-control' name='starttime' type='time' placeholder='HH:MM'/>";

  echo "<input type='hidden' name='numRows' value='$numRows' />";
?>

</div>

<br>

<div>
<input type="submit" class="btn btn-info" value="Add" name="Add">
</div>



</form>


</div>



<?php
if (isset($_GET['Add'])) {

  $numRows = $_GET['numRows'];
  for ($i=0; $i < $numRows; $i++) {

    if (isset($_GET["select". $i .""])) {

      $offerid = $_GET["select". $i .""];
      $pickuploc = $_GET['pickuploc'];
      $dropoffloc = $_GET['dropoffloc'];
      $starttime = $_GET['starttime'];

      $addPickUpLocQuery = "INSERT INTO cs2102.Has_PickupPoints (pickuploc, offerid, starttime) VALUES ('$pickuploc', '$offerid','$starttime')";
      $addDropOffLocQuery = "INSERT INTO cs2102.Has_DropoffPoints (dropoffloc, offerid) VALUES ('$dropoffloc', '$offerid')";
      
      if (($pickuploc != "") && ($dropoffloc != "")) {


        $addPickUpLoc = pg_query($dbconn, $addPickUpLocQuery);
        $addDropOffLoc = pg_query($dbconn, $addDropOffLocQuery);

      } elseif ($pickuploc != "") {
        $addPickUpLoc = pg_query($dbconn, $addPickUpLocQuery);
        echo pg_last_error($dbconn);

      } elseif ($dropoffloc != "") {
        $addDropOffLoc = pg_query($dbconn, $addDropOffLocQuery);

      } else {
        echo "empty input!";
      } 
    }

  }


$page = $_SERVER['PHP_SELF'];
echo '<meta http-equiv="Refresh" content="0;' . $page . '">';
  

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