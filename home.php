<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">

		<header>
			<h2 class="logo">Welcome <?=$_SESSION['username']?>!</h2>
			<nav class="navigation">
				<a id="btnResults-popup" href="#">Results</a>
				<a id="btnSchedule-popup" href="#">Schedule</a>
				
				<?php
					if($_SESSION['access_level'] == 1){
						echo '<a id="btnMyPerformance-popup" href="#">My performance</a>';
					}

					if($_SESSION['access_level'] == 2){
						echo '<a id="btnComposers-popup" href="#">Composers</a>';
						echo '<a id="btnSongs-popup" href="#">Songs</a>';
					}
				?>
				<a href="logout.php">Logout</a>
			</nav>

		</header>
		
		<?php
		if($_SESSION['access_level'] == 2 || $_SESSION['access_level'] == 1) {
			echo <<<EOL
			<div id="results" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box login">
					<h2>Results</h2>
				</div>
			</div>
			EOL;
		}

		if($_SESSION['access_level'] == 2 || $_SESSION['access_level'] == 1) {
			echo <<<EOL
			<div id="schedule" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box login">
					<h2>Schedule</h2>
				</div>
			</div>
			EOL;
		}

		if($_SESSION['access_level'] == 1) {
			echo <<<EOL
			<div id="myPerformance" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box login">
					<h2>My Performance</h2>
				</div>
			</div>
			EOL;
		}

		if($_SESSION['access_level'] == 2) {
			echo <<<EOL
			<div id="composers" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box login">
					<h2>Composers</h2>
				</div>
			</div>
			EOL;
		}

		if($_SESSION['access_level'] == 2) {
			echo <<<EOL
			<div id="songs" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box login">
					<h2>Songs</h2>
				</div>
			</div>
			EOL;
		}
		?>
		<script src="home_script.js"></script>
		<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    	<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	</body>
</html>
