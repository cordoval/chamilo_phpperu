<?php
/*
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

function array_get($items, $names){
	$result = array();
	$args = func_get_args();
	$args = array_shift($args);
	foreach($args as $name){
		$result[$name] = $items[$name];
	}
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





*/

?>