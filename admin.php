<?php
session_start();

if (isset($_SESSION['admin_id'])) {

    require_once('vendor/autoload.php');

    $client = new Everyman\Neo4j\Client('music.sb06.stations.graphenedb.com', 24789);
    $client->getTransport()->setAuth('Music', '6DepBn2i4cmLeroINp4C');

    $queryString = "MATCH (n:USER) RETURN COUNT(n) AS user";
    $query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
    $result = $query->getResultSet();

    foreach ($result as $row) {
        echo "Total user: " . $row['user'] . "<br>";
    }

    $queryString = "MATCH (n:PLAYLIST) RETURN COUNT(n) AS pl";
    $query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
    $result = $query->getResultSet();

    foreach ($result as $row) {
        echo "Total playlist: " . $row['pl'] . "<br>";
    }

    $queryString = "MATCH (n:SONG) RETURN COUNT(n) AS song";
    $query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
    $result = $query->getResultSet();

    foreach ($result as $row) {
        echo "Total song: " . $row['song'] . "<br>";
    }
} else {
    $forbident = '<html>
<head>
	<title>403 Forbidden</title>
</head>
<body>

<p>Directory access is forbidden.</p>

</body>
</html>';
    die($forbident);
}
?>

<!DOCTYPE>
<html>
    <head>
        <title>System Statistics</title>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    </head>
    <body>
        <button class="btn btn-default" onclick="location.href = 'info.php'">Back</button>
    </body>
</html>
