<?php

class shape{
	
	public static function polygone_to_string($polygone, $separator = ','){
		$result = array();
		foreach($polygone as $point){
			$result[] = $point[0];
			$result[] = $point[1];
		}
		return implode($separator, $result);
	}
	
	public static function string_to_polygone($text, $separator = ','){
		$result = array();
		$items = explode($separator, $text);
		for($i = 0; $i<count($items); $i += 2){
			$result[] = array(floatval($items[$i]), floatval($items[$i+1]));
		}
		if(count($result) % 2 == 1){
			$result[] = $result[0];
		}
		return $result;
	}
	
	public static function point_to_string($point, $separator = ' '){
		return implode($separator, $point);
	}
	
	public static function string_to_point($text, $separator = ' '){
		return explode($separator, $text);
	}
	
	public static function inside_polygone($polygone, $point){
		if(count($polygone) % 2 == 1){
			$polygone[] = $polygone[0];
		}
		if(count($point)<2){
			throw new Exception('Invalid point');
		}
		$count = 0;
		$x = 0;
		$y = 1;
		$p1 = $polygone[0];
		for($i = 1; $i<count($polygone); $i++){
			$p2 = $polygone[$i];
			if(	min($p1[$y], $p2[$y])<=$point[$y] && 
				$point[$y] <= max($p1[$y], $p2[$y]) &&
				$point[$x] <= max($p1[$x], $p2[$x])){
					$x_intersection = ($point[$y]-$p1[$y])*($p2[$x]-$p1[$x])/($p2[$y]-$p1[$y])+$p1[$x];
					if ($p1[$x] == $p2[$x] || $point[$x] <= $x_intersection){
              			$count++;
          			}
				}
			$p1 = $p2;
		}
		return $count != 0 && $count % 2 == 1;
	}
	
	public static function inside_rectangle($rectangle, $point){
		$x = $point[0];
		$y = $point[1];
		$left = min($rectangle[0][0], $rectangle[1][0]);
		$right = max($rectangle[0][0], $rectangle[1][0]);
		$bottom = min($rectangle[0][1], $rectangle[1][1]);
		$top = max($rectangle[0][1], $rectangle[1][1]);
		return $left<=$x && $x <=$right && $top<=$y && $y <= $bottom;
	}
	
	public static function inside_circle($center_x, $center_y, $radius, $point){
		$x = $point[0];
		$y = $point[1];
		$distance = sqrt(($x-$center_x) * ($x-$center_x) + ($y-$center_y) * ($y-$center_y));
		return $distance <= $radius;
	}
	
	public static function get_point_inside_polygon($polygone){
		if(count($polygone) == 2){
			return self::middle($polygone[0], $polygone[1]);
		}
		if(count($polygone) % 2 == 1){
			$polygone[] = $polygone[0];
		}
		$a = $polygone[0];
		$h = $polygone[1];
		$b = $polygone[2];
		$m1 = self::middle($a, $b);
		$m1 = self::middle($m1, $h);
		$m2 = array(2*$h[0]-$m1[0], 2*$h[1]-$m1[1]);
		for($i=0; $i<50; $i++){
			if(self::inside_polygone($polygone, $m1)){
				return $m1;
			}
			if(self::inside_polygone($polygone, $m2)){
				return $m2;
			}
			$m1 = self::middle($h, $m1);
			$m2 = self::middle($h, $m2);
		}
		
		debug('not found');
		return array();
	}
	
	private static function middle($a, $b){
		return array(($a[0]+$b[0])/2, ($a[1]+$b[1])/2);
	}
	
}










