<?php
	require_once("../includes/functions.php");
	require_once("../includes/session.php");
	include("../includes/header.php");
	
	check_login();
	
	$user_id = $_SESSION["id"];
	
	$username = ""; //variables to be used more easily if the user edits
	$password = "";
	$grade = 0;
	$email = "";
	$hash = "";
	
?>
<div id="page" style="font-size:16px; font-family: Verdana;">
<br />
<h1>Profile</h1>
<?php

	$query = "SELECT * ";
	$query .= "FROM users ";
	$query .= "WHERE id = {$user_id} ";
	$query .= "LIMIT 1";
	$user_set = mysqli_query($connection, $query);
	
	$output = "";
	if ($_SESSION["messages"] != "")
		$output .= $_SESSION["messages"] . "<br /><br />";
	$_SESSION["messages"] = "";

	while ($user = mysqli_fetch_assoc($user_set))
	{
		$username = $user["username"];
		$password = $user["password"];
		$grade = $user["grade"];
		$email = $user["email"];
		$hash = $user["hash"];
		
		$output .= "Name: ";
		$output .= $username;
		$output .= "<br /><br />Password: ";
		$password_length = strlen($password);
		for ($i = 1; $i <= $password_length; $i++)
			$output .= "*";
		$output .= "<br /><br />Grade Level: ";
		$output .= $grade;
		$output .= "<br /><br />Email: ";
		$output .= $email;
	}
	$output .= "<br /><br /><form action=\"profile.php\" method=\"post\">";
	//if (mysqli_real_escape_string($connection, $_GET["edit"]) != 1) 
		$output .= "<input class=\"big\" type=\"submit\" name=\"edit\" value=\"Edit\"></form>";
	echo $output;
	echo "<br /><br /><br /><br /><br />";
	

	if (isset($_POST["edit"]))
	{
		$output = "An empty field will automatically assume no changes to your current information.<br /><br />";
		$output .= "<form action=\"profile.php\" method=\"post\">";
		$output .= "Name:<br /><input type=\"text\" name=\"username\" value=\"";
		$output .= $username; 
		$output .= "\"/> <br /><br />";
		$output .= "Old Password:<br /><input type = \"password\" name=\"old_password\" value=\"\" /><br /><br />";
		$output .= "New Password:<br /><input type = \"password\" name=\"password\" value=\"\" /><br /><br />";
		$output .= "Confirm New Password:<br /><input type = \"password\" name=\"confirm_password\" value=\"\" /><br /><br />";
		$output .= "Grade Level:<br /><select name=\"grade\"";
		for($count = 5; $count <= 12; $count++) //strangely I'm testing this and $count = 5 to start.. should start with 6 but then the output is wrong
		{
			$output .= "<option value=\"{$count}\"";
			if ($count == $grade)
			 	$output .= " selected";
			$output .= ">{$count}</option>";
		}
		$output .= "</select><br /><br />";
		$output .= "Email:<br /><input type = \"text\" name = \"email\" value = \"";
		$output .= $email;
		$output .= "\" /><br /><br />";
		$output .= "<input class=\"big\" type=\"submit\" name=\"change\" value=\"Save\"><br /><br />";
		echo $output;
	}
	
	
	if (isset($_POST["change"]))
	{ //isset
		$old_password = $_POST["old_password"];
		$new_password = $_POST["password"];
		$confirm_password = $_POST["confirm_password"];
		
		$password_ok = 0; //to see if the password change is ok

		
		//if there is a need to check password input
		if(!($old_password == "" && $new_password == "" & $confirm_password == ""))			
		{	
			//old password is wrong
			if (crypt($old_password, $hash) != $hash){
				echo "You must enter your old password correctly before changing it to a new one.";
			}	

	
			//confirmed password is wrong
			else if ($new_password != $confirm_password){
				echo "Your confirmation failed to match your new password.";
			}
		
			//$new_password = $password; //no changes
			else
			{
			$hash = encrypt($new_password);
			$password = $new_password;
			$password_ok = 1;
			}
		}//big if
		else $password_ok = 1;
		//<p style="font-size:100%; color:blue;" 
		//changing username
		if (trim(htmlspecialchars($_POST["username"])) != ""){
			$new_username = trim(htmlspecialchars($_POST["username"]));
		}
		else $new_username = $username;
		
		$new_grade = $_POST["grade"];
		
		if (trim($_POST["email"]) != "")
			$new_email = trim($_POST["email"]);
		else $new_email = $email;				
		
			$query = "UPDATE users ";
			$query .= "SET username = '{$new_username}', ";
			$query .= "    password = '{$password}', ";
			$query .= "    grade = {$new_grade}, ";
			$query .= "    email = '{$new_email}', ";
			$query .= "    hash = '{$hash}' ";
			$query .= "WHERE id = {$user_id}";
			$update = mysqli_query($connection, $query);
			if ($password_ok)
			{
				$_SESSION["messages"] = "Your changes have been saved.";
				redirect_to("profile.php");
			}
	} //end isset
	
?>


</div>
</body>
</html>
<?php
	ob_end_flush();
?>
