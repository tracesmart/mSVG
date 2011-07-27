<?php
include '../mSVG.php';

$m = new mSVG();
$m->setDimensions(670, 200);
$m->setData(array(
    array('label' => 'Hyperspace', 'value' => '7'),
    array('label' => 'Fire Control', 'value' => '15'),
    array('label' => 'Gravity Well', 'value' => '5'),
    array('label' => 'RU Drop-off', 'value' => '7'),
    array('label' => 'Research Module', 'value' => '1'),
    array('label' => 'Platform Module', 'value' => '2'),
));

$chart = new PieChart();
$m->setChartType($chart);

header('Content-Type: image/svg+xml');
echo $m->render();