<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class UserReportingBlock extends ReportingBlock
{
	public function count_data()
	{}
	
	public function retrieve_data()
	{}
	
	public function get_data_manager()
	{
		return UserDataManager::get_instance();
	}
	
	public function get_available_diplaymodes()
	{}
	
	public static function getDateArray($data, $format)
    {
    	foreach ($data as $key => $value)
        {
            $bla = explode('-', $value->get_date());
            $bla2 = explode(' ', $bla[2]);
            $hoursarray = explode(':', $bla2[1]);
            $bus = mktime($hoursarray[0], $hoursarray[1], $hoursarray[2], $bla[1], $bla2[0], $bla[0]);
            $arr2[$bus] ++;    
        }
        
        //sort the array
        ksort($arr2);
        foreach ($arr2 as $key => $value)
        {
            $date = date($format, $key);
            $date = (is_numeric($date)) ? $date : Translation :: get($date . 'Long');
            if (array_key_exists($date, $arr2))
                $arr2[$date] += $arr2[$key];
            else
                $arr2[$date] = $arr2[$key];
            unset($arr2[$key]);
        }
        return $arr2;
    }
}
?>