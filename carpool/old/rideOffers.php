<html>
<head> 
<title>Ride Offers</title></head>
<body>
<table>
<tr> <td colspan="2" style="background-color:#FFFFFF;">
<h1 style="text-align: center;">Ride Offers</h1>
</td> </tr>

<?php
$dbconn = pg_connect("host=localhost port=5432 dbname=carpool user=postgres password=marissa")
    or die('Could not connect: ' . pg_last_error());
?>

<tr>
<td style="background-color:#eeeeee;">

<?php
    $query = "SELECT offerid, startloc, endloc, starttime, duration, minprice, endbidtime, carcapacity, carplateno FROM rideoffer";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    echo "<table border=\"1\" >
    <col width=\"75%\">
    <col width=\"25%\">
    <tr>
    <th>Offer ID</th>
    <th>Start Location</th>
    <th>End Location</th>
    <th>Start Time</th>
    <th>Duration</th>
    <th>Min Price</th>
    <th>End Bid Time</th>
    <th>Car Capacity</th>
    <th>Car Plate No.</th>
    </tr>";


    while ($row = pg_fetch_row($result)){
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
      echo "</tr>";
    }
    echo "</table>";
    
    pg_free_result($result);
?>

</td> </tr>
<?php
pg_close($dbconn);
?>

<a href="index.php">Back</a>
</table>

</body>
</html>
