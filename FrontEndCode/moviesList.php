<?php
    session_start();
?>
<html>
	<head>
        <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Movies Page</title>
		<link rel='stylesheet' type='text/css' href='DBFrontEndStyle.css'></link>
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
	<head>
	<body>
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
                <li><a href="userpage.php">User Page</a></li> <!-- LINK TO USER PAGE --------------------------------------->
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
		
	</body>
</html>

<?php 
	include("setup.php");
	//$_SESSION['UserID'] = '3'; //TEST SESSION VAR
 
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	
	$type = "";
	$value = "";
	$uid = $_SESSION['UserID'];
		
	if(isset($_POST['type']) && isset($_POST['value']))
	{
		$type = $_POST['type'];
		$value = $_POST['value'];
	}
	else if(isset($_GET['type']))
	{
		$type = $_GET['type'];
		$value = "some arbitrary and useless value";
	}
	
	$sql='';
	$valueCheck = "%". $value. "%";
	
	switch ($type)
	{
		case("ActorName"):
			$sql = "SELECT Movie.MovieName, Movie.MovieID, Movie.ReleaseDate, Actor.ActorName, AVG(Rating.Rating)
					FROM Movie, ActedIn, Actor, Rating
					WHERE Movie.MovieID = ActedIn.MovieID
					AND	Movie.MovieID = Rating.MovieID
					AND ActedIn.ActorID = Actor.ActorID
					AND Actor.ActorName LIKE '$valueCheck'
					GROUP BY MovieName";
			break;
		case("DirectorName"):
			$sql = "SELECT Movie.MovieName, Movie.MovieID,  Movie.ReleaseDate, Director.DirectorName, AVG(Rating.Rating)
					FROM Movie, Director, Rating
					WHERE Movie.DirectorID = Director.DirectorID
					AND	Movie.MovieID = Rating.MovieID
					AND Director.DirectorName LIKE '$valueCheck'
					GROUP BY MovieName";
				
			break;
		case("GenreName"):
			$sql = "SELECT Movie.MovieName, Movie.MovieID, Movie.ReleaseDate, Genre.GenreName, AVG(Rating.Rating)
					FROM Movie, Genre, Rating
					WHERE Movie.GenreID = Genre.GenreID
					AND	Movie.MovieID = Rating.MovieID
					AND Genre.GenreName LIKE '$valueCheck'
					GROUP BY MovieName";
			break;
		case("MovieName"):
			$sql = "SELECT Movie.MovieName, Movie.MovieID, Movie.ReleaseDate, AVG(Rating.Rating)
					FROM Movie, Rating
					WHERE Movie.MovieName LIKE '$valueCheck'
					AND	Movie.MovieID = Rating.MovieID
					GROUP BY MovieName";
			break;
		case("WatchList"):
			$sql = "SELECT Movie.MovieName, Movie.MovieID, Movie.ReleaseDate, User.UserID, AVG(Rating.Rating)
					FROM Movie, Rating, User, watchlist
					WHERE Movie.MovieID = Rating.MovieID
					AND watchlist.MovieID = Movie.MovieID
					AND watchlist.UserID = User.UserID 
					AND User.UserID LIKE '$uid'
					GROUP BY MovieName";
			break;
		case("RentalHistory");
			$sql = "SELECT Movie.MovieName, Movie.MovieID, Movie.ReleaseDate, User.UserID, AVG(Rating.Rating), RentalHistory.DateRented, RentalHistory.DateReturned
					FROM Movie, Rating, User, RentalHistory
					WHERE Movie.MovieID = Rating.MovieID
					AND RentalHistory.MovieID = Movie.MovieID
					AND RentalHistory.UserID = '$uid'
					AND User.UserID LIKE '$uid'
					GROUP BY MovieName";
			break;
		default: 
			$sql = "SELECT Movie.MovieName, Movie.MovieID, Movie.ReleaseDate, AVG(Rating.Rating)
					FROM Movie, Rating
					WHERE Movie.MovieID = Rating.MovieID
					GROUP BY MovieName";
	}
	
	if(isset($_POST['SortType']))
	{
		$SortType = $_POST['SortType'];
		$sql = $sql. " ORDER BY $SortType";
		//echo "</br>". $sql. "; ". $conn->error. "</br>";
	}
	if(isset($_POST['remove']))
	{
		$tempsql = "DELETE FROM watchlist 
					WHERE UserID=$uid 
					AND MovieID=$value";
		if($conn->query($tempsql) === TRUE)
		{
			echo "Watchlist updated successfully</br>";
		}
		else
		{
			echo "ERROR: ". $tempsql. "<br>". $conn->error;
		}
	}
	$result = $conn->query($sql);
	
	if($result != FALSE && $result->num_rows > 0)
	{
		echo "<br/><div class='row'>
                <div class='col-md-10 col-md-offset-1'>
                <table class='table table-hover'>
                <th>
                <form method='POST' action='moviesList.php'>
						<select name='SortType'>
						<option value='MovieName ASC'>Movie Name ASC</option>
						<option value='MovieName DESC'>Movie Name DESC</option>
						<input type='hidden' name='type' value ='$type'></input>
						<input type='hidden' name='value' value ='$value'></input>
						<input type='submit'>
						</form>
				</th>
				<th>
                <form method='POST' action='moviesList.php'>
						<select name='SortType'>
						<option value='ReleaseDate ASC'>Release Date ASC</option>
						<option value='ReleaseDate DESC'>Release Date DESC</option>
						<input type='hidden' name='type' value ='$type'></input>
						<input type='hidden' name='value' value ='$value'></input>
						<input type='submit'>
						</form>
				</th>
					<th>
                    <form method='POST' action='moviesList.php'>
						<select name='SortType'>
						<option value='AVG(Rating.Rating) ASC'>Rating ASC</option>
						<option value='AVG(Rating.Rating) DESC'>Rating DESC</option>
						<input type='hidden' name='type' value ='$type'></input>
						<input type='hidden' name='value' value ='$value'></input>
						<input type='submit'>
						</form>
				</th>";
				
		if($type =="ActorName") echo"<th>Actor Name</th>";
		if($type =="GenreName") echo"<th>Genre Name</th>";
		if($type =="DirectorName") echo"<th>Director Name</th>";
		if($type =="RentalHistory")
			{
				echo 	"<th>
                            <form method='POST' action='moviesList.php'>
							<select name='SortType'>
							<option value='DateRented ASC'>Date Rented ASC</option>
							<option value='DateRented DESC'>Date Rented DESC</option>
							<input type='hidden' name='type' value ='$type'></input>
							<input type='hidden' name='value' value ='$value'></input>
							<input type='submit'>
						</form></th>";
				echo "<th>
                        <form method='POST' action='moviesList.php'>
							<select name='SortType'>
							<option value='DateReturned ASC'>Date Returned ASC</option>
							<option value='DateReturned DESC'>Date Returned DESC</option>
							<input type='hidden' name='type' value ='$type'></input>
							<input type='hidden' name='value' value ='$value'></input>
							<input type='submit'>
						</form></th>";
			}
		echo"</tr>";
		
		while($row = $result->fetch_assoc()) 
		{
			$MovieID = $row["MovieID"];
			echo "<tr> <td> <a href='MoviePage.php?MovieID=$MovieID'>" . $row["MovieName"]. "</a></td> <td>" . $row["ReleaseDate"]. "</td> <td>". $row["AVG(Rating.Rating)"]. "</td>";
			if($type =="ActorName")		echo "<td>". $row["ActorName"]. "</td>";
			if($type =="GenreName") 	echo "<td>". $row["GenreName"]. "</td>";
			if($type =="DirectorName") 	echo "<td>". $row["DirectorName"]. "</td>";
			if($type =="RentalHistory")
			{
				echo "<td>". $row["DateRented"]. "</td>";
				echo "<td>"; 
                if ($row["DateReturned"] == '1900-01-01'){
                    echo 'Currently Rented</td>';
                }
                else{
                    echo $row["DateRented"]."</td>";
                }
			}
			if($type =="WatchList")
			{
				echo 
					"<td>
						<form method='POST' action='moviesList.php'>
							<input type='hidden' name='type' value='WatchList'></input>
							<input type='hidden' name='value' value='$MovieID'></input>
							<input type='hidden' name='remove' value='remove'></input>
							<input value='Remove From My Watchlist' type='submit'>
						</form>
					</td>";
			}
			echo "</tr>";
		}	
		echo "</table></div></div>";
	}
	else
	{
		echo "<div class='row'>
                    <div class='col-md-4 col-md-offset-1'>
                        <h4>No Results Found!</h4>
                    </div>
                </div>";
	}
	//echo $sql;
		
	$conn->close();
?>
