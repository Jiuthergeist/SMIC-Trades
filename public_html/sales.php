<?php
	require_once("../includes/functions.php");
	require_once("../includes/session.php");
	include("../includes/header.php");
	
	check_login(); //makes sure people cannot skip to this page without logging in
	delete_offer(); //if the user clicks on "delete offer," this function will run
	
	$user_id = $_SESSION["id"];
?>
<div id="page" style="font-size:16px; font-family: Verdana;">
	<h1 class="h1_spacing">My Books to Sell:</h1>
	<?php		
		$query = "SELECT * "; //know which books the user is selling
		$query .= "FROM sell ";
		$query .= "WHERE seller_id = {$user_id} ";
		$query .= "ORDER BY id ASC";
		$sell_set = mysqli_query($connection, $query);

		while ($sell_order = mysqli_fetch_assoc($sell_set))
		{
			$sold_to = $sell_order["sold_to"];
			$output = print_book_details_for_sales($sell_order);
			if ($sold_to == 0)
			{
				$output .= "<form action=\"sales.php?id="; //create a delete button that corresponds to the id in the "sell" database
				$output .= urlencode($sell_order["id"]);
				$output .= "\" method=\"post\">";
				$output .= "<input type=\"submit\" name=\"delete\" value=";
				$output .= "\"Cancel Offer\">";
				$output .= "</form>";
			}
			else
			{
				$query = "SELECT * ";
				$query .= "FROM users ";
				$query .= "WHERE id = {$sold_to} ";
				$query .= "LIMIT 1";
				$user_set = mysqli_query($connection, $query);
				
				while ($user = mysqli_fetch_assoc($user_set))
				{
					$output .= "<br />&nbsp;&nbsp;&nbsp;Sold to: ";
					$output .= $user["username"];
					$output .= " (Grade ";
					$output .= $user["grade"];
					$output .= ")";
				}
			}
			$output .= "<br /><br />";
			echo $output;
			
		}		
	?>
	<h1 class="h1_spacing">Purchased Books:</h1>
	<br />
	<?php		
		$query = "SELECT * ";
		$query .= "FROM buy ";
		$query .= "WHERE buyer_id = {$user_id}";
		
		$buy_set = mysqli_query($connection, $query);
		
		while ($buy_order = mysqli_fetch_assoc($buy_set)) //pull all the books that the user bought
		{
			$sell_id = $buy_order["sell_id"];
			
			$query = "SELECT * ";
			$query .= "FROM sell ";
			$query .= "WHERE id = {$sell_id}";
			$sell_set = mysqli_query($connection, $query);
			
			
			while ($sell_order = mysqli_fetch_assoc($sell_set))
			{
				$output = "";
				$sold_to = $sell_order["sold_to"];
				if ($sold_to != 0)
				{
					$output .= "[SOLD TO ";
					if ($user_id == $sold_to)
						$output .= "YOU";
					else $output .= "SOMEONE ELSE";
					$output .= "] ";
				}
				$output .= print_book_details_for_sales($sell_order); //print some book details
			
				$seller_id = $sell_order["seller_id"];
			
				$query = "SELECT * ";
				$query .= "FROM users ";
				$query .= "WHERE id = {$seller_id} ";
				$query .= "LIMIT 1";
				$user_set = mysqli_query($connection, $query);
			
				while ($user = mysqli_fetch_assoc($user_set))
					$output .= "Seller: " . $user["username"] . ", Grade " . $user["grade"];
				
				$output .= "<br /><br />";
				echo $output;
				
			}
		}	
	?>
</div>
</body>
</html>
<?php
	ob_end_flush();
?>
