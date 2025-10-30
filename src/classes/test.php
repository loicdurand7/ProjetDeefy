<?php
declare(strict_types=1);

//require_once 'InvalidPropertyNameException';
require_once 'AudioTrack.php';
require_once 'AlbumTrack.php';
require_once 'PodcastTrack.php';
require_once 'Renderer.php';
require_once 'AudioTrackRenderer.php';
require_once 'AlbumTrackRenderer.php';
require_once 'PodcastRenderer.php';

$piste1 = new iutnc\deefy\audio\tracks\AlbumTrack('titre1a', 't-rex-roar.mp3', 'album1', 33);
$piste1->setArtiste('artiste1');


echo '<pre>'; // si pas xdebug

//var_dump($piste1);

//var_dump($piste1->__toString());

//echo $piste1->__toString();

echo $piste1; // methode magique : echo attend un string donc appel automatique de __toString()
echo '<br>';
print $piste1; 
echo '<br>';

printf('avec printf : %s', $piste1);

echo '<br>';


$affichage = new AlbumTrackRenderer($piste1);

echo $affichage->render(2); // affichage long



$piste2 = new PodcastTrack('titre2p', 't-rex-roar.mp3', 'auteur2', '01/01/2001');
var_dump($piste2);

$affichage2 = new PodcastRenderer($piste2);

echo $affichage2->render(2); // affichage long