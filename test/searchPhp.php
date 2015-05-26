<?php

session_start();
echo "Hello<br>";
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '\Services\Soundcloud.php';

// create a client object with your app credentials
$client = new Services_Soundcloud('359ef1baa55fe505db2183245fe15314', '9b4a324ab436030a3622cefc03a9e52c', 'http://localhost/web/ass/searchPhp.php');
$client->setDevelopment(FALSE);

$oauthURL = $client->getAuthorizeUrl();
echo "<p>";
echo "<a href = '$oauthURL'> Connect to SoundCloud </a>";

try {
    $accessToken = $client->accessToken($_GET['code'], array(
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ));
    var_dump($accessToken);
} catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
    exit($e->getMessage());
}

//try {
//    if (isset($_GET['code'])) {
////        $accessToken = $client->accessToken($_GET['code']);
//        $accessToken = $_GET['code'];
//        var_dump($accessToken);
//    } else {
//        echo "Click link to get acces";
//    }
//} catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
//    exit($e->getMessage());
//}

//try {
//    $me = json_decode($client->get('me'), true);
//    var_dump($me);
//} catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
//    exit($e->getMessage());
//}

// find all sounds of buskers licensed under 'creative commons share alike'
//try {
//    $tracks = $client->get('tracks', array('q' => 'buskers'));
//} catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
//    exit($e->getMessage());
//}

