<!DOCTYPE>
<a href="upl.php" id="link">Page is redirecting, please wait...</a><br><br>
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<script>
    $(document).ready(function () {
//        document.getElementById("link").focus();
//        document.getElementById("link").click();
        setTimeout(function () {
            window.location.href = "../upl.php"; //will redirect to your blog page (an ex: blog.html)
        }, 4000);
    });
</script>

<?php
require_once('../vendor/autoload.php');
$base_url = "http://localhost/web/Assignment";
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
    echo $file_size / 1024 / 1024 . " MB<br>";
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
    if ($file_size / 1024 / 1024 > 10) {
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
            ->setProperty('url', $base_url . "/uploads/" . $file_name)->setProperty('created', $date)
            ->setProperty('server', $server)->save();
    $label = $node->addLabels(array($lable_song, $label_sv));
    echo "Song 's ID: " . $node->getID() . "<br>";

    $pl_id = $_POST['pl_id'];
    $pl_node = $client->getNode($pl_id);

    $relate = $client->makeRelationship();
    $relate->setStartNode($pl_node)->setEndNode($node)->setType('CONTAIN')->save();
    if (!$relate) {
        echo "Error while adding data <br>";
    } else {
        echo "Added song to playlist <br>";
    }

//    echo "<br> Page is redirecting, please wait... <br>";
}
?>
