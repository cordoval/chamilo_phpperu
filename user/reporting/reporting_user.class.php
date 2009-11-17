<?php
/**
 * $Id: reporting_user.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.reporting
 */

class ReportingUser
{

    function ReportingUser()
    {
    }

    /**
     * Checks if a given start date is greater than a given end date
     * @param <type> $start_date
     * @param <type> $end_date
     * @return <type>
     */
    public static function greaterDate($start_date, $end_date)
    {
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        if ($start - $end > 0)
            return 1;
        else
            return 0;
    }

    /**
     * Returns all the active and inactive users
     * @param <type> $params
     * @return <type>
     */
    public static function getActiveInactive($params)
    {
        $udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $active[Translation :: get('Active')][0] = 0;
        $active[Translation :: get('Inactive')][0] = 0;
        while ($user = $users->next_result())
        {
            if ($user->get_active())
            {
                $active[Translation :: get('Active')][0] ++;
            }
            else
            {
                $active[Translation :: get('Inactive')][0] ++;
            }
        }
        return Reporting :: getSerieArray($active);
    } //getActiveInactive


    /**
     * Returns the number of users
     * @return <type>
     */
    public static function getNoOfUsers()
    {
        $udm = UserDataManager :: get_instance();

        $arr[Translation :: get('NumberOfUsers')][] = $udm->count_users();

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the number of logins
     * @return <type>
     */
    public static function getNoOfLogins()
    {
        require_once (dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $arr[Translation :: get('Logins')][] = sizeof($trackerdata);

        return Reporting :: getSerieArray($arr);
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

    /**
     * Returns the number of logins per month
     * @return <type>
     */
    public static function getNoOfLoginsMonth()
    {
        require_once (dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $months = self :: getDateArray($trackerdata, 'F');

        return Reporting :: getSerieArray($months);
    }

    /**
     * Returns the number of logins per day
     * @return <type>
     */
    public static function getNoOfLoginsDay()
    {
        require_once (dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $days = self :: getDateArray($trackerdata, 'l');
        $new_days = array();

        $day_names = array(Translation :: get('MondayLong'), Translation :: get('TuesdayLong'), Translation :: get('WednesdayLong'), Translation :: get('ThursdayLong'), Translation :: get('FridayLong'), Translation :: get('SaturdayLong'), Translation :: get('SundayLong'));

        foreach ($day_names as $name)
        {
            $new_days[$name] = $days[$name] ? $days[$name] : array(0);
        }
        return Reporting :: getSerieArray($new_days);
    }

    /**
     * Returns the number of logins per hour
     * @return <type>
     */
    public static function getNoOfLoginsHour()
    {
        require_once (dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        $condition = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        $hours = self :: getDateArray($trackerdata, 'G');

        ksort($hours);

        return Reporting :: getSerieArray($hours);
    }

    /**
     * returns the number of users with and without picture
     * @return <type>
     */
    public static function getNoOfUsersPicture()
    {
        $udm = UserDataManager :: get_instance();
        $users = $udm->retrieve_users();
        $picturetext = Translation :: get('Picture');
        $nopicturetext = Translation :: get('NoPicture');
        $picture[$picturetext][0] = 0;
        $picture[$nopicturetext][0] = 0;
        while ($user = $users->next_result())
        {
            if ($user->get_picture_uri())
            {
                $picture[$picturetext][0] ++;
            }
            else
            {
                $picture[$nopicturetext][0] ++;
            }
        }
        return Reporting :: getSerieArray($picture);
    }

    /**
     * Returns the number of users subscribed to a course
     * @return <type>
     */
    public static function getNoOfUsersSubscribedCourse()
    {
        require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
        $udm = UserDataManager :: get_instance();
        $users = $udm->count_users();

        $wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->count_distinct_course_user_relations();

        $arr[Translation :: get('UsersSubscribedToCourse')][] = $courses;
        $arr[Translation :: get('UsersNotSubscribedToCourse')][] = $users - $courses;

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the user information about a specified user
     * @param <type> $params
     * @return <type>
     */
    public static function getUserInformation($params)
    {
        $uid = $params[ReportingManager :: PARAM_USER_ID];
        //$uid = 2;
        require_once Path :: get_admin_path() . '/trackers/online_tracker.class.php';
        $udm = UserDataManager :: get_instance();
        $tracking = new OnlineTracker();

        $items = $tracking->retrieve_tracker_items();
        foreach ($items as $item)
        {
            if ($item->get_user_id() == $uid)
            {
                $online = 1;
            }
        }

        $user = $udm->retrieve_user($uid);

        $arr[Translation :: get('Name')][] = $user->get_fullname();
        $arr[Translation :: get('Email')][] = '<a href="mailto:' . $user->get_email() . '" >' . $user->get_email() . '</a>';
        $arr[Translation :: get('Phone')][] = $user->get_phone();
        //$arr[Translation :: get('Status')] = $user->get_status_name();
        $arr[Translation :: get('Online')][] = ($online) ? Translation :: get('Online') : Translation :: get('Offline');

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the platform statistics from a specified user
     * @param <type> $params
     * @return <type>
     */
    public static function getUserPlatformStatistics($params)
    {
        $uid = $params[ReportingManager :: PARAM_USER_ID];
        require_once (dirname(__FILE__) . '/../trackers/login_logout_tracker.class.php');
        require_once (dirname(__FILE__) . '/../trackers/visit_tracker.class.php');
        $conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_USER_ID, $uid);
        $conditions[] = new EqualityCondition(LoginLogoutTracker :: PROPERTY_TYPE, 'login');
        $condition = new AndCondition($conditions);
        $tracker = new LoginLogoutTracker();
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        foreach ($trackerdata as $key => $value)
        {
            if (! $firstconnection)
            {
                $firstconnection = $value->get_date();
                $lastconnection = $value->get_date();
            }
            if (! self :: greaterDate($value->get_date(), $firstconnection))
            {
                $firstconnection = $value->get_date();
            }
            else
                if (self :: greaterDate($value->get_date(), $lastconnection))
                {
                    $lastconnection = $value->get_date();
                }
        }
        $arr[Translation :: get('FirstConnection')][] = $firstconnection;
        $arr[Translation :: get('LastConnection')][] = $lastconnection;
        unset($conditions);
        unset($condition);
        $tracker = new VisitTracker();

        $condition = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $uid);
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        $arr[Translation :: get('TimeOnPlatform')][] = self :: get_total_time($trackerdata);

        return Reporting :: getSerieArray($arr);
    }

    public static function getUserCourseStatistics($params)
    {
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        require_once (dirname(__FILE__) . '/../trackers/visit_tracker.class.php');
        $tracker = new VisitTracker();

        $conditions[] = new LikeCondition(VisitTracker :: PROPERTY_LOCATION, '&course=' . $course_id);
        $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
        $condition = new AndCondition($conditions);
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        $count = 0;
        foreach ($trackerdata as $key => $value)
        {
            $count ++;
            if (! $firstconnection)
            {
                $firstconnection = $value->get_enter_date();
                $lastconnection = $value->get_leave_date();
            }
            if (self :: greaterDate($value->get_leave_date(), $lastconnection))
                $lastconnection = $value->get_leave_date();
            if (self :: greaterDate($firstconnection, $value->get_enter_date()))
                $firstconnection = $value->get_enter_date();
        }

        //        unset($conditions);
        //        unset($condition);
        //        $conditions[] = new LikeCondition(VisitTracker :: PROPERTY_LOCATION,'&course='.$course_id);
        //        $conditions[] = new EqualityCondition(VisitTracker::PROPERTY_USER_ID,$uid);
        //        $condition = new AndCondition($conditions);
        //        $trackerdata = $tracker->retrieve_tracker_items($condition);


        $arr[Translation :: get('FirstAccessToCourse')][] = $firstconnection;
        $arr[Translation :: get('LastAccessToCourse')][] = $lastconnection;
        $arr[Translation :: get('TimeOnCourse')][] = self :: get_total_time($trackerdata);
        $arr[Translation :: get('TotalTimesAccessed')][] = $count;

        return Reporting :: getSerieArray($arr);
    }

    private static function get_total_time($trackerdata)
    {
        foreach ($trackerdata as $key => $value)
        {
            $time += strtotime($value->get_leave_date()) - strtotime($value->get_enter_date());
        }

        $time = mktime(0, 0, $time, 0, 0, 0);
        $time = date('G:i:s', $time);
        return $time;
    }

    /**
     * Returns a list of browsers and their amount
     * @return <type>
     */
    public static function getBrowsers()
    {
        require_once (dirname(__FILE__) . '/../trackers/browsers_tracker.class.php');
        $tracker = new BrowsersTracker();
        $condition = new EqualityCondition(BrowsersTracker :: PROPERTY_TYPE, 'browser');
        $description[0] = Translation :: get('Browsers');

        return Reporting :: array_from_tracker($tracker, $condition, $description);
    }

    /**
     * Returns a list of countries logged in from and their amount
     * @return <type>
     */
    public static function getCountries()
    {
        require_once (dirname(__FILE__) . '/../trackers/countries_tracker.class.php');
        $tracker = new CountriesTracker();
        $condition = new EqualityCondition(CountriesTracker :: PROPERTY_TYPE, 'country');
        $description[0] = Translation :: get('Countries');

        return Reporting :: array_from_tracker($tracker, $condition, $description);
    }

    /**
     * Returns a list of os logged in from and their amount
     * @return <type>
     */
    public static function getOs()
    {
        require_once (dirname(__FILE__) . '/../trackers/os_tracker.class.php');
        $tracker = new OSTracker();
        $condition = new EqualityCondition(OSTracker :: PROPERTY_TYPE, 'os');
        $description[0] = Translation :: get('Os');

        return Reporting :: array_from_tracker($tracker, $condition, $description);
    }

    /**
     * Returns a list of providers logged in from and their amount
     * @return <type>
     */
    public static function getProviders()
    {
        require_once (dirname(__FILE__) . '/../trackers/providers_tracker.class.php');
        $tracker = new ProvidersTracker();
        $condition = new EqualityCondition(ProvidersTracker :: PROPERTY_TYPE, 'provider');
        $description[0] = Translation :: get('Providers');

        return Reporting :: array_from_tracker($tracker, $condition, $description);
    }

    /**
     * Returns a list of referers logged in from and their amount
     * @return <type>
     */
    public static function getReferers()
    {
        require_once (dirname(__FILE__) . '/../trackers/referrers_tracker.class.php');
        $tracker = new ReferrersTracker();
        $condition = new EqualityCondition(ReferrersTracker :: PROPERTY_TYPE, 'referer');
        $description[0] = Translation :: get('Referers');

        return Reporting :: array_from_tracker($tracker, $condition, $description);
    }

    public static function getUserTracking($params)
    {
        require_once Path :: get_application_path() . '/lib/weblcms/weblcms_data_manager.class.php';
        require_once (dirname(__FILE__) . '/../trackers/visit_tracker.class.php');

        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $wdm = WeblcmsDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();

        $course = $wdm->retrieve_course($course_id);
        $list = $wdm->retrieve_course_user_relations(new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_id));
        //$list = $wdm->retrieve_course_users($course);
        $tracker = new VisitTracker();
        while ($user_relation = $list->next_result())
        {
            $user_id = $user_relation->get_user();
            unset($conditions);
            unset($condition);
            $conditions[] = new LikeCondition(VisitTracker :: PROPERTY_LOCATION, '&course=' . $course_id);
            $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);

            $trackerdata = $tracker->retrieve_tracker_items($condition);
            $params[ReportingManager :: PARAM_USER_ID] = $user_id;
            $user = $udm->retrieve_user($user_id);
            $arr[Translation :: get('LastName')][] = $user->get_lastname();
            $arr[Translation :: get('FirstName')][] = $user->get_firstname();
            $arr[Translation :: get('TimeOnCourse')][] = self :: get_total_time($trackerdata);
            $arr[Translation :: get('LearningPathProgress')][] = 0;
            $arr[Translation :: get('ExcerciseProgress')][] = 0;
            $arr[Translation :: get('TotalPublications')][] = $rdm->count_content_objects(new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id));
            $url = Reporting :: get_weblcms_reporting_url('CourseStudentTrackerDetailReportingTemplate', $params);
            $arr[Translation :: get('UserDetail')][] = '<a href="' . $url . '">' . Translation :: get('Detail') . '</a>';
        }

        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($arr, $description);
    }
}
?>