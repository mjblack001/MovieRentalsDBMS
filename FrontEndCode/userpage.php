<?php
    session_start();
?>
<html>
	<head>
		<link rel='stylesheet' type='text/css' href='DBFrontEndStyle.css'></link>
	<head>
</html>
<?php
    include("setup.php");
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
            $poster = '<img src= '.$row['Poster'].'>';
            $due = $row['DueDate'];
            echo 'You still have <i>'.$movie.'</i> rented <br/>';
            echo $poster.'<br/>';
            echo 'It is due on '.$due;
        }
    }
    else
    {
        echo 'You have no movies currently rented';
    }
   
?>