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

<div class="container-table ">
<?php


  $query = "SELECT b.offerid, o.startdate, b.starttime, b.pickuploc, b.dropoffloc, o.minprice, b.bidprice FROM cs2102.Customer_RideOffer_Bid b NATURAL JOIN cs2102.RideOffer o WHERE b.email = $1 AND o.endbiddate >= current_date ORDER BY o.startdate";



  $getQuery = pg_query_params($dbconn, $query, array($email));
  echo pg_last_error($dbconn);
  
  $numRows = pg_num_rows($getQuery);

  //$resultSet = pg_fetch_row($getQuery);


  echo "<table class=\"table table-striped\">
    <thead>
      <tr>
        <th> </th>
        <th>Ride Offer ID</th>
        <th>Start Date</th>
        <th>Start Time</th>
        <th>Pick Up Location</th>
        <th>Drop Off Location</th>
        <th>Minimum Bid</th>
        <th>Your Current Bid</th>
        <th>Enter New Bid</th>

      </tr>
    </thead>";

  $count = 0;  

  while ($row = pg_fetch_row($getQuery)) {
    echo "<tr>";
      echo "<td><label><input type='checkbox' name='select" .$count. "' value ='" . $row[0] . "'/></label></td>"; 

      echo "<td>" . $row[0] . "</td>";
      echo "<td>" . $row[1] . "</td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
      echo "<td>" . $row[5] . "</td>";
      echo "<td>" . $row[6] . "</td>";


      echo "<td><input class='form-control' type='number' name='newBid" .$count. "'/></td>";

      echo "</tr>";

      $count++;
  }

  echo "</tbody>
  </table>";
 
  echo "<input type='hidden' name='numRows' value='$numRows' />";


?>
</div>

<div class="text-center">
<input type="submit" class="btn btn-info" value="Confirm" name="Confirm">
</div>

 
</form>




<?php

if (isset($_GET['Confirm'])) {


  $numRows = $_GET['numRows'];
  for ($i=0; $i < $numRows; $i++) {

    if (isset($_GET["select". $i .""])) {

      $newBid = $_GET["newBid". $i .""];

      $offerid = $_GET["select". $i .""];
    
      $updateBidQuery = "UPDATE cs2102.Customer_RideOffer_Bid SET bidprice = $1 WHERE email = $2 and offerid = $3";

      $updateBid = pg_query_params($dbconn, $updateBidQuery, array($newBid, $email, $offerid));
     
      echo pg_last_notice($dbconn);
      echo "<br>";
      echo pg_last_error($dbconn);

      //$page = $_SERVER['PHP_SELF'];
      //echo '<meta http-equiv="Refresh" content="0;' . $page . '">';

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