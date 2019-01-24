<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Bootstrap 101 Template</title>

<!-- Bootstrap -->
<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../classes/normalizer.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<style>

.container-table {
  padding-top: 70px;
  padding-bottom: 70px;
}

</style>

<?php

session_start();

$email = $_SESSION['email'];
$pickuploc =  $_GET['pickuploc'];
$dropoffloc = $_GET['dropoffloc'];


// Check if passenger is logged in
if (!isset($_SESSION['email'])) { 
  header("Location: ../index.php");
  exit;
}

?>

<?php
  include('passengerNav.php');
?>



<?php

include ('../classes/password.php');
$dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=".$password) or die ( 'Could not connect: ' . pg_last_error () );
?>

<body>


<form>
<div class="container-table">

<?php

//if (isset($_GET['Search'])) {



  if(($pickuploc!="") && ($dropoffloc!="")) {

    $searchQuery = "SELECT o.offerid, o.startdate, p.starttime, p.pickuploc, d.dropoffloc, o.carcapacity, o.endbiddate, o.endbidtime, o.minprice FROM cs2102.Has_PickupPoints p INNER JOIN cs2102.RideOffer o ON p.offerid = o.offerid INNER JOIN cs2102.Has_DropoffPoints d ON d.offerid = p.offerid WHERE p.pickuploc LIKE $1 AND d.dropoffloc LIKE $2 AND o.endbiddate>=current_date ORDER BY o.endbiddate, o.endbidtime";


  //$getQuery = pg_query($dbconn, $query);

    $searchResult = pg_query_params($dbconn, $searchQuery, array("%" . $pickuploc . "%", "%" . $dropoffloc . "%"));
    echo pg_last_error($dbconn);
  
    $numRows = pg_num_rows($searchResult);

  } elseif(($pickuploc!="") && ($dropoffloc=="")) {

    $searchQuery = "SELECT o.offerid, o.startdate, p.starttime, p.pickuploc, d.dropoffloc, o.carcapacity, o.endbiddate, o.endbidtime, o.minprice FROM cs2102.Has_PickupPoints p INNER JOIN cs2102.RideOffer o ON p.offerid = o.offerid INNER JOIN cs2102.Has_DropoffPoints d ON d.offerid = p.offerid WHERE p.pickuploc LIKE $1 AND o.endbiddate>=current_date ORDER BY o.endbiddate, o.endbidtime";

    $searchResult = pg_query_params($dbconn, $searchQuery, array("%" . $pickuploc . "%"));
    echo pg_last_error($dbconn);
  
    $numRows = pg_num_rows($searchResult);

  } else{
    //header("Location: searchRide.php");
  }

  echo "<input type='hidden' name='numRows' value='$numRows' />";


  //$resultSet = pg_fetch_row($getQuery);


  echo "<table class=\"table table-striped\">
    <thead>
      <tr>
        <th></th>
        <th>Offer ID</th>
        <th>Start Date</th>
        <th>Start Time</th>
        <th>Pick Up Locations</th>
        <th>Drop Off Locations</th>
        <th>Car Capacity</th>
        <th>End Bid Date</th>
        <th>End Bid Time</th>
        <th>Minimum Price</th>
        <th>Your Bid</th>

      </tr>
    </thead>";

  $count = 0;  

  while ($row = pg_fetch_row($searchResult)) {
    echo "<tr>";
      echo "<td><label><input type='checkbox' name='select" .$count. "' value ='" . $row[0] . "'/></label>".
                       "<input type='hidden' type='time' name='starttime" . $count . "' value ='" . $row[2] . "'/>".
                       "<input type='hidden' name='pickuploc" . $count . "' value ='" . $row[3] . "'/>".
                       "<input type='hidden' name='dropoffloc" . $count . "' value ='" . $row[4] . "'/>".
                       "</td>";      

      echo "<td>" . $row[0] . "</td>";
      echo "<td>" . $row[1] . "</td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
      echo "<td>" . $row[5] . "</td>";
      echo "<td>" . $row[6] . "</td>";
      echo "<td>" . $row[7] . "</td>";
      echo "<td>" . $row[8] . "</td>";

      echo "<td><input type='form-control' type='number' name='bidprice" . $count . "'/></td>";

      echo "</tr>";

      $count++;
  }

  echo "</tbody>
  </table>";



  echo "<div class='text-center'>
<input type='submit' class='btn btn-info' value='Bid' name='Bid'>
</div>";

//}
 
?>


</div>

</form>



<?php
if (isset($_GET['Bid'])) {

  $createBidQuery = "INSERT INTO cs2102.customer_rideoffer_bid (offerid, starttime, pickuploc, dropoffloc, bidprice, email) VALUES ($1, $2, $3, $4, $5, $6)";

  $numRows = $_GET['numRows'];

  for ($i = 0; $i < $numRows; $i++) {

    if (isset($_GET["select". $i .""])) {
      $createBid = pg_query_params($dbconn, $createBidQuery, array($_GET["select". $i .""], $_GET["starttime". $i .""], $_GET["pickuploc". $i .""], $_GET["dropoffloc". $i .""], $_GET["bidprice". $i .""], $email));
      echo pg_last_notice($dbconn);
      echo "<br>";
      echo pg_last_error($dbconn);

    }

  }
  
  }



pg_close($dbconn);


?>





<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
