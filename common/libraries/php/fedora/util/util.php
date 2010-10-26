<?php
namespace common\libraries;

if(! function_exists('today')){
	/**
	 * Returns today date as a unix time stamp. That is with hour = 0, minutes = 0 and seconds = 0
	 */
	function today(){
		$date = getdate();
		$year = $date['year'];
		$month = $date['mon'];
		$day = $date['mday'];
		return mktime(0, 0, 0, $month, $day, $year);
	}
}

if(! function_exists('this_week')){
	function this_week(){
		$date = getdate();
		$days = $date['wday'];
		$interval = new DateInterval('P'.$days.'D');
		$date = new DateTime();
		$date->sub($interval);
		return $date->getTimestamp();
	}
}

if(! function_exists('last_week')){
	function last_week($week_count = 1){
		$date = this_week();
		$week = new DateInterval('P7D');
		$date = new DateTime();
		for($i = 0; $i<$week_count; $i++){
			$date->sub($week);
		}
		return $date->getTimestamp();
	}
}

if(! function_exists('endoftime')){
	function endoftime(){
		static $result = 0;
		if(empty($result)){
			$result = mktime(24, 60, 60, 12, 31, 10000);
		}
		return $result;
	}
}