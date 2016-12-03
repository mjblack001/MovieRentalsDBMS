
<html>
	<head>
		<title>Movie Page</title>
		<link rel='stylesheet' type='text/css' href='DBFrontEndStyle.css'></link>
	<head>
</html>

<?php
    session_start();
    $_SESSION['UserID'];
	include("setup.php");
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	
	$MovieID = $_GET['MovieID'];

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
			echo 	"Movie Name: " . $row["MovieName"]. 
					"</br>Release Date: ". $row["ReleaseDate"].
					"</br>Average Rating: ". ROUND($row["AVG(Rating.Rating)"],2). "/5".
					"</br>Genre: ". $row["GenreName"].
					"</br>Director: ". $row["DirectorName"].
					"</br>Poster: </br><img src= ". $img_src. ">".
					"</br>";
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
        $sql = "INSERT INTO RentalHistory (UserID, MovieID, DateRented, DateReturned) VALUES (".$_SESSION['UserID'].", '.$MovieID.', CURRENT_DATE(), '1900-01-01')";
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
	
	echo"</br>";
	//Add to my watch list
	
	echo "</br>";
	//ACTOR INFO
	$sql = "SELECT Movie.MovieName, Actor.ActorName
			FROM Movie, Actor, ActedIn
			WHERE Movie.MovieID = '$MovieID'
			AND ActedIn.ActorID = Actor.ActorID
			AND ActedIn.MovieID = Movie.MovieID";
	
	$result = $conn->query($sql);
	
	if($result->num_rows > 0)
	{
		echo "Actors: </br>";
		while($row = $result->fetch_assoc())
		{
			echo $row["ActorName"]."</br>";
		}
	}
	else
	{
		echo "Error: ". $sql. "<br>". $conn->error;
	}
	
	echo "</br>";
	
	//RATING INFO
	$sql = "SELECT Movie.MovieName, Rating.Rating, Rating.Comment, User.Email
			FROM Movie, Rating, User
			WHERE Movie.MovieID = '$MovieID'
			AND Rating.UserID = User.UserID
			AND Rating.MovieID = Movie.MovieID";
	$result = $conn->query($sql);
	
	if($result->num_rows > 0)
	{
		echo "</br>Ratings: </br> <table> <th>User</th><th>Rating</th><th>Comment</th>";
		while($row = $result->fetch_assoc())
		{
			echo "<tr><td>". $row["Email"]."</td><td>". $row["Rating"]."/5</td> <td>". $row["Comment"]."</td></tr>";
		}
		echo "</table>";
	}
	else
	{
		echo "Error: ". $sql. "<br>". $conn->error;
	}
    
    echo '<form method="POST" action="MoviePage.php?MovieID='.$MovieID.'">';
    echo '<input name="watchlist" type="submit" value="Add to Watchlist"></input><br/></form>';
    echo '<form method="POST" action="MoviePage.php?MovieID='.$MovieID.'">';
    echo '<input name="rent" type="submit" value="Rent This Movie"></input><br/></form>';
    
	$conn->close();
?>
