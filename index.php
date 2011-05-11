<?php
include 'mSVG.php';

$m = new mSVG();
$m->setSize(80);
$m->setCenter(250,100);
/*
$m->setData(array(
    'Hyperspace' => 7,
    'Fire Control' => 15,
    'Gravity Well' => 5,
    'RU Drop-off' => 7,
    'Research Module' => 1,
    'Platform Module' => 2,
));
$m->setColors(array(
    'Hyperspace' => 'blue',
    'Fire Control' => 'red',
    'Gravity Well' => 'Yellow',
    'RU Drop-off' => 'Orange',
    'Research Module' => 'Green',
    'Platform Module' => 'Purple',
));
//$m->setData(array(40,1,1,1,1,1,1,1,30));
//$m->setData(array(40,1,1,1,1,1,1,30,20,100,20,33,33,3,2,3,34,4,23,2,3,20));
//$m->setData(array(50,10,10,10,10,10));
*/
$m->setData(array(9,2,2,6,3,1,6));

$m->setPrecision(0);
if(!empty($_GET['d'])) {
    $rawdata = explode(',',$_GET['d']);
    $data = array();
    foreach ($rawdata as $item) {
    	$inter = explode('|',$item);
    	if(count($inter) > 1) {
    		$data[$inter[0]] = $inter[1];
    	} else {
    		$data[] = $item;
    	}
    }
    $m->setData($data);
}
header('Content-Type: image/svg+xml');
echo $m->render('pie');
