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
    protected $labels = array('left' => 0, 'right' => 0);
    
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
     * Sets the daat
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setData($data)
    {
        $this->data = $data;
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
                'offset' => $offset,
                'label' => $label,
                'color' => $this->getGradientColor(),
                'position' => $this->getSlicePosition($sizeInDegrees, $offset, $this->center),
                'link' => $this->getLinkRoute($sizeInDegrees, $offset, $this->center),
            );
            $offset = $sizeInDegrees + $offset;
        }
        //echo '<pre>';print_r($slices);die;
        return $slices;
    }
    
    /**
     * undocumented function
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function getLinkRoute($size, $offset, $center)
    {
        // https://chart.googleapis.com/chart?cht=p&chd=t:40,1,1,1,1,1,1,1,1&chs=350x200&chp=-1.57&chl=World|World|World|World|World|World|World|World|World
        // https://chart.googleapis.com/chart?cht=p&chd=t:40,1,1,1,1,1,1,1,30&chs=350x200&chp=-1.57&chl=World|World|World|World|World|World|World|World|World
        $route = array();
        // Starting point
        $x = $center['x'] + cos(deg2rad($offset+$size/2)) * ($this->size-1);
        $y = $center['y'] + sin(deg2rad($offset+$size/2)) * ($this->size-1);
        $route[] = "$x,$y";
        
        // Little stub
        $x = $center['x'] + cos(deg2rad($offset+$size/2)) * ($this->size+5);
        $y = $center['y'] + sin(deg2rad($offset+$size/2)) * ($this->size+5);
        $route[] = "$x,$y";

        // Horizontal Line
        $stub = 10;
        if($offset+$size/2 > 90) {
            $x -= $stub + $this->size + cos(deg2rad($offset+$size/2)) * ($this->size+5);
            $labelx = $x - 5;
            $align = 'end';
        } else {
            $x += $stub + $this->size - cos(deg2rad($offset+$size/2)) * ($this->size+5);
            $labelx = $x + 5;
            $align = 'start';
        }
        
        $route[] = "$x,$y";
        
        return array(
            'path' => implode(' ', $route),
            'label' => array(
                'align' => $align,
                'x' => $labelx,
                'y' => $y,
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
        $linkx = $center['x'] + cos(deg2rad($offset+$size/2)) * $this->size;
        $linky = $center['y'] + sin(deg2rad($offset+$size/2)) * $this->size;
        return array(
            'lineX' => $x,
            'lineY' => $y,
            'arc' => $arcType,
            'arcX' => -($arcx),
            'arcY' => -($arcy),
            'linkX' => $linkx,
            'linkY' => $linky,
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
}
