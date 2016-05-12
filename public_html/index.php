<?php
	require_once("../includes/functions.php");
	require_once("../includes/session.php");
	include("../includes/header.php");
	
	find_selected_page(); //find page user is in
?>
<!--<div id="main">
		</ul> </div> <!--end categories and navigation-->
		
		<div id="page"><br /> <!--page of textbooks, each occupies a square in space-->
		
		<h3><i>There is a known formatting issue with Firefox. Please use a different browser such as Internet Explorer, Google Chrome, or Safari. We're sorry for the inconvenience this has caused.</i></h3>
		<h2>The middle school booklist for 2016-2017 has been uploaded! Please wait until the school releases the book list for high school.</h2>
		<!--sorting selection should go here-->
		<?php
			echo index_header(); //create the buttons of categories and subjects
		?>
		
		<h2>
		<?php
			if (!is_logged_in())
				echo cannot_buy_or_sell();
		?>
		</h2>
		<!-- books-->
		<?php
			echo index_body();	
		?>

		</div> <!-- div for page content-->
<!--</div>-->
</body>
</html>
<?php
	ob_end_flush();
?>