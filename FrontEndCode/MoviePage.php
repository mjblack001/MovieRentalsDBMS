
<html>
	<head>
        <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Movie Page</title>
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
	</head>


<?php
    session_start();
    $_SESSION['UserID'];
	include("setup.php");
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	
	$MovieID = $_GET['MovieID'];
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
            <div class="col-md-1">
                <input name="update" class="btn btn-default" type="submit" value="Search"><br/>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-md-offset-1">
<?php
	//MOVIE INFO 
	
	$sql = "SELECT Movie.MovieName, Movie.ReleaseDate, AVG(Rating.Rating), Movie.Poster, Genre.GenreName, Director.DirectorName
			FROM Movie, rating, director, genre
			WHERE Movie.MovieID = '$MovieID'
			AND Movie.GenreID = Genre.GenreID
			AND Movie.DirectorID = Director.DirectorID
			AND Movie.MovieID = Rating.MovieID";
	
	$result = $conn->query($sql);
    
    if($result->num_rows > 0)
	{
		while($row = $result->fetch_assoc())
		{
			$img_src = $row['Poster'];
			echo 	"<h3>" . $row["MovieName"]. 
					"</h3></div></div>
                    <div class='row'><div class='col-md-1 col-md-offset-1'><img src= ". $img_src. ">".
					"</div>";
                    $releasedate = $row["ReleaseDate"];
					$avg = ROUND($row["AVG(Rating.Rating)"],2);
					$genre = $row["GenreName"];
					$director = $row["DirectorName"];
		}
	}
    
    
    
    if(isset($_POST['watchlist']))
    {
        $sql = "INSERT INTO WatchList (UserID, MovieID) VALUES (".$_SESSION['UserID'].", '.$MovieID.')";
        $result = $conn->query($sql);
        if($result === TRUE)
			{
				header('location: moviesList.php?type=WatchList');
			}
			else
			{
				echo "Error: ". $sql. "<br>". $conn->error;
			}
    }
    else if(isset($_POST['rent']))
    {
        $sql = "INSERT INTO RentalHistory (UserID, MovieID, DateRented, DateReturned) VALUES (".$_SESSION['UserID'].", ".$MovieID.", CURRENT_DATE(), '1900-01-01')";
        $result = $conn->query($sql);
        if($result === TRUE)
			{
				header('location: moviesList.php?type=RentalHistory');
			}
			else
			{
				echo "Error: ". $sql. "<br>". $conn->error;
			}
    }
    if(isset($_POST['rated']))
    {
        $sql = "SELECT RatingID FROM Rating ORDER BY RatingID DESC LIMIT 1";
			$result = $conn->query($sql);
			$IDCheck = $result->fetch_assoc();
			$newID = $IDCheck['RatingID'];
			$newID++;
        $rating = $_POST['rating'];
        $comment = $_POST['comment'];
        $sql = "INSERT INTO Rating (RatingID, Rating, Comment, UserID, MovieID) VALUES (".$newID.", ".$rating.", '".$comment."', ".$_SESSION['UserID'].", ".$MovieID.")";
        $result = $conn->query($sql);
        if($result === TRUE)
			{
				
			}
			else
			{
				echo "Error: ". $sql. "<br>". $conn->error;
			}
    }
	
	//Add to my watch list
	echo "<div class='col-md-5 col-md-offset-1'>
          <h4>Director: <small>".$director."</small></h4>";
	//ACTOR INFO
	$sql = "SELECT Movie.MovieName, Actor.ActorName
			FROM Movie, Actor, ActedIn
			WHERE Movie.MovieID = '$MovieID'
			AND ActedIn.ActorID = Actor.ActorID
			AND ActedIn.MovieID = Movie.MovieID";
	
	$result = $conn->query($sql);
	
	if($result->num_rows > 0)
	{
		echo "<h4>Actors: <small></br>";
		while($row = $result->fetch_assoc())
		{
			echo $row["ActorName"]."</br>";
		}
	}
	else
	{
		echo "Error: ". $sql. "<br>". $conn->error;
	}
	
	echo "</small></h4>
            <h4>Released: <small>".$releasedate."</small></h4>
            <h4>Genre: <small>".$genre."</small></h4>
            <h4>Average Rating: <small>".$avg."/5</small></h4>";
        
    echo '<form method="POST" action="MoviePage.php?MovieID='.$MovieID.'">
                <input name="watchlist" type="submit" value="Add to Watchlist"></input>
          </form>
          <form method="POST" action="MoviePage.php?MovieID='.$MovieID.'">
                <input name="rent" type="submit" value="Rent This Movie"></input>
          </form></div></div></br>';
    
    echo"<div class='row'><div class='col-md-10 col-md-offset-1'><h3>Rate this Movie</h3>";
        
    echo "<form method='POST' action='MoviePage.php?MovieID=".$MovieID."'>
				<h5>Rating: <input name='rating' type='text'>/5</h5>
				<h5>Comments:</h5><textarea  name='comment' cols='100' rows='5' placeholder='Optional'></textarea></br></br/>
				<input name='rated' type='submit' class='btn btn-default'>
				</form></div></div>";
	
	//RATING INFO
	$sql = "SELECT Movie.MovieName, Rating.Rating, Rating.Comment, User.Email
			FROM Movie, Rating, User
			WHERE Movie.MovieID = '$MovieID'
			AND Rating.UserID = User.UserID
			AND Rating.MovieID = Movie.MovieID";
	$result = $conn->query($sql);
	
    
        
	if($result->num_rows > 0)
	{
        echo "<div class='row'><div class='col-md-10 col-md-offset-1'><h3>User Ratings</h3>";		
        while($row = $result->fetch_assoc())
		{
			echo "<strong>User:</strong> ".$row['Email']."<br/>
                  <strong>Rating:</strong> ".$row['Rating']."/5<br/>";
            if($row['Comment'] != "")
            {
                echo "<strong>Comments:</strong> ".$row['Comment']."<br/>";
            }
            echo "<br/>";
		}
	}
	else
	{
		echo "Error: ". $sql. "<br>". $conn->error;
	}
   
    
	$conn->close();
?> 
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
</html>
