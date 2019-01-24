
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Driver Login</title>

  <!-- Bootstrap core CSS -->
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
    </head>

    <?php
// Connect to DB
    include ('../classes/password.php');
    $dbconn = pg_connect ( "host=localhost port=5432 dbname=carpool user=postgres password=".$password) or die ( 'Could not connect: ' . pg_last_error () );
    ?>

    
    <?php
    session_start();

// Declare helper variables

    if(isset($_POST["submitButton"])){

// Store email and password into variables
      $email = $_POST["email"];
      $password = $_POST["password"];

// Query database to check if exists
      $query = "SELECT password, email FROM cs2102.driver WHERE email=$1";
      $getQuery = pg_query_params($dbconn, $query, array($email));
    // Check no. of rows in query
      $numRows = pg_num_rows($getQuery);
    // Get the result of query
      $resultSet = pg_fetch_row($getQuery);

      if($numRows == 0) {
        header("Location: ../index.php");
        exit;
      }

// If email and password match that in DB, log user in and redirect to passenger home page
      else if($password == $resultSet[0] && $email == $resultSet[1] && $numRows != 0) {
// Set session 'email' to the user's email
        $_SESSION['email']=$resultSet[1];
    // Redirect user to next page
        header("Location: confirmedRides.php");
        exit;
      }
    }
    ?>
    

    <body>
      <div class="container">
        <div class="row">
          <div class="col-md-4">
          </div>
          <div class="col-md-4">
            <form name="passengerLogin" method="post" class="form-signin">
              <h2 class="form-signin-heading text-center">Driver Login</h2>
              <label for="email" class="sr-only">Email address</label>
              <input name ="email" type="email" id="email" class="form-control" placeholder="Email address" required autofocus>
              <label for="email" class="sr-only">Password</label>
              <input name="password" type="password" id="email" class="form-control" placeholder="Password" required>
              <div class="checkbox">
                <label>
                  <input type="checkbox" value="remember-me"> Remember me
                </label>
              </div>
              <button name="submitButton" class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
          </div>
          <div class="col-md-4">
          </div>
        </div> <!-- /row -->
      </div> <!-- /container -->
    </body>

    </html>