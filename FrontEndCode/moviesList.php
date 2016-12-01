<?php 
	include("setup.php");
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	//SELECT
	$sql = "SELECT * FROM movies";
	$result = $conn->query($sql);

	if($result->num_rows > 0)
	{
		echo "
			<table>
			<tr>
				<th>MovieName</th>
				<th>ReleaseDate</th>
			</tr>";
		while($row = $result->fetch_assoc()) 
		{
				echo "<tr> <td>" . $row["MovieID"]. "</td> <td>" . $row["Name"]. "</td> </tr>";
		}
		echo "</table>";
	}
	else
		echo "No Results Found";

	$conn->close();
?>
<html>
	<head>
		<title>Movies Page</title>
		<link rel='stylesheet' type='text/css' href='DBFrontEndStyle.css'></link>
	<head>
		

</html>