<?php

class ReportingViewer extends SubManager
{
	const PARAM_REPORTING_VIEWER_ACTION = 'reporting_action';

	const ACTION_VIEW_TEMPLATE = 'viewer';
	const ACTION_EXPORT_TEMPLATE = 'exporter';
	const ACTION_SAVE_TEMPLATE = 'saver';

	const DEFAULT_ACTION = self :: ACTION_VIEW_TEMPLATE;

	private $template;
	private $trail;
	private $export;
	private $show_all_blocks;

	function ReportingViewer($parent)
	{
		parent :: __construct($parent);

		$reporting_viewer_action = Request :: get(self :: PARAM_REPORTING_VIEWER_ACTION);
        if ($reporting_viewer_action)
        {
            $this->set_parameter(self :: PARAM_REPORTING_VIEWER_ACTION, $reporting_viewer_action);
        }
	}

	function show_all_blocks()
	{
		$this->show_all_blocks = true;
	}

	function hide_all_blocks()
	{
		$this->show_all_blocks = false;
	}

	function are_all_blocks_visible()
	{
		return $this->show_all_blocks;
	}

	function add_template_by_id($template_id)
	{
		$registration = ReportingDataManager :: get_instance()->retrieve_reporting_template_registration($template_id);
		$this->set_template($registration);
	}

	function add_template_by_name($template, $application)
	{
		$condition [] = new EqualityCondition(ReportingTemplateRegistration::PROPERTY_APPLICATION, $application);
		$condition [] = new EqualityCondition(ReportingTemplateRegistration::PROPERTY_TEMPLATE, $template);
		$conditions = new AndCondition($condition);
		$registration = ReportingDataManager :: get_instance()->retrieve_reporting_template_registration_by_condition($conditions);
		$this->set_template($registration);
	}

	function add_template_by_registration($registration)
	{
		$this->set_template($registration);
	}

	function add_export($export)
	{
		$this->set_export($export);
	}

	function set_export($export)
	{
		$this->export = $export;
	}

	function get_export()
	{
		return $this->export;
	}

	function set_breadcrumb_trail($trail)
	{
		$this->trail = $trail;
	}

	function get_breadcrumb_trail()
	{
		if (isset($this->trail))
		{
			return $this->trail;
		}
		else
		{
			return BreadcrumbTrail :: get_instance();
		}
	}

	function run()
	{
		$parent = $this->get_parameter(self :: PARAM_REPORTING_VIEWER_ACTION);

        switch ($parent)
        {
            case self :: ACTION_VIEW_TEMPLATE :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_EXPORT_TEMPLATE :
                $component = $this->create_component('Exporter');
                break;
            case self :: ACTION_SAVE_TEMPLATE :
                $component = $this->create_component('Saver');
                break;
            default :
                $component = $this->create_component('Viewer');
                $this->set_parameter(self :: PARAM_REPORTING_VIEWER_ACTION, self :: ACTION_VIEW_TEMPLATE);
                break;
        }

        $component->run();
	}

	function get_application_component_path()
	{
		return Path :: get_application_library_path() . 'reporting_viewer/component/';
	}

	function get_template()
	{
		return $this->template;
	}

	function set_template($template)
	{
		$this->template = $template;
		$this->set_parameter(ReportingManager::PARAM_TEMPLATE_ID, $template->get_id());
	}

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_REPORTING_VIEWER_ACTION;
    }

    /**
     * @param Application $application
     * @return ReporingViewer
     */
    static function construct($application)
    {
        return parent :: construct(__CLASS__, $application);
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        self :: construct(__CLASS__, $application)->run();
    }
}
?>