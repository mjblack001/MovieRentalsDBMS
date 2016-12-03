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
    
    $sql = 'SELECT Email FROM User WHERE UserID = '.$_SESSION['UserID'];
    $result = $conn->query($sql);

	if($result->num_rows > 0)
	{
        echo 'Welcome Back: ';
        while($row = $result->fetch_assoc()) 
		{
            $username = $row['Email'];
            echo $username. '<br/>';
        }
    }

    $sql = "SELECT Movie.MovieName, Movie.Poster, DATE_ADD(RentalHistory.DateRented,INTERVAL 30 DAY) AS DueDate
            FROM Movie, RentalHistory
            WHERE Movie.MovieID = RentalHistory.MovieID
            AND RentalHistory.DateReturned='1900=01-01'
            AND RentalHistory.UserID = ".$_SESSION['UserID'];
    
     $result = $conn->query($sql);

	if($result->num_rows > 0)
	{
        while($row = $result->fetch_assoc()) 
		{
            $movie = $row['MovieName'];
            $poster = '<img src= '.$row['Poster'].' height="300" width="200">';
            $due = $row['DueDate'];
            echo 'You still have <i>'.$movie.'</i> rented <br/>';
            echo $poster.'<br/>';
            echo 'It is due on '.$due.'<br/.>';
        }
    }
    else
    {
        echo 'You have no movies currently rented<br/>';
    }
            
?>
      <form method="POST" action="userpage.php">
            Email: <input name="email" type="text" ></br>
            Password: <input  name="password" type="text"></br>
            Address: <input  name="address" type="text"></br>
            <input name='update' type="submit">Update User Information</input>
    </form>
<br/><br/>
<form method="POST" action="moviesList.php">
			<select name="type">
				<option value="MovieName">Movie</option>
				<option value="ActorName">Actor</option>
				<option value="DirectorName">Director</option>
				<option value="GenreName">Genre</option>
			</select></br>
			value: <input  name="value" type="text"></br>
			<input type="submit">
		</form>
<input type="submit" value="moviesList.php?type=WatchList"></input>
<input type="submit" value="moviesList.php?type=RentalHistory"></input>
    
      
<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  Update User Settings
</button>
<div class="collapse" id="collapseExample">
  <div class="well">
    <form method="POST" action="userpage.php">
			<div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Email</span>
                <input type="text" value="email" class="form-control" placeholder="Email" aria-describedby="basic-addon1">
                
                <span class="input-group-addon" id="basic-addon1">Password</span>
                <input type="text" value="password" class="form-control" placeholder="Password" aria-describedby="basic-addon1">
                
                <span class="input-group-addon" id="basic-addon1">Address</span>
                <input type="text" value="address" class="form-control" placeholder="Address" aria-describedby="basic-addon1">
            </div>
			<input type="submit">
      </form>
  </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    </body></html>