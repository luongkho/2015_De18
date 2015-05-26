<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
}
if (!isset($_GET['pl_id'])) {
    header("location: info.php");
}
if (isset($_POST['out'])) {
    session_destroy();
}
require_once('vendor/autoload.php');

// Get playlist info
$client = new Everyman\Neo4j\Client('music.sb06.stations.graphenedb.com', 24789);
$client->getTransport()->setAuth('Music', '6DepBn2i4cmLeroINp4C');
$pl_id = intval($_GET['pl_id']);
$pl_node = $client->getNode($pl_id);
//$user_name;
//$pl_info = array();

$rl = $pl_node->getRelationships(array('CONTAIN'), 'out');
$songs = array_map(function ($rel) {
    return $rel->getEndNode();
}, $rl);

//foreach ($songs as $song) {
////    echo $song->getProperties()['artist'] . "<br>";
//    echo $song->getID() . "<br>";
//}

$count_song = 1;
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Stream your musics</title>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <style>
            #playlist,audio {
                background:#666;
                width:400px;
                padding:20px;
            }
            .active a   {
                color:#5DB0E6;
                text-decoration:none;
            }
            li a    {
                color:#eeeedd;
                background:#333;
                padding:5px;
                display:block;
            }
            li a:hover  {
                text-decoration:none;
            }
            li  {
                color: #333;
            }
            table   {
                background-color: #333;
            }
            #main_div   {
                margin: auto;
            }
        </style>
    </head>

    <body>
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"></a>
                </div>

                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="index.php">
                                <span class="glyphicon glyphicon-home" aria-hidden="true"></span> Home
                            </a></li>
                        <?php if (isset($_SESSION['admin_id'])): ?>
                            <li><a href="admin.php">
                                    <span class="glyphicon glyphicon-signal" aria-hidden="true"></span> System Status
                                </a></li>
                        <?php endif; ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="info.php">
                                <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
                                <?php echo $_SESSION['user_name'] ?>
                            </a></li>
                        <li><a href="server/logOut.php">
                                <span class="glyphicon glyphicon-off" aria-hidden="true" name="out"></span> Log Out
                            </a></li>
                    </ul>
                </div>
            </div>
        </nav>


        <h3>Playlist <?php echo $pl_node->getProperties()['name']; ?></h3>

        <div id="main_div">
            <audio id="audio" preload="auto" controls="">
                <source src="<?php echo $songs[0]->getProperties()['url']; ?>">
            </audio>

            <ul id="playlist">
                <li class="active">
                    <a href="<?php echo $songs[0]->getProperties()['url']; ?>"
                       id="<?php echo $songs[0]->getID() ?>" name="<?php echo $count_song ?>">
                           <?php
                           echo "(" . $count_song++ . ") ";
                           if (!array_key_exists('artist', $songs[0]->getProperties())) {
                               echo $songs[0]->getProperties()['title'];
                           } else if (!array_key_exists('title', $songs[0]->getProperties())) {
                               echo $songs[0]->getProperties()['artist'];
                           } else {
                               echo $songs[0]->getProperties()['title'] . ' - ' . $songs[0]->getProperties()['artist'];
                           }
                           ?>
                    </a>
                </li>

                <?php
                $first = TRUE;
                foreach ($songs as $song):
                    ?>
                    <?php
                    if ($first) {
                        $first = FALSE;
                        continue;
                    }
                    ?>
                    <li>
                        <a href="<?php echo $song->getProperties()['url']; ?>"
                           id="<?php echo $song->getID() ?>" name="<?php echo $count_song ?>">
                               <?php
                               echo "(" . $count_song++ . ") ";
                               if (!array_key_exists('artist', $song->getProperties())) {
                                   echo $song->getProperties()['title'];
                               } else if (!array_key_exists('title', $song->getProperties())) {
                                   echo $song->getProperties()['artist'];
                               } else {
                                   echo $song->getProperties()['title'] . ' - ' . $song->getProperties()['artist'];
                               }
                               ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <form class="form-inline" id="delete_song">
                <div class="form-group">
                    <label for="song_index">Song index</label>
                    <input type="text" class="form-control" id="song_index" placeholder="Index song to delete" required>
                </div>
                <button type="submit" class="btn btn-danger">Delete song</button>
                <p id="err"></p>
            </form>
        </div>
    </body>

    <script>
        var err = $("#err");
        $("#delete_song").submit(function (e) {
            e.preventDefault();
            err.html("Please wait...");
            var song_index = $("#song_index").val().trim();
            if (!$.isNumeric(song_index)) {
                err.html("Song index must be a number");
                return;
            }
            song_index = parseInt(song_index);
            console.log(song_index);

            if (song_index > <?php echo --$count_song ?>) {
                err.html("Song index too high");
                return;
            }

            var song_id = document.getElementsByName(song_index)[0].id;
            song_id = parseInt(song_id);
            console.log(song_id);

            var data = {'action': 'delete_song_from_pl', 'pl_id': <?php echo $_GET['pl_id'] ?>, 'song_id': song_id};
            $.post('server/server.php', data, function (a) {
//                alert(a);
                console.log(a);
                try {
                    var result = JSON.parse(a);
                    console.log(result);
                    if (result.result === 1) {
                        location.reload();
                    } else {
                        alert(result.result);
                    }
                }
                catch (err) {
                    alert("Error while connected server!");
                }
            });
        });

        function run(link, player) {
            player.src = link.attr('href');
            //            console.log(player.src);
            par = link.parent();
            par.addClass('active').siblings().removeClass('active');
            audio[0].load();
            audio[0].play();
        }

        var audio;
        var playlist;
        var tracks;
        var current;
        init();
        function init() {
            current = 0;
            audio = $('#audio');
            playlist = $('#playlist');
            tracks = playlist.find('li a');
            console.log(tracks);
            len = tracks.length - 1;
            console.log(len);
            audio[0].volume = 1;
            console.log(audio[0]);
            audio[0].play();
            //            console.log(playlist.find('a'));
            playlist.find('a').click(function (e) {
                e.preventDefault();
                link = $(this);
                //                console.log(link);
                current = link.parent().index();
                console.log(current);
                run(link, audio[0]);
            });
            audio[0].addEventListener('ended', function (e) {
                current++;
                console.log("Curr: " + current);
                if (current === len + 1) {
                    current = 0;
                    link = playlist.find('a')[0];
                } else {
                    link = playlist.find('a')[current];
                }
                run($(link), audio[0]);
            });
        }
    </script>
</html>