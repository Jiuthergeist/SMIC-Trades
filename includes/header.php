<?php
	ob_start();
	require_once("../includes/functions.php");
	require_once("../includes/session.php");


	db_connection(); /*did books.php worth of db connection here*/
	reset_cookie();//when the user clicks into something, the cookie is reset, and the user has one hour before auto-logout
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<title>Sharks Book Trading</title>		
	<link href="../includes/main_style.css" media="all" rel="stylesheet" type="text/css" />

	</head>
	<body>
	
		<navv id="header">
			<a href="index.php"><h1>SMIC Trades</h1></a>

		<ul class="header_line">
			<top class="hover">  
				<div class="no_underline">
					<br /><form action="index.php" method="post">
					<b>ISBN: </b>
					<input type="text" name="isbn" value="" />&nbsp;
					<input type="submit" name="search" value="Search" /><br />
				</form><br />
				<font color="#FFFFFF" size="1">
				<?php echo isbn_search(); //for searching the isbn ?>
			</font>
			</div>
		
					<a href="index.php" class="no_underline">
						
	<li class="dropdown"> 
						Books
					</a>
			</top>
		
		<ul class="dropdownmenu">
			<?php 
			/*db_connection(); /*access mysql*/
			/*category_dropdownlist() = dropdown menu*/
			 echo category_dropdownlist(); //produces the dropdown list under "Books" when the mouse hovers over it
			?>
			</ul>
			
	</li>
		
		
		
		<top class="hover"> 
		
	<!--other login buttons-->
		<?php
			if (is_logged_in())
			{
				$output = "<a href=\"profile.php\" class=\"no_underline\">";
				$output .= "<li class=\"dropdown\">";
				$output .= "Me";
				if (unread_message($_SESSION["id"]) == 1)
					$output .= "*";
				echo $output;
			}
		?>
		</a></top>
		
		
		<ul class="dropdownmenu">
		<?php
			if(is_logged_in()) {
			//echo $_SESSION["login"];
			//available buttons if the user is logged in
			echo "<a href=\"profile.php\"><li>Profile</li></a>";
			echo "<a href=\"sales.php\"><li>Sales & Purchases</li></a>";
			echo "<a href=\"messages.php\"><li>Messages";
			if (unread_message($_SESSION["id"]) == 1)
				echo "*";
			echo "</li></a>";
			}
		?>
		</ul></li>
		<top class="hover">
			
		<!--login logout buttons-->
		<?php
		$output = "";
			if (is_logged_in())
				$output .= "<a href=\"logout.php\" class=\"no_underline\"><li>Logout</li></a>";
			else
			{
				//echo "<top>";
				$output .= "<a href=\"login.php\" class=\"no_underline\">"; 
				$output .= "<li class=\"dropdown\">";
				$output .= "Login";
				$output .= "</a></top>";
				$output .= "<ul class=\"dropdownmenu\">";
				$output .= "<a href=\"signup.php\"><li>Register</li></a>";
				$output .= "</ul></li>";
			}
		echo $output;
		/* quick login bar: future development
		$log ="Name: <input type=\"text\" name=\"username\" value=\"";
		if(isset($_SESSION["username"]))
		$log.=htmlspecialchars($_SESSION["username"]);

		$log.="\"/>";

		$log.="<br />Password:<input type=\"password\" name=\"password\" value=\"\" />";
		$log.="<input type=\"submit\" name=\"submit\" value=\"Quick login\"/>";
		echo $log;
*/
		?>
		</top>
</ul>
	</navv>