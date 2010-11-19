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
		$year = $date['year'];
		$month = $date['mon'];
		$mdays = $date['mday'];
		$days = $date['wday'];
		$result = mktime(0, 0, 0, $month, $mdays - $days+1, $year);
		return $result;
	}
}

if(! function_exists('last_week')){
	function last_week($week_count = 1){
		$date = getdate(this_week());
		$year = $date['year'];
		$month = $date['mon'];
		$mdays = $date['mday'];
		$days = $date['wday'];
		$result = mktime(0, 0, 0, $month, $mdays - 7*$week_count, $year);
		return $result;
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