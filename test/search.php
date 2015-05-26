<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$search = 'http://api.soundcloud.com/tracks.json?client_id=359ef1baa55fe505db2183245fe15314&q=sugar,maroon5&limit=20';
$result = file_get_contents($search);
//var_dump($result);
//echo "<br><br><br><br>";
$track = json_decode($result, TRUE);
//var_dump($track);

foreach ($track as $value) {
//    var_dump($key);
//    var_dump($value);
//    echo $value[$key] . ", ";
    echo $value['id'] . ",  ";
    echo $value['permalink'] . ",  ";
    echo $value['title']. ",  ";
    echo $value['original_format']. ",  ";
    echo $value['artwork_url']. ",  ";
//    echo $value['avatar_url']. ",  ";
    echo $value['streamable']. ",  ";
    echo "<br>";
}