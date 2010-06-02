<?php

/**
 * Utility class to translate wildcard patterns (* = any character) to a regex and
 * the other way around.
 *  
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class Wildcard{

	/**
	 * Very simple translation. Mostly intended to reimport the export.
	 * @param unknown_type $regex
	 */
	public static function from_regex($regex){
		$result = $regex;
		$letters = 'a b c d e f g h i j k l m n o p q r s t u v w x y z';
		$letters = explode(' ', $letters);
		foreach($letters as $letter){
			$pattern = '['. strtolower($letter).strtoupper($letter) . ']';
			$result = str_ireplace($pattern, $letter, $result);
		}
		$quantifiers = '+ * ?';
		$quantifiers = explode(' ', $quantifiers);
		foreach($quantifiers as $quantifier){
			$pattern = ".$quantifier";
			$result = str_replace($pattern, '*', $result);
		}
		$pattern = '/\\.{\\d+, \\d+}/';
		$result = preg_replace($pattern, '*', $result);
		$result = self::preg_unquote($result);
		return $result;
	}
	
	public static function preg_unquote($text){
		$result = $text;
		$chars = '. \\ + * ? [ ^ ] $ ( ) { } = ! < > | : -';
		$chars = explode(' ', $chars);
		foreach($chars as $char){
			$pattern = '\\'.$char;
			$result = str_replace($pattern, $char, $result); 
		} 
		return $result;
	}
	
	public static function to_regex($regex, $is_case_sensitive){
		$result = $is_case_sensitive ? $regex : strtolower($regex);
		$star_escape = '_aa_start_aa_';
		$result = str_replace('*', $star_escape, $result);
		$result = preg_quote($result);
		$result = str_replace($star_escape, '.*', $result);
		if(!$is_case_sensitive){
			$letters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
			foreach($letters as $letter){
				$result = str_replace($letter, '['.strtoupper($letter).strtolower($letter).']', $result);
			}
		}
		return $result;
	}

	public static function has_wildcard($text){
		return strpos($text, '*') !== false;
	}
	
	/**
	 * Simple check. Mostly intended to reimport the export.
	 * @param unknown_type $regex
	 */
	public static function is_case_sensitive($regex){
		$letters = 'a b c d e f g h i j k l m n o p q r s t u v w x y z';
		$letters = explode(' ', $letters);
		foreach($letters as $letter){
			$pattern = '['. strtolower($letter).strtoupper($letter) . ']';
			$regex = str_ireplace($pattern, '', $regex);
		}
		
		foreach($letters as $letter){
			if(strpos($regex, $letter) !== false){
				return true;
			}
		}
		return false;
		
	}
	
}