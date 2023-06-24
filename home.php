<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
require_once "connect.php";

// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}

$connecting_string = "host=$host dbname=$db_name user=$db_user password=$db_password port=$db_port";
    // echo $connecting_string;
    $conn = pg_connect($connecting_string);

    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.

    $composers = pg_query($conn,
                         "SELECT * FROM kompozytorzy");
	$songs = pg_query($conn,
                         "SELECT u.id as u_id, tytul, imie, nazwisko, kompozytor_id from utwory as u JOIN kompozytorzy as k ON u.kompozytor_id=k.id");


?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link href="style_selects.css" rel="stylesheet" type="text/css">
		<link href="style_home.css" rel="stylesheet" type="text/css">
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
				<div class="from-box mode1">
					<h2>Results</h2>
				</div>
			</div>
			EOL;
		}

		if($_SESSION['access_level'] == 2 || $_SESSION['access_level'] == 1) {
			echo <<<EOL
			<div id="schedule" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>Schedule</h2>
					<div style="overflow-x:auto;">
						<table>
							
						</table>
					</div>
				</div>
			</div>
			EOL;
		}

		if($_SESSION['access_level'] == 1) {
			echo <<<EOL
			<div id="myPerformance" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>My Performance</h2>
					<label for="multi-select">List of composers</label>
					<div class="select select--multiple">
						<select id="multi-select" multiple>
							<option value="Option 1">Option 1</option>
							<option value="Option 2">Option 2</option>
							<option value="Option 3">Option 3</option>
							<option value="Option 4">Option 4</option>
							<option value="Option 5">Option 5</option>
							<option value="Option length">Option that has too long of a value to fit</option>
						</select>
					<span class="focus"></span>
					</div>
				</div>
			</div>
			EOL;
		}

		if($_SESSION['access_level'] == 2) {
			echo <<<EOL
			<div id="composers" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>Composers</h2>
					<div class="scrollable">
						<table border="1" align=center>
						<tr>
							<th>Composer Id</th>
							<th>Name</th>
							<th>Surname</th>
						</tr>
			EOL;

			for($i=0; $i<pg_numrows($composers); $i++) {
				$row = pg_fetch_array($composers, $i);

					echo '
						<tr>
							<td> '.$row["id"].' </td>
							<td> '.$row["imie"].' </td>
							<td> '.$row["nazwisko"].' </td>
						</tr>';
			}
			
			echo <<<EOL
						</table>
					</div>
					<div class="text_button">
						<p><a href="#" class="addNewComposer-link">Add New Composer</a></p>
					</div>
				</div>
			EOL;

			echo <<<EOL
				<div class="from-box mode2">
					<h2>Add new composer</h2>
					<form action="add_new_composer.php" method="post">
						<div class="input-box">
							<span class="icon"><ion-icon name="person"></ion-icon></span>
							<input type="text" name="name" required>
							<label>Composer name</label>
						</div>
						<div class="input-box">
							<span class="icon"><ion-icon name="person"></ion-icon></span>
							<input type="text" name="surname" required>
							<label>Composer surname</label>
						</div>
						<button type="submit" class="btn">Add Composer</button>
						<div class="text_button">
							<p><a href="#" class="listOfComposers-link">Composers list</a></p>
						</div>
					</form>
				</div>
			</div>
			EOL;
		}

		if($_SESSION['access_level'] == 2) {
			echo <<<EOL
			<div id="songs" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>Songs</h2>
					<div class="scrollable">
						<table border="1" align=center>
						<tr>
							<th>Title</th>
							<th>Song Id</th>
							<th>Composer</th>
							<th>Composer Id</th>
						</tr>


			EOL;

			for($i=0; $i<pg_numrows($songs); $i++) {
				$row = pg_fetch_array($songs, $i);

				echo '
					<tr>
						<td> '.$row["tytul"].' </td>
						<td> '.$row["u_id"].' </td>
						<td> '.$row["imie"].' '.$row["nazwisko"].'  </td>
						<td> '.$row["kompozytor_id"].' </td>
					</tr>';
			}

			
			echo <<<EOL
						</table>
					</div>
					<div class="text_button">
						<p><a href="#" class="addNewSong-link">Add New Song</a></p>
					</div>
				</div>
			EOL;

			echo <<<EOL
				<div class="from-box mode2">
				<h2>Add new song</h2>
				<form action="add_new_song.php" method="post">
					<div class="input-box">
						<span class="icon"><ion-icon name="musical-notes"></ion-icon></span>
						<input type="text" name="title" required>
						<label>Song title</label>
					</div>
					<div class="input-box">
						<span class="icon"><ion-icon name="arrow-down-circle"></ion-icon></span>

						<select name="composer_id">
							<optgroup>
			EOL;

			for($i=0; $i < pg_numrows($composers); $i++) { 
				$row = pg_fetch_array($composers, $i);
				echo '<option value="'.$row["id"].'">'.$row["imie"].' '.$row["nazwisko"].'</option>';
			}

			echo '			</optgroup>												
						</select>
						<label>Composer</label>
					</div>
					<button type="submit" class="btn">Add song</button>
					<div class="text_button">
						<p><a href="#" class="listOfSongs-link">Songs list</a></p>
					</div>
				</form>
				</div>';

			echo "</div>";
		}
		?>
		<script src="home_script.js"></script>
		<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    	<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	</body>
</html>
