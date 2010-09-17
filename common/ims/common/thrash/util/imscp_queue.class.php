<?php
/*
class ImscpQueue
{
    private $todo = array();
    private $done = array();

    function __construct($directory)
    {
    }
    
    public function push($value){
    	if(in_array($value, $this->done) || in_array($value, $this->todo))
    		return 0;
    		
    	return array_push($this->todo, $value);
    }

    public function push_all(array $items){
    	foreach($items as $item)
    		$this->push($item);
    }
    
    public function pop(){
    	$result = array_pop($this->todo);
    		
        if(!is_null($result) && !in_array($result, $this->done))
        	array_push($this->done, $result);    		
    
    	return $result;
    }
    
    public function all(){
    	return $this->todo;
    }
    
    public function clear(){
	    $this->todo = array();
	    $this->done = array();
    }
*/
    
}







?>