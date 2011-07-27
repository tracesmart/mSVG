<?php
include 'Chart.php';
include 'Chart/BarChart.php';
include 'Chart/PieChart.php';
class mSVG
{
    /**
     * Holds the Chart Type class
     *
     * @var ChartType
     **/
    private $_chart;
    
    /**
     * Sets the chart type class
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setChartType($chart)
    {
        $this->_chart = $chart;
    }
    
    /**
     * Global Chart Dimensions
     *
     * @var Array
     **/
    private $_dimensions;
    
    /**
     * Sets the global dimensions
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setDimensions($width, $height)
    {
        $this->_dimensions = array(
            'width' => $width,
            'height' => $height,
        );
    }
    
    /**
     * Data
     *
     * @var array
     **/
    protected $data;
    
    /**
     * Label percentage rounding precision
     *
     * @var int
     **/
    protected $precision;
    
    /**
     * Sets the label rounding precision
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function setPrecision($precision)
    {
        $this->precision = $precision;
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
     * Renders a view file
     *
     * @return string
     * @author Yarek Tyshchenko
     **/
    public function render()
    {
        $this->_chart->setDimensions($this->_dimensions['width'], $this->_dimensions['height']);
        $this->_chart->precision = $this->precision;
        $this->_chart->setData($this->data);
        return $this->_chart->render();
    }
    
    /**
     * Renders an SVG and writes it to a file
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function writeToFile($file)
    {
        file_put_contents($file, $this->render());
    }
    
    /**
     * Writes the SVG to a temp file in a specified dir
     * @return string Filename
     */
    public function writeToTempFile($dir)
    {
        $file = tempnam($dir, 'graph_');
        $this->writeToFile($file);
        return $file;
    }
}
