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
        header('refresh:2;url= home.php');
    }


    $competitionStages = array();

    // Sprawdź czy zapytanie zwróciło wyniki
    if ($result) {
        // Pobierz wszystkie wiersze jako tablicę asocjacyjną
        $rows = pg_fetch_all($result);
        // Iteruj przez wiersze i dodaj wartości do tablicy
        foreach ($rows as $row) {
            $competitionStages[] = $row['value'];
        }
    } else {
        echo "Error ";
        header('refresh:2;url= home.php');
    }

    pg_query($conn, "UPDATE wykonania 
                            SET
                                ocena = NULL,
                                zaakceptowany = false,
                                wykonany = false,
                                harmonogram = NULL");
    //pg_query($conn, "DELETE FROM wykonania");
    pg_query($conn, "UPDATE konkurs 
                            SET 
                                data_zakonczenia = NULL,
                                etap_id = 1");


    echo 'You have successfully change settings';
    //header("Location: home.php");
    header("Location: home.php");
    pg_close($conn);
?>
