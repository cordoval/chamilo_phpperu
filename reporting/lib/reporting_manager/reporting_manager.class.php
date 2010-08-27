<?php
/**
 * $Id: reporting_manager.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib.reporting_manager
 * @author Michael Kyndt
 */

/**
 * A reporting manager provides some functionalities to the admin to manage
 * the reporting
 */
class ReportingManager extends CoreApplication
{

    const APPLICATION_NAME = 'reporting';

    const PARAM_APPLICATION = 'application';
    const PARAM_TEMPLATE_ID = 'template';
    const PARAM_TEMPLATE_NAME = 'template_name';
    const PARAM_PUBLICATION_ID = 'pid';
    const PARAM_TOOL = 'tool';
    const PARAM_REPORTING_BLOCK_ID = 'reporting_block';
    const PARAM_EXPORT_TYPE = 'export';
    const PARAM_TEMPLATE_FUNCTION_PARAMETERS = 'template_parameters';
    const PARAM_ROLE_ID = 'role';
    const PARAM_USER_ID = 'user_id';
    const PARAM_COURSE_ID = 'course_id';
    const PARAM_REPORTING_PARENT = 'reporting_parent';

    const ACTION_BROWSE_TEMPLATES = 'browser';
    const ACTION_ADD_TEMPLATE = 'add';
    const ACTION_DELETE_TEMPLATE = 'delete';
    const ACTION_VIEW_TEMPLATE = 'view';
    const ACTION_EDIT_TEMPLATE = 'edit';
    const ACTION_EXPORT = 'export';

    const DEFAULT_ACTION = self :: ACTION_BROWSE_TEMPLATES;

    function ReportingManager($user = null)
    {
        parent :: __construct($user);
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function count_reporting_template_registrations($condition = null)
    {
        return ReportingDataManager :: get_instance()->count_reporting_template_registrations($condition);
    }

    function retrieve_reporting_template_registrations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return ReportingDataManager :: get_instance()->retrieve_reporting_template_registrations($condition, $offset, $count, $order_property);
    }

    function retrieve_reporting_template_registration($reporting_template_registration_id)
    {
        return ReportingDataManager :: get_instance()->retrieve_reporting_template_registration($reporting_template_registration_id);
    }

    public static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('List'), Translation :: get('ListDescription'), Theme :: get_image_path() . 'browse_list.png', Redirect :: get_link(self :: APPLICATION_NAME, array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_TEMPLATES), array(), false, Redirect :: TYPE_CORE));

        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;

        return $info;
    }

    function get_reporting_template_registration_viewing_url($reporting_template_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_TEMPLATE, self :: PARAM_TEMPLATE_ID => $reporting_template_registration->get_id()));
    }

    function get_reporting_template_registration_editing_url($reporting_template_registration)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_TEMPLATE, self :: PARAM_TEMPLATE_ID => $reporting_template_registration->get_id()));
    }

    /**
     * Gets the reporting template registration link
     * params: application, ...
     * @param array $params
     * @return link
     */
    function get_reporting_template_registration_url($classname, $para)
    {
        $condition = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_CLASSNAME, $classname);
        $rpdm = ReportingDataManager :: get_instance();
        $templates = $rpdm->retrieve_reporting_template_registrations($condition);
        if ($template = $templates->next_result())
        {
            $parameters = array();
            $parameters[Application :: PARAM_ACTION] = ReportingManager :: ACTION_VIEW_TEMPLATE;
            $parameters[ReportingManager :: PARAM_TEMPLATE_ID] = $template->get_id();
            $parameters[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS] = $para;
        }
        else
        {
            $parameters = array();
            $parameters[Application :: PARAM_ACTION] = ReportingManager :: ACTION_VIEW_TEMPLATE;
            $parameters[ReportingManager :: PARAM_TEMPLATE_ID] = 0;
        }

        $url = ReportingManager :: get_link() . '?' . http_build_query($parameters);

        return $url;
    }

    function get_reporting_template_registration_url_content($parent, $params)
    {
        //$_SESSION[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS] = $params;
        return $parent->get_parent()->get_reporting_url($params);
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}