<?php
require_once PATH :: get_reporting_path() . '/lib/reporting_block.class.php';

abstract class UserReportingBlock extends ReportingBlock
{

    public function get_data_manager()
    {
        return UserDataManager :: get_instance();
    }

    function get_user_id()
    {
        return $this->get_parent()->get_parameter(UserManager :: PARAM_USER_USER_ID);
    }

    public static function getDateArray($data, $format)
    {
        $login_dates = array();
        //
        //        while ($login_date = $data->next_result())
        //        {
        //            $login_dates[$login_date->get_date()]++;
        //        }


        //        foreach ($data->as_array() as $key => $value)
        //        {
        //            $dates[$value->get_date()] ++;
        //        }
        //
        //        dump($dates);


        while ($login_date = $data->next_result())
        {
            $date = date($format, $login_date->get_date());
//            dump($date);
//            $date = (is_numeric($date)) ? $date : Translation :: get($date . 'Long');

//            $login_dates[$login_date->get_date()] ++;

            if (array_key_exists($date, $login_dates))
            {
                $login_dates[$date] ++;
            }
            else
            {
                $login_dates[$date] = 1;
            }
        }

        //        //sort the array
        //        ksort($dates);
        //        foreach ($dates as $key => $value)
        //        {
        //            $date = date($format, $key);
        //            $date = (is_numeric($date)) ? $date : Translation :: get($date . 'Long');
        //            if (array_key_exists($date, $dates))
        //                $dates[$date] += $dates[$key];
        //            else
        //                $dates[$date] = $dates[$key];
        //            unset($dates[$key]);
        //        }
        return $login_dates;
    }
}
?>