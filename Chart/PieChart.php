<?php
/**
 * Pie Chart type of Chart
 * Calculated Pie logic
 *
 * @package mSVG
 * @author Yarek Tyshchenko
 * @since 2011-07-25
 **/
class PieChart extends Chart
{
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
    protected $_center;
    
    /**
     * Array of labels
     *
     * @var array
     **/
    protected $labels = array('left' => array(), 'right' => array());
    
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
    
    public function getSize()
    {
        if (!$this->size) {
            $this->size = 
                    $this->_dimensions['height'] * 0.4;
        }
        return $this->size;
    }
    
    /**
     * Set Center
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setCenter($x, $y)
    {
        $this->_center = array(
            'x' => $x,
            'y' => $y,
        );
    }
    
    public function getCenter()
    {
        if (!$this->_center) {
            $this->_center = array(
                'x' => $this->_dimensions['width'] / 2 -
                    $this->_dimensions['width'] / 10,
                'y' => $this->_dimensions['height'] / 2,
            );
        }
        return $this->_center;
    }
    
    /**
     * sprintf's the number
     *
     * @return float
     * @author Yarek Tyshchenko
     **/
    public function r($number)
    {
        return sprintf('%f', $number);
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
        $total = 0;
        foreach ($this->data as $item) {
            $total += $item['value'];
        }
        foreach ($this->data as $item) {
            $label = $item['label'];
            $size = $item['value'];
            if (empty($item['color'])) {
                $color = $this->getRandomColor();
            } else {
                $color = $item['color'];
            }
            $sizeInDegrees = $size / $total * 360;
            $slices[] = array(
                'size' => $size,
                'percent' => round($size / $total * 100, $this->precision),
                'circle' => ($size == $total)?true:false,
                'offset' => $offset,
                'label' => array('text' => $label),
                'color' => $color,
                //'color' => $this->getRandomColor(),
                'position' => $this->getSlicePosition($sizeInDegrees, $offset, $this->getCenter()),
                'link' => $this->getLinkRoute($sizeInDegrees, $offset, $this->getCenter()),
            );
            $offset = $sizeInDegrees + $offset;
        }
        $this->labels = $this->preprocessLabelPositions($slices);
        for($i = 0; $i < 100; $i++)
            $this->repositionLabels();
        $slices = $this->finishLinkRoute($slices);
        $slices = $this->processLabelPosition($slices);
        $slices = $this->correctPercentages($slices);
        //echo '<pre>';print_r($slices);die;
        return $slices;
    }
    /**
     * Fudges one of the values so the total would equal 100%
     *
     * @return array
     * @author Yarek Tyshchenko
     **/
    public function correctPercentages($slices)
    {
        $total = 0;
        $biggestSlice = 0;
        // Loop through all slices, find the biggest and compute the total
        foreach ($slices as $key => $slice) {
            $total += round($slice['percent'],$this->precision);
            if($slices[$biggestSlice]['size'] < $slice['size']) {
                $biggestSlice = $key;
            }
        }

        if($total > 100) {
            $slices[$biggestSlice]['percent'] -= ($total - 100);
        } else if ($total < 100) {
            $slices[$biggestSlice]['percent'] += (100 - $total);
        }
        
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
        $center = $this->getCenter();
        $limit = $center['y']*2;
        $offset = 13;
        $labelsLeft = $this->labels['left'];
        if($d) print_r('Labels Left'.PHP_EOL);
        for ($i = 0; $i < count($labelsLeft); $i++) {
            if($d) print_r($labelsLeft[$i]['position'].PHP_EOL);
            $c = 0;
            while(true) {
                if($c++ > 1000) break;
                // If label is too close to the edge
                if ($labelsLeft[$i]['position'] < $offset) {
                    $labelsLeft[$i]['position']++;
                    if($d) print_r($c.' Top Edge is '.$labelsLeft[$i]['position'].PHP_EOL);
                    continue;
                }
                
                if ($labelsLeft[$i]['position'] > $limit - $offset) {
                    $labelsLeft[$i]['position']--;
                    if($d) print_r($c.' Bottom Edge '.$labelsLeft[$i]['position'].PHP_EOL);
                    continue;
                }
                
                // If label is too close to a previous label
                if (!empty($labelsLeft[$i-1]) && $labelsLeft[$i-1]['position'] - $labelsLeft[$i]['position'] < $offset) {
                    if($d) print_r("difference between prev and current : ".($labelsLeft[$i-1]['position'] - $labelsLeft[$i]['position']).PHP_EOL);
                    $labelsLeft[$i-1]['position']++;
                    $labelsLeft[$i]['position']--;
                } else if (!empty($labelsLeft[$i+1]) && $labelsLeft[$i]['position'] - $labelsLeft[$i+1]['position'] < $offset) {
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
            $c = 0;
            while(true) {
                if($c++ > 1000) break;
                // If label is too close to the edge
                if ($labelsRight[$i]['position'] < $offset) {
                    $labelsRight[$i]['position']++;
                    if($d) print_r($c.' Top Edge is '.$labelsRight[$i]['position'].PHP_EOL);
                    continue;
                }
                
                if ($labelsRight[$i]['position'] > $limit - $offset) {
                    $labelsRight[$i]['position']--;
                    if($d) print_r($c.' Bottom Edge '.$labelsRight[$i]['position'].PHP_EOL);
                    continue;
                }
                
                // If label is too close to a previous label
                if (!empty($labelsRight[$i+1]) && $labelsRight[$i+1]['position'] - $labelsRight[$i]['position'] < $offset) {
                    if($d) print_r("difference between prev and current : ".($labelsRight[$i+1]['position'] - $labelsRight[$i]['position']).PHP_EOL);
                    $labelsRight[$i+1]['position']++;
                    $labelsRight[$i]['position']--;
                } else if (!empty($labelsRight[$i-1]) && $labelsRight[$i]['position'] - $labelsRight[$i-1]['position'] < $offset) {
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
            'lineX' => $this->r($x),
            'lineY' => $this->r($y),
            'arc' => $arcType,
            'arcX' => $this->r(-($arcx)),
            'arcY' => $this->r(-($arcy)),
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
    
    // @TODO: Remove mView related stuff
    public $graph;
    public $slices;
    
    public function render()
    {
        $this->data = $this->_data;
        $this->size = $this->getSize();
        $this->center = $this->getCenter();
        $this->slices = $this->getSlices($this->_data);
        return $this->getView();
    }
    
} // END class PieChart