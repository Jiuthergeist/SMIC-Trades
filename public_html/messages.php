<?php
	require_once("../includes/functions.php");
	require_once("../includes/session.php");
	include("../includes/header.php");
	
	check_login(); //makes sure people cannot skip to this page without logging in
	
	$sell_id = 0; //initialize variables for use later
	$book_id = 0;
	$user_id = $_SESSION["id"]; //user id of the person logged in
	$checking_message = 0; //boolean variable to see if the user is looking at a specific conversation or not
	$action = ""; //store the action (buy or sell) to use when checking_message == 1 and for the final printing of the textfield and button
	if (isset($_GET["id"]))
		$checking_message = 1;
	if (isset($_GET["action"]))
		$action = mysqli_real_escape_string($connection, $_GET["action"]);
	
?>
<div id="page" style="font-size:16px; font-family: Verdana;">
	<?php
		if (!$checking_message) //if the person is simply in the overview of all the conversations he/she is in
		{
			$output = "<h1 class=\"h1_spacing\">Messages from Purchase:</h1>";
			
			$count = 0; //"count" counts the number of purchases the user made
			
			$query = "SELECT * ";
			$query .= "FROM messages ";
			$query .= "WHERE buyer_id = {$user_id} ";
			$purchase_set = mysqli_query($connection, $query);
		
			while ($purchase = mysqli_fetch_assoc($purchase_set))
			{
				$count++;
				$messages_id = $purchase["id"]; //the id under the "messages" database
				//$book_id = $purchase["book_id"];
				$sell_id = $purchase["sell_id"]; //the corresponding id under the "sell" database
			
				for ($i = 5; $i > 0; $i--)
				{
					$speaker_var = "speaker" . $i;
					if ($purchase[$speaker_var] != NULL)
					{
						if ($purchase["message_read"] == 0 && $purchase[$speaker_var] == 's')
						{
							$output .= "***";
						}
						$i = 0;
					}
				}
		
				$output .= "<a href=\"messages.php?id="; //link to the specific conversation
				$output .= urlencode($messages_id);
				//$output .= "&sell=";
				//$output .= urlencode($sell_id);
				$output .= "&action=buy\">"; //the user is a buyer here, so this helps make the conversation remember that this person is the buyer
		
				$query = "SELECT * "; //use this query to pick up on who is selling the book
				$query .= "FROM sell ";
				$query .= "WHERE id = {$sell_id} ";
				//$query .= "WHERE book_id = {$book_id} ";
				//$query .= "AND seller_id != {$user_id}";
				$query .= "ORDER BY sold_to ASC";
				$sell_set = mysqli_query($connection, $query);
		
				while ($sell_order = mysqli_fetch_assoc($sell_set))
				{
					$output .= print_book_details_for_sales($sell_order);
					$output .= "Seller: ";
			
					$seller_id = $sell_order["seller_id"]; //the query earlier is responsible for picking up the id of the user selling this book
			
					$query = "SELECT * "; //then this query can pick up information on the user
					$query .= "FROM users ";
					$query .= "WHERE id = {$seller_id} ";
					$query .= "LIMIT 1";
					$user_set = mysqli_query($connection, $query);

					while ($user = mysqli_fetch_assoc($user_set))
					{
						$output .= $user["username"];
						$output .= ", Grade ";
						$output .= $user["grade"];
					}
				}
				$output .= "</a><br /><br />";
			}
			
			if ($count == 0)
				$output .= "You are currently not purchasing any books. <a href=\"index.php\">Purchase one now!</a>";
			echo $output;

			//this part is the same as above, except things are flipped around because the user logged in is the seller for these books
			$output = "<h1 class=\"h1_spacing\">Messages from Selling:</h1>";
			$count = 0;
			
			$query = "SELECT * ";
			$query .= "FROM messages ";
			$query .= "WHERE seller_id = {$user_id} ";
			$selling_set = mysqli_query($connection, $query);
		
			while ($selling = mysqli_fetch_assoc($selling_set))
			{
				$count++;
				$messages_id = $selling["id"];
				$sell_id = $selling["sell_id"];
				$buyer_id = $selling["buyer_id"];
				//$book_id = $selling["book_id"];
				
				for ($i = 5; $i > 0; $i--)
				{
					$speaker_var = "speaker" . $i;
					if ($selling[$speaker_var] != NULL)
					{
						if ($selling["message_read"] == 0 && $selling[$speaker_var] == 'b')
						{
							$output .= "***";
						}
						$i = 0;
					}
				}
			
				$output .= "<a href=\"messages.php?id=";
				$output .= urlencode($messages_id);
				//$output .= "&sell_id=";
				//$output .= urlencode($sell_id);
				$output .= "&action=sell\">";
			
				
				$query = "SELECT * ";
				$query .= "FROM sell ";
				$query .= "WHERE id = {$sell_id} ";
				//$query .= "WHERE book_id = {$book_id} ";
				//$query .= "AND seller_id = {$user_id}";
				$query .= "ORDER BY sold_to DESC";
				$sell_set = mysqli_query($connection, $query);
			
				while ($sell_order = mysqli_fetch_assoc($sell_set))
				{
				
					$output .= print_book_details_for_sales($sell_order);
					$output .= "Buyer: ";
					
						
					$query = "SELECT * ";
					$query .= "FROM users ";
					$query .= "WHERE id = {$buyer_id} ";
					$query .= "LIMIT 1";
					$user_set = mysqli_query($connection, $query);
					
					while ($user = mysqli_fetch_assoc($user_set))
					{
						$output .= $user["username"];
						$output .= ", Grade ";
						$output .= $user["grade"];
					}
				}
				$output .= "</a><br /><br />";
			}
			if ($count == 0)
				$output .= "You are currently not selling any books. <a href=\"index.php\">Sell one now!</a>";
			echo $output;
		}
		else //checking_message == 1
		{			
			$convo_partner_id = 0; //initialize variable to store who the user is talking with
			
			$header_output = "<h1>Conversation between you and "; //the first line output
			$output = "<h2>Most recent 5 messages:</h2>"; //the rest of the output
			
			$messages_id = urlencode($_GET["id"]); //id that corresponds to the id in the "messages" database
			update_message_read($user_id, $messages_id);
			
			$message_query = "SELECT * "; //pull out the conversation
			$message_query .= "FROM messages ";
			$message_query .= "WHERE id = {$messages_id} ";
			$message_query .= "LIMIT 1";
			$messages_set = mysqli_query($connection, $message_query);
			
			while ($messages = mysqli_fetch_assoc($messages_set))
			{	
				$message_id = $messages["id"];
				$book_id = $messages["book_id"];
				
				$output2 = "<h3>You are ";
				$query = "SELECT * ";
				$query .= "FROM users ";
				if ($action == "buy") //if the user is buying the book
				{
					if ($user_id != $messages["buyer_id"]) //if the user messes with the action here (the user is actually buying the book but places "sell" in the URL)
						redirect_to("messages.php");
					
					$seller_id = $messages["seller_id"]; //need to know the seller
					$convo_partner_id = $seller_id;
					$query .= "WHERE id = {$seller_id} ";
					$output2 .= "purchasing:</h3>";
				}
				elseif($action == "sell") //if the user is selling the book
				{
					if ($user_id != $messages["seller_id"]) //if the user messes with the action here (the user is actually selling the book but places "buy" in the URL)
						redirect_to("messages.php");
						
					$buyer_id = $messages["buyer_id"]; //need to know the buyer
					$convo_partner_id = $buyer_id;
					$query .= "WHERE id = {$buyer_id} ";
					$output2 .= "selling:</h3>";
					$output2 .= "<form action=\"messages.php?id={$message_id}&action={$action}\" method=\"post\">";
					$output2 .= "<input type=\"submit\" name=\"sell_to\" value=\"Sell to this Buyer\"></form><br />";
				}
				else //if the user messed with the URL (action), the user will be redirected back to the overview page of all messages
					redirect_to("messages.php");
				
				$query .= "LIMIT 1"; //continue the query to find who the logged in user is talking with
				$user_set = mysqli_query($connection, $query);
				
				while ($user = mysqli_fetch_assoc($user_set))
				{
					$convo_partner = $user["username"]; //pull out the name
					$header_output .= $convo_partner;
				}
				$header_output .= "</h1>";
				$header_output .= $output2;
				
				$sell_id = $messages["sell_id"]; //use the id from the "sell" database to know which selling order is being discussed
				
				$query = "SELECT * ";
				$query .= "FROM sell ";
				$query .= "WHERE id = {$sell_id}";
				$sell_set = mysqli_query($connection, $query);
				
				while ($sell_order = mysqli_fetch_assoc($sell_set))
				{
					$header_output .= print_book_details_for_sales($sell_order);
					$header_output .= "<br /><h3>Book Description:</h3>";
					$header_output .= $sell_order["description"];
				}

				$buyer_name = "";
				$buyer_id = $messages["buyer_id"];
				$seller_name = "";
				$seller_id = $messages["seller_id"];			
				
				$query = "SELECT * ";
				$query .= "FROM users ";
				$query .= "WHERE id = {$buyer_id} ";
				$query .= "LIMIT 1";
				$buyer_set = mysqli_query($connection, $query);
			
				while ($buyer = mysqli_fetch_assoc($buyer_set)) //extract the name of the buyer
					$buyer_name = $buyer["username"];
			
				$query = "SELECT * ";
				$query .= "FROM users ";
				$query .= "WHERE id = {$seller_id} ";
				$query .= "LIMIT 1";
				$seller_set = mysqli_query($connection, $query);
			
				while ($seller = mysqli_fetch_assoc($seller_set)) //extract the name of the seller
					$seller_name = $seller["username"];
				
				for ($i = 1; $i <= 5; $i++)
				{
					$speaker_var = "speaker" . $i;
					$output .= "<h3>";
					if ($messages["{$speaker_var}"] == 'b') //instead of printing who is the buyer and seller, print out the name of the buyer and seller based on who is what
						$output .= $buyer_name;
					elseif($messages["{$speaker_var}"] == 's')
						$output .= $seller_name;
					
					$output .= "</h3>";
					
					$message_var = "message" . $i;
					$output .= $messages["{$message_var}"];
				}
			
			}
			
			echo $header_output;
			echo $output;
			echo "<br /><br />";
			echo make_field_and_button($messages_id, $action); //the textfield and the correct button (add message or reply) will be made depending on the action
			add_message($messages_id, $action); //if the user clicks on the button, one of these functions will run
			reply($messages_id, $action);
			delete_messages_after_sell($sell_id, $user_id, $convo_partner_id, $book_id);
		}
	?>
</div>
</body>
</html>
<?php
	ob_end_flush();
?>