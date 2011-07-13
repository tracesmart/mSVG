<?php
/**
 * Logic for drawing bar charts
 *
 * @package mSVG
 * @author Yarek Tyshchenko
 * @since 2011-07-12
 **/
class BarChart extends Chart
{
    /**
     * Renders the view file into SVG
     *
     * @return String
     * @author Yarek Tyshchenko
     **/
    public function render()
    {
        $this->graphX = 200;
        $this->graphY = 30;
        $this->graphWidth = $this->getWidth() - $this->graphX - 20;
        $this->graphHeight = $this->getHeight() - $this->graphY - 20;
        
        $this->rowHeight = ($this->graphHeight / count($this->_data));
        $this->barHeight = $this->rowHeight/1.6;
        $this->barOffset = $this->rowHeight/2 - $this->barHeight/2;
        
        foreach ($this->_data as $key => $item) {
            $yBarPosition = $key * $this->rowHeight + $this->barOffset + $this->graphY;
            $this->data[] = array(
                'rowPosition' => $key * $this->rowHeight + $this->graphY,
                'yBarPosition' => $yBarPosition,
                'xBarPosition' => $this->graphX,
                'barValue' => $item['value'],
                'barLabel' => $item['label'],
                'barWidth' => $this->_normalise($item['value']),
                'barColor' => 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')',
                //'barColor' => '#4F81BD',
            );
        }
        return $this->getView();
    }
    
    /**
     * Normalise the value acording to maximum
     *
     * @return string
     * @author Yarek Tyshchenko
     **/
    private function _normalise($value)
    {
        return $value / $this->max * $this->graphWidth;
    }
    
    /**
     * Overrides Parent's setData
     * To find out the MAX of array
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setData($data)
    {
        parent::setData($data);
        $max = 1;
        foreach ($data as $item) {
            if ($item['value'] > $max) {
                $max = $item['value'];
            }
        }
        $this->max = $max;
    }
    
} // END class BarChart