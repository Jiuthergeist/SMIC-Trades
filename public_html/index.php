<?php
	require_once("../includes/functions.php");
	require_once("../includes/session.php");
	include("../includes/header.php");
	
	find_selected_page(); //find page user is in
?>
<!--<div id="main">
		</ul> </div> <!--end categories and navigation-->
		
		<div id="page"><br /> <!--page of textbooks, each occupies a square in space-->
		<h3><a href="about.php">About Us</a></h3>
		<h3><i>There is a known formatting issue with Firefox. Please use a different browser such as Internet Explorer, Google Chrome, or Safari. We're sorry for the inconvenience this has caused.</i></h3>
		<h2>THE MIDDLE SCHOOL AND HIGH SCHOOL BOOKLIST FOR 2016-2017 HAS BEEN UPLOADED!</h2>
		<h3><i>Need help? Feel free to contact <a href="mailto:smiccomputerclub@hotmail.com">smiccomputerclub@hotmail.com</a>, and we will answer your questions as soon as possible!</i></h3>
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