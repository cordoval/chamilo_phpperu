<?php

require_once dirname (__FILE__) . '/../user_reporting_block.class.php';

class UserNoOfLoginsMonthReportingBlock extends UserReportingBlock
{
	public function count_data()
	{
		require_once (dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $months = self :: getDateArray($trackerdata, 'F');

        return Reporting :: getSerieArray($months);
	}	
	
	public function retrieve_data()
	{
		return count_data();		
	}
	
	function get_application()
	{
		return UserManager::APPLICATION_NAME;
	}

    /**
     * Splits given data into a given date format
     * @param <type> $data
     * @param <type> $format
     * @return <type>
     */
    public static function getDateArray($data, $format)
    {
        foreach ($data as $key => $value)
        {
            $bla = explode('-', $value->get_date());
            $bla2 = explode(' ', $bla[2]);
            $hoursarray = explode(':', $bla2[1]);
            $bus = mktime($hoursarray[0], $hoursarray[1], $hoursarray[2], $bla[1], $bla2[0], $bla[0]);
            //            $date = date($format,mktime($hoursarray[0],$hoursarray[1],$hoursarray[2],$bla[1],$bla2[0],$bla[0]));
            //            $date = (is_numeric($date))?$date:Translation :: get($date.'Long');
            //            //dump($date);
            //            if (array_key_exists($date, $arr))
            //                $arr[$date][0]++;
            //            else
            //                $arr[$date][0] = 1;


            $arr2[$bus][0] ++;
            //            if (array_key_exists($bus,$arr2))
        //                $arr2[$bus][0]++;
        //            else
        //                $arr2[$bus][0] = 1;
        }
        //sort the array
        ksort($arr2);
        foreach ($arr2 as $key => $value)
        {
            $date = date($format, $key);
            $date = (is_numeric($date)) ? $date : Translation :: get($date . 'Long');
            if (array_key_exists($date, $arr2))
                $arr2[$date][0] += $arr2[$key][0];
            else
                $arr2[$date][0] = $arr2[$key][0];
            unset($arr2[$key]);
        }
        return $arr2;
    }
}
?>
