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
  include('bidManagementNav.php');
?>


<?php

include ('../classes/password.php');
$dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=".$password) or die ( 'Could not connect: ' . pg_last_error () );
?>


<body>

<form action='chooseBid.php' method='GET'>

<div class="container-table ">
<?php


  $query = "SELECT o.offerid, o.startdate, o.starttime, o.endbiddate, o.endbidtime, o.startloc, o.endloc FROM cs2102.RideOffer o WHERE o.offerid NOT IN (SELECT DISTINCT r.offerid from cs2102.Ride r WHERE r.carplateno = (SELECT d1.carplateno FROM cs2102.Driver d1 WHERE d1.email=$1)) AND o.endbiddate <= current_date AND o.startdate > current_date AND o.carplateno = (SELECT d.carplateno FROM cs2102.Driver d WHERE d.email=$1) ORDER BY o.endbiddate, o.endbidtime";




  //$getQuery = pg_query($dbconn, $query);

  $getQuery = pg_query_params($dbconn, $query, array($email));
  
  //$numRows = pg_num_rows($getQuery);

  //$resultSet = pg_fetch_row($getQuery);


  echo "<table class=\"table table-striped\">
    <thead>
      <tr>
        <th> </th>
        <th>Ride Offer ID</th>
        <th>Start Date</th>
        <th>Start Time</th>
        <th>End Bid Date</th>
        <th>End Bid Time</th>
        <th>Start Location</th>
        <th>End Location</th>

      </tr>
    </thead>";

  while ($row = pg_fetch_row($getQuery)) {
    echo "<tr>";
      echo "<td><label><input type=\"checkbox\" value=" . $row[0] . " name=\"offerid\"></label></td>"; 

      echo "<td>" . $row[0] . "</td>";
      echo "<td>" . $row[1] . "</td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
      echo "<td>" . $row[5] . "</td>";
      echo "<td>" . $row[6] . "</td>";

      echo "</tr>";
  }

  echo "</tbody>
  </table>";

?>
</div>

<div class="text-center">
<input type="submit" class="btn btn-info" value="Choose Bid" name="Choose">
</div>


<?php
pg_close($dbconn);


?> 

</form>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>