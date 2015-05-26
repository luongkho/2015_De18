<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_pls'])) {
//    $forbident = '<html>
//<head>
//	<title>403 Forbidden</title>
//</head>
//<body>
//
//<p>Directory access is forbidden.</p>
//
//</body>
//</html>';
//    die($forbident);
    header("location: login.php");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Upload your own music</title>
        <!-- JQuery  -->
        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

        <style>
            #nav    {
                /*margin-top: 5%;*/
            }
            form    {
                margin-top: 5%;
                margin-left: 5%;
            }
            form    div {
                margin-left: 5%;
            }
            #span    {
                margin-left: 10%;
                color: red;
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
                        <li><a href="/server/logOut.php">
                                <span class="glyphicon glyphicon-off" aria-hidden="true" name="out"></span> Log Out
                            </a></li>
                    </ul>
                </div>
            </div>
        </nav>


        <!--<div class="col-sm-12">-->
        <form action="server/upload.php" method="post" enctype="multipart/form-data" class="form-inline" id="form_up"
              onsubmit="return file_validation('#upload_file', '#span');">

            <h1>Upload music</h1>
            <select class="form-control" id="active_pl" name="pl_id">
                <option value="no">-- Choose active playlist --</option>
                <?php foreach ($_SESSION['user_pls'] as $opt): ?>
                    <option value="<?php echo $opt['id'] ?>">
                        <?php echo $opt['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="Song's title" required>
            </div>
            <div class="form-group">
                <label for="artist">Artist</label>
                <input type="text" class="form-control" id="artist" name="artist" placeholder="Artist" required>
            </div>
            <div class="form-group">
                <label for="upload_file">Select music</label>
                <input type="file" id="upload_file" name="upload_file" accept="audio/*"/>
            </div>
            <input type="submit" value="Upload" name="upload" class="btn btn-default"
                   data-toggle="tooltip" title="Max filesize is 10MB. For good stream quality, below 5 MB is recommended">
        </form>
        <!--</div>-->
        <p id="span"></p>

        <script>
            var title, artist;
            function file_validation(input, span) {
                var ext = $(input).val().split('.').pop().toLowerCase();
                if ($.inArray(ext, ['mp3', 'ogg', 'wav']) === -1) {
                    console.log("Acceptable file type: .mp3, .ogg, .wav");
                    $(span).html("Acceptable file type: .mp3, .ogg, .wav");
                    return false;
                }

                title = $("#title").val();
                title = title.trim();
                if (!title.match("^[a-zA-Z0-9 ]*$")) {
                    $(span).html("Title is not valid, only characters, numbers and space allow");
                    return false;
                }

                artist = $("#artist").val();
                artist = artist.trim();
                if (!artist.match("^[a-zA-Z0-9 ]*$")) {
                    $(span).html("Artist is not valid, only characters, numbers and space allow");
                    return false;
                }

                var active_pl = $("#active_pl").val();
                if (!active_pl) {
                    if (confirm('You must login to create playlist. Login now?')) {
                        window.location.href = "login.php";
                    }
                    return false;
                }
                if (active_pl === "no") {
                    $(span).html("You must choose a playlist to add songs");
                    return false;
                }

                $(span).html("Uploaded");
                console.log("Uploaded");
                return true;
            }
        </script>
    </body>
</html>