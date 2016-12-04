<?php
    session_start();
    include("setup.php");
    $_SESSION['UserID'];
    if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
    
    if(isset($_POST['update']))
   {
        if(strlen($_POST['email']) > 0){
            $email = $_POST['email'];
            $sql = 'UPDATE User
                    SET Email = "'.$email.'"
                    WHERE UserID = '.$_SESSION['UserID'];
            $result = $conn->query($sql);
        }
        if(strlen($_POST['password']) > 0){
            $password = $_POST['password'];
            $sql = 'UPDATE User
                    SET Password = "'.$password.'"
                    WHERE UserID = '.$_SESSION['UserID'];
            $result = $conn->query($sql);
        }
        if(strlen($_POST['address']) > 0){
            $address = $_POST['address'];
            $sql = 'UPDATE User
                    SET Address = "'.Address.'"
                    WHERE UserID = '.$_SESSION['UserID'];
            $result = $conn->query($sql);
        }
   }
    
    $sql = 'SELECT Email FROM User WHERE UserID = '.$_SESSION['UserID'];
    $result = $conn->query($sql);

	if($result->num_rows > 0)
	{
        while($row = $result->fetch_assoc()) 
		{
            $username = $row['Email'];
        }
    }
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $username ?>'s Page</title>

    <!-- Bootstrap -->
    <script src="js/bootstrap.js"></script>
      <script src="js/bootstrap.min.js"></script>
      
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
      <?php
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
    
        ?>
<nav class="navbar navbar-default">
    <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="userpage.php">DBMS Project</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="moviesList.php?type=WatchList">Watchlist</a></li>
                <li><a href="moviesList.php?type=RentalHistory">Rental History</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="signup.php?logout=true">Logout</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="row">
    <div class="input-group">
        <form method="POST" action="moviesList.php">
            <div class="col-md-5 col-md-offset-1"> 
                <input name="value" type="text" class="form-control" placeholder="Search">
            </div>
            <div class="col-md-4">
                <select class="form-control" name="type">
                    <option value="MovieName">Movie</option>
                    <option value="ActorName">Actor</option>
                    <option value="DirectorName">Director</option>
                    <option value="GenreName">Genre</option>
                </select>
            </div>
            <div class="col-md-2">
                <input name="update" class="btn btn-default" type="submit" value="Search"><br/>
            </div>
        </form>
    </div>
</div>
      <?php
    $sql = 'SELECT Email FROM User WHERE UserID = '.$_SESSION['UserID'];
    $result = $conn->query($sql);

	if($result->num_rows > 0)
	{
        echo '<div class="row"> 
                <div class="col-md-5 col-md-offset-1">
                    <h2>Welcome: ';
        while($row = $result->fetch_assoc()) 
		{
            $username = $row['Email'];
            echo $username. '</h2></div></div>';
        }
    }

    $sql = "SELECT Movie.MovieName, Movie.Poster, DATE_ADD(RentalHistory.DateRented,INTERVAL 30 DAY) AS DueDate
            FROM Movie, RentalHistory
            WHERE Movie.MovieID = RentalHistory.MovieID
            AND RentalHistory.DateReturned='1900=01-01'
            AND RentalHistory.UserID = ".$_SESSION['UserID'];
    
     $result = $conn->query($sql);
?>
      <div class="row">
          <div class="col-md-5 col-md-offset-1">
<?php
	if($result->num_rows > 0)
	{
        while($row = $result->fetch_assoc()) 
		{
            echo "<h4>You have the following movies rented:</h4><br/>";
            $movie = $row['MovieName'];
            $poster = '<img src= '.$row['Poster'].' height="300" width="200">';
            $due = $row['DueDate'];
            echo '<i>'.$movie.'</i> due on '.$due.'<br/>';
            echo $poster.'<br/>';
        }
    }
    else
    {
        echo '<h4>You have no movies currently rented</h4><br/>';
    }
            
?>
          </div></div>
      <br/>
<div class="row">
      <div class="col-md-10 col-md-offset-1">
<button class="btn btn-default" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  Update User Settings
</button>
<div class="collapse" id="collapseExample">
  <div class="well">
    
			<div class="input-group">
                <form method="POST" action="userpage.php">
                    <input type="text" name="email" class="form-control" placeholder="Email">
                    <input type="text" name="password" class="form-control" placeholder="Password">
                    <input type="text" name="address" class="form-control" placeholder="Address">
                    <input name="update" class="btn btn-default" type="submit" value="Submit Changes">
                </form>
            </div>
    </div>
			
  </div>
</div>
    </div>
      <br/><br/>

    
    
      

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    </body></html>