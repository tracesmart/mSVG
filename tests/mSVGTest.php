<?php
/**
 * Unit test for mSVG main class
 *
 * @package mSVG
 * @author Yarek Tyshchenko
 * @since 2011-09-19
 **/
include_once '../mSVG.php';
class mSVGTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests setting of chart type
     *
     * @author Yarek Tyshchenko
     **/
    public function testSetChartType()
    {
        $m = new mSVG;
        $m->setChartType(new stdClass);
    }
    
    /**
     * Tests the setting of dimensions
     *
     * @author Yarek Tyshchenko
     **/
    public function testSetDimensions()
    {
        $m = new mSVG;
        $m->setDimensions(10,10);
    }
    
    /**
     * Tests the setting of precision
     *
     * @author Yarek Tyshchenko
     **/
    public function testSetPrecision()
    {
        $m = new mSVG;
        $m->setPrecision(42);
    }
    
    /**
     * Tests setting of Data
     *
     * @author Yarek Tyshchenko
     **/
    public function testSetData()
    {
        $m = new mSVG;
        $m->setData(array('hi'));
    }
    
    /**
     * Tests rendering of mSVG
     *
     * @author Yarek Tyshchenko
     **/
    public function testRender()
    {
        $m = new mSVG;
        $chart = $this->getMock(
            'Chart',
            array('setDimensions', 'setData', 'render')
        );
        $chart->expects($this->once())
            ->method('render');
        $m->setChartType($chart);
        $m->setDimensions(10,10);
        $m->setPrecision(42);
        $m->setData(array('hi'));
        $m->render();
    }
    
    /**
     * Tests writing to file
     *
     * @author Yarek Tyshchenko
     **/
    public function testWriteToFile()
    {
        $m = new mSVG;
        $chart = $this->getMock(
            'Chart',
            array('setDimensions', 'setData', 'render')
        );
        $chart->expects($this->once())
            ->method('render')
            ->will($this->returnValue('hi'));
        $m->setChartType($chart);
        $m->writeToFile('/dev/null');
    }
    
    /**
     * Tests writing to temporary file
     *
     * @author Yarek Tyshchenko
     **/
    public function testWriteToTempFile()
    {
        $m = new mSVG;
        $chart = $this->getMock(
            'Chart',
            array('setDimensions', 'setData', 'render')
        );
        $chart->expects($this->once())
            ->method('render')
            ->will($this->returnValue('hi'));
        $m->setChartType($chart);
        $file = $m->writeToTempFile('/tmp');
        $this->assertTrue(strlen($file) > 0);
        unlink($file);
    }
} // END class mSVGTest extends PHPUnit_Framework_TestCase