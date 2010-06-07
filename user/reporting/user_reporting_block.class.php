<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class UserReportingBlock extends ReportingBlock
{	
	public function get_data_manager()
	{
		return UserDataManager::get_instance();
	}
	
	function get_user_id()
	{
		return $this->get_parent()->get_parameter(UserManager::PARAM_USER_USER_ID);	
	}
	
	public static function getDateArray($data, $format)
    {
    	foreach ($data as $key => $value)
            $arr2[$value->get_date()] ++;
        
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