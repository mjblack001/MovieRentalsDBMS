<?php
	session_start();
?>
<html>
	<head>
		<title>Sign In/Up!</title>
		<link rel='stylesheet' type='text/css' href='DBFrontEndStyle.css'></link>
	<head>
</html>
<?php 
	include("setup.php");
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	//var_dump($_POST);
	if(isset($_POST['login']))
	{
		$username = $_POST['email'];
		$password = $_POST['password'];
		
		//SELECT
		$sql = "SELECT Email, Password, UserID FROM user WHERE Email = '$username' AND Password = '$password'";
		$result = $conn->query($sql);
		
		if($result->num_rows > 0)
		{
			echo "Welcome, ". $username. "!<br/>";
			$row = $result->fetch_assoc();
			$_SESSION['UserID'] = $row['UserID'];
			//header('location: DBFrontEndStyle.css');
		}
		else
			echo "User not found. Create New User? </br>
					<form method='POST' action='connect.php'>
						Email: <input name='email' type='text' ></br>
						Password: <input  name='password' type='text'></br>
						Address: <input  name='address' type='text'></br>
						<input name='register' value='Sign Up' type='submit'>
					</form>";
	}
	else if(isset($_POST['register']))
	{
		if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['address']))		
		{	
			
			$username = $_POST['email'];
			$password = $_POST['password'];
			$address = $_POST['address'];
			
			$sql = "SELECT UserID FROM user ORDER BY UserID DESC LIMIT 1";
			$result = $conn->query($sql);
			$IDCheck = $result->fetch_assoc();
			$newID = $IDCheck['UserID'];
			$newID++;
			$_SESSION['UserID'] = $newID;
			$sql = "INSERT INTO user (Email, Password, Address, UserID) VALUES ('$username', '$password', '$address', '$newID')";
			if($conn->query($sql) === TRUE)
			{
				//header('location: DBFrontEndStyle.css');
				echo "User Added";
			}
			else
			{
				echo "Error: ". $sql. "<br>". $conn->error;
			}
		}
		else 
			echo
				"<form method='POST' action='connect.php'>
					Email: <input name='email' type='text' ></br>
					Password: <input  name='password' type='text'></br>
					Address: <input  name='address' type='text'></br>
					<input name='register' value='Sign Up' type='submit'>
				</form>";
		
	}
	echo "Session UserID: ". $_SESSION['UserID'];
	$conn->close();
?>