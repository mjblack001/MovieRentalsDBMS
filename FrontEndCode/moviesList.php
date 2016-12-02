<?php 
	include("setup.php");
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	if(isset($_POST['type']) && isset($_POST['value']))
	{
		$type = $_POST['type'];
		$value = $_POST['value'];
		
		
		$sql='';
		$valueCheck = "%". $value. "%";
		
		switch ($type)
		{
			case("ActorName"):
				$sql = "SELECT Movie.MovieName, Movie.ReleaseDate, Actor.ActorName, AVG(Rating.Rating)
						FROM Movie, ActedIn, Actor, Rating
						WHERE Movie.MovieID = ActedIn.MovieID
						AND	Movie.MovieID = Rating.MovieID
						AND ActedIn.ActorID = Actor.ActorID
						AND Actor.ActorName LIKE '$valueCheck'
						GROUP BY MovieName";
				break;
			case("DirectorName"):
				$sql = "SELECT Movie.MovieName, Movie.ReleaseDate, Director.DirectorName, AVG(Rating.Rating)
						FROM Movie, Director, Rating
						WHERE Movie.DirectorID = Director.DirectorID
						AND	Movie.MovieID = Rating.MovieID
						AND Director.DirectorName LIKE '$valueCheck'
						GROUP BY MovieName";
					
				break;
			case("GenreName"):
				$sql = "SELECT Movie.MovieName, Movie.ReleaseDate, Genre.GenreName, AVG(Rating.Rating)
						FROM Movie, Genre, Rating
						WHERE Movie.GenreID = Genre.GenreID
						AND	Movie.MovieID = Rating.MovieID
						AND Genre.GenreName LIKE '$valueCheck'
						GROUP BY MovieName";
				break;
			case("MovieName"):
				$sql = "SELECT Movie.MovieName, Movie.ReleaseDate, AVG(Rating.Rating)
						FROM Movie, Rating
						WHERE Movie.MovieName LIKE '$valueCheck'
						AND	Movie.MovieID = Rating.MovieID
						GROUP BY MovieName";
				break;
			default: $sql = "SELECT MovieName, ReleaseDate FROM movie";	
		}
		
		if(isset($_POST['SortType']))
		{
			$SortType = $_POST['SortType'];
			$sql = $sql. " ORDER BY $SortType";
			echo "</br>". $sql. "; ". $conn->error. "</br>";
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
							</form>";
					
			if($type =="ActorName") echo"<th>Actor Name</th>";
			if($type =="GenreName") echo"<th>Genre Name</th>";
			if($type =="DirectorName") echo"<th>Director Name</th>";
			echo"</tr>";
			
			while($row = $result->fetch_assoc()) 
			{
				echo "<tr> <td>" . $row["MovieName"]. "</td> <td>" . $row["ReleaseDate"]. "</td> <td>". $row["AVG(Rating.Rating)"]. "</td>";
				if($type =="ActorName")		echo "<td>". $row["ActorName"]. "</td>";
				if($type =="GenreName") 		echo "<td>". $row["GenreName"]. "</td>";
				if($type =="DirectorName") 	echo "<td>". $row["DirectorName"]. "</td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		else
		{
			echo "</br>Error: ". $sql. "<br>". $conn->error;
		}
	}
	else
		echo"No Post values";
		
	$conn->close();
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
			value: <input  name="value" type="text"></br>
			<input type="submit">
		</form>
	</body>
</html>
