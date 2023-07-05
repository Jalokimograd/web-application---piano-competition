<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
require_once "connect.php";
require "available_options.php";

// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}

$connecting_string = "host=$host dbname=$db_name user=$db_user password=$db_password port=$db_port";

$conn = pg_connect($connecting_string);

$competition = pg_fetch_array(pg_query($conn,
					"SELECT k.id as k_id, e.id as e_id, data_zakonczenia, e.nazwa as etap from konkurs as k JOIN etapy_konkursu as e ON k.etap_id = e.id Limit 1"), 0);
$composers = pg_query($conn,
						"SELECT * FROM kompozytorzy");
$songs = pg_query($conn,
						"SELECT u.id as u_id, tytul, imie, nazwisko, kompozytor_id 
						FROM utwory as u JOIN kompozytorzy as k ON u.kompozytor_id=k.id");

if($_SESSION['access_level'] == 1) {
	$myPerformances = pg_query_params($conn,
			"SELECT w.id as w_id, ocena, zaakceptowany, wykonany, harmonogram, tytul 
				FROM wykonania as w JOIN utwory as u ON w.utwory_id=u.id 
				WHERE w.pianisci_id = $1",
			array($_SESSION['id']));

	// lista utworów kóre mogę jeszcze dopisać do swojego wystąpienia
	$availableSongs = pg_query_params($conn,
			"SELECT u.id as u_id, tytul, imie, nazwisko, kompozytor_id 
				FROM utwory as u JOIN kompozytorzy as k ON u.kompozytor_id=k.id 
				WHERE k.id NOT IN (SELECT u.kompozytor_id FROM wykonania as w JOIN utwory as u ON w.utwory_id=u.id WHERE w.pianisci_id = $1)",
			array($_SESSION['id']));
}

$allPerformances = pg_query($conn,
			"SELECT w.id as w_id, ocena, wykonany, harmonogram, p.imie as pianista_imie, p.nazwisko as pianista_nazwisko, tytul 
				FROM wykonania as w JOIN pianisci as p ON w.pianisci_id=p.id JOIN utwory as u ON w.utwory_id=u.id 
				WHERE zaakceptowany is TRUE
				ORDER BY harmonogram");

$submissions = pg_query($conn,
			"SELECT w.id as w_id, ocena, data_zgloszenia, harmonogram, p.imie as pianista_imie, p.nazwisko as pianista_nazwisko, tytul, zaakceptowany
				FROM wykonania as w JOIN pianisci as p ON w.pianisci_id=p.id JOIN utwory as u ON w.utwory_id=u.id
				ORDER BY data_zgloszenia DESC");
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
				<a id="btnPerformances-popup" href="#">Accepted Performances</a>
				<a id="btnPianistsScore-popup" href="#">Pianists Score</a>
				
				<?php
					if($_SESSION['access_level'] == 1){
						echo '<a id="btnMyPerformances-popup" href="#">My performance</a>';
					}

					if($_SESSION['access_level'] == 2){
						echo '<a id="btnSubmissions-popup" href="#">Submissions</a>';
						echo '<a id="btnComposers-popup" href="#">Composers</a>';
						echo '<a id="btnSongs-popup" href="#">Songs</a>';
						echo '<a id="btnSettings-popup" href="#">Settings</a>';
					}
				?>
				<a href="logout.php">Logout</a>
			</nav>

		</header>
		
		<?php
		//===================================================================================================================== Pianists SCORE

		if($_SESSION['access_level'] == 2 || $_SESSION['access_level'] == 1) {
			echo <<<EOL
			<div id="pianistsScore" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>Pianists score</h2>
					<div class="scrollable">
						<table border="1" align=center>
							<thead>
								<tr>
									<th>Pianist's ID</th>
									<th>Pianist</th>
									<th>Score</th>
								</tr>
							</thead>

							<tbody>
			EOL;
			if($display_pianists_score){
				$pianists = pg_query($conn, "SELECT * FROM ocena_pianistow");

				for($i=0; $i<pg_numrows($pianists); $i++) {
					$row = pg_fetch_array($pianists, $i);
						echo '
									<tr>
										<td> '.$row["id"].' </td>
										<td> '.$row["pianista_imie"].' '.$row["pianista_nazwisko"].'</td>
										<td> '.$row["ocena_calkowita"].' </td>
									</tr>';
				}			
			}

			echo '	
							</tbody>
						</table>
					</div>
				</div>
			</div>';
		}

		//===================================================================================================================== Performances

		if($_SESSION['access_level'] == 2 || $_SESSION['access_level'] == 1) {
			echo <<<EOL
			<div id="performances" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>Accepted performances</h2>
					<div class="scrollable">
						<table border="1" align=center>
							<thead>
								<tr>
									<th>Schedule</th>
									<th>Performed</th>
									<th>Song Title</th>
									<th>Pianist</th>
									<th>Score</th>
								</tr>
							</thead>

							<tbody>
			EOL;

			for($i=0; $i<pg_numrows($allPerformances); $i++) {
				$row = pg_fetch_array($allPerformances, $i);

				if($_SESSION['access_level'] == 1 || ($_SESSION['access_level'] == 2 && !$performing_available)){
					echo '
								<tr>
									<td> '.$row["harmonogram"].' </td>
									<td> '.$row["wykonany"].' </td>
									<td> '.$row["tytul"].' </td>
									<td> '.$row["pianista_imie"].' '.$row["pianista_nazwisko"].'</td>
									<td> '.$row["ocena"].' </td>
								</tr>';
				}
				else if($_SESSION['access_level'] == 2 && $performing_available) {
					echo '
								<tr>
									<td> '.$row["harmonogram"].' </td>';

					if($row["wykonany"] == 't') {
						echo '		<td> <ion-icon name="checkmark-circle"></ion-icon> </td>';
					}
					else {
						echo '		<td>
										<form action="performed_performance.php" method="post">
											<input name="id" value="'.$row["w_id"].'" type="hidden">
											<button type="submit" class="btn"> Check </button>
										</form>
									</td>';			
					}

					echo '
									<td> '.$row["tytul"].' </td>
									<td> '.$row["pianista_imie"].' '.$row["pianista_nazwisko"].'</td>';
					

					if(!$row["ocena"] && ($row["wykonany"] == 't')) {
						echo '		<td>
										<form action="add_rate.php" method="post">
											<select class="select" name="rate">';

							for ($j = 0; $j <= 6; $j+= 0.1) {
								echo "			<option value=\"$j\">$j</option>";
							}

						
						echo '				</select>
											<input name="id" value="'.$row["w_id"].'" type="hidden">
											<button type="submit" class="btn"> rate </button>
										</form>
									</td>';
					}
					else {
						echo '		<td> '.$row["ocena"].' </td>';
					}				
					echo '		</tr>';
				}
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
									<th>Score</th>
									<th>Schedule</th>
									<th>Accepted</th>
									<th>Performed</th>
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
									<td> '.$row["wykonany"].' </td>
								</tr>';
			}
			echo 			'</tbody>';

			echo <<<EOL
						</table>
					</div>
					<div class="text_button">
						<p><a href="#" id="addNewPerformance-link">Add New Performance</a></p>
					</div>
					<div class="text_button">
						<p><a href="#" id="deletePerformance-link">Delete existing Performance</a></p>
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
			else if(!$submission_adding_available){
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
			else if(!$submission_adding_available){
				echo '<p class="warning"> submissions closed </p>
				<button type="submit" class="btn" disabled>Add song</button>';
			}
			else {
				echo '<button type="submit" class="btn">Add song</button>';
			}

				echo '	<div class="text_button">
							<p><a href="#" id="listOfMyPerformances-link">My performances list</a></p>
						</div>
					</form>
					</div>';


			echo <<<EOL
					<div class="from-box mode3">
						<h2>Delete Performance</h2>
						<form action="delete_performance.php" method="post">
							<table border="1" align=center>
								<thead>
									<tr>
										<th>Song Title</th>
										<th>Accepted</th>
										<th>Delete?</th>
									</tr>
								</thead>

								<tbody>
			EOL;
					
			for($i=0; $i<pg_numrows($myPerformances); $i++) {
				$row = pg_fetch_array($myPerformances, $i);
					echo '
									<tr>
										<td> '.$row["tytul"].' </td>
										<td> '.$row["zaakceptowany"].' </td>';
				if($row["zaakceptowany"] == 'f'){
					echo '				<td><input type="checkbox" name="selected_ids[]" value="'.$row["w_id"].'"></td>';
				}
				else{
					echo '				<td><ion-icon title="You cant delete accepted performance" name="ban-outline"></ion-icon></td>';
				}				

				echo'				</tr>';
			}
			echo '				</tbody>
							</table>
							<button type="submit" class="btn">Delete Performances</button>
						</form>
						<div class="text_button">
							<p><a href="#" id="listOfMyPerformances2-link">My performances list</a></p>
						</div>
					</div>';

			echo "</div>";
		}

		//===================================================================================================================== Submissions

		if($_SESSION['access_level'] == 2) {
			echo <<<EOL
			<div id="submissions" class="wrapper">
				<span class="icon-close"><ion-icon name="close"></ion-icon></span>
				<div class="from-box mode1">
					<h2>Submissions</h2>
					<form action="submission_management.php" method="post">
						<div class="scrollable">			
							<table border="1" align=center>
								<thead>
									<tr>
										<th>Song Title</th>
										<th>Pianist</th>
										<th>Submission date</th>
										<th>Accepted?</th>
									</tr>
								</thead>

								<tbody>
			EOL;

			for($i=0; $i<pg_numrows($submissions); $i++) {
				$row = pg_fetch_array($submissions, $i);

				$checked = $row["zaakceptowany"]=="t" ? "checked" : " ";

				echo '
									<tr>
										<td> '.$row["tytul"].' </td>
										<td> '.$row["pianista_imie"].' '.$row["pianista_nazwisko"].'</td>
										<td> '.$row["data_zgloszenia"].' </td>';
				if($submission_acceptation_available){
					echo 			'	<td> <input type="checkbox" name="selected_ids[]" value="'.$row["w_id"].'" '. $checked .'> </td>';
				}
				else{
					if($row["zaakceptowany"]=="t"){
						echo 		 '	<td> <ion-icon name="checkmark-circle"></ion-icon> </td>';
					}
					else{
						echo 		 '	<td> <ion-icon name="close"></ion-icon> </td>';
					}		
				}
										
				echo					'</tr>';
			}

			echo '	
								</tbody>
							</table>
						</div>';
			if($submission_acceptation_available){
			echo '		<button type="submit" class="btn">accept/unaccept</button>';
			}
			else{
			echo '		<button type="submit" class="btn" disabled="">accept/unaccept</button>';
			}
				
			echo '			
					</form>		
				</div>
			</div>';
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
							<thead>
								<tr>
									<th>Composer Id</th>
									<th>Name</th>
									<th>Surname</th>
								</tr>
							</thead>
							<tbody>
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
							<tbody>
						</table>
					</div>
					<div class="text_button">
						<p><a href="#" id="addNewComposer-link">Add New Composer</a></p>
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
							<p><a href="#" id="listOfComposers-link">Composers list</a></p>
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
						<thead>
							<tr>
								<th>Title</th>
								<th>Song Id</th>
								<th>Composer</th>
								<th>Composer Id</th>
							</tr>
						</thead>
						<tbody>

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
						</tbody>
					</table>
				</div>
				<div class="text_button">
					<p><a href="#" id="addNewSong-link">Add New Song</a></p>
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
							<option disabled selected>   </option>
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
						<p><a href="#" id="listOfSongs-link">Songs list</a></p>
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
					<h2>Competition settings</h2>
					<table border="1" align=center>
						<thead>
							<tr>
								<th>End date</th>
								<th>Actual stage</th>
							</tr>
						</thead>
						<tbody>
			EOL;

			echo '
							<tr>
								<td> '.$competition["data_zakonczenia"].' </td>
								<td> '.$competition["etap"].' </td>
							</tr>';								
			echo <<<EOL
	
						</tbody>
					</thead>
					</table>
					<div class="text_button">
						<p><a href="#" id="changeSettings-link">Change settings</a></p>
					</div>
				</div>
			EOL;



			echo '
				<div class="from-box mode2">
				<h2>Settings</h2>
				
				<div class="demarcation-line">	</div>

				<table align=center, style="width: 100%; border: 0px;">
					<tr>
						<th style="width: 60%;"></th>
						<th style="width: 40%;"></th>
					</tr>
					<form action="settings.php" method="post">
					<tr>
						<td>
							<div class="input-box">			
								<input type="datetime-local" name="date" value='. date('Y-m-d\TH:i', strtotime($competition["data_zakonczenia"])) .'>
								<label>The end of the competition: </label>
							</div>
						</td>
						<td>
							<button type="submit" class="btn">Set time</button>
						</td>
					</tr>
					</form>
				</table>

				<div class="demarcation-line">	</div>
	
				<table align=center, style="width: 100%; border: 0px;">
					<tr>
						<th style="width: 60%;"></th>
						<th style="width: 40%;"></th>
					</tr>
					<form action="next_stage.php" method="post">
					<tr>
						<td>	
							'.$competition["etap"].'
						</td>
						<td>
							<button type="submit" class="btn">Set next stage</button>
						</td>
					</tr>
					</form>
				</table>
				
				<div class="demarcation-line">	</div>

				<form action="reset_competition.php" method="post">
					<div class="text_button">Reset actual rating, schedule and change stage to 1.</div>
					<button type="submit" title="reset actual rating, schedule and change stage to 1." class="btn">Reset Competition</button>
				</form>

				<div class="text_button">
					<p><a href="#" id="viewSettings-link">View settings</a></p>
				</div>
				</div>
			</div>';
		}
		?>
		<script src="interactive_buttons_script.js"></script>
		<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    	<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	</body>
</html>
