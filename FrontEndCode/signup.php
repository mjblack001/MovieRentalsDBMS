<?php
	session_start();
?>
<html>
	<head>
        <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Welcome to The Movies Database!</title>
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
<html>
<?php 
	include("setup.php");
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	//var_dump($_POST);
	else if(isset($_POST['login']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		//SELECT
		$sql = "SELECT Username, Password, UserID FROM user WHERE Username = '$username' AND Password = '$password'";
		$result = $conn->query($sql);
		
		if($result->num_rows > 0)
		{
			echo "Welcome, ". $username. "!<br/>";
			$row = $result->fetch_assoc();
			$_SESSION['UserID'] = $row['UserID'];
			header('location: userpage.php');
		}
		else
			echo "<div class='alert alert-danger' role='alert'>Login Failed.  Please Try Again</div>
					<br/><form method='POST' action='signup.php' class='form-horizontal'>
                <div class='row'>
                    <div class='col-md-1 col-md-offset-4'>
                        <strong>Username:</strong>
                    </div>
                    <div class='col-md-2'>
                        <input name='username' type='text' class='form-control'>
                    </div>
                </div>
                <br/>
                <div class='row'>
                    <div class='col-md-1 col-md-offset-4'>
				        <strong>Password:</strong>
                    </div>
                    <div class='col-md-2'>
                        <input  name='password' type='password' class='form-control'>
                    </div>
                </div>
                <br/>
                <div class='row'>
                    <div class='col-md-3 col-md-offset-4'>
                        <input name='login' value='Sign In' type='submit' class='form-control'>
                    </div>
                </div>
                <br/>
                <div class='row'>
                    <div class='col-md-3 col-md-offset-4'>
				        <input name='register' value='Create An Account' type='submit' class='form-control'>
                    </div>
                </div>
            </form>";
	}
	else if(isset($_POST['register']))
	{
		if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['address']))		
		{	
			
			$username = $_POST['username'];
			$password = $_POST['password'];
			$address = $_POST['address'];
			
			$sql = "SELECT UserID FROM user ORDER BY UserID DESC LIMIT 1";
			$result = $conn->query($sql);
			$IDCheck = $result->fetch_assoc();
			$newID = $IDCheck['UserID'];
			$newID++;
			$_SESSION['UserID'] = $newID;
			$sql = "INSERT INTO user (Username, Password, Address, UserID) VALUES ('$username', '$password', '$address', '$newID')";
			if($conn->query($sql) === TRUE)
			{
				header('location: userpage.php');
			}
			else
			{
				echo "Error: ". $sql. "<br>". $conn->error;
			}
		}
		else 
			echo
				"<br/><form method='POST' action='signup.php' class='form-horizontal'>
                <div class='row'>
                    <div class='col-md-1 col-md-offset-4'>
                        <strong>Username:</strong>
                    </div>
                    <div class='col-md-2'>
                        <input name='username' type='text' class='form-control'>
                    </div>
                </div>
                <br/>
                <div class='row'>
                    <div class='col-md-1 col-md-offset-4'>
				        <strong>Password:</strong>
                    </div>
                    <div class='col-md-2'>
                        <input  name='password' type='password' class='form-control'>
                    </div>
                </div>
                <br/>
                <div class='row'>
                    <div class='col-md-1 col-md-offset-4'>
				        <strong>Address:</strong>
                    </div>
                    <div class='col-md-2'>
                        <input  name='address' type='text' class='form-control'>
                    </div>
                </div>
                <br/>
				<div class='row'>
                    <div class='col-md-3 col-md-offset-4'>
				        <input name='register' value='Create An Account' type='submit' class='form-control'>
                    </div>
                </div>
            </form>";
		
	}
	else{
        if(isset($_GET['logout'])){
            $logout = $_GET['logout'];
            if ($logout == 'true')
			{
				session_unset(); 
				session_destroy(); 
            }
        echo '<div class="alert alert-warning" role="alert">You have been logged out</div>';
        }
		echo "<br/><form method='POST' action='signup.php' class='form-horizontal'>
                <div class='row'>
                    <div class='col-md-1 col-md-offset-4'>
                        <strong>Username:</strong>
                    </div>
                    <div class='col-md-2'>
                        <input name='username' type='text' class='form-control'>
                    </div>
                </div>
                <br/>
                <div class='row'>
                    <div class='col-md-1 col-md-offset-4'>
				        <strong>Password:</strong>
                    </div>
                    <div class='col-md-2'>
                        <input  name='password' type='password' class='form-control'>
                    </div>
                </div>
                <br/>
                <div class='row'>
                    <div class='col-md-3 col-md-offset-4'>
                        <input name='login' value='Sign In' type='submit' class='form-control'>
                    </div>
                </div>
                <br/>
                <div class='row'>
                    <div class='col-md-3 col-md-offset-4'>
				        <input name='register' value='Create An Account' type='submit' class='form-control'>
                    </div>
                </div>
            </form>";
        }
		
	$conn->close();
?>
