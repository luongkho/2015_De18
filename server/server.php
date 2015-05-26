<?php

session_start();

require_once('../vendor/autoload.php');
$base_url = "http://localhost/web/Assignment/";
//header("refresh:5;url=$base_url/upl.php");

/**
 * When user upload music to server
 */
if (isset($_POST['upload'])) {
    // Return to upload page
//    header("refresh:5;url=$base_url/upl.php");

    $file_name = $_FILES['upload_file']['name'];
    $file_path = $_FILES["upload_file"]["tmp_name"];
    $file_size = $_FILES["upload_file"]["size"];
    $file_ext = substr($file_name, strpos($file_name, '.') + 1);
    $file_name = $_POST['title'] . "_" . $_POST['artist'] . "." . $file_ext;
    $uploadOk = 1;

    echo $file_name . "<br>";
    echo $file_path . "<br>";
    echo $file_size . "<br>";
    echo $file_ext . "<br>";

    $target_dir = "../uploads/";
    $target_file = $target_dir . $file_name;

// Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
        return;
    }

// Check file size
    if ($file_size / 1024 / 1024 > 20) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
        return;
    }

// Allow certain file formats
    if ($file_ext != "mp3" && $file_ext != "ogg" && $file_ext != "wav") {
        echo "Only mp3, ogg, wav are allowed.";
        $uploadOk = 0;
        return;
    }

    if (move_uploaded_file($file_path, $target_file)) {
        echo "The file " . $file_name . " has been uploaded <br>";
    } else {
        echo "Sorry, there was an error uploading your file <br>";
    }

    // Add node to database
    $client = new Everyman\Neo4j\Client('music.sb06.stations.graphenedb.com', 24789);
    $client->getTransport()->setAuth('Music', '6DepBn2i4cmLeroINp4C');

    $lable_song = $client->makeLabel('SONG');
    $label_sv = $client->makeLabel('SV');
    $date = time();
    $server = "Server";

    $node = $client->makeNode();
    $node->setProperty('title', $_POST['title'])->setProperty('artist', $_POST['artist'])
            ->setProperty('url', $base_url . "/uploads" . $file_name)->setProperty('created', $date)
            ->setProperty('server', $server)->save();
    $label = $node->addLabels(array($lable_song, $label_sv));
    echo "Song 's ID: " . $node->getID() . "<br>";

    echo "<br> Page is redirecting, please wait... <br>";
}

function test_input($str) {
    $str = trim($str);
    $str = stripslashes($str);
    $str = htmlspecialchars($str);
    return $str;
}

/**
 *  Action when client send ajax */
if (isset($_POST['action'])) {
    $client = new Everyman\Neo4j\Client('music.sb06.stations.graphenedb.com', 24789);
    $client->getTransport()->setAuth('Music', '6DepBn2i4cmLeroINp4C');

    switch ($_POST['action']) {
        case "signup":
            sign_up($client, $_POST['user'], $_POST['pass']);
            break;
        case "search_sv":
            search_sv($client);
            break;
        case "login":
            login($client, $_POST['user'], $_POST['pass']);
            break;
        case "user_session":
            if ($_POST['ad'] == 0) {
                $_SESSION['user_id'] = $_POST['user_id'];
            } else {
                $_SESSION['admin_id'] = $_SESSION['user_id'] = $_POST['user_id'];
            }
            break;
        case "add_song_sv":
            add_song_sv($client, $_POST['song_id'], $_POST['pl_id']);
            break;
        case "add_song_sc":
            add_song_sc($client, $_POST['pl_id'], $_POST['title'], $_POST['url']);
            break;
        case "add_song_sp":
            add_song_sp($client, $_POST['pl_id'], $_POST['title'], $_POST['artist'], $_POST['url']);
            break;
        case "delete_pl":
            delete_pl($client, $_POST['pl_id']);
            break;
        case "create_pl":
            create_pl($client, intval($_POST['user_id']), $_POST['name']);
            break;
        case "delete_song_from_pl":
            delete_song($client, $_POST['pl_id'], $_POST['song_id']);
            break;
        default:
            echo json_encode(array('result' => "No function call"));
            break;
    }
}

// Sign up
function sign_up($client, $user, $pass) {
//$client = new Everyman\Neo4j\Client('music.sb06.stations.graphenedb.com', 24789);
//$client->getTransport()->setAuth('Music', '6DepBn2i4cmLeroINp4C');
//$user = "user1";
    $queryString = "MATCH (n:USER) WHERE n.name = {name} RETURN COUNT(n) AS num";
    $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array("name" => $user));
    $result = $query->getResultSet();
    $data = array();

    foreach ($result as $row) {
        $data['user_num'] = $row['num'];
    }
//    var_dump($data);

    if ($data['user_num'] > 0) {
        die(json_encode($data));
    }

    $node = $client->makeNode()->setProperty('name', $user)->setProperty('pass', $pass)
                    ->setProperty('created', time())->setProperty('role', 'normal')->save();

    $lb_user = $client->makeLabel('USER');
    $lb_normal = $client->makeLabel('NORMAL');
    $labels = $node->addLabels(array($lb_user, $lb_normal));
//    $qs2 = "CREATE (n:USER) WHERE n.name = {name} AND n.pass = {pass} AND n.created = {time} "
//            . "AND n.role = {role} RETURN n";
//    $q2 = new Everyman\Neo4j\Cypher\Query($client, $qs2, array("name" => $user,
//        'pass' => $pass, 'time' => time(), 'role' => 'user'));
//    $r2 = $q2->getResultSet();
//
    if (!$node) {
        $data['created'] = 0;
    } else {
        $data['created'] = $node->getID();
    }
    echo json_encode($data);
}

//search_sv();
function search_sv($client) {
    $term = $_POST['term'];
//    $term = "the";
    $data = array();
    $queryString = "MATCH n WHERE n.artist =~ '(?i).*$term.*' RETURN n, ID(n) AS id LIMIT 20 " .
            "UNION MATCH n WHERE n.title =~ '(?i).*$term.*' RETURN n, ID(n) AS id LIMIT 20";
    $query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
    $result = $query->getResultSet();

    foreach ($result as $row) {
        if (!$row['n']->url) {
            continue;
        }
        array_push($data, array('id' => $row['id'], 'title' => $row['n']->title,
            'artist' => $row['n']->artist, 'url' => $row['n']->url,
            'server' => $row['n']->server));
    }
//var_dump($data);
    echo json_encode($data);
}

// Login
function login($client, $name, $pass) {
//    $client = new Everyman\Neo4j\Client('music.sb06.stations.graphenedb.com', 24789);
//    $client->getTransport()->setAuth('Music', '6DepBn2i4cmLeroINp4C');
//    $name = $pass = "user1";
    $data = array();

    $queryString = "MATCH (n:USER) WHERE n.name = {name} AND n.pass = {pass} RETURN n, ID(n) AS id, n.role AS role LIMIT 1";
    $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array("name" => $name, "pass" => $pass));
    $result = $query->getResultSet();

    foreach ($result as $row) {
//        echo $row['id'];
        array_push($data, array('id' => $row['id'], 'role' => $row['role']));
    }
    echo json_encode($data);
}

// Add song from server to playlist
function add_song_sv($client, $song_id, $pl_id) {
    $pl_node = $client->getNode($pl_id);
    $rl = $pl_node->getRelationships(array('CONTAIN'), 'out');
    $songs = array_map(function ($rel) {
        return $rel->getEndNode();
    }, $rl);

    foreach ($songs as $s) {
        if ($song_id == $s->getID()) {
            echo json_encode(array('result' => 'Song already in playlist'));
            return;
        }
    }

    $song_node = $client->getNode($song_id);
    $relate = $client->makeRelationship();
    $relate->setStartNode($pl_node)->setEndNode($song_node)->setType('CONTAIN')->save();
    if (!$relate) {
        echo json_encode(array('result' => 'Error while adding data'));
    } else {
        echo json_encode(array('result' => 'Add song completed'));
    }
}

// Add song from SoundCloud to playlist
function add_song_sc($client, $pl_id, $title, $url) {
    $pl_node = $client->getNode($pl_id);
    if (!$pl_node) {
        echo json_encode(array('result' => 'Playlist not found'));
        return;
    }

    $song_node = $client->makeNode()
                    ->setProperty('created', time())
                    ->setProperty('title', $title)
                    ->setProperty('server', 'SoundCloud')
                    ->setProperty('url', $url)->save();
    if (!$song_node) {
        echo json_encode(array('result' => 'Failure in create node'));
        return;
    }

    $lb_song = $client->makeLabel('SONG');
    $lb_sc = $client->makeLabel('SOUND_CLOUD');
    $labels = $song_node->addLabels(array($lb_song, $lb_sc));
    if (!$labels) {
        echo json_encode(array('result' => 'Failure in create node'));
        return;
    }

    $relate = $client->makeRelationship()
                    ->setStartNode($pl_node)
                    ->setEndNode($song_node)
                    ->setType('CONTAIN')->save();
    if (!$relate) {
        echo json_encode(array('result' => 'Error while adding data'));
    } else {
        echo json_encode(array('result' => 'Add song completed'));
    }
}

// Add song from Spotify to playlist
function add_song_sp($client, $pl_id, $title, $artist, $url) {
    $pl_node = $client->getNode($pl_id);
    if (!$pl_node) {
        echo json_encode(array('result' => 'Playlist not found'));
        return;
    }

    $song_node = $client->makeNode()
                    ->setProperty('created', time())
                    ->setProperty('title', $title)
                    ->setProperty('artist', $artist)
                    ->setProperty('server', 'Spotify')
                    ->setProperty('url', $url)->save();
    if (!$song_node) {
        echo json_encode(array('result' => 'Failure in create node'));
        return;
    }

    $lb_song = $client->makeLabel('SONG');
    $lb_sp = $client->makeLabel('SPOTIFY');
    $labels = $song_node->addLabels(array($lb_song, $lb_sp));
    if (!$labels) {
        echo json_encode(array('result' => 'Failure in create node'));
        return;
    }

    $relate = $client->makeRelationship()
                    ->setStartNode($pl_node)
                    ->setEndNode($song_node)
                    ->setType('CONTAIN')->save();
    if (!$relate) {
        echo json_encode(array('result' => 'Error while adding data'));
    } else {
        echo json_encode(array('result' => 'Add song completed'));
    }
}

// Delete playlist 
function delete_pl($client, $pl_id) {
    $pl_node = $client->getNode($pl_id);
    if (!$pl_node) {
        echo json_encode(array('result' => 'Playlist not found'));
        return;
    }

    $rl_out = $pl_node->getRelationships(array('CONTAIN'), 'out');
    foreach ($rl_out as $rl) {
        $rl->delete();
    }
    $rl_in = $pl_node->getRelationships(array('HAS_PL'), 'in');
    foreach ($rl_in as $rl) {
        $rl->delete();
    }

    $dl = $pl_node->delete();
    if (!$dl) {
        echo json_encode(array('result' => 'Error while delete playlist'));
    } else {
        echo json_encode(array('result' => 1));
    }
}

// Create new playlist
function create_pl($client, $user_id, $name) {
    $name = test_input($name);
    $pl_node = $client->makeNode()
                    ->setProperty('created', time())
                    ->setProperty('name', $name)->save();
    if (!$pl_node) {
        echo json_encode(array('result' => 'Failure in create node'));
        return;
    }

    $lb_pl = $client->makeLabel('PLAYLIST');
    $labels = $pl_node->addLabels(array($lb_pl));
    if (!$labels) {
        echo json_encode(array('result' => 'Failure in create node'));
        return;
    }

    $user_node = $client->getNode($user_id);
    if (!$user_node) {
        echo json_encode(array('result' => 'Playlist not found'));
        return;
    }

    $relate = $client->makeRelationship()
                    ->setStartNode($user_node)
                    ->setEndNode($pl_node)
                    ->setType('HAS_PL')->save();
    if (!$relate) {
        echo json_encode(array('result' => 'Error while adding data'));
    } else {
        echo json_encode(array('result' => 1));
    }
}

// Delete a song from playlist
function delete_song($client, $pl_id, $song_id) {
    $pl_id = intval($pl_id);
    $song_id = intval($song_id);
    $queryString = "MATCH (n:SONG)<-[r]-(m:PLAYLIST) WHERE ID(n) = {song} AND ID(m) = {pl} DELETE r";
    $query = new Everyman\Neo4j\Cypher\Query($client, $queryString, array('song' => $song_id, 'pl' => $pl_id));
    $result = $query->getResultSet();

    if (!$result) {
        echo json_encode(array('result' => 'Error while delete song'));
        return;
    }
    echo json_encode(array('result' => 1));
//    foreach ($result as $row) {
//        if (!$row['n']->url) {
//            continue;
//        }
//        array_push($data, array('id' => $row['id'], 'title' => $row['n']->title,
//            'artist' => $row['n']->artist, 'url' => $row['n']->url,
//            'server' => $row['n']->server));
//    }
//
//    $song_node = $client->getNode($song_id);
//    if (!$song_node) {
//        echo json_encode(array('result' => 'Song not found'));
//        return;
//    }
//    echo $song_id;
//    $rls = $song_node->getRelationships(array('CONTAIN'), 'in');
////    var_dump($rls);
//    if (!$rls) {
//        echo json_encode(array('result' => 'Error while finding data'));
//        return;
//    }
//    foreach ($rls as $rl) {
//        if ($rl->getID() == $pl_id) {
//            $rl->delete();
//            echo json_encode(array('result' => 1));
//            return;
//        }
//    }
//    echo json_encode(array('result' => 'Error while delete song'));
}
