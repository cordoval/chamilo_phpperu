<?php
/**
 * $Id: xml_course_user_group_feed.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.xml_feeds
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course_group/course_group.class.php';

if (Authentication :: is_valid())
{
    $course = Request :: get('course');

    if ($course)
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $course = $wdm->retrieve_course($course);

        $query = Request :: get('query');
        $exclude = Request :: get('exclude');

        $user_conditions = array();

        if ($query)
        {
            $q = '*' . $query . '*';

            $user_conditions[] = new PatternMatchCondition(User :: PROPERTY_USERNAME, $q);
        }

        if ($exclude)
        {
            if (! is_array($exclude))
            {
                $exclude = array($exclude);
            }

            $exclude_conditions = array();

            foreach ($exclude as $id)
            {
                $id = explode('_', $id);
                $condition = new NotCondition(new EqualityCondition(User :: PROPERTY_ID, $id[1]));

                $exclude_conditions[] = $condition;
            }

            if (count($exclude_conditions) > 0)
            {
                $user_conditions[] = new AndCondition($exclude_conditions);
            }
        }

        //if ($user_conditions)
        if (count($user_conditions) > 0)
        {
            $user_condition = new AndCondition($user_conditions);
        }
        else
        {
            $user_condition = null;
        }

        $udm = UserDataManager :: get_instance();
        $wdm = WeblcmsDataManager :: get_instance();

        $user_result_set = $udm->retrieve_users($user_condition);
        $relation_condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course);
        $course_user_relation_result_set = $wdm->retrieve_course_user_relations();

        $user_ids = array();
        while ($course_user = $course_user_relation_result_set->next_result())
        {
            $user_ids[] = $course_user->get_user();
        }

        $users = array();
        while ($user = $user_result_set->next_result())
        {
            if (in_array($user->get_id(), $user_ids))
            {
                $users[] = $user;
            }
        }
    }
    else
    {
        $users = array();
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($users);

echo '</tree>';

function dump_tree($users)
{
    if (contains_results($users))
    {
        echo '<node id="user" classes="type_category unlinked" title="Users">', "\n";
        foreach ($users as $user)
        {
            echo '<leaf id="user_' . $user->get_id() . '" classes="' . 'type type_user' . '" title="' . htmlentities($user->get_username()) . '" description="' . htmlentities($user->get_fullname()) . '"/>' . "\n";
        }
        echo '</node>', "\n";
    }
}

function contains_results($objects)
{
    if (count($objects))
    {
        return true;
    }
    return false;
}
?>