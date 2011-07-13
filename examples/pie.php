<?php
include '../mSVG.php';

$m = new mSVG();
$m->setSize(80);
$m->setCenter(250,100);
$m->setData(array(
    'Hyperspace' => 7,
    'Fire Control' => 15,
    'Gravity Well' => 5,
    'RU Drop-off' => 7,
    'Research Module' => 1,
    'Platform Module' => 2,
));

$m->setColors(array(
    'Hyperspace' => 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')',
    'Fire Control' => 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')',
    'Gravity Well' => 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')',
    'RU Drop-off' => 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')',
    'Research Module' => 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')',
    'Platform Module' => 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')',
));
$m->setPrecision(0);

header('Content-Type: image/svg+xml');
echo $m->render('pie');