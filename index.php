<?php
session_start();

if (isset($_POST['out'])) {
    session_destroy();
    header("location: main.php");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Main page</title>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <style>
            .col-sm-12  {
                margin-top: 1%;
            }
            #div_sv {
                background-color: #EBEBEB;
            }
            #div_sc {
                background-color: #F3F3F3;
            }
            #div_sp {
                background-color: #C0C0C0;
            }
            .result {
                margin-top: 3%;
            }
        </style>
        <script>
            function stripslashes(str) {
                str = str.replace(/\\'/g, '\'');
                str = str.replace(/\\"/g, '"');
                str = str.replace(/\\0/g, '\0');
                str = str.replace(/\\\\/g, '\\');
                return str;
            }
            function escapeHtml(str) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return str.replace(/[&<>"']/g, function (m) {
                    return map[m];
                });
            }

            function add_song_to_pl(id, title, artist, url, func) {
                var active_pl = $("#active_pl").val();
                if (!active_pl) {
                    if (confirm('You must login to create playlist. Login now?')) {
                        window.location.href = "login.php";
                    }
                    return;
                }
                if (active_pl === "no") {
                    alert("You must choose a playlist to add songs");
                    return;
                }
                console.log(active_pl);

                if (func === "sv") {
                    console.log(id);
                    var data = {'action': 'add_song_sv', 'song_id': id, 'pl_id': parseInt(active_pl)};
                    $.post('server/server.php', data, function (a) {
                        try {
                            var result = JSON.parse(a);
                            console.log(result);
                            alert(result.result);
                        }
                        catch (err) {
                            alert("Error while connected server!");
                        }
                    });
                }

                if (func === "sc") {
                    var data = {'action': 'add_song_sc', 'pl_id': parseInt(active_pl),
                        'title': title, 'url': url};
                    $.post('server/server.php', data, function (a) {
                        try {
                            var result = JSON.parse(a);
                            console.log(result);
                            alert(result.result);
                        }
                        catch (err) {
                            alert("Error while connected server!");
                        }
                    });
                }

                if (func === "sp") {
                    var data = {'action': 'add_song_sp',
                        'pl_id': parseInt(active_pl),
                        'title': title,
                        'artist': artist, 'url': url};
                    $.post('server/server.php', data, function (a) {
                        try {
                            var result = JSON.parse(a);
                            console.log(result);
                            alert(result.result);
                        }
                        catch (err) {
                            alert("Error while connected server!");
                        }
                    });
                }
            }

            function create_div_result(id, title, artist, url, server) {
                var div = document.createElement("div");
                var div_small = document.createElement("div");
                div.className = "result";
                //                div.id = id;
                var text = "";
                if (!artist) {
                    var text = "Title: " + title + "<br>"
                            + "From: " + server + "<br>";
                } else {
                    var text = "Title: " + title + "<br>"
                            + "Artist: " + artist + "<br>"
                            + "From: " + server + "<br>";
                }

                div.innerHTML = text;
                var audio = document.createElement("audio");
                audio.setAttribute("controls", "");
                //                audio.setAttribute("preload", "");
//                                div.appendChild(audio);                
                var source = document.createElement("source");
                source.setAttribute("src", url);
                audio.appendChild(source);
                div_small.appendChild(audio);
                var button = document.createElement("button");
                button.className = "btn btn-warning";
                button.innerHTML = "Add";
                button.id = id;
                button.addEventListener("click", function () {
                    add_song_to_pl(id, title, artist, url, "sv");
                });
                div_small.appendChild(button);
                div.appendChild(div_small);
                return div;
            }

            function create_div_result2(id, title, artwork) {
                var host = "http://localhost/web/Assignment/materials/artwork.jpeg";
                var div = document.createElement("div");
                div.className = "result row";

                var div_left = document.createElement("div");
                div_left.className = "col-sm-3";
                //                    div_left.setAttribute("style", "float:left");
                var img = document.createElement("img");
                img.className = "img-circle";
                if (artwork) {
                    img.setAttribute("src", artwork);
                } else {
                    img.setAttribute("src", host);
                }

                div_left.appendChild(img);
                div.appendChild(div_left);

                var text = document.createTextNode(title);
                div.appendChild(text);

                var div_small = document.createElement("div");
                div_small.className = "col-sm-7";
                var audio = document.createElement("audio");
                audio.setAttribute("controls", "");
                var source = document.createElement("source");
                var url = "http://api.soundcloud.com/tracks/" + id + "/stream?client_id=359ef1baa55fe505db2183245fe15314";
                source.setAttribute("src", url);
                audio.appendChild(source);
                div_small.appendChild(audio);

                var button = document.createElement("button");
                button.className = "btn btn-warning";
                button.innerHTML = "Add";
                button.addEventListener("click", function () {
                    add_song_to_pl(id, title, "", url, "sc");
                });
                div_small.appendChild(button);
                div.appendChild(div_small);

                return div;
            }

            function create_div_result3(id, title, artist, url, artwork) {
                var div = document.createElement("div");
                div.className = "result row";

                var div_left = document.createElement("div");
                div_left.className = "col-sm-3";
                var img = document.createElement("img");
                img.className = "img-circle";
                img.setAttribute("src", artwork);
                div_left.appendChild(img);
                div.appendChild(div_left);

                var text = "Title: " + title + "<br>" + "Artist: " + artist + "<br>";
                var div_small = document.createElement("div");
                div_small.className = "col-sm-7";
                div_small.innerHTML = text;

                var audio = document.createElement("audio");
                audio.setAttribute("controls", "");
                var source = document.createElement("source");
                source.setAttribute("src", url);
                audio.appendChild(source);
                div_small.appendChild(audio);

                var button = document.createElement("button");
                button.className = "btn btn-warning";
                button.innerHTML = "Add";
                button.addEventListener("click", function () {
                    add_song_to_pl(id, title, artist, url, "sp");
                });
                div_small.appendChild(button);
                div.appendChild(div_small);

                return div;
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
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="info.php">
                                    <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 
                                    <?php echo $_SESSION['user_name'] ?>
                                </a></li>
                            <li><a href="server/logOut.php">
                                    <span class="glyphicon glyphicon-off" aria-hidden="true" name="out"></span> Log Out
                                </a></li>
                        <?php else : ?>
                            <li><a href="login.php">
                                    <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Login
                                </a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>


        <div class="col-sm-12">
            <div class="col-sm-6">
                <?php if (isset($_SESSION['user_pls'])): ?>
                    <select class="form-control" id="active_pl">
                        <option value="no">-- Choose active playlist --</option>
                        <?php foreach ($_SESSION['user_pls'] as $opt): ?>
                            <option value="<?php echo $opt['id'] ?>">
                                <?php echo $opt['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div class="col-sm-6">
                <a href="upl.php">Still not found. Upload your own music</a>
            </div>
        </div>

        <div class="col-sm-12">

            <div class="col-sm-4" id="div_sv">
                <form action="" method="post" class="form-inline" id="sv">
                    <label class="sr-only" for="search_server">Search Server</label>
                    <input type="text" class="form-control" id="search_server" name="term_sv" placeholder="Search by title or artist">
                    <button type="submit" name="search_server" class="btn btn-default">Search Server</button>
                </form>
                <div id="result_server"></div>
            </div>

            <script>
                $("#sv").submit(function (e) {
                    e.preventDefault();
                    var result = document.getElementById("result_server");
                    result.innerHTML = "Please wait.....";
                    var term = $('#search_server').val();
                    var data = {'action': 'search_sv', 'term': term};

                    $.post("server/server.php", data, function (a) {
                        try {
                            var resp = JSON.parse(a);
                            //                                console.log(resp);
                            result.innerHTML = "";
                            for (var i = 0; i < resp.length; i++) {
                                //                                for (var i = 0; i < 5; i++) {
                                console.log(resp[i].id);
                                console.log('Call create_div');
                                var div = create_div_result(resp[i].id, resp[i].title, resp[i].artist, resp[i].url, resp[i].server);
//                                console.log('Call create_div');
                                result.appendChild(div);
                            }
                        }
                        catch (err) {
                            $("#result_server").html("Error while searching server!");
                        }
                    });
                });
            </script>

            <div class="col-sm-4" id="div_sc">
                <form action="" method="post" class="form-inline" id="sc">
                    <label class="sr-only" for="search_sc">Search SoundCloud</label>
                    <input type="text" class="form-control" id="search_sc" name="term_sc" placeholder="Search by title or artist">
                    <button type="submit" name="search_sc" class="btn btn-default">Search SoundCloud</button>
                </form>
                <div id="result_sc"></div>
            </div>

            <script src="http://connect.soundcloud.com/sdk.js"></script>
            <script>
                $("#sc").submit(function (e) {
                    e.preventDefault();
                    var result = document.getElementById("result_sc");
                    result.innerHTML = "Please wait.....";
                    var term = $("#search_sc").val();
                    SC.initialize({client_id: '359ef1baa55fe505db2183245fe15314'});
                    SC.get('/tracks', {q: term}, function (tracks) {
                        console.log(tracks.length);
                        result.innerHTML = ""; //                            for (var i = 0; i < tracks.length; i++) {
                        for (var i = 0; i < 15; i++) {
                            if (tracks[i].streamable == false) {
                                continue;
                            }
                            var div = create_div_result2(tracks[i].id, tracks[i].title, tracks[i].artwork_url);
                            result.appendChild(div);
                        }
                    });
                });
            </script>

            <div class="col-sm-4" id="div_sp">
                <form action="" method="post" class="form-inline" id="sp">
                    <label class="sr-only" for="search_sp">Search Spotify</label>
                    <input type="text" class="form-control" id="search_sp" name="term_sp" placeholder="Search by title">
                    <button type="submit" name="search_sp" class="btn btn-default">Search Spotify</button>
                </form>
                <div id="result_sp"></div>
            </div>

            <script>
                $("#sp").submit(function (e) {
                    e.preventDefault();
                    var result = document.getElementById("result_sp");
                    result.innerHTML = "Please wait.....";
                    var term = $('#search_sp').val();
                    var get = "https://api.spotify.com/v1/search?query=" + term + "&offset=0&limit=20&type=track";
                    $.get(get, function (a) {
                        var track = a.tracks.items;
                        //                        console.log(track);
                        result.innerHTML = "";
                        for (var i = 0; i < 10; i++) {
                            try {
                                var div = create_div_result3(track[i].id, track[i].name, track[i].artists[0].name, track[i].preview_url, track[i].album.images[2].url);
                                result.appendChild(div);
                            } catch (e) {
                                result.innerHTML = "Error while fetching data";
                            }
                        }
                    });
                });
            </script>
        </div>
    </body>
</html>