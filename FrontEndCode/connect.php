<?php 
	include("setup.php");
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	//var_dump($_POST);
		
			
	if(isset($_POST['email']))
	{
		$username = $_POST['email'];
		$password = $_POST['password'];
		$address = $_POST['address'];
		
		//SELECT
		$sql = "SELECT Email, Password FROM user WHERE Email = '$username' AND Password = '$password'";
		$result = $conn->query($sql);
		
		
		if($result->num_rows > 0)
		{
			echo "Welcome, ". $username. "!";
		}
		else
		{
			echo "User not found. Create New User? </br>";
			
			$sql = "SELECT UserID FROM user ORDER BY UserID DESC LIMIT 1";
			$result = $conn->query($sql);
			$IDCheck = $result->fetch_assoc();
			$newID = $IDCheck['UserID'];
			$newID++;
			
			$sql = "INSERT INTO user (Email, Password, Address, UserID) VALUES ('$username', '$password', '$address', '$newID')";
			if($conn->query($sql) === TRUE)
			{
				echo "User added";
			}
			else
			{
				echo "Error: ". $sql. "<br>". $conn->error;
			}
		}
	}
	else
		echo "Error: No username received!";
	$conn->close();


?>


<html>
	<head>
		<title>Sign In/Up!</title>
		<link rel='stylesheet' type='text/css' href='DBFrontEndStyle.css'></link>
	<head>
		
	
</html>