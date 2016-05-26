<?php
	require_once("../includes/functions.php");
	require_once("../includes/session.php");
	include("../includes/header.php");
	
	check_login(); //makes sure people cannot skip to this page without logging in
	delete_offer(); //if the user clicks on "cancel offer," this function will run
	cancel_purchase(); //if the user clicks on "cancel purchase," this function will run
	edit_price();
	
	$user_id = $_SESSION["id"];
?>
<div id="page" style="font-size:16px; font-family: Verdana;">
	<h1 class="h1_spacing">My Books to Sell:</h1>
	<?php
		$output = "";
		$count = 0;
		
		if ($_SESSION["messages"] != "")
			$output .= $_SESSION["messages"] . "<br /><br />";
		$_SESSION["messages"] = "";
		
		$query = "SELECT * "; //know which books the user is selling
		$query .= "FROM sell ";
		$query .= "WHERE seller_id = {$user_id} ";
		$query .= "ORDER BY id ASC";
		$sell_set = mysqli_query($connection, $query);

		while ($sell_order = mysqli_fetch_assoc($sell_set))
		{
			$count++;
			$sold_to = $sell_order["sold_to"];
			$sell_id = $sell_order["id"];
			
			/*			
			$query = "SELECT * ";
			$query .= "FROM messages ";
			$query .= "WHERE id = {$sell_id} ";
			$query .= "LIMIT 1";
			$message_set = mysqli_query($connection, $query);
			
			while ($message = mysqli_fetch_assoc($message_set))
			{
				$output .= "<a href=\"messages.php?id=";
				$output .= $sell_id;
				$output .= "&action=";
				
				$buyer_id = $message["buyer_id"];
				$seller_id = $message["seller_id"];
				
				if ($buyer_id == $user_id)
					$output .= "buy";
				elseif ($seller_id == $user_id)
					$output .= "sell";
				
				$output .= "\">";
			}
			*/
			$output .= print_book_details_for_sales($sell_order);
			//$output .= "</a>";
			
			if ($sold_to == 0)
			{
				$output .= "<form action=\"sales.php?id="; //create a delete button that corresponds to the id in the "sell" database
				$output .= urlencode($sell_order["id"]);
				$output .= "\" method=\"post\">";
				$output .= "<input type=\"submit\" name=\"delete\" value= \"Cancel Offer\">";
				/*$output .= "<button onclick=\"confirm_cancel_offer()\">Cancel Offer</button>
				<script>
				function confirm_cancel_offer() {
				    if (confirm(\"Are you sure you want to close this offer? Future buyers will not be able to see this book.\"))
				        delete_offer(1);
					else delete_offer(0);
				}
				</script>";
				
				$output .= "onClick=\"retval = window.confirm('Are you sure you want to close this offer? Future buyers will not be able to see this book.'); 
				window.status=(retval)?'Yes, close this offer':'No, I haven\'t sold my book yet.'; \">";
				*/	
				$output .= "</form>";
				
				$output .= "<form action=\"sales.php\" method=\"post\">";
				$output .= "<input type=\"submit\" name=\"edit_price";
				$output .= $count;
				$output .= "\" value=\"Edit Price\"></form>";
				
				if (isset($_POST["edit_price" . "{$count}"]))
				{
					$output .= "<form action=\"sales.php?id="; //create a delete button that corresponds to the id in the "sell" database
					$output .= urlencode($sell_order["id"]);
					$output .= "\" method=\"post\">";
					$output .= "<input type=\"text\" name=\"new_price\" value=\"\">";
					$output .= "<input type=\"submit\" name=\"confirm_price_change\" value=\"Confirm\">";
				}
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
					$output .= ")<br />";
				}
			}
			$output .= "<br />";
		}
		if ($count == 0)
			$output .= "You have not sold any books. <a href=\"index.php\">Sell one now!</a>";
			
		echo $output;		
	?>
	<h1 class="h1_spacing">Purchased Books:</h1>
	<br />
	<?php		
		$count = 0;
		$output = "";
		
		$query = "SELECT * ";
		$query .= "FROM buy ";
		$query .= "WHERE buyer_id = {$user_id}";
		
		$buy_set = mysqli_query($connection, $query);
		
		while ($buy_order = mysqli_fetch_assoc($buy_set)) //pull all the books that the user bought
		{
			$count++;
			$sell_id = $buy_order["sell_id"];
			
			$query = "SELECT * ";
			$query .= "FROM sell ";
			$query .= "WHERE id = {$sell_id}";
			$sell_set = mysqli_query($connection, $query);
			
			
			while ($sell_order = mysqli_fetch_assoc($sell_set))
			{
				$sold_to = $sell_order["sold_to"];
				if ($sold_to != 0)
				{
					$output .= "<b>[SOLD TO ";
					if ($user_id == $sold_to)
						$output .= "YOU";
					else $output .= "SOMEONE ELSE";
					$output .= "]</b>";
				}
				else 
				{
					$output .= "<a href=\"messages.php?id=";
					
					$query = "SELECT * ";
					$query .= "FROM messages ";
					$query .= "WHERE sell_id = {$sell_id} ";
					$query .= "  AND buyer_id = {$user_id} ";
					$query .= "LIMIT 1";
					$message_set = mysqli_query($connection, $query);
					
					while ($message = mysqli_fetch_assoc($message_set))
						$output .= $message["id"];
					
					$output .= "&action=buy\">";
				}
				$output .= print_book_details_for_sales($sell_order); //print some book details
			
				$seller_id = $sell_order["seller_id"];
			
				$query = "SELECT * ";
				$query .= "FROM users ";
				$query .= "WHERE id = {$seller_id} ";
				$query .= "LIMIT 1";
				$user_set = mysqli_query($connection, $query);
			
				while ($user = mysqli_fetch_assoc($user_set))
				{
					$output .= "Seller: " . $user["username"] . ", Grade " . $user["grade"];
					if ($sold_to == 0)
					{
						$output .= "<form action=\"sales.php?id="; //create a delete button that corresponds to the id in the "sell" database
						$output .= urlencode($sell_order["id"]);
						$output .= "&buyer_id=";
						$output .= $user_id;
						$output .= "\" method=\"post\">";
						$output .= "<input type=\"submit\" name=\"cancel\" value= ";
						$output .= "\"Cancel Purchase\">";
						$output .= "</form><br />";
					}
				}
			}
		}	
		if ($count == 0)
			$output .= "You have not purchased any books. <a href=\"index.php\">Purchase one now!</a>";
		$output .= "<br /><br />";
		echo $output;
	?>
</div>
</body>
</html>
<?php
	ob_end_flush();
?>