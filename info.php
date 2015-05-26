<?php
session_start();

// if user already login, do nothing
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
}

// When click LOG OUT
if (isset($_POST['out'])) {
    session_destroy();
    header("location: login.php");
}

require_once('vendor/autoload.php');
// Get user info
$client = new Everyman\Neo4j\Client('music.sb06.stations.graphenedb.com', 24789);
$client->getTransport()->setAuth('Music', '6DepBn2i4cmLeroINp4C');
$user_id = intval($_SESSION['user_id']);
$user_node = $client->getNode($user_id);
$user_name;
$pl_info = array();

foreach ($user_node->getProperties() as $key => $value) {
    if ($key === 'name') {
        $user_name = $value;
    }
}

$_SESSION['user_name'] = $user_name;

$rl = $user_node->getRelationships(array('HAS_PL'), 'out');
//echo count($rl);
$playlists = array_map(function ($rel) {
    return $rel->getEndNode();
}, $rl);

foreach ($playlists as $pl) {
    array_push($pl_info, array('id' => $pl->getID(), 'name' => $pl->getProperties()['name']));
}
$_SESSION['user_pls'] = $pl_info;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>User Info</title>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <style>
            .con   {
                margin-top: 10%;
            }
            #error  {
                color: red;
            }
        </style>

        <script>
            function delete_pl(pl_id) {
                if (confirm('Delete this playlist?')) {
                    ;
                } else {
                    return;
                }

                var data = {'action': 'delete_pl', 'pl_id': parseInt(pl_id)};
                $.post('server/server.php', data, function (a) {
//                    console.log(a);
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
            }
        </script>
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
                                <?php echo $user_name ?>
                            </a></li>
                        <li><a href="server/logOut.php">
                                <span class="glyphicon glyphicon-off" aria-hidden="true" name="out"></span> Log Out
                            </a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <h3> All Playlists</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created date</th>
                    <th>Total songs</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($playlists as $pl): ?>
                    <tr>
                        <th>
                            <button class="btn btn-danger" onclick="delete_pl(<?php echo $pl->getID(); ?>)">X</button>
                        </th>
                        <td>
                            <?php echo $pl->getID(); ?>
                        </td>
                        <td><a href="playlist.php?pl_id=<?php echo $pl->getID(); ?>">
                                <?php echo $pl->getProperties()['name']; ?>
                            </a></td>
                        <td>
                            <?php echo date('d-m-Y', $pl->getProperties()['created']); ?>
                        </td>
                        <td>
                            <?php
                            echo count($client->getNode($pl->getID())->getRelationships(array('CONTAIN'), 'out'));
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form class="form-inline" id="create_playlist">
            <div class="form-group">
                <label for="pl_name">Playlist Name</label>
                <input type="text" class="form-control" id="pl_name" placeholder="Characters, numbers, _" required>
            </div>
            <button type="submit" class="btn btn-primary">Create new Playlist</button>
            <span id='err'></span>
        </form>

        <script>
            var err = $("#err");
            $("#create_playlist").submit(function (e) {
                e.preventDefault();
                err.html("Please wait...");
                var pl_name = $("#pl_name").val().trim();
                if (!pl_name.match("^[a-zA-Z0-9_]*$")) {
                    err.html("Playlist name allow only characters, numbers, _ <br>");
                    return;
                }

                var data = {'action': 'create_pl', 'name': pl_name,
                    'user_id': <?php echo $_SESSION['user_id'] ?>};
                $.post('server/server.php', data, function (a) {
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
        </script>
    </body>
</html>