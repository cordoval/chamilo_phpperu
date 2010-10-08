<?php

/**
 * Utility functions used by the IMS formats - CP and QTI.
 * 
 * 
 * @copyright (c) 2010 University of Geneva 
 * 
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */

function require_once_all($pattern){
	if($files = glob($pattern)){
		foreach($files as $file){
			require_once $file;
		}
	}else{
		//debug($files);
	}
}

function str_right($text, $length){
	$text_length = strlen($text);
	return substr($text, $text_length - $length, $length);
}

function str_left($text, $length){
	return substr($text, 0, $length);
}

function html_trim_tag($text, $tag_){
	$result = $text;
    $tags = func_get_args();
    array_shift($tags);
    $match = true;
    while($match){
    	$match = false;
    	foreach($tags as $tag){
	    	$s = "<$tag>";
	    	$sl = strlen($s);
	    	$e = "</$tag>";
	    	$el = strlen($e);
	    	$result = trim($result);
	    	if(str_left($result, $sl) == $s && str_right($result, $el) == $e){
	    		$match = true;
	    		$result = substr($result,$sl, strlen($result) - $sl);
		    	$result = substr($result, 0, strlen($result) - $el);
	    	}
    	}
    }
	    		
    //debug(htmlentities($result));
    return $result;
    }
    
function str_safe($value, $replace = '_'){
	$result = '';
	for ($i = 0, $j = strlen($value); $i < $j; $i++) {
		$ascii = ord($value[$i]);
		$char = $value[$i];
		if(	(ord('a')<= $ascii && $ascii <=ord('z')) || 
			(ord('A')<= $ascii && $ascii <=ord('Z')) ||
			(ord('0')<= $ascii && $ascii <=ord('9')) ||
			$char == '.' || $char == '_'){
			$result .= $value[$i];
		}else{
			$result .= $replace;
		}
	}
    return $result;
}

function array_flatten(array $items, $deep = false){
	$result = array();
	foreach($items as $item){
		if(is_array($item) && $deep){
			$childs = array_flatten($item);
			$result = array_merge($result, $childs);
		}else{
			$result[] = $item;
		}
	}
	return $result;
}

function object_sort(&$objects, $name, $is_function = true){
	object_sort_class::factory($name, $is_function)->sort($objects);
}

class object_sort_class{

	public static function factory($name, $is_function){
		return new self($name, $is_function);
	}


	private $name = '';
	private $is_function = true;

	public function __construct($name, $is_function){
		$this->name = $name;
		$this->is_function = $is_function;
	}

	public function sort(&$objects){
		usort($objects, array($this, 'compare'));
	}

	public function __invoke(&$objects){
		$this->sort($objects);
	}

	protected function compare($left, $right){
		$name = $this->name;
		if($this->is_function){
			$wa = $left->$name();
			$wb = $right->$name();
		}else{
			$wa = $left->$name;
			$wb = $right->$name;

		}
		if ($wa == $wb) {
			return 0;
		}else{
			return ($wa < $wb) ? -1 : 1;
		}
	}
}



?>