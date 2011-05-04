<?php
include 'mView.php';
class mSVG
{
    /**
     * Data
     *
     * @var array
     **/
    protected $data;
    
    /**
     * Radius of the disk
     *
     * @var int
     **/
    protected $size;
    
    /**
     * Center of the pie
     *
     * @var string
     **/
    protected $center = array('x' => 0, 'y' => 0);
    
    /**
     * Array of labels
     *
     * @var array
     **/
    protected $labels = array('left' => array(), 'right' => array());
    
    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $color = array(
        'start' => array('r' => 255,'g' => 153,'b' => 0),
        'end' => array('r' => 255,'g' => 235,'b' => 204),
        'iteration' => 0,
    );
    
    /**
     * Colors array
     *
     * @var array
     **/
    protected $colors;
    
    /**
     * Sets the radius of the disk
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setSize($size)
    {
        $this->size = $size;
    }
    
    /**
     * Set Center
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setCenter($x, $y)
    {
        $this->center['x'] = $x;
        $this->center['y'] = $y;
    }
    
    /**
     * Sets the data
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setData($data)
    {
        $this->data = $data;
    }
    
    /**
     * Sets the colors for data
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setColors($colors)
    {
        $this->colors = $colors;
    }
    
    /**
     * Get slices from data
     *
     * @return array
     * @author Yarek Tyshchenko
     **/
    public function getSlices()
    {
        $slices = array();
        $offset = -90;
        foreach ($this->data as $label => $size) {
            $sizeInDegrees = $size / array_sum($this->data) * 360;
            $slices[] = array(
                'size' => $size,
                'percent' => round($size / array_sum($this->data) * 100),
                'offset' => $offset,
                'label' => array('text' => $label),
                'color' => $this->getColorForSliceLabel($label),
                //'color' => $this->getRandomColor(),
                'position' => $this->getSlicePosition($sizeInDegrees, $offset, $this->center),
                'link' => $this->getLinkRoute($sizeInDegrees, $offset, $this->center),
            );
            $offset = $sizeInDegrees + $offset;
        }
        $this->labels = $this->preprocessLabelPositions($slices);
        for($i = 0; $i < 100; $i++)
            $this->repositionLabels();
        $slices = $this->finishLinkRoute($slices);
        $slices = $this->processLabelPosition($slices);
        //echo '<pre>';print_r($slices);die;
        return $slices;
    }
    
    /**
     * Reposition Labels
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function repositionLabels()
    {
        $d = 0;
        if($d) echo '<pre>';
        $limit = $this->center['y']*2;
        $offset = 13;
        $labelsLeft = $this->labels['left'];
        if($d) print_r('Labels Left'.PHP_EOL);
        for ($i = 0; $i < count($labelsLeft); $i++) {
            if(is_null(@$labelsLeft[$i-1]) || is_null(@$labelsLeft[$i+1])) {
                continue;
            }
            if($d) print_r($labelsLeft[$i]['position'].PHP_EOL);
            $c = 0;
            while(true) {
                if($c++ > 1000) break;
                // If label is too close to the edge
                if ($labelsLeft[$i]['position'] < $offset * 2) {
                    $labelsLeft[$i]['position']++;
                    if($d) print_r($c.' Top Edge is '.$labelsLeft[$i]['position'].PHP_EOL);
                    continue;
                }
                
                if ($labelsLeft[$i]['position'] > $limit - $offset*2) {
                    $labelsLeft[$i]['position']--;
                    if($d) print_r($c.' Bottom Edge '.$labelsLeft[$i]['position'].PHP_EOL);
                    continue;
                }
                
                // If label is too close to a previous label
                if ($labelsLeft[$i-1]['position'] - $labelsLeft[$i]['position'] < $offset) {
                    if($d) print_r("difference between prev and current : ".($labelsLeft[$i-1]['position'] - $labelsLeft[$i]['position']).PHP_EOL);
                    $labelsLeft[$i-1]['position']++;
                    $labelsLeft[$i]['position']--;
                } else if ($labelsLeft[$i]['position'] - $labelsLeft[$i+1]['position'] < $offset) {
                    if($d) print_r("difference between current and next : ".($labelsLeft[$i]['position'] - $labelsLeft[$i+1]['position']).PHP_EOL);
                    $labelsLeft[$i]['position']++;
                    $labelsLeft[$i+1]['position']--;
                } else {
                    if($d) print_r($c.' Break'.PHP_EOL);
                    break;
                }
                
            }
        }
        $this->labels['left'] = $labelsLeft;
        
        if($d) print_r('Labels Right'.PHP_EOL);
        $labelsRight = $this->labels['right'];
        for ($i = 0; $i < count($labelsRight); $i++) {
            if(is_null(@$labelsRight[$i-1]) || is_null(@$labelsRight[$i+1])) {
                continue;
            }
            $c = 0;
            while(true) {
                if($c++ > 1000) break;
                // If label is too close to the edge
                if ($labelsRight[$i]['position'] < $offset * 2) {
                    $labelsRight[$i]['position']++;
                    if($d) print_r($c.' Top Edge is '.$labelsRight[$i]['position'].PHP_EOL);
                    continue;
                }
                
                if ($labelsRight[$i]['position'] > $limit - $offset*2) {
                    $labelsRight[$i]['position']--;
                    if($d) print_r($c.' Bottom Edge '.$labelsRight[$i]['position'].PHP_EOL);
                    continue;
                }
                
                // If label is too close to a previous label
                if ($labelsRight[$i+1]['position'] - $labelsRight[$i]['position'] < $offset) {
                    if($d) print_r("difference between prev and current : ".($labelsRight[$i+1]['position'] - $labelsRight[$i]['position']).PHP_EOL);
                    $labelsRight[$i+1]['position']++;
                    $labelsRight[$i]['position']--;
                } else if ($labelsRight[$i]['position'] - $labelsRight[$i-1]['position'] < $offset) {
                    if($d) print_r("difference between current and next : ".($labelsRight[$i]['position'] - $labelsRight[$i-1]['position']).PHP_EOL);
                    $labelsRight[$i]['position']++;
                    $labelsRight[$i-1]['position']--;
                } else {
                    if($d) print_r($c.' Break'.PHP_EOL);
                    break;
                }
                
            }
        }
        $this->labels['right'] = $labelsRight;
        if($d) die;
    }
    
    /**
     * Finish connecting link route
     *
     * @return array
     * @author Yarek Tyshchenko
     **/
    public function finishLinkRoute($slices)
    {
        
        foreach($this->labels['left'] as $label) {
            $y = round($label['position'])-0.5;
            $x = $slices[$label['key']]['link']['label']['x'] + 10;
            $slices[$label['key']]['link']['path'] .= " $x,$y ".($x - 5).",$y";
        }
        foreach($this->labels['right'] as $label) {
            $y = round($label['position'])-0.5;
            $x = $slices[$label['key']]['link']['label']['x'] - 10;
            $slices[$label['key']]['link']['path'] .= " $x,$y ".($x + 5).",$y";
        }
        return $slices;
    }
    
    /**
     * Process offset label position
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function processLabelPosition($slices)
    {
        $labels = array_merge($this->labels['left'], $this->labels['right']);
        foreach($labels as $label) {
            $slices[$label['key']]['link']['label']['y'] = $label['position'];
        }
        return $slices;
    }
    
    /**
     * Preprocesses positions of labels
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function preprocessLabelPositions($slices)
    {
        $labelsLeft = array();
        $labelsRight = array(); 
        foreach ($slices as $key => $slice) {
            if ($slice['link']['label']['align'] == 'end') {
                $labelsLeft[] = array(
                    'key' => $key,
                    'position' => $slice['link']['label']['y'],
                    'preferred' => $slice['link']['label']['y'],
                    'label' => $slice['label']['text'],
                    );
            } else {
                $labelsRight[] = array(
                    'key' => $key,
                    'position' => $slice['link']['label']['y'],
                    'preferred' => $slice['link']['label']['y'],
                    'label' => $slice['label']['text'],
                    );
            }
        }
        return $labels = array('left' => $labelsLeft, 'right' => $labelsRight);
    }
    
    /**
     * undocumented function
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function getLinkRoute($size, $offset, $center)
    {
        $route = array();
        // Starting point
        $x = $center['x'] + cos(deg2rad($offset+$size/2)) * ($this->size-1);
        $y = $center['y'] + sin(deg2rad($offset+$size/2)) * ($this->size-1);
        //$route[] = (floor($x)+0.5).','.(floor($y) + 0.5);
        $route[] = $x.','.$y;
        
        // Little stub
        $x = $center['x'] + cos(deg2rad($offset+$size/2)) * ($this->size+5);
        $y = $center['y'] + sin(deg2rad($offset+$size/2)) * ($this->size+5);
        $route[] = (floor($x)+0.5).','.(floor($y) + 0.5);

        // Horizontal Line
        $stub = 5;
        if($offset+$size/2 > 90) {
            $x -= $stub + $this->size + cos(deg2rad($offset+$size/2)) * ($this->size+5);
            $labelx = $x - 20;
            $align = 'end';
        } else {
            $x += $stub + $this->size - cos(deg2rad($offset+$size/2)) * ($this->size+5);
            $labelx = $x + 20;
            $align = 'start';
        }
        
        $route[] = round($x).','.(floor($y) + 0.5);
        
        return array(
            'path' => implode(' ', $route),
            'label' => array(
                'align' => $align,
                'x' => round($labelx),
                'y' => (floor($y) + 0.5),
            ),
        );
    }
    
    /**
     * Returns positional data for a slice
     *
     * @return array
     * @author Yarek Tyshchenko
     **/
    public function getSlicePosition($size, $offset, $center)
    {
        $angle = deg2rad($offset + $size);
        
        if($size > 180) {
            $arcType = '1';
        } else {
            $arcType = '0';
        }

        $x = cos($angle) * $this->size;
        $y = sin($angle) * $this->size;
        $arcx = $x - (cos(deg2rad($offset)) * $this->size);
        $arcy = $y - (sin(deg2rad($offset)) * $this->size);
        return array(
            'lineX' => sprintf("%.0f", $x),
            'lineY' => sprintf("%.0f", $y),
            'arc' => $arcType,
            'arcX' => -($arcx),
            'arcY' => -($arcy),
        );
    }

    /**
     * Returns a random hex color
     *
     * @return hex Color
     * @author Yarek Tyshchenko
     **/
    public function getRandomHexColor()
    {
        return '#'.dechex(rand(0,255)).dechex(rand(0,255)).dechex(rand(0,255));
    }
    
    /**
     * Get random color
     *
     * @return color
     * @author Yarek Tyshchenko
     **/
    public function getRandomColor()
    {
        return 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')';
    }
    
    /**
     * Returns the color for a slice Label
     *
     * @return color
     * @author Yarek Tyshchenko
     **/
    public function getColorForSliceLabel($label)
    {
        return $this->colors[$label];
    }
    
    /**
     * Returns a decreasing gradient of color
     *
     * @return string
     * @author Yarek Tyshchenko
     **/
    public function getGradientColor()
    {
        $total = count($this->data)-1;
        $r = (($this->color['end']['r'] - $this->color['start']['r'])/$total) * $this->color['iteration'];
        $g = (($this->color['end']['g'] - $this->color['start']['g'])/$total) * $this->color['iteration'];
        $b = (($this->color['end']['b'] - $this->color['start']['b'])/$total) * $this->color['iteration'];
        $color = array(
            'r' => round($this->color['start']['r']+$r),
            'g' => round($this->color['start']['g']+$g),
            'b' => round($this->color['start']['b']+$b),
        );
        $this->color['iteration']++;
        return 'rgb('.implode(',',$color).')';
    }
    
    /**
     * Renders a view file
     *
     * @return string
     * @author Yarek Tyshchenko
     **/
    public function render($graph)
    {
        $mView = new mView();
        $mView->graph = $graph;
        $mView->size = $this->size;
        $mView->center = $this->center;
        $mView->slices = $this->getSlices($this->data);
        return $mView->render();
    }
    
    /**
     * Renders an SVG and writes it to a file
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function writeToFile($file, $graph)
    {
        file_put_contents($file, $this->render($graph));
    }
}
