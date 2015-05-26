<?php
require_once('../vendor/autoload.php');
$client = new Everyman\Neo4j\Client('music.sb06.stations.graphenedb.com', 24789);
$client->getTransport()->setAuth('Music', '6DepBn2i4cmLeroINp4C');

$lable_song = $client->makeLabel('SONG');
$label_sv = $client->makeLabel('SV');
$date = time();
$server = "Server";
$base_url = "http://streammusic.freevnn.com/uploads/";

$dic = 'C:\xampp\htdocs\web\Assignment\uploads';
foreach (glob($dic . '/*.*') as $file) {
    $name = substr($file, strpos($file, "uploads") + 8);
    if (!strpos($name, '_')) {
        $title = $name;
        echo "Title: " . $title . "<br>";
        $node = $client->makeNode();
        $node->setProperty('title', $title)->setProperty('url', $base_url . $name)
                ->setProperty('created', $date)->setProperty('server', $server)->save();
        $label = $node->addLabels(array($lable_song, $label_sv));
        var_dump($node);
        var_dump($label);
    } else {
        $title = substr($name, 0, strpos($name, '_'));
        echo "Title: " . $title . ", ";
        $artist_ext = substr($name, strpos($name, '_') + 1);
        $artist = substr($artist_ext, 0, strpos($artist_ext, '.'));
        echo "Artist: " . $artist . "<br>";
        $node = $client->makeNode();
        $node->setProperty('title', $title)->setProperty('artist', $artist)
                ->setProperty('url', $base_url . $name)->setProperty('created', $date)->setProperty('server', $server)->save();
        $label = $node->addLabels(array($lable_song, $label_sv));
    }
}
