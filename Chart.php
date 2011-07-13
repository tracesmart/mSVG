<?php
/**
 * Abstract chart class
 *
 * @package mSVG
 * @author Yarek Tyshchenko
 * @since 2011-07-12
 **/
abstract class Chart
{
    /**
     * Array of dimensions
     *
     * @var Array
     **/
    protected $_dimensions;
    
    /**
     * Holds the Data to plot
     *
     * @var Array
     **/
    protected $_data;
    
    /**
     * Stores the view
     *
     * @var string
     **/
    protected $_viewFile;
    
    /**
     * Sets the Chart dimensions
     *
     * @return Void
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
     * Return width of the graph
     *
     * @return string
     * @author Yarek Tyshchenko
     **/
    public function getWidth()
    {
        return $this->_dimensions['width'];
    }
    
    /**
     * Return the height of the graph
     *
     * @return void
     * @author Yarek Tyshchenko
     **/
    public function getHeight()
    {
        return $this->_dimensions['height'];
    }
    
    /**
     * Sets the Data to plot
     *
     * @return Void
     * @author Yarek Tyshchenko
     **/
    public function setData($data)
    {
        $this->_data = $data;
    }
    
    /**
     * Returns the Data array
     *
     * @return Array
     * @author Yarek Tyshchenko
     **/
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * Sets the view file
     *
     * @return Void
     * @author Yarek Tyshchenko
     **/
    public function setView($viewFile)
    {
        $this->_viewFile = $viewFile;
    }
    
    /**
     * Render the view file with local vars
     *
     * @return String
     * @author Yarek Tyshchenko
     **/
    public function getView()
    {
        ob_start();
        include 'View/'.$this->_viewFile.'.psvg';
        $outout = ob_get_clean();
        return $outout;
    }
} // END abstract class Chart