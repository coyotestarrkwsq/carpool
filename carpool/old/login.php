<html>
<head><title>Passenger Statistics</title></head>
<body>

<?php
ini_set('display_errors', 1);

//Start session 
session_start();

// Check if user is logged in
// Check if session is not registered, redirect back to main page.
if (!isset($_SESSION['username']) || $_SESSION['userstatus'] != 1) { //not logged in
	header ( "location:login.php" );
	exit();
}

$dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=marissa) 
			or die ( 'Could not connect: ' . pg_last_error () );

?>
			
<?php 



?>

<?php
echo "<br><li><b>View username of those who booked all trips of a driver</b></li><br>";
//Users who booked all trips of driver
echo "<table><tr><td>Enter the email of driver</td></tr><tr>
<form method='POST'>
<td><input type='text' name='email' size='50' required></td><td><input type='Submit' value='Search' name='findName'></td></tr></table>
</form>"
?>




<?php
//Users who booked all trips of driver (Condition and code)
if(isset($_POST['findName'])) {

$whoBookAll = "SELECT c.username FROM cs2102.customer c
				WHERE NOT EXISTS(
					SELECT * FROM cs2102.ride r 
					WHERE r.email = $1
					AND NOT EXISTS(
						SELECT * FROM cs2102.passengerride p 
						WHERE c.email = payer AND p.timeofride = r.time 
						AND p.licenceplate=r.licenceplate
						)
					)
				";
$result = pg_query_params($dbconn, $whoBookAll, array($_POST['email'])) or die('Error while selecting');
$numOfRows = pg_num_rows($result);

if($numOfRows>=1){
	echo "<h3>Result for " . $_POST['email'] . ":</h3><ul>";
	while($arr=pg_fetch_array($result,NULL, PGSQL_NUM)){
		echo "<li>$arr[0]</li>";
	}
	echo "</ul>";
}else{
	echo "<h3>No result found.</h3>";
}

}
echo	"</ul></td></tr>
		</table></div>";	
pg_free_result($result);
pg_close($dbconn);

?>

</body>
</html>