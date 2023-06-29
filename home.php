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
	
	if($_SESSION['access_level'] == 1) {
		$myPerformances = pg_query_params($conn,
				"SELECT * FROM wykonania as w JOIN utwory as u ON w.utwory_id=u.id WHERE w.pianisci_id = $1",
				array($_SESSION['id']));

		// lista utworów kóre mogę jeszcze dopisać do swojego wystąpienia
		$availableSongs = pg_query_params($conn,
				"SELECT u.id as u_id, tytul, imie, nazwisko, kompozytor_id from utwory as u JOIN kompozytorzy as k ON u.kompozytor_id=k.id WHERE k.id NOT IN (SELECT u.kompozytor_id FROM wykonania as w JOIN utwory as u ON w.utwory_id=u.id WHERE w.pianisci_id = $1)",
				array($_SESSION['id']));
	}

	$allPerformances = pg_query($conn,
				"SELECT w.id as w_id, ocena, harmonogram, p.imie as pianista_imie, p.nazwisko as pianista_nazwisko, tytul  FROM wykonania as w JOIN pianisci as p ON w.pianisci_id=p.id JOIN utwory as u ON w.utwory_id=u.id");

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
				<a id="btnPerformances-popup" href="#">All Performances</a>
				
				<?php
					if($_SESSION['access_level'] == 1){
						echo '<a id="btnMyPerformances-popup" href="#">My performance</a>';
					}

					if($_SESSION['access_level'] == 2){
						echo '<a id="btnComposers-popup" href="#">Composers</a>';
						echo '<a id="btnSongs-popup" href="#">Songs</a>';
						echo '<a id="btnSettings-popup" href="#">Settings</a>';
					}
				?>
				<a href="logout.php">Logout</a>
			</nav>

		</header>
		
		<?php
		//===================================================================================================================== Performances

		if($_SESSION['access_level'] == 2 || $_SESSION['access_level'] == 1) {
			echo <<<EOL
			<div id="performances" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>All performances</h2>
					<div class="scrollable">
						<table border="1" align=center>
							<thead>
								<tr>
									<th>Schedule</th>
									<th>Song Title</th>
									<th>Pianist</th>
									<th>Rating</th>
								</tr>
							</thead>

							<tbody>
			EOL;

			for($i=0; $i<pg_numrows($allPerformances); $i++) {
				$row = pg_fetch_array($allPerformances, $i);

				echo '
								<tr>
									<td> '.$row["harmonogram"].' </td>
									<td> '.$row["tytul"].' </td>
									<td> '.$row["pianista_imie"].' '.$row["pianista_nazwisko"].'</td>
									<td> '.$row["ocena"].' </td>
								</tr>';
			}

			echo '	
							</tbody>
						</table>
					</div>
				</div>
			</div>';
		}
		//===================================================================================================================== MY PERFORMANCES

		if($_SESSION['access_level'] == 1) {			
			echo <<<EOL
			<div id="myPerformances" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>My Performances</h2>
					<div class="scrollable">
						<table border="1" align=center>
							<thead>
								<tr>
									<th>Song Title</th>
									<th>Rate</th>
									<th>Schedule</th>
									<th>Accepted</th>
								</tr>
							</thead>
							<tbody>

			EOL;

			for($i=0; $i<pg_numrows($myPerformances); $i++) {
				$row = pg_fetch_array($myPerformances, $i);

				echo '
								<tr>
									<td> '.$row["tytul"].' </td>
									<td> '.$row["ocena"].' </td>
									<td> '.$row["harmonogram"].' </td>
									<td> '.$row["zaakceptowany"].' </td>
								</tr>';
			}
			echo 			'</tbody>';

			echo <<<EOL
						</table>
					</div>
					<div class="text_button">
						<p><a href="#" class="addNewPerformance-link">Add New Performance</a></p>
					</div>
				</div>
			EOL;

			echo <<<EOL
				<div class="from-box mode2">
				<h2>Add new performance</h2>
				<form action="add_new_performance.php" method="post">
					<div class="input-box">
						<span class="icon"><ion-icon name="arrow-down-circle"></ion-icon></span>

			EOL;
			if(pg_numrows($myPerformances) >= 3){
				echo '<select name="song_id" disabled>';
			}
			else {
				echo '<select name="song_id">';
			}
			
			echo <<<EOL
							<optgroup>
							<option disabled selected>   </option>
			EOL;

			for($i=0; $i < pg_numrows($availableSongs); $i++) { 
				$row = pg_fetch_array($availableSongs, $i);
				echo '<option value="'.$row["u_id"].'">'.$row["tytul"].' - '.$row["imie"].' '.$row["nazwisko"].'</option>';
			}

			echo '			</optgroup>												
						</select>
						<label>Songs</label>
					</div>';
			if(pg_numrows($myPerformances) >= 3) {
				echo '<p class="warning"> Maximum of 3 songs can be selected </p>
				<button type="submit" class="btn" disabled>Add song</button>';
			}
			else {
				echo '<button type="submit" class="btn">Add song</button>';
			}

			echo '	<div class="text_button">
						<p><a href="#" class="listOfMyPerformances-link">My performances list</a></p>
					</div>
				</form>
				</div>';

			echo "</div>";
		}
		//===================================================================================================================== COMPOSERS
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
		//===================================================================================================================== SONGS

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

		//===================================================================================================================== Settings

		if($_SESSION['access_level'] == 2) {
			echo <<<EOL
			<div id="settings" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>Settings</h2>
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
