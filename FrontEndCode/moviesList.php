<?php
    session_start();
?>
<html>
	<head>
		<title>Movies Page</title>
		<link rel='stylesheet' type='text/css' href='DBFrontEndStyle.css'></link>
	<head>
	<body>
		<form method="POST" action="moviesList.php">
			<select name="type">
				<option value="MovieName">Movie</option>
				<option value="ActorName">Actor</option>
				<option value="DirectorName">Director</option>
				<option value="GenreName">Genre</option>
			</select></br>
			Search: <input  name="value" type="text"></br>
			<input type="submit">
		</form>
	</body>
</html>

<?php 
	include("setup.php");
	//$_SESSION['UserID'] = '3'; //TEST SESSION VAR
 
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	$type;
	$value;
	$uid = $_SESSION['UserID'];
		
	if(isset($_POST['type']) && isset($_POST['value']))
	{
		$type = $_POST['type'];
		$value = $_POST['value'];
	}
	else
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
		default: $sql = "SELECT MovieName, ReleaseDate, MovieID FROM movie";	
	}
	
	if(isset($_POST['SortType']))
	{
		$SortType = $_POST['SortType'];
		$sql = $sql. " ORDER BY $SortType";
		//echo "</br>". $sql. "; ". $conn->error. "</br>";
	}
	$result = $conn->query($sql);
	
	if($result->num_rows > 0)
	{
		echo "
			<table>
			<tr>
				<th>Movie
						<form method='POST' action='moviesList.php'>
						<select name='SortType'>
						<option value='MovieName ASC'>Movie Name ASC</option>
						<option value='MovieName DESC'>Movie Name DESC</option>
						<input type='hidden' name='type' value ='$type'></input>
						<input type='hidden' name='value' value ='$value'></input>
						<input type='submit'>
						</form>
				</th>
				<th>Release Date
						<form method='POST' action='moviesList.php'>
						<select name='SortType'>
						<option value='ReleaseDate ASC'>Release Date ASC</option>
						<option value='ReleaseDate DESC'>Release Date DESC</option>
						<input type='hidden' name='type' value ='$type'></input>
						<input type='hidden' name='value' value ='$value'></input>
						<input type='submit'>
						</form>
				</th>
					<th>Rating
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
				echo 	"<th>Date Rented
						<form method='POST' action='moviesList.php'>
							<select name='SortType'>
							<option value='DateRented ASC'>Date Rented ASC</option>
							<option value='DateRented DESC'>Date Rented DESC</option>
							<input type='hidden' name='type' value ='$type'></input>
							<input type='hidden' name='value' value ='$value'></input>
							<input type='submit'>
						</form></th>";
				echo "<th>Date Returned
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
				echo "<td>". $row["DateReturned"]. "</td>";
			}
			echo "</tr>";
		}	
		echo "</table>";
	}
	else
	{
		echo "</br>Error: ". $sql. "<br>". $conn->error;
	}
	//echo $sql;
		
	$conn->close();
?>
