<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<!DOCTYPE html>
<html>
    Hello
    <script src="http://connect.soundcloud.com/sdk.js"></script>
    <script>
        // initialize client with app credentials
        SC.initialize({
            client_id: '359ef1baa55fe505db2183245fe15314',
            redirect_uri: 'http://localhost/web/ass/getAccess.php'
        });

        // initiate auth popup
//        SC.connect(function () {
//            SC.get('/me', function (me) {
//                alert('Hello, ' + me.username);
//            });
//        });

//        SC.connect(function () {
//            SC.get("/groups/55517/tracks", {limit: 1}, function (tracks) {
//                alert("Latest track: " + tracks[0].title);
//            });
//        });

        SC.stream("/tracks/293", {
            autoPlay: true,
            ontimedcomments: function (comments) {
                console.log(comments[0].body);
            }
        });
    </script>
</html>