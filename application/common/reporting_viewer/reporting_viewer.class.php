<?php
require_once dirname(__FILE__) . '/reporting_viewer_component.class.php';

class ReportingViewer extends SubManager
{
	const PARAM_REPORTING_VIEWER_ACTION = 'reporting_action';
	const ACTION_VIEW_TEMPLATE = 'view';
	const ACTION_EXPORT_TEMPLATE = 'export';
	const ACTION_SAVE_TEMPLATE = 'save';
	
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
			return new BreadcrumbTrail();
		}
	}
	
	function run()
	{
		$parent = $this->get_parameter(self :: PARAM_REPORTING_VIEWER_ACTION);
        
        switch ($parent)
        {
            case self :: ACTION_VIEW_TEMPLATE :
                $component = ReportingViewerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_EXPORT_TEMPLATE :
                $component = ReportingViewerComponent :: factory('Exporter', $this);
                break;
            case self :: ACTION_SAVE_TEMPLATE :
                $component = ReportingViewerComponent :: factory('Saver', $this);
                break;
            default :
                $component = ReportingViewerComponent :: factory('Viewer', $this);
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
}
?>