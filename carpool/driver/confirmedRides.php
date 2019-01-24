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
  include('confirmedRidesNav.php');
?>


<?php

include ('../classes/password.php');
$dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=".$password) or die ( 'Could not connect: ' . pg_last_error () );
?>


<body>


<div class="container-table ">
<?php

  //$nameGenderMobileQuery = "SELECT r.email FROM cs2102.ride r WHERE r.carplateno = ANY (SELECT d.carplateno FROM cs2102.driver d WHERE d.email=$1) AND r.startdate > current_date";


  $query = "SELECT o.offerid, c.name, c.gender, c.mobileno, o.startdate, r.starttime, o.startloc, o.endloc, r.startloc, r.endloc FROM cs2102.Driver d INNER JOIN cs2102.Ride r ON d.carplateno = r.carplateno INNER JOIN cs2102.Customer c ON c.email = r.email INNER JOIN cs2102.RideOffer o ON o.carplateno = d.carplateno AND o.offerid = r.offerid WHERE d.email = $1 AND o.startdate >=current_date ORDER BY o.startdate, r.starttime";

  //$getQuery = pg_query($dbconn, $query);

  $getQuery = pg_query_params($dbconn, $query, array($email));
  
  //$numRows = pg_num_rows($getQuery);

  //$resultSet = pg_fetch_row($getQuery);


  echo "<table class=\"table table-striped\">
    <thead>
      <tr>
        <th>Ride Offer ID</th>
        <th>Customer Name</th>
        <th>Gender</th>
        <th>Contact No</th>
        <th>Start Date</th>
        <th>Start Time</th>
        <th>Start Location</th>
        <th>End Location</th>
        <th>Pickup Location</th>
        <th>Dropoff Location</th>
      </tr>
    </thead>";

  while ($row = pg_fetch_row($getQuery)) {
    echo "<tr>";
      echo "<td>" . $row[0] . "</td>";
      echo "<td>" . $row[1] . "</td>";
      echo "<td>" . $row[2] . "</td>";
      echo "<td>" . $row[3] . "</td>";
      echo "<td>" . $row[4] . "</td>";
      echo "<td>" . $row[5] . "</td>";
      echo "<td>" . $row[6] . "</td>";
      echo "<td>" . $row[7] . "</td>";
      echo "<td>" . $row[8] . "</td>";
      echo "<td>" . $row[9] . "</td>";
      echo "</tr>";
  }

  echo "</tbody>
  </table>";

?>
</div>


<?php
pg_close($dbconn);
?> 



<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>