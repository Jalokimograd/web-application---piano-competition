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

    $competition = pg_fetch_array(pg_query($conn, "SELECT * from konkurs LIMIT 1"),0);
    $competitionStage = $competition['etap_id'];


    // Sprawdź czy zapytanie zwróciło wyniki
    if (!$competition) {
        echo "Error ";
        header('refresh:2;url= home.php');
    }
    
    switch ($competitionStage) {
        case 1:
            // kończymy Stage 1. więc blokuje się możliwość dodawania nowych zgłoszeń
            $nextStage = 2;
            break;
        case 2:
            // kończymy Stage 2. więc z zatwierdzonych już wykonań zatwierdzane ostatecznie są trójkami te które dotyczą jednego pianisty
            // nastęnie generowany jest harmonogram i odblokowuje się możliwość wykonywania utworów
            $nextStage = 3;

            // jeśli ktoś nie ma 3 zaakceptowanych wykonań to jest anulowany
            pg_query($conn,
                "UPDATE wykonania set zaakceptowany=false WHERE pianisci_id IN (SELECT p.id FROM wykonania as w JOIN pianisci as p ON w.pianisci_id=p.id WHERE w.zaakceptowany=true GROUP BY p.id HAVING count(*) <> 3)");
    
            // harmonogram jest generowany przez funkcję sql ustaw_harmonogram()
            pg_query($conn, "SELECT ustaw_harmonogram()");
            break;
        case 3:
            // kończy się Stage 3. więc utwory odblokowuje się możliwość oceniania
            $nextStage = 4;
            break;
        case 4:
            // kończy się Stage 4. więc utwory zostały już ocenione. Obliczane są wyniki uczestników i kończy konkurs
            $nextStage = 4;
            break;
    }

    $stmt = pg_query_params($conn,
    "UPDATE konkurs 
        SET
            etap_id = $1",
        array($nextStage));


    pg_close($conn);

    echo 'You have successfully change settings';
    header("Location: home.php");
    //header("Location: home.php");    
?>
