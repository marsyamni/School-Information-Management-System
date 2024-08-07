<html>
<body>
<?php

//Retrieve data
$username=$_POST["username"];
$password=$_POST["password"];

//Start the $sql, connect to database to search for the value stored in the database
$con = mysqli_connect("localhost","root","","fyp");

//Execute SQL
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($con, $sql);

//2. search every row whether the data entered is same as data as stored
if(mysqli_num_rows($result) == 0) 
{
	echo "<script>alert('Username does not exist!');</script>";
}
else
{ //3. To link the variable from the $rows with $result
	$row = mysqli_fetch_array($result, MYSQLI_BOTH); 
	
	//4. Search the password for the respective username in $result
	if ($row ["password"] == $password) 
	{
		$_SESSION["username"] = $username;
		// Redirect to different landing pages based on user type
    if ($row["user_type"] == "staff") {
      header("Location: admin_index.php");
    } elseif ($row["user_type"] == "parent") {
      header("Location: parent_index.html");
    } elseif ($row["user_type"] == "teacher") {
      header("Location: teacher_index.html");
    } else {
      echo "<script>alert('Invalid user type!');</script>";
    }
	}
	else
	{
		echo "<script>alert('Login failed!');</script>";
	}
}
// Close connection
mysqli_close($con);
?>
</body>
</html>