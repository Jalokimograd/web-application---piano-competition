<?php
    session_start();
    require_once "connect.php";


    // Create connection
    $connecting_string = "host=$host dbname=$db_name user=$db_user password=$db_password port=$db_port";
    // echo $connecting_string;
    $conn = pg_connect($connecting_string);
    // Check connection
    if (!$conn) {
        die("Connection failed ");
    }
    echo "Connected successfully ";


    if ( !isset($_POST['selected_ids']) || !is_array($_POST["selected_ids"])) {
        // Could not get the data that should have been sent.
        exit('error values!');
        header('refresh:3;url= home.php');
    }

    foreach ($_POST["selected_ids"] as $selected_id) {
        // Usunięcie wiersza z bazy danych o podanym id i pole "zaakceptowany" ustawione na false
        $query = "DELETE FROM wykonania WHERE id = $selected_id AND zaakceptowany = false";
        $result = pg_query($conn, $query);
        
        if ($result) {
          echo "Usunięto wiersz o ID: $selected_id<br>";
        } else {
          echo "Błąd podczas usuwania wiersza o ID: $selected_id<br>";
        }
        header('refresh:2;url= home.php');
    }
  


    pg_close($conn);
?>
