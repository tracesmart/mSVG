<?php
include 'mSVG.php';

$m = new mSVG();
$m->setSize(100);
$m->setCenter(250.5,140.5);
$m->setData(array(
    'Hyperspace' => 7,
    'Fire Control' => 15,
    'Gravity Well' => 5,
    'Resource Drop-off' => 7,
    'Research Module' => 1,
    'Platform Module' => 2,
));
$m->setData(array(40,1,1,1,1,1,1,1,30));
//$m->setData(array(40,30,20,100,20,33,33,3,2,3,34,4,23,2,3,20));
//$m->setData(array(40,20));
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
//$m->setData($data);
echo $m->render('pie');
