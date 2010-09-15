<?php

class CoursesRightsEditorManager extends RightsEditorManager
{
    const PARAM_COURSE_GROUP = 'course_group';
    const ACTION_SET_COURSE_GROUP_RIGHTS = 'set_course_group_rights';

    function __construct($parent, $locations)
    {
        parent :: __construct($parent, $locations);

        $users = array();

        $relations = WeblcmsDataManager :: get_instance()->retrieve_course_user_relations(new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->get_parent()->get_course_id()));
        while ($relation = $relations->next_result())
        {
            $users[] = $relation->get_user();
        }

        $this->limit_users($users);
    }

    function run()
    {
        $action = $this->get_parameter(self :: PARAM_RIGHTS_EDITOR_ACTION);

        switch ($action)
        {
            case self :: ACTION_SET_GROUP_RIGHTS :
                $component = $this->create_component('CourseGroupRightsSetter');
                return;
        }

        parent :: run();
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

}