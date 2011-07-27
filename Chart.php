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
     * Default view file
     * @var string
     */
    protected $_defaultViewFile;
    
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
    public function setViewFile($viewFile)
    {
        $this->_viewFile = $viewFile;
    }
    
    /**
     * Returns a default view file if one is not set
     * @return string ViewFile
     */
    public function getViewFile()
    {
        if (!$this->_viewFile) {
            $this->_viewFile = 'View/'.$this->_defaultViewFile.'.psvg';
        }
        return $this->_viewFile;
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
        include $this->getViewFile();
        $outout = ob_get_clean();
        return $outout;
    }
} // END abstract class Chart