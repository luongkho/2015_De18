<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("location: info.php");
}
$rand = mt_rand(0, 1000);
//echo $rand;
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <style>
            .con   {
                margin-top: 10%;
            }
            #error  {
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
                    </ul>
                </div>
            </div>
        </nav>

        <div class="con" id="form">
            <form class="form-horizontal" id="login" method="post">
                <div class="form-group">
                    <label for="user_name" class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="user_name" placeholder="Charactes, numbers, _" name="user" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pass" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" id="pass" placeholder="Charactes, numbers, _" name="pass" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="code" class="col-sm-2 sr-only">Validation code</label>
                    <div class="col-sm-8 col-sm-offset-2">
                        <input type="text" class="form-control" id="code" disabled>
                    </div>
                </div>
                <div class="form-group">
                    <label for="validation" class="col-sm-2 control-label">Validation code</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="validation" placeholder="Enter code above">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-8">
                        <input type="submit" value="Login" class="btn btn-default">
                        <a class="btn btn-primary" href="signup.php">Create New Account</a>
                    </div>
                </div>
            </form>        
        </div>

        <span class="col-sm-offset-3" id="error">
            <?php // echo $error;  ?> <br><br>
        </span>

        <script>
            var err = $("#error");
            $(document).ready(function () {
                $("#code").val(<?php echo $rand; ?>);
            });

            $("#login").submit(function (e) {
                e.preventDefault();
                err.html("Please wait...");
                console.log("Please wait...");

                var user = $("#user_name").val().trim();
                var pass = $("#pass").val();
                if (!user.match("^[a-zA-Z0-9_]*$") || !pass.match("^[a-zA-Z0-9_]*$")) {
                    err.html("Username and Password allow only characters, numbers, _ <br>");
                    return;
                }

                var code = $("#validation").val();
                if (code !== $("#code").val()) {
                    err.html("Wrong code <br>");
                    return;
                }
//                err.html("");
                console.log("Pass");

                var data = {"action": "login", "user": user, "pass": pass};
                $.post("server/server.php", data, function (a) {
//                    try {
                    err.html("Please wait...");
                    var result = JSON.parse(a);
                    console.log(result.length);
                    if (result.length < 1) {
                        err.html("Account does not exist or wrong Username/Password");
                        return;
                    }
//                        } else {
                    console.log(result);
                    err.html("Login success, redirect page");

                    if (result[0].role === "normal") {
                        $.post('server/server.php', {'action': 'user_session', 'user_id': result[0].id, 'ad': 0}, function () {
                            window.location.href = "info.php";
                        });
                    } else {
                        $.post('server/server.php', {'action': 'user_session', 'user_id': result[0].id, 'ad': 1}, function () {
                            window.location.href = "info.php";
                        });
                    }
//                        }
                    $("#code").val(<?php echo $rand2 = mt_rand(0, 1000); ?>);
//                    }
//                    catch (err) {
//                        err.html("Error while searching server!");
//                    }
                });
            });
        </script>

    </body>
</html>