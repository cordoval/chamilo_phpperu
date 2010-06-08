<?php
/**
 * $Id: reporting_weblcms.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.reporting
 */
/**
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';

class ReportingWeblcms
{

    function ReportingWeblcms()
    {
    }

    private static function visit_tracker_to_array($condition, $user, $order_by)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $tracker = new VisitTracker();
        $udm = UserDataManager :: get_instance();

        if (! $order_by)
        {
            $order_by = new ObjectTableOrder(VisitTracker :: PROPERTY_ENTER_DATE, SORT_DESC);
        }

        $trackerdata = $tracker->retrieve_tracker_items_result_set($condition, null, null, $order_by);

        while ($visittracker = $trackerdata->next_result())
        {
            if (! $user)
            {
                $user = $udm->retrieve_user($visittracker->get_user_id());
            }

            $arr[Translation :: get('User')][] = $user->get_fullname();
            $arr[Translation :: get('LastAccess')][] = $visittracker->get_enter_date();
            $time = strtotime($visittracker->get_leave_date()) - strtotime($visittracker->get_enter_date());
            $time = mktime(0, 0, $time, 0, 0, 0);
            $time = date('G:i:s', $time);
            $arr[Translation :: get('TotalTime')][] = $time;
        }

        return $arr;
    } //visit_tracker_to_array


    /**
     * Returns the course information
     * @param <type> $params
     * @return <type>
     */
    public static function getCourseInformation($params)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $course = $wdm->retrieve_course($params[ReportingManager :: PARAM_COURSE_ID]);
        $arr[Translation :: get('Name')][] = $course->get_name();
        $arr[Translation :: get('Titular')][] = $course->get_titular_string();
        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the learning path information from a given course & user
     * @param <type> $params
     * @return <type>
     */
    public static function getCourseUserLearningpathInformation($params)
    {
        return self :: getCourseUserExcerciseInformation($params);

        $array = array();
        $wdm = WeblcmsDataManager :: get_instance();
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_id);
        $access = array();
        $access[] = new InCondition('user_id', $user_id, $wdm->get_alias('content_object_publication_user'));
       //$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_alias('content_object_publication_course_group'));
        if (! empty($user_id))
        {
            $access[] = new EqualityCondition('user_id', null, $wdm->get_alias('content_object_publication_user'));
        }
        $conditions[] = new OrCondition($access);
        $condition = new AndCondition($conditions);

        $series = $wdm->count_content_object_publications_new($condition);
        $lops = $wdm->retrieve_content_object_publications_new($condition);
    }

    /**
     * Returns excercise information from a course / user information
     * @param <type> $params
     * @return <type>
     */
    public static function getCourseUserExcerciseInformation($params)
    {
        return Reporting :: getSerieArray($arr);
    }

    /**
     * returns the number of courses currently on the system
     * @param <type> $params
     * @return <type>
     */
    public static function getNoOfCourses($params)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $count = $wdm->count_courses();

        $arr[Translation :: get('CourseCount')][] = $count;

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of tools with their access statistics for a specified course
     * @param <type> $params
     */
    public static function getLastAccessToTools($params)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $tools = $wdm->get_course_modules($course_id);

        foreach ($tools as $key => $value)
        {
            $name = $value->name;
            //$link = '<img src="'.Theme :: get_image_path('weblcms').'tool_'.$name.'.png" style="vertical-align: middle;" />';// <a href="run.php?go=courseviewer&course='.$course_id.'&tool='.$name.'&application=weblcms">'.Translation :: get(Utilities::underscores_to_camelcase($name)).'</a>';
            $link = ' <a href="run.php?go=courseviewer&course=' . $course_id . '&tool=' . $name . '&application=weblcms">' . Translation :: get(Utilities :: underscores_to_camelcase($name)) . '</a>';
            $date = $wdm->get_last_visit_date_per_course($course_id, $name);
            if ($date)
            {
                $date = date('d F Y (G:i:s)', $date);
            }
            else
            {
                $date = Translation :: get('NeverAccessed');
            }
            $conditions = array();
            $conditions2 = array();
            $conditions3 = array();
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*course=' . $course_id . '*tool=' . $name . '*');
            if (isset($user_id))
                $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $conditions2[] = new AndCondition($conditions);

            if ($name == 'reporting')
            {
                $conditions3[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*course_id???=' . $course_id . '*');
                if (isset($user_id))
                    $conditions3[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
                $conditions2[] = new AndCondition($conditions3);
            }
            $condition = new OrCondition($conditions2);

            $trackerdata = $tracker->retrieve_tracker_items($condition);

            $arr[$link][] = $date;
            $arr[$link][] = count($trackerdata);
            $params['tool'] = $name;
            $url = Reporting :: get_weblcms_reporting_url('ToolPublicationsDetailReportingTemplate', $params);
            $arr[$link][] = '<a href="' . $url . '">' . Translation :: get('ViewPublications') . '</a>';
        }

        $description[0] = Translation :: get('Tool');
        $description[1] = Translation :: get('LastAccess');
        $description[2] = Translation :: get('Clicks');
        $description[3] = Translation :: get('Publications');
        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_VERTICAL;
        return Reporting :: getSerieArray($arr, $description);
    }

    public static function getLastAccessToToolsPlatform($params)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();

        $tools = $wdm->get_all_course_modules();

        foreach ($tools as $name)
        {
            $link = '<img src="' . Theme :: get_image_path('weblcms') . 'tool_' . $name . '.png" style="vertical-align: middle;" /> ' . Translation :: get(Utilities :: underscores_to_camelcase($name));
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*tool=' . $name . '*');

            $trackerdata = $tracker->retrieve_tracker_items($condition);

            $arr[$link][] = count($trackerdata);
            $params['tool'] = $name;
            $url = Reporting :: get_weblcms_reporting_url('ToolPublicationsDetailReportingTemplate', $params);
            $arr[$link][] = '<a href="' . $url . '">' . Translation :: get('ViewPublications') . '</a>';
        }

        $description[0] = Translation :: get('Tool');
        $description[1] = Translation :: get('Clicks');
        $description[2] = Translation :: get('Publications');
        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_VERTICAL;
        return Reporting :: getSerieArray($arr, $description);
    }

    /**
     * Returns a list of the latest acces to a course
     * If a user is specified, returns access for this user to the course, else
     * it returns a list of all users
     * @param <type> $params
     */
    public static function getLatestAccess($params)
    {
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $udm = UserDataManager :: get_instance();

        if (isset($user_id))
        {
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*course=' . $course_id . '*');
            $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new PattenMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course_id . '*');
        }

        $user = $udm->retrieve_user($user_id);
        $arr = self :: visit_tracker_to_array($condition, $user);

        $description['default_sort_column'] = 1;
        return Reporting :: getSerieArray($arr, $description);
    }

    /**
     * Returns the number of courses listed by language
     * @param <type> $params
     * @return <type>
     */
    public static function getNoOfCoursesByLanguage($params)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $arr = array();
        $courses = $wdm->retrieve_courses();
        while ($course = $courses->next_result())
        {
            $lang = $course->get_language();
            if (array_key_exists($lang, $arr))
            {
                $arr[$lang][0] ++;
            }
            else
            {
                $arr[$lang][0] = 1;
            }
        }

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of courses active within the last 24hrs, last week, last month
     * @param array $params
     */
    public static function getMostActiveInactiveLastVisit($params)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $courses = $wdm->retrieve_courses(null, null, null, $params['order_by']);

        $arr[Translation :: get('Past24hr')][0] = 0;
        $arr[Translation :: get('PastWeek')][0] = 0;
        $arr[Translation :: get('PastMonth')][0] = 0;
        $arr[Translation :: get('PastYear')][0] = 0;
        $arr[Translation :: get('NeverAccessed')][0] = 0;

        while ($course = $courses->next_result())
        {
            $lastaccess = 0;
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course->get_id() . '*');
            $trackerdata = $tracker->retrieve_tracker_items($condition);
            foreach ($trackerdata as $key => $value)
            {
                $lastaccess = $value->get_leave_date();
            }

            if ($lastaccess == 0)
            {
                $arr[Translation :: get('NeverAccessed')][0] ++;
            }
            else
                if (strtotime($lastaccess) > time() - 86400)
                {
                    $arr[Translation :: get('Past24hr')][0] ++;
                }
                else
                    if (strtotime($lastaccess) > time() - 604800)
                    {
                        $arr[Translation :: get('PastWeek')][0] ++;
                    }
                    else
                        if (strtotime($lastaccess) > time() - 18144000)
                        {
                            $arr[Translation :: get('PastMonth')][0] ++;
                        }
                        else
                            if (strtotime($lastaccess) > time() - 31536000)
                            {
                                $arr[Translation :: get('PastYear')][0] ++;
                            }
                            else
                            {
                                $arr[Translation :: get('MoreThenOneYear')][0] ++;
                            }
        }
        $description[0] = Translation :: get('Time');
        $description[1] = Translation :: get('TimesAccessed');
        return Reporting :: getSerieArray($arr, $description);
    }

    /**
     * Returns a list of courses active within the last 24hrs, last week, last month
     * @param array $params
     */
    public static function getMostActiveInactiveLastPublication($params)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $courses = $wdm->retrieve_courses(null, null, null, $params['order_by']);

        $arr[Translation :: get('Past24hr')][0] = 0;
        $arr[Translation :: get('PastWeek')][0] = 0;
        $arr[Translation :: get('PastMonth')][0] = 0;
        $arr[Translation :: get('PastYear')][0] = 0;
        $arr[Translation :: get('NothingPublished')][0] = 0;

        while ($course = $courses->next_result())
        {
            $lastpublication = 0;

            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
            $publications = $datamanager->retrieve_content_object_publications_new($condition);

            while ($publication = $publications->next_result())
            {
                $lastpublication = $publication->get_modified_date();
                $lastpublication = date('Y-m-d G:i:s', $lastpublication);
            }

            if ($lastpublication == 0)
            {
                $arr[Translation :: get('NothingPublished')][0] ++;
            }
            else
                if (strtotime($lastpublication) > time() - 86400)
                {
                    $arr[Translation :: get('Past24hr')][0] ++;
                }
                else
                    if (strtotime($lastpublication) > time() - 604800)
                    {
                        $arr[Translation :: get('PastWeek')][0] ++;
                    }
                    else
                        if (strtotime($lastpublication) > time() - 18144000)
                        {
                            $arr[Translation :: get('PastMonth')][0] ++;
                        }
                        else
                            if (strtotime($lastpublication) > time() - 31536000)
                            {
                                $arr[Translation :: get('PastYear')][0] ++;
                            }
                            else
                            {
                                $arr[Translation :: get('MoreThenOneYear')][0] ++;
                            }
        }
        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns the most active / inactive courses
     * Link to course
     * @param array $params
     */
    public static function getMostActiveInactiveDetail($params)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $wdm = WeblcmsDataManager :: get_instance();
        $tracker = new VisitTracker();
        $courses = $wdm->retrieve_courses(null, null, null, $params['order_by']);
        while ($course = $courses->next_result())
        {
            $lastaccess = Translation :: get('NeverAccessed');
            $lastpublication = Translation :: get('NothingPublished');

            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&course=' . $course->get_id() . '*');
            $trackerdata = $tracker->retrieve_tracker_items($condition);
            foreach ($trackerdata as $key => $value)
            {
                $lastaccess = $value->get_leave_date();
            }

            $condition = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
            $publications = $datamanager->retrieve_content_object_publications_new($condition);

            while ($publication = $publications->next_result())
            {
                $lastpublication = $publication->get_modified_date();
                //$lastpublication = date_create($lastpublication);
                $lastpublication = date('Y-m-d G:i:s', $lastpublication);
            }

            $arr[Translation :: get('Course')][] = '<a href="run.php?go=courseviewer&course=' . $course->get_id() . '&application=weblcms&" />' . $course->get_name() . '</a>';
            $arr[Translation :: get('LastVisit')][] = $lastaccess;
            $arr[Translation :: get('LastPublication')][] = $lastpublication;
        }

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of published object types and their amount
     * @param array $params
     */
    public static function getNoOfPublishedObjectsPerType($params)
    {
        $list = RepositoryDataManager :: get_registered_types();
        foreach ($list as $key => $value)
        {
            $arr[$value][0] = 0;
        }

        $wdm = WeblcmsDataManager :: get_instance();
        $content_objects = $wdm->retrieve_content_object_publications_new();
        while ($content_object = $content_objects->next_result())
        {
            //dump($content_object);
            $arr[$content_object->get_content_object()->get_type()][0] ++;
        }

        foreach ($arr as $key => $value)
        {
            $arr[Translation :: get(Utilities :: underscores_to_camelcase($key))] = $arr[$key];
            unset($arr[$key]);
        }

        return Reporting :: getSerieArray($arr);
    }

    /**
     * Returns a list of object types and their amount
     * @param array $params
     */
    public static function getNoOfObjectsPerType($params)
    {
        $list = RepositoryDataManager :: get_registered_types();
        foreach ($list as $key => $value)
        {
            $arr[$value][0] = 0;
        }

        $list = $rdm->retrieve_content_objects();
        while ($content_object = $list->next_result())
        {
            $arr[$content_object->get_type()][0] ++;
        }

        foreach ($arr as $key => $value)
        {
            $arr[Translation :: get(Utilities :: underscores_to_camelcase($key))] = $arr[$key];
            unset($arr[$key]);
        }

        $description[0] = Translation :: get('Object');
        return Reporting :: getSerieArray($arr, $description);
    }

    public static function getAverageLearningpathScore($params)
    {
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $wdm = WeblcmsDataManager :: get_instance();

        $course = $wdm->retrieve_course($course_id);

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'learning_path');
        $lops = $wdm->retrieve_content_object_publications_new($condition, $params['order_by']);

        while ($lop = $lops->next_result())
        {
            $lpo = $lop->get_content_object();
            $arr[$lpo->get_title()][0] = 0;
        }

        $datadescription[0] = Translation :: get('LearningPath');
        $datadescription[1] = Translation :: get('Average');

        return Reporting :: getSerieArray($arr, $datadescription);
    }

    public static function getAverageExcerciseScore($params)
    {
        return Reporting :: getSerieArray($arr);
    }

    public static function getWikiPageMostActiveUser($params)
    {
        $dm = RepositoryDataManager :: get_instance();
        $cloi = $dm->retrieve_complex_content_object_item($params['cid']);
        $wiki_page = $dm->retrieve_content_object($cloi->get_ref());
        $versions = $dm->retrieve_content_object_versions($wiki_page);
        $users = array();
        foreach ($versions as $version)
        {
            $users[$version->get_default_property(ContentObject :: PROPERTY_OWNER_ID)] ++;
        }
        arsort($users);
        $keys = array_keys($users);
        $user = UserDataManager :: get_instance()->retrieve_user($keys[0]);
        $arr[Translation :: get('MostActiveUser')][] = $user->get_username();
        $arr[Translation :: get('NumberOfContributions')][] = $users[$user->get_id()];

        return Reporting :: getSerieArray($arr);
    }

    public static function getWikiPageUsersContributions($params)
    {
        $dm = RepositoryDataManager :: get_instance();
        $cloi = $dm->retrieve_complex_content_object_item($params['cid']);
        $wiki_page = $dm->retrieve_content_object($cloi->get_ref());
        $versions = $dm->retrieve_content_object_versions($wiki_page);
        $users = array();
        foreach ($versions as $version)
        {
            $users[$version->get_default_property(ContentObject :: PROPERTY_OWNER_ID)] ++;
        }
        arsort($users);
        foreach ($users as $user => $number)
        {
            if ($count < 5)
            {
                $user = UserDataManager :: get_instance()->retrieve_user($user);
                $arr[Translation :: get('Username')][] = $user->get_username();
                $arr[Translation :: get('NumberOfContributions')][] = $number;
                $count ++;
            }
        }
        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($arr, $description);
    }

    public static function getWikiMostVisitedPage($params)
    {
        $tdm = TrackingDataManager :: get_instance();
        if (Request :: get('application') == 'wiki')
        {
            $parameter = 'wiki_publication';
        }
        else
            $parameter = Tool :: PARAM_PUBLICATION_ID;

        $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*display_action=view_item*&' . $parameter . '=' . $params[Tool :: PARAM_PUBLICATION_ID] . '*');
        $items = $tdm->retrieve_tracker_items('visit_tracker', 'VisitTracker', $condition);
        if (empty($items))
            return Reporting :: getSerieArray($arr);
        foreach ($items as $item)
        {
            $var[] = explode('&', $item->get_location());
        }
        foreach ($var as $piece)
        {
            foreach ($piece as &$entry)
            {
                $entry = (explode('=', $entry));
                if (strcmp($entry[0], 'selected_cloi') === 0)
                    $cids[] = $entry[1];
            }
        }
        foreach ($cids as &$cid)
        {
            $visits[$cid] = $visits[$cid] + 1;
            $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($cid);
            if (! empty($cloi))
                $cloi_refs[$cid] = $cloi->get_ref();
        }
        if (! empty($cloi_refs))
        {
            switch (Request :: get('tool'))
            {
                case 'learning_path' :
                    $tool_action = 'view_clo';
                    break;
                default :
                    $tool_action = 'view';
                    break;
            }
            arsort($visits);
            $keys = array_keys($visits);
            foreach ($keys as $key)
            {
                if (in_array($key, array_keys($cloi_refs)))
                {
                    $page = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi_refs[$key]);
                    $url = (Redirect :: get_url(array('go' => 'courseviewer', 'course' => $params['course_id'], 'tool' => 'wiki', 'application' => 'weblcms', 'tool_action' => $tool_action, 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => $params[Tool :: PARAM_PUBLICATION_ID], 'selected_cloi' => $keys[0])));
                    $arr[Translation :: get('MostVisitedPage')][] = '<a href="' . $url . '">' . htmlspecialchars($page->get_title()) . '</a>';
                    $arr[Translation :: get('NumberOfVisits')][] = $visits[$keys[0]];
                    break;
                }
            }

        }
        return Reporting :: getSerieArray($arr);
    }

    public static function getWikiMostEditedPage($params)
    {
        require_once Path :: get_application_path() . 'lib/weblcms/data_manager/database.class.php';
        $wiki = RepositoryDataManager :: get_instance()->retrieve_content_object($params[Tool :: PARAM_PUBLICATION_ID]);

        $clois = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $wiki->get_id(), ComplexContentObjectItem :: get_table_name()), $params['order_by'])->as_array();

        if (empty($clois))
            return Reporting :: getSerieArray($arr);

        foreach ($clois as $cloi)
        {
            $pages[$cloi->get_id()] = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
        }

        foreach ($pages as $cid => $page)
        {
            $edits[$page->get_title()] = RepositoryDataManager :: get_instance()->count_content_object_versions($page);
            $page_ids[$page->get_title()] = $cid;
        }
        arsort($edits);
        $keys = array_keys($edits);
        $url = (Redirect :: get_url(array('go' => 'courseviewer', 'course' => $params['course_id'], 'tool' => 'wiki', 'application' => 'weblcms', 'tool_action' => 'view', 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => $wiki->get_id(), 'cid' => $page_ids[$keys[0]])));
        $arr[Translation :: get('MostEditedPage')][] = '<a href="' . $url . '">' . htmlspecialchars($keys[0]) . '</a>';
        $arr[Translation :: get('NumberOfEdits')][] = $edits[$keys[0]];
        return Reporting :: getSerieArray($arr);
    }

    public static function getToolPublicationsDetail($params)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $tool = $params['tool'];

        $tracker = new VisitTracker();
        $wdm = WeblcmsDataManager :: get_instance();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course_id);
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, $tool);

        $access = array();
        $access[] = new InCondition('user_id', $user_id, $wdm->get_alias('content_object_publication_user'));
        if (! empty($user_id))
        {
            $access[] = new EqualityCondition('user_id', null, $wdm->get_alias('content_object_publication_user'));
        }
        $conditions[] = new OrCondition($access);
        $condition = new AndCondition($conditions);
        $lops = $wdm->retrieve_content_object_publications_new($condition, $params['order_by']);

        while ($lop = $lops->next_result())
        {
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*course=' . $course_id . '*pid=' . $lop->get_id() . '*');
            $trackerdata = $tracker->retrieve_tracker_items($condition);

            foreach ($trackerdata as $key => $value)
            {
                if ($value->get_leave_date() > $lastaccess)
                    $lastaccess = $value->get_leave_date();
            }
            $url = 'run.php?go=courseviewer&course=' . $course_id . '&tool=' . $tool . '&application=weblcms&tool_action=view&pid=' . $lop->get_id();
            $arr[Translation :: get('Title')][] = '<a href="' . $url . '">' . $lop->get_content_object()->get_title() . '</a>';

            $des = $lop->get_content_object()->get_description();
            $arr[Translation :: get('Description')][] = Utilities :: truncate_string($des, 50);
            $arr[Translation :: get('LastAccess')][] = $lastaccess;
            $arr[Translation :: get('TotalTimesAccessed')][] = count($trackerdata);
            $params[Tool :: PARAM_PUBLICATION_ID] = $lop->get_id();
            $url = Reporting :: get_weblcms_reporting_url('PublicationDetailReportingTemplate', $params);
            $arr[Translation :: get('PublicationDetails')][] = '<a href="' . $url . '">' . Translation :: get('AccessDetails') . '</a>';
        }

        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;

        return Reporting :: getSerieArray($arr, $description);
    }

    public static function getPublicationDetail($params)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $tool = $params['tool'];
        $pid = $params[Tool :: PARAM_PUBLICATION_ID];

        $tracker = new VisitTracker();
        $wdm = WeblcmsDataManager :: get_instance();
        $condition = new EqualityCondition(WeblcmsManager :: PARAM_TOOL, $tool);
        $lop = $wdm->retrieve_content_object_publication($pid);
        if (empty($lop))
        {
            $lop = RepositoryDataManager :: get_instance()->retrieve_content_object($pid);
            $title = $lop->get_title();
            $id = $lop->get_id();
            $descr = $lop->get_description();
        }
        else
        {
            $title = $lop->get_content_object()->get_title();
            $id = $pid;
            $descr = $lop->get_content_object()->get_description();
        }

        $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*pid=' . $pid . '*');
        $trackerdata = $tracker->retrieve_tracker_items($condition);

        foreach ($trackerdata as $key => $value)
        {
            if ($value->get_leave_date() > $lastaccess)
                $lastaccess = $value->get_leave_date();
        }
        //      run.php?go=courseviewer&course=1&tool=announcement&application=weblcms&pid=1&tool_action=view
        $url = 'run.php?go=courseviewer&course=' . $course_id . '&tool=' . $tool . '&application=weblcms&pid=' . $id . '&tool_action=view';
        $arr[Translation :: get('Title')][] = '<a href="' . $url . '">' . $title . '</a>';

        $arr[Translation :: get('Description')][] = Utilities :: truncate_string($descr, 50);
        $arr[Translation :: get('LastAccess')][] = $lastaccess;
        $arr[Translation :: get('TotalTimesAccessed')][] = count($trackerdata);

        //$description[Reporting::PARAM_ORIENTATION] = Reporting::ORIENTATION_HORIZONTAL;


        return Reporting :: getSerieArray($arr, $description);
    }

    public static function getPublicationAccess($params)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $pid = $params[Tool :: PARAM_PUBLICATION_ID];
        $tool = $params['tool'];

        $udm = UserDataManager :: get_instance();

        if (isset($user_id))
        {
            $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*pid=' . $pid . '*');
            $conditions[] = new EqualityCondition(VisitTracker :: PROPERTY_USER_ID, $user_id);
            $condition = new AndCondition($conditions);
        }
        else
        {
            $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*&pid=' . $pid . '*');
        }
        $user = $udm->retrieve_user($user_id);

        $arr = self :: visit_tracker_to_array($condition, $user, $params['order_by']);
        $description['default_sort_column'] = 1;
        return Reporting :: getSerieArray($arr, $description);
    }

    /**
     * Returns a list of all users which have accessed this publication
     * + last access date & how many times
     * @param <type> $params
     */
    public static function getPublicationUserAccess($params)
    {
        require_once Path :: get_user_path() . 'trackers/visit_tracker.class.php';

        $tracker = new VisitTracker();
        $course_id = $params[ReportingManager :: PARAM_COURSE_ID];
        $user_id = $params[ReportingManager :: PARAM_USER_ID];
        $tool = $params['tool'];
        $pid = $params[Tool :: PARAM_PUBLICATION_ID];

        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user($user_id);

        $condition = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*pid=' . $pid . '*');

        $order_by = new ObjectTableOrder(VisitTracker :: PROPERTY_ENTER_DATE, SORT_DESC);
        if ($params['order_by'])
        {
            $order_by = $params['order_by'];
        }

        $trackerdata = $tracker->retrieve_tracker_items_result_set($condition, null, null, $order_by);

        while ($value = $trackerdata->next_result())
        {
            $time = strtotime($value->get_leave_date()) - strtotime($value->get_enter_date());

            if (! in_array($udm->retrieve_user($value->get_user_id())->get_fullname(), $arr[Translation :: get('User')]))
            {
                $arr[Translation :: get('User')][] = $udm->retrieve_user($value->get_user_id())->get_fullname();
                $arr[Translation :: get('LastAccess')][] = $value->get_enter_date();
                $arr[Translation :: get('TotalTime')][] = $time;
                $arr[Translation :: get('Clicks')][] = 1;
            }
            else
            {
                $keys = array_keys($arr[Translation :: get('User')], $udm->retrieve_user($value->get_user_id())->get_fullname());
                $key = $keys[0];
                $arr[Translation :: get('TotalTime')][$key] += $time;
                $arr[Translation :: get('Clicks')][$key] ++;
            }
        }

        foreach ($arr[Translation :: get('TotalTime')] as $key => $value)
        {
            $value = mktime(0, 0, $value, 0, 0, 0);
            $value = date('G:i:s', $value);
            $arr[Translation :: get('TotalTime')][$key] = $value;
        }

        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($arr, $description);
    }

    public static function getCoursesPerCategory($params)
    {
        $wdm = WeblcmsDataManager :: get_instance();

        $categories = $wdm->retrieve_course_categories();

        while ($category = $categories->next_result())
        {
            $arr[$category->get_name()][0] = 0;
            $condition = new EqualityCondition(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID, $category->get_id());
            $courses = $wdm->retrieve_courses($condition);
            while ($course = $courses->next_result())
            {
                $arr[$category->get_name()][0] ++;
            }
        }

        return Reporting :: getSerieArray($arr);
    } //getCoursesPerCategory


    public static function getLearningPathProgress($params)
    {
        $data = array();
        $objects = $params['objects'];
        $attempt_data = $params['attempt_data'];
        $cid = $params['cid'];
        $url = $params['url'];
        $total = 0;

        if ($cid)
        {
            $object = $objects[$cid];
            $tracker_datas = $attempt_data[$cid];

            foreach ($tracker_datas['trackers'] as $tracker)
            {
                if (get_class($object) == 'Assessment')
                {
                    $data[' '][] = '<a href="' . $url . '&cid=' . $cid . '&details=' . $tracker->get_id() . '">' . Theme :: get_common_image('action_view_results') . '</a>';
                }

                $data[Translation :: get('LastStartTime')][] = DatetimeUtilities :: format_locale_date($tracker->get_start_time());
                $data[Translation :: get('Status')][] = Translation :: get($tracker->get_status() == 'completed' ? 'Completed' : 'Incomplete');
                $data[Translation :: get('Score')][] = $tracker->get_score() . '%';
                $data[Translation :: get('Time')][] = Utilities :: format_seconds_to_hours($tracker->get_total_time());
                $total += $tracker->get_total_time();

                if ($params['delete'])
                    $data['  '][] = Text :: create_link($params['url'] . '&stats_action=delete_lpi_attempt&delete_id=' . $tracker->get_id(), Theme :: get_common_image('action_delete'));
            }

            $data[Translation :: get('LastStartTime')][] = '';

        }
        else
        {
            foreach ($objects as $wrapper_id => $object)
            {
                $tracker_data = $attempt_data[$wrapper_id];

                $data[' '][] = $object->get_icon();
                $data[Translation :: get('Title')][] = '<a href="' . $url . '&cid=' . $wrapper_id . '">' . $object->get_title() . '</a>';

                if ($tracker_data)
                {
                    $data[Translation :: get('Status')][] = Translation :: get($tracker_data['completed'] ? 'Completed' : 'Incomplete');
                    $data[Translation :: get('Score')][] = round($tracker_data['score'] / $tracker_data['size']) . '%';
                    $data[Translation :: get('Time')][] = Utilities :: format_seconds_to_hours($tracker_data['time']);
                    $total += $tracker_data['time'];
                }
                else
                {
                    $data[Translation :: get('Status')][] = 'incomplete';
                    $data[Translation :: get('Score')][] = '0%';
                    $data[Translation :: get('Time')][] = '0:00:00';
                }

                if ($params['delete'])
                    $data['  '][] = Text :: create_link($params['url'] . '&stats_action=delete_lpi_attempts&item_id=' . $wrapper_id, Theme :: get_common_image('action_delete'));
            }

            $data[Translation :: get('Title')][] = '';
        }

        $data[' '][] = '';
        $data[Translation :: get('Status')][] = '<span style="font-weight: bold;">' . Translation :: get('TotalTime') . '</span>';
        $data[Translation :: get('Score')][] = '';
        $data[Translation :: get('Time')][] = '<span style="font-weight: bold;">' . Utilities :: format_seconds_to_hours($total) . '</span>';

        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;

        return Reporting :: getSerieArray($data, $description);
    }

    public static function getLearningPathAttempts($params)
    {
        $data = array();

        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $params['course']);
        $conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $params['publication']->get_id());
        $condition = new AndCondition($conditions);

        $udm = UserDataManager :: get_instance();

        $dummy = new WeblcmsLpAttemptTracker();
        $trackers = $dummy->retrieve_tracker_items($condition);
        foreach ($trackers as $tracker)
        {
            $url = $params['url'] . '&attempt_id=' . $tracker->get_id();
            $delete_url = $url . '&stats_action=delete_lp_attempt';

            $user = $udm->retrieve_user($tracker->get_user_id());
            $data[Translation :: get('User')][] = $user->get_fullname();
            $data[Translation :: get('Progress')][] = $tracker->get_progress() . '%';
            //$data[Translation :: get('Details')][] = '<a href="' . $url . '">' . Theme :: get_common_image('action_reporting') . '</a>';
            $data[' '][] = Text :: create_link($url, Theme :: get_common_image('action_reporting')) . ' ' . Text :: create_link($delete_url, Theme :: get_common_image('action_delete'));
        }

        $description[Reporting :: PARAM_ORIENTATION] = Reporting :: ORIENTATION_HORIZONTAL;
        return Reporting :: getSerieArray($data, $description);
    }
}
?>