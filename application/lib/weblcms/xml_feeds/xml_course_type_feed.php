<?php
/**
 * $Id: application.lib.weblcms.xml_feeds.xml_course_type_feed.php 224 2009-11-13 14:40:30Z kariboe $
 * @package application.lib.weblcms.xml_feeds
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course_type/course_type.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course_user_relation.class.php';

Translation :: set_application('weblcms');

if (Authentication :: is_valid())
{
    $query = Request :: get('query');
    $exclude = Request :: get('exclude');

    $course_type_conditions = array();

    if ($query)
    {
        $q = '*' . $query . '*';
        $course_type_conditions[] = new PatternMatchCondition(CourseType :: PROPERTY_NAME, $q);
    }

    if ($exclude)
    {
        if (! is_array($exclude))
        {
            $exclude = array($exclude);
        }

        $exclude_conditions = array();
        $exclude_conditions['coursetype'] = array();

        foreach ($exclude as $id)
        {
            $id = explode('_', $id);

            if ($id[0] == 'coursetype')
            {
                $condition = new NotCondition(new EqualityCondition(CourseType :: PROPERTY_ID, $id[1]));
            }

            $exclude_conditions[$id[0]][] = $condition;
        }

        if (count($exclude_conditions['coursetype']) > 0)
        {
            $course_type_conditions[] = new AndCondition($exclude_conditions['coursetype']);
        }
    }
	$course_type_conditions[] = new EqualityCondition(CourseType :: PROPERTY_ACTIVE, 1);
	$course_type_condition = new AndCondition($course_type_conditions);

    $course_types = array();
    $course_types_result_set = WeblcmsDataManager :: get_instance()->retrieve_course_types($course_type_condition, null, null, array(new ObjectTableOrder(CourseType :: PROPERTY_NAME)));
    while ($course_type = $course_types_result_set->next_result())
    {
    	$conditions = array();
       	$conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, Session :: get_user_id(), CourseUserRelation :: get_table_name());
       	$conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, $course_type->get_id());
       	$condition = new AndCondition($conditions);
       	$courses_count = WeblcmsDataManager :: get_instance()->count_user_courses($condition);
       	if($courses_count > 0)
			$course_types[$course_type->get_id()] = $course_type->get_name();
    }
        
   	$conditions = array();
    $conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, Session :: get_user_id(), CourseUserRelation :: get_table_name());
    $conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, 0);
   	$condition = new AndCondition($conditions);
   	$courses_count= WeblcmsDataManager :: get_instance()->count_user_courses($condition);
   	if($courses_count > 0)
		$course_types[0] = Translation :: get('NoCourseType');
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($course_types);

echo '</tree>';

function dump_tree($course_types)
{
    if (contains_results($course_types))
    {
        echo '<node id="coursetype" classes="category unlinked" title="Coursetypes">', "\n";
        foreach ($course_types as $index => $course_type)
        {
            echo '<leaf id="coursetype_' . $index . '" classes="' . 'type type_coursetype' . '" title="' . htmlentities($course_type) . '" description="' . htmlentities($course_type) . '"/>' . "\n";
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