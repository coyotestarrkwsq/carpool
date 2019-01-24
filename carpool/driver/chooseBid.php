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
$offerid = $_GET['offerid'];

// Check if passenger is logged in
if (!isset($_SESSION['email'])) { 
  header("Location: ../index.php");
  exit;
}

?>

<?php
  include('bidManagementNav.php');
?>


<?php

include ('../classes/password.php');
$dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=".$password) or die ( 'Could not connect: ' . pg_last_error () );
?>


<body>

<form>

<div class="container-table ">
<?php



  $biddersQuery = "SELECT c.name, c.gender, b.starttime, b.pickuploc, b.dropoffloc, b.bidprice, c.email FROM cs2102.Customer_RideOffer_Bid b NATURAL JOIN cs2102.Customer c WHERE b.offerid = $1 ORDER BY b.bidprice DESC";

  $getBiddersQuery = pg_query_params($dbconn, $biddersQuery, array($offerid));

 
  $carCapacityQuery = "SELECT o.carcapacity FROM cs2102.RideOffer o WHERE o.offerid = $1";

  $getCarpacityQuery = pg_query_params($dbconn, $carCapacityQuery, array($offerid));


  $carCapacity = pg_fetch_row($getCarpacityQuery)[0];
  
  $numBidders = pg_num_rows($getBiddersQuery);


  if ($numBidders < $carCapacity) {
    echo "<h2 class=\"text-center\"> Please select " . $numBidders . " passengers </h2>";

  } else {
    echo "<h2 class=\"text-center\"> Please select " . $carCapacity . " passengers </h2>";

  }

  echo "<br>";

  echo "<table class=\"table table-striped\">
    <thead>
      <tr>
        <th></th>
        <th>Name</th>
        <th>Gender</th>
        <th>Start Time</th>
        <th>Pick Up Location</th>
        <th>Drop Off Location</th>
        <th>Bid Price</th>


      </tr>
    </thead>";

  $count = 0;  

  while ($row = pg_fetch_row($getBiddersQuery)) {
    echo "<tr>";
      echo "<td><label><input type='checkbox' name='select" .$count. "' value ='" . $offerid . "'/></label>".
                       "<input type='hidden' name='email" . $count . "' value ='" . $row[6] . "'/>".
                       "<input type='hidden' name='starttime" . $count . "' value ='" . $row[2] . "'/>".
                       "<input type='hidden' name='startloc" . $count . "' value ='" . $row[3] . "'/>".
                       "<input type='hidden' name='endloc" . $count . "' value ='" . $row[4] . "'/>".
                       "<input type='hidden' name='price" . $count . "' value ='" . $row[5] . "'/>".
                       "</td>";

      echo "<td>" . $row[0] . "</td>";
      echo "<td>" . $row[1] . "</td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
      echo "<td>" . $row[5] . "</td>";




      echo "</tr>";
      $count++;
  }

  echo "</tbody>
  </table>";

  echo "<input type='hidden' name='numBidders' value='$numBidders' />";


?>


<div class="text-center">
<input type="submit" class="btn btn-info" value="Comfirm" name="Confirm">


</div>


</form>

</div>




<?php

//$dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=".$password) or die ( 'Could not connect: ' . pg_last_error () );

$carplatenoQuery = "SELECT d.carplateno FROM cs2102.Driver d WHERE d.email=$1";


$getCarplatenoQuery = pg_query_params($dbconn, $carplatenoQuery, array($email));

$carplateno = pg_fetch_row($getCarplatenoQuery)[0];



$createNewRideQuery = "INSERT INTO cs2102.Ride (timecreated, email, carplateno, offerid, startloc, endloc, starttime, price) VALUES (now(), $1, $2, $3, $4, $5, $6, $7)";

//pg_prepare($dbconn, "", $createNewRideQuery);


if (isset($_GET['Confirm'])) {


  $numBidders = $_GET['numBidders'];
  for ($i = 0; $i < $numBidders; $i++) {


    if (isset($_GET["select". $i .""])) {
      echo $carplateno;
       $result = pg_query_params($dbconn, $createNewRideQuery, array($_GET["email". $i .""], $carplateno, $_GET["select". $i .""], $_GET["startloc". $i .""], $_GET["endloc". $i .""], $_GET["starttime". $i .""], $_GET["price". $i .""]));
       echo pg_last_error($dbconn);
    }
  }

}
pg_close($dbconn);

header("Location: confirmedRides.php");

?>





<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>