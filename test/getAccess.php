<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<!DOCTYPE html>
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Connect with SoundCloud</title>
    </head>

    <body onload="window.opener.setTimeout(window.opener.SC.connectCallback, 1)">
        <b style="width: 100%; text-align: center;">This popup should automatically close in a few seconds</b>
    </body>

<!--    <script src="http://connect.soundcloud.com/sdk.js"></script>
    <script>
          SC.get("/groups/55517/tracks", {limit: 1}, function (tracks) {
              alert("Latest track: " + tracks[0].title);
          });
    </script>-->
</html>