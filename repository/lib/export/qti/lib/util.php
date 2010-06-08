<?php

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