<?php
class mView
{
    public $graph;
    public $slices;
    public $size;
    public $center;
    
    public function render() {
        ob_start();
	include $this->graph.'.psvg';
        $outout = ob_get_contents();
        ob_clean();
        return $outout;
    }
}
?>
